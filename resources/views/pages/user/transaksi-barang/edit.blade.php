@extends('layout.base')

@section('title-head')
    <title>Transaksi Barang</title>
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Edit Data Transaksi Barang</h4>
                        <form id="editForm" action="{{ route('transaksi-barang.update') }}" method="POST">
                            @csrf
                            @method('PUT')
                            <input type="text" name="id" value="{{ $transaksi_barang->id }}" required hidden>
                            <div class="form-group">
                                <label for="barang_id" class="required">Material</label>
                                <select class="tom-select-class" id="barang_id" name="barang_id" required>
                                    <option value="" selected disabled>- pilih material -</option>
                                    @foreach ($barang as $item)
                                        <option value="{{ $item->id }}"
                                            @if ($transaksi_barang->barang_id == $item->id) selected @endif>
                                            {{ $item->material_number ?? '#' }} -
                                            {{ $item->name ?? '#' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="qty" class="required">Quantity</label>
                                <input type="number" min="1" class="form-control" id="qty" name="qty"
                                    autocomplete="off" placeholder="jumlah material" value="{{ $transaksi_barang->qty }}"
                                    required>
                            </div>
                            <div class="form-group">
                                <label for="equipment_id" class="required">Equipment</label>
                                <select class="tom-select-class" id="equipment_id" name="equipment_id" required>
                                    <option value="" selected disabled>- pilih equipment -</option>
                                    @foreach ($equipment as $item)
                                        <option value="{{ $item->id }}"
                                            @if ($transaksi_barang->equipment_id == $item->id) selected @endif>
                                            {{ $item->name }} -
                                            ({{ $item->code ?? '-' }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="tanggal" class="required">Tanggal</label>
                                <input type="date" class="form-control" id="tanggal" name="tanggal" autocomplete="off"
                                    value="{{ $transaksi_barang->tanggal }}" required>
                            </div>
                            <div class="form-group d-flex justify-content-end">
                                <a href="{{ route('transaksi-barang.index') }}" type="button"
                                    class="btn btn-secondary">Cancel</a>
                                <button type="submit" form="editForm" class="btn btn-primary">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
