<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class WorkOrder extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'work_order';

    protected $guarded = [];

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
            $code = 'WO-' . Str::upper(Str::random(8));
        } while (self::where('ticket_number', $code)->exists());

        return $code;
    }

    public function tipe_pekerjaan()
    {
        return $this->belongsTo(TipePekerjaan::class, 'tipe_pekerjaan_id');
    }

    public function relasi_area()
    {
        return $this->belongsTo(RelasiArea::class);
    }

    public function relasi_struktur()
    {
        return $this->belongsTo(RelasiStruktur::class);
    }

    public function classification()
    {
        return $this->belongsTo(Classification::class);
    }

    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function transaksi_barang()
    {
        return $this->hasMany(TransaksiBarang::class);
    }

    public function trans_workorder_equipment()
    {
        return $this->hasMany(TransWorkOrderEquipment::class);
    }

    public function trans_workorder_tasklist()
    {
        return $this->hasMany(TransWorkOrderTasklist::class);
    }

    public function trans_workorder_user()
    {
        return $this->hasMany(TransWorkOrderUser::class);
    }

    public function trans_workorder_photo()
    {
        return $this->hasMany(TransWorkOrderPhoto::class);
    }

    public function trans_workorder_functional_location()
    {
        return $this->hasMany(TransWorkOrderFunctionalLocation::class);
    }

    public function trans_workorder_approval()
    {
        return $this->hasMany(TransWorkOrderApproval::class);
    }

    public function gangguan()
    {
        return $this->hasMany(Gangguan::class);
    }
}
