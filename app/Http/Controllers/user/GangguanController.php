<?php

namespace App\Http\Controllers\user;

use App\DataTables\GangguanDataTable;
use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\Category;
use App\Models\Classification;
use App\Models\Equipment;
use App\Models\Gangguan;
use App\Models\RelasiArea;
use App\Models\Status;
use App\Models\TipeEquipment;
use App\Models\TransaksiBarang;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;

class GangguanController extends Controller
{
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
        ]);

        $area_id = $request->area_id ?? null;
        $category_id = $request->category_id ?? null;
        $equipment_id = $request->equipment_id ?? null;
        $tipe_equipment_id = $request->tipe_equipment_id ?? null;
        $classification_id = $request->classification_id ?? null;
        $status_id = $request->status_id ?? null;
        $start_date = $request->start_date ?? null;
        $end_date = $request->end_date ?? $start_date;
        $is_changed = $request->is_changed ?? null;

        $equipment = Equipment::all();
        $barang = Barang::all();
        $status = Status::all();
        $category = Category::all();
        $classification = Classification::all();
        $tipe_equipment = TipeEquipment::all();
        $area = RelasiArea::where('lokasi_id', 2)->distinct('sub_lokasi_id')->get();


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
        ])->render('pages.user.gangguan.index', compact([
            'equipment',
            'tipe_equipment',
            'barang',
            'status',
            'category',
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
        ]));
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $raw_data = $request->validate([
            'equipment_id' => 'required|numeric',
            'report_date' => 'required|date',
            'report_by' => 'required|string',
            'problem' => 'required|string',
            'category_id' => 'required|numeric',
            'classification_id' => 'required|numeric',
            'action' => 'required|string',
            'response_date' => 'required|date',
            'solved_by' => 'required|string',
            'solved_date' => 'required|date',
            'analysis' => 'required|string',
            'status_id' => 'required|numeric',
            'is_changed' => 'boolean'
        ]);

        $request->validate([
            'photo' => 'file|image',
            'photo_after' => 'file|image',
            'barang_ids' => 'array',
            'qty' => 'array',
        ]);

        $data = Gangguan::create($raw_data);

        if ($request->hasFile('photo') && $request->photo != '') {
            $image = Image::make($request->file('photo'));

            $imageName = time().'-'.$request->file('photo')->getClientOriginalName();
            $detailPath = 'photo/gangguan/';
            $destinationPath = public_path('storage/'. $detailPath);

            $image->resize(null, 500, function ($constraint) {
                $constraint->aspectRatio();
            });

            $image->save($destinationPath.$imageName);

            $photo = $detailPath.$imageName;

            $data->update([
                "photo" => $photo,
            ]);
        }

        if ($request->hasFile('photo_after') && $request->photo != '') {
            $image = Image::make($request->file('photo_after'));

            $imageName = time().'-'.$request->file('photo_after')->getClientOriginalName();
            $detailPath = 'photo/gangguan/';
            $destinationPath = public_path('storage/'. $detailPath);

            $image->resize(null, 500, function ($constraint) {
                $constraint->aspectRatio();
            });

            $image->save($destinationPath.$imageName);

            $photo = $detailPath.$imageName;

            $data->update([
                "photo_after" => $photo,
            ]);
        }

        if($request->barang_ids != null)
        {
            foreach ($request->barang_ids as $i => $barang_id) {
                $qty = $request->qty[$i];

                TransaksiBarang::create([
                    'barang_id' => $barang_id,
                    'equipment_id' => $request->equipment_id,
                    'tanggal' => $request->report_date,
                    'qty' => $qty,
                    'gangguan_id' => $data->id,
                ]);
            }
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

        $equipment = Equipment::all();
        $status = Status::all();
        $category = Category::all();
        $classification = Classification::all();

        return view('pages.user.gangguan.edit', compact([
            'gangguan',
            'equipment',
            'status',
            'category',
            'classification',
        ]));
    }

    public function update(Request $request)
    {
        $raw_data = $request->validate([
            'equipment_id' => 'required|numeric',
            'report_date' => 'required|date',
            'report_by' => 'required|string',
            'problem' => 'required|string',
            'category_id' => 'required|numeric',
            'classification_id' => 'required|numeric',
            'action' => 'required|string',
            'response_date' => 'required|date',
            'solved_by' => 'required|string',
            'solved_date' => 'required|date',
            'analysis' => 'required|string',
            'status_id' => 'required|numeric',
            'is_changed' => 'boolean'
        ]);

        $request->validate([
            'photo' => 'file|image',
            'photo_after' => 'file|image',
            'id' => 'required|numeric'
        ]);

        $data = Gangguan::findOrFail($request->id);

        $data->update($raw_data);

        if ($request->hasFile('photo') && $request->photo != '') {
            $image = Image::make($request->file('photo'));

            $dataPhoto = $data->photo;
            if ($dataPhoto != null) {
                Storage::delete($dataPhoto);
            }

            $imageName = time().'-'.$request->file('photo')->getClientOriginalName();
            $detailPath = 'photo/gangguan/';
            $destinationPath = public_path('storage/'. $detailPath);

            $image->resize(null, 500, function ($constraint) {
                $constraint->aspectRatio();
            });

            $image->save($destinationPath.$imageName);

            $photo = $detailPath.$imageName;

            $data->update([
                "photo" => $photo,
            ]);
        }

        if ($request->hasFile('photo_after') && $request->photo != '') {
            $image = Image::make($request->file('photo_after'));

            $dataPhoto = $data->photo_after;
            if ($dataPhoto != null) {
                Storage::delete($dataPhoto);
            }

            $imageName = time().'-'.$request->file('photo_after')->getClientOriginalName();
            $detailPath = 'photo/gangguan/';
            $destinationPath = public_path('storage/'. $detailPath);

            $image->resize(null, 500, function ($constraint) {
                $constraint->aspectRatio();
            });

            $image->save($destinationPath.$imageName);

            $photo = $detailPath.$imageName;

            $data->update([
                "photo_after" => $photo,
            ]);
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
}
