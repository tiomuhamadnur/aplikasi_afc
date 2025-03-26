<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class RelasiArea extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'relasi_area';

    protected $guarded = [];

    public static function boot()
    {
        parent::boot();

        self::creating(function ($model) {
            $model->uuid = Str::uuid();
        });
    }

    public function scopeLintas($query)
    {
        return $query->where('lokasi_id', 4); // lokasi_id 4 = Lintas
    }

    public function scopeLine($query)
    {
        return $query->where('lokasi_id', 5); // lokasi_id 5 = line
    }

    public function lokasi()
    {
        return $this->belongsTo(Lokasi::class);
    }

    public function sub_lokasi()
    {
        return $this->belongsTo(SubLokasi::class, 'sub_lokasi_id');
    }

    public function detail_lokasi()
    {
        return $this->belongsTo(DetailLokasi::class);
    }
}
