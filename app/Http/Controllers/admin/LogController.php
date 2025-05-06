<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\ConfigEquipmentAFC;
use App\Models\ConfigPG;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use PharData;
use RecursiveIteratorIterator;
use ZipArchive;

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


    public function logAinoDownlaod(Request $request)
    {
        $validated = $request->validate([
            'pg_id' => 'required|string',
            'date' => 'required|date',
        ]);

        $tanggal = Carbon::parse($validated['date'])->format('Ymd');

        // Ambil konfigurasi PG
        $pg = ConfigEquipmentAFC::where('equipment_type_code', 'PG')->findOrFail($validated['pg_id']);

        // SFTP setup
        $sftpConfig = config('filesystems.disks.sftp');
        $sftpConfig['host'] = $pg->ip_address;
        $sftp = Storage::build($sftpConfig);

        $logRoot = '/AG_BackupSys/LogBack';
        $logDirs = collect($sftp->directories($logRoot))
            ->filter(fn($dir) => Str::startsWith(basename($dir), 'LogSedai'));

        $filesToZip = [];

        foreach ($logDirs as $dir) {
            $subDirs = collect($sftp->directories($dir))
                ->filter(fn($sub) => Str::startsWith(basename($sub), $tanggal)); // YYYYMMDD match

            foreach ($subDirs as $sub) {
                $tarName = basename($sub) . '.tar.gz';
                $tarPath = $sub . '/' . $tarName;

                if (!$sftp->exists($tarPath)) continue;

                // Ambil konten .tar.gz dari SFTP
                $tarContent = $sftp->get($tarPath);
                if (!$tarContent) continue;

                // Simpan sementara ke memory
                $tmpTarGz = tempnam(sys_get_temp_dir(), 'log_') . '.tar.gz';
                file_put_contents($tmpTarGz, $tarContent);

                // Ekstrak AINO.log
                try {
                    $phar = new PharData($tmpTarGz);
                    $phar->decompress(); // .tar

                    $tarFile = str_replace('.gz', '', $tmpTarGz);
                    $untar = new PharData($tarFile);

                    foreach (new RecursiveIteratorIterator($untar) as $file) {
                        if (Str::endsWith($file->getFilename(), 'AINO.log')) {
                            $logContent = file_get_contents($file->getPathname());

                            $equipmentName = str_replace(' ', '_', $pg->equipment_name);
                            $stationCode = str_replace(' ', '_', $pg->station_code);
                            $timestamp = basename($sub);

                            $logFilename = "{$stationCode}_{$equipmentName}_{$timestamp}_AINO.log";
                            $filesToZip[$logFilename] = $logContent;
                        }
                    }
                } catch (\Exception $e) {
                    // Gagal ekstrak
                    continue;
                } finally {
                    // Cleanup sementara
                    @unlink($tmpTarGz);
                    @unlink($tarFile ?? '');
                }
            }
        }

        if (empty($filesToZip)) {
            return back()->withNotifyerror('Tidak ada file AINO.log ditemukan untuk tanggal tersebut.');
        }

        // Buat ZIP in-memory
        $zipName = "{$pg->station_code}_{$pg->equipment_name}_" . $tanggal . ".zip";
        $tmpZip = tempnam(sys_get_temp_dir(), 'zip_');
        $zip = new ZipArchive();
        $zip->open($tmpZip, ZipArchive::CREATE | ZipArchive::OVERWRITE);

        foreach ($filesToZip as $filename => $content) {
            $zip->addFromString($filename, $content);
        }

        $zip->close();

        return response()->download($tmpZip, $zipName)->deleteFileAfterSend(true);
    }
}
