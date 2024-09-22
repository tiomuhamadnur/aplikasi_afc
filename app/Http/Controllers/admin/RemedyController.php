<?php

namespace App\Http\Controllers\admin;

use App\DataTables\RemedyDataTable;
use App\Http\Controllers\Controller;
use App\Imports\RemedyImport;
use App\Models\Remedy;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class RemedyController extends Controller
{
    // public function index()
    // {
    //     $remedy = Remedy::all();

    //     return view('pages.admin.remedy.index', compact([
    //         'remedy',
    //     ]));
    // }

    public function index(RemedyDataTable $dataTable)
    {
        return $dataTable->render('pages.admin.remedy.index');
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

        Remedy::create($data);

        return redirect()->route('remedy.index')->withNotify('Data berhasil ditambahkan');
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
            Excel::import(new RemedyImport, $file);
        }

        return redirect()->route('remedy.index')->withNotify('Data berhasil diimport');
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

        $data = Remedy::findOrFail($request->id);

        $data->update($rawData);

        return redirect()->route('remedy.index')->withNotify('Data berhasil diubah');
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'id' => 'required|numeric',
        ]);

        $data = Remedy::findOrFail($request->id);
        $data->delete();

        return redirect()->route('remedy.index')->withNotify('Data berhasil dihapus');
    }
}
