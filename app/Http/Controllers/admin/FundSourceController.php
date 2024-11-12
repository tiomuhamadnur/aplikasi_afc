<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\BudgetAbsorption;
use App\Models\Fund;
use App\Models\FundSource;
use Illuminate\Http\Request;

class FundSourceController extends Controller
{
    public function index()
    {
        $fund_source = FundSource::all();
        $fund = Fund::all();

        $fund_source->each(function ($item) {
            $sum_value_absorption = BudgetAbsorption::whereRelation('project.fund_source', 'id', '=', $item->id)->sum('value');

            $item->balance = (int) $item->balance;
            $item->current_balance = $item->balance - $sum_value_absorption;
        });

        return view('pages.admin.fund-source.index', compact([
            'fund_source',
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
            'current_balance' => 'required|numeric|min:0',
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
            'current_balance' => 'required|numeric|min:0',
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
