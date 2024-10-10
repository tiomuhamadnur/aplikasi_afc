@extends('layout.base')

@section('title-head')
    <title>Work Order - {{ $work_order->ticket_number }}</title>
    <style>
        table.table td {
            padding: 8px;
        }
    </style>
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Data Work Order</h4>
                        <div class="btn-group my-2">
                            <button type="button" onclick="window.location.href='{{ route('work-order.index') }}'"
                                title="Back" class="btn btn-outline-primary btn-rounded btn-icon">
                                <i class="mdi mdi-arrow-left-bold"></i>
                            </button>
                            <button type="button" title="Filter" class="btn btn-outline-primary btn-rounded btn-icon">
                                <i class="mdi mdi-filter"></i>
                            </button>
                            <button type="button" title="Export" class="btn btn-outline-primary btn-rounded btn-icon">
                                <i class="mdi mdi-file-export"></i>
                            </button>
                        </div>
                        <form action="#" id="formSubmit">
                            <div class="table-responsive">
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <tbody>
                                            <tr class="p-1">
                                                <td style="width: 130px; border-right: none;" class="fw-bolder">WO Date</td>
                                                <td style="width: 10px; border-left: none; border-right: none;">:</td>
                                                <td style="width: 250px; border-left: none;">{{ $work_order->date }}</td>
                                                <td class="text-center" rowspan="2">
                                                    <h2>PT. MRT Jakarta</h2>
                                                    <h4>No: {{ $work_order->ticket_number }}</h4>
                                                </td>
                                                <td class="text-center" rowspan="2" style="width: 330px">
                                                    <img style="border-radius: 0; width: 220px; height: 60px;"
                                                        src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRmOAdOiswyFtDd73NrG0oMhBeZmGW5ySFAmw&s"
                                                        alt="image">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="fw-bolder" style="border-right: none;">Created by</td>
                                                <td style="border-left: none; border-right: none;">:</td>
                                                <td style="border-left: none;">{{ $work_order->user->name ?? 'NA' }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <hr class="my-4">
                                <div>
                                    <h3>1. Detail</h3>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-borderless">
                                        <tbody>
                                            <tr>
                                                <td style="width: 130px" class="fw-bolder">Order</td>
                                                <td style="width: 10px">:</td>
                                                <td style="width: 250px">{{ $work_order->name ?? '-' }}</td>
                                                <td></td>
                                                <td class="fw-bolder" style="width: 130px">Type</td>
                                                <td style="width: 10px">:</td>
                                                <td style="width: 250px">{{ $work_order->tipe_pekerjaan->name ?? '-' }}
                                                    ({{ $work_order->tipe_pekerjaan->code ?? '-' }})</td>
                                            </tr>
                                            <tr>
                                                <td class="fw-bolder">Location</td>
                                                <td>:</td>
                                                <td>{{ $work_order->relasi_area->sub_lokasi->name ?? '-' }}</td>
                                                <td></td>
                                                <td class="fw-bolder">Work Center</td>
                                                <td>:</td>
                                                <td>{{ $work_order->relasi_struktur->departemen->name ?? '-' }}</td>
                                            </tr>
                                            <tr>
                                                <td class="fw-bolder">Priority</td>
                                                <td>:</td>
                                                <td>{{ $work_order->classification->name ?? '-' }}
                                                    ({{ $work_order->classification->id ?? '-' }})</td>
                                                <td></td>
                                                <td class="fw-bolder">No. WO SAP</td>
                                                <td>:</td>
                                                <td>{{ $work_order->wo_number_sap ?? '-' }}</td>
                                            </tr>
                                            <tr class="p-1">
                                                <td class="fw-bolder">Job Start</td>
                                                <td>:</td>
                                                <td style="width: 250px">
                                                    <input type="datetime-local" class="form-control form-control-sm"
                                                        name="start_time" value="{{ $work_order->start_time }}">
                                                </td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <td class="fw-bolder">Job Finish</td>
                                                <td>:</td>
                                                <td>
                                                    <input type="datetime-local" class="form-control form-control-sm"
                                                        name="end_time" value="{{ $work_order->end_time }}">
                                                </td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <td class="fw-bolder">Status</td>
                                                <td>:</td>
                                                <td>
                                                    <select name="status_id" class="form-control">
                                                        <option value="" selected disabled>- pilih status -</option>
                                                        @foreach ($status as $item)
                                                            <option value="{{ $item->id }}"
                                                                @if ($item->id == $work_order->status_id) selected @endif>
                                                                {{ $item->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <hr class="my-4">

                                {{-- EQUIPMENTS --}}
                                <div>
                                    <h3>2. Equipments</h3>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-bordered text-center">
                                        <thead>
                                            <tr>
                                                <th class="fw-bolder" style="width: 10px"> # </th>
                                                <th class="fw-bolder" style="width: 110px"> Checksheet </th>
                                                <th class="fw-bolder"> Equipment Name </th>
                                                <th class="fw-bolder"> Equipment Code </th>
                                                <th class="fw-bolder"> Equipment Number </th>
                                                <th class="fw-bolder"> Type </th>
                                                <th class="fw-bolder"> Status </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if ($work_order->trans_workorder_equipment)
                                                @foreach ($work_order->trans_workorder_equipment as $item)
                                                    <tr>
                                                        <td>{{ $loop->iteration }}</td>
                                                        <td>
                                                            @if ($item->status == null)
                                                                <a href="{{ route('checksheet.create', [
                                                                    'uuid_work_order' => $work_order->uuid,
                                                                    'uuid_equipment' => $item->equipment->uuid,
                                                                ]) }}"
                                                                    title="Input Checksheet">
                                                                    <button type="button"
                                                                        class="btn btn-gradient-primary btn-rounded btn-icon">
                                                                        <i class="mdi mdi-lead-pencil"></i>
                                                                    </button>
                                                                </a>
                                                            @endif
                                                        </td>
                                                        <td class="text-start">{{ $item->equipment->name ?? '-' }}</td>
                                                        <td class="text-start">{{ $item->equipment->code ?? '-' }}</td>
                                                        <td>{{ $item->equipment->equipment_number ?? '-' }}</td>
                                                        <td>{{ $item->equipment->tipe_equipment->code ?? '-' }}</td>
                                                        <td>
                                                            @if ($item->status == null)
                                                                <h1 class="text-danger fw-bolder" title="Incomplete">
                                                                    <i class="mdi mdi-close"></i>
                                                                </h1>
                                                            @else
                                                                <h1 class="text-success fw-bolder"
                                                                    title="{{ $item->status }}">
                                                                    <i class="mdi mdi-check"></i>
                                                                </h1>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @else
                                                <tr>
                                                    <td colspan="7" class="text-center">No data found!</td>
                                                </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                                <hr class="my-4">

                                {{-- MAN POWER --}}
                                <div>
                                    <h3>3. Man Power</h3>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-bordered text-center">
                                        <thead>
                                            <tr>
                                                <th class="fw-bolder" style="width: 10px"> # </th>
                                                <th class="fw-bolder" style="width: 110px"> Select </th>
                                                <th class="fw-bolder"> Name </th>
                                                <th class="fw-bolder"> Role </th>
                                                <th class="fw-bolder"> Employee Type </th>
                                                <th class="fw-bolder"> Company </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($user as $item)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>
                                                        <input type="checkbox" class="form-check-success"
                                                            style="height: 25px; width:25px;"
                                                            @if ($work_order->trans_workorder_user) @foreach ($work_order->trans_workorder_user as $data)
                                                            @if ($item->id == $data->user_id) checked disabled @else  name="user_ids[]"
                                                            value="{{ $item->id }}" @endif
                                                            @endforeach
                                            @endif>
                                            </td>
                                            <td class="text-start">{{ $item->name }}</td>
                                            <td>{{ $item->jabatan->name ?? 'NA' }}</td>
                                            <td class="text-start">{{ $item->tipe_employee->name ?? 'N/A' }}</td>
                                            <td class="text-start">{{ $item->perusahaan->name ?? 'N/A' }}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <hr class="my-4">

                                {{-- DOCUMENTATION --}}
                                <div>
                                    <h3>4. Documentation</h3>
                                </div>
                                <div class="form-group">
                                    <label for="photos">Photos <span class="text-danger">(max: 5)</span></label>
                                    <div class="preview-container" id="prevContainer"></div>
                                    <input type="file" class="form-control image-input" name="photos[]"
                                        id="photos" accept="image/*" multiple>
                                </div>
                                <hr class="my-4">

                                {{-- NOTES --}}
                                <div>
                                    <h3>5. Note</h3>
                                </div>
                                <div class="form-group">
                                    <label for="note">Note</label>
                                    <textarea class="form-control" name="note" id="note" placeholder="Input catatan (optional)" cols="30"
                                        rows="5"></textarea>
                                </div>
                            </div>
                        </form>
                        <div class="form-group d-flex justify-content-end my-5">
                            <button type="submit" form="formSubmit" class="btn btn-primary">Submit</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('javascript')
    <script>
        document.getElementById('photos').addEventListener('change', function(event) {
            const files = event.target.files;
            const prevContainer = document.querySelector('.preview-container');

            prevContainer.innerHTML = '';

            if (files.length > 5) {
                event.target.value = '';
                prevContainer.innerHTML = '';
                swal("Ooopss!", "You can only upload a maximum of 5 photos", "error");
            } else {
                for (let i = 0; i < files.length; i++) {
                    const img = document.createElement('img');
                    img.src = URL.createObjectURL(files[i]);
                    img.className = 'img-thumbnail';
                    img.style.maxWidth = '150px';
                    img.style.margin = '5px';
                    prevContainer.appendChild(img);
                }
            }
        });
    </script>
@endsection
