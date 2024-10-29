@extends('layout.base')

@section('title-head')
    <title>Edit Work Order - {{ $work_order->ticket_number }}</title>
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
                        <h4 class="card-title">Edit Work Order</h4>
                        <div class="btn-group my-2">
                            <div class="btn-group my-2">
                                <a href="{{ route('work-order.index') }}" title="Back" class="btn btn-primary btn-rounded">
                                    <i class="mdi mdi-arrow-left"></i> Back
                                </a>
                            </div>
                        </div>
                        <form action="{{ route('work-order.update') }}" method="POST" id="formSubmit">
                            @csrf
                            @method('PUT')
                            <input type="text" name="uuid" value="{{ $work_order->uuid }}" required hidden>
                            <div class="table-responsive">
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <tbody>
                                            <tr class="p-1">
                                                <td style="width: 130px; border-right: none;" class="fw-bolder">WO Date</td>
                                                <td style="width: 10px; border-left: none; border-right: none;">:</td>
                                                <td style="width: 250px; border-left: none;">
                                                    <input type="date" class="form-control form-control-sm"
                                                        name="date" id="date" value="{{ $work_order->date ?? '' }}">
                                                </td>
                                                <td class="text-center" rowspan="2">
                                                    <h2>PT. MRT Jakarta</h2>
                                                </td>
                                                <td class="text-center" rowspan="2" style="width: 330px">
                                                    <img style="border-radius: 0; width: 220px; height: 60px;"
                                                        src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRmOAdOiswyFtDd73NrG0oMhBeZmGW5ySFAmw&s"
                                                        alt="image">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="fw-bolder" style="border-right: none;">Create by</td>
                                                <td style="border-left: none; border-right: none;">:</td>
                                                <td style="border-left: none;">
                                                    <input type="text" class="form-control form-control-sm"
                                                        autocomplete="off" value="{{ $work_order->user->name ?? 'N/A' }}"
                                                        disabled>
                                                </td>
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
                                                <td style="width: 130px" class="fw-bolder">Order Name</td>
                                                <td style="width: 10px">:</td>
                                                <td style="width: 250px">
                                                    <input type="text" class="form-control form-control-sm"
                                                        placeholder="input order name" name="name" id="name"
                                                        value="{{ $work_order->name ?? '' }}" autocomplete="off">
                                                </td>
                                                <td></td>
                                                <td class="fw-bolder" style="width: 130px">Location</td>
                                                <td style="width: 10px">:</td>
                                                <td style="width: 260px">
                                                    <input type="text" class="form-control form-control-sm"
                                                        value="{{ $work_order->relasi_area->sub_lokasi->name ?? '-' }}"
                                                        disabled>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="fw-bolder">Description</td>
                                                <td>:</td>
                                                <td>
                                                    <input type="text" class="form-control form-control-sm"
                                                        placeholder="input description" name="description" id="description"
                                                        value="{{ $work_order->description ?? '' }}">
                                                </td>
                                                <td></td>
                                                <td class="fw-bolder">Work Center</td>
                                                <td>:</td>
                                                <td>
                                                    <input type="text" class="form-control form-control-sm"
                                                        value="{{ $work_order->relasi_struktur->departemen->name ?? '-' }}"
                                                        disabled>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="fw-bolder">Type</td>
                                                <td>:</td>
                                                <td>
                                                    <select class="form-control form-control-sm" name="tipe_pekerjaan_id"
                                                        id="tipe_pekerjaan_id" required>
                                                        <option value="" selected disabled>- select order type -
                                                        </option>
                                                        @foreach ($tipe_pekerjaan as $item)
                                                            <option value="{{ $item->id }}"
                                                                @if ($item->id == $work_order->tipe_pekerjaan_id) selected @endif>
                                                                {{ $item->name }} ({{ $item->code }})
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td></td>
                                                <td class="fw-bolder">Priority</td>
                                                <td>:</td>
                                                <td>
                                                    <select name="classification_id" class="form-control form-control-sm"
                                                        required>
                                                        <option value="" selected disabled>- select priority -
                                                        </option>
                                                        @foreach ($classification as $item)
                                                            <option value="{{ $item->id }}"
                                                                @if ($item->id == $work_order->classification_id) selected @endif>
                                                                {{ $item->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="fw-bolder">Status</td>
                                                <td>:</td>
                                                <td>
                                                    <select name="status_id" class="form-control form-control-sm" required>
                                                        <option value="" selected disabled>- select status -</option>
                                                        @foreach ($status as $item)
                                                            <option value="{{ $item->id }}"
                                                                @if ($item->id == $work_order->status_id) selected @endif>
                                                                {{ $item->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td></td>
                                                <td class="fw-bolder">No. WO SAP</td>
                                                <td>:</td>
                                                <td>
                                                    <input type="text" class="form-control form-control-sm"
                                                        name="wo_number_sap" id="wo_number_sap"
                                                        placeholder="input WO SAP number" autocomplete="off"
                                                        value="{{ $work_order->wo_number_sap ?? '' }}">
                                                </td>
                                            </tr>
                                            <tr>
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
                                        </tbody>
                                    </table>
                                </div>
                                <hr class="my-5">


                                {{-- EQUIPMENTS --}}
                                <div>
                                    <h3>2. Equipment / Functional Location</h3>
                                </div>
                                @if (
                                    $work_order->trans_workorder_equipment->count() == 0 &&
                                        $work_order->trans_workorder_functional_location->count() == 0)
                                    <div class="d-flex justify-content-between align-items-center mx-auto mb-2">
                                        <label class="mb-0"></label>
                                        <div class="btn-group">
                                            <button type="button" data-bs-toggle="modal"
                                                data-bs-target="#addEquipmentModal"
                                                class="btn btn-success btn-rounded btn-icon" title="Add Equipment"
                                                id="addRowEquipment">
                                                <i class="mdi mdi-plus-circle"></i>
                                            </button>
                                            <button type="button" data-bs-toggle="modal"
                                                data-bs-target="#addFunctionalLocationModal"
                                                class="btn btn-warning btn-rounded btn-icon"
                                                title="Add Functional Location" id="addRowFunctionalLocation">
                                                <i class="mdi mdi-plus-circle"></i>
                                            </button>
                                        </div>
                                    </div>
                                @endif
                                @if ($work_order->trans_workorder_equipment->count() > 0)
                                    <div class="d-flex justify-content-between align-items-center mx-auto mb-2">
                                        <label class="mb-0"></label>
                                        <button type="button" data-bs-toggle="modal" data-bs-target="#addEquipmentModal"
                                            class="btn btn-success btn-rounded btn-icon" title="Add row"
                                            id="addRowEquipment">
                                            <i class="mdi mdi-plus-circle"></i>
                                        </button>
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
                                                    <th class="fw-bolder"> Funct. Location </th>
                                                    <th class="fw-bolder"> Action </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if ($work_order->trans_workorder_equipment->count() > 0)
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
                                                            <td class="text-start">{{ $item->equipment->name ?? '-' }}
                                                            </td>
                                                            <td>{{ $item->equipment->code ?? '-' }}</td>
                                                            <td>{{ $item->equipment->equipment_number ?? '-' }}</td>
                                                            <td>{{ $item->equipment->tipe_equipment->code ?? '-' }}</td>
                                                            <td>{{ $item->equipment->functional_location->code ?? '-' }}
                                                            </td>
                                                            <td>
                                                                <button type="button" title="Delete"
                                                                    class="btn btn-gradient-danger btn-rounded btn-icon"
                                                                    data-bs-toggle="modal" data-bs-target="#deleteModal"
                                                                    data-id="{{ $item->id }}"
                                                                    data-route="{{ route('trans-workorder-equipment.delete') }}">
                                                                    <i class="mdi mdi-delete"></i>
                                                                </button>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                @else
                                                    <tr>
                                                        <td colspan="8" class="text-center">No data found!</td>
                                                    </tr>
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>
                                @endif

                                @if ($work_order->trans_workorder_functional_location->count() > 0)
                                    <div class="d-flex justify-content-between align-items-center mx-auto mb-2">
                                        <label class="mb-0"></label>
                                        <button type="button" data-bs-toggle="modal"
                                            data-bs-target="#addFunctionalLocationModal"
                                            class="btn btn-success btn-rounded btn-icon" title="Add row"
                                            id="addRowFunctionalLocation">
                                            <i class="mdi mdi-plus-circle"></i>
                                        </button>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-bordered text-center">
                                            <thead>
                                                <tr>
                                                    <th class="fw-bolder" style="width: 10px"> # </th>
                                                    <th class="fw-bolder" style="width: 110px"> Checksheet </th>
                                                    <th class="fw-bolder"> Funct. Loc. Name </th>
                                                    <th class="fw-bolder"> Funct. Loc. Code </th>
                                                    <th class="fw-bolder"> Description </th>
                                                    <th class="fw-bolder"> Parent </th>
                                                    <th class="fw-bolder"> Action </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if ($work_order->trans_workorder_functional_location->count() > 0)
                                                    @foreach ($work_order->trans_workorder_functional_location as $item)
                                                        <tr>
                                                            <td>{{ $loop->iteration }}</td>
                                                            <td>
                                                                @if ($item->status == null)
                                                                    <a href="{{ route('checksheet.create', [
                                                                        'uuid_work_order' => $work_order->uuid,
                                                                        'uuid_functional_location' => $item->functional_location->uuid,
                                                                    ]) }}"
                                                                        title="Input Checksheet">
                                                                        <button type="button"
                                                                            class="btn btn-gradient-primary btn-rounded btn-icon">
                                                                            <i class="mdi mdi-lead-pencil"></i>
                                                                        </button>
                                                                    </a>
                                                                @endif
                                                            </td>
                                                            <td class="text-start">
                                                                {{ $item->functional_location->name ?? '-' }}</td>
                                                            <td>{{ $item->functional_location->code ?? '-' }}</td>
                                                            <td>{{ $item->functional_location->description ?? '-' }}</td>
                                                            <td>{{ $item->functional_location->parent->code ?? '-' }}</td>
                                                            </td>
                                                            <td>
                                                                <button type="button" title="Delete"
                                                                    class="btn btn-gradient-danger btn-rounded btn-icon"
                                                                    data-bs-toggle="modal" data-bs-target="#deleteModal"
                                                                    data-id="{{ $item->id }}"
                                                                    data-route="{{ route('trans-workorder-functional-location.delete') }}">
                                                                    <i class="mdi mdi-delete"></i>
                                                                </button>
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
                                @endif
                                <hr class="my-5">


                                {{-- TASKLIST --}}
                                <div>
                                    <h3>3. Tasklist</h3>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mx-auto mb-2">
                                    <label class="mb-0"></label>
                                    <button type="button" class="btn btn-success btn-rounded btn-icon" title="Add row"
                                        data-bs-toggle="modal" data-bs-target="#addTasklistModal">
                                        <i class="mdi mdi-plus-circle"></i>
                                    </button>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-bordered text-center" id="tasklistTable">
                                        <thead>
                                            <tr>
                                                <th class="fw-bolder" style="width: 10px"> No </th>
                                                <th class="fw-bolder" style="width: 50%"> Tasklist/Operation </th>
                                                <th class="fw-bolder"> Plan Duration (Minutes) </th>
                                                <th class="fw-bolder"> Actual Duration (Minutes) </th>
                                                <th class="fw-bolder"> Reference Document </th>
                                                <th class="fw-bolder" style="width: 10px"> Action </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if ($work_order->trans_workorder_tasklist->count() > 0)
                                                @foreach ($work_order->trans_workorder_tasklist as $item)
                                                    <tr>
                                                        <td>{{ $loop->iteration }}</td>
                                                        <td class="text-start">{{ $item->name }}</td>
                                                        <td>{{ $item->duration ?? '-' }}</td>
                                                        <td>{{ $item->actual_duration ?? '-' }}</td>
                                                        <td>{{ $item->reference ?? '-' }}</td>
                                                        <td>
                                                            <button type='button'
                                                                class='btn btn-gradient-warning btn-rounded btn-icon'
                                                                title='Edit' data-bs-toggle="modal"
                                                                data-bs-target="#editTasklistModal"
                                                                data-id="{{ $item->id }}"
                                                                data-name="{{ $item->name }}"
                                                                data-duration="{{ $item->duration }}"
                                                                data-actual_duration="{{ $item->actual_duration }}"
                                                                data-reference="{{ $item->reference }}">
                                                                <i class='text-white mdi mdi-lead-pencil'></i>
                                                            </button>
                                                            <button type="button" title="Delete"
                                                                class="btn btn-gradient-danger btn-rounded btn-icon"
                                                                data-bs-toggle="modal" data-bs-target="#deleteModal"
                                                                data-id="{{ $item->id }}"
                                                                data-route="{{ route('trans-workorder-tasklist.delete') }}">
                                                                <i class="mdi mdi-delete"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @else
                                                <tr>
                                                    <td class="text-center" colspan="6">No data found!</td>
                                                </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                                <hr class="my-5">


                                {{-- SPARE PARTS --}}
                                <div>
                                    <h3>4. Spare Parts</h3>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mx-auto mb-2">
                                    <label class="mb-0"></label>
                                    <button type="button" class="btn btn-success btn-rounded btn-icon" title="Add row"
                                        id="addRowSparepart" data-bs-toggle="modal" data-bs-target="#addSparepartModal">
                                        <i class="mdi mdi-plus-circle"></i>
                                    </button>
                                </div>
                                <div class="table-responsive mb-3">
                                    <table class="table table-bordered text-center">
                                        <thead>
                                            <tr>
                                                <th class="fw-bolder" style="width: 10px"> No </th>
                                                <th class="fw-bolder"> Sparepart Name </th>
                                                <th class="fw-bolder"> Material Number </th>
                                                <th class="fw-bolder" style="width: 20%"> Qty </th>
                                                <th class="fw-bolder" style="width: 20%"> Unit </th>
                                                <th class="fw-bolder" style="width: 10px"> Action </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if ($work_order->transaksi_barang->count() > 0)
                                                @foreach ($work_order->transaksi_barang as $item)
                                                    <tr>
                                                        <td>{{ $loop->iteration }}</td>
                                                        <td class="text-start">
                                                            {{ $item->barang->name ?? '-' }}
                                                        </td>
                                                        <td>
                                                            {{ $item->barang->material_number ?? '-' }}
                                                        </td>
                                                        <td>{{ $item->qty ?? '-' }}</td>
                                                        <td>{{ $item->barang->satuan->code ?? '-' }}</td>
                                                        <td>
                                                            <button type="button" title="Delete"
                                                                class="btn btn-gradient-danger btn-rounded btn-icon"
                                                                data-bs-toggle="modal" data-bs-target="#deleteModal"
                                                                data-id="{{ $item->id }}"
                                                                data-route="{{ route('trans-workorder-barang.delete') }}">
                                                                <i class="mdi mdi-delete"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @else
                                                <tr>
                                                    <td colspan="6" class="text-center">No data found!</td>
                                                </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                                <hr class="my-5">


                                {{-- MAN POWER --}}
                                <div>
                                    <h3>5. Man Power</h3>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mx-auto mb-2">
                                    <label class="mb-0"></label>
                                    <button type="button" data-bs-toggle="modal" data-bs-target="#addUserModal"
                                        class="btn btn-success btn-rounded btn-icon" title="Add row" id="addRowManPower">
                                        <i class="mdi mdi-plus-circle"></i>
                                    </button>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-bordered text-center">
                                        <thead>
                                            <tr>
                                                <th class="fw-bolder" style="width: 10px"> # </th>
                                                <th class="fw-bolder"> Name </th>
                                                <th class="fw-bolder"> Role </th>
                                                <th class="fw-bolder"> Employee Type </th>
                                                <th class="fw-bolder"> Company </th>
                                                <th class="fw-bolder"> Action </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if ($work_order->trans_workorder_user->count() > 0)
                                                @foreach ($work_order->trans_workorder_user as $item)
                                                    <tr>
                                                        <td>{{ $loop->iteration }}</td>
                                                        <td class="text-start">{{ $item->user->name ?? 'N/A' }}</td>
                                                        <td>{{ $item->user->jabatan->name ?? 'NA' }}</td>
                                                        <td>
                                                            {{ $item->user->tipe_employee->name ?? 'N/A' }}
                                                        </td>
                                                        <td>
                                                            {{ $item->user->perusahaan->name ?? 'N/A' }}</td>
                                                        <td>
                                                            <button type="button" title="Delete"
                                                                class="btn btn-gradient-danger btn-rounded btn-icon"
                                                                data-bs-toggle="modal" data-bs-target="#deleteModal"
                                                                data-id="{{ $item->id }}"
                                                                data-route="{{ route('trans-workorder-user.delete') }}">
                                                                <i class="mdi mdi-delete"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @else
                                                <tr>
                                                    <td class="text-center" colspan="6">No data found!</td>
                                                </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                                <hr class="my-5">


                                {{-- DOCUMENTATION --}}
                                <div>
                                    <h3>6. Documentation</h3>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mx-auto mb-2">
                                    <label class="mb-0"></label>
                                    <button type="button" data-bs-toggle="modal" data-bs-target="#addPhotoModal"
                                        class="btn btn-success btn-rounded btn-icon" title="Add row" id="addRowPhoto">
                                        <i class="mdi mdi-plus-circle"></i>
                                    </button>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-bordered text-center">
                                        <thead>
                                            <tr>
                                                <th class="fw-bolder" style="width: 10px"> # </th>
                                                <th class="fw-bolder"> Photo </th>
                                                <th class="fw-bolder" style="width: 30%"> Action </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if ($work_order->trans_workorder_photo->count() > 0)
                                                @foreach ($work_order->trans_workorder_photo as $item)
                                                    <tr>
                                                        <td>{{ $loop->iteration }}</td>
                                                        <td>
                                                            <img class="img-thumbnail"
                                                                src="{{ asset('storage/' . $item->photo) }}"
                                                                alt="No Photo found"
                                                                style="border-radius: 0; height: 300px; width: auto;">
                                                        </td>
                                                        <td>
                                                            <button type="button" title="Delete"
                                                                class="btn btn-gradient-danger btn-rounded btn-icon"
                                                                data-bs-toggle="modal" data-bs-target="#deleteModal"
                                                                data-id="{{ $item->id }}"
                                                                data-route="{{ route('trans-workorder-photo.delete') }}">
                                                                <i class="mdi mdi-delete"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @else
                                                <tr>
                                                    <td colspan="3" class="text-center">No data found!</td>
                                                </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                                <hr class="my-5">

                                {{-- NOTES --}}
                                <div>
                                    <h3>7. Remark/Note</h3>
                                </div>
                                <div class="form-group">
                                    <textarea class="form-control" name="note" id="note" placeholder="Input remark/note (optional)"
                                        rows="7">{{ $work_order->note }}</textarea>
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


    <!-- Add Equipment Modal -->
    <div class="modal fade" id="addEquipmentModal" tabindex="-1" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Form Add</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addEquipmentForm"
                        action="{{ route('trans-workorder-equipment.store', $work_order->uuid) }}" method="POST"
                        class="forms-sample">
                        @csrf
                        @method('POST')
                        <div class="form-group">
                            <label for="equipment_id">Equipment</label>
                            <select class="tom-select-class" name="equipment_id" id="equipment_id">
                                <option value="" selected disabled>- select equipment -</option>
                                @foreach ($equipment as $item)
                                    <option value="{{ $item->id }}">{{ $item->name ?? '-' }}
                                        {{ $item->code ?? '-' }} {{ $item->equipment_number ?? '-' }}</option>
                                @endforeach
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" form="addEquipmentForm" class="btn btn-gradient-primary me-2">Submit</button>
                </div>
            </div>
        </div>
    </div>
    <!-- End Add Equipment Modal -->

    <!-- Add Functional Location Modal -->
    <div class="modal fade" id="addFunctionalLocationModal" tabindex="-1" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Form Add</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addFunctionalLocationForm"
                        action="{{ route('trans-workorder-functional-location.store', $work_order->uuid) }}"
                        method="POST" class="forms-sample">
                        @csrf
                        @method('POST')
                        <div class="form-group">
                            <label for="functional_location_id">Functional Location</label>
                            <select class="tom-select-class" name="functional_location_id" id="functional_location_id">
                                <option value="" selected disabled>- select functional location -</option>
                                @foreach ($functional_location as $item)
                                    <option value="{{ $item->id }}">{{ $item->name ?? '-' }} -----
                                        ({{ $item->code ?? '-' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" form="addFunctionalLocationForm"
                        class="btn btn-gradient-primary me-2">Submit</button>
                </div>
            </div>
        </div>
    </div>
    <!-- End Add Functional Location Modal -->

    <!-- Add Tasklist Modal -->
    <div class="modal fade" id="addTasklistModal" tabindex="-1" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Form Add</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addTasklistForm" action="{{ route('trans-workorder-tasklist.store', $work_order->uuid) }}"
                        method="POST" class="forms-sample">
                        @csrf
                        @method('POST')
                        <div class="form-group">
                            <label for="name">Tasklist/Operation</label>
                            <input type="text" class="form-control" id="name" name="name"
                                placeholder="input tasklist/operation" autocomplete="off" required>
                        </div>
                        <div class="form-group">
                            <label for="duration">Plan Duration (Minutes)</label>
                            <input type="number" class="form-control" id="duration" name="duration"
                                placeholder="input plan duration (optional)" autocomplete="off" min="1">
                        </div>
                        <div class="form-group">
                            <label for="actual_duration">Actual Duration (Minutes)</label>
                            <input type="number" class="form-control" id="actual_duration" name="actual_duration"
                                placeholder="input actual duration (optional)" autocomplete="off" min="1">
                        </div>
                        <div class="form-group">
                            <label for="reference">Reference Document</label>
                            <input type="text" class="form-control" id="reference" name="reference"
                                placeholder="input reference document" autocomplete="off">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" form="addTasklistForm" class="btn btn-gradient-primary me-2">Submit</button>
                </div>
            </div>
        </div>
    </div>
    <!-- End Add Tasklist Modal -->

    <!-- Edit Tasklist Modal -->
    <div class="modal fade" id="editTasklistModal" tabindex="-1" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Form Edit</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editTasklistForm" action="{{ route('trans-workorder-tasklist.update') }}" method="POST"
                        class="forms-sample">
                        @csrf
                        @method('PUT')
                        <input type="text" name="id" id="id_tasklist_edit" hidden>
                        <div class="form-group">
                            <label for="name">Tasklist/Operation</label>
                            <input type="text" class="form-control" id="name_tasklist_edit" name="name"
                                placeholder="input Tasklist/Operation" autocomplete="off" required>
                        </div>
                        <div class="form-group">
                            <label for="duration_tasklist_edit">Plan Duration (Minutes)</label>
                            <input type="number" class="form-control" id="duration_tasklist_edit" name="duration"
                                placeholder="input plan duration (optional)" autocomplete="off" min="1">
                        </div>
                        <div class="form-group">
                            <label for="actual_duration_tasklist_edit">Actual Duration (Minutes)</label>
                            <input type="number" class="form-control" id="actual_duration_tasklist_edit"
                                name="actual_duration" placeholder="input actual duration (optional)" autocomplete="off"
                                min="1">
                        </div>
                        <div class="form-group">
                            <label for="reference_tasklist_edit">Reference Document</label>
                            <input type="text" class="form-control" id="reference_tasklist_edit" name="reference"
                                placeholder="input reference document" autocomplete="off">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" form="editTasklistForm" class="btn btn-gradient-primary me-2">Update</button>
                </div>
            </div>
        </div>
    </div>
    <!-- End Edit Tasklist Modal -->

    <!-- Add Sparepart Modal -->
    <div class="modal fade" id="addSparepartModal" tabindex="-1" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Form Add</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addSparepartForm" action="{{ route('trans-workorder-barang.store', $work_order->uuid) }}"
                        method="POST" class="forms-sample">
                        @csrf
                        @method('POST')
                        <div class="form-group">
                            <label for="barang_id">Sparepart</label>
                            <select class="tom-select-class" name="barang_id" id="barang_id" required>
                                <option value="" selected disabled>- select sparepart -</option>
                                @foreach ($barang as $item)
                                    <option value="{{ $item->id }}">{{ $item->name ?? '#' }} ----
                                        {{ $item->material_number ?? '#' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="barang_id">Qty</label>
                            <input type="number" class="form-control" name="qty" id="qty"
                                placeholder="input qty" min="1" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" form="addSparepartForm" class="btn btn-gradient-primary me-2">Submit</button>
                </div>
            </div>
        </div>
    </div>
    <!-- End Add Sparepart Modal -->

    <!-- Add User Modal -->
    <div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Form Add</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addUserForm" action="{{ route('trans-workorder-user.store', $work_order->uuid) }}"
                        method="POST" class="forms-sample">
                        @csrf
                        @method('POST')
                        <div class="form-group">
                            <label for="user_id">Man Power</label>
                            <select class="tom-select-class" name="user_id" id="user_id">
                                <option value="" selected disabled>- select man power -</option>
                                @foreach ($user as $item)
                                    <option value="{{ $item->id }}">{{ $item->name ?? '#' }} ----
                                        {{ $item->jabatan->name ?? '#' }} ---- {{ $item->tipe_employee->name ?? '#' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" form="addUserForm" class="btn btn-gradient-primary me-2">Submit</button>
                </div>
            </div>
        </div>
    </div>
    <!-- End Add User Modal -->

    <!-- Add Photo Modal -->
    <div class="modal fade" id="addPhotoModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Form Add</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addPhotoForm" action="{{ route('trans-workorder-photo.store', $work_order->uuid) }}"
                        method="POST" class="forms-sample" enctype="multipart/form-data">
                        @csrf
                        @method('POST')
                        <div class="form-group">
                            <label for="photo">Photo</label>
                            <div class="text-center">
                                <img class="img-thumbnail mb-2" id="previewImage" src="#" alt="Tidak ada photo"
                                    style="max-width: 250px; max-height: 250px; display: none;">
                            </div>
                            <input type="file" class="form-control" id="photo" name="photo" autocomplete="off"
                                placeholder="input photo" accept="image/*">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" form="addPhotoForm" class="btn btn-gradient-primary me-2">Submit</button>
                </div>
            </div>
        </div>
    </div>
    <!-- End Add Photo Modal -->

    <!-- Delete Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Are you sure?</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="deleteForm" action="#" method="POST" class="forms-sample">
                        @csrf
                        @method('delete')
                        <input type="text" name="id" id="id_delete" hidden>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" form="deleteForm" class="btn btn-gradient-danger me-2">Delete</button>
                </div>
            </div>
        </div>
    </div>
    <!-- End Delete Modal -->
@endsection

@section('javascript')
    <script>
        $(document).ready(function() {
            $('#editTasklistModal').on('show.bs.modal', function(e) {
                var id = $(e.relatedTarget).data('id');
                var name = $(e.relatedTarget).data('name');
                var duration = $(e.relatedTarget).data('duration');
                var actual_duration = $(e.relatedTarget).data('actual_duration');
                var reference = $(e.relatedTarget).data('reference');

                $('#id_tasklist_edit').val(id);
                $('#name_tasklist_edit').val(name);
                $('#duration_tasklist_edit').val(duration);
                $('#actual_duration_tasklist_edit').val(actual_duration);
                $('#reference_tasklist_edit').val(reference);
            });

            $('#deleteModal').on('show.bs.modal', function(e) {
                var id = $(e.relatedTarget).data('id');
                var route = $(e.relatedTarget).data('route');

                $('#id_delete').val(id);
                $('#deleteForm').attr('action', route);
            });

            const imageInput = document.getElementById('photo');
            const previewImage = document.getElementById('previewImage');

            imageInput.addEventListener('change', function(event) {
                const selectedFile = event.target.files[0];

                if (selectedFile) {
                    const reader = new FileReader();

                    reader.onload = function(e) {
                        previewImage.src = e.target.result;
                        previewImage.style.display = 'block';
                    }

                    reader.readAsDataURL(selectedFile);
                }
            });
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var isChangedSelect = document.getElementById('is_changed');
            var sparePartContainer = document.getElementById('sparePartContainer');
            var barangSelect = document.getElementById('barang_id');
            var qtyInput = document.getElementById('qty');

            function removeAllTomSelectBaru() {
                var elements = document.querySelectorAll('.tom-select-baru');

                elements.forEach(function(element) {
                    element.remove();
                });
            }

            function updateDisplay() {
                if (isChangedSelect.value === '1') {
                    sparePartContainer.style.display = 'block';
                    barangSelect.required = true;
                    qtyInput.required = true;
                } else {
                    sparePartContainer.style.display = 'none';
                    barangSelect.required = false;
                    qtyInput.required = false;
                    removeAllTomSelectBaru();
                }
            }

            // Initial update on page load
            updateDisplay();

            // Add event listener to handle changes
            isChangedSelect.addEventListener('change', function() {
                updateDisplay();
            });

            var addRowButton = document.getElementById('addRow');
            var inputContainer = document.getElementById('inputContainer');
            var settings = {}; // Atur pengaturan Tom Select sesuai kebutuhan

            function initializeTomSelect(selector) {
                document.querySelectorAll(selector).forEach(function(el) {
                    if (!el.tomSelectInstance) {
                        el.tomSelectInstance = new TomSelect(el, settings);
                    }
                });
            }

            // Inisialisasi Tom Select pada load halaman
            // initializeTomSelect('.tom-select-class');

            addRowButton.addEventListener('click', function() {
                var row = document.createElement('div');
                row.classList.add('input-group');
                row.classList.add('tom-select-baru');
                var uniqueClass = 'tom-select-' + Date.now();
                row.innerHTML = `
                <select class="tom-select-class mt-2 ${uniqueClass} col-8" name="barang_ids[]" required>
                    <option value="" selected disabled>- pilih spare part -</option>
                    @foreach ($barang as $item)
                        <option value="{{ $item->id }}">
                            ({{ $item->material_number ?? '-' }}) - {{ $item->name }}
                        </option>
                    @endforeach
                </select>
                <input type="number" class="form-control col-3" name="qty[]" placeholder="qty" required min="1">
                <div class="input-group-append">
                    <button type="button" class="btn btn-danger btn-rounded btn-icon removeRow" title="Remove row" style="padding: 0.25rem 0.5rem; font-size: 0.75rem; line-height: 1.5;">
                        <i class="mdi mdi-minus-circle"></i>
                    </button>
                </div>
            `;
                inputContainer.appendChild(row);

                // Inisialisasi Tom Select untuk elemen baru
                initializeTomSelect('.' + uniqueClass);

                // Add event listener for the new remove button
                row.querySelector('.removeRow').addEventListener('click', function() {
                    if (inputContainer.children.length > 1) {
                        inputContainer.removeChild(row);
                    } else {
                        console.warn('Cannot remove the last row.');
                    }
                });
            });

            // Event delegation for remove buttons in existing rows
            inputContainer.addEventListener('click', function(event) {
                if (event.target.classList.contains('removeRow')) {
                    var row = event.target.closest('.input-group');
                    if (inputContainer.children.length > 1) {
                        inputContainer.removeChild(row);
                    }
                }
            });
        });
    </script>

    <script>
        $(document).ready(function() {
            // Add row
            $('#addRowTasklist').on('click', function() {
                var rowCount = $('#tasklistTable tbody tr').length + 1; // Get the current number of rows

                var newRow = `
                <tr>
                    <td>` + rowCount + `</td>
                    <td>
                        <input type="text" class="form-control" name="tasklist[]" placeholder="input tasklist" required autocomplete="off">
                    </td>
                    <td>
                        <input type="number" class="form-control" name="duration[]" placeholder="input duration (optional)" min="1">
                    </td>
                    <td>
                        <button type="button" class="btn btn-danger btn-rounded btn-icon btn-remove">
                            <i class="mdi mdi-minus-circle"></i>
                        </button>
                    </td>
                </tr>
            `;
                $('#tasklistTable tbody').append(newRow); // Add the new row to the table
            });

            // Remove row
            $(document).on('click', '.btn-remove', function() {
                $(this).closest('tr').remove(); // Remove the row when 'Remove' button is clicked

                // Re-index the rows after removal
                $('#tasklistTable tbody tr').each(function(index, tr) {
                    $(tr).find('td:first').text(index + 1); // Update row number
                });
            });
        });
    </script>
@endsection
