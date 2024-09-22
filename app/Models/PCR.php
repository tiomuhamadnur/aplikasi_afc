<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class PCR extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'pcr';

    protected $guarded = [];

    public static function boot()
    {
        parent::boot();

        self::creating(function ($model) {
            $model->uuid = Str::uuid();
        });
    }

    public function tipe_equipment()
    {
        return $this->belongsTo(TipeEquipment::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function problem()
    {
        return $this->belongsTo(Problem::class);
    }

    public function cause()
    {
        return $this->belongsTo(Cause::class);
    }

    public function remedy()
    {
        return $this->belongsTo(Remedy::class);
    }

    public function classification()
    {
        return $this->belongsTo(Classification::class);
    }
}
