@extends('layout.base')

@section('title-head')
    <title>Admin | Monitoring Equipment AFC</title>
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Data Monitoring Equipment AFC</h4>
                        <div class="btn-group my-2">
                            <button type="button" title="Filter" class="btn btn-outline-primary btn-rounded btn-icon"
                                data-bs-toggle="modal" data-bs-target="#filterModal">
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
                                        <th rowspan="2">No</th>
                                        <th rowspan="2">Equipment Type</th>
                                        <th rowspan="2">Station</th>
                                        <th rowspan="2">IP Address</th>
                                        <th rowspan="2">Status</th>
                                        <th rowspan="2">Uptime</th>
                                        <th colspan="4">Load Average</th>
                                        <th rowspan="2">RAM Usage</th>
                                        <th rowspan="2">Disk Root Usage</th>
                                        <th rowspan="2">CPU Core</th>
                                    </tr>
                                    <tr>
                                        <th>1 min</th>
                                        <th>5 min</th>
                                        <th>15 min</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($results as $index => $item)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $item['equipment_type_code'] }}</td>
                                            <td>{{ $item['station_code'] }}</td>
                                            <td>{{ $item['ip'] }}</td>
                                            <td>
                                                <span class="badge {{ $item['status'] === 'online' ? 'bg-success' : 'bg-danger' }}">
                                                    {{ $item['status'] }}
                                                </span>
                                            </td>
                                            <td>{{ $item['uptime'] }}</td>
                                            <td>{{ $item['load_average']['1m'] }}</td>
                                            <td>{{ $item['load_average']['5m'] }}</td>
                                            <td>{{ $item['load_average']['15m'] }}</td>
                                            <td>
                                                <span class="badge
                                                    @if($item['load_average']['status'] === 'normal') bg-success
                                                    @elseif($item['load_average']['status'] === 'busy') bg-warning
                                                    @else bg-danger @endif">
                                                    {{ ucfirst($item['load_average']['status']) }}
                                                </span>
                                            </td>
                                            <td>{{ $item['ram']['used'] }} / {{ $item['ram']['total'] }} ({{ $item['ram']['percent'] }}%)</td>
                                            <td>{{ $item['disk_root']['used'] }} / {{ $item['disk_root']['total'] }} ({{ $item['disk_root']['percent'] }}%)</td>
                                            <td>{{ $item['cpu_cores'] }}</td>
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


    <!-- Filter Modal -->
    <div class="modal fade" id="filterModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Form Filter</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addForm" action="{{ route('monitoring-equipment-afc.store') }}" method="POST" class="forms-sample">
                        @csrf
                        @method('POST')
                        <div class="form-group">
                            <label for="scu_id" class="required">SCU Station</label>
                            <select class="form-control" name="scu_id" id="scu_id" required>
                                <option value="" selected disabled>- select station -</option>
                                <option value="all">All Station</option>
                                @foreach ($config_equipment_afc as $item)
                                    <option value="{{ $item->id }}">
                                        {{ $item->station_code }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <a href="{{ route('monitoring-equipment-afc.index') }}" class="btn btn-gradient-warning">Reset</a>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" form="addForm" class="btn btn-gradient-primary me-2">Submit</button>
                </div>
            </div>
        </div>
    </div>
    <!-- End Filter Modal -->
@endsection

@section('javascript')
@endsection
