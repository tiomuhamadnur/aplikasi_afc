@extends('layout.base')

@section('title-head')
    <title>Create Data Work Order</title>
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Create Data Work Order</h4>
                        <form id="addForm" action="{{ route('work-order.store') }}" class="forms-sample mt-4" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            @method('POST')
                            <div class="form-group">
                                <label for="tipe_pekerjaan_id">Tipe Pekerjaan</label>
                                <select class="form-control form-control-lg" name="tipe_pekerjaan_id" id="tipe_pekerjaan_id"
                                    required>
                                    <option value="">- pilih tipe pekerjaan -</option>
                                    @foreach ($tipe_pekerjaan as $item)
                                        <option value="{{ $item->id }}">{{ $item->code }} ({{ $item->name }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="wo_number_sap">WO Number SAP <span class="text-info">(optional)</span></label>
                                <input type="text" class="form-control" id="wo_number_sap" name="wo_number_sap"
                                    placeholder="Input WO number from SAP" autocomplete="off">
                            </div>
                            <div class="form-group">
                                <label for="name">Name</label>
                                <input type="text" class="form-control" id="name" name="name"
                                    placeholder="Input name" autocomplete="off" required>
                            </div>
                            <div class="form-group">
                                <label for="description">Description</label>
                                <input type="text" class="form-control" id="description" name="description"
                                    placeholder="Input description" autocomplete="off" required>
                            </div>
                            <div class="form-group">
                                <label for="date">Date</label>
                                <input type="date" class="form-control" id="date" name="date"
                                    placeholder="Input date" autocomplete="off" required>
                            </div>
                            @livewire('form-wo-equipment')
                            <div class="form-group">
                                <label for="classification_id">Priority</label>
                                <select class="form-control form-control-lg" name="classification_id" id="classification_id"
                                    required>
                                    <option value="">- pilih priority -</option>
                                    @foreach ($classification as $item)
                                        <option value="{{ $item->id }}">{{ $item->name ?? '-' }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group d-flex justify-content-end">
                                <a href="{{ route('work-order.index') }}" type="button"
                                    class="btn btn-secondary">Cancel</a>
                                <button type="submit" form="addForm" class="btn btn-primary">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @livewireScripts
@endsection

@section('javascript')
    <script></script>
@endsection
