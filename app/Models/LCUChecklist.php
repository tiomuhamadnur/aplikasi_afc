<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class LCUChecklist extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'lcu_checklist';

    protected $guarded = [];

    public static function boot()
    {
        parent::boot();

        self::creating(function ($model) {
            $model->uuid = Str::uuid();
        });
    }

    public function functional_location()
    {
        return $this->belongsTo(FunctionalLocation::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
