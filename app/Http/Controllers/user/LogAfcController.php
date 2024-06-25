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
        //
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

        $filePath = $request->file('logfile')->getRealPath();
        $logs = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);




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

        foreach ($logs as $line) {
            if (strpos($line, 'elapsed time :') !== false) {
                $elapsedTime = $this->extractValue($line, 'elapsed time :');
                $PAN = null;
                $transactionSpeed = null;
            } elseif (strpos($line, 'PAN :') !== false) {
                if ($elapsedTime !== null) {
                    $PAN = $this->extractValue($line, 'PAN :');
                }
            } elseif (strpos($line, 'TRANSACTION SPEED :') !== false) {
                if ($elapsedTime !== null && $PAN !== null) {
                    $transactionSpeed = $this->extractValue($line, 'TRANSACTION SPEED :');
                }
            }

            if ($elapsedTime !== null && $PAN !== null && $transactionSpeed !== null) {
                LogAFC::create([
                    'elapsed_time' => $elapsedTime,
                    'PAN' => $PAN,
                    'transaction_speed' => $transactionSpeed
                ]);

                $elapsedTime = null;
                $PAN = null;
                $transactionSpeed = null;
            }
        }

        return redirect()->route('log.export');
    }

    private function extractValue($line, $prefix)
    {
        $value = trim(substr($line, strpos($line, $prefix) + strlen($prefix)));

        return str_replace(' s', '', $value);
    }

    public function export()
    {
        $waktu = Carbon::now()->format('Ymd');
        $name = '_Generated log file.xlsx';

        return Excel::download(new LogAfcExport(), $waktu . $name, \Maatwebsite\Excel\Excel::XLSX);
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
