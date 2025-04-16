<!--
<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
-->
<ul class="navbar-nav sidebar sidebar-dark accordion" id="accordionSidebar" style="background: #21243D;">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ route('admin.dashboard') }}">
        <div class="sidebar-brand-icon">
            <img style="width: 3rem" src="{{ asset('img/logoAdmin.png') }}" alt="">
        </div>
        <div class="sidebar-brand-text mx-3">Pemancingan Kameumeut</div>
    </a>

    @if (Auth::user()->role->name = 'admin')
        <!-- Divider -->
        <hr class="sidebar-divider my-0">

        <!-- Nav Item - Dashboard -->
        <li class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.dashboard') }}">
                <i class="fas fa-fw fa-tachometer-alt"></i>
                <span>Dashboard</span></a>
        </li>

        <!-- Divider -->
        <hr class="sidebar-divider">

        <!-- Heading -->
        <div class="sidebar-heading">
            Menu Utama
        </div>

        <!-- Nav Item - Lapangan Collapse Menu -->
        <li class="nav-item {{ request()->routeIs('admin.queue.index') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.queue.index') }}">
                <i class="fas fa-ticket-alt"></i>
                <span>Data Antrian</span>
            </a>
        </li>
        <li
            class="nav-item {{ request()->routeIs('admin.fieldsIndex') || request()->routeIs('admin.scheduleIndex') ? 'active' : '' }}">
            <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseLapangan"
                aria-expanded="true" aria-controls="collapseLapangan">
                <i class="fas fa-fw fa-list"></i>
                <span>Data Lapak</span>
                <i class="bi bi-caret-down-fill"></i>
            </a>
            <div id="collapseLapangan" class="collapse" aria-labelledby="headingLapangan"
                data-bs-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <a class="collapse-item {{ request()->routeIs('admin.fieldsIndex') ? 'active' : '' }}"
                        href="{{ route('admin.fieldsIndex') }}">Lapak</a>
                    
                    {{--  <a class="collapse-item {{ request()->routeIs('admin.indexIkan') ? 'active' : '' }}"
                        href="{{ route('admin.indexIkan') }}">Ikan</a>--}}

                    <a class="collapse-item {{ request()->routeIs('admin.scheduleIndex') ? 'active' : '' }}"
                        href="{{ route('admin.scheduleIndex') }}">Jadwal Mancing</a>
                    <a class="collapse-item {{ request()->routeIs('admin.scheduleActiveIndex') ? 'active' : '' }}"
                        href="{{ route('admin.scheduleActiveIndex') }}">Jadwal Ketersediaan</a>
                </div>
            </div>
        </li>

        <li class="nav-item {{ request()->routeIs('admin.bookingIndex') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.bookingIndex') }}">
                <i class="fas fa-fw fa-book"></i>
                <span>Data Booking</span>
            </a>
        </li>
        <li class="nav-item {{ request()->routeIs('admin.transactionIndex') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.transactionIndex') }}">
                <i class="fas fa-fw fa-file-invoice-dollar"></i>
                <span>Data Transaksi</span>
            </a>
        </li>
        <!-- Divider -->
        <hr class="sidebar-divider d-none d-md-block">
        <div class="sidebar-heading">
            Menu Tambahan
        </div>
        <li class="nav-item {{ request()->routeIs('admin.userIndex') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.userIndex') }}">
                <i class="fas fa-fw fa-user"></i>
                <span>Data User</span>
            </a>
        </li>
        <li class="nav-item {{ request()->routeIs('admin.paymentMethodIndex') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.paymentMethodIndex') }}">
                <i class="fas fa-fw fa-cash-register"></i>
                <span>Metode Pembayaran</span>
            </a>
        </li>
        <li class="nav-item {{ request()->routeIs('admin.postIndex') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.postIndex') }}">
                <i class="fas fa-fw fa-upload"></i>
                <span>Postingan</span>
            </a>
        </li>
        <li class="nav-item {{ request()->routeIs('admin.indexData') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.indexData') }}">
                <i class="fas fa-fw fa-info-circle"></i>
                <span>Pengaturan Data</span>
            </a>
        </li>
    @endif

    <!-- Sidebar Toggler (Sidebar) -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>
</ul>
