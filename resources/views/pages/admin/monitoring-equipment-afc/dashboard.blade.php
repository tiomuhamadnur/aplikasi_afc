@extends('layout.base')

@section('title-head')
    <title>Dashboard Equipment AFC</title>
    <style>
        .svg-container svg {
            width: 100%;
            height: auto;
            max-width: 100%;
        }
    </style>
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-md-12 stretch-card grid-margin">
                <div class="svg-container">
                    @include('layout.svg.' . ($station_code ?? 'default'))
                    <div id="equipment-tooltip"
                        style="position: absolute;
                        display: none;
                        background: rgba(51, 51, 51, 0.95);
                        color: #fff;
                        padding: 6px 10px;
                        border-radius: 6px;
                        font-size: 13px;
                        pointer-events: none;
                        z-index: 9999;">
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
                    svgElement.classList.remove('online', 'offline');
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
@endsection
