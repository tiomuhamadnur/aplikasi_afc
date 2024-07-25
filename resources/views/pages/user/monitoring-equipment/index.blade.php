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
                            @livewire('monitoring-equipment')
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
                        Livewire.dispatch('reload');
                    }, 1000);
                    console.log(response.message);
                },
                error: function(response) {
                    console.log(response);
                }
            });
        }
    </script>
@endsection
