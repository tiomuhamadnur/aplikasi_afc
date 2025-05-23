@extends('layout.base')

@section('title-head')
    <title>Create Work Order</title>
    <style>
        table.table td {
            padding: 8px;
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.2.1/themes/default/style.min.css" />
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Create Work Order</h4>
                        <div class="btn-group my-2">
                            <a href="{{ route('work-order.index') }}" title="Back"
                                class="btn btn-outline-primary btn-rounded">
                                <i class="mdi mdi-arrow-left"></i> Back
                            </a>
                        </div>
                        <form action="{{ route('work-order.store') }}" method="POST" id="formSubmit">
                            @csrf
                            @method('POST')
                            <div class="table-responsive">
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <tbody>
                                            <tr class="p-1">
                                                <td style="width: 130px; border-right: none;" class="fw-bolder">WO Date</td>
                                                <td style="width: 10px; border-left: none; border-right: none;">:</td>
                                                <td style="width: 250px; border-left: none;">
                                                    <input type="date" class="form-control form-control-sm"
                                                        name="date" id="date" autocomplete="off" required>
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
                                                        autocomplete="off" value="{{ auth()->user()->name }}" disabled>
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
                                                        name="name" id="name" placeholder="input order name"
                                                        required autocomplete="off">
                                                </td>
                                                <td></td>
                                                <td class="fw-bolder" style="width: 130px">Location</td>
                                                <td style="width: 10px">:</td>
                                                <td style="width: 260px">
                                                    <input type="text" class="form-control form-control-sm"
                                                        value="" disabled>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="fw-bolder">Description</td>
                                                <td>:</td>
                                                <td>
                                                    <input type="text" class="form-control form-control-sm"
                                                        name="description" id="description" placeholder="input description"
                                                        required autocomplete="off">
                                                </td>
                                                <td></td>
                                                <td class="fw-bolder">Work Center</td>
                                                <td>:</td>
                                                <td>
                                                    <input type="text" class="form-control form-control-sm"
                                                        value="{{ auth()->user()->relasi_struktur->departemen->name ?? '-' }}"
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
                                                            <option value="{{ $item->id }}">{{ $item->name }}
                                                                ({{ $item->code }})
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
                                                            <option value="{{ $item->id }}">
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
                                                            <option value="{{ $item->id }}">
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
                                                        placeholder="input WO SAP number" autocomplete="off">
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <hr class="my-5">

                                {{-- EQUIPMENTS or Functional Location --}}
                                <div>
                                    <h3>2. Equipments / Functional Location</h3>
                                </div>
                                <div class="form-group">
                                    <label for="objectOrder">Your Object Order?</label>
                                    <select class="tom-select-class" id="objectOrder" name="object_order_type" required>
                                        <option value="">- select object order -</option>
                                        <option value="equipment">Equipment</option>
                                        <option value="functional_location">Functional Location</option>
                                    </select>
                                </div>
                                <div class="table-responsive">
                                    {{-- <table class="table table-bordered text-center">
                                        <thead>
                                            <tr>
                                                <th class="fw-bolder" style="width: 10px"> # </th>
                                                <th class="fw-bolder"> Equipment Name </th>
                                                <th class="fw-bolder"> Equipment Code </th>
                                                <th class="fw-bolder"> Equipment Number </th>
                                                <th class="fw-bolder"> Type </th>
                                                <th class="fw-bolder"> Funct. Location </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>1</td>
                                                <td>{{ $gangguan->equipment->name ?? '-' }}</td>
                                                <td>{{ $gangguan->equipment->code ?? '-' }}</td>
                                                <td>{{ $gangguan->equipment->equipment_number ?? '-' }}</td>
                                                <td>{{ $gangguan->equipment->tipe_equipment->code ?? '-' }}</td>
                                                <td>{{ $gangguan->equipment->functional_location->code ?? '-' }}</td>
                                            </tr>
                                        </tbody>
                                    </table> --}}
                                    <div id="dataTree" class="border p-3 mt-2"></div>
                                    <div id="inputEquipmentContainer"></div>
                                </div>
                                <hr class="my-5">

                                {{-- TASKLIST --}}
                                <div>
                                    <h3>3. Tasklist</h3>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mx-auto mb-2">
                                    <label class="mb-0"></label>
                                    <button type="button" class="btn btn-success btn-rounded btn-icon" title="Add row"
                                        id="addRowTasklist">
                                        <i class="mdi mdi-plus-circle"></i>
                                    </button>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-bordered text-center" id="tasklistTable">
                                        <thead>
                                            <tr>
                                                <th class="fw-bolder" style="width: 10px"> No </th>
                                                <th class="fw-bolder" style="width: 50%"> Tasklist/Operation </th>
                                                <th class="fw-bolder"> Plan Duration <br> (Minutes) </th>
                                                <th class="fw-bolder"> Reference Document </th>
                                                <th class="fw-bolder" style="width: 5%"> Action </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>1</td>
                                                <td>
                                                    <input type="text" class="form-control" name="tasklist[]"
                                                        placeholder="input tasklist" required autocomplete="off">
                                                </td>
                                                <td>
                                                    <input type="number" class="form-control" name="duration[]"
                                                        placeholder="input plan duration (optional)" min="1"
                                                        autocomplete="off">
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control" name="reference[]"
                                                        placeholder="input reference document (optional)"
                                                        autocomplete="off">
                                                </td>
                                                <td></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <hr class="my-5">

                                {{-- SPARE PARTS --}}
                                <div>
                                    <h3>4. Spare Parts</h3>
                                </div>
                                <div class="form-group">
                                    <div class="form-group">
                                        <label for="is_changed">Need Sparepart?</label>
                                        <select class="tom-select-class" id="is_changed" required>
                                            <option value="">- select option -</option>
                                            <option value="0">No</option>
                                            <option value="1">Yes</option>
                                        </select>
                                    </div>
                                    <div class="form-group" id="sparePartContainer" style="display: none">
                                        <div class="d-flex justify-content-between align-items-center mx-auto mb-2">
                                            <label for="barang_id" class="mb-0">Spare Part</label>
                                            <button type="button" class="btn btn-success btn-rounded btn-icon"
                                                title="Add row" id="addRow">
                                                <i class="mdi mdi-plus-circle"></i>
                                            </button>
                                        </div>
                                        <div class="input-group" id="inputContainer">
                                            <select class="tom-select-class col-8" id="barang_id" name="barang_ids[]">
                                                <option value="" selected disabled>- pilih spare part -</option>
                                                @foreach ($barang as $item)
                                                    <option value="{{ $item->id }}">
                                                        ({{ $item->material_number ?? '-' }})
                                                        - {{ $item->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <input type="number" class="form-control col-3" id="qty"
                                                name="qty[]" placeholder="qty" min="1">
                                        </div>
                                    </div>
                                </div>
                                <hr class="my-5">


                                {{-- MAN POWER --}}
                                <div>
                                    <h3>5. Man Power</h3>
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
                                                            style="height: 25px; width:25px;" name="user_ids[]"
                                                            value="{{ $item->id }}">
                                                    </td>
                                                    <td class="text-start">{{ $item->name }}</td>
                                                    <td>{{ $item->jabatan->name ?? 'NA' }}</td>
                                                    <td>{{ $item->tipe_employee->name ?? 'N/A' }}</td>
                                                    <td class="text-start">{{ $item->perusahaan->name ?? 'N/A' }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <hr class="my-5">
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
    {{-- Spare Part --}}
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
                    <option value="" selected disabled>- select spare part -</option>
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

    {{-- Tasklist --}}
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
                        <input type="text" class="form-control" name="reference[]" placeholder="input reference document (optional)" autocomplete="off">
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

    {{-- Equipment --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.2.1/jstree.min.js"></script>

    <script type="text/javascript">
        $(function() {
            $('#dataTree').jstree({
                "core": {
                    // 'data': data
                    'data': {
                        'url': "{{ route('api.data.asset', ['relasi_struktur_id' => auth()->user()->relasi_struktur_id]) }}", // URL untuk mengambil data tree
                        'dataType': 'json' // Data dikembalikan dalam bentuk JSON
                    },
                    "themes": {
                        "variant": "default"
                    }
                },
                "checkbox": {
                    "keep_selected_style": false,
                    "three_state": false,
                    "cascade": "none",
                },
                "types": {
                    "default": {
                        "icon": "mdi mdi-database"
                    },
                    "demo": {
                        "icon": "jstree-file" // Ikon untuk node yang tidak punya children
                    }
                },
                "plugins": [
                    "types",
                    "checkbox"
                ]
            });
        });

        $('#dataTree').on('select_node.jstree', function(e, data) {
            var parentId = data.node.id;
            console.log("Node yang dipilih:", parentId);

            // Membuat elemen input baru
            var newInput = $('<input>', {
                type: 'text',
                name: 'objectOrderValue[]', // Menyimpan nama yang sama
                value: parentId, // Mengisi nilai dengan parentId
                class: 'inputObjectOrder',
                hidden: true,
            });

            // Menambahkan input baru ke dalam DOM, misalnya di dalam sebuah div dengan ID "inputContainer"
            $('#inputEquipmentContainer').append(newInput);

            // Log nilai input baru
            console.log("Input baru ditambahkan:", newInput.val());
        });

        // function submitMe() {
        //     var checked_ids = [];
        //     $("#dataTree").jstree("get_checked", null, true).each(function() {
        //         checked_ids.push(this.id);
        //     });
        //     doStuff(checked_ids);

        //     var selectedElmsIds = [];
        //     var selectedElms = $('#dataTree').jstree("get_selected", true);
        //     $.each(selectedElms, function() {
        //         selectedElmsIds.push(this.id);
        //     })

        //     console.log(selectedElmsIds);
        // }
    </script>
@endsection
