@extends('layout.base')

@section('title-head')
    <title>Transaksi Tiket</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Data Transaksi Tiket</h4>
                        <div class="btn-group my-2">
                            <button type="button" title="Add" class="btn btn-outline-primary btn-rounded btn-icon"
                                data-bs-toggle="modal" data-bs-target="#convertModal">
                                <i class="mdi mdi-plus-circle"></i>
                            </button>
                            <button type="button" title="Filter" data-bs-toggle="modal" data-bs-target="#filterModal"
                                class="btn btn-outline-primary btn-rounded btn-icon">
                                <i class="mdi mdi-filter"></i>
                            </button>
                            <button type="button" title="Export" class="btn btn-outline-primary btn-rounded btn-icon">
                                <i class="mdi mdi-file-export"></i>
                            </button>
                        </div>
                        {{-- <a href="{{ route('transaksi.tiket.ftp') }}" title="Sync">
                            <span><i class="mdi mdi-refresh"></i></span>
                        </a> --}}
                        <div class="table-responsive">
                            {{ $dataTable->table() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Import Convert Modal -->
    <div class="modal fade" id="convertModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Form Import</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="convertForm" action="{{ route('transaksi.tiket.import') }}" method="POST"
                        class="forms-sample" enctype="multipart/form-data">
                        @csrf
                        @method('POST')
                        <div class="form-group">
                            <label for="">
                                Log File
                            </label>
                            <input type="file"
                                class="form-control form-control-lg @error('logfile') is-invalid @enderror" id="logfile"
                                name="logfile" required>
                            @error('logfile')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" form="convertForm" class="btn btn-gradient-primary me-2">Submit</button>
                </div>
            </div>
        </div>
    </div>
    <!-- End Convert Log Modal -->

    <!-- Filter Modal -->
    <div class="modal fade" id="filterModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Form Filter</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addForm" action="{{ route('transaksi.tiket.store') }}" method="POST" class="forms-sample">
                        @csrf
                        @method('POST')
                        <div class="form-group">
                            <label for="station_id" class="required">Station</label>
                            <select class="form-control" name="station_id" id="station_id" required>
                                <option value="" selected disabled>- select station -</option>
                                @foreach ($config_pg as $item)
                                    <option value="{{ $item->station_id }}" @selected($station_id == $item->station_id)>
                                        {{ $item->station_code }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="date" class="required">Date</label>
                            <input type="date" class="form-control" id="date" name="date"
                                placeholder="input PG ID" autocomplete="off" value="{{ $date }}"
                                required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <a href="{{ route('transaksi.tiket.index') }}" class="btn btn-gradient-warning">Reset</a>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" form="addForm" class="btn btn-gradient-primary me-2">Submit</button>
                </div>
            </div>
        </div>
    </div>
    <!-- End Filter Modal -->
@endsection

@push('scripts')
    {{ $dataTable->scripts() }}
@endpush

@section('javascript')
    {{-- <script type="text/javascript">
        $(function() {
            var table = $('#transaksitiket-table').DataTable({
                // processing: true,
                serverSide: true,
                ajax: "",
                pageLength: 100, // Menampilkan 100 baris secara default
                lengthMenu: [1, 100, 200, 500, 1000],
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Cari..."
                },
            });

        });
    </script> --}}
@endsection
