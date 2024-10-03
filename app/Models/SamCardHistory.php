<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class SamCardHistory extends Model
{
    use HasFactory;

    protected $table = 'sam_card_history';

    protected $guarded = [];

    public static function boot()
    {
        parent::boot();

        self::creating(function ($model) {
            $model->uuid = Str::uuid();
        });
    }

    public function sam_card()
    {
        return $this->belongsTo(SamCard::class);
    }

    public function old_sam_card()
    {
        return $this->belongsTo(SamCard::class, 'old_sam_card_id');
    }

    public function equipment()
    {
        return $this->belongsTo(Equipment::class);
    }

    public function relasi_area()
    {
        return $this->belongsTo(RelasiArea::class);
    }
}
