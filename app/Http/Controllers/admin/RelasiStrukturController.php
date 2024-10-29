<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Departemen;
use App\Models\Direktorat;
use App\Models\Divisi;
use App\Models\RelasiStruktur;
use App\Models\Seksi;
use Illuminate\Http\Request;

class RelasiStrukturController extends Controller
{
    public function index()
    {
        $struktur = RelasiStruktur::all();

        $direktorat = Direktorat::all();
        $divisi = Divisi::all();
        $departemen = Departemen::all();
        $seksi = Seksi::all();

        return view('pages.admin.struktur.index', compact([
            'struktur',
            'direktorat',
            'divisi',
            'departemen',
            'seksi',
        ]));
    }

    public function store(Request $request)
    {
        $request->validate([
            'direktorat_id' => 'required',
            'divisi_id' => 'required',
            'departemen_id' => 'required',
            'seksi_id' => 'required',
        ]);

        RelasiStruktur::create(([
            'direktorat_id' => $request->direktorat_id,
            'divisi_id' => $request->divisi_id,
            'departemen_id' => $request->departemen_id,
            'seksi_id' => $request->seksi_id,
        ]));

        return redirect()->route('struktur.index');
    }

    public function show(string $id)
    {
        //
    }

    public function edit(string $uuid)
    {
        $struktur = RelasiStruktur::where('uuid', $uuid)->firstOrFail();

        $direktorat = Direktorat::all();
        $divisi = Divisi::all();
        $departemen = Departemen::all();
        $seksi = Seksi::all();

        return view('pages.admin.struktur.edit', compact([
            'struktur',
            'direktorat',
            'divisi',
            'departemen',
            'seksi',
        ]));
    }

    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'direktorat_id' => 'required',
            'divisi_id' => 'required',
            'departemen_id' => 'required',
            'seksi_id' => 'required',
        ]);

        $data = RelasiStruktur::findOrFail($request->id);
        $data->update([
            'direktorat_id' => $request->direktorat_id,
            'divisi_id' => $request->divisi_id,
            'departemen_id' => $request->departemen_id,
            'seksi_id' => $request->seksi_id,
        ]);

        return redirect()->route('struktur.index');
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'id' => 'required',
        ]);

        $data = RelasiStruktur::findOrFail($request->id);
        $data->delete();

        return redirect()->route('struktur.index');
    }
}
