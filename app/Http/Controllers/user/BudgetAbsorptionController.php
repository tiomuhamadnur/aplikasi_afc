<?php

namespace App\Http\Controllers\user;

use App\DataTables\BudgetAbsorptionDataTable;
use App\Http\Controllers\Controller;
use App\Models\BudgetAbsorption;
use App\Models\Departemen;
use App\Models\Fund;
use App\Models\FundSource;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BudgetAbsorptionController extends Controller
{
    public function index(BudgetAbsorptionDataTable $dataTable, Request $request)
    {
        $project = Project::all();
        $fund = Fund::all();
        $departemen = Departemen::all();

        $fund_id = $request->fund_id ?? null;
        $project_id = $request->project_id ?? null;
        $departemen_id = $request->departemen_id ?? null;
        $type = $request->type ?? null;
        $start_date = $request->start_date ?? null;
        $end_date = $request->end_date ?? $start_date;

        return $dataTable->with([
            'fund_id' => $fund_id,
            'project_id' => $project_id,
            'departemen_id' => $departemen_id,
            'type' => $type,
            'start_date' => $start_date,
            'end_date' => $end_date,
        ])->render('pages.user.budget-absorption.index', compact([
            'project',
            'fund',
            'departemen',
            'fund_id',
            'project_id',
            'departemen_id',
            'type',
            'start_date',
            'end_date',
        ]));
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'project_id' => 'required|numeric|min:1',
            'name' => 'required|string',
            'description' => 'required|string',
            'date' => 'required|date',
            'value' => 'required|numeric|min:1',
            'po_number_sap' => 'nullable|numeric|min:1',
            'status' => 'required|string',
        ]);

        $request->validate([
            'attachment' => 'nullable|file|mimes:pdf',
        ]);

        $termin = BudgetAbsorption::where('project_id')->count() + 1;

        $data['user_id'] = auth()->user()->id;
        $data['termin'] = $termin;

        $budget_absorption = BudgetAbsorption::updateOrCreate($data, $data);

        if ($request->hasFile('attachment') && $request->attachment != '') {
            $imageName = time().'-'.$request->file('attachment')->getClientOriginalName();
            $detailPath = 'attachment/budget-absorption/';
            $destinationPath = public_path('storage/'. $detailPath);

            if(!file_exists($destinationPath)) {
                mkdir($destinationPath, 0777, true);
            }

            $request->file('attachment')->move($destinationPath, $imageName);

            $attachment = $detailPath.$imageName;

            $budget_absorption->update([
                "attachment" => $attachment,
            ]);
        }

        return redirect()->route('budget-absorption.index')->withNotify('Data berhasil ditambahkan');
    }

    public function edit(string $uuid)
    {
        $budget_absorption = BudgetAbsorption::where('uuid', $uuid)->firstOrFail();
        $project = Project::all();

        return view('pages.user.budget-absorption.edit', compact([
            'budget_absorption',
            'project'
        ]));
    }

    public function update(Request $request)
    {
        $data = BudgetAbsorption::findOrFail($request->id);

        $rawData = $request->validate([
            'project_id' => 'required|numeric|min:1',
            'name' => 'required|string',
            'description' => 'required|string',
            'date' => 'required|date',
            'value' => 'required|numeric|min:1',
            'po_number_sap' => 'nullable|numeric|min:1',
            'status' => 'required|string',
        ]);

        $request->validate([
            'attachment' => 'nullable|file|mimes:pdf',
        ]);

        $rawData['user_id'] = auth()->user()->id;

        $data->update($rawData);

        if ($request->hasFile('attachment') && $request->attachment != '') {
            $imageName = time().'-'.$request->file('attachment')->getClientOriginalName();
            $detailPath = 'attachment/budget-absorption/';
            $destinationPath = public_path('storage/'. $detailPath);

            if(!file_exists($destinationPath)) {
                mkdir($destinationPath, 0777, true);
            }

            if ($data->attachment != null) {
                Storage::delete($data->attachment);
            }

            $request->file('attachment')->move($destinationPath, $imageName);

            $attachment = $detailPath.$imageName;

            $data->update([
                "attachment" => $attachment,
            ]);
        }

        return redirect()->route('budget-absorption.index')->withNotify('Data berhasil diperbaharui');
    }

    public function destroy(Request $request)
    {
        $data = BudgetAbsorption::findOrFail($request->id);
        $data->delete();

        return redirect()->route('budget-absorption.index')->withNotify('Data berhasil dihapus');
    }
}
