<?php

namespace App\DataTables;

use App\Models\LCUChecklist;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class LCUChecklistDataTable extends DataTable
{
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
            ->addColumn('#', function ($item) {
                $photoRoomUrl = asset('storage/' . $item->room_temp_photo);
                $photoRackUrl = asset('storage/' . $item->rack_temp_photo);
                $buttonPhoto = "<button type='button' title='Show Photo' class='btn btn-gradient-primary btn-rounded btn-icon'
                        data-bs-toggle='modal' data-bs-target='#photoModal' data-photo_room='{$photoRoomUrl}' data-photo_rack='{$photoRackUrl}'>
                        <i class='mdi mdi-file-image'></i>
                        </button>";

                if (auth()->user()->role_id != 1) {
                    return $buttonPhoto;
                }

                $deleteModal = "<button type='button' title='Delete'
                    class='btn btn-gradient-danger btn-rounded btn-icon'
                    data-bs-toggle='modal' data-bs-target='#deleteModal'
                    data-id='{$item->id}'>
                    <i class='mdi mdi-delete'></i>
                </button>";

                $buttonEdit = "<button type='button' title='Edit'
                    class='btn btn-gradient-warning btn-rounded btn-icon'
                    data-bs-toggle='modal' data-bs-target='#editModal'
                    data-id='{$item->id}'
                    data-date='" . Carbon::parse($item->date)->format('Y-m-d\TH:i') . "'
                    data-mks_status='{$item->mks_status}'
                    data-lighting_status='{$item->lighting_status}'
                    data-cctv_status='{$item->cctv_status}'
                    data-ac_status='{$item->ac_status}'
                    data-room_cleanliness='{$item->room_cleanliness}'
                    data-server_status='{$item->server_status}'
                    data-server_alert='{$item->server_alert}'
                    data-switch_status='{$item->switch_status}'
                    data-switch_alert='{$item->switch_alert}'
                    data-ups_status='{$item->ups_status}'
                    data-ups_alert='{$item->ups_alert}'
                    data-cable_status='{$item->cable_status}'
                    data-room_temperature='{$item->room_temperature}'
                    data-rack_temperature='{$item->rack_temperature}'
                    data-room_temp_photo='{$photoRoomUrl}'
                    data-rack_temp_photo='{$photoRackUrl}'
                    data-user_id='{$item->user_id}'
                    data-functional_location_id='{$item->functional_location_id}'
                    data-remark='{$item->remark}'>
                    <i class='mdi mdi-lead-pencil'></i>
                </button>";

                return $buttonPhoto . $buttonEdit . $deleteModal;
            })
            ->addColumn('location', function ($item) {
                return $item->functional_location->name;
            })
            ->editColumn('mks_status', fn($item) => $this->formatStatus($item->mks_status))
            ->editColumn('lighting_status', fn($item) => $this->formatStatus($item->lighting_status))
            ->editColumn('cctv_status', fn($item) => $this->formatStatus($item->cctv_status))
            ->editColumn('ac_status', fn($item) => $this->formatStatus($item->ac_status))
            ->editColumn('room_cleanliness', fn($item) => $this->formatStatus($item->room_cleanliness))
            ->editColumn('server_status', fn($item) => $this->formatStatus($item->server_status))
            ->editColumn('server_alert', fn($item) => $this->formatStatus($item->server_alert))
            ->editColumn('switch_status', fn($item) => $this->formatStatus($item->switch_status))
            ->editColumn('switch_alert', fn($item) => $this->formatStatus($item->switch_alert))
            ->editColumn('ups_status', fn($item) => $this->formatStatus($item->ups_status))
            ->editColumn('ups_alert', fn($item) => $this->formatStatus($item->ups_alert))
            ->editColumn('cable_status', fn($item) => $this->formatStatus($item->cable_status))
            ->rawColumns(['#', 'location', 'photo']);
    }

    private function formatStatus($value): string
    {
        return $value ? 'OK' : 'NOT OK';
    }

    public function query(LCUChecklist $model): QueryBuilder
    {
        $query = $model->with(['user', 'functional_location'])->newQuery();

        if ($this->start_date != null && $this->end_date != null) {
            $start = Carbon::parse($this->start_date)->startOfDay()->format('Y-m-d H:i:s');
            $end = Carbon::parse($this->end_date)->endOfDay()->format('Y-m-d H:i:s');

            $query->whereBetween('date', [$start, $end]);
        }

        return $query;
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('lcuchecklist-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->pageLength(10)
            ->lengthMenu([10, 50, 100, 250, 500, 1000])
            ->dom('frtiplB')
            ->orderBy([1, 'desc'])
            ->buttons([
                [
                    'extend' => 'excel',
                    'text' => 'Export to Excel',
                    'attr' => [
                        'id' => 'datatable-excel',
                        'style' => 'display: none;',
                    ],
                ],
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
            Column::make('date')->title('Date'),
            Column::make('user.name')->title('Checked by'),
            Column::computed('location')
                    ->exportable(true)
                    ->sortable(true)
                    ->title('Location'),
            Column::make('mks_status')->title('MKS Status'),
            Column::make('lighting_status')->title('Lighting Status'),
            Column::make('cctv_status')->title('CCTV Status'),
            Column::make('ac_status')->title('AC Status'),
            Column::make('room_cleanliness')->title('Kebersihan Ruangan'),
            Column::make('server_status')->title('Server Status'),
            Column::make('server_alert')->title('Server Alert'),
            Column::make('switch_status')->title('Switch Status'),
            Column::make('switch_alert')->title('Switch Alert'),
            Column::make('ups_status')->title('UPS Status'),
            Column::make('ups_alert')->title('UPS Alert'),
            Column::make('cable_status')->title('Cable Status'),
            Column::make('room_temperature')->title('Room Temperature (°C)'),
            Column::make('rack_temperature')->title('Rack Temperature (°C)'),
            Column::make('remark')->title('Remarks'),
        ];
    }

    protected function filename(): string
    {
        return date('Ymd') . '_Data LCU Checklist';
    }
}
