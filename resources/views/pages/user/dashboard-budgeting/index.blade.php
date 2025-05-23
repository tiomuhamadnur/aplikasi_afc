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
                            Divisi {{ auth()->user()->relasi_struktur->divisi->name ?? 'N/A' }}
                            ({{ auth()->user()->relasi_struktur->divisi->code ?? 'N/A' }})
                        </h3>
                        <h4>{{ $today }} <button data-bs-toggle="modal" data-bs-target="#evaluasiModal"
                                class="btn btn-gradient-primary text-white" title="Pilih tanggal evaluasi">
                                <i class="mdi mdi-calendar"></i>
                            </button>
                            @if (auth()->user()->role_id == 1)
                                <button title="Sync Data to Looker" data-bs-toggle="modal" data-bs-target="#syncLookerModal"
                                    class="btn btn-gradient-success text-white">
                                    <i class="mdi mdi-sync"></i>
                                </button>
                            @endif
                        </h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-3 stretch-card grid-margin">
                <div class="card card-img-holder mrt-orange text-white">
                    <div class="card-body">
                        <img src="{{ asset('assets/images/dashboard/circle.svg') }}" class="card-img-absolute"
                            alt="circle-image">
                        <h4 class="font-weight-normal mb-3">Anggaran
                            <i class="mdi mdi-database mdi-24px float-right"></i>
                        </h4>
                        <h3 class="mb-3">{{ $total_balance }}</h3>
                        <h6 class="card-text">Divisi {{ auth()->user()->relasi_struktur->divisi->code ?? 'N/A' }}
                        </h6>
                    </div>
                </div>
            </div>
            <div class="col-md-3 stretch-card grid-margin">
                <div class="card mrt-blue card-img-holder text-white">
                    <div class="card-body">
                        <img src="{{ asset('assets/images/dashboard/circle.svg') }}" class="card-img-absolute"
                            alt="circle-image">
                        <h4 class="font-weight-normal mb-3">Penyerapan <i class="mdi mdi-database mdi-24px float-right"></i>
                        </h4>
                        <h3 class="mb-3">{{ $used_balance }}</h3>
                        <h6 class="card-text">Divisi {{ auth()->user()->relasi_struktur->divisi->code ?? 'N/A' }}
                        </h6>
                    </div>
                </div>
            </div>
            <div class="col-md-3 stretch-card grid-margin">
                <div class="card mrt-green card-img-holder text-white">
                    <div class="card-body">
                        <img src="{{ asset('assets/images/dashboard/circle.svg') }}" class="card-img-absolute"
                            alt="circle-image">
                        <h4 class="font-weight-normal mb-3">Proyeksi
                            <i class="mdi mdi-database mdi-24px float-right"></i>
                        </h4>
                        <h3>{{ $planned_balance }}</h3>
                        <h6 class="card-text">Divisi {{ auth()->user()->relasi_struktur->divisi->code ?? 'N/A' }}
                        </h6>
                    </div>
                </div>
            </div>
            <div class="col-md-3 stretch-card grid-margin">
                <div class="card mrt-grey card-img-holder text-white">
                    <div class="card-body">
                        <img src="{{ asset('assets/images/dashboard/circle.svg') }}" class="card-img-absolute"
                            alt="circle-image">
                        <h4 class="font-weight-normal mb-3">Sisa
                            <i class="mdi mdi-database mdi-24px float-right"></i>
                        </h4>
                        <h3>{{ $remaining_balance }}</h3>
                        <h6 class="card-text">Divisi {{ auth()->user()->relasi_struktur->divisi->code ?? 'N/A' }}
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
                <div class="card" style="height: auto;">
                    <div id="tiapDepartemenGraph"></div>
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
                    <form id="evaluasiForm" action="{{ route('dashboard-budget.index') }}" method="GET"
                        class="forms-sample">
                        @csrf
                        @method('GET')
                        <div class="form-group">
                            <label for="start_date" class="required">Pilih Periode</label>
                            <div class="input-group">
                                <input type="date" class="form-control" placeholder="Start Date" name="start_date"
                                    autocomplete="off" value="{{ $start_date ?? null }}">
                                <input type="date" class="form-control" placeholder="End Date" name="end_date"
                                    autocomplete="off" value="{{ $end_date ?? null }}">
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <a href="{{ route('dashboard-budget.index') }}" class="btn btn-gradient-warning">Reset</a>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" form="evaluasiForm" class="btn btn-gradient-primary">Evaluate</button>
                </div>
            </div>
        </div>
    </div>
    <!-- End Evaluasi Modal -->


    <!-- Sync Looker Modal -->
    <div class="modal fade" id="syncLookerModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Are you sure?</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center">
                        <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/4/4c/Looker.svg/768px-Looker.svg.png?20210222181719"
                            alt="Excel" style="height: 110px;">
                    </div>
                    <form class="hidden" id="syncLooker" action="{{ route('google.looker.sync.budgeting') }}"
                        method="POST">
                        @csrf
                        @method('POST')
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" form="syncLooker" class="btn btn-gradient-success me-2">Sync Looker</button>
                </div>
            </div>
        </div>
    </div>
    <!-- End Sync Looker Modal -->
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

            // SETIAP DEPARTEMEN
            const categoriesDepartemen = @json($categoriesDepartemen);
            const seriesData = @json($series);


            const minHeightTiapDepartemenGraph = 400;
            const extraHeightPerCategory = 40;

            const chartHeightTiapDepartemen = minHeightTiapDepartemenGraph + (categoriesDepartemen.length *
                extraHeightPerCategory);
            document.getElementById('tiapDepartemenGraph').style.height = chartHeightTiapDepartemen + 'px';

            Highcharts.chart('tiapDepartemenGraph', {
                chart: {
                    type: 'column',
                },
                colors: colors,
                title: {
                    text: 'Penyerapan Anggaran Tiap Departemen (Div. {{ auth()->user()->relasi_struktur->divisi->code ?? 'N/A' }})',
                    align: 'left',
                },
                xAxis: {
                    categories: categoriesDepartemen.map(dept => dept.name),
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
                tooltip: {
                    formatter: function() {
                        return '<b>' + this.series.name + '</b>: Rp. ' +
                            Highcharts.numberFormat(this.y, 0, ',', '.');
                    }
                },
                plotOptions: {
                    column: {
                        stacking: 'normal',
                        dataLabels: {
                            enabled: true,
                            formatter: function() {
                                return 'Rp. ' + Highcharts.numberFormat(this.y, 0, ',', '.');
                            }
                        },
                    },
                    series: {
                        cursor: 'pointer',
                        point: {
                            events: {
                                click: function() {
                                    const department = categoriesDepartemen[this.index];
                                    if (department && department.url) {
                                        location.href = department.url
                                    }
                                }
                            }
                        }
                    }
                },
                series: seriesData,
                responsive: {
                    rules: [{
                        condition: {
                            maxWidth: 500
                        },
                        chartOptions: {
                            legend: {
                                align: 'center',
                                verticalAlign: 'bottom',
                                layout: 'horizontal'
                            },
                            yAxis: {
                                labels: {
                                    align: 'left',
                                    x: 0,
                                    y: -5
                                },
                                title: {
                                    text: null
                                }
                            },
                            subtitle: {
                                text: null
                            },
                            credits: {
                                enabled: false
                            }
                        }
                    }]
                }
            });


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
                    text: 'Penyerapan Anggaran Tiap Fund (Div. {{ auth()->user()->relasi_struktur->divisi->code ?? 'N/A' }})',
                    align: 'left',
                },
                xAxis: {
                    categories: categoriesFund,
                    labels: {
                        formatter: function() {
                            return '<span title="' + namesFund[this.pos] + '">' + this.value +
                            '</span>';
                        },
                        useHTML: true, // Memastikan HTML digunakan untuk menampilkan nama fund saat dihover
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
                colors: colors,
                title: {
                    text: 'CAPEX (Div. {{ auth()->user()->relasi_struktur->divisi->code ?? 'N/A' }})',
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
                colors: colors,
                title: {
                    text: 'OPEX (Div. {{ auth()->user()->relasi_struktur->divisi->code ?? 'N/A' }})',
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
                colors: colors,
                title: {
                    text: 'TOTAL (Div. {{ auth()->user()->relasi_struktur->divisi->code ?? 'N/A' }})',
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
