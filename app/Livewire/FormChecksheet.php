<?php

namespace App\Livewire;

use App\Models\Form;
use App\Models\Parameter;
use Livewire\Component;

class FormChecksheet extends Component
{
    public $tipe_equipment_id;
    public $functional_location_id;
    public $form_id = '';

    public function mount($tipe_equipment_id = null, $functional_location_id = null)
    {
        $this->tipe_equipment_id = $tipe_equipment_id;
        $this->functional_location_id = $functional_location_id;
    }

    public function render()
    {
        if($this->form_id) {
            $parameter = Parameter::where('form_id', $this->form_id)
                    ->orderBy('urutan', 'ASC')
                    ->get();
        } else {
            $parameter = collect();
        }

        // Modify form query based on tipe_equipment_id or functional_location_id
        if ($this->tipe_equipment_id) {
            $form = Form::where('tipe_equipment_id', $this->tipe_equipment_id)->get();
        } elseif ($this->functional_location_id) {
            $form = Form::where('functional_location_id', $this->functional_location_id)->get();
        } else {
            $form = collect();
        }

        return view('livewire.form-checksheet', compact([
            'form',
            'parameter',
        ]));
    }
}
