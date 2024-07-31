<?php

namespace App\Http\Controllers\user;

use App\DataTables\SamCardHistoryDataTable;
use App\Http\Controllers\Controller;
use App\Models\Equipment;
use App\Models\RelasiArea;
use App\Models\SamCard;
use App\Models\SamCardHistory;
use Illuminate\Http\Request;

class SamCardHistoryController extends Controller
{
    // public function index()
    // {
    //     $sam_card = SamCard::where('status', 'ready')->where('mc', '!=', null)->orderBy('created_at', 'DESC')->get();
    //     $area = RelasiArea::where('lokasi_id', 2)->distinct('sub_lokasi_id')->get();

    //     $sam_card_history = SamCardHistory::all();

    //     return view('pages.user.sam-card-history.index', compact([
    //         'sam_card',
    //         'area',
    //         'sam_card_history',
    //     ]));
    // }

    public function index(SamCardHistoryDataTable $dataTable)
    {
        $sam_card = SamCard::where('mc', '!=', null)->orderBy('created_at', 'DESC')->get();
        $area = RelasiArea::where('lokasi_id', 2)->distinct('sub_lokasi_id')->get();
        $pg = Equipment::where('tipe_equipment_id', 1)->get();

        return $dataTable->render('pages.user.sam-card-history.index', compact([
            'sam_card',
            'area',
            'pg'
        ]));
    }

    public function create($uuid)
    {
        $sam_card = SamCard::where('uuid', $uuid)->firstOrFail();
        $area = RelasiArea::where('lokasi_id', 2)->distinct('sub_lokasi_id')->get();
        $pg = Equipment::where('tipe_equipment_id', 1)->get();

        return view('pages.user.sam-card-history.create', compact([
            'sam_card',
            'area',
            'pg'
        ]));
    }

    public function store(Request $request)
    {
        // dd($request);
        $request->validate([
            "sam_card_id" => 'required|numeric',
            'equipment_id' => 'required|numeric',
            "type" => 'required',
            "tanggal" => 'required|date'
        ]);

        SamCardHistory::create([
            "sam_card_id" => $request->sam_card_id,
            "equipment_id" => $request->equipment_id,
            "type" => $request->type,
            "tanggal" => $request->tanggal,
        ]);

        $sam_card = SamCard::findOrFail($request->sam_card_id);
        $sam_card->update([
            'status' => 'used'
        ]);

        return redirect()->route('sam-history.index')->withNotify('Data berhasil ditambahkan');
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
