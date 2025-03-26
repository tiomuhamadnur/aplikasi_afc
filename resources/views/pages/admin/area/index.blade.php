@extends('layout.base')

@section('title-head')
    <title>Admin | Area</title>
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Data Area</h4>
                        <div class="btn-group my-2">
                            <button type="button" title="Add" class="btn btn-outline-primary btn-rounded btn-icon"
                                data-bs-toggle="modal" data-bs-target="#addModal">
                                <i class="mdi mdi-plus-circle"></i>
                            </button>
                            <button type="button" title="Filter" class="btn btn-outline-primary btn-rounded btn-icon"
                                data-bs-toggle="modal" data-bs-target="#filterModal">
                                <i class="mdi mdi-filter"></i>
                            </button>
                            <button type="button" title="Export" class="btn btn-outline-primary btn-rounded btn-icon"
                                data-bs-toggle="modal" data-bs-target="#exportExcelModal">
                                <i class="mdi mdi-file-export"></i>
                            </button>
                        </div>
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
                    <form id="addForm" action="{{ route('area.store') }}" method="POST" class="forms-sample">
                        @csrf
                        @method('POST')
                        <div class="form-group">
                            <label class="required" for="lokasi_id">Lokasi</label>
                            <select class="tom-select-class" name="lokasi_id" id="lokasi_id" required>
                                <option value="" selected disabled>- pilih lokasi -</option>
                                @foreach ($lokasi as $item)
                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="required" for="sub_lokasi_id">Sub Lokasi</label>
                            <select class="tom-select-class" name="sub_lokasi_id" id="sub_lokasi_id" required>
                                <option value="" selected disabled>- pilih sub lokasi -</option>
                                @foreach ($sub_lokasi as $item)
                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="required" for="detail_lokasi_id">Detail Lokasi</label>
                            <select class="tom-select-class" name="detail_lokasi_id" id="detail_lokasi_id" required>
                                <option value="" selected disabled>- pilih detail lokasi -</option>
                                @foreach ($detail_lokasi as $item)
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

    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Form Edit</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editForm" action="{{ route('area.update') }}" method="POST" class="forms-sample">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="id" id="id_edit">
                        <div class="form-group">
                            <label class="required" for="lokasi_id">Lokasi</label>
                            <select class="form-control" name="lokasi_id" id="lokasi_id_edit" required>
                                <option value="" selected disabled>- pilih lokasi -</option>
                                @foreach ($lokasi as $item)
                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="required" for="sub_lokasi_id">Sub Lokasi</label>
                            <select class="form-control" name="sub_lokasi_id" id="sub_lokasi_id_edit" required>
                                <option value="" selected disabled>- pilih sub lokasi -</option>
                                @foreach ($sub_lokasi as $item)
                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="required" for="detail_lokasi_id">Detail Lokasi</label>
                            <select class="form-control" name="detail_lokasi_id" id="detail_lokasi_id_edit" required>
                                <option value="" selected disabled>- pilih detail lokasi -</option>
                                @foreach ($detail_lokasi as $item)
                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" form="editForm" class="btn btn-gradient-primary me-2">Update</button>
                </div>
            </div>
        </div>
    </div>
    <!-- End Edit Modal -->

    <!-- Add Filter -->
    <div class="modal fade" id="filterModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Form Filter</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="filterForm" action="{{ route('area.index') }}" method="GET" class="forms-sample">
                        @csrf
                        @method('GET')
                        <div class="form-group">
                            <label for="lokasi_id">Lokasi</label>
                            <select class="tom-select-class" name="lokasi_id" id="lokasi_id">
                                <option value="" selected disabled>- pilih lokasi -</option>
                                @foreach ($lokasi as $item)
                                    <option value="{{ $item->id }}" @selected($item->id == $lokasi_id)>
                                        {{ $item->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="sub_lokasi_id">Sub Lokasi</label>
                            <select class="tom-select-class" name="sub_lokasi_id" id="sub_lokasi_id">
                                <option value="" selected disabled>- pilih sub lokasi -</option>
                                @foreach ($sub_lokasi as $item)
                                    <option value="{{ $item->id }}" @selected($item->id == $sub_lokasi_id)>
                                        {{ $item->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="detail_lokasi_id">Detail Lokasi</label>
                            <select class="tom-select-class" name="detail_lokasi_id" id="detail_lokasi_id">
                                <option value="" selected disabled>- pilih detail lokasi -</option>
                                @foreach ($detail_lokasi as $item)
                                    <option value="{{ $item->id }}" @selected($item->id == $detail_lokasi_id)>
                                        {{ $item->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <a href="{{ route('area.index') }}" class="btn btn-gradient-warning me-2">Reset</a>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" form="filterForm" class="btn btn-gradient-primary me-2">Filter</button>
                </div>
            </div>
        </div>
    </div>
    <!-- End Add Filter -->

    <!-- Delete Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Are you sure?</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="deleteForm" action="{{ route('area.delete') }}" method="POST" class="forms-sample">
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
            $('#editModal').on('show.bs.modal', function(e) {
                var id = $(e.relatedTarget).data('id');
                var lokasi_id = $(e.relatedTarget).data('lokasi_id');
                var sub_lokasi_id = $(e.relatedTarget).data('sub_lokasi_id');
                var detail_lokasi_id = $(e.relatedTarget).data('detail_lokasi_id');

                $('#id_edit').val(id);
                $('#lokasi_id_edit').val(lokasi_id);
                $('#sub_lokasi_id_edit').val(sub_lokasi_id);
                $('#detail_lokasi_id_edit').val(detail_lokasi_id);
            });

            $('#deleteModal').on('show.bs.modal', function(e) {
                var id = $(e.relatedTarget).data('id');

                $('#id_delete').val(id);
            });
        });
    </script>

    <script>
        function exportExcel() {
            $('#datatable-excel').click();
        }
    </script>
@endsection
