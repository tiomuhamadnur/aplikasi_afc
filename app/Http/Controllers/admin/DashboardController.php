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
    public function index(Request $request)
    {
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        if ($request->start_date && $request->end_date) {
            $startYear = Carbon::parse($request->start_date)->year;
            $endYear = Carbon::parse($request->end_date)->year;

            if ($startYear !== $endYear) {
                return redirect()->back()->withNotifyerror('Tanggal evaluasi harus di tahun yang sama');
            }
        }

        $end_date = $request->end_date;
        $start_date = $request->start_date;

        $sekarang = $end_date ? Carbon::parse($end_date) : Carbon::now();
        $year = $sekarang->year;
        $start_date = $start_date ? Carbon::parse($start_date) : Carbon::create($sekarang->year, 1, 1)->startOfDay();

        // Format untuk ditampilkan
        $mulai = $start_date->isoFormat('D MMM Y');
        $akhir = $sekarang->isoFormat('D MMM Y');
        $today = "$mulai - $akhir";

        // Range dalam format datetime untuk query
        $range = [
            $start_date->startOfDay()->format('Y-m-d H:i:s'),
            $sekarang->endOfDay()->format('Y-m-d H:i:s'),
        ];


        $permit = MonitoringPermit::where('departemen_id', auth()->user()->relasi_struktur->departemen->id)->count();

        $samcard_ready = SamCard::where('status', 'ready')->count();
        $samcard_used = SamCard::where('status', 'used')->count();
        $samcard = [
            'ready' => $samcard_ready,
            'used' => $samcard_used
        ];

        $wo = WorkOrder::whereBetween('date', $range)
                        ->where('relasi_struktur_id', auth()->user()->relasi_struktur_id)
                        ->get();
        $work_order = [
            'PM' => $wo->where('tipe_pekerjaan_id', 1)->count(),
            'CM' => $wo->where('tipe_pekerjaan_id', 2)->count()
        ];

        $gangguan = Gangguan::whereBetween('report_date', $range)->count();
        $latest_gangguan = Gangguan::latest()->take(5)->orderBy('report_date', 'DESC')->get();
        $transaksi_barang = TransaksiBarang::whereBetween('tanggal', $range)->count();

        $bulan = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12];
        $bulan_name = ['JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN', 'JUL', 'AUG', 'SEP', 'OCT', 'NOV', 'DEC'];

        $availability = [];
        $trend_gangguan = [];
        $trend_sparepart = [];
        $trend_sam_card = [];

        foreach($bulan as $i)
        {
            $availability[] = rand(90, 99);
            $trend_gangguan[] = Gangguan::whereYear('report_date', $year)->whereMonth('report_date', $i)->count();
            $trend_sparepart[] = TransaksiBarang::whereYear('tanggal', $year)->whereMonth('tanggal', $i)->count();
            $trend_sam_card[] = SamCardHistory::whereYear('tanggal', $year)->whereMonth('tanggal', $i)->count();
        }

        $data = [];
        foreach ($bulan as $i => $b) {
            $data[] = [
                'bulan' => $bulan_name[$i],
                'availability' => $availability[$i],
                'url' => route('dashboard.availability.bulan', ['y' => $year, 'm' => $b]),
                'trend_gangguan' => $trend_gangguan[$i],
                'url_trend_gangguan' => route('gangguan.trend.monthly', ['y' => $year, 'm' => $b]),
                'trend_sparepart' => $trend_sparepart[$i],
                'url_trend_sparepart' => route('transaksi-barang.trend.monthly', ['y' => $year, 'm' => $b]),
                'trend_sam_card' => $trend_sam_card[$i],
                'url_trend_sam_card' => route('sam-history.index', ['start_date' => Carbon::create($year, $b)->startOfMonth()->toDateString(), 'end_date' => Carbon::create($year, $b)->endOfMonth()->toDateString()]),
            ];
        }

        $statusCounts = Gangguan::whereBetween('report_date', $range)
                        ->get()
                        ->groupBy('status_id')
                        ->map(fn($group) => $group->count());

        $gangguanByStatus = [
            ['Open', $statusCounts->get(1, 0)],
            ['Closed', $statusCounts->get(2, 0)],
            ['Monitoring', $statusCounts->get(3, 0)],
            ['Pending', $statusCounts->get(4, 0)],
        ];

        $klasifikasiCounts = Gangguan::whereBetween('report_date', $range)
                        ->get()
                        ->groupBy('classification_id')
                        ->map(fn($group) => $group->count());

        $gangguanByKlasifikasi = [
            ['Low', $klasifikasiCounts->get(1, 0)],
            ['Medium', $klasifikasiCounts->get(2, 0)],
            ['High', $klasifikasiCounts->get(3, 0)],
        ];

        return view('pages.user.dashboard.index', [
            'today' => $today,
            'start_date' => Carbon::parse($start_date)->isoFormat('YYYY-MM-DD'),
            'end_date' => $sekarang->isoFormat('YYYY-MM-DD'),
            'year' => $year,
            'permit' => $permit,
            'samcard' => $samcard,
            'work_order' => $work_order,
            'gangguan' => $gangguan,
            'gangguanByStatus' => $gangguanByStatus,
            'gangguanByKlasifikasi' => $gangguanByKlasifikasi,
            'latest_gangguan' => $latest_gangguan,
            'transaksi_barang' => $transaksi_barang,
            'data' => $data
        ]);
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
