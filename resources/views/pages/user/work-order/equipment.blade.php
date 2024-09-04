@extends('layout.base')

@section('title-head')
    <title>Work Order - Equipment</title>
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Data Work Order - Equipment</h4>
                        <div class="btn-group my-2">
                            <button type="button" onclick="window.location.href='{{ route('work-order.index') }}'"
                                title="Back" class="btn btn-outline-primary btn-rounded btn-icon">
                                <i class="mdi mdi-arrow-left-bold"></i>
                            </button>
                            <button type="button" title="Filter" class="btn btn-outline-primary btn-rounded btn-icon">
                                <i class="mdi mdi-filter"></i>
                            </button>
                            <button type="button" title="Export" class="btn btn-outline-primary btn-rounded btn-icon">
                                <i class="mdi mdi-file-export"></i>
                            </button>
                        </div>
                        <table class="table-borderless my-3">
                            <tbody>
                                <tr>
                                    <td style="width: 120px">WO. Number</td>
                                    <td style="width: 20px">:</td>
                                    <td>{{ $work_order->ticket_number }}</td>
                                </tr>
                                <tr>
                                    <td>WO. SAP</td>
                                    <td>:</td>
                                    <td>{{ $work_order->wo_number_sap ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td>Date</td>
                                    <td>:</td>
                                    <td>{{ $work_order->date }}</td>
                                </tr>
                                <tr>
                                    <td>Location</td>
                                    <td>:</td>
                                    <td>{{ $work_order->relasi_area->sub_lokasi->name ?? '-' }}</td>
                                </tr>
                            </tbody>
                        </table>
                        <div class="table-responsive">
                            <table class="table table-bordered text-center">
                                <thead>
                                    <tr>
                                        <th> # </th>
                                        <th> Checksheet </th>
                                        <th> Name </th>
                                        <th> Code </th>
                                        <th> Equipment Number </th>
                                        <th> Type </th>
                                        <th> Location </th>
                                        <th> Status </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($trans_wo_equipment as $item)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>
                                                @if ($item->status == null)
                                                    <a href="{{ route('checksheet.create', [
                                                        'uuid_work_order' => $work_order->uuid,
                                                        'uuid_equipment' => $item->equipment->uuid,
                                                    ]) }}"
                                                        title="Input Checksheet">
                                                        <button type="button"
                                                            class="btn btn-gradient-warning btn-rounded btn-icon">
                                                            <i class="mdi mdi-lead-pencil"></i>
                                                        </button>
                                                    </a>
                                                @endif
                                            </td>
                                            <td>{{ $item->equipment->name ?? '-' }}</td>
                                            <td>{{ $item->equipment->code ?? '-' }}</td>
                                            <td>{{ $item->equipment->equipment_number ?? '-' }}</td>
                                            <td>{{ $item->equipment->tipe_equipment->code ?? '-' }}</td>
                                            <td>{{ $item->equipment->relasi_area->sub_lokasi->name ?? '-' }}</td>
                                            <td>
                                                @if ($item->status == null)
                                                    <button type="button"
                                                        class="btn btn-gradient-danger btn-rounded btn-icon"
                                                        title="Incomplete">
                                                        <i class="mdi mdi-close"></i>
                                                    </button>
                                                @else
                                                    <button type="button"
                                                        class="btn btn-gradient-success btn-rounded btn-icon"
                                                        title="Complete">
                                                        <i class="mdi mdi-check"></i>
                                                    </button>
                                                @endif
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

    <!-- Delete Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Are you sure?</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="deleteForm" action="{{ route('work-order.delete') }}" method="POST" class="forms-sample">
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
            $('#deleteModal').on('show.bs.modal', function(e) {
                var id = $(e.relatedTarget).data('id');

                $('#id_delete').val(id);
            });
        });
    </script>
@endsection
