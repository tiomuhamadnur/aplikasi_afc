<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\TransWorkOrderEquipment;
use App\Models\WorkOrder;
use Illuminate\Http\Request;

class TransWorkOrderEquipmentController extends Controller
{
    public function store(string $uuid_workorder, Request $request)
    {
        $work_order = WorkOrder::where('uuid', $uuid_workorder)->firstOrFail();

        $request->validate([
            'equipment_id' => 'required|numeric',
        ]);

        TransWorkOrderEquipment::create([
            'work_order_id' => $work_order->id,
            'equipment_id' => $request->equipment_id,
        ]);

        return redirect()->back()->withNotify('Data equipment berhasil ditambahkan');
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'id' => 'required|numeric',
        ]);

        $data = TransWorkOrderEquipment::findOrFail($request->id);
        $data->forceDelete();

        return redirect()->back()->withNotify('Data equipment berhasil dihapus');
    }
}
