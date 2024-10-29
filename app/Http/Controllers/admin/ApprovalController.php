<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Approval;
use App\Models\Jabatan;
use App\Models\RelasiStruktur;
use App\Models\TipeEmployee;
use Illuminate\Http\Request;

class ApprovalController extends Controller
{
    public function index()
    {
        $approval = Approval::orderBy('relasi_struktur_id', 'ASC')->orderBy('priority', 'ASC')->get();
        $relasi_struktur = RelasiStruktur::all();
        $jabatan = Jabatan::all();
        $tipe_employee = TipeEmployee::all();

        return view('pages.admin.approval.index', compact([
            'approval',
            'relasi_struktur',
            'jabatan',
            'tipe_employee',
        ]));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'relasi_struktur_id' => 'required|numeric',
            'jabatan_id' => 'required|numeric',
            'tipe_employee_id' => 'required|numeric',
            'priority' => 'required|numeric',
            'name' => 'required|string',
        ]);

        Approval::updateOrCreate($data, $data);

        return redirect()->route('approval.index')->withNotify('Data berhasil ditambakan');
    }

    public function edit(string $uuid)
    {
        $approval = Approval::where('uuid', $uuid)->firstOrFail();
        $relasi_struktur = RelasiStruktur::all();
        $jabatan = Jabatan::all();
        $tipe_employee = TipeEmployee::all();

        return view('pages.admin.approval.edit', compact([
            'approval',
            'relasi_struktur',
            'jabatan',
            'tipe_employee',
        ]));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'relasi_struktur_id' => 'required|numeric',
            'jabatan_id' => 'required|numeric',
            'tipe_employee_id' => 'required|numeric',
            'priority' => 'required|numeric',
            'name' => 'required|string',
        ]);

        $request->validate([
            'id' => 'numeric|required|min:1'
        ]);

        $approval = Approval::findOrFail($request->id);
        $approval->update($data);

        return redirect()->route('approval.index')->withNotify('Data berhasil diubah');
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'id' => 'numeric|required|min:1'
        ]);

        $approval = Approval::findOrFail($request->id);
        $approval->delete();

        return redirect()->route('approval.index')->withNotify('Data berhasil dihapus');
    }
}
