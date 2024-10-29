<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TransWorkOrderFunctionalLocation extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'trans_workorder_funloc';

    protected $guarded = [];

    public function work_order()
    {
        return $this->belongsTo(WorkOrder::class);
    }

    public function functional_location()
    {
        return $this->belongsTo(FunctionalLocation::class);
    }
}
