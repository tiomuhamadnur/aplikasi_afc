<?php

namespace App\DataTables;

use App\Models\FunctionalLocation;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class FunctionalLocationDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
        ->addColumn('#', function($item) {
            $editRoute = route('fun_loc.edit', $item->uuid);

            $editButton = "<a href='{$editRoute}'>
                <button type='button' title='Edit' class='btn btn-gradient-warning btn-rounded btn-icon'>
                    <i class='mdi mdi-lead-pencil'></i>
                </button>
            </a>";

            return $editButton;
        })
        ->rawColumns(['#']);
    }

    public function query(FunctionalLocation $model): QueryBuilder
    {
        return $model->with(['parent'])->newQuery();
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
                    ->setTableId('functionallocation-table')
                    ->columns($this->getColumns())
                    ->minifiedAjax()
                    ->pageLength(10)
                    ->lengthMenu([10, 50, 100, 250, 500, 1000])
                    //->dom('Bfrtip')
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
            Column::make('id')->title('ID'),
            Column::make('name')->title('Name'),
            Column::make('code')->title('Code'),
            Column::make('description')->title('Description'),
            Column::make('parent.code')->title('Parent'),
            Column::computed('#')
                ->exportable(false)
                ->printable(false)
                ->width(60)
                ->addClass('text-center'),
        ];
    }

    protected function filename(): string
    {
        return date('Ymd') . '_Master Functional Location';
    }
}
