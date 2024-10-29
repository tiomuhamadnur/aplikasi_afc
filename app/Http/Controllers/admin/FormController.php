<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Form;
use App\Models\FunctionalLocation;
use App\Models\TipeEquipment;
use Illuminate\Http\Request;

class FormController extends Controller
{
    public function index()
    {
        $form = Form::where('status', 'active')->get();
        $tipe_equipment = TipeEquipment::all();
        $functional_location = FunctionalLocation::all();

        return view('pages.admin.form.index', compact([
            'form',
            'tipe_equipment',
            'functional_location',
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
            'object_type' => 'required|string',
            'description' => 'string|nullable',
            'tipe_equipment_id' => 'nullable|numeric|required_if:object_type,equipment',
            'functional_location_id' => 'nullable|numeric|required_if:object_type,functional_location',
        ]);

        // Menetapkan nilai null berdasarkan object_type
        if ($data['object_type'] === 'equipment') {
            $data['functional_location_id'] = null;
        } elseif ($data['object_type'] === 'functional_location') {
            $data['tipe_equipment_id'] = null;
        }

        Form::create($data);

        return redirect()->route('form.index')->withNotify('Data berhasil ditambahkan');
    }

    public function edit(string $uuid)
    {
        $form = Form::where('uuid', $uuid)->firstOrFail();
        $tipe_equipment = TipeEquipment::all();
        $functional_location = FunctionalLocation::all();

        return view('pages.admin.form.edit', compact([
            'form',
            'tipe_equipment',
            'functional_location',
        ]));
    }

    public function update(Request $request)
    {
        $rawData = $request->validate([
            'name' => 'required|string',
            'code' => 'required|string',
            'object_type' => 'required|string',
            'description' => 'string|nullable',
            'tipe_equipment_id' => 'nullable|numeric|required_if:object_type,equipment',
            'functional_location_id' => 'nullable|numeric|required_if:object_type,functional_location',
        ]);

        $request->validate([
            'id' => 'required|numeric'
        ]);

        // Menetapkan nilai null berdasarkan object_type
        if ($rawData['object_type'] === 'equipment') {
            $rawData['functional_location_id'] = null;
        } elseif ($rawData['object_type'] === 'functional_location') {
            $rawData['tipe_equipment_id'] = null;
        }

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
