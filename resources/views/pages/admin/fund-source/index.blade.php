@extends('layout.base')

@section('title-head')
    <title>Admin | Fund Source</title>
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Data Fund Source</h4>
                        <div class="btn-group my-2">
                            <button type="button" title="Add" class="btn btn-outline-primary btn-rounded btn-icon"
                                data-bs-toggle="modal" data-bs-target="#addModal">
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
                                        <th> Fund </th>
                                        <th> Name </th>
                                        <th> Balance (IDR) </th>
                                        <th> Current Balance (IDR) </th>
                                        <th> Start Period </th>
                                        <th> End Period </th>
                                        <th> Update By </th>
                                        <th> Update At </th>
                                        <th> Action </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($fund_source as $item)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $item->fund->code ?? '-' }}</td>
                                            <td>{{ $item->fund->name ?? '-' }}</td>
                                            <td>@currency($item->balance)</td>
                                            <td>@currency($item->current_balance)</td>
                                            <td>{{ $item->start_period }}</td>
                                            <td>{{ $item->end_period }}</td>
                                            <td>{{ $item->user->name }}</td>
                                            <td>{{ $item->updated_at }}</td>
                                            <td>
                                                <button
                                                    onclick="location.href='{{ route('fund-source.edit', $item->uuid) }}';"
                                                    type="button" title="Edit"
                                                    class="btn btn-gradient-warning btn-rounded btn-icon">
                                                    <i class="mdi mdi-lead-pencil"></i>
                                                </button>
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

    <!-- Add Modal -->
    <div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Form Add</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addForm" action="{{ route('fund-source.store') }}" method="POST" class="forms-sample">
                        @csrf
                        @method('POST')
                        <div class="form-group">
                            <label for="fund_id">Fund</label>
                            <select class="tom-select-class" name="fund_id" id="fund_id" required>
                                <option value="" disabled selected>- select fund -</option>
                                @foreach ($fund as $item)
                                    <option value="{{ $item->id }}">{{ $item->code ?? '-' }} -
                                        {{ $item->name ?? '-' }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="balance">Balance (IDR)</label>
                            <input type="number" min="0" class="form-control" id="balance" name="balance"
                                placeholder="Input Balance" autocomplete="off" required>
                        </div>
                        <div class="form-group">
                            <label for="current_balance">Current Balance (IDR)</label>
                            <input type="number" min="0" class="form-control" id="current_balance"
                                name="current_balance" placeholder="Input Current Balance" autocomplete="off" required>
                        </div>
                        <div class="form-group">
                            <label for="start_period">Start Period</label>
                            <input type="date" class="form-control" id="start_period" name="start_period"
                                placeholder="Start Period" autocomplete="off" required>
                        </div>
                        <div class="form-group">
                            <label for="end_period">End Period</label>
                            <input type="date" class="form-control" id="end_period" name="end_period"
                                placeholder="End Period" autocomplete="off" required>
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

    <!-- Delete Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Are you sure?</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="deleteForm" action="{{ route('fund-source.delete') }}" method="POST"
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
            $('#deleteModal').on('show.bs.modal', function(e) {
                var id = $(e.relatedTarget).data('id');

                $('#id_delete').val(id);
            });
        });
    </script>
@endsection