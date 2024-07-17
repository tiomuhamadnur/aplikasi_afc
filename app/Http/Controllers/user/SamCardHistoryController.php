<?php

namespace App\Http\Controllers\user;

use App\DataTables\SamCardHistoryDataTable;
use App\Http\Controllers\Controller;
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

        return $dataTable->render('pages.user.sam-card-history.index', compact([
            'sam_card',
            'area'
        ]));
    }

    public function create($uuid)
    {
        $sam_card = SamCard::where('uuid', $uuid)->firstOrFail();
        $area = RelasiArea::where('lokasi_id', 2)->distinct('sub_lokasi_id')->get();

        return view('pages.user.sam-card-history.create', compact([
            'sam_card',
            'area',
        ]));
    }

    public function store(Request $request)
    {
        // dd($request);
        $request->validate([
            "sam_card_id" => 'required|numeric',
            "relasi_area_id" => 'required|numeric',
            "pg_id" => 'required',
            "type" => 'required',
            "tanggal" => 'required|date'
        ]);

        SamCardHistory::create([
            "sam_card_id" => $request->sam_card_id,
            "relasi_area_id" => $request->relasi_area_id,
            "pg_id" => $request->pg_id,
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
