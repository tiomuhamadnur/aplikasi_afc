@extends('layout.base')

@section('title-head')
    <title>Dashboard</title>
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                <span class="page-title-icon bg-gradient-primary text-white me-2">
                    <i class="mdi mdi-home"></i>
                </span> Dashboard
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
                        <h4 class="font-weight-normal mb-3">Trouble Report
                            <i class="mdi mdi-receipt mdi-24px float-right"></i>
                        </h4>
                        <h2 class="mb-3">{{ $gangguan ?? 'N/A' }}</h2>
                        <h6 class="card-text">Departemen {{ auth()->user()->relasi_struktur->departemen->code ?? 'N/A' }}
                        </h6>
                    </div>
                </div>
            </div>
            <div class="col-md-3 stretch-card grid-margin">
                <div class="card bg-gradient-success card-img-holder text-white">
                    <div class="card-body">
                        <img src="assets/images/dashboard/circle.svg" class="card-img-absolute" alt="circle-image" />
                        <h4 class="font-weight-normal mb-3">Log Sparepart <i
                                class="mdi mdi-repeat mdi-24px float-right"></i>
                        </h4>
                        <h2 class="mb-3">{{ $transaksi_barang ?? 'N/A' }}</h2>
                        <h6 class="card-text">Departemen {{ auth()->user()->relasi_struktur->departemen->code ?? 'N/A' }}
                        </h6>
                    </div>
                </div>
            </div>
            <div class="col-md-3 stretch-card grid-margin">
                <div class="card bg-gradient-warning card-img-holder text-white">
                    <div class="card-body">
                        <img src="assets/images/dashboard/circle.svg" class="card-img-absolute" alt="circle-image" />
                        <h4 class="font-weight-normal mb-3">SAM Card
                            <i class="mdi mdi-key-variant mdi-24px float-right"></i>
                        </h4>
                        <div class="mb-3">
                            <span class="badge badge-gradient-success">
                                <h5>Ready: {{ $samcard['ready'] ?? 'N/A' }}</h5>
                            </span>
                            <span class="badge badge-gradient-danger">
                                <h5>Used: {{ $samcard['used'] ?? 'N/A' }}</h5>
                            </span>
                        </div>
                        <h6 class="card-text">Departemen {{ auth()->user()->relasi_struktur->departemen->code ?? 'N/A' }}
                        </h6>
                    </div>
                </div>
            </div>
            <div class="col-md-3 stretch-card grid-margin">
                <div class="card bg-gradient-danger card-img-holder text-white">
                    <div class="card-body">
                        <img src="assets/images/dashboard/circle.svg" class="card-img-absolute" alt="circle-image" />
                        <h4 class="font-weight-normal mb-3">Work Order
                            <i class="mdi mdi-briefcase mdi-24px float-right"></i>
                        </h4>
                        <h2 class="mb-3">
                            <span class="badge badge-gradient-success">
                                <h5>PM: {{ $work_order['PM'] ?? 0 }}</h5>
                            </span>
                            <span class="badge badge-gradient-primary">
                                <h5>CM: {{ $work_order['CM'] ?? 0 }}</h5>
                            </span>
                        </h2>
                        <h6 class="card-text">Departemen {{ auth()->user()->relasi_struktur->departemen->code ?? 'N/A' }}
                        </h6>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-7 grid-margin stretch-card">
                <div class="card" id="trendGangguanGraph">
                    {{-- <div class="card-body">
                        <div id="trendGangguanGraph"></div>
                    </div> --}}
                </div>
            </div>
            <div class="col-md-5 grid-margin stretch-card">
                <div class="card" id="klasifikasiGangguanGraph">
                    {{-- <div class="card-body">
                        <div id="klasifikasiGangguanGraph"></div>
                    </div> --}}
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card" id="trendSparepartGraph">
                    {{-- <div class="card-body">
                        <div id="trendSparepartGraph"></div>
                    </div> --}}
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12 grid-margin">
                <div class="card" id="availabilityTotalGraph">
                    {{-- <div class="card-body">
                        <div id="availabilityTotalGraph"></div>
                    </div> --}}
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12 grid-margin">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title fw-bolder">Recent Trouble Reports</h4>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th> Location </th>
                                        <th> Equipment ID </th>
                                        <th> Category </th>
                                        <th> Date </th>
                                        <th> Ticket Number </th>
                                        <th> Status </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($latest_gangguan as $item)
                                        <tr>
                                            <td>{{ $item->equipment->relasi_area->sub_lokasi->name ?? 'N/A' }}</td>
                                            <td>{{ $item->equipment->code ?? 'N/A' }}</td>
                                            <td>{{ $item->category->name ?? 'N/A' }}</td>
                                            <td>{{ $item->report_date ?? 'N/A' }}</td>
                                            <td>{{ $item->ticket_number ?? 'N/A' }}</td>
                                            <td>
                                                @php
                                                    if ($item->status_id == 2) {
                                                        $badgeClass = 'badge-gradient-success';
                                                    } elseif ($item->status_id == 3) {
                                                        $badgeClass = 'badge-gradient-warning';
                                                    } elseif ($item->status_id == 4) {
                                                        $badgeClass = 'badge-gradient-info';
                                                    } else {
                                                        $badgeClass = 'badge-gradient-danger';
                                                    }
                                                @endphp
                                                <label
                                                    class="badge {{ $badgeClass }}">{{ $item->status->code ?? 'N?A' }}</label>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{-- <div class="row">
            <div class="col-md-7 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Project Status</h4>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th> # </th>
                                        <th> Name </th>
                                        <th> Due Date </th>
                                        <th> Progress </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td> 1 </td>
                                        <td> Herman Beck </td>
                                        <td> May 15, 2015 </td>
                                        <td>
                                            <div class="progress">
                                                <div class="progress-bar bg-gradient-success" role="progressbar"
                                                    style="width: 25%" aria-valuenow="25" aria-valuemin="0"
                                                    aria-valuemax="100"></div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td> 2 </td>
                                        <td> Messsy Adam </td>
                                        <td> Jul 01, 2015 </td>
                                        <td>
                                            <div class="progress">
                                                <div class="progress-bar bg-gradient-danger" role="progressbar"
                                                    style="width: 75%" aria-valuenow="75" aria-valuemin="0"
                                                    aria-valuemax="100"></div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td> 3 </td>
                                        <td> John Richards </td>
                                        <td> Apr 12, 2015 </td>
                                        <td>
                                            <div class="progress">
                                                <div class="progress-bar bg-gradient-warning" role="progressbar"
                                                    style="width: 90%" aria-valuenow="90" aria-valuemin="0"
                                                    aria-valuemax="100"></div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td> 4 </td>
                                        <td> Peter Meggik </td>
                                        <td> May 15, 2015 </td>
                                        <td>
                                            <div class="progress">
                                                <div class="progress-bar bg-gradient-primary" role="progressbar"
                                                    style="width: 50%" aria-valuenow="50" aria-valuemin="0"
                                                    aria-valuemax="100"></div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td> 5 </td>
                                        <td> Edward </td>
                                        <td> May 03, 2015 </td>
                                        <td>
                                            <div class="progress">
                                                <div class="progress-bar bg-gradient-danger" role="progressbar"
                                                    style="width: 35%" aria-valuenow="35" aria-valuemin="0"
                                                    aria-valuemax="100"></div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td> 5 </td>
                                        <td> Ronald </td>
                                        <td> Jun 05, 2015 </td>
                                        <td>
                                            <div class="progress">
                                                <div class="progress-bar bg-gradient-info" role="progressbar"
                                                    style="width: 65%" aria-valuenow="65" aria-valuemin="0"
                                                    aria-valuemax="100"></div>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-5 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title text-white">Todo</h4>
                        <div class="add-items d-flex">
                            <input type="text" class="form-control todo-list-input"
                                placeholder="What do you need to do today?">
                            <button class="add btn btn-gradient-primary font-weight-bold todo-list-add-btn"
                                id="add-task">Add</button>
                        </div>
                        <div class="list-wrapper">
                            <ul class="d-flex flex-column-reverse todo-list todo-list-custom">
                                <li>
                                    <div class="form-check">
                                        <label class="form-check-label">
                                            <input class="checkbox" type="checkbox"> Meeting with Alisa
                                        </label>
                                    </div>
                                    <i class="remove mdi mdi-close-circle-outline"></i>
                                </li>
                                <li class="completed">
                                    <div class="form-check">
                                        <label class="form-check-label">
                                            <input class="checkbox" type="checkbox" checked> Call John
                                        </label>
                                    </div>
                                    <i class="remove mdi mdi-close-circle-outline"></i>
                                </li>
                                <li>
                                    <div class="form-check">
                                        <label class="form-check-label">
                                            <input class="checkbox" type="checkbox"> Create invoice
                                        </label>
                                    </div>
                                    <i class="remove mdi mdi-close-circle-outline"></i>
                                </li>
                                <li>
                                    <div class="form-check">
                                        <label class="form-check-label">
                                            <input class="checkbox" type="checkbox"> Print Statements
                                        </label>
                                    </div>
                                    <i class="remove mdi mdi-close-circle-outline"></i>
                                </li>
                                <li class="completed">
                                    <div class="form-check">
                                        <label class="form-check-label">
                                            <input class="checkbox" type="checkbox" checked> Prepare for
                                            presentation </label>
                                    </div>
                                    <i class="remove mdi mdi-close-circle-outline"></i>
                                </li>
                                <li>
                                    <div class="form-check">
                                        <label class="form-check-label">
                                            <input class="checkbox" type="checkbox"> Pick up kids from
                                            school </label>
                                    </div>
                                    <i class="remove mdi mdi-close-circle-outline"></i>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div> --}}
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
            Highcharts.chart('trendGangguanGraph', {
                chart: {
                    type: 'column'
                },
                title: {
                    text: 'Trend Gangguan Tahun {{ $tahun ?? '-' }} (Dummy)',
                    align: 'left',
                },
                xAxis: {
                    categories: ['JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN', 'JUL', 'AUG', 'SEP', 'OCT',
                        'NOV', 'DEC'
                    ]
                },
                yAxis: {
                    title: {
                        text: 'Jumlah Gangguan'
                    }
                },
                series: [{
                    name: 'Open',
                    color: 'red',
                    data: [2, 3, 5, 1, 2, 4, 3, 1, 5, 7, 3, 4]
                }, {
                    name: 'Closed',
                    color: 'green',
                    data: [5, 7, 3, 2, 3, 5, 1, 2, 4, 3, 2, 1]
                }]
            });



            let open = 23;
            let closed = 12;
            let monitoring = 31;
            let pending = 10;
            Highcharts.chart('klasifikasiGangguanGraph', {
                chart: {
                    type: 'pie',
                    options3d: {
                        enabled: true,
                        alpha: 45
                    }
                },
                title: {
                    text: 'Distribusi Status Gangguan Tahun {{ $tahun ?? '-' }} (Dummy)',
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
                    name: 'Jumlah Gangguan',
                    colors: ['red', 'green', 'yellow', 'blue'],
                    data: [
                        ['Open', open],
                        ['Closed', closed],
                        ['Monitoring', monitoring],
                        ['Pending', pending],
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


            Highcharts.chart('availabilityTotalGraph', {
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
                            format: '{y}%',
                            style: {
                                fontSize: '13px',
                                fontWeight: 'bold',
                                color: '#000000'
                            }
                        }
                    }
                },
                title: {
                    text: 'Total Availability Ticketing System Tahun {{ $tahun ?? '-' }} (Dummy)',
                    align: 'left',
                    margin: 50
                },
                xAxis: {
                    categories: {!! json_encode(array_column($data, 'bulan')) !!} // Nama bulan
                },
                yAxis: {
                    title: {
                        text: 'Availability (%)'
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

        });
    </script>
@endsection
