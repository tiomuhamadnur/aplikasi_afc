@extends('layout.base')

@section('title-head')
    <title>Log AFC</title>
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Data Log AFC</h4>
                        <div class="btn-group my-2">
                            <button type="button" title="Add" class="btn btn-outline-primary btn-rounded btn-icon"
                                data-bs-toggle="modal" data-bs-target="#importLogModal">
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
                        <div class="table-responsive">
                            <table class="table table-bordered text-center">
                                <thead>
                                    <tr>
                                        <th> # </th>
                                        <th> Tanggal </th>
                                        <th> Waktu </th>
                                        <th> Bank </th>
                                        <th> PAN </th>
                                        <th> Elapsed Time </th>
                                        <th> Transaction Speed </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="text-center" colspan="7">
                                            Silahkan upload data log file!
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

    <!-- Import Log Modal -->
    <div class="modal fade" id="importLogModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Form Import Log</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="importLogForm" action="{{ route('log.import') }}" method="POST" class="forms-sample"
                        enctype="multipart/form-data">
                        @csrf
                        @method('POST')
                        <div class="form-group">
                            <label for="tanggal">
                                Tanggal
                            </label>
                            <input type="date" class="form-control form-control-lg" id="tanggal" name="tanggal"
                                required>
                        </div>
                        <div class="form-group">
                            <label for="stasiun">
                                Stasiun
                            </label>
                            <input type="text" class="form-control form-control-lg" id="stasiun" name="stasiun"
                                required placeholder="Nama Stasiun">
                        </div>
                        <div class="form-group">
                            <label for="nomor">
                                Nomor PG
                            </label>
                            <input type="text" class="form-control form-control-lg" id="nomor" name="nomor"
                                required placeholder="Nomor PG">
                        </div>
                        <div class="form-group">
                            <label for="">
                                Log File
                            </label>
                            <input type="file"
                                class="form-control form-control-lg @error('logfile') is-invalid @enderror" id="logfile"
                                accept=".txt,.log" name="logfile" required>
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
                    <button type="submit" form="importLogForm" class="btn btn-gradient-primary me-2">Generate</button>
                </div>
            </div>
        </div>
    </div>
    <!-- End Import Log Modal -->
@endsection
