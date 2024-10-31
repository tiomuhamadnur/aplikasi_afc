<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\TransWorkOrderPhoto;
use App\Models\WorkOrder;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;

class TransWorkOrderPhotoController extends Controller
{
    public function store(string $uuid_workorder, Request $request)
    {
        $request->validate([
            'photo' => 'required|file|image',
            'description' => 'string|required',
        ]);

        $work_order = WorkOrder::where('uuid', $uuid_workorder)->firstOrFail();

        if ($request->hasFile('photo') && $request->photo != '') {
            $image = Image::make($request->file('photo'));

            $imageName = time().'-'.$request->file('photo')->getClientOriginalName();
            $detailPath = 'photo/work-order/documentation/';
            $destinationPath = public_path('storage/'. $detailPath);

            $image->resize(null, 300, function ($constraint) {
                $constraint->aspectRatio();
            });

            $image->save($destinationPath.$imageName);

            $photo = $detailPath.$imageName;

            TransWorkOrderPhoto::create([
                "work_order_id" => $work_order->id,
                "photo" => $photo,
                "description" => $request->description,
            ]);
        }

        return redirect()->back()->withNotify('Data Dokumentasi berhasil ditambahkan');
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'id' => 'required|numeric|min:1',
        ]);

        $data = TransWorkOrderPhoto::findOrFail($request->id);

        $dataPhoto = $data->photo;
        if ($dataPhoto != null) {
            Storage::delete($dataPhoto);
        }

        $data->forceDelete();

        return redirect()->back()->withNotify('Data Dokumentasi berhasil dihapus');
    }
}
