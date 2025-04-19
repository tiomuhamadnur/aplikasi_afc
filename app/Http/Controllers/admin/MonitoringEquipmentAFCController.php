<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\ConfigEquipmentAFC;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Process;
use Illuminate\Process\Pool;
use Illuminate\Support\Facades\Cache;

class MonitoringEquipmentAFCController extends Controller
{
    const EQUIPMENT_TYPE_SCU = 'SCU';
    const EQUIPMENT_TYPE_PG = 'PG';

    protected $sshTimeout = 1;
    protected $pingTimeout = 1;
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

    protected function checkEquipmentStatusParallel(
        Collection $equipments,
        string $username,
        string $password,
        bool $checkTemp = false
    ): array {
        $results = [];

        // 1. Parallel Ping Check
        $pingPool = Process::pool(function (Pool $pool) use ($equipments) {
            foreach ($equipments as $eq) {
                $pool->command(['ping', '-c', '1', '-W', $this->pingTimeout, $eq->ip_address])
                    ->timeout($this->pingTimeout + 1);
            }
        })->start();

        $pingResults = $pingPool->wait();
        $onlineDevices = collect($pingResults)
            ->filter(fn ($result) => $result->successful())
            ->keys();

        // 2. Parallel SSH Processing
        $sshPool = Process::pool(function (Pool $pool) use ($onlineDevices, $username, $password, $checkTemp) {
            foreach ($onlineDevices as $ip) {
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

                $combinedCmd = implode(' && ', array_map(
                    fn($cmd) => "echo '===CMD_".md5($cmd)."===' && $cmd",
                    $commands
                ));

                $pool->command([
                    'sshpass', '-p', $password,
                    'ssh', '-o', "ConnectTimeout={$this->sshTimeout}",
                    '-o', 'StrictHostKeyChecking=no',
                    "{$username}@{$ip}",
                    $combinedCmd
                ])->timeout($this->sshTimeout + 2);
            }
        })->concurrently($this->concurrency);

        $sshResults = $sshPool->wait();

        // 3. Build Results
        foreach ($equipments as $eq) {
            $ip = $eq->ip_address;

            if (!$onlineDevices->contains($ip)) {
                $results[$ip] = $this->createOfflineResponse($eq, $ip);
                continue;
            }

            if (!isset($sshResults[$ip]) || !$sshResults[$ip]->successful()) {
                $results[$ip] = $this->createOfflineResponse($eq, $ip);
                continue;
            }

            $parsed = $this->parseCombinedOutput($sshResults[$ip]->output(), $checkTemp);

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
        preg_match_all('/Core \d+:\s+\+([\d.]+)°?C/', $sensors, $matches);
        return $matches[1] ?? [];
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
}
