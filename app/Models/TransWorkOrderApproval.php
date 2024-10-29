<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class TransWorkOrderApproval extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'trans_workorder_approval';

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

    public function approval()
    {
        return $this->belongsTo(Approval::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
