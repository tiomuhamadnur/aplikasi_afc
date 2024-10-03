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
                    <form id="addForm" action="{{ route('sam-history.store') }}" method="POST" class="forms-sample"
                        enctype="multipart/form-data">
                        @csrf
                        @method('POST')
                        <div class="form-group">
                            <label for="sam_card_id">SAM Card</label>
                            <select name="sam_card_id" id="sam_card_id" class="tom-select-class" required>
                                <option value="" selected disabled>- pilih SAM card -</option>
                                @foreach ($sam_card as $item)
                                    <option value="{{ $item->id }}">{{ $item->tid }} - {{ $item->pin }} -
                                        {{ $item->mc ?? 'No MC' }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="equipment_id">PG ID</label>
                            <select name="equipment_id" id="equipment_id" class="tom-select-class" required>
                                <option value="" selected disabled>- pilih PG ID -</option>
                                @foreach ($pg as $item)
                                    <option value="{{ $item->id }}">{{ $item->name ?? '-' }} ({{ $item->code ?? '-' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="type">Type</label>
                            <select name="type" id="type" class="tom-select-class" required>
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
                        <div class="form-group">
                            <label for="old_uid">UID Old SAM Card <span class="text-info">(optional)</span></label>
                            <input type="text" class="form-control" name="old_uid" id="old_uid"
                                placeholder="input Old UID" autocomplete="off">
                        </div>
                        <div class="form-group">
                            <label for="old_sam_card_id">Old SAM Card <span class="text-info">(optional)</span></label>
                            <select name="old_sam_card_id" id="old_sam_card_id" class="tom-select-class">
                                <option value="" selected disabled>- pilih old SAM card -</option>
                                @foreach ($sam_card as $item)
                                    <option value="{{ $item->id }}">{{ $item->tid }} - {{ $item->pin }} -
                                        {{ $item->mc ?? 'No MC' }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="photo">Photo Old SAM Card</label>
                            <div class="text-center">
                                <img class="img-thumbnail" id="previewImage" src="#" alt="Preview"
                                    style="max-width: 250px; max-height: 250px; display: none;">
                            </div>
                            <input type="file" class="form-control" id="photo" name="photo" accept="image/*"
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

    <!-- Photo Modal -->
    <div class="modal fade" id="photoModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Photo Old SAM Card</h5>
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

    <!-- Add Filter -->
    <div class="modal fade" id="filterModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Form Filter</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="filterForm" action="{{ route('sam-history.index') }}" method="GET"
                        class="forms-sample">
                        @csrf
                        @method('GET')
                        <div class="form-group">
                            <label for="">Tanggal Expired</label>
                            <div class="input-group">
                                <input type="text" id="start_date" onfocus="(this.type='date')"
                                    onblur="(this.type='text')" class="form-control" placeholder="Start Date"
                                    name="start_date" autocomplete="off" value="{{ $start_date ?? null }}">
                                <input type="text" id="end_date" onfocus="(this.type='date')"
                                    onblur="(this.type='text')" class="form-control" placeholder="End Date"
                                    name="end_date" autocomplete="off" value="{{ $end_date ?? null }}">
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <a href="{{ route('sam-history.index') }}" class="btn btn-gradient-warning me-2">Reset</a>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" form="filterForm" class="btn btn-gradient-primary me-2">Filter</button>
                </div>
            </div>
        </div>
    </div>
    <!-- End Add Filter -->

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
