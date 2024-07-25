@extends('layout.base')

@section('title-head')
    <title>Admin | Edit Equipment</title>
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Edit Data Equipment</h4>
                        <form id="editForm" action="{{ route('equipment.update') }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <input type="text" name="id" value="{{ $equipment->id }}" hidden>
                            <div class="form-group">
                                <label for="name">Name</label>
                                <input type="text" class="form-control" id="name" name="name" placeholder="Name"
                                    autocomplete="off" required value="{{ $equipment->name }}">
                            </div>
                            <div class="form-group">
                                <label for="code">Code</label>
                                <input type="text" class="form-control" id="code" name="code" placeholder="Code"
                                    autocomplete="off" required value="{{ $equipment->code }}">
                            </div>
                            <div class="form-group">
                                <label for="equipment_number">Equipment Number</label>
                                <input type="number" min="1" class="form-control" id="equipment_number"
                                    name="equipment_number" placeholder="Equipment Number" autocomplete="off"
                                    value="{{ $equipment->equipment_number }}">
                            </div>
                            <div class="form-group">
                                <label for="tipe_equipment_id">Tipe Equipment</label>
                                <select class="form-control form-control-lg" id="tipe_equipment_id" name="tipe_equipment_id"
                                    required>
                                    <option value="" selected disabled>- pilih tipe equipment -</option>
                                    @foreach ($tipe_equipment as $item)
                                        <option value="{{ $item->id }}"
                                            @if ($item->id == $equipment->tipe_equipment_id) selected @endif>{{ $item->name }}
                                            ({{ $item->code }})
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
                                        <option value="{{ $item->id }}"
                                            @if ($item->id == $equipment->relasi_area_id) selected @endif>{{ $item->lokasi->name }} -
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
                                        <option value="{{ $item->id }}"
                                            @if ($item->id == $equipment->relasi_struktur_id) selected @endif>{{ $item->divisi->name }} -
                                            {{ $item->departemen->name }} - {{ $item->seksi->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="arah_id">Corner</label>
                                <select class="form-control form-control-lg" id="arah_id" name="arah_id" required>
                                    <option value="" selected disabled>- pilih corner -</option>
                                    @foreach ($arah as $item)
                                        <option value="{{ $item->id }}"
                                            @if ($item->id == $equipment->arah_id) selected @endif>
                                            {{ $item->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="status">Status</label>
                                <select class="form-control form-control-lg" id="status" name="status" required>
                                    <option value="" selected disabled>- pilih status -</option>
                                    <option value="active" @if ($equipment->status == 'active') selected @endif>Active</option>
                                    <option value="non-active" @if ($equipment->status == 'non-active') selected @endif>Non-active
                                    </option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="deskripsi">Deskripsi</label>
                                <input type="text" class="form-control" id="deskripsi" name="deskripsi"
                                    placeholder="Deskripsi" autocomplete="off" value="{{ $equipment->deskripsi }}">
                            </div>
                            <div class="form-group">
                                <label for="photo">Photo</label>
                                <div class="text-left">
                                    <img class="img-thumbnail" id="previewImage"
                                        src="{{ asset('storage/' . $equipment->photo) }}" alt="tidak ada photo"
                                        style="max-width: 250px; max-height: 250px;">
                                </div>
                                <input type="file" class="form-control" id="photo" name="photo" autocomplete="off"
                                    accept="image/*">
                            </div>
                            <div class="form-group d-flex justify-content-end">
                                <a href="{{ route('equipment.index') }}" type="button"
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
