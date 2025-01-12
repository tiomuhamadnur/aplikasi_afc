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
    protected $start_date;
    protected $end_date;

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
        ->addColumn('old_sam_card_uid', function($item) {
            return $item->old_sam_card_id ? $item->old_sam_card->uid : $item->old_uid;

        })
        ->addColumn('#', function($item) {
            $photoUrl = asset('storage/' . $item->photo);
            $photoButton = "<button type='button' title='Show' class='btn btn-gradient-primary btn-rounded btn-icon'
                data-bs-toggle='modal' data-bs-target='#photoModal' data-photo='{$photoUrl}'>
                <i class='mdi mdi-eye'></i>
                </button>";

            return $photoButton;
        })
        ->rawColumns(['old_sam_card_uid', '#']);
    }

    public function query(SamCardHistory $model): QueryBuilder
    {
        $query = $model->with(['sam_card', 'old_sam_card', 'equipment.relasi_area.sub_lokasi'])->newQuery();

        if($this->start_date != null && $this->end_date != null)
        {
            $query->whereBetween('tanggal', [$this->start_date, $this->end_date]);
        }

        return $query;
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
                    ->setTableId('samcardhistory-table')
                    ->columns($this->getColumns())
                    ->minifiedAjax()
                    ->pageLength(10)
                    ->lengthMenu([10, 50, 100, 250, 500, 1000])
                    ->dom('frtiplB')
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
                Column::make('tanggal')->title('Tanggal'),
            Column::make('sam_card.tid')->title('TID'),
            Column::computed('old_sam_card_uid')
                    ->title('Old UID')
                    ->exportable(true)
                    ->printable(true)
                    ->searchable(true),
            Column::make('sam_card.uid')->title('UID'),
            Column::make('equipment.relasi_area.sub_lokasi.name')->title('Location'),
            Column::make('equipment.code')->title('PG ID'),
            Column::make('type')->title('Type'),
        ];
    }

    protected function filename(): string
    {
        return date('Ymd') . '_Data History SAM Card';
    }
}
