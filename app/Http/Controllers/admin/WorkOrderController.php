<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\Classification;
use App\Models\Equipment;
use App\Models\Gangguan;
use App\Models\RelasiArea;
use App\Models\RelasiStruktur;
use App\Models\Status;
use App\Models\TipePekerjaan;
use App\Models\TransaksiBarang;
use App\Models\TransWorkOrderEquipment;
use App\Models\TransWorkOrderTasklist;
use App\Models\TransWorkOrderUser;
use App\Models\User;
use App\Models\WorkOrder;
use Illuminate\Http\Request;

class WorkOrderController extends Controller
{
    public function index()
    {
        $work_order = WorkOrder::all();

        return view('pages.user.work-order.index', compact([
            'work_order'
        ]));
    }

    public function create()
    {
        $tipe_pekerjaan = TipePekerjaan::all();
        $relasi_area = RelasiArea::where('lokasi_id', 2)->distinct('sub_lokasi_id')->get();
        $relasi_struktur = RelasiStruktur::distinct('departemen_id')->get();
        $classification = Classification::all();
        $status = Status::all();

        return view('pages.user.work-order.create', compact([
            'tipe_pekerjaan',
            'relasi_area',
            'relasi_struktur',
            'classification',
            'status',
        ]));

    }

    public function create_from_gangguan(string $uuid)
    {
        $gangguan = Gangguan::where('uuid', $uuid)->firstOrFail();

        if($gangguan->work_order_id)
        {
            $work_order = WorkOrder::findOrFail($gangguan->work_order_id);
            return redirect()->route('work-order.detail', $work_order->uuid);
        }

        $tipe_pekerjaan = TipePekerjaan::all();
        $status = Status::where('id', 1)->get();
        $user = User::where('relasi_struktur_id', auth()->user()->relasi_struktur_id)->get();
        $classification = Classification::all();

        $barang = Barang::all();

        return view('pages.user.work-order.create-form-gangguan', compact([
            'gangguan',
            'tipe_pekerjaan',
            'status',
            'user',
            'classification',
            'barang',
        ]));
    }

    public function store(Request $request)
    {
        $rawData = $request->validate([
            "tipe_pekerjaan_id" => "required|numeric",
            "wo_number_sap" => "nullable|numeric",
            "name" => "string|required",
            "description" => "string|required",
            "date" => "date|required",
            "relasi_area_id" => "required|numeric",
            "relasi_struktur_id" => "required|numeric",
            "classification_id" => "required|numeric",
        ]);

        $request->validate([
            'equipment_ids' => 'required|array',
            'equipment_ids.*' => 'numeric',
        ]);

        $rawData['status_id'] = 1; //status OPEN
        $rawData['user_id'] = auth()->user()->id;

        $data = WorkOrder::create($rawData);

        foreach($request->equipment_ids as $equipment_id)
        {
            TransWorkOrderEquipment::create([
                'work_order_id' => $data->id,
                'equipment_id' => $equipment_id,
            ]);
        }

        return redirect()->route('work-order.index')->withNotify('Data berhasil ditambahkan');
    }

