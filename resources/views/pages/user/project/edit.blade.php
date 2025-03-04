@extends('layout.base')

@section('title-head')
    <title>Edit Data Project</title>
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Edit Data Project</h4>
                        <form id="editForm" action="{{ route('project.update') }}" class="forms-sample mt-4" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <input type="text" name="id" value="{{ $project->id }}" hidden>
                            <div class="form-group">
                                <label for="name" class="required">Project Name</label>
                                <input type="text" class="form-control" id="name" name="name"
                                    placeholder="input project name" autocomplete="off" required
                                    value="{{ $project->name }}">
                            </div>
                            {{-- <div class="form-group">
                                <label for="description">Description</label>
                                <input type="text" class="form-control" id="description" name="description"
                                    placeholder="input project description" autocomplete="off" required
                                    value="{{ $project->description }}">
                            </div> --}}
                            <div class="form-group">
                                <label for="fund_source_id" class="required">Fund Source</label>
                                <select class="tom-select-class" name="fund_source_id" id="fund_source_id" required>
                                    <option value="" disabled selected>- select fund source -</option>
                                    @foreach ($fund_source as $item)
                                        <option value="{{ $item->id }}"
                                            @if ($item->id == $project->fund_source_id) selected @endif>
                                            {{ $item->fund->code ?? '-' }} {{ $item->fund->name ?? '-' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="start_period" class="required">Start Period</label>
                                <input type="date" class="form-control" id="start_period" name="start_period"
                                    placeholder="Start Period" autocomplete="off" required
                                    value="{{ $project->start_period }}">
                            </div>
                            <div class="form-group">
                                <label for="end_period" class="required">End Period</label>
                                <input type="date" class="form-control" id="end_period" name="end_period"
                                    placeholder="End Period" autocomplete="off" required value="{{ $project->end_period }}">
                            </div>
                            <div class="form-group">
                                <label for="departemen_id" class="required">Project Owner</label>
                                <select class="tom-select-class" name="departemen_id" id="departemen_id" required>
                                    <option value="" disabled selected>- select project owner -</option>
                                    @foreach ($departemen as $item)
                                        <option value="{{ $item->id }}"
                                            @if ($item->id == $project->departemen_id) selected @endif>
                                            {{ $item->name ?? '-' }} ({{ $item->code ?? '-' }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="perusahaan_id" class="required">Company</label>
                                <select class="tom-select-class" name="perusahaan_id" id="perusahaan_id" required>
                                    <option value="" disabled selected>- select company -</option>
                                    @foreach ($perusahaan as $item)
                                        <option value="{{ $item->id }}"
                                            @if ($item->id == $project->perusahaan_id) selected @endif>
                                            {{ $item->name ?? '-' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="status_budgeting_id" class="required">Status</label>
                                <select class="tom-select-class" name="status_budgeting_id" id="status_budgeting_id"
                                    required>
                                    <option value="" disabled selected>- select status -</option>
                                    @foreach ($status_budgeting as $item)
                                        <option value="{{ $item->id }}"
                                            @if ($item->id == $project->status_budgeting_id) selected @endif>{{ $item->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group d-flex justify-content-end">
                                <a href="{{ route('project.index') }}" type="button" class="btn btn-secondary">Cancel</a>
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
