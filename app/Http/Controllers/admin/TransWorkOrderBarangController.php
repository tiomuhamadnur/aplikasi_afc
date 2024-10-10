<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\TransaksiBarang;
use App\Models\WorkOrder;
use Illuminate\Http\Request;

class TransWorkOrderBarangController extends Controller
{
    public function store(string $uuid_workorder, Request $request)
    {
        $request->validate([
            'barang_id' => 'required|numeric|min:1',
            'qty' => 'required|numeric|min:1',
        ]);

        $work_order = WorkOrder::where('uuid', $uuid_workorder)->firstOrFail();

        TransaksiBarang::create([
            'work_order_id' => $work_order->id,
            'barang_id' => $request->barang_id,
            'qty' => $request->qty,
            'user_id' => auth()->user()->id,
            'tanggal' => $work_order->date,
        ]);

        return redirect()->back()->withNotify('Data Sparepart berhasil ditambahkan');
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'id' => 'required|numeric|min:1',
        ]);

        $data = TransaksiBarang::findOrFail($request->id);
        $data->forceDelete();

        return redirect()->back()->withNotify('Data Sparepart berhasil dihapus');
    }
}
