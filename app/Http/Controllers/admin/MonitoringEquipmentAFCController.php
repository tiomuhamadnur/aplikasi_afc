<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\ConfigEquipmentAFC;
use Illuminate\Http\Request;
use Illuminate\Process\Exceptions\ProcessTimedOutException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Process;

class MonitoringEquipmentAFCController extends Controller
{
    const EQUIPMENT_TYPE_SCU = 'SCU';
    const EQUIPMENT_TYPE_PG = 'PG';

    protected $sshTimeout = 2;
    protected $pingTimeout = 2;
    protected $maxConcurrentProcesses = 20;

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
        $results = $this->checkEquipmentStatusParallel($equipments, env('SSH_SCU_USERNAME'), env('SSH_SCU_PASSWORD'));
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

        $results = $this->checkEquipmentStatusParallel(
            $equipments,
            env('SSH_PG_USERNAME'),
            env('SSH_PG_PASSWORD'),
            true
        );

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
        $batches = array_chunk($equipments->all(), $this->maxConcurrentProcesses);

        foreach ($batches as $batch) {
            $processes = [];

            // First phase: Parallel ping checks
            foreach ($batch as $eq) {
                $ip = $eq->ip_address;
                $process = new Process(['ping', '-c', '1', '-W', $this->pingTimeout, $ip]);
                $process->start();
                $processes[$ip] = [
                    'process' => $process,
                    'equipment' => $eq,
                    'type' => 'ping'
                ];
            }

            // Process ping results and start SSH checks for online devices
            foreach ($processes as $ip => $data) {
                try {
                    $data['process']->wait();
                    $eq = $data['equipment'];

                    if (!$data['process']->isSuccessful()) {
                        $results[$ip] = $this->createOfflineResponse($eq, $ip);
                        continue;
                    }

                    // Prepare SSH command
                    $sshCmd = [
                        'sshpass', '-p', $password,
                        'ssh', '-o', 'ConnectTimeout='.$this->sshTimeout,
                        '-o', 'StrictHostKeyChecking=no',
                        $username.'@'.$ip,
                        'uptime -p && free -h && df -h / && grep -c ^processor /proc/cpuinfo'.
                        ($checkTemp ? ' && sensors' : '')
                    ];

                    $sshProcess = new Process($sshCmd);
                    $sshProcess->setTimeout($this->sshTimeout);
                    $sshProcess->start();
                    $processes[$ip] = [
                        'process' => $sshProcess,
                        'equipment' => $eq,
                        'type' => 'ssh'
                    ];
                } catch (\Exception $e) {
                    $results[$ip] = $this->createOfflineResponse($data['equipment'], $ip);
                }
            }

            // Process SSH results
            foreach ($processes as $ip => $data) {
                if ($data['type'] !== 'ssh' || isset($results[$ip])) {
                    continue;
                }

                try {
                    $data['process']->wait();
                    $eq = $data['equipment'];

                    if (!$data['process']->isSuccessful()) {
                        $results[$ip] = $this->createOfflineResponse($eq, $ip);
                        continue;
                    }

                    $output = $data['process']->getOutput();
                    $lines = array_values(array_filter(explode("\n", trim($output))));

                    $result = $this->parseSshOutput($eq, $ip, $lines, $checkTemp);
                    $results[$ip] = $result;
                } catch (\Exception $e) {
                    $results[$ip] = $this->createOfflineResponse($data['equipment'], $ip);
                }
            }
        }

        return array_values($results);
    }

    protected function parseSshOutput($eq, string $ip, array $lines, bool $checkTemp): array
    {
        $result = [
            'scu_id' => $eq->id,
            'station_code' => $eq->station_code,
            'equipment_type_code' => $eq->equipment_type_code,
            'equipment_name' => $eq->equipment_name,
            'corner_id' => $eq->corner_id,
            'ip' => $ip,
            'status' => 'online',
            'uptime' => $lines[0] ?? '-',
            'core_temperatures' => [],
        ];

        // Find indices of each command output
        $freeIndex = $this->findLineIndex($lines, 'Mem:');
        $dfIndex = $this->findLineIndex($lines, '/dev/');
        $coresIndex = $this->findLineIndex($lines, 'processor', true);

        // Parse system information
        $cores = max(1, (int)($lines[$coresIndex] ?? 1));

        $result['cpu_cores'] = $cores;
        $result['load_average'] = $this->parseLoadAverage($lines[1] ?? '', $cores);

        if ($freeIndex !== null) {
            $result['ram'] = $this->parseRamUsage(implode("\n", array_slice($lines, $freeIndex, 2)));
        } else {
            $result['ram'] = $this->createEmptyResource();
        }

        if ($dfIndex !== null) {
            $result['disk_root'] = $this->parseDiskUsage(implode("\n", array_slice($lines, $dfIndex, 2)));
        } else {
            $result['disk_root'] = $this->createEmptyResource();
        }

        if ($checkTemp) {
            $sensorsStart = max($freeIndex, $dfIndex, $coresIndex) + 1;
            $sensorsOutput = implode("\n", array_slice($lines, $sensorsStart));
            $result['core_temperatures'] = $this->parseCoreTemperatures($sensorsOutput);
        }

        return $result;
    }

    protected function findLineIndex(array $lines, string $search, bool $contains = false): ?int
    {
        foreach ($lines as $i => $line) {
            if ($contains ? str_contains($line, $search) : $line === $search) {
                return $i;
            }
        }
        return null;
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
            'ram' => $this->createEmptyResource(),
            'disk_root' => $this->createEmptyResource(),
            'cpu_cores' => 0,
            'core_temperatures' => [],
        ];
    }

    protected function createEmptyResource(): array
    {
        return [
            'used' => '-',
            'total' => '-',
            'percent' => 0,
        ];
    }

    protected function parseLoadAverage(string $uptime, int $cores): array
    {
        preg_match('/load average: ([\d.]+), ([\d.]+), ([\d.]+)/', $uptime, $matches);

        $load1m = (float)($matches[1] ?? 0);

        return [
            '1m' => $load1m,
            '5m' => (float)($matches[2] ?? 0),
            '15m' => (float)($matches[3] ?? 0),
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
            'percent' => $this->calculateRamPercent($memLine[2] ?? '0M', $memLine[1] ?? '1M')
        ];
    }

    protected function parseDiskUsage(string $df): array
    {
        $diskLine = explode("\n", trim($df))[1] ?? '';
        $diskParts = preg_split('/\s+/', $diskLine);

        return [
            'used' => $diskParts[2] ?? '0M',
            'total' => $diskParts[1] ?? '1G',
            'percent' => (int)rtrim($diskParts[4] ?? '0%', '%')
        ];
    }

    protected function parseCoreTemperatures(string $sensors): array
    {
        preg_match_all('/Core \d+:\s+\+([\d.]+)°C/', $sensors, $matches);
        return $matches[1] ?? [];
    }

    protected function calculateRamPercent(string $used, string $total): int
    {
        $usedMB = $this->convertToMegabytes($used);
        $totalMB = $this->convertToMegabytes($total);
        return (int)min(100, round(($usedMB / max(1, $totalMB)) * 100));
    }

    protected function convertToMegabytes(string $size): float
    {
        $size = trim($size);

        if (str_ends_with($size, 'G')) {
            return (float)$size * 1024;
        }
        if (str_ends_with($size, 'M')) {
            return (float)$size;
        }
        if (str_ends_with($size, 'K')) {
            return (float)$size / 1024;
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
