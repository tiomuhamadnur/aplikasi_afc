@extends('layout.base')

@section('title-head')
    <title>Admin | Edit Approval</title>
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Edit Data Approval</h4>
                        <form id="editForm" action="{{ route('approval.update') }}" method="POST">
                            @csrf
                            @method('PUT')
                            <input type="text" name="id" value="{{ $approval->id }}" hidden>
                            <div class="form-group">
                                <label for="relasi_struktur_id">Relasi Struktur</label>
                                <select class="form-control form-control-lg" name="relasi_struktur_id"
                                    id="relasi_struktur_id" required>
                                    <option value="" selected disabled>- pilih relasi struktur -</option>
                                    @foreach ($relasi_struktur as $item)
                                        <option value="{{ $item->id }}"
                                            @if ($item->id == $approval->relasi_struktur_id) selected @endif>
                                            {{ $item->departemen->name ?? '#' }} -
                                            {{ $item->seksi->name ?? '#' }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="jabatan_id">Jabatan</label>
                                <select class="form-control form-control-lg" name="jabatan_id" id="jabatan_id" required>
                                    <option value="" selected disabled>- pilih jabatan -</option>
                                    @foreach ($jabatan as $item)
                                        <option value="{{ $item->id }}"
                                            @if ($item->id == $approval->jabatan_id) selected @endif>
                                            {{ $item->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="tipe_employee_id">Tipe Employee</label>
                                <select class="form-control form-control-lg" name="tipe_employee_id" id="tipe_employee_id"
                                    required>
                                    <option value="" selected disabled>- pilih tipe employee -</option>
                                    @foreach ($tipe_employee as $item)
                                        <option value="{{ $item->id }}"
                                            @if ($item->id == $approval->tipe_employee_id) selected @endif>
                                            {{ $item->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="priority">Priority</label>
                                <input type="number" min="1" class="form-control" name="priority" id="priority"
                                    autocomplete="off" placeholder="input priority" value="{{ $approval->priority }}"
                                    required>
                            </div>
                            <div class="form-group">
                                <label for="name">Name</label>
                                <input type="text" class="form-control" name="name" id="name" autocomplete="off"
                                    placeholder="input label name" value="{{ $approval->name }}" required>
                            </div>
                            <div class="form-group d-flex justify-content-end">
                                <a href="{{ route('approval.index') }}" type="button" class="btn btn-secondary">Cancel</a>
                                <button type="submit" form="editForm" class="btn btn-primary">Update</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
