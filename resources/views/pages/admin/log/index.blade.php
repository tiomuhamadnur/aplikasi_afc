@extends('layout.base')

@section('title-head')
    <title>Admin | Log PG</title>
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Data Log PG</h4>
                        <div class="btn-group my-2">
                            <button type="button" title="Filter" class="btn btn-outline-primary btn-rounded btn-icon"
                                data-bs-toggle="modal" data-bs-target="#filterModal">
                                <i class="mdi mdi-filter"></i>
                            </button>
                            <button type="button" title="Export to Excel" data-bs-toggle="modal"
                                data-bs-target="#exportExcelModal" class="btn btn-outline-primary btn-rounded btn-icon">
                                <i class="mdi mdi-file-export"></i>
                            </button>
                        </div>
                        <div class="table-responsive">
                            <table id="myTable" class="table table-bordered text-center">
                                <thead class="table-light">
                                    <tr>
                                        <th>No</th>
                                        <th>Date</th>
                                        <th>Time</th>
                                        <th>Station</th>
                                        <th>PG ID</th>
                                        <th>Error Code</th>
                                        <th>Error Description</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($results as $item)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $item['date'] }}</td>
                                            <td>{{ $item['time'] }}</td>
                                            <td>{{ $item['station_code'] }}</td>
                                            <td>{{ $item['pg_id'] }}</td>
                                            <td>{{ $item['error_code'] }}</td>
                                            <td>{{ $item['description'] }}</td>
                                            <td>
                                                <span
                                                    class="badge {{ $item['status'] === 'Occurring' ? 'bg-danger' : 'bg-success' }}">
                                                    {{ $item['status'] }}
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                    <tr>
                                        <td colspan="8" class="text-muted py-4">
                                            Tidak ada data log yang ditemukan.
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- Filter Modal -->
    <div class="modal fade" id="filterModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Form Filter</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addForm" action="{{ route('log.store') }}" method="POST" class="forms-sample">
                        @csrf
                        @method('POST')
                        <div class="form-group">
                            <label for="station_id" class="required">Station</label>
                            <select class="form-control" name="station_id" id="station_id" required>
                                <option value="" selected disabled>- select station -</option>
                                @foreach ($config_pg as $item)
                                    <option value="{{ $item->station_id }}" @selected($station_id == $item->station_id)>
                                        {{ $item->station_code }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="pg_id" class="required">PG ID</label>
                            <input type="number" class="form-control" id="pg_id" name="pg_id"
                                placeholder="input PG ID" autocomplete="off" min="1" value="{{ $pg_id }}"
                                required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <a href="{{ route('log.index') }}" class="btn btn-gradient-warning">Reset</a>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" form="addForm" class="btn btn-gradient-primary me-2">Submit</button>
                </div>
            </div>
        </div>
    </div>
    <!-- End Filter Modal -->

    <!-- Export Excel Modal -->
    <div class="modal fade" id="exportExcelModal" tabindex="-1" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Are you sure?</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center">
                        <img src="https://i.pinimg.com/originals/1b/db/8a/1bdb8ac897512116cbac58ffe7560d82.png"
                            alt="Excel" style="height: 150px; width: 150px">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" id="exportButton" onclick="exportExcel()"
                        class="btn btn-gradient-success me-2">Download</button>
                </div>
            </div>
        </div>
    </div>
    <!-- End Export Excel Modal -->
@endsection

@section('javascript')
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script>
    function exportExcel() {
        var table = document.getElementById('myTable');
        var wb = XLSX.utils.table_to_book(table, {
            sheet: "Data"
        });
        XLSX.writeFile(wb, 'data_log_passenger_gate.xlsx');
    }
</script>
@endsection
