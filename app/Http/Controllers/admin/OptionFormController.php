<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\OptionForm;
use Illuminate\Http\Request;

class OptionFormController extends Controller
{
    public function index()
    {
        $option_form = OptionForm::all();

        return view('pages.admin.option_form.index', compact([
            'option_form'
        ]));
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'code' => 'required|string',
            'value' => 'required|array',
        ]);

        OptionForm::create([
            'name' => $request->name,
            'code' => $request->code,
            'value' => json_encode($request->value),
        ]);

        return redirect()->route('option-form.index')->withNotify('Data berhasil ditambahkan');
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
            'id' => 'required|numeric',
            'name' => 'required|string',
            'code' => 'required|string',
            'value' => 'required|string',
        ]);

        $data = OptionForm::findOrFail($request->id);

        $value = explode(',', $request->value);
        $data->update([
            'name' => $request->name,
            'code' => $request->code,
            'value' => $value,
        ]);

        return redirect()->route('option-form.index')->withNotify('Data berhasil diubah');
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'id' => 'required|numeric',
        ]);

        $data = OptionForm::findOrFail($request->id);
        $data->delete();

        return redirect()->route('option-form.index')->withNotify('Data berhasil dihapus');
    }
}
