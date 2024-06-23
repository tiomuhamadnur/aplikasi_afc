<?php

namespace App\Imports;

use App\Models\Barang;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class BarangImport implements  ToModel, WithHeadingRow
{
    public $relasi_struktur_id;

    public function __construct($relasi_struktur_id)
    {
        $this->relasi_struktur_id = $relasi_struktur_id;
    }

    public function model(array $row)
    {
        return new Barang([
            'relasi_struktur_id' => $this->relasi_struktur_id,
            'name' => $row['name'],
            'merk' => $row['merk'],
            'material_number' => $row['material_number'],
            'serial_number' => $row['serial_number'],
            'tipe_barang_id' => $row['tipe_barang_id'],
            'relasi_area_id' => $row['relasi_area_id'],
            'satuan_id' => $row['satuan_id'],
            'spesifikasi' => $row['spesifikasi'],
            'harga' => $row['harga'],
            'expired_date' => $row['expired_date'],
            'deskripsi' => $row['deskripsi'],
        ]);
    }
}
