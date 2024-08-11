@extends('layout.base')

@section('title-head')
    <title>Admin | Edit Problem</title>
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Edit Data Problem</h4>
                        <form id="editForm" action="{{ route('problem.update') }}" method="POST">
                            @csrf
                            @method('PUT')
                            <input type="text" name="id" value="{{ $problem->id }}" hidden>
                            <div class="form-group">
                                <label for="category_id">Category</label>
                                <select class="form-control form-control-lg" name="category_id" id="category_id" required>
                                    <option value="">- pilih category -</option>
                                    @foreach ($category as $item)
                                        <option value="{{ $item->id }}"
                                            @if ($item->id == $problem->category_id) selected @endif>
                                            {{ $item->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="tipe_equipment_id">Tipe Equipment</label>
                                <select class="form-control form-control-lg" name="tipe_equipment_id" id="tipe_equipment_id"
                                    required>
                                    <option value="">- pilih tipe equipment -</option>
                                    @foreach ($tipe_equipment as $item)
                                        <option value="{{ $item->id }}"
                                            @if ($item->id == $problem->tipe_equipment_id) selected @endif>
                                            {{ $item->name }} ({{ $item->code }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="name">Name</label>
                                <input type="text" class="form-control" id="name" name="name" placeholder="Name"
                                    autocomplete="off" required value="{{ $problem->name }}">
                            </div>
                            <div class="form-group">
                                <label for="code">Code</label>
                                <input type="text" class="form-control" id="code" name="code" placeholder="Code"
                                    autocomplete="off" required value="{{ $problem->code }}">
                            </div>
                            <div class="form-group d-flex justify-content-end">
                                <a href="{{ route('problem.index') }}" type="button" class="btn btn-secondary">Cancel</a>
                                <button type="submit" form="editForm" class="btn btn-primary">Update</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
