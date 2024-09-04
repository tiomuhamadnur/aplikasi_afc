<?php

namespace App\Exports;

use App\Models\Checksheet;
use App\Models\Equipment;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class HistoryChecksheetExport implements FromView, ShouldAutoSize
{
    public $uuid_equipment;

    public function __construct(?string $uuid_equipment = null)
    {
        $this->uuid_equipment = $uuid_equipment;
    }

    public function view(): View
    {
        $checksheetData = Checksheet::whereRelation('equipment', 'uuid', $this->uuid_equipment)
            ->whereHas('parameter', function ($query) {
                $query->whereNull('deleted_at');
            })
            ->with(['parameter' => function ($query) {
                $query->select('id', 'name', 'urutan', 'tipe', 'satuan_id', 'min_value', 'max_value')
                    ->with('satuan'); // Pastikan satuan juga diambil jika diperlukan
            }, 'work_order'])
            ->get();

        if ($checksheetData->isEmpty()) {
            return redirect()->back()->withNotifyerror('Data tidak ditemukan');
        }

        $pivotData = $checksheetData->groupBy('work_order.date')->map(function ($items) {
            return $items->sortBy(function ($item) {
                return $item->parameter->urutan;
            })->mapWithKeys(function ($item) {
                return [$item->parameter->name => $item->value];
            });
        });


        $parameters = $checksheetData->pluck('parameter')->unique('id')->sortBy('urutan');
        $equipment = Equipment::where('uuid', $this->uuid_equipment)->firstOrFail();

        return view('pages.user.checksheet.export.excel', [
            'equipment' => $equipment,
            'pivotData' => $pivotData,
            'parameters' => $parameters,
        ]);
    }
}
