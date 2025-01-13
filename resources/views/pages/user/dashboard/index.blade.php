@extends('layout.base')

@section('title-head')
    <title>Dashboard</title>
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-md-12 stretch-card grid-margin">
                <div class="card mrt-dark-grey card-img-holder text-white">
                    <div class="card-body p-1 text-center">
                        <h3>
                            {{ auth()->user()->relasi_struktur->departemen->name ?? 'N/A' }}
                            ({{ auth()->user()->relasi_struktur->departemen->code ?? 'N/A' }})
                        </h3>
                        <h4>{{ $today }} <button data-bs-toggle="modal" data-bs-target="#evaluasiModal"
                                class="bg-gradient-primary text-white" title="Pilih tanggal evaluasi">
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
                        <img src="{{ asset('assets/images/dashboard/circle.svg') }}" class="card-img-absolute"
                            alt="circle-image">
                        <h4 class="font-weight-normal mb-3">Failure Report
                            <i class="mdi mdi-receipt mdi-24px float-right"></i>
                        </h4>
                        <h2 class="mb-3">{{ $gangguan ?? 'N/A' }}</h2>
                        <h6 class="card-text">Departemen {{ auth()->user()->relasi_struktur->departemen->code ?? 'N/A' }}
                        </h6>
                    </div>
                </div>
            </div>
            <div class="col-md-3 stretch-card grid-margin">
                <div class="card mrt-blue card-img-holder text-white">
                    <div class="card-body">
                        <img src="{{ asset('assets/images/dashboard/circle.svg') }}" class="card-img-absolute"
                            alt="circle-image">
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
                <div class="card mrt-green card-img-holder text-white">
                    <div class="card-body">
                        <img src="{{ asset('assets/images/dashboard/circle.svg') }}" class="card-img-absolute"
                            alt="circle-image">
                        <h4 class="font-weight-normal mb-3">SAM Card
                            <i class="mdi mdi-credit-card-scan mdi-24px float-right"></i>
                        </h4>
                        <div class="mb-3">
                            <span class="badge badge-gradient-primary">
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
                <div class="card mrt-grey card-img-holder text-white">
                    <div class="card-body">
                        <img src="{{ asset('assets/images/dashboard/circle.svg') }}" class="card-img-absolute"
                            alt="circle-image">
                        <h4 class="font-weight-normal mb-3">Work Order
                            <i class="mdi mdi-briefcase mdi-24px float-right"></i>
                        </h4>
                        <h2 class="mb-3">
                            <span class="badge badge-gradient-primary">
                                <h5>PM: {{ $work_order['PM'] ?? 0 }}</h5>
                            </span>
                            <span class="badge badge-gradient-danger">
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
            <div class="col-md-8 grid-margin stretch-card">
                <div class="card" id="trendGangguanGraph">
                </div>
            </div>
            <div class="col-md-4 grid-margin stretch-card">
                <div class="card" id="klasifikasiGangguanGraph">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-8 grid-margin stretch-card">
                <div class="card" id="trendSparepartGraph">
                </div>
            </div>
            <div class="col-md-4 grid-margin stretch-card">
                <div class="card" id="severityGangguanGraph">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12 grid-margin">
                <div class="card" id="availabilityTotalGraph">
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
                    <form id="evaluasiForm" action="{{ route('dashboard.index') }}" method="GET"
                        class="forms-sample">
                        @csrf
                        @method('GET')
                        <div class="form-group">
                            <label for="start_date">Pilih Periode</label>
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
                    <a href="{{ route('dashboard.index') }}" class="btn btn-gradient-warning">Reset</a>
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
            const colors = ['#0053B2', '#43B53A', '#88BFF8', '#A3AAB1'];

            // BY MONTH
            Highcharts.chart('trendGangguanGraph', {
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
                    text: 'Trend Gangguan',
                    align: 'left',
                    margin: 50
                },
                xAxis: {
                    categories: {!! json_encode(array_column($data, 'bulan')) !!}
                },
                yAxis: {
                    title: {
                        text: 'Jumlah Gangguan'
                    }
                },
                legend: {
                    backgroundColor: '#FCFFC5',
                    borderColor: '#C98657',
                    borderWidth: 1
                },
                series: [{
                    name: 'Gangguan',
                    color: '#cb6ce6',
                    data: {!! json_encode(
                        array_map(function ($item) {
                            return ['y' => $item['trend_gangguan'], 'url' => $item['url_trend_gangguan']];
                        }, $data),
                    ) !!}
                }],
            });

            // BY STATUS
            const gangguanByStatus = @json($gangguanByStatus);
            Highcharts.chart('klasifikasiGangguanGraph', {
                chart: {
                    type: 'pie',
                    options3d: {
                        enabled: true,
                        alpha: 45
                    }
                },
                title: {
                    text: 'Distribusi Status Gangguan',
                    align: 'left'
                },
                subtitle: {
                    text: '',
                    align: 'left'
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
                    backgroundColor: '#FCFFC5',
                    borderColor: '#C98657',
                    borderWidth: 1
                },
                series: [{
                    name: 'Jumlah Gangguan',
                    colors: colors,
                    data: gangguanByStatus,
                }]
            });

            // BY KLASIFIKASI
            const gangguanByKlasifikasi = @json($gangguanByKlasifikasi);
            Highcharts.chart('severityGangguanGraph', {
                chart: {
                    type: 'pie',
                    options3d: {
                        enabled: true,
                        alpha: 45
                    }
                },
                title: {
                    text: 'Distribusi Klasifikasi Gangguan',
                    align: 'left'
                },
                subtitle: {
                    text: '',
                    align: 'left'
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
                    backgroundColor: '#FCFFC5',
                    borderColor: '#C98657',
                    borderWidth: 1
                },
                series: [{
                    name: 'Jumlah Gangguan',
                    colors: ['#43B53A', '#88BFF8', 'red'],
                    data: gangguanByKlasifikasi,
                }]
            });

            // SPAREPART & SAMCARD
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
                    text: 'Trend Pergantian Sparepart & SAM Card',
                    align: 'left',
                    margin: 50
                },
                xAxis: {
                    categories: {!! json_encode(array_column($data, 'bulan')) !!}
                },
                yAxis: {
                    title: {
                        text: 'Jumlah'
                    }
                },
                series: [{
                    name: 'Sparepart',
                    color: '#43B53A',
                    data: {!! json_encode(
                        array_map(function ($item) {
                            return ['y' => $item['trend_sparepart'], 'url' => $item['url_trend_sparepart']];
                        }, $data),
                    ) !!}
                }, {
                    name: 'SAM Card',
                    color: '#0053B2',
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
                    text: 'Total Availability Ticketing System Tahun {{ $year ?? '-' }} (Dummy)',
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
