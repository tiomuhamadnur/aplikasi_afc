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

        return view('pages.admin.library-bank-card.index', compact(['results', 'stations', 'pgs']));
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
            ->when($station_id !== 'all', fn($query) => $query->where('station_code', $station_id))
            ->when($pg_id !== 'all', fn($query) => $query->where('id', $pg_id))
            ->get();

        if ($pgs->isEmpty()) {
            return redirect()->route('library-bank-card.index')->with('notifyerror', 'PG tidak ditemukan');
        }

        $paths = [
            '6603' => '/AG_AplData/Maintenance/csv_text/6603.txt',
            '6604' => '/AG_AplData/Maintenance/csv_text/6604.txt',
        ];

        $baseConfig = config('filesystems.disks.sftp');
        $results = collect();

        foreach ($pgs as $pg) {
            $config = array_merge($baseConfig, [
                'host' => $pg->ip_address,
                'timeout' => 5,
            ]);

            try {
                $disk = Storage::build($config);

                // Test connection
                $disk->files('/');

                $libraries = [];

                foreach ($paths as $key => $path) {
                    $libraries["library{$key}"] = $disk->exists($path)
                        ? trim($disk->get($path))
                        : 'File not found';
                }

                $results->push([
                    'station_code' => $pg->station_code,
                    'pg_id' => $pg->equipment_name,
                    'direction' => $pg->direction,
                    'library6603' => $libraries['library6603'] ?? 'N/A',
                    'library6604' => $libraries['library6604'] ?? 'N/A',
                    'status' => 'online',
                ]);
            } catch (\Exception $e) {
                $results->push([
                    'station_code' => $pg->station_code,
                    'pg_id' => $pg->equipment_name,
                    'direction' => $pg->direction,
                    'library6603' => 'Server offline',
                    'library6604' => 'Server offline',
                    'status' => 'offline',
                ]);
            }
        }

        $stations = ConfigPG::orderBy('order', 'asc')->get();
        $pgList = ConfigEquipmentAFC::where('equipment_type_code', 'PG')->get();

        return view('pages.admin.library-bank-card.index', [
            'results' => $results,
            'stations' => $stations,
            'pgs' => $pgList,
        ]);
    }

}
