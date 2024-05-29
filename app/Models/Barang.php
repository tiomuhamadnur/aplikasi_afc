<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Barang extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'barang';

    protected $guarded = [];

    public static function boot()
    {
        parent::boot();

        self::creating(function ($model) {
            $model->uuid = Str::uuid();
        });
    }

    public function satuan()
    {
        return $this->belongsTo(Satuan::class);
    }

    public function relasi_area()
    {
        return $this->belongsTo(RelasiArea::class);
    }

    public function tipe_barang()
    {
        return $this->belongsTo(TipeBarang::class);
    }
}
