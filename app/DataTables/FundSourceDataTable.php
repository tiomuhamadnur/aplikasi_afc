<?php

namespace App\DataTables;

use App\Models\BudgetAbsorption;
use App\Models\FundSource;
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

class FundSourceDataTable extends DataTable
{
    protected $year;
    protected $fund_id;

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
            $editModal = "<button type='button' title='Edit'
                class='btn btn-gradient-warning btn-rounded btn-icon'
                data-bs-toggle='modal' data-bs-target='#editModal'
                data-id='{$item->id}'
                data-fund_id='{$item->fund_id}'
                data-balance='{$item->balance}'
                data-year='{$item->year}'>
                <i class='mdi mdi-lead-pencil'></i>
            </button>";

            $deleteModal = "<button type='button' title='Delete'
                class='btn btn-gradient-danger btn-rounded btn-icon'
                data-bs-toggle='modal' data-bs-target='#deleteModal'
                data-id='{$item->id}'>
                <i class='mdi mdi-delete'></i>
            </button>";

            return $editModal . $deleteModal;
        })
        ->addColumn('balance', function($item) {
            return RupiahFormat::currency($item->balance ?? null);
        })
        ->addColumn('used_balance', function($item) {
            $sum_value_absorption = BudgetAbsorption::whereRelation('project.fund_source',  'id', '=', $item->id)->sum('value');
            return RupiahFormat::currency($sum_value_absorption);
        })
        ->addColumn('remaining_balance', function($item) {
            $balance = $item->balance ?? null;
            $sum_value_absorption = BudgetAbsorption::whereRelation('project.fund_source',  'id', '=', $item->id)->sum('value');
            return RupiahFormat::currency($balance - $sum_value_absorption);
        })
        ->rawColumns(['#']);
    }

    public function query(FundSource $model): QueryBuilder
    {
        $query = $model->with([
            'fund',
            'user'
            ])->newQuery();

        // Filter
        if($this->year != null)
        {
            $query->where('year', $this->year);
        }

        if($this->fund_id != null)
        {
            $query->where('fund_id', $this->fund_id);
        }

        return $query;
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
                    ->setTableId('fundsource-table')
                    ->columns($this->getColumns())
                    ->minifiedAjax()
                    ->pageLength(10)
                    ->lengthMenu([10, 50, 100, 250, 500, 1000])
                    ->dom('frtiplB')
                    ->orderBy([9, 'desc'])
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
            Column::make('fund.code')->title('Fund Code'),
            Column::make('fund.type')->title('Type'),
            Column::make('fund.name')->title('Fund Name'),
            Column::computed('balance')->title('Total Balance'),
            Column::computed('used_balance')->title('Used Balance'),
            Column::computed('remaining_balance')->title('Remaining Balance'),
            // Column::make('start_period')->title('Start Period'),
            // Column::make('end_period')->title('End Period'),
            Column::make('year')->title('Year'),
            Column::make('user.name')->sortable(false)->title('Updated By'),
            Column::make('updated_at')->sortable(true)->title('Updated At'),
        ];
    }

    protected function filename(): string
    {
        return date('Ymd') . '_Data Fund Source';
    }
}
