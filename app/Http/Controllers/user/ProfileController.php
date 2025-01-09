<?php

namespace App\Http\Controllers\user;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\ImageUploadService;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    protected $imageUploadService;

    public function __construct(ImageUploadService $imageUploadService)
    {
        $this->imageUploadService = $imageUploadService;
    }

    public function index()
    {
        return view('pages.user.profile.index');
    }

    public function update(Request $request)
    {
        $request->validate([
            'no_hp' => 'numeric|required',
            'photo' => 'file|image|nullable',
            'ttd' => 'file|image|nullable',
        ]);

        $data = User::findOrFail(auth()->user()->id);

        // Update nomor HP
        $data->update([
            'no_hp' => $request->no_hp,
        ]);

        // Update photo jika ada
        if ($request->hasFile('photo')) {
            $photoPath = $this->imageUploadService->uploadPhoto(
                $request->file('photo'),
                'photo/profile/', // Path untuk photo
                300
            );

            // Hapus file lama
            if ($data->photo) {
                Storage::delete($data->photo);
            }

            // Update path photo di database
            $data->update(['photo' => $photoPath]);
        }

        // Update tanda tangan (ttd) jika ada
        if ($request->hasFile('ttd')) {
            $ttdPath = $this->imageUploadService->uploadPhoto(
                $request->file('ttd'),
                'photo/ttd/', // Path untuk ttd
                300
            );

            // Hapus file lama
            if ($data->ttd) {
                Storage::delete($data->ttd);
            }

            // Update path ttd di database
            $data->update(['ttd' => $ttdPath]);
        }

        return redirect()->route('profile.index')->withNotify('Data Profil berhasil diupdate');
    }
}
