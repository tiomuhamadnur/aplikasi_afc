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
                            <button type="button" title="Filter" class="btn btn-outline-primary btn-rounded btn-icon"
                                data-bs-toggle="modal" data-bs-target="#filterModal">
                                <i class="mdi mdi-filter"></i>
                            </button>
                            <button type="button" title="Replace Ini File PG"
                                class="btn btn-outline-primary btn-rounded btn-icon" data-bs-toggle="modal"
                                data-bs-target="#replaceModal">
                                <i class="mdi mdi-file-replace"></i>
                            </button>
                            <button type="button" title="Export" class="btn btn-outline-primary btn-rounded btn-icon">
                                <i class="mdi mdi-file-export"></i>
                            </button>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered text-center align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th rowspan="2">No</th>
                                        <th rowspan="2">Station</th>
                                        <th rowspan="2">PG ID</th>
                                        <th rowspan="2">Filename</th>
                                        <th rowspan="2">Direction Type</th>
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
                                    @forelse ($results as $item)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $item['station_code'] }}</td>
                                            <td>{{ $item['pg_name'] }}</td>
                                            <td>
                                                <button type="button" title="Replace Ini File PG"
                                                    class="btn btn-link fw-bold text-primary p-0 m-0 align-baseline"
                                                    data-bs-toggle="modal" data-bs-target="#replaceModal"
                                                    data-filename="{{ $item['actual_filename'] }}"
                                                    data-pg_id="{{ $item['pg_id'] }}" data-pg_name="{{ $item['pg_name'] }}">
                                                    {{ $item['actual_filename'] }}
                                                </button>
                                            </td>
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
                                    @empty
                                        <tr>
                                            <td colspan="44" class="text-muted py-4">
                                                Tidak ada data .ini file yang ditemukan.
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
                    <form id="addForm" action="{{ route('ini-file.store') }}" method="POST" class="forms-sample">
                        @csrf
                        @method('POST')
                        <div class="form-group">
                            <label for="pg_id" class="required">PG ID</label>
                            <select class="tom-select-class" name="pg_id" id="pg_id" required>
                                <option value="" selected disabled>- select PG ID -</option>
                                @foreach ($equipments as $item)
                                    <option value="{{ $item->id }}" @selected($item->id == $pg_id)>
                                        {{ $item->station_code }} {{ $item->equipment_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="type">Type</label>
                            <select class="tom-select-class" name="type" id="type">
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

    <!-- Replace Modal -->
    <div class="modal fade" id="replaceModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Form Replace .ini File PG</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="replaceForm" action="{{ route('ini-file.update') }}" method="POST" class="forms-sample">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="pg_id" id="pg_id_edit">
                        <div class="form-group">
                            <label class="required">PG ID</label>
                            <input type="text" class="form-control" id="pg_name_edit" placeholder="input PG name"
                                autocomplete="off" disabled>
                        </div>
                        <div class="form-group">
                            <label for="filename_edit" class="required">Filename .ini File</label>
                            <input type="text" class="form-control" id="filename_edit" name="filename"
                                placeholder="input filename .ini file" autocomplete="off" required readonly>
                        </div>
                        <div class="form-group">
                            <label for="sam_card_id" class="required">SAM Card</label>
                            <select class="tom-select-class" name="sam_card_id" id="sam_card_id" required>
                                <option value="" selected disabled>- select SAM card -</option>
                                @foreach ($sam_cards as $item)
                                    <option value="{{ $item->id }}">
                                        TID: {{ $item->tid }} | UID: {{ $item->uid }} | PIN: {{ $item->pin }} |
                                        MC: {{ $item->mc }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <a href="{{ route('ini-file.index') }}" class="btn btn-gradient-warning">Reset</a>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" form="replaceForm" class="btn btn-gradient-primary me-2">Submit</button>
                </div>
            </div>
        </div>
    </div>
    <!-- End Replace Modal -->
@endsection

@section('javascript')
    <script>
        $(document).ready(function() {
            $('#replaceModal').on('show.bs.modal', function(e) {
                var pg_id = $(e.relatedTarget).data('pg_id');
                var pg_name = $(e.relatedTarget).data('pg_name');
                var filename = $(e.relatedTarget).data('filename');

                $('#pg_id_edit').val(pg_id);
                $('#pg_name_edit').val(pg_name);
                $('#filename_edit').val(filename);
            });
        });
    </script>
@endsection
