<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TransWorkOrderEquipment extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'trans_workorder_equipment';

    protected $guarded = [];

    public function work_order()
    {
        return $this->belongsTo(WorkOrder::class);
    }

    public function equipment()
    {
        return $this->belongsTo(Equipment::class);
    }
}
