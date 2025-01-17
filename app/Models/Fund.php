<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Fund extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'fund';

    protected $guarded = [];

    public static function boot()
    {
        parent::boot();

        self::creating(function ($model) {
            $model->uuid = Str::uuid();
        });
    }

    public function divisi()
    {
        return $this->belongsTo(Divisi::class);
    }

    public function status_budgeting()
    {
        return $this->belongsTo(StatusBudgeting::class);
    }

    public function fund_source()
    {
        return $this->hasMany(FundSource::class);
    }
}
