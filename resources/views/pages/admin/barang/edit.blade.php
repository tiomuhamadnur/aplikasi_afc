@extends('layout.base')

@section('title-head')
    <title>Admin | Edit Barang</title>
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Edit Data Barang</h4>
                        <form id="editForm" action="{{ route('barang.update') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <input type="text" name="id" value="{{ $barang->id }}" hidden>
                            <div class="form-group">
                                <label for="name">Name</label>
                                <input type="text" class="form-control" id="name" name="name" placeholder="Name"
                                    autocomplete="off" required value="{{ $barang->name }}">
                            </div>
                            <div class="form-group">
                                <label for="spesifikasi">Spesifikasi</label>
                                <input type="text" class="form-control" id="spesifikasi" name="spesifikasi"
                                    placeholder="Spesifikasi" autocomplete="off" required
                                    value="{{ $barang->spesifikasi }}">
                            </div>
                            <div class="form-group">
                                <label for="material_number">Material Number</label>
                                <input type="number" min="1" class="form-control" id="material_number"
                                    name="material_number" placeholder="Material Number" autocomplete="off"
                                    value="{{ $barang->material_number }}">
                            </div>
                            <div class="form-group">
                                <label for="serial_number">Serial Number</label>
                                <input type="number" min="1" class="form-control" id="serial_number"
                                    name="serial_number" placeholder="Serial Number" autocomplete="off"
                                    value="{{ $barang->serial_number }}">
                            </div>
                            <div class="form-group">
                                <label for="tipe_barang_id">Tipe Barang</label>
                                <select class="form-control form-control-lg" id="tipe_barang_id" name="tipe_barang_id"
                                    required>
                                    <option value="" selected disabled>- pilih tipe barang -</option>
                                    @if ($barang->tipe_barang_id != null)
                                        @foreach ($tipe_barang as $item)
                                            <option value="{{ $item->id }}"
                                                @if ($item->id == $barang->tipe_barang->id) selected @endif>
                                                {{ $item->name }}
                                            </option>
                                        @endforeach
                                    @else
                                        @foreach ($tipe_barang as $item)
                                            <option value="{{ $item->id }}">
                                                {{ $item->name }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="relasi_struktur_id">Owner</label>
                                <select class="form-control form-control-lg" id="relasi_struktur_id"
                                    name="relasi_struktur_id" required>
                                    <option value="" selected disabled>- pilih owner -</option>
                                    @if ($barang->relasi_struktur_id != null)
                                        @foreach ($struktur as $item)
                                            <option value="{{ $item->id }}"
                                                @if ($item->id == $barang->relasi_struktur_id) selected @endif>Divisi
                                                {{ $item->divisi->code }} -
                                                Departemen {{ $item->departemen->code }} - Seksi {{ $item->seksi->code }}
                                            </option>
                                        @endforeach
                                    @else
                                        @foreach ($struktur as $item)
                                            <option value="{{ $item->id }}">Divisi
                                                {{ $item->divisi->code }} -
                                                Departemen {{ $item->departemen->code }} - Seksi {{ $item->seksi->code }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="satuan_id">Satuan</label>
                                <select class="form-control form-control-lg" id="satuan_id" name="satuan_id" required>
                                    <option value="" selected disabled>- pilih satuan -</option>
                                    @if ($barang->satuan_id != null)
                                        @foreach ($satuan as $item)
                                            <option value="{{ $item->id }}"
                                                @if ($item->id == $barang->satuan->id) selected @endif>
                                                {{ $item->code }}
                                            </option>
                                        @endforeach
                                    @else
                                        @foreach ($satuan as $item)
                                            <option value="{{ $item->id }}">
                                                {{ $item->code }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="relasi_area_id">Area</label>
                                <select class="form-control form-control-lg" id="relasi_area_id" name="relasi_area_id"
                                    required>
                                    <option value="" selected disabled>- pilih area spesifik -</option>
                                    @if ($barang->relasi_area_id != null)
                                        @foreach ($area as $item)
                                            <option value="{{ $item->id }}"
                                                @if ($item->id == $barang->relasi_area_id) selected @endif>
                                                {{ $item->lokasi->name }} -
                                                {{ $item->sub_lokasi->name }} - {{ $item->detail_lokasi->name }}
                                            </option>
                                        @endforeach
                                    @else
                                        @foreach ($area as $item)
                                            <option value="{{ $item->id }}">
                                                {{ $item->lokasi->name }} -
                                                {{ $item->sub_lokasi->name }} - {{ $item->detail_lokasi->name }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="deskripsi">Deskripsi</label>
                                <input type="text" class="form-control" id="deskripsi" name="deskripsi"
                                    placeholder="Deskripsi" autocomplete="off" value="{{ $barang->deskripsi }}">
                            </div>
                            <div class="form-group">
                                <label for="photo">Photo</label>
                                <div class="text-left">
                                    <img class="img-thumbnail" id="previewImage"
                                        src="{{ asset('storage/' . $barang->photo) }}" alt="Tidak ada photo"
                                        style="max-width: 250px; max-height: 250px;">
                                </div>
                                <input type="file" class="form-control" id="photo" name="photo"
                                    autocomplete="off" accept="image/*" required>
                            </div>
                            <div class="form-group d-flex justify-content-end">
                                <a href="{{ route('barang.index') }}" type="button"
                                    class="btn btn-secondary">Cancel</a>
                                <button type="submit" form="editForm" class="btn btn-primary">Update</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
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
    </script>
@endsection
