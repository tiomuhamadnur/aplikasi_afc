<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\ConfigEquipmentAFC;
use App\Models\ConfigPG;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Spatie\Async\Pool;

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
        // Validate input
        $validated = $request->validate([
            'station_id' => 'required|string',
            'pg_id' => 'required|string',
        ]);

        // Get filtered PGs
        $pgs = ConfigEquipmentAFC::where('equipment_type_code', 'PG')
            ->when($validated['station_id'] !== 'all', fn($query) => $query->where('station_code', $validated['station_id']))
            ->when($validated['pg_id'] !== 'all', fn($query) => $query->where('id', $validated['pg_id']))
            ->get();

        if ($pgs->isEmpty()) {
            return redirect()
                ->route('library-bank-card.index')
                ->withNotifyerror('PG tidak ditemukan');
        }

        // Configuration
        $paths = [
            '6603' => '/AG_AplData/Maintenance/csv_text/6603.txt',
            '6604' => '/AG_AplData/Maintenance/csv_text/6604.txt',
        ];

        $baseConfig = config('filesystems.disks.sftp');
        $results = collect();

        // Process PGs in parallel
        $pool = Pool::create()
            ->timeout(10) // Set a reasonable timeout
            ->sleepTime(100); // Add small delay between jobs

        foreach ($pgs as $pg) {
            $pool->add(function () use ($pg, $baseConfig, $paths) {
                try {
                    $config = array_merge($baseConfig, [
                        'host' => $pg->ip_address,
                        'timeout' => 5,
                    ]);

                    $disk = Storage::build($config);

                    // Test connection
                    $disk->files('/');

                    $libraries = [];
                    foreach ($paths as $key => $path) {
                        $libraries["library{$key}"] = $disk->exists($path)
                            ? trim($disk->get($path))
                            : 'File not found';
                    }

                    return [
                        'station_code' => $pg->station_code,
                        'pg_id' => $pg->equipment_name,
                        'direction' => $pg->direction,
                        'library6603' => $libraries['library6603'] ?? 'N/A',
                        'library6604' => $libraries['library6604'] ?? 'N/A',
                        'status' => 'online',
                        'ip_address' => $pg->ip_address, // Added for debugging
                    ];
                } catch (\Exception $e) {
                    return [
                        'station_code' => $pg->station_code,
                        'pg_id' => $pg->equipment_name,
                        'direction' => $pg->direction,
                        'library6603' => 'Connection failed',
                        'library6604' => 'Connection failed',
                        'status' => 'offline',
                        'ip_address' => $pg->ip_address,
                        'error' => $e->getMessage(), // Added error details
                    ];
                }
            });
        }

        // Wait for all jobs to complete and convert to collection
        $results = collect($pool->wait());

        // Get additional data for the view
        $stations = ConfigPG::orderBy('order', 'asc')->get();
        $pgList = ConfigEquipmentAFC::where('equipment_type_code', 'PG')->get();

        return view('pages.admin.library-bank-card.index', [
            'results' => $results,
            'stations' => $stations,
            'pgs' => $pgList,
            'current_station' => $validated['station_id'],
            'current_pg' => $validated['pg_id'],
        ]);
    }

}
