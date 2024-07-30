@extends('layout.base')

@section('title-head')
    <title>Transaksi Barang</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Data Transaksi Barang</h4>
                        <div class="btn-group my-2">
                            <button type="button" title="Add" class="btn btn-outline-primary btn-rounded btn-icon"
                                data-bs-toggle="modal" data-bs-target="#addModal">
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
                            {{ $dataTable->table() }}
                            {{-- <table class="table table-responsive table-hover data-table">
                                <thead>
                                    <tr>
                                        <th> # </th>
                                        <th style="width: 200px"> Material Name</th>
                                        <th> Material Number </th>
                                        <th> Qty. </th>
                                        <th> Equipment Name </th>
                                        <th> Equipment ID </th>
                                        <th> Location </th>
                                        <th> Tanggal </th>
                                        <th> Updated By </th>
                                        <th> Action </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($transaksi_barang as $item)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td class="text-wrap">{{ $item->barang->name ?? '-' }}</td>
                                            <td>{{ $item->barang->material_number ?? '-' }}</td>
                                            <td>{{ $item->qty ?? '-' }}</td>
                                            <td>{{ $item->equipment->name ?? '-' }}</td>
                                            <td>{{ $item->equipment->code ?? '-' }}</td>
                                            <td>{{ $item->equipment->relasi_area->sub_lokasi->name ?? '-' }}</td>
                                            <td>{{ $item->tanggal ?? '-' }}</td>
                                            <td>{{ $item->user->name ?? '-' }}</td>
                                            <td>
                                                <a href="{{ route('transaksi-barang.edit', $item->uuid) }}" title="Edit">
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

    <!-- Add Modal -->
    <div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Form Add</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addForm" action="{{ route('transaksi-barang.store') }}" method="POST" class="forms-sample"
                        enctype="multipart/form-data">
                        @csrf
                        @method('POST')
                        <div class="form-group">
                            <label for="barang_id">Material</label>
                            <select class="tom-select-class" id="barang_id" name="barang_id" required>
                                <option value="" selected disabled>- pilih material -</option>
                                @foreach ($barang as $item)
                                    <option value="{{ $item->id }}">{{ $item->material_number ?? '#' }} -
                                        {{ $item->name ?? '#' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="qty">Quantity</label>
                            <input type="number" min="1" class="form-control" id="qty" name="qty"
                                autocomplete="off" placeholder="jumlah material" required>
                        </div>
                        <div class="form-group">
                            <label for="equipment_id">Equipment</label>
                            <select class="tom-select-class" id="equipment_id" name="equipment_id" required>
                                <option value="" selected disabled>- pilih equipment -</option>
                                @foreach ($equipment as $item)
                                    <option value="{{ $item->id }}">{{ $item->name }} -
                                        ({{ $item->code ?? '-' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="tanggal">Tanggal</label>
                            <input type="date" class="form-control" id="tanggal" name="tanggal" autocomplete="off"
                                required>
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
                    <form id="deleteForm" action="{{ route('transaksi-barang.delete') }}" method="POST"
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
