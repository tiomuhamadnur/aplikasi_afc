<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Equipment;
use App\Models\Gangguan;
use App\Models\MonitoringPermit;
use App\Models\RelasiArea;
use App\Models\SamCard;
use App\Models\SamCardHistory;
use App\Models\TipeEquipment;
use App\Models\TransaksiBarang;
use App\Models\WorkOrder;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $departemen_id = auth()->user()->relasi_struktur->departemen->id;
        $permit = MonitoringPermit::where('departemen_id', $departemen_id)->count();

        $samcard_ready = SamCard::where('status', 'ready')->count();
        $samcard_used = SamCard::where('status', 'used')->count();
        $samcard = [
            'ready' => $samcard_ready,
            'used' => $samcard_used
        ];

        $tahun = Carbon::now()->year;

        $wo = WorkOrder::whereYear('date', $tahun)->where('relasi_struktur_id', auth()->user()->relasi_struktur_id)->get();
        $work_order = [
            'PM' => $wo->where('tipe_pekerjaan_id', 1)->count(),
            'CM' => $wo->where('tipe_pekerjaan_id', 2)->count()
        ];

        $gangguan = Gangguan::whereYear('report_date', $tahun)->count();
        $latest_gangguan = Gangguan::latest()->take(5)->orderBy('report_date', 'DESC')->get();
        $transaksi_barang = TransaksiBarang::whereYear('tanggal', $tahun)->count();

        $bulan = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12];
        $bulan_name = ['JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN', 'JUL', 'AUG', 'SEP', 'OCT', 'NOV', 'DEC'];

        $availability = [];
        $trend_gangguan = [];
        $trend_sam_card = [];

        foreach($bulan as $i)
        {
            $availability[] = rand(90, 99);
            $trend_gangguan[] = TransaksiBarang::whereYear('tanggal', $tahun)->whereMonth('tanggal', $i)->count();
            $trend_sam_card[] = SamCardHistory::whereYear('tanggal', $tahun)->whereMonth('tanggal', $i)->count();
        }

        $data = [];
        foreach ($bulan as $i => $b) {
            $data[] = [
                'bulan' => $bulan_name[$i],
                'availability' => $availability[$i],
                'url' => route('dashboard.availability.bulan', ['y' => $tahun, 'm' => $b]),
                'trend_gangguan' => $trend_gangguan[$i],
                'url_trend_gangguan' => route('transaksi-barang.trend.monthly', ['y' => $tahun, 'm' => $b]),
                'trend_sam_card' => $trend_sam_card[$i],
                'url_trend_sam_card' => route('sam-history.index', ['start_date' => Carbon::create($tahun, $b)->startOfMonth()->toDateString(), 'end_date' => Carbon::create($tahun, $b)->endOfMonth()->toDateString()]),
            ];
        }

        return view('pages.user.dashboard.index', compact([
            'tahun',
            'permit',
            'samcard',
            'work_order',
            'gangguan',
            'latest_gangguan',
            'transaksi_barang',
            'data'
        ]));
    }

    public function availability_bulan(Request $request)
    {
        $request->validate([
            'y' => ['required', 'integer', 'digits:4', 'min:1900'],
            'm' => ['required', 'integer', 'min:1', 'max:12'],
        ]);

        $tahun = $request->y;
        $bulan = $request->m;
        $bulan_name = Carbon::create()->month($bulan)->format('F');

        $stasiun_id = RelasiArea::where('lokasi_id', 2)
                            ->select('id', 'sub_lokasi_id')
                            ->get()
                            ->unique('sub_lokasi_id')
                            ->pluck('id')
                            ->toArray();

        $stasiun_name = [];
        foreach($stasiun_id as $id)
        {
            $stasiun_name[] = RelasiArea::findOrFail($id)->sub_lokasi->code;
        }

        $availability = [];
        foreach($stasiun_name as $i)
        {
            $availability[] = rand(90, 99);
        }

        $data = [];
        foreach ($stasiun_id as $i => $b) {
            $data[] = [
                'stasiun' => $stasiun_name[$i],
                'availability' => $availability[$i],
                'url' => route('dashboard.availability.station', ['s' => $b, 'y' => $tahun, 'm' => $bulan])
            ];
        }

        return view('pages.user.dashboard.availabilty.station', compact([
            'tahun',
            'bulan',
            'bulan_name',
            'data'
        ]));
    }

    public function availability_station(Request $request)
    {
        $request->validate([
            's' => ['required', 'integer', 'min:1'],
            'y' => ['required', 'integer', 'digits:4', 'min:1900'],
            'm' => ['required', 'integer', 'min:1', 'max:12'],
            'tipe_equipment_id' => ['nullable', 'integer']
        ]);

        $stasiun_id = $request->s;
        $tahun = $request->y;
        $bulan = $request->m;
        $tipe_eq_id = $request->tipe_equipment_id;
        $bulan_name = Carbon::create()->month($bulan)->format('F');

        $relasi_area = RelasiArea::where('lokasi_id', 2)
                                ->get()
                                ->unique('sub_lokasi_id');
        $tipe_equipment_code = TipeEquipment::pluck('code')->toArray();
        $tipe_equipment_id = TipeEquipment::pluck('id')->toArray();
        $tipe_equipment = $tipe_eq_id ? TipeEquipment::findOrFail($tipe_eq_id) : TipeEquipment::first();
        // $tipe_equipment_id = $tipe_equipment->id;
        $equipment = Equipment::where('tipe_equipment_id', $tipe_equipment_id)
                                ->where('relasi_area_id', $stasiun_id)
                                ->pluck('code')->toArray();
        $stasiun_name = RelasiArea::findOrFail($stasiun_id)->sub_lokasi->name;

        $availability = [];
        foreach($tipe_equipment_id as $item)
        {
            $availability[] = rand(90, 99);
        }

        $data = [];
        foreach ($tipe_equipment_id as $i => $id) {
            $data[] = [
                'tipe_equipment' => $tipe_equipment_code[$i],
                'availability' => $availability[$i],
                'url' => route('dashboard.availability.equipment', ['s' => $stasiun_id, 'y' => $tahun, 'm' => $bulan, 'tipe_equipment_id' => $id])
            ];
        }

        return view('pages.user.dashboard.availabilty.tipe_equipment', compact([
            'tahun',
            'bulan',
            'bulan_name',
            'relasi_area',
            'stasiun_id',
            // 'tipe_equipments',
            'tipe_equipment',
            // 'tipe_equipment_id',
            'tipe_equipment_code',
            'stasiun_name',
            'data'
        ]));
    }
    public function availability_equipment(Request $request)
    {
        $request->validate([
            's' => ['required', 'integer', 'min:1'],
            'y' => ['required', 'integer', 'digits:4', 'min:1900'],
            'm' => ['required', 'integer', 'min:1', 'max:12'],
            'tipe_equipment_id' => ['nullable', 'integer']
        ]);

        $stasiun_id = $request->s;
        $tahun = $request->y;
        $bulan = $request->m;
        $tipe_eq_id = $request->tipe_equipment_id;
        $bulan_name = Carbon::create()->month($bulan)->format('F');

        $relasi_area = RelasiArea::where('lokasi_id', 2)->distinct('sub_lokasi_id')->get();
        $tipe_equipments = TipeEquipment::all();
        $tipe_equipment = $tipe_eq_id ? TipeEquipment::findOrFail($tipe_eq_id) : TipeEquipment::first();
        $tipe_equipment_id = $tipe_equipment->id;
        $equipment = Equipment::where('tipe_equipment_id', $tipe_equipment_id)
                                ->where('relasi_area_id', $stasiun_id)
                                ->pluck('code')->toArray();
        $stasiun_name = RelasiArea::findOrFail($stasiun_id)->sub_lokasi->name;

        $availability = [];
        foreach($equipment as $item)
        {
            $availability[] = rand(90, 99);
        }

        $data = [];
        foreach ($equipment as $i => $e) {
            $data[] = [
                'availability' => $availability[$i],
                'equipment' => $e
            ];
        }

        return view('pages.user.dashboard.availabilty.equipment', compact([
            'tahun',
            'bulan',
            'bulan_name',
            'relasi_area',
            'stasiun_id',
            'tipe_equipments',
            'tipe_equipment',
            'tipe_equipment_id',
            'stasiun_name',
            'data'
        ]));
    }

    public function show(string $id)
    {
        //
    }

    public function edit(string $id)
    {
        //
    }

    public function update(Request $request, string $id)
    {
        //
    }

    public function destroy(string $id)
    {
        //
    }
}
