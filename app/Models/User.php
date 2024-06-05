<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'no_hp',
        'photo',
        'ttd',
        'password',
        'perusahaan_id',
        'gender_id',
        'role_id',
        'jabatan_id',
        'tipe_employee_id',
        'relasi_struktur_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public static function boot()
    {
        parent::boot();

        self::creating(function ($model) {
            $model->uuid = Str::uuid();
        });
    }

    public function jabatan()
    {
        return $this->belongsTo(Jabatan::class);
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function relasi_struktur()
    {
        return $this->belongsTo(RelasiStruktur::class);
    }

    public function tipe_employee()
    {
        return $this->belongsTo(TipeEmployee::class);
    }

    public function perusahaan()
    {
        return $this->belongsTo(Perusahaan::class);
    }

    public function gender()
    {
        return $this->belongsTo(Gender::class);
    }
}
