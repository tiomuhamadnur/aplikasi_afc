<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Equipment;
use App\Models\Gangguan;
use App\Models\MonitoringPermit;
use App\Models\RelasiArea;
use App\Models\SamCard;
use App\Models\SamCardHistory;
use App\Models\SubLokasi;
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
        $range = [$start_date->startOfDay()->format('Y-m-d H:i:s'), $sekarang->endOfDay()->format('Y-m-d H:i:s')];

        $permit = MonitoringPermit::where('departemen_id', auth()->user()->relasi_struktur->departemen->id)->count();

        $samcard_ready = SamCard::where('status', 'ready')->count();
        $samcard_used = SamCard::where('status', 'used')->count();
        $samcard = [
            'ready' => $samcard_ready,
            'used' => $samcard_used,
        ];

        $wo = WorkOrder::whereBetween('date', $range)
            ->where('relasi_struktur_id', auth()->user()->relasi_struktur_id)
            ->get();
        $work_order = [
            'PM' => $wo->where('tipe_pekerjaan_id', 1)->count(),
            'CM' => $wo->where('tipe_pekerjaan_id', 2)->count(),
        ];

        $gangguan = Gangguan::whereBetween('report_date', $range)->count();
        $latest_gangguan = Gangguan::latest()->take(5)->orderBy('report_date', 'DESC')->get();
        $transaksi_barang = TransaksiBarang::whereBetween('tanggal', $range)->count();

        $bulan = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12];
        $bulan_name = ['JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN', 'JUL', 'AUG', 'SEP', 'OCT', 'NOV', 'DEC'];

        $trend_gangguan = [];
        $trend_sparepart = [];
        $trend_sam_card = [];

        foreach ($bulan as $i) {
            $trend_gangguan[] = Gangguan::whereYear('report_date', $year)->whereMonth('report_date', $i)->count();
            $trend_sparepart[] = TransaksiBarang::whereYear('tanggal', $year)->whereMonth('tanggal', $i)->count();
            $trend_sam_card[] = SamCardHistory::whereYear('tanggal', $year)->whereMonth('tanggal', $i)->count();
        }

        $data = [];
        foreach ($bulan as $i => $b) {
            $data[] = [
                'bulan' => $bulan_name[$i],
                'availability' => $this->get_data_availability_bulanan($year, $b, auth()->user()->relasi_struktur_id),
                'url' => route('dashboard.availability.bulan', ['y' => $year, 'm' => $b]),
                'trend_gangguan' => $trend_gangguan[$i],
                'url_trend_gangguan' => route('gangguan.trend.monthly', ['y' => $year, 'm' => $b]),
                'trend_sparepart' => $trend_sparepart[$i],
                'url_trend_sparepart' => route('transaksi-barang.trend.monthly', ['y' => $year, 'm' => $b]),
                'trend_sam_card' => $trend_sam_card[$i],
                'url_trend_sam_card' => route('sam-history.index', ['start_date' => Carbon::create($year, $b)->startOfMonth()->toDateString(), 'end_date' => Carbon::create($year, $b)->endOfMonth()->toDateString()]),
            ];
        }

        // Status
        $statusCounts = Gangguan::whereBetween('report_date', $range)->get()->groupBy('status_id')->map(fn($group) => $group->count());

        $gangguanByStatus = [['Open', $statusCounts->get(1, 0)], ['Closed', $statusCounts->get(2, 0)], ['Monitoring', $statusCounts->get(3, 0)], ['Pending', $statusCounts->get(4, 0)]];

        // Klasifikasi
        $klasifikasiCounts = Gangguan::whereBetween('report_date', $range)->get()->groupBy('classification_id')->map(fn($group) => $group->count());

        $gangguanByKlasifikasi = [['Low', $klasifikasiCounts->get(1, 0)], ['Medium', $klasifikasiCounts->get(2, 0)], ['High', $klasifikasiCounts->get(3, 0)]];

        // Area
        $stasiun = RelasiArea::where('lokasi_id', 2)->select('id', 'sub_lokasi_id')->get()->unique('sub_lokasi_id');

        $gangguanByArea = [];
        foreach ($stasiun as $item) {
            $gangguanCount = Gangguan::whereBetween('report_date', $range)
                ->whereRelation('equipment.relasi_area', 'id', '=', $item->id)
                ->whereRelation('equipment.relasi_struktur', 'id', '=', auth()->user()->relasi_struktur->id)
                ->count();

            $gangguanByArea[] = [
                'name' => $item->sub_lokasi->code,
                'value' => $gangguanCount,
                'colorValue' => $gangguanCount,
            ];
        }

        // Tipe
        $tipe = TipeEquipment::get();

        $gangguanByTipe = [];
        foreach ($tipe as $item) {
            $gangguanCount = Gangguan::whereBetween('report_date', $range)
                ->whereRelation('equipment.relasi_struktur', 'id', '=', auth()->user()->relasi_struktur->id)
                ->whereRelation('equipment.tipe_equipment', 'id', '=', $item->id)
                ->count();

            $gangguanByTipe[] = [
                'name' => $item->code,
                'value' => $gangguanCount,
                'colorValue' => $gangguanCount,
            ];
        }

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
            'gangguanByArea' => $gangguanByArea,
            'gangguanByTipe' => $gangguanByTipe,
            'latest_gangguan' => $latest_gangguan,
            'transaksi_barang' => $transaksi_barang,
            'data' => $data,
        ]);
    }

    public function get_data_availability_bulanan($tahun, $bulan, $relasi_struktur_id)
    {
        $today = Carbon::today();
        $requestedDate = Carbon::createFromDate($tahun, $bulan, 1);

        if ($requestedDate->greaterThan($today)) {
            return 0;
        }

        $date = Carbon::createFromDate($tahun, $bulan, 1);
        $daysInMonth = $date->daysInMonth;

        $operation_time = Equipment::where('relasi_struktur_id', $relasi_struktur_id)
            ->whereHas('tipe_equipment')
            ->with('tipe_equipment')
            ->get()
            ->sum(function ($equipment) use ($daysInMonth) {
                return $equipment->tipe_equipment->operation_time * $daysInMonth * 60; // Konversi ke menit
            });

        $down_time = Gangguan::whereRelation('equipment.relasi_struktur', 'id', '=', $relasi_struktur_id)->whereYear('report_date', $tahun)->whereMonth('report_date', $bulan)->where('is_downtime', true)->sum('total_time');

        $availability = (($operation_time - $down_time) / $operation_time) * 100; // dalam persen (%)

        return $availability;
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

        // Mendapatkan stasiun_id dengan lebih efisien
        $stasiun_ids = RelasiArea::where('lokasi_id', 2)
            ->distinct('sub_lokasi_id') // Pastikan ID unik
            ->pluck('sub_lokasi_id') // Ambil hanya kolom sub_lokasi_id
            ->toArray();

        $stasiuns = SubLokasi::whereIn('id', $stasiun_ids)->get();

        $data = [];
        foreach ($stasiuns as $item) {
            $data[] = [
                'stasiun' => $item->code,
                'availability' => $this->get_data_availability_station($tahun, $bulan, auth()->user()->relasi_struktur->id, $item->id),
                'url' => route('dashboard.availability.station', ['s' => $item->id, 'y' => $tahun, 'm' => $bulan]),
            ];
        }

        return view('pages.user.dashboard.availabilty.station', compact(['tahun', 'bulan', 'bulan_name', 'data']));
    }

    public function get_data_availability_station($tahun, $bulan, $relasi_struktur_id, $stasiun_id)
    {
        // Validasi tanggal agar tidak lebih dari hari ini
        $today = Carbon::today();
        $requestedDate = Carbon::createFromDate($tahun, $bulan, 1);

        if ($requestedDate->greaterThan($today)) {
            return 0; // Availability 0% jika bulan dan tahun belum terjadi
        }

        $date = Carbon::createFromDate($tahun, $bulan, 1);
        $daysInMonth = $date->daysInMonth;

        // Optimalkan query untuk mendapatkan operation_time
        $operation_time = Equipment::whereRelation('relasi_area.sub_lokasi', 'id', '=', $stasiun_id)
            ->where('relasi_struktur_id', $relasi_struktur_id)
            ->whereHas('tipe_equipment') // Pastikan tipe_equipment ada
            ->with('tipe_equipment') // Memuat relasi tipe_equipment
            ->get()
            ->sum(function ($equipment) use ($daysInMonth) {
                // Pastikan operation_time ada dan valid
                return $equipment->tipe_equipment ? $equipment->tipe_equipment->operation_time * $daysInMonth * 60 : 0;
            });

        // Menghitung downtime
        $down_time = Gangguan::whereRelation('equipment.relasi_struktur', 'id', '=', $relasi_struktur_id)
            ->whereRelation('equipment.relasi_area.sub_lokasi', 'id', '=', $stasiun_id)
            ->whereYear('report_date', $tahun)
            ->whereMonth('report_date', $bulan)
            ->where('is_downtime', true)
            ->sum('total_time');

        // Menghitung availability dalam persen
        if ($operation_time == 0) {
            return 0; // Jika operation_time 0, maka availability juga 0%
        }

        $availability = (($operation_time - $down_time) / $operation_time) * 100; // dalam persen (%)

        return $availability;
    }

    public function availability_station(Request $request)
    {
        $request->validate([
            's' => ['required', 'integer', 'min:1'],
            'y' => ['required', 'integer', 'digits:4', 'min:1900'],
            'm' => ['required', 'integer', 'min:1', 'max:12'],
        ]);

        $stasiun_id = $request->s;
        $tahun = $request->y;
        $bulan = $request->m;
        $bulan_name = Carbon::create()->month($bulan)->format('F');
        $stasiun_name = SubLokasi::findOrFail($stasiun_id)->name;

        $tipe_equipment_ids = Equipment::where('relasi_struktur_id', auth()->user()->relasi_struktur->id)
                ->whereRelation('relasi_area.sub_lokasi', 'id', '=', $stasiun_id)
                ->whereHas('tipe_equipment') // Memastikan relasi tipe_equipment ada
                ->with('tipe_equipment') // Memuat relasi tipe_equipment
                ->get()
                ->pluck('tipe_equipment.id') // Ambil kolom id dari relasi tipe_equipment
                ->unique()
                ->sort()
                ->values() // Reset index array
                ->toArray(); // Konversi ke array

        $tipe_equipments = TipeEquipment::whereIn('id', $tipe_equipment_ids)->get();

        $data = [];
        foreach ($tipe_equipments as $item) {
            $data[] = [
                'tipe_equipment' => $item->code,
                'availability' => $this->get_data_availability_tipe_equipment($tahun, $bulan, auth()->user()->relasi_struktur->id, $stasiun_id, $item->id),
                'url' => route('dashboard.availability.equipment', ['s' => $stasiun_id, 'y' => $tahun, 'm' => $bulan, 'tipe_equipment_id' => $item->id]),
            ];
        }

        return view(
            'pages.user.dashboard.availabilty.tipe_equipment',
            compact([
                'tahun',
                'bulan',
                'bulan_name',
                'stasiun_name',
                'data',
            ]),
        );
    }

    public function get_data_availability_tipe_equipment($tahun, $bulan, $relasi_struktur_id, $stasiun_id, $tipe_equipment_id)
    {
        // Validasi tanggal agar tidak lebih dari hari ini
        $today = Carbon::today();
        $requestedDate = Carbon::createFromDate($tahun, $bulan, 1);

        if ($requestedDate->greaterThan($today)) {
            return 0; // Availability 0% jika bulan dan tahun belum terjadi
        }

        $date = Carbon::createFromDate($tahun, $bulan, 1);
        $daysInMonth = $date->daysInMonth;

        // Optimalkan query untuk mendapatkan operation_time
        $operation_time = Equipment::whereRelation('relasi_area.sub_lokasi', 'id', '=', $stasiun_id)
            ->where('relasi_struktur_id', $relasi_struktur_id)
            ->where('tipe_equipment_id', $tipe_equipment_id)
            ->whereHas('tipe_equipment')
            ->with('tipe_equipment')
            ->get()
            ->sum(function ($equipment) use ($daysInMonth) {
                return $equipment->tipe_equipment ? $equipment->tipe_equipment->operation_time * $daysInMonth * 60 : 0;
            });

        // Menghitung downtime
        $down_time = Gangguan::whereRelation('equipment.relasi_struktur', 'id', '=', $relasi_struktur_id)
            ->whereRelation('equipment.relasi_area.sub_lokasi', 'id', '=', $stasiun_id)
            ->whereRelation('equipment.tipe_equipment', 'id', '=', $tipe_equipment_id)
            ->whereYear('report_date', $tahun)
            ->whereMonth('report_date', $bulan)
            ->where('is_downtime', true)
            ->sum('total_time');

        // Menghitung availability dalam persen
        if ($operation_time == 0) {
            return 0; // Jika operation_time 0, maka availability juga 0%
        }

        $availability = (($operation_time - $down_time) / $operation_time) * 100; // dalam persen (%)

        return $availability;
    }

    public function availability_equipment(Request $request)
    {
        $request->validate([
            's' => ['required', 'integer', 'min:1'],
            'y' => ['required', 'integer', 'digits:4', 'min:1900'],
            'm' => ['required', 'integer', 'min:1', 'max:12'],
            'tipe_equipment_id' => ['nullable', 'integer'],
        ]);

        $stasiun_id = $request->s;
        $tahun = $request->y;
        $bulan = $request->m;
        $bulan_name = Carbon::create()->month($bulan)->format('F');
        $tipe_equipment_id = $request->tipe_equipment_id;
        $tipe_equipment = TipeEquipment::findOrFail($tipe_equipment_id);
        $stasiun_name = SubLokasi::findOrFail($stasiun_id)->name;

        $equipments = Equipment::whereRelation('relasi_area.sub_lokasi', 'id', '=', $stasiun_id)
                ->where('tipe_equipment_id', $tipe_equipment_id)
                ->get();

        $data = [];
        foreach ($equipments as $item) {
            $data[] = [
                'availability' => $this->get_data_availability_equipment($tahun, $bulan, auth()->user()->relasi_struktur->id, $stasiun_id, $tipe_equipment_id, $item->id),
                'equipment' => $item->code,
            ];
        }

        return view('pages.user.dashboard.availabilty.equipment', compact(['tahun', 'bulan', 'bulan_name', 'tipe_equipment', 'stasiun_name', 'data']));
    }

    public function get_data_availability_equipment($tahun, $bulan, $relasi_struktur_id, $stasiun_id, $tipe_equipment_id, $equipment_id)
    {
        // Validasi tanggal agar tidak lebih dari hari ini
        $today = Carbon::today();
        $requestedDate = Carbon::createFromDate($tahun, $bulan, 1);

        if ($requestedDate->greaterThan($today)) {
            return 0; // Availability 0% jika bulan dan tahun belum terjadi
        }

        $date = Carbon::createFromDate($tahun, $bulan, 1);
        $daysInMonth = $date->daysInMonth;

        // Optimalkan query untuk mendapatkan operation_time
        $operation_time = Equipment::whereRelation('relasi_area.sub_lokasi', 'id', '=', $stasiun_id)
            ->where('relasi_struktur_id', $relasi_struktur_id)
            ->where('tipe_equipment_id', $tipe_equipment_id)
            ->whereHas('tipe_equipment')
            ->with('tipe_equipment')
            ->get()
            ->sum(function ($equipment) use ($daysInMonth) {
                return $equipment->tipe_equipment ? $equipment->tipe_equipment->operation_time * $daysInMonth * 60 : 0;
            });

        // Menghitung downtime
        $down_time = Gangguan::whereRelation('equipment.relasi_struktur', 'id', '=', $relasi_struktur_id)
            ->whereRelation('equipment.relasi_area.sub_lokasi', 'id', '=', $stasiun_id)
            ->whereRelation('equipment.tipe_equipment', 'id', '=', $tipe_equipment_id)
            ->where('equipment_id', $equipment_id)
            ->whereYear('report_date', $tahun)
            ->whereMonth('report_date', $bulan)
            ->where('is_downtime', true)
            ->sum('total_time');

        // Menghitung availability dalam persen
        if ($operation_time == 0) {
            return 0; // Jika operation_time 0, maka availability juga 0%
        }

        $availability = (($operation_time - $down_time) / $operation_time) * 100; // dalam persen (%)

        return $availability;
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
