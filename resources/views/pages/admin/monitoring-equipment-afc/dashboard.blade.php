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
                        style="position: absolute; display: none;
                            background: #333;
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
@endsection

@section('javascript')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const equipments = @json($results);
            const tooltip = document.getElementById('equipment-tooltip');

            equipments.forEach(eq => {
                const svgElement = document.getElementById(eq.id);
                if (svgElement) {
                    // Update class status
                    svgElement.classList.remove('online', 'offline');
                    svgElement.classList.add(eq.status);

                    // Simpan metadata ke element (dataset)
                    svgElement.dataset.name = eq.equipment_name;
                    svgElement.dataset.ip = eq.ip;
                    svgElement.dataset.status = eq.status;
                    svgElement.dataset.uptime = eq.uptime;

                    // Event Hover
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

                    // Optional: Click event (bisa buat alert / modal)
                    svgElement.addEventListener('click', function(e) {
                        alert(
                            `${eq.equipment_name}\nIP: ${eq.ip}\nStatus: ${eq.status}\nUptime: ${eq.uptime}`);
                    });
                }
            });
        });
    </script>
@endsection
