@extends('layout.base')

@section('title-head')
    <title>Detail Work Order - {{ $work_order->ticket_number }}</title>
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
                            <div class="btn-group my-2">
                                <a href="{{ route('work-order.index') }}" title="Back" class="btn btn-primary btn-rounded">
                                    <i class="mdi mdi-arrow-left"></i> Back
                                </a>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <div>
                                <h3>1. Detail</h3>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-borderless">
                                    <tbody>
                                        <tr>
                                            <td style="width: 130px" class="fw-bolder">WO Number</td>
                                            <td style="width: 10px">:</td>
                                            <td style="width: 330px">
                                                <input type="text" class="form-control form-control-sm"
                                                    autocomplete="off" value="{{ $work_order->ticket_number ?? '' }}"
                                                    disabled>
                                            </td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bolder">No. WO SAP</td>
                                            <td>:</td>
                                            <td>
                                                <input type="text" class="form-control form-control-sm"
                                                    placeholder="input WO SAP number" autocomplete="off"
                                                    value="{{ $work_order->wo_number_sap ?? '' }}" disabled>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bolder">Date</td>
                                            <td>:</td>
                                            <td>
                                                <input type="date" class="form-control form-control-sm"
                                                    value="{{ $work_order->date ?? '' }}" disabled>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bolder">Order Name</tdvclass=>
                                            <td>:</td>
                                            <td>
                                                <input type="text" class="form-control form-control-sm"
                                                    placeholder="input order name" value="{{ $work_order->name ?? '-' }}"
                                                    autocomplete="off" disabled>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bolder">Description</td>
                                            <td>:</td>
                                            <td>
                                                <input type="text" class="form-control form-control-sm"
                                                    placeholder="input description"
                                                    value="{{ $work_order->description ?? '' }}" disabled>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bolder">Type</td>
                                            <td>:</td>
                                            <td>
                                                <input type="text" class="form-control form-control-sm"
                                                    value="{{ $work_order->tipe_pekerjaan->name ?? '-' }} ({{ $work_order->tipe_pekerjaan->code ?? '-' }})"
                                                    disabled>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bolder">Work Center</td>
                                            <td>:</td>
                                            <td>
                                                <input type="text" class="form-control form-control-sm"
                                                    value="{{ $work_order->relasi_struktur->departemen->name ?? '-' }}"
                                                    disabled>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bolder">Priority</td>
                                            <td>:</td>
                                            <td>
                                                <input type="text" class="form-control form-control-sm"
                                                    value="{{ $work_order->classification->name ?? '-' }} ({{ $work_order->classification->id }})"
                                                    disabled>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bolder">Created By</td>
                                            <td>:</td>
                                            <td>
                                                <input type="text" class="form-control form-control-sm"
                                                    value="{{ $work_order->user->name ?? '-' }}" disabled>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bolder">Status</td>
                                            <td>:</td>
                                            <td>
                                                <input type="text" class="form-control form-control-sm"
                                                    value="{{ $work_order->status->name ?? '-' }}" disabled>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <hr class="my-5">


                            {{-- EQUIPMENTS --}}
                            <div>
                                <h3>2. Equipment / Functional Location</h3>
                            </div>
                            @if ($work_order->trans_workorder_equipment->count() > 0)
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
                            <div class="table-responsive">
                                <table class="table table-bordered text-center" id="tasklistTable">
                                    <thead>
                                        <tr>
                                            <th class="fw-bolder" style="width: 10px"> No </th>
                                            <th class="fw-bolder" style="width: 10px"> Action </th>
                                            <th class="fw-bolder" style="width: 50%"> Tasklist/Operation </th>
                                            <th class="fw-bolder"> Plan Duration <br> (Minutes) </th>
                                            <th class="fw-bolder"> Actual Duration <br> (Minutes) </th>
                                            <th class="fw-bolder"> Reference Document </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if ($work_order->trans_workorder_tasklist->count() > 0)
                                            @foreach ($work_order->trans_workorder_tasklist as $item)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>
                                                        <button type='button'
                                                            class='btn btn-gradient-primary btn-rounded btn-icon'
                                                            title='Input Actual Duration' data-bs-toggle="modal"
                                                            data-bs-target="#editTasklistModal"
                                                            data-id="{{ $item->id }}"
                                                            data-name="{{ $item->name }}"
                                                            data-duration="{{ $item->duration }}"
                                                            data-actual_duration="{{ $item->actual_duration }}"
                                                            data-reference="{{ $item->reference }}">
                                                            <i class='text-white mdi mdi-lead-pencil'></i>
                                                        </button>
                                                    </td>
                                                    <td class="text-start">{{ $item->name }}</td>
                                                    <td>{{ $item->duration ?? '-' }}</td>
                                                    <td>{{ $item->actual_duration ?? '-' }}</td>
                                                    <td>{{ $item->reference ?? '-' }}</td>
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
                            <div class="table-responsive mb-3">
                                <table class="table table-bordered text-center">
                                    <thead>
                                        <tr>
                                            <th class="fw-bolder" style="width: 10px"> No </th>
                                            <th class="fw-bolder"> Sparepart Name </th>
                                            <th class="fw-bolder"> Material Number </th>
                                            <th class="fw-bolder" style="width: 20%"> Qty </th>
                                            <th class="fw-bolder" style="width: 20%"> Unit </th>
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
                                            <th class="fw-bolder"> Description </th>
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
                                                        {{ $item->description ?? '-' }}
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
                                                <td colspan="4" class="text-center">No data found!</td>
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
                            <div class="table-responsive">
                                <table class="table table-bordered text-center" id="tasklistTable">
                                    <thead>
                                        <tr>
                                            <th class="fw-bolder" style="width: 10px"> No </th>
                                            <th class="fw-bolder" style="width: 10px"> Action </th>
                                            <th class="fw-bolder"> Remark/Note </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>1</td>
                                            <td>
                                                <button type='button'
                                                    class='btn btn-gradient-primary btn-rounded btn-icon'
                                                    title='Input Remark/Note' data-bs-toggle="modal"
                                                    data-bs-target="#noteModal" data-note="{{ $work_order->note }}">
                                                    <i class='text-white mdi mdi-lead-pencil'></i>
                                                </button>
                                            </td>
                                            <td class="text-start">
                                                {{ $work_order->note ?? '-' }}
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <hr class="my-5">


                            {{-- JOB TIME --}}
                            <div>
                                <h3>8. Job Time</h3>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-bordered text-center" id="tasklistTable">
                                    <thead>
                                        <tr>
                                            <th class="fw-bolder" style="width: 10px"> No </th>
                                            <th class="fw-bolder" style="width: 10px"> Action </th>
                                            <th class="fw-bolder"> Start Time </th>
                                            <th class="fw-bolder"> Finish Time </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>1</td>
                                            <td>
                                                <button type='button'
                                                    class='btn btn-gradient-primary btn-rounded btn-icon'
                                                    title='Input Job Time' data-bs-toggle="modal"
                                                    data-bs-target="#jobTimeModal"
                                                    data-start_time="{{ $work_order->start_time }}"
                                                    data-end_time="{{ $work_order->end_time }}">
                                                    <i class='text-white mdi mdi-lead-pencil'></i>
                                                </button>
                                            </td>
                                            <td>{{ $work_order->start_time ?? '-' }}</td>
                                            <td>{{ $work_order->end_time ?? '-' }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <hr class="my-5">

                            {{-- APPROVAL --}}
                            <div>
                                <h3>9. Approval</h3>
                            </div>
                            <div class="table-responsive">
                                @if ($work_order->trans_workorder_approval->count() > 0)
                                    <table class="table table-bordered text-center">
                                        <thead>
                                            <tr>
                                                @foreach ($work_order->trans_workorder_approval as $item)
                                                    <th class="fw-bolder">{{ $item->approval->name ?? '#' }}</th>
                                                @endforeach
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr style="height: 160px">
                                                @foreach ($work_order->trans_workorder_approval as $item)
                                                    @if ($item->status == 'approved')
                                                        <td>
                                                            <img style="height: auto; width: 150px; border-radius: 0;"
                                                                src="{{ asset('storage/' . $item->user->ttd) }}"
                                                                alt="no signed">
                                                        </td>
                                                    @else
                                                        <td></td>
                                                    @endif
                                                @endforeach
                                            </tr>
                                            <tr>
                                                @foreach ($work_order->trans_workorder_approval as $item)
                                                    @if ($item->status == 'approved')
                                                        <td class="fw-bolder">{{ $item->user->name ?? '-' }}
                                                            <br>
                                                            {{ $item->date ?? '-' }}
                                                        </td>
                                                    @else
                                                        <td>
                                                            @if (
                                                                $item->approval->relasi_struktur_id == auth()->user()->relasi_struktur_id &&
                                                                    $item->approval->jabatan_id == auth()->user()->jabatan_id &&
                                                                    $item->approval->tipe_employee_id == auth()->user()->tipe_employee_id)
                                                                {{-- <button type="button" class="btn btn-danger btn-rounded"
                                                                    data-bs-toggle="modal" data-bs-target="#reviseModal">
                                                                    <i class="mdi mdi-lead-pencil"></i> Revise
                                                                </button> --}}
                                                                <button type="button" class="btn btn-success btn-rounded"
                                                                    data-bs-toggle="modal" data-bs-target="#approveModal">
                                                                    <i class="mdi mdi-check"></i> Approve
                                                                </button>
                                                            @else
                                                                Waiting Approval
                                                            @endif
                                                        </td>
                                                    @endif
                                                @endforeach
                                            </tr>
                                        </tbody>
                                    </table>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

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
                    <form id="editTasklistForm" action="{{ route('trans-workorder-tasklist.update-actual-duration') }}"
                        method="POST" class="forms-sample">
                        @csrf
                        @method('PUT')
                        <input type="text" name="id" id="id_tasklist_edit" hidden>
                        <div class="form-group">
                            <label for="name">Tasklist/Operation</label>
                            <input type="text" class="form-control" id="name_tasklist_edit"
                                placeholder="input Tasklist/Operation" autocomplete="off" required disabled>
                        </div>
                        <div class="form-group">
                            <label for="duration_tasklist_edit">Plan Duration (Minutes)</label>
                            <input type="number" class="form-control" id="duration_tasklist_edit"
                                placeholder="input plan duration (optional)" autocomplete="off" min="1" disabled>
                        </div>
                        <div class="form-group">
                            <label for="actual_duration_tasklist_edit">Actual Duration (Minutes)</label>
                            <input type="number" class="form-control" id="actual_duration_tasklist_edit"
                                name="actual_duration" placeholder="input actual duration (optional)" autocomplete="off"
                                min="1" required>
                        </div>
                        <div class="form-group">
                            <label for="reference_tasklist_edit">Reference Document</label>
                            <input type="text" class="form-control" id="reference_edit"
                                placeholder="input reference document" autocomplete="off" disabled>
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
                                placeholder="input photo" accept="image/*" required>
                        </div>
                        <div class="form-group">
                            <label for="description">Description</label>
                            <input type="text" class="form-control" id="description" name="description"
                                autocomplete="off" placeholder="input description" required>
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

    <!-- Note Modal -->
    <div class="modal fade" id="noteModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Form Add</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addNoteForm" action="{{ route('work-order.note.update', $work_order->uuid) }}"
                        method="POST" class="forms-sample">
                        @csrf
                        @method('PUT')
                        <div class="form-group">
                            <label for="note_edit">Remark / Note</label>
                            <input type="text" class="form-control form-control-lg" name="note" id="note_edit"
                                autocomplete="off" placeholder="input remark / note" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" form="addNoteForm" class="btn btn-gradient-primary me-2">Submit</button>
                </div>
            </div>
        </div>
    </div>
    <!-- End Note Modal -->

    <!-- Job Time Modal -->
    <div class="modal fade" id="jobTimeModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Form Add</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addJobTimeForm" action="{{ route('work-order.time.update', $work_order->uuid) }}"
                        method="POST" class="forms-sample">
                        @csrf
                        @method('PUT')
                        <div class="form-group">
                            <label for="start_time_edit">Start Time</label>
                            <input type="datetime-local" class="form-control form-control-lg" name="start_time"
                                id="start_time_edit" autocomplete="off" required>
                        </div>
                        <div class="form-group">
                            <label for="end_time_edit">Finish Time</label>
                            <input type="datetime-local" class="form-control form-control-lg" name="end_time"
                                id="end_time_edit" autocomplete="off" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" form="addJobTimeForm" class="btn btn-gradient-primary me-2">Submit</button>
                </div>
            </div>
        </div>
    </div>
    <!-- End Job Time Modal -->

    <!-- Approve Modal -->
    <div class="modal fade" id="approveModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Are you sure?</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="approveForm" action="{{ route('work-order.approve', $work_order->uuid) }}" method="POST"
                        class="forms-sample">
                        @csrf
                        @method('PUT')
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" form="approveForm" class="btn btn-gradient-success me-2">Approve</button>
                </div>
            </div>
        </div>
    </div>
    <!-- End Approve Modal -->

    <!-- Revise Modal -->
    <div class="modal fade" id="reviseModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Are you sure?</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="reviseForm" action="{{ route('work-order.revise', $work_order->uuid) }}" method="POST"
                        class="forms-sample">
                        @csrf
                        @method('PUT')
                        <div class="modal-body">
                            <form id="reviseForm" action="{{ route('work-order.approve', $work_order->uuid) }}"
                                method="POST" class="forms-sample">
                                @csrf
                                @method('PUT')
                                <div class="form-group">
                                    <label for="note">Note</label>
                                    <textarea class="form-control" name="note" id="note" rows="5"
                                        placeholder="input your note to revise this Work Order" required></textarea>
                                </div>
                            </form>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" form="reviseForm" class="btn btn-gradient-danger me-2">Revise</button>
                </div>
            </div>
        </div>
    </div>
    <!-- End Revise Modal -->

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

            $('#noteModal').on('show.bs.modal', function(e) {
                var note = $(e.relatedTarget).data('note');

                $('#note_edit').val(note);
            });

            $('#jobTimeModal').on('show.bs.modal', function(e) {
                var start_time = $(e.relatedTarget).data('start_time');
                var end_time = $(e.relatedTarget).data('end_time');

                $('#start_time_edit').val(start_time);
                $('#end_time_edit').val(end_time);
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

    {{-- Sparepart --}}
    {{-- <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
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
    </script> --}}

    {{-- Tasklist --}}
    {{-- <script>
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
    </script> --}}
@endsection
