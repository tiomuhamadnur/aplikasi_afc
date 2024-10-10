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
    protected $start_date;
    protected $end_date;
    protected $tipe_equipment_id;

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
            ->addColumn('work_order', function($item) {
                if($item->work_order_id == null)
                {
                    return "";
                }

                $showRoute = route('work-order.detail',  $item->work_order->uuid);
                $work_order = $item->work_order->ticket_number ?? '-';

                return "
                    <button type='button' class='btn btn-gradient-primary btn-rounded p-2'
                        onclick=\"window.location.href='{$showRoute}'\" title='Show Detail Work Order'>
                        {$work_order}
                    </button>
                ";
            })
            ->addColumn('ticket_number', function($item) {
                $showRoute = route('gangguan.show', $item->gangguan->uuid ?? '-');
                $ticket_number = $item->gangguan->ticket_number ?? '-';

                if($item->gangguan_id == null)
                {
                    return "";
                }

                return "
                    <td>
                        <button type='button' class='btn btn-gradient-success btn-rounded p-2'
                            onclick=\"window.location.href='{$showRoute}'\" title='Show Detail Gangguan'>
                            {$ticket_number}
                        </button>
                    </td>
                ";
            })
            ->addColumn('#', function($item) {
                if (auth()->user()->role_id != 1) {
                    return '';
                }
                $editRoute = route('transaksi-barang.edit', $item->uuid);

                return "
                    <td>
                        <button type='button' class='btn btn-gradient-warning btn-rounded btn-icon'
                        onclick=\"window.location.href='{$editRoute}'\" title='Edit'>
                        <i class='text-white mdi mdi-lead-pencil'></i>
                        </button>
                        <button type='button' title='Delete' class='btn btn-gradient-danger btn-rounded btn-icon'
                            data-bs-toggle='modal' data-bs-target='#deleteModal' data-id='{$item->id}'>
                            <i class='mdi mdi-delete'></i>
                        </button>
                    </td>
                ";
            })
            ->rawColumns(['work_order', 'ticket_number', '#']);
    }

    public function query(TransaksiBarang $model): QueryBuilder
    {
        $query = $model->with(['equipment.relasi_area.sub_lokasi', 'gangguan', 'work_order', 'barang', 'user'])->newQuery();

        if($this->start_date != null && $this->end_date != null)
        {
            $query->whereBetween('tanggal', [$this->start_date, $this->end_date]);
        }

        if($this->tipe_equipment_id != null)
        {
            $query->whereRelation('equipment.tipe_equipment', 'id', '=', $this->tipe_equipment_id);
        }

        return $query;
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
                    ->setTableId('transaksibarang-table')
                    ->columns($this->getColumns())
                    ->minifiedAjax()
                    ->pageLength(10)
                    ->lengthMenu([10, 50, 100, 250, 500, 1000])
                    //->dom('Bfrtip')
                    ->orderBy([0, 'desc'])
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
            Column::make('tanggal')->title('Tanggal'),
            Column::computed('ticket_number')
                    ->title('Ticket Gangguan')
                    ->exportable(true)
                    ->printable(false)
                    ->searchable(true)
                    ->width(60)
                    ->addClass('text-center'),
            Column::computed('work_order')
                    ->title('Work Order')
                    ->exportable(true)
                    ->printable(false)
                    ->searchable(true)
                    ->width(60)
                    ->addClass('text-center'),
            Column::make('barang.name')->title('Material Name'),
            Column::make('barang.material_number')->title('Material Number'),
            Column::make('qty')->title('Qty.'),
            Column::make('equipment.name')->title('Equipment Name'),
            Column::make('equipment.code')->title('Equipment ID'),
            Column::make('equipment.relasi_area.sub_lokasi.name')->title('Location'),
            Column::make('user.name')->title('Updated by'),
            Column::computed('#')
                    ->exportable(false)
                    ->printable(false)
                    ->width(60)
                    ->addClass('text-center'),
        ];
    }

    protected function filename(): string
    {
        return date('Ymd') . '_Data Transaksi Saparepart';
    }
}
