<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Seksi;
use Illuminate\Http\Request;

class SeksiController extends Controller
{
    public function index()
    {
        $seksi = Seksi::all();
        return view('pages.admin.seksi.index', compact([
            'seksi',
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

        Seksi::create(([
            'name' => $request->name,
            'email' => $request->email,
            'code' => $request->code,
        ]));

        return redirect()->route('seksi.index');
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

        $data = Seksi::findOrFail($request->id);

        $data->update([
            'name' => $request->name,
            'email' => $request->email,
            'code' => $request->code,
        ]);

        return redirect()->route('seksi.index');
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'id' => 'required',
        ]);

        $data = Seksi::findOrFail($request->id);
        $data->delete();

        return redirect()->route('seksi.index');
    }
}
