@extends('layout.base')

@section('title-head')
    <title>Sam Card</title>
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Edit Data Sam Card</h4>
                        <form id="editForm" action="{{ route('sam-card.update') }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <input type="text" name="id" value="{{ $sam_card->id }}" required hidden>
                            <div class="form-group">
                                <label for="uid">UID</label>
                                <input type="text" class="form-control" id="uid" name="uid" placeholder="UID"
                                    autocomplete="off" value="{{ $sam_card->uid }}" required>
                            </div>
                            <div class="form-group">
                                <label for="mid">MID</label>
                                <input type="text" class="form-control" id="mid" name="mid" placeholder="MID"
                                    autocomplete="off" value="{{ $sam_card->mid }}" required>
                            </div>
                            <div class="form-group">
                                <label for="tid">TID</label>
                                <input type="text" class="form-control" id="tid" name="tid" placeholder="TID"
                                    autocomplete="off" value="{{ $sam_card->tid }}" required>
                            </div>
                            <div class="form-group">
                                <label for="pin">PIN</label>
                                <input type="text" class="form-control" id="pin" name="pin" placeholder="PIN"
                                    autocomplete="off" value="{{ $sam_card->pin }}" required>
                            </div>
                            <div class="form-group">
                                <label for="mc">Marry Code</label>
                                <input type="text" class="form-control" id="mc" name="mc"
                                    placeholder="Marry Code" autocomplete="off" value="{{ $sam_card->mc }}" required>
                            </div>
                            <div class="form-group">
                                <label for="alokasi">Alokasi <span class="text-info">(opsional)</span></label>
                                <input type="text" class="form-control" id="alokasi" name="alokasi"
                                    placeholder="Alokasi" value="{{ $sam_card->alokasi }}" autocomplete="off">
                            </div>
                            <div class="form-group">
                                <label for="status">Status</label>
                                <select name="status" id="status" class="form-control form-control-lg" required>
                                    <option value="">- pilih status -</option>
                                    <option value="ready" @if ($sam_card->status == 'ready') selected @endif>READY</option>
                                    <option value="used" @if ($sam_card->status == 'used') selected @endif>USED</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="photo">Photo <span class="text-info">(opsional)</span></label>
                                <div class="text-start mb-1">
                                    <img class="img-thumbnail" id="previewImage"
                                        src="{{ asset('storage/' . $sam_card->photo) }}" alt="No Photo SAM Card"
                                        style="max-width: 250px; max-height: 250px;">
                                </div>
                                <input type="file" class="form-control" id="photo" name="photo" accept="image/*">
                            </div>
                            <div class="form-group d-flex justify-content-end">
                                <a href="{{ route('sam-card.index') }}" type="button" class="btn btn-secondary">Cancel</a>
                                <button type="submit" form="editForm" class="btn btn-primary">Submit</button>
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
