<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\MonitoringPermit;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $departemen_id = auth()->user()->relasi_struktur->departemen->id;
        $permit = MonitoringPermit::where('departemen_id', $departemen_id)->count();

        return view('pages.admin.dashboard.index', compact([
            'permit'
        ]));
    }

    public function create()
    {
        //
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
