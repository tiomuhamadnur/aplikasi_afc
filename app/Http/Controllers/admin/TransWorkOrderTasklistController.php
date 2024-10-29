<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\TransWorkOrderTasklist;
use App\Models\WorkOrder;
use Illuminate\Http\Request;

class TransWorkOrderTasklistController extends Controller
{
    public function store(string $uuid_workorder, Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'duration' => 'nullable|numeric|min:1',
            'actual_duration' => 'nullable|numeric|min:1',
            'reference' => 'nullable|string',
        ]);

        $work_order = WorkOrder::where('uuid', $uuid_workorder)->firstOrFail();

        TransWorkOrderTasklist::create([
            'work_order_id' => $work_order->id,
            'name' => $request->name,
            'duration' => $request->duration,
            'actual_duration' => $request->actual_duration,
            'reference' => $request->reference,
        ]);

        return redirect()->back()->withNotify('Data Tasklist/Operation berhasil ditambahkan');
    }

    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required|numeric|min:1',
            'name' => 'required|string',
            'duration' => 'nullable|numeric|min:1',
            'actual_duration' => 'nullable|numeric|min:1',
            'reference' => 'nullable|string',
        ]);

        $data = TransWorkOrderTasklist::findOrFail($request->id);
        $data->update([
            'name' => $request->name,
            'duration' => $request->duration,
            'actual_duration' => $request->actual_duration,
            'reference' => $request->reference,
        ]);

        return redirect()->back()->withNotify('Data Tasklist/Operation berhasil diubah');
    }

    public function update_actual_duration(Request $request)
    {
        $request->validate([
            'id' => 'required|numeric|min:1',
            'actual_duration' => 'nullable|numeric|min:1',
        ]);

        $data = TransWorkOrderTasklist::findOrFail($request->id);
        $data->update([
            'actual_duration' => $request->actual_duration,
        ]);

        return redirect()->back()->withNotify('Data Actual Time Tasklist/Operation berhasil ditambahkan');
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'id' => 'required|numeric|min:1',
        ]);

        $data = TransWorkOrderTasklist::findOrFail($request->id);
        $data->forceDelete();

        return redirect()->back()->withNotify('Data Tasklist/Operation berhasil dihapus');
    }
}
