<?php

namespace App\DataTables;

use App\Models\Checksheet;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class ChecksheetDataTable extends DataTable
{
    protected $uuid_equipment;
    protected $parameters;

    public function setUuidEquipment($uuid_equipment)
    {
        $this->uuid_equipment = $uuid_equipment;
        return $this;
    }

    public function setParameters($parameters)
    {
        $this->parameters = $parameters;
        return $this;
    }

    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        $parameters = $this->parameters ?? [];

        return (new EloquentDataTable($query))
            ->addColumn('date', function ($row) {
                return $row->work_order->date ?? 'N/A'; // Handle if date is null
            })
            ->addColumn('parameters', function ($row) use ($parameters) {
                $values = [];
                foreach ($parameters as $parameter) {
                    $checksheetItem = $row->where('parameter_id', $parameter->id)->first();
                    $values[$parameter->name] = $checksheetItem ? $checksheetItem->value : '-';
                }
                return implode(' | ', $values); // Format sesuai kebutuhan
            })
            ->addColumn('action', 'checksheet.action')
            ->setRowId('id');
    }

    public function query(Checksheet $model): QueryBuilder
    {
        return $model->newQuery()
            ->whereHas('equipment', function ($query) {
                $query->where('uuid', $this->uuid_equipment);
            })
            ->whereHas('parameter', function ($query) {
                $query->whereNull('deleted_at');
            })
            ->with(['parameter', 'work_order'])
            ->orderBy('work_order.date', 'asc');
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
                    ->setTableId('checksheet-table')
                    ->columns($this->getColumns())
                    ->minifiedAjax()
                    ->dom('Blfrtip')
                    ->orderBy(1)
                    ->selectStyleSingle()
                    ->buttons([]);
    }

    public function getColumns(): array
    {
        $parameters = $this->parameters ?? [];

        $columns = [
            Column::make('date')->title('Date'),
        ];

        foreach ($parameters as $parameter) {
            $columns[] = Column::make($parameter->name)->title($parameter->name);
        }

        $columns[] = Column::computed('action')
                    ->exportable(false)
                    ->printable(false)
                    ->width(60)
                    ->addClass('text-center');

        return $columns;
    }

    protected function filename(): string
    {
        return 'Checksheet_' . date('YmdHis');
    }
}
