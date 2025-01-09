<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\TransWorkOrderPhoto;
use App\Models\WorkOrder;
use App\Services\ImageUploadService;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;

class TransWorkOrderPhotoController extends Controller
{
    protected $imageUploadService;

    public function __construct(ImageUploadService $imageUploadService)
    {
        $this->imageUploadService = $imageUploadService;
    }

    public function store(string $uuid_workorder, Request $request)
    {
        $request->validate([
            'photo' => 'required|file|image',
            'description' => 'string|required',
        ]);

        $work_order = WorkOrder::where('uuid', $uuid_workorder)->firstOrFail();

        // Update photo jika ada
        if ($request->hasFile('photo')) {
            $photoPath = $this->imageUploadService->uploadPhoto(
                $request->file('photo'),
                'photo/work-order/documentation/', // Path untuk photo
                300
            );

            TransWorkOrderPhoto::create([
                "work_order_id" => $work_order->id,
                "photo" => $photoPath,
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
        if ($data->photo) {
            Storage::delete($data->photo);
        }

        $data->forceDelete();

        return redirect()->back()->withNotify('Data Dokumentasi berhasil dihapus');
    }
}
