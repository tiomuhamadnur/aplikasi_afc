<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class FundSource extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'fund_source';

    protected $guarded = [];

    public static function boot()
    {
        parent::boot();

        self::creating(function ($model) {
            $model->uuid = Str::uuid();
        });
    }

    public function fund()
    {
        return $this->belongsTo(Fund::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function status_budgeting()
    {
        return $this->belongsTo(StatusBudgeting::class);
    }

    public function project()
    {
        return $this->hasMany(Project::class);
    }
}
