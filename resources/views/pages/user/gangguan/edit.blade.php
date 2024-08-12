@extends('layout.base')

@section('title-head')
    <title>Edit Data Gangguan</title>
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Edit Data Gangguan</h4>
                        <form id="editForm" action="{{ route('gangguan.update') }}" class="forms-sample mt-4" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <input type="text" name="id" value="{{ $gangguan->id }}" hidden>
                            <div class="form-group">
                                <label for="report_by">Report By</label>
                                <input type="text" class="form-control" id="report_by" name="report_by"
                                    autocomplete="off" required placeholder="input report by"
                                    value="{{ $gangguan->report_by }}">
                            </div>
                            <div class="form-group">
                                <label for="report_date">Report Date</label>
                                <input type="datetime-local" class="form-control" id="report_date" name="report_date"
                                    autocomplete="off" required value="{{ $gangguan->report_date }}">
                            </div>
                            <div class="form-group">
                                <label for="equipment_id">Equipment</label>
                                <select class="tom-select-class" id="equipment_id" name="equipment_id" required>
                                    <option value="" selected disabled>- pilih equipment -</option>
                                    @foreach ($equipment as $item)
                                        <option value="{{ $item->id }}"
                                            @if ($item->id == $gangguan->equipment_id) selected @endif>
                                            {{ $item->name }} - ({{ $item->code ?? '-' }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="category_id">Category</label>
                                <select class="tom-select-class" name="category_id" id="category_id" required>
                                    <option value="" selected disabled>- pilih category problem -</option>
                                    @foreach ($category as $item)
                                        <option value="{{ $item->id }}"
                                            @if ($item->id == $gangguan->category_id) selected @endif>
                                            {{ $item->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="problem_id">Problem</label>
                                <select class="tom-select-class" name="problem_id" id="problem_id" required>
                                    <option value="" selected disabled>- pilih problem -</option>
                                    <option value="0" selected>- Other -</option>
                                    @foreach ($problem as $item)
                                        <option value="{{ $item->id }}"
                                            @if ($item->id == $gangguan->problem_id) selected @endif>
                                            {{ $item->category->name ?? '-' }} - {{ $item->tipe_equipment->code ?? '-' }}
                                            -
                                            {{ $item->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div id="problemOtherContainer" class="form-group"
                                @if ($gangguan->problem_id != null) style="display: none" @endif>
                                <label for="problem_other">Problem Other</label>
                                <input type="text" class="form-control" id="problem_other" name="problem_other"
                                    autocomplete="off" placeholder="input problem other"
                                    value="{{ $gangguan->problem_other }}">
                            </div>
                            <div class="form-group">
                                <label for="photo">Photo <span class="text-info">(optional)</span></label>
                                <div class="text-left">
                                    <img class="img-thumbnail" id="previewImage"
                                        src="{{ asset('storage/' . $gangguan->photo) }}" alt="Tidak ada photo"
                                        style="max-width: 250px; max-height: 250px;">
                                </div>
                                <input type="file" class="form-control" id="photo" name="photo" autocomplete="off"
                                    placeholder="input photo" accept="image/*">
                            </div>
                            <div class="form-group">
                                <label for="classification_id">Classification</label>
                                <select class="tom-select-class" name="classification_id" id="classification_id" required>
                                    <option value="" selected disabled>- pilih classification problem -</option>
                                    @foreach ($classification as $item)
                                        <option value="{{ $item->id }}"
                                            @if ($item->id == $gangguan->classification_id) selected @endif>
                                            {{ $item->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="action">Action</label>
                                <input type="text" class="form-control" id="action" name="action" autocomplete="off"
                                    required placeholder="input action" value="{{ $gangguan->action }}">
                            </div>
                            <div class="form-group">
                                <label for="response_date">Action Date</label>
                                <input type="datetime-local" class="form-control" id="response_date" name="response_date"
                                    autocomplete="off" required value="{{ $gangguan->response_date }}">
                            </div>
                            <div class="form-group">
                                <label for="solved_by">Action By</label>
                                <input type="text" class="form-control" id="solved_by" name="solved_by"
                                    autocomplete="off" required placeholder="input action by"
                                    value="{{ $gangguan->solved_by }}">
                            </div>
                            <div class="form-group">
                                <label for="solved_date">Solved Date</label>
                                <input type="datetime-local" class="form-control" id="solved_date" name="solved_date"
                                    autocomplete="off" required value="{{ $gangguan->solved_date }}">
                            </div>
                            <div class="form-group">
                                <label for="analysis">Analysis</label>
                                <input type="text" class="form-control" id="analysis" name="analysis"
                                    autocomplete="off" required placeholder="input analysis"
                                    value="{{ $gangguan->analysis }}">
                            </div>
                            <div class="form-group">
                                <label for="photo_after">Photo After <span class="text-info">(optional)</span></label>
                                <div class="text-left">
                                    <img class="img-thumbnail" id="previewImageAfter"
                                        src="{{ asset('storage/' . $gangguan->photo_after) }}" alt="Tidak ada photo"
                                        style="max-width: 250px; max-height: 250px;">
                                </div>
                                <input type="file" class="form-control" id="photo_after" name="photo_after"
                                    autocomplete="off" placeholder="input photo" accept="image/*">
                            </div>
                            <div class="form-group">
                                <label for="status_id">Status</label>
                                <select class="tom-select-class" name="status_id" id="status_id" required>
                                    <option value="" selected disabled>- pilih status -</option>
                                    @foreach ($status as $item)
                                        <option value="{{ $item->id }}"
                                            @if ($item->id == $gangguan->status_id) selected @endif>
                                            {{ $item->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="is_changed">Ada pergantian Sparepart?</label>
                                <select class="tom-select-class" name="is_changed" id="is_changed" required>
                                    <option value="" selected disabled>- pilih keterangan -</option>
                                    <option value="0" @if ($gangguan->is_changed == 0) selected @endif>No
                                    </option>
                                    <option value="1" @if ($gangguan->is_changed == 1) selected @endif>Yes</option>
                                </select>
                            </div>
                            <div class="form-group d-flex justify-content-end">
                                <a href="{{ route('gangguan.index') }}" type="button"
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
