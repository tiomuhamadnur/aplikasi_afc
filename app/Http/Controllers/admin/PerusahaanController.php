<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Perusahaan;
use Illuminate\Http\Request;

class PerusahaanController extends Controller
{
    public function index()
    {
        $perusahaan = Perusahaan::all();
        return view('pages.admin.perusahaan.index', compact([
            'perusahaan',
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
            'alamat' => 'required',
        ]);

        Perusahaan::create(([
            'name' => $request->name,
            'alamat' => $request->alamat,
        ]));

        return redirect()->route('perusahaan.index');
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
            'alamat' => 'required',
        ]);

        $data = Perusahaan::findOrFail($request->id);

        $data->update([
            'name' => $request->name,
            'alamat' => $request->alamat,
        ]);

        return redirect()->route('perusahaan.index');
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'id' => 'required',
        ]);

        $data = Perusahaan::findOrFail($request->id);
        $data->delete();

        return redirect()->route('perusahaan.index');
    }
}
