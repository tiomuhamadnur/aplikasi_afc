<?php

namespace App\Http\Controllers\user;

use App\DataTables\GangguanDataTable;
use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\Equipment;
use App\Models\Gangguan;
use App\Models\TransaksiBarang;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;

class GangguanController extends Controller
{
    // public function index()
    // {
    //     $gangguan = Gangguan::all();
    //     $equipment = Equipment::all();
    //     return view('pages.user.gangguan.index', compact([
    //         'gangguan',
    //         'equipment'
    //     ]));
    // }

    public function index(GangguanDataTable $dataTable)
    {
        $equipment = Equipment::all();
        $barang = Barang::all();

        return $dataTable->render('pages.user.gangguan.index', compact([
            'equipment',
            'barang'
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
            'category' => 'required|string',
            'classification' => 'required|string',
            'action' => 'required|string',
            'response_date' => 'required|date',
            'solved_by' => 'required|string',
            'solved_date' => 'required|date',
            'analysis' => 'required|string',
            'status' => 'required|string',
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
        return view('pages.user.gangguan.edit', compact([
            'gangguan',
            'equipment'
        ]));
    }

    public function update(Request $request)
    {
        $raw_data = $request->validate([
            'equipment_id' => 'required|numeric',
            'report_date' => 'required|date',
            'report_by' => 'required|string',
            'problem' => 'required|string',
            'category' => 'required|string',
            'classification' => 'required|string',
            'action' => 'required|string',
            'response_date' => 'required|date',
            'solved_by' => 'required|string',
            'solved_date' => 'required|date',
            'analysis' => 'required|string',
            'status' => 'required|string',
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
