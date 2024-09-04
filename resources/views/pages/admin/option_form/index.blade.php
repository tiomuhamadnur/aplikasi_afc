@extends('layout.base')

@section('title-head')
    <title>Admin | Option Form</title>
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Data Option Form</h4>
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
                                        <th> Value </th>
                                        <th> Aksi </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($option_form as $item)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $item->name }}</td>
                                            <td>{{ $item->code }}</td>
                                            <td>
                                                @php
                                                    $data = json_decode($item->value);
                                                @endphp
                                                @foreach ($data as $value)
                                                    {{ $loop->iteration }}. {{ $value }} <br>
                                                @endforeach
                                            </td>
                                            <td>
                                                <button type="button" title="Edit"
                                                    class="btn btn-gradient-warning btn-rounded btn-icon"
                                                    data-bs-toggle="modal" data-bs-target="#editModal"
                                                    data-id="{{ $item->id }}" data-name="{{ $item->name }}"
                                                    data-code="{{ $item->code }}" data-value="{{ $item->value }}">
                                                    <i class="mdi mdi-lead-pencil"></i>
                                                </button>
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
                    <form id="addForm" action="{{ route('option-form.store') }}" method="POST" class="forms-sample">
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
                        <div id="inputContainer">
                            <div class="form-group">
                                <label for="value">Value</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="value" name="value[]"
                                        placeholder="Option value" autocomplete="off" required>
                                    <button type="button" class="btn btn-primary" onclick="addRow()">Add</button>
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

    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Form Edit</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editForm" action="{{ route('option-form.update') }}" method="POST"
                        class="forms-sample">
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
                        <div class="form-group">
                            <label for="value">Value <span class="text-danger">(pisah dengan tanda koma ',' untuk
                                    setiap opsi)</span></label>
                            <input type="text" class="form-control" id="value_edit" name="value"
                                placeholder="Value" autocomplete="off" required>
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
                    <form id="deleteForm" action="{{ route('option-form.delete') }}" method="POST"
                        class="forms-sample">
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
