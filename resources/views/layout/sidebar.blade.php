<nav class="sidebar sidebar-offcanvas" id="sidebar">
    <ul class="nav">
        {{-- <li class="nav-item nav-profile">
            <a href="{{ route('profile.index') }}" class="nav-link">
                <div class="nav-profile-image">
                    <img src="{{ auth()->user()->photo ? asset('storage/' . auth()->user()->photo) : 'https://img.freepik.com/free-psd/3d-illustration-human-avatar-profile_23-2150671142.jpg' }}"
                        alt="image">
                    <span class="login-status online"></span>
                    <!--change to offline or busy as needed-->
                </div>
                <div class="nav-profile-text d-flex flex-column">
                    <span class="font-weight-bold mb-2">{{ auth()->user()->name ?? '-' }}</span>
                    <span class="text-secondary text-small">{{ auth()->user()->jabatan->name ?? '-' }}</span>
                </div>
            </a>
        </li> --}}
        <li class="nav-item">
            <a class="nav-link" href="{{ route('dashboard.index') }}">
                <span class="menu-title">Dashboard</span>
                <i class="mdi mdi-home menu-icon"></i>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('gangguan.index') }}">
                <span class="menu-title">Failure Report</span>
                <i class="mdi mdi-receipt menu-icon"></i>
            </a>
        </li>
        @if (auth()->user()->role->id != 3)
            <li class="nav-item">
                <a class="nav-link" href="{{ route('work-order.index') }}">
                    <span class="menu-title">Work Order</span>
                    <i class="mdi mdi-briefcase menu-icon"></i>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('transaksi-barang.index') }}">
                    <span class="menu-title">Log Sparepart</span>
                    <i class="mdi mdi-repeat menu-icon"></i>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="collapse" href="#budgeting" aria-expanded="false"
                    aria-controls="budgeting">
                    <span class="menu-title">Monitoring Budget</span>
                    <i class="menu-arrow"></i>
                    <i class="mdi mdi mdi-currency-usd menu-icon"></i>
                </a>
                <div class="collapse" id="budgeting">
                    <ul class="nav flex-column sub-menu">
                        <li class="nav-item"> <a class="nav-link" href="{{ route('fund.index') }}">Fund</a></li>
                        <li class="nav-item"> <a class="nav-link" href="{{ route('fund-source.index') }}">Fund
                                Source</a></li>
                        <li class="nav-item"> <a class="nav-link" href="{{ route('project.index') }}">Projects</a>
                        </li>
                        <li class="nav-item"> <a class="nav-link" href="{{ route('budget-absorption.index') }}">Budget
                                Absorption</a>
                        </li>
                        <li class="nav-item"> <a class="nav-link" href="{{ route('dashboard-budget.index') }}">Dashboard
                                Divisi</a>
                        </li>
                    </ul>
                </div>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('monitoring-permit.index') }}">
                    <span class="menu-title">Monitoring Permit</span>
                    <i class="mdi mdi-key-variant menu-icon"></i>
                </a>
            </li>
            {{-- <li class="nav-item">
                <a class="nav-link" href="{{ route('monitoring-equipment.index') }}">
                    <span class="menu-title">Monitoring Equipment</span>
                    <i class="mdi mdi-monitor-multiple menu-icon"></i>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('log.index') }}">
                    <span class="menu-title">Log Perform. KUE Bank</span>
                    <i class="mdi mdi-file-find menu-icon"></i>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('transaksi.tiket.index') }}">
                    <span class="menu-title">Transaksi Tiket</span>
                    <i class="mdi mdi-cards-outline menu-icon"></i>
                </a>
            </li> --}}
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="collapse" href="#sam-card" aria-expanded="false"
                    aria-controls="sam-card">
                    <span class="menu-title">SAM Card</span>
                    <i class="menu-arrow"></i>
                    <i class="mdi mdi-credit-card-scan menu-icon"></i>
                </a>
                <div class="collapse" id="sam-card">
                    <ul class="nav flex-column sub-menu">
                        <li class="nav-item"> <a class="nav-link" href="{{ route('sam-card.index') }}">Data SAM
                                Card</a>
                        </li>
                        <li class="nav-item"> <a class="nav-link" href="{{ route('sam-history.index') }}">
                                History SAM Card</a>
                        </li>
                    </ul>
                </div>
            </li>
            @if (auth()->user()->role->id == 1)
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="collapse" href="#ui-basic" aria-expanded="false"
                        aria-controls="ui-basic">
                        <span class="menu-title">Master Data</span>
                        <i class="menu-arrow"></i>
                        <i class="mdi mdi-database menu-icon"></i>
                    </a>
                    <div class="collapse" id="ui-basic">
                        <ul class="nav flex-column sub-menu">
                            <li class="nav-item"> <a class="nav-link" href="{{ route('user.index') }}">User</a></li>
                            <li class="nav-item"> <a class="nav-link" href="{{ route('lokasi.index') }}">Lokasi</a>
                            </li>
                            <li class="nav-item"> <a class="nav-link" href="{{ route('sub-lokasi.index') }}">Sub
                                    Lokasi</a>
                            </li>
                            <li class="nav-item"> <a class="nav-link" href="{{ route('detail-lokasi.index') }}">Detail
                                    Lokasi</a></li>
                            <li class="nav-item"> <a class="nav-link" href="{{ route('area.index') }}">Relasi Area</a>
                            </li>
                            <li class="nav-item"> <a class="nav-link"
                                    href="{{ route('direktorat.index') }}">Direktorat</a>
                            </li>
                            <li class="nav-item"> <a class="nav-link" href="{{ route('divisi.index') }}">Divisi</a>
                            </li>
                            <li class="nav-item"> <a class="nav-link"
                                    href="{{ route('departemen.index') }}">Departemen</a>
                            </li>
                            <li class="nav-item"> <a class="nav-link" href="{{ route('seksi.index') }}">Seksi</a>
                            </li>
                            <li class="nav-item"> <a class="nav-link" href="{{ route('struktur.index') }}">Relasi
                                    Struktur</a>
                            </li>
                            <li class="nav-item"> <a class="nav-link"
                                    href="{{ route('approval.index') }}">Approval</a>
                            </li>
                            <li class="nav-item"> <a class="nav-link" href="{{ route('tipe-barang.index') }}">Tipe
                                    Barang</a>
                            </li>
                            <li class="nav-item"> <a class="nav-link"
                                    href="{{ route('tipe-pekerjaan.index') }}">Tipe
                                    Pekerjaan</a>
                            </li>
                            <li class="nav-item"> <a class="nav-link" href="{{ route('tipe-permit.index') }}">Tipe
                                    Permit</a>
                            </li>
                            <li class="nav-item"> <a class="nav-link"
                                    href="{{ route('tipe-equipment.index') }}">Tipe
                                    Equipment</a>
                            </li>
                            <li class="nav-item"> <a class="nav-link" href="{{ route('tipe-employee.index') }}">Tipe
                                    Employee</a>
                            </li>
                            <li class="nav-item"> <a class="nav-link" href="{{ route('satuan.index') }}">Satuan</a>
                            </li>
                            <li class="nav-item"> <a class="nav-link" href="{{ route('gender.index') }}">Gender</a>
                            </li>
                            <li class="nav-item"> <a class="nav-link" href="{{ route('status.index') }}">Status</a>
                            </li>
                            <li class="nav-item"> <a class="nav-link"
                                    href="{{ route('status-budgeting.index') }}">Status Budgeting</a>
                            </li>
                            <li class="nav-item"> <a class="nav-link"
                                    href="{{ route('classification.index') }}">Classification</a>
                            </li>
                            <li class="nav-item"> <a class="nav-link"
                                    href="{{ route('category.index') }}">Category</a>
                            </li>
                            <li class="nav-item"> <a class="nav-link" href="{{ route('problem.index') }}">Problem
                                    (P)</a>
                            </li>
                            <li class="nav-item"> <a class="nav-link" href="{{ route('cause.index') }}">Cause
                                    (C)</a>
                            </li>
                            <li class="nav-item"> <a class="nav-link" href="{{ route('remedy.index') }}">Remedy
                                    (R)</a>
                            </li>
                            <li class="nav-item"> <a class="nav-link" href="{{ route('pcr.index') }}">PCR</a>
                            </li>
                            <li class="nav-item"> <a class="nav-link"
                                    href="{{ route('jabatan.index') }}">Jabatan</a>
                            </li>
                            <li class="nav-item"> <a class="nav-link" href="{{ route('role.index') }}">Role</a>
                            </li>
                            <li class="nav-item"> <a class="nav-link" href="{{ route('arah.index') }}">Arah</a>
                            </li>
                            <li class="nav-item"> <a class="nav-link"
                                    href="{{ route('perusahaan.index') }}">Perusahaan</a>
                            </li>
                            <li class="nav-item"> <a class="nav-link" href="{{ route('option-form.index') }}">Option
                                    Form</a>
                            </li>
                            <li class="nav-item"> <a class="nav-link" href="{{ route('form.index') }}">Form</a>
                            </li>
                            <li class="nav-item"> <a class="nav-link" href="{{ route('barang.index') }}">Barang</a>
                            </li>
                            <li class="nav-item"> <a class="nav-link"
                                    href="{{ route('equipment.index') }}">Equipment</a>
                            </li>
                            <li class="nav-item"> <a class="nav-link" href="{{ route('fun_loc.index') }}">Funct.
                                    Location</a>
                            </li>
                            <li class="nav-item"> <a class="nav-link" href="{{ route('asset.index') }}">Asset
                                    Structure</a>
                            </li>
                        </ul>
                    </div>
                </li>
            @endif
        @endif
    </ul>
</nav>
