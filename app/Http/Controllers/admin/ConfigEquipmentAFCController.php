<?php

namespace App\Http\Controllers\admin;

use App\DataTables\ConfigEquipmentAFCDataTable;
use App\Http\Controllers\Controller;
use App\Models\ConfigEquipmentAFC;
use Illuminate\Http\Request;
use Spatie\Ssh\Ssh;

class ConfigEquipmentAFCController extends Controller
{
    public function index(ConfigEquipmentAFCDataTable $dataTable, Request $request)
    {
        $station_code = $request->station_code ?? null;
        $equipment_type_code = $request->equipment_type_code ?? null;
        $corner_id = $request->corner_id ?? null;
        $direction = $request->direction ?? null;

        $station_codes = ConfigEquipmentAFC::whereNotNull('station_code')->where('station_code', '!=', '')->distinct()->orderBy('station_code')->pluck('station_code');
        $equipment_type_codes = ConfigEquipmentAFC::whereNotNull('equipment_type_code')->where('equipment_type_code', '!=', '')->distinct()->orderBy('equipment_type_code')->pluck('equipment_type_code');
        $corner_ids = ConfigEquipmentAFC::whereNotNull('corner_id')->where('corner_id', '!=', '')->distinct()->orderBy('corner_id')->pluck('corner_id');
        $directions = ConfigEquipmentAFC::whereNotNull('direction')->where('direction', '!=', '')->distinct()->orderBy('direction')->pluck('direction');

        return $dataTable
            ->with([
                'station_code' => $station_code,
                'equipment_type_code' => $equipment_type_code,
                'corner_id' => $corner_id,
                'direction' => $direction,
            ])
            ->render('pages.admin.config-equipment-afc.index', compact([
                'station_codes',
                'equipment_type_codes',
                'corner_ids',
                'directions',
                'station_code',
                'equipment_type_code',
                'corner_id',
                'direction',
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

    public function control_pg(Request $request)
    {
        $request->validate([
            'uuid' => 'required|string',
            'control_type' => 'required|string|in:on,off,reboot',
        ]);

        $uuid = $request->uuid;
        $control_type = $request->control_type;

        $pg = ConfigEquipmentAFC::where('uuid', $uuid)->firstOrFail();
        $scu = ConfigEquipmentAFC::where('equipment_type_code', 'SCU')
                ->where('station_name', $pg->station_name)
                ->where('station_code', $pg->station_code)
                ->firstOrFail();

        if($control_type == 'on') {
            $message = $this->pg_power_on($scu->ip_address, $pg->mac_address);
            return redirect()->route('config-equipment-afc.index')->withNotify($message);
        }
        elseif($control_type == 'off') {
            $message = $this->pg_power_off($pg->ip_address);
            return redirect()->route('config-equipment-afc.index')->withNotify($message);
        }
        elseif($control_type == 'reboot') {
            $message = $this->pg_reboot($pg->ip_address);
            return redirect()->route('config-equipment-afc.index')->withNotify($message);
        }

        return redirect()->route('config-equipment-afc.index')->withNotofyerror('Something went wrong, please check your connection');
    }

    private function pg_power_on(string $scu_ip_address, string $pg_mac_address)
    {
        $command = 'ether-wake -i em1 ' . $pg_mac_address;
        $connection = Ssh::create(env('SSH_SCU_USERNAME'), $scu_ip_address, (int)env('SSH_SCU_PORT'), env('SSH_SCU_PASSWORD'))
                    ->executeAsync($command);
        return $connection->getOutput();
    }

    private function pg_power_off(string $pg_ip_address)
    {
        $command = 'shutdown -h now';
        $connection = Ssh::create(env('SSH_PG_USERNAME'), $pg_ip_address, (int)env('SSH_PG_PORT'), env('SSH_PG_PASSWORD'))
                    ->executeAsync($command);
        return $connection->getOutput();
    }

    private function pg_reboot(string $pg_ip_address)
    {
        $command = 'reboot';
        $connection = Ssh::create(env('SSH_PG_USERNAME'), $pg_ip_address, (int)env('SSH_PG_PORT'), env('SSH_PG_PASSWORD'))
                    ->executeAsync($command);
        return $connection->getOutput();
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
