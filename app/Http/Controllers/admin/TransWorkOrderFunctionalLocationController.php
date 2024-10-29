<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\TransWorkOrderFunctionalLocation;
use App\Models\WorkOrder;
use Illuminate\Http\Request;

class TransWorkOrderFunctionalLocationController extends Controller
{
    public function store(string $uuid_workorder, Request $request)
    {
        $work_order = WorkOrder::where('uuid', $uuid_workorder)->firstOrFail();

        $request->validate([
            'functional_location_id' => 'required|numeric|min:1',
        ]);

        $data = [
            'work_order_id' => $work_order->id,
            'functional_location_id' => $request->functional_location_id,
        ];

        TransWorkOrderFunctionalLocation::updateOrCreate($data, $data);

        return redirect()->back()->withNotify('Data Functional Location berhasil ditambahkan');
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'id' => 'required|numeric|min:1',
        ]);

        $data = TransWorkOrderFunctionalLocation::findOrFail($request->id);
        $data->forceDelete();

        return redirect()->back()->withNotify('Data Functional Location berhasil dihapus');
    }
}
