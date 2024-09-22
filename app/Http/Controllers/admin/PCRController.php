<?php

namespace App\Http\Controllers\admin;

use App\DataTables\PCRDataTable;
use App\Http\Controllers\Controller;
use App\Imports\PCRImport;
use App\Models\Category;
use App\Models\Cause;
use App\Models\Classification;
use App\Models\PCR;
use App\Models\Problem;
use App\Models\Remedy;
use App\Models\TipeEquipment;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class PCRController extends Controller
{
    public function index(PCRDataTable $dataTable)
    {
        $tipe_equipment = TipeEquipment::orderBy('name', 'ASC')->get();
        $category = Category::all();
        $problem = Problem::all();
        $cause = Cause::all();
        $remedy = Remedy::all();
        $classification = Classification::all();

        return $dataTable->render('pages.admin.pcr.index', compact([
            'tipe_equipment',
            'category',
            'problem',
            'cause',
            'remedy',
            'classification',
        ]));
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'tipe_equipment_id' => 'required|numeric',
            'category_id' => 'required|numeric',
            'problem_id' => 'required|numeric',
            'cause_id' => 'required|numeric',
            'remedy_id' => 'required|numeric',
            'classification_id' => 'required|numeric',
        ]);

        PCR::create($data);

        return redirect()->route('pcr.index')->withNotify('Data berhasil ditambahkan');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls',
        ]);

        if($request->hasFile('file'))
        {
            $file = $request->file('file');
            Excel::import(new PCRImport, $file);
        }

        return redirect()->route('pcr.index')->withNotify('Data berhasil diimport');
    }

    public function edit(string $uuid)
    {
        $pcr = PCR::where('uuid', $uuid)->firstOrFail();

        $tipe_equipment = TipeEquipment::orderBy('name', 'ASC')->get();
        $category = Category::all();
        $problem = Problem::all();
        $cause = Cause::all();
        $remedy = Remedy::all();
        $classification = Classification::all();

        return view('pages.admin.pcr.edit', compact([
            'pcr',
            'tipe_equipment',
            'category',
            'problem',
            'cause',
            'remedy',
            'classification',
        ]));
    }

    public function update(Request $request)
    {
        $rawData = $request->validate([
            'tipe_equipment_id' => 'required|numeric',
            'category_id' => 'required|numeric',
            'problem_id' => 'required|numeric',
            'cause_id' => 'required|numeric',
            'remedy_id' => 'required|numeric',
            'classification_id' => 'required|numeric',
        ]);

        $request->validate([
            'id' => 'required|numeric',
        ]);

        $data = PCR::findOrFail($request->id);

        $data->update($rawData);

        return redirect()->route('pcr.index')->withNotify('Data berhasil diubah');
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'id' => 'required|numeric',
        ]);

        $data = PCR::findOrFail($request->id);
        $data->delete();

        return redirect()->route('pcr.index')->withNotify('Data berhasil dihapus');
    }
}
