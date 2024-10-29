<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\Barang;
use App\Models\Equipment;
use App\Models\FunctionalLocation;
use App\Models\MonitoringEquipment;
use Carbon\Carbon;
use Illuminate\Http\Request;

class GetDataController extends Controller
{
    public function data_monitoring_equipment()
    {
        $subQuery = MonitoringEquipment::selectRaw('MAX(id) as id')->groupBy('equipment_id');

        $monitoring_equipment = MonitoringEquipment::whereIn('id', $subQuery)
            ->with(['equipment.relasi_area.sub_lokasi', 'equipment.arah'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($monitoring_equipment);
    }

    protected function disconnectAllDevices()
    {
        MonitoringEquipment::whereRelation('equipment.tipe_equipment', 'id', '=', 18)->update([
            'status' => 'disconnected',
            'waktu' => Carbon::now(),
        ]);
    }

    private function buildTree(array $elements, $parentId = null) {
        $branch = [];

        foreach ($elements as $element) {
            if ($element['parent_id'] == $parentId) {
                $children = $this->buildTree($elements, $element['id']);

                $icon = !empty($children) ? 'mdi mdi-home' : 'mdi mdi-home text-danger';

                $branch[] = [
                    'id' => $element['id'],
                    'text' => ($element['code'] ?? '') . ' ------- ' . $element['name'],
                    'children' => !empty($children) ? $children : [],  // Anak-anak dari node ini
                    'icon' => $icon
                ];
            }
        }

        return $branch;
    }

    // public function data_asset()
    // {
    //     $assets = Asset::get()->toArray();
    //     // $assets = Equipment::get()->toArray();

    //     $treeData = $this->buildTree($assets);

    //     return response()->json($treeData);
    // }

    public function data_functional_location()
    {
        $functionalLocations = FunctionalLocation::get()->toArray();

        $treeData = $this->buildTree($functionalLocations);

        return response()->json($treeData);
    }

    // public function data_functional_location()
    // {
    //     $functionalLocations = FunctionalLocation::all()->toArray();

    //     $equipments = Equipment::all()->toArray();

    //     function buildFunctionalLocationTree(array $locations, array $equipments, $parentId = null) {
    //         $branch = [];

    //         foreach ($locations as $location) {
    //             if ($location['parent_id'] == $parentId) {
    //                 $children = buildFunctionalLocationTree($locations, $equipments, $location['id']);

    //                 $icon = !empty($children) ? 'mdi mdi-home text-primary' : 'mdi mdi-home text-primary';

    //                 $locationEquipments = array_filter($equipments, function($equipment) use ($location) {
    //                     return $equipment['functional_location_id'] == $location['id'];
    //                 });

    //                 $equipmentNodes = array_map(function($equipment) {
    //                     return [
    //                         'id' => 'equipment_id_' . $equipment['id'],
    //                         'text' => $equipment['name'] . ' (' . ($equipment['equipment_number'] ?? 'N/A') . ')',
    //                         'icon' => 'mdi mdi-settings',
    //                         'children' => []
    //                     ];
    //                 }, $locationEquipments);

    //                 $branch[] = [
    //                     'id' => 'location_id_' . $location['id'],
    //                     'text' => $location['name'],
    //                     'children' => array_merge($children, $equipmentNodes),
    //                     'icon' => $icon,
    //                 ];
    //             }
    //         }

    //         return $branch;
    //     }

    //     $treeData = buildFunctionalLocationTree($functionalLocations, $equipments);

    //     return response()->json($treeData);
    // }


    public function data_asset(Request $request)
    {
        $request->validate([
            'relasi_struktur_id' => 'nullable|numeric|min:1',
        ]);

        $relasi_struktur_id = $request->relasi_struktur_id;
        $functionalLocations = FunctionalLocation::all()->toArray();

        $equipmentsQuery = Equipment::query();

        if ($relasi_struktur_id) {
            $equipmentsQuery->where('relasi_struktur_id', $relasi_struktur_id);
        }

        $equipments = $equipmentsQuery->get()->toArray();

        function buildFunctionalLocationTree(array $locations, array $equipments, $parentId = null) {
            $branch = [];

            foreach ($locations as $location) {
                if ($location['parent_id'] == $parentId) {
                    $children = buildFunctionalLocationTree($locations, $equipments, $location['id']);

                    $icon = !empty($children) ? 'mdi mdi-home' : 'mdi mdi-home text-danger';
                    // $icon = !empty($children) ? false : false;

                    $locationEquipments = array_filter($equipments, function($equipment) use ($location) {
                        return $equipment['functional_location_id'] == $location['id'] && is_null($equipment['parent_id']);
                    });

                    $equipmentNodes = array_map(function($equipment) use ($equipments) {
                        $equipmentChildren = buildEquipmentTree($equipments, $equipment['id']);

                        return [
                            'id' => 'equipment_id_' . $equipment['id'],
                            'text' => ($equipment['equipment_number'] ?? '#') . ' ------ ' . $equipment['name'],
                            'icon' => 'mdi mdi-settings text-success',
                            'children' => $equipmentChildren
                        ];
                    }, $locationEquipments);

                    $branch[] = [
                        'id' => 'location_id_' . $location['id'],
                        'text' => ($location['code'] ?? '#') . ' ------ ' .$location['name'],
                        'children' => array_merge($children, $equipmentNodes),
                        'icon' => $icon,
                    ];
                }
            }

            return $branch;
        }

        function buildEquipmentTree(array $equipments, $parentId) {
            $children = [];

            foreach ($equipments as $equipment) {
                if ($equipment['parent_id'] == $parentId) {
                    $equipmentChildren = buildEquipmentTree($equipments, $equipment['id']);

                    $children[] = [
                        'id' => 'equipment_id_' . $equipment['id'],
                        'text' => ($equipment['equipment_number'] ?? 'N/A') . ' ------ ' . $equipment['name'],
                        'icon' => 'mdi mdi-settings text-danger',
                        'children' => $equipmentChildren
                    ];
                }
            }

            return $children;
        }

        $treeData = buildFunctionalLocationTree($functionalLocations, $equipments);

        return response()->json($treeData);
    }
}
