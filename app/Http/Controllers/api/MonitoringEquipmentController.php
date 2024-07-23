<?php

namespace App\Http\Controllers\api;

use App\Events\MonitoringEquipmentEvent;
use App\Http\Controllers\Controller;
use App\Models\MonitoringEquipment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;

class MonitoringEquipmentController extends Controller
{
    public function check_status()
    {
        $this->disconnectAllDevices();

        $data = [
            'secret' => 'tiomuhamadnur',
            'command' => 'check-status'
        ];

        Event::dispatch(new MonitoringEquipmentEvent($data));

        return response()->json([
            'status' => 'ok',
            'message' => 'The data event was successfully triggered',
        ]);
    }

    protected function disconnectAllDevices()
    {
        MonitoringEquipment::whereRelation('equipment.tipe_equipment', 'id', '=', 18)->update([
            'status' => 'disconnected',
            'waktu' => Carbon::now(),
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
