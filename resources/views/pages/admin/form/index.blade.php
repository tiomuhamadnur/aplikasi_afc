@extends('layout.base')

@section('title-head')
    <title>Admin | Form</title>
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Data Form</h4>
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
                                        <th> Tipe Equipment </th>
                                        <th> Description </th>
                                        <th> Status </th>
                                        <th> List Parameter </th>
                                        <th> Aksi </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($form as $item)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $item->name }}</td>
                                            <td>{{ $item->code }}</td>
                                            <td>
                                                {{ $item->tipe_equipment->code ?? '-' }} <br>
                                                ({{ $item->tipe_equipment->name ?? '-' }})
                                            </td>
                                            <td>{{ $item->description }}</td>
                                            <td>{{ $item->status }}</td>
                                            <td>
                                                <a href="{{ route('parameter.index', $item->uuid) }}"
                                                    title="Show Parameter">
                                                    <button type="button"
                                                        class="btn btn-gradient-success btn-rounded btn-icon">
                                                        <i class="mdi mdi-eye"></i>
                                                    </button>
                                                </a>
                                            </td>
                                            <td>
                                                <a href="{{ route('form.edit', $item->uuid) }}" title="Edit">
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
                    <form id="addForm" action="{{ route('form.store') }}" method="POST" class="forms-sample">
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
                            <label for="tipe_equipment_id">Tipe Equipment</label>
                            <select class="form-control form-control-lg" name="tipe_equipment_id" id="tipe_equipment_id"
                                required>
                                <option value="" selected disabled>- pilih tipe equipment -</option>
                                @foreach ($tipe_equipment as $item)
                                    <option value="{{ $item->id }}">{{ $item->code }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="description">Description <span class="text-info">(optional)</span></label>
                            <input type="text" class="form-control" id="description" name="description"
                                placeholder="Description" autocomplete="off">
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

    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Form Edit</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editForm" action="{{ route('form.update') }}" method="POST" class="forms-sample">
                        @csrf
                        @method('PUT')
                        <input type="text" name="id" id="id_edit" hidden>
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" class="form-control" id="name_edit" name="name" placeholder="Name"
                                autocomplete="off" required>
                        </div>
                        <div class="form-group">
                            <label for="code">Code</label>
                            <input type="text" class="form-control" id="code_edit" name="code" placeholder="Code"
                                autocomplete="off" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" form="editForm" class="btn btn-gradient-primary me-2">Update</button>
                </div>
            </div>
        </div>
    </div>
    <!-- End Edit Modal -->

    <!-- Delete Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Are you sure?</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="deleteForm" action="{{ route('form.delete') }}" method="POST" class="forms-sample">
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
        });

        function addRow() {
            const inputContainer = document.getElementById('inputContainer');

            // Membuat div baru untuk form-group
            const newFormGroup = document.createElement('div');
            newFormGroup.classList.add('form-group', 'input-group');

            // Membuat input text baru
            const newInput = document.createElement('input');
            newInput.type = 'text';
            newInput.className = 'form-control';
            newInput.name = 'value[]';
            newInput.placeholder = 'Option value';
            newInput.autocomplete = 'off';
            newInput.required = true;

            // Membuat tombol hapus
            const removeButton = document.createElement('button');
            removeButton.type = 'button';
            removeButton.className = 'btn btn-danger';
            removeButton.textContent = 'Remove';
            removeButton.onclick = function() {
                removeRow(this);
            };

            // Menambahkan input dan tombol hapus ke dalam form-group
            newFormGroup.appendChild(newInput);
            newFormGroup.appendChild(removeButton);

            // Menambahkan form-group baru ke dalam container
            inputContainer.appendChild(newFormGroup);
        }

        function removeRow(button) {
            const formGroup = button.parentNode;
            const inputContainer = document.getElementById('inputContainer');

            // Hapus form-group dari container
            inputContainer.removeChild(formGroup);
        }
    </script>
@endsection
