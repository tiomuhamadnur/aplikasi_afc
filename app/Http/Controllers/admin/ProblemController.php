<?php

namespace App\Http\Controllers\admin;

use App\DataTables\ProblemDataTable;
use App\Http\Controllers\Controller;
use App\Imports\ProblemImport;
use App\Models\Category;
use App\Models\Problem;
use App\Models\TipeEquipment;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ProblemController extends Controller
{
    public function index(ProblemDataTable $dataTable)
    {
        return $dataTable->render('pages.admin.problem.index');
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

        Problem::create($data);

        return redirect()->route('problem.index')->withNotify('Data berhasil ditambahkan');
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
            Excel::import(new ProblemImport, $file);
        }

        return redirect()->route('problem.index')->withNotify('Data berhasil diimport');
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

        $data = Problem::findOrFail($request->id);

        $data->update($rawData);

        return redirect()->route('problem.index')->withNotify('Data berhasil diubah');
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'id' => 'required|numeric',
        ]);

        $data = Problem::findOrFail($request->id);
        $data->delete();

        return redirect()->route('problem.index')->withNotify('Data berhasil dihapus');
    }
}
