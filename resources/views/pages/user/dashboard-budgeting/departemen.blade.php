@extends('layout.base')

@section('title-head')
    <title>Dashboard Budgeting</title>
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                <span class="page-title-icon bg-gradient-primary text-white me-2">
                    <i class="mdi mdi-home"></i>
                </span> Dashboard Budgeting
            </h3>
            <nav aria-label="breadcrumb">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item active" aria-current="page">
                        <span></span>Overview <i class="mdi mdi-alert-circle-outline icon-sm text-primary align-middle"></i>
                    </li>
                </ul>
            </nav>
        </div>
        <div class="row">
            <div class="col-md-12 stretch-card grid-margin">
                <div class="card bg-gradient-primary card-img-holder text-white">
                    <div class="card-body p-1 text-center">
                        <h3>
                            Departemen {{ $departemen->code ?? 'N/A' }}
                        </h3>
                        <h4>Update: {{ $today }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-3 stretch-card grid-margin">
                <div class="card bg-gradient-info card-img-holder text-white">
                    <div class="card-body">
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
                <div class="card bg-gradient-success card-img-holder text-white">
                    <div class="card-body">
                        <h4 class="font-weight-normal mb-3">Penyerapan <i class="mdi mdi-database mdi-24px float-right"></i>
                        </h4>
                        <h3 class="mb-3">{{ $used_balance }}</h3>
                        <h6 class="card-text">Departemen {{ $departemen->code ?? 'N/A' }}
                        </h6>
                    </div>
                </div>
            </div>
            <div class="col-md-3 stretch-card grid-margin">
                <div class="card bg-gradient-warning card-img-holder text-white">
                    <div class="card-body">
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
                <div class="card bg-gradient-danger card-img-holder text-white">
                    <div class="card-body">
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
                }
            });

            // SETIAP FUND
            // Data categories
            const categoriesFund = @json($categoriesFund);
            const seriesFund = @json($seriesFund);


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
                title: {
                    text: 'Penyerapan Anggaran Tiap Fund (Dept. {{ $departemen->code }})',
                    align: 'left',
                },
                xAxis: {
                    categories: categoriesFund,
                },
                yAxis: {
                    title: {
                        text: 'Jumlah Anggaran (IDR)',
                    },
                    stackLabels: {
                        enabled: true,
                        formatter: function() {
                            return Highcharts.numberFormat(this.total, 0, ',', '.');
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
                        return '<b>' + this.series.name + '</b>: ' +
                            Highcharts.numberFormat(this.y, 0, ',', '.');
                    }
                },
                plotOptions: {
                    series: {
                        stacking: 'normal',
                        dataLabels: {
                            enabled: true,
                            formatter: function() {
                                return Highcharts.numberFormat(this.y, 0, ',', '.');
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
                title: {
                    text: 'CAPEX (Dept. {{ $departemen->code }})',
                    align: 'left'
                },
                subtitle: {
                    text: '',
                    align: 'left'
                },
                tooltip: {
                    formatter: function() {
                        return '<b>' + this.series.name + '</b>: ' +
                            Highcharts.numberFormat(this.y, 0, ',', '.');
                    }
                },
                plotOptions: {
                    series: {
                        allowPointSelect: true,
                        cursor: 'pointer',
                        dataLabels: [{
                            enabled: true,
                            distance: 20
                        }, {
                            enabled: true,
                            distance: -40,
                            format: '{point.percentage:.1f}%',
                            style: {
                                fontSize: '1.2em',
                                textOutline: 'none',
                                opacity: 0.7
                            },
                            filter: {
                                operator: '>',
                                property: 'percentage',
                                value: 10
                            }
                        }]
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
                title: {
                    text: 'OPEX (Dept. {{ $departemen->code }})',
                    align: 'left'
                },
                subtitle: {
                    text: '',
                    align: 'left'
                },
                tooltip: {
                    formatter: function() {
                        return '<b>' + this.series.name + '</b>: ' +
                            Highcharts.numberFormat(this.y, 0, ',', '.');
                    }
                },
                plotOptions: {
                    series: {
                        allowPointSelect: true,
                        cursor: 'pointer',
                        dataLabels: [{
                            enabled: true,
                            distance: 20
                        }, {
                            enabled: true,
                            distance: -40,
                            format: '{point.percentage:.1f}%',
                            style: {
                                fontSize: '1.2em',
                                textOutline: 'none',
                                opacity: 0.7
                            },
                            filter: {
                                operator: '>',
                                property: 'percentage',
                                value: 10
                            }
                        }]
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
                title: {
                    text: 'TOTAL (Dept. {{ $departemen->code }})',
                    align: 'left'
                },
                subtitle: {
                    text: '',
                    align: 'left'
                },
                tooltip: {
                    formatter: function() {
                        return '<b>' + this.series.name + '</b>: ' +
                            Highcharts.numberFormat(this.y, 0, ',', '.');
                    }
                },
                plotOptions: {
                    series: {
                        allowPointSelect: true,
                        cursor: 'pointer',
                        dataLabels: [{
                            enabled: true,
                            distance: 20
                        }, {
                            enabled: true,
                            distance: -40,
                            format: '{point.percentage:.1f}%',
                            style: {
                                fontSize: '1.2em',
                                textOutline: 'none',
                                opacity: 0.7
                            },
                            filter: {
                                operator: '>',
                                property: 'percentage',
                                value: 10
                            }
                        }]
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
