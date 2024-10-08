<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class TransGangguanPending extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'trans_gangguan_pending';

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
}
