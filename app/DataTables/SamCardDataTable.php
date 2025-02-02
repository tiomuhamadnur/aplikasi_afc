<?php

namespace App\DataTables;

use App\Models\SamCard;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Support\Facades\Request;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class SamCardDataTable extends DataTable
{
    protected $status;

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
                ->addColumn('status', function($item) {
                    $badgeClass = $item->status == 'ready' ? 'badge-gradient-success' : 'badge-gradient-danger';
                    return "<span class='badge {$badgeClass} text-uppercase'>{$item->status}</span>";
                })
                ->addColumn('#', function($item) {
                    if (auth()->user()->role_id != 1) {
                        return '';
                    }

                    $photoUrl = asset('storage/' . $item->photo);
                    $photoButton = "<button type='button' title='Show' class='btn btn-gradient-primary btn-rounded btn-icon'
                        data-bs-toggle='modal' data-bs-target='#photoModal' data-photo='{$photoUrl}'>
                        <i class='mdi mdi-eye'></i>
                        </button>";
                    $editRoute = route('sam-card.edit', $item->uuid);
                    $createRoute = route('sam-history.create', $item->uuid);
                    $deleteModal = "<button type='button' title='Delete'
                        class='btn btn-gradient-danger btn-rounded btn-icon'
                        data-bs-toggle='modal' data-bs-target='#deleteModal'
                        data-id='{$item->id}'>
                        <i class='mdi mdi-delete'></i>
                    </button>";

                    $editButton = "<button type='button' class='btn btn-gradient-warning btn-rounded btn-icon'
                        onclick=\"window.location.href='{$editRoute}'\" title='Edit'>
                        <i class='text-white mdi mdi-lead-pencil'></i>
                    </button>";

                    $useButton = $item->mc != null ?
                    "<button type='button' class='btn btn-gradient-success btn-rounded btn-icon'
                        onclick=\"window.location.href='{$createRoute}'\" title='Use this SAM Card'>
                        <i class='text-white mdi mdi-rocket'></i>
                    </button>" : '';

                    return $photoButton . $editButton . $useButton . $deleteModal;
                })
                ->rawColumns(['status', '#']);
    }

    public function query(SamCard $model): QueryBuilder
    {
        $query = $model->newQuery();

        if($this->status != null)
        {
            $query->where('status', $this->status);
        }

        return $query;
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
                    ->setTableId('samcard-table')
                    ->columns($this->getColumns())
                    ->minifiedAjax()
                    ->pageLength(10)
                    ->lengthMenu([10, 20, 50, 100, 250, 500])
                    ->dom('frtiplB')
                    ->orderBy(1)
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
            Column::make('uid')->title('UID'),
            Column::make('tid')->title('TID'),
            Column::make('mid')->title('MID'),
            Column::make('pin')->title('PIN'),
            Column::make('mc')->title('MC'),
            Column::make('alokasi')->title('Alokasi'),
            Column::computed('status')
                    ->exportable(false)
                    ->printable(false)
                    ->width(60)
                    ->addClass('text-center')
                    ->searchable(true),
        ];
    }

    protected function filename(): string
    {
        return date('Ymd') . '_Data SAM Card';
    }
}
