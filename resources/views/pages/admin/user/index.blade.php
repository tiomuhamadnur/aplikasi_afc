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
                            <button type="button" title="Export" class="btn btn-outline-primary btn-rounded btn-icon"
                                data-bs-toggle="modal" data-bs-target="#exportExcelModal">
                                <i class="mdi mdi-file-export"></i>
                            </button>
                        </div>
                        <button type="button" title="List Banned Users" class="btn btn-outline-danger btn-rounded btn-icon"
                            onclick="window.location.href='{{ route('user.banned') }}'">
                            <i class="mdi mdi-account-remove"></i>
                        </button>
                        <div class="table-responsive">
                            <div class="table-responsive">
                                {{ $dataTable->table() }}
                            </div>
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
                            <label for="name" class="required">Name</label>
                            <input type="text" class="form-control" id="name" name="name" placeholder="Input Name"
                                autocomplete="off" required>
                        </div>
                        <div class="form-group">
                            <label for="email" class="required">Email</label>
                            <input type="email" class="form-control" id="email" name="email" placeholder="Input Email"
                                autocomplete="off" required>
                        </div>
                        <div class="form-group">
                            <label for="no_hp" class="required">No HP</label>
                            <input type="text" class="form-control" id="no_hp" name="no_hp" placeholder="Input No HP"
                                autocomplete="off" required>
                        </div>
                        <div class="form-group">
                            <label for="gender_id" class="required">Gender</label>
                            <select class="tom-select-class" name="gender_id" id="gender_id" required>
                                <option value="" selected disabled>- pilih gender -</option>
                                @foreach ($gender as $item)
                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="tipe_employee_id" class="required">Tipe Employee</label>
                            <select class="tom-select-class" name="tipe_employee_id" id="tipe_employee_id" required>
                                <option value="" selected disabled>- pilih tipe employee -</option>
                                @foreach ($tipe_employee as $item)
                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="perusahaan_id" class="required">Perusahaan</label>
                            <select class="tom-select-class" name="perusahaan_id" id="perusahaan_id" required>
                                <option value="" selected disabled>- pilih perusahaan -</option>
                                @foreach ($perusahaan as $item)
                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="relasi_struktur_id" class="required">Struktur</label>
                            <select class="tom-select-class" name="relasi_struktur_id" id="relasi_struktur_id" required>
                                <option value="" selected disabled>- pilih struktur -</option>
                                @foreach ($relasi_struktur as $item)
                                    <option value="{{ $item->id }}">{{ $item->seksi->name }} -
                                        {{ $item->departemen->name }} - {{ $item->divisi->name }} -
                                        {{ $item->direktorat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="jabatan_id" class="required">Jabatan</label>
                            <select class="tom-select-class" name="jabatan_id" id="jabatan_id" required>
                                <option value="" selected disabled>- pilih jabatan -</option>
                                @foreach ($jabatan as $item)
                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="role_id" class="required">Role</label>
                            <select class="tom-select-class" name="role_id" id="role_id" required>
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

    <!-- Export Excel Modal -->
    <div class="modal fade" id="exportExcelModal" tabindex="-1" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Are you sure?</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center">
                        <img src="https://i.pinimg.com/originals/1b/db/8a/1bdb8ac897512116cbac58ffe7560d82.png"
                            alt="Excel" style="height: 150px; width: 150px">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" id="exportButton" onclick="exportExcel()"
                        class="btn btn-gradient-success me-2">Download</button>
                </div>
            </div>
        </div>
    </div>
    <!-- End Export Excel Modal -->

    <!-- Banned Modal -->
    <div class="modal fade" id="bannedModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Are you sure?</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="deleteForm" action="{{ route('user.ban') }}" method="POST" class="forms-sample">
                        @csrf
                        @method('delete')
                        <input type="text" name="uuid" id="uuid_banned" hidden>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" form="deleteForm" class="btn btn-gradient-danger me-2">Banned</button>
                </div>
            </div>
        </div>
    </div>
    <!-- End Banned Modal -->

    <!-- Change Password Modal -->
    <div class="modal fade" id="changePasswordModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Are you sure?</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="text-center">Password user ini akan kembali default menjadi:</p>
                    <h5 class="text-center"><b>user123</b></h5>
                    <form id="changePasswordForm" action="{{ route('user.change_password') }}" method="POST" class="forms-sample">
                        @csrf
                        @method('put')
                        <input type="text" name="uuid" id="uuid_change_password" hidden>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" form="changePasswordForm" class="btn btn-gradient-primary me-2">Reset</button>
                </div>
            </div>
        </div>
    </div>
    <!-- End Change Password Modal -->
@endsection

@push('scripts')
    {{ $dataTable->scripts() }}
@endpush

@section('javascript')
    <script>
        $(document).ready(function() {
            $('#bannedModal').on('show.bs.modal', function(e) {
                var uuid = $(e.relatedTarget).data('uuid');
                $('#uuid_banned').val(uuid);
            });

            $('#changePasswordModal').on('show.bs.modal', function(e) {
                var uuid = $(e.relatedTarget).data('uuid');
                $('#uuid_change_password').val(uuid);
            });
        });
    </script>

    <script>
        function exportExcel() {
            document.getElementById('datatable-excel').click();
        }
    </script>
@endsection
