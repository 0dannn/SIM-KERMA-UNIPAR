<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'SIM-KERMA UNIPAR - Sistem Informasi Dokumen Kerja Sama' }}</title>

    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ asset('partial/images/favicon.ico') }}">

    <!-- Google Fonts: IBM Plex Sans & Public Sans, Mulish, Instrument Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,400&family=Instrument+Sans:wght@400;500;600;700&family=Mulish:wght@600;700&family=Spline+Sans+Mono:wght@400&display=swap" rel="stylesheet">

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

        /* Branding Navbar (Tema Terang Bersih Sesuai Minia) */
        .navbar-brand-box {
            background: #ffffff !important;
            border-right: 1px solid #e9e9ef !important;
            box-shadow: none !important;
        }
        .navbar-brand-box .logo-txt {
            font-size: 1.05rem;
            font-weight: 700;
            color: #5156be !important;
            letter-spacing: 0.8px;
        }

        /* Topbar & Search (Tema Terang) */
        #page-topbar,
        body[data-topbar="dark"] #page-topbar {
            background-color: #ffffff !important;
            border-bottom: 1px solid #e9e9ef !important;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.03) !important;
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

        /* Vertical Sidebar Styling (Tema Terang Bersih Minia kerma_lama) */
        .vertical-menu,
        body[data-sidebar="dark"] .vertical-menu,
        body[data-sidebar="light"] .vertical-menu {
            background: #ffffff !important;
            border-right: 1px solid #e9e9ef !important;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.04) !important;
        }
        #sidebar-menu ul li.menu-title,
        body[data-sidebar="dark"] #sidebar-menu ul li.menu-title {
            color: #7b8190 !important;
            font-size: 0.68rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            font-weight: 600;
            padding: 14px 20px 6px;
        }
        #sidebar-menu ul li a,
        body[data-sidebar="dark"] #sidebar-menu ul li a {
            color: #555b6d !important;
            font-size: 0.84rem;
            font-weight: 500;
            padding: 10px 18px;
            display: flex;
            align-items: center;
            transition: all 0.2s ease;
            text-decoration: none;
            border-radius: 6px;
            margin: 2px 10px;
        }
        #sidebar-menu ul li a i,
        body[data-sidebar="dark"] #sidebar-menu ul li a i {
            font-size: 1.15rem;
            min-width: 1.75rem;
            color: #74788d !important;
            transition: all 0.2s ease;
        }
        #sidebar-menu ul li a:hover,
        body[data-sidebar="dark"] #sidebar-menu ul li a:hover {
            color: #5156be !important;
            background-color: #f3f3f9 !important;
        }
        #sidebar-menu ul li a:hover i,
        body[data-sidebar="dark"] #sidebar-menu ul li a:hover i {
            color: #5156be !important;
        }
        #sidebar-menu ul li a.active,
        #sidebar-menu ul li.mm-active > a,
        body[data-sidebar="dark"] #sidebar-menu ul li a.active,
        body[data-sidebar="dark"] #sidebar-menu ul li.mm-active > a {
            color: #ffffff !important;
            background-color: #5156be !important;
            font-weight: 600;
            box-shadow: 0 2px 6px rgba(81, 86, 190, 0.3) !important;
        }
        #sidebar-menu ul li a.active i,
        #sidebar-menu ul li.mm-active > a i,
        body[data-sidebar="dark"] #sidebar-menu ul li a.active i,
        body[data-sidebar="dark"] #sidebar-menu ul li.mm-active > a i {
            color: #ffffff !important;
        }

        /* Sidebar Footer / User Identity (Tema Terang) */
        .sidebar-bottom-profile {
            background-color: #f8f9fa !important;
            border-top: 1px solid #e9e9ef !important;
            padding: 14px 18px;
            margin-top: auto;
            transition: all 0.2s ease;
        }

        /* ============================================================== */
        /* PERBAIKAN SIDEBAR KETIKA DI-MINIMIZE (data-sidebar-size="sm")  */
        /* ============================================================== */
        body[data-sidebar-size="sm"] .navbar-brand-box {
            width: 70px !important;
            padding: 0 !important;
            text-align: center !important;
        }
        body[data-sidebar-size="sm"] .navbar-brand-box .logo span.logo-lg {
            display: none !important;
        }
        body[data-sidebar-size="sm"] .navbar-brand-box .logo span.logo-sm {
            display: inline-block !important;
            line-height: 70px !important;
        }
        body[data-sidebar-size="sm"] #vertical-menu-btn {
            margin-left: 0 !important;
            margin-right: 15px !important;
        }
        body[data-sidebar-size="sm"] .vertical-menu {
            width: 70px !important;
            position: absolute !important;
            z-index: 1001 !important;
        }
        body[data-sidebar-size="sm"] .vertical-menu #sidebar-menu .menu-title {
            display: none !important;
        }
        body[data-sidebar-size="sm"] #sidebar-menu ul li a {
            margin: 4px 0 !important;
            padding: 12px 0 !important;
            text-align: center !important;
            justify-content: center !important;
            border-radius: 0 !important;
            display: block !important;
            width: 70px !important;
        }
        body[data-sidebar-size="sm"] #sidebar-menu ul li a i {
            min-width: unset !important;
            margin: 0 auto !important;
            font-size: 1.35rem !important;
            display: block !important;
            line-height: 1 !important;
        }
        body[data-sidebar-size="sm"] #sidebar-menu ul li a span {
            display: none !important;
        }
        /* Hover flyout state for minimized sidebar items */
        body[data-sidebar-size="sm"] .vertical-menu #sidebar-menu > ul > li:hover > a {
            position: relative !important;
            width: 250px !important;
            background-color: #ffffff !important;
            color: #5156be !important;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.08) !important;
            border: 1px solid #e9e9ef !important;
            border-left: none !important;
            border-radius: 0 6px 6px 0 !important;
            display: flex !important;
            align-items: center !important;
            justify-content: flex-start !important;
            padding: 12px 18px !important;
            z-index: 1005 !important;
            text-align: left !important;
        }
        body[data-sidebar-size="sm"] .vertical-menu #sidebar-menu > ul > li:hover > a i {
            margin: 0 12px 0 0 !important;
            color: #5156be !important;
            font-size: 1.25rem !important;
            display: inline-block !important;
        }
        body[data-sidebar-size="sm"] .vertical-menu #sidebar-menu > ul > li:hover > a span {
            display: inline !important;
            padding-left: 0 !important;
            white-space: nowrap !important;
            font-size: 0.84rem !important;
            font-weight: 600 !important;
            color: #5156be !important;
        }
        /* Active item in minimized state */
        body[data-sidebar-size="sm"] #sidebar-menu ul li.mm-active > a,
        body[data-sidebar-size="sm"] #sidebar-menu ul li a.active {
            background-color: #5156be !important;
            color: #ffffff !important;
        }
        body[data-sidebar-size="sm"] #sidebar-menu ul li.mm-active > a i,
        body[data-sidebar-size="sm"] #sidebar-menu ul li a.active i {
            color: #ffffff !important;
        }
        body[data-sidebar-size="sm"] .vertical-menu #sidebar-menu > ul > li.mm-active:hover > a {
            background-color: #5156be !important;
            color: #ffffff !important;
        }
        body[data-sidebar-size="sm"] .vertical-menu #sidebar-menu > ul > li.mm-active:hover > a i,
        body[data-sidebar-size="sm"] .vertical-menu #sidebar-menu > ul > li.mm-active:hover > a span {
            color: #ffffff !important;
        }
        /* Sembunyikan profil teks di bagian bawah sidebar saat di-minimize agar tidak overflow */
        body[data-sidebar-size="sm"] .sidebar-bottom-profile {
            display: none !important;
        }

        /* Cegah / Netralkan Segala Efek Tema Gelap (Force Pure Light) */
        body[data-layout-mode="dark"],
        body[data-layout-mode="dark"] .vertical-menu,
        body[data-layout-mode="dark"] #page-topbar,
        body[data-layout-mode="dark"] .navbar-brand-box {
            background-color: #ffffff !important;
            color: #495057 !important;
        }
        body[data-layout-mode="dark"] .card {
            background-color: #ffffff !important;
            color: #495057 !important;
            border-color: #e9e9ef !important;
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
        /* Hilangkan double-border & margin bawah saat table-responsive berada langsung di dalam card */
        .card > .table-responsive,
        .card-wireframe > .table-responsive {
            border: none !important;
            border-radius: 0 !important;
            margin-bottom: 0 !important;
        }
        .card > .table-responsive > .table,
        .card-wireframe > .table-responsive > .table {
            border-left: none !important;
            border-right: none !important;
        }
        .card-footer {
            border-bottom-left-radius: 8px !important;
            border-bottom-right-radius: 8px !important;
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
        .btn-outline-primary {
            color: #4549a2 !important;
            border-color: #5156be !important;
        }
        .btn-outline-primary:hover, .btn-outline-primary:focus {
            background-color: #5156be !important;
            border-color: #5156be !important;
            color: #ffffff !important;
        }
        .btn-success {
            background-color: #157347 !important;
            border-color: #157347 !important;
            color: #ffffff !important;
        }
        .btn-info {
            background-color: #0c63a7 !important;
            border-color: #0c63a7 !important;
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
        .badge.bg-success { background-color: #147a50 !important; color: #ffffff !important; }
        .badge.bg-info    { background-color: #0c63a7 !important; color: #ffffff !important; }
        .badge.bg-warning { background-color: #ffbf53 !important; color: #212529 !important; }
        .badge.bg-danger  { background-color: #c0322f !important; color: #ffffff !important; }
        .badge.bg-secondary { background-color: #74788d !important; }
        .badge-soft-primary { background-color: rgba(81, 86, 190, 0.12) !important; color: #3c409c !important; }
        .badge-soft-success { background-color: rgba(42, 181, 125, 0.13) !important; color: #116944 !important; }
        .badge-soft-info    { background-color: rgba(75, 166, 239, 0.14) !important; color: #0c63a7 !important; }
        .badge-soft-warning { background-color: rgba(255, 191, 83, 0.20) !important; color: #805300 !important; }
        .badge-soft-danger  { background-color: rgba(253, 98, 94, 0.13)  !important; color: #c0322f !important; }
        .badge-soft-secondary { background-color: rgba(116, 120, 141, 0.14) !important; color: #545869 !important; }

        /* Subtle helpers & WCAG AA Muted/Secondary Contrast */
        .text-primary { color: #5156be !important; }
        .text-success { color: #116944 !important; }
        .kpi-val-warning { color: #9a5b00 !important; }
        .text-muted, .text-secondary { color: #595e70 !important; }
        .breadcrumb-item.active { color: #525768 !important; }
        .btn-outline-secondary { color: #525768 !important; border-color: #94a3b8 !important; }
        .btn-outline-secondary:hover { background-color: #525768 !important; border-color: #525768 !important; color: #ffffff !important; }
        .modal-header.bg-dark .text-white-50 { color: rgba(255, 255, 255, 0.80) !important; }
        .border-primary { border-color: #5156be !important; }
        .bg-soft-primary { background-color: rgba(81, 86, 190, 0.1) !important; }
        .bg-soft-success { background-color: rgba(42, 181, 125, 0.1) !important; }
        .bg-soft-warning { background-color: rgba(255, 191, 83, 0.1) !important; }
        .bg-soft-danger  { background-color: rgba(253, 98, 94, 0.1) !important; }
        .bg-soft-info    { background-color: rgba(75, 166, 239, 0.1) !important; }
        .bg-soft-secondary { background-color: rgba(116, 120, 141, 0.1) !important; }

        /* Kompatibilitas Bootstrap 5.3 subtle/emphasis & spacing halus pada Bootstrap 5.0.2 */
        .bg-primary-subtle { background-color: rgba(81, 86, 190, 0.12) !important; }
        .border-primary-subtle { border-color: rgba(81, 86, 190, 0.28) !important; }
        .bg-success-subtle { background-color: rgba(42, 181, 125, 0.12) !important; }
        .border-success-subtle { border-color: rgba(42, 181, 125, 0.28) !important; }
        .bg-info-subtle { background-color: rgba(75, 166, 239, 0.13) !important; }
        .border-info-subtle { border-color: rgba(75, 166, 239, 0.30) !important; }
        .text-info-emphasis { color: #1976b2 !important; }
        .bg-warning-subtle { background-color: rgba(255, 191, 83, 0.18) !important; }
        .border-warning-subtle { border-color: rgba(255, 191, 83, 0.42) !important; }
        .text-warning-emphasis { color: #9a6700 !important; }
        .bg-danger-subtle { background-color: rgba(253, 98, 94, 0.12) !important; }
        .border-danger-subtle { border-color: rgba(253, 98, 94, 0.28) !important; }
        .bg-secondary-subtle { background-color: rgba(116, 120, 141, 0.12) !important; }
        .border-secondary-subtle { border-color: rgba(116, 120, 141, 0.28) !important; }
        .bg-light-subtle { background-color: #f8f9fa !important; }

        .p-2\.5 { padding: 0.625rem !important; }
        .p-3\.5 { padding: 0.875rem !important; }
        .py-0\.5 { padding-top: 0.125rem !important; padding-bottom: 0.125rem !important; }
        .py-1\.5 { padding-top: 0.375rem !important; padding-bottom: 0.375rem !important; }
        .py-2\.5 { padding-top: 0.625rem !important; padding-bottom: 0.625rem !important; }
        .px-1\.5 { padding-left: 0.375rem !important; padding-right: 0.375rem !important; }
        .px-2\.5 { padding-left: 0.625rem !important; padding-right: 0.625rem !important; }
        .mt-0\.5 { margin-top: 0.125rem !important; }
        .mb-0\.5 { margin-bottom: 0.125rem !important; }
        .mb-1\.5 { margin-bottom: 0.375rem !important; }
        .mb-2\.5 { margin-bottom: 0.625rem !important; }
        .me-0\.5 { margin-right: 0.125rem !important; }
        .me-1\.5 { margin-right: 0.375rem !important; }
        .gap-0\.5 { gap: 0.125rem !important; }
        .gap-1\.5 { gap: 0.375rem !important; }
        .gap-2\.5 { gap: 0.625rem !important; }
        .shadow-2xs { box-shadow: 0 1px 2px rgba(15, 23, 42, 0.05) !important; }
        .overflow-x-auto { overflow-x: auto !important; }
        .overflow-y-auto { overflow-y: auto !important; }
        .dropdown-toggle-no-caret::after { display: none !important; }

        /* Menu 1 Dashboard — Interactive Cards, Icon Boxes & Distribution Bars */
        .dash-kpi-card {
            transition: transform 0.15s ease, box-shadow 0.15s ease;
            cursor: pointer;
            text-decoration: none !important;
            display: block;
        }
        .dash-kpi-card:hover,
        .dash-kpi-card:focus-visible {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(15, 23, 42, 0.08) !important;
            outline: 2px solid rgba(81, 86, 190, 0.35);
        }
        .dash-kpi-icon {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            flex-shrink: 0;
        }
        .dash-dist-link {
            color: inherit;
            text-decoration: none !important;
            transition: background-color 0.15s ease;
        }
        .dash-dist-link:hover,
        .dash-dist-link:focus-visible {
            background-color: #f8f9ff !important;
        }
        .dash-dist-bar {
            height: 5px;
            border-radius: 999px;
            background-color: #eef0f7;
            overflow: hidden;
        }

        /* Menu 3 Repositori Dokumen — Table Card & Filter Bar Polish */
        .repo-filter-bar {
            background-color: #f8f9fa;
            border-bottom: 1px solid #e9e9ef;
            padding: 10px 16px 0 16px;
        }
        #repoTabs {
            border-bottom: none;
            gap: 4px;
            margin-bottom: 0;
            overflow-x: auto;
            overflow-y: hidden;
            scrollbar-width: none;
        }
        #repoTabs::-webkit-scrollbar {
            display: none;
        }
        @media (min-width: 992px) {
            #repoTabs {
                overflow: visible !important;
            }
        }
        #repoTabs .repo-tab-btn {
            border: 1px solid transparent;
            border-bottom: none;
            border-radius: 6px 6px 0 0;
            padding: 8px 12px;
            font-size: 0.78rem;
            color: #525768;
            background: transparent;
            transition: color 0.15s ease, background-color 0.15s ease;
        }
        #repoTabs .repo-tab-btn:focus-visible,
        #repoTable thead th.repo-sortable:focus-visible {
            outline: 2px solid #5156be !important;
            outline-offset: -2px;
        }
        #repoTabs .repo-tab-btn:hover:not(.active) {
            color: #5156be;
            background-color: rgba(81, 86, 190, 0.05);
        }
        #repoTabs .repo-tab-btn.active {
            background-color: #ffffff !important;
            color: #5156be !important;
            border-color: #e9e9ef !important;
            border-bottom: 1px solid #ffffff !important;
            margin-bottom: -1px;
            font-weight: 600;
        }
        .repo-controls {
            padding-bottom: 8px;
        }
        .repo-controls .repo-search-group {
            width: 255px;
        }
        @media (max-width: 575.98px) {
            .repo-controls {
                width: 100%;
            }
            .repo-controls .repo-search-group {
                width: 100% !important;
            }
        }
        .repo-controls .form-select-sm,
        #repoAdvancedFilterPanel .form-select-sm {
            padding-right: 1.85rem !important;
            text-overflow: ellipsis;
            white-space: nowrap;
            overflow: hidden;
        }
        .repo-controls .form-select-sm,
        .repo-controls .input-group-sm .form-control,
        .repo-controls .input-group-sm .input-group-text,
        .repo-controls .input-group-sm .btn {
            height: 31px !important;
            padding-top: 4px !important;
            padding-bottom: 4px !important;
            font-size: 0.78rem !important;
            line-height: 1.3 !important;
        }
        .repo-controls .input-group-sm .input-group-text {
            padding-left: 10px !important;
            padding-right: 8px !important;
        }
        #repoTable {
            table-layout: fixed;
            width: 100%;
            min-width: 880px;
            border-color: #e2e6ee !important;
        }
        #repoTable th,
        #repoTable td {
            border-color: #e2e6ee !important;
        }
        #repoTable thead th {
            padding: 10px 8px !important;
            font-size: 0.78rem !important;
            line-height: 1.25;
            white-space: nowrap;
            text-align: center !important;
        }
        #partnerTable thead th {
            padding: 10px 8px !important;
            font-size: 0.74rem !important;
            line-height: 1.25;
            white-space: nowrap;
            text-align: center !important;
        }
        #repoTable thead th.repo-sortable,
        #partnerTable thead th.partner-sortable {
            cursor: pointer;
            user-select: none;
            transition: background-color 0.15s ease, color 0.15s ease;
        }
        #repoTable thead th.repo-sortable:hover,
        #partnerTable thead th.partner-sortable:hover {
            background-color: #eef0f7 !important;
            color: #5156be !important;
        }
        #repoTable thead th.repo-sortable.sort-active,
        #partnerTable thead th.partner-sortable.sort-active {
            color: #5156be !important;
            background-color: #f3f4fb !important;
        }
        .repo-sort-icon,
        .partner-sort-icon {
            font-size: 0.75rem;
            margin-left: 4px;
            opacity: 0.72;
            vertical-align: middle;
        }
        #repoTable thead th.sort-active .repo-sort-icon,
        #partnerTable thead th.sort-active .partner-sort-icon {
            opacity: 1;
            color: #5156be;
        }
        #repoTable tbody tr:nth-of-type(odd) {
            background-color: #ffffff;
        }
        #repoTable tbody tr:nth-of-type(even) {
            background-color: #f8f9fc !important;
        }
        #repoTable tbody tr:hover {
            background-color: #eef1fb !important;
        }
        #repoTable tbody td {
            padding: 11px 8px !important;
            font-size: 0.78rem !important;
            line-height: 1.4;
            word-wrap: break-word;
            overflow-wrap: anywhere;
        }
        #repoTable.hide-legacy-badges .repo-legacy-badge {
            display: none !important;
        }
        .repo-doc-title:hover,
        .repo-file-link:hover {
            color: #5156be !important;
            text-decoration: underline !important;
        }
        .btn-repo-xs {
            padding: 4px 10px !important;
            font-size: 0.75rem !important;
            line-height: 1.25 !important;
            border-radius: 5px !important;
            min-height: 30px;
        }
        #repoTable .repo-action-btn {
            min-width: 31px;
            min-height: 31px;
            height: 31px;
            border-radius: 4px !important;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 4px 10px !important;
            font-size: 0.75rem !important;
        }
        #repoTable .repo-action-icon {
            width: 31px;
            padding: 0 !important;
        }
        .repo-inline-pill {
            min-height: 24px !important;
            padding: 3px 8px !important;
            font-size: 0.72rem !important;
            display: inline-flex !important;
            align-items: center;
            gap: 4px;
        }
        #repoPaginationWrapper .btn,
        #repoPerPage {
            min-height: 30px !important;
            min-width: 30px !important;
            font-size: 0.76rem !important;
        }
        .repo-cta-primary {
            min-height: 32px !important;
            display: inline-flex !important;
            align-items: center;
        }
        .repo-kpi-chip {
            background-color: #ffffff !important;
            border-color: #d8dce6 !important;
            color: #334155 !important;
            font-weight: 500;
            min-height: 30px !important;
            font-size: 0.75rem !important;
            transition: background-color 0.15s ease, border-color 0.15s ease, color 0.15s ease;
        }
        .repo-kpi-chip:hover {
            background-color: #f8f9ff !important;
            border-color: #5156be !important;
            color: #373ca1 !important;
        }
        .repo-kpi-chip.active {
            background-color: #eef0fb !important;
            border-color: #5156be !important;
            color: #373ca1 !important;
            font-weight: 600;
        }
        .repo-legacy-badge {
            background-color: #f1f5f9 !important;
            color: #475569 !important;
            border: 1px solid #cbd5e1 !important;
        }
        .repo-partner-wrap {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            line-height: 1.3;
        }
        .table-audit-modal thead th {
            background-color: #343a40 !important;
            color: #ffffff !important;
            border-color: #454d55 !important;
        }
        .badge-repo-compact {
            padding: 3px 7px !important;
            font-size: 0.72rem !important;
            font-weight: 600;
            border-radius: 4px;
            display: inline-block;
            line-height: 1.2;
        }
        /* Pembeda Visual Jenis Dokumen Menu 3 (MoU = Level 1 Indigo, MoA/PKS = Level 2 Teal, IA = Level 3 Plum) */
        .badge-doc-mou {
            background-color: #5156be !important;
            color: #ffffff !important;
            border: 1px solid #4549a2 !important;
        }
        .badge-doc-moa {
            background-color: rgba(13, 148, 136, 0.12) !important;
            color: #0d6860 !important;
            border: 1px solid rgba(13, 148, 136, 0.35) !important;
        }
        .badge-doc-ia {
            background-color: rgba(147, 51, 234, 0.10) !important;
            color: #6b21a8 !important;
            border: 1px solid rgba(147, 51, 234, 0.32) !important;
        }
        .repo-type-dot {
            display: inline-block;
            width: 7px;
            height: 7px;
            border-radius: 50%;
            margin-right: 5px;
            vertical-align: middle;
        }

        /* Menu 2 Data Mitra — Proporsi Tabel Tetap, Ergonomis & Bebas Garis Kisi Vertikal */
        #partnerTable {
            table-layout: fixed;
            width: 100%;
            min-width: 900px;
            border-left: none !important;
            border-right: none !important;
        }
        #partnerTable th,
        #partnerTable td {
            border-left: none !important;
            border-right: none !important;
        }
        #partnerTable thead th {
            padding: 10px 8px !important;
        }
        #partnerTable thead th.partner-sortable:focus-visible {
            outline: 2px solid #5156be !important;
            outline-offset: -2px;
        }
        #partnerTable tbody tr:nth-of-type(odd) {
            background-color: #ffffff;
        }
        #partnerTable tbody tr:nth-of-type(even) {
            background-color: #f8f9fc !important;
        }
        #partnerTable tbody tr:hover {
            background-color: #eef1fb !important;
        }
        #partnerTable tbody td {
            padding: 11px 8px !important;
            font-size: 0.78rem !important;
            line-height: 1.4;
            word-wrap: break-word;
            overflow-wrap: anywhere;
        }
        #partnerTable .partner-action-btn {
            height: 32px !important;
            min-width: 32px !important;
            display: inline-flex !important;
            align-items: center;
            justify-content: center;
            white-space: nowrap !important;
            line-height: 1 !important;
        }
        .btn-partner-profile {
            background-color: #f3f4fb !important;
            color: #3c409c !important;
            border: 1px solid #c7cbf2 !important;
            transition: background-color 0.15s ease, color 0.15s ease, border-color 0.15s ease;
        }
        .btn-partner-profile:hover,
        .btn-partner-profile:focus-visible {
            background-color: #5156be !important;
            color: #ffffff !important;
            border-color: #5156be !important;
        }
        .partner-filter-bar {
            background-color: #f8f9fa !important;
            border-bottom: 1px solid #e9e9ef !important;
        }
        .partner-filter-bar .form-select-sm,
        .partner-filter-bar .form-control,
        .partner-filter-bar .btn {
            min-height: 32px !important;
            font-size: 0.80rem !important;
        }
        .partner-toolbar .btn {
            min-height: 32px !important;
            display: inline-flex;
            align-items: center;
            font-size: 0.78rem !important;
        }
        #partnerPaginationWrapper .btn,
        #partnerPerPage {
            min-height: 30px !important;
            min-width: 30px !important;
            font-size: 0.76rem !important;
        }
        .badge-partner-cat {
            background-color: #f8fafc !important;
            color: #334155 !important;
            border: 1px solid #94a3b8 !important;
        }
        /* Color-Coding 5 Sektor Resmi Mitra Menu 2 */
        .badge-cat-pt { background-color: #eef0fb !important; color: #373ca1 !important; border: 1px solid #c7cbf2 !important; }
        .badge-cat-gov { background-color: #e0f2fe !important; color: #0369a1 !important; border: 1px solid #7dd3fc !important; }
        .badge-cat-ind { background-color: #fef3c7 !important; color: #92400e !important; border: 1px solid #fcd34d !important; }
        .badge-cat-eduhealth { background-color: #ccfbf1 !important; color: #0f766e !important; border: 1px solid #5eead4 !important; }
        .badge-cat-org { background-color: #f3e8ff !important; color: #6b21a8 !important; border: 1px solid #d8b4fe !important; }

        .badge-scope-intl { background-color: #e0f2fe !important; color: #0369a1 !important; border: 1px solid #7dd3fc !important; }
        .badge-scope-local { background-color: #f8fafc !important; color: #475569 !important; border: 1px solid #cbd5e1 !important; }
        .badge-naskah-soft { background-color: #eef0f7 !important; color: #373ca1 !important; border: 1px solid #c7cbf2 !important; }
        .badge-comp-neutral { background-color: #f8fafc !important; color: #334155 !important; border: 1px solid #cbd5e1 !important; }

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
        /* =========================================================================
           FSM WORKFLOW MODAL STYLES (Tema Ungu Harmonis SIMKERMA UNIPAR #5156be)
           ========================================================================= */
        :root {
            --fsm-royal-violet: #5156be;
            --fsm-soft-lavender: rgba(81, 86, 190, 0.08);
            --fsm-on-primary-container: #2b2e6b;
            --fsm-border: #E2E4EC;
            --fsm-error: #DC2626;
            --fsm-error-container: #FEF2F2;
        }

        .fsm-modal-dialog {
            max-width: 960px !important;
        }
        .fsm-modal-content {
            border-radius: 12px !important;
            border: 1px solid #E2E4EC !important;
            overflow: hidden;
            background-color: #ffffff;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04) !important;
            font-family: 'Instrument Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        }
        .fsm-header {
            background-color: #5156be !important;
            color: #ffffff !important;
            padding: 16px 24px;
        }
        .fsm-header-icon {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            background-color: #ffffff;
            color: #5156be;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .fsm-close-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
            border-radius: 8px;
            color: #ffffff;
            background: transparent;
            border: none;
            transition: background-color 0.15s ease;
        }
        .fsm-close-btn:hover {
            background-color: rgba(255, 255, 255, 0.15);
            color: #ffffff;
        }
        .fsm-title-banner {
            background-color: rgba(81, 86, 190, 0.08);
            border: 1px solid rgba(81, 86, 190, 0.18);
            border-radius: 8px;
            padding: 14px 16px;
        }
        .fsm-card-elevated {
            background-color: #ffffff;
            border: 1px solid #E2E4EC;
            border-radius: 8px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }
        .fsm-card-elevated .card-header {
            background-color: rgba(81, 86, 190, 0.08) !important;
            border-bottom: 1px solid #E2E4EC !important;
            padding: 12px 16px;
            color: #5156be;
            font-weight: 700;
            font-size: 0.92rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .fsm-card-elevated .card-body {
            padding: 16px;
            font-size: 0.82rem;
        }
        .fsm-btn-primary {
            background-color: #5156be !important;
            color: #ffffff !important;
            border: none !important;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.88rem;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.15s ease-in-out;
        }
        .fsm-btn-primary:hover {
            background-color: #4549a2 !important;
            color: #ffffff !important;
            opacity: 0.95;
        }
        .fsm-btn-secondary {
            background-color: #ffffff !important;
            color: #1E202B !important;
            border: 1px solid #E2E4EC !important;
            padding: 10px 16px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.88rem;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.15s ease-in-out;
        }
        .fsm-btn-secondary:hover {
            background-color: #F8F9FC !important;
            color: #1E202B !important;
        }
        .fsm-btn-destructive {
            background-color: #ffffff !important;
            color: #DC2626 !important;
            border: 1px solid rgba(220, 38, 38, 0.4) !important;
            padding: 10px 16px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.88rem;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.15s ease-in-out;
        }
        .fsm-btn-destructive:hover {
            background-color: #FEF2F2 !important;
            color: #DC2626 !important;
        }
        .fsm-btn-pdf {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 14px;
            border-radius: 8px;
            background-color: #FEF2F2;
            color: #DC2626;
            border: 1px solid rgba(220, 38, 38, 0.3);
            font-weight: 600;
            font-size: 0.84rem;
            text-decoration: none;
            transition: all 0.15s ease-in-out;
        }
        .fsm-btn-pdf:hover {
            background-color: rgba(220, 38, 38, 0.12);
            color: #B91C1C;
        }

        /* =========================================================================
           DETAIL DOKUMEN KERJA SAMA (ISOLATED SCOPED CSS - HIGH READABILITY)
           ========================================================================= */
        .detail-doc-shell {
            max-width: 1440px;
            margin: 0 auto;
            font-size: 0.875rem;
            line-height: 1.5;
            color: #1e293b;
        }
        .detail-doc-shell .detail-page-bar {
            gap: 12px;
            margin-bottom: 14px;
        }
        .detail-doc-shell .detail-page-heading {
            font-size: 1rem;
            font-weight: 700;
            color: #334155;
            letter-spacing: -0.01em;
        }
        .detail-doc-shell .detail-back-btn {
            width: 34px;
            height: 34px;
            border-radius: 8px;
            border-color: #cbd5e1;
            color: #334155;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #ffffff;
            transition: all 0.15s ease;
        }
        .detail-doc-shell .detail-back-btn:hover {
            background: #f8fafc;
            border-color: #5156be;
            color: #5156be;
        }

        /* Scoped Card Overrides (Bebas dari global .card-header/.card-body 20px !important) */
        .detail-doc-shell .detail-card {
            background-color: #ffffff;
            border: 1px solid #e2e8f0 !important;
            border-radius: 10px !important;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04) !important;
            margin-bottom: 16px;
        }
        .detail-doc-shell .detail-card > .card-header {
            background-color: #ffffff !important;
            border-bottom: 1px solid #f1f5f9 !important;
            padding: 12px 18px !important;
            border-top-left-radius: 10px !important;
            border-top-right-radius: 10px !important;
        }
        .detail-doc-shell .detail-card > .card-body {
            padding: 16px 18px !important;
        }
        .detail-doc-shell .detail-card > .card-body.p-0 {
            padding: 0 !important;
        }
        .detail-doc-shell .detail-card-title {
            font-size: 0.92rem;
            font-weight: 700;
            color: #1e293b;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* Summary Banner */
        .detail-doc-shell .detail-summary-banner > .card-body {
            padding: 16px 20px !important;
        }
        .detail-doc-shell .detail-doc-icon {
            width: 48px;
            height: 48px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .detail-doc-shell .detail-doc-title {
            font-size: 1.25rem;
            font-weight: 700;
            line-height: 1.35;
            color: #0f172a;
            letter-spacing: -0.02em;
            margin-bottom: 6px;
        }
        .detail-doc-shell .detail-doc-parties {
            font-size: 0.86rem;
            color: #475569;
        }

        /* Underline Modern Nav Tabs */
        .detail-doc-shell .detail-tabs {
            border-bottom: 2px solid #e2e8f0;
            gap: 4px;
            flex-wrap: nowrap;
            overflow-x: auto;
            overflow-y: hidden;
            scrollbar-width: none;
            margin-bottom: 16px;
        }
        .detail-doc-shell .detail-tabs::-webkit-scrollbar {
            display: none;
        }
        .detail-doc-shell .detail-tabs .nav-link {
            border: none !important;
            border-bottom: 2px solid transparent !important;
            margin-bottom: -2px;
            background: transparent !important;
            color: #64748b !important;
            font-size: 0.85rem;
            font-weight: 500;
            padding: 10px 14px !important;
            white-space: nowrap;
            border-radius: 6px 6px 0 0;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: color 0.15s ease, background-color 0.15s ease, border-color 0.15s ease;
        }
        .detail-doc-shell .detail-tabs .nav-link:hover:not(.active) {
            color: #5156be !important;
            background-color: rgba(81, 86, 190, 0.05) !important;
        }
        .detail-doc-shell .detail-tabs .nav-link.active {
            color: #5156be !important;
            font-weight: 700 !important;
            border-bottom-color: #5156be !important;
            background-color: rgba(81, 86, 190, 0.04) !important;
        }

        /* Symmetrical 7-Node Horizontal Stepper */
        .detail-doc-shell .detail-stepper-scroll {
            overflow-x: auto;
            padding-bottom: 6px;
            margin-bottom: 12px;
            -webkit-overflow-scrolling: touch;
        }
        .detail-doc-shell .detail-stepper-track {
            position: relative;
            min-width: 610px;
            padding-top: 2px;
        }
        .detail-doc-shell .detail-stepper-line {
            position: absolute;
            top: 15px;
            left: calc(100% / 14);
            right: calc(100% / 14);
            height: 3px;
            background-color: #e2e8f0;
            z-index: 1;
            border-radius: 2px;
            overflow: hidden;
        }
        .detail-doc-shell .detail-stepper-grid {
            display: grid;
            grid-template-columns: repeat(7, minmax(0, 1fr));
            position: relative;
            z-index: 2;
            gap: 4px;
        }
        .detail-doc-shell .detail-step-node {
            text-align: center;
            padding: 0 2px;
        }
        .detail-doc-shell .detail-step-circle {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
            font-size: 0.78rem;
        }
        .detail-doc-shell .detail-step-label {
            font-size: 0.76rem;
            line-height: 1.25;
            margin-top: 6px;
            color: #334155;
        }
        .detail-doc-shell .detail-step-date {
            font-size: 0.71rem;
            line-height: 1.25;
            margin-top: 2px;
            color: #64748b;
        }
        .detail-doc-shell .detail-step-warning-text {
            color: #b45309 !important;
        }

        /* Clean Key-Value List (Bebas border kotak global .table) */
        .detail-doc-shell .detail-kv-list {
            display: flex;
            flex-direction: column;
        }
        .detail-doc-shell .detail-kv-row {
            display: flex;
            flex-direction: column;
            gap: 2px;
            padding: 8px 0;
            border-bottom: 1px solid #f1f5f9;
        }
        .detail-doc-shell .detail-kv-row:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }
        .detail-doc-shell .detail-kv-row:first-child {
            padding-top: 0;
        }
        @media (min-width: 576px) {
            .detail-doc-shell .detail-kv-row {
                flex-direction: row;
                align-items: baseline;
                gap: 12px;
            }
            .detail-doc-shell .detail-kv-label {
                width: 175px;
                flex-shrink: 0;
            }
        }
        .detail-doc-shell .detail-kv-label {
            font-size: 0.81rem;
            font-weight: 500;
            color: #64748b;
        }
        .detail-doc-shell .detail-kv-value {
            font-size: 0.86rem;
            font-weight: 500;
            color: #0f172a;
            line-height: 1.5;
            flex: 1;
            min-width: 0;
            word-break: break-word;
        }

        /* Scoped Clean Data Tables in Detail Tabs */
        .detail-doc-shell .detail-data-table {
            border: 1px solid #e2e8f0 !important;
            border-radius: 8px;
            margin-bottom: 0 !important;
            font-size: 0.82rem !important;
            line-height: 1.45;
        }
        .detail-doc-shell .detail-data-table thead th {
            background-color: #f8fafc !important;
            color: #475569 !important;
            font-size: 0.74rem !important;
            font-weight: 700 !important;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            padding: 10px 12px !important;
            border-color: #e2e8f0 !important;
            white-space: nowrap;
        }
        .detail-doc-shell .detail-data-table thead.detail-thead-dark th {
            background-color: #1e293b !important;
            color: #f8fafc !important;
            border-color: #334155 !important;
        }
        .detail-doc-shell .detail-data-table tbody td {
            padding: 10px 12px !important;
            border-color: #f1f5f9 !important;
            font-size: 0.82rem !important;
            color: #1e293b;
            vertical-align: middle;
        }
        .detail-doc-shell .detail-data-table tbody tr:nth-of-type(even) {
            background-color: #fcfdff;
        }
        .detail-doc-shell .detail-data-table tbody tr:hover {
            background-color: #f4f6fb !important;
        }

        /* Right Panel File Cards & 2-Line Clamped Timeline */
        .detail-doc-shell .detail-file-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            padding: 10px 12px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            background-color: #f8fafc;
            transition: border-color 0.15s ease, background-color 0.15s ease;
        }
        .detail-doc-shell .detail-file-item:hover {
            border-color: #cbd5e1;
            background-color: #ffffff;
        }
        .detail-doc-shell .detail-file-icon {
            width: 34px;
            height: 34px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-size: 1rem;
        }
        .detail-doc-shell .detail-file-name {
            font-size: 0.82rem;
            font-weight: 600;
            color: #0f172a;
        }
        .detail-doc-shell .detail-file-meta {
            font-size: 0.74rem;
            color: #64748b;
        }
        .detail-doc-shell .detail-timeline {
            position: relative;
            padding-left: 20px;
            margin-left: 8px;
            border-left: 2px solid #e2e8f0;
        }
        .detail-doc-shell .detail-timeline-item {
            position: relative;
            padding-left: 6px;
            margin-bottom: 14px;
        }
        .detail-doc-shell .detail-timeline-item:last-child {
            margin-bottom: 0;
        }
        .detail-doc-shell .detail-timeline-dot {
            position: absolute;
            left: -31px;
            top: 2px;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            font-size: 0.68rem;
            box-shadow: 0 0 0 3px #ffffff;
        }
        .detail-doc-shell .detail-timeline-note {
            font-size: 0.81rem;
            font-weight: 600;
            color: #1e293b;
            line-height: 1.45;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .detail-doc-shell .detail-timeline-meta {
            font-size: 0.74rem;
            color: #64748b;
            margin-top: 2px;
        }

        /* Tab 2 PDF Preview Frame */
        .detail-doc-shell .detail-pdf-frame {
            width: 100%;
            height: 440px;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            background-color: #ffffff;
            overflow: hidden;
        }
        .detail-doc-shell .detail-pdf-frame iframe {
            width: 100%;
            height: 100%;
            border: 0;
        }
        @media (max-width: 767.98px) {
            .detail-doc-shell .detail-pdf-frame {
                height: 320px;
            }
            .detail-doc-shell .detail-doc-title {
                font-size: 1.08rem;
            }
        }
    </style>
    @stack('styles')
</head>

<body data-layout-mode="light" data-topbar="light" data-sidebar="light">

<!-- Begin page -->
<div id="layout-wrapper">

    <!-- ============================================================== -->
    <!-- TOP NAVIGATION BAR (Header Minia - Tema Terang)               -->
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
                <!-- Tautan Kembali ke Portal Publik Beranda -->
                <a href="{{ route('home') }}" class="btn btn-outline-primary btn-sm rounded-pill me-2 d-none d-sm-inline-flex align-items-center gap-1.5 px-2.5 py-1 font-size-12 shadow-2xs" style="border-color: #cbd5e1; color: #1e293b;" target="_blank" title="Buka Portal Beranda Publik di Tab Baru">
                    <i class="bi bi-globe text-primary"></i> <span>Portal Publik</span>
                </a>

                <!-- NOTIFICATION BELL DROPDOWN (SISTEM NOTIFIKASI IN-APP) -->
                @php
                    $appNotifs = \App\Models\AppNotification::orderBy('id', 'desc')->take(8)->get();
                    $unreadCount = \App\Models\AppNotification::where('is_read', false)->count();
                @endphp
                <div class="dropdown d-inline-block me-1">
                    <button type="button" class="btn header-item noti-icon position-relative waves-effect d-flex align-items-center justify-content-center p-2" id="page-header-notifications-dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="Pemberitahuan Sistem">
                        <i class="bx bx-bell font-size-22 text-secondary"></i>
                        @if ($unreadCount > 0)
                            <span class="badge bg-danger rounded-pill position-absolute" style="top: 12px; right: 4px; font-size: 0.65rem; padding: 0.2em 0.45em;">
                                {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                            </span>
                        @endif
                    </button>
                    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0 shadow-lg border" aria-labelledby="page-header-notifications-dropdown" style="min-width: 320px; max-width: 360px; border-radius: 8px;">
                        <div class="p-3 bg-light border-bottom d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center gap-2">
                                <h6 class="m-0 fw-bold font-size-14 text-dark">
                                    <i class="bi bi-bell-fill text-primary me-1"></i> Notifikasi Sistem
                                </h6>
                                @if ($unreadCount > 0)
                                    <span class="badge bg-primary rounded-pill font-size-11">{{ $unreadCount }} Baru</span>
                                @endif
                            </div>
                            @if ($unreadCount > 0)
                                <form method="POST" action="{{ route('simkerma.notifications.mark-all-read') }}" class="m-0">
                                    @csrf
                                    <button type="submit" class="btn btn-link p-0 text-primary text-decoration-none" style="font-size: 0.72rem;">
                                        Tandai Semua Dibaca
                                    </button>
                                </form>
                            @endif
                        </div>

                        <div style="max-height: 330px; overflow-y: auto;">
                            @forelse ($appNotifs as $notif)
                                <form method="POST" action="{{ route('simkerma.notifications.mark-read', $notif->id) }}" class="m-0">
                                    @csrf
                                    <button type="submit" class="w-100 text-start border-0 border-bottom p-2.5 d-flex align-items-start gap-2.5 text-decoration-none" style="background-color: {{ $notif->is_read ? '#ffffff' : '#f0f7ff' }}; cursor: pointer; transition: background 0.15s;">
                                        <div class="flex-shrink-0 mt-0.5">
                                            <div class="rounded-circle d-flex align-items-center justify-content-center text-white shadow-2xs" style="width: 32px; height: 32px; font-size: 0.85rem; background-color: {{ match($notif->type) { 'success' => '#16a34a', 'warning' => '#d97706', 'danger' => '#dc2626', default => '#2563eb' } }};">
                                                <i class="bi {{ $notif->icon ?: 'bi-bell' }}"></i>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1 overflow-hidden">
                                            <div class="d-flex justify-content-between align-items-center mb-0.5">
                                                <h6 class="mb-0 fw-bold text-dark text-truncate" style="font-size: 0.78rem;">{{ $notif->title }}</h6>
                                                @if (!$notif->is_read)
                                                    <span class="badge bg-primary p-1 rounded-circle" style="width: 6px; height: 6px;" title="Belum dibaca"></span>
                                                @endif
                                            </div>
                                            <div class="text-secondary small mb-1" style="font-size: 0.72rem; line-height: 1.35; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                                {{ $notif->message }}
                                            </div>
                                            <div class="text-muted d-flex align-items-center gap-1" style="font-size: 0.66rem;">
                                                <i class="bi bi-clock"></i> {{ $notif->created_at ? $notif->created_at->diffForHumans() : 'Baru saja' }}
                                                @if ($notif->doc_id)
                                                    &bull; <span class="fw-semibold text-primary">{{ $notif->doc_id }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </button>
                                </form>
                            @empty
                                <div class="text-center py-4 text-muted">
                                    <i class="bi bi-bell-slash text-secondary fs-3 d-block mb-1"></i>
                                    <div class="small">Belum ada pemberitahuan baru</div>
                                </div>
                            @endforelse
                        </div>

                        <div class="p-2 border-top text-center bg-light">
                            <a href="{{ route('simkerma.index', ['menu' => 4]) }}" class="small text-primary fw-semibold text-decoration-none" style="font-size: 0.74rem;">
                                Buka Menu Pengajuan Kerja Sama &rarr;
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Reset Data Pengajuan (Mode Pengembangan) -->
                <form method="POST" action="{{ route('simkerma.reset-all-documents') }}" class="d-none d-sm-inline me-2" onsubmit="return confirm('PERINGATAN PENGEMBANGAN:\n\nApakah Anda yakin ingin menghapus SEMUA data pengajuan dan persetujuan kerja sama?\n\nSemua dokumen, jejak audit (audit trails), dan log siklus FSM akan dihapus permanen.')">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-danger shadow-sm d-flex align-items-center gap-1 py-1 px-2" style="font-size: 0.75rem;" title="Hapus Semua Pengajuan dan Persetujuan (Mode Pengembangan)">
                        <i class="bx bx-trash align-middle"></i>
                        <span class="d-none d-md-inline">Reset Data Dev</span>
                    </button>
                </form>

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

            <!-- Sidebar Bottom Attribution (Tema Terang) -->
            <div class="sidebar-bottom-profile mt-auto">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <div class="avatar-xs flex-shrink-0">
                        <span class="avatar-title rounded-circle bg-primary text-white font-size-12">
                            <i class="bx bx-user"></i>
                        </span>
                    </div>
                    <div class="overflow-hidden">
                        <div class="text-dark text-truncate font-size-12 fw-semibold">
                            {{ auth()->check() ? auth()->user()->name : 'Tamu (Guest)' }}
                        </div>
                        <span class="badge bg-soft-primary text-primary font-size-10 py-0">
                            {{ auth()->check() ? auth()->user()->role : 'Public' }}
                        </span>
                    </div>
                </div>
                <div class="font-size-11 text-secondary">
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

                            <div class="page-title-right d-flex align-items-center gap-2">
                                <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2 {{ in_array($activeMenu ?? 0, [1, 2, 3], true) ? '' : 'd-none' }}" id="btnToggleSpecAlert" onclick="toggleSpecAlert('{{ $activeMenu ?? '' }}')" title="Tampilkan Poin Spesifikasi">
                                    <i class="mdi mdi-information-outline me-1"></i> Spesifikasi
                                </button>
                                <ol class="breadcrumb m-0">
                                    <li class="breadcrumb-item"><a href="{{ route('simkerma.index') }}">SIM-KERMA</a></li>
                                    <li class="breadcrumb-item active">{{ $current['title'] }}</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- end page title -->

                <!-- Kotak Informasi Spesifikasi Dokumen Resmi -->
                <div class="alert alert-secondary alert-dismissible fade {{ in_array($activeMenu ?? 0, [1, 2, 3], true) ? 'd-none' : 'show' }} border-0 bg-soft-secondary mb-4" id="specDocAlert" role="alert">
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
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close" onclick="dismissSpecAlert('{{ $activeMenu ?? '' }}')"></button>
                </div>

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
                        <script>document.write(new Date().getFullYear())</script> © Universitas Parna Raya Manado (UNIPAR).
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
                            <span class="text-muted fw-bold d-block" style="font-size: 0.72rem;">NOMOR NASKAH PIHAK 1:</span>
                            <div class="fw-bold font-monospace text-dark" id="qrModalDocNum" style="font-size: 0.85rem;">-</div>
                        </div>
                        <div class="mb-2" id="qrModalPartnerDocContainer">
                            <span class="text-muted fw-bold d-block" style="font-size: 0.72rem;">NOMOR NASKAH PIHAK 2:</span>
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
    document.getElementById('qrModalDocNum').innerText = (docNum && docNum.trim() !== '') ? docNum : '-';
    document.getElementById('qrModalTitle').innerText = title || '-';
    document.getElementById('qrModalPartner').innerText = partner || '-';
    document.getElementById('qrModalDate').innerText = date || '30 Oktober 2026';
    
    // Partner Document Number (Pihak 2)
    const partnerContainer = document.getElementById('qrModalPartnerDocContainer');
    const partnerEl = document.getElementById('qrModalPartnerDocNum');
    if (partnerEl) {
        partnerEl.innerText = (partnerDocNum && partnerDocNum.trim() !== '') ? partnerDocNum : '-';
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

// Pengelolaan Banner Poin Spesifikasi Dokumen Resmi (Penyimpanan Status di localStorage)
function dismissSpecAlert(curMenu) {
    if (!curMenu) return;
    try {
        localStorage.setItem('hide_spec_alert_' + curMenu, 'true');
        const toggleBtn = document.getElementById('btnToggleSpecAlert');
        if (toggleBtn) toggleBtn.classList.remove('d-none');
    } catch(e) {}
}

function toggleSpecAlert(curMenu) {
    const alertEl = document.getElementById('specDocAlert');
    const toggleBtn = document.getElementById('btnToggleSpecAlert');
    if (!alertEl) return;
    
    if (alertEl.classList.contains('d-none')) {
        alertEl.classList.remove('d-none');
        alertEl.classList.add('show');
        if (curMenu) {
            try {
                if (['1', '2', '3'].includes(curMenu)) localStorage.setItem('hide_spec_alert_' + curMenu, 'false');
                else localStorage.removeItem('hide_spec_alert_' + curMenu);
            } catch(e) {}
        }
        if (toggleBtn) toggleBtn.classList.add('d-none');
    } else {
        alertEl.classList.add('d-none');
        if (curMenu) {
            try { localStorage.setItem('hide_spec_alert_' + curMenu, 'true'); } catch(e) {}
        }
        if (toggleBtn) toggleBtn.classList.remove('d-none');
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const curMenu = '{{ $activeMenu ?? "" }}';
    const alertEl = document.getElementById('specDocAlert');
    const toggleBtn = document.getElementById('btnToggleSpecAlert');
    if (curMenu && alertEl && toggleBtn) {
        try {
            if (['1', '2', '3'].includes(curMenu)) {
                if (localStorage.getItem('hide_spec_alert_' + curMenu) === 'false') {
                    alertEl.classList.remove('d-none');
                    alertEl.classList.add('show');
                    toggleBtn.classList.add('d-none');
                }
            } else if (localStorage.getItem('hide_spec_alert_' + curMenu) === 'true') {
                alertEl.classList.add('d-none');
                toggleBtn.classList.remove('d-none');
            }
        } catch(e) {}
    }
});

// Kunci Sistem Hanya Menerapkan Tema Terang (Light Mode Only)
(function() {
    function enforceLightTheme() {
        document.body.setAttribute('data-layout-mode', 'light');
        document.body.setAttribute('data-topbar', 'light');
        document.body.setAttribute('data-sidebar', 'light');
        try {
            sessionStorage.removeItem('layout-mode');
            localStorage.removeItem('layout-mode');
            sessionStorage.setItem('is_visited', 'layout-ltr');
        } catch(e) {}
    }
    enforceLightTheme();
    window.addEventListener('DOMContentLoaded', enforceLightTheme);
})();
</script>

@stack('scripts')
</body>
</html>
