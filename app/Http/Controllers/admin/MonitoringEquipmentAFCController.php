<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\ConfigEquipmentAFC;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Process;

class MonitoringEquipmentAFCController extends Controller
{
    public function index()
    {
        $results = [];
        $scu = ConfigEquipmentAFC::where('equipment_type_code', 'SCU')->get();
        $pg = ConfigEquipmentAFC::where('equipment_type_code', 'PG')->get();
        return view('pages.admin.monitoring-equipment-afc.index', compact([
            'scu',
            'pg',
            'results',
        ]));
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $request->validate([
            'scu_id' => 'required'
        ]);

        $scu_id = $request->scu_id;

        if ($scu_id === 'all') {
            $equipments = ConfigEquipmentAFC::where('equipment_type_code', 'SCU')->get();
        } else {
            $equipments = ConfigEquipmentAFC::where('equipment_type_code', 'SCU')->where('id', $scu_id)->get();
        }

        $user = env('SSH_SCU_USERNAME');
        $pass = env('SSH_SCU_PASSWORD');

        $results = [];

        foreach ($equipments as $eq) {
            $ip = $eq->ip_address;

            $ssh = "sshpass -p '$pass' ssh -o ConnectTimeout=5 -o StrictHostKeyChecking=no $user@$ip";

            // Cek status online/offline
            $ping = Process::timeout(5)->run("ping -c 1 $ip");
            $status = $ping->successful() ? 'online' : 'offline';

            if ($status === 'offline') {
                $results[] = [
                    'scu_id' => $eq->id,
                    'ip' => $ip,
                    'status' => 'offline'
                ];
                continue;
            }

            // Ambil data dari SSH
            $uptime = Process::run("$ssh 'uptime'")->output(); // Uptime untuk load average
            $uptime_p = Process::run("$ssh 'uptime -p'")->output(); // Uptime bersih
            $free = Process::run("$ssh 'free -h'")->output();
            $df = Process::run("$ssh 'df -h /'")->output();
            $cores = (int) trim(Process::run("$ssh 'nproc'")->output());

            // Parse load average
            preg_match('/load average: ([\d.]+), ([\d.]+), ([\d.]+)/', $uptime, $matches);
            $load1m = (float)($matches[1] ?? 0);
            $load5m = (float)($matches[2] ?? 0);
            $load15m = (float)($matches[3] ?? 0);
            $load_status = $this->classifyLoad($load1m, $cores);

            // Parse RAM Usage
            $lines = explode("\n", $free);
            $memLine = preg_split('/\s+/', $lines[1]);
            $ramUsed = $memLine[2] ?? '-';
            $ramTotal = $memLine[1] ?? '-';
            $ramPercent = (is_numeric(str_replace('G', '', $ramUsed)) && is_numeric(str_replace('G', '', $ramTotal)))
                ? round(((float)str_replace('G', '', $ramUsed) / (float)str_replace('G', '', $ramTotal)) * 100)
                : 0;

            $ram = [
                'used' => $ramUsed,
                'total' => $ramTotal,
                'percent' => $ramPercent,
            ];

            // Parse Disk Usage
            $diskLine = explode("\n", trim($df))[1] ?? '';
            $diskParts = preg_split('/\s+/', $diskLine);
            $disk = [
                'used' => $diskParts[2] ?? '-',
                'total' => $diskParts[1] ?? '-',
                'percent' => (int)rtrim($diskParts[4] ?? '0%', '%'),
            ];

            // Menyusun data hasil
            $results[] = [
                'scu_id' => $eq->id,
                'station_code' => $eq->station_code,
                'equipment_type_code' => $eq->equipment_type_code,
                'ip' => $ip,
                'status' => $status,
                'uptime' => $uptime_p, // Menampilkan uptime dalam format yang bersih
                'load_average' => [
                    '1m' => $load1m,
                    '5m' => $load5m,
                    '15m' => $load15m,
                    'status' => $load_status,
                ],
                'ram' => $ram,
                'disk_root' => $disk,
                'cpu_cores' => $cores,
                'core_temperatures' => [],
            ];
        }

        // Kirim data ke view
        $scu = ConfigEquipmentAFC::where('equipment_type_code', 'SCU')->get();
        $pg = ConfigEquipmentAFC::where('equipment_type_code', 'PG')->get();
        return view('pages.admin.monitoring-equipment-afc.index', compact([
            'scu',
            'pg',
            'results',
        ]));
    }

    private function classifyLoad($load1m, $cpuCores)
    {
        $percent = $load1m / $cpuCores;
        if ($percent < 0.7) return 'normal';
        if ($percent < 1.0) return 'busy';
        return 'overload';
    }

