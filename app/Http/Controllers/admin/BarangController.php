<?php

namespace App\Http\Controllers\admin;

use App\DataTables\BarangDataTable;
use App\Http\Controllers\Controller;
use App\Imports\BarangImport;
use App\Models\Arah;
use App\Models\Barang;
use App\Models\RelasiArea;
use App\Models\RelasiStruktur;
use App\Models\Satuan;
use App\Models\TipeBarang;
use App\Services\ImageUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Maatwebsite\Excel\Facades\Excel;

class BarangController extends Controller
{
    protected $imageUploadService;

    public function __construct(ImageUploadService $imageUploadService)
    {
        $this->imageUploadService = $imageUploadService;
    }

    public function index(BarangDataTable $dataTable)
    {
        $tipe_barang = TipeBarang::all();
        $area = RelasiArea::all();
        $struktur = RelasiStruktur::all();
        $arah = Arah::all();
        $satuan = Satuan::all();

        return $dataTable->render('pages.admin.barang.index', compact([
            'tipe_barang',
            'area',
            'struktur',
            'arah',
            'satuan'
        ]));
    }

    public function store(Request $request)
    {
        $rawData = $request->validate([
            'name' => 'required',
            'spesifikasi' => 'required',
            'material_number' => 'nullable|numeric',
            'serial_number' => 'nullable|numeric',
            'deskripsi' => 'nullable',
            'relasi_area_id' => 'required|numeric',
            'relasi_struktur_id' => 'required|numeric',
            'tipe_barang_id' => 'required|numeric',
            'satuan_id' => 'required|numeric',
        ]);

        $request->validate([
            'photo' => 'nullable|image|file',
        ]);

        $data = Barang::updateOrCreate($rawData, $rawData);

        // Update photo jika ada
        if ($request->hasFile('photo')) {
            $photoPath = $this->imageUploadService->uploadPhoto(
                $request->file('photo'),
                'photo/barang/', // Path untuk photo
                300
            );

            // Hapus file lama
            if ($data->photo) {
                Storage::delete($data->photo);
            }

            // Update path photo di database
            $data->update(['photo' => $photoPath]);
        }

        return redirect()->route('barang.index')->withNotify('Data berhasil ditambahkan');
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
            Excel::import(new BarangImport($relasi_struktur_id), $file);
        }

        return redirect()->route('barang.index')->withNotify('Data berhasil diimport');
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
        $rawData = $request->validate([
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
        ]);

        $request->validate([
            'photo' => 'nullable|file|image',
        ]);

        $data = Barang::findOrFail($request->id);
        $data->update($rawData);

        // Update photo jika ada
        if ($request->hasFile('photo')) {
            $photoPath = $this->imageUploadService->uploadPhoto(
                $request->file('photo'),
                'photo/barang/', // Path untuk photo
                300
            );

            // Hapus file lama
            if ($data->photo) {
                Storage::delete($data->photo);
            }

            // Update path photo di database
            $data->update(['photo' => $photoPath]);
        }

        return redirect()->route('barang.index')->withNotify('Data berhasil diubah');
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'id' => 'required|numeric',
        ]);

        $data = Barang::findOrFail($request->id);
        $data->delete();

        return redirect()->route('barang.index')->withNotify('Data berhasil dihapus');
    }
}
