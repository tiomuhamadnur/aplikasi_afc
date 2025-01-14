<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Equipment extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'equipment';

    protected $guarded = [];

    public static function boot()
    {
        parent::boot();

        self::creating(function ($model) {
            $model->uuid = Str::uuid();
        });
    }

    public function relasi_area()
    {
        return $this->belongsTo(RelasiArea::class, 'relasi_area_id');
    }

    public function relasi_struktur()
    {
        return $this->belongsTo(RelasiStruktur::class);
    }

    public function tipe_equipment()
    {
        return $this->belongsTo(TipeEquipment::class);
    }

    public function arah()
    {
        return $this->belongsTo(Arah::class);
    }

    public function functional_location()
    {
        return $this->belongsTo(FunctionalLocation::class);
    }

    public function parent()
    {
        return $this->belongsTo(Equipment::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Equipment::class, 'parent_id');
    }
}
