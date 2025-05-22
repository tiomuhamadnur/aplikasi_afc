<?php

namespace App\DataTables;

use App\Models\Dokuman;
use App\Models\Dokumen;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class DokumenDataTable extends DataTable
{
    protected $departemen_id;
    protected $tipe_dokumen_id;

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
                    $editModal = "<button type='button' title='Edit' class='btn btn-gradient-warning btn-rounded btn-icon'
                        data-bs-toggle='modal' data-bs-target='#editModal'
                        data-id='{$item->id}'
                        data-departemen_id='{$item->departemen_id}'
                        data-tipe_dokumen_id='{$item->tipe_dokumen_id}'
                        data-judul='{$item->judul}'
                        data-nomor='{$item->nomor}'
                        data-nomor_revisi='{$item->nomor_revisi}'
                        data-tanggal_pengesahan='{$item->tanggal_pengesahan}'
                        data-url='{$item->url}'
                        data-keterangan='{$item->keterangan}'>
                        <i class='text-white mdi mdi-lead-pencil'></i>
                        </button>";
                    $showRoute = $item->url;
                    $showButton = "<button type='button' class='btn btn-gradient-primary btn-rounded btn-icon'
                        onclick=\"window.open('{$showRoute}', '_blank')\" title='Show'>
                        <i class='mdi mdi-eye'></i>
                    </button>";

                    return $showButton . $editModal . $deleteModal;
                })
                ->rawColumns(['#']);
    }

    public function query(Dokumen $model): QueryBuilder
    {
        $query = $model->with(['departemen', 'departemen.relasi_struktur.divisi', 'tipe_dokumen'])->newQuery();

        if($this->departemen_id != null)
        {
            $query->where('departemen_id',$this->departemen_id);
        }

        if($this->tipe_dokumen_id != null)
        {
            $query->whereRelation('tipe_dokumen', 'id', '=', $this->tipe_dokumen_id);
        }

        return $query;
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
                    ->setTableId('dokumen-table')
                    ->columns($this->getColumns())
                    ->minifiedAjax()
                    ->pageLength(10)
                    ->lengthMenu([10, 50, 100, 250, 500, 1000])
                    ->dom('frtiplB')
                    ->orderBy([7, 'desc'])
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
            Column::make('departemen.relasi_struktur.divisi.code')->title('Divisi'),
            Column::make('departemen.code')->title('Departemen'),
            Column::make('tipe_dokumen.code')->title('Tipe Dokumen'),
            Column::make('judul')->title('Judul'),
            Column::make('nomor')->title('Nomor Dokumen'),
            Column::make('nomor_revisi')->title('Nomor Revisi'),
            Column::make('tanggal_pengesahan')->title('Tanggal Pengesahan'),
            Column::make('keterangan')->title('Keterangan'),
        ];
    }

    protected function filename(): string
    {
        return 'Dokumen_' . date('YmdHis');
    }
}
