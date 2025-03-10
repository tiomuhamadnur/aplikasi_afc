<?php

namespace App\Http\Controllers\admin;

use App\DataTables\FundDataTable;
use App\Http\Controllers\Controller;
use App\Models\Divisi;
use App\Models\Fund;
use Illuminate\Http\Request;

class FundController extends Controller
{
    public function index(FundDataTable $dataTable, Request $request)
    {
        $request->validate([
            'divisi_id' => 'nullable',
        ]);

        $divisi_id = $request->divisi_id ?? null;

        $divisi = Divisi::all();

        return $dataTable->with([
            'divisi_id' => $divisi_id,
        ])->render('pages.admin.fund.index', compact([
            'divisi',
            'divisi_id',
        ]));
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'code' => 'required|string',
            'type' => 'required|string',
            'description' => 'required|string',
            'divisi_id' => 'required|numeric',
        ]);

        Fund::updateOrCreate($data, $data);

        return redirect()->route('fund.index')->withNotify('Data berhasil ditambahkan');
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
        $data = Fund::findOrFail($request->id);

        $rawData = $request->validate([
            'name' => 'required|string',
            'code' => 'required|string',
            'type' => 'required|string',
            'description' => 'required|string',
            'divisi_id' => 'required|numeric',
        ]);

        $data->update($rawData);

        return redirect()->route('fund.index')->withNotify('Data berhasil diperbaharui');
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'id' => 'required',
        ]);

        $data = Fund::findOrFail($request->id);
        $data->delete();

        return redirect()->route('fund.index')->withNotify('Data berhasil dihapus');
    }
}
