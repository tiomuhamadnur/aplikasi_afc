<?php

namespace App\Http\Controllers\admin;

use App\DataTables\TipeEquipmentDataTable;
use App\Http\Controllers\Controller;
use App\Models\TipeEquipment;
use Illuminate\Http\Request;

class TipeEquipmentController extends Controller
{
    public function index(TipeEquipmentDataTable $dataTable)
    {
        return $dataTable->render('pages.admin.tipe-equipment.index');
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'code' => 'required',
        ]);

        TipeEquipment::create(([
            'name' => $request->name,
            'code' => $request->code,
        ]));

        return redirect()->route('tipe-equipment.index');
    }

    public function show(string $id)
    {
        //
    }

    public function edit(string $id)
    {
        //
    }

    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'name' => 'required',
            'code' => 'required',
        ]);

        $data = TipeEquipment::findOrFail($request->id);

        $data->update([
            'name' => $request->name,
            'code' => $request->code,
        ]);

        return redirect()->route('tipe-equipment.index');
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'id' => 'required',
        ]);

        $data = TipeEquipment::findOrFail($request->id);
        $data->delete();

        return redirect()->route('tipe-equipment.index');
    }
}