    public function store_from_gangguan($uuid, Request $request)
    {
        $gangguan = Gangguan::where('uuid', $uuid)->firstOrfail();

        $rawData = $request->validate([
            "tipe_pekerjaan_id" => "required|numeric",
            "wo_number_sap" => "nullable|numeric",
            "name" => "string|required",
            "description" => "string|required",
            "date" => "date|required",
            "classification_id" => "required|numeric",
            "status_id" => "required|numeric",
        ]);

        $request->validate([
            'tasklist' => 'required|array',
            'tasklist.*' => 'required|string',
            'duration' => 'nullable|array',
            'duration.*' => 'nullable|numeric',
            'reference' => 'nullable|array',
            'reference.*' => 'nullable|string',
            'user_ids' => 'nullable|array',
            'user_ids.*' => 'nullable|numeric',
            'barang_ids' => 'nullable|array',
            'barang_ids.*' => 'nullable|numeric',
            'qty' => 'nullable|array',
            'qty.*' => 'nullable|numeric',
        ]);

        $tasklist = $request->tasklist;
        $duration = $request->duration;
        $reference = $request->reference;
        $user_ids = $request->user_ids;
        $barang_ids = $request->barang_ids;
        $qty = $request->qty;

        $rawData['relasi_area_id'] = $gangguan->equipment->relasi_area_id;
        $rawData['relasi_struktur_id'] = $gangguan->equipment->relasi_struktur_id;
        $rawData['user_id'] = auth()->user()->id;

        $work_order = WorkOrder::create($rawData);

        // Trans Equipment
        TransWorkOrderEquipment::create([
            'work_order_id' => $work_order->id,
            'equipment_id' => $gangguan->equipment->id,
        ]);

        // Trans Tasklist
        if($tasklist != null)
        {
            foreach($tasklist as $index => $name)
            {
                TransWorkOrderTasklist::create([
                    'work_order_id' => $work_order->id,
                    'name' => $name,
                    'duration' => $duration[$index],
                    'reference' => $reference[$index],
                ]);
            }
        }

        // Trans Sparepart
        if($barang_ids != null)
        {
            foreach ($barang_ids as $index => $barang_id) {
                TransaksiBarang::create([
                    'work_order_id' => $work_order->id,
                    'tanggal' => $work_order->date,
                    'equipment_id' => $gangguan->equipment_id,
                    'gangguan_id' => $gangguan->id,
                    'barang_id' => $barang_id,
                    'qty' => $qty[$index],
                    'user_id' => auth()->user()->id,
                ]);
            }
        }

        // Trans User
        if($user_ids != null)
        {
            foreach ($user_ids as $user_id) {
                TransWorkOrderUser::create([
                    'work_order_id' => $work_order->id,
                    'user_id' => $user_id,
                ]);
            }
        }

        // Update Gangguan
        $gangguan->update([
            'work_order_id' => $work_order->id
        ]);

        return redirect()->route('work-order.index')->withNotify('Data Work Order ' . $work_order->ticket_number .' berhasil ditambahkan');
    }

    public function equipment($uuid)
    {
        $work_order = WorkOrder::where('uuid', $uuid)->firstOrFail();
        $trans_wo_equipment = TransWorkOrderEquipment::where('work_order_id', $work_order->id)->get();

        return view('pages.user.work-order.equipment', compact([
            'work_order',
            'trans_wo_equipment',
        ]));
    }

    public function detail($uuid)
    {
        $work_order = WorkOrder::where('uuid', $uuid)->firstOrFail();
        $user = User::where('relasi_struktur_id', $work_order->relasi_struktur_id)->get();
        $status = Status::all();

        return view('pages.user.work-order.detail', compact([
            'work_order',
            'user',
            'status',
        ]));
    }

    public function edit(string $uuid)
    {
        $work_order = WorkOrder::where('uuid', $uuid)->firstOrFail();

        $tipe_pekerjaan = TipePekerjaan::all();
        $status = Status::all();
        $user = User::where('relasi_struktur_id', auth()->user()->relasi_struktur_id)->get();
        $classification = Classification::all();

        $equipment_ids = $work_order->trans_workorder_equipment->pluck('equipment_id')->toArray();
        $equipment = Equipment::where('relasi_struktur_id', $work_order->relasi_struktur_id)
                                ->whereNotIn('id', $equipment_ids)
                                ->get();

        $user_ids = $work_order->trans_workorder_user->pluck('user_id')->toArray();
        $user = User::whereNotIn('id', $user_ids)->get();

        $barang_ids = $work_order->transaksi_barang->pluck('barang_id')->toArray();
        $barang = Barang::whereNotIn('id', $barang_ids)->get();

        return view('pages.user.work-order.edit-form-gangguan', compact([
            'work_order',
            'tipe_pekerjaan',
            'status',
            'user',
            'classification',
            'barang',
            'equipment',
            'user',
        ]));
    }

    public function update(Request $request)
    {
        $request->validate([
            "uuid" => 'required|string'
        ]);

        $rawData = $request->validate([
            "date" => 'required|date',
            "name" => 'required|string',
            "description" => 'required|string',
            "tipe_pekerjaan_id" => 'required|numeric|min:1',
            "classification_id" => 'required|numeric|min:1',
            "status_id" => 'required|numeric|min:1',
            "wo_number_sap" => 'nullable|string',
            "start_time" => 'required|date',
            "end_time" => 'required|date',
            "note" => 'nullable|string',
        ]);

        $data = WorkOrder::where('uuid', $request->uuid)->firstOrFail();

        $data->update($rawData);

        return redirect()->route('work-order.index')->withNotify('Data Work Order berhasil diubah');
    }

    public function destroy(Request $request)
    {
        //
    }
}
