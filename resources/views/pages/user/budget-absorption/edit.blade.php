@extends('layout.base')

@section('title-head')
    <title>Edit Budget Absorption</title>
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Edit Budget Absorption</h4>
                        <form id="editForm" action="{{ route('budget-absorption.update') }}" class="forms-sample mt-4"
                            method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <input type="text" name="id" value="{{ $budget_absorption->id }}" hidden>
                            <div class="form-group">
                                <label for="project_id">Project</label>
                                <select class="tom-select-class" name="project_id" id="project_id" required>
                                    <option value="" disabled selected>- select project -</option>
                                    @foreach ($project as $item)
                                        <option value="{{ $item->id }}"
                                            @if ($item->id == $budget_absorption->project_id) selected @endif>
                                            {{ $item->name ?? '-' }} ({{ $item->fund_source->fund->name ?? '-' }} -
                                            {{ $item->fund_source->fund->code ?? '-' }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="name">Name</label>
                                <input type="text" class="form-control" id="name" name="name"
                                    placeholder="input project name" autocomplete="off" required
                                    value="{{ $budget_absorption->name }}">
                            </div>
                            <div class="form-group">
                                <label for="description">Description</label>
                                <input type="text" class="form-control" id="description" name="description"
                                    placeholder="input project description" autocomplete="off" required
                                    value="{{ $budget_absorption->description }}">
                            </div>
                            <div class="form-group">
                                <label for="date">Date</label>
                                <input type="date" min="0" class="form-control" id="date" name="date"
                                    autocomplete="off" required value="{{ $budget_absorption->date }}">
                            </div>
                            <div class="form-group">
                                <label for="value">Value (IDR)</label>
                                <input type="number" min="0" class="form-control" id="value" name="value"
                                    placeholder="input value project" autocomplete="off" required
                                    value="{{ $budget_absorption->value }}">
                            </div>
                            <div class="form-group">
                                <label for="po_number_sap">PO Number SAP</label>
                                <input type="number" min="1" class="form-control" id="po_number_sap"
                                    name="po_number_sap" placeholder="input PO number SAP" autocomplete="off"
                                    value="{{ $budget_absorption->po_number_sap }}">
                            </div>
                            <div class="form-group">
                                <label for="attachment">Attachment</label> <br>
                                @if ($budget_absorption->attachment != null)
                                    <button type='button' title='Attachment'
                                        class='btn btn-gradient-success btn-rounded btn-icon mb-2'
                                        onclick="window.open('{{ asset('storage/' . $budget_absorption->attachment) }}', '_blank')">
                                        <i class='mdi mdi-file-pdf'></i>
                                    </button>
                                @endif
                                <input type="file" class="form-control" id="attachment" name="attachment"
                                    accept="application/pdf">
                            </div>
                            <div class="form-group">
                                <label for="status">Status</label>
                                <select class="tom-select-class" name="status" id="status" required>
                                    <option value="" disabled selected>- select status -</option>
                                    <option value="Planned" @if ($budget_absorption->status == 'Planned') selected @endif>Planned
                                    </option>
                                    <option value="Realisasi Kegiatan" @if ($budget_absorption->status == 'Realisasi Kegiatan') selected @endif>
                                        Realisasi Kegiatan</option>
                                    <option value="Realisasi Pembayaran" @if ($budget_absorption->status == 'Realisasi Pembayaran') selected @endif>
                                        Realisasi Pembayaran</option>
                                </select>
                            </div>
                            <div class="form-group d-flex justify-content-end">
                                <a href="{{ route('budget-absorption.index') }}" type="button"
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
            const problemSelect = document.getElementById('problem_id');
            const problemOtherContainer = document.getElementById('problemOtherContainer');
            const problemOtherInput = document.getElementById('problem_other');

            problemSelect.addEventListener('change', function() {
                if (problemSelect.value === '0') {
                    // Jika problem_id bernilai null (selected value is empty)
                    problemOtherContainer.style.display = 'block';
                    problemOtherInput.setAttribute('required', 'required');
                } else {
                    // Jika problem_id tidak bernilai null
                    problemOtherContainer.style.display = 'none';
                    problemOtherInput.removeAttribute('required');
                    problemOtherInput.value = ''; // Mengosongkan input jika disembunyikan
                }
            });


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

            const imageInputAfter = document.getElementById('photo_after');
            const previewImageAfter = document.getElementById('previewImageAfter');

            imageInputAfter.addEventListener('change', function(event) {
                const selectedFile = event.target.files[0];

                if (selectedFile) {
                    const reader = new FileReader();

                    reader.onload = function(e) {
                        previewImageAfter.src = e.target.result;
                        previewImageAfter.style.display = 'block';
                    }

                    reader.readAsDataURL(selectedFile);
                }
            });
        });
    </script>
@endsection
