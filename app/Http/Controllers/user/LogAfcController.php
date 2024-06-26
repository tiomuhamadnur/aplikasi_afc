<?php

namespace App\Http\Controllers\user;

use App\Exports\LogAfcExport;
use App\Http\Controllers\Controller;
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

        // $filePath = $request->file('logfile')->getRealPath();
        // $logs = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);




        // $countElapsedTime = 0;
        // $countPAN = 0;
        // $countTransactionSpeed = 0;

        // foreach ($logs as $line) {
        //     if (strpos($line, 'elapsed time :') !== false) {
        //         $countElapsedTime++;
        //     } elseif (strpos($line, 'PAN :') !== false) {
        //         $countPAN++;
        //     } elseif (strpos($line, 'TRANSACTION SPEED :') !== false) {
        //         $countTransactionSpeed++;
        //     }
        // }

        // $hasil = 'ET: ' . $countElapsedTime . ' - PAN: ' . $countPAN . ' - TS : ' . $countTransactionSpeed;
        // dd($hasil);







        $elapsedTime = null;
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
                $PAN = null;
                $transactionSpeed = null;
                $bank = null;
            } elseif (strpos($line, 'PAN :') !== false) {
                if ($elapsedTime !== null) {
                    $PAN = $this->extractValue($line, 'PAN :');
                    $bank = $this->getBankByPAN($PAN);
                }
            } elseif (strpos($line, 'TRANSACTION SPEED :') !== false) {
                if ($elapsedTime !== null && $PAN !== null) {
                    $transactionSpeed = $this->extractValue($line, 'TRANSACTION SPEED :');
                }
            }

            if ($elapsedTime !== null && $PAN !== null && $transactionSpeed !== null) {
                LogAFC::create([
                    'time_stamp' => $timeStamp,
                    'elapsed_time' => $elapsedTime,
                    'PAN' => $PAN,
                    'transaction_speed' => $transactionSpeed,
                    'bank' => $bank
                ]);

                $elapsedTime = null;
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
        $bankCodes = [
            '7546' => 'BNI',
            '0145' => 'BCA',
            '6032' => 'MDR',
            '6013' => 'BRI',
            '9360' => 'BDKI'
        ];

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
