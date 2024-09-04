@extends('layout.base')

@section('title-head')
    <title>Admin | Parameter</title>
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Data Parameter ({{ $form->name ?? '-' }})</h4>
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
                                        <th> Description </th>
                                        <th> Tipe </th>
                                        <th> Option Form </th>
                                        <th> Min Value </th>
                                        <th> Max Value </th>
                                        <th> Satuan </th>
                                        <th> Photo </th>
                                        <th> Urutan </th>
                                        <th> Aksi </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($parameter as $item)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $item->name }}</td>
                                            <td>{{ $item->code }}</td>
                                            <td>{{ $item->description }}</td>
                                            <td>{{ $item->tipe }}</td>
                                            <td>{{ $item->option_form->code ?? '-' }}</td>
                                            <td>{{ $item->min_value ?? '-' }}</td>
                                            <td>{{ $item->max_value ?? '-' }}</td>
                                            <td>
                                                @if ($item->satuan_id != null)
                                                    {{ $item->satuan->code ?? '-' }} ({{ $item->satuan->name ?? '-' }})
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>
                                                @if ($item->photo_instruction != null)
                                                    <button type='button' title='Show'
                                                        class='btn btn-gradient-primary btn-rounded btn-icon'
                                                        data-bs-toggle='modal' data-bs-target='#photoModal'
                                                        data-photo="{{ asset('storage/' . $item->photo_instruction) }}">
                                                        <i class='mdi mdi-eye'></i>
                                                    </button>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>{{ $item->urutan }}</td>
                                            <td>
                                                <a href="{{ route('parameter.edit', $item->uuid) }}" title="Edit">
                                                    <button type="button"
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
                    <form id="addForm" action="{{ route('parameter.store') }}" method="POST" class="forms-sample"
                        enctype="multipart/form-data">
                        @csrf
                        @method('POST')
                        <input type="text" name="form_id" value="{{ $form->id }}" hidden>
                        <div class="form-group">
                            <label for="form">Form</label>
                            <input type="text" class="form-control" id="form" placeholder="Form" autocomplete="off"
                                value="{{ $form->name }}" disabled>
                        </div>
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
                            <label for="description">Description <span class="text-info">(optional)</span></label>
                            <input type="text" class="form-control" id="description" name="description"
                                placeholder="Description" autocomplete="off">
                        </div>
                        <div class="form-group">
                            <label for="tipe">Tipe Input</label>
                            <select class="tom-select-class" name="tipe" id="tipe" required>
                                <option value="" selected disabled>- pilih tipe input -</option>
                                <option value="number">Number</option>
                                <option value="option">Option</option>
                                <option value="text">Text</option>
                            </select>
                        </div>
                        <div id="optionFormContainer" class="form-group" style="display: none;">
                            <label for="option_form_id">Option Form</label>
                            <select class="tom-select-class" name="option_form_id" id="option_form_id">
                                <option value="" selected disabled>- pilih option form -</option>
                                @foreach ($option_form as $item)
                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div id="numberContainer" style="display: none">
                            <div class="form-group">
                                <label for="min_value">Min. Value</label>
                                <input type="number" class="form-control" id="min_value" name="min_value"
                                    placeholder="Min. Value" autocomplete="off" step="0.01">
                            </div>
                            <div class="form-group">
                                <label for="max_value">Max. Value</label>
                                <input type="number" class="form-control" id="max_value" name="max_value"
                                    placeholder="Max. Value" autocomplete="off" step="0.01">
                            </div>
                            <div class="form-group">
                                <label for="satuan_id">Satuan</label>
                                <select class="tom-select-class" name="satuan_id" id="satuan_id">
                                    <option value="" selected disabled>- pilih satuan -</option>
                                    @foreach ($satuan as $item)
                                        <option value="{{ $item->id }}">{{ $item->code }} ({{ $item->name }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="urutan">Urutan</label>
                            <input type="number" class="form-control" id="urutan" name="urutan"
                                placeholder="Urutan" autocomplete="off" min="1">
                        </div>
                        <div class="form-group">
                            <label for="photo_instruction">Photo Instruction</label>
                            <div class="text-center">
                                <img class="img-thumbnail" id="previewImage" src="#" alt="Preview"
                                    style="max-width: 250px; max-height: 250px; display: none;">
                            </div>
                            <input type="file" class="form-control" id="photo_instruction" name="photo_instruction"
                                placeholder="Urutan" autocomplete="off" accept="image/*">
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

    <!-- Delete Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Are you sure?</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="deleteForm" action="{{ route('parameter.delete') }}" method="POST" class="forms-sample">
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

    <!-- Photo Modal -->
    <div class="modal fade" id="photoModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Photo Instruction</h5>
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
@endsection

@section('javascript')
    <script>
        document.getElementById('tipe').addEventListener('change', function() {
            var selectedValue = this.value;
            var optionFormContainer = document.getElementById('optionFormContainer');
            var numberContainer = document.getElementById('numberContainer');
            var optionFormInput = document.getElementById('option_form_id');

            var minValue = document.getElementById('min_value');
            var maxValue = document.getElementById('max_value');
            var satuanId = document.getElementById('satuan_id');

            if (selectedValue === 'option') {
                optionFormContainer.style.display = 'block';
                numberContainer.style.display = 'none';
                optionFormInput.required = true;
            } else if (selectedValue === 'number') {
                numberContainer.style.display = 'block';
                optionFormContainer.style.display = 'none';
                optionFormInput.required = false;
                minValue.required = true;
                maxValue.required = true;
                satuanId.required = true;
            } else {
                optionFormContainer.style.display = 'none';
                numberContainer.style.display = 'none';
                optionFormInput.required = false;
                minValue.required = false;
                maxValue.required = false;
                satuanId.required = false;
            }
        });

        $(document).ready(function() {
            $('#editModal').on('show.bs.modal', function(e) {
                var id = $(e.relatedTarget).data('id');
                var name = $(e.relatedTarget).data('name');
                var code = $(e.relatedTarget).data('code');
                var value = $(e.relatedTarget).data('value');

                $('#id_edit').val(id);
                $('#name_edit').val(name);
                $('#code_edit').val(code);
                $('#value_edit').val(value);
            });

            $('#deleteModal').on('show.bs.modal', function(e) {
                var id = $(e.relatedTarget).data('id');

                $('#id_delete').val(id);
            });

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

            $('#photoModal').on('show.bs.modal', function(e) {
                var photo = $(e.relatedTarget).data('photo');
                document.getElementById("photo_modal").src = photo;
            });
        });
    </script>
@endsection
