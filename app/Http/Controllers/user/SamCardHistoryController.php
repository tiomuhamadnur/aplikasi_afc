<?php

namespace App\Http\Controllers\user;

use App\DataTables\SamCardHistoryDataTable;
use App\Http\Controllers\Controller;
use App\Models\Equipment;
use App\Models\RelasiArea;
use App\Models\SamCard;
use App\Models\SamCardHistory;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;

class SamCardHistoryController extends Controller
{
    public function index(SamCardHistoryDataTable $dataTable, Request $request)
    {
        $request->validate([
            'start_date' => 'date|nullable',
            'end_date' => 'date|nullable',
        ]);

        $start_date = $request->start_date ?? null;
        $end_date = $request->end_date ?? $start_date;

        $sam_card = SamCard::where('mc', '!=', null)->orderBy('created_at', 'DESC')->get();
        $area = RelasiArea::where('lokasi_id', 2)->distinct('sub_lokasi_id')->get();
        $pg = Equipment::where('tipe_equipment_id', 1)->get();

        return $dataTable->with([
            'start_date' => $start_date,
            'end_date' => $end_date,
        ])->render('pages.user.sam-card-history.index', compact([
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
        $sam_cards = SamCard::all();

        return view('pages.user.sam-card-history.create', compact([
            'sam_card',
            'sam_cards',
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
            "tanggal" => 'required|date',
            "old_uid" => 'nullable|string',
            "old_sam_card_id" => 'nullable|numeric',
            'photo' => 'nullable|file|image',
        ]);

        $data = SamCardHistory::create([
            "sam_card_id" => $request->sam_card_id,
            "equipment_id" => $request->equipment_id,
            "type" => $request->type,
            "tanggal" => $request->tanggal,
            "old_uid" => $request->old_uid,
            "old_sam_card_id" => $request->old_sam_card_id,
        ]);

        if ($request->hasFile('photo') && $request->photo != '') {
            $image = Image::make($request->file('photo'));

            $imageName = time().'-'.$request->file('photo')->getClientOriginalName();
            $detailPath = 'photo/sam-card-history/';
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
