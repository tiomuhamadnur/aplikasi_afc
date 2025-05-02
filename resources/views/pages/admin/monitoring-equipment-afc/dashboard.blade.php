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
            pointer-events: all;
            /* Ensure the SVG is still clickable and hoverable */
            z-index: 10;
            /* Ensure the SVG stays on top of other content */
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

        /* Tooltip styling */
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
                        <dt class="col-sm-4">Type</dt>
                        <dd class="col-sm-8" id="modal-equipment-type"></dd>
                        <dt class="col-sm-4">Station</dt>
                        <dd class="col-sm-8" id="modal-station-code"></dd>
                        <dt class="col-sm-4">Equipment</dt>
                        <dd class="col-sm-8" id="modal-equipment-name"></dd>
                        <dt class="col-sm-4">Status</dt>
                        <dd class="col-sm-8" id="modal-status"></dd>
                        <dt class="col-sm-4">Corner</dt>
                        <dd class="col-sm-8" id="modal-corner-id"></dd>
                        <dt class="col-sm-4">IP Address</dt>
                        <dd class="col-sm-8" id="modal-ip-address"></dd>
                        <dt class="col-sm-4">Uptime</dt>
                        <dd class="col-sm-8" id="modal-uptime"></dd>

                        <!-- Load Average -->
                        <dt class="col-sm-4">Load Average (1m/5m/15m)</dt>
                        <dd class="col-sm-8" id="modal-load-average"></dd>

                        <!-- RAM Progress -->
                        <dt class="col-sm-4">RAM</dt>
                        <dd class="col-sm-8" id="modal-ram"></dd>
                        <div class="progress mt-1" style="height: 3px;">
                            <div id="modal-ram-progress" class="progress-bar" style="width: 0%"></div>
                        </div>

                        <!-- Disk Progress -->
                        <dt class="col-sm-4">Disk</dt>
                        <dd class="col-sm-8" id="modal-disk"></dd>
                        <div class="progress mt-1" style="height: 3px;">
                            <div id="modal-disk-progress" class="progress-bar" style="width: 0%"></div>
                        </div>

                        <!-- Other Details -->
                        <dt class="col-sm-4">CPU Cores</dt>
                        <dd class="col-sm-8" id="modal-cores"></dd>
                        <dt class="col-sm-4">Core Temperatures</dt>
                        <dd class="col-sm-8" id="modal-temperatures"></dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
    <!-- End Modal Detail Equipment -->
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

                    // Pastikan event listeners tetap aktif setelah status diubah
                    attachEventListeners(svgElement, eq);
                }
            });

            function attachEventListeners(svgElement, eq) {
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
                    // Modal content
                    document.getElementById('modal-equipment-type').textContent = eq.equipment_type_code;
                    document.getElementById('modal-station-code').textContent = eq.station_code;
                    document.getElementById('modal-equipment-name').textContent = eq.equipment_name;
                    document.getElementById('modal-status').innerHTML =
                        `<span class="badge bg-${eq.status === 'online' ? 'success' : 'danger'}">${eq.status}</span>`;
                    document.getElementById('modal-corner-id').textContent = eq.corner_id ?? '-';
                    document.getElementById('modal-ip-address').textContent = eq.ip;
                    document.getElementById('modal-uptime').textContent = eq.uptime;

                    // Load Average
                    document.getElementById('modal-load-average').innerHTML = `
                    ${eq.load_average['1m'].toFixed(2)} / ${eq.load_average['5m'].toFixed(2)} / ${eq.load_average['15m'].toFixed(2)}
                    <br><span class="badge bg-${loadStatusColor(eq.load_average.status)}">${eq.load_average.status}</span>
                `;

                    // RAM Progress
                    const ramUsed = eq.ram.used;
                    const ramTotal = eq.ram.total;
                    const ramPercent = eq.ram.percent || 0;
                    document.getElementById('modal-ram').textContent = `${ramUsed} / ${ramTotal}`;
                    document.getElementById('modal-ram-progress').style.width = `${ramPercent}%`;
                    document.getElementById('modal-ram-progress').classList.remove('bg-danger',
                        'bg-warning', 'bg-success');
                    if (ramPercent > 90) document.getElementById('modal-ram-progress').classList.add(
                        'bg-danger');
                    else if (ramPercent > 70) document.getElementById('modal-ram-progress').classList.add(
                        'bg-warning');
                    else document.getElementById('modal-ram-progress').classList.add('bg-success');

                    // Disk Progress
                    const diskUsed = eq.disk_root.used;
                    const diskTotal = eq.disk_root.total;
                    const diskPercent = eq.disk_root.percent || 0;
                    document.getElementById('modal-disk').textContent = `${diskUsed} / ${diskTotal}`;
                    document.getElementById('modal-disk-progress').style.width = `${diskPercent}%`;
                    document.getElementById('modal-disk-progress').classList.remove('bg-danger',
                        'bg-warning', 'bg-success');
                    if (diskPercent > 90) document.getElementById('modal-disk-progress').classList.add(
                        'bg-danger');
                    else if (diskPercent > 70) document.getElementById('modal-disk-progress').classList.add(
                        'bg-warning');
                    else document.getElementById('modal-disk-progress').classList.add('bg-success');

                    // CPU Cores and Temperatures
                    document.getElementById('modal-cores').textContent = eq.cpu_cores;
                    document.getElementById('modal-temperatures').innerHTML = formatTemperatures(eq
                        .core_temperatures);

                    // Show modal
                    modal.show();
                });
            }

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
@endsection
