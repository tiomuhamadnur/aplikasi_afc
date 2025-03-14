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

class BudgetAbsorptionDataTable extends DataTable
{
    protected $fund_id;
    protected $project_id;
    protected $departemen_id;
    protected $type;
    protected $status;
    protected $start_date;
    protected $end_date;


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
            if($item->attachment != null)
            {
                $attachmentRoute = asset('storage/' . $item->attachment);
                $attachmentButton = "<button type='button' title='Attachment'
                    class='btn btn-gradient-success btn-rounded btn-icon'
                    onclick=\"window.open('{$attachmentRoute}', '_blank')\">
                    <i class='mdi mdi-file-pdf'></i>
                </button>";
            } else {
                $attachmentButton = '';
            }

            $editRoute = route('budget-absorption.edit', $item->uuid);

            $editButton = "<button type='button' class='btn btn-gradient-warning btn-rounded btn-icon'
                    onclick=\"window.location.href='{$editRoute}'\" title='Edit'>
                    <i class='text-white mdi mdi-lead-pencil'></i>
                </button>";

            if (auth()->user()->role_id == 2) {
                return $attachmentButton . $editButton;
            }

            $deleteModal = "<button type='button' title='Delete'
                class='btn btn-gradient-danger btn-rounded btn-icon'
                data-bs-toggle='modal' data-bs-target='#deleteModal'
                data-id='{$item->id}'>
                <i class='mdi mdi-delete'></i>
            </button>";

            return $attachmentButton . $editButton . $deleteModal;
        })
        ->addColumn('rka', function($item) {
            return RupiahFormat::currency($item->project->fund_source->balance);
        })
        ->addColumn('project_value', function($item) {
            $project_value = Project::findOrFail(  $item->project->id)->value;
            return RupiahFormat::currency($project_value);
        })
        ->addColumn('value', function($item) {
            return RupiahFormat::currency($item->value);
        })
        ->rawColumns(['#']);
    }

    public function query(BudgetAbsorption $model): QueryBuilder
    {
        $query = $model->with([
            'project',
            'project.fund_source.fund',
            'project.fund_source',
            'project.departemen',
            'user'
            ])->orderBy('project_id', 'DESC')->newQuery();

        // Filter
        if($this->fund_id != null)
        {
            $query->whereRelation('project.fund_source.fund', 'id', '=', $this->fund_id);
        }

        if($this->project_id != null)
        {
            $query->where('project_id', $this->project_id);
        }

        if($this->departemen_id != null)
        {
            $query->whereRelation('project.departemen', 'id', '=', $this->departemen_id);
        }

        if($this->type != null)
        {
            $query->whereRelation('project.fund_source.fund', 'type', '=', $this->type);
        }

        if($this->status != null)
        {
            $query->where('status', $this->status);
        }

        if($this->start_date != null && $this->end_date != null)
        {
            $query->whereBetween('activity_date', [$this->start_date, $this->end_date]);
        }

        return $query->orderBy('activity_date', 'DESC');
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
                    ->setTableId('budgetabsorption-table')
                    ->columns($this->getColumns())
                    ->minifiedAjax()
                    ->pageLength(10)
                    ->lengthMenu([10, 50, 100, 250, 500, 1000])
                    ->dom('frtiplB')
                    // ->orderBy([14, 'desc'])
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
            Column::make('project.fund_source.fund.code')->title('Fund')->sortable(false),
            Column::computed('rka')->title('RKA Budget')->sortable(false),
            Column::make('project.fund_source.fund.type')->title('Type')->sortable(false),
            Column::make('project.name')->title('Project Name')->sortable(false),
            Column::computed('project_value')->title('Project value')->sortable(false),
            Column::make('name')->title('Activity Name')->sortable(false),
            // Column::make('description')->title('Description'),
            Column::computed('value')->title('Activity Value')->sortable(false),
            Column::make('activity_date')->sortable(true)->title('Activity Date')->sortable(false),
            Column::make('paid_date')->title('Paid Date')->sortable(false),
            Column::make('po_number_sap')->title('PO Number SAP')->sortable(false),
            Column::make('project.departemen.code')->title('Department')->sortable(false),
            Column::make('status')->title('Status')->sortable(false),
            // Column::make('termin')->title('Termin'),
            Column::make('user.name')->title('Updated By')->sortable(false),
            Column::make('updated_at')->title('Updated At')->sortable(false),
        ];
    }

    protected function filename(): string
    {
        return date('Ymd') . '_Data Budget Absorption';
    }
}
