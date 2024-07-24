@extends('layout.base')

@section('title-head')
    <title>Monitoring Equipment</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .card-device {
            border: none;
            border-radius: 10px;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
            margin-bottom: 20px;
            background-color: white;
        }

        .card-header-device {
            background-color: #e100ff;
            color: white;
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
            padding: 10px 15px;
            font-size: 0.9rem;
        }

        .card-body-device {
            padding: 10px 15px;
        }

        .status-online {
            color: green;
            font-weight: bold;
        }

        .status-offline {
            color: red;
            font-weight: bold;
        }

        .table-borderless td,
        .table-borderless th {
            border: 0;
            padding: 2px 5px;
            /* Mengurangi padding antar baris */
        }
    </style>
    @livewireStyles
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Data Monitoring Equipment</h4>
                        <div class="btn-group my-2">
                            <button type="button" title="Add" class="btn btn-outline-primary btn-rounded btn-icon"
                                data-bs-toggle="modal" data-bs-target="#addModal">
                                <i class="mdi mdi-plus-circle"></i>
                            </button>
                            <button type="button" title="Filter" data-bs-toggle="modal" data-bs-target="#filterModal"
                                class="btn btn-outline-primary btn-rounded btn-icon">
                                <i class="mdi mdi-filter"></i>
                            </button>
                            <button type="button" id="export" title="Export"
                                class="btn btn-outline-primary btn-rounded btn-icon">
                                <i class="mdi mdi-file-export"></i>
                            </button>
                        </div>
                        <button type="button" id="check" title="Check Status"
                            class="btn btn-outline-success btn-rounded btn-icon" onclick="check()">
                            <i class="mdi mdi-eye"></i>
                        </button>
                        <div class="mt-3">
                            <div class="row" id="equipmentContainer">
                                @foreach ($monitoring_equipment as $item)
                                    <div class="col-lg-3 col-md-4 col-sm-6">
                                        <div class="card-device">
                                            <div
                                                class="card-header-device @if ($item->status == 'connected') bg-gradient-success @else bg-gradient-danger @endif fw-bolder text-center">
                                                <h4>{{ $item->equipment->name ?? '-' }}</h4>
                                            </div>
                                            <div class="card-body-device">
                                                <div class="table">
                                                    <p><strong>Stasiun :</strong>
                                                        {{ $item->equipment->relasi_area->sub_lokasi->name ?? '-' }}</p>
                                                    <p><strong>Corner :</strong> {{ $item->equipment->arah->name ?? '-' }}
                                                    </p>
                                                    <p><strong>Status :</strong> <span
                                                            class="badge @if ($item->status == 'connected') badge-gradient-success @else badge-gradient-danger @endif text-uppercase">
                                                            {{ $item->status }}
                                                        </span>
                                                    </p>
                                                    <p><strong>Waktu :</strong> {{ $item->waktu }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @livewireScripts
@endsection

@section('javascript')
    <script>
        $(document).ready(function() {
            window.Echo.channel('monitoring-equipment-channel')
                .listen('.MonitoringEquipmentEvent', (e) => {
                    console.log('Command:', e.command);
                });
        });

        function check() {
            var url = "{{ route('api.monitoring-equipment.check') }}";
            $.ajax({
                type: 'GET',
                url: url,
                success: (response) => {
                    setTimeout(() => {
                        reload();
                        // this.Livewire.emit('loadData');
                    }, 1000);
                    console.log(response.message);
                },
                error: function(response) {
                    console.log(response);
                }
            });
        }

        function reload() {
            $.ajax({
                type: 'GET',
                url: "{{ route('api.data.monitoring-equipment') }}",
                success: (data) => {
                    let container = $('#equipmentContainer');
                    container.empty(); // Clear the container

                    $.each(data, function(index, item) {
                        let areaName = item.equipment && item.equipment.relasi_area && item
                            .equipment
                            .relasi_area.sub_lokasi ? item.equipment.relasi_area.sub_lokasi
                            .name : '-';
                        let directionName = item.equipment && item.equipment.arah ? item
                            .equipment.arah.name : '-';
                        let statusClass = item.status === 'connected' ?
                            'bg-gradient-success' : 'bg-gradient-danger';
                        let badgeClass = item.status === 'connected' ?
                            'badge-gradient-success' : 'badge-gradient-danger';

                        container.append(`
                                <div class="col-lg-3 col-md-4 col-sm-6">
                                    <div class="card-device">
                                        <div class="card-header-device ${statusClass} fw-bolder text-center">
                                            <h4>${item.equipment ? item.equipment.name : '-'}</h4>
                                        </div>
                                        <div class="card-body-device">
                                            <div class="table">
                                                <p><strong>Stasiun :</strong> ${areaName}</p>
                                                <p><strong>Corner :</strong> ${directionName}</p>
                                                <p><strong>Status :</strong>
                                                    <span class="badge ${badgeClass} text-uppercase">${item.status}</span>
                                                </p>
                                                <p><strong>Waktu :</strong> ${item.waktu}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                `);
                    });
                },
                error: function(response) {
                    console.log(response);
                }
            });
        }
    </script>
@endsection
