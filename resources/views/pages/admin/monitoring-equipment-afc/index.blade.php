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
                            <button type="button" title="Monitoring SCU" class="btn btn-outline-primary btn-rounded btn-icon"
                                data-bs-toggle="modal" data-bs-target="#scuModal">
                                <i class="mdi mdi-desktop-classic"></i>
                            </button>
                            <button type="button" title="Monitoring PG"
                                class="btn btn-outline-primary btn-rounded btn-icon" data-bs-toggle="modal"
                                data-bs-target="#pgModal">
                                <i class="mdi mdi-boom-gate"></i>
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
                                        <th rowspan="2">Core Temperature</th>
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
                                                <span
                                                    class="badge {{ $item['status'] === 'online' ? 'bg-success' : 'bg-danger' }}">
                                                    {{ $item['status'] }}
                                                </span>
                                            </td>
                                            <td>{{ $item['uptime'] }}</td>
                                            <td>{{ $item['load_average']['1m'] }}</td>
                                            <td>{{ $item['load_average']['5m'] }}</td>
                                            <td>{{ $item['load_average']['15m'] }}</td>
                                            <td>
                                                <span
                                                    class="badge
                                                    @if ($item['load_average']['status'] === 'normal') bg-success
                                                    @elseif($item['load_average']['status'] === 'busy') bg-warning
                                                    @else bg-danger @endif">
                                                    {{ ucfirst($item['load_average']['status']) }}
                                                </span>
                                            </td>
                                            <td>{{ $item['ram']['used'] }} / {{ $item['ram']['total'] }}
                                                ({{ $item['ram']['percent'] }}%)</td>
                                            <td>{{ $item['disk_root']['used'] }} / {{ $item['disk_root']['total'] }}
                                                ({{ $item['disk_root']['percent'] }}%)</td>
                                            <td>{{ $item['cpu_cores'] }}</td>
                                            <td>
                                                @if (!empty($item['core_temperatures']))
                                                    <ul style="font-size: 0.75rem; padding-left: 1rem;">
                                                        @foreach ($item['core_temperatures'] as $label => $temp)
                                                            <li>
                                                                {{ $label }}:
                                                                <span
                                                                    class="{{ (float) $temp > 70 ? 'text-danger' : 'text-success' }}">
                                                                    {{ $temp }}
                                                                </span>
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                @else
                                                    <span>-</span>
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


    <!-- SCU Modal -->
    <div class="modal fade" id="scuModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Form Monitoring SCU</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addForm" action="{{ route('monitoring-equipment-afc.store') }}" method="POST"
                        class="forms-sample">
                        @csrf
                        @method('POST')
                        <div class="form-group">
                            <label for="scu_id" class="required">Station</label>
                            <select class="form-control" name="scu_id" id="scu_id" required>
                                <option value="" selected disabled>- select station -</option>
                                <option value="all">All Station</option>
                                @foreach ($scu as $item)
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
    <!-- End SCU Modal -->

    <!-- PG Modal -->
    <div class="modal fade" id="pgModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Form Filter</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addForm" action="{{ route('monitoring-equipment-afc.store_pg') }}" method="POST"
                        class="forms-sample">
                        @csrf
                        @method('POST')
                        <div class="form-group">
                            <label for="station_code">Station</label>
                            <select class="form-control" name="station_code" id="station_code">
                                {{-- <option value="" selected disabled>- select station -</option> --}}
                                <option value="all" selected>All Station</option>
                                @foreach ($scu as $item)
                                    <option value="{{ $item->station_code }}">
                                        {{ $item->station_code }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="pg_id">PG ID</label>
                            <select class="tom-select-class" name="pg_id" id="pg_id">
                                {{-- <option value="" selected disabled>- select station -</option> --}}
                                <option value="all" selected>All PG</option>
                                @foreach ($pg as $item)
                                    <option value="{{ $item->id }}">
                                        {{ $item->station_code }} {{ $item->equipment_name }}
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
    <!-- End PG Modal -->
@endsection

@section('javascript')
@endsection
