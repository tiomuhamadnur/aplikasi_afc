<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\StatusBudgeting;
use Illuminate\Http\Request;

class StatusBudgetingController extends Controller
{
    public function index()
    {
        $status_budgeting = StatusBudgeting::all();
        return view('pages.admin.status-budgeting.index', compact([
            'status_budgeting',
        ]));
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required',
            'code' => 'required',
        ]);

        StatusBudgeting::updateOrCreate($data, $data);

        return redirect()->route('status-budgeting.index')->withNotify('Data berhasil ditambahkan');
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
        $rawData = $request->validate([
            'id' => 'required',
            'name' => 'required',
            'code' => 'required',
        ]);

        $data = StatusBudgeting::findOrFail($request->id);

        $data->update($rawData);

        return redirect()->route('status-budgeting.index')->withNotify('Data berhasil diubah');
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'id' => 'required',
        ]);

        $data = StatusBudgeting::findOrFail($request->id);
        $data->delete();

        return redirect()->route('status-budgeting.index')->withNotify('Data berhasil dihapus');
    }
}
