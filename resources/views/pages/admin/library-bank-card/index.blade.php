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
                            <button type="button" title="Export" class="btn btn-outline-primary btn-rounded btn-icon">
                                <i class="mdi mdi-file-export"></i>
                            </button>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered text-center">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Station</th>
                                        <th>Equipment ID</th>
                                        <th>Direction</th>
                                        <th>Library 6603</th>
                                        <th>Library 6604</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($results as $item)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $item['station_code'] }}</td>
                                            <td>{{ $item['pg_id'] }}</td>
                                            <td>{{ $item['direction'] }}</td>

                                            {{-- Library 6603 --}}
                                            <td class="text-start">
                                                @if (!empty($item['library6603']))
                                                    @foreach (explode(',', $item['library6603']) as $lib)
                                                        <div>{{ trim($lib) }}</div>
                                                    @endforeach
                                                @else
                                                    <div><i>Tidak ada library</i></div>
                                                @endif
                                            </td>

                                            {{-- Library 6604 --}}
                                            <td class="text-start">
                                                @if (!empty($item['library6604']))
                                                    @foreach (explode(',', $item['library6604']) as $lib)
                                                        <div>{{ trim($lib) }}</div>
                                                    @endforeach
                                                @else
                                                    <div><i>Tidak ada library</i></div>
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
@endsection

@section('javascript')
@endsection
