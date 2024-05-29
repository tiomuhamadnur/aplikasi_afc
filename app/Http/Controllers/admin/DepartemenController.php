<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Departemen;
use Illuminate\Http\Request;

class DepartemenController extends Controller
{
    public function index()
    {
        $departemen = Departemen::all();
        return view('pages.admin.departemen.index', compact([
            'departemen',
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

        Departemen::create(([
            'name' => $request->name,
            'code' => $request->code,
        ]));

        return redirect()->route('departemen.index');
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

        $data = Departemen::findOrFail($request->id);

        $data->update([
            'name' => $request->name,
            'code' => $request->code,
        ]);

        return redirect()->route('departemen.index');
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'id' => 'required',
        ]);

        $data = Departemen::findOrFail($request->id);
        $data->delete();

        return redirect()->route('departemen.index');
    }
}
