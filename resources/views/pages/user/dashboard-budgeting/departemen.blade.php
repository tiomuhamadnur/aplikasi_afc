@extends('layout.base')

@section('title-head')
    <title>Dashboard Budgeting</title>
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-md-12 stretch-card grid-margin">
                <div class="card mrt-dark-grey card-img-holder text-white">
                    <div class="card-body p-1 text-center">
                        <h3>
                            Departemen {{ $departemen->code ?? 'N/A' }}
                        </h3>
                        <h4>{{ $today }} <button data-bs-toggle="modal" data-bs-target="#evaluasiModal" class="bg-gradient-primary text-white" title="Pilih tanggal evaluasi">
                            <i class="mdi mdi-calendar"></i>
                            </button>
                        </h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-3 stretch-card grid-margin">
                <div class="card mrt-orange card-img-holder text-white">
                    <div class="card-body">
                        <img src="{{ asset('assets/images/dashboard/circle.svg') }}" class="card-img-absolute" alt="circle-image">
                        <h4 class="font-weight-normal mb-3">Anggaran
                            <i class="mdi mdi-database mdi-24px float-right"></i>
                        </h4>
                        <h3 class="mb-3">{{ $total_balance }}</h3>
                        <h6 class="card-text">Departemen {{ $departemen->code ?? 'N/A' }}
                        </h6>
                    </div>
                </div>
            </div>
            <div class="col-md-3 stretch-card grid-margin">
                <div class="card mrt-blue card-img-holder text-white">
                    <div class="card-body">
                        <img src="{{ asset('assets/images/dashboard/circle.svg') }}" class="card-img-absolute" alt="circle-image">
                        <h4 class="font-weight-normal mb-3">Penyerapan <i class="mdi mdi-database mdi-24px float-right"></i>
                        </h4>
                        <h3 class="mb-3">{{ $used_balance }}</h3>
                        <h6 class="card-text">Departemen {{ $departemen->code ?? 'N/A' }}
                        </h6>
                    </div>
                </div>
            </div>
            <div class="col-md-3 stretch-card grid-margin">
                <div class="card mrt-green card-img-holder text-white">
                    <div class="card-body">
                        <img src="{{ asset('assets/images/dashboard/circle.svg') }}" class="card-img-absolute" alt="circle-image">
                        <h4 class="font-weight-normal mb-3">Proyeksi
                            <i class="mdi mdi-database mdi-24px float-right"></i>
                        </h4>
                        <h3>{{ $planned_balance }}</h3>
                        <h6 class="card-text">Departemen {{ $departemen->code ?? 'N/A' }}
                        </h6>
                    </div>
                </div>
            </div>
            <div class="col-md-3 stretch-card grid-margin">
                <div class="card mrt-grey card-img-holder text-white">
                    <div class="card-body">
                        <img src="{{ asset('assets/images/dashboard/circle.svg') }}" class="card-img-absolute" alt="circle-image">
                        <h4 class="font-weight-normal mb-3">Sisa
                            <i class="mdi mdi-database mdi-24px float-right"></i>
                        </h4>
                        <h3>{{ $remaining_balance }}</h3>
                        <h6 class="card-text">Departemen {{ $departemen->code ?? 'N/A' }}
                        </h6>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4 grid-margin stretch-card">
                <div class="card" id="capexGraph">
                </div>
            </div>
            <div class="col-md-4 grid-margin stretch-card">
                <div class="card" id="opexGraph">
                </div>
            </div>
            <div class="col-md-4 grid-margin stretch-card">
                <div class="card" id="totalGraph">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card" style="height: auto">
                    <div id="fundGraph"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Evaluasi Modal -->
    <div class="modal fade" id="evaluasiModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Form Evaluasi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="evaluasiForm" action="{{ route('dashboard-budget.departemen') }}" method="GET"
                        class="forms-sample">
                        @csrf
                        @method('GET')
                        <div class="form-group">
                            <input type="text" name="departemen_uuid" value="{{ $departemen->uuid }}" hidden>
                            <label for="start_date" class="required">Pilih Periode</label>
                            <div class="input-group">
                                <input type="date" class="form-control" placeholder="Start Date"
                                    name="start_date" autocomplete="off" value="{{ $start_date ?? null }}">
                                <input type="date" class="form-control" placeholder="End Date"
                                    name="end_date" autocomplete="off" value="{{ $end_date ?? null }}">
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <a href="{{ route('dashboard-budget.departemen', ['departemen_uuid' => $departemen->uuid]) }}" class="btn btn-gradient-warning">Reset</a>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" form="evaluasiForm" class="btn btn-gradient-primary">Evaluate</button>
                </div>
            </div>
        </div>
    </div>
    <!-- End Evaluasi Modal -->
