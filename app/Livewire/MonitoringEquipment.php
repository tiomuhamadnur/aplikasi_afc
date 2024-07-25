<?php

namespace App\Livewire;

use App\Models\MonitoringEquipment as ModelsMonitoringEquipment;
use App\Models\RelasiArea;
use Livewire\Component;

class MonitoringEquipment extends Component
{
    public $area_id = '';
    protected $listeners = ['reload'];

    public function render()
    {
        $subQuery = ModelsMonitoringEquipment::selectRaw('MAX(id) as id')->groupBy('equipment_id');

        $data = ModelsMonitoringEquipment::query()->whereIn('id', $subQuery);

        $data->when($this->area_id, function ($query) {
            return $query->whereRelation('equipment.relasi_area', 'id', '=', $this->area_id);
        });

        $monitoring_equipment = $data->orderBy('created_at', 'desc')->get();

        $area = RelasiArea::where('lokasi_id', 2)->get();

        return view('livewire.monitoring-equipment', compact([
            'monitoring_equipment',
            'area'
        ]));
    }
}
