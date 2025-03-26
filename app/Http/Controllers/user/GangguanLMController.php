<?php

namespace App\Http\Controllers\user;

use App\DataTables\GangguanLMDataTable;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Cause;
use App\Models\Classification;
use App\Models\Equipment;
use App\Models\GangguanLM;
use App\Models\Problem;
use App\Models\RelasiArea;
use App\Models\Status;
use App\Models\TransGangguanRemedy;
use App\Models\User;
use App\Services\ImageUploadService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GangguanLMController extends Controller
{
    protected $imageUploadService;

    public function __construct(ImageUploadService $imageUploadService)
    {
        $this->imageUploadService = $imageUploadService;
    }

    public function index(GangguanLMDataTable $dataTable, Request $request)
    {
        return $dataTable->render('pages.user.gangguan-lm.index');
    }

    public function create()
    {
        $userQuery = User::notBanned()->orderBy('name', 'ASC');

        if (auth()->user()->role_id == 2) {
            $userQuery->where('relasi_struktur_id', auth()->user()->relasi_struktur_id); // Hanya user terkait
        }

        if (auth()->user()->role_id == 3) {
            $userQuery->where('id', auth()->user()->id); // Hanya user terkait
        }

        $equipment = Equipment::all();
        $user = $userQuery->get();
        $category = Category::all();
        $classification = Classification::all();
        $lintas = RelasiArea::lintas()->get();
        $line = RelasiArea::line()->get();
        $problem = Problem::all();
        $cause = Cause::all();
        $status = Status::all();

        return view('pages.user.gangguan-lm.create', compact(['equipment', 'user', 'category', 'classification', 'lintas', 'line', 'problem', 'cause', 'status']));
    }

    public function store(Request $request)
    {
        $raw_data = $request->validate([
            'report_user_id' => 'required|numeric|min:1',
            'report_user' => 'nullable|string',
            'report_date' => 'required|date',
            'equipment_id' => 'required|numeric|min:1',
            'category_id' => 'required|numeric|min:1',
            'classification_id' => 'required|numeric|min:1',
            'lintas_id' => 'required|numeric|min:1',
            'line_id' => 'required|numeric|min:1',
            'is_downtime' => 'required|numeric',
            'is_delay' => 'required|numeric',
            'delay' => 'required|numeric|min:0',
            'response_user_id' => 'required|numeric|min:1',
            'response_user' => 'nullable|string',
            'response_date' => 'required|date',
            'problem_id' => 'nullable|numeric|min:1',
            'problem_other' => 'required|string',
            'cause_id' => 'nullable|numeric|min:1',
            'cause_other' => 'required|string',
            'is_change_sparepart' => 'required|numeric',
            'is_change_trainset' => 'required|numeric',
            'solved_user_id' => 'required|numeric|min:1',
            'solved_user' => 'nullable|string',
            'solved_date' => 'required|date',
            'status_id' => 'required|numeric|min:1',
            'remark' => 'nullable|string',
            'analysis' => 'nullable|string',
        ]);

        $request->validate([
            'remedy_other' => ['required', 'array', 'min:1'],
            'remedy_other.0' => ['required', 'string'],
            'remedy_other.*' => ['nullable', 'string'],

            'remedy_date' => ['required', 'array', 'min:1'],
            'remedy_date.0' => ['required', 'date'],
            'remedy_date.*' => ['nullable', 'date'],

            'remedy_user_id' => ['required', 'array', 'min:1'],
            'remedy_user_id.0' => ['required', 'numeric'],
            'remedy_user_id.*' => ['nullable', 'numeric'],

            'photo_before' => 'nullable|file|image',
            'photo_after' => 'nullable|file|image',
        ]);

        if ($request->report_date && $request->response_date && $request->solved_date) {
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

        $data = GangguanLM::updateOrCreate($raw_data, $raw_data);

        foreach ($request->remedy_other as $index => $remedy_other) {
            $remedy_user_id = $request->remedy_user_id[$index] ?? null;
            $remedy_date = $request->remedy_date[$index] ?? null;

            if (!empty($remedy_other) && !empty($remedy_user_id) && !empty($remedy_date)) {
                $data_remedy = [
                    'gangguan_lm_id' => $data->id,
                    'remedy_other' => $remedy_other,
                    'user_id' => $remedy_user_id,
                    'date' => $remedy_date,
                ];

                TransGangguanRemedy::updateOrCreate($data_remedy, $data_remedy);
            }
        }

        // Update photo jika ada
        if ($request->hasFile('photo_before')) {
            $photoPath = $this->imageUploadService->uploadPhoto(
                $request->file('photo_before'),
                'photo/gangguan-lm/', // Path untuk photo
                300,
            );

            // Hapus file lama
            if ($data->photo_before) {
                Storage::delete($data->photo_before);
            }

            // Update path photo di database
            $data->update(['photo_before' => $photoPath]);
        }

        // Update photo after jika ada
        if ($request->hasFile('photo_after')) {
            $photoAfterPath = $this->imageUploadService->uploadPhoto(
                $request->file('photo_after'),
                'photo/gangguan-lm/', // Path untuk photo
                300,
            );

            // Hapus file lama
            if ($data->photo_after) {
                Storage::delete($data->photo_after);
            }

            // Update path photo di database
            $data->update(['photo_after' => $photoAfterPath]);
        }

        return redirect()->route('gangguan.lm.index')->withNotify('Data berhasil ditambahkan');
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

    public function destroy(Request $request)
    {
        //
    }
}
