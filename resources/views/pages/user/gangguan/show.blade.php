@extends('layout.base')

@section('title-head')
    <title>Detail Data Gangguan</title>
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Detail Data Gangguan</h4>
                        <a href="{{ route('gangguan.index') }}" title="Back" class="btn btn-outline-primary btn-rounded">
                            <i class="mdi mdi-arrow-left"></i> Back
                        </a>
                        <div class="table-responsive mt-4">
                            <table class="table table-bordered">
                                <tbody>
                                    <tr>
                                        <td class="fw-bolder">Ticket Number</td>
                                        <td>{{ $gangguan->ticket_number ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bolder">Status</td>
                                        <td>
                                            {{ $gangguan->status->name ?? '-' }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bolder">Classification</td>
                                        <td>{{ $gangguan->classification->name ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bolder">Report By</td>
                                        <td>{{ $gangguan->report_by }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bolder">Report Date</td>
                                        <td>{{ $gangguan->report_date }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bolder">Equipment</td>
                                        <td>{{ $gangguan->equipment->name }} - ({{ $gangguan->equipment->code ?? '-' }})
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bolder">Category</td>
                                        <td>{{ $gangguan->category->name ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bolder">Problem (P)</td>
                                        <td>
                                            {{ $gangguan->problem->name ?? '' }}
                                            <br>
                                            Other : {{ $gangguan->problem_other ?? '-' }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bolder">Cause (C)</td>
                                        <td>
                                            {{ $gangguan->cause->name ?? '' }}
                                            <br>
                                            Other : {{ $gangguan->cause_other ?? '-' }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bolder">Remedy (R)</td>
                                        <td>
                                            @foreach ($gangguan->trans_gangguan_remedy as $item)
                                                {{ $loop->iteration }}. {{ $item->remedy->name ?? $item->remedy_other }}
                                                ------- (by: {{ $item->user->name ?? '-' }} {{ $item->date }})
                                                <br>
                                            @endforeach
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bolder">Photo Before</td>
                                        <td>
                                            <div class="text-left">
                                                <img class="img-thumbnail" src="{{ asset('storage/' . $gangguan->photo) }}"
                                                    alt="Tidak ada photo before"
                                                    style="width: 250px; height: 250px; border-radius: 0;">
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bolder">Photo After</td>
                                        <td>
                                            <div class="text-left">
                                                <img class="img-thumbnail"
                                                    src="{{ asset('storage/' . $gangguan->photo_after) }}"
                                                    alt="Tidak ada photo after"
                                                    style="width: 250px; height: 250px; border-radius: 0;">
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bolder">Remark</td>
                                        <td>
                                            {{ $gangguan->remark ?? '-' }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bolder">Penggantian Sparepart?</td>
                                        <td>
                                            @if ($gangguan->is_changed == 1)
                                                Yes
                                            @else
                                                No
                                            @endif
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
