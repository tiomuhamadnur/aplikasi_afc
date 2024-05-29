<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\TipePekerjaan;
use Illuminate\Http\Request;

class TipePekerjaanController extends Controller
{
    public function index()
    {
        $tipe_pekerjaan = TipePekerjaan::all();
        return view('pages.admin.tipe-pekerjaan.index', compact([
            'tipe_pekerjaan',
        ]));
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'code' => 'required',
        ]);

        TipePekerjaan::create(([
            'name' => $request->name,
            'code' => $request->code,
        ]));

        return redirect()->route('tipe-pekerjaan.index');
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
            'id' => 'required',
            'name' => 'required',
            'code' => 'required',
        ]);

        $data = TipePekerjaan::findOrFail($request->id);

        $data->update([
            'name' => $request->name,
            'code' => $request->code,
        ]);

        return redirect()->route('tipe-pekerjaan.index');
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'id' => 'required',
        ]);

        $data = TipePekerjaan::findOrFail($request->id);
        $data->delete();

        return redirect()->route('tipe-pekerjaan.index');
    }
}
