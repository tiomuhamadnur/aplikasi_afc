@extends('layout.base')

@section('title-head')
    <title>Admin | Edit Parameter</title>
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Edit Data Parameter</h4>
                        <form id="editForm" action="{{ route('parameter.update') }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <input type="text" name="id" value="{{ $parameter->id }}" hidden>
                            <input type="text" name="form_id" value="{{ $parameter->form->id }}" hidden>

                            <div class="form-group">
                                <label for="form">Form</label>
                                <input type="text" class="form-control" id="form" placeholder="Form"
                                    autocomplete="off" value="{{ $parameter->form->name }}" disabled>
                            </div>
                            <div class="form-group">
                                <label for="name">Name</label>
                                <input type="text" class="form-control" id="name" name="name" placeholder="Name"
                                    autocomplete="off" required value="{{ $parameter->name }}">
                            </div>
                            <div class="form-group">
                                <label for="code">Code</label>
                                <input type="text" class="form-control" id="code" name="code" placeholder="Code"
                                    autocomplete="off" required value="{{ $parameter->code }}">
                            </div>
                            <div class="form-group">
                                <label for="description">Description <span class="text-info">(optional)</span></label>
                                <input type="text" class="form-control" id="description" name="description"
                                    placeholder="Description" autocomplete="off" value="{{ $parameter->description }}">
                            </div>
                            <div class="form-group">
                                <label for="tipe">Tipe Input</label>
                                <select class="tom-select-class" name="tipe" id="tipe" required>
                                    <option value="" selected disabled>- pilih tipe input -</option>
                                    <option value="number" @if ($parameter->tipe == 'number') selected @endif>Number</option>
                                    <option value="option" @if ($parameter->tipe == 'option') selected @endif>Option</option>
                                    <option value="text" @if ($parameter->tipe == 'text') selected @endif>Text</option>
                                    <option value="file" @if ($parameter->tipe == 'file') selected @endif>File</option>
                                </select>
                            </div>
                            <div id="optionFormContainer" class="form-group">
                                <label for="option_form_id">Option Form</label>
                                <select class="tom-select-class" name="option_form_id" id="option_form_id">
                                    <option value="" selected disabled>- pilih option form -</option>
                                    @foreach ($option_form as $item)
                                        <option value="{{ $item->id }}"
                                            @if ($item->id == $parameter->option_form_id) selected @endif>{{ $item->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div id="numberContainer">
                                <div class="form-group">
                                    <label for="min_value">Min. Value</label>
                                    <input type="number" class="form-control" id="min_value" name="min_value"
                                        placeholder="Min. Value" autocomplete="off" step="0.01"
                                        value="{{ $parameter->min_value }}">
                                </div>
                                <div class="form-group">
                                    <label for="max_value">Max. Value</label>
                                    <input type="number" class="form-control" id="max_value" name="max_value"
                                        placeholder="Max. Value" autocomplete="off" step="0.01"
                                        value="{{ $parameter->max_value }}">
                                </div>
                                <div class="form-group">
                                    <label for="satuan_id">Satuan</label>
                                    <select class="tom-select-class" name="satuan_id" id="satuan_id">
                                        <option value="" selected disabled>- pilih satuan -</option>
                                        @foreach ($satuan as $item)
                                            <option value="{{ $item->id }}"
                                                @if ($item->id == $parameter->satuan_id) selected @endif>
                                                {{ $item->code }} ({{ $item->name }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="urutan">Urutan</label>
                                <input type="number" class="form-control" id="urutan" name="urutan"
                                    placeholder="Urutan" autocomplete="off" min="1"
                                    value="{{ $parameter->urutan }}" required>
                            </div>
                            <div class="form-group">
                                <label for="photo_instruction">Photo Instruction</label>
                                <div>
                                    <img class="img-thumbnail" id="previewImage"
                                        src="{{ asset('storage/' . $parameter->photo_instruction) }}" alt="Preview"
                                        style="max-width: 250px; max-height: 250px;">
                                </div>
                                <input type="file" class="form-control" id="photo_instruction"
                                    name="photo_instruction" autocomplete="off" accept="image/*">
                            </div>

                            <div class="form-group d-flex justify-content-end">
                                <a href="{{ route('parameter.index', $parameter->form->uuid) }}" type="button"
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
        $(document).ready(function() {
            const imageInput = document.getElementById('photo_instruction');
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
        })
    </script>
@endsection
