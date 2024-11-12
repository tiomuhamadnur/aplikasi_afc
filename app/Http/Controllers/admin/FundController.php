<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Fund;
use Illuminate\Http\Request;

class FundController extends Controller
{
    public function index()
    {
        $fund = Fund::all();
        return view('pages.admin.fund.index', compact([
            'fund',
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
