<?php

namespace App\Http\Controllers\user;

use App\Http\Controllers\Controller;
use App\Models\Departemen;
use App\Models\RelasiStruktur;
use Illuminate\Http\Request;

class DashboardBudgetController extends Controller
{
    public function index()
    {
        $departemen_id = RelasiStruktur::where('divisi_id', auth()->user()->relasi_struktur->divisi_id)
                    ->pluck('departemen_id')
                    ->unique()
                    ->toArray();

        $departments = Departemen::whereIn('id', $departemen_id)
                    ->select('code', 'uuid')
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
            $seriesData['Realisasi Kegiatan'][] = rand(1, 15) * 1_000_000_000; // Nilai acak antara 1M - 15M
            $seriesData['Realisasi Pembayaran'][] = rand(5, 20) * 1_000_000_000;
            $seriesData['Proyeksi'][] = rand(0, 10) * 1_000_000_000;
            $seriesData['Sisa'][] = rand(2, 12) * 1_000_000_000;
        }

        // Format data series untuk Highcharts
        $series = [];
        foreach ($seriesData as $name => $data) {
            $series[] = [
                'name' => $name,
                'data' => $data,
            ];
        }

        return view('pages.user.dashboard-budgeting.index', compact([
            'categoriesDepartemen',
            'series',
        ]));
    }

    public function departemen(Request $request)
    {
        $departemen = Departemen::where('uuid', $request->departemen_uuid)->firstOrFail();
        $data = [];
        return view('pages.user.dashboard-budgeting.departemen', compact([
            'data',
            'departemen'
        ]));
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
