<?php

namespace App\Http\Controllers\admin;

use App\DataTables\WorkOrderDataTable;
use App\Http\Controllers\Controller;
use App\Models\Approval;
use App\Models\Barang;
use App\Models\Classification;
use App\Models\Equipment;
use App\Models\FunctionalLocation;
use App\Models\Gangguan;
use App\Models\RelasiArea;
use App\Models\RelasiStruktur;
use App\Models\Status;
use App\Models\TipePekerjaan;
use App\Models\TransaksiBarang;
use App\Models\TransWorkOrderApproval;
use App\Models\TransWorkOrderEquipment;
use App\Models\TransWorkOrderFunctionalLocation;
use App\Models\TransWorkOrderTasklist;
use App\Models\TransWorkOrderUser;
use App\Models\User;
use App\Models\WorkOrder;
use Carbon\Carbon;
use Illuminate\Http\Request;

class WorkOrderController extends Controller
{
    public function index(WorkOrderDataTable $dataTable, Request $request)
    {
        return $dataTable->render('pages.user.work-order.index');
    }

    public function create()
    {
        $tipe_pekerjaan = TipePekerjaan::all();
        $status = Status::where('id', 1)->get();
        $user = User::where('relasi_struktur_id', auth()->user()->relasi_struktur_id)->get();
        $classification = Classification::all();

        $barang = Barang::all();

        return view('pages.user.work-order.create', compact([
            'tipe_pekerjaan',
            'status',
            'user',
            'classification',
            'barang',
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
            // "relasi_area_id" => "required|numeric",
            // "relasi_struktur_id" => "required|numeric",
            "classification_id" => "required|numeric",
            "status_id" => "required|numeric",
        ]);

        $request->validate([
            "object_order_type" => 'required|string',
            "objectOrderValue" => 'required|array',
            "objectOrderValue.*" => 'required|string',
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

        $object_order_type = $request->object_order_type;
        $objectOrderValue = $request->objectOrderValue;
        $tasklist = $request->tasklist;
        $duration = $request->duration;
        $reference = $request->reference;
        $user_ids = $request->user_ids;
        $barang_ids = $request->barang_ids;
        $qty = $request->qty;
        $relasi_struktur_id = auth()->user()->relasi_struktur->id;

        $rawData['user_id'] = auth()->user()->id;
        $rawData['relasi_struktur_id'] = $relasi_struktur_id;

        $work_order = WorkOrder::create($rawData);

        // Trans Equipment or Functional Location
        if($object_order_type != null)
        {
            if($object_order_type == 'equipment')
            {
                foreach($objectOrderValue as $item)
                {
                    // Hanya proses jika mengandung 'equipment_id_'
                    if (strpos($item, 'equipment_id_') !== false)
                    {
                        $equipment_id = str_replace('equipment_id_', '', $item);
                        $data = [
                            'work_order_id' => $work_order->id,
                            'equipment_id' => (int)$equipment_id,
                        ];
                        TransWorkOrderEquipment::updateOrCreate($data, $data);
                    }
                }
            }
            elseif($object_order_type == 'functional_location')
            {
                foreach($objectOrderValue as $item)
                {
                    // Hanya proses jika mengandung 'location_id_'
                    if (strpos($item, 'location_id_') !== false)
                    {
                        $functional_location_id = str_replace('location_id_', '', $item);
                        $data = [
                            'work_order_id' => $work_order->id,
                            'functional_location_id' => (int)$functional_location_id,
                        ];
                        TransWorkOrderFunctionalLocation::updateOrCreate($data, $data);
                    }
                }
            }
        }

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
                $data = [
                    'work_order_id' => $work_order->id,
                    'tanggal' => $work_order->date,
                    // 'equipment_id' => $gangguan->equipment_id,
                    // 'gangguan_id' => $gangguan->id,
                    'barang_id' => $barang_id,
                    'qty' => $qty[$index],
                    'user_id' => auth()->user()->id,
                ];
                TransaksiBarang::updateOrCreate($data,$data);
            }
        }

        // Trans User
        if($user_ids != null)
        {
            foreach ($user_ids as $user_id) {
                $data = [
                    'work_order_id' => $work_order->id,
                    'user_id' => $user_id,
                ];
                TransWorkOrderUser::updateOrCreate($data, $data);
            }
        }

        // Trans Approval
        if($relasi_struktur_id != null)
        {
            $approval = Approval::where('relasi_struktur_id', $relasi_struktur_id)->orderBy('priority', 'ASC')->get();
            foreach ($approval as $item) {
                $data = [
                    'work_order_id' => $work_order->id,
                    'approval_id' => $item->id,
                ];
                TransWorkOrderApproval::updateOrCreate($data, $data);
            }
        }

        return redirect()->route('work-order.index')->withNotify('Work Order '. $work_order->ticket_number .' berhasil ditambahkan');
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

        return redirect()->route('work-order.index')->withNotify('Work Order ' . $work_order->ticket_number .' berhasil ditambahkan');
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
        $user = User::where('relasi_struktur_id', $work_order->relasi_struktur_id)
                    ->whereNotIn('id', $work_order->trans_workorder_user->pluck('user_id')->toArray())
                    ->get();
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

        $functional_location_ids = $work_order->trans_workorder_functional_location->pluck('functional_location_id')->toArray();
        $functional_location = FunctionalLocation::whereNotIn('id', $functional_location_ids)->get();

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
            'functional_location',
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

    public function update_note(string $uuid_workorder, Request $request)
    {
        $request->validate([
            'note' => 'required|string',
        ]);

        $work_order = WorkOrder::where('uuid', $uuid_workorder)->firstOrFail();
        $work_order->update([
            'note' => $request->note
        ]);

        return redirect()->back()->withNotify('Data Note berhasil ditambahkan');
    }

    public function update_time(string $uuid_workorder, Request $request)
    {
        $request->validate([
            'start_time' => 'required|date',
            'end_time' => 'required|date',
        ]);

        $work_order = WorkOrder::where('uuid', $uuid_workorder)->firstOrFail();
        $work_order->update([
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
        ]);

        return redirect()->back()->withNotify('Data Job Time berhasil ditambahkan');
    }

    public function approve(string $uuid_workorder)
    {
        $work_order = WorkOrder::where('uuid', $uuid_workorder)->firstOrFail();
        $trans_workorder_approvals = $work_order->trans_workorder_approval;

        if ($trans_workorder_approvals->count() == 0) {
            return redirect()->back()->withNotifyerror('Approval data untuk Work Order ini belum tersedia. Silakan hubungi admin.');
        }

        // Cek essentials data
        $message = $this->checkEssentialData($work_order->id);
        if($message != null)
        {
            return redirect()->back()->withNotifyerror($message);
        }

        $firstItem = true; // Menandai item pertama
        $previousApproved = true; // Inisialisasi flag untuk approval sebelumnya

        foreach ($trans_workorder_approvals as $item) {
            // Jika ini bukan item pertama dan approval sebelumnya belum diselesaikan, hentikan proses
            if (!$firstItem && !$previousApproved) {
                return redirect()->back()->withNotifyerror('Proses approval sebelumnya belum selesai.');
            }

            // Cek role approval untuk user saat ini
            $statusRole = $this->checkRoleApproval($item->id);

            // Hanya approve jika status belum 'approved' dan role valid
            if ($item->status != 'approved' && $statusRole == 'ok') {
                $item->update([
                    'user_id' => auth()->user()->id,
                    'status' => 'approved',
                    'date' => Carbon::now(),
                ]);

                $statusWO = $this->checkClosedWorkOrder($work_order->id);
                $message = '';
                if($statusWO == 'closed')
                {
                    $message = 'dan sudah berstatus Closed';
                }

                return redirect()->back()->withNotify('Work Order berhasil di-approve ' . $message);
            }

            // Update flag untuk cek approval selanjutnya
            $previousApproved = ($item->status == 'approved');
            $firstItem = false; // Set flag bahwa ini bukan lagi item pertama
        }
    }


    private function checkRoleApproval($trans_workorder_approval_id)
    {
        $trans_workorder_approval = TransWorkOrderApproval::findOrFail($trans_workorder_approval_id);
        $user = auth()->user();

        if (
            $trans_workorder_approval->approval->relasi_struktur_id != $user->relasi_struktur_id ||
            $trans_workorder_approval->approval->jabatan_id != $user->jabatan_id ||
            $trans_workorder_approval->approval->tipe_employee_id != $user->tipe_employee_id
        ) {
            return "fail";
        }

        return "ok";
    }

    private function checkClosedWorkOrder($work_order_id)
    {
        $work_order = WorkOrder::findOrFail($work_order_id);
        $count_all_approval = $work_order->trans_workorder_approval->count();
        $count_approved_approval = $work_order->trans_workorder_approval->where('status', 'approved')->count();

        if($count_all_approval == $count_approved_approval)
        {
            $work_order->update([
                'status_id' => 2
            ]);

            return 'closed';
        }

        return 'open';
    }

    private function checkEssentialData($work_order_id)
    {
        $work_order = WorkOrder::findOrFail($work_order_id);

        // Cek durasi aktual tasklist
        $tasklist = $work_order->trans_workorder_tasklist->where('actual_duration', null)->count();

        // Cek man power
        $man_power = $work_order->trans_workorder_user->count();

        // Cek dokumentasi
        $dokumentasi = $work_order->trans_workorder_photo->count();

        // Cek job time
        $job_time = $work_order->orWhere('start_time', null)->orWhere('end_time', null)->count();


        if($tasklist > 0)
        {
            return 'Data Actual Duration Tasklist belum diisi';
        }

        if($man_power == 0)
        {
            return 'Data Man Power belum diisi';
        }

        if($dokumentasi == 0)
        {
            return 'Data Dokumentasi belum diisi';
        }

        if($job_time > 0)
        {
            return 'Data Dokumentasi belum diisi';
        }

        return null;
    }

    public function destroy(Request $request)
    {
        //
    }
}
