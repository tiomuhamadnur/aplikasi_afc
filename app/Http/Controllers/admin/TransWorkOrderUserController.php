<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\TransWorkOrderUser;
use App\Models\WorkOrder;
use Illuminate\Http\Request;

class TransWorkOrderUserController extends Controller
{
    public function store(string $uuid_workorder, Request $request)
    {
        $request->validate([
            'user_id' => 'required|numeric|min:1',
        ]);

        $work_order = WorkOrder::where('uuid', $uuid_workorder)->firstOrFail();

        TransWorkOrderUser::create([
            'work_order_id' => $work_order->id,
            'user_id' => $request->user_id,
        ]);

        return redirect()->back()->withNotify('Data Man Power berhasil ditambahkan');
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'id' => 'required|numeric|min:1',
        ]);

        $data = TransWorkOrderUser::findOrFail($request->id);
        $data->forceDelete();

        return redirect()->back()->withNotify('Data Man Power berhasil dihapus');
    }
}
