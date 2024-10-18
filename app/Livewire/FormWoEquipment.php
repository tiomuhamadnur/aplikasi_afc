<?php

namespace App\Livewire;

use App\Models\Equipment;
use App\Models\RelasiArea;
use App\Models\RelasiStruktur;
use App\Models\TipeEquipment;
use Livewire\Component;

class FormWoEquipment extends Component
{
    public $relasi_area_id = '';
    public $relasi_struktur_id = '';
    public $tipe_equipment_id = '';

    public function render()
    {
        if(!$this->relasi_area_id && !$this->relasi_struktur_id)
        {
            $equipment = [];
        }
        else {
            $data = Equipment::query();

            if($this->relasi_area_id && $this->relasi_struktur_id)
            {
                $data->where('relasi_area_id', $this->relasi_area_id);
            }

            if($this->relasi_struktur_id)
            {
                $data->where('relasi_struktur_id', $this->relasi_struktur_id);
            }

            if($this->tipe_equipment_id)
            {
                $data->where('tipe_equipment_id', $this->tipe_equipment_id);
            }

            $equipment = $data->orderBy('tipe_equipment_id', 'ASC')->get();
        }

        $relasi_area = RelasiArea::where('lokasi_id', 2)->distinct('sub_lokasi_id')->get();
        $relasi_struktur = RelasiStruktur::all();
        $tipe_equipment = TipeEquipment::all();

        return view('livewire.form-wo-equipment', compact([
            'equipment',
            'relasi_area',
            'relasi_struktur',
            'tipe_equipment'
        ]));
    }
    // Method to select all equipment
    public function selectAll()
    {
        // Get all equipment IDs and assign to the $selectedEquipment array
        $this->selectedEquipment = $this->equipment->pluck('id')->toArray();
    }

    // Method to unselect all equipment
    public function unselectAll()
    {
        // Clear the $selectedEquipment array
        $this->selectedEquipment = [];
    }
}
