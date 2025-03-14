<?php

namespace App\Http\Controllers\user;

use App\DataTables\BudgetAbsorptionDataTable;
use App\Http\Controllers\Controller;
use App\Models\BudgetAbsorption;
use App\Models\Departemen;
use App\Models\Fund;
use App\Models\FundSource;
use App\Models\Project;
use App\Models\StatusBudgeting;
use App\Services\FileUploadService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Laraindo\RupiahFormat;

class BudgetAbsorptionController extends Controller
{
    protected $fileUploadService;

    public function __construct(FileUploadService $fileUploadService)
    {
        $this->fileUploadService = $fileUploadService;
    }

    public function index(BudgetAbsorptionDataTable $dataTable, Request $request)
    {
        $request->validate([
            'fund_id' => 'nullable',
            'project_id' => 'nullable',
            'departemen_id' => 'nullable',
            'type' => 'nullable',
            'status' => 'nullable',
            'start_date' => 'nullable',
            'end_date' => 'nullable',
        ]);

        $fund_id = $request->fund_id ?? null;
        $project_id = $request->project_id ?? null;
        $departemen_id = $request->departemen_id ?? null;
        $type = $request->type ?? null;
        $status = $request->status ?? null;
        $start_date = $request->start_date;
        $end_date = $request->end_date;

        $project = Project::all();
        $fund = Fund::all();
        $departemen = Departemen::all();

        return $dataTable
            ->with([
                'fund_id' => $fund_id,
                'project_id' => $project_id,
                'departemen_id' => $departemen_id,
                'type' => $type,
                'status' => $status,
                'start_date' => $start_date,
                'end_date' => $end_date,
            ])
            ->render('pages.user.budget-absorption.index', compact(['project', 'fund', 'departemen', 'fund_id', 'project_id', 'departemen_id', 'type', 'status', 'start_date', 'end_date']));
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

        if ($result['value'] < 0) {
            return redirect()
                ->route('project.show', $project->uuid)
                ->withNotifyerror('Nilai budget activity ini melebihi sisa budget Fund ' . $project->fund_source->fund->code . ', tersisa ' . RupiahFormat::currency($result['remaining_budget']));
        }

        $budget_absorption = BudgetAbsorption::updateOrCreate($data, $data);

        // Handle file attachment using FileUploadService
        if ($request->hasFile('attachment')) {
            $attachmentPath = $this->fileUploadService->uploadFile($request->file('attachment'), 'attachment/budget-absorption/');

            // Update attachment path in the database
            $budget_absorption->update(['attachment' => $attachmentPath]);
        }

        return redirect()->route('project.show', $project->uuid)->withNotify('Data berhasil ditambahkan');
    }

    public function edit(string $uuid)
    {
        $this_year = Carbon::now()->year;
        $budget_absorption = BudgetAbsorption::where('uuid', $uuid)->firstOrFail();
        $project = auth()->user()->role_id == 1
                ? Project::all()
                : Project::whereRelation('fund_source', 'year', '=', $this_year)->get();

        return view('pages.user.budget-absorption.edit', compact(['budget_absorption', 'project']));
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
        if ($result['value'] + $data->value < 0) {
            return redirect()
                ->route('project.show', $project->uuid)
                ->withNotifyerror('Nilai budget activity ini melebihi sisa budget Fund ' . $project->fund_source->fund->code . ', tersisa ' . RupiahFormat::currency($result['remaining_budget']));
        }

        $data->update($rawData);

        // Handle file attachment using FileUploadService
        if ($request->hasFile('attachment')) {
            $attachmentPath = $this->fileUploadService->uploadFile($request->file('attachment'), 'attachment/budget-absorption/');

            // Delete attachment lama
            if ($data->attachment != null) {
                Storage::delete($data->attachment);
            }

            // Update attachment path in the database
            $data->update(['attachment' => $attachmentPath]);
        }

        return redirect()->route('project.show', $project->uuid)->withNotify('Data berhasil diperbaharui');
    }

    public function show(string $uuid, BudgetAbsorptionDataTable $dataTable, Request $request)
    {
        $project = Project::where('uuid', $uuid)->firstOrFail();

        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
        ]);

        $start_date = $request->start_date ?? null;
        $end_date = $request->end_date ?? $start_date;

        $project_value = RupiahFormat::currency($project->value);
        $absorbed_budget = RupiahFormat::currency(BudgetAbsorption::where('project_id',  $project->id)->sum('value'));
        $remaining_budget = RupiahFormat::currency($project->value - BudgetAbsorption::where('project_id',  $project->id)->sum('value'));

        return $dataTable
            ->with([
                'project_id' => $project->id,
                'start_date' => $start_date,
                'end_date' => $end_date,
            ])
            ->render('pages.user.budget-absorption.by_project', compact([
                'project',
                'project_value',
                'absorbed_budget',
                'remaining_budget',
                'start_date',
                'end_date',
            ]));
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

        return redirect()->route('budget-absorption.by_project.show', $data->project->uuid)->withNotify('Data berhasil dihapus');
    }
}
