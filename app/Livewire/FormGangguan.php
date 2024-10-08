<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Cause;
use App\Models\Classification;
use App\Models\Equipment;
use App\Models\PCR;
use App\Models\Problem;
use App\Models\RelasiArea;
use App\Models\Remedy;
use App\Models\TipeEquipment;
use Livewire\Component;

// class FormGangguan extends Component
// {
//     public $relasi_area_id = '';
//     public $tipe_equipment_id = '';
//     public $category_id = '';
//     public $problem_id = '';
//     public $cause_id = '';
//     public $remedy_id = '';

//     public function render()
//     {
//         if(!$this->relasi_area_id && !$this->tipe_equipment_id)
//         {
//             $equipment = [];
//         }
//         else {
//             $data = Equipment::query();

//             if($this->relasi_area_id)
//             {
//                 $data->where('relasi_area_id', $this->relasi_area_id)
//                     ->where('relasi_struktur_id', auth()->user()->relasi_struktur_id);
//             }

//             if($this->tipe_equipment_id)
//             {
//                 $data->where('tipe_equipment_id', $this->tipe_equipment_id);
//             }

//             $equipment = $data->orderBy('tipe_equipment_id', 'ASC')->get();
//         }

//         if($this->category_id)
//         {
//             $problem_ids = PCR::where('category_id', $this->category_id)->pluck('problem_id')->toArray();
//         }

//         if($this->category_id && $this->problem_id)
//         {
//             $problem = Problem::whereIn('id', $problem_ids)->get();
//             $cause_ids = PCR::where('category_id', $this->category_id)
//                             ->where('problem_id', $this->problem_id)
//                             ->pluck('cause_id')
//                             ->toArray();
//         }

//         if($this->category_id && $this->problem_id && $this->cause_id)
//         {
//             $cause = Cause::whereIn('id', $cause_ids)->get();
//             $remedy_ids = PCR::where('category_id', $this->category_id)
//                             ->where('problem_id', $this->problem_id)
//                             ->where('cause_id', $this->cause_id)
//                             ->pluck('remedy_id')
//                             ->toArray();
//         }

//         if($this->category_id && $this->problem_id && $this->cause_id && $this->remedy_id)
//         {
//             $remedy = Remedy::whereIn('id', $remedy_ids)->get();
//             $classification_ids = PCR::where('category_id', $this->category_id)
//                             ->where('problem_id', $this->problem_id)
//                             ->where('cause_id', $this->cause_id)
//                             ->where('remedy_id', $this->remedy_id)
//                             ->pluck('classification_id')
//                             ->toArray();

//             $classification = Classification::where('id', $classification_ids)->get();
//         }

//         $area = RelasiArea::where('lokasi_id', 2)->distinct('sub_lokasi_id')->get();
//         $tipe_equipment = TipeEquipment::all();
//         // $problem = Problem::all();
//         // $cause = Cause::all();
//         // $remedy = Remedy::all();
//         // $classification = Classification::all();

//         $category = Category::all();

//         return view('livewire.form-gangguan', compact([
//             'equipment',
//             'tipe_equipment',
//             'area',
//             'category',
//             'problem',
//             'cause',
//             'remedy',
//             'classification',
//         ]));
//     }
// }


class FormGangguan extends Component
{
    public $relasi_area_id = '';
    public $tipe_equipment_id = '';
    public $category_id = '';
    public $problem_id = '';
    public $cause_id = '';
    public $remedy_id = '';

    public function render()
    {
        $equipment = collect(); // Default empty collection
        $problem = collect();
        $cause = collect();
        $remedy = collect();
        $classification = Classification::all();

        // Query Equipment jika ada filter relasi_area_id atau tipe_equipment_id
        if ($this->relasi_area_id || $this->tipe_equipment_id) {
            $equipment = Equipment::query()
                ->when($this->relasi_area_id, function($query) {
                    return $query->where('relasi_area_id', $this->relasi_area_id)
                                 ->where('relasi_struktur_id', auth()->user()->relasi_struktur_id);
                })
                ->when($this->tipe_equipment_id, function($query) {
                    return $query->where('tipe_equipment_id', $this->tipe_equipment_id);
                })
                ->orderBy('tipe_equipment_id', 'ASC')
                ->get();
        }

        // Fetch problem_ids only if category_id is set and not 0
        if ($this->category_id && $this->category_id != 0 && $this->tipe_equipment_id && $this->tipe_equipment_id != 0) {
            $problem_ids = PCR::where('category_id', $this->category_id)
                            ->where('tipe_equipment_id', $this->tipe_equipment_id)
                            ->pluck('problem_id')->toArray();

            // Query Problem jika ada category_id
            $problem = Problem::whereIn('id', $problem_ids)->get();
        }

        // Fetch cause_ids if both category_id and problem_id are set and not 0
        if ($this->category_id && $this->category_id != 0 && $this->problem_id && $this->problem_id != 0) {
            $cause_ids = PCR::where('category_id', $this->category_id)
                            ->where('problem_id', $this->problem_id)
                            ->pluck('cause_id')->toArray();

            // Query Cause jika ada problem_id dan category_id
            $cause = Cause::whereIn('id', $cause_ids)->get();
        }

        // Fetch remedy_ids if category_id, problem_id, and cause_id are set and not 0
        if ($this->category_id && $this->category_id != 0 && $this->problem_id && $this->problem_id != 0 && $this->cause_id && $this->cause_id != 0) {
            $remedy_ids = PCR::where('category_id', $this->category_id)
                            ->where('problem_id', $this->problem_id)
                            ->where('cause_id', $this->cause_id)
                            ->pluck('remedy_id')->toArray();

            // Query Remedy jika ada cause_id
            $remedy = Remedy::whereIn('id', $remedy_ids)->get();
        }

        // Fetch classification_ids if all filters are set and not 0
        if ($this->category_id && $this->category_id != 0 && $this->problem_id && $this->problem_id != 0 && $this->cause_id && $this->cause_id != 0 && $this->remedy_id && $this->remedy_id != 0) {
            $classification_ids = PCR::where('category_id', $this->category_id)
                            ->where('problem_id', $this->problem_id)
                            ->where('cause_id', $this->cause_id)
                            ->where('remedy_id', $this->remedy_id)
                            ->pluck('classification_id')->toArray();

            // Query Classification jika ada remedy_id
            $classification = Classification::whereIn('id', $classification_ids)->get();
        }

        // Area dan tipe_equipment query statis
        $area = RelasiArea::where('lokasi_id', 2)->distinct('sub_lokasi_id')->get();
        $tipe_equipment = TipeEquipment::all();
        $category = Category::all();

        // Return view dengan data yang sudah di-query
        return view('livewire.form-gangguan', compact([
            'equipment',
            'tipe_equipment',
            'area',
            'category',
            'problem',
            'cause',
            'remedy',
            'classification',
        ]));
    }
}

