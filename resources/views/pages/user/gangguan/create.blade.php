@extends('layout.base')

@section('title-head')
    <title>Add Data Gangguan</title>
    <style>
        .input-group {
            display: flex;
            width: 100%;
        }

        .input-group select {
            flex: 1 1 80%;
        }

        .input-group input {
            flex: 1 1 20%;
        }
    </style>
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Add Data Gangguan</h4>
                        <form id="addForm" action="{{ route('gangguan.store') }}" class="forms-sample mt-4" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            @method('POST')
                            <div class="form-group">
                                <label for="report_by">Report By</label>
                                <input type="text" class="form-control" id="report_by" name="report_by"
                                    autocomplete="off" required placeholder="input report by">
                            </div>
                            <div class="form-group">
                                <label for="report_date">Report Date</label>
                                <input type="datetime-local" class="form-control" id="report_date" name="report_date"
                                    autocomplete="off" required>
                            </div>
                            @livewire('form-gangguan')
                            <div id="problemOtherContainer" class="form-group" style="display: none">
                                <label for="problem_other">Problem (P) Other</label>
                                <input type="text" class="form-control" id="problem_other" name="problem_other"
                                    autocomplete="off" placeholder="input problem other">
                            </div>
                            <div id="causeOtherContainer" class="form-group" style="display: none">
                                <label for="cause_other">Cause (C) Other</label>
                                <input type="text" class="form-control" id="cause_other" name="cause_other"
                                    autocomplete="off" placeholder="input other cause">
                            </div>
                            <div id="remedyOtherContainer" class="form-group" style="display: none">
                                <label for="remedy_other">Remedy (R) Other</label>
                                <input type="text" class="form-control" id="remedy_other" name="remedy_other"
                                    autocomplete="off" placeholder="input other remedy">
                            </div>
                            <div class="form-group">
                                <label for="response_date">Action Date</label>
                                <input type="datetime-local" class="form-control" id="response_date" name="response_date"
                                    autocomplete="off" required>
                            </div>
                            <div class="form-group">
                                <label for="solved_user_id">Action By</label>
                                <select class="form-control form-control-lg" name="solved_user_id" id="solved_user_id"
                                    required>
                                    <option value="" selected disabled>- pilih user -</option>
                                    @foreach ($user as $item)
                                        <option value="{{ $item->id }}"
                                            @if ($item->id == auth()->user()->id) selected @endif>
                                            {{ $item->name ?? '-' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="solved_date">Solved Date</label>
                                <input type="datetime-local" class="form-control" id="solved_date" name="solved_date"
                                    autocomplete="off" required>
                            </div>
                            <div class="form-group">
                                <label for="photo">Photo Before <span class="text-info">(optional)</span></label>
                                <div class="text-left">
                                    <img class="img-thumbnail" id="previewImage" src="#" alt="Tidak ada photo"
                                        style="max-width: 250px; max-height: 250px; display: none;">
                                </div>
                                <input type="file" class="form-control" id="photo" name="photo" autocomplete="off"
                                    placeholder="input photo" accept="image/*">
                            </div>
                            <div class="form-group">
                                <label for="photo_after">Photo After <span class="text-info">(optional)</span></label>
                                <div class="text-left">
                                    <img class="img-thumbnail" id="previewImageAfter" src="#" alt="Tidak ada photo"
                                        style="max-width: 250px; max-height: 250px; display: none;">
                                </div>
                                <input type="file" class="form-control" id="photo_after" name="photo_after"
                                    autocomplete="off" placeholder="input photo" accept="image/*">
                            </div>
                            <div class="form-group">
                                <label for="status_id">Status</label>
                                <select class="form-control form-control-lg" name="status_id" id="status_id" required>
                                    <option value="" selected disabled>- pilih status -</option>
                                    @foreach ($status as $item)
                                        <option value="{{ $item->id }}">
                                            {{ $item->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="remark">Remark <span class="text-info">(optional)</span></label>
                                <textarea class="form-control" name="remark" id="remark" rows="4" placeholder="input remark (optional)"></textarea>
                            </div>
                            <div class="form-group">
                                <label for="is_changed">Ada pergantian Sparepart?</label>
                                <select class="form-control form-control-lg" name="is_changed" id="is_changed" required>
                                    <option value="" selected disabled>- pilih keterangan -</option>
                                    <option value="0">No</option>
                                    <option value="1">Yes</option>
                                </select>
                            </div>
                            {{-- <div class="form-group" id="sparePartContainer" style="display: none">
                                <div class="d-flex justify-content-between align-items-center mx-auto mb-2">
                                    <label for="barang_id" class="mb-0">Spare Part</label>
                                    <button type="button" class="btn btn-success btn-rounded btn-icon" title="Add row"
                                        id="addRow">
                                        <i class="mdi mdi-plus-circle"></i>
                                    </button>
                                </div>
                                <div id="inputContainer">
                                    <div class="input-group">
                                        <select class="tom-select-class col-8" id="barang_id" name="barang_ids[]">
                                            <option value="" selected disabled>- pilih spare part -</option>
                                            @foreach ($barang as $item)
                                                <option value="{{ $item->id }}">
                                                    ({{ $item->material_number ?? '-' }})
                                                    - {{ $item->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <input type="number" class="form-control col-4" id="qty" name="qty[]"
                                            placeholder="qty" min="1">
                                    </div>
                                </div>
                            </div> --}}
                            <div class="form-group d-flex justify-content-end">
                                <a href="{{ route('gangguan.index') }}" type="button"
                                    class="btn btn-secondary">Cancel</a>
                                <button type="submit" form="addForm" class="btn btn-primary">Submit</button>
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




            const causeSelect = document.getElementById('cause_id');
            const causeOtherContainer = document.getElementById('causeOtherContainer');
            const causeOtherInput = document.getElementById('cause_other');

            causeSelect.addEventListener('change', function() {
                if (causeSelect.value === '0') {
                    // Jika problem_id bernilai null (selected value is empty)
                    causeOtherContainer.style.display = 'block';
                    causeOtherInput.setAttribute('required', 'required');
                } else {
                    // Jika problem_id tidak bernilai null
                    causeOtherContainer.style.display = 'none';
                    causeOtherInput.removeAttribute('required');
                    causeOtherInput.value = ''; // Mengosongkan input jika disembunyikan
                }
            });



            const remedySelect = document.getElementById('remedy_id');
            const remedyOtherContainer = document.getElementById('remedyOtherContainer');
            const remedyOtherInput = document.getElementById('remedy_other');

            remedySelect.addEventListener('change', function() {
                if (remedySelect.value === '0') {
                    // Jika problem_id bernilai null (selected value is empty)
                    remedyOtherContainer.style.display = 'block';
                    remedyOtherInput.setAttribute('required', 'required');
                } else {
                    // Jika problem_id tidak bernilai null
                    remedyOtherContainer.style.display = 'none';
                    remedyOtherInput.removeAttribute('required');
                    remedyOtherInput.value = ''; // Mengosongkan input jika disembunyikan
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

    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
    {{-- <script>
        document.addEventListener('DOMContentLoaded', function() {
            var isChangedSelect = document.getElementById('is_changed');
            var sparePartContainer = document.getElementById('sparePartContainer');
            var barangSelect = document.getElementById('barang_id');
            var qtyInput = document.getElementById('qty');

            function removeAllTomSelectBaru() {
                var elements = document.querySelectorAll('.tom-select-baru');

                elements.forEach(function(element) {
                    element.remove();
                });
            }

            function updateDisplay() {
                if (isChangedSelect.value === '1') {
                    sparePartContainer.style.display = 'block';
                    barangSelect.required = true;
                    qtyInput.required = true;
                } else {
                    sparePartContainer.style.display = 'none';
                    barangSelect.required = false;
                    qtyInput.required = false;
                    removeAllTomSelectBaru();
                }
            }

            // Initial update on page load
            updateDisplay();

            // Add event listener to handle changes
            isChangedSelect.addEventListener('change', function() {
                updateDisplay();
            });

            var addRowButton = document.getElementById('addRow');
            var inputContainer = document.getElementById('inputContainer');
            var settings = {}; // Atur pengaturan Tom Select sesuai kebutuhan

            function initializeTomSelect(selector) {
                document.querySelectorAll(selector).forEach(function(el) {
                    if (!el.tomSelectInstance) {
                        el.tomSelectInstance = new TomSelect(el, settings);
                    }
                });
            }

            // Inisialisasi Tom Select pada load halaman
            // initializeTomSelect('.tom-select-class');

            addRowButton.addEventListener('click', function() {
                var row = document.createElement('div');
                row.classList.add('input-group');
                row.classList.add('tom-select-baru');
                var uniqueClass = 'tom-select-' + Date.now();
                row.innerHTML = `
                <select class="tom-select-class ${uniqueClass} col-8" name="barang_ids[]" required>
                    <option value="" selected disabled>- pilih spare part -</option>
                    @foreach ($barang as $item)
                        <option value="{{ $item->id }}">
                            ({{ $item->material_number ?? '-' }}) - {{ $item->name }}
                        </option>
                    @endforeach
                </select>
                <input type="number" class="form-control col-3" name="qty[]" placeholder="qty" required min="1">
                <div class="input-group-append">
                    <button type="button" class="btn btn-danger btn-rounded btn-icon removeRow" title="Remove row" style="padding: 0.25rem 0.5rem; font-size: 0.75rem; line-height: 1.5;">
                        <i class="mdi mdi-minus-circle"></i>
                    </button>
                </div>
            `;
                inputContainer.appendChild(row);

                // Inisialisasi Tom Select untuk elemen baru
                initializeTomSelect('.' + uniqueClass);

                // Add event listener for the new remove button
                row.querySelector('.removeRow').addEventListener('click', function() {
                    if (inputContainer.children.length > 1) {
                        inputContainer.removeChild(row);
                    } else {
                        console.warn('Cannot remove the last row.');
                    }
                });
            });

            // Event delegation for remove buttons in existing rows
            inputContainer.addEventListener('click', function(event) {
                if (event.target.classList.contains('removeRow')) {
                    var row = event.target.closest('.input-group');
                    if (inputContainer.children.length > 1) {
                        inputContainer.removeChild(row);
                    }
                }
            });
        });
    </script> --}}
@endsection
