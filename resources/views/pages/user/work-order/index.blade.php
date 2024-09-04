@extends('layout.base')

@section('title-head')
    <title>Work Order</title>
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Data Work Order</h4>
                        <div class="btn-group my-2">
                            <button type="button" onclick="window.location.href='{{ route('work-order.create') }}'"
                                title="Add" class="btn btn-outline-primary btn-rounded btn-icon">
                                <i class="mdi mdi-plus-circle"></i>
                            </button>
                            <button type="button" title="Filter" class="btn btn-outline-primary btn-rounded btn-icon">
                                <i class="mdi mdi-filter"></i>
                            </button>
                            <button type="button" title="Export" class="btn btn-outline-primary btn-rounded btn-icon">
                                <i class="mdi mdi-file-export"></i>
                            </button>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered text-center">
                                <thead>
                                    <tr>
                                        <th> # </th>
                                        <th> Date </th>
                                        <th> WO Number </th>
                                        <th> WO SAP </th>
                                        <th> Name </th>
                                        <th> Description </th>
                                        <th> Location </th>
                                        <th> Type </th>
                                        <th> Status </th>
                                        <th> Updated By </th>
                                        <th> Detail </th>
                                        <th> Action </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($work_order as $item)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $item->date }}</td>
                                            <td class="fw-bolder">{{ $item->ticket_number ?? '-' }}</td>
                                            <td>{{ $item->wo_number_sap ?? '-' }}</td>
                                            <td>{{ $item->name }}</td>
                                            <td>{{ $item->description }}</td>
                                            <td>{{ $item->relasi_area->sub_lokasi->name ?? '-' }}</td>
                                            <td>{{ $item->tipe_pekerjaan->code ?? '-' }}</td>
                                            <td>{{ $item->status->code ?? '-' }}</td>
                                            <td>
                                                {{ $item->user->name ?? '-' }} <br>
                                                ({{ $item->updated_at }})
                                            </td>
                                            <td>
                                                <a href="{{ route('work-order.detail', $item->uuid) }}"
                                                    title="Show Detail Work Order">
                                                    <button type="button"
                                                        class="btn btn-gradient-success btn-rounded btn-icon">
                                                        <i class="mdi mdi-eye"></i>
                                                    </button>
                                                </a>
                                            </td>
                                            <td>
                                                <a href="{{ route('work-order.edit', $item->uuid) }}" title="Edit">
                                                    <button type="button"
                                                        class="btn btn-gradient-warning btn-rounded btn-icon">
                                                        <i class="mdi mdi-lead-pencil"></i>
                                                    </button>
                                                </a>
                                                {{-- <button type="button" title="Delete"
                                                    class="btn btn-gradient-danger btn-rounded btn-icon"
                                                    data-bs-toggle="modal" data-bs-target="#deleteModal"
                                                    data-id="{{ $item->id }}">
                                                    <i class="mdi mdi-delete"></i>
                                                </button> --}}
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
