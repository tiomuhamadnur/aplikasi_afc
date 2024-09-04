<?php

namespace App\Http\Controllers\user;

use App\DataTables\ChecksheetDataTable;
use App\Exports\HistoryChecksheetExport;
use App\Http\Controllers\Controller;
use App\Models\Checksheet;
use App\Models\Equipment;
use App\Models\Form;
use App\Models\Parameter;
use App\Models\TipeEquipment;
use App\Models\TransWorkOrderEquipment;
use App\Models\WorkOrder;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ChecksheetController extends Controller
{
    public function index()
    {
        //
    }

    public function create($uuid_work_order, $uuid_equipment)
    {
        $work_order = WorkOrder::where('uuid', $uuid_work_order)->firstOrFail();
        $equipment = Equipment::where('uuid', $uuid_equipment)->firstOrFail();
        $tipe_equipment = TipeEquipment::where('uuid', $equipment->tipe_equipment->uuid)->firstOrFail();

        $parameter = Parameter::whereRelation('form.tipe_equipment', 'id', '=', $tipe_equipment->id)
                    ->orderBy('urutan', 'ASC')
                    ->get();

        return view('pages.user.checksheet.create', compact([
            'work_order',
            'tipe_equipment',
            'equipment',
            'parameter',
        ]));
    }

    public function store(Request $request)
    {
        $request->validate([
            "work_order_id" => 'required|numeric',
            "equipment_id" => 'required|numeric',
            "values" => 'required|array',
            "parameter_ids" => 'required|array',
            'parameter_ids.*' => 'numeric',
        ]);

        $work_order = WorkOrder::findOrFail($request->work_order_id);
        $equipment = Equipment::findOrFail($request->equipment_id);

        foreach ($request->values as $index => $value) {
            $status = $this->checkTolerance((int) $request->parameter_ids[$index], $value);
            Checksheet::create([
                'work_order_id' => $work_order->id,
                'equipment_id' => $equipment->id,
                'parameter_id' => $request->parameter_ids[$index],
                'value' => $value,
                'user_id' => auth()->user()->id,
                'status' => $status,
            ]);
        }

        $data = TransWorkOrderEquipment::where('work_order_id', $work_order->id)
                                ->where('equipment_id', $equipment->id)
                                ->firstOrFail();

        $data->update([
            'status' => 'completed'
        ]);

        return redirect()->route('work-order.equipment', $work_order->uuid)
                        ->withNotify('Data checksheet ' . $equipment->name . ' berhasil ditambahkan');
    }

    private function checkTolerance(int $parameter_id, $value)
    {
        $status = null;
        $parameter = Parameter::findOrFail($parameter_id);
        if($parameter->tipe == 'number')
        {
            if($value >= $parameter->min_value && $value <= $parameter->max_value)
            {
                $status = 'In tolerance';
            } else {
                $status = 'Out of tolerance';
            }
        }

        return $status;
    }

    public function history($uuid_equipment)
    {
        $checksheetData = Checksheet::whereRelation('equipment', 'uuid', $uuid_equipment)
            ->whereHas('parameter', function ($query) {
                $query->whereNull('deleted_at');
            })
            ->with(['parameter' => function ($query) {
                $query->select('id', 'uuid', 'name', 'urutan', 'tipe', 'satuan_id', 'min_value', 'max_value')
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

        $equipment = Equipment::where('uuid', $uuid_equipment)->firstOrFail();

        return view('pages.user.checksheet.history', compact([
            'equipment',
            'pivotData',
            'parameters'
        ]));
    }

    public function export_excel($uuid_equipment)
    {
        $equipment = Equipment::where('uuid', $uuid_equipment)->firstOrFail();

        $waktu = Carbon::now()->format('Ymd');

        return Excel::download(new HistoryChecksheetExport($uuid_equipment), $waktu . '_data history checksheet_' . $equipment->name .'.xlsx', \Maatwebsite\Excel\Excel::XLSX);
    }

    public function trend(Request $request)
    {
        $eq_uuid = $request->eq_uuid;
        $param_uuid = $request->param_uuid;

        $data = Checksheet::whereRelation('equipment', 'uuid', $eq_uuid)
            ->whereRelation('parameter', 'uuid', $param_uuid)
            ->with('work_order')
            ->get()
            ->sortBy(function($item) {
                return $item->work_order->date; // Pastikan 'date' adalah nama kolom tanggal di model WorkOrder
            })
            ->groupBy(function($item) {
                return Carbon::parse($item->work_order->date)->format('Y-m-d');
            });

        $dates = [];
        $values = [];
        $equipment = Equipment::where('uuid', $eq_uuid)->firstOrFail();
        $parameter = Parameter::where('uuid', $param_uuid)->firstOrFail();

        foreach ($data as $date => $items) {
            $dates[] = $date;
            foreach ($items as $item) {
                $values[] = (float)$item->value; // Mengambil nilai mentah dari 'value'
            }
        }

        return view('pages.user.checksheet.chart.trend', compact([
            'equipment',
            'parameter',
            'dates',
            'values',
        ]));
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
