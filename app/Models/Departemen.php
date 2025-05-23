<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Departemen extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'departemen';

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
        return $this->hasOne(RelasiStruktur::class, 'departemen_id', 'id');
    }
}
