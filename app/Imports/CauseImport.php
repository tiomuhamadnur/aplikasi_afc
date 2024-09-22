<?php

namespace App\Imports;

use App\Models\Cause;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class CauseImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        return new Cause([
            'name' => $row['name'],
            'code' => $row['code'],
        ]);
    }
}
