<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Lokasi;
use Illuminate\Http\Request;

class LokasiController extends Controller
{
    public function index()
    {
        $lokasi = Lokasi::all();
        return view('pages.admin.lokasi.index', compact([
            'lokasi',
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

        Lokasi::create(([
            'name' => $request->name,
            'code' => $request->code,
        ]));

        return redirect()->route('lokasi.index');
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

        $lokasi = Lokasi::findOrFail($request->id);

        $lokasi->update([
            'name' => $request->name,
            'code' => $request->code,
        ]);

        return redirect()->route('lokasi.index');
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'id' => 'required',
        ]);

        $lokasi = Lokasi::findOrFail($request->id);
        $lokasi->delete();

        return redirect()->route('lokasi.index');
    }
}
