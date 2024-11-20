<?php

namespace App\Http\Controllers\user;

use App\Http\Controllers\Controller;
use App\Models\BudgetAbsorption;
use App\Models\Departemen;
use App\Models\Fund;
use App\Models\FundSource;
use App\Models\Project;
use App\Models\RelasiStruktur;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Laraindo\RupiahFormat;

class DashboardBudgetController extends Controller
{
    public function index()
    {
        $sekarang = Carbon::now();
        $today = $sekarang->isoFormat('dddd, D MMM Y - HH:mm:ss') . ' WIB';
        $hari_ini = $sekarang->toDateString();
        $divisi_id = auth()->user()->relasi_struktur->divisi_id;

        // BALANCE TOTAL
        $balance_total = FundSource::whereRelation('fund.divisi', 'id', '=', $divisi_id)
                        ->whereDate('start_period', '<=', $hari_ini)
                        ->whereDate('end_period', '>=', $hari_ini)
                        ->get();

        $total_balance = $balance_total->sum('balance');

        $used_balance = BudgetAbsorption::whereRelation('project.fund_source.fund.divisi', 'id', '=', $divisi_id)
                        ->whereRelation('project.fund_source', 'start_period', '<=', $hari_ini)
                        ->whereRelation('project.fund_source', 'end_period', '>=', $hari_ini)
                        ->whereIn('status', ['Realisasi Kegiatan', 'Realisasi Pembayaran'])
                        ->sum('value');

        $planned_balance = BudgetAbsorption::whereRelation('project.fund_source.fund.divisi', 'id', '=', $divisi_id)
                        ->whereRelation('project.fund_source', 'start_period', '<=', $hari_ini)
                        ->whereRelation('project.fund_source', 'end_period', '>=', $hari_ini)
                        ->where('status', 'Planned')
                        ->sum('value');

        $remaining_balance = $total_balance - ($used_balance + $planned_balance);


        // OPEX & CAPEX
        $opex = BudgetAbsorption::whereRelation('project.fund_source.fund.divisi', 'id', '=', $divisi_id)
                        ->whereRelation('project.fund_source.fund', 'type', '=', 'opex')
                        ->whereRelation('project.fund_source', 'start_period', '<=', $hari_ini)
                        ->whereRelation('project.fund_source', 'end_period', '>=', $hari_ini)
                        ->get();

        $balance_opex = $balance_total->filter(function ($item) {
                            return $item->fund->type === 'opex';
                        })->sum('balance');

        $groupedOpexData = $opex->groupBy('status');

        $getSumOpex = function ($status) use ($groupedOpexData) {
            return $groupedOpexData->has($status) ? $groupedOpexData->get($status)->sum('value') : 0;
        };

        $seriesOpex = [
            ['Realisasi Kegiatan', $getSumOpex("Realisasi Kegiatan")],
            ['Realisasi Pembayaran', $getSumOpex("Realisasi Pembayaran")],
            ['Proyeksi', $getSumOpex("Planned")],
            ['Sisa', $balance_opex - ($getSumOpex("Realisasi Pembayaran") + $getSumOpex("Realisasi Kegiatan") + $getSumOpex("Planned"))]
        ];

        $capex = BudgetAbsorption::whereRelation('project.fund_source.fund.divisi', 'id', '=', $divisi_id)
                        ->whereRelation('project.fund_source.fund', 'type', '=', 'capex')
                        ->whereRelation('project.fund_source', 'start_period', '<=', $hari_ini)
                        ->whereRelation('project.fund_source', 'end_period', '>=', $hari_ini)
                        ->get();

        $balance_capex = $balance_total->filter(function ($item) {
                    return $item->fund->type === 'capex';
                })->sum('balance');

        $groupedCapexData = $capex->groupBy('status');

        $getSumCapex = function ($status) use ($groupedCapexData) {
            return $groupedCapexData->has($status) ? $groupedCapexData->get($status)->sum('value') : 0;
        };

        $seriesCapex = [
            ['Realisasi Kegiatan', $getSumCapex("Realisasi Kegiatan")],
            ['Realisasi Pembayaran', $getSumCapex("Realisasi Pembayaran")],
            ['Proyeksi', $getSumCapex("Planned")],
            ['Sisa', $balance_capex - ($getSumCapex("Realisasi Pembayaran") + $getSumCapex("Realisasi Kegiatan") + $getSumCapex("Planned"))]
        ];


        $seriesCapexOpexTotal = [
            ['Realisasi Kegiatan', $seriesCapex[0][1] + $seriesOpex[0][1]], // Menjumlahkan "Realisasi Kegiatan"
            ['Realisasi Pembayaran', $seriesCapex[1][1] + $seriesOpex[1][1]], // Menjumlahkan "Realisasi Pembayaran"
            ['Proyeksi', $seriesCapex[2][1] + $seriesOpex[2][1]], // Menjumlahkan "Proyeksi"
            ['Sisa', $seriesCapex[3][1] + $seriesOpex[3][1]] // Menjumlahkan "Sisa"
        ];



        // TIAP DEPARTEMEN
        $departemen_id = RelasiStruktur::where('divisi_id', $divisi_id)
                    ->pluck('departemen_id')
                    ->unique()
                    ->toArray();

        $departments = Departemen::whereIn('id', $departemen_id)
                    ->select('id', 'code', 'uuid')
                    ->get();

        // Buat array categoriesDepartemen dengan nama dan URL masing-masing
        $categoriesDepartemen = $departments->map(function ($departemen) {
            return [
                'name' => $departemen->code,
                'url' => route('dashboard-budget.departemen', ['departemen_uuid' => $departemen->uuid]),
            ];
        })->toArray();

        // Data untuk series, nilai random dalam miliaran
        $seriesData = [
            'Realisasi Kegiatan' => [],
            'Realisasi Pembayaran' => [],
            'Proyeksi' => [],
            'Sisa' => [],
        ];

        foreach ($departments as $department) {
            $kegiatan = BudgetAbsorption::where('status', 'Realisasi Kegiatan')
                                    ->whereRelation('project.departemen', 'id', '=', $department->id)
                                    ->whereRelation('project.fund_source', 'start_period', '<=', $hari_ini)
                                    ->whereRelation('project.fund_source', 'end_period', '>=', $hari_ini)
                                    ->sum('value');
            $pembayaran = BudgetAbsorption::where('status', 'Realisasi Pembayaran')
                                    ->whereRelation('project.departemen', 'id', '=', $department->id)
                                    ->whereRelation('project.fund_source', 'start_period', '<=', $hari_ini)
                                    ->whereRelation('project.fund_source', 'end_period', '>=', $hari_ini)
                                    ->sum('value');
            $dept_planned = BudgetAbsorption::where('status', 'Planned')
                                    ->whereRelation('project.departemen', 'id', '=', $department->id)
                                    ->whereRelation('project.fund_source', 'start_period', '<=', $hari_ini)
                                    ->whereRelation('project.fund_source', 'end_period', '>=', $hari_ini)
                                    ->sum('value');
            $fund_source_ids = BudgetAbsorption::whereRelation('project.departemen', 'id', '=', $department->id)->whereRelation('project.fund_source', 'start_period', '<=', $hari_ini)->whereRelation('project.fund_source', 'end_period', '>=', $hari_ini)->pluck('id')->toArray();
            $total = FundSource::whereIn('id', $fund_source_ids)->sum('balance');

            $seriesData['Realisasi Kegiatan'][] = $kegiatan;
            $seriesData['Realisasi Pembayaran'][] = $pembayaran;
            $seriesData['Proyeksi'][] = $dept_planned;
            $seriesData['Sisa'][] = $total - ($kegiatan + $pembayaran + $dept_planned);
        }

        // Format data series untuk Highcharts
        $series = [];
        foreach ($seriesData as $name => $data) {
            $series[] = [
                'name' => $name,
                'data' => $data,
            ];
        }


        // TIAP FUND
        $funds = Fund::with([
                        'fund_source.project.budget_absorption' => function ($query) use ($hari_ini) {
                            $query->whereHas('project.fund_source', function ($q) use ($hari_ini) {
                                $q->where('start_period', '<=', $hari_ini)
                                        ->where('end_period', '>=', $hari_ini);
                            });
                        }
                    ])
                    ->whereRelation('divisi', 'id', '=', $divisi_id)
                    ->get();

        $categoriesFund = [];
        $realisasiKegiatan = [];
        $realisasiPembayaran = [];
        $proyeksiFund = []; // Placeholder untuk proyeksi
        $sisa = [];

        foreach ($funds as $fund) {
            $categoriesFund[] = $fund->code; // Gunakan kode Fund sebagai kategori

            // Menghitung total balance untuk Fund
            $totalBalance = $fund->fund_source->sum('balance');

            // Menginisialisasi nilai total
            $totalAbsorbed = 0;
            $totalRealisasiKegiatan = 0;
            $totalRealisasiPembayaran = 0;
            $totalProyeksi = 0;

            // Iterasi pada setiap Fund Source
            foreach ($fund->fund_source as $fundSource) {
                // Iterasi pada setiap Project di Fund Source
                foreach ($fundSource->project as $project) {
                    // Menghitung nilai berdasarkan status
                    $totalRealisasiKegiatan += $project->budget_absorption
                        ->where('status', 'Realisasi Kegiatan')
                        ->sum('value');

                    $totalRealisasiPembayaran += $project->budget_absorption
                        ->where('status', 'Realisasi Pembayaran')
                        ->sum('value');

                    $totalProyeksi += $project->budget_absorption
                        ->where('status', 'Planned')
                        ->sum('value');

                    // Menghitung total anggaran yang terserap
                    $totalAbsorbed += $project->budget_absorption->sum('value');
                }
            }

            // Menyimpan hasil ke array masing-masing
            $realisasiKegiatan[] = $totalRealisasiKegiatan;
            $realisasiPembayaran[] = $totalRealisasiPembayaran;
            $proyeksiFund[] = $totalProyeksi;
            $sisa[] = $totalBalance - $totalAbsorbed;
        }

        // Bentuk data series untuk Highcharts
        $seriesFund = [
            [
                'name' => 'Realisasi Kegiatan',
                'data' => $realisasiKegiatan,
            ],
            [
                'name' => 'Realisasi Pembayaran',
                'data' => $realisasiPembayaran,
            ],
            [
                'name' => 'Proyeksi',
                'data' => $proyeksiFund,
            ],
            [
                'name' => 'Sisa',
                'data' => $sisa,
            ],
        ];


        return view('pages.user.dashboard-budgeting.index', [
            'today' => $today,
            'total_balance' => RupiahFormat::currency($total_balance),
            'used_balance' => RupiahFormat::currency($used_balance),
            'planned_balance' => RupiahFormat::currency($planned_balance),
            'remaining_balance' => RupiahFormat::currency($remaining_balance),
            'categoriesDepartemen' => $categoriesDepartemen,
            'series' => $series,
            'seriesOpex' => $seriesOpex,
            'seriesCapex' => $seriesCapex,
            'seriesCapexOpexTotal' => $seriesCapexOpexTotal,
            'categoriesFund' => $categoriesFund,
            'seriesFund' => $seriesFund,
        ]);
    }

