<?php

namespace App\Http\Controllers\admin;

use App\DataTables\CauseDataTable;
use App\Http\Controllers\Controller;
use App\Imports\CauseImport;
use App\Models\Cause;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class CauseController extends Controller
{
    public function index(CauseDataTable $dataTable)
    {
        return $dataTable->render('pages.admin.cause.index');
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
        ]);

        Cause::create($data);

        return redirect()->route('cause.index')->withNotify('Data berhasil ditambahkan');
    }

    public function show(string $id)
    {
        //
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls',
        ]);

        if($request->hasFile('file'))
        {
            $file = $request->file('file');
            Excel::import(new CauseImport, $file);
        }

        return redirect()->route('cause.index')->withNotify('Data berhasil diimport');
    }

    public function update(Request $request)
    {
        $rawData = $request->validate([
            'name' => 'required',
            'code' => 'required',
        ]);

        $request->validate([
            'id' => 'required|numeric',
        ]);

        $data = Cause::findOrFail($request->id);

        $data->update($rawData);

        return redirect()->route('cause.index')->withNotify('Data berhasil diubah');
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'id' => 'required|numeric',
        ]);

        $data = Cause::findOrFail($request->id);
        $data->delete();

        return redirect()->route('cause.index')->withNotify('Data berhasil dihapus');
    }
}
