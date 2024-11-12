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
                </span> Dashboard Budgeting (Divisi {{ auth()->user()->relasi_struktur->divisi->name ?? 'N/A' }})
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
                        <img src="assets/images/dashboard/circle.svg" class="card-img-absolute" alt="circle-image" />
                        <h4 class="font-weight-normal mb-3">Anggaran
                            <i class="mdi mdi-database mdi-24px float-right"></i>
                        </h4>
                        <h3 class="mb-3">Rp. 45.700.000.000</h3>
                        <h6 class="card-text">Divisi {{ auth()->user()->relasi_struktur->divisi->code ?? 'N/A' }}
                        </h6>
                    </div>
                </div>
            </div>
            <div class="col-md-3 stretch-card grid-margin">
                <div class="card bg-gradient-success card-img-holder text-white">
                    <div class="card-body">
                        <img src="assets/images/dashboard/circle.svg" class="card-img-absolute" alt="circle-image" />
                        <h4 class="font-weight-normal mb-3">Penyerapan <i class="mdi mdi-database mdi-24px float-right"></i>
                        </h4>
                        <h3 class="mb-3">Rp. 35.600.000.000</h3>
                        <h6 class="card-text">Divisi {{ auth()->user()->relasi_struktur->divisi->code ?? 'N/A' }}
                        </h6>
                    </div>
                </div>
            </div>
            <div class="col-md-3 stretch-card grid-margin">
                <div class="card bg-gradient-warning card-img-holder text-white">
                    <div class="card-body">
                        <img src="assets/images/dashboard/circle.svg" class="card-img-absolute" alt="circle-image" />
                        <h4 class="font-weight-normal mb-3">Proyeksi
                            <i class="mdi mdi-database mdi-24px float-right"></i>
                        </h4>
                        <h3>Rp. 2.000.000.000</h3>
                        <h6 class="card-text">Divisi {{ auth()->user()->relasi_struktur->divisi->code ?? 'N/A' }}
                        </h6>
                    </div>
                </div>
            </div>
            <div class="col-md-3 stretch-card grid-margin">
                <div class="card bg-gradient-danger card-img-holder text-white">
                    <div class="card-body">
                        <img src="assets/images/dashboard/circle.svg" class="card-img-absolute" alt="circle-image" />
                        <h4 class="font-weight-normal mb-3">Sisa
                            <i class="mdi mdi-database mdi-24px float-right"></i>
                        </h4>
                        <h3>Rp. 10.100.000.000</h3>
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
                title: {
                    text: 'Penyerapan Anggaran Tiap Departemen',
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
                    },
                },
                plotOptions: {
                    column: {
                        stacking: 'normal',
                        dataLabels: {
                            enabled: true,
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
            const categories = ['J1234', 'J4312', 'J0120', 'J0635', 'J4345', 'J8643', 'J3666',
                '89544', 'J2234',
                'J5566', 'J7788', 'J9900', 'J3322', 'J4455', 'J6677'
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
                        text: 'Jumlah Anggaran (IDR)',
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
            let capex_kegiatan = 34000000000;
            let capex_pembayaran = 45000000000;
            let capex_proyeksi = 17000000000;
            let capex_sisa = 3500000000;
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
                    name: 'Jumlah Anggaran (IDR)',
                    data: [
                        ['Realisasi Kegiatan', capex_kegiatan],
                        ['Realisasi Pembayaran', capex_pembayaran],
                        ['Proyeksi', capex_proyeksi],
                        ['Sisa', capex_sisa],
                    ]
                }]
            });


            // OPEX
            let opex_kegiatan = 23000000000;
            let opex_pembayaran = 12000000000;
            let opex_proyeksi = 31000000000;
            let opex_sisa = 10000000000;
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
                    name: 'Jumlah Anggaran (IDR)',
                    data: [
                        ['Realisasi Kegiatan', opex_kegiatan],
                        ['Realisasi Pembayaran', opex_pembayaran],
                        ['Proyeksi', opex_proyeksi],
                        ['Sisa', opex_sisa],
                    ]
                }]
            });


            // TOTAL
            let total_kegiatan = 25000000000;
            let total_pembayaran = 17000000000;
            let total_proyeksi = 39000000000;
            let total_sisa = 23000000000;
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
                    name: 'Jumlah Anggaran (IDR)',
                    data: [
                        ['Realisasi Kegiatan', total_kegiatan],
                        ['Realisasi Pembayaran', total_pembayaran],
                        ['Proyeksi', total_proyeksi],
                        ['Sisa', total_sisa],
                    ]
                }]
            });
        });
    </script>
@endsection
