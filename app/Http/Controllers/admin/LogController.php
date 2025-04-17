<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\ConfigPG;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class LogController extends Controller
{
    public function index()
    {
        $station_id = null;
        $pg_id = null;
        $results = [];
        $config_pg = ConfigPG::orderBy('order', 'ASC')->get();

        return view('pages.admin.log.index', compact(['results', 'config_pg', 'station_id', 'pg_id']));
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        // Validasi parameter
        $request->validate([
            'station_id' => 'required|string',
            'pg_id' => 'required|string',
        ]);

        $station_id = $request->station_id;
        $pg_id = $request->pg_id;

        $config_pg = ConfigPG::where('station_id', $station_id)->firstOrFail();

        $host = Str::beforeLast($config_pg->ip_address, '.') . '.' . $pg_id;

        // Direktori + nama file
        $path = '/AG_AplData/Maintenance/8001.txt';

        $baseConfig = config('filesystems.disks.sftp');
        $baseConfig['host'] = $host;
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
            $line = trim(preg_replace('/\s+/', ' ', $line)); // Normalize spasi berlebih
            if (empty($line)) {
                continue;
            }

            $parts = explode(' ', $line, 6); // Ambil 6 bagian maksimum
            if (count($parts) < 6) {
                continue;
            }

            $results[] = [
                'date' => $parts[0],
                'time' => $parts[1],
                'error_code' => $parts[2],
                'description' => $parts[3] . ' ' . $parts[4],
                'status' => $parts[5],
            ];
        }

        // Cek kalau kosong
        if (empty($results)) {
            return redirect()->route('log.index')->withNotifyerror('Data log tidak ditemukan');
        }

        // Load config_pg buat dropdown atau kebutuhan tampilan
        $config_pg = ConfigPG::orderBy('order', 'ASC')->get();

        return view('pages.admin.log.index', compact(['results', 'config_pg', 'station_id', 'pg_id']));
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
