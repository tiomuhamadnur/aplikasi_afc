<?php

namespace App\DataTables;

use App\Models\SamCardHistory;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Support\Facades\Request;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class SamCardHistoryDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addColumn('action', 'samcardhistory.action')
            ->setRowId('id');
    }

    public function query(SamCardHistory $model, Request $request): QueryBuilder
    {
        $query = $model->with(['sam_card', 'relasi_area.sub_lokasi'])->newQuery();

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
                    ->setTableId('samcardhistory-table')
                    ->columns($this->getColumns())
                    ->minifiedAjax()
                    ->pageLength(50)
                    ->lengthMenu([10, 50, 100, 250, 500, 1000])
                    //->dom('Bfrtip')
                    ->orderBy([0, 'asc'])
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

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        return [
            Column::make('sam_card.tid')->title('TID'),
            Column::make('sam_card.uid')->title('UID'),
            Column::make('relasi_area.sub_lokasi.name')->title('Lokasi'),
            Column::make('pg_id')->title('PG ID'),
            Column::make('type')->title('Type'),
            Column::make('tanggal')->title('Tanggal'),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'SamCardHistory_' . date('YmdHis');
    }
}
