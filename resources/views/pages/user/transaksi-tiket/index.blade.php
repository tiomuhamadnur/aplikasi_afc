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
                        <a href="{{ route('transaksi.tiket.ftp') }}" title="Sync">
                            <span><i class="mdi mdi-refresh"></i></span>
                        </a>
                        <div class="table-responsive">
                            {{ $dataTable->table() }}
                            {{-- <table class="table table-hover text-center data-table">
                                <thead>
                                    <tr>
                                        <th> # </th>
                                        <th> Transaction <br> Type </th>
                                        <th> Transaction <br> ID </th>
                                        <th> Device </th>
                                        <th> Corner <br> ID </th>
                                        <th> PG <br> ID </th>
                                        <th> PAN </th>
                                        <th> Transaction <br> Amount </th>
                                        <th> Balance <br> Before </th>
                                        <th> Balance <br> After </th>
                                        <th> Card <br> Type </th>
                                        <th> Tap In Time </th>
                                        <th> Tap In Station </th>
                                        <th> Tap Out Time </th>
                                        <th> Tap Out Station </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($transaksi as $item)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $item->transaction_type }}</td>
                                            <td>{{ $item->transaction_id }}</td>
                                            <td>{{ $item->device }}</td>
                                            <td>{{ $item->corner_id }}</td>
                                            <td>{{ $item->pg_id }}</td>
                                            <td>{{ $item->pan }}</td>
                                            <td>{{ $item->transaction_amount }}</td>
                                            <td>{{ $item->balance_before }}</td>
                                            <td>{{ $item->balance_after }}</td>
                                            <td>{{ $item->card_type }}</td>
                                            <td>{{ $item->tap_in_time }}</td>
                                            <td>{{ $item->tap_in_station }}</td>
                                            <td>{{ $item->tap_out_time }}</td>
                                            <td>{{ $item->tap_out_station }}</td>
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
