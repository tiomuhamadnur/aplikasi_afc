<?php

namespace App\Http\Controllers\user;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function index()
    {
        return view('pages.user.profile.index');
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

    public function show(string $id)
    {
        //
    }

    public function edit(string $id)
    {
        //
    }

    public function update(Request $request)
    {
        $request->validate([
            'no_hp' => 'numeric|required',
            'photo' => 'file|image|nullable',
            'ttd' => 'file|image|nullable',
        ]);

        $data = User::findOrFail(auth()->user()->id);

        $data->update([
            'no_hp' => $request->no_hp,
        ]);

        if ($request->hasFile('photo') && $request->photo != '') {
            $image = Image::make($request->file('photo'));

            $dataPhoto = $data->photo;
            if ($dataPhoto != null) {
                Storage::delete($dataPhoto);
            }

            $imageName = time().'-'.$request->file('photo')->getClientOriginalName();
            $detailPath = 'photo/profile/';
            $destinationPath = public_path('storage/'. $detailPath);

            $image->resize(null, 300, function ($constraint) {
                $constraint->aspectRatio();
            });

            $image->save($destinationPath.$imageName);

            $photo = $detailPath.$imageName;

            $data->update([
                "photo" => $photo,
            ]);
        }

        if ($request->hasFile('ttd') && $request->ttd != '') {
            $image = Image::make($request->file('ttd'));

            $dataPhoto = $data->ttd;
            if ($dataPhoto != null) {
                Storage::delete($dataPhoto);
            }

            $imageName = time().'-'.$request->file('ttd')->getClientOriginalName();
            $detailPath = 'photo/ttd/';
            $destinationPath = public_path('storage/'. $detailPath);

            $image->resize(null, 300, function ($constraint) {
                $constraint->aspectRatio();
            });

            $image->save($destinationPath.$imageName);

            $photo = $detailPath.$imageName;

            $data->update([
                "ttd" => $photo,
            ]);
        }

        return redirect()->route('profile.index')->withNotify('Data Profil berhasil diupdate');
    }

    public function destroy(string $id)
    {
        //
    }
}
