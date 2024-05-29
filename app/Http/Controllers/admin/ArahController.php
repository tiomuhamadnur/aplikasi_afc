<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Arah;
use Illuminate\Http\Request;

class ArahController extends Controller
{
    public function index()
    {
        $arah = Arah::all();
        return view('pages.admin.arah.index', compact([
            'arah',
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

        Arah::create(([
            'name' => $request->name,
            'code' => $request->code,
        ]));

        return redirect()->route('arah.index');
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

        $data = Arah::findOrFail($request->id);

        $data->update([
            'name' => $request->name,
            'code' => $request->code,
        ]);

        return redirect()->route('arah.index');
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'id' => 'required',
        ]);

        $data = Arah::findOrFail($request->id);
        $data->delete();

        return redirect()->route('arah.index');
    }
}
