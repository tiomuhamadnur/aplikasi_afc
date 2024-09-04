@extends('layout.base')

@section('title-head')
    <title>Input Data Checksheet</title>
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Input Data Checksheet</h4>
                        <div class="btn-group my-2">
                            <button type="button"
                                onclick="window.location.href='{{ route('work-order.detail', $work_order->uuid) }}'"
                                title="Back" class="btn btn-outline-primary btn-rounded btn-icon">
                                <i class="mdi mdi-arrow-left"></i>
                            </button>
                            <button type="button" title="Filter" class="btn btn-outline-primary btn-rounded btn-icon">
                                <i class="mdi mdi-filter"></i>
                            </button>
                            <button type="button" title="Export" class="btn btn-outline-primary btn-rounded btn-icon">
                                <i class="mdi mdi-file-export"></i>
                            </button>
                        </div>
                        <form id="editForm" action="{{ route('checksheet.store') }}" class="forms-sample mt-4"
                            method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('POST')
                            <input type="text" name="work_order_id" hidden value="{{ $work_order->id }}">
                            <input type="text" name="equipment_id" hidden value="{{ $equipment->id }}">
                            <table class="table-borderless">
                                <tbody>
                                    <tr>
                                        <td style="width: 120px">WO. Number</td>
                                        <td style="width: 20px">:</td>
                                        <td>{{ $work_order->ticket_number }}</td>
                                    </tr>
                                    <tr>
                                        <td>WO. SAP</td>
                                        <td>:</td>
                                        <td>{{ $work_order->wo_number_sap ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td>Date</td>
                                        <td>:</td>
                                        <td>{{ $work_order->date }}</td>
                                    </tr>
                                    <tr>
                                        <td>Location</td>
                                        <td>:</td>
                                        <td>{{ $work_order->relasi_area->sub_lokasi->name ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td>Equipment</td>
                                        <td>:</td>
                                        <td>{{ $equipment->name ?? '-' }}</td>
                                    </tr>
                                </tbody>
                            </table>
                            @livewire('form-checksheet', [
                                'tipe_equipment_id' => $tipe_equipment->id,
                            ])
                            {{-- <div class="p-2 my-3">
                                <h3 class="text-center">Form Checksheet</h3>
                                <hr>
                                @foreach ($parameter as $item)
                                    @if ($item->tipe == 'option')
                                        <div class="form-group">
                                            <label class="fw-bolder">{{ $loop->iteration }}. {{ $item->name }}
                                                @if ($item->photo_instruction != null)
                                                    <span>
                                                        <button type="button"
                                                            class="btn btn-gradient-primary btn-rounded btn-icon"
                                                            title="Show Instruction Photo" data-bs-toggle='modal'
                                                            data-bs-target='#photoModal'
                                                            data-photo='{{ asset('storage/' . $item->photo_instruction) }}'>
                                                            <i class="mdi mdi-magnify"></i>
                                                        </button>
                                                    </span>
                                                @endif
                                            </label>
                                            <select class="form-control form-control-lg" name="value[]" required>
                                                <option value="">- pilih value -</option>
                                                @php
                                                    $option = json_decode($item->option_form->value);
                                                @endphp
                                                @foreach ($option as $value)
                                                    <option value="{{ $value }}">{{ $value }}</option>
                                                @endforeach
                                            </select>
                                            <input type="text" name="parameter_id[]" value="{{ $item->id }}" hidden>
                                        </div>
                                    @else
                                        <div class="form-group">
                                            <label class="fw-bolder">{{ $loop->iteration }}. {{ $item->name }}
                                                @if ($item->photo_instruction != null)
                                                    <span>
                                                        <button type="button"
                                                            class="btn btn-gradient-primary btn-rounded btn-icon"
                                                            title="Show Instruction Photo" data-bs-toggle='modal'
                                                            data-bs-target='#photoModal'
                                                            data-photo='{{ asset('storage/' . $item->photo_instruction) }}'>
                                                            <i class="mdi mdi-magnify"></i>
                                                        </button>
                                                    </span>
                                                @endif
                                            </label>
                                            <input type="{{ $item->tipe }}" class="form-control" name="value[]"
                                                autocomplete="off" accept="image/*" required
                                                placeholder="input {{ $item->code }} {{ $item->satuan_id ? '(' . $item->satuan->name . ')' : '' }}"
                                                step="0.01">
                                            <input type="text" name="parameter_id[]" value="{{ $item->id }}" hidden>
                                        </div>
                                    @endif
                                    <hr class="my-5">
                                @endforeach
                            </div> --}}

                            <div class="form-group d-flex justify-content-end">
                                <button id="submitFormButton" disabled type="submit" form="editForm"
                                    class="btn btn-primary">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Photo Modal -->
    <div class="modal fade" id="photoModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Detail Photo Instruction</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="container">
                        <div class="mb-4 text-center align-middle">
                            <div class="border mx-auto">
                                <img src="#" id="photo_modal" class="img-thumbnail" alt="Tidak ada photo">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <!-- End Photo Modal -->

    @livewireScripts
@endsection

@section('javascript')
    <script>
        $(document).ready(function() {
            $('#photoModal').on('show.bs.modal', function(e) {
                var photo = $(e.relatedTarget).data('photo');

                document.getElementById("photo_modal").src = photo;
            });

            document.getElementById('form_id').addEventListener('change', function() {
                var submitButton = document.getElementById('submitFormButton');
                if (this.value !== '') {
                    submitButton.disabled = false;
                } else {
                    submitButton.disabled = true;
                }
            });
        })
    </script>
@endsection
