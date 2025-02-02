<?php

namespace App\Http\Controllers\user;

use App\DataTables\GangguanDataTable;
use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\Category;
use App\Models\Cause;
use App\Models\Classification;
use App\Models\Equipment;
use App\Models\Gangguan;
use App\Models\Problem;
use App\Models\RelasiArea;
use App\Models\Remedy;
use App\Models\Status;
use App\Models\TipeEquipment;
use App\Models\TransaksiBarang;
use App\Models\TransGangguanRemedy;
use App\Models\User;
use App\Services\ImageUploadService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;

class GangguanController extends Controller
{
    protected $imageUploadService;

    public function __construct(ImageUploadService $imageUploadService)
    {
        $this->imageUploadService = $imageUploadService;
    }

    public function index(GangguanDataTable $dataTable, Request $request)
    {
        $request->validate([
            'area_id' => 'numeric|nullable',
            'category_id' => 'numeric|nullable',
            'equipment_id' => 'numeric|nullable',
            'tipe_equipment_id' => 'numeric|nullable',
            'classification_id' => 'numeric|nullable',
            'status_id' => 'numeric|nullable',
            'start_date' => 'date|nullable',
            'end_date' => 'date|nullable',
            'is_changed' => 'numeric|nullable',
            'is_downtime' => 'numeric|nullable',
        ]);

        $area_id = $request->area_id ?? null;
        $category_id = $request->category_id ?? null;
        $equipment_id = $request->equipment_id ?? null;
        $tipe_equipment_id = $request->tipe_equipment_id ?? null;
        $classification_id = $request->classification_id ?? null;
        $status_id = $request->status_id ?? null;
        $start_date = $request->start_date ?? Carbon::now()->format('Y-m-d');
        $end_date = $request->end_date ?? $start_date;
        $is_changed = $request->is_changed ?? null;
        $is_downtime = $request->is_downtime ?? null;

        $equipment = Equipment::where('relasi_struktur_id', auth()->user()->relasi_struktur_id)->get();
        $barang = Barang::all();
        $status = Status::all();
        $category = Category::all();
        $classification = Classification::all();
        $tipe_equipment = TipeEquipment::all();
        $area = RelasiArea::where('lokasi_id', 2)
                        ->get()
                        ->unique('sub_lokasi_id');
        $problem = Problem::all();


        return $dataTable->with([
            'area_id' => $area_id,
            'category_id' => $category_id,
            'equipment_id' => $equipment_id,
            'tipe_equipment_id' => $tipe_equipment_id,
            'classification_id' => $classification_id,
            'status_id' => $status_id,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'is_changed' => $is_changed,
            'is_downtime' => $is_downtime,
        ])->render('pages.user.gangguan.index', compact([
            'equipment',
            'tipe_equipment',
            'barang',
            'status',
            'category',
            'problem',
            'classification',
            'area',
            'area_id',
            'category_id',
            'equipment_id',
            'tipe_equipment_id',
            'classification_id',
            'status_id',
            'start_date',
            'end_date',
            'is_changed',
            'is_downtime',
        ]));
    }

    public function create()
    {
        $barang = Barang::all();
        $status = Status::all();

        $userQuery = User::where('relasi_struktur_id', auth()->user()->relasi_struktur_id)
                        ->notBanned()
                        ->orderBy('name', 'ASC');

        if (auth()->user()->role_id == 3) {
            $userQuery->where('id', auth()->user()->id); // Hanya user terkait
        }

        $user = $userQuery->get();

        return view('pages.user.gangguan.create', compact([
            'barang',
            'status',
            'user',
        ]));
    }

