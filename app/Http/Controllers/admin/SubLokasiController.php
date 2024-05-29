<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\SubLokasi;
use Illuminate\Http\Request;

class SubLokasiController extends Controller
{
    public function index()
    {
        $sub_lokasi = SubLokasi::all();
        return view('pages.admin.sub-lokasi.index', compact([
            'sub_lokasi',
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

        SubLokasi::create(([
            'name' => $request->name,
            'code' => $request->code,
        ]));

        return redirect()->route('sub-lokasi.index');
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

        $data = SubLokasi::findOrFail($request->id);

        $data->update([
            'name' => $request->name,
            'code' => $request->code,
        ]);

        return redirect()->route('sub-lokasi.index');
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'id' => 'required',
        ]);

        $data = SubLokasi::findOrFail($request->id);
        $data->delete();

        return redirect()->route('sub-lokasi.index');
    }
}
