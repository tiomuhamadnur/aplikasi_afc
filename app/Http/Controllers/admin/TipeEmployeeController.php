<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\TipeEmployee;
use Illuminate\Http\Request;

class TipeEmployeeController extends Controller
{
    public function index()
    {
        $tipe_employee = TipeEmployee::all();
        return view('pages.admin.tipe-employee.index', compact([
            'tipe_employee',
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

        TipeEmployee::create(([
            'name' => $request->name,
            'code' => $request->code,
        ]));

        return redirect()->route('tipe-employee.index');
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

        $data = TipeEmployee::findOrFail($request->id);

        $data->update([
            'name' => $request->name,
            'code' => $request->code,
        ]);

        return redirect()->route('tipe-employee.index');
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'id' => 'required',
        ]);

        $data = TipeEmployee::findOrFail($request->id);
        $data->delete();

        return redirect()->route('tipe-employee.index');
    }
}
