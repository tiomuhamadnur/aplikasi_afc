@extends('layout.base')

@section('title-head')
    <title>LCU Checklist</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css" rel="stylesheet">
    <style>
        .input-group {
            display: flex;
            width: 100%;
        }

        .input-group select {
            flex: 1 1 80%;
        }

        .input-group input {
            flex: 1 1 20%;
        }
    </style>
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Data LCU Checklist</h4>
                        <button type="button" title="Make Request" data-bs-toggle="modal" data-bs-target="#addModal"
                            class="btn btn-gradient-primary btn-rounded">
                            Create Record
                        </button>
                        <div class="btn-group my-2">
                            <button type="button" title="Filter" data-bs-toggle="modal" data-bs-target="#filterModal"
                                class="btn btn-outline-primary btn-rounded btn-icon">
                                <i class="mdi mdi-filter"></i>
                            </button>
                            <button type="button" title="Export Excel" data-bs-toggle="modal"
                                data-bs-target="#exportExcelModal" class="btn btn-outline-primary btn-rounded btn-icon">
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

    <!-- Add Modal -->
    <div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Form Add</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addForm" action="{{ route('lcu-checklist.store') }}" method="POST" class="forms-sample"
                        enctype="multipart/form-data">
                        @csrf
                        @method('POST')
                        <div class="form-group">
                            <label for="user_id" class="required">Checked by</label>
                            <input type="text" class="form-control" autocomplete="off" placeholder="input checked by"
                                value="{{ auth()->user()->name }}" disabled>
                        </div>
                        <div class="form-group">
                            <label for="date" class="required">Date</label>
                            <input type="date" class="form-control" autocomplete="off" value="{{ $today }}"
                                disabled>
                        </div>
                        <div class="form-group">
                            <label for="functional_location_id" class="required">Location</label>
                            <select class="tom-select-class" id="functional_location_id" name="functional_location_id"
                                required>
                                <option value="" selected disabled>- select location -</option>
                                @foreach ($functional_location as $item)
                                    <option value="{{ $item->id }}">{{ $item->name }} -
                                        ({{ $item->code ?? '-' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="mks_status" class="required">MKS Status?</label>
                            <select class="tom-select-class" id="mks_status" name="mks_status" required>
                                <option value="" selected disabled>- select option -</option>
                                <option value="1">OK</option>
                                <option value="0">NOT OK</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="lighting_status" class="required">Lighting Status?</label>
                            <select class="tom-select-class" id="lighting_status" name="lighting_status" required>
                                <option value="" selected disabled>- select option -</option>
                                <option value="1">OK</option>
                                <option value="0">NOT OK</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="cctv_status" class="required">CCTV Status?</label>
                            <select class="tom-select-class" id="cctv_status" name="cctv_status" required>
                                <option value="" selected disabled>- select option -</option>
                                <option value="1">OK</option>
                                <option value="0">NOT OK</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="ac_status" class="required">AC Status?</label>
                            <select class="tom-select-class" id="ac_status" name="ac_status" required>
                                <option value="" selected disabled>- select option -</option>
                                <option value="1">OK</option>
                                <option value="0">NOT OK</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="room_cleanliness" class="required">Kebersihan ruangan?</label>
                            <select class="tom-select-class" id="room_cleanliness" name="room_cleanliness" required>
                                <option value="" selected disabled>- select option -</option>
                                <option value="1">OK</option>
                                <option value="0">NOT OK</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="server_status" class="required">Server Status?</label>
                            <select class="tom-select-class" id="server_status" name="server_status" required>
                                <option value="" selected disabled>- select option -</option>
                                <option value="1">OK</option>
                                <option value="0">NOT OK</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="server_alert" class="required">Server Alert?</label>
                            <select class="tom-select-class" id="server_alert" name="server_alert" required>
                                <option value="" selected disabled>- select option -</option>
                                <option value="1">OK</option>
                                <option value="0">NOT OK</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="switch_status" class="required">Switch Status?</label>
                            <select class="tom-select-class" id="switch_status" name="switch_status" required>
                                <option value="" selected disabled>- select option -</option>
                                <option value="1">OK</option>
                                <option value="0">NOT OK</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="switch_alert" class="required">Switch Alert?</label>
                            <select class="tom-select-class" id="switch_alert" name="switch_alert" required>
                                <option value="" selected disabled>- select option -</option>
                                <option value="1">OK</option>
                                <option value="0">NOT OK</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="ups_status" class="required">UPS Status?</label>
                            <select class="tom-select-class" id="ups_status" name="ups_status" required>
                                <option value="" selected disabled>- select option -</option>
                                <option value="1">OK</option>
                                <option value="0">NOT OK</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="ups_alert" class="required">UPS Alert?</label>
                            <select class="tom-select-class" id="ups_alert" name="ups_alert" required>
                                <option value="" selected disabled>- select option -</option>
                                <option value="1">OK</option>
                                <option value="0">NOT OK</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="cable_status" class="required">Cable Status?</label>
                            <select class="tom-select-class" id="cable_status" name="cable_status" required>
                                <option value="" selected disabled>- select option -</option>
                                <option value="1">OK</option>
                                <option value="0">NOT OK</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="room_temperature" class="required">Temperatur Ruangan? (°C)</label>
                            <input type="number" class="form-control" step="0.01" id="room_temperature"
                                name="room_temperature" autocomplete="off" placeholder="input temperatur ruangan"
                                min="1" required>
                        </div>
                        <div class="form-group">
                            <label for="room_temp_photo" class="required">Photo Temperatur Ruangan</label>
                            <div class="text-left my-1">
                                <img class="img-thumbnail" id="previewImageRoom" src="#"
                                    alt="Tidak ada photo temperature room"
                                    style="max-width: 250px; max-height: 250px; display: none;">
                            </div>
                            <input type="file" class="form-control" id="room_temp_photo" name="room_temp_photo"
                                accept="image/*" required>
                        </div>
                        <div class="form-group">
                            <label for="rack_temperature" class="required">Temperatur Rack? (°C)</label>
                            <input type="number" class="form-control" step="0.01" id="rack_temperature"
                                name="rack_temperature" autocomplete="off" placeholder="input temperatur rack"
                                min="1" required>
                        </div>
                        <div class="form-group">
                            <label for="rack_temp_photo" class="required">Photo Temperatur Rack</label>
                            <div class="text-left my-1">
                                <img class="img-thumbnail" id="previewImageRack" src="#"
                                    alt="Tidak ada photo temperature room"
                                    style="max-width: 250px; max-height: 250px; display: none;">
                            </div>
                            <input type="file" class="form-control" id="rack_temp_photo" name="rack_temp_photo"
                                accept="image/*" required>
                        </div>
                        <div class="form-group">
                            <label for="remark">Remarks <span class="text-info">(optional)</span></label>
                            <textarea class="form-control" name="remark" id="remark" rows="4" placeholder="input jika ada catatan"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" form="addForm" class="btn btn-gradient-primary me-2">Submit</button>
                </div>
            </div>
        </div>
    </div>
    <!-- End Add Modal -->

    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Form Edit</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="updateForm" action="{{ route('lcu-checklist.update') }}" method="POST" class="forms-sample"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <input type="number" id="id_edit" name="id" hidden>
                        <div class="form-group">
                            <label for="user_id" class="required">Checked by</label>
                            <select class="form-control form-control-lg" name="user_id" id="user_id_edit">
                                <option value="">- select option -</option>
                                @foreach ($user as $item)
                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="date" class="required">Date</label>
                            <input type="datetime-local" class="form-control" autocomplete="off" id="date_edit" name="date">
                        </div>
                        <div class="form-group">
                            <label for="functional_location_id_edit" class="required">Location</label>
                            <select class="form-control form-control-lg" id="functional_location_id_edit" name="functional_location_id"
                                required>
                                <option value="" selected disabled>- select location -</option>
                                @foreach ($functional_location as $item)
                                    <option value="{{ $item->id }}">{{ $item->name }} -
                                        ({{ $item->code ?? '-' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="mks_status_edit" class="required">MKS Status?</label>
                            <select class="form-control form-control-lg" id="mks_status_edit" name="mks_status" required>
                                <option value="" selected disabled>- select option -</option>
                                <option value="1">OK</option>
                                <option value="0">NOT OK</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="lighting_status_edit" class="required">Lighting Status?</label>
                            <select class="form-control form-control-lg" id="lighting_status_edit" name="lighting_status" required>
                                <option value="" selected disabled>- select option -</option>
                                <option value="1">OK</option>
                                <option value="0">NOT OK</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="cctv_status_edit" class="required">CCTV Status?</label>
                            <select class="form-control form-control-lg" id="cctv_status_edit" name="cctv_status" required>
                                <option value="" selected disabled>- select option -</option>
                                <option value="1">OK</option>
                                <option value="0">NOT OK</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="ac_status_edit" class="required">AC Status?</label>
                            <select class="form-control form-control-lg" id="ac_status_edit" name="ac_status" required>
                                <option value="" selected disabled>- select option -</option>
                                <option value="1">OK</option>
                                <option value="0">NOT OK</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="room_cleanliness_edit" class="required">Kebersihan ruangan?</label>
                            <select class="form-control form-control-lg" id="room_cleanliness_edit" name="room_cleanliness" required>
                                <option value="" selected disabled>- select option -</option>
                                <option value="1">OK</option>
                                <option value="0">NOT OK</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="server_status_edit" class="required">Server Status?</label>
                            <select class="form-control form-control-lg" id="server_status_edit" name="server_status" required>
                                <option value="" selected disabled>- select option -</option>
                                <option value="1">OK</option>
                                <option value="0">NOT OK</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="server_alert_edit" class="required">Server Alert?</label>
                            <select class="form-control form-control-lg" id="server_alert_edit" name="server_alert" required>
                                <option value="" selected disabled>- select option -</option>
                                <option value="1">OK</option>
                                <option value="0">NOT OK</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="switch_status_edit" class="required">Switch Status?</label>
                            <select class="form-control form-control-lg" id="switch_status_edit" name="switch_status" required>
                                <option value="" selected disabled>- select option -</option>
                                <option value="1">OK</option>
                                <option value="0">NOT OK</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="switch_alert_edit" class="required">Switch Alert?</label>
                            <select class="form-control form-control-lg" id="switch_alert_edit" name="switch_alert" required>
                                <option value="" selected disabled>- select option -</option>
                                <option value="1">OK</option>
                                <option value="0">NOT OK</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="ups_status_edit" class="required">UPS Status?</label>
                            <select class="form-control form-control-lg" id="ups_status_edit" name="ups_status" required>
                                <option value="" selected disabled>- select option -</option>
                                <option value="1">OK</option>
                                <option value="0">NOT OK</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="ups_alert_edit" class="required">UPS Alert?</label>
                            <select class="form-control form-control-lg" id="ups_alert_edit" name="ups_alert" required>
                                <option value="" selected disabled>- select option -</option>
                                <option value="1">OK</option>
                                <option value="0">NOT OK</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="cable_status_edit" class="required">Cable Status?</label>
                            <select class="form-control form-control-lg" id="cable_status_edit" name="cable_status" required>
                                <option value="" selected disabled>- select option -</option>
                                <option value="1">OK</option>
                                <option value="0">NOT OK</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="room_temperature_edit" class="required">Temperatur Ruangan? (°C)</label>
                            <input type="number" class="form-control form-control-lg" step="0.01" id="room_temperature_edit"
                                name="room_temperature" autocomplete="off" placeholder="input temperatur ruangan"
                                min="1" required>
                        </div>
                        <div class="form-group">
                            <label for="room_temp_photo_edit" class="required">Photo Temperatur Ruangan</label>
                            <div class="text-left my-1">
                                <img class="img-thumbnail" id="previewImageRoom_edit" src="#"
                                    alt="Tidak ada photo temperature room"
                                    style="max-width: 250px; max-height: 250px;">
                            </div>
                            <input type="file" class="form-control" id="room_temp_photo_edit" name="room_temp_photo"
                                accept="image/*">
                        </div>
                        <div class="form-group">
                            <label for="rack_temperature_edit" class="required">Temperatur Rack? (°C)</label>
                            <input type="number" class="form-control form-control-lg" step="0.01" id="rack_temperature_edit"
                                name="rack_temperature" autocomplete="off" placeholder="input temperatur rack"
                                min="1" required>
                        </div>
                        <div class="form-group">
                            <label for="rack_temp_photo_edit" class="required">Photo Temperatur Rack</label>
                            <div class="text-left my-1">
                                <img class="img-thumbnail" id="previewImageRack_edit" src="#"
                                    alt="Tidak ada photo temperature room"
                                    style="max-width: 250px; max-height: 250px;">
                            </div>
                            <input type="file" class="form-control" id="rack_temp_photo_edit" name="rack_temp_photo"
                                accept="image/*">
                        </div>
                        <div class="form-group">
                            <label for="remark">Remarks <span class="text-info">(optional)</span></label>
                            <textarea class="form-control" name="remark" id="remark_edit" rows="4" placeholder="input jika ada catatan"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" form="updateForm" class="btn btn-gradient-primary me-2">Submit</button>
                </div>
            </div>
        </div>
    </div>
    <!-- End Edit Modal -->

    <!-- Add Filter -->
    <div class="modal fade" id="filterModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Form Filter</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="filterForm" action="{{ route('lcu-checklist.index') }}" method="GET" class="forms-sample">
                        @csrf
                        @method('GET')
                        <div class="form-group">
                            <label for="" class="required">Tanggal</label>
                            <div class="input-group">
                                <input type="text" id="start_date" onfocus="(this.type='date')"
                                    onblur="(this.type='text')" class="form-control" placeholder="Start Date"
                                    name="start_date" autocomplete="off" value="{{ $start_date ?? null }}" required>
                                <input type="text" id="end_date" onfocus="(this.type='date')"
                                    onblur="(this.type='text')" class="form-control" placeholder="End Date"
                                    name="end_date" autocomplete="off" value="{{ $end_date ?? null }}" required>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <a href="{{ route('lcu-checklist.index') }}" class="btn btn-gradient-warning">Reset</a>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" form="filterForm" class="btn btn-gradient-primary">Filter</button>
                </div>
            </div>
        </div>
    </div>
    <!-- End Add Filter -->

    <!-- Photo Modal -->
    <div class="modal fade" id="photoModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Detail</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="container">
                        <div class="mb-4 text-center align-middle">
                            <h5>Photo Temperature Room</h5>
                            <div class="border mx-auto">
                                <img src="#" id="photo_room_modal" class="img-thumbnail" alt="Tidak ada photo">
                            </div>
                        </div>
                    </div>
                    <div class="container">
                        <div class="mb-4 text-center align-middle">
                            <h5>Photo Temperature Rack</h5>
                            <div class="border mx-auto">
                                <img src="#" id="photo_rack_modal" class="img-thumbnail" alt="Tidak ada photo">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <!-- End Photo Modal -->

    <!-- Delete Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Are you sure?</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="deleteForm" action="{{ route('lcu-checklist.delete') }}" method="POST"
                        class="forms-sample">
                        @csrf
                        @method('delete')
                        <input type="hidden" name="id" id="id_delete">
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" form="deleteForm" class="btn btn-gradient-danger me-2">Delete</button>
                </div>
            </div>
        </div>
    </div>
    <!-- End Delete Modal -->

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
            const imageInputRoom = document.getElementById('room_temp_photo');
            const previewImageRoom = document.getElementById('previewImageRoom');

            imageInputRoom.addEventListener('change', function(event) {
                const selectedFile = event.target.files[0];

                if (selectedFile) {
                    const reader = new FileReader();

                    reader.onload = function(e) {
                        previewImageRoom.src = e.target.result;
                        previewImageRoom.style.display = 'block';
                    }

                    reader.readAsDataURL(selectedFile);
                }
            });

            const imageInputRack = document.getElementById('rack_temp_photo');
            const previewImageRack = document.getElementById('previewImageRack');

            imageInputRack.addEventListener('change', function(event) {
                const selectedFile = event.target.files[0];

                if (selectedFile) {
                    const reader = new FileReader();

                    reader.onload = function(e) {
                        previewImageRack.src = e.target.result;
                        previewImageRack.style.display = 'block';
                    }

                    reader.readAsDataURL(selectedFile);
                }
            });

            $('#photoModal').on('show.bs.modal', function(e) {
                var photo_room = $(e.relatedTarget).data('photo_room');
                var photo_rack = $(e.relatedTarget).data('photo_rack');

                document.getElementById("photo_room_modal").src = photo_room;
                document.getElementById("photo_rack_modal").src = photo_rack;
            });

            $('#editModal').on('show.bs.modal', function(e) {
                var id = $(e.relatedTarget).data('id');
                var date = $(e.relatedTarget).data('date');
                var user_id = $(e.relatedTarget).data('user_id');
                var functional_location_id = $(e.relatedTarget).data('functional_location_id');
                var mks_status = $(e.relatedTarget).data('mks_status');
                var lighting_status = $(e.relatedTarget).data('lighting_status');
                var cctv_status = $(e.relatedTarget).data('cctv_status');
                var ac_status = $(e.relatedTarget).data('ac_status');
                var room_cleanliness = $(e.relatedTarget).data('room_cleanliness');
                var server_status = $(e.relatedTarget).data('server_status');
                var server_alert = $(e.relatedTarget).data('server_alert');
                var switch_status = $(e.relatedTarget).data('switch_status');
                var switch_alert = $(e.relatedTarget).data('switch_alert');
                var ups_status = $(e.relatedTarget).data('ups_status');
                var ups_alert = $(e.relatedTarget).data('ups_alert');
                var cable_status = $(e.relatedTarget).data('cable_status');
                var room_temperature = $(e.relatedTarget).data('room_temperature');
                var rack_temperature = $(e.relatedTarget).data('rack_temperature');
                var room_temp_photo = $(e.relatedTarget).data('room_temp_photo');
                var rack_temp_photo = $(e.relatedTarget).data('rack_temp_photo');
                var remark = $(e.relatedTarget).data('remark');

                $('#id_edit').val(id);
                $('#date_edit').val(date);
                $('#user_id_edit').val(user_id);
                $('#functional_location_id_edit').val(functional_location_id);
                $('#mks_status_edit').val(mks_status);
                $('#lighting_status_edit').val(lighting_status);
                $('#cctv_status_edit').val(cctv_status);
                $('#ac_status_edit').val(ac_status);
                $('#room_cleanliness_edit').val(room_cleanliness);
                $('#server_status_edit').val(server_status);
                $('#server_alert_edit').val(server_alert);
                $('#switch_status_edit').val(switch_status);
                $('#switch_alert_edit').val(switch_alert);
                $('#ups_status_edit').val(ups_status);
                $('#ups_alert_edit').val(ups_alert);
                $('#cable_status_edit').val(cable_status);
                $('#room_temperature_edit').val(room_temperature);
                $('#rack_temperature_edit').val(rack_temperature);
                $('#remark_edit').val(remark);
                document.getElementById("previewImageRoom_edit").src = room_temp_photo
                document.getElementById("previewImageRack_edit").src = rack_temp_photo
            });

            const imageInputRoom_edit = document.getElementById('room_temp_photo_edit');
            const previewImageRoom_edit = document.getElementById('previewImageRoom_edit');

            imageInputRoom_edit.addEventListener('change', function(event) {
                const selectedFile = event.target.files[0];

                if (selectedFile) {
                    const reader = new FileReader();

                    reader.onload = function(e) {
                        previewImageRoom_edit.src = e.target.result;
                        previewImageRoom_edit.style.display = 'block';
                    }

                    reader.readAsDataURL(selectedFile);
                }
            });

            const imageInputRack_edit = document.getElementById('rack_temp_photo_edit');
            const previewImageRack_edit = document.getElementById('previewImageRack_edit');

            imageInputRack_edit.addEventListener('change', function(event) {
                const selectedFile = event.target.files[0];

                if (selectedFile) {
                    const reader = new FileReader();

                    reader.onload = function(e) {
                        previewImageRack_edit.src = e.target.result;
                        previewImageRack_edit.style.display = 'block';
                    }

                    reader.readAsDataURL(selectedFile);
                }
            });

            $('#deleteModal').on('show.bs.modal', function(e) {
                var id = $(e.relatedTarget).data('id');

                $('#id_delete').val(id);
            });
        });
    </script>

    <script>
        function exportExcel() {
            document.getElementById('datatable-excel').click();
        }
    </script>
@endsection
