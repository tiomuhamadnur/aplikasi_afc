@extends('layout.base')

@section('title-head')
    <title>Admin | Config Equipment AFC</title>
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Data Config Equipment AFC</h4>
                        <div class="btn-group my-2">
                            {{-- <button type="button" title="Add" class="btn btn-outline-primary btn-rounded btn-icon"
                                data-bs-toggle="modal" data-bs-target="#addModal">
                                <i class="mdi mdi-plus-circle"></i>
                            </button> --}}
                            <button type="button" title="Filter" data-bs-toggle="modal" data-bs-target="#filterModal"
                                class="btn btn-outline-primary btn-rounded btn-icon">
                                <i class="mdi mdi-filter"></i>
                            </button>
                            <button type="button" title="Export" data-bs-toggle="modal" data-bs-target="#exportExcelModal"
                                class="btn btn-outline-primary btn-rounded btn-icon">
                                <i class="mdi mdi-file-export"></i>
                            </button>
                        </div>
                        <div class="table-responsive">
                            {{ $dataTable->table() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Modal -->
    <div class="modal fade" id="filterModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Form Filter</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="filterForm" action="{{ route('config-equipment-afc.index') }}" method="GET"
                        class="forms-sample">
                        @csrf
                        @method('GET')
                        <div class="form-group">
                            <label for="station_code">Station</label>
                            <select class="tom-select-class" id="station_code" name="station_code">
                                <option value="" selected disabled>- select station -</option>
                                @foreach ($station_codes as $item)
                                    <option value="{{ $item }}" @selected($item == $station_code)>
                                        {{ $item }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="equipment_type_code">Equipment Type</label>
                            <select class="tom-select-class" id="equipment_type_code" name="equipment_type_code">
                                <option value="" selected disabled>- select equipment type -</option>
                                @foreach ($equipment_type_codes as $item)
                                    <option value="{{ $item }}" @selected($item == $equipment_type_code)>
                                        {{ $item }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="corner_id">Corner</label>
                            <select class="tom-select-class" id="corner_id" name="corner_id">
                                <option value="" selected disabled>- select corner -</option>
                                @foreach ($corner_ids as $item)
                                    <option value="{{ $item }}" @selected($item == $corner_id)>
                                        {{ $item }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="direction">Direction</label>
                            <select class="tom-select-class" id="direction" name="direction">
                                <option value="" selected disabled>- select direction -</option>
                                @foreach ($directions as $item)
                                    <option value="{{ $item }}" @selected($item == $direction)>
                                        {{ $item }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <a href="{{ route('config-equipment-afc.index') }}" class="btn btn-gradient-warning">Reset</a>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" form="filterForm" class="btn btn-gradient-primary me-2">Filter</button>
                </div>
            </div>
        </div>
    </div>
    <!-- End Filter Modal -->

    <!-- PG Control Modal -->
    <div class="modal fade" id="controlPGModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Form Control PG</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="controlPGForm" action="{{ route('config-equipment-afc.control-pg') }}" method="POST"
                        class="forms-sample">
                        @csrf
                        @method('POST')
                        <input type="hidden" name="uuid" id="uuid_edit">
                        <div class="form-group">
                            <label for="station_code">Station</label>
                            <input type="text" class="form-control" id="station_code_edit" name="station_code"
                                disabled>
                        </div>
                        <div class="form-group">
                            <label for="equipment_type_code">Equipment Type</label>
                            <input type="text" class="form-control" id="equipment_type_code_edit"
                                name="equipment_type_code" disabled>
                        </div>
                        <div class="form-group">
                            <label for="equipment_name">Equipment Name</label>
                            <input type="text" class="form-control" id="equipment_name_edit" name="equipment_name"
                                disabled>
                        </div>
                        <div class="form-group">
                            <label for="equipment_id">Equipment ID</label>
                            <input type="text" class="form-control" id="equipment_id_edit" name="equipment_id"
                                disabled>
                        </div>
                        <div class="form-group">
                            <label for="corner_id">Corner</label>
                            <input type="text" class="form-control" id="corner_id_edit" name="corner_id" disabled>
                        </div>
                        <div class="form-group">
                            <label for="direction">Direction</label>
                            <input type="text" class="form-control" id="direction_edit" name="direction" disabled>
                        </div>
                        <div class="form-group">
                            <label for="control_type">Power (ON/OFF/Reboot)</label>
                            <select class="tom-select-class" name="control_type" id="control_type">
                                <option value="" disabled selected>- select power option -</option>
                                <option value="on">Power ON</option>
                                <option value="off">Power OFF</option>
                                <option value="reboot">Reboot</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" form="controlPGForm" class="btn btn-gradient-primary me-2">Execute</button>
                </div>
            </div>
        </div>
    </div>
    <!-- End PG Control Modal -->

    <!-- Export Excel Modal -->
    <div class="modal fade" id="exportExcelModal" tabindex="-1" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Are you sure?</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center">
                        <img src="https://i.pinimg.com/originals/1b/db/8a/1bdb8ac897512116cbac58ffe7560d82.png"
                            alt="Excel" style="height: 150px; width: 150px">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" id="exportButton" onclick="exportExcel()"
                        class="btn btn-gradient-success me-2">Download</button>
                </div>
            </div>
        </div>
    </div>
    <!-- End Export Excel Modal -->
@endsection

@push('scripts')
    {{ $dataTable->scripts() }}
@endpush

@section('javascript')
    <script>
        $(document).ready(function() {
            $('#controlPGModal').on('show.bs.modal', function(e) {
                var id = $(e.relatedTarget).data('id');
                var uuid = $(e.relatedTarget).data('uuid');
                var station_code = $(e.relatedTarget).data('station_code');
                var equipment_type_code = $(e.relatedTarget).data('equipment_type_code');
                var equipment_name = $(e.relatedTarget).data('equipment_name');
                var equipment_id = $(e.relatedTarget).data('equipment_id');
                var corner_id = $(e.relatedTarget).data('corner_id');
                var direction = $(e.relatedTarget).data('direction');

                $('#uuid_edit').val(uuid);
                $('#station_code_edit').val(station_code);
                $('#equipment_type_code_edit').val(equipment_type_code);
                $('#equipment_name_edit').val(equipment_name);
                $('#equipment_id_edit').val(equipment_id);
                $('#corner_id_edit').val(corner_id);
                $('#direction_edit').val(direction);
            });
        });
    </script>
    <script>
        function exportExcel() {
            $('#datatable-excel').click();
        }
    </script>
@endsection
