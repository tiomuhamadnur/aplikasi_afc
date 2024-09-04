<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Parameter extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'parameter';

    protected $guarded = [];

    public static function boot()
    {
        parent::boot();

        self::creating(function ($model) {
            $model->uuid = Str::uuid();
        });
    }

    public function form()
    {
        return $this->belongsTo(Form::class);
    }

    public function option_form()
    {
        return $this->belongsTo(OptionForm::class);
    }

    public function satuan()
    {
        return $this->belongsTo(Satuan::class);
    }
}
