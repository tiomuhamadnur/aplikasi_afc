<?php

namespace App\Http\Controllers\user;

use App\Events\MonitoringEquipmentEvent;
use App\Http\Controllers\Controller;
use App\Models\Equipment;
use App\Models\MonitoringEquipment;
use App\Models\RelasiArea;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;

class MonitoringEquipmentController extends Controller
{
    public function index()
    {
        // $monitoring_equipment = MonitoringEquipment::orderBy('waktu', 'DESC')->get();
        $subQuery = MonitoringEquipment::selectRaw('MAX(id) as id')->groupBy('equipment_id');

        $monitoring_equipment = MonitoringEquipment::whereIn('id', $subQuery)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('pages.user.monitoring-equipment.index', compact([
            'monitoring_equipment'
        ]));
    }

    public function client_index(Request $request)
    {
        $device = null;
        if($request->uuid)
        {
        $device = Equipment::where('uuid', $request->uuid)->firstOrFail() ?? null;
        }
        $equipment = Equipment::where('tipe_equipment_id', 18)->get();

        return view('pages.public.monitoring-equipment.index', compact([
            'device',
            'equipment',
        ]));
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $request->validate([
            'uuid' => 'required',
            'status' => 'required'
        ]);

        $equipment = Equipment::where('uuid', $request->uuid)->firstOrFail();

        MonitoringEquipment::updateOrCreate([
            'equipment_id' => $equipment->id,
        ], [
            'status' => $request->status,
            'waktu' => Carbon::now(),
        ]);

        $this->checkDisconnectedDevices();

        return response()->json([
            'status' => 'ok',
            'message' => 'Data status '. $equipment->name . ' berhasil disimpan',
        ]);
    }

    protected function checkDisconnectedDevices()
    {
        $threshold = Carbon::now()->subMinutes(3);
        $connectedEquipment = MonitoringEquipment::where('created_at', '>=', $threshold)->pluck('equipment_id')->toArray();

        $allDevices = Equipment::pluck('id')->toArray();
        $disconnectedEquipment = array_diff($allDevices, $connectedEquipment);

        foreach ($disconnectedEquipment as $equipmentId) {
            MonitoringEquipment::updateOrCreate([
                'equipment_id' => $equipmentId,
            ], [
                'status' => 'disconnected',
                'waktu' => Carbon::now(),
            ]);
        }
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

    public function destroy(Request $request)
    {
        $request->validate([
            'id' => 'required|numeric'
        ]);

        $data = MonitoringEquipment::findOrFail($request->id);
        $data->delete();

        return redirect()->route('monitoring-equipment.index')->withNotify('Data berhasil dihapus');
    }
}