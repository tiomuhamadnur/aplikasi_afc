@extends('layout.base')

@section('title-head')
    <title>Monitoring Permit</title>
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Data Monitoring Permit</h4>
                        <div class="btn-group my-2">
                            <button type="button" title="Add" class="btn btn-outline-primary btn-rounded btn-icon"
                                data-bs-toggle="modal" data-bs-target="#addModal">
                                <i class="mdi mdi-plus-circle"></i>
                            </button>
                            <button type="button" title="Filter" data-bs-toggle="modal" data-bs-target="#filterModal"
                                class="btn btn-outline-primary btn-rounded btn-icon">
                                <i class="mdi mdi-filter"></i>
                            </button>
                            <button type="button" title="Export" data-bs-toggle="modal" data-bs-target="#exportExcelModal"
                                class="btn btn-outline-primary btn-rounded btn-icon">
                                <i class="mdi mdi-file-export"></i>
                            </button>
                        </div>
                        <div class="table-responsive">
                            {{ $dataTable->table() }}
                            {{-- <table class="table .table-hover text-center">
                                <thead>
                                    <tr>
                                        <th> # </th>
                                        <th> Tipe <br> Permit </th>
                                        <th> Tipe <br> Pekerjaan </th>
                                        <th> Nomor </th>
                                        <th> Nama Pekerjaan </th>
                                        <th> Departemen </th>
                                        <th> Area </th>
                                        <th> Tanggal Expired </th>
                                        <th> Sisa Hari </th>
                                        <th> Status </th>
                                        <th> Aksi </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($monitoring_permit as $item)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $item->tipe_permit->code }}</td>
                                            <td>{{ $item->tipe_pekerjaan->code ?? '-' }}</td>
                                            <td class="font-weight-bold">{{ $item->nomor }}</td>
                                            <td>{{ $item->name }}</td>
                                            <td>{{ $item->departemen->code ?? '-' }}</td>
                                            <td>
                                                @if ($item->relasi_area_id != null)
                                                    {{ $item->relasi_area->lokasi->name }} <br>
                                                    {{ $item->relasi_area->sub_lokasi->name }} <br>
                                                    {{ $item->relasi_area->detail_lokasi->name }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td class="font-weight-bold">
                                                {{ date('j F Y', strtotime($item->tanggal_expired)) }}
                                            </td>
                                            <td class="font-weight-bold">
                                                {{ $item->remaining_days ?? '-' }}
                                            </td>
                                            <td>
                                                <label
                                                    class="badge @if ($item->status == 'active') badge-gradient-success @else badge-gradient-danger @endif text-uppercase">
                                                    {{ $item->status }}
                                                </label>
                                            </td>
                                            <td>
                                                <a href="{{ route('monitoring-permit.edit', $item->uuid) }}"
                                                    title="Edit">
                                                    <button type="button"
                                                        class="btn btn-gradient-warning btn-rounded btn-icon">
                                                        <i class="text-white mdi mdi-lead-pencil"></i>
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
                            </table> --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Filter -->
    <div class="modal fade" id="filterModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Form Filter</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="filterForm" action="{{ route('monitoring-permit.index') }}" method="GET"
                        class="forms-sample">
                        @csrf
                        @method('GET')
                        <div class="form-group">
                            <label for="">Tipe Permit</label>
                            <select class="form-control form-control-lg" name="tipe_permit_id">
                                <option value="" selected disabled>- pilih tipe permit -</option>
                                @foreach ($tipe_permit as $item)
                                    <option value="{{ $item->id }}">{{ $item->code }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="">Tipe Pekerjaan</label>
                            <select class="form-control form-control-lg" name="tipe_pekerjaan_id">
                                <option value="" selected disabled>- pilih tipe pekerjaan -</option>
                                @foreach ($tipe_pekerjaan as $item)
                                    <option value="{{ $item->id }}">{{ $item->name }} ({{ $item->code }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="">Area</label>
                            <select class="form-control form-control-lg" name="relasi_area_id">
                                <option value="" selected disabled>- pilih area -</option>
                                @foreach ($area as $item)
                                    <option value="{{ $item->id }}">{{ $item->lokasi->name }} -
                                        {{ $item->sub_lokasi->name }} - {{ $item->detail_lokasi->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="">Status</label>
                            <select class="form-control form-control-lg" name="status">
                                <option value="" selected disabled>- pilih status -</option>
                                <option value="active">Active</option>
                                <option value="expired">Expired</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <a href="{{ route('monitoring-permit.index') }}" class="btn btn-gradient-warning me-2">Reset</a>
                    <button type="submit" form="filterForm" class="btn btn-gradient-primary me-2">Filter</button>
                </div>
            </div>
        </div>
    </div>
    <!-- End Add Filter -->

    <!-- Add Modal -->
    <div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Form Add</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addForm" action="{{ route('monitoring-permit.store') }}" method="POST"
                        class="forms-sample" enctype="multipart/form-data">
                        @csrf
                        @method('POST')
                        <div class="form-group">
                            <label for="departemen">Departemen</label>
                            <input type="text" class="form-control" id="departemen" placeholder="Departemen"
                                autocomplete="off" value="{{ auth()->user()->relasi_struktur->departemen->name }}"
                                disabled>
                        </div>
                        <div class="form-group">
                            <label for="tipe_permit_id">Tipe Permit</label>
                            <select class="form-control form-control-lg" id="tipe_permit_id" name="tipe_permit_id"
                                required>
                                <option value="" selected disabled>- pilih tipe permit -</option>
                                @foreach ($tipe_permit as $item)
                                    <option value="{{ $item->id }}">{{ $item->code }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="tipe_pekerjaan_id">Tipe Pekerjaan</label>
                            <select class="form-control form-control-lg" id="tipe_pekerjaan_id" name="tipe_pekerjaan_id"
                                required>
                                <option value="" selected disabled>- pilih tipe pekerjaan -</option>
                                @foreach ($tipe_pekerjaan as $item)
                                    <option value="{{ $item->id }}">{{ $item->name }} ({{ $item->code }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="nomor">Nomor</label>
                            <input type="text" class="form-control" id="nomor" name="nomor"
                                placeholder="Nomor" autocomplete="off" required>
                        </div>
                        <div class="form-group">
                            <label for="name">Nama Pekerjaan</label>
                            <input type="text" class="form-control" id="name" name="name"
                                placeholder="Nama Pekerjaan" autocomplete="off" required>
                        </div>
                        <div class="form-group">
                            <label for="tanggal_expired">Tanggal Expired</label>
                            <input type="date" class="form-control" id="tanggal_expired" name="tanggal_expired"
                                autocomplete="off" required>
                        </div>
                        <div class="form-group">
                            <label for="relasi_area_id">Area <span class="text-info">(opsional)</span></label>
                            <select class="form-control form-control-lg" id="relasi_area_id" name="relasi_area_id">
                                <option value="" selected disabled>- pilih area spesifik -</option>
                                @foreach ($area as $item)
                                    <option value="{{ $item->id }}">{{ $item->lokasi->name }} -
                                        {{ $item->sub_lokasi->name }} - {{ $item->detail_lokasi->name }}</option>
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

    <!-- Delete Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Are you sure?</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="deleteForm" action="{{ route('monitoring-permit.delete') }}" method="POST"
                        class="forms-sample">
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
@endsection

@push('scripts')
    {{ $dataTable->scripts() }}
@endpush

@section('javascript')
    <script>
        $(document).ready(function() {
            $('#deleteModal').on('show.bs.modal', function(e) {
                var id = $(e.relatedTarget).data('id');

                $('#id_delete').val(id);
            });
        });
    </script>

    <script>
        function exportExcel() {
            document.getElementById('datatable-excel').click();
        }
    </script>
@endsection
