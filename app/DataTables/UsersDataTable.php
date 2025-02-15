<?php

namespace App\DataTables;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class UsersDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
        ->addColumn('#', function($item) {
            if (auth()->user()->role_id != 1) {
                return '';
            }

            $editRoute = route('user.edit', $item->uuid);
            $bannedModal = "<button type='button' title='Banned this User'
                class='btn btn-gradient-danger btn-rounded btn-icon'
                data-bs-toggle='modal' data-bs-target='#bannedModal'
                data-uuid='{$item->uuid}'>
                <i class='mdi mdi-account-remove'></i>
            </button>";

            $editButton = "<button type='button' class='btn btn-gradient-warning btn-rounded btn-icon'
                onclick=\"window.location.href='{$editRoute}'\" title='Edit'>
                <i class='text-white mdi mdi-lead-pencil'></i>
            </button>";

            $changePasswordModal = "<button type='button' title='Reset Password'
                class='btn btn-gradient-primary btn-rounded btn-icon'
                data-bs-toggle='modal' data-bs-target='#changePasswordModal'
                data-uuid='{$item->uuid}'>
                <i class='mdi mdi-account-key'></i>
            </button>";

            return $editButton . $bannedModal . $changePasswordModal;
        })
        ->rawColumns(['#']);
    }

    public function query(User $model): QueryBuilder
    {
        return $model->notBanned()->with([
            'jabatan',
            'role',
            'tipe_employee',
            'perusahaan',
            'relasi_struktur.seksi',
            'relasi_struktur.departemen',
            'relasi_struktur.divisi',
        ])->newQuery();
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
                    ->setTableId('users-table')
                    ->columns($this->getColumns())
                    ->minifiedAjax()
                    ->pageLength(10)
                    ->lengthMenu([10, 50, 100, 250, 500, 1000])
                    ->dom('frtiplB')
                    ->orderBy([1, 'asc'])
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
            Column::make('name')->title('Name'),
            Column::make('email')->title('Email'),
            Column::make('jabatan.name')->title('Jabatan'),
            Column::make('tipe_employee.name')->title('Employee Type'),
            Column::make('no_hp')->title('No HP'),
            Column::make('relasi_struktur.seksi.code')->title('Section'),
            Column::make('relasi_struktur.departemen.code')->title('Department'),
            Column::make('relasi_struktur.divisi.code')->title('Division'),
            Column::make('jabatan.name')->title('Jabatan'),
            Column::make('role.name')->title('Role'),
            Column::make('perusahaan.name')->title('Company'),
        ];
    }

    protected function filename(): string
    {
        return 'Users_' . date('YmdHis');
    }
}
