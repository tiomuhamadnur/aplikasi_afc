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
                            <div class="row">
                                @foreach ($monitoring_equipment as $item)
                                    <div class="col-lg-3 col-md-4 col-sm-6">
                                        <div class="card-device">
                                            <div class="card-header-device bg-gradient-primary text-center">
                                                <h4>{{ $item->equipment->name ?? '-' }}</h4>
                                            </div>
                                            <div class="card-body-device">
                                                <div class="table">
                                                    {{-- <p><strong>Code :</strong> {{ $item->equipment->code ?? '-' }}</p> --}}
                                                    <p><strong>Stasiun :</strong>
                                                        {{ $item->equipment->relasi_area->sub_lokasi->name ?? '-' }}</p>
                                                    <p><strong>Corner :</strong> {{ $item->equipment->arah->name ?? '-' }}
                                                    </p>
                                                    <p><strong>Status :</strong> <span
                                                            class="@if ($item->status == 'connected') status-online @else status-offline @endif">{{ $item->status }}</span>
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

    <!-- Delete Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Are you sure?</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="deleteForm" action="{{ route('monitoring-equipment.delete') }}" method="POST"
                        class="forms-sample">
                        @csrf
                        @method('delete')
                        <input type="text" name="id" id="id_delete" hidden>
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
@endsection

@section('javascript')
    <script>
        $(document).ready(function() {
            window.Echo.channel('monitoring-equipment-channel')
                .listen('.MonitoringEquipmentEvent', (e) => {
                    console.log('Command:', e.command);
                });

            $('#deleteModal').on('show.bs.modal', function(e) {
                var id = $(e.relatedTarget).data('id');

                $('#id_delete').val(id);
            });
        });

        function check() {
            var url = "{{ route('api.monitoring-equipment.check') }}";
            $.ajax({
                type: 'GET',
                url: url,
                success: (response) => {
                    console.log(response.message);
                    // reload();
                    setTimeout(() => {
                        location.reload();
                    }, 3000);
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
                    // let tbody = $('#equipmentTableBody');
                    // tbody.empty(); // Clear the table body

                    // $.each(data, function(index, item) {
                    //     let areaName = item.equipment && item.equipment.relasi_area && item.equipment
                    //         .relasi_area.sub_lokasi ? item.equipment.relasi_area.sub_lokasi.name : '-';
                    //     let code = item.equipment ? item.equipment.code : '-';
                    //     let statusClass = item.status === 'connected' ? 'badge-gradient-success' :
                    //         'badge-gradient-danger';

                    //     tbody.append(`
                //         <tr>
                //             <td>${index + 1}</td>
                //             <td>${areaName}</td>
                //             <td>${code}</td>
                //             <td><label class="badge ${statusClass} text-uppercase">${item.status}</label></td>
                //             <td>${item.waktu}</td>
                //             <td>
                //                 <button type="button" title="Delete" class="btn btn-gradient-danger btn-rounded btn-icon" data-bs-toggle="modal" data-bs-target="#deleteModal" data-id="${item.id}">
                //                     <i class="mdi mdi-delete"></i>
                //                 </button>
                //             </td>
                //         </tr>
                //     `);
                    // });
                    // setTimeout(() => {
                    //     location.reload();
                    // }, 5000);
                },
                error: function(response) {
                    console.log(response);
                }
            });
        }
    </script>
@endsection
