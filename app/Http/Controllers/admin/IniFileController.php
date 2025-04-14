<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class IniFileController extends Controller
{
    public function index(Request $request)
    {
        $results = [];
        return view('pages.admin.ini-file.index', compact('results'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'filename' => 'required|string',
            'mandiri_pin' => 'required|string',
            'bni_mc' => 'required|string',
        ]);

        $disk = Storage::disk('sftp');
        $directory = '/AG_System/Install/AINO/ini';
        $filename = $request->filename;
        $mandiri_pin = $request->mandiri_pin;
        $bni_mc = $request->bni_mc;
        $fullPath = $directory . '/' . $filename;

        // 1. Pastikan file benar-benar ada
        if (!$disk->exists($fullPath)) {
            return response()->json(['error' => 'File not found'], 404);
        }

        // 2. Ambil isi file
        $fileContent = $disk->get($fullPath);

        // 3. Decode JSON (validasi JSON dulu)
        $data = json_decode($fileContent, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return response()->json(['error' => 'Invalid JSON in file'], 422);
        }

        // 4. Ubah isi jika ada input
        if ($request->filled('mandiri_pin') && isset($data['Mandiri'])) {
            $data['Mandiri']['pin'] = $mandiri_pin;
        }

        if ($request->filled('bni_mc') && isset($data['BNI'])) {
            $data['BNI']['mc'] = $bni_mc;
        }

        // 5. Encode ulang dan simpan
        $updatedContent = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        $disk->put($fullPath, $updatedContent);

        // 6. Response OK
        return response()->json([
            'message' => 'File .ini updated successfully',
            'filename' => $filename,
            'updated' => [
                'Mandiri.pin' => $mandiri_pin,
                'BNI.mc' => $bni_mc,
            ],
        ]);
    }

    public function store(Request $request)
    {
        // Validasi parameter
        $request->validate([
            'host' => 'required|string|ip',
            'station_id' => 'nullable|string',
            'pg_id' => 'nullable|string',
            'type' => 'nullable|in:Paid,UnPaid',
        ]);

        $host = $request->host;
        $station_id = $request->station_id;
        $pg_id = $request->pg_id;
        $type = $request->type;

        // Direktori tempat file .ini berada
        $directory = '/AG_System/Install/AINO/ini';

        // Buat koneksi SFTP
        $baseConfig = config('filesystems.disks.sftp');
        $baseConfig['host'] = $host;
        $disk = Storage::build($baseConfig);

        // Ambil semua file di dalam direktori
        $allFiles = $disk->allFiles($directory);

        $results = [];

        foreach ($allFiles as $file) {
            $filename = basename($file);

            // Filter hanya file dengan ekstensi .ini
            if (!Str::endsWith($filename, '.ini')) {
                continue;
            }

            // Cari 12 digit angka dari nama file
            if (preg_match('/AinoConfiguration_(\d{12})_/', $filename, $matches)) {
                $code = $matches[1];
                $station = substr($code, 3, 3); // angka ke-4 s.d. ke-6
                $pg = substr($code, 9, 3); // angka ke-10 s.d. ke-12

                if (($station_id && $station !== $station_id) || ($pg_id && $pg !== $pg_id)) {
                    continue;
                }

                // Ekstrak 'type' dari nama file (Paid atau UnPaid)
                $filenameParts = explode('_', pathinfo($filename, PATHINFO_FILENAME));
                $fileType = end($filenameParts); // Ambil bagian terakhir sebelum .ini

                // Filter berdasarkan type (jika ada)
                if ($type && strtolower($type) !== strtolower($fileType)) {
                    continue;
                }

                // Ambil isi file jika lolos filter
                $fileContent = Storage::disk('sftp')->get($file);

                // Decode JSON
                $json = json_decode($fileContent, true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    continue;
                }

                // Tambahkan actual_filename di urutan pertama
                $finalData = ['actual_filename' => $filename] + $json;

                $results[] = $finalData;
            }
        }

        // return response()->json($results);
        return view('pages.admin.ini-file.index', compact($results));
    }
}