    public function store_pg(Request $request)
    {
        $request->validate([
            'station_code' => 'required',
            'pg_id' => 'required',
        ]);

        $station_code = $request->station_code;
        $pg_id = $request->pg_id;

        $equipments = ConfigEquipmentAFC::where('equipment_type_code', 'PG')
            ->when($station_code !== 'all', function ($query) use ($station_code) {
                $query->where('station_code', $station_code);
            })
            ->when($pg_id !== 'all', function ($query) use ($pg_id) {
                $query->where('id', $pg_id);
            })
            ->get();

        $user = env('SSH_PG_USERNAME');
        $pass = env('SSH_PG_PASSWORD');

        $results = [];
        foreach ($equipments as $eq) {
            $ip = $eq->ip_address;

            $ssh = "sshpass -p '$pass' ssh -o ConnectTimeout=5 -o StrictHostKeyChecking=no $user@$ip";

            // Cek status online/offline
            $ping = Process::timeout(5)->run("ping -c 1 $ip");
            $status = $ping->successful() ? 'online' : 'offline';

            if ($status === 'offline') {
                $results[] = [
                    'scu_id' => $eq->id,
                    'ip' => $ip,
                    'status' => 'offline'
                ];
                continue;
            }

            // Ambil data dari SSH
            $uptime = Process::run("$ssh 'uptime'")->output();
            $uptime_p = Process::run("$ssh 'uptime -p'")->output();
            $free = Process::run("$ssh 'free -h'")->output();
            $df = Process::run("$ssh 'df -h /'")->output();
            $cores = (int) trim(Process::run("$ssh \"grep -c ^processor /proc/cpuinfo\"")->output());
            $sensors = Process::run("$ssh 'sensors'")->output();

            // Parse load average
            preg_match('/load average: ([\d.]+), ([\d.]+), ([\d.]+)/', $uptime, $matches);
            $load1m = (float)($matches[1] ?? 0);
            $load5m = (float)($matches[2] ?? 0);
            $load15m = (float)($matches[3] ?? 0);
            $load_status = $this->classifyLoad($load1m, $cores);

            // Parse RAM Usage
            $lines = explode("\n", $free);
            $memLine = preg_split('/\s+/', $lines[1]);
            $ramUsed = $memLine[2] ?? '-';
            $ramTotal = $memLine[1] ?? '-';
            $ramPercent = (is_numeric(str_replace('G', '', $ramUsed)) && is_numeric(str_replace('G', '', $ramTotal)))
                ? round(((float)str_replace('G', '', $ramUsed) / (float)str_replace('G', '', $ramTotal)) * 100)
                : 0;

            $ram = [
                'used' => $ramUsed,
                'total' => $ramTotal,
                'percent' => $ramPercent,
            ];

            // Parse Disk Usage
            $diskLine = explode("\n", trim($df))[1] ?? '';
            $diskParts = preg_split('/\s+/', $diskLine);
            $disk = [
                'used' => $diskParts[2] ?? '-',
                'total' => $diskParts[1] ?? '-',
                'percent' => (int)rtrim($diskParts[4] ?? '0%', '%'),
            ];

            // Parse Core Temperature
            preg_match_all('/Core \d+:\s+\+([\d.]+) C/', $sensors, $tempMatches);
            $coreTemps = $tempMatches[1] ?? []; // Menyimpan suhu setiap core dalam array

            $results[] = [
                'scu_id' => $eq->id,
                'station_code' => $eq->station_code,
                'equipment_type_code' => $eq->equipment_type_code,
                'ip' => $ip,
                'status' => $status,
                'uptime' => trim($uptime_p),
                'load_average' => [
                    '1m' => $load1m,
                    '5m' => $load5m,
                    '15m' => $load15m,
                    'status' => $load_status,
                ],
                'ram' => $ram,
                'disk_root' => $disk,
                'cpu_cores' => $cores,
                'core_temperatures' => $coreTemps, // array of temps per core
            ];
        }

        $scu = ConfigEquipmentAFC::where('equipment_type_code', 'SCU')->get();
        $pg = ConfigEquipmentAFC::where('equipment_type_code', 'PG')->get();

        return view('pages.admin.monitoring-equipment-afc.index', compact([
            'scu',
            'pg',
            'results',
        ]));
    }

    public function show(string $id)
    {
        //
    }

    public function edit(string $id)
    {
        //
    }

    public function update(Request $request, string $id)
    {
        //
    }

    public function destroy(string $id)
    {
        //
    }
}
