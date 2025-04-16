<?php

namespace App\Http\Controllers\user;

use App\DataTables\TransaksiTiketDataTable;
use App\Http\Controllers\Controller;
use App\Models\ConfigPG;
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
        $station_id = null;
        $date = null;
        $config_pg = ConfigPG::orderBy('order', 'ASC')->get();

        return $dataTable->render('pages.user.transaksi-tiket.index', compact(['station_id', 'date', 'config_pg']));
    }
    public function ftp()
    {
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
            $uploadedFile = new UploadedFile($tempPath, basename($file), mime_content_type($tempPath), null, true);

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

    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'station_id' => 'string|required',
    //         'date' => 'date|required',
    //     ]);

    //     $station_id = $request->station_id;
    //     $date = $request->date;

    //     $config_pg = ConfigPG::where('station_id', $station_id)->firstOrFail();
    //     $host = Str::beforeLast($config_pg->ip_address, '.') . '.161';

    //     $directory = '/home/sps/bank/alreadysent';

    //     $baseConfig = config('filesystems.disks.sftp');
    //     $baseConfig['host'] = 'localhost';
    //     $baseConfig['port'] = (int)2222;
    //     $baseConfig['password'] = 'spsadministrator';

    //     $disk = Storage::build($baseConfig);

    //     $allFiles = $disk->allFiles($directory);

    //     $dateFilter = Carbon::parse($date)->format('Ymd');

    //     // Filter file berdasarkan tanggal
    //     $filteredFiles = array_filter($allFiles, function ($file) use ($dateFilter) {
    //         $filename = basename($file);
    //         $fileDate = substr($filename, 10, 8);
    //         return $fileDate === $dateFilter;
    //     });

    //     // (Optional) Batasi jumlah file
    //     // $filteredFiles = array_slice($filteredFiles, 0, 100);

    //     $result = [];

    //     foreach ($filteredFiles as $file) {
    //         try {
    //             $filename = basename($file);

    //             $size = $disk->size($file);
    //             $lastModified = $disk->lastModified($file);

    //             $result[] = [
    //                 'filename' => $filename,
    //                 'size' => $size,
    //                 'last_modified' => Carbon::createFromTimestamp($lastModified)->toDateTimeString(),
    //             ];
    //         } catch (\Exception $e) {
    //             $result[] = [
    //                 'filename' => $filename,
    //                 'error' => $e->getMessage(),
    //             ];
    //         }
    //     }

    //     return response()->json($result);
    // }


    public function store(Request $request)
    {
        $request->validate([
            'station_id' => 'string|required',
            'date' => 'date|required',
        ]);

        $station_id = $request->station_id;
        $date = $request->date;

        $config_pg = ConfigPG::where('station_id', $station_id)->firstOrFail();
        $host = Str::beforeLast($config_pg->ip_address, '.') . '.161';

        $directory = '/home/sps/bank/alreadysent';

        $baseConfig = config('filesystems.disks.sftp');

        $baseConfig['host'] = $host;
        $baseConfig['port'] = (int) env("SFTP_SCU_PORT");
        $baseConfig['username'] = env("SFTP_SCU_USERNAME");
        $baseConfig['password'] = env("SFTP_SCU_PASSWORD");

        $disk = Storage::build($baseConfig);

        // Ambil semua file dari SFTP
        $allFiles = $disk->allFiles($directory);
        $dateFilter = Carbon::parse($date)->format('Ymd');

        // Filter berdasarkan tanggal pada nama file
        $filteredFiles = array_filter($allFiles, function ($file) use ($dateFilter) {
            $filename = basename($file);
            return substr($filename, 10, 8) === $dateFilter;
        });

        $now = now();
        $totalInserted = 0;

        // Hapus semua data dulu
        TransaksiTiket::truncate();

        foreach ($filteredFiles as $filePath) {
            try {
                $filename = basename($filePath);
                $rawContent = $disk->get($filePath);

                // Coba beberapa encoding umum sebelum menggunakan UTF-8
                $encodings = ['ISO-8859-1', 'Windows-1252', 'UTF-8'];
                $utf8Data = false;

                foreach ($encodings as $encoding) {
                    $convertedData = @mb_convert_encoding($rawContent, 'UTF-8', $encoding);
                    if ($convertedData !== false) {
                        $utf8Data = $convertedData;
                        break;
                    }
                }

                if ($utf8Data === false) {
                    continue;
                }

                // Potong berdasarkan delimiter blok transaksi
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
                        'device' => substr($filename, 0, 2),
                        'corner_id' => substr($filename, 6, 2),
                        'pg_id' => hexdec(substr($filename, 8, 2)),
                        'pan' => $data1[0] ?? null,
                        'transaction_amount' => $data1[1] ?? null,
                        'balance_before' => $data1[6] ?? null,
                        'balance_after' => $data1[7] ?? null,
                        'card_type' => $data1[9] ?? null,
                        'tap_in_time' => $data2[0] ?? null,
                        'tap_in_station' => $data2[1] ?? null,
                        'tap_out_time' => $data2[2] ?? null,
                        'tap_out_station' => $data2[3] ?? null,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];

                    // Mengecek apakah data sudah ada di database
                    $existingEntry = TransaksiTiket::where('transaction_id', $item['transaction_id'])->first();

                    if (!$existingEntry) {
                        TransaksiTiket::create($item);
                        $totalInserted++;
                    }
                }

            } catch (\Exception $e) {
                continue;
            }
        }

        // Menampilkan hasil ke user
        $message = sprintf(
            'Import semua file berhasil. Jumlah file diproses: %d, Total data dimasukkan: %d',
            count($filteredFiles),
            $totalInserted
        );

        return redirect()->route('transaksi.tiket.index')->withNotify($message);
    }



    public function import(Request $request)
    {
        $request->validate(
            [
                'logfile' => 'required|file',
            ],
            [
                'logfile.required' => 'Log file wajib diunggah.',
                'logfile.file' => 'File yang diunggah harus berupa file yang valid.',
            ],
        );

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
