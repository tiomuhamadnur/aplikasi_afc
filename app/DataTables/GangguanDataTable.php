<?php

namespace App\DataTables;

use App\Models\Gangguan;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Support\Facades\Request;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class GangguanDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addColumn('classification', function($item) {
                $badgeClass = '';
                if ($item->classification == 'minor') {
                    $badgeClass = 'badge-gradient-success';
                } elseif ($item->classification == 'moderate') {
                    $badgeClass = 'badge-gradient-warning';
                } else {
                    $badgeClass = 'badge-gradient-danger';
                }
                return "<label class='badge {$badgeClass} text-uppercase'>{$item->classification}</label>";
            })
            ->addColumn('status', function($item) {
                $badgeClass = '';
                if ($item->status == 'closed') {
                    $badgeClass = 'badge-gradient-success';
                } elseif ($item->status == 'pending') {
                    $badgeClass = 'badge-gradient-warning';
                } else {
                    $badgeClass = 'badge-gradient-danger';
                }
                return "<label class='badge {$badgeClass} text-uppercase'>{$item->status}</label>";
            })
            ->addColumn('photo', function($item) {
                $photoUrl = asset('storage/' . $item->photo);
                $photoAfterUrl = asset('storage/' . $item->photo_after);
                return "<button type='button' title='Show' class='btn btn-gradient-primary btn-rounded btn-icon'
                        data-bs-toggle='modal' data-bs-target='#photoModal' data-photo='{$photoUrl}' data-photo_after='{$photoAfterUrl}'>
                        <i class='mdi mdi-eye'></i>
                        </button>";
            })
            ->addColumn('ticket_number', function($item) {
                return "<span class='fw-bolder'>{$item->ticket_number}</span>";
            })
            ->addColumn('is_changed', function($item) {
                return $item->is_changed ? 'Yes' : 'No';
            })
            ->addColumn('#', function($item) {
                $editRoute = route('gangguan.edit', $item->uuid);
                $deleteModal = "<button type='button' title='Delete'
                    class='btn btn-gradient-danger btn-rounded btn-icon'
                    data-bs-toggle='modal' data-bs-target='#deleteModal'
                    data-id='{$item->id}'>
                    <i class='mdi mdi-delete'></i>
                </button>";

                $editButton = "<a href='{$editRoute}' title='Edit'>
                    <button type='button' class='btn btn-gradient-warning btn-rounded btn-icon'>
                        <i class='text-white mdi mdi-lead-pencil'></i>
                    </button>
                </a>";

                return $editButton . $deleteModal;
            })
            ->rawColumns(['ticket_number', 'status', 'classification', 'photo', 'is_changed', '#']);
    }

    public function query(Gangguan $model, Request $request): QueryBuilder
    {
        $query = $model->with(['equipment', 'equipment.tipe_equipment', 'equipment.relasi_area.sub_lokasi'])->newQuery();

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
                    ->setTableId('gangguan-table')
                    ->columns($this->getColumns())
                    ->minifiedAjax()
                    ->pageLength(50)
                    ->lengthMenu([10, 50, 100, 250, 500, 1000])
                    //->dom('Bfrtip')
                    ->orderBy([2, 'desc'])
                    ->selectStyleSingle()
                    ->buttons([]);
    }

    public function getColumns(): array
    {
        return [
            Column::make('ticket_number')->title('Ticket Number'),
            Column::make('equipment.relasi_area.sub_lokasi.name')->title('Station'),
            Column::make('report_date')->title('Report Date'),
            Column::make('report_by')->title('Report By'),
            Column::make('equipment.tipe_equipment.code')->title('Equipment Type'),
            Column::make('equipment.code')->title('Equipment ID'),
            Column::make('problem')->title('Problem'),
            Column::make('category')->title('Category'),
            Column::make('action')->title('Action'),
            Column::make('response_date')->title('Action Date'),
            Column::make('solved_by')->title('Action By'),
            Column::make('solved_date')->title('Solved Date'),
            Column::make('analysis')->title('Analysis'),
            Column::computed('classification')
                    ->exportable(true)
                    ->printable(false)
                    ->width(20)
                    ->addClass('text-center')
                    ->searchable(true),
            Column::computed('status')
                    ->exportable(true)
                    ->printable(false)
                    ->width(20)
                    ->addClass('text-center')
                    ->searchable(true),
            Column::computed('photo')
                    ->exportable(false)
                    ->printable(false)
                    ->width(30)
                    ->addClass('text-center')
                    ->searchable(true),
            Column::computed('is_changed')
                    ->title('Changed Sparepart?')
                    ->exportable(true)
                    ->printable(false)
                    ->width(30)
                    ->searchable(true)
                    ->addClass('text-center'),
            Column::computed('#')
                    ->exportable(false)
                    ->printable(false)
                    ->width(30)
                    ->addClass('text-center'),
        ];
    }

    protected function filename(): string
    {
        return 'Gangguan_' . date('YmdHis');
    }
}
