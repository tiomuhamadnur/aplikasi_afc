@extends('layout.base')

@section('title-head')
    <title>Admin | Edit Form</title>
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Edit Data Form</h4>
                        <form id="editForm" action="{{ route('form.update') }}" method="POST">
                            @csrf
                            @method('PUT')
                            <input type="text" name="id" value="{{ $form->id }}" hidden>
                            <div class="form-group">
                                <label for="name">Name</label>
                                <input type="text" class="form-control" id="name" name="name" placeholder="Name"
                                    autocomplete="off" required value="{{ $form->name }}">
                            </div>
                            <div class="form-group">
                                <label for="code">Code</label>
                                <input type="text" class="form-control" id="code" name="code" placeholder="Code"
                                    autocomplete="off" required value="{{ $form->code }}">
                            </div>
                            <div class="form-group">
                                <label for="object_type">Object Type</label>
                                <select class="form-control form-control-lg" name="object_type" id="object_type" required>
                                    <option value="" selected disabled>- select object type -</option>
                                    <option value="equipment" @if ($form->object_type == 'equipment') selected @endif>
                                        Equipment
                                    </option>
                                    <option value="functional_location" @if ($form->object_type == 'functional_location') selected @endif>
                                        Functional Location
                                    </option>
                                </select>
                            </div>
                            <div class="form-group" id="tipeEquipmentContainer"
                                @if ($form->object_type != 'equipment') style="display: none" @endif>
                                <label for="tipe_equipment_id">Tipe Equipment</label>
                                <select class="form-control form-control-lg" name="tipe_equipment_id"
                                    id="tipe_equipment_id">
                                    <option value="" selected disabled>- pilih tipe equipment -</option>
                                    @foreach ($tipe_equipment as $item)
                                        <option value="{{ $item->id }}"
                                            @if ($item->id == $form->tipe_equipment_id) selected @endif>
                                            {{ $item->code }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group" id="functionalLocationContainer"
                                @if ($form->object_type != 'functional_location') style="display: none" @endif>
                                <label for="functional_location_id">Functional Location</label>
                                <select class="form-control form-control-lg" name="functional_location_id"
                                    id="functional_location_id">
                                    <option value="" selected disabled>- pilih functional location -</option>
                                    @foreach ($functional_location as $item)
                                        <option value="{{ $item->id }}"
                                            @if ($item->id == $form->functional_location_id) selected @endif>
                                            {{ $item->name }} ----
                                            ({{ $item->code ?? '#' }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="description">Description <span class="text-info">(optional)</span></label>
                                <input type="text" class="form-control" id="description" name="description"
                                    placeholder="Description" autocomplete="off" value="{{ $form->description }}">
                            </div>
                            <div class="form-group">
                                <label for="status">Status</label>
                                <select class="tom-select-class" name="status" id="status" required>
                                    <option value="" selected disabled>- pilih status -</option>
                                    <option value="active" @if ($form->status == 'active') selected @endif>
                                        Active
                                    </option>
                                    <option value="inactive" @if ($form->status == 'inactive') selected @endif>
                                        Inactive
                                    </option>
                                </select>
                            </div>
                            <div class="form-group d-flex justify-content-end">
                                <a href="{{ route('form.index') }}" type="button" class="btn btn-secondary">Cancel</a>
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
        document.addEventListener('DOMContentLoaded', function() {
            // Elemen-elemen yang terlibat
            const objectTypeSelect = document.getElementById('object_type');
            const tipeEquipmentContainer = document.getElementById('tipeEquipmentContainer');
            const functionalLocationContainer = document.getElementById('functionalLocationContainer');
            const tipeEquipmentSelect = document.getElementById('tipe_equipment_id');
            const functionalLocationSelect = document.getElementById('functional_location_id');

            // Fungsi untuk menyembunyikan dan menampilkan container yang sesuai
            function toggleContainers() {
                const selectedType = objectTypeSelect.value;

                if (selectedType === 'equipment') {
                    // Tampilkan Tipe Equipment, sembunyikan Functional Location
                    tipeEquipmentContainer.style.display = 'block';
                    functionalLocationContainer.style.display = 'none';

                    // Set required dan clear value untuk Functional Location
                    tipeEquipmentSelect.required = true;
                    functionalLocationSelect.required = false;
                    functionalLocationSelect.value = ''; // Kosongkan pilihan Functional Location

                } else if (selectedType === 'functional_location') {
                    // Tampilkan Functional Location, sembunyikan Tipe Equipment
                    functionalLocationContainer.style.display = 'block';
                    tipeEquipmentContainer.style.display = 'none';

                    // Set required dan clear value untuk Tipe Equipment
                    functionalLocationSelect.required = true;
                    tipeEquipmentSelect.required = false;
                    tipeEquipmentSelect.value = ''; // Kosongkan pilihan Tipe Equipment

                } else {
                    // Jika tidak ada yang dipilih, sembunyikan keduanya dan set semua required false
                    tipeEquipmentContainer.style.display = 'none';
                    functionalLocationContainer.style.display = 'none';
                    tipeEquipmentSelect.required = false;
                    functionalLocationSelect.required = false;
                }
            }

            // Event listener ketika nilai dropdown diubah
            objectTypeSelect.addEventListener('change', toggleContainers);

            // Panggil fungsi saat halaman pertama kali dimuat
            toggleContainers();
        });
    </script>
@endsection
