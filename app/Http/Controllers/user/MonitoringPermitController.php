<?php

namespace App\Http\Controllers\user;

use App\Http\Controllers\Controller;
use App\Models\Departemen;
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
        $monitoring_permit = MonitoringPermit::where('departemen_id', auth()->user()->relasi_struktur->departemen->id)->get();
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

    public function filter(Request $request)
    {
        $departemen_id = auth()->user()->relasi_struktur->departemen->id;
        $nomor = $request->nomor;
        $tipe_permit_id = $request->tipe_permit_id;
        $tipe_pekerjaan_id = $request->tipe_pekerjaan_id;
        $relasi_area_id = $request->relasi_area_id;
        $status = $request->status;
        $start_date = $request->start_date;
        $end_date = $request->end_date ?? $start_date;

        $data = MonitoringPermit::query();

        $data->where('departemen_id', $departemen_id);

        // Filter by nomor
        $data->when($nomor, function ($query) use ($request) {
            return $query->where('nomor', $request->nomor);
        });

        // Filter by tipe_permit_id
        $data->when($tipe_permit_id, function ($query) use ($request) {
            return $query->where('tipe_permit_id', $request->tipe_permit_id);
        });

        // Filter by tipe_pekerjaan_id
        $data->when($tipe_pekerjaan_id, function ($query) use ($request) {
            return $query->where('tipe_pekerjaan_id', $request->tipe_pekerjaan_id);
        });

        // Filter by relasi_area_id
        $data->when($relasi_area_id, function ($query) use ($request) {
            return $query->where('relasi_area_id', $request->relasi_area_id);
        });

        // Filter by status
        $data->when($status, function ($query) use ($request) {
            return $query->where('status', $request->status);
        });

        // Filter by tanggal_expired
        $data->when($start_date && $end_date, function ($query) use ($start_date, $end_date) {
            return $query->whereBetween('tanggal_expired', [$start_date, $end_date]);
        });

        $monitoring_permit = $data->get();

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

        $departemen_id = auth()->user()->relasi_struktur->departemen->id;

        $tanggal_sekarang = Carbon::now();
        $status = 'active';

        if($request->tanggal_expired < $tanggal_sekarang){
            $status = 'expired';
        }

        MonitoringPermit::create(([
            'tipe_permit_id' => $request->tipe_permit_id,
            'tipe_pekerjaan_id' => $request->tipe_pekerjaan_id,
            'departemen_id'=> $departemen_id,
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
