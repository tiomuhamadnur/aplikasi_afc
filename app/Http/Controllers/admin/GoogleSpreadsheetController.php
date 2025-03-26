<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Gangguan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Revolution\Google\Sheets\Facades\Sheets;

class GoogleSpreadsheetController extends Controller
{
    public function index()
    {
        $sheetName = 'Sheet90';

        $rows = Sheets::spreadsheet(config('google.post_spreadsheet_id'))->sheet($sheetName)->get();

        $header = $rows->pull(0);
        $values = Sheets::collection(header: $header, rows: $rows);
        $values->toArray();

        return $values;
    }

    public function store()
    {
        $sheetName = 'Backup';
        $spreadsheetId = config('google.post_spreadsheet_id');

        // Ambil seluruh data dari Google Sheet
        $rows = Sheets::spreadsheet($spreadsheetId)->sheet($sheetName)->get();

        if ($rows->isEmpty()) {
            return response()->json(['message' => 'Google Sheet is empty or not found'], 404);
        }

        // Ambil hanya header (baris pertama)
        $header = $rows->first();

        // Hapus semua data dari baris kedua ke bawah tanpa menghapus header
        Sheets::spreadsheet($spreadsheetId)->sheet($sheetName)->range('A2:Z')->clear();

        $gangguan = Gangguan::all();

        $gangguan_array = $gangguan->map(function ($item) {
                            return [
                                'ID'                => $item->id,
                                'Ticket Number'     => $item->ticket_number,
                                'Report Date'       => $item->report_date,
                                'Equipment Type'    => $item->equipment->tipe_equipment->code,
                                'Equipment ID'      => $item->equipment->code,
                                'Equipment Name'    => $item->equipment->name,
                                'Problem (P)'       => $item->problem_other,
                                'Cause (C)'         => $item->cause_other,
                                'Remedy (R)'        => $item->trans_gangguan_remedy->map(function($transRemedy) {
                                                        return $transRemedy->remedy ? $transRemedy->remedy->name : $transRemedy->remedy_other;
                                                    })->implode(' -> '),
                                'Classification'    => $item->classification->name,
                                'Station Name'      => $item->equipment->relasi_area->sub_lokasi->name,
                                'Station Code'      => $item->equipment->relasi_area->sub_lokasi->code,
                                'Category'          => $item->category->name,
                                'Action Date'       => $item->response_date,
                                'Solved Date'       => $item->solved_date,
                                'Status'            => $item->status->name,
                                'Changed Sparepart' => $item->is_changed ? 'Yes' : 'No',
                                'Downtime'          => $item->is_downtime ? 'Yes' : 'No',
                                'Response Time'     => (int) $item->response_time,
                                'Resolution Time'   => (int) $item->resolution_time,
                                'Total Time'        => (int) $item->total_time,
                            ];
                        })->toArray();

        if (!empty($gangguan_array)) {
            Sheets::spreadsheet($spreadsheetId)->sheet($sheetName)->append($gangguan_array);
        }

        // return response()->json([
        //     'message' => 'Data successfully synced to Google Sheets',
        //     'stored_data' => $gangguan_array
        // ]);

        return redirect()->route('gangguan.index')->withNotify('Data Synced to Looker Successfully.');
    }

    public function store_2(Request $request)
    {
        $sheetName = 'Sheet90';
        $spreadsheetId = config('google.post_spreadsheet_id');

        // Ambil seluruh data dari Google Sheet
        $rows = Sheets::spreadsheet($spreadsheetId)->sheet($sheetName)->get();

        if ($rows->isEmpty()) {
            return response()->json(['message' => 'Google Sheet is empty or not found'], 404);
        }

        // Ambil hanya header (baris pertama)
        $header = $rows->first();

        // Hapus semua data dari baris kedua ke bawah tanpa menghapus header
        Sheets::spreadsheet($spreadsheetId)->sheet($sheetName)->range('A2:Z')->clear();

        // Ambil semua data dari database (contoh: tabel 'users')
        $users = User::select('id', 'name', 'email')->limit(10)->get()->toArray();

        // Simpan data baru ke Google Sheet setelah header
        if (!empty($users)) {
            Sheets::spreadsheet($spreadsheetId)->sheet($sheetName)->append($users);
        }

        return response()->json([
            'message' => 'Data successfully synced to Google Sheets',
            'stored_data' => $users
        ]);
    }

    public function storeUnique(Request $request)
    {
        $data = [['ID' => '34234', 'Name' => 'Panji Ibrahim Nurrachmat']];
        $sheetName = 'Sheet90';

        $rows = Sheets::spreadsheet(config('google.post_spreadsheet_id'))->sheet($sheetName)->get();
        $header = $rows->pull(0);
        $values = Sheets::collection(header: $header, rows: $rows)->toArray();

        $existingEntries = array_map('json_encode', $values);

        $newData = array_filter($data, fn($row) => !in_array(json_encode($row), $existingEntries));

        if ($newData) {
            Sheets::append($newData);
        }

        return response()->json(['message' => 'Data processed successfully', 'added' => $newData]);
    }

    public function show(string $id)
    {
        //
    }

    public function edit(string $id)
    {
        //
    }

    public function update(Request $request, string $id)
    {
        //
    }

    public function destroy(string $id)
    {
        //
    }
}
