<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class MonitoringEquipment extends Model
{
    use HasFactory;

    protected $table = 'monitoring_equipment';

    protected $guarded = [];

    public static function boot()
    {
        parent::boot();

        self::creating(function ($model) {
            $model->uuid = Str::uuid();
        });
    }

    public function equipment()
    {
        return $this->belongsTo(Equipment::class);
    }
}
