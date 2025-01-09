<?php

namespace App\Http\Controllers\admin;

use App\DataTables\EquipmentDataTable;
use App\Http\Controllers\Controller;
use App\Imports\EquipmentImport;
use App\Models\Arah;
use App\Models\Equipment;
use App\Models\FunctionalLocation;
use App\Models\RelasiArea;
use App\Models\RelasiStruktur;
use App\Models\TipeEquipment;
use App\Services\ImageUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Maatwebsite\Excel\Facades\Excel;

class EquipmentController extends Controller
{
    protected $imageUploadService;

    public function __construct(ImageUploadService $imageUploadService)
    {
        $this->imageUploadService = $imageUploadService;
    }

    public function index(EquipmentDataTable $dataTable)
    {
        $tipe_equipment = TipeEquipment::all();
        $area = RelasiArea::all();
        $struktur = RelasiStruktur::all();
        $arah = Arah::all();
        $functional_location = FunctionalLocation::all();
        $equipment = Equipment::all();

        return $dataTable->render('pages.admin.equipment.index', compact([
            'tipe_equipment',
            'area',
            'struktur',
            'arah',
            'functional_location',
            'equipment'
        ]));
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $rawData = $request->validate([
            'name' => 'required',
            'code' => 'required',
            'equipment_number' => 'nullable|numeric',
            'tipe_equipment_id' => 'required|numeric',
            'relasi_area_id' => 'required|numeric',
            'relasi_struktur_id' => 'required|numeric',
            'functional_location_id' => 'required|numeric',
            'parent_id' => 'nullable|numeric',
            'arah_id' => 'nullable|numeric',
            'deskripsi' => 'nullable',
        ]);

        $request->validate([
            'photo' => 'image',
        ]);

        $data = Equipment::updateOrCreate($rawData, $rawData);

        // Update photo jika ada
        if ($request->hasFile('photo')) {
            $photoPath = $this->imageUploadService->uploadPhoto(
                $request->file('photo'),
                'photo/equipment/', // Path untuk photo
                300
            );

            // Hapus file lama
            if ($data->photo) {
                Storage::delete($data->photo);
            }

            // Update path photo di database
            $data->update(['photo' => $photoPath]);
        }

        return redirect()->route('equipment.index')->withNotify('Data berhasil ditambahkan');
    }

    public function import(Request $request)
    {
        $request->validate([
            'relasi_struktur_id' => 'required|numeric',
            'file' => 'required|file|mimes:xls,xlsx',
        ]);

        $relasi_struktur_id = $request->relasi_struktur_id;

        if($request->hasFile('file'))
        {
            $file = $request->file('file');
            Excel::import(new EquipmentImport($relasi_struktur_id), $file);
        }

        return redirect()->route('equipment.index')->withNotify('Data berhasil diimport');
    }

    public function edit(string $uuid)
    {
        $equipment = Equipment::where('uuid', $uuid)->firstOrFail();
        $tipe_equipment = TipeEquipment::all();
        $area = RelasiArea::all();
        $struktur = RelasiStruktur::all();
        $arah = Arah::all();
        $functional_location = FunctionalLocation::all();
        $equipments = Equipment::whereNot('id', $equipment->id)->get();

        return view('pages.admin.equipment.edit', compact([
            'equipment',
            'tipe_equipment',
            'area',
            'struktur',
            'arah',
            'functional_location',
            'equipments'
        ]));
    }

    public function update(Request $request)
    {
        $rawData = $request->validate([
            'id' => 'required|numeric',
            'name' => 'required',
            'code' => 'required',
            'equipment_number' => 'nullable|numeric',
            'tipe_equipment_id' => 'required|numeric',
            'relasi_area_id' => 'required|numeric',
            'relasi_struktur_id' => 'required|numeric',
            'functional_location_id' => 'required|numeric',
            'parent_id' => 'nullable|numeric',
            'arah_id' => 'nullable|numeric',
            'deskripsi' => 'nullable',
            'status' => 'required',
        ]);

        $request->validate([
            'photo' => 'nullable|image|file',
        ]);

        $data = Equipment::findOrFail($request->id);

        $data->update($rawData);

        // Update photo jika ada
        if ($request->hasFile('photo')) {
            $photoPath = $this->imageUploadService->uploadPhoto(
                $request->file('photo'),
                'photo/equipment/', // Path untuk photo
                300
            );

            // Hapus file lama
            if ($data->photo) {
                Storage::delete($data->photo);
            }

            // Update path photo di database
            $data->update(['photo' => $photoPath]);
        }

        return redirect()->route('equipment.index')->withNotify('Data berhasil diubah');
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'id' => 'required|numeric',
        ]);

        $data = Equipment::findOrFail($request->id);
        $data->delete();

        return redirect()->route('equipment.index')->withNotify('Data berhasil dihapus');
    }
}