    public function store(Request $request)
    {
        $raw_data = $request->validate([
            'report_by' => 'required|string',
            'report_date' => 'required|date',
            'equipment_id' => 'required|numeric',
            'category_id' => 'required|numeric',
            'problem_id' => 'nullable|numeric',
            'problem_other' => 'required|string',
            'cause_id' => 'nullable|numeric',
            'cause_other' => 'required|string',
            'response_date' => 'required|date',
            'solved_user_id' => 'required|numeric',
            'solved_date' => 'required|date',
            'classification_id' => 'required|numeric',
            'remark' => 'nullable|string',
            'status_id' => 'required|numeric',
            'is_changed' => 'boolean|required',
            'is_downtime' => 'boolean|required',
        ]);

        $request->validate([
            'photo' => 'file|image',
            'photo_after' => 'file|image',
            // 'barang_ids' => 'array',
            // 'qty' => 'array',
            'remedy_id' => 'nullable|numeric',
            'remedy_other' => 'required|string',
        ]);

        // if ($raw_data['problem_id'] == 0 && $raw_data['cause_id'] == 0) {
        //     $raw_data['problem_id'] = null;
        //     $raw_data['cause_id'] = null;
        // }

        if($request->report_date && $request->response_date && $request->solved_date)
        {
            $report_date = Carbon::parse($request->report_date);
            $response_date = Carbon::parse($request->response_date);
            $solved_date = Carbon::parse($request->solved_date);

            $response_time = $report_date->diffInMinutes($response_date);
            $resolution_time = $response_date->diffInMinutes($solved_date);
            $total_time = $response_time + $resolution_time;

            $raw_data['response_time'] = $response_time;
            $raw_data['resolution_time'] = $resolution_time;
            $raw_data['total_time'] = $total_time;
        }

        $data = Gangguan::create($raw_data);

        TransGangguanRemedy::updateOrCreate([
            'gangguan_id' => $data->id,
            // 'remedy_id' => $request->remedy_id < 1 ? null : $request->remedy_id,
            'remedy_other' => $request->remedy_other,
            // 'user_id' => $request->solved_user_id,
            // 'date' => $data->response_date,
        ],[
            'gangguan_id' => $data->id,
            // 'remedy_id' => $request->remedy_id < 1 ? null : $request->remedy_id,
            'remedy_other' => $request->remedy_other,
            // 'user_id' => $request->solved_user_id,
            // 'date' => $data->response_date,
        ]);

        // Update photo jika ada
        if ($request->hasFile('photo')) {
            $photoPath = $this->imageUploadService->uploadPhoto(
                $request->file('photo'),
                'photo/gangguan/', // Path untuk photo
                300
            );

            // Hapus file lama
            if ($data->photo) {
                Storage::delete($data->photo);
            }

            // Update path photo di database
            $data->update(['photo' => $photoPath]);
        }

        // Update photo after jika ada
        if ($request->hasFile('photo_after')) {
            $photoAfterPath = $this->imageUploadService->uploadPhoto(
                $request->file('photo_after'),
                'photo/gangguan/', // Path untuk photo
                300
            );

            // Hapus file lama
            if ($data->photo_after) {
                Storage::delete($data->photo_after);
            }

            // Update path photo di database
            $data->update(['photo_after' => $photoAfterPath]);
        }

        return redirect()->route('gangguan.index')->withNotify('Data berhasil ditambahkan');
    }

    public function show(string $uuid)
    {
        $gangguan = Gangguan::where('uuid', $uuid)->firstOrFail();

        return view('pages.user.gangguan.show', compact([
            'gangguan',
        ]));
    }

    public function edit(string $uuid)
    {
        $gangguan = Gangguan::where('uuid', $uuid)->firstOrFail();

        $equipment = Equipment::where('relasi_struktur_id', auth()->user()->relasi_struktur_id)->get();
        $status = Status::all();
        $category = Category::all();
        $classification = Classification::all();
        $problem = Problem::all();
        $cause = Cause::all();
        $remedies = Remedy::all();
        $remedy_id = TransGangguanRemedy::where('gangguan_id', $gangguan->id)->first()->remedy_id;
        $user = User::where('relasi_struktur_id', auth()->user()->relasi_struktur_id)->orderBy('name', 'ASC')->get();

        return view('pages.user.gangguan.edit', compact([
            'gangguan',
            'equipment',
            'status',
            'problem',
            'cause',
            'remedies',
            'remedy_id',
            'category',
            'classification',
            'user',
        ]));
    }

