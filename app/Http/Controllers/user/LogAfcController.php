<?php

namespace App\Http\Controllers\user;

use App\Exports\LogAfcExport;
use App\Http\Controllers\Controller;
use App\Models\Bank;
use App\Models\LogAFC;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class LogAfcController extends Controller
{
    public function index()
    {
        return view('pages.user.log-afc.index');
    }

    public function import(Request $request)
    {
        $request->validate([
            'logfile' => 'required|file|mimetypes:text/plain,text/x-log,application/octet-stream',
        ], [
            'logfile.required' => 'Log file wajib diunggah.',
            'logfile.file' => 'File yang diunggah harus berupa file yang valid.',
            'logfile.mimetypes' => 'File yang diunggah harus dengan ekstensi .txt atau .log.',
        ]);

        $this->destroy();

        $elapsedTime = null;
        $debitAmount = null;
        $PAN = null;
        $transactionSpeed = null;
        $timeStamp = null;
        $bank = null;

        $filePath = $request->file('logfile')->getRealPath();
        $logs = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        foreach ($logs as $line) {
            if (strpos($line, 'elapsed time :') !== false) {
                $elapsedTime = $this->extractValue($line, 'elapsed time :');
                $timeStamp = $this->extractTimeStamp($line);
                $debitAmount = null;
                $PAN = null;
                $transactionSpeed = null;
                $bank = null;
            } elseif (strpos($line, 'Debit Amount :') !== false) {
                if ($elapsedTime !== null) {
                    $debitAmount = $this->extractValue($line, 'Debit Amount :');
                }
            } elseif (strpos($line, 'PAN :') !== false) {
                if ($elapsedTime !== null && $debitAmount !== null) {
                    $PAN = $this->extractValue($line, 'PAN :');
                    $bank = $this->getBankByPAN($PAN);
                }
            } elseif (strpos($line, 'TRANSACTION SPEED :') !== false) {
                if ($elapsedTime !== null && $debitAmount !== null && $PAN !== null) {
                    $transactionSpeed = $this->extractValue($line, 'TRANSACTION SPEED :');
                }
            }

            if ($elapsedTime !== null && $debitAmount !== null && $PAN !== null && $transactionSpeed !== null) {
                LogAFC::create([
                    'time_stamp' => $timeStamp,
                    'elapsed_time' => $elapsedTime,
                    'debit_amount' => $debitAmount,
                    'PAN' => $PAN,
                    'transaction_speed' => $transactionSpeed,
                    'bank' => $bank
                ]);

                $elapsedTime = null;
                $debitAmount = null;
                $PAN = null;
                $transactionSpeed = null;
                $timeStamp = null;
                $bank = null;
            }
        }

        return redirect()->route('log.export', [
            'tanggal' => $request->tanggal,
            'stasiun' => $request->stasiun,
            'nomor' => $request->nomor,
        ]);
    }

    public function import_convert(Request $request)
    {
        $request->validate([
            'logfile' => 'required|file',
        ], [
            'logfile.required' => 'Log file wajib diunggah.',
            'logfile.file' => 'File yang diunggah harus berupa file yang valid.',
        ]);

        $file = $request->file('logfile');

        $originalFilename = $file->getClientOriginalName();

        // Baca isi file
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
                'device' => substr($originalFilename, 0, 2),
                'station_code' => substr($originalFilename, 2, 4),
                'corner_id' => substr($originalFilename, 6, 2),
                'pg_id' => hexdec(substr($originalFilename, 8, 2)),
                'date' => Carbon::parse(substr($originalFilename, 10, 8))->format('Y-m-d'),
                'pan' => $parts[0],
                'transaction_id' => $parts[1],
                // 'pan_2' => $data1[0],
                'transaction_amount' => $data1[1],
                'balance_before' => $data1[6],
                'balance_after' => $data1[7],
                'card_type' => $data1[9],
                'tap_in_time' => $data2[0],
                'tap_in_station' => $data2[1],
                'tap_out_time' => $data2[2],
                'tap_out_station' => $data2[3],
            ];

            // Tambahkan data ke dalam array hasil
            $result[] = $item;
        }

        // Ubah array ke dalam format JSON
        $jsonResult = json_encode($result, JSON_PRETTY_PRINT);

        // Kembalikan hasil konversi dalam format JSON
        // return response()->json($jsonResult);

        echo '<pre>' . htmlspecialchars($jsonResult) . '</pre>';

    }

    private function extractValue($line, $prefix)
    {
        $value = trim(substr($line, strpos($line, $prefix) + strlen($prefix)));

        return str_replace(' s', '', $value);
    }

    private function extractTimeStamp($line) {
        // Extract the timestamp from the start of the line
        preg_match('/\[(.*?)\] (\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})/', $line, $matches);
        if (isset($matches[2])) {
            // Parse the matched timestamp using Carbon and format it
            return Carbon::parse($matches[2])->format('Y-m-d H:i:s');
        }
        return null;
    }

    private function getBankByPAN($PAN)
    {
        $banks = Bank::all();

        $bankCodes = $banks->pluck('code', 'nomor')->toArray();

        $prefix = substr($PAN, 0, 4);

        return $bankCodes[$prefix] ?? 'Unknown';
    }

    public function export(Request $request)
    {
        $waktu = Carbon::parse($request->tanggal)->format('Ymd');
        $stasiun = $request->stasiun;
        $nomor = $request->nomor;
        $name = $waktu . '_' . $stasiun . '_' . $nomor . '_Generated log file.xlsx';

        return Excel::download(new LogAfcExport(), $name, \Maatwebsite\Excel\Excel::XLSX);
    }

    public function store(Request $request)
    {
        //
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

    public function destroy()
    {
        LogAFC::truncate();
    }
}
