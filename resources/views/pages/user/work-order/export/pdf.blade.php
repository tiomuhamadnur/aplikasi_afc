<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Work Order - {{ $work_order->ticket_number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 13px;
        }

        .page-break {
            page-break-after: always;
        }

        .text-center {
            text-align: center;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            table-layout: auto;
        }

        .table-bordered td,
        .table-bordered th {
            border: 1px solid #000;
            padding: 4px;
            vertical-align: middle;
        }

        .table thead th {
            background-color: #cfcfcf;
            /* Warna abu-abu untuk background */
        }

        .fw-bolder {
            font-weight: bold;
        }

        .text-center {
            text-align: center;
        }

        h2 {
            margin: 0;
            font-size: 20px;
        }

        p {
            margin: 0;
            font-size: 15px;
        }

        img {
            display: block;
            margin: 0 auto;
            width: 200px;
            height: auto;
        }

        /* Pengaturan ukuran kertas A4 portrait */
        @page {
            size: A4 portrait;
            margin: 15px;
        }

        /* Responsive untuk halaman cetak */
        @media print {
            body {
                font-size: 10px;
            }

            .table {
                width: 100%;
            }
        }
    </style>
</head>

<body>

    <table class="table table-bordered">
        <tbody>
            <tr>
                <td class="fw-bolder" style="width: 75px; border-right: none; vertical-align: middle;">WO Date</td>
                <td style="border-left: none; border-right: none; text-align: center; vertical-align: middle;">:</td>
                <td style="width: 165px; border-left: none; vertical-align: middle;">
                    {{ $work_order->date ?? '-' }}
                </td>
                <td class="text-center" rowspan="2" style="vertical-align: middle;">
                    <h2>PT. MRT Jakarta</h2>
                    <p style="">No: {{ $work_order->ticket_number ?? '-' }}</p>
                </td>
                <td class="text-center" rowspan="2" style="width: 200px; vertical-align: middle;">
                    <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRmOAdOiswyFtDd73NrG0oMhBeZmGW5ySFAmw&s"
                        alt="PT. MRT Jakarta Logo">
                </td>
            </tr>
            <tr>
                <td class="fw-bolder" style="border-right: none; vertical-align: middle;">Created by</td>
                <td style="border-left: none; border-right: none; text-align: center; vertical-align: middle;">:</td>
                <td style="border-left: none; vertical-align: middle;">
                    {{ $work_order->user->name ?? '-' }}
                </td>
            </tr>
        </tbody>
    </table>

    <div style="margin-top: 5px">
        <h3>1. Detail</h3>
    </div>

    <table class="table">
        <tbody>
            <tr>
                <td style="width: 85px;" class="fw-bolder">Order Name</td>
                <td style="width: 10px;">:</td>
                <td style="width: 180px;">
                    {{ $work_order->name ?? '-' }}
                </td>
                <td style="width: auto;"></td>
                <td class="fw-bolder" style="width: 85px"></td>
                <td style="width: 10px"></td>
                <td style="width: 180px"></td>
            </tr>
            <tr>
                <td class="fw-bolder">Description</td>
                <td>:</td>
                <td>
                    {{ $work_order->description ?? '-' }}
                </td>
                <td></td>
                <td class="fw-bolder">Work Center</td>
                <td>:</td>
                <td>
                    {{ $work_order->relasi_struktur->departemen->code ?? '-' }}
                </td>
            </tr>
            <tr>
                <td class="fw-bolder">Type</td>
                <td>:</td>
                <td>
                    {{ $work_order->tipe_pekerjaan->code ?? '-' }}
                </td>
                <td></td>
                <td class="fw-bolder">Priority</td>
                <td>:</td>
                <td>
                    {{ $work_order->classification->name ?? '-' }} ({{ $work_order->classification->id ?? '-' }})
                </td>
            </tr>
            <tr>
                <td class="fw-bolder">Status</td>
                <td>:</td>
                <td>
                    {{ $work_order->status->name }}
                </td>
                <td></td>
                <td class="fw-bolder">No. WO SAP</td>
                <td>:</td>
                <td>
                    {{ $work_order->wo_number_sap ?? '-' }}
                </td>
            </tr>
            <tr>
                <td class="fw-bolder">Job Start</td>
                <td>:</td>
                <td>
                    {{ $work_order->start_time ?? '-' }}
                </td>
                <td></td>
                <td class="fw-bolder">End Time</td>
                <td>:</td>
                <td>
                    {{ $work_order->end_time ?? '-' }}
                </td>
            </tr>
        </tbody>
    </table>

    <div style="margin-top: 15px">
        <h3>2. Object Maintenance</h3>
    </div>
    @if ($work_order->trans_workorder_equipment->count() > 0)
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th class="fw-bolder" style="width: 20px"> # </th>
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
                        <td colspan="8">No data</td>
                    </tr>
                @endif
            </tbody>
        </table>
    @endif

    @if ($work_order->trans_workorder_functional_location->count() > 0)
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th class="fw-bolder" style="width: 20px"> # </th>
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
                            <td>{{ $item->functional_location->name ?? '-' }}</td>
                            <td>{{ $item->functional_location->code ?? '-' }}</td>
                            <td>{{ $item->functional_location->description ?? '-' }}</td>
                            <td>{{ $item->functional_location->parent->code ?? '-' }}</td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="5" class="text-center">No data</td>
                    </tr>
                @endif
            </tbody>
        </table>
    @endif


    <div style="margin-top: 15px">
        <h3>3. Tasklist</h3>
    </div>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th class="fw-bolder" style="width: 20px"> No </th>
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
                        <td>{{ $loop->iteration ?? '-' }}</td>
                        <td>{{ $item->name ?? '-' }}</td>
                        <td>{{ $item->duration ?? '-' }}</td>
                        <td>{{ $item->actual_duration ?? '-' }}</td>
                        <td>{{ $item->reference ?? '-' }}</td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td class="text-center" colspan="5">No data</td>
                </tr>
            @endif
        </tbody>
    </table>


    <div style="margin-top: 15px">
        <h3>4. Spare Parts</h3>
    </div>
    <table class="table table-bordered">
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
                        <td>{{ $item->barang->name ?? '-' }}</td>
                        <td>{{ $item->barang->material_number ?? '-' }}</td>
                        <td>{{ $item->qty ?? '-' }}</td>
                        <td>{{ $item->barang->satuan->code ?? '-' }}</td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="5" class="text-center">No data</td>
                </tr>
            @endif
        </tbody>
    </table>


    <div style="margin-top: 15px">
        <h3>5. Man Power</h3>
    </div>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th class="fw-bolder" style="width: 20px"> # </th>
                <th class="fw-bolder"> Name </th>
                <th class="fw-bolder"> Role </th>
                <th class="fw-bolder"> Employee Type </th>
                <th class="fw-bolder"> Company </th>
            </tr>
        </thead>
        <tbody>
            @if ($work_order->trans_workorder_user->count() > 0)
                @foreach ($work_order->trans_workorder_user as $item)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td class="text-start">{{ $item->user->name ?? 'N/A' }}</td>
                        <td>{{ $item->user->jabatan->name ?? 'NA' }}</td>
                        <td>{{ $item->user->tipe_employee->name ?? 'N/A' }}</td>
                        <td>{{ $item->user->perusahaan->name ?? 'N/A' }}</td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td class="text-center" colspan="5">No data</td>
                </tr>
            @endif
        </tbody>
    </table>


    <div style="margin-top: 15px">
        <h3>6. Remark/Note</h3>
    </div>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th class="fw-bolder"> Remark/Note </th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $work_order->note ?? '-' }}</td>
            </tr>
        </tbody>
    </table>



    <div class="page-break"></div>

    <div style="margin-top: 15px">
        <h3>7. Documentation</h3>
    </div>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th class="fw-bolder" style="width: 20px"> # </th>
                <th class="fw-bolder"> Photo </th>
                <th class="fw-bolder" style="width: 45%"> Description </th>
            </tr>
        </thead>
        <tbody>
            @if ($work_order->trans_workorder_photo->count() > 0)
                @foreach ($work_order->trans_workorder_photo as $item)
                    <tr>
                        <td style="vertical-align: top;">{{ $loop->iteration }}</td>
                        <td>
                            <img class="img-thumbnail" src="{{ public_path('storage/' . $item->photo) }}"
                                alt="No Photo found" style="border-radius: 0; height: auto; width: 250px;">
                        </td>
                        <td style="vertical-align: top;">{{ $item->description ?? '-' }}</td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="3" class="text-center">No data</td>
                </tr>
            @endif
        </tbody>
    </table>


    <div style="margin-top: 15px">
        <h3>8. Approval</h3>
    </div>
    @if ($work_order->trans_workorder_approval->count() > 0)
        <table class="table table-bordered text-center">
            <thead>
                <tr>
                    @foreach ($work_order->trans_workorder_approval as $item)
                        <th class="fw-bolder">{{ $item->approval->name ?? '-' }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                <tr style="height: auto">
                    @foreach ($work_order->trans_workorder_approval as $item)
                        @if ($item->status == 'approved')
                            <td>
                                <img style="height: auto; width: 150px; border-radius: 0;"
                                    src="{{ public_path('storage/' . $item->user->ttd) }}" alt="no signed">
                            </td>
                        @else
                            <td></td>
                        @endif
                    @endforeach
                </tr>
                <tr>
                    @foreach ($work_order->trans_workorder_approval as $item)
                        @if ($item->status == 'approved')
                            <td>{{ $item->user->name ?? '-' }}
                                <br>
                                {{ $item->date ?? '-' }}
                            </td>
                        @else
                            <td>
                                @if (
                                    $item->approval->relasi_struktur_id == auth()->user()->relasi_struktur_id &&
                                        $item->approval->jabatan_id == auth()->user()->jabatan_id &&
                                        $item->approval->tipe_employee_id == auth()->user()->tipe_employee_id)
                                    <button type="button" class="btn btn-success btn-rounded" data-bs-toggle="modal"
                                        data-bs-target="#approveModal">
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

</body>

</html>
