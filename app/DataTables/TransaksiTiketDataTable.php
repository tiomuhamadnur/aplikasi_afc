<?php

namespace App\DataTables;

use App\Models\TransaksiTiket;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Support\Facades\Request;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class TransaksiTiketDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))->setRowId('id');
    }

    public function query(TransaksiTiket $model, Request $request): QueryBuilder
    {
        $query = $model->newQuery();

        // // Apply filters
        // if ($request->has('transaction_type')) {
        //     $query->where('transaction_type', $request->get('transaction_type'));
        // }
        // if ($request->has('transaction_id')) {
        //     $query->where('transaction_id', 'like', '%' . $request->get('transaction_id') . '%');
        // }
        // Add more filters as needed

        return $query;
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
                    ->setTableId('transaksitiket-table')
                    ->columns($this->getColumns())
                    ->minifiedAjax()
                    ->pageLength(100)
                    ->lengthMenu([10, 50, 100, 250, 500, 1000])
                    ->dom('Blfrtip')
                    ->orderBy(1)
                    ->selectStyleSingle()
                    ->buttons([
                        // Button::make('excel'),
                        // Button::make('csv'),
                        // Button::make('pdf'),
                        // Button::make('print'),
                        // Button::make('reset'),
                        // Button::make('reload')
                    ]);
    }

    public function getColumns(): array
    {
        return [
            // Column::computed('action')
            //         ->exportable(false)
            //         ->printable(false)
            //         ->width(60)
            //         ->addClass('text-center'),
            // Column::make('id'),
            Column::make('transaction_type'),
            Column::make('transaction_id'),
            Column::make('device'),
            Column::make('corner_id'),
            Column::make('pg_id'),
            Column::make('pan'),
            Column::make('transaction_amount'),
            Column::make('balance_before'),
            Column::make('balance_after'),
            Column::make('card_type'),
            Column::make('tap_in_time'),
            Column::make('tap_in_station'),
            Column::make('tap_out_time'),
            Column::make('tap_out_station'),
        ];
    }

    protected function filename(): string
    {
        return 'TransaksiTiket_' . date('YmdHis');
    }
}
