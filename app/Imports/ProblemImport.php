<?php

namespace App\Imports;

use App\Models\Problem;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ProblemImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        return new Problem([
            'name' => $row['name'],
            'code' => $row['code'],
        ]);
    }
}
