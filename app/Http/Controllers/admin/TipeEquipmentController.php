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
        $rawData = $request->validate([
            'name' => 'required',
            'code' => 'required',
            'operation_time' => 'required|numeric|min:1|max:24',
        ]);

        TipeEquipment::updateOrCreate($rawData, $rawData);

        return redirect()->route('tipe-equipment.index')->withNotify('Data berhasil ditambahkan');
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
        $rawData = $request->validate([
            'id' => 'required|numeric',
            'name' => 'required',
            'code' => 'required',
            'operation_time' => 'required|numeric|min:1|max:24',
        ]);

        $data = TipeEquipment::findOrFail($request->id);

        $data->update($rawData);

        return redirect()->route('tipe-equipment.index')->withNotify('Data berhasil diubah');
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'id' => 'required',
        ]);

        $data = TipeEquipment::findOrFail($request->id);
        $data->delete();

        return redirect()->route('tipe-equipment.index')->withNotify('Data berhasil dihapus');
    }
}
