@extends('layout.login')

@section('title-head')
    <title>Monitoring Equipment</title>
    <style>
        .float {
            position: fixed;
            bottom: 20px;
            right: 20px;
        }
    </style>
@endsection

@section('content')
    <div class="content-wrapper d-flex align-items-center auth">
        <div class="row flex-grow">
            <div class="col-lg-4 mx-auto">
                <div class="auth-form-light text-left p-4">
                    <div class="brand-logo text-center">
                        <img src="{{ asset('assets/images/logo.png') }}" alt="logo">
                    </div>
                    <h4>Aplikasi monitoring ini milik AFC, jangan ditutup!</h4>
                    <div class="table-responsive mt-4">
                        <table class="table">
                            <tbody>
                                <tr>
                                    <td class="fw-bolder">
                                        Name
                                    </td>
                                    <td>:</td>
                                    <td id="nameTable">
                                        -
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-bolder">
                                        Code
                                    </td>
                                    <td>:</td>
                                    <td id="codeTable">
                                        -
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-bolder">
                                        Stasiun
                                    </td>
                                    <td>:</td>
                                    <td id="stasiunTable">
                                        -
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-bolder">
                                        Corner
                                    </td>
                                    <td>:</td>
                                    <td id="cornerTable">
                                        -
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-bolder">
                                        Status
                                    </td>
                                    <td>:</td>
                                    <td id="statusTable">
                                        -
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div style="display: none">
                        <div class="form-group mt-4">
                            <label for="name">Name</label>
                            <input id="name" type="text" class="form-control" value="{{ $device->name ?? '' }}"
                                disabled>
                        </div>
                        <div class="form-group">
                            <label for="code">ID</label>
                            <input id="code" type="text" class="form-control" value="{{ $device->code ?? '' }}"
                                disabled>
                        </div>
                        <div class="form-group">
                            <label for="stasiun">Stasiun</label>
                            <input id="stasiun" type="text" class="form-control"
                                value="{{ $device->relasi_area->sub_lokasi->name ?? '' }}" disabled>
                        </div>
                        <div class="form-group">
                            <label for="corner">Corner</label>
                            <input id="corner" type="text" class="form-control"
                                value="{{ $device->arah->name ?? '' }}" disabled>
                        </div>
                        <div class="form-group">
                            <label for="status">Status</label>
                            <input id="status" type="text" class="form-control" value="disconnected" disabled>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="float">
            <button type="button" title="Connect" onclick="connect()" class="btn btn-outline-primary btn-rounded btn-icon">
                <i class="mdi mdi-lan-connect"></i>
            </button>
            <button type="button" title="Settings" class="btn btn-outline-primary btn-rounded btn-icon"
                data-bs-toggle="modal" data-bs-target="#settingModal">
                <i class="mdi mdi-settings"></i>
            </button>
            <button type="button" id="trigger" title="Trigger" class="btn btn-outline-primary btn-rounded btn-icon">
                <i class="mdi mdi-run"></i>
            </button>
            <button type="button" onclick="location.reload();" id="refresh" title="Refresh"
                class="btn btn-outline-primary btn-rounded btn-icon">
                <i class="mdi mdi-refresh"></i>
            </button>
        </div>
    </div>

    <!-- Add Modal -->
    <div class="modal fade" id="settingModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Form Add</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addForm" action="{{ route('client.monitoring-equipment.index') }}" method="GET"
                        class="forms-sample">
                        @csrf
                        @method('GET')
                        <div class="form-group">
                            <label for="uuid">Equipment</label>
                            <select class="form-control form-control-lg" id="uuid" name="uuid" required>
                                <option value="" selected disabled>- pilih equipment -</option>
                                @foreach ($equipment as $item)
                                    @php
                                        $device_uuid = $device->uuid ?? '-';
                                    @endphp
                                    <option value="{{ $item->uuid }}" @if ($item->uuid == $device_uuid) selected @endif>
                                        {{ $item->name ?? '-' }} - {{ $item->code ?? '-' }}
                                        - {{ $item->relasi_area->sub_lokasi->code ?? '-' }} -
                                        {{ $item->arah->name ?? '-' }}
                                    </option>
                                @endforeach
                            </select>
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
@endsection

@section('javascript')
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    <script>
        $(document).ready(function() {
            Pusher.logToConsole = true;
            var pusher = new Pusher('{{ config('services.pusher.key') }}', {
                cluster: '{{ config('services.pusher.cluster') }}'
            });

            var channel = pusher.subscribe('monitoring-equipment-channel');
            channel.bind('MonitoringEquipmentEvent', function(dataRaw) {
                var dataString = JSON.stringify(dataRaw)
                var data = JSON.parse(dataString);

                if (data.command === 'check-status') {
                    $('#trigger').click();
                }
            });

            $('#trigger').click(function(e) {
                e.preventDefault();

                const uuid = localStorage.getItem('uuid');
                const status = 'connected';
                const parameter = `?uuid=${uuid}&status=${status}`;
                const url = `{{ route('monitoring-equipment.store') }}${parameter}`;

                $.ajax({
                    type: 'GET',
                    url: url,
                    success: (response) => {
                        console.log(response.message);
                        localStorage.setItem('status', 'connected');
                    },
                    error: function(response) {
                        console.log(response);
                        localStorage.setItem('status', 'disconnected');
                    }
                });
            });
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @if ($device == null)
                document.getElementById('uuid').value = localStorage.getItem('uuid');
                document.getElementById('name').value = localStorage.getItem('name');
                document.getElementById('code').value = localStorage.getItem('code');
                document.getElementById('stasiun').value = localStorage.getItem('stasiun');
                document.getElementById('corner').value = localStorage.getItem('corner');
                document.getElementById('status').value = localStorage.getItem('status');
                document.getElementById('nameTable').innerHTML = localStorage.getItem('name');
                document.getElementById('codeTable').innerHTML = localStorage.getItem('code');
                document.getElementById('stasiunTable').innerHTML = localStorage.getItem('stasiun');
                document.getElementById('cornerTable').innerHTML = localStorage.getItem('corner');
                document.getElementById('statusTable').innerHTML = localStorage.getItem('status');
            @endif

            if (localStorage.getItem('uuid')) {
                document.getElementById('status').value = 'connected';
                console.log((localStorage.getItem('uuid')));
                console.log((localStorage.getItem('name')));
                console.log((localStorage.getItem('code')));
                console.log((localStorage.getItem('stasiun')));
                console.log((localStorage.getItem('corner')));
                console.log((localStorage.getItem('status')));
            }
        });

        function connect() {
            var uuid = document.getElementById('uuid').value;
            var name = document.getElementById('name').value;
            var code = document.getElementById('code').value;
            var stasiun = document.getElementById('stasiun').value;
            var corner = document.getElementById('corner').value;
            var status = 'connected';

            localStorage.setItem('uuid', uuid);
            localStorage.setItem('name', name);
            localStorage.setItem('code', code);
            localStorage.setItem('stasiun', stasiun);
            localStorage.setItem('corner', corner);
            localStorage.setItem('status', status);

            window.location.href = "{{ route('client.monitoring-equipment.index') }}";
        }
    </script>
@endsection
