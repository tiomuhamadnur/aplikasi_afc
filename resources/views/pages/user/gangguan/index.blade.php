@extends('layout.base')

@section('title-head')
    <title>Failure Report</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css" rel="stylesheet">
    <style>
        .input-group {
            display: flex;
            width: 100%;
        }

        .input-group select {
            flex: 1 1 80%;
        }

        .input-group input {
            flex: 1 1 20%;
        }
    </style>
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Data Failure Report</h4>
                        <a href="{{ route('gangguan.create') }}" class="btn btn-gradient-primary btn-rounded">Create
                            Request</a>
                        <div class="btn-group my-2">
                            <button type="button" title="Filter" data-bs-toggle="modal" data-bs-target="#filterModal"
                                class="btn btn-outline-primary btn-rounded btn-icon">
                                <i class="mdi mdi-filter"></i>
                            </button>
                            <button type="button" title="Export Excel" data-bs-toggle="modal"
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
                    <form id="addForm" action="{{ route('gangguan.store') }}" method="POST" class="forms-sample"
                        enctype="multipart/form-data">
                        @csrf
                        @method('POST')
                        <div class="form-group">
                            <label for="report_by">Report By</label>
                            <input type="text" class="form-control" id="report_by" name="report_by" autocomplete="off"
                                required placeholder="input report by">
                        </div>
                        <div class="form-group">
                            <label for="report_date">Report Date</label>
                            <input type="datetime-local" class="form-control" id="report_date" name="report_date"
                                autocomplete="off" required>
                        </div>
                        <div class="form-group">
                            <label for="equipment_id">Equipment</label>
                            <select class="tom-select-gangguan" id="equipment_id" name="equipment_id" required>
                                <option value="" selected disabled>- pilih equipment -</option>
                                @foreach ($equipment as $item)
                                    <option value="{{ $item->id }}">{{ $item->name }} -
                                        ({{ $item->code ?? '-' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="category_id">Category</label>
                            <select class="tom-select-gangguan" name="category_id" id="category_id" required>
                                <option value="" selected disabled>- pilih category -</option>
                                @foreach ($category as $item)
                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="problem_id">Problem</label>
                            <select class="tom-select-gangguan" name="problem_id" id="problem_id" required>
                                <option value="" selected disabled>- pilih problem -</option>
                                <option value="0">- Other -</option>
                                @foreach ($problem as $item)
                                    <option value="{{ $item->id }}">
                                        {{ $item->category->name ?? '-' }} - {{ $item->tipe_equipment->code ?? '-' }} -
                                        {{ $item->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div id="problemOtherContainer" class="form-group" style="display: none">
                            <label for="problem_other">Problem Other</label>
                            <input type="text" class="form-control" id="problem_other" name="problem_other"
                                autocomplete="off" placeholder="input problem other">
                        </div>
                        <div class="form-group">
                            <label for="photo">Photo Before <span class="text-info">(optional)</span></label>
                            <div class="text-center">
                                <img class="img-thumbnail" id="previewImage" src="#" alt="Preview"
                                    style="max-width: 250px; max-height: 250px; display: none;">
                            </div>
                            <input type="file" class="form-control" id="photo" name="photo" autocomplete="off"
                                placeholder="input photo" accept="image/*">
                        </div>
                        <div class="form-group">
                            <label for="classification_id">Classification</label>
                            <select class="tom-select-gangguan" name="classification_id" id="classification_id" required>
                                <option value="" selected disabled>- pilih classification problem -</option>
                                @foreach ($classification as $item)
                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="action">Action</label>
                            <input type="text" class="form-control" id="action" name="action" autocomplete="off"
                                required placeholder="input action">
                        </div>
                        <div class="form-group">
                            <label for="response_date">Action Date</label>
                            <input type="datetime-local" class="form-control" id="response_date" name="response_date"
                                autocomplete="off" required>
                        </div>
                        <div class="form-group">
                            <label for="solved_by">Action By</label>
                            <input type="text" class="form-control" id="solved_by" name="solved_by"
                                autocomplete="off" required placeholder="input action by">
                        </div>
                        <div class="form-group">
                            <label for="solved_date">Solved Date</label>
                            <input type="datetime-local" class="form-control" id="solved_date" name="solved_date"
                                autocomplete="off" required>
                        </div>
                        <div class="form-group">
                            <label for="analysis">Analysis</label>
                            <input type="text" class="form-control" id="analysis" name="analysis"
                                autocomplete="off" required placeholder="input analysis">
                        </div>
                        <div class="form-group">
                            <label for="photo_after">Photo After <span class="text-info">(optional)</span></label>
                            <div class="text-center">
                                <img class="img-thumbnail" id="previewImageAfter" src="#" alt="Preview"
                                    style="max-width: 250px; max-height: 250px; display: none;">
                            </div>
                            <input type="file" class="form-control" id="photo_after" name="photo_after"
                                autocomplete="off" placeholder="input photo" accept="image/*">
                        </div>
                        <div class="form-group">
                            <label for="status_id">Status</label>
                            <select class="tom-select-gangguan" name="status_id" id="status_id" required>
                                <option value="" selected disabled>- pilih status -</option>
                                @foreach ($status as $item)
                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="is_changed">Ada pergantian Sparepart?</label>
                            <select class="tom-select-gangguan" name="is_changed" id="is_changed" required>
                                <option value="">- pilih keterangan -</option>
                                <option value="0">No</option>
                                <option value="1">Yes</option>
                            </select>
                        </div>
                        <div class="form-group" id="sparePartContainer" style="display: none">
                            <div class="d-flex justify-content-between align-items-center mx-auto mb-2">
                                <label for="barang_id" class="mb-0">Spare Part</label>
                                <button type="button" class="btn btn-success btn-rounded btn-icon" title="Add row"
                                    id="addRow">
                                    <i class="mdi mdi-plus-circle"></i>
                                </button>
                            </div>
                            <div id="inputContainer">
                                <div class="input-group">
                                    <select class="tom-select-gangguan col-8" id="barang_id" name="barang_ids[]">
                                        <option value="" selected disabled>- pilih spare part -</option>
                                        @foreach ($barang as $item)
                                            <option value="{{ $item->id }}">
                                                ({{ $item->material_number ?? '-' }})
                                                - {{ $item->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <input type="number" class="form-control col-3" id="qty" name="qty[]"
                                        placeholder="qty" min="1">
                                </div>
                            </div>
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
                    <form id="filterForm" action="{{ route('gangguan.index') }}" method="GET" class="forms-sample">
                        @csrf
                        @method('GET')
                        <div class="form-group">
                            <label for="">Tanggal</label>
                            <div class="input-group">
                                <input type="text" id="start_date" onfocus="(this.type='date')"
                                    onblur="(this.type='text')" class="form-control" placeholder="Start Date"
                                    name="start_date" autocomplete="off" value="{{ $start_date ?? null }}" required>
                                <input type="text" id="end_date" onfocus="(this.type='date')"
                                    onblur="(this.type='text')" class="form-control" placeholder="End Date"
                                    name="end_date" autocomplete="off" value="{{ $end_date ?? null }}" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="area_id">Lokasi</label>
                            <select class="tom-select-class" name="area_id" id="area_id">
                                <option value="" selected disabled>- pilih lokasi -</option>
                                @foreach ($area as $item)
                                    <option value="{{ $item->id }}" @if ($item->id == $area_id) selected @endif>
                                        {{ $item->sub_lokasi->name ?? '-' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="tipe_equipment_id">Tipe Equipment</label>
                            <select class="tom-select-class" name="tipe_equipment_id" id="tipe_equipment_id">
                                <option value="" selected disabled>- pilih tipe equipment -</option>
                                @foreach ($tipe_equipment as $item)
                                    <option value="{{ $item->id }}" @if ($item->id == $tipe_equipment_id) selected @endif>
                                        {{ $item->code }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="category_id">Category</label>
                            <select class="tom-select-class" name="category_id" id="category_id">
                                <option value="" selected disabled>- pilih category problem -</option>
                                @foreach ($category as $item)
                                    <option value="{{ $item->id }}" @if ($item->id == $category_id) selected @endif>
                                        {{ $item->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="classification_id">Classification</label>
                            <select class="tom-select-class" name="classification_id" id="classification_id">
                                <option value="" selected disabled>- pilih classification -</option>
                                @foreach ($classification as $item)
                                    <option value="{{ $item->id }}" @if ($item->id == $classification_id) selected @endif>
                                        {{ $item->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="status_id">Status</label>
                            <select class="tom-select-class" name="status_id" id="status_id">
                                <option value="" selected disabled>- pilih status -</option>
                                @foreach ($status as $item)
                                    <option value="{{ $item->id }}" @if($item->id == $status_id) selected @endif>
                                        {{ $item->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="is_changed">Is Changed Sparepart?</label>
                            <select class="tom-select-class" name="is_changed" id="is_changed">
                                <option value="" selected disabled>- pilih keterangan -</option>
                                <option value="1" @if($is_changed == "1") selected @endif>Yes</option>
                                <option value="0" @if($is_changed == "0") selected @endif>No</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="is_downtime">Is Downtime?</label>
                            <select class="tom-select-class" name="is_downtime" id="is_downtime">
                                <option value="" selected disabled>- pilih keterangan -</option>
                                <option value="1" @if($is_downtime == "1") selected @endif>Yes</option>
                                <option value="0" @if($is_downtime == "0") selected @endif>No</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <a href="{{ route('gangguan.index') }}" class="btn btn-gradient-warning">Reset</a>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" form="filterForm" class="btn btn-gradient-primary">Filter</button>
                </div>
            </div>
        </div>
    </div>
    <!-- End Add Filter -->

    <!-- Photo Modal -->
    <div class="modal fade" id="photoModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Detail</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="container">
                        <div class="mb-4 text-center align-middle">
                            <h5>Photo Before</h5>
                            <div class="border mx-auto">
                                <img src="#" id="photo_modal" class="img-thumbnail" alt="Tidak ada photo">
                            </div>
                        </div>
                    </div>
                    <div class="container">
                        <div class="mb-4 text-center align-middle">
                            <h5>Photo After</h5>
                            <div class="border mx-auto">
                                <img src="#" id="photo_after_modal" class="img-thumbnail" alt="Tidak ada photo">
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
                    <form id="deleteForm" action="{{ route('gangguan.delete') }}" method="POST" class="forms-sample">
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
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var isChangedSelect = document.getElementById('is_changed');
            var sparePartContainer = document.getElementById('sparePartContainer');
            var barangSelect = document.getElementById('barang_id');
            var qtyInput = document.getElementById('qty');

            function removeAllTomSelectBaru() {
                var elements = document.querySelectorAll('.tom-select-baru');

                elements.forEach(function(element) {
                    element.remove();
                });
            }

            function updateDisplay() {
                if (isChangedSelect.value === '1') {
                    sparePartContainer.style.display = 'block';
                    barangSelect.required = true;
                    qtyInput.required = true;
                } else {
                    sparePartContainer.style.display = 'none';
                    barangSelect.required = false;
                    qtyInput.required = false;
                    removeAllTomSelectBaru();
                }
            }

            // Initial update on page load
            updateDisplay();

            // Add event listener to handle changes
            isChangedSelect.addEventListener('change', function() {
                updateDisplay();
            });

            var addRowButton = document.getElementById('addRow');
            var inputContainer = document.getElementById('inputContainer');
            var settings = {}; // Atur pengaturan Tom Select sesuai kebutuhan

            function initializeTomSelect(selector) {
                document.querySelectorAll(selector).forEach(function(el) {
                    if (!el.tomSelectInstance) {
                        el.tomSelectInstance = new TomSelect(el, settings);
                    }
                });
            }

            // Inisialisasi Tom Select pada load halaman
            // initializeTomSelect('.tom-select-class');

            addRowButton.addEventListener('click', function() {
                var row = document.createElement('div');
                row.classList.add('input-group');
                row.classList.add('tom-select-baru');
                var uniqueClass = 'tom-select-' + Date.now();
                row.innerHTML = `
                    <select class="tom-select-class ${uniqueClass} col-8" name="barang_ids[]" required>
                        <option value="" selected disabled>- pilih spare part -</option>
                        @foreach ($barang as $item)
                            <option value="{{ $item->id }}">
                                ({{ $item->material_number ?? '-' }}) - {{ $item->name }}
                            </option>
                        @endforeach
                    </select>
                    <input type="number" class="form-control col-3" name="qty[]" placeholder="qty" required min="1">
                    <div class="input-group-append">
                        <button type="button" class="btn btn-danger btn-rounded btn-icon removeRow" title="Remove row" style="padding: 0.25rem 0.5rem; font-size: 0.75rem; line-height: 1.5;">
                            <i class="mdi mdi-minus-circle"></i>
                        </button>
                    </div>
                `;
                inputContainer.appendChild(row);

                // Inisialisasi Tom Select untuk elemen baru
                initializeTomSelect('.' + uniqueClass);

                // Add event listener for the new remove button
                row.querySelector('.removeRow').addEventListener('click', function() {
                    if (inputContainer.children.length > 1) {
                        inputContainer.removeChild(row);
                    } else {
                        console.warn('Cannot remove the last row.');
                    }
                });
            });

            // Event delegation for remove buttons in existing rows
            inputContainer.addEventListener('click', function(event) {
                if (event.target.classList.contains('removeRow')) {
                    var row = event.target.closest('.input-group');
                    if (inputContainer.children.length > 1) {
                        inputContainer.removeChild(row);
                    }
                }
            });
        });
    </script>


    <script>
        $(document).ready(function() {
            const problemSelect = document.getElementById('problem_id');
            const problemOtherContainer = document.getElementById('problemOtherContainer');
            const problemOtherInput = document.getElementById('problem_other');

            problemSelect.addEventListener('change', function() {
                if (problemSelect.value === '0') {
                    // Jika problem_id bernilai null (selected value is empty)
                    problemOtherContainer.style.display = 'block';
                    problemOtherInput.setAttribute('required', 'required');
                } else {
                    // Jika problem_id tidak bernilai null
                    problemOtherContainer.style.display = 'none';
                    problemOtherInput.removeAttribute('required');
                    problemOtherInput.value = ''; // Mengosongkan input jika disembunyikan
                }
            });



            var settings = {};
            document.querySelectorAll('.tom-select-gangguan').forEach(function(el) {
                new TomSelect(el, settings);
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

            const imageInputAfter = document.getElementById('photo_after');
            const previewImageAfter = document.getElementById('previewImageAfter');

            imageInputAfter.addEventListener('change', function(event) {
                const selectedFile = event.target.files[0];

                if (selectedFile) {
                    const reader = new FileReader();

                    reader.onload = function(e) {
                        previewImageAfter.src = e.target.result;
                        previewImageAfter.style.display = 'block';
                    }

                    reader.readAsDataURL(selectedFile);
                }
            });

            $('#photoModal').on('show.bs.modal', function(e) {
                var photo = $(e.relatedTarget).data('photo');
                var photo_after = $(e.relatedTarget).data('photo_after');

                document.getElementById("photo_modal").src = photo;
                document.getElementById("photo_after_modal").src = photo_after;
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
