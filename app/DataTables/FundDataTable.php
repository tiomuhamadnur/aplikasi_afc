<?php

namespace App\DataTables;

use App\Models\Fund;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class FundDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
        ->addColumn('#', function($item) {
            $editButton = "<button type='button' class='btn btn-gradient-warning btn-rounded btn-icon'
                    data-bs-toggle='modal' data-bs-target='#editModal' data-id='{$item->id}' data-name='{$item->name}'
                    data-code='{$item->code}' data-type='{$item->type}' data-description='{$item->description}' data-divisi_id='{$item->divisi_id}' title='Edit'>
                    <i class='text-white mdi mdi-lead-pencil'></i>
                </button>";

            $deleteModal = "<button type='button' title='Delete'
                class='btn btn-gradient-danger btn-rounded btn-icon'
                data-bs-toggle='modal' data-bs-target='#deleteModal'
                data-id='{$item->id}'>
                <i class='mdi mdi-delete'></i>
            </button>";

            return $editButton . $deleteModal;
        })
        ->rawColumns(['#']);
    }

    public function query(Fund $model): QueryBuilder
    {
        $query = $model->with([
            'divisi',
        ])->newQuery();

        return $query;
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
                    ->setTableId('fund-table')
                    ->columns($this->getColumns())
                    ->minifiedAjax()
                    ->pageLength(10)
                    ->lengthMenu([10, 50, 100, 250, 500, 1000])
                    //->dom('Bfrtip')
                    ->orderBy([1, 'desc'])
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
            Column::computed('#')
                    ->exportable(false)
                    ->printable(false)
                    ->width(60)
                    ->addClass('text-center'),
            Column::make('code')->title('Code'),
            Column::make('name')->title('Name'),
            Column::make('type')->title('Type'),
            Column::make('description')->title('Description'),
            Column::make('divisi.code')->title('Division'),
        ];
    }

    protected function filename(): string
    {
        return date('Ymd') . '_Data Fund Source';
    }
}
