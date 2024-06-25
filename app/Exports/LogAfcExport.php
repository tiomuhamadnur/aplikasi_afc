<?php

namespace App\Exports;

use App\Models\LogAFC;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class LogAfcExport implements FromView, ShouldAutoSize
{
    public function view(): View
    {
        $data = LogAFC::all();

        return view('pages.user.log-afc.export-excel', [
            'data' => $data,
        ]);
    }
}
