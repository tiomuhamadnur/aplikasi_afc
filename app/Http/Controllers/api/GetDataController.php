<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\MonitoringEquipment;
use Illuminate\Http\Request;

class GetDataController extends Controller
{
    public function data_monitoring_equipment()
    {
        $this->disconnectAllDevices();
        $subQuery = MonitoringEquipment::selectRaw('MAX(id) as id')->groupBy('equipment_id');

        $monitoring_equipment = MonitoringEquipment::whereIn('id', $subQuery)
            ->with(['equipment.relasi_area.sub_lokasi'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($monitoring_equipment);
    }

    protected function disconnectAllDevices()
    {
        MonitoringEquipment::query()->update([
            'status' => 'disconnected',
        ]);
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
