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
        $validated = $request->validate([
            'pg_id' => 'required|string',
            'type' => 'nullable|in:Paid,Unpaid',
        ]);

        // Get PG configuration
        $pg = ConfigEquipmentAFC::where('equipment_type_code', 'PG')
            ->findOrFail($validated['pg_id']);

        // Get station ID (keeping original separate query as requested)
        $station_id = ConfigPG::where('station_code', $pg->station_code)
            ->firstOrFail()
            ->station_id;

        // Prepare SFTP connection
        $sftpConfig = config('filesystems.disks.sftp');
        $sftpConfig['host'] = $pg->ip_address;
        $sftp = Storage::build($sftpConfig);

        // Process files efficiently
        $results = collect($sftp->files('/AG_System/Install/AINO/ini'))
            ->filter(function ($file) {
                return Str::endsWith($file, '.ini');
            })
            ->mapWithKeys(function ($file) {
                return [basename($file) => $file];
            })
            ->filter(function ($file, $filename) use ($station_id, $pg, $validated) {
                if (!preg_match('/AinoConfiguration_(\d{12})_(Paid|Unpaid)\.ini$/i', $filename, $matches)) {
                    return false;
                }

                $code = $matches[1];
                $fileType = $matches[2];

                // Extract IDs from code
                $fileStationId = substr($code, 3, 3);
                $filePgId = substr($code, 9, 3);

                // Apply filters
                return $fileStationId === $station_id &&
                    $filePgId === $pg->equipment_id &&
                    (!isset($validated['type']) || strcasecmp($validated['type'], $fileType) === 0);
            })
            ->map(function ($file) use ($sftp, $pg) {
                $content = $sftp->get($file);
                $json = json_decode($content, true);

                return json_last_error() === JSON_ERROR_NONE
                    ? array_merge([
                        'station_code' => $pg->station_code,
                        'pg_id' => $pg->id,
                        'pg_name' => $pg->equipment_name,
                        'actual_filename' => basename($file),
                    ], $json)
                    : null;
            })
            ->filter()
            ->values()
            ->toArray();

        if (empty($results)) {
            return redirect()->route('ini-file.index')
                ->withNotifyerror('Data .ini file tidak ditemukan');
        }

        return view('pages.admin.ini-file.index', [
            'results' => $results,
            'config_pg' => ConfigPG::orderBy('order')->get(),
            'sam_cards' => SamCard::where('status', 'ready')->get(),
            'equipments' => ConfigEquipmentAFC::where('equipment_type_code', 'PG')->get(),
            'type' => $validated['type'] ?? null,
        ]);
    }
}
