@extends('layout.base')

@section('title-head')
    <title>Sam Card</title>
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Data Sam Card</h4>
                        <div class="btn-group my-2">
                            <button type="button" title="Add" class="btn btn-outline-primary btn-rounded btn-icon"
                                data-bs-toggle="modal" data-bs-target="#addModal">
                                <i class="mdi mdi-plus-circle"></i>
                            </button>
                            <button type="button" title="Filter" data-bs-toggle="modal" data-bs-target="#success-modal"
                                class="btn btn-outline-primary btn-rounded btn-icon">
                                <i class="mdi mdi-filter"></i>
                            </button>
                            {{-- <button type="button" title="Marry Code SAM Card" data-bs-toggle="modal"
                                data-bs-target="#samModal" class="btn btn-outline-primary btn-rounded btn-icon">
                                <i class="mdi mdi-lan-pending"></i>
                            </button> --}}
                            <button type="button" title="Import" class="btn btn-outline-primary btn-rounded btn-icon"
                                data-bs-toggle="modal" data-bs-target="#importModal">
                                <i class="mdi mdi-file-import"></i>
                            </button>
                        </div>
                        <div class="table-responsive">
                            {{ $dataTable->table() }}
                            {{-- <table class="table .table-hover text-center">
                                <thead>
                                    <tr>
                                        <th> # </th>
                                        <th> UID </th>
                                        <th> TID </th>
                                        <th> MID </th>
                                        <th> PIN </th>
                                        <th> MC </th>
                                        <th> Alokasi </th>
                                        <th> Status </th>
                                        <th> Aksi </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($sam_card as $item)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $item->uid ?? '-' }}</td>
                                            <td>{{ $item->tid ?? '-' }}</td>
                                            <td>{{ $item->mid ?? '-' }}</td>
                                            <td>{{ $item->pin ?? '-' }}</td>
                                            <td>{{ $item->mc ?? '-' }}</td>
                                            <td>{{ $item->alokasi ?? '-' }}</td>
                                            <td>
                                                <label
                                                    class="badge @if ($item->status == 'ready') badge-gradient-success @else badge-gradient-danger @endif text-uppercase">
                                                    {{ $item->status }}
                                                </label>
                                            </td>
                                            <td>
                                                <a href="{{ route('sam-card.edit', $item->uuid) }}" title="Edit">
                                                    <button type="button"
                                                        class="btn btn-gradient-warning btn-rounded btn-icon">
                                                        <i class="text-white mdi mdi-lead-pencil"></i>
                                                    </button>
                                                </a>
                                                @if ($item->mc != null)
                                                    <a href="{{ route('sam-history.create', $item->uuid) }}"
                                                        title="Use this SAM Card">
                                                        <button type="button"
                                                            class="btn btn-gradient-success btn-rounded btn-icon">
                                                            <i class="text-white mdi mdi-rocket"></i>
                                                        </button>
                                                    </a>
                                                @endif
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

    <!-- Add Modal -->
    <div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Form Add</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addForm" action="{{ route('sam-card.store') }}" method="POST" class="forms-sample"
                        enctype="multipart/form-data">
                        @csrf
                        @method('POST')
                        <div class="form-group">
                            <label for="uid">UID</label>
                            <input type="text" class="form-control" id="uid" name="uid" placeholder="UID"
                                autocomplete="off" required>
                        </div>
                        <div class="form-group">
                            <label for="mid">MID</label>
                            <input type="text" class="form-control" id="mid" name="mid" placeholder="MID"
                                autocomplete="off" required>
                        </div>
                        <div class="form-group">
                            <label for="tid">TID</label>
                            <input type="text" class="form-control" id="tid" name="tid" placeholder="TID"
                                autocomplete="off" required>
                        </div>
                        <div class="form-group">
                            <label for="pin">PIN</label>
                            <input type="text" class="form-control" id="pin" name="pin" placeholder="PIN"
                                autocomplete="off" required>
                        </div>
                        <div class="form-group">
                            <label for="mc">Merry Code</label>
                            <input type="text" class="form-control" id="mc" name="mc"
                                placeholder="Merry Code" autocomplete="off" required>
                        </div>
                        <div class="form-group">
                            <label for="alokasi">Alokasi <span class="text-info">(opsional)</span></label>
                            <input type="text" class="form-control" id="alokasi" name="alokasi" placeholder="Alokasi"
                                autocomplete="off">
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

    <!-- Merry Code Modal -->
    <div class="modal fade" id="samModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Form Merry Code</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="samForm" action="{{ route('sam-card.merry-code.store') }}" method="POST"
                        class="forms-sample">
                        @csrf
                        @method('POST')
                        <div class="form-group">
                            <label for="comm">COM</label>
                            <input type="number" class="form-control" name="com" id="com" required
                                min="1" max="10" placeholder="Input COM Reader">
                        </div>
                        <div class="form-group">
                            <label for="slot">SLOT</label>
                            <select name="slot" id="slot" class="form-control form-control-lg" required>
                                <option value="">- pilih slot -</option>
                                <option value="1">Slot 1</option>
                                <option value="2">Slot 2</option>
                                <option value="3">Slot 3</option>
                                <option value="4">Slot 4</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="uid">UID</label>
                            <input type="text" class="form-control" id="uid" name="uid"
                                placeholder="Input UID" autocomplete="off" required>
                        </div>
                        <div class="form-group">
                            <label for="mid">MID</label>
                            <input type="text" class="form-control" id="mid" name="mid"
                                placeholder="Input MID" autocomplete="off" required>
                        </div>
                        <div class="form-group">
                            <label for="tid">TID</label>
                            <input type="text" class="form-control" id="tid" name="tid"
                                placeholder="Input TID" autocomplete="off" required>
                        </div>
                        <div class="form-group">
                            <label for="pin">PIN</label>
                            <input type="text" class="form-control" id="pin" name="pin"
                                placeholder="Input PIN" autocomplete="off" required>
                        </div>
                        <div class="form-group">
                            <label for="alokasi">Alokasi <span class="text-info">(opsional)</span></label>
                            <input type="text" class="form-control" id="alokasi" name="alokasi"
                                placeholder="Alokasi Stasiun" autocomplete="off">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" form="samForm" class="btn btn-gradient-primary me-2">Generate</button>
                </div>
            </div>
        </div>
    </div>
    <!-- End Merry Code Modal -->

    <!-- Import Modal -->
    <div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Form Import</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="importForm" action="{{ route('sam-card.import') }}" method="POST" class="forms-sample"
                        enctype="multipart/form-data">
                        @csrf
                        @method('POST')
                        <div class="form-group">
                            <label for="">
                                File Import
                                <span>
                                    <a href="{{ asset('assets/format/Template Format Import Sam Card.xlsx') }}">
                                        <button type="button" class="btn btn-icon btn-sm btn-success btn-rounded p-0"
                                            title="Download Template File Import"><i class="mdi mdi-cloud-download"></i>
                                        </button>
                                    </a>
                                </span>
                            </label>
                            <input type="file" class="form-control form-control-lg" id="file"
                                accept=".xls,.xlsx" name="file" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" form="importForm" class="btn btn-gradient-primary me-2">Submit</button>
                </div>
            </div>
        </div>
    </div>
    <!-- End Import Modal -->

    <!-- Delete Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Are you sure?</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="deleteForm" action="{{ route('sam-card.delete') }}" method="POST" class="forms-sample">
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
