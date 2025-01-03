<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class TransaksiBarang extends Model
{
    use HasFactory;

    protected $table = 'transaksi_barang';

    protected $guarded = [];

    public static function boot()
    {
        parent::boot();

        self::creating(function ($model) {
            $model->uuid = Str::uuid();
            $model->user_id = auth()->user()->id;
        });
    }

    public function equipment()
    {
        return $this->belongsTo(Equipment::class);
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class);
    }

    public function gangguan()
    {
        return $this->belongsTo(Gangguan::class);
    }

    public function work_order()
    {
        return $this->belongsTo(WorkOrder::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
