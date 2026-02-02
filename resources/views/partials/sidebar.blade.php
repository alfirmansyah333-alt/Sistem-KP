<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo">
        <a href="{{ route('dashboard') }}" class="app-brand-link">
            <img src="{{ asset('assets/img/Logo PSTI PCR.png') }}" alt="Logo PSTI PCR" style="height: 70px; width: auto; margin-right: 8px;">
            <span class="app-brand-text fw-bolder ms-2">Sistem KP</span>
        </a>

        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
            <i class="bx bx-chevron-left bx-sm align-middle"></i>
        </a>
    </div>

    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1 ps ps--active-y">
        <!-- Menu Mahasiswa -->
        @if(auth()->check() && auth()->user()->hasRole('mahasiswa'))
        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Mahasiswa</span>
        </li>

        <!-- Dashboard Mahasiswa -->
        <li class="menu-item {{ request()->routeIs('mahasiswa.dashboard') ? 'active' : '' }}">
            <a href="{{ route('mahasiswa.dashboard') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-home-circle"></i>
                <div>Dashboard</div>
            </a>
        </li>

        <!-- Pengajuan KP -->
        <li class="menu-item {{ request()->routeIs('mahasiswa.pengajuan*') ? 'active' : '' }}">
            <a href="{{ route('mahasiswa.pengajuan') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-upload"></i>
                <div>Pengajuan KP</div>
            </a>
        </li>

        <!-- Penerimaan KP -->
        <li class="menu-item {{ request()->routeIs('mahasiswa.penerimaan*') ? 'active' : '' }}">
            <a href="{{ route('mahasiswa.penerimaan') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-file"></i>
                <div>Penerimaan KP</div>
            </a>
        </li>

        <!-- Pembimbing -->
        <li class="menu-item {{ request()->routeIs('mahasiswa.pembimbing*') ? 'active' : '' }}">
            <a href="{{ route('mahasiswa.pembimbing') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-user-pin"></i>
                <div>Data Pembimbing</div>
            </a>
        </li>

        <!-- Seminar KP -->
        <li class="menu-item {{ request()->routeIs('mahasiswa.seminar*') ? 'active' : '' }}">
            <a href="{{ route('mahasiswa.seminar') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-calendar-event"></i>
                <div>Seminar KP</div>
            </a>
        </li>

        <!-- Laporan Akhir -->
        <li class="menu-item {{ request()->routeIs('mahasiswa.laporan*') ? 'active' : '' }}">
            <a href="{{ route('mahasiswa.laporan') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-book"></i>
                <div>Laporan KP</div>
            </a>
        </li>

        <!-- Nilai -->
        <li class="menu-item {{ request()->routeIs('mahasiswa.nilai*') ? 'active' : '' }}">
            <a href="{{ route('mahasiswa.nilai') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-bar-chart"></i>
                <div>Nilai KP</div>
            </a>
        </li>
        @endif

        <!-- Menu Dosen -->
        @if(auth()->check() && auth()->user()->hasRole('dosen'))
        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Dosen</span>
        </li>

        <!-- Dashboard Dosen -->
        <li class="menu-item {{ request()->routeIs('dosen.dashboard') ? 'active' : '' }}">
            <a href="{{ route('dosen.dashboard') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-home-circle"></i>
                <div>Dashboard</div>
            </a>
        </li>

        <!-- Mahasiswa Bimbingan -->
        <li class="menu-item {{ request()->routeIs('dosen.bimbingan*') || request()->routeIs('dosen.mahasiswa.*') ? 'active' : '' }}">
            <a href="{{ route('dosen.bimbingan') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-group"></i>
                <div>Mahasiswa Bimbingan</div>
            </a>
        </li>

        <!-- Laporan KP -->
        <li class="menu-item {{ request()->routeIs('dosen.laporan*') ? 'active' : '' }}">
            <a href="{{ route('dosen.laporan') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-book"></i>
                <div>Laporan KP</div>
            </a>
        </li>
        @endif

        <!-- Menu Koordinator -->
        @if(auth()->check() && auth()->user()->hasRole('koor'))
        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Koordinator</span>
        </li>

        <li class="menu-item {{ request()->routeIs('koor.dashboard') ? 'active' : '' }}">
            <a href="{{ route('koor.dashboard') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-home-circle"></i>
                <div data-i18n="Dashboard">Dashboard</div>
            </a>
        </li>

        <li class="menu-item {{ request()->routeIs('koor.pengajuan*') ? 'active' : '' }}">
            <a href="{{ route('koor.pengajuan') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-upload"></i>
                <div data-i18n="Pengajuan KP">Pengajuan KP</div>
            </a>
        </li>

        <li class="menu-item {{ request()->routeIs('koor.penerimaan*') ? 'active' : '' }}">
            <a href="{{ route('koor.penerimaan') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-file"></i>
                <div data-i18n="Penerimaan KP">Penerimaan KP</div>
            </a>
        </li>

        <li class="menu-item {{ request()->routeIs('koor.data.mahasiswa') || request()->routeIs('koor.assign.*') ? 'active' : '' }}">
            <a href="{{ route('koor.data.mahasiswa') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-group"></i>
                <div data-i18n="Data Mahasiswa">Data Mahasiswa</div>
            </a>
        </li>

        <li class="menu-item {{ request()->routeIs('koor.seminar*') ? 'active' : '' }}">
            <a href="{{ route('koor.seminar') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-calendar"></i>
                <div data-i18n="Seminar KP">Seminar KP</div>
            </a>
        </li>

        <li class="menu-item {{ request()->routeIs('koor.rekap*') ? 'active' : '' }}">
            <a href="{{ route('koor.rekap') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-spreadsheet"></i>
                <div data-i18n="Rekap Data">Rekap Data</div>
            </a>
        </li>
        @endif

        <!-- Menu Admin -->
        @if(auth()->check() && auth()->user()->hasRole('admin'))
        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Admin</span>
        </li>

        <!-- Dashboard Admin -->
        <li class="menu-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <a href="{{ route('admin.dashboard') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-home-circle"></i>
                <div data-i18n="Dashboard">Dashboard</div>
            </a>
        </li>

        <li class="menu-item {{ request()->routeIs('admin.data-mahasiswa*') ? 'active' : '' }}">
            <a href="{{ route('admin.data-mahasiswa') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-user"></i>
                <div data-i18n="Data Mahasiswa">Data Mahasiswa</div>
            </a>
        </li>

        <li class="menu-item {{ request()->routeIs('admin.perusahaan*') ? 'active' : '' }}">
            <a href="{{ route('admin.perusahaan.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-buildings"></i>
                <div data-i18n="Data Perusahaan">Data Perusahaan</div>
            </a>
        </li>

        <li class="menu-item {{ request()->routeIs('admin.data-staff*') ? 'active' : '' }}">
            <a href="{{ route('admin.data-staff') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-user-voice"></i>
                <div data-i18n="Data Staff">Data Dosen & Koor</div>
            </a>
        </li>
        @endif

    </ul>
</aside>