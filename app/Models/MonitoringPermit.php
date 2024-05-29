<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class MonitoringPermit extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'monitoring_permit';

    protected $guarded = [];

    public static function boot()
    {
        parent::boot();

        self::creating(function ($model) {
            $model->uuid = Str::uuid();
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function relasi_area()
    {
        return $this->belongsTo(RelasiArea::class);
    }

    public function tipe_pekerjaan()
    {
        return $this->belongsTo(TipePekerjaan::class);
    }

    public function tipe_permit()
    {
        return $this->belongsTo(TipePermit::class);
    }
}
