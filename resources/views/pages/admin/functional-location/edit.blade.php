@extends('layout.base')

@section('title-head')
    <title>Admin | Edit Functional Location</title>
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Edit Data Functional Location</h4>
                        <form id="editForm" action="{{ route('fun_loc.update') }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <input type="text" name="id" value="{{ $functional_location->id }}" hidden>
                            <div class="form-group">
                                <label for="name">Name</label>
                                <input type="text" class="form-control" id="name" name="name" placeholder="Name"
                                    autocomplete="off" required value="{{ $functional_location->name }}">
                            </div>
                            <div class="form-group">
                                <label for="code">Code</label>
                                <input type="text" class="form-control" id="code" name="code" placeholder="Code"
                                    autocomplete="off" required value="{{ $functional_location->code }}">
                            </div>
                            <div class="form-group">
                                <label for="description">Description</label>
                                <input type="text" class="form-control" id="description" name="description"
                                    placeholder="Description" autocomplete="off"
                                    value="{{ $functional_location->description }}">
                            </div>
                            <div class="form-group">
                                <label for="parent_id">Parent <span class="text-primary">(optional)</span></label>
                                <select class="tom-select-class" id="parent_id" name="parent_id">
                                    <option value="" selected disabled>- pilih parent -</option>
                                    @foreach ($functional_locations as $item)
                                        <option value="{{ $item->id }}"
                                            @if ($item->id == $functional_location->parent_id) selected @endif>
                                            {{ $item->code ?? 'N/A' }} -
                                            {{ $item->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group d-flex justify-content-end">
                                <a href="{{ route('fun_loc.index') }}" type="button" class="btn btn-secondary">Cancel</a>
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
