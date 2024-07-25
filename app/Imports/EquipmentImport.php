<?php

namespace App\Imports;

use App\Models\Equipment;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class EquipmentImport implements ToModel, WithHeadingRow
{
    public $relasi_struktur_id;

    public function __construct($relasi_struktur_id)
    {
        $this->relasi_struktur_id = $relasi_struktur_id;
    }

    public function model(array $row)
    {
        return new Equipment([
            'relasi_struktur_id' => $this->relasi_struktur_id,
            'name' => $row['name'],
            'code' => $row['code'],
            'equipment_number' => $row['equipment_number'],
            'tipe_equipment_id' => $row['tipe_equipment_id'],
            'relasi_area_id' => $row['relasi_area_id'],
            'arah_id' => $row['arah_id'],
            'deskripsi' => $row['deskripsi'],
            'status' => 'active',
        ]);
    }
}
