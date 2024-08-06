@extends('layout.base')

@section('title-head')
    <title>Gangguan</title>
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
                        <h4 class="card-title">Data Gangguan</h4>
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
                                    <tr class="text-center">
                                        <th>Ticket Number</th>
                                        <th>Station</th>
                                        <th>Report Date</th>
                                        <th>Report By</th>
                                        <th>Equipment Type</th>
                                        <th>Equipment ID</th>
                                        <th>Problem</th>
                                        <th>Category</th>
                                        <th>Action</th>
                                        <th>Action Date</th>
                                        <th>Action By</th>
                                        <th>Solved Date</th>
                                        <th>Analysis</th>
                                        <th>Class</th>
                                        <th>Status</th>
                                        <th>Photo</th>
                                        <th>Changed Sparepart?</th>
                                        <th>#</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($gangguan as $item)
                                        <tr>
                                            <td class="fw-bolder">{{ $item->ticket_number ?? '-' }}</td>
                                            <td>{{ $item->equipment->relasi_area->sub_lokasi->name ?? '-' }}</td>
                                            <td>{{ $item->report_date ?? '-' }}</td>
                                            <td>{{ $item->report_by ?? '-' }}</td>
                                            <td>{{ $item->equipment->tipe_equipment->code ?? '-' }}</td>
                                            <td>{{ $item->equipment->code ?? '-' }}</td>
                                            <td>{{ $item->problem ?? '-' }}</td>
                                            <td>{{ $item->category ?? '-' }}</td>
                                            <td>{{ $item->action ?? '-' }}</td>
                                            <td>{{ $item->response_date ?? '-' }}</td>
                                            <td>{{ $item->solved_by ?? '-' }}</td>
                                            <td>{{ $item->solved_date ?? '-' }}</td>
                                            <td>{{ $item->analysis ?? '-' }}</td>
                                            <td>{{ $item->classification ?? '-' }}</td>
                                            <td>
                                                <label
                                                    class="badge @if ($item->status == 'closed') badge-gradient-success @elseif ($item->status == 'pending') badge-gradient-warning @else badge-gradient-danger @endif text-uppercase">
                                                    {{ $item->status }}
                                                </label>
                                            </td>
                                            <td class="text-center">
                                                <button type="button" title="Show"
                                                    class="btn btn-gradient-primary btn-rounded btn-icon"
                                                    data-bs-toggle="modal" data-bs-target="#photoModal"
                                                    data-photo='{{ asset('storage/' . $item->photo) }}'>
                                                    <i class="mdi mdi-eye"></i>
                                                </button>
                                            </td>
                                            <td class="text-center">{{ $item->is_changed ? 'Yes' : 'No' }}</td>
                                            <td>
                                                <a href="{{ route('gangguan.edit', $item->uuid) }}" title="Edit">
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
                    <form id="addForm" action="{{ route('gangguan.store') }}" method="POST" class="forms-sample"
                        enctype="multipart/form-data">
                        @csrf
                        @method('POST')
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
                            <label for="report_date">Report Date</label>
                            <input type="datetime-local" class="form-control" id="report_date" name="report_date"
                                autocomplete="off" required>
                        </div>
                        <div class="form-group">
                            <label for="report_by">Report By</label>
                            <input type="text" class="form-control" id="report_by" name="report_by" autocomplete="off"
                                required placeholder="input report by">
                        </div>
                        <div class="form-group">
                            <label for="problem">Problem</label>
                            <input type="text" class="form-control" id="problem" name="problem" autocomplete="off"
                                required placeholder="input problem">
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
                            <label for="category">Category</label>
                            <select class="tom-select-gangguan" name="category" id="category" required>
                                <option value="" selected disabled>- pilih category problem -</option>
                                <option value="hardware">Hardware</option>
                                <option value="software">Software</option>
                                <option value="operation">Operation</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="classification">Classification</label>
                            <select class="tom-select-gangguan" name="classification" id="classification" required>
                                <option value="" selected disabled>- pilih classification problem -</option>
                                <option value="minor">Minor</option>
                                <option value="moderate">Moderate</option>
                                <option value="major">Major</option>
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
                            <label for="status">Status</label>
                            <select class="tom-select-gangguan" name="status" id="status" required>
                                <option value="" selected disabled>- pilih status -</option>
                                <option value="open">Open</option>
                                <option value="closed">Closed</option>
                                <option value="pending">Pending</option>
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
            initializeTomSelect('.tom-select-class');

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
@endsection
