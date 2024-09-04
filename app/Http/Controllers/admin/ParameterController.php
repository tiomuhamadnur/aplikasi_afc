<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Form;
use App\Models\OptionForm;
use App\Models\Parameter;
use App\Models\Satuan;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;

class ParameterController extends Controller
{
    public function index(string $uuid)
    {
        $form = Form::where('uuid', $uuid)->firstOrFail();
        $parameter = Parameter::where('form_id', $form->id)->orderBy('urutan', 'ASC')->get();
        $option_form = OptionForm::all();
        $satuan = Satuan::all();

        return view('pages.admin.parameter.index', compact([
            'parameter',
            'form',
            'option_form',
            'satuan'
        ]));
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $request->validate([
            'form_id' => 'required|numeric',
            'name' => 'required|string',
            'code' => 'required|string',
            'description' => 'nullable|string',
            'tipe' => 'required|string',
            'option_form_id' => 'nullable|numeric',
            'min_value' => 'nullable|numeric',
            'max_value' => 'nullable|numeric',
            'satuan_id' => 'nullable|numeric',
            'photo_instruction' => 'nullable|file',
            'urutan' => 'required|numeric'
        ]);

        $data = Parameter::create([
            'form_id' => $request->form_id,
            'name' => $request->name,
            'code' => $request->code,
            'description' => $request->description,
            'tipe' => $request->tipe,
            'option_form_id' => $request->option_form_id,
            'min_value' => $request->min_value,
            'max_value' => $request->max_value,
            'satuan_id' => $request->satuan_id,
            'urutan' => $request->urutan,
        ]);

        if ($request->hasFile('photo_instruction') && $request->photo_instruction != '') {
            $image = Image::make($request->file('photo_instruction'));

            $imageName = time().'-'.$request->file('photo_instruction')->getClientOriginalName();
            $detailPath = 'photo/checksheet/';
            $destinationPath = public_path('storage/'. $detailPath);

            $image->resize(null, 500, function ($constraint) {
                $constraint->aspectRatio();
            });

            $image->save($destinationPath.$imageName);

            $photo = $detailPath.$imageName;

            $data->update([
                "photo_instruction" => $photo,
            ]);
        }

        return redirect()->back()->withNotify('Data berhasil ditambahkan');
    }

    public function show(string $id)
    {
        //
    }

    public function edit(string $uuid)
    {
        $parameter = Parameter::where('uuid', $uuid)->firstOrFail();
        $option_form = OptionForm::all();
        $satuan = Satuan::all();

        return view('pages.admin.parameter.edit', compact([
            'parameter',
            'option_form',
            'satuan'
        ]));
    }

    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required|numeric',
            'form_id' => 'required|numeric',
            'name' => 'required|string',
            'code' => 'required|string',
            'description' => 'nullable|string',
            'tipe' => 'required|string',
            'option_form_id' => 'nullable|numeric',
            'min_value' => 'nullable|numeric',
            'max_value' => 'nullable|numeric',
            'satuan_id' => 'nullable|numeric',
            'photo_instruction' => 'nullable|file',
            'urutan' => 'required|numeric'
        ]);

        $data = Parameter::findOrFail($request->id);
        $data->update([
            'form_id' => $request->form_id,
            'name' => $request->name,
            'code' => $request->code,
            'description' => $request->description,
            'tipe' => $request->tipe,
            'option_form_id' => $request->option_form_id,
            'min_value' => $request->min_value,
            'max_value' => $request->max_value,
            'satuan_id' => $request->satuan_id,
            'urutan' => $request->urutan,
        ]);

        if ($request->hasFile('photo_instruction') && $request->photo_instruction != '') {
            $image = Image::make($request->file('photo_instruction'));

            $dataPhoto = $data->photo_instruction;
            if ($dataPhoto != null) {
                Storage::delete($dataPhoto);
            }

            $imageName = time().'-'.$request->file('photo_instruction')->getClientOriginalName();
            $detailPath = 'photo/checksheet/';
            $destinationPath = public_path('storage/'. $detailPath);

            $image->resize(null, 500, function ($constraint) {
                $constraint->aspectRatio();
            });

            $image->save($destinationPath.$imageName);

            $photo = $detailPath.$imageName;

            $data->update([
                "photo_instruction" => $photo,
            ]);
        }

        return redirect()->route('parameter.index', $data->form->uuid)->withNotify('Data berhasil diubah');
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'id' => 'required|numeric'
        ]);

        $data = Parameter::findOrFail($request->id);
        $data->delete();

        return redirect()->back()->withNotify('Data berhasil dihapus');
    }
}
