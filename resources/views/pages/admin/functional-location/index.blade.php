@extends('layout.base')

@section('title-head')
    <title>Admin | Functional Location</title>
    <!-- jsTree CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.2.1/themes/default/style.min.css" />
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Data Functional Location</h4>
                        <div class="btn-group my-2">
                            <button type="button" title="Add" class="btn btn-outline-primary btn-rounded btn-icon"
                                data-bs-toggle="modal" data-bs-target="#addModal">
                                <i class="mdi mdi-plus-circle"></i>
                            </button>
                            <button type="button" title="Filter" class="btn btn-outline-primary btn-rounded btn-icon">
                                <i class="mdi mdi-filter"></i>
                            </button>
                            <button type="button" title="Export" data-bs-toggle="modal" data-bs-target="#exportExcelModal"
                                class="btn btn-outline-primary btn-rounded btn-icon">
                                <i class="mdi mdi-file-export"></i>
                            </button>
                        </div>
                        <div class="table-responsive border p-3">
                            <div id="dataTree"></div>
                        </div>
                        <div class="table-responsive">
                            {{ $dataTable->table() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Modal -->
    <div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Form Add</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addForm" action="{{ route('fun_loc.store') }}" method="POST" class="forms-sample">
                        @csrf
                        @method('POST')
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" class="form-control" id="name" name="name" placeholder="Name"
                                autocomplete="off" required>
                        </div>
                        <div class="form-group">
                            <label for="code">Code</label>
                            <input type="text" class="form-control" id="code" name="code" placeholder="Code"
                                autocomplete="off" required>
                        </div>
                        <div class="form-group">
                            <label for="description">Description</label>
                            <input type="text" class="form-control" id="description" name="description"
                                placeholder="Description" autocomplete="off" required>
                        </div>
                        <div class="form-group">
                            <label for="parent_id">Parent <span class="text-primary">(optional)</span></label>
                            <select class="tom-select-class" name="parent_id" id="parent_id">
                                <option value="" selected disabled>- select parent -</option>
                                @foreach ($functional_location as $item)
                                    <option value="{{ $item->id }}">
                                        {{ $item->code }} - {{ $item->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" form="addForm" class="btn btn-gradient-primary me-2">Submit</button>
                </div>
            </div>
        </div>
    </div>
    <!-- End Add Modal -->

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
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" id="exportButton" onclick="exportExcel()"
                        class="btn btn-gradient-success me-2">Download</button>
                </div>
            </div>
        </div>
    </div>
    <!-- End Export Excel Modal -->
@endsection

@push('scripts')
    {{ $dataTable->scripts() }}
@endpush

@section('javascript')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.2.1/jstree.min.js"></script>

    <script type="text/javascript">
        $(function() {
            $('#dataTree').jstree({
                "core": {
                    "data": {
                        "url": "{{ route('api.data.functional_location') }}",
                        "dataType": "json",
                        "error": function(jqXHR, textStatus, errorThrown) {
                            console.error("Error loading tree data:", textStatus,
                                errorThrown);
                        }
                    },
                    "check_callback": true,
                    "themes": {
                        "variant": "small",
                        "icons": true
                    }
                },
                "checkbox": {
                    "keep_selected_style": false,
                    "three_state": false,
                    "cascade": "none"
                },
                "plugins": [
                    "types",
                    // "checkbox",
                ]
            });
        });


        // $('#dataTree').on('select_node.jstree', function(e, data) {
        //     var parentId = data.node.id;
        //     console.log("Node yang dipilih:", parentId);
        // });

        // function submitMe() {
        //     var selectedElmsIds = [];
        //     var selectedElms = $('#dataTree').jstree("get_selected", true);
        //     $.each(selectedElms, function() {
        //         selectedElmsIds.push(this.id);
        //     })

        //     console.log(selectedElmsIds);
        // }
    </script>

    <script>
        function exportExcel() {
            $('#datatable-excel').click();
        }
    </script>
@endsection
