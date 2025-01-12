<?php

namespace App\DataTables;

use App\Models\PCR;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class PCRDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addColumn('#', function($item) {
                $editRoute = route('pcr.edit', $item->uuid);

                $deleteModal = "<button type='button' title='Delete'
                        class='btn btn-gradient-danger btn-rounded btn-icon'
                        data-bs-toggle='modal' data-bs-target='#deleteModal'
                        data-id='{$item->id}'>
                        <i class='mdi mdi-delete'></i>
                    </button>";

                $editButton = "<button type='button' class='btn btn-gradient-warning btn-rounded btn-icon'
                        onclick=\"window.location.href='{$editRoute}'\" title='Edit'>
                        <i class='text-white mdi mdi-lead-pencil'></i>
                    </button>";


                return $editButton . $deleteModal;
            })
            ->rawColumns(['#']);
    }

    public function query(PCR $model): QueryBuilder
    {
        $query = $model->newQuery()
                    ->select('pcr.*')
                    ->with(['tipe_equipment', 'category', 'problem', 'cause', 'remedy', 'classification']);

        return $query;
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
                    ->setTableId('pcr-table')
                    ->columns($this->getColumns())
                    ->minifiedAjax()
                    ->pageLength(10)
                    ->lengthMenu([10, 50, 100, 250, 500, 1000])
                    ->dom('frtiplB')
                    ->orderBy([0, 'ASC'])
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
            Column::make('tipe_equipment.code')->title('Tipe Equipment'),
            Column::make('category.name')->title('Category'),
            Column::make('problem.name')->title('Problem (P)'),
            Column::make('cause.name')->title('Cause (C)'),
            Column::make('remedy.name')->title('Remedy (R)'),
            Column::make('classification.name')->title('Classification'),
            Column::computed('#')
                    ->exportable(false)
                    ->printable(false)
                    ->width(30)
                    ->addClass('text-center'),
        ];
    }

    protected function filename(): string
    {
        return date('Ymd') . '_Data PCR';
    }
}
