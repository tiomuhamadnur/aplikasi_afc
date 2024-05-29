@extends('layout.base')

@section('title-head')
    <title>Admin | Barang</title>
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Data Barang</h4>
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
                            <table class="table table-bordered text-center table-responsive">
                                <thead>
                                    <tr>
                                        <th> # </th>
                                        <th> Nama </th>
                                        <th> Spesifikasi </th>
                                        <th> Material <br> Number </th>
                                        <th> Tipe Barang </th>
                                        <th> Lokasi </th>
                                        <th> Detail </th>
                                        <th> Aksi </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($barang as $item)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td class="text-wrap font-weight-bold">{{ $item->name }}</td>
                                            <td class="text-wrap">{{ $item->spesifikasi }}</td>
                                            <td class="font-weight-bold">{{ $item->material_number }}</td>
                                            <td>{{ $item->tipe_barang->name }}</td>
                                            <td>
                                                {{ $item->relasi_area->lokasi->name }} <br>
                                                {{ $item->relasi_area->sub_lokasi->name }} <br>
                                                {{ $item->relasi_area->detail_lokasi->name }}
                                            </td>
                                            <td>
                                                <button type="button" title="Show"
                                                    class="btn btn-gradient-success btn-rounded btn-icon"
                                                    data-bs-toggle="modal" data-bs-target="#photoModal"
                                                    data-photo='{{ asset('storage/' . $item->photo) }}'
                                                    data-name="{{ $item->name }}"
                                                    data-spesifikasi="{{ $item->spesifikasi }}"
                                                    data-material_number="{{ $item->material_number }}"
                                                    data-serial_number="{{ $item->serial_number }}"
                                                    data-tipe_barang="{{ $item->tipe_barang->name }}"
                                                    data-lokasi="{{ $item->relasi_area->lokasi->name }} - {{ $item->relasi_area->sub_lokasi->name }} - {{ $item->relasi_area->detail_lokasi->name }}"
                                                    data-satuan="{{ $item->satuan->code }}"
                                                    data-deskripsi="{{ $item->deskripsi }}">
                                                    <i class="mdi mdi-eye"></i>
                                                </button>
                                            </td>
                                            <td>
                                                <button type="button" title="Edit"
                                                    class="btn btn-gradient-warning btn-rounded btn-icon">
                                                    <i class="mdi mdi-lead-pencil"></i>
                                                </button>
                                                <button type="button" title="Delete"
                                                    class="btn btn-gradient-danger btn-rounded btn-icon">
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
                    <form id="addForm" action="{{ route('barang.store') }}" method="POST" class="forms-sample"
                        enctype="multipart/form-data">
                        @csrf
                        @method('POST')
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" class="form-control" id="name" name="name" placeholder="Name"
                                autocomplete="off" required>
                        </div>
                        <div class="form-group">
                            <label for="spesifikasi">Spesifikasi</label>
                            <input type="text" class="form-control" id="spesifikasi" name="spesifikasi"
                                placeholder="Spesifikasi" autocomplete="off" required>
                        </div>
                        <div class="form-group">
                            <label for="material_number">Material Number</label>
                            <input type="number" min="1" class="form-control" id="material_number"
                                name="material_number" placeholder="Material Number" autocomplete="off">
                        </div>
                        <div class="form-group">
                            <label for="serial_number">Serial Number</label>
                            <input type="number" min="1" class="form-control" id="serial_number"
                                name="serial_number" placeholder="Serial Number" autocomplete="off">
                        </div>
                        <div class="form-group">
                            <label for="tipe_barang_id">Tipe Barang</label>
                            <select class="form-control form-control-lg" id="tipe_barang_id" name="tipe_barang_id"
                                required>
                                <option value="" selected disabled>- pilih tipe barang -</option>
                                @foreach ($tipe_barang as $item)
                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="satuan_id">Satuan</label>
                            <select class="form-control form-control-lg" id="satuan_id" name="satuan_id" required>
                                <option value="" selected disabled>- pilih satuan -</option>
                                @foreach ($satuan as $item)
                                    <option value="{{ $item->id }}">{{ $item->code }}</option>
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
                                accept="image/*" required>
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
                                        <td>Spesifikasi</td>
                                        <td class="text-center">:</td>
                                        <td id="spesifikasi_modal" class="text-wrap">-</td>
                                    </tr>
                                    <tr>
                                        <td>Material Number</td>
                                        <td class="text-center">:</td>
                                        <td id="material_number_modal" class="text-wrap">-</td>
                                    </tr>
                                    <tr>
                                        <td>Serial Number</td>
                                        <td class="text-center">:</td>
                                        <td id="serial_number_modal" class="text-wrap">-</td>
                                    </tr>
                                    <tr>
                                        <td>Tipe Barang</td>
                                        <td class="text-center">:</td>
                                        <td id="tipe_barang_modal" class="text-wrap">-</td>
                                    </tr>
                                    <tr>
                                        <td>Lokasi</td>
                                        <td class="text-center">:</td>
                                        <td id="lokasi_modal" class="text-wrap">-</td>
                                    </tr>
                                    <tr>
                                        <td>Satuan</td>
                                        <td class="text-center">:</td>
                                        <td id="satuan_modal" class="text-wrap">-</td>
                                    </tr>
                                    <tr>
                                        <td>Deskripsi</td>
                                        <td class="text-center">:</td>
                                        <td id="deskripsi_modal" class="text-wrap">-</td>
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
            var spesifikasi = $(e.relatedTarget).data('spesifikasi');
            var material_number = $(e.relatedTarget).data('material_number');
            var serial_number = $(e.relatedTarget).data('serial_number');
            var tipe_barang = $(e.relatedTarget).data('tipe_barang');
            var lokasi = $(e.relatedTarget).data('lokasi');
            var satuan = $(e.relatedTarget).data('satuan');
            var deskripsi = $(e.relatedTarget).data('deskripsi');

            document.getElementById("photo_modal").src = photo;
            document.getElementById("name_modal").innerText = name;
            document.getElementById("spesifikasi_modal").innerText = spesifikasi;
            document.getElementById("material_number_modal").innerText = material_number;
            document.getElementById("serial_number_modal").innerText = serial_number;
            document.getElementById("serial_number_modal").innerText = serial_number;
            document.getElementById("tipe_barang_modal").innerText = tipe_barang;
            document.getElementById("lokasi_modal").innerText = lokasi;
            document.getElementById("satuan_modal").innerText = satuan;
            document.getElementById("deskripsi_modal").innerText = deskripsi;
        });
    </script>
@endsection
