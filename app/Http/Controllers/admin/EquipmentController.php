<?php

namespace App\Http\Controllers\admin;

use App\DataTables\EquipmentDataTable;
use App\Http\Controllers\Controller;
use App\Imports\EquipmentImport;
use App\Models\Arah;
use App\Models\Equipment;
use App\Models\RelasiArea;
use App\Models\RelasiStruktur;
use App\Models\TipeEquipment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Maatwebsite\Excel\Facades\Excel;

class EquipmentController extends Controller
{
    // public function index()
    // {
    //     $equipment = Equipment::all();
    //     $tipe_equipment = TipeEquipment::all();
    //     $area = RelasiArea::all();
    //     $struktur = RelasiStruktur::all();
    //     $arah = Arah::all();

    //     return view('pages.admin.equipment.index', compact([
    //         'equipment',
    //         'tipe_equipment',
    //         'area',
    //         'struktur',
    //         'arah',
    //     ]));
    // }

    public function index(EquipmentDataTable $dataTable)
    {
        $tipe_equipment = TipeEquipment::all();
        $area = RelasiArea::all();
        $struktur = RelasiStruktur::all();
        $arah = Arah::all();

        return $dataTable->render('pages.admin.equipment.index', compact([
            'tipe_equipment',
            'area',
            'struktur',
            'arah',
        ]));
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'code' => 'required',
            'equipment_number' => 'nullable|numeric',
            'tipe_equipment_id' => 'required|numeric',
            'relasi_area_id' => 'required|numeric',
            'relasi_struktur_id' => 'required|numeric',
            'arah_id' => 'required|numeric',
            'photo' => 'image',
            'deskripsi' => 'nullable',
        ]);


        $equipment = Equipment::create([
            "name" => $request->name,
            "code" => $request->code,
            "equipment_number" => $request->equipment_number,
            "tipe_equipment_id" => $request->tipe_equipment_id,
            "relasi_area_id" => $request->relasi_area_id,
            "relasi_struktur_id" => $request->relasi_struktur_id,
            "arah_id" => $request->arah_id,
            "deskripsi" => $request->deskripsi,
        ]);

        if ($request->hasFile('photo') && $request->photo != '') {
            $image = Image::make($request->file('photo'));

            $imageName = time().'-'.$request->file('photo')->getClientOriginalName();
            $detailPath = 'photo/equipment/';
            $destinationPath = public_path('storage/'. $detailPath);

            $image->resize(null, 500, function ($constraint) {
                $constraint->aspectRatio();
            });

            $image->save($destinationPath.$imageName);

            $photo = $detailPath.$imageName;

            $equipment->update([
                'photo' => $photo
            ]);
        }

        return redirect()->route('equipment.index')->withNotify('Data berhasil ditambahkan');
    }

    public function import(Request $request)
    {
        // dd($request);
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

        return view('pages.admin.equipment.edit', compact([
            'equipment',
            'tipe_equipment',
            'area',
            'struktur',
            'arah',
        ]));
    }

    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required|numeric',
            'name' => 'required',
            'code' => 'required',
            'equipment_number' => 'nullable|numeric',
            'tipe_equipment_id' => 'required|numeric',
            'relasi_area_id' => 'required|numeric',
            'relasi_struktur_id' => 'required|numeric',
            'arah_id' => 'required|numeric',
            'photo' => 'image',
            'deskripsi' => 'nullable',
            'status' => 'required',
        ]);

        $equipment = Equipment::findOrFail($request->id);

        $equipment->update([
            "name" => $request->name,
            "code" => $request->code,
            "equipment_number" => $request->equipment_number,
            "tipe_equipment_id" => $request->tipe_equipment_id,
            "relasi_area_id" => $request->relasi_area_id,
            "relasi_struktur_id" => $request->relasi_struktur_id,
            "arah_id" => $request->arah_id,
            "deskripsi" => $request->deskripsi,
            "status" => $request->status,
        ]);

        if ($request->hasFile('photo') && $request->photo != '') {
            $dataPhoto = $equipment->photo;
            if ($dataPhoto != null) {
                Storage::delete($dataPhoto);
            }

            $image = Image::make($request->file('photo'));

            $imageName = time().'-'.$request->file('photo')->getClientOriginalName();
            $detailPath = 'photo/equipment/';
            $destinationPath = public_path('storage/'. $detailPath);

            $image->resize(null, 500, function ($constraint) {
                $constraint->aspectRatio();
            });

            $image->save($destinationPath.$imageName);

            $photo = $detailPath.$imageName;

            $equipment->update([
                'photo' => $photo
            ]);
        }

        return redirect()->route('equipment.index');
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'id' => 'required',
        ]);

        $data = Equipment::findOrFail($request->id);
        $data->delete();

        return redirect()->route('equipment.index')->withNotify('Data berhasil dihapus');
    }
}
