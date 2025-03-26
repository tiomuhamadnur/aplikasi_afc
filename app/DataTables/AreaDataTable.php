<?php

namespace App\DataTables;

use App\Models\RelasiArea;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class AreaDataTable extends DataTable
{
    protected $lokasi_id;
    protected $sub_lokasi_id;
    protected $detail_lokasi_id;

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
            ->addColumn('#', function($item) {
                $deleteModal = "<button type='button' title='Delete'
                        class='btn btn-gradient-danger btn-rounded btn-icon'
                        data-bs-toggle='modal' data-bs-target='#deleteModal'
                        data-id='{$item->id}'>
                        <i class='mdi mdi-delete'></i>
                    </button>";

                $editButton = "<button type='button' title='Edit'
                        class='btn btn-gradient-warning btn-rounded btn-icon'
                        data-bs-toggle='modal' data-bs-target='#editModal'
                        data-id='{$item->id}' data-lokasi_id='{$item->lokasi_id}' data-sub_lokasi_id='{$item->sub_lokasi_id}' data-detail_lokasi_id='{$item->detail_lokasi_id}'>
                        <i class='mdi mdi-lead-pencil'></i>
                    </button>";


                return $editButton . $deleteModal;
            })
            ->rawColumns(['#']);
    }

    public function query(RelasiArea $model): QueryBuilder
    {
        $query = $model->with(['lokasi', 'sub_lokasi', 'detail_lokasi'])
                    ->newQuery();

        // Filter
        if($this->lokasi_id != null)
        {
            $query->where('lokasi_id', $this->lokasi_id);
        }

        if($this->sub_lokasi_id != null)
        {
            $query->where('sub_lokasi_id', $this->sub_lokasi_id);
        }

        if($this->detail_lokasi_id != null)
        {
            $query->where('detail_lokasi_id', $this->detail_lokasi_id);
        }

        return $query;
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
                    ->setTableId('area-table')
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
            Column::make('id')->title('ID'),
            Column::make('lokasi.name')->title('Location'),
            Column::make('sub_lokasi.name')->title('Sub Location'),
            Column::make('detail_lokasi.name')->title('Detail Location'),
            Column::computed('#')
                ->exportable(false)
                ->printable(false)
                ->width(60)
                ->addClass('text-center'),
        ];
    }

    protected function filename(): string
    {
        return date('Ymd') . '_Master Data Relasi Area';
    }
}
