<?php

namespace App\Http\Controllers\user;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\ImageUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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

    public function change_password(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => [
                'required',
                'string',
                'min:6',
                'regex:/[A-Z]/', // Harus mengandung huruf kapital
                'regex:/[0-9]/', // Harus mengandung angka
                'confirmed', // Password confirmation
                function ($attribute, $value, $fail) use ($request) {
                    if (Hash::check($value, Auth::user()->password)) {
                        $fail('Password baru tidak boleh sama dengan password lama.');
                    }
                }
            ],
        ], [
            'current_password.current_password' => 'Password lama yang Anda masukkan salah!',
            'password.regex' => 'Password harus mengandung setidaknya satu huruf kapital dan satu angka.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        // Update password
        $user = Auth::user();
        $user->update([
            'password' => Hash::make($request->password),
        ]);

        // Logout user
        Auth::logout();

        // Redirect ke login dengan pesan
        return redirect()->route('login')->withNotify('Password Berhasil diubah, silahkan login menggunakan password baru');
    }
}
