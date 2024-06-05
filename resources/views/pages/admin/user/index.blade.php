@extends('layout.base')

@section('title-head')
    <title>Admin | User</title>
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Data User</h4>
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
                                        <th> Email </th>
                                        <th> No HP </th>
                                        <th> Jabatan </th>
                                        <th> Struktur </th>
                                        <th> Role </th>
                                        <th> Tipe <br> Employee </th>
                                        <th> Perusahaan </th>
                                        <th> Aksi </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($user as $item)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $item->name }}</td>
                                            <td>{{ $item->email }}</td>
                                            <td>{{ $item->no_hp ?? '-' }}</td>
                                            <td>{{ $item->jabatan->name ?? '-' }}</td>
                                            <td class="text-wrap">
                                                Seksi {{ $item->relasi_struktur->seksi->code ?? '-' }} <br>
                                                Departemen {{ $item->relasi_struktur->departemen->code ?? '-' }} <br>
                                                Divisi {{ $item->relasi_struktur->divisi->code ?? '-' }} <br>
                                                Direktorat {{ $item->relasi_struktur->direktorat->code ?? '-' }} <br>
                                            </td>
                                            <td>{{ $item->role->name ?? '-' }}</td>
                                            <td>{{ $item->tipe_employee->name ?? '-' }}</td>
                                            <td>{{ $item->perusahaan->name ?? '-' }}</td>
                                            <td>
                                                <a href="{{ route('user.edit', $item->uuid) }}">
                                                    <button type="button" title="Edit"
                                                        class="btn btn-gradient-warning btn-rounded btn-icon">
                                                        <i class="mdi mdi-lead-pencil"></i>
                                                    </button>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Modal -->
    <div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Form Add</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addForm" action="{{ route('user.store') }}" method="POST" class="forms-sample">
                        @csrf
                        @method('POST')
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" class="form-control" id="name" name="name" placeholder="Name"
                                autocomplete="off" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" class="form-control" id="email" name="email" placeholder="Email"
                                autocomplete="off" required>
                        </div>
                        <div class="form-group">
                            <label for="no_hp">No HP</label>
                            <input type="text" class="form-control" id="no_hp" name="no_hp" placeholder="No HP"
                                autocomplete="off" required>
                        </div>
                        <div class="form-group">
                            <label for="gender_id">Gender</label>
                            <select class="form-control form-control-lg" name="gender_id" id="gender_id">
                                <option value="" selected disabled>- pilih gender -</option>
                                @foreach ($gender as $item)
                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="tipe_employee_id">Tipe Employee</label>
                            <select class="form-control form-control-lg" name="tipe_employee_id" id="tipe_employee_id">
                                <option value="" selected disabled>- pilih tipe employee -</option>
                                @foreach ($tipe_employee as $item)
                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="perusahaan_id">Perusahaan</label>
                            <select class="form-control form-control-lg" name="perusahaan_id" id="perusahaan_id">
                                <option value="" selected disabled>- pilih perusahaan -</option>
                                @foreach ($perusahaan as $item)
                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="relasi_struktur_id">Struktur</label>
                            <select class="form-control form-control-lg" name="relasi_struktur_id" id="relasi_struktur_id">
                                <option value="" selected disabled>- pilih struktur -</option>
                                @foreach ($relasi_struktur as $item)
                                    <option value="{{ $item->id }}">{{ $item->seksi->name }} -
                                        {{ $item->departemen->name }} - {{ $item->divisi->name }} -
                                        {{ $item->direktorat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="jabatan_id">Jabatan</label>
                            <select class="form-control form-control-lg" name="jabatan_id" id="jabatan_id">
                                <option value="" selected disabled>- pilih jabatan -</option>
                                @foreach ($jabatan as $item)
                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="role_id">Role</label>
                            <select class="form-control form-control-lg" name="role_id" id="role_id">
                                <option value="" selected disabled>- pilih role -</option>
                                @foreach ($role as $item)
                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" form="addForm" class="btn btn-gradient-primary me-2">Submit</button>
                </div>
            </div>
        </div>
    </div>
    <!-- End Add Modal -->
@endsection
