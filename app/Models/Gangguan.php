<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Gangguan extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'gangguan';

    protected $guarded = [];

    protected $casts = [
        'is_changed' => 'boolean',
    ];

    public static function boot()
    {
        parent::boot();

        self::creating(function ($model) {
            $model->uuid = Str::uuid();
            $model->ticket_number = self::generateUniqueCode();
        });
    }

    private static function generateUniqueCode()
    {
        do {
            $code = Str::upper(Str::random(8));
        } while (self::where('ticket_number', $code)->exists());

        return $code;
    }

    public function equipment()
    {
        return $this->belongsTo(Equipment::class);
    }

    public function report_user()
    {
        return $this->belongsTo(User::class);
    }

    public function response_user()
    {
        return $this->belongsTo(User::class);
    }

    public function solved_user()
    {
        return $this->belongsTo(User::class);
    }
}