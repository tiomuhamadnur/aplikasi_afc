@extends('layout.base')

@section('title-head')
    <title>Admin | Equipment</title>
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Data Equipment</h4>
                        <div class="btn-group my-2">
                            <button type="button" title="Add" class="btn btn-outline-primary btn-rounded btn-icon"
                                data-bs-toggle="modal" data-bs-target="#addModal">
                                <i class="mdi mdi-plus-circle"></i>
                            </button>
                            <button type="button" title="Filter" class="btn btn-outline-primary btn-rounded btn-icon">
                                <i class="mdi mdi-filter"></i>
                            </button>
                            <button type="button" title="Export" class="btn btn-outline-primary btn-rounded btn-icon">
                                <i class="mdi mdi-file-export"></i>
                            </button>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered text-center">
                                <thead>
                                    <tr>
                                        <th> # </th>
                                        <th> Nama </th>
                                        <th> Code </th>
                                        <th> Tipe </th>
                                        <th> Lokasi </th>
                                        <th> Detail Lokasi </th>
                                        <th> Photo </th>
                                        <th> Aksi </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>1</td>
                                        <td>PG101</td>
                                        <td>PG101</td>
                                        <td>PG (Passenger Gate)</td>
                                        <td>Stasiun LBB</td>
                                        <td>Concourse</td>
                                        <td>
                                            <button type="button" class="btn btn-primary btn-fw p-1">
                                                <i class="mdi mdi-eye"></i> Show
                                            </button>
                                        </td>
                                        <td>
                                            <button type="button" title="Edit"
                                                class="btn btn-gradient-warning btn-rounded btn-icon" data-bs-toggle="modal"
                                                data-bs-target="#editModal">
                                                <i class="mdi mdi-lead-pencil"></i>
                                            </button>
                                            <button type="button" title="Delete"
                                                class="btn btn-gradient-danger btn-rounded btn-icon" data-bs-toggle="modal"
                                                data-bs-target="#deleteModal" data-id="">
                                                <i class="mdi mdi-delete"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
