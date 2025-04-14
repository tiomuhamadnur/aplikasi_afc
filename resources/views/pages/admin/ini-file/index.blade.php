@extends('layout.base')

@section('title-head')
    <title>Admin | .ini File</title>
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Data .ini File</h4>
                        <div class="btn-group my-2">
                            {{-- <button type="button" title="Search" class="btn btn-outline-primary btn-rounded btn-icon"
                                data-bs-toggle="modal" data-bs-target="#searchModal">
                                <i class="mdi mdi-plus-circle"></i>
                            </button> --}}
                            <button type="button" title="Filter" class="btn btn-outline-primary btn-rounded btn-icon" data-bs-toggle="modal" data-bs-target="#filterModal">
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
                                        <th rowspan="2">Filename</th>
                                        <th rowspan="2">Location</th>
                                        <th colspan="6">BCA</th>
                                        <th colspan="6">Mandiri</th>
                                        <th colspan="6">BRI</th>
                                        <th colspan="6">BNI</th>
                                    </tr>
                                    <tr>
                                        <th>BankActive</th>
                                        <th>TID</th>
                                        <th>MID</th>
                                        <th>Samslot</th>
                                        <th>MinBalance</th>
                                        <th>Merchant Key</th>

                                        <th>BankActive</th>
                                        <th>TID</th>
                                        <th>MID</th>
                                        <th>Samslot</th>
                                        <th>PIN</th>
                                        <th>IID</th>

                                        <th>BankActive</th>
                                        <th>TID</th>
                                        <th>MID</th>
                                        <th>Samslot</th>
                                        <th>Proccode</th>

                                        <th>BankActive</th>
                                        <th>TID</th>
                                        <th>MID</th>
                                        <th>Samslot</th>
                                        <th>MC</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($results as $item)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $item['actual_filename'] }}</td>
                                            <td>{{ $item['location'] }}</td>

                                            {{-- BCA --}}
                                            <td>{{ $item['BCA']['BankActive'] ? 'true' : 'false' }}</td>
                                            <td>{{ $item['BCA']['tid'] ?? '-' }}</td>
                                            <td>{{ $item['BCA']['mid'] ?? '-' }}</td>
                                            <td>{{ $item['BCA']['samslot'] ?? '-' }}</td>
                                            <td>{{ $item['BCA']['minbalance'] ?? '-' }}</td>
                                            <td>{{ $item['BCA']['merchant_key'] ?? '-' }}</td>

                                            {{-- Mandiri --}}
                                            <td>{{ $item['Mandiri']['BankActive'] ? 'true' : 'false' }}</td>
                                            <td>{{ $item['Mandiri']['tid'] ?? '-' }}</td>
                                            <td>{{ $item['Mandiri']['mid'] ?? '-' }}</td>
                                            <td>{{ $item['Mandiri']['samslot'] ?? '-' }}</td>
                                            <td>{{ $item['Mandiri']['pin'] ?? '-' }}</td>
                                            <td>{{ $item['Mandiri']['iid'] ?? '-' }}</td>

                                            {{-- BRI --}}
                                            <td>{{ $item['BRI']['BankActive'] ? 'true' : 'false' }}</td>
                                            <td>{{ $item['BRI']['tid'] ?? '-' }}</td>
                                            <td>{{ $item['BRI']['mid'] ?? '-' }}</td>
                                            <td>{{ $item['BRI']['samslot'] ?? '-' }}</td>
                                            <td>{{ $item['BRI']['proccode'] ?? '-' }}</td>

                                            {{-- BNI --}}
                                            <td>{{ $item['BNI']['BankActive'] ? 'true' : 'false' }}</td>
                                            <td>{{ $item['BNI']['tid'] ?? '-' }}</td>
                                            <td>{{ $item['BNI']['mid'] ?? '-' }}</td>
                                            <td>{{ $item['BNI']['samslot'] ?? '-' }}</td>
                                            <td>{{ $item['BNI']['mc'] ?? '-' }}</td>
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
                    <form id="addForm" action="{{ route('ini-file.store') }}" method="POST" class="forms-sample">
                        @csrf
                        @method('POST')
                        <div class="form-group">
                            <label for="host" class="required">Host</label>
                            <input type="text" class="form-control" id="host" name="host"
                                placeholder="input host/ip address" value="{{ $host }}" autocomplete="off" required>
                        </div>
                        <div class="form-group">
                            <label for="station_id">Station</label>
                            <select class="form-control" name="station_id" id="station_id">
                                <option value="" selected disabled>- select station -</option>
                                <option value="101" @selected($station_id == "101")>LBB</option>
                                <option value="105" @selected($station_id == "105")>FTM</option>
                                <option value="109" @selected($station_id == "109")>CPR</option>
                                <option value="113" @selected($station_id == "113")>HJN</option>
                                <option value="117" @selected($station_id == "117")>BLA</option>
                                <option value="121" @selected($station_id == "121")>BLM</option>
                                <option value="125" @selected($station_id == "125")>ASN</option>
                                <option value="129" @selected($station_id == "129")>SNY</option>
                                <option value="133" @selected($station_id == "133")>IST</option>
                                <option value="137" @selected($station_id == "137")>BNH</option>
                                <option value="141" @selected($station_id == "141")>STB</option>
                                <option value="145" @selected($station_id == "145")>DKA</option>
                                <option value="149" @selected($station_id == "149")>BHI</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="pg_id">PG ID</label>
                            <input type="number" class="form-control" id="pg_id" name="pg_id"
                                placeholder="input PG ID" autocomplete="off" min="1" value="{{ $pg_id }}">
                        </div>
                        <div class="form-group">
                            <label for="type">Type</label>
                            <select class="form-control" name="type" id="type">
                                <option value="" selected disabled>- select type -</option>
                                <option value="Paid" @selcted($type == "Paid")>Paid</option>
                                <option value="UnPaid" @selcted($type == "UnPaid")>UnPaid</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <a href="{{ route('ini-file.index') }}" class="btn btn-gradient-warning">Reset</a>
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
