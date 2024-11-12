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
                </span> Dashboard Budgeting (Departemen {{ $departemen->code ?? 'N/A' }})
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
            <div class="col-md-3 stretch-card grid-margin">
                <div class="card bg-gradient-info card-img-holder text-white">
                    <div class="card-body">
                        <h4 class="font-weight-normal mb-3">Anggaran
                            <i class="mdi mdi-database mdi-24px float-right"></i>
                        </h4>
                        <h3 class="mb-3">Rp. 10.500.000.000</h3>
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
                        <h3 class="mb-3">Rp. 8.200.000.000</h3>
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
                        <h3>Rp. 1.200.000.000</h3>
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
                        <h3>Rp. 2.300.000.000</h3>
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
            const categories = ['J1234', 'J4312', 'J0120', 'J0635', 'J4345', 'J8643', 'J3666',
                '89544', 'J2234', 'J5566', 'J7788', 'J9900', 'J3322', 'J4455', 'J6677'
            ];
            const minChartHeight = 400; // Tinggi minimum
            const additionalHeightPerCategory = 40; // Tinggi tambahan per kategori

            // Mengatur tinggi chart secara dinamis
            const chartHeight = minChartHeight + (categories.length * additionalHeightPerCategory);
            document.getElementById('fundGraph').style.height = chartHeight + 'px';

            // Membuat chart Highcharts
            Highcharts.chart('fundGraph', {
                chart: {
                    type: 'bar',
                },
                title: {
                    text: 'Penyerapan Anggaran Tiap Fund',
                    align: 'left',
                },
                xAxis: {
                    categories: categories,
                },
                yAxis: {
                    title: {
                        text: 'Anggaran',
                    },
                    stackLabels: {
                        enabled: true,
                    },
                },
                legend: {
                    align: 'center',
                    verticalAlign: 'top',
                    layout: 'horizontal'
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
                        stacking: 'normal',
                        dataLabels: {
                            enabled: true,
                        },
                    },
                },
                series: [{
                    name: 'Realisasi Kegiatan',
                    data: [3000000000, 5000000000, 1000000000, 13000000000, 7000000000, 10000000000,
                        4000000000, 8000000000, 6000000000, 9000000000, 2000000000, 5000000000,
                        12000000000, 11000000000, 7000000000
                    ],
                }, {
                    name: 'Realisasi Pembayaran',
                    data: [14000000000, 8000000000, 8000000000, 12000000000, 9000000000, 3000000000,
                        5000000000, 10000000000, 11000000000, 4000000000, 7000000000,
                        13000000000, 6000000000, 8000000000, 5000000000
                    ],
                }, {
                    name: 'Proyeksi',
                    data: [0000000000, 2000000000, 6000000000, 3000000000, 5000000000, 8000000000,
                        4000000000, 7000000000, 2000000000, 6000000000, 3000000000, 1000000000,
                        9000000000, 5000000000, 8000000000
                    ],
                }, {
                    name: 'Sisa',
                    data: [1000000000, 3000000000, 7000000000, 2000000000, 6000000000, 9000000000,
                        5000000000, 4000000000, 3000000000, 2000000000, 7000000000, 8000000000,
                        4000000000, 6000000000, 1000000000
                    ],
                }],
            });


            // CAPEX
            let capex_kegiatan = 23;
            let capex_pembayaran = 12;
            let capex_proyeksi = 31;
            let capex_sisa = 10;
            Highcharts.chart('capexGraph', {
                chart: {
                    type: 'pie',
                    options3d: {
                        enabled: true,
                        alpha: 45
                    }
                },
                title: {
                    text: 'CAPEX',
                    align: 'left'
                },
                subtitle: {
                    text: '',
                    align: 'left'
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
                    name: 'Jumlah Anggaran',
                    data: [
                        ['Realisasi Kegiatan', capex_kegiatan],
                        ['Realisasi Pembayaran', capex_pembayaran],
                        ['Proyeksi', capex_proyeksi],
                        ['Sisa', capex_sisa],
                    ]
                }]
            });


            // OPEX
            let opex_kegiatan = 23;
            let opex_pembayaran = 12;
            let opex_proyeksi = 31;
            let opex_sisa = 10;
            Highcharts.chart('opexGraph', {
                chart: {
                    type: 'pie',
                    options3d: {
                        enabled: true,
                        alpha: 45
                    }
                },
                title: {
                    text: 'OPEX',
                    align: 'left'
                },
                subtitle: {
                    text: '',
                    align: 'left'
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
                    name: 'Jumlah Anggaran',
                    data: [
                        ['Realisasi Kegiatan', opex_kegiatan],
                        ['Realisasi Pembayaran', opex_pembayaran],
                        ['Proyeksi', opex_proyeksi],
                        ['Sisa', opex_sisa],
                    ]
                }]
            });


            // TOTAL
            let total_kegiatan = 23;
            let total_pembayaran = 12;
            let total_proyeksi = 31;
            let total_sisa = 10;
            Highcharts.chart('totalGraph', {
                chart: {
                    type: 'pie',
                    options3d: {
                        enabled: true,
                        alpha: 45
                    }
                },
                title: {
                    text: 'TOTAL',
                    align: 'left'
                },
                subtitle: {
                    text: '',
                    align: 'left'
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
                    name: 'Jumlah Anggaran',
                    data: [
                        ['Realisasi Kegiatan', total_kegiatan],
                        ['Realisasi Pembayaran', total_pembayaran],
                        ['Proyeksi', total_proyeksi],
                        ['Sisa', total_sisa],
                    ]
                }]
            });


            Highcharts.chart('trendSparepartGraph', {
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
                    text: 'Trend Pergantian Sparepart & SAM Card Tahun {{ $tahun ?? '-' }}',
                    align: 'left',
                    margin: 50
                },
                xAxis: {
                    categories: {!! json_encode(array_column($data, 'bulan')) !!} // Nama bulan
                },
                yAxis: {
                    title: {
                        text: 'Jumlah'
                    }
                },
                series: [{
                    name: 'Sparepart',
                    color: 'blue',
                    data: {!! json_encode(
                        array_map(function ($item) {
                            return ['y' => $item['trend_gangguan'], 'url' => $item['url_trend_gangguan']];
                        }, $data),
                    ) !!}
                }, {
                    name: 'SAM Card',
                    color: 'green',
                    data: {!! json_encode(
                        array_map(function ($item) {
                            return ['y' => $item['trend_sam_card'], 'url' => $item['url_trend_sam_card']];
                        }, $data),
                    ) !!}
                }],
                legend: {
                    backgroundColor: '#FCFFC5',
                    borderColor: '#C98657',
                    borderWidth: 1
                },
            });
        });
    </script>
@endsection
