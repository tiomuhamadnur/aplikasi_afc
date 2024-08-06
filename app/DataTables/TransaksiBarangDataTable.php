<?php

namespace App\DataTables;

use App\Models\TransaksiBarang;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Support\Facades\Request;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class TransaksiBarangDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addColumn('ticket_number', function($item) {
                $showRoute = route('gangguan.show', $item->gangguan->uuid ?? '-');
                $ticket_number = $item->gangguan->ticket_number ?? '-';

                if($item->gangguan_id == null)
                {
                    return "-";
                }

                return "
                    <td>
                        <a href='{$showRoute}' title='Show Detail'>
                            <button type='button' class='btn btn-gradient-primary btn-rounded'>
                                {$ticket_number}
                            </button>
                        </a>
                    </td>
                ";
            })
            ->addColumn('action', function($item) {
                $editRoute = route('transaksi-barang.edit', $item->uuid);

                return "
                    <td>
                        <a href='{$editRoute}' title='Edit'>
                            <button type='button' class='btn btn-gradient-warning btn-rounded btn-icon'>
                                <i class='text-white mdi mdi-lead-pencil'></i>
                            </button>
                        </a>
                        <button type='button' title='Delete' class='btn btn-gradient-danger btn-rounded btn-icon'
                            data-bs-toggle='modal' data-bs-target='#deleteModal' data-id='{$item->id}'>
                            <i class='mdi mdi-delete'></i>
                        </button>
                    </td>
                ";
            })
            ->rawColumns(['ticket_number', 'action']);
    }

    public function query(TransaksiBarang $model, Request $request): QueryBuilder
    {
        $query = $model->with(['equipment.relasi_area.sub_lokasi', 'gangguan', 'barang', 'user'])->newQuery();

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
                    ->setTableId('transaksibarang-table')
                    ->columns($this->getColumns())
                    ->minifiedAjax()
                    ->pageLength(50)
                    ->lengthMenu([10, 50, 100, 250, 500, 1000])
                    //->dom('Bfrtip')
                    ->orderBy([0, 'desc'])
                    ->selectStyleSingle()
                    ->buttons([]);
    }

    public function getColumns(): array
    {
        return [
            Column::make('tanggal')->title('Tanggal'),
            Column::computed('ticket_number')
                    ->title('Ticket Gangguan')
                    ->exportable(false)
                    ->printable(false)
                    ->width(60)
                    ->addClass('text-center'),
            Column::make('barang.name')->title('Material Name'),
            Column::make('barang.material_number')->title('Material Number'),
            Column::make('qty')->title('Qty.'),
            Column::make('equipment.name')->title('Equipment Name'),
            Column::make('equipment.code')->title('Equipment ID'),
            Column::make('equipment.relasi_area.sub_lokasi.name')->title('Location'),
            Column::make('user.name')->title('Updated by'),
            Column::computed('action')
                    ->exportable(false)
                    ->printable(false)
                    ->width(60)
                    ->addClass('text-center'),
        ];
    }

    protected function filename(): string
    {
        return 'TransaksiBarang_' . date('YmdHis');
    }
}
