@extends('layout.base')

@section('title-head')
    <title>Trend Checksheet - {{ $equipment->name }}</title>
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Data Trend Checksheet</h4>
                        <div class="btn-group my-2">
                            <button type="button" title="Filter" data-bs-toggle="modal" data-bs-target="#filterModal"
                                class="btn btn-outline-primary btn-rounded">
                                <i class="mdi mdi-filter"></i> Filter
                            </button>
                        </div>
                        <div id="trendChecksheetHistoryGraph"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- <!-- Add Filter -->
    <div class="modal fade" id="filterModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Form Filter</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="filterForm" action="{{ route('transaksi-barang.trend.monthly') }}" method="GET"
                        class="forms-sample">
                        <div class="form-group">
                            <label for="tahun">Tahun</label>
                            @php
                                $year = 2019;
                            @endphp
                            <select class="form-control form-control-lg" name="y" id="tahun">
                                @for ($y = $year; $y < $year + 11; $y++)
                                    <option value="{{ $y }}" @if ($y == $tahun) selected @endif>
                                        {{ $y }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="bulan">Bulan</label>
                            <select class="form-control form-control-lg" name="m" id="bulan">
                                <option value="1" @if ($bulan == 1) selected @endif>January</option>
                                <option value="2" @if ($bulan == 2) selected @endif>February</option>
                                <option value="3" @if ($bulan == 3) selected @endif>March</option>
                                <option value="4" @if ($bulan == 4) selected @endif>April</option>
                                <option value="5" @if ($bulan == 5) selected @endif>May</option>
                                <option value="6" @if ($bulan == 6) selected @endif>June</option>
                                <option value="7" @if ($bulan == 7) selected @endif>July</option>
                                <option value="8" @if ($bulan == 8) selected @endif>August</option>
                                <option value="9" @if ($bulan == 9) selected @endif>September</option>
                                <option value="10" @if ($bulan == 10) selected @endif>October</option>
                                <option value="11" @if ($bulan == 11) selected @endif>November</option>
                                <option value="12" @if ($bulan == 12) selected @endif>December</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" form="filterForm" class="btn btn-gradient-primary me-2">Filter</button>
                </div>
            </div>
        </div>
    </div>
    <!-- End Add Filter --> --}}
@endsection

@section('javascript')
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/highcharts-more.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>
    <script src="https://code.highcharts.com/modules/accessibility.js"></script>
    <script>
        var dates = @json($dates);
        var values = @json($values);


        Highcharts.chart('trendChecksheetHistoryGraph', {
            chart: {
                type: 'line'
            },
            plotOptions: {
                series: {
                    cursor: 'pointer',
                    dataLabels: {
                        enabled: true,
                        format: '{y}',
                        style: {
                            fontSize: '13px',
                            fontWeight: 'bold',
                            color: '#000000'
                        }
                    }
                }
            },
            title: {
                text: '{{ $equipment->name ?? '-' }}',
                align: 'left',
                margin: 50
            },
            xAxis: {
                categories: dates
            },
            yAxis: {
                title: {
                    text: 'Value'
                },
                plotBands: [{ // Menambahkan zona
                    from: parseFloat('{{ $parameter->min_value }}'), // Batas bawah zona
                    to: parseFloat('{{ $parameter->max_value }}'), // Batas atas zona
                    color: '#8cff94', // Warna zona dengan transparansi
                    label: {
                        text: 'In Tolerance', // Label untuk zona
                        align: 'center', // Posisi horizontal label
                        verticalAlign: 'middle', // Posisi vertikal label
                        style: {
                            color: '#000000', // Warna teks
                            backgroundColor: '#b3b3b3', // Warna latar belakang
                            fontSize: '15px', // Ukuran font
                            fontWeight: 'bold', // Ketebalan font
                            padding: '5px', // Jarak antara teks dan batas label
                            borderRadius: '5px', // Radius border untuk sudut membulat
                            borderColor: '#C98657', // Warna border
                            borderWidth: 1 // Lebar border
                        }
                    }
                }]
            },
            series: [{
                name: '{{ $parameter->name }}',
                color: 'blue',
                data: values,
            }],
            legend: {
                backgroundColor: '#FCFFC5',
                borderColor: '#C98657',
                borderWidth: 1
            },
        });
    </script>
@endsection