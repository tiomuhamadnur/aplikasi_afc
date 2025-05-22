@extends('layout.base')

@section('title-head')
    <title>Dokumen</title>
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Data Dokumen</h4>
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
                    <form id="filterForm" action="{{ route('dokumen.index') }}" method="GET" class="forms-sample">
                        @csrf
                        @method('GET')
                        <div class="form-group">
                            <label for="">Departemen</label>
                            <select class="tom-select-class" name="departemen_id">
                                <option value="" selected disabled>- pilih departemen -</option>
                                @foreach ($departemen as $item)
                                    <option value="{{ $item->id }}" @selected($item->id == $departemen_id)>
                                        {{ $item->code }} ({{ $item->name }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="">Tipe Dokumen</label>
                            <select class="tom-select-class" name="tipe_dokumen_id">
                                <option value="" selected disabled>- pilih tipe dokumen -</option>
                                @foreach ($tipe_dokumen as $item)
                                    <option value="{{ $item->id }}" @selected($item->id == $tipe_dokumen_id)>
                                        {{ $item->code }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <a href="{{ route('dokumen.index') }}" class="btn btn-gradient-warning me-2">Reset</a>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
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
                    <form id="addForm" action="{{ route('dokumen.store') }}" method="POST" class="forms-sample"
                        enctype="multipart/form-data">
                        @csrf
                        @method('POST')
                        <div class="form-group">
                            <label for="departemen_id" class="required">Departemen</label>
                            <select class="form-control" id="departemen_id" name="departemen_id" required>
                                <option value="" selected disabled>- pilih departemen -</option>
                                @foreach ($departemen as $item)
                                    <option value="{{ $item->id }}">{{ $item->code }} ({{ $item->name }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="tipe_dokumen_id" class="required">Tipe Dokumen</label>
                            <select class="form-control" id="tipe_dokumen_id" name="tipe_dokumen_id" required>
                                <option value="" selected disabled>- pilih tipe dokumen -</option>
                                @foreach ($tipe_dokumen as $item)
                                    <option value="{{ $item->id }}">{{ $item->code }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="judul" class="required">Judul Dokumen</label>
                            <input type="text" class="form-control" id="judul" name="judul"
                                placeholder="Input judul dokumen" autocomplete="off" required>
                        </div>
                        <div class="form-group">
                            <label for="nomor" class="required">Nomor Dokumen</label>
                            <input type="text" class="form-control" id="nomor" name="nomor"
                                placeholder="Input Nomor dokumen" autocomplete="off" required>
                        </div>
                        <div class="form-group">
                            <label for="nomor_revisi" class="required">Nomor Revisi</label>
                            <input type="text" class="form-control" id="nomor_revisi" name="nomor_revisi"
                                placeholder="Input Nomor revisi" autocomplete="off" required>
                        </div>
                        <div class="form-group">
                            <label for="tanggal_pengesahan" class="required">Tanggal Pengesahan</label>
                            <input type="date" class="form-control" id="tanggal_pengesahan" name="tanggal_pengesahan"
                                placeholder="Input Tanggal pengesahan" autocomplete="off" required>
                        </div>
                        <div class="form-group">
                            <label for="url" class="required">URL Dokumen</label>
                            <input type="text" class="form-control" id="url" name="url"
                                placeholder="Input Link/URL dokumen" autocomplete="off" required>
                        </div>
                        <div class="form-group">
                            <label for="keterangan">Keterangan</label>
                            <textarea class="form-control" name="keterangan" id="keterangan" placeholder="Input keterangan (opsional)" rows="4"></textarea>
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
                    <form id="editForm" action="{{ route('dokumen.update') }}" method="POST" class="forms-sample"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="id" id="id_edit">
                        <div class="form-group">
                            <label for="departemen_id" class="required">Departemen</label>
                            <select class="form-control" id="departemen_id_edit" name="departemen_id" required>
                                <option value="" selected disabled>- pilih departemen -</option>
                                @foreach ($departemen as $item)
                                    <option value="{{ $item->id }}">{{ $item->code }} ({{ $item->name }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="tipe_dokumen_id" class="required">Tipe Dokumen</label>
                            <select class="form-control" id="tipe_dokumen_id_edit" name="tipe_dokumen_id" required>
                                <option value="" selected disabled>- pilih tipe dokumen -</option>
                                @foreach ($tipe_dokumen as $item)
                                    <option value="{{ $item->id }}">{{ $item->code }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="judul" class="required">Judul Dokumen</label>
                            <input type="text" class="form-control" id="judul_edit" name="judul"
                                placeholder="Input judul dokumen" autocomplete="off" required>
                        </div>
                        <div class="form-group">
                            <label for="nomor" class="required">Nomor Dokumen</label>
                            <input type="text" class="form-control" id="nomor_edit" name="nomor"
                                placeholder="Input Nomor dokumen" autocomplete="off" required>
                        </div>
                        <div class="form-group">
                            <label for="nomor_revisi" class="required">Nomor Revisi</label>
                            <input type="text" class="form-control" id="nomor_revisi_edit" name="nomor_revisi"
                                placeholder="Input Nomor revisi" autocomplete="off" required>
                        </div>
                        <div class="form-group">
                            <label for="tanggal_pengesahan" class="required">Tanggal Pengesahan</label>
                            <input type="date" class="form-control" id="tanggal_pengesahan_edit" name="tanggal_pengesahan"
                                placeholder="Input Tanggal pengesahan" autocomplete="off" required>
                        </div>
                        <div class="form-group">
                            <label for="url" class="required">URL Dokumen</label>
                            <input type="text" class="form-control" id="url_edit" name="url"
                                placeholder="Input Link/URL dokumen" autocomplete="off" required>
                        </div>
                        <div class="form-group">
                            <label for="keterangan">Keterangan</label>
                            <textarea class="form-control" name="keterangan" id="keterangan_edit" placeholder="Input keterangan (opsional)" rows="4"></textarea>
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

    <!-- Delete Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Are you sure?</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="deleteForm" action="{{ route('dokumen.delete') }}" method="POST"
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
            $('#editModal').on('show.bs.modal', function(e) {
                var id = $(e.relatedTarget).data('id');
                var departemen_id = $(e.relatedTarget).data('departemen_id');
                var tipe_dokumen_id = $(e.relatedTarget).data('tipe_dokumen_id');
                var judul = $(e.relatedTarget).data('judul');
                var nomor = $(e.relatedTarget).data('nomor');
                var nomor_revisi = $(e.relatedTarget).data('nomor_revisi');
                var tanggal_pengesahan = $(e.relatedTarget).data('tanggal_pengesahan');
                var url = $(e.relatedTarget).data('url');
                var keterangan = $(e.relatedTarget).data('keterangan');

                $('#id_edit').val(id);
                $('#departemen_id_edit').val(departemen_id);
                $('#tipe_dokumen_id_edit').val(tipe_dokumen_id);
                $('#judul_edit').val(judul);
                $('#nomor_edit').val(nomor);
                $('#nomor_revisi_edit').val(nomor_revisi);
                $('#tanggal_pengesahan_edit').val(tanggal_pengesahan);
                $('#url_edit').val(url);
                $('#keterangan_edit').val(keterangan);
            });

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
