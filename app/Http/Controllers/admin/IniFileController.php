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
        $pg_id = null;
        $results = [];
        $config_pg = ConfigPG::orderBy('order', 'ASC')->get();
        $sam_cards = SamCard::where('status', 'ready')->get();
        $equipments = ConfigEquipmentAFC::where('equipment_type_code', 'PG')->get();

        return view('pages.admin.ini-file.index', compact(['results', 'config_pg', 'sam_cards', 'equipments', 'pg_id', 'type']));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'pg_id' => 'required|numeric',
            'filename' => 'required|string',
            'sam_card_id' => 'required|numeric',
        ]);

        // 1. Get PG and SAM Card Data
        $pg = ConfigEquipmentAFC::findOrFail($validated['pg_id']);
        $samCard = SamCard::findOrFail($validated['sam_card_id']);

        // 2. Setup SFTP Connection
        $sftpConfig = config('filesystems.disks.sftp');
        $sftpConfig['host'] = $pg->ip_address;
        $sftp = Storage::build($sftpConfig);

        $baseDir = '/AG_System/Install/AINO/ini';
        $backupDir = $baseDir . '/BACKUP';
        $originalPath = $baseDir . '/' . $validated['filename'];

        // 3. Validate Original File
        if (!$sftp->exists($originalPath)) {
            return response()->json(
                [
                    'status' => 'error',
                    'message' => 'Original file not found',
                ],
                404,
            );
        }

        // 4. Create BACKUP directory if not exists
        if (!$sftp->exists($backupDir)) {
            $sftp->makeDirectory($backupDir);
        }

        // 5. Read Original File
        $originalContent = $sftp->get($originalPath);
        $data = json_decode($originalContent, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return response()->json(
                [
                    'status' => 'error',
                    'message' => 'Invalid JSON format in original file',
                ],
                400,
            );
        }

        // 6. Create Backup Data
        $backupData = $data;

        // Update values from SAM Card
        $changes = [];
        if (isset($backupData['Mandiri'])) {
            $changes['Mandiri.pin'] = [
                'from' => $backupData['Mandiri']['pin'] ?? null,
                'to' => $samCard->pin,
            ];
            $backupData['Mandiri']['pin'] = $samCard->pin;
        }

        if (isset($backupData['BNI'])) {
            $changes['BNI.mc'] = [
                'from' => $backupData['BNI']['mc'] ?? null,
                'to' => $samCard->mc,
            ];
            $backupData['BNI']['mc'] = $samCard->mc;
        }

        // 7. Generate Backup Filename (with .ini extension)
        $originalName = pathinfo($validated['filename'], PATHINFO_FILENAME);
        $backupFilename = $originalName . '_BACKUP_' . date('Ymd_His') . '.ini';
        $backupPath = $backupDir . '/' . $backupFilename;

        // 8. Save Backup File
        $sftp->put($backupPath, json_encode($backupData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

        // 9. Verify Both Files
        $verification = [
            'original' => [
                'exists' => $sftp->exists($originalPath),
                'size' => $sftp->size($originalPath),
                'md5' => md5($originalContent),
            ],
            'backup' => [
                'exists' => $sftp->exists($backupPath),
                'size' => $sftp->size($backupPath),
                'md5' => md5(json_encode($backupData)),
            ],
        ];

        return response()->json([
            'status' => 'success',
            'message' => 'Backup file created successfully with .ini extension',
            'original_file' => [
                'path' => $originalPath,
                'filename' => $validated['filename'],
                'size' => $verification['original']['size'],
                'last_modified' => $sftp->lastModified($originalPath),
            ],
            'backup_file' => [
                'path' => $backupPath,
                'filename' => $backupFilename,
                'changes_applied' => $changes,
                'size' => $verification['backup']['size'],
            ],
            'verification' => $verification,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'pg_id' => 'required|string',
            'type' => 'nullable|in:Paid,UnPaid',
        ]);

        // Get PG configuration
        $pg = ConfigEquipmentAFC::where('equipment_type_code', 'PG')->findOrFail($validated['pg_id']);

        // Get station ID (keeping original separate query as requested)
        $station_id = ConfigPG::where('station_code', $pg->station_code)->firstOrFail()->station_id;

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
                if (!preg_match('/AinoConfiguration_(\d{12})_(Paid|UnPaid)\.ini$/i', $filename, $matches)) {
                    return false;
                }

                $code = $matches[1];
                $fileType = $matches[2];

                // Extract IDs from code
                $fileStationId = substr($code, 3, 3);
                $filePgId = substr($code, 9, 3);

                // Apply filters
                return $fileStationId === $station_id && $filePgId === $pg->equipment_id && (!isset($validated['type']) || strcasecmp($validated['type'], $fileType) === 0);
            })
            ->map(function ($file) use ($sftp, $pg) {
                $content = $sftp->get($file);
                $json = json_decode($content, true);

                return json_last_error() === JSON_ERROR_NONE
                    ? array_merge(
                        [
                            'station_code' => $pg->station_code,
                            'pg_id' => $pg->id,
                            'pg_name' => $pg->equipment_name,
                            'actual_filename' => basename($file),
                        ],
                        $json,
                    )
                    : null;
            })
            ->filter()
            ->values()
            ->toArray();

        if (empty($results)) {
            return redirect()->route('ini-file.index')->withNotifyerror('Data .ini file tidak ditemukan');
        }

        return view('pages.admin.ini-file.index', [
            'results' => $results,
            'config_pg' => ConfigPG::orderBy('order')->get(),
            'sam_cards' => SamCard::where('status', 'ready')->get(),
            'equipments' => ConfigEquipmentAFC::where('equipment_type_code', 'PG')->get(),
            'pg_id' => $validated['pg_id'],
            'type' => $validated['type'] ?? null,
        ]);
    }
}
