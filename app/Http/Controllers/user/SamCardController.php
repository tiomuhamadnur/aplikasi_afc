<?php

namespace App\Http\Controllers\user;

use App\DataTables\SamCardDataTable;
use App\Http\Controllers\Controller;
use App\Imports\SamCardImport;
use App\Models\RelasiArea;
use App\Models\SamCard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class SamCardController extends Controller
{
    // public function index()
    // {
    //     $sam_card = SamCard::orderByDesc('updated_at')->get();

    //     return view('pages.user.sam-card.index', compact(['sam_card']));
    // }

    public function index(SamCardDataTable $dataTable)
    {
        return $dataTable->render('pages.user.sam-card.index');
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $request->validate([
            "uid" => 'required',
            "mid" => 'required',
            "tid" => 'required',
            "pin" => 'required',
            "mc" => 'required',
        ]);

        $status = 'ready';

        SamCard::create([
            'uid' => $request->uid,
            'mid' => $request->mid,
            'tid' => $request->tid,
            'pin' => $request->pin,
            'mc' => $request->mc,
            'status' => $status,
            'alokasi' => $request->alokasi,
        ]);

        return redirect()->route('sam-card.index');
    }

    public function merry_code(Request $request)
    {
        // dd($request);
        $request->validate([
            "uid" => 'required',
            "mid" => 'required',
            "tid" => 'required',
            "pin" => 'required',
            "com" => 'required',
            "slot" => 'required',
        ]);

        $pythonPath = 'python';

        $scriptPath = '"C:\\Users\\mtio\\AppData\\Local\\Programs\\Python\\Python37-32\\Marry Code\\MarrySAMCustom.py"';
        $command = "$pythonPath $scriptPath –comm serial –serialpath COM$request->com baudrate 115200 –slot $request->slot";

        $output = shell_exec($command);


        // Return the output or handle it as needed
        $responseData = json_decode($output, true);

        $status = $responseData['status'];
        $mc = $responseData['mc'];
        $error_message = $responseData['error_message'];

        if($status == 'success')
        {
            $status = 'ready';

            SamCard::create([
                'uid' => $request->uid,
                'mid' => $request->mid,
                'tid' => $request->tid,
                'pin' => $request->pin,
                'mc' => $mc,
                'status' => $status,
            ]);
        }
        else
        {
            return redirect()->route('sam-card.index')->withNotifyerror($error_message);
        }

        return redirect()->route('sam-card.index')->withNotify('Data SAM Card berhasil ditambahkan');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls',
        ]);

        if($request->hasFile('file'))
        {
            $file = $request->file('file');
            Excel::import(new SamCardImport, $file);
        }

        return redirect()->route('sam-card.index');
    }

    public function edit($uuid)
    {
        $sam_card = SamCard::where('uuid', $uuid)->firstOrFail();

        return view('pages.user.sam-card.edit', compact([
            'sam_card',
        ]));
    }

    public function update(Request $request)
    {
        $request->validate([
            "id" => 'required|numeric',
            "uid" => 'required',
            "mid" => 'required',
            "tid" => 'required',
            "pin" => 'required',
            "mc" => 'required',
            "status" => 'required',
        ]);

        $data = SamCard::findOrFail($request->id);
        $data->update([
            'uid' => $request->uid,
            'mid' => $request->mid,
            'tid' => $request->tid,
            'pin' => $request->pin,
            'mc' => $request->mc,
            'status' => $request->status,
            'alokasi' => $request->alokasi,
        ]);

        return redirect()->route('sam-card.index')->withNotify('Data berhasil diperbaharui.');
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'id' => 'required|numeric',
        ]);

        $data = SamCard::findOrFail($request->id);
        $data->delete();

        return redirect()->route('sam-card.index')->withNotify('Data berhasil dihapus!');
    }
}
