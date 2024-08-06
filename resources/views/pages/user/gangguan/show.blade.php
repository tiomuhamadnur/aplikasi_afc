@extends('layout.base')

@section('title-head')
    <title>Detail Data Gangguan</title>
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Detail Data Gangguan</h4>
                        <form id="editForm" action="#" class="forms-sample mt-4" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="form-group">
                                <label for="equipment_id">Equipment</label>
                                <input type="text" class="form-control" autocomplete="off" required
                                    value="{{ $gangguan->equipment->name }} - ({{ $gangguan->equipment->code ?? '-' }})"
                                    disabled>
                            </div>
                            <div class="form-group">
                                <label for="report_date">Report Date</label>
                                <input type="datetime-local" class="form-control" id="report_date" name="report_date"
                                    autocomplete="off" required value="{{ $gangguan->report_date }}" disabled>
                            </div>
                            <div class="form-group">
                                <label for="report_by">Report By</label>
                                <input type="text" class="form-control" id="report_by" name="report_by"
                                    autocomplete="off" required placeholder="input report by"
                                    value="{{ $gangguan->report_by }}" disabled>
                            </div>
                            <div class="form-group">
                                <label for="problem">Problem</label>
                                <input type="text" class="form-control" id="problem" name="problem" autocomplete="off"
                                    required placeholder="input problem" value="{{ $gangguan->problem }}" disabled>
                            </div>
                            <div class="form-group">
                                <label for="photo">Photo Before</label>
                                <div class="text-left">
                                    <img class="img-thumbnail" id="previewImage"
                                        src="{{ asset('storage/' . $gangguan->photo) }}" alt="Tidak ada photo"
                                        style="max-width: 250px; max-height: 250px;">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="photo">Photo After</label>
                                <div class="text-left">
                                    <img class="img-thumbnail" id="previewImage"
                                        src="{{ asset('storage/' . $gangguan->photo_after) }}" alt="Tidak ada photo"
                                        style="max-width: 250px; max-height: 250px;">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="category">Category</label>
                                <input type="text" class="form-control" id="report_by" name="report_by"
                                    autocomplete="off" required placeholder="input report by"
                                    value="{{ $gangguan->category }}" disabled>
                            </div>
                            <div class="form-group">
                                <label for="classification">Classification</label>
                                <input type="text" class="form-control" id="report_by" name="report_by"
                                    autocomplete="off" required placeholder="input report by"
                                    value="{{ $gangguan->classification }}" disabled>
                            </div>
                            <div class="form-group">
                                <label for="action">Action</label>
                                <input type="text" class="form-control" id="action" name="action" autocomplete="off"
                                    required placeholder="input action" value="{{ $gangguan->action }}" disabled>
                            </div>
                            <div class="form-group">
                                <label for="response_date">Action Date</label>
                                <input type="datetime-local" class="form-control" id="response_date" name="response_date"
                                    autocomplete="off" required value="{{ $gangguan->response_date }}" disabled>
                            </div>
                            <div class="form-group">
                                <label for="solved_by">Action By</label>
                                <input type="text" class="form-control" id="solved_by" name="solved_by"
                                    autocomplete="off" required placeholder="input action by"
                                    value="{{ $gangguan->solved_by }}" disabled>
                            </div>
                            <div class="form-group">
                                <label for="solved_date">Solved Date</label>
                                <input type="datetime-local" class="form-control" id="solved_date" name="solved_date"
                                    autocomplete="off" required value="{{ $gangguan->solved_date }}" disabled>
                            </div>
                            <div class="form-group">
                                <label for="analysis">Analysis</label>
                                <input type="text" class="form-control" id="analysis" name="analysis"
                                    autocomplete="off" required placeholder="input analysis"
                                    value="{{ $gangguan->analysis }}" disabled>
                            </div>
                            <div class="form-group">
                                <label for="status">Status</label>
                                <input type="text" class="form-control" id="report_by" name="report_by"
                                    autocomplete="off" required placeholder="input report by"
                                    value="{{ $gangguan->status }}" disabled>
                            </div>
                            <div class="form-group">
                                <label for="is_changed">Ada pergantian Sparepart?</label>
                                <input type="text" class="form-control" id="report_by" name="report_by"
                                    autocomplete="off" required placeholder="input report by"
                                    value="@if ($gangguan->is_changed == 1) Yes @else No @endif" disabled>
                            </div>
                            <div class="form-group d-flex justify-content-end">
                                <a href="{{ route('transaksi-barang.index') }}" type="button"
                                    class="btn btn-secondary">Cancel</a>
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
        });
    </script>
@endsection
