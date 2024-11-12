@extends('layout.base')

@section('title-head')
    <title>Admin | Edit Fund Source</title>
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Edit Data Fund Source</h4>
                        <form id="editForm" action="{{ route('fund-source.update') }}" method="POST">
                            @csrf
                            @method('PUT')
                            <input type="text" name="id" value="{{ $fund_source->id }}" hidden>
                            <div class="form-group">
                                <label for="fund_id">Fund</label>
                                <select class="tom-select-class" name="fund_id" id="fund_id" required>
                                    <option value="" selected disabled>- select fund -</option>
                                    @foreach ($fund as $item)
                                        <option value="{{ $item->id }}"
                                            @if ($item->id == $fund_source->fund_id) selected @endif>
                                            {{ $item->code ?? '-' }} {{ $item->name ?? '-' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="balance">Balance (IDR)</label>
                                <input type="number" min="0" class="form-control" id="balance" name="balance"
                                    placeholder="Input Balance" autocomplete="off" required
                                    value="{{ $fund_source->balance }}">
                            </div>
                            <div class="form-group">
                                <label for="current_balance">Current Balance (IDR)</label>
                                <input type="number" min="0" class="form-control" id="current_balance"
                                    name="current_balance" placeholder="Input Current Balance" autocomplete="off" required
                                    value="{{ $fund_source->current_balance }}">
                            </div>
                            <div class="form-group">
                                <label for="start_period">Start Period</label>
                                <input type="date" class="form-control" id="start_period" name="start_period"
                                    placeholder="Start Period" autocomplete="off" required
                                    value="{{ $fund_source->start_period }}">
                            </div>
                            <div class="form-group">
                                <label for="end_period">End Period</label>
                                <input type="date" class="form-control" id="end_period" name="end_period"
                                    placeholder="End Period" autocomplete="off" required
                                    value="{{ $fund_source->end_period }}">
                            </div>
                            <div class="form-group d-flex justify-content-end">
                                <a href="{{ route('fund-source.index') }}" type="button"
                                    class="btn btn-secondary">Cancel</a>
                                <button type="submit" form="editForm" class="btn btn-primary">Update</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
