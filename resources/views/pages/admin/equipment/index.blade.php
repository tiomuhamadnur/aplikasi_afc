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
                            <button type="button" title="Export" class="btn btn-outline-primary btn-rounded btn-icon">
                                <i class="mdi mdi-file-export"></i>
                            </button>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered text-center">
                                <thead>
                                    <tr>
                                        <th> # </th>
                                        <th> Nama </th>
                                        <th> Code </th>
                                        <th> Tipe </th>
                                        <th> Equipment <br> Number </th>
                                        <th> Lokasi </th>
                                        <th> Photo </th>
                                        <th> Aksi </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($equipment as $item)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $item->name }}</td>
                                            <td>{{ $item->code ?? '-' }}</td>
                                            <td class="text-wrap">
                                                {{ $item->tipe_equipment->code }} <br> ({{ $item->tipe_equipment->name }})
                                            </td>
                                            <td>{{ $item->equipment_number ?? '-' }}</td>
                                            <td class="text-wrap">
                                                {{ $item->relasi_area->lokasi->code ?? '-' }} <br>
                                                {{ $item->relasi_area->sub_lokasi->code ?? '-' }} <br>
                                                {{ $item->relasi_area->detail_lokasi->code ?? '-' }} <br>
                                                {{ $item->arah->name ?? '-' }}
                                            </td>
                                            <td>
                                                <button type="button" title="Show"
                                                    class="btn btn-gradient-success btn-rounded btn-icon"
                                                    data-bs-toggle="modal" data-bs-target="#photoModal"
                                                    data-photo='{{ asset('storage/' . $item->photo) }}'
                                                    data-name="{{ $item->name }}" data-code="{{ $item->code }}"
                                                    data-equipment_number="{{ $item->equipment_number }}"
                                                    data-tipe_equipment="{{ $item->tipe_equipment->code }} ({{ $item->tipe_equipment->name }})"
                                                    data-lokasi="{{ $item->relasi_area->lokasi->name }} - {{ $item->relasi_area->sub_lokasi->name }} - {{ $item->relasi_area->detail_lokasi->name }}"
                                                    data-struktur="{{ $item->relasi_struktur->divisi->code }} - {{ $item->relasi_struktur->departemen->code }} - {{ $item->relasi_struktur->seksi->code }}"
                                                    data-arah="{{ $item->arah->name }}" data-status="{{ $item->status }}"
                                                    data-deskripsi="{{ $item->deskripsi }}">
                                                    <i class="mdi mdi-eye"></i>
                                                </button>
                                            </td>
                                            <td>
                                                <a href="{{ route('equipment.edit', $item->uuid) }}">
                                                    <button type="button" title="Edit"
                                                        class="btn btn-gradient-warning btn-rounded btn-icon">
                                                        <i class="mdi mdi-lead-pencil"></i>
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
                            </table>
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
                            <select class="form-control form-control-lg" id="tipe_equipment_id" name="tipe_equipment_id"
                                required>
                                <option value="" selected disabled>- pilih tipe equipment -</option>
                                @foreach ($tipe_equipment as $item)
                                    <option value="{{ $item->id }}">{{ $item->name }} ({{ $item->code }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="relasi_area_id">Area</label>
                            <select class="form-control form-control-lg" id="relasi_area_id" name="relasi_area_id"
                                required>
                                <option value="" selected disabled>- pilih area spesifik -</option>
                                @foreach ($area as $item)
                                    <option value="{{ $item->id }}">{{ $item->lokasi->name }} -
                                        {{ $item->sub_lokasi->name }} - {{ $item->detail_lokasi->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="relasi_struktur_id">Owner</label>
                            <select class="form-control form-control-lg" id="relasi_struktur_id"
                                name="relasi_struktur_id" required>
                                <option value="" selected disabled>- pilih owner -</option>
                                @foreach ($struktur as $item)
                                    <option value="{{ $item->id }}">{{ $item->divisi->name }} -
                                        {{ $item->departemen->name }} - {{ $item->seksi->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="arah_id">Corner</label>
                            <select class="form-control form-control-lg" id="arah_id" name="arah_id" required>
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
