<?php

namespace App\Models;

use Carbon\Carbon;
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

    protected $dates = ['tanggal_expired'];

    public function getRemainingDaysAttribute()
    {
        $tanggalExpired = Carbon::parse($this->tanggal_expired);
        return $tanggalExpired->diffInDays(Carbon::now());
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
