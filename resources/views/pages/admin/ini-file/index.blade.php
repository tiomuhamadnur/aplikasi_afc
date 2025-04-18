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
                                        <th rowspan="2">Filename</th>
                                        <th rowspan="2">Location</th>
                                        <th rowspan="2">ConfigVersion</th>
                                        <th rowspan="2">CreatedOn</th>
                                        <th colspan="6">Mandiri</th>
                                        <th colspan="5">BNI</th>
                                        <th colspan="6">BCA</th>
                                        <th colspan="5">BRI</th>
                                        <th colspan="5">DKI2</th>
                                        <th colspan="4">MEGA2</th>
                                        <th colspan="6">NOBU</th>
                                    </tr>
                                    <tr>
                                        {{-- Mandiri --}}
                                        <th>BankActive</th>
                                        <th>TID</th>
                                        <th>MID</th>
                                        <th>Samslot</th>
                                        <th>PIN</th>
                                        <th>IID</th>

                                        {{-- BNI --}}
                                        <th>BankActive</th>
                                        <th>TID</th>
                                        <th>MID</th>
                                        <th>Samslot</th>
                                        <th>MC</th>

                                        {{-- BCA --}}
                                        <th>BankActive</th>
                                        <th>TID</th>
                                        <th>MID</th>
                                        <th>Samslot</th>
                                        <th>MinBalance</th>
                                        <th>Merchant Key</th>

                                        {{-- BRI --}}
                                        <th>BankActive</th>
                                        <th>TID</th>
                                        <th>MID</th>
                                        <th>Samslot</th>
                                        <th>Proccode</th>

                                        {{-- DKI2 --}}
                                        <th>BankActive</th>
                                        <th>TID</th>
                                        <th>MID</th>
                                        <th>Samslot</th>
                                        <th>CardStatus</th>

                                        {{-- MEGA2 --}}
                                        <th>BankActive</th>
                                        <th>TID</th>
                                        <th>MID</th>
                                        <th>Samslot</th>

                                        {{-- NOBU --}}
                                        <th>BankActive</th>
                                        <th>TID</th>
                                        <th>MID</th>
                                        <th>Samslot</th>
                                        <th>token</th>
                                        <th>area_key</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($results as $item)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $item['actual_filename'] }}</td>
                                            <td>{{ $item['location'] }}</td>
                                            <td>{{ $item['config_version'] }}</td>
                                            <td>{{ $item['created_on'] }}</td>

                                            {{-- Mandiri --}}
                                            <td>{{ $item['Mandiri']['BankActive'] ? 'true' : 'false' }}</td>
                                            <td>{{ $item['Mandiri']['tid'] ?? '-' }}</td>
                                            <td>{{ $item['Mandiri']['mid'] ?? '-' }}</td>
                                            <td>{{ $item['Mandiri']['samslot'] ?? '-' }}</td>
                                            <td>{{ $item['Mandiri']['pin'] ?? '-' }}</td>
                                            <td>{{ $item['Mandiri']['iid'] ?? '-' }}</td>

                                            {{-- BNI --}}
                                            <td>{{ $item['BNI']['BankActive'] ? 'true' : 'false' }}</td>
                                            <td>{{ $item['BNI']['tid'] ?? '-' }}</td>
                                            <td>{{ $item['BNI']['mid'] ?? '-' }}</td>
                                            <td>{{ $item['BNI']['samslot'] ?? '-' }}</td>
                                            <td>{{ $item['BNI']['mc'] ?? '-' }}</td>

                                            {{-- BCA --}}
                                            <td>{{ $item['BCA']['BankActive'] ? 'true' : 'false' }}</td>
                                            <td>{{ $item['BCA']['tid'] ?? '-' }}</td>
                                            <td>{{ $item['BCA']['mid'] ?? '-' }}</td>
                                            <td>{{ $item['BCA']['samslot'] ?? '-' }}</td>
                                            <td>{{ $item['BCA']['minbalance'] ?? '-' }}</td>
                                            <td>{{ $item['BCA']['merchant_key'] ?? '-' }}</td>

                                            {{-- BRI --}}
                                            <td>{{ $item['BRI']['BankActive'] ? 'true' : 'false' }}</td>
                                            <td>{{ $item['BRI']['tid'] ?? '-' }}</td>
                                            <td>{{ $item['BRI']['mid'] ?? '-' }}</td>
                                            <td>{{ $item['BRI']['samslot'] ?? '-' }}</td>
                                            <td>{{ $item['BRI']['proccode'] ?? '-' }}</td>

                                            {{-- DKI2 --}}
                                            <td>{{ $item['DKI2']['BankActive'] ? 'true' : 'false' }}</td>
                                            <td>{{ $item['DKI2']['tid'] ?? '-' }}</td>
                                            <td>{{ $item['DKI2']['mid'] ?? '-' }}</td>
                                            <td>{{ $item['DKI2']['samslot'] ?? '-' }}</td>
                                            <td>{{ $item['DKI2']['card_status'] ?? '-' }}</td>

                                            {{-- MEGA2 --}}
                                            <td>{{ $item['MEGA2']['BankActive'] ? 'true' : 'false' }}</td>
                                            <td>{{ $item['MEGA2']['tid'] ?? '-' }}</td>
                                            <td>{{ $item['MEGA2']['mid'] ?? '-' }}</td>
                                            <td>{{ $item['MEGA2']['samslot'] ?? '-' }}</td>

                                            {{-- NOBU --}}
                                            <td>{{ $item['NOBU']['BankActive'] ? 'true' : 'false' }}</td>
                                            <td>{{ $item['NOBU']['tid'] ?? '-' }}</td>
                                            <td>{{ $item['NOBU']['mid'] ?? '-' }}</td>
                                            <td>{{ $item['NOBU']['samslot'] ?? '-' }}</td>
                                            <td>{{ $item['NOBU']['token'] ?? '-' }}</td>
                                            <td>{{ $item['NOBU']['area_key'] ?? '-' }}</td>
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
                        {{-- <div class="form-group">
                            <label for="host" class="required">Host</label>
                            <input type="text" class="form-control" id="host" name="host"
                                placeholder="input host/ip address" value="{{ $host }}" autocomplete="off" required>
                        </div> --}}
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
                        <div class="form-group">
                            <label for="type">Type</label>
                            <select class="form-control" name="type" id="type">
                                <option value="" selected disabled>- select type -</option>
                                <option value="Paid" @selected($type == 'Paid')>Paid</option>
                                <option value="UnPaid" @selected($type == 'UnPaid')>UnPaid</option>
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
