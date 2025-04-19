<?php

namespace App\Http\Controllers\admin;

use App\DataTables\ConfigEquipmentAFCDataTable;
use App\Http\Controllers\Controller;
use App\Models\ConfigEquipmentAFC;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Process;
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
            ->render('pages.admin.config-equipment-afc.index', compact(['station_codes', 'equipment_type_codes', 'corner_ids', 'directions', 'station_code', 'equipment_type_code', 'corner_id', 'direction']));
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

        $notifyMessage = '';

        switch ($control_type) {
            case 'on':
                $notifyMessage = $this->pg_power_on($scu->ip_address, $pg->mac_address) . ' Power On - (' . $pg->equipment_name . ')';
                break;
            case 'off':
                $notifyMessage = $this->pg_power_off($pg->ip_address) . ' Power Off - (' . $pg->equipment_name . ')';
                break;
            case 'reboot':
                $notifyMessage = $this->pg_reboot($pg->ip_address) . ' Rebooting - (' . $pg->equipment_name . ')';
                break;
            default:
                $notifyMessage = 'Invalid control type';
                break;
        }

        return redirect()->route('config-equipment-afc.index')->withNotify($notifyMessage);
    }

    private function sshExecute(string $ip, string $username, string $password, int $port, string $command)
    {
        $sshCommand = sprintf(
            'sshpass -p %s ssh -o StrictHostKeyChecking=no -p %d %s@%s "%s"',
            escapeshellarg($password),
            $port,
            $username,
            $ip,
            $command
        );

        $result = Process::run($sshCommand);

        if ($result->failed()) {
            throw new \RuntimeException("SSH command failed: " . $result->errorOutput());
        }

        return $result->output();
    }

    private function pg_power_on(string $scu_ip_address, string $pg_mac_address)
    {
        $command = sprintf('ether-wake -i em1 %s', $pg_mac_address);

        return $this->sshExecute(
            $scu_ip_address,
            env('SSH_SCU_USERNAME'),
            env('SSH_SCU_PASSWORD'),
            (int) env('SSH_SCU_PORT'),
            $command
        );
    }

    private function pg_power_off(string $pg_ip_address)
    {
        $command = 'shutdown -h now';

        return $this->sshExecute(
            $pg_ip_address,
            env('SSH_PG_USERNAME'),
            env('SSH_PG_PASSWORD'),
            (int) env('SSH_PG_PORT'),
            $command
        );
    }

    private function pg_reboot(string $pg_ip_address)
    {
        $command = 'reboot';

        return $this->sshExecute(
            $pg_ip_address,
            env('SSH_PG_USERNAME'),
            env('SSH_PG_PASSWORD'),
            (int) env('SSH_PG_PORT'),
            $command
        );
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
