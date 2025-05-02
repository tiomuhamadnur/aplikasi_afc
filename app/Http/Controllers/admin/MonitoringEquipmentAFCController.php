<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\ConfigEquipmentAFC;
use App\Models\User;
use App\Services\WhatsappService;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Process;
use Illuminate\Process\Pool;
use Illuminate\Support\Facades\Cache;

class MonitoringEquipmentAFCController extends Controller
{
    const EQUIPMENT_TYPE_SCU = 'SCU';
    const EQUIPMENT_TYPE_PG = 'PG';

    protected $sshTimeout = 5;
    protected $pingTimeout = 5;
    protected $concurrency = 25;

    public function index()
    {
        $equipmentTypes = [
            'scu' => ConfigEquipmentAFC::where('equipment_type_code', self::EQUIPMENT_TYPE_SCU)->get(),
            'pg' => ConfigEquipmentAFC::where('equipment_type_code', self::EQUIPMENT_TYPE_PG)->get(),
        ];

        return view('pages.admin.monitoring-equipment-afc.index', [
            'scu' => $equipmentTypes['scu'],
            'pg' => $equipmentTypes['pg'],
            'results' => [],
        ]);
    }

    public function store(Request $request)
    {
        $request->validate(['scu_id' => 'required']);
        $equipments = $this->getTargetEquipment(self::EQUIPMENT_TYPE_SCU, $request->scu_id);

        $cacheKey = 'monitoring_scu_' . $request->scu_id;
        $results = Cache::remember($cacheKey, 30, function () use ($equipments) {
            return $this->checkEquipmentStatusParallel($equipments, env('SSH_SCU_USERNAME'), env('SSH_SCU_PASSWORD'));
        });

        return $this->buildResponse($results);
    }

    public function store_pg(Request $request)
    {
        $request->validate([
            'station_code' => 'required',
            'pg_id' => 'required',
        ]);

        $cacheKey = 'monitoring_pg_' . $request->station_code . '_' . $request->pg_id;
        $results = Cache::remember($cacheKey, 30, function () use ($request) {
            $equipments = ConfigEquipmentAFC::where('equipment_type_code', self::EQUIPMENT_TYPE_PG)
                ->when($request->station_code !== 'all', fn($q) => $q->where('station_code', $request->station_code))
                ->when($request->pg_id !== 'all', fn($q) => $q->where('id', $request->pg_id))
                ->get();

            return $this->checkEquipmentStatusParallel(
                $equipments,
                env('SSH_PG_USERNAME'),
                env('SSH_PG_PASSWORD'),
                true, // Include temperature check
            );
        });

        return $this->buildResponse($results);
    }

    protected function getTargetEquipment(string $type, $id)
    {
        $query = ConfigEquipmentAFC::where('equipment_type_code', $type);
        return $id === 'all' ? $query->get() : $query->where('id', $id)->get();
    }

