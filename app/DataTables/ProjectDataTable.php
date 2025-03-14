<?php

namespace App\DataTables;

use App\Models\BudgetAbsorption;
use App\Models\Project;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Laraindo\RupiahFormat;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class ProjectDataTable extends DataTable
{
    protected $fund_source_id;
    protected $departemen_id;
    protected $type;
    protected $status_budgeting_id;
    protected $year;

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
            $showRoute = route('budget-absorption.by_project.show', $item->uuid);
            $showButton = "<button type='button' class='btn btn-gradient-primary btn-rounded btn-icon'
                    onclick=\"window.location.href='{$showRoute}'\" title='Show'>
                <i class='text-white mdi mdi-eye'></i>
                </button>";

            $editRoute = route('project.edit', $item->uuid);

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
        ->addColumn('rka', function($item) {
            $rka = $item->fund_source->balance ?? null;
            return RupiahFormat::currency($rka);
        })
        ->addColumn('value', function($item) {
            return RupiahFormat::currency($item->value);
        })
        ->addColumn('project_value', function($item) {
            return RupiahFormat::currency($item->value ?? 0);
        })
        ->addColumn('absorbed_budget', function($item) {
            $sum_value_absorption = BudgetAbsorption::where('project_id',  $item->id)->sum('value');
            return RupiahFormat::currency($sum_value_absorption);
        })
        ->addColumn('remaining_budget', function($item) {
            $sum_value_absorption = BudgetAbsorption::where('project_id',  $item->id)->sum('value');
            return RupiahFormat::currency($item-> value - $sum_value_absorption);
        })
        ->addColumn('updated_at', function($item) {
            return Carbon::parse($item->updated_at)->format('Y-m-d H:i:s');
        })
        ->rawColumns(['#', 'updated_at']);
    }

    public function query(Project $model): QueryBuilder
    {
        $query = $model->with(['fund_source', 'fund_source.fund', 'perusahaan', 'departemen', 'user', 'status_budgeting'])
                    ->newQuery();

        // Fund Source
        if($this->fund_source_id != null)
        {
            $query->whereRelation('fund_source', 'id', '=', $this->fund_source_id);
        }

        // Departemen
        if($this->departemen_id != null)
        {
            $query->whereRelation('departemen', 'id', '=', $this->departemen_id);
        }

        // Type
        if($this->type != null)
        {
            $query->whereRelation('fund_source.fund', 'type', '=', $this->type);
        }

        // Status Budgeting
        if($this->status_budgeting_id != null)
        {
            $query->where('status_budgeting_id', $this->status_budgeting_id);
        }

        // Year
        if($this->year != null)
        {
            $query->whereRelation('fund_source', 'year', '=', $this->year);
        }

        return $query->orderBy('fund_source_id', 'ASC');
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
                    ->setTableId('project-table')
                    ->columns($this->getColumns())
                    ->minifiedAjax()
                    ->pageLength(10)
                    ->lengthMenu([10, 50, 100, 250, 500, 1000])
                    ->dom('frtiplB')
                    // ->orderBy([6, 'desc'])
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
            Column::make('fund_source.fund.code')->title('Fund')->sortable(false),
            Column::computed('rka')->title('RKA Budget'),
            Column::make('fund_source.fund.type')->title('Type')->sortable(false),
            Column::make('name')->title('Project Name')->sortable(false),
            Column::computed('project_value')->title('Project Value')->sortable(false),
            Column::computed('absorbed_budget')->title('Absorbed Budget')->sortable(false),
            Column::computed('remaining_budget')->title('Remaining Budget')->sortable(false),
            // Column::make('description')->title('Description'),
            // Column::make('start_period')->title('Start Period'),
            // Column::make('end_period')->title('End Period'),
            Column::make('fund_source.year')->title('Year')->sortable(false),
            Column::make('departemen.code')->title('Department')->sortable(false),
            Column::make('perusahaan.name')->title('Company')->sortable(false),
            Column::make('status_budgeting.name')->title('Status')->sortable(false),
            Column::make('user.name')->title('Updated By')->sortable(false),
            Column::computed('updated_at')->title('Updated At'),
            // Column::computed('#')->exportable(false)->printable(false)->width(60)->addClass('text-center'),
        ];
    }

    protected function filename(): string
    {
        return date('Ymd') . '_Data Project';
    }
}
