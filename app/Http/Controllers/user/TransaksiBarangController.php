<?php

namespace App\Http\Controllers\user;

use App\DataTables\TransaksiBarangDataTable;
use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\Equipment;
use App\Models\RelasiArea;
use App\Models\TipeBarang;
use App\Models\TipeEquipment;
use App\Models\TransaksiBarang;
use Illuminate\Http\Request;

class TransaksiBarangController extends Controller
{
    // public function index()
    // {
    //     $transaksi_barang = TransaksiBarang::orderBy('tanggal', 'DESC')->get();
    //     $barang = Barang::all();
    //     $equipment = Equipment::all();

    //     return view('pages.user.transaksi-barang.index', compact([
    //         'transaksi_barang',
    //         'barang',
    //         'equipment',
    //     ]));
    // }

    public function index(TransaksiBarangDataTable $dataTable)
    {
        $barang = Barang::all();
        $equipment = Equipment::all();

        return $dataTable->render('pages.user.transaksi-barang.index', compact([
            'barang',
            'equipment'
        ]));
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'barang_id' => 'required|numeric',
            'equipment_id' => 'required|numeric',
            'tanggal' => 'required|date',
            'qty' => 'required|numeric'
        ]);

        TransaksiBarang::create($data);

        return redirect()->route('transaksi-barang.index')->withNotify('Data berhasil ditambahkan');
    }

    public function show(string $id)
    {
        //
    }

    public function edit($uuid)
    {
        $transaksi_barang = TransaksiBarang::where('uuid', $uuid)->firstOrFail();
        $barang = Barang::all();
        $equipment = Equipment::all();

        return view('pages.user.transaksi-barang.edit', compact([
            'transaksi_barang',
            'barang',
            'equipment'
        ]));
    }

    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required|numeric',
            'barang_id' => 'required|numeric',
            'equipment_id' => 'required|numeric',
            'tanggal' => 'required|date',
            'qty' => 'required|numeric'
        ]);

        $data = TransaksiBarang::findOrFail($request->id);
        $data->update([
            'barang_id' => $request->barang_id,
            'equipment_id' => $request->equipment_id,
            'tanggal' => $request->tanggal,
            'qty' => $request->qty,
            'user_id' => auth()->user()->id,
        ]);

        return redirect()->route('transaksi-barang.index')->withNotify('Data berhasil diubah');
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'id' => 'required|numeric',
        ]);

        $data = TransaksiBarang::findOrFail($request->id);
        $data->delete();

        return redirect()->route('transaksi-barang.index')->withNotify('Data berhasil dihapus!');
    }
}
