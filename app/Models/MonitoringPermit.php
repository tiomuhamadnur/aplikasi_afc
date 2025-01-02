<?php

namespace App\Models;

use App\Helpers\WhatsAppHelper;
use App\Http\Controllers\mail\ExpiringPermitMailController;
use App\Mail\ExpiringPermitMail;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class MonitoringPermit extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'monitoring_permit';

    protected $guarded = [];

    public static function boot()
    {
        parent::boot();

        self::creating(function ($model) {
            $model->uuid = Str::uuid();
        });
    }

    protected $dates = ['tanggal_expired'];

    public function getRemainingDaysAttribute()
    {
        $tanggalExpired = Carbon::parse($this->tanggal_expired)->startOfDay();;
        $now = Carbon::now()->startOfDay();
        return $tanggalExpired->diffInDays($now) * ($now->greaterThan($tanggalExpired) ? -1 : 1);
    }

    public static function updateStatus()
    {
        $today = Carbon::today()->toDateString();

        self::query()
            ->where('tanggal_expired', '<', $today)
            ->where('status', '!=', 'expired')
            ->update(['status' => 'expired']);

        self::query()
            ->where('tanggal_expired', '>=', $today)
            ->where('status', '!=', 'active')
            ->update(['status' => 'active']);
    }

    public static function notifyExpiringPermits()
    {
        $today = Carbon::today();
        $startDate = $today->copy()->addDays(1);
        $endDate = $today->copy()->addDays(3);

        $departemen_ids = Departemen::distinct()->pluck('id')->toArray();

        foreach ($departemen_ids as $departemen_id)
        {
            $jumlah = self::query()
                ->where('departemen_id', $departemen_id)
                ->whereBetween('tanggal_expired', [$startDate, $endDate])
                ->where('status', 'active')
                ->count();

            if ($jumlah > 0) {
                $departemen = Departemen::findOrFail($departemen_id)->name;
                $url = route('monitoring-permit.index', [
                    'start_date' => $startDate->toDateString(),
                    'end_date' => $endDate->toDateString()
                ]);

                $user_ids = User::whereRelation('relasi_struktur.departemen', 'id', '=', $departemen_id)
                                    ->whereRelation('jabatan', 'id', '=', 6)
                                    ->distinct()
                                    ->pluck('id')
                                    ->toArray();

                foreach ($user_ids as $user_id)
                {
                    $user = User::findOrFail($user_id);
                    $gender = ($user->gender->id == 1) ? "Bapak" : "Ibu";
                    $name = $user->name;
                    $seksi = $user->relasi_struktur->seksi->name;
                    $no_hp = $user->no_hp;

                    $data = [
                        $gender,
                        $name,
                        $departemen,
                        $seksi,
                        $jumlah,
                        $url,
                    ];

                    $message = WhatsAppHelper::formatMessage($data);
                    WhatsAppHelper::sendNotification($no_hp, $message);
                }
            }

        }
    }


    public static function notifyExpiringPermitsByEmail()
    {
        $today = Carbon::today();
        $hari_notifikasi = 2;
        $jabatan_id_notifikasi = 6;
        $startDate = $today->copy()->addDays(1);
        $endDate = $today->copy()->addDays($hari_notifikasi);

        $departemen_ids = Departemen::distinct()->pluck('id')->toArray();
        $results = [];

        foreach ($departemen_ids as $departemen_id) {
            // Hitung jumlah permits yang akan expired
            $jumlah = self::query()
                ->where('departemen_id', $departemen_id)
                ->whereBetween('tanggal_expired', [$startDate, $endDate])
                ->where('status', 'active')
                ->count();

            if ($jumlah > 0) {
                $departemen = Departemen::find($departemen_id)->name;
                $url = route('monitoring-permit.index', [
                    'start_date' => $startDate->toDateString(),
                    'end_date' => $endDate->toDateString(),
                ]);

                // Ambil semua user yang relevan
                $users = User::whereRelation('relasi_struktur.departemen', 'id', '=', $departemen_id)
                    ->whereRelation('jabatan', 'id', '=', $jabatan_id_notifikasi)
                    ->with(['relasi_struktur.seksi', 'gender'])
                    ->get();

                foreach ($users as $user) {
                    $results[] = [
                        'gender' => $user->gender->id == 1 ? "Bapak" : "Ibu",
                        'name' => $user->name,
                        'departemen' => $departemen,
                        'seksi' => $user->relasi_struktur->seksi->name ?? null,
                        'jumlah' => $jumlah,
                        'url' => $url,
                        'no_hp' => $user->no_hp,
                        'email' => $user->email,
                    ];
                }
            }
        }

        return $results;
    }





    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function relasi_area()
    {
        return $this->belongsTo(RelasiArea::class);
    }

    public function tipe_pekerjaan()
    {
        return $this->belongsTo(TipePekerjaan::class);
    }

    public function tipe_permit()
    {
        return $this->belongsTo(TipePermit::class);
    }

    public function departemen()
    {
        return $this->belongsTo(Departemen::class);
    }
}
