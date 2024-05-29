@extends('layout.base')

@section('title-head')
    <title>Admin | Edit Struktur</title>
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Edit Data Struktur</h4>
                        <form id="editForm" action="{{ route('struktur.update') }}" method="POST">
                            @csrf
                            @method('PUT')
                            <input type="text" name="id" value="{{ $struktur->id }}" hidden>
                            <div class="form-group">
                                <label for="direktorat_id">Direktorat</label>
                                <select class="form-control form-control-lg" name="direktorat_id" id="direktorat_id"
                                    required>
                                    <option value="" selected disabled>- pilih direktorat -</option>
                                    @foreach ($direktorat as $item)
                                        <option value="{{ $item->id }}"
                                            @if ($item->id == $struktur->direktorat->id) selected @endif>{{ $item->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="divisi_id">Divisi</label>
                                <select class="form-control form-control-lg" name="divisi_id" id="divisi_id" required>
                                    <option value="" selected disabled>- pilih divisi -</option>
                                    @foreach ($divisi as $item)
                                        <option value="{{ $item->id }}"
                                            @if ($item->id == $struktur->divisi->id) selected @endif>{{ $item->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="departemen_id">Departemen</label>
                                <select class="form-control form-control-lg" name="departemen_id" id="departemen_id"
                                    required>
                                    <option value="" selected disabled>- pilih departemen -</option>
                                    @foreach ($departemen as $item)
                                        <option value="{{ $item->id }}"
                                            @if ($item->id == $struktur->departemen->id) selected @endif>{{ $item->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="seksi_id">Seksi</label>
                                <select class="form-control form-control-lg" name="seksi_id" id="seksi_id" required>
                                    <option value="" selected disabled>- pilih seksi -</option>
                                    @foreach ($seksi as $item)
                                        <option value="{{ $item->id }}"
                                            @if ($item->id == $struktur->seksi->id) selected @endif>{{ $item->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group d-flex justify-content-end">
                                <a href="{{ route('struktur.index') }}" type="button" class="btn btn-secondary">Cancel</a>
                                <button type="submit" form="editForm" class="btn btn-primary">Update</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