@endsection

@section('javascript')
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/highcharts-more.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>
    <script src="https://code.highcharts.com/modules/accessibility.js"></script>
    <script src="https://code.highcharts.com/modules/solid-gauge.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Highcharts.setOptions({
                lang: {
                    numericSymbols: ['rb', 'jt', 'M', 'T']
                },
                thousandsSep: '.',
            });

            const colors = ['#88BFF8', '#0053B2', '#43B53A', '#A3AAB1'];


            // SETIAP FUND
            // Data categories
            const categoriesFund = @json($categoriesFund);
            const seriesFund = @json($seriesFund);
            const namesFund = @json($namesFund);

            const minChartHeight = 400; // Tinggi minimum
            const additionalHeightPerCategory = 40; // Tinggi tambahan per kategori

            // Mengatur tinggi chart secara dinamis
            const chartHeight = minChartHeight + (categoriesFund.length * additionalHeightPerCategory);
            document.getElementById('fundGraph').style.height = chartHeight + 'px';

            // Membuat chart Highcharts
            Highcharts.chart('fundGraph', {
                chart: {
                    type: 'bar',
                },
                colors: colors,
                title: {
                    text: 'Penyerapan Anggaran Tiap Fund (Dept. {{ auth()->user()->relasi_struktur->departemen->code ?? 'N/A' }})',
                    align: 'left',
                },
                xAxis: {
                    categories: categoriesFund,
                    labels: {
                        formatter: function() {
                            return '<span title="' + namesFund[this.pos] + '">' + this.value + '</span>';
                        },
                        useHTML: true,  // Memastikan HTML digunakan untuk menampilkan nama fund saat dihover
                    },
                    tooltip: {
                        // Menggunakan pointFormatter untuk menampilkan tooltip yang lebih terstruktur
                        pointFormatter: function() {
                            return '<div style="padding: 5px; border: 1px solid #ccc; background-color: #f9f9f9; border-radius: 5px; font-size: 14px;">' +
                                '<b>' + namesFund[this.index] + '</b>' +
                                '</div>';
                        },
                    },
                },
                yAxis: {
                    title: {
                        text: 'Jumlah Anggaran (IDR)',
                    },
                    stackLabels: {
                        enabled: true,
                        formatter: function() {
                            return 'Rp. ' + Highcharts.numberFormat(this.total, 0, ',', '.');
                        },
                    },
                },
                legend: {
                    align: 'center',
                    verticalAlign: 'top',
                    layout: 'horizontal'
                },
                tooltip: {
                    formatter: function() {
                        return '<b>' + this.series.name + '</b>: Rp. ' +
                            Highcharts.numberFormat(this.y, 0, ',', '.');
                    }
                },
                plotOptions: {
                    series: {
                        stacking: 'normal',
                        dataLabels: {
                            enabled: true,
                            formatter: function() {
                                return 'Rp. ' + Highcharts.numberFormat(this.y, 0, ',', '.');
                            }
                        },
                    },
                },
                series: seriesFund,
            });



            // CAPEX
            const seriesCapex = @json($seriesCapex);
            Highcharts.chart('capexGraph', {
                chart: {
                    type: 'pie',
                    options3d: {
                        enabled: true,
                        alpha: 45
                    }
                },
                colors: colors, // Menambahkan warna khusus
                title: {
                    text: 'CAPEX (Dept. {{ $departemen->code }})', // Konten tetap, hanya format chart yang berubah
                    align: 'left'
                },
                subtitle: {
                    text: '',
                    align: 'left'
                },
                tooltip: {
                    formatter: function() {
                        return '<b>' + this.point.name + '</b>: Rp. ' +
                            Highcharts.numberFormat(this.y, 0, ',', '.') +
                            ' (' + Highcharts.numberFormat(this.percentage, 2, ',', '.') + '%)';
                    }
                },
                plotOptions: {
                    pie: {
                        allowPointSelect: true,
                        cursor: 'pointer',
                        dataLabels: {
                            enabled: true,
                            distance: 20, // Jarak label dari chart
                            format: '{point.percentage:.2f}%', // Tampilkan persentase
                            style: {
                                fontSize: '1.2em',
                                textOutline: 'none'
                            }
                        },
                        showInLegend: true // Menampilkan legend di bawah chart
                    }
                },
                legend: {
                    align: 'center',
                    verticalAlign: 'bottom',
                    layout: 'horizontal',
                    itemStyle: {
                        fontSize: '1em'
                    }
                },
                series: [{
                    name: 'Jumlah Anggaran (IDR)',
                    data: seriesCapex
                }]
            });


            // OPEX
            const seriesOpex = @json($seriesOpex);
            Highcharts.chart('opexGraph', {
                chart: {
                    type: 'pie',
                    options3d: {
                        enabled: true,
                        alpha: 45
                    }
                },
                colors: colors, // Menambahkan warna khusus
                title: {
                    text: 'OPEX (Dept. {{ $departemen->code }})', // Konten tetap, hanya format chart yang berubah
                    align: 'left'
                },
                subtitle: {
                    text: '',
                    align: 'left'
                },
                tooltip: {
                    formatter: function() {
                        return '<b>' + this.point.name + '</b>: Rp. ' +
                            Highcharts.numberFormat(this.y, 0, ',', '.') +
                            ' (' + Highcharts.numberFormat(this.percentage, 2, ',', '.') + '%)';
                    }
                },
                plotOptions: {
                    pie: {
                        allowPointSelect: true,
                        cursor: 'pointer',
                        dataLabels: {
                            enabled: true,
                            distance: 20, // Jarak label dari chart
                            format: '{point.percentage:.2f}%', // Tampilkan persentase
                            style: {
                                fontSize: '1.2em',
                                textOutline: 'none'
                            }
                        },
                        showInLegend: true // Menampilkan legend di bawah chart
                    }
                },
                legend: {
                    align: 'center',
                    verticalAlign: 'bottom',
                    layout: 'horizontal',
                    itemStyle: {
                        fontSize: '1em'
                    }
                },
                series: [{
                    name: 'Jumlah Anggaran (IDR)',
                    data: seriesOpex
                }]
            });


            // TOTAL
            const seriesCapexOpexTotal = @json($seriesCapexOpexTotal);
            Highcharts.chart('totalGraph', {
                chart: {
                    type: 'pie',
                    options3d: {
                        enabled: true,
                        alpha: 45
                    }
                },
                colors: colors, // Menambahkan warna khusus
                title: {
                    text: 'TOTAL (Dept. {{ $departemen->code }})', // Konten tetap, hanya format chart yang berubah
                    align: 'left'
                },
                subtitle: {
                    text: '',
                    align: 'left'
                },
                tooltip: {
                    formatter: function() {
                        return '<b>' + this.point.name + '</b>: Rp. ' +
                            Highcharts.numberFormat(this.y, 0, ',', '.') +
                            ' (' + Highcharts.numberFormat(this.percentage, 2, ',', '.') + '%)';
                    }
                },
                plotOptions: {
                    pie: {
                        allowPointSelect: true,
                        cursor: 'pointer',
                        dataLabels: {
                            enabled: true,
                            distance: 20, // Jarak label dari chart
                            format: '{point.percentage:.2f}%', // Tampilkan persentase
                            style: {
                                fontSize: '1.2em',
                                textOutline: 'none'
                            }
                        },
                        showInLegend: true // Menampilkan legend di bawah chart
                    }
                },
                legend: {
                    align: 'center',
                    verticalAlign: 'bottom',
                    layout: 'horizontal',
                    itemStyle: {
                        fontSize: '1em'
                    }
                },
                series: [{
                    name: 'Jumlah Anggaran (IDR)',
                    data: seriesCapexOpexTotal
                }]
            });

        });
    </script>
@endsection
