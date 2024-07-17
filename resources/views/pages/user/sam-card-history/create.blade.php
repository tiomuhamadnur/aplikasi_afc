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
                        <form id="editForm" action="{{ route('sam-history.store') }}" method="POST">
                            @csrf
                            @method('POST')
                            <div class="form-group">
                                <label for="sam_card_id">SAM Card</label>
                                <input type="text" name="sam_card_id" value="{{ $sam_card->id }}" hidden>
                                <select class="form-control form-control-lg">
                                    <option selected disabled>
                                        {{ $sam_card->tid ?? 'No TID' }} - {{ $sam_card->pin ?? 'No pin' }} -
                                        {{ $sam_card->mc ?? 'No MC' }}
                                    </option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="relasi_area_id">Stasiun</label>
                                <select name="relasi_area_id" id="relasi_area_id" class="form-control form-control-lg"
                                    required>
                                    <option value="" selected disabled>- pilih stasiun -</option>
                                    @foreach ($area as $item)
                                        <option value="{{ $item->id }}">{{ $item->sub_lokasi->name ?? '-' }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="pg_id">PG ID</label>
                                <input type="text" class="form-control" name="pg_id" id="pg_id"
                                    placeholder="input PG ID" required autocomplete="off">
                            </div>
                            <div class="form-group">
                                <label for="type">Type</label>
                                <select name="type" id="type" class="form-control form-control-lg" required>
                                    <option value="">- pilih type -</option>
                                    <option value="entry">Entry</option>
                                    <option value="exit">Exit</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="tanggal">Tanggal</label>
                                <input type="date" class="form-control" name="tanggal" id="tanggal"
                                    placeholder="input tanggal" required>
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
