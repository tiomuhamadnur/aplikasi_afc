<?php

namespace App\Http\Controllers\user;

use App\DataTables\TransaksiTiketDataTable;
use App\Http\Controllers\Controller;
use App\Models\LogAFC;
use App\Models\TransaksiTiket;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class TransaksiTiketController extends Controller
{
    public function index(TransaksiTiketDataTable $dataTable)
    {
        return $dataTable->render('pages.user.transaksi-tiket.index');
    }
    public function ftp()
    {
        // dd('ok');
        // Ambil semua file dari direktori tertentu
        set_time_limit(300);
        $allFiles = Storage::disk('ftp')->allFiles('/contoh_ftp');
        // dd($allFiles);

        // Iterasi setiap file dan ambil kontennya
        foreach ($allFiles as $file) {
            $fileContent = Storage::disk('ftp')->get($file);

            // Buat file sementara
            $tempPath = tempnam(sys_get_temp_dir(), 'ftp');
            file_put_contents($tempPath, $fileContent);

            // Buat instance UploadedFile dari file sementara
            $uploadedFile = new UploadedFile(
                $tempPath,
                basename($file),
                mime_content_type($tempPath),
                null,
                true
            );

            // Buat request dan tambahkan file ke dalamnya
            $request = new Request();
            $request->files->set('logfile', $uploadedFile);

            // Panggil metode import dengan request yang baru dibuat
            $this->import($request);

            // Hapus file sementara setelah digunakan
            unlink($tempPath);
        }

        return redirect()->route('transaksi.tiket.index');
    }

    public function ini_file(Request $request)
    {
        $directory = "/AG_System/Install/AINO/ini";
        $allFiles = Storage::disk('sftp')->allFiles($directory);

        $results = [];

        foreach ($allFiles as $file) {
            $filename = basename($file);

            // Filter hanya file dengan ekstensi .ini
            if (!Str::endsWith($filename, '.ini')) {
                continue;
            }

            // Ambil isi file
            $fileContent = Storage::disk('sftp')->get($file);

            // Pastikan isinya valid JSON
            $json = json_decode($fileContent, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                continue; // skip jika bukan JSON valid
            }

            // Simpan isi asli (bukan path, bukan nama saja)
            $results[] = $json;
        }

        return response()->json($results);
    }

    public function store(Request $request)
    {
        //
    }

    public function import(Request $request)
    {
        $request->validate([
            'logfile' => 'required|file',
        ], [
            'logfile.required' => 'Log file wajib diunggah.',
            'logfile.file' => 'File yang diunggah harus berupa file yang valid.',
        ]);

        $file = $request->file('logfile');

        $originalFilename = $file->getClientOriginalName();

        $asciiData = file_get_contents($file->getRealPath());

        // Coba beberapa encoding umum sebelum menggunakan UTF-8
        $encodings = ['ISO-8859-1', 'Windows-1252', 'UTF-8'];
        $utf8Data = false;

        foreach ($encodings as $encoding) {
            $convertedData = @mb_convert_encoding($asciiData, 'UTF-8', $encoding);
            if ($convertedData !== false) {
                $utf8Data = $convertedData;
                break;
            }
        }

        if ($utf8Data === false) {
            return response()->json(['error' => 'Unable to detect character encoding'], 400);
        }

        $pattern = '/(\d+;[0-9A-F\-]+;\d+,\d+\.\d+,.*?)(?=0000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000)/s';

        preg_match_all($pattern, $utf8Data, $matches);

        $extractedData = $matches[0];

        $result = [];

        foreach ($extractedData as $key => $data) {
            $parts = explode(';', $data);

            $data1 = explode(',', $parts[2]);
            $data2 = explode(',', $parts[3]);

            $item = [
                'transaction_type' => 'KUE',
                'transaction_id' => $parts[1],
                'device' => substr($originalFilename, 0, 2),
                'corner_id' => substr($originalFilename, 6, 2),
                // 'station_code' => substr($originalFilename, 2, 4),
                'pg_id' => hexdec(substr($originalFilename, 8, 2)),
                // 'date' => Carbon::parse(substr($originalFilename, 10, 8))->format('Y-m-d'),
                // 'pan' => $parts[0],
                'pan' => $data1[0],
                'transaction_amount' => $data1[1],
                'balance_before' => $data1[6],
                'balance_after' => $data1[7],
                'card_type' => $data1[9],
                'tap_in_time' => $data2[0],
                'tap_in_station' => $data2[1],
                'tap_out_time' => $data2[2],
                'tap_out_station' => $data2[3],
            ];

            $existingEntry = TransaksiTiket::where('transaction_id', $item['transaction_id'])->first();

            if (!$existingEntry) {
                TransaksiTiket::create($item);
            } else {
                // Jika entry sudah ada, skip atau lakukan tindakan lainnya
            }

            $result[] = $item;
        }

        return redirect()->route('transaksi.tiket.index');
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
