<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\ConfigEquipmentAFC;
use App\Models\ConfigPG;
use App\Models\SamCard;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class IniFileController extends Controller
{
    public function index(Request $request)
    {
        $host = null;
        $station_id = null;
        $pg_id = null;
        $type = null;
        $results = [];
        $config_pg = ConfigPG::orderBy('order', 'ASC')->get();
        $sam_cards = SamCard::where('status', 'ready')->get();
        $equipments = ConfigEquipmentAFC::where('equipment_type_code', 'PG')->get();

        return view('pages.admin.ini-file.index', compact([
            'results',
            'config_pg',
            'sam_cards',
            'equipments',
            'host',
            'station_id',
            'pg_id',
            'type',
        ]));
    }

    public function update(Request $request)
    {
        $request->validate([
            'pg_id' => 'required|numeric',
            'filename' => 'required|string',
            'mandiri_pin' => 'required|string',
            'bni_mc' => 'required|string',
        ]);

        $pg_id = $request->pg_id;
        $filename = $request->filename;
        $mandiri_pin = $request->mandiri_pin;
        $bni_mc = $request->bni_mc;

        $directory = '/AG_System/Install/AINO/ini';
        $fullPath = $directory . '/' . $filename;

        $pg = ConfigEquipmentAFC::findOrFail($pg_id);

        $baseConfig['host'] = $pg->ip_address;
        $disk = Storage::build($baseConfig);

        // 1. Pastikan file benar-benar ada
        if (!$disk->exists($fullPath)) {
            return redirect()->route('ini-file.index')->withNotifyerror('File not found');
        }

        // 2. Ambil isi file
        $fileContent = $disk->get($fullPath);

        // 3. Decode JSON (validasi JSON dulu)
        $data = json_decode($fileContent, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return redirect()->route('ini-file.index')->withNotifyerror('Invalid JSON in file');
        }

        return $data;

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
        $request->validate([
            'pg_id' => 'required|string',
            'type' => 'nullable|in:Paid,UnPaid',
        ]);

        $pg_id = $request->pg_id;
        $type = $request->type;

        $pg = ConfigEquipmentAFC::where('equipment_type_code', 'PG')->findOrFail($pg_id);
        $station_id = ConfigPG::where('station_code', $pg->station_code)->firstOrFail()->station_id;
        $pg_id = $pg->equipment_id;

        // Direktori tempat file .ini berada
        $directory = '/AG_System/Install/AINO/ini';

        // Buat koneksi SFTP
        $baseConfig = config('filesystems.disks.sftp');
        $baseConfig['host'] = $pg->ip_address;
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

            // Cek file dengan pola: AinoConfiguration_123456789012_Paid.ini atau UnPaid.ini
            if (preg_match('/AinoConfiguration_(\d{12})_(Paid|UnPaid)\.ini$/i', $filename, $matches)) {
                $code = $matches[1]; // 12 digit kode dari nama file
                $fileType = $matches[2]; // Paid atau UnPaid

                // Ekstrak station dan pg id dari kode
                $station = substr($code, 3, 3); // digit ke-4 sampai ke-6
                $pg = substr($code, 9, 3); // digit ke-10 sampai ke-12

                // Filter berdasarkan station_id dan pg_id jika dikirim dari request
                if (($station_id && $station !== $station_id) || ($pg_id && $pg !== $pg_id)) {
                    continue;
                }

                // Filter berdasarkan type jika dikirim dari request
                if ($type && strtolower($type) !== strtolower($fileType)) {
                    continue;
                }

                // Ambil isi file jika lolos filter
                $fileContent = $disk->get($file);

                // Decode JSON
                $json = json_decode($fileContent, true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    continue;
                }

                // Tambahkan nama file ke hasil data
                $finalData = ['actual_filename' => $filename] + $json;

                $results[] = $finalData;
            }
        }

        if (empty($results)) {
            return redirect()->route('ini-file.index')->withNotifyerror('Data .ini file tidak ditemukan');
        }

        // return response()->json($results);

        $config_pg = ConfigPG::orderBy('order', 'ASC')->get();
        $sam_cards = SamCard::where('status', 'ready')->get();
        $equipments = ConfigEquipmentAFC::where('equipment_type_code', 'PG')->get();

        return view('pages.admin.ini-file.index', compact([
            'results',
            'config_pg',
            'sam_cards',
            'equipments',
            'host',
            'station_id',
            'pg_id',
            'type',
        ]));
    }
}
