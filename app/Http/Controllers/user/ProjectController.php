<?php

namespace App\Http\Controllers\user;

use App\DataTables\BudgetAbsorptionDataTable;
use App\DataTables\ProjectDataTable;
use App\Http\Controllers\Controller;
use App\Models\Departemen;
use App\Models\Fund;
use App\Models\FundSource;
use App\Models\Perusahaan;
use App\Models\Project;
use App\Models\RelasiStruktur;
use App\Models\StatusBudgeting;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ProjectController extends Controller
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

    public function index(ProjectDataTable $dataTable, Request $request)
    {
        $request->validate([
            'fund_source_id' => 'nullable',
            'project_id' => 'nullable',
            'departemen_id' => 'nullable',
            'type' => 'nullable',
            'status_budgeting_id' => 'nullable',
            'year' => 'nullable',
        ]);

        $fund_source_id = $request->fund_source_id ?? null;
        $project_id = $request->project_id ?? null;
        $departemen_id = $request->departemen_id ?? null;
        $type = $request->type ?? null;
        $status_budgeting_id = $request->status_budgeting_id ?? null;
        $this_year = $request->year ?? date('Y');


        $fund_source = FundSource::where('year', $this_year)
                    ->orderBy('year', 'DESC')
                    ->orderBy('created_at', 'DESC')
                    ->get();
        $relasi_struktur = RelasiStruktur::all();
        $departemen = Departemen::all();
        $perusahaan = Perusahaan::all();
        $status_budgeting = StatusBudgeting::all();
        $years = $this->years;

        return $dataTable->with([
            'fund_source_id' => $fund_source_id,
            'departemen_id' => $departemen_id,
            'type' => $type,
            'status_budgeting_id' => $status_budgeting_id,
            'year' => $this_year,
        ])->render('pages.user.project.index', compact([
            'fund_source',
            'relasi_struktur',
            'departemen',
            'perusahaan',
            'status_budgeting',
            'fund_source_id',
            'departemen_id',
            'type',
            'status_budgeting_id',
            'years',
            'this_year',
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
            'fund_source_id' => 'required|numeric',
            // 'description' => 'required|string',
            // 'start_period' => 'required|date',
            // 'end_period' => 'required|date',
            'departemen_id' => 'required|numeric|min:1',
            'perusahaan_id' => 'required|numeric|min:1',
            'value' => 'required|numeric|min:1',
            'status_budgeting_id' => 'required|numeric',
        ]);

        $data['user_id'] = auth()->user()->id;

        Project::updateOrCreate($data, $data);

        return redirect()->route('project.index')->withNotify('Data berhasil ditambahkan');
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

        return $dataTable->with([
            'project_id' => $project->id,
            'start_date' => $start_date,
            'end_date' => $end_date,
        ])->render('pages.user.project.detail', compact([
            'project',
            'start_date',
            'end_date',
        ]));
    }

    public function edit(string $uuid)
    {
        $project = Project::where('uuid', $uuid)->firstOrFail();

        $fund_source = FundSource::all();
        $relasi_struktur = RelasiStruktur::all();
        $departemen = Departemen::all();
        $perusahaan = Perusahaan::all();
        $status_budgeting = StatusBudgeting::all();

        return view('pages.user.project.edit', compact([
            'project',
            'fund_source',
            'relasi_struktur',
            'departemen',
            'perusahaan',
            'status_budgeting',
        ]));
    }

    public function update(Request $request)
    {
        $data = Project::findOrFail($request->id);
        $rawData = $request->validate([
            'name' => 'required|string',
            'fund_source_id' => 'required|numeric',
            // 'description' => 'required|string',
            // 'start_period' => 'required|date',
            // 'end_period' => 'required|date',
            'departemen_id' => 'required|numeric|min:1',
            'perusahaan_id' => 'required|numeric|min:1',
            'value' => 'required|numeric|min:1',
            'status_budgeting_id' => 'required|numeric',
        ]);

        $rawData['user_id'] = auth()->user()->id;

        $data->update($rawData);

        return redirect()->route('project.index')->withNotify('Data berhasil diperbaharui');
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'id' => 'required',
        ]);

        $data = Project::findOrFail($request->id);
        $data->delete();

        return redirect()->route('project.index')->withNotify('Data berhasil dihapus');
    }
}
