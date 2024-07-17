<?php

namespace App\Imports;

use App\Models\SamCard;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class SamCardImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        return new SamCard([
            'uid' => $row['uid'],
            'mid' => $row['mid'],
            'tid' => $row['tid'],
            'pin' => $row['pin'],
            'mc' => $row['mc'],
            'alokasi' => $row['alokasi'],
            'status' => $row['status'],
        ]);
    }
}
