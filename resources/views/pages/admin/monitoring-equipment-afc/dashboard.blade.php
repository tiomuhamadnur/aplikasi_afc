@extends('layout.base')

@section('title-head')
    <title>Dashboard Equipment AFC</title>
    <style>
        /* Container and SVG Base Styles */
        .svg-container {
            position: relative;
            width: 100%;
            height: auto;
        }

        .svg-container svg {
            width: 100%;
            height: auto;
            max-width: 100%;
        }

        /* Status Classes */
        .svg-container svg .online {
            fill: #00E600 !important;
            pointer-events: all;
        }

        .svg-container svg .standby {
            fill: #8e8e8e !important;
            pointer-events: all;
        }

        .svg-container svg .offline {
            fill: #ff4040 !important;
            animation: blinkOffline 1s infinite;
            pointer-events: all;
        }

        /* Safer Animation (No Filter) */
        @keyframes blinkOffline {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.5;
            }
        }

        /* Tooltip Styling */
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
                    <div id="equipment-tooltip"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="equipmentModal" tabindex="-1" aria-labelledby="equipmentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="equipmentModalLabel">Equipment Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <dl class="row small" id="equipment-details"></dl>
                </div>
            </div>
        </div>
    </div>
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
                    // Set status class
                    svgElement.classList.remove('online', 'offline', 'standby');
                    svgElement.classList.add(eq.status);

                    // Tooltip events
                    svgElement.addEventListener('mouseenter', (e) => {
                        tooltip.innerHTML = `
                            <strong>${eq.equipment_name}</strong><br>
                            IP: ${eq.ip}<br>
                            Status: ${eq.status}<br>
                            Uptime: ${eq.uptime}
                        `;
                        tooltip.style.display = 'block';
                    });

                    svgElement.addEventListener('mousemove', (e) => {
                        tooltip.style.left = `${e.pageX + 15}px`;
                        tooltip.style.top = `${e.pageY + 15}px`;
                    });

                    svgElement.addEventListener('mouseleave', () => {
                        tooltip.style.display = 'none';
                    });

                    // Click event → show modal
                    svgElement.addEventListener('click', () => {
                        modalDetails.innerHTML = `
                            <dt class="col-sm-4">Type</dt><dd class="col-sm-8">${eq.equipment_type_code}</dd>
                            <dt class="col-sm-4">Station</dt><dd class="col-sm-8">${eq.station_code}</dd>
                            <dt class="col-sm-4">Equipment</dt><dd class="col-sm-8">${eq.equipment_name}</dd>
                            <dt class="col-sm-4">Status</dt><dd class="col-sm-8"><span class="badge bg-${eq.status === 'online' ? 'success' : 'danger'}">${eq.status}</span></dd>
                            <dt class="col-sm-4">Corner</dt><dd class="col-sm-8">${eq.corner_id ?? '-'}</dd>
                            <dt class="col-sm-4">IP Address</dt><dd class="col-sm-8">${eq.ip}</dd>
                            <dt class="col-sm-4">Uptime</dt><dd class="col-sm-8">${eq.uptime}</dd>
                            <dt class="col-sm-4">Load Average</dt><dd class="col-sm-8">
                                ${eq.load_average['1m'].toFixed(2)} / ${eq.load_average['5m'].toFixed(2)} / ${eq.load_average['15m'].toFixed(2)}<br>
                                <span class="badge bg-${eq.load_average.status === 'normal' ? 'success' : (eq.load_average.status === 'busy' ? 'warning' : 'danger')}">
                                    ${eq.load_average.status}
                                </span>
                            </dd>
                            <dt class="col-sm-4">RAM</dt><dd class="col-sm-8">${eq.ram.used} / ${eq.ram.total}</dd>
                            <dt class="col-sm-4">Disk</dt><dd class="col-sm-8">${eq.disk_root.used} / ${eq.disk_root.total}</dd>
                            <dt class="col-sm-4">CPU Cores</dt><dd class="col-sm-8">${eq.cpu_cores}</dd>
                            <dt class="col-sm-4">Core Temperatures</dt><dd class="col-sm-8">
                                ${eq.core_temperatures?.map((temp, i) => `
                                            <span class="badge bg-${temp > 75 ? 'danger' : (temp > 60 ? 'warning' : 'success')}-subtle text-${temp > 75 ? 'danger' : (temp > 60 ? 'warning' : 'success')} me-1 mb-1">
                                                Core ${i + 1}: ${temp.toFixed(1)}°C
                                            </span>
                                        `).join('') || '<span class="text-muted">N/A</span>'}
                            </dd>
                        `;
                        modal.show();
                    });
                }
            });
        });
    </script>
@endsection
