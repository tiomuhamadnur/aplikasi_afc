<?php

namespace App\Http\Controllers\user;

use App\Http\Controllers\Controller;
use App\Models\TransaksiTiket;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TransaksiTiketController extends Controller
{
    public function index()
    {
        $transaksi = TransaksiTiket::orderByDesc('tap_out_time')->get();
        return view('pages.user.transaksi-tiket.index', compact([
            'transaksi'
        ]));
    }

    public function create()
    {
        //
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

            TransaksiTiket::create($item);

            // Tambahkan data ke dalam array hasil
            $result[] = $item;
        }

        return redirect()->route('transaksi.tiket.index');

        // Ubah array ke dalam format JSON
        // $jsonResult = json_encode($result, JSON_PRETTY_PRINT);

        // Kembalikan hasil konversi dalam format JSON
        // return response()->json($jsonResult);

        // echo '<pre>' . htmlspecialchars($jsonResult) . '</pre>';
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
