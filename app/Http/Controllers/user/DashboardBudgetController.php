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
    public function index(Request $request)
    {
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        if ($request->start_date && $request->end_date) {
            $startYear = Carbon::parse($request->start_date)->year;
            $endYear = Carbon::parse($request->end_date)->year;

            if ($startYear !== $endYear) {
                return redirect()->back()->withNotifyerror('Tanggal evaluasi harus di tahun yang sama');
            }
        }

        $end_date = $request->end_date;

        $sekarang = $end_date ? Carbon::parse($end_date) : Carbon::now();
        $year = $sekarang->isoFormat('Y');


        $start_date = $request->start_date ?? Carbon::createFromDate($year)->startOfYear();
        $mulai = Carbon::parse($start_date)->isoFormat('D MMM Y');
        $akhir = $sekarang->isoFormat('D MMM Y');

        $today = $mulai . ' - ' . $akhir;

        $range = [$start_date, $end_date];
        $divisi_id = auth()->user()->relasi_struktur->divisi_id;

        // BALANCE TOTAL
        $balance_total = FundSource::whereRelation('fund.divisi', 'id', '=', $divisi_id)
                        ->whereYear('start_period',  $year)
                        ->whereYear('end_period', $year)
                        ->get();

        $total_balance = $balance_total->sum('balance');

        $used_balance = BudgetAbsorption::whereRelation('project.fund_source.fund.divisi', 'id', '=', $divisi_id)
                        ->whereBetween('activity_date', $range)
                        ->whereIn('status', ['Realisasi Kegiatan', 'Realisasi Pembayaran'])
                        ->sum('value');

        $planned_balance = BudgetAbsorption::whereRelation('project.fund_source.fund.divisi', 'id', '=', $divisi_id)
                        ->whereBetween('activity_date', $range)
                        ->where('status', 'Planned')
                        ->sum('value');

        $remaining_balance = $total_balance - ($used_balance + $planned_balance);


        // OPEX & CAPEX
        $opex = BudgetAbsorption::whereRelation('project.fund_source.fund.divisi', 'id', '=', $divisi_id)
                        ->whereRelation('project.fund_source.fund', 'type', '=', 'opex')
                        ->whereBetween('activity_date', $range)
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
                        ->whereBetween('activity_date', $range)
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
        // Ambil ID departemen yang terkait dengan divisi
        $departemen_id = RelasiStruktur::where('divisi_id', $divisi_id)
            ->pluck('departemen_id')
            ->unique()
            ->toArray();

        $departments = Departemen::whereIn('id', $departemen_id)
            ->select('id', 'code', 'uuid')
            ->get();

        // Buat array categoriesDepartemen dengan nama dan URL masing-masing
        $categoriesDepartemen = $departments->map(function ($departemen) use ($start_date, $sekarang) {
            return [
                'name' => 'Dept. ' . $departemen->code,
                'url' => route('dashboard-budget.departemen', [
                    'departemen_uuid' => $departemen->uuid,
                    'start_date' => Carbon::parse($start_date)->isoFormat('YYYY-MM-DD'),
                    'end_date' => $sekarang->isoFormat('YYYY-MM-DD'),
                ]),
            ];
        })->toArray();

        // Data untuk series
        $seriesData = [
            'Realisasi Kegiatan' => [],
            'Realisasi Pembayaran' => [],
            'Proyeksi' => [],
            'Sisa' => [],
        ];

        foreach ($departments as $department) {
            $kegiatan = BudgetAbsorption::where('status', 'Realisasi Kegiatan')
                ->whereRelation('project.departemen', 'id', $department->id)
                ->whereBetween('activity_date', $range)
                ->sum('value');

            $pembayaran = BudgetAbsorption::where('status', 'Realisasi Pembayaran')
                ->whereRelation('project.departemen', 'id', $department->id)
                ->whereBetween('activity_date', $range)
                ->sum('value');

            $dept_planned = BudgetAbsorption::where('status', 'Planned')
                ->whereRelation('project.departemen', 'id', $department->id)
                ->whereBetween('activity_date', $range)
                ->sum('value');

            // Ambil ID sumber dana dari BudgetAbsorption
            $fund_source_ids = BudgetAbsorption::whereRelation('project.departemen', 'id', $department->id)
                ->whereBetween('activity_date', $range)
                ->get() // Ambil seluruh data
                ->pluck('project.fund_source.id') // Akses relasi nested
                ->unique()
                ->toArray();

            // Hitung total balance dari sumber dana
            $total_balance_fund = FundSource::whereIn('id', $fund_source_ids)->sum('balance');

            $seriesData['Realisasi Kegiatan'][] = intval($kegiatan);
            $seriesData['Realisasi Pembayaran'][] = intval($pembayaran);
            $seriesData['Proyeksi'][] = intval($dept_planned);
            $seriesData['Sisa'][] = intval($total_balance_fund - ($kegiatan + $pembayaran + $dept_planned));
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
            'fund_source.project.budget_absorption' => function ($query) use ($year) {
                $query->whereHas('project.fund_source', function ($q) use ($year) {
                    $q->whereYear('start_period',  $year)
                    ->whereYear('end_period', $year);
                });
            }
        ])
        ->whereHas('fund_source', function ($query) use ($year) {
            $query->whereYear('start_period', $year)
                ->whereYear('end_period', $year);
        })
        ->whereRelation('divisi', 'id', '=', $divisi_id)
        ->get();

        $categoriesFund = [];
        $namesFund = [];
        $realisasiKegiatan = [];
        $realisasiPembayaran = [];
        $proyeksiFund = []; // Placeholder untuk proyeksi
        $sisa = [];

        foreach ($funds as $fund) {
            $categoriesFund[] = $fund->code; // Gunakan kode Fund sebagai kategori
            $namesFund[] = $fund->name;

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
            'start_date' => Carbon::parse($start_date)->isoFormat('YYYY-MM-DD'),
            'end_date' => $sekarang->isoFormat('YYYY-MM-DD'),
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
            'namesFund' => $namesFund,
            'seriesFund' => $seriesFund,
        ]);
    }

    public function departemen(Request $request)
    {
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        if ($request->start_date && $request->end_date) {
            $startYear = Carbon::parse($request->start_date)->year;
            $endYear = Carbon::parse($request->end_date)->year;

            if ($startYear !== $endYear) {
                return redirect()->back()->withNotifyerror('Tanggal evaluasi harus di tahun yang sama');
            }
        }

        $departemen = Departemen::where('uuid', $request->departemen_uuid)->firstOrFail();
        $departemen_id = $departemen->id;

        $end_date = $request->end_date;
        $sekarang = $end_date ? Carbon::parse($end_date) : Carbon::now();
        $year = $sekarang->isoFormat('Y');
        $start_date = $request->start_date ?? Carbon::createFromDate($year)->startOfYear();
        $mulai = Carbon::parse($start_date)->isoFormat('D MMM Y');
        $akhir = $sekarang->isoFormat('D MMM Y');
        $today = $mulai . ' - ' . $akhir;
        $range = [$start_date, $end_date];

        // BALANCE TOTAL
        $fund_source_ids = Project::where('departemen_id', $departemen_id)
                ->pluck('fund_source_id')
                ->toArray();

        $balance_total = FundSource::whereIn('id', $fund_source_ids)
                ->whereYear('start_period',  $year)
                ->whereYear('end_period',  $year)
                ->get();

        $total_balance = $balance_total->sum('balance');

        $used_balance = BudgetAbsorption::whereRelation('project.departemen', 'id', '=', $departemen_id)
                ->whereBetween('activity_date', $range)
                ->whereIn('status', ['Realisasi Kegiatan', 'Realisasi Pembayaran'])
                ->sum('value');

        $planned_balance = BudgetAbsorption::whereRelation('project.departemen', 'id', '=', $departemen_id)
                ->whereBetween('activity_date', $range)
                ->where('status', 'Planned')
                ->sum('value');

        $remaining_balance = $total_balance - ($used_balance + $planned_balance);


        // OPEX & CAPEX
        $opex = BudgetAbsorption::whereRelation('project.departemen', 'id', '=', $departemen_id)
                ->whereRelation('project.fund_source.fund', 'type', '=', 'opex')
                ->whereBetween('activity_date', $range)
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
                ->whereBetween('activity_date', $range)
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
            'fund_source.project.budget_absorption' => function ($query) use ($year) {
                $query->whereHas('project.fund_source', function ($q) use ($year) {
                    $q->whereYear('start_period', $year)
                    ->whereYear('end_period', $year);
                });
            }
        ])
        ->whereRelation('fund_source', function ($query) use ($year) {
            $query->whereYear('start_period', $year)
                ->whereYear('end_period', $year);
        })
        ->whereRelation('fund_source.project.departemen', 'id', '=', $departemen_id)
        ->get();


        $categoriesFund = [];
        $namesFund = [];
        $realisasiKegiatan = [];
        $realisasiPembayaran = [];
        $proyeksiFund = []; // Placeholder untuk proyeksi
        $sisa = [];

        foreach ($funds as $fund) {
            $categoriesFund[] = $fund->code;
            $namesFund[] = $fund->name;

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
            'start_date' => Carbon::parse($start_date)->isoFormat('YYYY-MM-DD'),
            'end_date' => $sekarang->isoFormat('YYYY-MM-DD'),
            'departemen' => $departemen,
            'total_balance' => RupiahFormat::currency($total_balance),
            'used_balance' => RupiahFormat::currency($used_balance),
            'planned_balance' => RupiahFormat::currency($planned_balance),
            'remaining_balance' => RupiahFormat::currency($remaining_balance),
            'seriesOpex' => $seriesOpex,
            'seriesCapex' => $seriesCapex,
            'seriesCapexOpexTotal' => $seriesCapexOpexTotal,
            'categoriesFund' => $categoriesFund,
            'namesFund' => $namesFund,
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
