<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Dokumen extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'dokumen';

    protected $guarded = [];

    public static function boot()
    {
        parent::boot();

        self::creating(function ($model) {
            $model->uuid = Str::uuid();
        });
    }

    public function tipe_dokumen()
    {
        return $this->belongsTo(TipeDokumen::class);
    }

    public function departemen()
    {
        return $this->belongsTo(Departemen::class);
    }
}
