<?php

namespace App\Http\Controllers\user;

use App\DataTables\LCUChecklistDataTable;
use App\Http\Controllers\Controller;
use App\Models\FunctionalLocation;
use App\Models\LCUChecklist;
use App\Models\User;
use App\Services\ImageUploadService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LCUChecklistController extends Controller
{
    protected $imageUploadService;

    public function __construct(ImageUploadService $imageUploadService)
    {
        $this->imageUploadService = $imageUploadService;
    }

    public function index(LCUChecklistDataTable $dataTable, Request $request)
    {
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $start_date = $request->start_date ? Carbon::parse($request->start_date)->format('Y-m-d') : Carbon::now()->format('Y-m-d');
        $end_date = $request->end_date ? Carbon::parse($request->end_date)->format('Y-m-d') : $start_date;

        $today = Carbon::now()->format('Y-m-d');

        $data_center_id = 161; //Data center id
        $functional_location = FunctionalLocation::where('id', $data_center_id)->get();
        $user = User::notBanned()->get();

        return $dataTable
            ->with([
                'start_date' => $start_date,
                'end_date' => $end_date,
            ])
            ->render('pages.user.lcu-checklist.index', compact([
                'functional_location',
                'user',
                'today',
                'start_date',
                'end_date',
            ]));
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $rawData = $request->validate([
            'mks_status' => 'boolean|required',
            'lighting_status' => 'boolean|required',
            'cctv_status' => 'boolean|required',
            'ac_status' => 'boolean|required',
            'room_cleanliness' => 'boolean|required',
            'server_status' => 'boolean|required',
            'server_alert' => 'boolean|required',
            'switch_status' => 'boolean|required',
            'switch_alert' => 'boolean|required',
            'ups_status' => 'boolean|required',
            'ups_alert' => 'boolean|required',
            'cable_status' => 'boolean|required',
            'room_temperature' => 'numeric|required',
            'rack_temperature' => 'numeric|required',
            'remark' => 'string|nullable',
            'functional_location_id' => 'numeric|required',
        ]);

        $request->validate([
            'room_temp_photo' => 'file|image|required',
            'rack_temp_photo' => 'file|image|required',
        ]);

        $rawData['user_id'] = auth()->user()->id;
        $rawData['date'] = Carbon::now();

        $count_form = LCUChecklist::whereDate('date', Carbon::now())->count();
        if ($count_form >= 3) {
            return redirect()->route('lcu-checklist.index')->withNotify('Data LCU Checklist sudah diisi untuk hari ini!');
        }

        $data = LCUChecklist::updateOrCreate($rawData, $rawData);

        // Update photo temp room jika ada
        if ($request->hasFile('room_temp_photo')) {
            $photoTempRoomPath = $this->imageUploadService->uploadPhoto($request->file('room_temp_photo'), 'photo/lcu-checklist/', 300);

            if ($data->room_temp_photo) {
                Storage::delete($data->room_temp_photo);
            }

            $data->update(['room_temp_photo' => $photoTempRoomPath]);
        }

        // Update photo temp rack jika ada
        if ($request->hasFile('rack_temp_photo')) {
            $photoTempRackPath = $this->imageUploadService->uploadPhoto($request->file('rack_temp_photo'), 'photo/lcu-checklist/', 300);

            if ($data->rack_temp_photo) {
                Storage::delete($data->rack_temp_photo);
            }

            $data->update(['rack_temp_photo' => $photoTempRackPath]);
        }

        return redirect()->route('lcu-checklist.index')->withNotify('Data LCU Checklist berhasil ditambahkan');
    }

    public function show(string $id)
    {
        //
    }

    public function edit(string $id)
    {
        //
    }

    public function update(Request $request)
    {
        $rawData = $request->validate([
            'user_id' => 'numeric|required',
            'functional_location_id' => 'numeric|required',
            'date' => 'required|date_format:Y-m-d\TH:i',
            'mks_status' => 'boolean|required',
            'lighting_status' => 'boolean|required',
            'cctv_status' => 'boolean|required',
            'ac_status' => 'boolean|required',
            'room_cleanliness' => 'boolean|required',
            'server_status' => 'boolean|required',
            'server_alert' => 'boolean|required',
            'switch_status' => 'boolean|required',
            'switch_alert' => 'boolean|required',
            'ups_status' => 'boolean|required',
            'ups_alert' => 'boolean|required',
            'cable_status' => 'boolean|required',
            'room_temperature' => 'numeric|required',
            'rack_temperature' => 'numeric|required',
            'remark' => 'string|nullable',
        ]);

        $request->validate([
            'id' => 'required|numeric',
            'room_temp_photo' => 'file|image|nullable',
            'rack_temp_photo' => 'file|image|nullable',
        ]);

        $data = LCUChecklist::findOrFail($request->id);

        $data->update($rawData);

        // Update photo temp room jika ada
        if ($request->hasFile('room_temp_photo')) {
            $photoTempRoomPath = $this->imageUploadService->uploadPhoto($request->file('room_temp_photo'), 'photo/lcu-checklist/', 300);

            if ($data->room_temp_photo) {
                Storage::delete($data->room_temp_photo);
            }

            $data->update(['room_temp_photo' => $photoTempRoomPath]);
        }

        // Update photo temp rack jika ada
        if ($request->hasFile('rack_temp_photo')) {
            $photoTempRackPath = $this->imageUploadService->uploadPhoto($request->file('rack_temp_photo'), 'photo/lcu-checklist/', 300);

            if ($data->rack_temp_photo) {
                Storage::delete($data->rack_temp_photo);
            }

            $data->update(['rack_temp_photo' => $photoTempRackPath]);
        }

        return redirect()->route('lcu-checklist.index')->withNotify('Data LCU Checklist berhasil diubah');
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'id' => 'required|numeric',
        ]);

        $data = LCUChecklist::findOrFail($request->id);

        if ($data->room_temp_photo) {
            Storage::delete($data->room_temp_photo);
        }

        if ($data->rack_temp_photo) {
            Storage::delete($data->rack_temp_photo);
        }

        $data->forceDelete();

        return redirect()->route('lcu-checklist.index')->withNotify('Data LCU Checklist berhasil dihapus');
    }
}
