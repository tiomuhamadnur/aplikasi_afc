<?php

namespace App\Imports;

use App\Models\Remedy;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class RemedyImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        return new Remedy([
            'name' => $row['name'],
            'code' => $row['code'],
        ]);
    }
}
