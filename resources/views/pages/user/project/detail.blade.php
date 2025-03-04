@extends('layout.base')

@section('title-head')
    <title>Detail Project</title>
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Project {{ $project->name ?? null }}</h4>
                        <div class="btn-group my-2">
                            <button type='button' class='btn btn-outline-primary btn-rounded btn-icon'
                                onclick="window.location.href='{{ route('project.index') }}'" title='Back'>
                                <i class='mdi mdi-arrow-left'></i>
                            </button>
                            <button type="button" title="Add" class="btn btn-outline-primary btn-rounded btn-icon"
                                data-bs-toggle="modal" data-bs-target="#addModal">
                                <i class="mdi mdi-plus-circle"></i>
                            </button>
                            <button type="button" title="Export to Excel" data-bs-toggle="modal"
                                data-bs-target="#exportExcelModal" class="btn btn-outline-primary btn-rounded btn-icon">
                                <i class="mdi mdi-file-export"></i>
                            </button>
                            <button type="button" title="Show all activity"
                                onclick="location.href='{{ route('budget-absorption.index') }}'"
                                class="btn btn-outline-primary btn-rounded btn-icon">
                                <i class="mdi mdi-file-tree"></i>
                            </button>
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
                    <form id="addForm" action="{{ route('budget-absorption.store') }}" method="POST" class="forms-sample"
                        enctype="multipart/form-data">
                        @csrf
                        @method('POST')
                        <div class="form-group">
                            <label for="project_id" class="required">Project</label>
                            <select class="tom-select-class" name="project_id" id="project_id" required>
                                <option value="{{ $project->id }}">
                                    {{ $project->name ?? '-' }} ({{ $project->fund_source->fund->name ?? '-' }} -
                                    {{ $project->fund_source->fund->code ?? '-' }})
                                </option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="name" class="required">Activity Name</label>
                            <input type="text" class="form-control" id="name" name="name"
                                placeholder="input activity name" autocomplete="off" required>
                        </div>
                        {{-- <div class="form-group">
                            <label for="description">Description</label>
                            <input type="text" class="form-control" id="description" name="description"
                                placeholder="input project description" autocomplete="off" required>
                        </div> --}}
                        <div class="form-group">
                            <label for="value" class="required">Value (IDR)</label>
                            <input type="number" min="0" class="form-control" id="value" name="value"
                                placeholder="input activity value" autocomplete="off" required>
                        </div>
                        <div class="form-group">
                            <label for="activity_date" class="required">Activity Date</label>
                            <input type="date" min="0" class="form-control" id="activity_date"
                                name="activity_date" autocomplete="off" required>
                        </div>
                        <div class="form-group">
                            <label for="paid_date">Paid Date <span class="text-info">(optional)</span></label>
                            <input type="date" min="0" class="form-control" id="paid_date" name="paid_date"
                                autocomplete="off">
                        </div>
                        <div class="form-group">
                            <label for="po_number_sap">PO Number SAP <span class="text-info">(optional)</span></label>
                            <input type="number" min="1" class="form-control" id="po_number_sap"
                                name="po_number_sap" placeholder="input PO number SAP" autocomplete="off">
                        </div>
                        <div class="form-group">
                            <label for="attachment">Attachment Document <span class="text-info">(optional)</span></label>
                            <input type="file" class="form-control" id="attachment" name="attachment"
                                accept="application/pdf">
                        </div>
                        <div class="form-group">
                            <label for="status" class="required">Status</label>
                            <select class="tom-select-class" name="status" id="status" required>
                                <option value="" disabled selected>- select status -</option>
                                <option value="Planned">Planned</option>
                                <option value="Realisasi Kegiatan">Realisasi Kegiatan</option>
                                <option value="Realisasi Pembayaran">Realisasi Pembayaran</option>
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

    <!-- Delete Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Are you sure?</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="deleteForm" action="{{ route('budget-absorption.delete') }}" method="POST"
                        class="forms-sample">
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

    <!-- Export Excel Modal -->
    <div class="modal fade" id="exportExcelModal" tabindex="-1" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
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
    <script>
        $(document).ready(function() {
            $('#deleteModal').on('show.bs.modal', function(e) {
                var id = $(e.relatedTarget).data('id');
                $('#id_delete').val(id);
            });
        });
    </script>

    <script>
        function exportExcel() {
            document.getElementById('datatable-excel').click();
        }
    </script>
@endsection
