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
    protected $hari_ini;
    protected $fund_source_id;
    protected $departemen_id;
    protected $type;
    protected $start_period;
    protected $end_period;

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
        ->addColumn('detail', function($item) {
            $showRoute = route('budget-absorption.by_project.show', $item->uuid);
            $showButton = "<button type='button' class='btn btn-gradient-primary btn-rounded btn-icon'
                    onclick=\"window.location.href='{$showRoute}'\" title='Show'>
                <i class='text-white mdi mdi-eye'></i>
                </button>";

            return $showButton;
        })
        ->addColumn('#', function($item) {
            $editRoute = route('project.edit', $item->uuid);

            $editButton = "<button type='button' class='btn btn-gradient-warning btn-rounded btn-icon'
                    onclick=\"window.location.href='{$editRoute}'\" title='Edit'>
                    <i class='text-white mdi mdi-lead-pencil'></i>
                </button>";

            $deleteModal = "<button type='button' title='Delete'
                class='btn btn-gradient-danger btn-rounded btn-icon'
                data-bs-toggle='modal' data-bs-target='#deleteModal'
                data-id='{$item->id}'>
                <i class='mdi mdi-delete'></i>
            </button>";

            return $editButton . $deleteModal;
        })
        ->addColumn('rka', function($item) {
            $rka = $item->fund_source->balance ?? null;
            return RupiahFormat::currency($rka);
        })
        ->addColumn('value', function($item) {
            return RupiahFormat::currency($item->value);
        })
        ->addColumn('absorbed_budget', function($item) {
            $sum_value_absorption = BudgetAbsorption::where('project_id',  $item->id)->sum('value');
            return RupiahFormat::currency($sum_value_absorption);
        })
        ->addColumn('updated_at', function($item) {
            return Carbon::parse($item->updated_at)->format('Y-m-d H:i:s');
        })
        ->rawColumns(['detail', 'updated_at', '#']);
    }

    public function query(Project $model): QueryBuilder
    {
        $query = $model->with(['fund_source', 'fund_source.fund', 'perusahaan', 'departemen', 'user', 'status_budgeting'])
                    // ->where('departemen_id', auth()->user()->relasi_struktur->departemen_id)
                    // ->whereRelation('fund_source', 'start_period', '<=', $this->hari_ini)
                    // ->whereRelation('fund_source', 'end_period', '>=', $this->hari_ini)
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

        // Date
        if ($this->start_period != null && $this->end_period != null) {
            $query->where(function ($query) {
                $query->whereBetween('start_period', [$this->start_period, $this->end_period])
                    ->orWhereBetween('end_period', [$this->start_period, $this->end_period])
                    ->orWhere(function ($query) {
                        $query->where('start_period', '<=', $this->start_period)
                            ->where('end_period', '>=', $this->end_period);
                    });
            });
        }


        return $query;
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
                    ->setTableId('project-table')
                    ->columns($this->getColumns())
                    ->minifiedAjax()
                    ->pageLength(10)
                    ->lengthMenu([10, 50, 100, 250, 500, 1000])
                    //->dom('Bfrtip')
                    ->orderBy([6, 'desc'])
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
            Column::computed('detail')
                    ->exportable(false)
                    ->printable(false)
                    ->width(60)
                    ->addClass('text-center'),
            Column::make('fund_source.fund.code')->title('Fund'),
            Column::make('fund_source.fund.type')->title('Type'),
            Column::computed('rka')->title('RKA Budget'),
            Column::make('name')->title('Project Name'),
            // Column::make('description')->title('Description'),
            Column::computed('absorbed_budget')->title('Absorbed Budget'),
            Column::make('start_period')->title('Start Period'),
            Column::make('end_period')->title('End Period'),
            Column::make('departemen.code')->title('Department'),
            Column::make('perusahaan.name')->title('Company'),
            Column::make('status_budgeting.name')->title('Status'),
            Column::make('user.name')->title('Updated By'),
            Column::computed('updated_at')->title('Updated At'),
            Column::computed('#')
                    ->exportable(false)
                    ->printable(false)
                    ->width(60)
                    ->addClass('text-center'),
        ];
    }

    protected function filename(): string
    {
        return date('Ymd') . '_Data Project';
    }
}
