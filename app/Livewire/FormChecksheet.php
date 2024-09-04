<?php

namespace App\Livewire;

use App\Models\Form;
use App\Models\Parameter;
use Livewire\Component;

class FormChecksheet extends Component
{
    public $tipe_equipment_id;
    public $form_id = '';

    public function mount($tipe_equipment_id)
    {
        $this->tipe_equipment_id = $tipe_equipment_id;
    }

    public function render()
    {
        if(!$this->form_id) {
            $parameter = [];
        }
        else {
            $parameter = Parameter::where('form_id', $this->form_id)
                    ->orderBy('urutan', 'ASC')
                    ->get();
        }

        $form = Form::where('tipe_equipment_id', $this->tipe_equipment_id)->get();

        return view('livewire.form-checksheet', compact([
            'form',
            'parameter',
        ]));
    }
}
