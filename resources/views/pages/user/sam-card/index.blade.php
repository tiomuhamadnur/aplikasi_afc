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
                            @if (auth()->user()->role_id == 1)
                                <button type="button" title="Add" class="btn btn-outline-primary btn-rounded btn-icon"
                                    data-bs-toggle="modal" data-bs-target="#addModal">
                                    <i class="mdi mdi-plus-circle"></i>
                                </button>
                                <button type="button" title="Import" class="btn btn-outline-primary btn-rounded btn-icon"
                                    data-bs-toggle="modal" data-bs-target="#importModal">
                                    <i class="mdi mdi-file-import"></i>
                                </button>
                            @endif
                            <button type="button" title="Filter" data-bs-toggle="modal" data-bs-target="#filterModal"
                                class="btn btn-outline-primary btn-rounded btn-icon">
                                <i class="mdi mdi-filter"></i>
                            </button>
                            <button type="button" title="Export to Excel" data-bs-toggle="modal"
                                data-bs-target="#exportExcelModal" class="btn btn-outline-primary btn-rounded btn-icon">
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
                            <label for="uid" class="required">UID</label>
                            <input type="text" class="form-control" id="uid" name="uid" placeholder="Input UID"
                                autocomplete="off" required>
                        </div>
                        <div class="form-group">
                            <label for="mid" class="required">MID</label>
                            <input type="text" class="form-control" id="mid" name="mid" placeholder="Input MID"
                                autocomplete="off" required>
                        </div>
                        <div class="form-group">
                            <label for="tid" class="required">TID</label>
                            <input type="text" class="form-control" id="tid" name="tid" placeholder="Input TID"
                                autocomplete="off" required>
                        </div>
                        <div class="form-group">
                            <label for="pin" class="required">PIN</label>
                            <input type="text" class="form-control" id="pin" name="pin" placeholder="Input PIN"
                                autocomplete="off" required>
                        </div>
                        <div class="form-group">
                            <label for="mc" class="required">Merry Code</label>
                            <input type="text" class="form-control" id="mc" name="mc"
                                placeholder="Input Merry Code" autocomplete="off" required>
                        </div>
                        <div class="form-group">
                            <label for="alokasi">Alokasi Stasiun <span class="text-info">(opsional)</span></label>
                            <input type="text" class="form-control" id="alokasi" name="alokasi"
                                placeholder="Input Alokasi Stasiun" autocomplete="off">
                        </div>
                        <div class="form-group">
                            <label for="photo">Photo <span class="text-info">(opsional)</span></label>
                            <div class="text-center">
                                <img class="img-thumbnail" id="previewImage" src="#" alt="Preview"
                                    style="max-width: 250px; max-height: 250px; display: none;">
                            </div>
                            <input type="file" class="form-control" id="photo" name="photo" accept="image/*">
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

    <!-- Add Filter -->
    <div class="modal fade" id="filterModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Form Filter</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="filterForm" action="{{ route('sam-card.index') }}" method="GET" class="forms-sample">
                        @csrf
                        @method('GET')
                        <div class="form-group">
                            <label for="">Status</label>
                            <select class="tom-select-class" name="status">
                                <option value="" selected disabled>- pilih status -</option>
                                <option value="ready" @selected($status == 'ready')>Ready</option>
                                <option value="used" @selected($status == 'used')>Used</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <a href="{{ route('sam-card.index') }}" class="btn btn-gradient-warning me-2">Reset</a>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" form="filterForm" class="btn btn-gradient-primary me-2">Filter</button>
                </div>
            </div>
        </div>
    </div>
    <!-- End Add Filter -->

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

    <!-- Photo Modal -->
    <div class="modal fade" id="photoModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Photo SAM Card</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="container">
                        <div class="mb-4 text-center align-middle">
                            <div class="border mx-auto">
                                <img src="#" id="photo_modal" class="img-thumbnail" alt="Tidak ada photo">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <!-- End Photo Modal -->

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

            $('#photoModal').on('show.bs.modal', function(e) {
                var photo = $(e.relatedTarget).data('photo');
                document.getElementById("photo_modal").src = photo;
            });

            const imageInput = document.getElementById('photo');
            const previewImage = document.getElementById('previewImage');

            imageInput.addEventListener('change', function(event) {
                const selectedFile = event.target.files[0];

                if (selectedFile) {
                    const reader = new FileReader();

                    reader.onload = function(e) {
                        previewImage.src = e.target.result;
                        previewImage.style.display = 'block';
                    }

                    reader.readAsDataURL(selectedFile);
                }
            });
        });
    </script>

    <script>
        function exportExcel() {
            document.getElementById('datatable-excel').click();
        }
    </script>
@endsection
