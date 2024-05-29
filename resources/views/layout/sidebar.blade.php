<nav class="sidebar sidebar-offcanvas" id="sidebar">
    <ul class="nav">
        {{-- <li class="nav-item nav-profile">
            <a href="#" class="nav-link">
                <div class="nav-profile-image">
                    <img src="{{ asset('assets/images/faces/face1.jpg') }}" alt="profile">
                    <span class="login-status online"></span>
                    <!--change to offline or busy as needed-->
                </div>
                <div class="nav-profile-text d-flex flex-column">
                    <span class="font-weight-bold mb-2">{{ auth()->user()->name }}</span>
                    <span class="text-secondary text-small">Staff</span>
                </div>
                <i class="mdi mdi-bookmark-check text-success nav-profile-badge"></i>
            </a>
        </li> --}}
        <li class="nav-item">
            <a class="nav-link" href="{{ route('dashboard.index') }}">
                <span class="menu-title">Dashboard</span>
                <i class="mdi mdi-home menu-icon"></i>
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
                    <li class="nav-item"> <a class="nav-link" href="{{ route('satuan.index') }}">Satuan</a>
                    </li>
                    <li class="nav-item"> <a class="nav-link" href="{{ route('jabatan.index') }}">Jabatan</a>
                    </li>
                    <li class="nav-item"> <a class="nav-link" href="{{ route('role.index') }}">Role</a>
                    </li>
                    <li class="nav-item"> <a class="nav-link" href="{{ route('arah.index') }}">Arah</a>
                    </li>
                    <li class="nav-item"> <a class="nav-link" href="{{ route('barang.index') }}">Barang</a>
                    </li>
                    <li class="nav-item"> <a class="nav-link" href="{{ route('equipment.index') }}">Equipment
                            (BELUM)</a></li>
                </ul>
            </div>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="collapse" href="#aplikasi" aria-expanded="false"
                aria-controls="aplikasi">
                <span class="menu-title">Aplikasi</span>
                <i class="menu-arrow"></i>
                <i class="mdi mdi-animation menu-icon"></i>
            </a>
            <div class="collapse" id="aplikasi">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item"> <a class="nav-link" href="#">Transaksi Barang</a>
                    </li>
                    <li class="nav-item"> <a class="nav-link" href="{{ route('monitoring-permit.index') }}">Monitoring
                            JSA & HIRADC</a>
                    </li>
                    <li class="nav-item"> <a class="nav-link" href="#">Rekap Gangguan</a></li>
                </ul>
            </div>
        </li>
    </ul>
</nav>
