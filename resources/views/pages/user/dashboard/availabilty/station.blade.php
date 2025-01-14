@extends('layout.base')

@section('title-head')
    <title>Availability Per Station</title>
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="btn-group my-2">
                            <a href="{{ route('dashboard.index') }}" title="Back" class="btn btn-gradient-primary btn-rounded">
                                <i class="mdi mdi-arrow-left"></i> Back
                            </a>
                        </div>
                        <div class="row">
                            <div class="col-md-12 grid-margin stretch-card">
                                <div class="card" id="availabilityStationGraph">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('javascript')
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/highcharts-more.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>
    <script src="https://code.highcharts.com/modules/accessibility.js"></script>
    <script>
        Highcharts.chart('availabilityStationGraph', {
            chart: {
                type: 'column'
            },
            plotOptions: {
                series: {
                    cursor: 'pointer',
                    point: {
                        events: {
                            click: function() {
                                var url = this.options.url; // Mengambil URL dari data point
                                window.location.href = url;
                            }
                        }
                    },
                    dataLabels: {
                        enabled: true,
                        formatter: function() {
                            return this.y.toFixed(3) + '%'; // Membatasi 2 angka di belakang koma
                        },
                        style: {
                            fontSize: '13px',
                            fontWeight: 'bold',
                            color: '#000000'
                        }
                    }
                }
            },
            title: {
                text: 'Availability Per Stasiun - ({{ $bulan_name ?? '-' }} {{ $tahun ?? '-' }})',
                align: 'center',
            },
            xAxis: {
                categories: {!! json_encode(array_column($data, 'stasiun')) !!}
            },
            yAxis: {
                title: {
                    text: 'Availability (%)'
                }
            },
            tooltip: {
                    formatter: function() {
                        return `<br/>Availability: <b>${this.y.toFixed(3)}%</b>`;
                    }
                },
            series: [{
                name: 'Availability',
                color: '#cb6ce6',
                data: {!! json_encode(
                    array_map(function ($item) {
                        return ['y' => $item['availability'], 'url' => $item['url']];
                    }, $data),
                ) !!}
            }],
            legend: {
                backgroundColor: '#FCFFC5',
                borderColor: '#C98657',
                borderWidth: 1
            },
        });

        // Update tinggi chart saat layar diubah ukurannya
        window.addEventListener('resize', function() {
            Highcharts.charts.forEach(function(chart) {
                if (chart) {
                    chart.update({
                        chart: {
                            height: window.innerHeight *
                                0.8 // Tetap proporsional 60% dari tinggi layar
                        }
                    });
                }
            });
        });
    </script>
@endsection