    public function departemen(Request $request)
    {
        $departemen = Departemen::where('uuid', $request->departemen_uuid)->firstOrFail();
        $departemen_id = $departemen->id;
        $sekarang = Carbon::now();
        $today = $sekarang->isoFormat('dddd, D MMM Y - HH:mm:ss') . ' WIB';
        $hari_ini = $sekarang->toDateString();

        // BALANCE TOTAL
        $fund_source_ids = Project::where('departemen_id', $departemen_id)
                ->pluck('fund_source_id')
                ->toArray();

        $balance_total = FundSource::whereIn('id', $fund_source_ids)
                ->whereDate('start_period', '<=', $hari_ini)
                ->whereDate('end_period', '>=', $hari_ini)
                ->get();

        $total_balance = $balance_total->sum('balance');

        $used_balance = BudgetAbsorption::whereRelation('project.departemen', 'id', '=', $departemen_id)
                ->whereRelation('project.fund_source', 'start_period', '<=', $hari_ini)
                ->whereRelation('project.fund_source', 'end_period', '>=', $hari_ini)
                ->whereIn('status', ['Realisasi Kegiatan', 'Realisasi Pembayaran'])
                ->sum('value');

        $planned_balance = BudgetAbsorption::whereRelation('project.departemen', 'id', '=', $departemen_id)
                ->whereRelation('project.fund_source', 'start_period', '<=', $hari_ini)
                ->whereRelation('project.fund_source', 'end_period', '>=', $hari_ini)
                ->where('status', 'Planned')
                ->sum('value');

        $remaining_balance = $total_balance - ($used_balance + $planned_balance);


        // OPEX & CAPEX
        $opex = BudgetAbsorption::whereRelation('project.departemen', 'id', '=', $departemen_id)
                ->whereRelation('project.fund_source.fund', 'type', '=', 'opex')
                ->whereRelation('project.fund_source', 'start_period', '<=', $hari_ini)
                ->whereRelation('project.fund_source', 'end_period', '>=', $hari_ini)
                ->get();

        $balance_opex = $balance_total->filter(function ($item) {
                            return $item->fund->type === 'opex';
                        })->sum('balance');

        $groupedOpexData = $opex->groupBy('status');

        $getSumOpex = function ($status) use ($groupedOpexData) {
            return $groupedOpexData->has($status) ? $groupedOpexData->get($status)->sum('value') : 0;
        };

        $seriesOpex = [
            ['Realisasi Kegiatan', $getSumOpex("Realisasi Kegiatan")],
            ['Realisasi Pembayaran', $getSumOpex("Realisasi Pembayaran")],
            ['Proyeksi', $getSumOpex("Planned")],
            ['Sisa', $balance_opex - ($getSumOpex("Realisasi Pembayaran") + $getSumOpex("Realisasi Kegiatan") + $getSumOpex("Planned"))]
        ];

        $capex = BudgetAbsorption::whereRelation('project.departemen', 'id', '=', $departemen_id)
                ->whereRelation('project.fund_source.fund', 'type', '=', 'capex')
                ->whereRelation('project.fund_source', 'start_period', '<=', $hari_ini)
                ->whereRelation('project.fund_source', 'end_period', '>=', $hari_ini)
                ->get();

        $balance_capex = $balance_total->filter(function ($item) {
                    return $item->fund->type === 'capex';
                })->sum('balance');

        $groupedCapexData = $capex->groupBy('status');

        $getSumCapex = function ($status) use ($groupedCapexData) {
            return $groupedCapexData->has($status) ? $groupedCapexData->get($status)->sum('value') : 0;
        };

        $seriesCapex = [
            ['Realisasi Kegiatan', $getSumCapex("Realisasi Kegiatan")],
            ['Realisasi Pembayaran', $getSumCapex("Realisasi Pembayaran")],
            ['Proyeksi', $getSumCapex("Planned")],
            ['Sisa', $balance_capex - ($getSumCapex("Realisasi Pembayaran") + $getSumCapex("Realisasi Kegiatan") + $getSumCapex("Planned"))]
        ];


        $seriesCapexOpexTotal = [
            ['Realisasi Kegiatan', $seriesCapex[0][1] + $seriesOpex[0][1]], // Menjumlahkan "Realisasi Kegiatan"
            ['Realisasi Pembayaran', $seriesCapex[1][1] + $seriesOpex[1][1]], // Menjumlahkan "Realisasi Pembayaran"
            ['Proyeksi', $seriesCapex[2][1] + $seriesOpex[2][1]], // Menjumlahkan "Proyeksi"
            ['Sisa', $seriesCapex[3][1] + $seriesOpex[3][1]] // Menjumlahkan "Sisa"
        ];



        // TIAP FUND
        $funds = Fund::with([
            'fund_source.project.budget_absorption' => function ($query) use ($hari_ini) {
                $query->whereHas('project.fund_source', function ($q) use ($hari_ini) {
                    $q->where('start_period', '<=', $hari_ini)
                        ->where('end_period', '>=', $hari_ini);
                });
            }
        ])
        ->whereRelation('fund_source.project.departemen', 'id', '=', $departemen_id)
        ->get();

        $categoriesFund = [];
        $realisasiKegiatan = [];
        $realisasiPembayaran = [];
        $proyeksiFund = []; // Placeholder untuk proyeksi
        $sisa = [];

        foreach ($funds as $fund) {
            $categoriesFund[] = $fund->code; // Gunakan kode Fund sebagai kategori

            // Menghitung total balance untuk Fund
            $totalBalance = $fund->fund_source->sum('balance');

            // Menginisialisasi nilai total
            $totalAbsorbed = 0;
            $totalRealisasiKegiatan = 0;
            $totalRealisasiPembayaran = 0;
            $totalProyeksi = 0;

            // Iterasi pada setiap Fund Source
            foreach ($fund->fund_source as $fundSource) {
                // Iterasi pada setiap Project di Fund Source
                foreach ($fundSource->project as $project) {
                    // Menghitung nilai berdasarkan status
                    $totalRealisasiKegiatan += $project->budget_absorption
                        ->where('status', 'Realisasi Kegiatan')
                        ->sum('value');

                    $totalRealisasiPembayaran += $project->budget_absorption
                        ->where('status', 'Realisasi Pembayaran')
                        ->sum('value');

                    $totalProyeksi += $project->budget_absorption
                        ->where('status', 'Planned')
                        ->sum('value');

                    // Menghitung total anggaran yang terserap
                    $totalAbsorbed += $project->budget_absorption->sum('value');
                }
            }

            // Menyimpan hasil ke array masing-masing
            $realisasiKegiatan[] = $totalRealisasiKegiatan;
            $realisasiPembayaran[] = $totalRealisasiPembayaran;
            $proyeksiFund[] = $totalProyeksi;
            $sisa[] = $totalBalance - $totalAbsorbed;
        }

        // Bentuk data series untuk Highcharts
        $seriesFund = [
            [
                'name' => 'Realisasi Kegiatan',
                'data' => $realisasiKegiatan,
            ],
            [
                'name' => 'Realisasi Pembayaran',
                'data' => $realisasiPembayaran,
            ],
            [
                'name' => 'Proyeksi',
                'data' => $proyeksiFund,
            ],
            [
                'name' => 'Sisa',
                'data' => $sisa,
            ],
        ];

        return view('pages.user.dashboard-budgeting.departemen', [
            'today' => $today,
            'departemen' => $departemen,
            'total_balance' => RupiahFormat::currency($total_balance),
            'used_balance' => RupiahFormat::currency($used_balance),
            'planned_balance' => RupiahFormat::currency($planned_balance),
            'remaining_balance' => RupiahFormat::currency($remaining_balance),
            'seriesOpex' => $seriesOpex,
            'seriesCapex' => $seriesCapex,
            'seriesCapexOpexTotal' => $seriesCapexOpexTotal,
            'categoriesFund' => $categoriesFund,
            'seriesFund' => $seriesFund,
        ]);
    }

    public function store(Request $request)
    {
        //
    }

    public function show(string $id)
    {
        //
    }

    public function edit(string $id)
    {
        //
    }

    public function update(Request $request, string $id)
    {
        //
    }

    public function destroy(string $id)
    {
        //
    }
}
