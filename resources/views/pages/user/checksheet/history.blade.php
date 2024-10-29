@extends('layout.base')

@section('title-head')
    <title>History Data Checksheet</title>
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">History Data Checksheet
                            ({{ $equipment->name ?? '' }}{{ $functional_location->name ?? '' }})
                        </h4>
                        {{-- <h5 class="card-title">({{ $equipment->code }} - {{ $form->name }})</h5> --}}
                        <div class="btn-group my-2">
                            {{-- <button type="button" onclick="window.location.href='{{ route('work-order.equipment') }}'"
                                title="Back" class="btn btn-outline-primary btn-rounded btn-icon">
                                <i class="mdi mdi-arrow-left"></i>
                            </button> --}}
                            <button type="button" title="Filter" class="btn btn-outline-primary btn-rounded btn-icon">
                                <i class="mdi mdi-filter"></i>
                            </button>
                            <button type="button" title="Export" data-bs-toggle="modal" data-bs-target="#exportExcelModal"
                                class="btn btn-outline-primary btn-rounded btn-icon">
                                <i class="mdi mdi-file-export"></i>
                            </button>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered text-center">
                                <thead>
                                    <tr>
                                        <th class="fw-bolder"> # </th>
                                        <th class="fw-bolder">Date</th>
                                        @foreach ($parameters as $parameter)
                                            <th class="fw-bolder">
                                                {{ $parameter->name }}
                                                @if ($parameter->tipe == 'number')
                                                    ({{ $parameter->satuan->code ?? '-' }})
                                                    <br>
                                                    ({{ $parameter->min_value }} - {{ $parameter->max_value }})
                                                    <br>
                                                    <a href="{{ route('checksheet.trend', [
                                                        'eq_uuid' => $equipment->uuid ?? null,
                                                        'funloc_uuid' => $functional_location->uuid ?? null,
                                                        'param_uuid' => $parameter->uuid ?? null,
                                                    ]) }}"
                                                        target="_blank" title="Plot Graph">
                                                        <button type="button"
                                                            class="btn btn-gradient-success btn-rounded btn-icon">
                                                            <i class="mdi mdi-chart-areaspline"></i>
                                                        </button>
                                                    </a>
                                                @endif
                                            </th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($pivotData as $date => $values)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $date }}</td>
                                            @foreach ($parameters as $param)
                                                @php
                                                    $value = $values->get($param->name, '-');
                                                    $isOutOfRange =
                                                        is_numeric($value) &&
                                                        ($value < $param->min_value || $value > $param->max_value);
                                                @endphp
                                                <td
                                                    @if ($isOutOfRange) title="Out of Tolerance" class="fw-bolder" style="background-color: #ff6969" @endif>
                                                    {{ $value }}
                                                </td>
                                            @endforeach
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
                    <form hidden id="formExportExcel"
                        action="{{ route('checksheet.history.export.excel', ['uuid_equipment' => $equipment->uuid ?? null, 'uuid_functional_location' => $functional_location->uuid ?? null]) }}"
                        method="POST">
                        @csrf
                        @method('PUT')
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" form="formExportExcel" class="btn btn-gradient-success me-2">Download</button>
                </div>
            </div>
        </div>
    </div>
    <!-- End Export Excel Modal -->
@endsection


@section('javascript')
@endsection
