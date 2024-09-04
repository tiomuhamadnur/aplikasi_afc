<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Classification;
use App\Models\RelasiArea;
use App\Models\RelasiStruktur;
use App\Models\Status;
use App\Models\TipePekerjaan;
use App\Models\TransWorkOrderEquipment;
use App\Models\User;
use App\Models\WorkOrder;
use Illuminate\Http\Request;

class WorkOrderController extends Controller
{
    public function index()
    {
        $work_order = WorkOrder::all();

        return view('pages.user.work-order.index', compact([
            'work_order'
        ]));
    }

    public function create()
    {
        $tipe_pekerjaan = TipePekerjaan::all();
        $relasi_area = RelasiArea::where('lokasi_id', 2)->distinct('sub_lokasi_id')->get();
        $relasi_struktur = RelasiStruktur::distinct('departemen_id')->get();
        $classification = Classification::all();
        $status = Status::all();

        return view('pages.user.work-order.create', compact([
            'tipe_pekerjaan',
            'relasi_area',
            'relasi_struktur',
            'classification',
            'status',
        ]));

    }

    public function store(Request $request)
    {
        $rawData = $request->validate([
            "tipe_pekerjaan_id" => "required|numeric",
            "wo_number_sap" => "nullable|numeric",
            "name" => "string|required",
            "description" => "string|required",
            "date" => "date|required",
            "relasi_area_id" => "required|numeric",
            "relasi_struktur_id" => "required|numeric",
            "classification_id" => "required|numeric",
        ]);

        $request->validate([
            'equipment_ids' => 'required|array',
            'equipment_ids.*' => 'numeric',
        ]);

        $rawData['status_id'] = 1; //status OPEN
        $rawData['user_id'] = auth()->user()->id;

        $data = WorkOrder::create($rawData);

        foreach($request->equipment_ids as $equipment_id)
        {
            TransWorkOrderEquipment::create([
                'work_order_id' => $data->id,
                'equipment_id' => $equipment_id,
            ]);
        }

        return redirect()->route('work-order.index')->withNotify('Data berhasil ditambahkan');
    }

    public function equipment($uuid)
    {
        $work_order = WorkOrder::where('uuid', $uuid)->firstOrFail();
        $trans_wo_equipment = TransWorkOrderEquipment::where('work_order_id', $work_order->id)->get();

        return view('pages.user.work-order.equipment', compact([
            'work_order',
            'trans_wo_equipment',
        ]));
    }

    public function detail($uuid)
    {
        $work_order = WorkOrder::where('uuid', $uuid)->firstOrFail();
        $trans_wo_equipment = TransWorkOrderEquipment::where('work_order_id', $work_order->id)->get();
        $user = User::where('relasi_struktur_id', $work_order->relasi_struktur_id)->get();
        $status = Status::all();

        return view('pages.user.work-order.detail', compact([
            'work_order',
            'trans_wo_equipment',
            'user',
            'status',
        ]));
    }

    public function edit(string $uuid)
    {
        //
    }

    public function update(Request $request)
    {
        //
    }

    public function destroy(Request $request)
    {
        //
    }
}