    protected function checkEquipmentStatusParallel(Collection $equipments, string $username, string $password, bool $checkTemp = false): array
    {
        $results = [];

        // 1. First Phase: Parallel Ping Checks
        $pingProcesses = [];
        foreach ($equipments as $eq) {
            $pingProcesses[$eq->ip_address] = Process::timeout($this->pingTimeout)
                ->start(['ping', '-c', '1', '-W', $this->pingTimeout, $eq->ip_address]);
        }

        // Wait for ping results
        $onlineDevices = [];
        foreach ($pingProcesses as $ip => $process) {
            try {
                $process->wait();
                // Universal way to check process success
                $processOutput = $process->output();
                $isOnline = str_contains($processOutput, '1 received');

                if ($isOnline) {
                    $onlineDevices[] = $ip;
                }
            } catch (\Exception $e) {
                continue;
            }
        }

        // 2. Second Phase: Parallel SSH Processing
        $sshProcesses = [];
        foreach ($onlineDevices as $ip) {
            $commands = [
                'uptime' => 'uptime',
                'uptime_p' => 'uptime -p',
                'free' => 'free -h',
                'df' => 'df -h /',
                'cores' => 'grep -c ^processor /proc/cpuinfo'
            ];

            if ($checkTemp) {
                $commands['sensors'] = 'sensors'; // Command sederhana
            }

            $combinedCmd = implode(' && ', array_map(
                fn($cmd) => "echo '===CMD_".md5($cmd)."===' && $cmd",
                $commands
            ));

            $sshProcesses[$ip] = Process::timeout($this->sshTimeout)
                ->start([
                    'sshpass', '-p', $password,
                    'ssh', '-o', 'ConnectTimeout='.$this->sshTimeout,
                    '-o', 'StrictHostKeyChecking=no',
                    $username.'@'.$ip,
                    $combinedCmd
                ]);
        }

        // Process SSH results
        foreach ($sshProcesses as $ip => $process) {
            $eq = $equipments->firstWhere('ip_address', $ip);

            try {
                $process->wait();

                // Check SSH command success by output content
                $sshOutput = $process->output();
                $isSshSuccess = str_contains($sshOutput, '===CMD_');

                if (!$isSshSuccess) {
                    $results[$ip] = $this->createOfflineResponse($eq, $ip);
                    continue;
                }

                $parsed = $this->parseCombinedOutput($sshOutput, $checkTemp);

                $results[$ip] = [
                    'id' => $eq->id,
                    'station_code' => $eq->station_code,
                    'equipment_type_code' => $eq->equipment_type_code,
                    'equipment_name' => $eq->equipment_name,
                    'corner_id' => $eq->corner_id,
                    'ip' => $ip,
                    'status' => 'online',
                    'uptime' => $parsed['uptime_p'] ?? '-',
                    'load_average' => $this->parseLoadAverage($parsed['uptime'] ?? '', (int)($parsed['cores'] ?? 1)),
                    'ram' => $this->parseRamUsage($parsed['free'] ?? ''),
                    'disk_root' => $this->parseDiskUsage($parsed['df'] ?? ''),
                    'cpu_cores' => (int)($parsed['cores'] ?? 1),
                    'core_temperatures' => $checkTemp ? $this->parseCoreTemperatures($parsed['sensors'] ?? '') : [],
                ];
            } catch (\Exception $e) {
                $results[$ip] = $this->createOfflineResponse($eq, $ip);
            }
        }

        // Add offline devices to results
        foreach ($equipments as $eq) {
            if (!isset($results[$eq->ip_address])) {
                $results[$eq->ip_address] = $this->createOfflineResponse($eq, $eq->ip_address);
            }
        }

        // return array_values($results);

        // Sort offline duluan (offline = prioritas tinggi)
        $sortedResults = collect($results)->sortBy(function ($item) {
                            return $item['status'] === 'offline' ? 0 : 1;
                        })->values()->all();

        return $sortedResults;
    }

    protected function parseCombinedOutput(string $output, bool $checkTemp): array
    {
        $commands = [
            'uptime' => 'uptime',
            'uptime_p' => 'uptime -p',
            'free' => 'free -h',
            'df' => 'df -h /',
            'cores' => 'grep -c ^processor /proc/cpuinfo',
        ];

        if ($checkTemp) {
            $commands['sensors'] = 'sensors';
        }

        $results = [];
        $pattern = '/===CMD_([a-f0-9]{32})===/';
        $parts = preg_split($pattern, $output);
        array_shift($parts); // Remove initial empty part

        foreach (array_values($commands) as $index => $cmd) {
            if (isset($parts[$index])) {
                $results[array_keys($commands)[$index]] = trim($parts[$index]);
            }
        }

        return $results;
    }

    protected function createOfflineResponse($eq, string $ip): array
    {
        return [
            'scu_id' => $eq->id,
            'station_code' => $eq->station_code,
            'equipment_type_code' => $eq->equipment_type_code,
            'equipment_name' => $eq->equipment_name,
            'corner_id' => $eq->corner_id,
            'ip' => $ip,
            'status' => 'offline',
            'uptime' => '-',
            'load_average' => [
                '1m' => 0,
                '5m' => 0,
                '15m' => 0,
                'status' => 'offline',
            ],
            'ram' => [
                'used' => '-',
                'total' => '-',
                'percent' => 0,
            ],
            'disk_root' => [
                'used' => '-',
                'total' => '-',
                'percent' => 0,
            ],
            'cpu_cores' => 0,
            'core_temperatures' => [],
        ];
    }

    protected function parseLoadAverage(string $uptime, int $cores): array
    {
        preg_match('/load average: ([\d.]+), ([\d.]+), ([\d.]+)/', $uptime, $matches);

        $load1m = (float) ($matches[1] ?? 0);

        return [
            '1m' => $load1m,
            '5m' => (float) ($matches[2] ?? 0),
            '15m' => (float) ($matches[3] ?? 0),
            'status' => $this->classifyLoad($load1m, $cores),
        ];
    }

