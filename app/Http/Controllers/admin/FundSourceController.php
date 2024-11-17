<?php

namespace App\Http\Controllers\admin;

use App\DataTables\FundSourceDataTable;
use App\Http\Controllers\Controller;
use App\Models\BudgetAbsorption;
use App\Models\Fund;
use App\Models\FundSource;
use Illuminate\Http\Request;

class FundSourceController extends Controller
{
    public function index(FundSourceDataTable $dataTable, Request $request)
    {
        $request->validate([
            'start_period' => 'date|nullable',
            'end_period' => 'date|nullable',
        ]);

        $start_period = $request->start_period ?? null;
        $end_period = $request->end_period ?? $start_period;

        $fund = Fund::all();

        return $dataTable->with([
            'start_period' => $start_period,
            'end_period' => $end_period,
        ])->render('pages.admin.fund-source.index', compact([
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
            'fund_id' => 'required|numeric',
            'balance' => 'required|numeric|min:0',
            'start_period' => 'required|date',
            'end_period' => 'required|date',
        ]);

        $data['user_id'] = auth()->user()->id;

        FundSource::updateOrCreate($data, $data);

        return redirect()->route('fund-source.index')->withNotify('Data berhasil ditambahkan');
    }

    public function show(string $id)
    {
        //
    }

    public function edit(string $uuid)
    {
        $fund_source = FundSource::where('uuid', $uuid)->firstOrFail();
        $fund = Fund::all();

        return view('pages.admin.fund-source.edit', compact([
            'fund_source',
            'fund',
        ]));
    }

    public function update(Request $request)
    {
        $data = FundSource::findOrFail($request->id);

        $rawData = $request->validate([
            'fund_id' => 'required|numeric',
            'balance' => 'required|numeric|min:0',
            'start_period' => 'required|date',
            'end_period' => 'required|date',
        ]);

        $rawData['user_id'] = auth()->user()->id;

        $data->update($rawData);

        return redirect()->route('fund-source.index')->withNotify('Data berhasil diperbaharui');
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'id' => 'required',
        ]);

        $data = FundSource::findOrFail($request->id);
        $data->delete();

        return redirect()->route('fund-source.index')->withNotify('Data berhasil dihapus');
    }
}
