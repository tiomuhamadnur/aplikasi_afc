<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\MonitoringEquipment;
use Carbon\Carbon;
use Illuminate\Http\Request;

class GetDataController extends Controller
{
    public function data_monitoring_equipment()
    {
        $subQuery = MonitoringEquipment::selectRaw('MAX(id) as id')->groupBy('equipment_id');

        $monitoring_equipment = MonitoringEquipment::whereIn('id', $subQuery)
            ->with(['equipment.relasi_area.sub_lokasi', 'equipment.arah'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($monitoring_equipment);
    }

    protected function disconnectAllDevices()
    {
        MonitoringEquipment::whereRelation('equipment.tipe_equipment', 'id', '=', 18)->update([
            'status' => 'disconnected',
            'waktu' => Carbon::now(),
        ]);
    }

    public function data_equipment(Request $request)
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
