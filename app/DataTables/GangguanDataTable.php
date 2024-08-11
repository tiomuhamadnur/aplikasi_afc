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
    protected $area_id;
    protected $category_id;
    protected $equipment_id;
    protected $tipe_equipment_id;
    protected $classification_id;
    protected $status_id;
    protected $start_date;
    protected $end_date;
    protected $is_changed;


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
            ->addColumn('classification', function($item) {
                $badgeClass = '';
                if ($item->classification_id == 1) {
                    $badgeClass = 'badge-gradient-success';
                } elseif ($item->classification_id == 2) {
                    $badgeClass = 'badge-gradient-warning';
                } else {
                    $badgeClass = 'badge-gradient-danger';
                }
                return "<label class='badge {$badgeClass} text-uppercase'>{$item->classification->name}</label>";
            })
            ->addColumn('status', function($item) {
                $badgeClass = '';
                if ($item->status_id == 2) {
                    $badgeClass = 'badge-gradient-success';
                } elseif ($item->status_id == 3) {
                    $badgeClass = 'badge-gradient-warning';
                } elseif ($item->status_id == 4) {
                    $badgeClass = 'badge-gradient-info';
                } else {
                    $badgeClass = 'badge-gradient-danger';
                }
                return "<label class='badge {$badgeClass} text-uppercase'>{$item->status->name}</label>";
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

    public function query(Gangguan $model): QueryBuilder
    {
        $query = $model->with([
            'status',
            'category',
            'classification',
            'equipment',
            'equipment.tipe_equipment',
            'equipment.relasi_area.sub_lokasi'
            ])->newQuery();

        // Filter
        if($this->area_id != null)
        {
            $query->whereRelation('equipment.relasi_area', 'id', '=', $this->area_id);
        }

        if($this->category_id != null)
        {
            $query->where('category_id', $this->category_id);
        }

        if($this->tipe_equipment_id != null)
        {
            $query->whereRelation('equipment.tipe_equipment', 'id', '=', $this->tipe_equipment_id);
        }

        if($this->classification_id != null)
        {
            $query->where('classification_id', $this->classification_id);
        }

        if($this->status_id != null)
        {
            $query->where('status_id', $this->status_id);
        }

        if($this->start_date != null && $this->end_date != null)
        {
            $query->whereBetween('tanggal', [$this->start_date, $this->end_date]);
        }

        if($this->is_changed != null)
        {
            $query->where('is_changed', $this->is_changed);
        }

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
            Column::make('ticket_number')->title('Ticket Number'),
            Column::make('equipment.relasi_area.sub_lokasi.name')->title('Station'),
            Column::make('report_date')->title('Report Date'),
            Column::make('report_by')->title('Report By'),
            Column::make('equipment.tipe_equipment.code')->title('Equipment Type'),
            Column::make('equipment.code')->title('Equipment ID'),
            Column::make('problem')->title('Problem'),
            Column::make('category.name')->title('Category'),
            Column::make('action')->title('Action'),
            Column::make('response_date')->title('Action Date'),
            Column::make('solved_by')->title('Action By'),
            Column::make('solved_date')->title('Solved Date'),
            Column::make('analysis')->title('Analysis'),
            Column::computed('classification')
                    ->exportable(true)
                    ->printable(true)
                    ->width(20)
                    ->addClass('text-center')
                    ->searchable(true),
            Column::computed('status')
                    ->exportable(true)
                    ->printable(true)
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
                    ->printable(true)
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
        return date('Ymd') . '_Data Gangguan';
    }
}
