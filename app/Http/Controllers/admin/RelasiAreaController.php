<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\DetailLokasi;
use App\Models\Lokasi;
use App\Models\RelasiArea;
use App\Models\SubLokasi;
use Illuminate\Http\Request;

class RelasiAreaController extends Controller
{
    public function index()
    {
        $area = RelasiArea::all();

        $lokasi = Lokasi::all();
        $sub_lokasi = SubLokasi::all();
        $detail_lokasi = DetailLokasi::all();

        return view('pages.admin.area.index', compact([
            'area',
            'lokasi',
            'sub_lokasi',
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
            'lokasi_id' => 'required',
            'sub_lokasi_id' => 'required',
            'detail_lokasi_id' => 'required',
        ]);

        RelasiArea::create(([
            'lokasi_id' => $request->lokasi_id,
            'sub_lokasi_id' => $request->sub_lokasi_id,
            'detail_lokasi_id' => $request->detail_lokasi_id,
        ]));

        return redirect()->route('area.index');
    }

    public function show(string $id)
    {
        //
    }

    public function edit(string $uuid)
    {
        $area = RelasiArea::where('uuid', $uuid)->firstOrFail();

        $lokasi = Lokasi::all();
        $sub_lokasi = SubLokasi::all();
        $detail_lokasi = DetailLokasi::all();

        return view('pages.admin.area.edit', compact([
            'area',
            'lokasi',
            'sub_lokasi',
            'detail_lokasi',
        ]));
    }

    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required|numeric',
            'lokasi_id' => 'required|numeric',
            'sub_lokasi_id' => 'required|numeric',
            'detail_lokasi_id' => 'required|numeric',
        ]);

        $data = RelasiArea::findOrFail($request->id);
        $data->update([
            'lokasi_id' => $request->lokasi_id,
            'sub_lokasi_id' => $request->sub_lokasi_id,
            'detail_lokasi_id' => $request->detail_lokasi_id,
        ]);

        return redirect()->route('area.index');
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'id' => 'required',
        ]);

        $data = RelasiArea::findOrFail($request->id);
        $data->delete();

        return redirect()->route('area.index');
    }
}
