<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\MonitoringPermit;
use Illuminate\Http\Request;

class MonitoringPermitController extends Controller
{
    public function index()
    {
        //
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

    public function show(string $id)
    {
        //
    }

    public function edit(string $id)
    {
        //
    }

    public function update()
    {
        MonitoringPermit::updateStatus();
        MonitoringPermit::notifyExpiringPermits();

        return response()->json(['message' => 'Permit status updated successfully']);
    }

    public function destroy(string $id)
    {
        //
    }
}
