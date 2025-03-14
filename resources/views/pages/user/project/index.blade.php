@extends('layout.base')

@section('title-head')
    <title>Project</title>
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Data Project</h4>
                        <div class="btn-group my-2">
                            <button type="button" title="Add" class="btn btn-outline-primary btn-rounded btn-icon"
                                data-bs-toggle="modal" data-bs-target="#addModal">
                                <i class="mdi mdi-plus-circle"></i>
                            </button>
                            <button type="button" title="Filter" data-bs-toggle="modal" data-bs-target="#filterModal"
                                class="btn btn-outline-primary btn-rounded btn-icon">
                                <i class="mdi mdi-filter"></i>
                            </button>
                            <button type="button" title="Export to Excel" data-bs-toggle="modal"
                                data-bs-target="#exportExcelModal" class="btn btn-outline-primary btn-rounded btn-icon">
                                <i class="mdi mdi-file-export"></i>
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
                    <form id="addForm" action="{{ route('project.store') }}" method="POST" class="forms-sample"
                        enctype="multipart/form-data">
                        @csrf
                        @method('POST')
                        <div class="form-group">
                            <label for="fund_source_id" class="required">Fund Source</label>
                            <select class="tom-select-class" name="fund_source_id" id="fund_source_id" required>
                                <option value="" disabled selected>- select fund source -</option>
                                @foreach ($fund_source as $item)
                                    <option value="{{ $item->id }}">
                                        Tahun {{ $item->year ?? '-' }} - {{ $item->fund->code ?? '-' }} - {{ $item->fund->name ?? '-' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="name" class="required">Project Name</label>
                            <input type="text" class="form-control" id="name" name="name"
                                placeholder="input project name" autocomplete="off" required>
                        </div>
                        <div class="form-group">
                            <label for="value" class="required">Value (IDR)</label>
                            <input type="number" class="form-control" id="value" name="value"
                                placeholder="input project value" min="1" autocomplete="off" required>
                        </div>
                        {{-- <div class="form-group">
                            <label for="description">Description</label>
                            <input type="text" class="form-control" id="description" name="description"
                                placeholder="input project description" autocomplete="off" required>
                        </div>
                        <div class="form-group">
                            <label for="start_period" class="required">Start Period</label>
                            <input type="date" class="form-control" id="start_period" name="start_period"
                                placeholder="Start Period" autocomplete="off" required>
                        </div>
                        <div class="form-group">
                            <label for="end_period" class="required">End Period</label>
                            <input type="date" class="form-control" id="end_period" name="end_period"
                                placeholder="End Period" autocomplete="off" required>
                        </div> --}}
                        <div class="form-group">
                            <label for="departemen_id" class="required">Project Owner</label>
                            <select class="tom-select-class" name="departemen_id" id="departemen_id" required>
                                <option value="" disabled selected>- select project owner -</option>
                                @foreach ($departemen as $item)
                                    <option value="{{ $item->id }}">
                                        {{ $item->name ?? '-' }} ({{ $item->code ?? '-' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="perusahaan_id" class="required">Company</label>
                            <select class="tom-select-class" name="perusahaan_id" id="perusahaan_id" required>
                                <option value="" disabled selected>- select company -</option>
                                @foreach ($perusahaan as $item)
                                    <option value="{{ $item->id }}">
                                        {{ $item->name ?? '-' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="status_budgeting_id" class="required">Status</label>
                            <select class="tom-select-class" name="status_budgeting_id" id="status_budgeting_id" required>
                                <option value="" disabled selected>- select status -</option>
                                @foreach ($status_budgeting as $item)
                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
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

    <!-- Filter Modal -->
    <div class="modal fade" id="filterModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Form Filter</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="filterForm" action="{{ route('project.index') }}" method="GET" class="forms-sample">
                        @csrf
                        @method('GET')
                        <div class="form-group">
                            <label for="fund_source_id">Fund Source</label>
                            <select class="tom-select-class" name="fund_source_id" id="fund_source_id">
                                <option value="" selected disabled>- select fund source -</option>
                                @foreach ($fund_source as $item)
                                    <option value="{{ $item->id }}" @if ($item->id == $fund_source_id ?? null) selected @endif>
                                        Tahun {{ $item->year ?? '-' }} - {{ $item->fund->code }} - {{ $item->fund->name }} (RKA: @currency($item->balance))
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="departemen_id">Project Owner</label>
                            <select class="tom-select-class" name="departemen_id" id="departemen_id">
                                <option value="" selected disabled>- select department -</option>
                                @foreach ($departemen as $item)
                                    <option value="{{ $item->id }}" @if ($item->id == $departemen_id ?? null) selected @endif>
                                        {{ $item->code }} ({{ $item->name }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="type">Type</label>
                            <select class="tom-select-class" name="type" id="type">
                                <option value="" selected disabled>- select type -</option>
                                <option value="capex" @selected($type === 'capex')>Capex</option>
                                <option value="opex" @selected($type === 'opex')>Opex</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="status_budgeting_id">Status</label>
                            <select class="tom-select-class" name="status_budgeting_id" id="status_budgeting_id">
                                <option value="" selected disabled>- select status -</option>
                                @foreach ($status_budgeting as $item)
                                    <option value="{{ $item->id }}" @selected($item->id == $status_budgeting_id)>{{ $item->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        {{-- <div class="form-group">
                            <label for="start_date">Period</label>
                            <div class="input-group">
                                <input type="text" id="start_date" onfocus="(this.type='date')"
                                    onblur="(this.type='text')" class="form-control" placeholder="Start Date"
                                    name="start_date" autocomplete="off" value="{{ $start_date ?? null }}">
                                <input type="text" id="end_date" onfocus="(this.type='date')"
                                    onblur="(this.type='text')" class="form-control" placeholder="End Date"
                                    name="end_date" autocomplete="off" value="{{ $end_date ?? null }}">
                            </div>
                        </div> --}}
                        <div class="form-group">
                            <label for="year" class="required">Year</label>
                            <select class="tom-select-class" name="year" id="year" required>
                                <option value="" disabled selected>- select year -</option>
                                @foreach ($years as $year)
                                    <option value="{{ $year }}" @if($year == $this_year) selected @endif>
                                        {{ $year }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <a href="{{ route('project.index') }}" class="btn btn-gradient-warning">Reset</a>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" form="filterForm" class="btn btn-gradient-primary">Filter</button>
                </div>
            </div>
        </div>
    </div>
    <!-- End Filter Modal -->

    <!-- Delete Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Are you sure?</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="deleteForm" action="{{ route('project.delete') }}" method="POST" class="forms-sample">
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
