<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\RelasiArea;
use App\Models\Satuan;
use App\Models\TipeBarang;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;

class BarangController extends Controller
{
    public function index()
    {
        $barang = Barang::all();

        $area = RelasiArea::orderBy('lokasi_id', 'ASC')->orderBy('sub_lokasi_id', 'ASC')->orderBy('detail_lokasi_id', 'ASC')->get();
        $tipe_barang = TipeBarang::orderBy('name', 'ASC')->get();
        $satuan = Satuan::orderBy('name', 'ASC')->get();

        return view('pages.admin.barang.index', compact([
            'barang',
            'area',
            'tipe_barang',
            'satuan',
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
            'spesifikasi' => 'required',
            'material_number' => 'nullable',
            'serial_number' => 'nullable',
            'deskripsi' => 'nullable',
            'relasi_area_id' => 'required',
            'tipe_barang_id' => 'required',
            'satuan_id' => 'required',
            'photo' => 'required|image',
        ]);

        if ($request->hasFile('photo') && $request->photo != '') {
            $image = Image::make($request->file('photo'));

            $imageName = time().'-'.$request->file('photo')->getClientOriginalName();
            $detailPath = 'photo/barang/';
            $destinationPath = public_path('storage/'. $detailPath);

            $image->resize(null, 500, function ($constraint) {
                $constraint->aspectRatio();
            });

            $image->save($destinationPath.$imageName);

            $photo = $detailPath.$imageName;

            Barang::create([
                "name" => $request->name,
                "spesifikasi" => $request->spesifikasi,
                "material_number" => $request->material_number,
                "serial_number" => $request->serial_number,
                "tipe_barang_id" => $request->tipe_barang_id,
                "satuan_id" => $request->satuan_id,
                "relasi_area_id" => $request->relasi_area_id,
                "deskripsi" => $request->deskripsi,
                "photo" => $photo,
            ]);
        }

        return redirect()->route('barang.index');
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
