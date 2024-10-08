<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class TransGangguanRemedy extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'trans_gangguan_remedy';

    protected $guarded = [];

    public static function boot()
    {
        parent::boot();

        self::creating(function ($model) {
            $model->uuid = Str::uuid();
        });
    }

    public function gangguan()
    {
        return $this->belongsTo(Gangguan::class);
    }

    public function remedy()
    {
        return $this->belongsTo(Remedy::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
