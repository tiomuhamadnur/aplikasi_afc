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
use Carbon\Carbon;
use Illuminate\Http\Request;

class TransaksiBarangController extends Controller
{
    public function index(TransaksiBarangDataTable $dataTable, Request $request)
    {
        $request->validate([
            'start_date' => 'nullable',
            'end_date' => 'nullable',
            'tipe_equipment_id' => 'nullable',
        ]);

        $start_date = $request->start_date ?? Carbon::now()->format('Y-m-d');
        $end_date = $request->end_date ?? $start_date;
        $tipe_equipment_id = $request->tipe_equipment_id ?? null;

        $barang = Barang::all();
        $equipment = Equipment::all();
        $tipe_equipment = TipeEquipment::all();

        return $dataTable->with([
            'start_date' => $start_date,
            'end_date' => $end_date,
            'tipe_equipment_id' => $tipe_equipment_id,
        ])->render('pages.user.transaksi-barang.index', compact([
            'barang',
            'equipment',
            'tipe_equipment',
            'start_date',
            'end_date',
            'tipe_equipment_id',
        ]));
    }

    public function trend_monthly(Request $request)
    {
        $request->validate([
            'y' => ['required', 'integer', 'digits:4', 'min:1900'],
            'm' => ['required', 'integer', 'min:1', 'max:12'],
        ]);

        $tahun = $request->y;
        $bulan = $request->m;
        $bulan_name = Carbon::create()->month($bulan)->format('F');

        $tanggal = [];

        $startDate = Carbon::createFromDate($tahun, $bulan, 1);
        $endDate = $startDate->copy()->endOfMonth();

        for ($date = $startDate; $date->lte($endDate); $date->addDay()) {
            $tanggal[] = $date->format('Y-m-d');
        }

        $sparepart = [];
        foreach($tanggal as $i)
        {
            $sparepart[] = TransaksiBarang::whereDate('tanggal', $i)->count();
        }

        $data = [];
        foreach ($tanggal as $i => $tgl) {
            $data[] = [
                'tanggal' => $tgl,
                'sparepart' => $sparepart[$i],
                'url' => route('transaksi-barang.index', ['start_date' => $tgl, 'end_date' => $tgl])
            ];
        }

        return view('pages.user.transaksi-barang.chart.daily', compact([
            'tahun',
            'bulan',
            'bulan_name',
            'data'
        ]));
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
