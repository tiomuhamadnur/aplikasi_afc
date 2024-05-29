<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Direktorat;
use Illuminate\Http\Request;

class DirektoratController extends Controller
{
    public function index()
    {
        $direktorat = Direktorat::all();
        return view('pages.admin.direktorat.index', compact([
            'direktorat',
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
            'code' => 'required',
        ]);

        Direktorat::create(([
            'name' => $request->name,
            'code' => $request->code,
        ]));

        return redirect()->route('direktorat.index');
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
            'code' => 'required',
        ]);

        $data = Direktorat::findOrFail($request->id);

        $data->update([
            'name' => $request->name,
            'code' => $request->code,
        ]);

        return redirect()->route('direktorat.index');
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'id' => 'required',
        ]);

        $data = Direktorat::findOrFail($request->id);
        $data->delete();

        return redirect()->route('direktorat.index');
    }
}
