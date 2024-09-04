<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Checksheet extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'checksheet';

    protected $guarded = [];

    public static function boot()
    {
        parent::boot();

        self::creating(function ($model) {
            $model->uuid = Str::uuid();
        });
    }

    public function work_order()
    {
        return $this->belongsTo(WorkOrder::class, 'work_order_id');
    }

    public function equipment()
    {
        return $this->belongsTo(Equipment::class);
    }

    public function parameter()
    {
        return $this->belongsTo(Parameter::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
