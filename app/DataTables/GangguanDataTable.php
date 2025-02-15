<?php

namespace App\DataTables;

use App\Models\Gangguan;
use Carbon\Carbon;
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
    protected $is_downtime;


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
                return "<button type='button' title='Show' class='btn btn-gradient-danger btn-rounded btn-icon'
                        data-bs-toggle='modal' data-bs-target='#photoModal' data-photo='{$photoUrl}' data-photo_after='{$photoAfterUrl}'>
                        <i class='mdi mdi-file-image'></i>
                        </button>";
            })
            ->addColumn('remedy', function($item) {
                if ($item->trans_gangguan_remedy->isNotEmpty()) {
                    return $item->trans_gangguan_remedy->map(function($transRemedy) {
                        return $transRemedy->remedy ? $transRemedy->remedy->name : $transRemedy->remedy_other;
                    })->implode('<br>');
                }
                return '';
            })
            ->addColumn('action_by', function($item) {
                return $item->solved_user->name;
            })
            ->addColumn('is_changed', function($item) {
                return $item->is_changed ? 'Yes' : 'No';
            })
            ->addColumn('is_downtime', function($item) {
                return $item->is_downtime ? 'Yes' : 'No';
            })
            ->addColumn('fun_loc', function($item) {
                return $item->equipment->functional_location->code ?? '';
            })
            ->addColumn('#', function($item) {
                $showRoute = route('gangguan.show', $item->uuid);
                $showButton = "<button type='button' class='btn btn-gradient-primary btn-rounded btn-icon'
                    onclick=\"window.location.href='{$showRoute}'\" title='Show Detail'>
                    <i class='text-white mdi mdi-eye'></i>
                </button>";

                if (auth()->user()->role_id != 1) {
                    return $showButton;
                }

                $editRoute = route('gangguan.edit', $item->uuid);

                $editButton = "<button type='button' class='btn btn-gradient-warning btn-rounded btn-icon'
                        onclick=\"window.location.href='{$editRoute}'\" title='Edit'>
                        <i class='text-white mdi mdi-lead-pencil'></i>
                    </button>";

                $createWorkOrderButton = '';

                if($item->work_order_id == null)
                {
                $createWorkOrderRoute = route('work-order.create.from-gangguan', $item->uuid);

                $createWorkOrderButton = "<button type='button' class='btn btn-gradient-info btn-rounded btn-icon'
                        onclick=\"window.location.href='{$createWorkOrderRoute}'\" title='Create Work Order'>
                        <i class='text-white mdi mdi-briefcase-upload'></i>
                    </button>";
                }

                $deleteModal = "<button type='button' title='Delete'
                    class='btn btn-gradient-danger btn-rounded btn-icon'
                    data-bs-toggle='modal' data-bs-target='#deleteModal'
                    data-id='{$item->id}'>
                    <i class='mdi mdi-delete'></i>
                </button>";

                return $showButton . $createWorkOrderButton . $editButton . $deleteModal;
            })
            ->rawColumns(['status', 'classification', 'remedy','action_by', 'photo', 'is_changed', 'is_downtime', 'fun_loc', '#']);
    }

    public function query(Gangguan $model): QueryBuilder
    {
        $query = $model->with([
            'status',
            'category',
            'classification',
            'equipment',
            'problem',
            'cause',
            'trans_gangguan_remedy',
            'trans_gangguan_pending',
            'equipment.tipe_equipment',
            'equipment.relasi_area.sub_lokasi',
            'equipment.functional_location',
            'work_order'
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

        if ($this->start_date != null && $this->end_date != null) {
            $clean_start_date = explode('?', $this->start_date)[0];
            $clean_end_date = explode('?', $this->end_date)[0];

            $start = Carbon::parse($clean_start_date)->startOfDay()->format('Y-m-d H:i:s');
            $end = Carbon::parse($clean_end_date)->endOfDay()->format('Y-m-d H:i:s');

            $query->whereBetween('report_date', [$start, $end]);
        }

        if($this->is_changed != null)
        {
            $query->where('is_changed', $this->is_changed);
        }

        if($this->is_downtime != null)
        {
            $query->where('is_downtime', $this->is_downtime);
        }

        return $query;
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
                    ->setTableId('gangguan-table')
                    ->columns($this->getColumns())
                    ->minifiedAjax()
                    ->pageLength(10)
                    ->lengthMenu([10, 50, 100, 250, 500, 1000])
                    ->dom('frtiplB')
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
            Column::computed('#')
                    ->exportable(false)
                    ->printable(false)
                    ->width(30)
                    ->addClass('text-center'),
            Column::make('ticket_number')
                    ->title('Ticket Number')
                    ->exportable(true)
                    ->printable(true)
                    ->addClass('text-center')
                    ->searchable(true),
            Column::make('report_date')->title('Report Date'),
            Column::make('equipment.tipe_equipment.code')->title('Equipment Type'),
            Column::make('equipment.code')->title('Equipment ID'),
            // Column::make('problem.name')->title('Problem (P)'),
            Column::make('problem_other')->title('Problem (P)'),
            // Column::make('cause.name')->title('Cause (C)'),
            Column::make('cause_other')->title('Cause (C)'),
            Column::computed('remedy')
                    ->title('Remedy (R)')
                    ->exportable(true)
                    ->printable(true)
                    ->searchable(true),
            Column::computed('classification')
                    ->exportable(true)
                    ->printable(true)
                    ->width(20)
                    ->addClass('text-center')
                    ->searchable(true),
            Column::make('equipment.relasi_area.sub_lokasi.name')->title('Station'),
            Column::make('category.name')->title('Category'),
            Column::make('report_by')->title('Report By'),
            Column::computed('action_by')
                    ->title('Action By')
                    ->exportable(true)
                    ->printable(true)
                    ->searchable(true),
            Column::make('response_date')->title('Action Date'),
            Column::make('solved_date')->title('Solved Date'),
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
            Column::computed('is_downtime')
                    ->title('Downtime?')
                    ->exportable(true)
                    ->printable(true)
                    ->width(30)
                    ->searchable(true)
                    ->addClass('text-center'),
            Column::make('remark')->title('Remark'),
            Column::make('response_time')->title('Response Time (Min)'),
            Column::make('resolution_time')->title('Resolution Time (Min)'),
            Column::make('total_time')->title('Total Time (Min)'),
            Column::computed('fun_loc')
                    ->title('Functional Location')
                    ->exportable(true)
                    ->printable(true)
                    ->addClass('text-center'),
            Column::make('work_order.ticket_number')->title('Work Order'),
        ];
    }

    protected function filename(): string
    {
        return date('Ymd') . '_Data Failure Report';
    }
}
