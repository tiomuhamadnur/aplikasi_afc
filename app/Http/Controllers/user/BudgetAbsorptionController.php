<?php

namespace App\Http\Controllers\user;

use App\DataTables\BudgetAbsorptionDataTable;
use App\Http\Controllers\Controller;
use App\Models\BudgetAbsorption;
use App\Models\Departemen;
use App\Models\Fund;
use App\Models\FundSource;
use App\Models\Project;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Laraindo\RupiahFormat;

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
            // 'description' => 'required|string',
            'activity_date' => 'required|date',
            'paid_date' => 'nullable|date',
            'value' => 'required|numeric|min:1',
            'po_number_sap' => 'nullable|numeric|min:1',
            'status' => 'required|string',
        ]);

        $request->validate([
            'attachment' => 'nullable|file|mimes:pdf',
        ]);

        $termin = BudgetAbsorption::where('project_id', $request->project_id)->count() + 1;
        $project = Project::findOrFail($request->project_id);

        $data['user_id'] = auth()->user()->id;
        $data['termin'] = $termin;

        $result = $this->check_fund_source_budget($project->fund_source_id, $request->value);
        if($result['value'] < 0)
        {
            return redirect()->route('budget-absorption.index')->withNotifyerror('Nilai budget activity ini melebihi sisa budget Fund ' . $project->fund_source->fund->code . ', tersisa ' . RupiahFormat::currency($result['remaining_budget']));
        }

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

        return redirect()->route('project.show', $project->uuid)->withNotify('Data berhasil ditambahkan');
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
            // 'description' => 'required|string',
            'activity_date' => 'required|date',
            'paid_date' => 'nullable|date',
            'value' => 'required|numeric|min:1',
            'po_number_sap' => 'nullable|numeric|min:1',
            'status' => 'required|string',
        ]);

        $request->validate([
            'attachment' => 'nullable|file|mimes:pdf',
        ]);

        $rawData['user_id'] = auth()->user()->id;
        $project = Project::findOrFail($request->project_id);

        $result = $this->check_fund_source_budget($project->fund_source_id, $request->value);
        if(($result['value'] + $data->value) < 0)
        {
            return redirect()->route('budget-absorption.index')->withNotifyerror('Nilai budget activity ini melebihi sisa budget Fund ' . $project->fund_source->fund->code . ', tersisa ' . RupiahFormat::currency($result['remaining_budget']));
        }

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

        return redirect()->route('project.show', $project->uuid)->withNotify('Data berhasil diperbaharui');
    }

    public function check_fund_source_budget($fund_source_id, $value)
    {
        $fund_source = FundSource::findOrFail($fund_source_id);
        $absorbed_budget = BudgetAbsorption::whereHas('project', function ($query) use ($fund_source_id) {
            $query->where('fund_source_id', $fund_source_id);
        })->sum('value');

        $remaining_budget = $fund_source->balance - $absorbed_budget;
        $result = [
            'value' => $remaining_budget - $value,
            'remaining_budget' => $remaining_budget,
        ];

        return $result;
    }

    public function destroy(Request $request)
    {
        $data = BudgetAbsorption::findOrFail($request->id);
        $data->delete();

        return redirect()->route('budget-absorption.index')->withNotify('Data berhasil dihapus');
    }
}
