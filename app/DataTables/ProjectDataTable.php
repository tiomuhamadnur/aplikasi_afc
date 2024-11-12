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
        ->addColumn('value', function($item) {
            return RupiahFormat::currency($item->value);
        })
        ->addColumn('current_value', function($item) {
            $sum_value_absorption = BudgetAbsorption::where('project_id',  $item->id)->sum('value');
            return RupiahFormat::currency($sum_value_absorption);
        })
        ->addColumn('updated_at', function($item) {
            return Carbon::parse($item->updated_at)->format('Y-m-d H:i:s');
        })
        ->rawColumns(['updated_at', '#']);
    }

    public function query(Project $model): QueryBuilder
    {
        $query = $model->with(['fund_source.fund', 'perusahaan', 'departemen', 'user'])
                    ->where('departemen_id', auth()->user()->relasi_struktur->departemen_id)
                    ->newQuery();

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
                    ->orderBy([5, 'desc'])
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
            Column::make('name')->title('Project Name'),
            Column::make('description')->title('Description'),
            Column::make('fund_source.fund.code')->title('Fund'),
            Column::computed('value')->title('Value (IDR)'),
            Column::computed('current_value')->title('Current Value (IDR)'),
            Column::make('start_period')->title('Start Period'),
            Column::make('end_period')->title('End Period'),
            Column::make('perusahaan.name')->title('Company'),
            Column::make('departemen.code')->title('Department'),
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
