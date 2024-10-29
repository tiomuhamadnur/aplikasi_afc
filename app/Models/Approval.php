<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Approval extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'approval';

    protected $guarded = [];

    public static function boot()
    {
        parent::boot();

        self::creating(function ($model) {
            $model->uuid = Str::uuid();
        });
    }

    public function relasi_struktur()
    {
        return $this->belongsTo(RelasiStruktur::class);
    }

    public function jabatan()
    {
        return $this->belongsTo(Jabatan::class);
    }

    public function tipe_employee()
    {
        return $this->belongsTo(TipeEmployee::class);
    }
}
