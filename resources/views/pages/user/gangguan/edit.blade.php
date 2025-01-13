@extends('layout.base')

@section('title-head')
    <title>Edit Failure Report</title>
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Edit Failure Report</h4>
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
                                <label for="is_downtime">Apakah terjadi Downtime?</label>
                                <select class="tom-select-class" name="is_downtime" id="is_downtime" required>
                                    <option value="" selected disabled>- pilih keterangan -</option>
                                    <option value="0" @if($gangguan->is_downtime == 0) selected @endif>No</option>
                                    <option value="1" @if($gangguan->is_downtime == 1) selected @endif>Yes</option>
                                </select>
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
                            {{-- <div class="form-group">
                                <label for="problem_id">Problem (P)</label>
                                <select class="tom-select-class" name="problem_id" id="problem_id">
                                    <option value="" selected disabled>- pilih problem -</option>
                                    <option value="0">- Other -</option>
                                    @foreach ($problem as $item)
                                        <option value="{{ $item->id }}"
                                            @if ($item->id == $gangguan->problem_id) selected @endif>
                                            {{ $item->name ?? '-' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="cause_id">Cause (C)</label>
                                <select class="tom-select-class" name="cause_id" id="cause_id">
                                    <option value="" selected disabled>- pilih cause -</option>
                                    <option value="0">- Other -</option>
                                    @foreach ($cause as $item)
                                        <option value="{{ $item->id }}"
                                            @if ($item->id == $gangguan->cause_id) selected @endif>
                                            {{ $item->name ?? '-' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="remedy_id">Remedy (R)</label>
                                <select class="tom-select-class" name="remedy_id" id="remedy_id">
                                    <option value="" selected disabled>- pilih remedy -</option>
                                    <option value="0">- Other -</option>
                                    @foreach ($remedies as $item)
                                        <option value="{{ $item->id }}"
                                            @if ($item->id == $remedy_id) selected @endif>
                                            {{ $item->name ?? '-' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div> --}}
                            <div class="form-group">
                                <label for="classification_id">Classification</label>
                                <select class="tom-select-class" name="classification_id" id="classification_id" required>
                                    <option value="" selected disabled>- pilih classification -</option>
                                    @foreach ($classification as $item)
                                        <option value="{{ $item->id }}"
                                            @if ($item->id == $gangguan->classification_id) selected @endif>
                                            {{ $item->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="problem_other">Problem (P)</label>
                                <input type="text" class="form-control" id="problem_other" name="problem_other"
                                    autocomplete="off" placeholder="input problem"
                                    value="{{ $gangguan->problem_other }}" required>
                            </div>
                            <div class="form-group">
                                <label for="cause_other">Cause (C)</label>
                                <input type="text" class="form-control" id="cause_other" name="cause_other"
                                    autocomplete="off" placeholder="input cause"
                                    value="{{ $gangguan->cause_other }}" required>
                            </div>
                            <div class="form-group">
                                <label for="remedy_other">Remedy (R)</label>
                                @if ($gangguan->trans_gangguan_remedy)
                                    @foreach ($gangguan->trans_gangguan_remedy as $remedy)
                                        <input type="text" class="form-control" id="remedy_other" name="remedy_other"
                                            autocomplete="off" placeholder="input remedy"
                                            value="{{ $remedy->remedy_other }}" required>
                                    @endforeach
                                @endif
                            </div>
                            <div class="form-group">
                                <label for="response_date">Action Date</label>
                                <input type="datetime-local" class="form-control" id="response_date"
                                    name="response_date" autocomplete="off" required
                                    value="{{ $gangguan->response_date }}">
                            </div>
                            <div class="form-group">
                                <label for="solved_user_id">Action By</label>
                                <select class="tom-select-class" name="solved_user_id" id="solved_user_id" required>
                                    <option value="" selected disabled>- pilih user -</option>
                                    @foreach ($user as $item)
                                        <option value="{{ $item->id }}"
                                            @if ($item->id == $gangguan->solved_user_id) selected @endif>
                                            {{ $item->name ?? '-' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="solved_date">Solved Date</label>
                                <input type="datetime-local" class="form-control" id="solved_date" name="solved_date"
                                    autocomplete="off" required value="{{ $gangguan->solved_date }}">
                            </div>
                            <div class="form-group">
                                <label for="photo">Photo Before <span class="text-info">(optional)</span></label>
                                <div class="text-left">
                                    <img class="img-thumbnail" id="previewImage"
                                        src="{{ asset('storage/' . $gangguan->photo) }}" alt="Tidak ada photo"
                                        style="max-width: 250px; max-height: 250px;">
                                </div>
                                <input type="file" class="form-control" id="photo" name="photo"
                                    autocomplete="off" placeholder="input photo" accept="image/*">
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
                                <label for="remark">Remark <span class="text-info">(optional)</span></label>
                                <textarea class="form-control" name="remark" id="remark" rows="4" placeholder="input remark (optional)">{{ $gangguan->remark }}</textarea>
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
