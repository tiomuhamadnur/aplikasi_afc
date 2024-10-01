<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class FunctionalLocation extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'functional_location';

    protected $guarded = [];

    public static function boot()
    {
        parent::boot();

        self::creating(function ($model) {
            $model->uuid = Str::uuid();
        });
    }

    public function parent()
    {
        return $this->belongsTo(FunctionalLocation::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(FunctionalLocation::class, 'parent_id');
    }
}
