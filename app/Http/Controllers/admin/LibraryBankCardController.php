<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\ConfigEquipmentAFC;
use App\Models\ConfigPG;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LibraryBankCardController extends Controller
{
    public function index()
    {
        $results = collect();
        $stations = ConfigPG::orderBy('order', 'ASC')->get();
        $pgs = ConfigEquipmentAFC::where('equipment_type_code', 'PG')->get();

        return view('pages.admin.library-bank-card.index', compact([
            'results',
            'stations',
            'pgs',
        ]));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'station_id' => 'required|string',
            'pg_id' => 'required|string',
        ]);

        $station_id = $validated['station_id'];
        $pg_id = $validated['pg_id'];

        $pgs = ConfigEquipmentAFC::where('equipment_type_code', 'PG')
                ->when($station_id !== 'all', function ($query) use ($station_id) {
                    $query->where('station_code', $station_id);
                })
                ->when($pg_id !== 'all', function ($query) use ($pg_id) {
                    $query->where('id', $pg_id);
                })
                ->get();


        if ($pgs->isEmpty()) {
            return redirect()->route('library-bank-card.index')->withNotifyerror('PG tidak ditemukan');
        }

        $path = '/AG_AplData/Maintenance/csv_text/6603.txt';
        $baseConfig = config('filesystems.disks.sftp');

        $results = collect();

        foreach ($pgs as $pg) {
            $disk = Storage::build(array_merge($baseConfig, ['host' => $pg->ip_address]));

            if (!$disk->exists($path)) {
                continue;
            }

            try {
                $content = $disk->get($path);
            } catch (\Exception $e) {
                continue;
            }

            $library = collect(explode(',', $content))
                ->map(fn($item) => trim($item))
                ->filter()
                ->implode(', ');

            $results->push([
                'station_code' => $pg->station_code,
                'pg_id' => $pg->equipment_name,
                'library' => $library,
            ]);
        }

        if ($results->isEmpty()) {
            return redirect()->route('library-bank-card.index')->withNotifyerror('Data tidak ditemukan');
        }

        $stations = ConfigPG::orderBy('order', 'ASC')->get();
        $pgs = ConfigEquipmentAFC::where('equipment_type_code', 'PG')->get();

        return view('pages.admin.library-bank-card.index', [
            'results' => $results,
            'stations' => $stations,
            'pgs' => $pgs,
        ]);
    }
}
