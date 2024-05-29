@extends('layout.base')

@section('title-head')
    <title>Admin | Edit Area</title>
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Edit Data Area</h4>
                        <form id="editForm" action="{{ route('area.update') }}" method="POST">
                            @csrf
                            @method('PUT')
                            <input type="text" name="id" value="{{ $area->id }}" hidden>
                            <div class="form-group">
                                <label for="lokasi_id">Lokasi</label>
                                <select class="form-control form-control-lg" name="lokasi_id" id="lokasi_id" required>
                                    <option value="" selected disabled>- pilih lokasi -</option>
                                    @foreach ($lokasi as $item)
                                        <option value="{{ $item->id }}"
                                            @if ($item->id == $area->lokasi->id) selected @endif>{{ $item->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="sub_lokasi_id">Sub Lokasi</label>
                                <select class="form-control form-control-lg" name="sub_lokasi_id" id="sub_lokasi_id"
                                    required>
                                    <option value="" selected disabled>- pilih sub lokasi -</option>
                                    @foreach ($sub_lokasi as $item)
                                        <option value="{{ $item->id }}"
                                            @if ($item->id == $area->sub_lokasi->id) selected @endif>{{ $item->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="detail_lokasi_id">Detail Lokasi</label>
                                <select class="form-control form-control-lg" name="detail_lokasi_id" id="detail_lokasi_id"
                                    required>
                                    <option value="" selected disabled>- pilih detail lokasi -</option>
                                    @foreach ($detail_lokasi as $item)
                                        <option value="{{ $item->id }}"
                                            @if ($item->id == $area->detail_lokasi->id) selected @endif>{{ $item->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group d-flex justify-content-end">
                                <a href="{{ route('area.index') }}" type="button" class="btn btn-secondary">Cancel</a>
                                <button type="submit" form="editForm" class="btn btn-primary">Update</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
