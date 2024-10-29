<?php

namespace App\DataTables;

use App\Models\Equipment;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Support\Facades\Request;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class EquipmentDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
        ->addColumn('fun_loc', function($item) {
            return $item->functional_location->code ?? '';
        })
        ->addColumn('detail', function($item) {
            $photoUrl = asset('storage/' . $item->photo);
            $name = $item->name;
            $code = $item->code;
            $equipmentNumber = $item->equipment_number ?? '-';
            $tipeEquipmentCode = $item->tipe_equipment->code ?? '-';
            $tipeEquipmentName = $item->tipe_equipment->name ?? '-';
            $lokasi = ($item->relasi_area->sub_lokasi->name ?? '-') . ' - ' . ($item->relasi_area->detail_lokasi->name ?? '-');
            $struktur = ($item->relasi_struktur->divisi->code ?? '-') . ' - ' . ($item->relasi_struktur->departemen->code ?? '-') . ' - ' . ($item->relasi_struktur->seksi->code ?? '-');
            $arah = $item->arah->name ?? '-';
            $status = $item->status ?? '-';
            $deskripsi = $item->deskripsi ?? '-';

            return "<button type='button' title='Show'
                class='btn btn-gradient-success btn-rounded btn-icon'
                data-bs-toggle='modal' data-bs-target='#photoModal'
                data-photo='{$photoUrl}'
                data-name='{$name}' data-code='{$code}'
                data-equipment_number='{$equipmentNumber}'
                data-tipe_equipment='{$tipeEquipmentCode} ({$tipeEquipmentName})'
                data-lokasi='{$lokasi}'
                data-struktur='{$struktur}'
                data-arah='{$arah}'
                data-status='{$status}'
                data-deskripsi='{$deskripsi}'>
                <i class='mdi mdi-eye'></i>
            </button>";
        })
        ->addColumn('history', function($item) {
            $historyRoute = route('checksheet.history', ['uuid_equipment' => $item->uuid]);

            $historyButton = "<a href='{$historyRoute}'>
                <button type='button' title='History Checksheet' class='btn btn-gradient-primary btn-rounded btn-icon'>
                    <i class='mdi mdi-history'></i>
                </button>
            </a>";

            return $historyButton;
        })
        ->addColumn('#', function($item) {
            $editRoute = route('equipment.edit', $item->uuid);
            $deleteModal = "<button type='button' title='Delete'
                class='btn btn-gradient-danger btn-rounded btn-icon'
                data-bs-toggle='modal' data-bs-target='#deleteModal'
                data-id='{$item->id}'>
                <i class='mdi mdi-delete'></i>
            </button>";

            $editButton = "<a href='{$editRoute}'>
                <button type='button' title='Edit' class='btn btn-gradient-warning btn-rounded btn-icon'>
                    <i class='mdi mdi-lead-pencil'></i>
                </button>
            </a>";

            return $editButton . $deleteModal;
        })
        ->rawColumns(['detail', 'fun_loc', 'history', '#']);
    }

    public function query(Equipment $model): QueryBuilder
    {
        // $query = $model->with(['tipe_equipment', 'relasi_area.sub_lokasi', 'functional_location', 'parent'])->newQuery();
        $query = $model->with([
                'tipe_equipment',
                'relasi_area.sub_lokasi',
                'functional_location',
                'parent',
            ])->newQuery();

        return $query;
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
                    ->setTableId('equipment-table')
                    ->columns($this->getColumns())
                    ->minifiedAjax()
                    ->pageLength(10)
                    ->lengthMenu([10, 50, 100, 250, 500, 1000])
                    //->dom('Bfrtip')
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
                        ]
                    ]);
    }

    public function getColumns(): array
    {
        return [
            Column::make('id')->title('ID'),
            Column::make('name')->title('Name'),
            Column::make('code')->title('Code'),
            Column::make('tipe_equipment.code')->title('Type'),
            Column::make('equipment_number')->title('Equipment Number'),
            Column::make('relasi_area.sub_lokasi.name')->title('Location'),
            Column::computed('fun_loc')
                    ->title('Functional Location')
                    ->exportable(true)
                    ->printable(true)
                    ->addClass('text-center'),
            Column::make('parent.name')
                    ->title('Parent')
                    ->sortable(false),
            Column::computed('detail')->title('Detail')
                    ->exportable(false)
                    ->printable(false)
                    ->width(30)
                    ->addClass('text-center')
                    ->searchable(false),
            Column::computed('history')->title('History')
                    ->exportable(false)
                    ->printable(false)
                    ->width(30)
                    ->addClass('text-center')
                    ->searchable(false),
            Column::computed('#')
                    ->exportable(false)
                    ->printable(false)
                    ->width(40)
                    ->addClass('text-center'),
        ];
    }

    protected function filename(): string
    {
        return date('Ymd') . '_Master Data Equipments';
    }
}
