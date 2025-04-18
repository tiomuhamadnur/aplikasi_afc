<?php

namespace App\DataTables;

use App\Models\ConfigEquipmentAFC;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class ConfigEquipmentAFCDataTable extends DataTable
{
    protected $station_code;
    protected $equipment_type_code;
    protected $corner_id;
    protected $direction;

    public function with(array|string $key, mixed $value = null): static
    {
        if (is_array($key)) {
            foreach ($key as $k => $v) {
                $this->{$k} = $v;
            }
        } else {
            $this->{$key} = $value;
        }

        return $this;
    }

    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addColumn('#', function ($item) {
                if ($item->equipment_type_code == 'PG') {
                    $deleteModal = "<button type='button' title='Control PG'
                                        class='btn btn-gradient-danger btn-rounded btn-icon'
                                        data-bs-toggle='modal' data-bs-target='#controlPGModal'
                                        data-id='{$item->id}' data-uuid='{$item->uuid}' data-station_code='{$item->station_code}' data-equipment_type_code='{$item->equipment_type_code}' data-equipment_name='{$item->equipment_name}' data-equipment_id='{$item->equipment_id}' data-corner_id='{$item->corner_id}' data-direction='{$item->direction}'>
                                        <i class='mdi mdi-power'></i>
                                    </button>";

                    return $deleteModal;
                }

                return null;
            })
            ->rawColumns(['#']);
    }

    public function query(ConfigEquipmentAFC $model): QueryBuilder
    {
        $query = $model->newQuery();

        // Filter
        if ($this->station_code != null) {
            $query->where('station_code', $this->station_code);
        }

        if ($this->equipment_type_code != null) {
            $query->where('equipment_type_code', $this->equipment_type_code);
        }

        if ($this->corner_id != null) {
            $query->where('corner_id', $this->corner_id);
        }
        if ($this->direction != null) {
            $query->where('direction', $this->direction);
        }

        return $query;
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('configequipmentafc-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->pageLength(10)
            ->lengthMenu([10, 50, 100, 250, 500, 1000])
            ->dom('frtiplB')
            ->orderBy([0, 'asc'])
            ->selectStyleSingle()
            ->buttons([
                [
                    'extend' => 'excel',
                    'text' => 'Export to Excel',
                    'attr' => [
                        'id' => 'datatable-excel',
                        'style' => 'display: none;',
                    ],
                ],
            ]);
    }

    public function getColumns(): array
    {
        return [
            Column::make('id')->title('ID'),
            Column::make('line_code')->title('Line'),
            Column::make('station_name')->title('Station Name'),
            Column::make('station_code')->title('Station Code'),
            Column::make('equipment_type_code')->title('Equipment Type'),
            Column::make('equipment_name')->title('Equipment Name'),
            Column::make('equipment_id')->title('Equipment ID'),
            Column::make('corner_id')->title('Corner ID'),
            Column::make('ip_address')->title('IP Address'),
            Column::make('mac_address')->title('MAC Address'),
            Column::make('direction')->title('Direction'),
            Column::make('x_coordinate')->title('X Coordinate'),
            Column::make('y_coordinate')->title('Y Coordinate'),
            Column::make('ns_device_id')->title('NS Device ID'),
            Column::computed('#')->exportable(false)
                ->printable(false)
                ->width(40)
                ->addClass('text-center')
        ];
    }

    protected function filename(): string
    {
        return 'ConfigEquipmentAFC_' . date('YmdHis');
    }
}
