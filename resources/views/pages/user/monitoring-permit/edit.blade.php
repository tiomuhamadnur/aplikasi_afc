@extends('layout.base')

@section('title-head')
    <title>Admin | Edit Monitoring Permit</title>
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Edit Data Monitoring Permit</h4>
                        <form id="editForm" action="{{ route('monitoring-permit.update') }}" method="POST">
                            @csrf
                            @method('PUT')
                            <input type="text" name="id" value="{{ $monitoring_permit->id }}" hidden>
                            <div class="form-group">
                                <label for="departemen" class="required">Departemen</label>
                                <input type="text" class="form-control" id="departemen" placeholder="Departemen"
                                    value="{{ $monitoring_permit->departemen->name }}" autocomplete="off" disabled>
                            </div>
                            <div class="form-group">
                                <label for="tipe_permit_id" class="required">Tipe Permit</label>
                                <select class="tom-select-class" id="tipe_permit_id" name="tipe_permit_id"
                                    required>
                                    <option value="" selected disabled>- pilih tipe permit -</option>
                                    @foreach ($tipe_permit as $item)
                                        <option value="{{ $item->id }}"
                                            @if ($item->id == $monitoring_permit->tipe_permit->id) selected @endif>{{ $item->code }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="tipe_pekerjaan_id" class="required">Tipe Pekerjaan</label>
                                <select class="tom-select-class" id="tipe_pekerjaan_id" name="tipe_pekerjaan_id"
                                    required>
                                    <option value="" selected disabled>- pilih tipe pekerjaan -</option>
                                    @foreach ($tipe_pekerjaan as $item)
                                        <option value="{{ $item->id }}"
                                            @if ($item->id == $monitoring_permit->tipe_pekerjaan->id) selected @endif>{{ $item->name }}
                                            ({{ $item->code }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="nomor" class="required">Nomor Permit</label>
                                <input type="text" class="form-control" id="nomor" name="nomor" placeholder="Nomor"
                                    value="{{ $monitoring_permit->nomor }}" autocomplete="off" required>
                            </div>
                            <div class="form-group">
                                <label for="name" class="required">Nama Pekerjaan</label>
                                <input type="text" class="form-control" id="name" name="name"
                                    placeholder="Nama Pekerjaan" value="{{ $monitoring_permit->name }}" autocomplete="off"
                                    required>
                            </div>
                            <div class="form-group">
                                <label for="tanggal_expired">Tanggal Expired</label>
                                <input type="date" class="form-control" id="tanggal_expired" name="tanggal_expired"
                                    autocomplete="off" required value="{{ $monitoring_permit->tanggal_expired }}">
                            </div>
                            <div class="form-group">
                                <label for="relasi_area_id">Area <span class="text-info">(opsional)</span></label>
                                <select class="tom-select-class" id="relasi_area_id" name="relasi_area_id">
                                    <option value="" selected disabled>- pilih area spesifik -</option>
                                    <option value="{{ null }}">Tidak ada area</option>
                                    @if ($monitoring_permit->relasi_area_id != null)
                                        @foreach ($area as $item)
                                            <option value="{{ $item->id }}"
                                                @if ($item->id == $monitoring_permit->relasi_area->id) selected @endif>{{ $item->lokasi->name }}
                                                -
                                                {{ $item->sub_lokasi->name }} - {{ $item->detail_lokasi->name }}</option>
                                        @endforeach
                                    @else
                                        @foreach ($area as $item)
                                            <option value="{{ $item->id }}">{{ $item->lokasi->name }} -
                                                {{ $item->sub_lokasi->name }} - {{ $item->detail_lokasi->name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <div class="form-group d-flex justify-content-end">
                                <a href="{{ route('monitoring-permit.index') }}" type="button"
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
