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
    protected array $years;

    public function __construct()
    {
        $this->years = $this->getYears();
    }

    private function getYears(): array
    {
        $currentYear = date('Y');
        $startYear = $currentYear - 10;
        $endYear = $currentYear + 10;

        return range($endYear, $startYear);
    }

    public function index(FundSourceDataTable $dataTable, Request $request)
    {
        $request->validate([
            'year' => 'nullable',
            'fund_id' => 'nullable'
        ]);

        $this_year = $request->year ?? date('Y');
        $fund_id = $request->fund_id ?? null;

        $fund = Fund::all();
        $years = $this->years;

        return $dataTable->with([
            'year' => $this_year,
            'fund_id' => $fund_id,
        ])->render('pages.admin.fund-source.index', compact([
            'fund',
            'years',
            'this_year',
            'fund_id',
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
            'year' => 'required|integer|digits:4|min:2000|max:' . date('Y') + 10,
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
            'year' => 'required|integer|digits:4|min:2000',
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
