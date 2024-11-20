<?php

namespace App\Http\Controllers\user;

use App\DataTables\BudgetAbsorptionDataTable;
use App\DataTables\ProjectDataTable;
use App\Http\Controllers\Controller;
use App\Models\Departemen;
use App\Models\FundSource;
use App\Models\Perusahaan;
use App\Models\Project;
use App\Models\RelasiStruktur;
use App\Models\StatusBudgeting;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index(ProjectDataTable $dataTable, Request $request)
    {
        $request->validate([
            'start_period' => 'date|nullable',
            'end_period' => 'date|nullable',
        ]);

        $start_period = $request->start_period ?? null;
        $end_period = $request->end_period ?? $start_period;
        $hari_ini = Carbon::now()->toDateString();

        $fund_source = FundSource::all();
        $relasi_struktur = RelasiStruktur::all();
        $departemen = Departemen::all();
        $perusahaan = Perusahaan::all();
        $status_budgeting = StatusBudgeting::all();

        return $dataTable->with([
            'hari_ini' => $hari_ini,
            'start_period' => $start_period,
            'end_period' => $end_period,
        ])->render('pages.user.project.index', compact([
            'fund_source',
            'relasi_struktur',
            'departemen',
            'perusahaan',
            'status_budgeting',
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
            // 'description' => 'required|string',
            'fund_source_id' => 'required|numeric',
            'start_period' => 'required|date',
            'end_period' => 'required|date',
            'departemen_id' => 'required|numeric|min:1',
            'perusahaan_id' => 'required|numeric|min:1',
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
            // 'description' => 'required|string',
            'fund_source_id' => 'required|numeric',
            'start_period' => 'required|date',
            'end_period' => 'required|date',
            'departemen_id' => 'required|numeric|min:1',
            'perusahaan_id' => 'required|numeric|min:1',
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
