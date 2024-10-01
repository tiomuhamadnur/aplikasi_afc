@extends('layout.base')

@section('title-head')
    <title>Admin | Equipment</title>
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Data Equipment</h4>
                        <div class="btn-group my-2">
                            <button type="button" title="Add" class="btn btn-outline-primary btn-rounded btn-icon"
                                data-bs-toggle="modal" data-bs-target="#addModal">
                                <i class="mdi mdi-plus-circle"></i>
                            </button>
                            <button type="button" title="Filter" class="btn btn-outline-primary btn-rounded btn-icon">
                                <i class="mdi mdi-filter"></i>
                            </button>
                            <button type="button" title="Import" class="btn btn-outline-primary btn-rounded btn-icon"
                                data-bs-toggle="modal" data-bs-target="#importModal">
                                <i class="mdi mdi-file-import"></i>
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
                    <form id="addForm" action="{{ route('equipment.store') }}" method="POST" class="forms-sample"
                        enctype="multipart/form-data">
                        @csrf
                        @method('POST')
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" class="form-control" id="name" name="name" placeholder="Name"
                                autocomplete="off" required>
                        </div>
                        <div class="form-group">
                            <label for="code">Code</label>
                            <input type="text" class="form-control" id="code" name="code" placeholder="Code"
                                autocomplete="off" required>
                        </div>
                        <div class="form-group">
                            <label for="equipment_number">Equipment Number</label>
                            <input type="number" min="1" class="form-control" id="equipment_number"
                                name="equipment_number" placeholder="Equipment Number" autocomplete="off">
                        </div>
                        <div class="form-group">
                            <label for="tipe_equipment_id">Tipe Equipment</label>
                            <select class="tom-select-class" id="tipe_equipment_id" name="tipe_equipment_id" required>
                                <option value="" selected disabled>- pilih tipe equipment -</option>
                                @foreach ($tipe_equipment as $item)
                                    <option value="{{ $item->id }}">{{ $item->name }} ({{ $item->code }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="relasi_area_id">Area</label>
                            <select class="tom-select-class" id="relasi_area_id" name="relasi_area_id" required>
                                <option value="" selected disabled>- pilih area spesifik -</option>
                                @foreach ($area as $item)
                                    <option value="{{ $item->id }}">{{ $item->lokasi->name }} -
                                        {{ $item->sub_lokasi->name }} - {{ $item->detail_lokasi->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="functional_location_id">Functional Location</label>
                            <select class="tom-select-class" id="functional_location_id" name="functional_location_id"
                                required>
                                <option value="" selected disabled>- pilih functional location -</option>
                                @foreach ($functional_location as $item)
                                    <option value="{{ $item->id }}">{{ $item->name }} - {{ $item->code }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="parent_id">Parent Equipment <span class="text-primary">(optional)</span></label>
                            <select class="tom-select-class" id="parent_id" name="parent_id">
                                <option value="" selected disabled>- pilih parent equipment -</option>
                                @foreach ($equipment as $item)
                                    <option value="{{ $item->id }}">{{ $item->code ?? 'N/A' }} - {{ $item->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="relasi_struktur_id">Owner</label>
                            <select class="tom-select-class" id="relasi_struktur_id" name="relasi_struktur_id" required>
                                <option value="" selected disabled>- pilih owner -</option>
                                @foreach ($struktur as $item)
                                    <option value="{{ $item->id }}">{{ $item->divisi->name }} -
                                        {{ $item->departemen->name }} - {{ $item->seksi->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="arah_id">Corner <span class="text-primary">(optional)</span></label>
                            <select class="tom-select-class" id="arah_id" name="arah_id">
                                <option value="" selected disabled>- pilih corner -</option>
                                @foreach ($arah as $item)
                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="deskripsi">Deskripsi</label>
                            <input type="text" class="form-control" id="deskripsi" name="deskripsi"
                                placeholder="Deskripsi" autocomplete="off">
                        </div>
                        <div class="form-group">
                            <label for="photo">Photo</label>
                            <div class="text-center">
                                <img class="img-thumbnail" id="previewImage" src="#" alt="Preview"
                                    style="max-width: 250px; max-height: 250px; display: none;">
                            </div>
                            <input type="file" class="form-control" id="photo" name="photo" autocomplete="off"
                                accept="image/*">
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

    <!-- Import Modal -->
    <div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Form Import</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="importForm" action="{{ route('equipment.import') }}" method="POST" class="forms-sample"
                        enctype="multipart/form-data">
                        @csrf
                        @method('POST')
                        <div class="form-group">
                            <label for="relasi_struktur_id">Owner</label>
                            <select class="form-control form-control-lg" id="relasi_struktur_id"
                                name="relasi_struktur_id" required>
                                <option value="" selected disabled>- pilih owner -</option>
                                @foreach ($struktur as $item)
                                    <option value="{{ $item->id }}">Divisi {{ $item->divisi->code }} -
                                        Departemen {{ $item->departemen->code }} - Seksi {{ $item->seksi->code }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="">
                                File Import
                                <span>
                                    <a href="{{ asset('assets/format/Template Format Import Equipment.xlsx') }}">
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
                    <h5 class="modal-title" id="exampleModalLabel">Detail</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="container">
                        <div class="mb-4 text-center align-middle">
                            <div class="border mx-auto">
                                <img src="#" id="photo_modal" class="img-thumbnail" alt="Tidak ada photo">
                            </div>
                        </div>
                        <div class="mb-4 align-middle">
                            <table class="table-bordered" style="width: 100%">
                                <tbody>
                                    <tr>
                                        <td style="width: 32%">Nama</td>
                                        <td class="text-center" style="width: 4%">:</td>
                                        <td id="name_modal" class="text-wrap">-</td>
                                    </tr>
                                    <tr>
                                        <td>Code</td>
                                        <td class="text-center">:</td>
                                        <td id="code_modal" class="text-wrap">-</td>
                                    </tr>
                                    <tr>
                                        <td>Equipment Number</td>
                                        <td class="text-center">:</td>
                                        <td id="equipment_number_modal" class="text-wrap">-</td>
                                    </tr>
                                    <tr>
                                        <td>Tipe Equipment</td>
                                        <td class="text-center">:</td>
                                        <td id="tipe_equipment_modal" class="text-wrap">-</td>
                                    </tr>
                                    <tr>
                                        <td>Lokasi</td>
                                        <td class="text-center">:</td>
                                        <td id="lokasi_modal" class="text-wrap">-</td>
                                    </tr>
                                    <tr>
                                        <td>Corner</td>
                                        <td class="text-center">:</td>
                                        <td id="arah_modal" class="text-wrap">-</td>
                                    </tr>
                                    <tr>
                                        <td>Owner</td>
                                        <td class="text-center">:</td>
                                        <td id="struktur_modal" class="text-wrap">-</td>
                                    </tr>
                                    <tr>
                                        <td>Deskripsi</td>
                                        <td class="text-center">:</td>
                                        <td id="deskripsi_modal" class="text-wrap">-</td>
                                    </tr>
                                    <tr>
                                        <td>Status</td>
                                        <td class="text-center">:</td>
                                        <td id="status_modal" class="text-wrap">-</td>
                                    </tr>
                                </tbody>
                            </table>
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
                    <form id="deleteForm" action="{{ route('equipment.delete') }}" method="POST" class="forms-sample">
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

        $('#photoModal').on('show.bs.modal', function(e) {
            var photo = $(e.relatedTarget).data('photo');
            var name = $(e.relatedTarget).data('name');
            var code = $(e.relatedTarget).data('code');
            var equipment_number = $(e.relatedTarget).data('equipment_number');
            var tipe_equipment = $(e.relatedTarget).data('tipe_equipment');
            var lokasi = $(e.relatedTarget).data('lokasi');
            var arah = $(e.relatedTarget).data('arah');
            var struktur = $(e.relatedTarget).data('struktur');
            var deskripsi = $(e.relatedTarget).data('deskripsi');
            var status = $(e.relatedTarget).data('status');

            document.getElementById("photo_modal").src = photo;
            document.getElementById("name_modal").innerText = name;
            document.getElementById("code_modal").innerText = code;
            document.getElementById("equipment_number_modal").innerText = equipment_number;
            document.getElementById("tipe_equipment_modal").innerText = tipe_equipment;
            document.getElementById("lokasi_modal").innerText = lokasi;
            document.getElementById("arah_modal").innerText = arah;
            document.getElementById("struktur_modal").innerText = struktur;
            document.getElementById("deskripsi_modal").innerText = deskripsi;
            document.getElementById("status_modal").innerText = status;
        });

        $('#deleteModal').on('show.bs.modal', function(e) {
            var id = $(e.relatedTarget).data('id');

            $('#id_delete').val(id);
        });
    </script>
@endsection
