<nav class="sidebar sidebar-offcanvas" id="sidebar">
    <ul class="nav">
        <li class="nav-item nav-profile">
            <a href="#" class="nav-link">
                <div class="nav-profile-image">
                    <img src="https://img.freepik.com/free-psd/3d-illustration-human-avatar-profile_23-2150671142.jpg"
                        alt="profile">
                    <span class="login-status online"></span>
                    <!--change to offline or busy as needed-->
                </div>
                <div class="nav-profile-text d-flex flex-column">
                    <span class="font-weight-bold mb-2">{{ auth()->user()->name ?? '-' }}</span>
                    <span class="text-secondary text-small">{{ auth()->user()->jabatan->name ?? '-' }}</span>
                </div>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('dashboard.index') }}">
                <span class="menu-title">Dashboard</span>
                <i class="mdi mdi-home menu-icon"></i>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('monitoring-permit.index') }}">
                <span class="menu-title">Monitoring Permit</span>
                <i class="mdi mdi-key-variant menu-icon"></i>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('log.index') }}">
                <span class="menu-title">Log AFC</span>
                <i class="mdi mdi-file-find menu-icon"></i>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#">
                <span class="menu-title">Transaksi Barang</span>
                <i class="mdi mdi-repeat menu-icon"></i>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#">
                <span class="menu-title">Rekap Gangguan</span>
                <i class="mdi mdi-receipt menu-icon"></i>
            </a>
        </li>
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
                    <li class="nav-item"> <a class="nav-link" href="{{ route('lokasi.index') }}">Lokasi</a></li>
                    <li class="nav-item"> <a class="nav-link" href="{{ route('sub-lokasi.index') }}">Sub Lokasi</a></li>
                    <li class="nav-item"> <a class="nav-link" href="{{ route('detail-lokasi.index') }}">Detail
                            Lokasi</a></li>
                    <li class="nav-item"> <a class="nav-link" href="{{ route('area.index') }}">Relasi Area</a>
                    </li>
                    <li class="nav-item"> <a class="nav-link" href="{{ route('direktorat.index') }}">Direktorat</a>
                    </li>
                    <li class="nav-item"> <a class="nav-link" href="{{ route('divisi.index') }}">Divisi</a>
                    </li>
                    <li class="nav-item"> <a class="nav-link" href="{{ route('departemen.index') }}">Departemen</a>
                    </li>
                    <li class="nav-item"> <a class="nav-link" href="{{ route('seksi.index') }}">Seksi</a>
                    </li>
                    <li class="nav-item"> <a class="nav-link" href="{{ route('struktur.index') }}">Relasi Struktur</a>
                    </li>
                    <li class="nav-item"> <a class="nav-link" href="{{ route('tipe-barang.index') }}">Tipe Barang</a>
                    </li>
                    <li class="nav-item"> <a class="nav-link" href="{{ route('tipe-pekerjaan.index') }}">Tipe
                            Pekerjaan</a>
                    </li>
                    <li class="nav-item"> <a class="nav-link" href="{{ route('tipe-permit.index') }}">Tipe
                            Permit</a>
                    </li>
                    <li class="nav-item"> <a class="nav-link" href="{{ route('tipe-equipment.index') }}">Tipe
                            Equipment</a>
                    </li>
                    <li class="nav-item"> <a class="nav-link" href="{{ route('tipe-employee.index') }}">Tipe
                            Employee</a>
                    </li>
                    <li class="nav-item"> <a class="nav-link" href="{{ route('satuan.index') }}">Satuan</a>
                    </li>
                    <li class="nav-item"> <a class="nav-link" href="{{ route('gender.index') }}">Gender</a>
                    </li>
                    <li class="nav-item"> <a class="nav-link" href="{{ route('jabatan.index') }}">Jabatan</a>
                    </li>
                    <li class="nav-item"> <a class="nav-link" href="{{ route('role.index') }}">Role</a>
                    </li>
                    <li class="nav-item"> <a class="nav-link" href="{{ route('arah.index') }}">Arah</a>
                    </li>
                    <li class="nav-item"> <a class="nav-link" href="{{ route('perusahaan.index') }}">Perusahaan</a>
                    </li>
                    <li class="nav-item"> <a class="nav-link" href="{{ route('barang.index') }}">Barang</a>
                    </li>
                    <li class="nav-item"> <a class="nav-link" href="{{ route('equipment.index') }}">Equipment</a>
                    </li>
                </ul>
            </div>
        </li>
    </ul>
</nav>
