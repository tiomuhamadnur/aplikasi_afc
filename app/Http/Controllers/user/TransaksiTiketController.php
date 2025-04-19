<?php

namespace App\Http\Controllers\user;

use App\DataTables\TransaksiTiketDataTable;
use App\Http\Controllers\Controller;
use App\Models\ConfigEquipmentAFC;
use App\Models\ConfigPG;
use App\Models\LogAFC;
use App\Models\TransaksiTiket;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class TransaksiTiketController extends Controller
{
    public function index(TransaksiTiketDataTable $dataTable, Request $request)
    {
        $tap_in_station_code = $request->tap_in_station_code;
        $tap_out_station_code = $request->tap_out_station_code;
        $bank = $request->bank;
        $station_id = $request->station_id ?? null;
        $date = $request->date ?? null;
        $config_pg = ConfigPG::orderBy('order', 'ASC')->get();

        $banks = TransaksiTiket::distinct()->orderBy('card_type', 'ASC')->pluck('card_type');

        return $dataTable->with([
            'tap_in_station_code' => $tap_in_station_code,
            'tap_out_station_code' => $tap_out_station_code,
            'bank' => $bank,
            'date' => $date,
        ])->render('pages.user.transaksi-tiket.index', compact([
            'station_id',
            'date',
            'config_pg',
            'banks',
            'bank',
            'date',
            'tap_in_station_code',
            'tap_out_station_code',
        ]));
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

    // DEEPSEEK
    public function store(Request $request)
    {
        $request->validate([
            'is_deleted' => 'required|in:1,0',
            'station_id' => 'string|required',
            'date' => 'date|required',
        ]);

        // Persiapan data dasar
        $is_deleted = $request->is_deleted;
        $station_id = $request->station_id;
        $dateFilter = Carbon::parse($request->date)->format('Ymd');

        // Ambil config dengan cache
        $config_pg = Cache::remember("config_pg_{$station_id}", 3600, function() use ($station_id) {
            return ConfigPG::where('station_id', $station_id)->firstOrFail();
        });

        $scu = Cache::remember("scu_{$config_pg->station_code}", 3600, function() use ($config_pg) {
            return ConfigEquipmentAFC::where('equipment_type_code', 'SCU')
                                ->where('station_code', $config_pg->station_code)
                                ->firstOrFail();
        });

        // Setup koneksi SFTP yang dioptimasi
        $disk = Storage::build([
            'driver' => 'sftp',
            'host' => $scu->ip_address,
            'port' => (int)env('SFTP_SCU_PORT'),
            'username' => env('SFTP_SCU_USERNAME'),
            'password' => env('SFTP_SCU_PASSWORD'),
            'timeout' => 15,
        ]);

        $directory = '/home/sps/bank/alreadysent';

        // Hapus data lama jika diperlukan
        if ($is_deleted == 1) {
            TransaksiTiket::truncate();
        }

        // Proses file secara paralel
        $allFiles = collect($disk->allFiles($directory))
            ->filter(fn($file) => substr(basename($file), 10, 8) === $dateFilter);

        $stationCodeMap = Cache::remember('station_code_map', 3600, function() {
            return ConfigPG::pluck('station_code', 'station_kue_id')->toArray();
        });

        $results = $allFiles->chunk(100)->map(function($files) use ($disk, $stationCodeMap) {
            $inserted = 0;
            $skipped = 0;
            $problems = [];

            foreach ($files as $filePath) {
                $filename = basename($filePath);
                $fileProblems = 0;

                try {
                    $content = $disk->get($filePath);
                    $utf8Content = mb_convert_encoding($content, 'UTF-8', 'ISO-8859-1');
                    $cleanContent = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $utf8Content);

                    $transactions = array_filter(explode('|', $cleanContent));

                    $batch = [];
                    foreach ($transactions as $transaction) {
                        $parts = explode(';', $transaction);

                        if (count($parts) < 3) {
                            $skipped++;
                            $fileProblems++;
                            continue;
                        }

                        try {
                            $data1 = explode(',', $parts[2] ?? '');
                            $data2 = explode(',', $parts[3] ?? '');

                            $batch[] = [
                                'transaction_type' => 'KUE',
                                'transaction_id' => $parts[1] ?? 'UNKNOWN_' . uniqid(),
                                'device' => substr($filename, 0, 2),
                                'corner_id' => substr($filename, 6, 2),
                                'pg_id' => hexdec(substr($filename, 8, 2)),
                                'pan' => $data1[0] ?? null,
                                'transaction_amount' => $data1[1] ?? null,
                                'balance_before' => $data1[6] ?? null,
                                'balance_after' => $data1[7] ?? null,
                                'card_type' => $data1[9] ?? null,
                                'tap_in_time' => $data2[0] ?? null,
                                'tap_out_time' => $data2[2] ?? null,
                                'tap_in_station' => $stationCodeMap[$data2[1] ?? null] ?? null,
                                'tap_out_station' => $stationCodeMap[$data2[3] ?? null] ?? null,
                                'created_at' => now(),
                                'updated_at' => now()
                            ];
                        } catch (\Exception $e) {
                            $skipped++;
                            $fileProblems++;
                        }
                    }

                    if (!empty($batch)) {
                        TransaksiTiket::insert($batch);
                        $inserted += count($batch);
                    }

                    if ($fileProblems > 0) {
                        $problems[] = "$filename ($fileProblems masalah)";
                    }
                } catch (\Exception $e) {
                    $problems[] = $filename;
                    $skipped += count($transactions ?? []);
                }
            }

            return compact('inserted', 'skipped', 'problems');
        });

        // Agregasi hasil
        $totalInserted = $results->sum('inserted');
        $totalSkipped = $results->sum('skipped');
        $problematicFiles = $results->flatMap(function ($result) {
            return $result['problems'] ?? [];
        })->take(5)->toArray();

        // Format output sama persis seperti sebelumnya
        $message = sprintf('Import selesai. File: %d, Data masuk: %d, Di-skip: %d.',
            $allFiles->count(),
            $totalInserted,
            $totalSkipped
        );

        if ($totalSkipped > 0) {
            $message .= ' Contoh file bermasalah: ' . implode(', ', array_slice($problematicFiles, 0, 3));
        }

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
}
