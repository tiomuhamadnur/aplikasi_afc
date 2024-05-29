<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\DetailLokasi;
use Illuminate\Http\Request;

class DetailLokasiController extends Controller
{
    public function index()
    {
        $detail_lokasi = DetailLokasi::all();
        return view('pages.admin.detail-lokasi.index', compact([
            'detail_lokasi',
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

        DetailLokasi::create(([
            'name' => $request->name,
            'code' => $request->code,
        ]));

        return redirect()->route('detail-lokasi.index');
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

        $data = DetailLokasi::findOrFail($request->id);

        $data->update([
            'name' => $request->name,
            'code' => $request->code,
        ]);

        return redirect()->route('detail-lokasi.index');
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'id' => 'required',
        ]);

        $data = DetailLokasi::findOrFail($request->id);
        $data->delete();

        return redirect()->route('detail-lokasi.index');
    }
}
