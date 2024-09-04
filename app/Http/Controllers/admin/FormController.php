<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Form;
use App\Models\TipeEquipment;
use Illuminate\Http\Request;

class FormController extends Controller
{
    public function index()
    {
        $form = Form::where('status', 'active')->get();
        $tipe_equipment = TipeEquipment::all();

        return view('pages.admin.form.index', compact([
            'form',
            'tipe_equipment'
        ]));
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'code' => 'required|string',
            'tipe_equipment_id' => 'required|numeric',
            'description' => 'string|nullable',
        ]);

        Form::create($data);

        return redirect()->route('form.index')->withNotify('Data berhasil ditambahkan');
    }

    public function show(string $id)
    {
        //
    }

    public function edit(string $uuid)
    {
        $form = Form::where('uuid', $uuid)->firstOrFail();
        $tipe_equipment = TipeEquipment::all();

        return view('pages.admin.form.edit', compact([
            'form',
            'tipe_equipment'
        ]));
    }

    public function update(Request $request)
    {
        $rawData = $request->validate([
            'name' => 'required|string',
            'code' => 'required|string',
            'tipe_equipment_id' => 'required|numeric',
            'description' => 'string|nullable',
            'status' => 'required|string',
        ]);

        $request->validate([
            'id' => 'required|numeric'
        ]);

        $data = Form::findOrFail($request->id);
        $data->update($rawData);

        return redirect()->route('form.index')->withNotify('Data berhasil diubah');
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'id' => 'required|numeric'
        ]);

        $data = Form::findOrFail($request->id);
        $data->delete();

        return redirect()->route('form.index')->withNotify('Data berhasil dihapus');
    }
}
