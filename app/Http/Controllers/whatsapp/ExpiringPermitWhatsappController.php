<?php

namespace App\Http\Controllers\whatsapp;

use App\Http\Controllers\Controller;
use App\Models\Departemen;
use App\Models\MonitoringPermit;
use App\Models\User;
use App\Services\WhatsappService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ExpiringPermitWhatsappController extends Controller
{
    public function notification(WhatsappService $whatsappService)
    {
        $data = $this->get_data();

        foreach($data as $item) {
            $whatsAppData = [
                'gender' => $item['gender'],
                'name' => $item['name'],
                'departemen' => $item['departemen'],
                'seksi' => $item['seksi'],
                'jumlah' => $item['jumlah'],
                'url' => $item['url'],
            ];

            $message = $this->format_message($whatsAppData);
            $no_hp = $item['no_hp'];

            $whatsappService->sendMessage($no_hp, $message);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Permit status updated successfully',
            'data' => $data,
        ]);
    }

    public function get_data()
    {
        $today = Carbon::today();
        $hari_notifikasi = 2;
        $jabatan_id = 6; //Teknisi&Staff
        $tipe_employee_id = 1; //Organik
        $startDate = $today->copy()->addDays(1);
        $endDate = $today->copy()->addDays($hari_notifikasi);

        $departemen_ids = Departemen::distinct()->pluck('id')->toArray();
        $results = [];

        foreach ($departemen_ids as $departemen_id) {
            // Hitung jumlah permits yang akan expired
            $jumlah = MonitoringPermit::where('departemen_id', $departemen_id)
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
                    ->whereRelation('jabatan', 'id', '=', $jabatan_id)
                    ->whereRelation('tipe_employee', 'id', '=', $tipe_employee_id)
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

    public function format_message(array $data)
    {
        $enter = "\n";
        $div = '=============================';

        $gender = $data['gender'];
        $name = $data['name'];
        $departemen = $data['departemen'];
        $seksi = $data['seksi'];
        $jumlah = $data['jumlah'];
        $url = $data['url'];

        $message = '🔴 *ROLLING SYNC NOTIFICATION:* ' . $enter . $enter . $enter .
            'Dear ' . $gender .' *' . $name . '*,' . $enter . $enter.
            'Sebagai informasi, terdapat *Data Permit* yang akan *Expired* dan perlu ditindak lanjuti dengan detail sebagai berikut:' . $enter . $enter .
            $div . $enter . $enter .
            '*Departemen :*' . $enter .
            $departemen . $enter . $enter .
            '*Seksi :*' . $enter .
            $seksi . $enter . $enter .
            '*Jumlah :*' . $enter .
            $jumlah . ' permit' .  $enter . $enter .
            '*URL :*' . $enter .
            $url . $enter . $enter .
            $div . $enter . $enter .
            '_Regards,_' . $enter . $enter .
            '*RollingSyncBOT*' .
            $enter . $enter . $enter . $enter;

        return $message;
    }
}
