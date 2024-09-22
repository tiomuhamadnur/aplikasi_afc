@extends('layout.base')

@section('title-head')
    <title>Admin | Edit PCR</title>
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Edit Data PCR</h4>
                        <form id="editForm" action="{{ route('pcr.update') }}" method="POST">
                            @csrf
                            @method('PUT')
                            <input type="text" name="id" value="{{ $pcr->id }}" hidden>
                            <div class="form-group">
                                <label for="tipe_equipment_id">Tipe Equipment</label>
                                <select class="tom-select-class" name="tipe_equipment_id" id="tipe_equipment_id" required>
                                    <option value="" selected disabled>- pilih tipe equipment -</option>
                                    @foreach ($tipe_equipment as $item)
                                        <option value="{{ $item->id }}"
                                            @if ($item->id == $pcr->tipe_equipment_id) selected @endif>
                                            {{ $item->name }} ({{ $item->code }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="category_id">Category</label>
                                <select class="tom-select-class" name="category_id" id="category_id" required>
                                    <option value="" selected disabled>- pilih category -</option>
                                    @foreach ($category as $item)
                                        <option value="{{ $item->id }}"
                                            @if ($item->id == $pcr->category_id) selected @endif>
                                            {{ $item->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="problem_id">Problem (P)</label>
                                <select class="tom-select-class" name="problem_id" id="problem_id" required>
                                    <option value="" selected disabled>- pilih problem -</option>
                                    @foreach ($problem as $item)
                                        <option value="{{ $item->id }}"
                                            @if ($item->id == $pcr->problem_id) selected @endif>
                                            {{ $item->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="cause_id">Cause (C)</label>
                                <select class="tom-select-class" name="cause_id" id="cause_id" required>
                                    <option value="" selected disabled>- pilih cause -</option>
                                    @foreach ($cause as $item)
                                        <option value="{{ $item->id }}"
                                            @if ($item->id == $pcr->cause_id) selected @endif>
                                            {{ $item->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="remedy_id">Remedy (R)</label>
                                <select class="tom-select-class" name="remedy_id" id="remedy_id" required>
                                    <option value="" selected disabled>- pilih remedy -</option>
                                    @foreach ($remedy as $item)
                                        <option value="{{ $item->id }}"
                                            @if ($item->id == $pcr->remedy_id) selected @endif>
                                            {{ $item->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="classification_id">Classification</label>
                                <select class="tom-select-class" name="classification_id" id="classification_id" required>
                                    <option value="" selected disabled>- pilih classification -</option>
                                    @foreach ($classification as $item)
                                        <option value="{{ $item->id }}"
                                            @if ($item->id == $pcr->classification_id) selected @endif>
                                            {{ $item->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group d-flex justify-content-end">
                                <a href="{{ route('pcr.index') }}" type="button" class="btn btn-secondary">Cancel</a>
                                <button type="submit" form="editForm" class="btn btn-primary">Update</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
