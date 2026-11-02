<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'SIMKERMA UNIPAR - Sistem Informasi Dokumen Kerja Sama' }}</title>
    
    <!-- Google Fonts: Public Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,400&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5.3.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons 1.11.3 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    
    <style>
        :root {
            /* Palette Warna Eksak Sesuai UI_Reference */
            --brand-primary: #ea580c;        /* Oranye Hangat Utama */
            --brand-primary-hover: #c2410c;  /* Oranye Saat Hover */
            --brand-primary-light: #fff7ed;  /* Oranye Muda Latar Belakang */
            --brand-primary-subtle: #ffedd5; /* Oranye Lembut Batas */
            --brand-navy: #1e2430;           /* Slate Gelap Elegan untuk Sidebar */
            --brand-navy-dark: #161b24;
            --brand-slate: #334155;
            --bg-surface: #f8fafc;           /* Latar Utama Abu-abu Bersih */
            --border-color: #e2e8f0;         /* Garis Batas Kontainer Halus */
            --border-subtle: #f1f5f9;
            --card-radius: 12px;
            --card-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05), 0 1px 2px -1px rgba(0, 0, 0, 0.05);
        }

        /* Tipografi & Latar Utama: Public Sans */
        body {
            font-family: 'Public Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background-color: var(--bg-surface);
            font-size: 0.88rem;
            color: #334155;
            letter-spacing: -0.01em;
        }
        
        /* Layout Sidebar (Sesuai UI_Reference: #1e2430, Flat Dark Slate Elegan - Ramping 238px / 15% lebih hemat ruang) */
        .sidebar-container {
            width: 238px;
            min-height: 100vh;
            background-color: var(--brand-navy) !important;
            background-image: none !important;
            border-right: 1px solid #161b24;
        }
        @media (max-width: 991.98px) {
            .sidebar-container {
                width: 100%;
                min-height: auto;
            }
        }
        
        /* Navigasi Sidebar */
        .sidebar-nav .nav-link {
            color: #94a3b8;
            padding: 9px 12px;
            border-radius: 8px;
            margin-bottom: 3px;
            font-size: 0.83rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            transition: all 0.15s ease-in-out;
            text-decoration: none;
        }
        .sidebar-nav .nav-link:hover {
            color: #ffffff;
            background-color: rgba(255, 255, 255, 0.06);
        }
        .sidebar-nav .nav-link.active {
            color: #ffffff !important;
            background-color: var(--brand-primary) !important;
            font-weight: 600;
            box-shadow: 0 2px 8px rgba(234, 88, 12, 0.35);
        }
        
        /* Brand Icon Box (Oranye Sesuai UI_Reference) */
        .brand-icon-box {
            background-color: var(--brand-primary) !important;
            background-image: none !important;
            color: #ffffff;
            border-radius: 8px !important;
            box-shadow: 0 2px 6px rgba(234, 88, 12, 0.35);
        }
        
        /* Komponen Card & Container (Sesuai UI_Reference: Radius 12px, border #e2e8f0, soft shadow) */
        .card, .card-wireframe {
            border: 1px solid var(--border-color) !important;
            border-radius: var(--card-radius) !important;
            box-shadow: var(--card-shadow) !important;
            background-color: #ffffff !important;
        }
        .card-header {
            background-color: #ffffff !important;
            border-bottom: 1px solid var(--border-subtle) !important;
            border-top-left-radius: var(--card-radius) !important;
            border-top-right-radius: var(--card-radius) !important;
            padding: 14px 18px !important;
        }
        .card-body {
            padding: 20px !important;
        }

        /* Komponen Tabel (Sesuai UI_Reference: Header #f8fafc, teks #475569, padding lega, border halus) */
        .table-responsive {
            border-radius: var(--card-radius);
            border: 1px solid var(--border-color);
            background-color: #ffffff;
            box-shadow: var(--card-shadow);
        }
        .table, .table-wireframe {
            margin-bottom: 0 !important;
        }
        .table thead th, .table-wireframe thead th, .table-wireframe th {
            background-color: #f8fafc !important;
            color: #475569 !important;
            font-size: 0.77rem !important;
            font-weight: 600 !important;
            text-transform: uppercase !important;
            letter-spacing: 0.03em !important;
            padding: 12px 16px !important;
            border-bottom: 1px solid var(--border-color) !important;
            border-top: none !important;
            vertical-align: middle;
        }
        .table tbody td, .table-wireframe tbody td {
            padding: 13px 16px !important;
            border-bottom: 1px solid var(--border-subtle) !important;
            vertical-align: middle;
            color: #1e293b;
        }
        .table tbody tr:last-child td, .table-wireframe tbody tr:last-child td {
            border-bottom: none !important;
        }
        .table tbody tr:hover, .table-wireframe tbody tr:hover {
            background-color: #fafbfc !important;
        }

        /* Tombol & Aksi (Warna Oranye UI_Reference) */
        .btn {
            border-radius: 8px !important;
            font-weight: 500;
            transition: all 0.15s ease-in-out;
        }
        .btn-primary {
            background-color: var(--brand-primary) !important;
            border-color: var(--brand-primary) !important;
            color: #ffffff !important;
            font-weight: 600;
            box-shadow: 0 1px 3px rgba(234, 88, 12, 0.25);
        }
        .btn-primary:hover, .btn-primary:focus {
            background-color: var(--brand-primary-hover) !important;
            border-color: var(--brand-primary-hover) !important;
            color: #ffffff !important;
            box-shadow: 0 2px 6px rgba(234, 88, 12, 0.35);
        }
        .btn-outline-primary {
            color: var(--brand-primary) !important;
            border-color: #fdba74 !important;
            background-color: #ffffff !important;
        }
        .btn-outline-primary:hover, .btn-outline-primary:focus {
            background-color: var(--brand-primary) !important;
            border-color: var(--brand-primary) !important;
            color: #ffffff !important;
        }
        .text-primary {
            color: var(--brand-primary) !important;
        }
        .bg-primary {
            background-color: var(--brand-primary) !important;
        }
        .border-primary {
            border-color: var(--brand-primary) !important;
        }

        /* Form Controls & Inputs */
        .form-control, .form-select {
            border: 1px solid #cbd5e1 !important;
            border-radius: 8px !important;
            font-size: 0.85rem !important;
            padding: 8px 12px !important;
            color: #1e293b;
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--brand-primary) !important;
            box-shadow: 0 0 0 3px rgba(234, 88, 12, 0.15) !important;
        }

        /* Status Badges & Pills (Pastel Halus + Rounded-Pill dari UI_Reference) */
        .badge {
            font-weight: 600;
            border-radius: 9999px !important;
            padding: 4px 10px;
            letter-spacing: 0.01em;
        }
        .badge.bg-success, .badge-status-active {
            background-color: #dcfce7 !important;
            color: #15803d !important;
            border: 1px solid #bbf7d0 !important;
        }
        .badge.bg-info, .badge-status-process {
            background-color: #e0f2fe !important;
            color: #0369a1 !important;
            border: 1px solid #bae6fd !important;
        }
        .badge.bg-warning, .badge.bg-warning-subtle, .badge-status-warning {
            background-color: #fef3c7 !important;
            color: #b45309 !important;
            border: 1px solid #fde68a !important;
        }
        .badge.bg-danger, .badge-status-expired {
            background-color: #fee2e2 !important;
            color: #dc2626 !important;
            border: 1px solid #fecaca !important;
        }
        .badge.bg-secondary {
            background-color: #f1f5f9 !important;
            color: #475569 !important;
            border: 1px solid #e2e8f0 !important;
        }
        .badge.bg-dark {
            background-color: #1e293b !important;
            color: #f8fafc !important;
            border-radius: 6px !important;
        }
        .badge.bg-light {
            background-color: #f8fafc !important;
            color: #334155 !important;
            border: 1px solid #e2e8f0 !important;
        }

        /* Pastel Circular Icons untuk Stat Cards (Sesuai UI_Reference) */
        .bg-success-subtle { background-color: #dcfce7 !important; color: #16a34a !important; }
        .bg-primary-subtle { background-color: #e0f2fe !important; color: #0284c7 !important; }
        .bg-warning-subtle { background-color: #fef3c7 !important; color: #d97706 !important; }
        .bg-info-subtle    { background-color: #eff6ff !important; color: #2563eb !important; }
        .bg-danger-subtle  { background-color: #fee2e2 !important; color: #dc2626 !important; }
        .bg-dark-subtle    { background-color: #f1f5f9 !important; color: #334155 !important; }

        .tag-wireframe {
            font-size: 0.72rem;
            font-weight: 600;
            padding: 3px 8px;
            background-color: #f1f5f9;
            color: #475569;
            border-radius: 6px;
            border: 1px solid #cbd5e1;
        }
        
        /* Upload Box */
        .upload-dashed-box {
            border: 2px dashed #cbd5e1;
            border-radius: 8px;
            background-color: #f8fafc;
            padding: 18px;
            text-align: center;
            color: #64748b;
            cursor: pointer;
            transition: all 0.15s ease;
        }
        .upload-dashed-box:hover {
            border-color: var(--brand-primary);
            background-color: var(--brand-primary-light);
            color: var(--brand-primary);
        }
        
        /* Tabs Styling */
        .nav-pills .nav-link.active {
            background-color: var(--brand-primary) !important;
            color: #ffffff !important;
            box-shadow: 0 1px 3px rgba(234, 88, 12, 0.3);
            border-radius: 8px;
        }
        .nav-pills .nav-link {
            color: #64748b;
            border-radius: 8px;
        }

        /* Pagination Sesuai UI_Reference */
        .pagination .page-item .page-link {
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            margin: 0 2px;
            color: #334155;
            font-size: 0.85rem;
            padding: 6px 12px;
        }
        .pagination .page-item.active .page-link {
            background-color: var(--brand-primary) !important;
            border-color: var(--brand-primary) !important;
            color: #ffffff !important;
            font-weight: 600;
        }

        /* Clean Print Style untuk Laporan Resmi */
        @media print {
            .sidebar-container, .top-header, .btn, .breadcrumb, footer, .modal, .alert {
                display: none !important;
            }
            body, .main-content {
                background: #fff !important;
                margin: 0 !important;
                padding: 0 !important;
            }
            .card {
                border: 1px solid #ddd !important;
                box-shadow: none !important;
            }
        }
    </style>
    @stack('styles')
</head>
<body>

<div class="d-flex flex-column flex-lg-row">

    <!-- ========================================================================================= -->
    <!-- SIDEBAR NAVIGASI RESMI                                                                    -->
    <!-- ========================================================================================= -->
    <div class="sidebar-container text-white d-flex flex-column justify-content-between p-2 p-md-3 flex-shrink-0">
        <div>
            <!-- LOGO & BRAND -->
            <div class="d-flex align-items-center pb-3 mb-3 border-bottom border-secondary">
                <div class="brand-icon-box fw-bold rounded-3 d-flex align-items-center justify-content-center me-2" style="width: 36px; height: 36px; font-size: 1.1rem;">
                    <i class="bi bi-mortarboard-fill"></i>
                </div>
                <div>
                    <h6 class="mb-0 fw-bold text-uppercase text-white letter-spacing-1" style="font-size: 0.88rem;">SIM KERJA SAMA</h6>
                    <small class="text-secondary" style="font-size: 0.7rem;">Universitas Parna Raya</small>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-2 px-1">
                <span class="text-uppercase text-secondary fw-bold" style="font-size: 0.67rem; letter-spacing: 0.8px;">
                    MENU UTAMA
                </span>
                <span class="tag-wireframe bg-dark text-white-50 border-secondary" style="font-size: 0.62rem;">
                    UNIPAR
                </span>
            </div>

            <!-- DAFTAR MENU UTAMA -->
            <nav class="sidebar-nav">
                @foreach ($features as $fId => $f)
                    @php $isActive = ($activeMenu === $fId); @endphp
                    <a href="{{ route('simkerma.index', ['menu' => $fId]) }}" class="nav-link {{ $isActive ? 'active' : '' }}" title="{{ $f['title'] }}">
                        <i class="bi {{ $f['icon'] }} me-2 fs-6"></i>
                        <span class="flex-grow-1 text-truncate">{{ $f['title'] }}</span>
                        @if ($isActive)
                            <i class="bi bi-chevron-right ms-1 text-white small" style="font-size: 0.68rem;"></i>
                        @endif
                    </a>
                @endforeach
            </nav>
        </div>

        <!-- FOOTER SIDEBAR -->
        <div class="pt-3 mt-4 border-top border-secondary text-secondary" style="font-size: 0.75rem;">
            @if (auth()->check())
                <div class="bg-dark p-2 rounded mb-2 border border-secondary">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-2 overflow-hidden">
                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 28px; height: 28px; font-size: 0.75rem;">
                                <i class="bi bi-person-fill"></i>
                            </div>
                            <div class="overflow-hidden">
                                <div class="text-white text-truncate fw-semibold" style="font-size: 0.73rem;">{{ auth()->user()->name }}</div>
                                <span class="badge bg-danger text-white px-2 py-0" style="font-size: 0.62rem;">{{ auth()->user()->role }}</span>
                            </div>
                        </div>
                        <a href="{{ route('logout') }}" class="text-white-50 hover-text-white ms-1" title="Logout">
                            <i class="bi bi-box-arrow-right"></i>
                        </a>
                    </div>
                </div>
            @else
                <div class="mb-2">
                    <a href="{{ route('login') }}" class="btn btn-sm btn-outline-light w-100 py-1" style="font-size: 0.73rem;">
                        <i class="bi bi-box-arrow-in-right me-1"></i> Login Pengguna
                    </a>
                </div>
            @endif
            <div class="text-white-50 fw-semibold">Jordan Immanuel Pusung</div>
            <div class="small">NIM: 2355201011 &bull; TI UNIPAR</div>
            <div class="text-white-50 small mt-1">Laravel 11 &bull; MySQL Relasional</div>
        </div>
    </div>

    <!-- ========================================================================================= -->
    <!-- AREA KONTEN UTAMA (RESPONSIF TERHADAP MENU AKTIF)                                         -->
    <!-- ========================================================================================= -->
    <div class="flex-grow-1 d-flex flex-column min-vh-100">

        <!-- TOP BAR HEADER -->
        <header class="bg-white border-bottom py-3 px-4 shadow-sm">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
                <div>
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <span class="tag-wireframe fw-bold">{{ $current['tag'] }}</span>
                        <span class="text-muted small">Menu {{ $current['id'] }} dari 7 Fitur Utama</span>
                    </div>
                    <h4 class="mb-0 fw-bold text-dark text-uppercase">{{ $current['title'] }}</h4>
                    <p class="text-muted small mb-0">{{ $current['subtitle'] }}</p>
                </div>
                <div>
                    <div class="d-flex align-items-center gap-2">
                        <button type="button" class="btn btn-outline-dark btn-sm fw-semibold shadow-sm" onclick="showQrModal('REG-UNIPAR-2026-042', '341/UNIPAR/MoU/X/2026', 'PKS Program Magang Industri AI', 'PT Industri Inovasi Digital', 'APPROVED', '30 Oktober 2026')">
                            <i class="bi bi-qr-code-scan me-1 text-primary"></i> Cek QR Registrasi
                        </button>
                        @if (auth()->check())
                            <div class="dropdown">
                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle d-flex align-items-center gap-1 shadow-sm bg-white" type="button" data-bs-toggle="dropdown">
                                    <span class="badge bg-danger text-white rounded-pill px-2" style="font-size: 0.65rem;">{{ auth()->user()->role }}</span>
                                    <span class="fw-semibold small text-dark d-none d-md-inline">{{ auth()->user()->name }}</span>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 py-2" style="min-width: 220px;">
                                    <li class="px-3 py-1">
                                        <div class="fw-bold text-dark small">{{ auth()->user()->name }}</div>
                                        <div class="text-muted" style="font-size: 0.72rem;">{{ auth()->user()->email }}</div>
                                        <div class="badge bg-primary text-white mt-1" style="font-size: 0.65rem;">Unit: {{ auth()->user()->unit ?? '-' }}</div>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <a class="dropdown-item text-danger small d-flex align-items-center gap-2" href="{{ route('logout') }}">
                                            <i class="bi bi-box-arrow-right"></i> Keluar (Logout)
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        @else
                            <a href="{{ route('login') }}" class="btn btn-sm btn-primary shadow-sm">
                                <i class="bi bi-box-arrow-in-right me-1"></i> Login Akun
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </header>

        <!-- KONTEN FITUR SESUAI MENU -->
        <main class="p-4 flex-grow-1">

            <!-- KOTAK INFORMASI SPESIFIKASI DOKUMEN RESMI -->
            <div class="alert alert-secondary border d-flex align-items-start mb-4 py-2 px-3 bg-white shadow-sm" role="alert">
                <i class="bi bi-info-circle-fill text-dark me-2 mt-1 fs-6"></i>
                <div class="small text-dark">
                    <strong class="text-uppercase d-block mb-1">Poin Spesifikasi Sesuai Dokumen Resmi:</strong>
                    <ul class="mb-0 ps-3">
                        @foreach ($current['subfeatures'] as $sf)
                            <li>{{ $sf }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>

            @yield('content')

        </main>

        <!-- FOOTER INFORMASI DOKUMEN RESMI -->
        <footer class="bg-white border-top py-3 px-4 text-muted small d-flex flex-column flex-sm-row justify-content-between align-items-center gap-1">
            <div>
                <strong>SIM KERJA SAMA PT</strong> &mdash; Implementasi UI Bootstrap 5, Laravel 11 &amp; MySQL
            </div>
            <div>
                Program Studi Teknik Informatika, Universitas Parna Raya Manado
            </div>
        </footer>

    </div>

</div>

<!-- ========================================================================================= -->
<!-- MODAL INTERAKTIF: LEMBAR REGISTRASI & QR CODE RESMI DOKUMEN SIMKERMA UNIPAR               -->
<!-- ========================================================================================= -->
<div class="modal fade" id="qrModal" tabindex="-1" aria-labelledby="qrModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 580px;">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-dark text-white py-3">
                <div class="d-flex align-items-center gap-2">
                    <div class="bg-warning text-dark rounded p-1 d-flex align-items-center justify-content-center" style="width: 28px; height: 28px;">
                        <i class="bi bi-qr-code-scan fw-bold"></i>
                    </div>
                    <div>
                        <h6 class="modal-title fw-bold mb-0 text-uppercase" id="qrModalLabel">Lembar Registrasi &amp; QR Code Resmi</h6>
                        <small class="text-white-50">Sistem Informasi Dokumen Kerja Sama UNIPAR</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                
                <!-- Status Bar Dokumen -->
                <div class="d-flex justify-content-between align-items-center mb-3 p-2 bg-light rounded border">
                    <div>
                        <span class="text-muted small fw-bold">STATUS SIKLUS:</span>
                        <span id="qrModalStatusBadge" class="badge bg-primary ms-1">APPROVED (Terkunci Bebas Bypass)</span>
                    </div>
                    <span id="qrModalRegBadge" class="badge bg-dark">REG-UNIPAR-2026-042</span>
                </div>

                <!-- Kontainer QR Code & Detail -->
                <div class="row align-items-center g-3 mb-3">
                    <div class="col-sm-5 text-center">
                        <div class="p-2 border rounded bg-white d-inline-block shadow-sm">
                            <div id="qrcodeCanvas" style="width: 160px; height: 160px; display: flex; align-items: center; justify-content: center; margin: 0 auto;"></div>
                        </div>
                        <div class="text-muted mt-2 small" style="font-size: 0.72rem;">
                            <i class="bi bi-phone me-1 text-primary"></i> Pindai dengan Kamera HP
                        </div>
                    </div>
                    <div class="col-sm-7 small">
                        <div class="mb-2">
                            <span class="text-muted fw-bold d-block" style="font-size: 0.72rem;">NOMOR SURAT PIHAK I (UNIPAR):</span>
                            <strong id="qrModalDocNum" class="text-dark fs-6">341/UNIPAR/MoU/X/2026</strong>
                        </div>
                        <div class="mb-2" id="qrModalPartnerDocContainer" style="display: none;">
                            <span class="text-muted fw-bold d-block" style="font-size: 0.72rem;">NOMOR SURAT PIHAK II (MITRA):</span>
                            <strong id="qrModalPartnerDocNum" class="text-primary small"></strong>
                        </div>
                        <div class="mb-2">
                            <span class="text-muted fw-bold d-block" style="font-size: 0.72rem;">JUDUL KERJA SAMA:</span>
                            <span id="qrModalTitle" class="fw-semibold text-primary">PKS Program Magang Industri AI</span>
                        </div>
                        <div class="mb-2">
                            <span class="text-muted fw-bold d-block" style="font-size: 0.72rem;">INSTANSI MITRA:</span>
                            <span id="qrModalPartner" class="text-dark">PT Industri Inovasi Digital</span>
                        </div>
                        <div>
                            <span id="qrModalDateLabel" class="text-muted fw-bold d-block" style="font-size: 0.72rem;">TANGGAL PERSETUJUAN / PENGESAHAN:</span>
                            <span id="qrModalDate" class="text-muted">30 Oktober 2026</span>
                        </div>
                    </div>
                </div>

                <!-- Kotak Petunjuk Keamanan & Prosedur Fisik FSM Sinkron -->
                <div id="qrModalAlertBox" class="alert alert-primary py-2 px-3 small border-0 bg-primary-subtle text-dark d-flex align-items-start gap-2 mb-0">
                    <i id="qrModalAlertIcon" class="bi bi-lock-fill text-primary mt-1"></i>
                    <div id="qrModalAlertText" style="font-size: 0.78rem;">
                        <strong>Mekanisme Fisik &amp; Digital:</strong> Draf naskah berstatus <em>APPROVED</em> telah terkunci (<em>Freeze Text</em>). QR Code ini disematkan otomatis pada margin bawah naskah PDF resmi yang dicetak untuk ditandatangani basah dan ditempeli meterai di hadapan mitra.
                    </div>
                </div>

            </div>
            <div class="modal-footer bg-light py-2 px-3 d-flex flex-column flex-sm-row justify-content-between align-items-center gap-2">
                <a id="qrModalVerifyLink" href="/verify/REG-UNIPAR-2026-042" target="_blank" class="btn btn-outline-dark btn-sm fw-semibold w-100 w-sm-auto">
                    <i class="bi bi-box-arrow-up-right me-1"></i> Buka Verifikasi Publik
                </a>
                <div class="d-flex gap-2 w-100 w-sm-auto justify-content-end">
                    <button type="button" class="btn btn-primary btn-sm fw-bold px-3" onclick="openPrintableVerification()">
                        <i class="bi bi-printer me-1"></i> Buka / Cetak Lembar Registrasi Resmi
                    </button>
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
</div>

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
            alertBoxClass: 'alert alert-secondary py-2 px-3 small border-0 bg-secondary-subtle text-dark d-flex align-items-start gap-2 mb-0',
            alertIconClass: 'bi bi-pencil-square text-secondary mt-1',
            alertText: '<strong>Tahap Penyusunan Draf:</strong> Naskah usulan kerja sama sedang dirancang oleh unit pengusul di UNIPAR dan belum diajukan untuk pemeriksaan.',
            dateLabel: 'TANGGAL INISIASI DRAF:'
        },
        'REVIEW': {
            badgeClass: 'badge bg-info text-dark ms-1',
            badgeText: 'REVIEW (Pemeriksaan & Telaah)',
            alertBoxClass: 'alert alert-info py-2 px-3 small border-0 bg-info-subtle text-dark d-flex align-items-start gap-2 mb-0',
            alertIconClass: 'bi bi-hourglass-split text-info mt-1',
            alertText: '<strong>Tahap Pemeriksaan Naskah:</strong> Draf naskah kerja sama sedang ditelaah oleh pimpinan / staf kerja sama UNIPAR. Jika disetujui dapat disahkan menjadi AKTIF, atau dikembalikan ke DRAFT jika perlu perbaikan.',
            dateLabel: 'TANGGAL PENGAJUAN TELAAH:'
        },
        'ACTIVE': {
            badgeClass: 'badge bg-success text-white ms-1',
            badgeText: 'ACTIVE (Sah & Bermeterai)',
            alertBoxClass: 'alert alert-success py-2 px-3 small border-0 bg-success-subtle text-dark d-flex align-items-start gap-2 mb-0',
            alertIconClass: 'bi bi-check-circle-fill text-success mt-1',
            alertText: '<strong>Dokumen Sah Berlaku:</strong> Eksemplar fisik asli bertanda tangan basah dan bermeterai sah telah diverifikasi keabsahannya serta tersimpan resmi di SIMKERMA UNIPAR.',
            dateLabel: 'TANGGAL PENGESAHAN / AKTIF:'
        },
        'WARNING': {
            badgeClass: 'badge bg-warning text-dark ms-1',
            badgeText: 'WARNING (Peringatan H-90)',
            alertBoxClass: 'alert alert-warning py-2 px-3 small border-0 bg-warning-subtle text-dark d-flex align-items-start gap-2 mb-0',
            alertIconClass: 'bi bi-exclamation-triangle-fill text-warning mt-1',
            alertText: '<strong>Peringatan Ambang Batas Kedaluwarsa:</strong> Kerja sama masih aktif berlaku, namun sisa masa berlaku telah memasuki ambang batas peringatan dini (H-90/H-60). Diimbau segera menindaklanjuti perpanjangan.',
            dateLabel: 'TANGGAL PERINGATAN DINI:'
        },
        'EXPIRED': {
            badgeClass: 'badge bg-danger text-white ms-1',
            badgeText: 'EXPIRED (Kedaluwarsa)',
            alertBoxClass: 'alert alert-danger py-2 px-3 small border-0 bg-danger-subtle text-dark d-flex align-items-start gap-2 mb-0',
            alertIconClass: 'bi bi-x-octagon-fill text-danger mt-1',
            alertText: '<strong>Masa Berlaku Telah Berakhir:</strong> Masa perjanjian kerja sama ini telah habis secara formal. Hubungan kemitraan dapat dipulihkan melalui perpanjangan/reaktivasi naskah.',
            dateLabel: 'TANGGAL KEDALUWARSA / BERAKHIR:'
        },
        'CANCELLED': {
            badgeClass: 'badge bg-dark text-white ms-1',
            badgeText: 'CANCELLED (Dibatalkan)',
            alertBoxClass: 'alert alert-dark py-2 px-3 small border-0 bg-dark-subtle text-dark d-flex align-items-start gap-2 mb-0',
            alertIconClass: 'bi bi-slash-circle-fill text-dark mt-1',
            alertText: '<strong>Usulan Kemitraan Dibatalkan:</strong> Pengusulan dokumen kerja sama ini telah dihentikan secara permanen oleh pengusul.',
            dateLabel: 'TANGGAL PEMBATALAN:'
        }
    };

    const cfg = fsmStatusConfig[status] || {
        badgeClass: 'badge bg-secondary text-white ms-1',
        badgeText: (status || 'UNKNOWN') + ' (Status FSM)',
        alertBoxClass: 'alert alert-secondary py-2 px-3 small border-0 bg-secondary-subtle text-dark d-flex align-items-start gap-2 mb-0',
        alertIconClass: 'bi bi-info-circle-fill text-secondary mt-1',
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
            colorDark: "#0f172a",
            colorLight: "#ffffff",
            correctLevel: QRCode.CorrectLevel.M
        });
    } catch (e) {
        container.innerHTML = '<div class="alert alert-secondary p-2 small mb-0"><i class="bi bi-qr-code fs-1 d-block text-dark"></i>' + regId + '</div>';
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

<!-- Bootstrap 5.3.3 JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
