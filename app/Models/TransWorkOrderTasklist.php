<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class TransWorkOrderTasklist extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'trans_workorder_tasklist';

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
        return $this->belongsTo(WorkOrder::class);
    }
}
