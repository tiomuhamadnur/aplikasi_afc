<?php

namespace App\Http\Controllers\admin;

use App\DataTables\FunctionalLocationDataTable;
use App\Http\Controllers\Controller;
use App\Models\FunctionalLocation;
use Illuminate\Http\Request;

class FunctionalLocationController extends Controller
{
    public function index(FunctionalLocationDataTable  $dataTable)
    {
        $functional_location = FunctionalLocation::all();
        return $dataTable->render('pages.admin.functional-location.index', compact([
            'functional_location'
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
            'code' => 'unique:functional_location,code|required|string',
            'description' => 'required|string',
            'parent_id' => 'nullable|numeric'
        ], [
            'code.unique' => 'Data code sudah ada'
        ]);

        FunctionalLocation::create($data);

        return redirect()->route('fun_loc.store')->withNotify('Data berhasil ditambahkan');
    }

    public function show(string $uuid)
    {
        //
    }

    public function edit(string $uuid)
    {
        $functional_location = FunctionalLocation::where('uuid', $uuid)->firstOrFail();
        $functional_locations = FunctionalLocation::whereNot('id', $functional_location->id)->get();

        return view('pages.admin.functional-location.edit', compact([
            'functional_location',
            'functional_locations',
        ]));
    }

    public function update(Request $request)
    {
        $rawData = $request->validate([
            'name' => 'required|string',
            'code' => 'required|string',
            'description' => 'required|string',
            'parent_id' => 'nullable|numeric'
        ]);

        $request->validate([
            'id' => 'required|numeric',
        ]);

        $data = FunctionalLocation::findOrFail($request->id);

        $data->update($rawData);

        return redirect()->route('fun_loc.index')->withNotify('Data berhasil diperbaharui');
    }

    public function destroy(string $id)
    {
        //
    }
}
