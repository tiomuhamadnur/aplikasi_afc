<?php

namespace App\Http\Controllers\user;

use App\Http\Controllers\Controller;
use App\Models\MonitoringPermit;
use App\Models\RelasiArea;
use App\Models\TipePekerjaan;
use App\Models\TipePermit;
use Carbon\Carbon;
use Illuminate\Http\Request;

class MonitoringPermitController extends Controller
{
    public function index()
    {
        $monitoring_permit = MonitoringPermit::all();
        $area = RelasiArea::all();
        $tipe_permit = TipePermit::all();
        $tipe_pekerjaan = TipePekerjaan::all();

        return view('pages.user.monitoring-permit.index', compact([
            'monitoring_permit',
            'area',
            'tipe_permit',
            'tipe_pekerjaan',
        ]));
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $request->validate([
            'tipe_permit_id' => 'required|numeric',
            'tipe_pekerjaan_id' => 'required|numeric',
            'name' => 'required',
            'nomor' => 'required',
            'tanggal_expired' => 'required|date',
            'relasi_area_id' => 'nullable|numeric',
        ]);

        $tanggal_sekarang = Carbon::now();
        $status = 'active';

        if($request->tanggal_expired < $tanggal_sekarang){
            $status = 'expired';
        }

        MonitoringPermit::create(([
            'tipe_permit_id' => $request->tipe_permit_id,
            'tipe_pekerjaan_id' => $request->tipe_pekerjaan_id,
            'nomor' => $request->nomor,
            'name' => $request->name,
            'tanggal_expired' => $request->tanggal_expired,
            'relasi_area_id' => $request->relasi_area_id,
            'status' => $status,
            'user_id' => auth()->user()->id,
        ]));

        return redirect()->route('monitoring-permit.index');
    }

    public function show(string $id)
    {
        //
    }

    public function edit($uuid)
    {
        $monitoring_permit = MonitoringPermit::where('uuid', $uuid)->firstOrFail();

        $area = RelasiArea::all();
        $tipe_permit = TipePermit::all();
        $tipe_pekerjaan = TipePekerjaan::all();

        return view('pages.user.monitoring-permit.edit', compact([
            'monitoring_permit',
            'area',
            'tipe_permit',
            'tipe_pekerjaan',
        ]));
    }

    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required|numeric',
            'tipe_permit_id' => 'required|numeric',
            'tipe_pekerjaan_id' => 'required|numeric',
            'name' => 'required',
            'nomor' => 'required',
            'tanggal_expired' => 'required|date',
            'relasi_area_id' => 'nullable|numeric',
        ]);

        $tanggal_sekarang = Carbon::now();
        $status = 'active';

        if($request->tanggal_expired < $tanggal_sekarang){
            $status = 'expired';
        }

        $data = MonitoringPermit::findOrFail($request->id);
        $data->update([
            'tipe_permit_id' => $request->tipe_permit_id,
            'tipe_pekerjaan_id' => $request->tipe_pekerjaan_id,
            'nomor' => $request->nomor,
            'name' => $request->name,
            'tanggal_expired' => $request->tanggal_expired,
            'relasi_area_id' => $request->relasi_area_id,
            'status' => $status,
            'user_id' => auth()->user()->id,
        ]);

        return redirect()->route('monitoring-permit.index');
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'id' => 'required',
        ]);

        $data = MonitoringPermit::findOrFail($request->id);
        $data->delete();

        return redirect()->route('monitoring-permit.index');
    }
}
