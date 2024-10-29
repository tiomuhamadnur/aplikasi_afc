@extends('layout.base')

@section('title-head')
    <title>Admin | Approval</title>
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Data Approval</h4>
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
                                        <th> Departemen </th>
                                        <th> Seksi </th>
                                        <th> Jabatan </th>
                                        <th> Tipe Employee </th>
                                        <th> Priority </th>
                                        <th> Name </th>
                                        <th> Aksi </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($approval as $item)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $item->relasi_struktur->departemen->name ?? '-' }}</td>
                                            <td>{{ $item->relasi_struktur->seksi->name ?? '-' }}</td>
                                            <td>{{ $item->jabatan->name ?? '-' }}</td>
                                            <td>{{ $item->tipe_employee->name ?? '-' }}</td>
                                            <td>{{ $item->priority ?? '-' }}</td>
                                            <td>{{ $item->name ?? '-' }}</td>
                                            <td>
                                                <a href="{{ route('approval.edit', $item->uuid) }}" title="Edit">
                                                    <button type="button"
                                                        class="btn btn-gradient-warning btn-rounded btn-icon">
                                                        <i class="mdi mdi-lead-pencil"></i>
                                                    </button>
                                                </a>
                                                <button type="button" title="Delete"
                                                    class="btn btn-gradient-danger btn-rounded btn-icon"
                                                    data-bs-toggle="modal" data-bs-target="#deleteModal"
                                                    data-id="{{ $item->id }}">
                                                    <i class="mdi mdi-delete"></i>
                                                </button>
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
                    <form id="addForm" action="{{ route('approval.store') }}" method="POST" class="forms-sample">
                        @csrf
                        @method('POST')
                        <div class="form-group">
                            <label for="relasi_struktur_id">Relasi Struktur</label>
                            <select class="form-control form-control-lg" name="relasi_struktur_id" id="relasi_struktur_id"
                                required>
                                <option value="" selected disabled>- pilih relasi struktur -</option>
                                @foreach ($relasi_struktur as $item)
                                    <option value="{{ $item->id }}">{{ $item->departemen->name ?? '#' }} -
                                        {{ $item->seksi->name ?? '#' }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="jabatan_id">Jabatan</label>
                            <select class="form-control form-control-lg" name="jabatan_id" id="jabatan_id" required>
                                <option value="" selected disabled>- pilih jabatan -</option>
                                @foreach ($jabatan as $item)
                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="tipe_employee_id">Tipe Employee</label>
                            <select class="form-control form-control-lg" name="tipe_employee_id" id="tipe_employee_id"
                                required>
                                <option value="" selected disabled>- pilih tipe employee -</option>
                                @foreach ($tipe_employee as $item)
                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="priority">Priority</label>
                            <input type="number" min="1" class="form-control" name="priority" id="priority"
                                autocomplete="off" placeholder="input priority" required>
                        </div>
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" class="form-control" name="name" id="name" autocomplete="off"
                                placeholder="input label name" required>
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

    <!-- Delete Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Are you sure?</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="deleteForm" action="{{ route('approval.delete') }}" method="POST" class="forms-sample">
                        @csrf
                        @method('delete')
                        <input type="text" name="id" id="id_delete" hidden>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" form="deleteForm" class="btn btn-gradient-danger me-2">Delete</button>
                </div>
            </div>
        </div>
    </div>
    <!-- End Delete Modal -->
@endsection

@section('javascript')
    <script>
        $(document).ready(function() {
            $('#deleteModal').on('show.bs.modal', function(e) {
                var id = $(e.relatedTarget).data('id');

                $('#id_delete').val(id);
            });
        });
    </script>
@endsection
