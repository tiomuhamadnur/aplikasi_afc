@extends('layout.base')

@section('title-head')
    <title>Admin | Monitoring Equipment AFC</title>
    <style>
        .table td {
            vertical-align: middle;
        }

        .progress {
            min-width: 50px;
        }
    </style>
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
                            <button type="button" title="Export to Excel" data-bs-toggle="modal"
                                data-bs-target="#exportExcelModal" class="btn btn-outline-primary btn-rounded btn-icon">
                                <i class="mdi mdi-file-export"></i>
                            </button>
                        </div>
                        <div class="table-responsive">
                            <table id="myTable" class="table table-bordered text-center align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th rowspan="2">#</th>
                                        <th rowspan="2">Type</th>
                                        <th rowspan="2">Station</th>
                                        <th rowspan="2">Equipment</th>
                                        <th rowspan="2">Corner</th>
                                        <th rowspan="2">IP Address</th>
                                        <th rowspan="2">Status</th>
                                        <th rowspan="2">Uptime</th>
                                        <th colspan="4">Load Average</th>
                                        <th rowspan="2">RAM</th>
                                        <th rowspan="2">Disk</th>
                                        <th rowspan="2">Cores</th>
                                        <th rowspan="2">Temperatures</th>
                                    </tr>
                                    <tr>
                                        <th>1m</th>
                                        <th>5m</th>
                                        <th>15m</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($results as $item)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $item['equipment_type_code'] }}</td>
                                            <td>{{ $item['station_code'] }}</td>
                                            <td>{{ $item['equipment_name'] }}</td>
                                            <td>{{ $item['corner_id'] }}</td>
                                            <td class="font-monospace">{{ $item['ip'] }}</td>

                                            <td>
                                                <span
                                                    class="badge bg-{{ $item['status'] === 'online' ? 'success' : 'danger' }}">
                                                    {{ ucfirst($item['status']) }}
                                                </span>
                                            </td>

                                            <td>{{ $item['uptime'] }}</td>

                                            <td>{{ number_format($item['load_average']['1m'], 2) }}</td>
                                            <td>{{ number_format($item['load_average']['5m'], 2) }}</td>
                                            <td>{{ number_format($item['load_average']['15m'], 2) }}</td>
                                            <td>
                                                @php
                                                    $loadStatusColor = match ($item['load_average']['status']) {
                                                        'normal' => 'success',
                                                        'busy' => 'warning',
                                                        default => 'danger',
                                                    };
                                                @endphp
                                                <span class="badge bg-{{ $loadStatusColor }}">
                                                    {{ ucfirst($item['load_average']['status']) }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="d-flex flex-column small">
                                                    <div class="text-nowrap">{{ $item['ram']['used'] }} /
                                                        {{ $item['ram']['total'] }}</div>
                                                    @isset($item['ram']['percent'])
                                                        <div class="progress mt-1" style="height: 3px;">
                                                            <div class="progress-bar
                                                                @if ($item['ram']['percent'] > 90) bg-danger
                                                                @elseif($item['ram']['percent'] > 70) bg-warning
                                                                @else bg-success @endif"
                                                                style="width: {{ $item['ram']['percent'] }}%"
                                                                role="progressbar"
                                                                aria-valuenow="{{ $item['ram']['percent'] }}" aria-valuemin="0"
                                                                aria-valuemax="100">
                                                            </div>
                                                        </div>
                                                    @else
                                                        <span class="text-muted small">Data tidak tersedia</span>
                                                    @endisset
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex flex-column small">
                                                    <div class="text-nowrap">{{ $item['disk_root']['used'] }} /
                                                        {{ $item['disk_root']['total'] }}</div>
                                                    <div class="progress mt-1" style="height: 3px;">
                                                        <div class="progress-bar
                                                            @if ($item['disk_root']['percent'] > 90) bg-danger
                                                            @elseif($item['disk_root']['percent'] > 70) bg-warning
                                                            @else bg-success @endif"
                                                            style="width: {{ $item['disk_root']['percent'] }}%">
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>{{ $item['cpu_cores'] }}</td>
                                            <td>
                                                @if (!empty($item['core_temperatures']))
                                                    <div class="d-flex flex-wrap gap-2 justify-content-center">
                                                        @foreach ($item['core_temperatures'] as $index => $temp)
                                                            @php
                                                                $tempValue = (float) $temp;
                                                                $tempColor =
                                                                    $tempValue > 75
                                                                        ? 'danger'
                                                                        : ($tempValue > 60
                                                                            ? 'warning'
                                                                            : 'success');
                                                            @endphp

                                                            <span
                                                                class="badge bg-{{ $tempColor }}-subtle text-{{ $tempColor }} d-inline-flex align-items-center">
                                                                <span class="me-1">Core {{ $index + 1 }}</span>
                                                                <span
                                                                    class="fw-bold">{{ number_format($tempValue, 1) }}°C</span>
                                                            </span>
                                                        @endforeach
                                                    </div>
                                                @else
                                                    <span class="text-muted">N/A</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="16" class="text-muted py-4">
                                                No equipment data available
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


    <!-- SCU Modal -->
    <div class="modal fade" id="scuModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Form Monitoring SCU</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="scuForm" action="{{ route('monitoring-equipment-afc.store') }}" method="POST"
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
                    <button type="submit" form="scuForm" class="btn btn-gradient-primary me-2">Submit</button>
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
                    <h5 class="modal-title" id="exampleModalLabel">Form Monitorng PG</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="pgForm" action="{{ route('monitoring-equipment-afc.store_pg') }}" method="POST"
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
                    <button type="submit" form="pgForm" class="btn btn-gradient-primary me-2">Submit</button>
                </div>
            </div>
        </div>
    </div>
    <!-- End PG Modal -->

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
        XLSX.writeFile(wb, 'monitoring_equipment_afc.xlsx');
    }
</script>
@endsection
