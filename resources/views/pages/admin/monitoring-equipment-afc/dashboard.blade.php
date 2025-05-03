@extends('layout.base')

@section('title-head')
    <title>Dashboard Equipment AFC</title>
    <style>
        .svg-container svg {
            width: 100%;
            height: auto;
            max-width: 100%;
        }

        /* Status classes */
        .svg-container svg .online {
            fill: #00E600 !important;
        }

        .svg-container svg .standby {
            fill: #8e8e8e !important;
        }

        .svg-container svg .offline {
            fill: #ff4040 !important;
            animation: blinkOffline 1s infinite;
            /* Start as red */
        }

        /* Blink animation using FILTER (safe for SVG fill) */
        @keyframes blinkOffline {

            0%,
            100% {
                filter: brightness(1);
            }

            50% {
                filter: brightness(3.5);
            }
        }

        /* Tooltip styling (optional, biar rapi) */
        #equipment-tooltip {
            position: absolute;
            display: none;
            background: rgba(51, 51, 51, 0.95);
            color: #fff;
            padding: 6px 10px;
            border-radius: 6px;
            font-size: 13px;
            pointer-events: none;
            z-index: 9999;
            max-width: 250px;
            white-space: normal;
            word-break: break-word;
        }
    </style>
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-md-12 mb-1">
                <form action="{{ route('monitoring-equipment-afc.dashboard') }}" method="GET">
                    @method('GET')
                    @csrf
                    <div class="d-flex justify-content-end align-items-center gap-2">
                        <select class="tom-select-class w-auto" name="station_code" id="station_code">
                            <option value="">- select station -</option>
                            @foreach ($stations as $item)
                                <option value="{{ $item->station_code }}" @selected($item->station_code == $station_code)>
                                    {{ $item->station_name }}
                                </option>
                            @endforeach
                        </select>
                        <button type="submit" class="btn btn-gradient-primary text-white" title="Submit">
                            <i class="mdi mdi-magnify"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 stretch-card grid-margin">
                <div class="svg-container">
                    @include('layout.svg.' . ($station_code ?? 'default'))
                    <div id="equipment-tooltip" class="equipment-tooltip">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Detail Equipment -->
    <div class="modal fade" id="equipmentModal" tabindex="-1" aria-labelledby="equipmentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="equipmentModalLabel">Equipment Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <dl class="row small" id="equipment-details">
                        <!-- Data injected here -->
                    </dl>
                </div>
            </div>
        </div>
    </div>
    <!-- End Modal Detail Equipment -->

    <!-- Power ON Modal -->
    <div class="modal fade" id="powerOnModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Power On Confirmation?</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="powerOnForm" action="#" method="POST" class="forms-sample">
                        @csrf
                        @method('POST')
                        <input type="hidden" name="uuid" id="uuid_edit">
                        <input type="hidden" name="control_type" value="on">
                        <div class="form-group">
                            <label for="station_code">Station</label>
                            <input type="text" class="form-control" id="station_code_edit" name="station_code" disabled>
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
                            <label for="corner_id">Corner</label>
                            <input type="text" class="form-control" id="corner_id_edit" name="corner_id" disabled>
                        </div>
                        <div class="form-group">
                            <label for="direction">Direction</label>
                            <input type="text" class="form-control" id="direction_edit" name="direction" disabled>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" form="powerOnForm" class="btn btn-gradient-success me-2">Power On</button>
                </div>
            </div>
        </div>
    </div>
    <!-- End Power ON Modal -->

    <!-- Power OFF Modal -->
    <div class="modal fade" id="powerOffModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Power Off Confirmation?</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="powerOffForm" action="#" method="POST" class="forms-sample">
                        @csrf
                        @method('POST')
                        <input type="hidden" name="uuid" id="uuid_off">
                        <input type="hidden" name="control_type" value="off">
                        <div class="form-group">
                            <label for="station_code">Station</label>
                            <input type="text" class="form-control" id="station_code_off" name="station_code" disabled>
                        </div>
                        <div class="form-group">
                            <label for="equipment_type_code">Equipment Type</label>
                            <input type="text" class="form-control" id="equipment_type_code_off"
                                name="equipment_type_code" disabled>
                        </div>
                        <div class="form-group">
                            <label for="equipment_name">Equipment Name</label>
                            <input type="text" class="form-control" id="equipment_name_off" name="equipment_name"
                                disabled>
                        </div>
                        <div class="form-group">
                            <label for="corner_id">Corner</label>
                            <input type="text" class="form-control" id="corner_id_off" name="corner_id" disabled>
                        </div>
                        <div class="form-group">
                            <label for="direction">Direction</label>
                            <input type="text" class="form-control" id="direction_off" name="direction" disabled>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" form="powerOffForm" class="btn btn-gradient-danger me-2">Power Off</button>
                </div>
            </div>
        </div>
    </div>
    <!-- End Power OFF Modal -->

    <!-- Reboot Modal -->
    <div class="modal fade" id="rebootModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Reboot Confirmation?</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="rebootForm" action="#" method="POST" class="forms-sample">
                        @csrf
                        @method('POST')
                        <input type="hidden" name="uuid" id="uuid_reboot">
                        <input type="hidden" name="control_type" value="reboot">
                        <div class="form-group">
                            <label for="station_code">Station</label>
                            <input type="text" class="form-control" id="station_code_reboot" name="station_code" disabled>
                        </div>
                        <div class="form-group">
                            <label for="equipment_type_code">Equipment Type</label>
                            <input type="text" class="form-control" id="equipment_type_code_reboot"
                                name="equipment_type_code" disabled>
                        </div>
                        <div class="form-group">
                            <label for="equipment_name">Equipment Name</label>
                            <input type="text" class="form-control" id="equipment_name_reboot" name="equipment_name"
                                disabled>
                        </div>
                        <div class="form-group">
                            <label for="corner_id">Corner</label>
                            <input type="text" class="form-control" id="corner_id_reboot" name="corner_id" disabled>
                        </div>
                        <div class="form-group">
                            <label for="direction">Direction</label>
                            <input type="text" class="form-control" id="direction_reboot" name="direction" disabled>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" form="rebootForm" class="btn btn-gradient-warning me-2">Reboot</button>
                </div>
            </div>
        </div>
    </div>
    <!-- End Reboot Modal -->
