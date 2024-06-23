<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Imports\BarangImport;
use App\Models\Barang;
use App\Models\RelasiArea;
use App\Models\RelasiStruktur;
use App\Models\Satuan;
use App\Models\TipeBarang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Maatwebsite\Excel\Facades\Excel;

class BarangController extends Controller
{
    public function index()
    {
        $barang = Barang::all();

        $area = RelasiArea::orderBy('lokasi_id', 'ASC')->orderBy('sub_lokasi_id', 'ASC')->orderBy('detail_lokasi_id', 'ASC')->get();
        $tipe_barang = TipeBarang::orderBy('name', 'ASC')->get();
        $satuan = Satuan::orderBy('name', 'ASC')->get();
        $struktur = RelasiStruktur::all();

        return view('pages.admin.barang.index', compact([
            'barang',
            'area',
            'tipe_barang',
            'satuan',
            'struktur',
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
            'material_number' => 'nullable|numeric',
            'serial_number' => 'nullable|numeric',
            'deskripsi' => 'nullable',
            'relasi_area_id' => 'required|numeric',
            'relasi_struktur_id' => 'required|numeric',
            'tipe_barang_id' => 'required|numeric',
            'satuan_id' => 'required|numeric',
            'photo' => 'image',
        ]);

        $barang = Barang::create([
            "name" => $request->name,
            "spesifikasi" => $request->spesifikasi,
            "material_number" => $request->material_number,
            "serial_number" => $request->serial_number,
            "tipe_barang_id" => $request->tipe_barang_id,
            "satuan_id" => $request->satuan_id,
            "relasi_area_id" => $request->relasi_area_id,
            "relasi_struktur_id" => $request->relasi_struktur_id,
            "deskripsi" => $request->deskripsi,
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

            $barang->update([
                "photo" => $photo,
            ]);
        }

        return redirect()->route('barang.index');
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
            Excel::import(new BarangImport($relasi_struktur_id), $file);
        }

        return redirect()->route('barang.index');
    }

    public function edit(string $uuid)
    {
        $barang = Barang::where('uuid', $uuid)->firstOrFail();

        $area = RelasiArea::orderBy('lokasi_id', 'ASC')->orderBy('sub_lokasi_id', 'ASC')->orderBy('detail_lokasi_id', 'ASC')->get();
        $tipe_barang = TipeBarang::orderBy('name', 'ASC')->get();
        $satuan = Satuan::orderBy('name', 'ASC')->get();
        $struktur = RelasiStruktur::all();

        return view('pages.admin.barang.edit', compact([
            'barang',
            'area',
            'tipe_barang',
            'satuan',
            'struktur',
        ]));
    }

    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required|numeric',
            'name' => 'required',
            'spesifikasi' => 'required',
            'material_number' => 'nullable|numeric',
            'serial_number' => 'nullable|numeric',
            'deskripsi' => 'nullable',
            'relasi_area_id' => 'required|numeric',
            'relasi_struktur_id' => 'required|numeric',
            'tipe_barang_id' => 'required|numeric',
            'satuan_id' => 'required|numeric',
            'photo' => 'image',
        ]);

        $barang = Barang::findOrFail($request->id);
        $barang->update([
            "name" => $request->name,
            "spesifikasi" => $request->spesifikasi,
            "material_number" => $request->material_number,
            "serial_number" => $request->serial_number,
            "tipe_barang_id" => $request->tipe_barang_id,
            "satuan_id" => $request->satuan_id,
            "relasi_area_id" => $request->relasi_area_id,
            "relasi_struktur_id" => $request->relasi_struktur_id,
            "deskripsi" => $request->deskripsi,
        ]);

        if ($request->hasFile('photo') && $request->photo != '') {
            $dataPhoto = $barang->photo;
            if ($dataPhoto != null) {
                Storage::delete($dataPhoto);
            }

            $image = Image::make($request->file('photo'));

            $imageName = time().'-'.$request->file('photo')->getClientOriginalName();
            $detailPath = 'photo/barang/';
            $destinationPath = public_path('storage/'. $detailPath);

            $image->resize(null, 500, function ($constraint) {
                $constraint->aspectRatio();
            });

            $image->save($destinationPath.$imageName);

            $photo = $detailPath.$imageName;

            $barang->update([
                'photo' => $photo
            ]);
        }

        return redirect()->route('barang.index');
    }

    public function destroy(string $id)
    {
        //
    }
}
