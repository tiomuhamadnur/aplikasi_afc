<?php

namespace App\Http\Controllers\admin;

use App\DataTables\AreaDataTable;
use App\Http\Controllers\Controller;
use App\Models\DetailLokasi;
use App\Models\Lokasi;
use App\Models\RelasiArea;
use App\Models\SubLokasi;
use Illuminate\Http\Request;

class RelasiAreaController extends Controller
{
    public function index(AreaDataTable $dataTable, Request $request)
    {
        $request->validate([
            'lokasi_id' => 'nullable',
            'sub_lokasi_id' => 'nullable',
            'detail_lokasi_id' => 'nullable',
        ]);

        $lokasi_id = $request->lokasi_id ?? null;
        $sub_lokasi_id = $request->sub_lokasi_id ?? null;
        $detail_lokasi_id = $request->detail_lokasi_id ?? null;

        $area = RelasiArea::all();

        $lokasi = Lokasi::all();
        $sub_lokasi = SubLokasi::all();
        $detail_lokasi = DetailLokasi::all();

        return $dataTable->with([
            'lokasi_id' => $lokasi_id,
            'sub_lokasi_id' => $sub_lokasi_id,
            'detail_lokasi_id' => $detail_lokasi_id,
        ])->render('pages.admin.area.index', compact([
            'area',
            'lokasi',
            'sub_lokasi',
            'detail_lokasi',
            'lokasi_id',
            'sub_lokasi_id',
            'detail_lokasi_id',
        ]));
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'lokasi_id' => 'required|numeric',
            'sub_lokasi_id' => 'required|numeric',
            'detail_lokasi_id' => 'required|numeric',
        ]);

        RelasiArea::updateOrCreate($data, $data);

        return redirect()->route('area.index')->withNotify('Data berhasil ditambahkan.');
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
        $rawData = $request->validate([
            'id' => 'required|numeric',
            'lokasi_id' => 'required|numeric',
            'sub_lokasi_id' => 'required|numeric',
            'detail_lokasi_id' => 'required|numeric',
        ]);

        $data = RelasiArea::findOrFail($request->id);
        $data->update($rawData);

        return redirect()->route('area.index')->withNotify('Data berhasil diperbaharui.');
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'id' => 'required',
        ]);

        $data = RelasiArea::findOrFail($request->id);
        $data->delete();

        return redirect()->route('area.index')->withNotify('Data berhasil dihapus.');
    }
}
