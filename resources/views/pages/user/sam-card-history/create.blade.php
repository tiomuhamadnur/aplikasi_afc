@extends('layout.base')

@section('title-head')
    <title>Sam Card History</title>
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Add Data Sam Card History</h4>
                        <form id="editForm" action="{{ route('sam-history.store') }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            @method('POST')
                            <div class="form-group">
                                <label for="sam_card_id" class="required">SAM Card</label>
                                <input type="text" name="sam_card_id" value="{{ $sam_card->id }}" hidden>
                                <select class="tom-select-class">
                                    <option selected disabled>
                                        {{ $sam_card->tid ?? 'No TID' }} - {{ $sam_card->pin ?? 'No pin' }} -
                                        {{ $sam_card->mc ?? 'No MC' }}
                                    </option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="equipment_id" class="required">PG ID</label>
                                <select name="equipment_id" id="equipment_id" class="tom-select-class" required>
                                    <option value="" selected disabled>- pilih PG ID -</option>
                                    @foreach ($pg as $item)
                                        <option value="{{ $item->id }}">{{ $item->name ?? '-' }}
                                            ({{ $item->code ?? '-' }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="type" class="required">Type</label>
                                <select name="type" id="type" class="tom-select-class" required>
                                    <option value="">- pilih type -</option>
                                    <option value="entry">Entry</option>
                                    <option value="exit">Exit</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="tanggal" class="required">Tanggal</label>
                                <input type="date" class="form-control" name="tanggal" id="tanggal"
                                    placeholder="input tanggal" required>
                            </div>
                            <div class="form-group">
                                <label for="old_uid">UID Old SAM Card <span class="text-info">(optional)</span></label>
                                <input type="text" class="form-control" name="old_uid" id="old_uid"
                                    placeholder="input Old UID" autocomplete="off">
                            </div>
                            <div class="form-group">
                                <label for="old_sam_card_id">Old SAM Card <span class="text-info">(optional)</span></label>
                                <select name="old_sam_card_id" id="old_sam_card_id" class="tom-select-class">
                                    <option value="" selected disabled>- pilih old SAM card -</option>
                                    @foreach ($sam_cards as $item)
                                        <option value="{{ $item->id }}">{{ $item->tid }} - {{ $item->pin }} -
                                            {{ $item->mc ?? 'No MC' }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="photo" class="required">Photo Old SAM Card</label>
                                <div class="text-center">
                                    <img class="img-thumbnail" id="previewImage" src="#" alt="Preview"
                                        style="max-width: 250px; max-height: 250px; display: none;">
                                </div>
                                <input type="file" class="form-control" id="photo" name="photo" accept="image/*"
                                    required>
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
    </script>
@endsection
