<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index()
    {
        $role = Role::all();
        return view('pages.admin.role.index', compact([
            'role',
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

        Role::create(([
            'name' => $request->name,
            'code' => $request->code,
        ]));

        return redirect()->route('role.index');
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

        $data = Role::findOrFail($request->id);

        $data->update([
            'name' => $request->name,
            'code' => $request->code,
        ]);

        return redirect()->route('role.index');
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'id' => 'required',
        ]);

        $data = Role::findOrFail($request->id);
        $data->delete();

        return redirect()->route('role.index');
    }
}
