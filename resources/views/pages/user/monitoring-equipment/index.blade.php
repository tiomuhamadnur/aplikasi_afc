@extends('layout.base')

@section('title-head')
    <title>Monitoring Equipment</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
                            {{-- <div class="row">
                                @foreach ($monitoring_equipment as $item)
                                    <div class="col-md-4 grid-margin">
                                        <div class="card bg-gradient-success card-img-holder text-white">
                                            <div class="card-body">
                                                <img src="assets/images/dashboard/circle.svg" class="card-img-absolute"
                                                    alt="circle-image" />
                                                <h4 class="font-weight-normal mb-3">
                                                    {{ $item->equipment->code ?? '-' }}
                                                </h4>
                                                <h3 class="card-text mb-3">{{ $item->status }}</h3>
                                                <h6 class="card-text">
                                                    {{ $item->equipment->relasi_area->sub_lokasi->name ?? '-' }}</h6>
                                                <h6 class="card-text">{{ $item->waktu }}</h6>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div> --}}
                            <div class="table-responsive">
                                <table class="table .table-hover text-center">
                                    <thead>
                                        <tr>
                                            <th> # </th>
                                            <th> Lokasi </th>
                                            <th> Equiment ID</th>
                                            <th> Status </th>
                                            <th> Tanggal </th>
                                            <th> Aksi </th>
                                        </tr>
                                    </thead>
                                    <tbody id="equipmentTableBody">
                                        @foreach ($monitoring_equipment as $item)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>
                                                    @if ($item->equipment->relasi_area_id != null)
                                                        {{ $item->equipment->relasi_area->sub_lokasi->name ?? '-' }}
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>{{ $item->equipment->code ?? '-' }}</td>
                                                <td>
                                                    <label
                                                        class="badge @if ($item->status == 'connected') badge-gradient-success @else badge-gradient-danger @endif text-uppercase">
                                                        {{ $item->status }}
                                                    </label>
                                                </td>
                                                <td>
                                                    {{ $item->waktu }}
                                                </td>
                                                <td>
                                                    <button type="button" title="Delete"
                                                        class="btn btn-gradient-danger btn-rounded btn-icon"
                                                        data-bs-toggle="modal" data-bs-target="#deleteModal"
                                                        data-id="{{ $item->id }}">
                                                        <i class="mdi mdi-delete"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
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
            reload();

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
                    reload();
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
                    location.reload();
                },
                error: function(response) {
                    console.log(response);
                }
            });
        }
    </script>
@endsection
