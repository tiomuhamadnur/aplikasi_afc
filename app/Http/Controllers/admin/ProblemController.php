<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Problem;
use App\Models\TipeEquipment;
use Illuminate\Http\Request;

class ProblemController extends Controller
{
    public function index()
    {
        $problem = Problem::all();
        $category = Category::all();
        $tipe_equipment = TipeEquipment::all();

        return view('pages.admin.problem.index', compact([
            'problem',
            'category',
            'tipe_equipment',
        ]));
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'category_id' => 'required|numeric',
            'tipe_equipment_id' => 'required|numeric',
            'name' => 'required',
            'code' => 'required',
        ]);

        Problem::create($data);

        return redirect()->route('problem.index')->withNotify('Data berhasil ditambahkan');
    }

    public function show(string $id)
    {
        //
    }

    public function edit(string $uuid)
    {
        $problem = Problem::where('uuid', $uuid)->firstOrFail();

        $category = Category::all();
        $tipe_equipment = TipeEquipment::all();

        return view('pages.admin.problem.edit', compact([
            'problem',
            'category',
            'tipe_equipment',
        ]));
    }

    public function update(Request $request)
    {
        $rawData = $request->validate([
            'category_id' => 'required|numeric',
            'tipe_equipment_id' => 'required|numeric',
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
