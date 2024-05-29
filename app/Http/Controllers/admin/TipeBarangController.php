<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\TipeBarang;
use Illuminate\Http\Request;

class TipeBarangController extends Controller
{
    public function index()
    {
        $tipe_barang = TipeBarang::all();
        return view('pages.admin.tipe-barang.index', compact([
            'tipe_barang',
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

        TipeBarang::create(([
            'name' => $request->name,
            'code' => $request->code,
        ]));

        return redirect()->route('tipe-barang.index');
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

        $data = TipeBarang::findOrFail($request->id);

        $data->update([
            'name' => $request->name,
            'code' => $request->code,
        ]);

        return redirect()->route('tipe-barang.index');
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'id' => 'required',
        ]);

        $data = TipeBarang::findOrFail($request->id);
        $data->delete();

        return redirect()->route('tipe-barang.index');
    }
}
