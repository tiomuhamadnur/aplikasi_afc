<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Form;
use App\Models\OptionForm;
use App\Models\Parameter;
use App\Models\Satuan;
use App\Services\ImageUploadService;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;

class ParameterController extends Controller
{
    protected $imageUploadService;

    public function __construct(ImageUploadService $imageUploadService)
    {
        $this->imageUploadService = $imageUploadService;
    }

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

    public function store(Request $request)
    {
        $rawData = $request->validate([
            'form_id' => 'required|numeric',
            'name' => 'required|string',
            'code' => 'required|string',
            'description' => 'nullable|string',
            'tipe' => 'required|string',
            'option_form_id' => 'nullable|numeric',
            'min_value' => 'nullable|numeric',
            'max_value' => 'nullable|numeric',
            'satuan_id' => 'nullable|numeric',
            'urutan' => 'required|numeric'
        ]);

        $request->validate([
            'photo_instruction' => 'nullable|file|image',
        ]);

        $data = Parameter::updateOrCreate($rawData, $rawData);

        // Update photo jika ada
        if ($request->hasFile('photo_instruction')) {
            $photoPath = $this->imageUploadService->uploadPhoto(
                $request->file('photo_instruction'),
                'photo/checksheet/', // Path untuk photo
                400
            );

            // Hapus file lama
            if ($data->photo_instruction) {
                Storage::delete($data->photo_instruction);
            }

            // Update path photo di database
            $data->update(['photo_instruction' => $photoPath]);
        }

        return redirect()->back()->withNotify('Data berhasil ditambahkan');
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
        $rawData = $request->validate([
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
            'urutan' => 'required|numeric'
        ]);

        $request->validate([
            'photo_instruction' => 'nullable|file|image',
        ]);

        $data = Parameter::findOrFail($request->id);
        $data->update($rawData);

        // Update photo jika ada
        if ($request->hasFile('photo_instruction')) {
            $photoPath = $this->imageUploadService->uploadPhoto(
                $request->file('photo_instruction'),
                'photo/checksheet/', // Path untuk photo
                400
            );

            // Hapus file lama
            if ($data->photo_instruction) {
                Storage::delete($data->photo_instruction);
            }

            // Update path photo di database
            $data->update(['photo_instruction' => $photoPath]);
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
