<?php

namespace App\DataTables;

use App\Models\Barang;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class BarangDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
        ->addColumn('detail', function($item) {
            $photoUrl = asset('storage/' . $item->photo);
            $name = $item->name ?? '-';
            $spesifikasi = $item->spesifikasi ?? '-';
            $materialNumber = $item->material_number ?? '-';
            $serialNumber = $item->serial_number ?? '-';
            $tipeBarang = $item->tipe_barang->name ?? '-';
            $lokasi = ($item->relasi_area->lokasi->name ?? '#') . ' - ' . ($item->relasi_area->sub_lokasi->name ?? '#') . ' - ' . ($item->relasi_area->detail_lokasi->name ?? '#');
            $owner = ($item->relasi_struktur->divisi->name ?? '#') . ' - ' . ($item->relasi_struktur->departemen->name ?? '#') . ' - ' . ($item->relasi_struktur->seksi->name ?? '#');
            $satuan = $item->satuan->code ?? '-';
            $deskripsi = $item->deskripsi ?? '-';

            return "<td class='text-center'>
                <button type='button' title='Show'
                    class='btn btn-gradient-success btn-rounded btn-icon'
                    data-bs-toggle='modal' data-bs-target='#photoModal'
                    data-photo='{$photoUrl}'
                    data-name='{$name}'
                    data-spesifikasi='{$spesifikasi}'
                    data-material_number='{$materialNumber}'
                    data-serial_number='{$serialNumber}'
                    data-tipe_barang='{$tipeBarang}'
                    data-lokasi='{$lokasi}'
                    data-owner='{$owner}'
                    data-satuan='{$satuan}'
                    data-deskripsi='{$deskripsi}'>
                    <i class='mdi mdi-eye'></i>
                </button>
            </td>";
        })
        ->addColumn('action', function($item) {
            $editRoute = route('barang.edit', $item->uuid);
            $deleteModal = "<button type='button' title='Delete'
                class='btn btn-gradient-danger btn-rounded btn-icon'
                data-bs-toggle='modal' data-bs-target='#deleteModal'
                data-id='{$item->id}'>
                <i class='mdi mdi-delete'></i>
            </button>";

            $editButton = "<a href='{$editRoute}'>
                <button type='button' title='Edit' class='btn btn-gradient-warning btn-rounded btn-icon'>
                    <i class='mdi mdi-lead-pencil'></i>
                </button>
            </a>";

            return $editButton . $deleteModal;
        })
        ->rawColumns(['detail', 'action']);
    }

    public function query(Barang $model): QueryBuilder
    {
        $query = $model->with(['tipe_barang', 'relasi_area.detail_lokasi'])->newQuery();

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
                    ->setTableId('barang-table')
                    ->columns($this->getColumns())
                    ->minifiedAjax()
                    ->pageLength(50)
                    ->lengthMenu([10, 50, 100, 250, 500, 1000])
                    ->dom('Blfrtip')
                    ->orderBy([0, 'asc'])
                    ->selectStyleSingle()
                    ->buttons([]);
    }

    public function getColumns(): array
    {
        return [
            Column::make('material_number')->title('Material Number'),
            Column::make('name')->title('Name'),
            Column::make('tipe_barang.name')->title('Type')->orderable(false),
            Column::make('relasi_area.detail_lokasi.name')->title('Location')->orderable(false),
            Column::computed('detail')->title('Detail')
                    ->exportable(false)
                    ->printable(false)
                    ->width(30)
                    ->addClass('text-center')
                    ->searchable(false),
            Column::computed('action')
                    ->exportable(false)
                    ->printable(false)
                    ->width(40)
                    ->addClass('text-center'),
        ];
    }

    protected function filename(): string
    {
        return 'Barang_' . date('YmdHis');
    }
}
