<?php

namespace App\DataTables;

use App\Models\WorkOrder;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class WorkOrderDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
        ->addColumn('#', function($item) {
            $detailRoute = route('work-order.detail', $item->uuid);
            $detailButton = "<button type='button' class='btn btn-gradient-primary btn-rounded btn-icon'
                onclick=\"window.location.href='{$detailRoute}'\" title='Show Detail'>
                <i class='text-white mdi mdi-eye'></i>
            </button>";

            if (auth()->user()->role_id != 1) {
                return $detailButton;
            }

            $editRoute = route('work-order.edit', $item->uuid);

            $editButton = "<button type='button' class='btn btn-gradient-warning btn-rounded btn-icon'
                    onclick=\"window.location.href='{$editRoute}'\" title='Edit'>
                    <i class='text-white mdi mdi-lead-pencil'></i>
                </button>";

            return $detailButton . $editButton;
        })
        ->addColumn('status', function($item) {
            $badgeClass = '';
            if ($item->status_id == 2) {
                $badgeClass = 'badge-gradient-success';
            } elseif ($item->status_id == 3) {
                $badgeClass = 'badge-gradient-warning';
            } elseif ($item->status_id == 4) {
                $badgeClass = 'badge-gradient-info';
            } else {
                $badgeClass = 'badge-gradient-danger';
            }
            return "<label class='badge {$badgeClass} text-uppercase'>{$item->status->name}</label>";
        })
        ->addColumn('created_at', function($item) {
            return Carbon::parse($item->created_at)->format('Y-m-d H:i:s');
        })
        ->rawColumns(['#', 'created_at', 'status']);
    }

    public function query(WorkOrder $model): QueryBuilder
    {
        $query = $model->with([
            'tipe_pekerjaan',
            'status',
            'user',
            ])->newQuery();

        return $query;
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
                    ->setTableId('workorder-table')
                    ->columns($this->getColumns())
                    ->minifiedAjax()
                    ->pageLength(10)
                    ->lengthMenu([10, 50, 100, 250, 500, 1000])
                    ->dom('Blfrtip')
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
                    ->width(40)
                    ->addClass('text-center'),
            Column::make('date')->title('Date'),
            Column::make('ticket_number')->title('WO Number'),
            Column::make('wo_number_sap')->title('WO SAP'),
            Column::make('name')->title('Name'),
            Column::make('description')->title('Description'),
            Column::make('tipe_pekerjaan.code')->title('Order Type'),
            // Column::make('status.name')->title('Status'),
            Column::computed('status')
                    ->exportable(true)
                    ->printable(true)
                    ->width(20)
                    ->addClass('text-center')
                    ->searchable(true),
            Column::make('user.name')->title('Created By'),
            Column::computed('created_at')->title('Created At'),
        ];
    }

    protected function filename(): string
    {
        return date('Ymd') . '_Data Work Order';
    }
}
