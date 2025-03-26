<?php

namespace App\DataTables;

use App\Models\GangguanLM;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class GangguanLMDataTable extends DataTable
{
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
                $photoBeforeUrl = asset('storage/' . $item->photo_before);
                $photoAfterUrl = asset('storage/' . $item->photo_after);
                return "<button type='button' title='Show' class='btn btn-gradient-danger btn-rounded btn-icon'
                        data-bs-toggle='modal' data-bs-target='#photoModal' data-photo_before='{$photoBeforeUrl}' data-photo_after='{$photoAfterUrl}'>
                        <i class='mdi mdi-file-image'></i>
                        </button>";
            })
            // ->addColumn('remedy', function($item) {
            //     if ($item->trans_gangguan_remedy->isNotEmpty()) {
            //         return $item->trans_gangguan_remedy->map(function($transRemedy) {
            //             return $transRemedy->remedy ? $transRemedy->remedy->name : $transRemedy->remedy_other;
            //         })->implode('<br>');
            //     }
            //     return '';
            // })
            // ->addColumn('action_by', function($item) {
            //     return $item->solved_user->name;
            // })
            ->addColumn('is_downtime', function($item) {
                return $item->is_downtime ? 'Yes' : 'No';
            })
            ->addColumn('is_delay', function($item) {
                return $item->is_delay ? 'Yes' : 'No';
            })
            ->addColumn('is_change_sparepart', function($item) {
                return $item->is_change_sparepart ? 'Yes' : 'No';
            })
            ->addColumn('is_change_trainset', function($item) {
                return $item->is_change_trainset ? 'Yes' : 'No';
            })
            ->addColumn('fun_loc', function($item) {
                return $item->equipment->functional_location->code ?? '';
            })
            ->addColumn('#', function($item) {
                $showRoute = route('gangguan.lm.show', $item->uuid);
                $showButton = "<button type='button' class='btn btn-gradient-primary btn-rounded btn-icon'
                    onclick=\"window.location.href='{$showRoute}'\" title='Show Detail'>
                    <i class='text-white mdi mdi-eye'></i>
                </button>";

                if (auth()->user()->role_id == 3) {
                    return $showButton;
                }

                $editRoute = route('gangguan.lm.edit', $item->uuid);

                $editButton = "<button type='button' class='btn btn-gradient-warning btn-rounded btn-icon'
                        onclick=\"window.location.href='{$editRoute}'\" title='Edit'>
                        <i class='text-white mdi mdi-lead-pencil'></i>
                    </button>";

                if (auth()->user()->role_id == 2) {
                    return $showButton . $editButton;
                }

                $deleteModal = "<button type='button' title='Delete'
                    class='btn btn-gradient-danger btn-rounded btn-icon'
                    data-bs-toggle='modal' data-bs-target='#deleteModal'
                    data-id='{$item->id}'>
                    <i class='mdi mdi-delete'></i>
                </button>";

                return $showButton . $editButton . $deleteModal;
            })
            ->rawColumns(['status', 'classification', 'photo', 'is_downtime', 'is_delay', 'is_change_sparepart', 'is_change_trainset', 'fun_loc', '#']);
    }

    public function query(GangguanLM $model): QueryBuilder
    {
        $query = $model->with([
            'report_user',
            'status',
            'category',
            'classification',
            'lintas.detail_lokasi',
            'line.detail_lokasi',
            'equipment',
            'problem',
            'cause',
            // 'trans_gangguan_remedy',
            // 'trans_gangguan_pending',
            'equipment.tipe_equipment',
            'equipment.relasi_area.sub_lokasi',
            'equipment.functional_location',
            'work_order'
            ])->newQuery();

        return $query;
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
                    ->setTableId('gangguanlm-table')
                    ->columns($this->getColumns())
                    ->minifiedAjax()
                    ->pageLength(10)
                    ->lengthMenu([10, 50, 100, 250, 500, 1000])
                    ->dom('frtiplB')
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
            Column::make('report_user.name')->title('Report By'),
            Column::make('report_date')->title('Report Date'),
            Column::make('category.name')->title('Category'),
            Column::computed('classification')
                    ->exportable(true)
                    ->printable(true)
                    ->width(20)
                    ->addClass('text-center')
                    ->searchable(true),
            Column::make('lintas.detail_lokasi.name')->title('Location'),
            Column::make('line.detail_lokasi.name')->title('Line'),
            Column::make('equipment.tipe_equipment.code')->title('Equipment Type'),
            Column::make('equipment.code')->title('Equipment ID'),
            Column::make('equipment.name')->title('Equipment Name'),
            Column::make('problem.name')->title('Problem (P)'),
            Column::make('problem_other')->title('Problem (P)'),
            Column::make('cause.name')->title('Cause (C)'),
            Column::make('cause_other')->title('Cause (C)'),
            // Column::computed('remedy')
            //         ->title('Remedy (R)')
            //         ->exportable(true)
            //         ->printable(true)
            //         ->searchable(true),
            Column::make('equipment.relasi_area.sub_lokasi.name')->title('Station'),
            // Column::make('report_by')->title('Report By'),
            // Column::make('response_date')->title('Action Date'),
            // Column::make('solved_date')->title('Solved Date'),
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
            Column::computed('is_downtime')
                    ->title('Downtime?')
                    ->exportable(true)
                    ->printable(true)
                    ->width(30)
                    ->searchable(true)
                    ->addClass('text-center'),
            Column::computed('is_delay')
                    ->title('Downtime?')
                    ->exportable(true)
                    ->printable(true)
                    ->width(30)
                    ->searchable(true)
                    ->addClass('text-center'),
            Column::computed('is_change_sparepart')
                    ->title('Change Sparepart?')
                    ->exportable(true)
                    ->printable(true)
                    ->width(30)
                    ->searchable(true)
                    ->addClass('text-center'),
            Column::computed('is_change_trainset')
                    ->title('Change Trainset?')
                    ->exportable(true)
                    ->printable(true)
                    ->width(30)
                    ->searchable(true)
                    ->addClass('text-center'),
            Column::make('remark')->title('Remark'),
            Column::make('analysis')->title('Analysis'),
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
        return date('Ymd') . '_Data Failure Report_Light Maintenance';
    }
}
