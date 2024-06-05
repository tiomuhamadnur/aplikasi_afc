<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Divisi;
use Illuminate\Http\Request;

class DivisiController extends Controller
{
    public function index()
    {
        $divisi = Divisi::all();
        return view('pages.admin.divisi.index', compact([
            'divisi',
        ]));
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required',
            'code' => 'required',
        ]);

        Divisi::create(([
            'name' => $request->name,
            'email' => $request->email,
            'code' => $request->code,
        ]));

        return redirect()->route('divisi.index');
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
            'id' => 'required',
            'name' => 'required',
            'email' => 'required',
            'code' => 'required',
        ]);

        $data = Divisi::findOrFail($request->id);

        $data->update([
            'name' => $request->name,
            'email' => $request->email,
            'code' => $request->code,
        ]);

        return redirect()->route('divisi.index');
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'id' => 'required',
        ]);

        $data = Divisi::findOrFail($request->id);
        $data->delete();

        return redirect()->route('divisi.index');
    }
}