    public function update(Request $request)
    {
        $raw_data = $request->validate([
            'report_by' => 'required|string',
            'report_date' => 'required|date',
            'equipment_id' => 'required|numeric',
            'category_id' => 'required|numeric',
            'problem_id' => 'nullable|numeric',
            'problem_other' => 'required|string',
            'cause_id' => 'nullable|numeric',
            'cause_other' => 'required|string',
            'response_date' => 'required|date',
            'solved_user_id' => 'required|numeric',
            'solved_date' => 'required|date',
            'classification_id' => 'required|numeric',
            'remark' => 'nullable|string',
            'status_id' => 'required|numeric',
            'is_changed' => 'boolean|required',
            'is_downtime' => 'boolean|required',
        ]);

        $request->validate([
            'id' => 'required|numeric',
            'photo' => 'file|image',
            'photo_after' => 'file|image',
            // 'remedy_id' => 'nullable|numeric',
            // 'remedy_other' => 'required|string',
        ]);

        // if ($raw_data['problem_id'] == 0) {
        //     $raw_data['problem_id'] = null;
        // }

        if($request->report_date && $request->response_date && $request->solved_date)
        {
            $report_date = Carbon::parse($request->report_date);
            $response_date = Carbon::parse($request->response_date);
            $solved_date = Carbon::parse($request->solved_date);

            $response_time = $report_date->diffInMinutes($response_date);
            $resolution_time = $response_date->diffInMinutes($solved_date);
            $total_time = $response_time + $resolution_time;

            $raw_data['response_time'] = $response_time;
            $raw_data['resolution_time'] = $resolution_time;
            $raw_data['total_time'] = $total_time;
        }

        $data = Gangguan::findOrFail($request->id);

        $data->update($raw_data);

        // Update photo jika ada
        if ($request->hasFile('photo')) {
            $photoPath = $this->imageUploadService->uploadPhoto(
                $request->file('photo'),
                'photo/gangguan/', // Path untuk photo
                300
            );

            // Hapus file lama
            if ($data->photo) {
                Storage::delete($data->photo);
            }

            // Update path photo di database
            $data->update(['photo' => $photoPath]);
        }

        // Update photo after jika ada
        if ($request->hasFile('photo_after')) {
            $photoAfterPath = $this->imageUploadService->uploadPhoto(
                $request->file('photo_after'),
                'photo/gangguan/', // Path untuk photo
                300
            );

            // Hapus file lama
            if ($data->photo_after) {
                Storage::delete($data->photo_after);
            }

            // Update path photo di database
            $data->update(['photo_after' => $photoAfterPath]);
        }

        return redirect()->route('gangguan.index')->withNotify('Data berhasil diubah');
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'id' => 'required|numeric'
        ]);

        $data = Gangguan::findOrFail($request->id);
        $data->delete();

        return redirect()->route('gangguan.index')->withNotify('Data berhasil dihapus');
    }

    public function trend_monthly(Request $request)
    {
        $request->validate([
            'y' => ['required', 'integer', 'digits:4', 'min:1900'],
            'm' => ['required', 'integer', 'min:1', 'max:12'],
        ]);

        $tahun = $request->y;
        $bulan = $request->m;
        $bulan_name = Carbon::create()->month($bulan)->format('F');

        $tanggal = [];

        $startDate = Carbon::createFromDate($tahun, $bulan, 1);
        $endDate = $startDate->copy()->endOfMonth();

        for ($date = $startDate; $date->lte($endDate); $date->addDay()) {
            $tanggal[] = $date->format('Y-m-d');
        }

        $gangguan = [];
        foreach($tanggal as $i)
        {
            $gangguan[] = Gangguan::whereDate('report_date', $i)->count();
        }

        $data = [];
        foreach ($tanggal as $i => $tgl) {
            $data[] = [
                'tanggal' => $tgl,
                'gangguan' => $gangguan[$i],
                'url' => route('gangguan.index', ['start_date' => $tgl, 'end_date' => $tgl])
            ];
        }

        return view('pages.user.gangguan.chart.daily', compact([
            'tahun',
            'bulan',
            'bulan_name',
            'data'
        ]));
    }
}
