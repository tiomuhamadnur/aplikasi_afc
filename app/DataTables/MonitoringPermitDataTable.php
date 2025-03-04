<?php

namespace App\DataTables;

use App\Models\MonitoringPermit;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class MonitoringPermitDataTable extends DataTable
{
    protected $tipe_permit_id;
    protected $tipe_pekerjaan_id;
    protected $relasi_area_id;
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
            ->addColumn('status', function($item) {
                $badgeClass = '';
                if ($item->status == 'active') {
                    $badgeClass = 'badge-gradient-success';
                } else {
                    $badgeClass = 'badge-gradient-danger';
                }
                return "<label class='badge {$badgeClass} text-uppercase'>{$item->status}</label>";
            })
            ->addColumn('sisa_hari', function($item) {
                return "{$item->remaining_days}";
            })
            ->addColumn('#', function($item) {
                $editRoute = route('monitoring-permit.edit', $item->uuid);

                $editButton = "<button type='button' class='btn btn-gradient-warning btn-rounded btn-icon'
                    onclick=\"window.location.href='{$editRoute}'\" title='Edit'>
                    <i class='text-white mdi mdi-lead-pencil'></i>
                </button>";

                if (auth()->user()->role_id == 2) {
                    return $editButton;
                }

                $deleteModal = "<button type='button' title='Delete'
                    class='btn btn-gradient-danger btn-rounded btn-icon'
                    data-bs-toggle='modal' data-bs-target='#deleteModal'
                    data-id='{$item->id}'>
                    <i class='mdi mdi-delete'></i>
                </button>";

                return $editButton . $deleteModal;
            })
            ->rawColumns(['status', 'sisa_hari', '#']);
    }

    public function query(MonitoringPermit $model): QueryBuilder
    {
        $departemen_id = auth()->user()->relasi_struktur->departemen->id;

        $query = $model->with([
            'tipe_permit',
            'tipe_pekerjaan',
            'departemen',
            'relasi_area.sub_lokasi'
        ])->where('departemen_id', $departemen_id)
        ->orderBy('tanggal_expired', 'asc')
        ->newQuery();

        // Filter
        if($this->relasi_area_id != null)
        {
            $query->whereRelation('relasi_area', 'id', '=', $this->relasi_area_id);
        }

        if($this->tipe_permit_id != null)
        {
            $query->whereRelation('tipe_permit', 'id', '=', $this->tipe_permit_id);
        }

        if($this->tipe_pekerjaan_id != null)
        {
            $query->whereRelation('tipe_pekerjaan', 'id', '=', $this->tipe_pekerjaan_id);
        }

        if($this->status != null)
        {
            $query->where('status', $this->status);
        }

        if($this->start_date != null && $this->end_date != null)
        {
            $query->whereBetween('tanggal_expired', [$this->start_date, $this->end_date]);
        }

        return $query;
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
                    ->setTableId('monitoringpermit-table')
                    ->columns($this->getColumns())
                    ->minifiedAjax()
                    ->pageLength(10)
                    ->lengthMenu([10, 50, 100, 250, 500, 1000])
                    ->dom('frtiplB')
                    ->orderBy([3, 'asc'])
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
                    ->sortable(false)
                    ->addClass('text-center'),
            Column::make('tipe_permit.code')
                    ->title('Tipe Permit')
                    ->sortable(false),
            Column::make('tanggal_expired')
                    ->title('Tanggal Expired')
                    ->sortable(false),
            Column::computed('sisa_hari')
                    ->addClass('text-center')
                    ->searchable(true)
                    ->sortable(false),
            Column::computed('status')
                    ->addClass('text-center')
                    ->searchable(true)
                    ->sortable(false),
            Column::make('nomor')
                    ->title('Nomor')
                    ->sortable(false),
            Column::make('name')
                    ->title('Nama Pekerjaan')
                    ->sortable(false),
            Column::make('departemen.code')
                    ->title('Departemen')
                    ->sortable(false),
            Column::make('relasi_area.sub_lokasi.name')
                    ->title('Area')
                    ->sortable(false),
            Column::make('tipe_pekerjaan.code')
                    ->title('Tipe Pekerjaan')
                    ->sortable(false),
        ];
    }

    protected function filename(): string
    {
        return date('Ymd') . '_Data Permit_' . auth()->user()->relasi_struktur->departemen->name ?? '-';
    }
}