    protected function parseRamUsage(string $free): array
    {
        $lines = explode("\n", $free);
        $memLine = preg_split('/\s+/', $lines[1] ?? '');

        return [
            'used' => $memLine[2] ?? '0M',
            'total' => $memLine[1] ?? '1M',
            'percent' => $this->calculateRamPercent($memLine[2] ?? '0M', $memLine[1] ?? '1M'),
        ];
    }

    protected function parseDiskUsage(string $df): array
    {
        $diskLine = explode("\n", trim($df))[1] ?? '';
        $diskParts = preg_split('/\s+/', $diskLine);

        return [
            'used' => $diskParts[2] ?? '0M',
            'total' => $diskParts[1] ?? '1G',
            'percent' => (int) rtrim($diskParts[4] ?? '0%', '%'),
        ];
    }

    protected function parseCoreTemperatures(string $sensors): array
    {
        // Pattern untuk format Intel (contoh: "Core 0: +57.0 C")
        if (preg_match_all('/Core \d+:\s+\+([\d.]+)\s?[°]?C/', $sensors, $matches)) {
            return $matches[1];
        }

        // Pattern untuk sensor umum (contoh: "temp1: +52.0 C")
        if (preg_match_all('/temp\d+:\s+\+([\d.]+)\s?[°]?C/', $sensors, $matches)) {
            return $matches[1];
        }

        return []; // Return array kosong jika tidak ditemukan
    }

    protected function calculateRamPercent(string $used, string $total): int
    {
        $usedMB = $this->convertToMegabytes($used);
        $totalMB = $this->convertToMegabytes($total);
        return (int) min(100, round(($usedMB / max(1, $totalMB)) * 100));
    }

    protected function convertToMegabytes(string $size): float
    {
        $size = trim($size);

        if (str_ends_with($size, 'G')) {
            return (float) $size * 1024;
        }
        if (str_ends_with($size, 'M')) {
            return (float) $size;
        }
        if (str_ends_with($size, 'K')) {
            return (float) $size / 1024;
        }
        return 0;
    }

    protected function classifyLoad(float $load1m, int $cpuCores): string
    {
        if ($cpuCores <= 0) {
            return 'unknown';
        }

        $percent = $load1m / $cpuCores;

        return match (true) {
            $percent < 0.7 => 'normal',
            $percent < 1.0 => 'busy',
            default => 'overload',
        };
    }

    protected function buildResponse(array $results)
    {
        return view('pages.admin.monitoring-equipment-afc.index', [
            'scu' => ConfigEquipmentAFC::where('equipment_type_code', self::EQUIPMENT_TYPE_SCU)->get(),
            'pg' => ConfigEquipmentAFC::where('equipment_type_code', self::EQUIPMENT_TYPE_PG)->get(),
            'results' => $results,
        ]);
    }






