@extends('layout.base')

@section('title-head')
    <title>Sam Card History</title>
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Data Sam Card History</h4>
                        <div class="btn-group my-2">
                            <button type="button" title="Add" class="btn btn-outline-primary btn-rounded btn-icon"
                                data-bs-toggle="modal" data-bs-target="#addModal">
                                <i class="mdi mdi-plus-circle"></i>
                            </button>
                            <button type="button" title="Filter" data-bs-toggle="modal" data-bs-target="#success-modal"
                                class="btn btn-outline-primary btn-rounded btn-icon">
                                <i class="mdi mdi-filter"></i>
                            </button>
                            <button type="button" title="Export" id="liveToastBtn"
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
                                        <th> Tanggal </th>
                                        <th> TID </th>
                                        <th> PIN </th>
                                        <th> MC </th>
                                        <th> Stasiun </th>
                                        <th> PG ID </th>
                                        <th> Type </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($sam_card_history as $item)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $item->tanggal ?? '-' }}</td>
                                            <td>{{ $item->sam_card->tid ?? '-' }}</td>
                                            <td>{{ $item->sam_card->pin ?? '-' }}</td>
                                            <td>{{ $item->sam_card->mc ?? '-' }}</td>
                                            <td>{{ $item->relasi_area->sub_lokasi->code ?? '-' }}</td>
                                            <td>{{ $item->pg_id ?? '-' }}</td>
                                            <td>{{ $item->type ?? '-' }}</td>
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

    <!-- Add Modal -->
    <div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Form Add</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addForm" action="{{ route('sam-history.store') }}" method="POST" class="forms-sample">
                        @csrf
                        @method('POST')
                        <div class="form-group">
                            <label for="sam_card_id">SAM Card</label>
                            <select name="sam_card_id" id="sam_card_id" class="form-control form-control-lg" required>
                                <option value="" selected disabled>- pilih SAM card -</option>
                                @foreach ($sam_card as $item)
                                    <option value="{{ $item->id }}">{{ $item->tid }} - {{ $item->pin }} -
                                        {{ $item->mc ?? 'No MC' }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="relasi_area_id">Stasiun</label>
                            <select name="relasi_area_id" id="relasi_area_id" class="form-control form-control-lg" required>
                                <option value="" selected disabled>- pilih stasiun -</option>
                                @foreach ($area as $item)
                                    <option value="{{ $item->id }}">{{ $item->sub_lokasi->name ?? '-' }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="pg_id">PG ID</label>
                            <input type="text" class="form-control" name="pg_id" id="pg_id"
                                placeholder="input PG ID" required autocomplete="off">
                        </div>
                        <div class="form-group">
                            <label for="type">Type</label>
                            <select name="type" id="type" class="form-control form-control-lg" required>
                                <option value="">- pilih type -</option>
                                <option value="entry">Entry</option>
                                <option value="exit">Exit</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="tanggal">Tanggal</label>
                            <input type="date" class="form-control" name="tanggal" id="tanggal"
                                placeholder="input tanggal" required>
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
@endsection
