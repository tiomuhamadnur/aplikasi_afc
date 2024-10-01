@extends('layout.base')

@section('title-head')
    <title>Admin | Asset</title>
    <!-- jsTree CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.2.1/themes/default/style.min.css" />
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Data Asset</h4>
                        <div class="btn-group my-2">
                            <button type="button" title="Add" class="btn btn-outline-primary btn-rounded btn-icon"
                                data-bs-toggle="modal" data-bs-target="#addModal">
                                <i class="mdi mdi-plus-circle"></i>
                            </button>
                            <button type="button" title="Filter" class="btn btn-outline-primary btn-rounded btn-icon">
                                <i class="mdi mdi-filter"></i>
                            </button>
                            <button type="button" title="Export" class="btn btn-outline-primary btn-rounded btn-icon">
                                <i class="mdi mdi-file-export"></i>
                            </button>
                        </div>
                        <div class="table-responsive border p-3">
                            <div id="dataTree"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('javascript')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.2.1/jstree.min.js"></script>

    <script type="text/javascript">
        $(function() {
            $('#dataTree').jstree({
                "core": {
                    // 'data': data
                    'data': {
                        'url': "{{ route('api.data.asset') }}", // URL untuk mengambil data tree
                        'dataType': 'json' // Data dikembalikan dalam bentuk JSON
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
                    "types", "checkbox"
                ]
            });
        });

        $('#dataTree').on('select_node.jstree', function(e, data) {
            var parentId = data.node.id;
            console.log("Node yang dipilih:", parentId);
        });

        function submitMe() {
            // var checked_ids = [];
            // $("#dataTree").jstree("get_checked", null, true).each(function() {
            //     checked_ids.push(this.id);
            // });
            // doStuff(checked_ids);

            var selectedElmsIds = [];
            var selectedElms = $('#dataTree').jstree("get_selected", true);
            $.each(selectedElms, function() {
                selectedElmsIds.push(this.id);
            })

            console.log(selectedElmsIds);
        }
    </script>
@endsection
