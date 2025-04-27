<?php

namespace App\DataTables;

use App\Models\TransaksiTiket;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Support\Facades\Request;
use Laraindo\RupiahFormat;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class TransaksiTiketDataTable extends DataTable
{
    protected $tap_in_station_code;
    protected $tap_out_station_code;
    protected $bank;
    protected $date;

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
        ->addColumn('transaction_amount', function($item) {
            return RupiahFormat::currency($item->transaction_amount);
        })
        ->addColumn('balance_before', function($item) {
            return RupiahFormat::currency($item->balance_before);
        })
        ->addColumn('balance_after', function($item) {
            return RupiahFormat::currency($item->balance_after);
        });
    }

    public function query(TransaksiTiket $model, Request $request): QueryBuilder
    {
        $query = $model->newQuery();

        // Apply filters
        if ($this->bank != null) {
            $query->where('card_type', $this->bank);
        }

        if ($this->tap_in_station_code != null) {
            $query->where('tap_in_station', $this->tap_in_station_code);
        }

        if ($this->tap_out_station_code != null) {
            $query->where('tap_out_station', $this->tap_out_station_code);
        }

        if ($this->date != null) {
            $date = explode('?', $this->date)[0];

            $start = Carbon::parse($date)->startOfDay()->format('Y-m-d H:i:s');
            $end = Carbon::parse($date)->endOfDay()->format('Y-m-d H:i:s');

            $query->whereBetween('tap_out_time', [$start, $end]);
        }

        return $query;
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
                    ->setTableId('transaksitiket-table')
                    ->columns($this->getColumns())
                    ->minifiedAjax()
                    ->pageLength(10)
                    ->lengthMenu([10, 50, 100, 250, 500, 1000])
                    ->dom('frtiplB')
                    ->orderBy([12, 'desc'])
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
            Column::make('transaction_type')->title('Transaction Type'),
            Column::make('transaction_id')->title('Transaction ID'),
            Column::make('device')->title('Device'),
            Column::make('corner_id')->title('Corner ID'),
            Column::make('pg_id')->title('PG ID'),
            Column::make('pan')->title('PAN'),
            Column::computed('transaction_amount')->title('Transaction Amount'),
            Column::computed('balance_before')->title('Balance Before'),
            Column::computed('balance_after')->title('Balance After'),
            Column::make('card_type')->title('Card Type'),
            Column::make('tap_in_time')->title('Tap In Time'),
            Column::make('tap_in_station')->title('Tap In Station'),
            Column::make('tap_out_time')->title('Tap Out Time'),
            Column::make('tap_out_station')->title('Tap Out Station'),
            Column::make('file_name')->title('File Name'),
        ];
    }

    protected function filename(): string
    {
        return 'TransaksiTiket_' . date('YmdHis');
    }
}
