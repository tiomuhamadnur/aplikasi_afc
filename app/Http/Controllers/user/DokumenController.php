<?php

namespace App\Http\Controllers\user;

use App\DataTables\DokumenDataTable;
use App\Http\Controllers\Controller;
use App\Models\Departemen;
use App\Models\Dokumen;
use App\Models\TipeDokumen;
use Illuminate\Http\Request;

class DokumenController extends Controller
{
    public function index(DokumenDataTable $dataTable, Request $request)
    {
        $request->validate([
            'departemen_id' => 'nullable',
            'tipe_dokumen_id' => 'nullable',
        ]);

        $tipe_dokumen_id = $request->tipe_dokumen_id ?? null;
        $departemen_id = $request->departemen_id ?? null;

        $tipe_dokumen = TipeDokumen::all();
        $departemen = Departemen::all();

        return $dataTable->with([
            'departemen_id' => $departemen_id,
            'tipe_dokumen_id' => $tipe_dokumen_id,
        ])->render('pages.user.dokumen.index', compact([
            'departemen',
            'tipe_dokumen',
            'departemen_id',
            'tipe_dokumen_id',
        ]));
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'departemen_id' => 'required|exists:departemen,id',
            'tipe_dokumen_id' => 'required|exists:tipe_dokumen,id',
            'judul' => 'required|string',
            'nomor' => 'required|string',
            'nomor_revisi' => 'required|string',
            'tanggal_pengesahan' => 'required|date',
            'url' => 'required|string',
            'keterangan' => 'nullable|string',
        ]);

        Dokumen::updateOrCreate($data, $data);

        return redirect()->route('dokumen.index')->withNotify('Data berhasil ditambahkan');
    }

    public function show(string $id)
    {
        //
    }

    public function edit(string $id)
    {
        //
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'id' => 'required|numeric',
            'departemen_id' => 'required|exists:departemen,id',
            'tipe_dokumen_id' => 'required|exists:tipe_dokumen,id',
            'judul' => 'required|string',
            'nomor' => 'required|string',
            'nomor_revisi' => 'required|string',
            'tanggal_pengesahan' => 'required|date',
            'url' => 'required|string',
            'keterangan' => 'nullable|string',
        ]);

        $dokumen = Dokumen::findOrFail($request->id);

        $dokumen->update($data);

        return redirect()->route('dokumen.index')->withNotify('Data berhasil diperbaharui');
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'id' => 'required|numeric',
        ]);

        $dokumen = Dokumen::findOrFail($request->id);

        $dokumen->delete();

        return redirect()->route('dokumen.index')->withNotify('Data berhasil dihapus');
    }
}
