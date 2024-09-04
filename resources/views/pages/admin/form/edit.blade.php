@extends('layout.base')

@section('title-head')
    <title>Admin | Edit Form</title>
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Edit Data Form</h4>
                        <form id="editForm" action="{{ route('form.update') }}" method="POST">
                            @csrf
                            @method('PUT')
                            <input type="text" name="id" value="{{ $form->id }}" hidden>
                            <div class="form-group">
                                <label for="name">Name</label>
                                <input type="text" class="form-control" id="name" name="name" placeholder="Name"
                                    autocomplete="off" required value="{{ $form->name }}">
                            </div>
                            <div class="form-group">
                                <label for="code">Code</label>
                                <input type="text" class="form-control" id="code" name="code" placeholder="Code"
                                    autocomplete="off" required value="{{ $form->code }}">
                            </div>
                            <div class="form-group">
                                <label for="tipe_equipment_id">Tipe Equipment</label>
                                <select class="tom-select-class" name="tipe_equipment_id" id="tipe_equipment_id" required>
                                    <option value="" selected disabled>- pilih tipe equipment -</option>
                                    @foreach ($tipe_equipment as $item)
                                        <option value="{{ $item->id }}"
                                            @if ($item->id == $form->tipe_equipment_id) selected @endif>
                                            {{ $item->code }} ({{ $item->name }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="description">Description <span class="text-info">(optional)</span></label>
                                <input type="text" class="form-control" id="description" name="description"
                                    placeholder="Description" autocomplete="off" value="{{ $form->description }}">
                            </div>
                            <div class="form-group">
                                <label for="status">Status</label>
                                <select class="tom-select-class" name="status" id="status" required>
                                    <option value="" selected disabled>- pilih status -</option>
                                    <option value="active" @if ($form->status == 'active') selected @endif>
                                        Active
                                    </option>
                                    <option value="inactive" @if ($form->status == 'inactive') selected @endif>
                                        Inactive
                                    </option>
                                </select>
                            </div>
                            <div class="form-group d-flex justify-content-end">
                                <a href="{{ route('form.index') }}" type="button" class="btn btn-secondary">Cancel</a>
                                <button type="submit" form="editForm" class="btn btn-primary">Update</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
