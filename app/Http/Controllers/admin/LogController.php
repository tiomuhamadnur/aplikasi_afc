<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\ConfigEquipmentAFC;
use App\Models\ConfigPG;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class LogController extends Controller
{
    public function index()
    {
        $pgs = ConfigEquipmentAFC::where('equipment_type_code', 'PG')->get();
        $pg_id = null;
        $results = collect();

        return view('pages.admin.log.index', compact([
            'pgs',
            'pg_id',
            'results',
        ]));
    }

    public function store(Request $request)
    {
        // Validasi parameter
        $request->validate([
            'pg_id' => 'required|string',
        ]);

        $pg_id = $request->pg_id;

        $pg = ConfigEquipmentAFC::findOrFail($pg_id);

        // Direktori + nama file
        $path = '/AG_AplData/Maintenance/8001.txt';

        $baseConfig = config('filesystems.disks.sftp');
        $baseConfig['host'] = $pg->ip_address;
        $disk = Storage::build($baseConfig);

        // Cek apakah file ada
        if (!$disk->exists($path)) {
            return redirect()->route('log.index')->withNotifyerror('File log tidak ditemukan');
        }

        // Coba baca isi file
        try {
            $content = $disk->get($path);
        } catch (\Exception $e) {
            return redirect()
                ->route('log.index')
                ->withNotifyerror('Gagal membaca file log: ' . $e->getMessage());
        }

        // Parsing isi file
        $lines = explode("\n", $content);
        $results = [];

        foreach ($lines as $line) {
            $line = trim(preg_replace('/\s+/', ' ', $line)); // Normalize spasi
            if (empty($line)) continue;

            $parts = explode(' ', $line);

            if (count($parts) < 5) continue; // Minimal: date time code desc status

            $date = $parts[0];
            $time = $parts[1];
            $error_code = $parts[2];
            $status = array_pop($parts); // ambil kata terakhir sebagai status

            // Sisanya dari index 3 sampai sebelum terakhir = description
            $description_parts = array_slice($parts, 3);
            $description = implode(' ', $description_parts);

            $results[] = [
                'station_code' => $pg->station_code,
                'equipment_type' => $pg->equipment_type_code,
                'equipment_name' => $pg->equipment_name,
                'date' => $date,
                'time' => $time,
                'error_code' => $error_code,
                'description' => $description,
                'status' => $status,
            ];
        }

        // Cek kalau kosong
        if (empty($results)) {
            return redirect()->route('log.index')->withNotifyerror('Data log tidak ditemukan');
        }

        $pgs = ConfigEquipmentAFC::where('equipment_type_code', 'PG')->get();

        return view('pages.admin.log.index', compact([
            'pgs',
            'pg_id',
            'results',
        ]));
    }
}
