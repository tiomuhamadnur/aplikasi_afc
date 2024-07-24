<?php

namespace App\Livewire;

use App\Models\MonitoringEquipment as ModelsMonitoringEquipment;
use Livewire\Component;

class MonitoringEquipment extends Component
{
    public $search;
    public $monitoring_equipment;

    protected $listeners = ['loadData'];

    public function mount()
    {
        $this->loadData();
    }

    public function loadData()
    {
        $subQuery = ModelsMonitoringEquipment::selectRaw('MAX(id) as id')->groupBy('equipment_id');

        $monitoring_equipment = ModelsMonitoringEquipment::whereIn('id', $subQuery)
            ->where('status', 'LIKE', '%'.$this->search.'%')
            ->orderBy('created_at', 'desc')
            ->get();

        $this->monitoring_equipment = $monitoring_equipment;
    }


    public function render()
    {
        return view('livewire.monitoring-equipment');
    }
}
