@extends('layout.base')

@section('title-head')
    <title>Add Failure Report LM</title>
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Add Failure Report LM</h4>
                        <form id="addForm" action="{{ route('gangguan.lm.store') }}" class="forms-sample mt-4" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            @method('POST')
                            <div class="form-group">
                                <label for="report_user_id" class="required">Report By</label>
                                <select class="tom-select-class" name="report_user_id" id="report_user_id" required>
                                    <option value="" selected disabled>- select reporter -</option>
                                    @foreach ($user as $item)
                                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="report_user" class="required">Report By</label>
                                <input type="text" class="form-control form-control-lg" id="report_user"
                                    name="report_user" autocomplete="off" required placeholder="input report by">
                            </div>
                            <div class="form-group">
                                <label for="report_date" class="required">Report Date</label>
                                <input type="datetime-local" class="form-control form-control-lg" id="report_date"
                                    name="report_date" autocomplete="off" required>
                            </div>
                            <div class="form-group">
                                <label for="equipment_id" class="required">Equipment</label>
                                <select class="tom-select-class" name="equipment_id" id="equipment_id" required>
                                    <option value="" selected disabled>- select equipment -</option>
                                    @foreach ($equipment as $item)
                                        <option value="{{ $item->id }}">{{ $item->name }} ({{ $item->code }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="category_id" class="required">Category</label>
                                <select class="tom-select-class" name="category_id" id="category_id" required>
                                    <option value="" selected disabled>- select category -</option>
                                    @foreach ($category as $item)
                                        <option value="{{ $item->id }}">{{ $item->name }} ({{ $item->code }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="classification_id" class="required">Classification</label>
                                <select class="tom-select-class" name="classification_id" id="classification_id" required>
                                    <option value="" selected disabled>- select classification -</option>
                                    @foreach ($classification as $item)
                                        <option value="{{ $item->id }}">{{ $item->name }} ({{ $item->code }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="lintas_id" class="required">Lintas</label>
                                <select class="tom-select-class" name="lintas_id" id="lintas_id" required>
                                    <option value="" selected disabled>- select lintas -</option>
                                    @foreach ($lintas as $item)
                                        <option value="{{ $item->id }}">
                                            {{ $item->sub_lokasi->code ?? '' }} - ({{ $item->detail_lokasi->code ?? '' }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="line_id" class="required">Line</label>
                                <select class="tom-select-class" name="line_id" id="line_id" required>
                                    <option value="" selected disabled>- select line -</option>
                                    @foreach ($line as $item)
                                        <option value="{{ $item->id }}">
                                            {{ $item->sub_lokasi->code ?? '' }} - ({{ $item->detail_lokasi->code ?? '' }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="is_downtime" class="required">Is Downtime?</label>
                                <select class="tom-select-class" name="is_downtime" id="is_downtime" required>
                                    <option value="" selected disabled>- select option -</option>
                                    <option value="0">No</option>
                                    <option value="1">Yes</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="is_delay" class="required">Is Delay?</label>
                                <select class="tom-select-class" name="is_delay" id="is_delay" required>
                                    <option value="" selected disabled>- select option -</option>
                                    <option value="0">No</option>
                                    <option value="1">Yes</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="delay" class="required">Delay (Minutes)</label>
                                <input type="number" class="form-control form-control-lg" id="delay" name="delay"
                                    autocomplete="off" required placeholder="input delay value" min="0">
                            </div>
                            <div class="form-group">
                                <label for="response_user_id" class="required">Response By</label>
                                <select class="tom-select-class" name="response_user_id" id="response_user_id" required>
                                    <option value="" selected disabled>- select response by -</option>
                                    @foreach ($user as $item)
                                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="response_user" class="required">Response By</label>
                                <input type="text" class="form-control form-control-lg" id="response_user"
                                    name="response_user" autocomplete="off" required placeholder="input response by">
                            </div>
                            <div class="form-group">
                                <label for="response_date" class="required">Response Date</label>
                                <input type="datetime-local" class="form-control form-control-lg" id="response_date"
                                    name="response_date" autocomplete="off" required>
                            </div>
                            <div class="form-group">
                                <label for="problem_id" class="required">Problem (P)</label>
                                <select class="tom-select-class" name="problem_id" id="problem_id" required>
                                    <option value="" selected disabled>- select problem -</option>
                                    @foreach ($problem as $item)
                                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="problem_other" class="required">Problem (P)</label>
                                <textarea class="form-control" name="problem_other" id="problem_other" rows="4"
                                    placeholder="input detail problem" required></textarea>
                            </div>
                            <div class="form-group">
                                <label for="cause_id" class="required">Cause (C)</label>
                                <select class="tom-select-class" name="cause_id" id="cause_id" required>
                                    <option value="" selected disabled>- select cause -</option>
                                    @foreach ($cause as $item)
                                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="cause_other" class="required">Cause (C)</label>
                                <textarea class="form-control" name="cause_other" id="cause_other" rows="4" placeholder="input detail cause"
                                    required></textarea>
                            </div>
                            <div class="form-group">
                                <label for="remedy_other" class="required">Remedy (R)</label>
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <th>No</th>
                                            <th>Detail Action</th>
                                            <th>Action By</th>
                                            <th>Action Date</th>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>
                                                    <label class="required">1</label>
                                                </td>
                                                <td>
                                                    <textarea class="form-control" name="remedy_other[]" id="remedy_other" rows="2"
                                                        placeholder="input detail remedy" required></textarea>
                                                </td>
                                                <td>
                                                    <select class="tom-select-class" name="remedy_user_id[]"
                                                        id="remedy_user_id" required>
                                                        <option value="" selected disabled>- select action by -
                                                        </option>
                                                        @foreach ($user as $item)
                                                            <option value="{{ $item->id }}">{{ $item->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td>
                                                    <input type="datetime-local" class="form-control" id="remedy_date"
                                                        name="remedy_date[]" autocomplete="off" required>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>2</td>
                                                <td>
                                                    <textarea class="form-control" name="remedy_other[]" id="remedy_other" rows="2"
                                                        placeholder="input detail remedy (optional)"></textarea>
                                                </td>
                                                <td>
                                                    <select class="tom-select-class" name="remedy_user_id[]"
                                                        id="remedy_user_id">
                                                        <option value="" selected disabled>- select action by (optional) -</option>
                                                        @foreach ($user as $item)
                                                            <option value="{{ $item->id }}">{{ $item->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td>
                                                    <input type="datetime-local" class="form-control" id="remedy_date"
                                                        name="remedy_date[]" autocomplete="off">
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="is_change_sparepart" class="required">Is Change Sparepart?</label>
                                <select class="tom-select-class" name="is_change_sparepart" id="is_change_sparepart"
                                    required>
                                    <option value="" selected disabled>- select option -</option>
                                    <option value="0">No</option>
                                    <option value="1">Yes</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="is_change_trainset" class="required">Is Change Trainset?</label>
                                <select class="tom-select-class" name="is_change_trainset" id="is_change_trainset"
                                    required>
                                    <option value="" selected disabled>- select option -</option>
                                    <option value="0">No</option>
                                    <option value="1">Yes</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="solved_user_id" class="required">Solved By</label>
                                <select class="tom-select-class" name="solved_user_id" id="solved_user_id" required>
                                    <option value="" selected disabled>- select solved by -</option>
                                    @foreach ($user as $item)
                                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="solved_user" class="required">Solved By</label>
                                <input type="text" class="form-control form-control-lg" id="solved_user"
                                    name="solved_user" autocomplete="off" required placeholder="input solved by">
                            </div>
                            <div class="form-group">
                                <label for="solved_date" class="required">Solved Date</label>
                                <input type="datetime-local" class="form-control form-control-lg" id="solved_date"
                                    name="solved_date" autocomplete="off" required>
                            </div>
                            <div class="form-group">
                                <label for="photo_before">Photo Before <span class="text-info">(optional)</span></label>
                                <div class="text-left">
                                    <img class="img-thumbnail" id="previewImage" src="#" alt="Tidak ada photo"
                                        style="max-width: 250px; max-height: 250px; display: none;">
                                </div>
                                <input type="file" class="form-control" id="photo_before" name="photo_before"
                                    autocomplete="off" placeholder="input photo" accept="image/*">
                            </div>
                            <div class="form-group">
                                <label for="photo_after">Photo After <span class="text-info">(optional)</span></label>
                                <div class="text-left">
                                    <img class="img-thumbnail" id="previewImageAfter" src="#"
                                        alt="Tidak ada photo" style="max-width: 250px; max-height: 250px; display: none;">
                                </div>
                                <input type="file" class="form-control" id="photo_after" name="photo_after"
                                    autocomplete="off" placeholder="input photo" accept="image/*">
                            </div>
                            <div class="form-group">
                                <label for="status_id" class="required">Status</label>
                                <select class="tom-select-class" name="status_id" id="status_id" required>
                                    <option value="" selected disabled>- select status -</option>
                                    @foreach ($status as $item)
                                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="remark">Remark <span class="text-info">(optional)</span></label>
                                <textarea class="form-control" name="remark" id="remark" rows="4" placeholder="input remark (optional)"></textarea>
                            </div>
                            <div class="form-group">
                                <label for="analysis">Analysis <span class="text-info">(optional)</span></label>
                                <textarea class="form-control" name="analysis" id="analysis" rows="4"
                                    placeholder="input analysis (optional)"></textarea>
                            </div>

                            {{-- @livewire('form-gangguan') --}}

                            <div class="form-group d-flex justify-content-end">
                                <a href="{{ route('gangguan.lm.index') }}" type="button"
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
            const imageInput = document.getElementById('photo_before');
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
