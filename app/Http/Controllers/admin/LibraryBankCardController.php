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

        $path1 = '/AG_AplData/Maintenance/csv_text/6603.txt';
        $path2 = '/AG_AplData/Maintenance/csv_text/6604.txt';
        $baseConfig = config('filesystems.disks.sftp');

        $results = collect();

        foreach ($pgs as $pg) {
            $baseConfig['host'] = $pg->ip_address;
            $disk = Storage::build($baseConfig);

            $library6603 = '';
            $library6604 = '';

            // Ambil file 6603
            if ($disk->exists($path1)) {
                try {
                    $library6603 = trim($disk->get($path1));
                } catch (\Exception $e) {
                    $library6603 = '';
                }
            }

            // Ambil file 6604
            if ($disk->exists($path2)) {
                try {
                    $library6604 = trim($disk->get($path2));
                } catch (\Exception $e) {
                    $library6604 = '';
                }
            }

            $results->push([
                'station_code' => $pg->station_code,
                'pg_id' => $pg->equipment_name,
                'library6603' => $library6603,
                'library6604' => $library6604,
            ]);
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
