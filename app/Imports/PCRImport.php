<?php

namespace App\Imports;

use App\Models\PCR;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class PCRImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        return new PCR([
            'tipe_equipment_id' => $row['tipe_equipment_id'],
            'category_id' => $row['category_id'],
            'problem_id' => $row['problem_id'],
            'cause_id' => $row['cause_id'],
            'remedy_id' => $row['remedy_id'],
            'classification_id' => $row['classification_id'],
        ]);
    }
}
