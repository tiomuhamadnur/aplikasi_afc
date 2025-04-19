<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\ConfigEquipmentAFC;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Process;
use Symfony\Component\Process\Process as SymfonyProcess;
use Illuminate\Process\Exceptions\ProcessTimedOutException;
use Illuminate\Support\Facades\Log;

class MonitoringEquipmentAFCController extends Controller
{
    const EQUIPMENT_TYPE_SCU = 'SCU';
    const EQUIPMENT_TYPE_PG = 'PG';

    protected $sshTimeout = 1;
    protected $pingTimeout = 1;
    protected $concurrency = 20;

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

        $results = $this->checkEquipmentStatus($equipments, env('SSH_SCU_USERNAME'), env('SSH_SCU_PASSWORD'));

        return $this->buildResponse($results);
    }

    public function store_pg(Request $request)
    {
        $request->validate([
            'station_code' => 'required',
            'pg_id' => 'required',
        ]);

        $equipments = ConfigEquipmentAFC::where('equipment_type_code', self::EQUIPMENT_TYPE_PG)
            ->when($request->station_code !== 'all', fn($q) => $q->where('station_code', $request->station_code))
            ->when($request->pg_id !== 'all', fn($q) => $q->where('id', $request->pg_id))
            ->get();

        $results = $this->checkEquipmentStatus(
            $equipments,
            env('SSH_PG_USERNAME'),
            env('SSH_PG_PASSWORD'),
            true, // Include temperature check for PG
        );

        return $this->buildResponse($results);
    }

    protected function getTargetEquipment(string $type, $id)
    {
        $query = ConfigEquipmentAFC::where('equipment_type_code', $type);

        return $id === 'all' ? $query->get() : $query->where('id', $id)->get();
    }

    protected function checkEquipmentStatus(Collection $equipments, string $username, string $password, bool $checkTemp = false): array
    {
        $results = [];
        $chunks = $equipments->chunk($this->concurrency);

        foreach ($chunks as $chunk) {
            $batchResults = $this->processBatch($chunk, $username, $password, $checkTemp);
            $results = array_merge($results, $batchResults);
        }

        return $results;
    }

    protected function processBatch(Collection $batch, string $username, string $password, bool $checkTemp): array
    {
        $results = [];

        foreach ($batch as $eq) {
            $ip = $eq->ip_address;

            try {
                // 1. Ping check (synchronous)
                $pingResult = Process::run(['ping', '-c', '1', '-W', $this->pingTimeout, $ip]);

                if (!$pingResult->successful()) {
                    $results[$ip] = $this->createOfflineResponse($eq, $ip);
                    continue;
                }

                // 2. SSH commands (synchronous)
                $commands = [
                    'uptime' => 'uptime',
                    'uptime_p' => 'uptime -p',
                    'free' => 'free -h',
                    'df' => 'df -h /',
                    'cores' => 'grep -c ^processor /proc/cpuinfo'
                ];

                if ($checkTemp) {
                    $commands['sensors'] = 'sensors';
                }

                $combinedCommand = implode(' && ', array_map(
                    fn($cmd) => "echo '===CMD_".md5($cmd)."===' && $cmd",
                    $commands
                ));

                $sshResult = Process::run([
                    'sshpass', '-p', $password,
                    'ssh', '-o', 'ConnectTimeout='.$this->sshTimeout,
                    '-o', 'StrictHostKeyChecking=no',
                    $username.'@'.$ip,
                    $combinedCommand
                ]);

                if (!$sshResult->successful()) {
                    $results[$ip] = $this->createOfflineResponse($eq, $ip);
                    continue;
                }

                // Parsing output
                $parsed = $this->parseCombinedOutput($sshResult->output(), $checkTemp);

                $results[$ip] = [
                    'scu_id' => $eq->id,
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

        return array_values($results);
    }

    protected function parseCombinedOutput(string $output, bool $checkTemp): array
    {
        $commands = [
            'uptime' => 'uptime',
            'uptime_p' => 'uptime -p',
            'free' => 'free -h',
            'df' => 'df -h /',
            'cores' => 'grep -c ^processor /proc/cpuinfo'
        ];

        if ($checkTemp) {
            $commands['sensors'] = 'sensors';
        }

        $results = [];
        $pattern = '/===CMD_([a-f0-9]{32})===/';
        $parts = preg_split($pattern, $output);
        array_shift($parts); // Remove bagian awal

        foreach (array_values($commands) as $index => $cmd) {
            if (isset($parts[$index])) {
                $results[array_keys($commands)[$index]] = trim($parts[$index]);
            }
        }

        return $results;
    }

    protected function checkOnlineStatus(string $ip): string
    {
        try {
            $ping = Process::timeout($this->pingTimeout)->run("ping -c 1 $ip");
            return $ping->successful() ? 'online' : 'offline';
        } catch (ProcessTimedOutException $e) {
            return 'offline';
        }
    }

    protected function createSshCommand(string $user, string $pass, string $ip): string
    {
        return "sshpass -p '$pass' ssh -o ConnectTimeout={$this->sshTimeout} " . "-o StrictHostKeyChecking=no $user@$ip";
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

    protected function gatherSystemInfo($eq, string $ip, string $ssh, bool $checkTemp): array
    {
        try {
            $uptime = Process::run("$ssh 'uptime'")->output();
            $uptime_p = Process::run("$ssh 'uptime -p'")->output();
            $free = Process::run("$ssh 'free -h'")->output();
            $df = Process::run("$ssh 'df -h /'")->output();

            // Ensure we get at least 1 core
            $coresOutput = trim(Process::run("$ssh 'grep -c ^processor /proc/cpuinfo'")->output());
            $cores = max(1, (int) $coresOutput);

            $loadData = $this->parseLoadAverage($uptime, $cores);
            $ram = $this->parseRamUsage($free);
            $disk = $this->parseDiskUsage($df);

            $result = [
                'scu_id' => $eq->id,
                'station_code' => $eq->station_code,
                'equipment_type_code' => $eq->equipment_type_code,
                'equipment_name' => $eq->equipment_name,
                'corner_id' => $eq->corner_id,
                'ip' => $ip,
                'status' => 'online',
                'uptime' => trim($uptime_p),
                'load_average' => $loadData,
                'ram' => $ram,
                'disk_root' => $disk,
                'cpu_cores' => $cores,
                'core_temperatures' => [],
            ];

            if ($checkTemp) {
                $sensors = Process::run("$ssh 'sensors'")->output();
                $result['core_temperatures'] = $this->parseCoreTemperatures($sensors);
            }

            return $result;
        } catch (\Exception $e) {
            // Fallback to offline response if any command fails
            return $this->createOfflineResponse($eq, $ip);
        }
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
            return (float) $size * 1024; // Konversi GB ke MB
        }
        if (str_ends_with($size, 'M')) {
            return (float) $size; // Sudah dalam MB
        }
        return 0; // Fallback untuk format tidak dikenal
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
        preg_match_all('/Core \d+:\s+\+([\d.]+) C/', $sensors, $matches);
        return $matches[1] ?? [];
    }

    protected function calculatePercentage(string $used, string $total): int
    {
        $usedNum = (float) str_replace(['G', 'M'], '', $used);
        $totalNum = (float) str_replace(['G', 'M'], '', $total);

        return $totalNum > 0 ? round(($usedNum / $totalNum) * 100) : 0;
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
}