    /**
     * Mengirim notifikasi status peralatan via WhatsApp
     */
    public function sendMonitoringNotification(WhatsappService $whatsappService)
    {
        // Ambil data user yang akan menerima notifikasi
        $recipients = User::whereIn('id', [1, 2]) //sesuaikan jumlahnya
                    ->notBanned()
                    ->with(['gender', 'relasi_struktur.departemen', 'relasi_struktur.seksi'])
                    ->whereNotNull('no_hp')
                    ->get();

        // Jika tidak ada penerima yang valid
        if ($recipients->isEmpty()) {
            return response()->json([
                'status' => 'error',
                'message' => 'No valid recipients found'
            ], 400);
        }

        // Ambil data monitoring
        $scuEquipment = ConfigEquipmentAFC::where('equipment_type_code', self::EQUIPMENT_TYPE_SCU)->get();
        $scuResults = $this->checkEquipmentStatusParallel($scuEquipment, env('SSH_SCU_USERNAME'), env('SSH_SCU_PASSWORD'));

        $pgEquipment = ConfigEquipmentAFC::where('equipment_type_code', self::EQUIPMENT_TYPE_PG)->get();
        $pgResults = $this->checkEquipmentStatusParallel($pgEquipment, env('SSH_PG_USERNAME'), env('SSH_PG_PASSWORD'), true);

        // Periksa apakah ada perangkat offline
        $offlineScu = array_filter($scuResults, fn($item) => $item['status'] === 'offline');
        $offlinePg = array_filter($pgResults, fn($item) => $item['status'] === 'offline');

        if (empty($offlineScu) && empty($offlinePg)) {
            return response()->json([
                'status' => 'success',
                'message' => 'No offline equipment found, notification not sent'
            ]);
        }

        // Hitung status peralatan
        $monitoringData = [
            'scu' => [
                'online' => count(array_filter($scuResults, fn($item) => $item['status'] === 'online')),
                'total' => $scuEquipment->count(),
                'offline' => array_filter($scuResults, fn($item) => $item['status'] === 'offline')
            ],
            'pg' => [
                'online' => count(array_filter($pgResults, fn($item) => $item['status'] === 'online')),
                'total' => $pgEquipment->count(),
                'offline' => array_filter($pgResults, fn($item) => $item['status'] === 'offline')
            ]
        ];

        // Kirim notifikasi ke setiap user
        $successCount = 0;
        foreach ($recipients as $user) {
            $message = $this->formatPersonalizedMessage($user, $monitoringData);
            // $response = $this->sendNotification($user->no_hp, $message);
            $response = $whatsappService->sendMessage($user->no_hp, $message);

            if ($response !== false) {
                $successCount++;
            }
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Monitoring notification sent to '.$successCount.' recipients',
            'data' => [
                'total_recipients' => $recipients->count(),
                'successful_sends' => $successCount
            ]
        ]);
    }

    protected function formatPersonalizedMessage(User $user, array $monitoringData): string
    {
        $enter = "\n";
        $div = '=============================';

        // Data user
        $gender = $user->gender->id == 1 ? "Bapak" : "Ibu";
        $name = $user->name;
        $departemen = $user->relasi_struktur->departemen->name ?? 'N/A';
        $seksi = $user->relasi_struktur->seksi->name ?? 'N/A';

        // Data monitoring
        $scuOnline = $monitoringData['scu']['online'];
        $scuTotal = $monitoringData['scu']['total'];
        $scuOffline = $monitoringData['scu']['offline'];

        $pgOnline = $monitoringData['pg']['online'];
        $pgTotal = $monitoringData['pg']['total'];
        $pgOffline = $monitoringData['pg']['offline'];

        $message = '⚠️ *MONITORING EQUIPMENT AFC MRTJ* ' . $enter . $enter .
            'Dear ' . $gender . ' *' . $name . '*,' . $enter . $enter .
            'Berikut status monitoring *Equipment AFC* yang di-generate pada *' . now()->format('Y-m-d H:i:s') . '*:' . $enter . $enter .
            $div . $enter . $enter .
            '*SCU STATUS:*' . $enter .
            'Online: *' . $scuOnline . '*/*' . $scuTotal . '* Unit Server' . $enter;

        // Tambahkan list SCU offline jika ada
        if (!empty($scuOffline)) {
            $message .= $enter . '*SCU OFFLINE:*' . $enter;
            foreach ($scuOffline as $index => $scu) {
                $message .= ($index + 1) . '. ' . $scu['equipment_name'] . ' (' . $scu['ip'] . ') - ' . $scu['station_code'] . $enter;
            }
        }

        $message .= $enter . $enter .
            '*PG STATUS:*' . $enter .
            'Online: *' . $pgOnline . '*/*' . $pgTotal . '* Unit PG' . $enter;

        // Tambahkan list PG offline jika ada
        if (!empty($pgOffline)) {
            $message .= $enter . '*PG OFFLINE:*' . $enter;
            foreach ($pgOffline as $index => $pg) {
                $message .= ($index + 1) . '. ' . $pg['equipment_name'] . ' (' . $pg['ip'] . ') - ' . $pg['station_code'] . $enter;
            }
        }

        $message .= $enter . $div . $enter . $enter .
            '_Regards,_' . $enter . $enter .
            '*RollingSyncBOT*' .
            $enter . $enter . $enter . $enter;

        return $message;
    }

    public function dashboard(Request $request)
    {
        $request->validate([
            'station_code' => 'required'
        ]);

        $station_code = $request->station_code;

        $equipments = ConfigEquipmentAFC::where('equipment_type_code', self::EQUIPMENT_TYPE_PG)
            ->where('station_code', $station_code)
            ->get();

        if(!$equipments)
        {
            return redirect()->back()->withNotifyerror('Data PG tidak ditemukan');
        }

        $results = $this->checkEquipmentStatusParallel(
            $equipments,
            env('SSH_PG_USERNAME'),
            env('SSH_PG_PASSWORD'),
            true, // Include temperature check
        );

        return view('pages.admin.monitoring-equipment-afc.dashboard', [
            'station_code' => $station_code,
            'results' => $results,
        ]);
    }
}
