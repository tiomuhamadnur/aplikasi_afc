@extends('layout.base')

@section('title-head')
    <title>Admin | Library Bank Card</title>
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Data Library Bank Card</h4>
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
                                        <th>Station</th>
                                        <th>Equipment</th>
                                        <th>Direction</th>
                                        <th>Library Master (6603.txt)</th>
                                        <th>Library Slave (6604.txt)</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($results as $index => $item)
                                        <tr class="@if ($item['status'] === 'offline') table-danger @endif">
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $item['station_code'] ?? '-' }}</td>
                                            <td>{{ $item['pg_id'] ?? '-' }}</td>
                                            <td>{{ $item['direction'] ?? '-' }}</td>

                                            {{-- Library 6603 --}}
                                            <td class="text-start">
                                                @if ($item['status'] === 'offline')
                                                    <span class="text-danger">Server Offline</span>
                                                @elseif(empty($item['library6603']))
                                                    <div><i>-</i></div>
                                                @else
                                                    @foreach (explode(',', $item['library6603']) as $lib)
                                                        @php
                                                            $lib = trim($lib);
                                                            $class = '';
                                                            $content = e($lib); // Escape by default

                                                            if (str_contains($lib, 'DKI2:')) {
                                                                $class = 'badge bg-success';
                                                            } elseif (str_contains($lib, 'MEGA2:')) {
                                                                $class = 'badge bg-primary';
                                                            } elseif (str_contains($lib, 'Error')) {
                                                                $class = 'text-danger';
                                                            }
                                                        @endphp

                                                        @if ($class)
                                                            <span class="{{ $class }}">{{ $content }}</span>
                                                        @else
                                                            {{ $content }}
                                                        @endif
                                                        <br>
                                                    @endforeach
                                                @endif
                                            </td>

                                            {{-- Library 6604 --}}
                                            <td class="text-start">
                                                @if ($item['status'] === 'offline')
                                                    <span class="text-danger">Server Offline</span>
                                                @elseif(empty($item['library6604']))
                                                    <div><i>-</i></div>
                                                @else
                                                    @foreach (explode(',', $item['library6604']) as $lib)
                                                        @php
                                                            $lib = trim($lib);
                                                            $class = '';
                                                            $content = e($lib); // Escape by default

                                                            if (str_contains($lib, 'DKI2:')) {
                                                                $class = 'badge bg-success';
                                                            } elseif (str_contains($lib, 'MEGA2:')) {
                                                                $class = 'badge bg-primary';
                                                            } elseif (str_contains($lib, 'Error')) {
                                                                $class = 'text-danger';
                                                            }
                                                        @endphp

                                                        @if ($class)
                                                            <span class="{{ $class }}">{{ $content }}</span>
                                                        @else
                                                            {{ $content }}
                                                        @endif
                                                        <br>
                                                    @endforeach
                                                @endif
                                            </td>

                                            <td>
                                                @if ($item['status'] === 'online')
                                                    <span class="badge bg-success">Online</span>
                                                @else
                                                    <span class="badge bg-danger">Offline</span>
                                                    @if (!empty($item['error']))
                                                        <br><small
                                                            class="text-muted">{{ Str::limit($item['error'], 30) }}</small>
                                                    @endif
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-muted py-4">
                                                No bank card library data found.
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
                    <form id="addForm" action="{{ route('library-bank-card.store') }}" method="POST"
                        class="forms-sample">
                        @csrf
                        @method('POST')
                        <div class="form-group">
                            <label for="station_id" class="required">Station</label>
                            <select class="form-control" name="station_id" id="station_id" required>
                                <option value="all" selected>all station</option>
                                @foreach ($stations as $item)
                                    <option value="{{ $item->station_code }}">
                                        {{ $item->station_code }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="pg_id" class="required">PG</label>
                            <select class="tom-select-class" name="pg_id" id="pg_id" required>
                                <option value="all" selected>all PG</option>
                                @foreach ($pgs as $item)
                                    <option value="{{ $item->id }}">
                                        {{ $item->station_code }} {{ $item->equipment_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <a href="{{ route('library-bank-card.index') }}" class="btn btn-gradient-warning">Reset</a>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" form="addForm" class="btn btn-gradient-primary me-2">Submit</button>
                </div>
            </div>
        </div>
    </div>
    <!-- End Filter Modal -->

    <!-- Export Excel Modal -->
    <div class="modal fade" id="exportExcelModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
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
            XLSX.writeFile(wb, 'library_bank_card_version.xlsx');
        }
    </script>
@endsection
