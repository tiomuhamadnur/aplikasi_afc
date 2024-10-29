<?php

namespace App\Http\Controllers\user;

use App\DataTables\ChecksheetDataTable;
use App\Exports\HistoryChecksheetExport;
use App\Http\Controllers\Controller;
use App\Models\Checksheet;
use App\Models\Equipment;
use App\Models\Form;
use App\Models\FunctionalLocation;
use App\Models\Parameter;
use App\Models\TipeEquipment;
use App\Models\TransWorkOrderEquipment;
use App\Models\TransWorkOrderFunctionalLocation;
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

    public function create(Request $request)
    {
        $request->validate([
            'uuid_work_order' => 'required|string',
            'uuid_equipment' => 'nullable|string',
            'uuid_functional_location' => 'nullable|string',
        ]);

        $uuid_work_order = $request->uuid_work_order;
        $uuid_equipment = $request->uuid_equipment;
        $uuid_functional_location = $request->uuid_functional_location;

        $work_order = WorkOrder::where('uuid', $uuid_work_order)->firstOrFail();
        $equipment = null;
        $functional_location = null;

        if($uuid_equipment)
        {
            $equipment = Equipment::where('uuid', $uuid_equipment)->firstOrFail();
            $parameter = Parameter::whereRelation('form.tipe_equipment', 'id', '=', $equipment->tipe_equipment->id)
                        ->orderBy('urutan', 'ASC')
                        ->get();
        }
        elseif($uuid_functional_location)
        {
            $functional_location = FunctionalLocation::where('uuid', $uuid_functional_location)->firstOrFail();
            $parameter = Parameter::whereRelation('form.functional_location', 'id', '=', $functional_location->id)
                        ->orderBy('urutan', 'ASC')
                        ->get();

        }

        return view('pages.user.checksheet.create', compact([
            'work_order',
            'functional_location',
            'equipment',
            'parameter',
        ]));
    }

    public function store(Request $request)
    {
        $request->validate([
            "work_order_id" => 'required|numeric',
            "equipment_id" => 'nullable|numeric',
            "functional_location_id" => 'nullable|numeric',
            "parameter_ids" => 'required|array',
            'parameter_ids.*' => 'numeric',
            "values" => 'required|array',
            "values.*" => 'required|string',
        ]);

        $equipment_id = null;
        $functional_location_id = null;
        $work_order = WorkOrder::findOrFail($request->work_order_id);

        if($request->equipment_id != null)
        {
            $equipment = Equipment::findOrFail($request->equipment_id);
            $equipment_id = $equipment->id;
        }
        elseif($request->functional_location_id != null)
        {
            $functional_location = FunctionalLocation::findOrFail($request->functional_location_id);
            $functional_location_id = $functional_location->id;
        }

        foreach ($request->values as $index => $value) {
            $status = $this->checkTolerance((int) $request->parameter_ids[$index], $value);

            Checksheet::create([
                'work_order_id' => $work_order->id,
                'equipment_id' => $equipment_id,
                'functional_location_id' => $functional_location_id,
                'parameter_id' => $request->parameter_ids[$index],
                'value' => $value,
                'user_id' => auth()->user()->id,
                'status' => $status,
            ]);
        }

        if($request->equipment_id != null)
        {
            $data = TransWorkOrderEquipment::where('work_order_id', $work_order->id)
                                ->where('equipment_id', $equipment_id)
                                ->firstOrFail();
        } elseif($request->functional_location_id != null)
        {
            $data = TransWorkOrderFunctionalLocation::where('work_order_id', $work_order->id)
                                ->where('functional_location_id', $functional_location_id)
                                ->firstOrFail();
        }

        $data->update([
            'status' => 'completed'
        ]);

        return redirect()->route('work-order.detail', $work_order->uuid)
                        ->withNotify('Data checksheet berhasil diinput');
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

    public function history(Request $request)
    {
        // dd($request);
        $request->validate([
            'uuid_equipment' => 'nullable|string',
            'uuid_functional_location' => 'nullable|string',
        ]);

        $uuid_equipment = $request->uuid_equipment ?? null;
        $uuid_functional_location = $request->uuid_functional_location ?? null;

        $equipment = null;
        $functional_location = null;
        $checksheetData = collect();

        if($uuid_equipment != null)
        {
            $equipment = Equipment::where('uuid', $uuid_equipment)->firstOrFail();
            $checksheetData = Checksheet::whereRelation('equipment', 'uuid', $uuid_equipment)
                ->whereHas('parameter', function ($query) {
                    $query->whereNull('deleted_at');
                })
                ->with(['parameter' => function ($query) {
                    $query->select('id', 'uuid', 'name', 'urutan', 'tipe', 'satuan_id', 'min_value', 'max_value')
                        ->with('satuan'); // Pastikan satuan juga diambil jika diperlukan
                }, 'work_order'])
                ->get();
        } elseif($uuid_functional_location != null)
        {
            $functional_location = FunctionalLocation::where('uuid', $uuid_functional_location)->firstOrFail();
            $checksheetData = Checksheet::whereRelation('functional_location', 'uuid', $uuid_functional_location)
                ->whereHas('parameter', function ($query) {
                    $query->whereNull('deleted_at');
                })
                ->with(['parameter' => function ($query) {
                    $query->select('id', 'uuid', 'name', 'urutan', 'tipe', 'satuan_id', 'min_value', 'max_value')
                        ->with('satuan'); // Pastikan satuan juga diambil jika diperlukan
                }, 'work_order'])
                ->get();
        }

        if ($checksheetData->count() == 0) {
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

        return view('pages.user.checksheet.history', compact([
            'equipment',
            'functional_location',
            'pivotData',
            'parameters'
        ]));
    }

    public function export_excel(Request $request)
    {
        dd($request);
        $uuid_equipment = $request->uuid_equipment;
        $uuid_functional_location = $request->uuid_functional_location;
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
