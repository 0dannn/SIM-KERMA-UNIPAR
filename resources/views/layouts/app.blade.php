<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'SIM-KERMA UNIPAR - Sistem Informasi Dokumen Kerja Sama' }}</title>

    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ asset('partial/images/favicon.ico') }}">

    <!-- Google Fonts: IBM Plex Sans & Public Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,400&display=swap" rel="stylesheet">

    <!-- Preloader CSS -->
    <link rel="stylesheet" href="{{ asset('partial/css/preloader.min.css') }}" type="text/css" />

    <!-- Bootstrap CSS (Minia theme) -->
    <link href="{{ asset('partial/css/bootstrap.min.css') }}" id="bootstrap-style" rel="stylesheet" type="text/css" />

    <!-- Icons CSS (Boxicons, Feather, Material Design, FontAwesome) -->
    <link href="{{ asset('partial/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- Bootstrap Icons (Supplementary) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- App CSS (Minia theme) -->
    <link href="{{ asset('partial/css/app.min.css') }}" id="app-style" rel="stylesheet" type="text/css" />

    <!-- SweetAlert2 CSS -->
    <link href="{{ asset('partial/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />

    <style>
        /* Penyesuaian Tema Minia & Harmonisasi Komponen SIM-KERMA UNIPAR */
        body {
            font-family: "IBM Plex Sans", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background-color: #f4f5f8;
            color: #495057;
        }

        /* Branding Navbar */
        .navbar-brand-box {
            background: #2a3042 !important;
            border-right: 1px solid #32394e !important;
            box-shadow: none !important;
        }
        .navbar-brand-box .logo-txt {
            font-size: 1.05rem;
            font-weight: 700;
            color: #ffffff !important;
            letter-spacing: 0.8px;
        }

        /* Topbar & Search */
        #page-topbar {
            background-color: #ffffff;
            border-bottom: 1px solid #e9e9ef;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.03);
        }
        .app-search .form-control {
            border-radius: 20px;
            padding-left: 18px;
            padding-right: 45px;
            background-color: #f3f3f9;
            font-size: 0.84rem;
        }
        .app-search .btn {
            border-radius: 20px;
            width: 34px;
            height: 34px;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Vertical Sidebar Styling Sesuai Minia kerma_lama */
        body[data-sidebar="dark"] .vertical-menu {
            background: #2a3042 !important;
            border-right: 1px solid #32394e;
        }
        body[data-sidebar="dark"] #sidebar-menu ul li.menu-title {
            color: #7b8190 !important;
            font-size: 0.68rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            font-weight: 600;
            padding: 14px 20px 6px;
        }
        body[data-sidebar="dark"] #sidebar-menu ul li a {
            color: #a6b0cf;
            font-size: 0.84rem;
            font-weight: 500;
            padding: 10px 20px;
            display: flex;
            align-items: center;
            transition: all 0.2s ease;
            text-decoration: none;
        }
        body[data-sidebar="dark"] #sidebar-menu ul li a i {
            font-size: 1.15rem;
            min-width: 1.75rem;
            color: #79859c;
            transition: all 0.2s ease;
        }
        body[data-sidebar="dark"] #sidebar-menu ul li a:hover {
            color: #ffffff;
            background-color: rgba(255, 255, 255, 0.04);
        }
        body[data-sidebar="dark"] #sidebar-menu ul li a:hover i {
            color: #ffffff;
        }
        body[data-sidebar="dark"] #sidebar-menu ul li a.active,
        body[data-sidebar="dark"] #sidebar-menu ul li.mm-active > a {
            color: #ffffff !important;
            background-color: #5156be !important;
            font-weight: 600;
            box-shadow: 0 2px 6px rgba(81, 86, 190, 0.4);
        }
        body[data-sidebar="dark"] #sidebar-menu ul li a.active i,
        body[data-sidebar="dark"] #sidebar-menu ul li.mm-active > a i {
            color: #ffffff !important;
        }

        /* Sidebar Footer / User Identity */
        .sidebar-bottom-profile {
            background-color: rgba(0, 0, 0, 0.18);
            border-top: 1px solid rgba(255, 255, 255, 0.06);
            padding: 14px 18px;
            margin-top: auto;
        }

        /* Cards & Containers */
        .card, .card-wireframe {
            background-color: #ffffff;
            border: 1px solid #e9e9ef !important;
            border-radius: 8px !important;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04) !important;
            margin-bottom: 20px;
        }
        .card-header {
            background-color: #ffffff !important;
            border-bottom: 1px solid #f3f3f9 !important;
            padding: 14px 20px !important;
            border-top-left-radius: 8px !important;
            border-top-right-radius: 8px !important;
        }
        .card-body {
            padding: 20px !important;
        }
        .card-title {
            font-size: 0.95rem;
            font-weight: 600;
            color: #495057;
            margin-bottom: 0;
        }

        /* Tabel Sesuai Desain Eksak kerma_lama */
        .table-responsive {
            border: 1px solid #e9e9ef !important;
            border-radius: 6px;
            background-color: #ffffff;
            margin-bottom: 1rem;
        }
        .table, .table-wireframe, .table-bordered {
            margin-bottom: 0 !important;
            color: #495057;
            border: 1px solid #e9e9ef !important;
            border-collapse: collapse !important;
        }
        .table thead th, .table-wireframe thead th, .table-bordered thead th {
            background-color: #f8f9fa !important;
            color: #495057 !important;
            font-weight: 600 !important;
            font-size: 0.81rem !important;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            padding: 11px 14px !important;
            border: 1px solid #e9e9ef !important;
            vertical-align: middle;
        }
        .table tbody td, .table-wireframe tbody td, .table-bordered tbody td {
            padding: 11px 14px !important;
            border: 1px solid #e9e9ef !important;
            font-size: 0.84rem;
            vertical-align: middle;
            color: #495057;
        }
        .table tbody tr:nth-of-type(odd), .table-wireframe tbody tr:nth-of-type(odd) {
            background-color: #ffffff;
        }
        .table tbody tr:nth-of-type(even), .table-wireframe tbody tr:nth-of-type(even) {
            background-color: #fafbfe;
        }
        .table tbody tr:hover, .table-wireframe tbody tr:hover {
            background-color: #f1f5f9 !important;
        }

        /* Form Labels & Controls */
        .form-label {
            font-weight: 500;
            color: #495057;
            font-size: 0.84rem;
            margin-bottom: 6px;
        }
        .form-control, .form-select {
            border: 1px solid #ced4da;
            border-radius: 6px;
            font-size: 0.85rem;
            padding: 7px 12px;
            color: #495057;
        }
        .form-control:focus, .form-select:focus {
            border-color: #5156be;
            box-shadow: 0 0 0 0.15rem rgba(81, 86, 190, 0.2);
        }

        /* Tombol & Aksi (Minia Theme) */
        .btn {
            border-radius: 6px;
            font-size: 0.84rem;
            font-weight: 500;
            padding: 6px 14px;
            transition: all 0.2s;
        }
        .btn-sm {
            padding: 4px 10px;
            font-size: 0.76rem;
            border-radius: 4px;
        }
        .btn-primary {
            background-color: #5156be !important;
            border-color: #5156be !important;
            color: #ffffff !important;
        }
        .btn-primary:hover, .btn-primary:focus {
            background-color: #4549a2 !important;
            border-color: #4549a2 !important;
            color: #ffffff !important;
        }
        .btn-success {
            background-color: #2ab57d !important;
            border-color: #2ab57d !important;
        }
        .btn-info {
            background-color: #4ba6ef !important;
            border-color: #4ba6ef !important;
            color: #ffffff !important;
        }
        .btn-warning {
            background-color: #ffbf53 !important;
            border-color: #ffbf53 !important;
            color: #212529 !important;
        }
        .btn-danger {
            background-color: #fd625e !important;
            border-color: #fd625e !important;
        }

        /* Badges & Soft Badges */
        .badge {
            font-weight: 500;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.72rem;
            letter-spacing: 0.02em;
        }
        .badge.bg-primary { background-color: #5156be !important; }
        .badge.bg-success { background-color: #2ab57d !important; }
        .badge.bg-info    { background-color: #4ba6ef !important; }
        .badge.bg-warning { background-color: #ffbf53 !important; color: #212529 !important; }
        .badge.bg-danger  { background-color: #fd625e !important; }
        .badge.bg-secondary { background-color: #74788d !important; }
        .badge-soft-primary { background-color: rgba(81, 86, 190, 0.12) !important; color: #5156be !important; }
        .badge-soft-success { background-color: rgba(42, 181, 125, 0.12) !important; color: #2ab57d !important; }
        .badge-soft-info    { background-color: rgba(75, 166, 239, 0.12) !important; color: #4ba6ef !important; }
        .badge-soft-warning { background-color: rgba(255, 191, 83, 0.15) !important; color: #ffbf53 !important; }
        .badge-soft-danger  { background-color: rgba(253, 98, 94, 0.12)  !important; color: #fd625e !important; }
        .badge-soft-secondary { background-color: rgba(116, 120, 141, 0.12) !important; color: #74788d !important; }

        /* Subtle helpers */
        .bg-soft-primary { background-color: rgba(81, 86, 190, 0.1) !important; }
        .bg-soft-success { background-color: rgba(42, 181, 125, 0.1) !important; }
        .bg-soft-warning { background-color: rgba(255, 191, 83, 0.1) !important; }
        .bg-soft-danger  { background-color: rgba(253, 98, 94, 0.1) !important; }
        .bg-soft-info    { background-color: rgba(75, 166, 239, 0.1) !important; }
        .bg-soft-secondary { background-color: rgba(116, 120, 141, 0.1) !important; }

        .tag-wireframe {
            font-size: 0.72rem;
            font-weight: 500;
            padding: 3px 8px;
            background-color: #f1f3f7;
            color: #495057;
            border-radius: 4px;
            border: 1px solid #ced4da;
        }

        /* Upload Dashed Box */
        .upload-dashed-box {
            border: 2px dashed #ced4da;
            border-radius: 6px;
            background-color: #fafbfe;
            padding: 20px;
            text-align: center;
            color: #74788d;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .upload-dashed-box:hover {
            border-color: #5156be;
            background-color: rgba(81, 86, 190, 0.04);
            color: #5156be;
        }

        /* Tipografi Form Submission */
        .form-kerma-submission .col-form-label {
            font-size: 0.92rem !important;
            font-weight: 600 !important;
            color: #343a40 !important;
        }

        /* Page Content Padding */
        .page-content {
            padding: 90px 20px 60px !important;
        }
        @media (max-width: 991.98px) {
            .page-content {
                padding: 85px 12px 60px !important;
            }
        }

        /* Print Style */
        @media print {
            #page-topbar, .vertical-menu, .footer, .btn, .breadcrumb, .modal {
                display: none !important;
            }
            .main-content {
                margin-left: 0 !important;
                padding: 0 !important;
            }
            .page-content {
                padding: 0 !important;
            }
            .card {
                border: 1px solid #ccc !important;
                box-shadow: none !important;
            }
        }
    </style>
    @stack('styles')
</head>

<body data-sidebar="dark">

<!-- Begin page -->
<div id="layout-wrapper">

    <!-- ============================================================== -->
    <!-- TOP NAVIGATION BAR (Header Minia)                              -->
    <!-- ============================================================== -->
    <header id="page-topbar">
        <div class="navbar-header">
            <div class="d-flex align-items-center">
                <!-- LOGO -->
                <div class="navbar-brand-box">
                    <a href="{{ route('simkerma.index') }}" class="logo logo-dark">
                        <span class="logo-sm">
                            <img src="{{ asset('partial/images/logo/logo-sm.svg') }}" alt="" height="24">
                        </span>
                        <span class="logo-lg">
                            <img src="{{ asset('partial/images/logo/logo-sm.svg') }}" alt="" height="24"> <span class="logo-txt">SIM-KERMA</span>
                        </span>
                    </a>

                    <a href="{{ route('simkerma.index') }}" class="logo logo-light">
                        <span class="logo-sm">
                            <img src="{{ asset('partial/images/logo/logo-sm.svg') }}" alt="" height="24">
                        </span>
                        <span class="logo-lg">
                            <img src="{{ asset('partial/images/logo/logo-sm.svg') }}" alt="" height="24"> <span class="logo-txt">SIM-KERMA</span>
                        </span>
                    </a>
                </div>

                <button type="button" class="btn btn-sm px-3 font-size-16 header-item waves-effect" id="vertical-menu-btn">
                    <i class="fa fa-fw fa-bars"></i>
                </button>

                <!-- App Search Form -->
                <form class="app-search d-none d-lg-block" onsubmit="event.preventDefault(); var val = this.querySelector('input').value; var target = document.getElementById('searchDocsInput') || document.getElementById('searchMitraInput') || document.getElementById('docSearchInput'); if (target) { target.value = val; target.dispatchEvent(new Event('input')); target.dispatchEvent(new Event('keyup')); }">
                    <div class="position-relative">
                        <input type="text" class="form-control" placeholder="Cari kerja sama, nomor dokumen, mitra...">
                        <button class="btn btn-primary" type="submit"><i class="bx bx-search-alt align-middle"></i></button>
                    </div>
                </form>
            </div>

            <div class="d-flex align-items-center">

                <!-- Reset Data Pengajuan (Mode Pengembangan) -->
                <form method="POST" action="{{ route('simkerma.reset-all-documents') }}" class="d-none d-sm-inline me-2" onsubmit="return confirm('PERINGATAN PENGEMBANGAN:\n\nApakah Anda yakin ingin menghapus SEMUA data pengajuan dan persetujuan kerja sama?\n\nSemua dokumen, jejak audit (audit trails), dan log siklus FSM akan dihapus permanen.')">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-danger shadow-sm d-flex align-items-center gap-1 py-1 px-2" style="font-size: 0.75rem;" title="Hapus Semua Pengajuan dan Persetujuan (Mode Pengembangan)">
                        <i class="bx bx-trash align-middle"></i>
                        <span class="d-none d-md-inline">Reset Data Dev</span>
                    </button>
                </form>

                <!-- Dark / Light Mode Switcher -->
                <div class="dropdown d-none d-sm-inline-block">
                    <button type="button" class="btn header-item waves-effect" id="mode-setting-btn" title="Toggle Tampilan Gelap/Terang">
                        <i data-feather="moon" class="icon-lg layout-mode-dark"></i>
                        <i data-feather="sun" class="icon-lg layout-mode-light"></i>
                    </button>
                </div>

                <!-- User Profile Dropdown -->
                @if (auth()->check())
                    <div class="dropdown d-inline-block">
                        <button type="button" class="btn header-item bg-soft-light border-start border-end waves-effect" id="page-header-user-dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <img class="rounded-circle header-profile-user" src="{{ asset('partial/images/users/avatar-1.jpg') }}" alt="Header Avatar">
                            <span class="d-none d-xl-inline-block ms-1 fw-medium">{{ auth()->user()->name }}</span>
                            <span class="badge bg-primary ms-1 d-none d-md-inline-block">{{ auth()->user()->role }}</span>
                            <i class="mdi mdi-chevron-down d-none d-xl-inline-block"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end shadow-sm">
                            <div class="px-3 py-2 border-bottom">
                                <div class="fw-bold font-size-14">{{ auth()->user()->name }}</div>
                                <div class="text-muted font-size-12">{{ auth()->user()->email }}</div>
                                <span class="badge bg-soft-primary text-primary mt-1">Role: {{ auth()->user()->role }}</span>
                                @if(auth()->user()->unit)
                                    <div class="text-muted font-size-12 mt-1">Unit: {{ auth()->user()->unit }}</div>
                                @endif
                            </div>
                            <a class="dropdown-item" href="{{ route('simkerma.index', ['menu' => 7]) }}">
                                <i class="mdi mdi-cog font-size-16 align-middle me-1"></i> Konfigurasi Sistem
                            </a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item text-danger" href="{{ route('logout') }}">
                                <i class="mdi mdi-logout font-size-16 align-middle me-1"></i> Logout (Keluar)
                            </a>
                        </div>
                    </div>
                @else
                    <div class="ms-2">
                        <a href="{{ route('login') }}" class="btn btn-sm btn-primary">
                            <i class="bx bx-log-in me-1"></i> Login Portal
                        </a>
                    </div>
                @endif

            </div>
        </div>
    </header>

    <!-- ============================================================== -->
    <!-- LEFT SIDEBAR NAVIGASI (7 MENU UTAMA)                          -->
    <!-- ============================================================== -->
    <div class="vertical-menu">

        <div data-simplebar class="h-100 d-flex flex-column justify-content-between">

            <!--- Sidemenu -->
            <div id="sidebar-menu">
                <!-- Left Menu Start -->
                <ul class="metismenu list-unstyled" id="side-menu">
                    <li class="menu-title" data-key="t-menu">Main Navigation</li>

                    <!-- 1. Dashboard -->
                    <li class="{{ $activeMenu === 1 ? 'mm-active' : '' }}">
                        <a href="{{ route('simkerma.index', ['menu' => 1]) }}" class="{{ $activeMenu === 1 ? 'active' : '' }}">
                            <i class="bx bx-home-circle"></i>
                            <span data-key="t-dashboard">Dashboard</span>
                        </a>
                    </li>

                    <!-- 2. Master Data Mitra -->
                    <li class="{{ $activeMenu === 2 ? 'mm-active' : '' }}">
                        <a href="{{ route('simkerma.index', ['menu' => 2]) }}" class="{{ $activeMenu === 2 ? 'active' : '' }}">
                            <i class="bx bx-buildings"></i>
                            <span data-key="t-mitra">Data Master Mitra</span>
                        </a>
                    </li>

                    <!-- 3. Pengelolaan Dokumen Kerja Sama -->
                    <li class="{{ $activeMenu === 3 ? 'mm-active' : '' }}">
                        <a href="{{ route('simkerma.index', ['menu' => 3]) }}" class="{{ $activeMenu === 3 ? 'active' : '' }}">
                            <i class="bx bx-folder"></i>
                            <span data-key="t-dokumen">Dokumen Kerja Sama</span>
                        </a>
                    </li>

                    <!-- 4. Pengajuan & Persetujuan -->
                    <li class="{{ $activeMenu === 4 ? 'mm-active' : '' }}">
                        <a href="{{ route('simkerma.index', ['menu' => 4]) }}" class="{{ $activeMenu === 4 ? 'active' : '' }}">
                            <i class="bx bx-check-double"></i>
                            <span data-key="t-pengajuan">Pengajuan & Persetujuan</span>
                        </a>
                    </li>

                    <li class="menu-title" data-key="t-monitoring">Pelaksanaan & Evaluasi</li>

                    <!-- 5. Pemantauan Pelaksanaan -->
                    <li class="{{ $activeMenu === 5 ? 'mm-active' : '' }}">
                        <a href="{{ route('simkerma.index', ['menu' => 5]) }}" class="{{ $activeMenu === 5 ? 'active' : '' }}">
                            <i class="bx bx-calendar-check"></i>
                            <span data-key="t-pemantauan">Pemantauan Kegiatan</span>
                        </a>
                    </li>

                    <!-- 6. Monitoring Masa Berlaku & Evaluasi -->
                    <li class="{{ $activeMenu === 6 ? 'mm-active' : '' }}">
                        <a href="{{ route('simkerma.index', ['menu' => 6]) }}" class="{{ $activeMenu === 6 ? 'active' : '' }}">
                            <i class="bx bx-time-five"></i>
                            <span data-key="t-evaluasi">Masa Berlaku & Evaluasi</span>
                        </a>
                    </li>

                    <li class="menu-title" data-key="t-reporting">Laporan & Tata Kelola</li>

                    <!-- 7. Laporan dan Analitik -->
                    <li class="{{ $activeMenu === 7 ? 'mm-active' : '' }}">
                        <a href="{{ route('simkerma.index', ['menu' => 7]) }}" class="{{ $activeMenu === 7 ? 'active' : '' }}">
                            <i class="bx bx-bar-chart-alt-2"></i>
                            <span data-key="t-laporan">Laporan, Analitik & FSM</span>
                        </a>
                    </li>
                </ul>
            </div>
            <!-- Sidebar -->

            <!-- Sidebar Bottom Attribution -->
            <div class="sidebar-bottom-profile mt-auto text-white-50">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <div class="avatar-xs flex-shrink-0">
                        <span class="avatar-title rounded-circle bg-primary text-white font-size-12">
                            <i class="bx bx-user"></i>
                        </span>
                    </div>
                    <div class="overflow-hidden">
                        <div class="text-white text-truncate font-size-12 fw-semibold">
                            {{ auth()->check() ? auth()->user()->name : 'Tamu (Guest)' }}
                        </div>
                        <span class="badge bg-soft-info text-info font-size-10 py-0">
                            {{ auth()->check() ? auth()->user()->role : 'Public' }}
                        </span>
                    </div>
                </div>
                <div class="font-size-11 text-muted">
                    Jordan Immanuel Pusung &bull; 2355201011<br>
                    Teknik Informatika &bull; UNIPAR
                </div>
            </div>

        </div>
    </div>
    <!-- Left Sidebar End -->

    <!-- ============================================================== -->
    <!-- START RIGHT CONTENT HERE                                       -->
    <!-- ============================================================== -->
    <div class="main-content">

        <div class="page-content">
            <div class="container-fluid">

                <!-- start page title -->
                <div class="row">
                    <div class="col-12">
                        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                            <h4 class="mb-sm-0 font-size-18 text-uppercase">{{ $current['title'] }}</h4>

                            <div class="page-title-right">
                                <ol class="breadcrumb m-0">
                                    <li class="breadcrumb-item"><a href="{{ route('simkerma.index') }}">SIM-KERMA</a></li>
                                    <li class="breadcrumb-item active">{{ $current['title'] }}</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- end page title -->

                @if ($current['id'] != 4)
                <!-- Kotak Informasi Spesifikasi Dokumen Resmi -->
                <div class="alert alert-secondary alert-dismissible fade show border-0 bg-soft-secondary mb-4" role="alert">
                    <div class="d-flex align-items-start gap-2">
                        <i class="mdi mdi-information-outline font-size-18 text-secondary mt-1"></i>
                        <div class="small">
                            <strong class="d-block text-uppercase mb-1">Poin Spesifikasi Sesuai Dokumen Resmi:</strong>
                            <ul class="mb-0 ps-3">
                                @foreach ($current['subfeatures'] as $sf)
                                    <li>{{ $sf }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif

                <!-- Dynamic Content Blade -->
                @yield('content')

            </div>
            <!-- container-fluid -->
        </div>
        <!-- End Page-content -->

        <footer class="footer">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-6">
                        <script>document.write(new Date().getFullYear())</script> © Universitas PGRI Argopuro Jember (UNIPAR).
                    </div>
                    <div class="col-sm-6">
                        <div class="text-sm-end d-none d-sm-block">
                            SIM-KERMA UNIPAR — Sistem Informasi Dokumen Kerja Sama
                        </div>
                    </div>
                </div>
            </div>
        </footer>
    </div>
    <!-- end main content-->

</div>
<!-- END layout-wrapper -->

<!-- ========================================================================================= -->
<!-- MODAL INTERAKTIF: LEMBAR REGISTRASI & QR CODE RESMI DOKUMEN SIMKERMA UNIPAR               -->
<!-- ========================================================================================= -->
<div class="modal fade" id="qrModal" tabindex="-1" aria-labelledby="qrModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 580px;">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-dark text-white py-3">
                <div class="d-flex align-items-center gap-2">
                    <div class="bg-primary text-white p-1 rounded-2 d-flex align-items-center justify-content-center" style="width: 28px; height: 28px;">
                        <i class="bx bx-qr-scan font-size-18"></i>
                    </div>
                    <div>
                        <h6 class="modal-title text-white fw-bold mb-0" id="qrModalLabel" style="font-size: 0.95rem;">Lembar Registrasi &amp; QR Code Resmi Dokumen</h6>
                        <small class="text-white-50" style="font-size: 0.7rem;">Sistem Informasi Dokumen Kerja Sama UNIPAR</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-3 p-md-4">
                
                <!-- Kotak Registrasi & Validasi QR Code -->
                <div class="row align-items-center mb-3 g-3">
                    <div class="col-sm-5 text-center">
                        <div id="qrcodeCanvas" class="d-inline-flex p-2 bg-white border border-secondary border-opacity-25 rounded-3 shadow-sm justify-content-center align-items-center" style="min-width: 160px; min-height: 160px;">
                            <!-- QR Code di-generate via QRCode.js -->
                        </div>
                        <div class="mt-2">
                            <span class="badge bg-dark font-monospace px-2 py-1" id="qrModalRegBadge" style="font-size: 0.72rem;">REG-UNIPAR-2026-000</span>
                        </div>
                    </div>
                    <div class="col-sm-7">
                        <div class="mb-2">
                            <span class="text-muted fw-bold d-block" style="font-size: 0.72rem;">NOMOR DOKUMEN INTERNAL:</span>
                            <div class="fw-bold font-monospace text-dark" id="qrModalDocNum" style="font-size: 0.85rem;">-</div>
                        </div>
                        <div class="mb-2" id="qrModalPartnerDocContainer" style="display: none;">
                            <span class="text-muted fw-bold d-block" style="font-size: 0.72rem;">NOMOR DOKUMEN MITRA (PIHAK II):</span>
                            <div class="fw-semibold font-monospace text-primary" id="qrModalPartnerDocNum" style="font-size: 0.82rem;">-</div>
                        </div>
                        <div class="mb-2">
                            <span class="text-muted fw-bold d-block" style="font-size: 0.72rem;">JUDUL KERJA SAMA:</span>
                            <div class="fw-semibold text-dark small" id="qrModalTitle" style="font-size: 0.8rem; line-height: 1.35;">-</div>
                        </div>
                        <div class="mb-2">
                            <span class="text-muted fw-bold d-block" style="font-size: 0.72rem;">MITRA UTAMA:</span>
                            <div class="text-dark small" id="qrModalPartner" style="font-size: 0.8rem;">-</div>
                        </div>
                        <div class="mb-2">
                            <span class="text-muted fw-bold d-block" style="font-size: 0.72rem;">STATUS SIKLUS FSM:</span>
                            <span id="qrModalStatusBadge" class="badge bg-secondary text-white">DRAFT</span>
                        </div>
                        <div>
                            <span id="qrModalDateLabel" class="text-muted fw-bold d-block" style="font-size: 0.72rem;">TANGGAL PERSETUJUAN / PENGESAHAN:</span>
                            <span id="qrModalDate" class="text-muted small">30 Oktober 2026</span>
                        </div>
                    </div>
                </div>

                <!-- Kotak Petunjuk Keamanan & Prosedur Fisik FSM Sinkron -->
                <div id="qrModalAlertBox" class="alert alert-primary py-2 px-3 small border-0 bg-soft-primary text-dark d-flex align-items-start gap-2 mb-0">
                    <i id="qrModalAlertIcon" class="bx bx-lock-alt text-primary mt-1 font-size-16"></i>
                    <div id="qrModalAlertText" style="font-size: 0.78rem;">
                        <strong>Mekanisme Fisik &amp; Digital:</strong> Draf naskah berstatus <em>VERIFIED</em> telah lolos telaah mutu legal dan terkunci (<em>Freeze Text</em>). QR Code ini disematkan otomatis pada naskah PDF resmi yang dicetak untuk ditandatangani basah dan ditempeli meterai fisik Rp10.000 di hadapan pimpinan kedua belah pihak.
                    </div>
                </div>

            </div>
            <div class="modal-footer bg-light py-2 px-3 d-flex flex-column flex-sm-row justify-content-between align-items-center gap-2">
                <a id="qrModalVerifyLink" href="/verify/REG-UNIPAR-2026-042" target="_blank" class="btn btn-outline-dark btn-sm fw-semibold w-100 w-sm-auto">
                    <i class="bx bx-link-external me-1"></i> Buka Verifikasi Publik
                </a>
                <div class="d-flex gap-2 w-100 w-sm-auto justify-content-end">
                    <button type="button" class="btn btn-primary btn-sm fw-bold px-3 waves-effect waves-light" onclick="openPrintableVerification()">
                        <i class="bx bx-printer me-1"></i> Buka / Cetak Lembar Registrasi Resmi
                    </button>
                    <button type="button" class="btn btn-secondary btn-sm waves-effect" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ============================================================== -->
<!-- JAVASCRIPTS (Theme Minia kerma_lama)                           -->
<!-- ============================================================== -->
<script src="{{ asset('partial/libs/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('partial/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('partial/libs/metismenu/metisMenu.min.js') }}"></script>
<script src="{{ asset('partial/libs/simplebar/simplebar.min.js') }}"></script>
<script src="{{ asset('partial/libs/node-waves/waves.min.js') }}"></script>
<script src="{{ asset('partial/libs/feather-icons/feather.min.js') }}"></script>
<script src="{{ asset('partial/libs/sweetalert2/sweetalert2.min.js') }}"></script>

<!-- App js -->
<script src="{{ asset('partial/js/app.js') }}"></script>

<!-- Script QRCode JS -->
<script src="{{ asset('js/qrcode.min.js') }}"></script>
<script>
// Fallback if local asset fails to load
if (typeof QRCode === 'undefined') {
    document.write('<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"><\/script>');
}
</script>

<!-- SCRIPT PENGENDALI QR CODE & FSM INTERACTIVE ACTIONS -->
<script>
let currentQrInstance = null;

function showQrModal(regId, docNum, title, partner, status, date, partnerDocNum) {
    document.getElementById('qrModalLabel').innerText = 'Lembar Registrasi & QR Code Resmi: ' + regId;
    document.getElementById('qrModalRegBadge').innerText = regId;
    document.getElementById('qrModalDocNum').innerText = docNum || 'Dalam Proses Registrasi';
    document.getElementById('qrModalTitle').innerText = title || '-';
    document.getElementById('qrModalPartner').innerText = partner || '-';
    document.getElementById('qrModalDate').innerText = date || '30 Oktober 2026';
    
    // Partner Document Number (Pihak II)
    const partnerContainer = document.getElementById('qrModalPartnerDocContainer');
    const partnerEl = document.getElementById('qrModalPartnerDocNum');
    if (partnerContainer && partnerEl) {
        if (partnerDocNum && partnerDocNum.trim() !== '' && partnerDocNum.trim() !== '-') {
            partnerEl.innerText = partnerDocNum;
            partnerContainer.style.display = 'block';
        } else {
            partnerContainer.style.display = 'none';
        }
    }

    // Pemetaan Lengkap 6 Status Formal Finite State Machine UNIPAR
    const fsmStatusConfig = {
        'DRAFT': {
            badgeClass: 'badge bg-secondary text-white ms-1',
            badgeText: 'DRAFT (Penyusunan Awal)',
            alertBoxClass: 'alert alert-secondary py-2 px-3 small border-0 bg-soft-secondary text-dark d-flex align-items-start gap-2 mb-0',
            alertIconClass: 'bx bx-edit text-secondary mt-1 font-size-16',
            alertText: '<strong>Tahap Penyusunan Draf:</strong> Naskah usulan kerja sama sedang dirancang oleh unit pengusul di UNIPAR dan belum diajukan untuk pemeriksaan.',
            dateLabel: 'TANGGAL INISIASI DRAF:'
        },
        'REVIEW': {
            badgeClass: 'badge bg-info text-white ms-1',
            badgeText: 'REVIEW (Pemeriksaan & Telaah)',
            alertBoxClass: 'alert alert-info py-2 px-3 small border-0 bg-soft-info text-dark d-flex align-items-start gap-2 mb-0',
            alertIconClass: 'bx bx-time text-info mt-1 font-size-16',
            alertText: '<strong>Tahap Pemeriksaan Naskah:</strong> Draf naskah kerja sama sedang ditelaah oleh Tim Legal UNIPAR. Jika lolos verifikasi substansi, status beralih ke VERIFIED, atau dikembalikan ke DRAFT jika perlu perbaikan.',
            dateLabel: 'TANGGAL PENGAJUAN TELAAH:'
        },
        'VERIFIED': {
            badgeClass: 'badge bg-primary text-white ms-1',
            badgeText: 'VERIFIED (Lolos Verifikasi Legal)',
            alertBoxClass: 'alert alert-primary py-2 px-3 small border-0 bg-soft-primary text-dark d-flex align-items-start gap-2 mb-0',
            alertIconClass: 'bx bx-badge-check text-primary mt-1 font-size-16',
            alertText: '<strong>Naskah Terverifikasi Legal:</strong> Draf telah lolos telaah mutu dan hukum oleh Tim Legal. Teks naskah dibekukan (Freeze Text) dan siap dicetak untuk penandatanganan fisik (tanda tangan basah & bea meterai Rp10.000).',
            dateLabel: 'TANGGAL VERIFIKASI LEGAL:'
        },
        'APPROVED': {
            badgeClass: 'badge bg-primary text-white ms-1',
            badgeText: 'VERIFIED (Lolos Verifikasi Legal)',
            alertBoxClass: 'alert alert-primary py-2 px-3 small border-0 bg-soft-primary text-dark d-flex align-items-start gap-2 mb-0',
            alertIconClass: 'bx bx-badge-check text-primary mt-1 font-size-16',
            alertText: '<strong>Naskah Terverifikasi Legal:</strong> Draf telah lolos telaah mutu dan hukum oleh Tim Legal. Teks naskah dibekukan (Freeze Text) dan siap dicetak untuk penandatanganan fisik.',
            dateLabel: 'TANGGAL VERIFIKASI LEGAL:'
        },
        'ACTIVE': {
            badgeClass: 'badge bg-success text-white ms-1',
            badgeText: 'ACTIVE (Sah & Bermeterai)',
            alertBoxClass: 'alert alert-success py-2 px-3 small border-0 bg-soft-success text-dark d-flex align-items-start gap-2 mb-0',
            alertIconClass: 'bx bx-check-circle text-success mt-1 font-size-16',
            alertText: '<strong>Dokumen Sah Berlaku:</strong> Eksemplar fisik asli bertanda tangan basah dan bermeterai sah telah diverifikasi keabsahannya serta tersimpan resmi di SIMKERMA UNIPAR.',
            dateLabel: 'TANGGAL PENGESAHAN / AKTIF:'
        },
        'WARNING': {
            badgeClass: 'badge bg-warning text-dark ms-1',
            badgeText: 'WARNING (Peringatan H-90)',
            alertBoxClass: 'alert alert-warning py-2 px-3 small border-0 bg-soft-warning text-dark d-flex align-items-start gap-2 mb-0',
            alertIconClass: 'bx bx-error text-warning mt-1 font-size-16',
            alertText: '<strong>Peringatan Ambang Batas Kedaluwarsa:</strong> Kerja sama masih aktif berlaku, namun sisa masa berlaku telah memasuki ambang batas peringatan dini (H-90/H-60). Diimbau segera menindaklanjuti perpanjangan.',
            dateLabel: 'TANGGAL PERINGATAN DINI:'
        },
        'EXPIRED': {
            badgeClass: 'badge bg-danger text-white ms-1',
            badgeText: 'EXPIRED (Kedaluwarsa)',
            alertBoxClass: 'alert alert-danger py-2 px-3 small border-0 bg-soft-danger text-dark d-flex align-items-start gap-2 mb-0',
            alertIconClass: 'bx bx-x-circle text-danger mt-1 font-size-16',
            alertText: '<strong>Masa Berlaku Telah Berakhir:</strong> Masa perjanjian kerja sama ini telah habis secara formal. Hubungan kemitraan dapat dipulihkan melalui perpanjangan/reaktivasi naskah.',
            dateLabel: 'TANGGAL KEDALUWARSA / BERAKHIR:'
        },
        'CANCELLED': {
            badgeClass: 'badge bg-dark text-white ms-1',
            badgeText: 'CANCELLED (Dibatalkan)',
            alertBoxClass: 'alert alert-dark py-2 px-3 small border-0 bg-soft-secondary text-dark d-flex align-items-start gap-2 mb-0',
            alertIconClass: 'bx bx-block text-dark mt-1 font-size-16',
            alertText: '<strong>Usulan Kemitraan Dibatalkan:</strong> Pengusulan dokumen kerja sama ini telah dihentikan secara permanen oleh pengusul.',
            dateLabel: 'TANGGAL PEMBATALAN:'
        }
    };

    const cfg = fsmStatusConfig[status] || {
        badgeClass: 'badge bg-secondary text-white ms-1',
        badgeText: (status || 'UNKNOWN') + ' (Status FSM)',
        alertBoxClass: 'alert alert-secondary py-2 px-3 small border-0 bg-soft-secondary text-dark d-flex align-items-start gap-2 mb-0',
        alertIconClass: 'bx bx-info-circle text-secondary mt-1 font-size-16',
        alertText: 'Status dokumen saat ini tercatat sebagai <strong>' + (status || '-') + '</strong> pada mesin FSM SIMKERMA.',
        dateLabel: 'TANGGAL DOKUMEN:'
    };

    // Update Status Badge
    const badgeEl = document.getElementById('qrModalStatusBadge');
    badgeEl.className = cfg.badgeClass;
    badgeEl.innerText = cfg.badgeText;

    // Update Alert Petunjuk & Deskripsi Prosedur
    const alertBox = document.getElementById('qrModalAlertBox');
    const alertIcon = document.getElementById('qrModalAlertIcon');
    const alertText = document.getElementById('qrModalAlertText');
    const dateLabel = document.getElementById('qrModalDateLabel');
    if (alertBox) alertBox.className = cfg.alertBoxClass;
    if (alertIcon) alertIcon.className = cfg.alertIconClass;
    if (alertText) alertText.innerHTML = cfg.alertText;
    if (dateLabel) dateLabel.innerText = cfg.dateLabel;
    
    // Set Link Verifikasi Resmi
    const verifyUrl = window.location.origin + '/verify/' + encodeURIComponent(regId);
    document.getElementById('qrModalVerifyLink').href = verifyUrl;
    
    // Generate QR Code ke dalam kontainer
    const container = document.getElementById('qrcodeCanvas');
    container.innerHTML = '';
    
    try {
        currentQrInstance = new QRCode(container, {
            text: verifyUrl,
            width: 150,
            height: 150,
            colorDark: "#2a3042",
            colorLight: "#ffffff",
            correctLevel: QRCode.CorrectLevel.M
        });
    } catch (e) {
        container.innerHTML = '<div class="alert alert-secondary p-2 small mb-0"><i class="bx bx-qr font-size-24 d-block text-dark"></i>' + regId + '</div>';
    }
    
    const qrModal = new bootstrap.Modal(document.getElementById('qrModal'));
    qrModal.show();
}

function openPrintableVerification() {
    const link = document.getElementById('qrModalVerifyLink');
    if (link && link.href) {
        window.open(link.href, '_blank');
    }
}
</script>

@stack('scripts')
</body>
</html>