@endsection

@section('javascript')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const equipments = @json($results);
            const tooltip = document.getElementById('equipment-tooltip');
            const modal = new bootstrap.Modal(document.getElementById('equipmentModal'));
            const modalDetails = document.getElementById('equipment-details');

            equipments.forEach(eq => {
                const svgElement = document.getElementById(eq.id);
                if (svgElement) {
                    // Update class status
                    svgElement.classList.remove('online', 'offline', 'standby');
                    svgElement.classList.add(eq.status);

                    // Tooltip events
                    svgElement.addEventListener('mouseenter', function(e) {
                        tooltip.innerHTML = `
                        <strong>${eq.equipment_name}</strong><br>
                        IP: ${eq.ip}<br>
                        Status: ${eq.status}<br>
                        Uptime: ${eq.uptime}
                    `;
                        tooltip.style.display = 'block';
                    });

                    svgElement.addEventListener('mousemove', function(e) {
                        tooltip.style.left = (e.pageX + 15) + 'px';
                        tooltip.style.top = (e.pageY + 15) + 'px';
                    });

                    svgElement.addEventListener('mouseleave', function(e) {
                        tooltip.style.display = 'none';
                    });

                    // Click event → show modal
                    svgElement.addEventListener('click', function(e) {
                        modalDetails.innerHTML = `
                        <dt class="col-sm-4">Type</dt><dd class="col-sm-8">${eq.equipment_type_code}</dd>
                        <dt class="col-sm-4">Station</dt><dd class="col-sm-8">${eq.station_code}</dd>
                        <dt class="col-sm-4">Equipment</dt><dd class="col-sm-8">${eq.equipment_name}</dd>
                        <dt class="col-sm-4">Status</dt><dd class="col-sm-8"><span class="badge bg-${eq.status === 'online' ? 'success' : 'danger'}">${eq.status}</span></dd>
                        <dt class="col-sm-4">Corner</dt><dd class="col-sm-8">${eq.corner_id ?? '-'}</dd>
                        <dt class="col-sm-4">IP Address</dt><dd class="col-sm-8">${eq.ip}</dd>
                        <dt class="col-sm-4">Uptime</dt><dd class="col-sm-8">${eq.uptime}</dd>

                        <dt class="col-sm-4">Load Average (1m/5m/15m)</dt><dd class="col-sm-8">
                            ${eq.load_average['1m'].toFixed(2)} / ${eq.load_average['5m'].toFixed(2)} / ${eq.load_average['15m'].toFixed(2)}<br>
                            <span class="badge bg-${loadStatusColor(eq.load_average.status)}">${eq.load_average.status}</span>
                        </dd>

                        <dt class="col-sm-4">RAM</dt><dd class="col-sm-8">${eq.ram.used} / ${eq.ram.total}</dd>
                        <dt class="col-sm-4">Disk</dt><dd class="col-sm-8">${eq.disk_root.used} / ${eq.disk_root.total}</dd>
                        <dt class="col-sm-4">CPU Cores</dt><dd class="col-sm-8">${eq.cpu_cores}</dd>

                        <dt class="col-sm-4">Core Temperatures</dt><dd class="col-sm-8">
                            ${formatTemperatures(eq.core_temperatures)}
                        </dd>
                        <hr class="my-3">
                        <dt class="col-sm-4">Power On</dt><dd class="col-sm-8">
                            <button type="button" title="Power ON"
                                class="btn btn-gradient-success btn-rounded btn-icon"
                                data-bs-toggle="modal" data-bs-target="#powerOnModal"
                                data-id="${eq.id}" data-uuid="${eq.uuid}" data-station_code="${eq.station_code}" data-equipment_type_code="${eq.equipment_type_code}" data-equipment_name="${eq.equipment_name}" data-corner_id="${eq.corner_id}" data-direction="${eq.direction}">
                                <i class="mdi mdi-power"></i>
                            </button>
                        </dd>
                        <dt class="col-sm-4">Reboot</dt><dd class="col-sm-8">
                            <button type="button" title="Reboot"
                                class="btn btn-gradient-warning btn-rounded btn-icon"
                                data-bs-toggle="modal" data-bs-target="#rebootModal"
                                data-id="${eq.id}" data-uuid="${eq.uuid}" data-station_code="${eq.station_code}" data-equipment_type_code="${eq.equipment_type_code}" data-equipment_name="${eq.equipment_name}" data-corner_id="${eq.corner_id}" data-direction="${eq.direction}">
                                <i class="mdi mdi-reload"></i>
                            </button>
                        </dd>
                        <dt class="col-sm-4">Power Off</dt><dd class="col-sm-8">
                            <button type="button" title="Power Off"
                                class="btn btn-gradient-danger btn-rounded btn-icon"
                                data-bs-toggle="modal" data-bs-target="#powerOffModal"
                                data-id="${eq.id}" data-uuid="${eq.uuid}" data-station_code="${eq.station_code}" data-equipment_type_code="${eq.equipment_type_code}" data-equipment_name="${eq.equipment_name}" data-corner_id="${eq.corner_id}" data-direction="${eq.direction}">
                                <i class="mdi mdi-power"></i>
                            </button>
                        </dd>
                    `;
                        modal.show();
                    });
                }
            });

            function loadStatusColor(status) {
                switch (status) {
                    case 'normal':
                        return 'success';
                    case 'busy':
                        return 'warning';
                    default:
                        return 'danger';
                }
            }

            function formatTemperatures(temps) {
                if (!temps || temps.length === 0) {
                    return '<span class="text-muted">N/A</span>';
                }

                return temps.map((temp, index) => {
                    const val = parseFloat(temp);
                    let color = 'success';
                    if (val > 75) color = 'danger';
                    else if (val > 60) color = 'warning';

                    return `<span class="badge bg-${color}-subtle text-${color} me-1 mb-1">Core ${index + 1}: ${val.toFixed(1)}°C</span>`;
                }).join(' ');
            }
        });
    </script>

    <script>
        $(document).ready(function() {
            $('#powerOnModal').on('show.bs.modal', function(e) {
                var uuid = $(e.relatedTarget).data('uuid');
                var station_code = $(e.relatedTarget).data('station_code');
                var equipment_type_code = $(e.relatedTarget).data('equipment_type_code');
                var equipment_name = $(e.relatedTarget).data('equipment_name');
                var corner_id = $(e.relatedTarget).data('corner_id');
                var direction = $(e.relatedTarget).data('direction');

                $('#uuid_edit').val(uuid);
                $('#station_code_edit').val(station_code);
                $('#equipment_type_code_edit').val(equipment_type_code);
                $('#equipment_name_edit').val(equipment_name);
                $('#corner_id_edit').val(corner_id);
                $('#direction_edit').val(direction);
            });

            $('#rebootModal').on('show.bs.modal', function(e) {
                var uuid = $(e.relatedTarget).data('uuid');
                var station_code = $(e.relatedTarget).data('station_code');
                var equipment_type_code = $(e.relatedTarget).data('equipment_type_code');
                var equipment_name = $(e.relatedTarget).data('equipment_name');
                var corner_id = $(e.relatedTarget).data('corner_id');
                var direction = $(e.relatedTarget).data('direction');

                $('#uuid_reboot').val(uuid);
                $('#station_code_reboot').val(station_code);
                $('#equipment_type_code_reboot').val(equipment_type_code);
                $('#equipment_name_reboot').val(equipment_name);
                $('#corner_id_reboot').val(corner_id);
                $('#direction_reboot').val(direction);
            });

            $('#powerOffModal').on('show.bs.modal', function(e) {
                var uuid = $(e.relatedTarget).data('uuid');
                var station_code = $(e.relatedTarget).data('station_code');
                var equipment_type_code = $(e.relatedTarget).data('equipment_type_code');
                var equipment_name = $(e.relatedTarget).data('equipment_name');
                var corner_id = $(e.relatedTarget).data('corner_id');
                var direction = $(e.relatedTarget).data('direction');

                $('#uuid_off').val(uuid);
                $('#station_code_off').val(station_code);
                $('#equipment_type_code_off').val(equipment_type_code);
                $('#equipment_name_off').val(equipment_name);
                $('#corner_id_off').val(corner_id);
                $('#direction_off').val(direction);
            });
        });
    </script>
@endsection
