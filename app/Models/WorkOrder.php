<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class WorkOrder extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'work_order';

    protected $guarded = [];

    public static function boot()
    {
        parent::boot();

        self::creating(function ($model) {
            $model->uuid = Str::uuid();
            $model->ticket_number = self::generateUniqueCode();
        });
    }

    private static function generateUniqueCode()
    {
        do {
            $code = Str::upper(Str::random(8));
        } while (self::where('ticket_number', $code)->exists());

        return $code;
    }

    public function tipe_pekerjaan()
    {
        return $this->belongsTo(TipePekerjaan::class);
    }

    public function relasi_area()
    {
        return $this->belongsTo(RelasiArea::class);
    }

    public function relasi_struktur()
    {
        return $this->belongsTo(RelasiStruktur::class);
    }

    public function classification()
    {
        return $this->belongsTo(Classification::class);
    }

    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
