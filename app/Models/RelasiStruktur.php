<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class RelasiStruktur extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'relasi_struktur';

    protected $guarded = [];

    public static function boot()
    {
        parent::boot();

        self::creating(function ($model) {
            $model->uuid = Str::uuid();
        });
    }

    public function direktorat()
    {
        return $this->belongsTo(Direktorat::class);
    }

    public function divisi()
    {
        return $this->belongsTo(Divisi::class);
    }

    public function departemen()
    {
        return $this->belongsTo(Departemen::class);
    }

    public function seksi()
    {
        return $this->belongsTo(Seksi::class);
    }
}
