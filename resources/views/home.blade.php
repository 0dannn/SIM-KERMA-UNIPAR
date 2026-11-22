<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal Kemitraan & Kerja Sama - Universitas Kristen Papua (UNIPAR)</title>
    
    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ asset('partial/images/favicon.ico') }}">

    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Bootstrap CSS -->
    <link href="{{ asset('partial/css/bootstrap.min.css') }}" rel="stylesheet">
    <!-- Icons CSS (Boxicons & Bootstrap Icons) -->
    <link href="{{ asset('partial/css/icons.min.css') }}" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <!-- ApexCharts CSS -->
    <link href="{{ asset('partial/libs/apexcharts/apexcharts.css') }}" rel="stylesheet">

    <style>
        :root {
            --uni-navy: #0b1f3a;
            --uni-navy-light: #162f52;
            --uni-blue: #1d4ed8;
            --uni-blue-subtle: #eff6ff;
            --uni-slate: #0f172a;
            --uni-muted: #64748b;
            --uni-border: #e2e8f0;
            --uni-bg: #f8fafc;
            --uni-surface: #ffffff;
        }

        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background-color: var(--uni-bg);
            color: #334155;
            line-height: 1.6;
            scroll-behavior: smooth;
        }

        /* Top Institutional Bar */
        .top-inst-bar {
            background-color: var(--uni-navy);
            color: #94a3b8;
            font-size: 0.8rem;
            padding: 8px 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        }

        /* Main Navbar */
        .uni-navbar {
            background-color: rgba(255, 255, 255, 0.96);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid var(--uni-border);
            padding: 14px 0;
            position: sticky;
            top: 0;
            z-index: 1030;
        }

        .brand-logo-box {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
        }
        .brand-emblem {
            width: 44px;
            height: 44px;
            background: linear-gradient(135deg, var(--uni-navy) 0%, var(--uni-blue) 100%);
            color: white;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.35rem;
            font-weight: 800;
            box-shadow: 0 4px 10px rgba(11, 31, 58, 0.2);
        }
        .brand-text h1 {
            font-size: 1.05rem;
            font-weight: 800;
            letter-spacing: -0.02em;
            color: var(--uni-navy);
            margin: 0;
            line-height: 1.2;
        }
        .brand-text p {
            font-size: 0.72rem;
            color: var(--uni-muted);
            margin: 0;
            font-weight: 600;
            letter-spacing: 0.05em;
            text-transform: uppercase;
        }

        .nav-link-custom {
            color: #475569;
            font-weight: 600;
            font-size: 0.88rem;
            padding: 8px 14px;
            border-radius: 6px;
            transition: all 0.15s ease;
            text-decoration: none;
        }
        .nav-link-custom:hover {
            color: var(--uni-blue);
            background-color: var(--uni-blue-subtle);
        }

        /* Hero Section */
        .hero-section {
            background: radial-gradient(circle at top right, rgba(29, 78, 216, 0.05), transparent 45%),
                        radial-gradient(circle at bottom left, rgba(11, 31, 58, 0.04), transparent 50%),
                        #ffffff;
            border-bottom: 1px solid var(--uni-border);
            padding: 56px 0 44px;
        }
        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background-color: var(--uni-blue-subtle);
            color: var(--uni-blue);
            border: 1px solid #bfdbfe;
            padding: 5px 14px;
            border-radius: 50px;
            font-size: 0.8rem;
            font-weight: 700;
            margin-bottom: 16px;
        }
        .hero-title {
            font-size: 2.35rem;
            font-weight: 800;
            line-height: 1.25;
            color: var(--uni-navy);
            letter-spacing: -0.025em;
            margin-bottom: 14px;
        }
        .hero-subtitle {
            font-size: 1.05rem;
            color: #475569;
            line-height: 1.7;
            max-width: 720px;
            margin-bottom: 26px;
        }

        /* Top 4 KPI Metric Cards */
        .kpi-stat-card {
            background: #ffffff;
            border: 1px solid var(--uni-border);
            border-radius: 12px;
            padding: 22px;
            transition: all 0.2s ease;
            height: 100%;
            position: relative;
            overflow: hidden;
        }
        .kpi-stat-card:hover {
            border-color: #cbd5e1;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
            transform: translateY(-2px);
        }
        .kpi-top-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 12px;
        }
        .kpi-icon-wrap {
            width: 44px;
            height: 44px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.35rem;
        }
        .kpi-value {
            font-size: 2.15rem;
            font-weight: 800;
            line-height: 1;
            color: var(--uni-navy);
            letter-spacing: -0.03em;
            margin-bottom: 6px;
        }
        .kpi-label {
            font-size: 0.85rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 4px;
        }
        .kpi-subtext {
            font-size: 0.74rem;
            color: var(--uni-muted);
        }

        /* Section Titles */
        .section-header {
            margin-bottom: 32px;
        }
        .section-badge {
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--uni-blue);
            background: var(--uni-blue-subtle);
            padding: 4px 12px;
            border-radius: 20px;
            display: inline-block;
            margin-bottom: 8px;
        }
        .section-title {
            font-size: 1.85rem;
            font-weight: 800;
            color: var(--uni-navy);
            letter-spacing: -0.02em;
            margin-bottom: 6px;
        }
        .section-desc {
            font-size: 0.95rem;
            color: #64748b;
            max-width: 640px;
        }

        /* Chart Cards */
        .chart-box {
            background: #ffffff;
            border: 1px solid var(--uni-border);
            border-radius: 12px;
            padding: 24px;
            height: 100%;
            box-shadow: 0 2px 10px -2px rgba(0, 0, 0, 0.02);
        }
        .chart-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 18px;
            padding-bottom: 12px;
            border-bottom: 1px solid #f1f5f9;
        }
        .chart-title {
            font-size: 0.98rem;
            font-weight: 700;
            color: var(--uni-navy);
            margin: 0;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* Summary Bento Card */
        .summary-metric-card {
            background: #ffffff;
            border: 1px solid var(--uni-border);
            border-radius: 12px;
            padding: 22px;
            height: 100%;
        }

        /* Table styling */
        .uni-table {
            border-collapse: separate;
            border-spacing: 0;
            width: 100%;
        }
        .uni-table thead th {
            background-color: #f8fafc;
            color: #475569;
            font-size: 0.76rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            padding: 12px 16px;
            border-bottom: 1px solid var(--uni-border);
            border-top: 1px solid var(--uni-border);
        }
        .uni-table tbody td {
            padding: 14px 16px;
            font-size: 0.88rem;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
            background-color: #ffffff;
        }
        .uni-table tbody tr:hover td {
            background-color: #fbfcfe;
        }

        /* Partner Pill */
        .partner-pill {
            background: #ffffff;
            border: 1px solid var(--uni-border);
            border-radius: 10px;
            padding: 16px;
            display: flex;
            align-items: center;
            gap: 14px;
            transition: all 0.2s ease;
            height: 100%;
        }
        .partner-pill:hover {
            border-color: var(--uni-blue);
            box-shadow: 0 4px 12px rgba(29, 78, 216, 0.08);
        }
        .partner-icon-box {
            width: 42px;
            height: 42px;
            border-radius: 8px;
            background: #f1f5f9;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            color: var(--uni-navy);
            flex-shrink: 0;
        }

        /* Footer */
        .uni-footer {
            background-color: var(--uni-navy);
            color: #94a3b8;
            padding: 56px 0 28px;
            border-top: 1px solid rgba(255, 255, 255, 0.08);
            font-size: 0.88rem;
        }
        .footer-heading {
            color: #f8fafc;
            font-size: 0.95rem;
            font-weight: 700;
            margin-bottom: 16px;
        }
        .footer-link {
            color: #94a3b8;
            text-decoration: none;
            display: block;
            margin-bottom: 8px;
            transition: color 0.15s;
        }
        .footer-link:hover {
            color: #ffffff;
        }
        .footer-bottom {
            border-top: 1px solid rgba(255, 255, 255, 0.08);
            margin-top: 36px;
            padding-top: 22px;
            font-size: 0.8rem;
        }
    </style>
</head>
<body>

    <!-- TOP INSTITUTIONAL BAR -->
    <div class="top-inst-bar">
        <div class="container d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-3">
                <span><i class="bi bi-geo-alt-fill text-warning me-1"></i> Kampus Utama: Jl. Danau Ayamaru No. 1, Sorong, Papua Barat Daya</span>
                <span class="d-none d-md-inline text-secondary">|</span>
                <span class="d-none d-md-inline"><i class="bi bi-envelope-fill me-1"></i> kerjasama@unipar.ac.id</span>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-light text-dark fw-semibold" style="font-size: 0.72rem;">Portal Kemitraan & IKU 6</span>
                <span class="badge bg-primary text-white fw-semibold" style="font-size: 0.72rem;">Terakreditasi</span>
            </div>
        </div>
    </div>

    <!-- MAIN STICKY NAVBAR -->
    <nav class="uni-navbar">
        <div class="container d-flex align-items-center justify-content-between">
            <!-- Brand Logo -->
            <a href="{{ route('home') }}" class="brand-logo-box">
                <div class="brand-emblem">
                    <i class="bi bi-buildings"></i>
                </div>
                <div class="brand-text">
                    <h1>UNIVERSITAS KRISTEN PAPUA</h1>
                    <p>SIM-KERMA • Portal Informasi & Statistik Kerja Sama</p>
                </div>
            </a>

            <!-- Nav Links (Desktop) -->
            <div class="d-none d-lg-flex align-items-center gap-1">
                <a href="#beranda" class="nav-link-custom">Beranda</a>
                <a href="#grafik" class="nav-link-custom">Grafik Statistik</a>
                <a href="#ringkasan" class="nav-link-custom">Ringkasan Data</a>
                <a href="#dokumen" class="nav-link-custom">Direktori Naskah</a>
                <a href="#mitra" class="nav-link-custom">Mitra Kolaborasi</a>
            </div>

            <!-- Login / Access Action Button -->
            <div class="d-flex align-items-center gap-2">
                @if (auth()->check())
                    <!-- Authenticated User Menu -->
                    <div class="dropdown">
                        <button class="btn btn-primary btn-sm px-3 py-1.5 shadow-sm dropdown-toggle d-flex align-items-center gap-2 fw-semibold" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="font-size: 0.84rem;">
                            <i class="bi bi-person-check-fill"></i>
                            <span>{{ auth()->user()->name }}</span>
                            <span class="badge bg-white text-primary px-1.5 py-0.5" style="font-size: 0.68rem;">{{ auth()->user()->role }}</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border small py-1" style="min-width: 210px;">
                            <li class="px-3 py-1.5 text-muted border-bottom" style="font-size: 0.75rem;">
                                Peran: <strong>{{ auth()->user()->role }}</strong><br>
                                Unit: {{ auth()->user()->unit ?: 'Sivitas Kampus' }}
                            </li>
                            <li>
                                <a class="dropdown-item py-2 fw-semibold text-primary" href="{{ route('simkerma.index') }}">
                                    <i class="bi bi-speedometer2 me-1.5"></i> Dashboard SIMKERMA
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item py-1.5" href="{{ route('simkerma.index', ['menu' => 4]) }}">
                                    <i class="bi bi-file-earmark-check me-1.5"></i> Kelola Naskah Kerja Sama
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item py-1.5" href="{{ route('simkerma.index', ['menu' => 5]) }}">
                                    <i class="bi bi-calendar-event me-1.5"></i> Monitoring Kegiatan IKU 6
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item py-1.5" href="{{ route('simkerma.index', ['menu' => 7]) }}">
                                    <i class="bi bi-graph-up-arrow me-1.5"></i> Laporan & Analitik Internal
                                </a>
                            </li>
                            <li><hr class="dropdown-divider my-1"></li>
                            <li>
                                <a class="dropdown-item py-1.5 text-danger fw-semibold" href="{{ route('logout') }}">
                                    <i class="bi bi-box-arrow-right me-1.5"></i> Keluar (Logout)
                                </a>
                            </li>
                        </ul>
                    </div>
                @else
                    <!-- Guest Login Button -->
                    <a href="{{ route('login') }}" class="btn btn-outline-dark btn-sm px-3 py-1.5 fw-bold d-flex align-items-center gap-1.5 shadow-2xs" style="font-size: 0.84rem; border-color: #cbd5e1;">
                        <i class="bi bi-shield-lock-fill text-primary"></i>
                        <span>Login Sivitas / Admin</span>
                    </a>
                @endif
            </div>
        </div>
    </nav>

    <!-- HERO SECTION -->
    <section class="hero-section" id="beranda">
        <div class="container">
            <div class="row align-items-center g-4">
                <div class="col-lg-8">
                    <div class="hero-badge">
                        <i class="bi bi-mortarboard-fill text-primary"></i> Portal Resmi Kerja Sama & Kemitraan Universitas Kristen Papua
                    </div>
                    <h2 class="hero-title">
                        Tata Kelola Kemitraan Strategis, Kolaborasi Tridharma, & Akselerasi IKU 6
                    </h2>
                    <p class="hero-subtitle">
                        Pusat data terpadu dan keterbukaan informasi kemitraan Universitas Kristen Papua bersama dunia usaha & dunia industri (DUDI), kementerian/lembaga pemerintah, serta perguruan tinggi nasional dan internasional dalam mendukung program MBKM.
                    </p>
                    <div class="d-flex flex-wrap align-items-center gap-3">
                        <a href="#grafik" class="btn btn-primary btn-md px-4 py-2 fw-bold shadow-sm d-flex align-items-center gap-2" style="background-color: var(--uni-navy); border-color: var(--uni-navy);">
                            <i class="bi bi-bar-chart-fill"></i> Lihat Grafik Statistik
                        </a>
                        <a href="#dokumen" class="btn btn-outline-secondary btn-md px-4 py-2 fw-bold bg-white d-flex align-items-center gap-2" style="border-color: #cbd5e1; color: var(--uni-navy);">
                            <i class="bi bi-file-earmark-text"></i> Direktori Naskah
                        </a>
                        @if (!auth()->check())
                            <a href="{{ route('login') }}" class="btn btn-outline-primary btn-md px-3 py-2 fw-semibold d-flex align-items-center gap-1.5">
                                <i class="bi bi-box-arrow-in-right"></i> Akses Internal Sivitas
                            </a>
                        @endif
                    </div>
                </div>

                <div class="col-lg-4 text-center text-lg-end">
                    <div class="bg-white border rounded-4 p-4 shadow-sm text-start" style="border-color: #e2e8f0;">
                        <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                            <span class="fw-bold text-dark font-size-13 text-uppercase">
                                <i class="bi bi-award-fill text-warning me-1"></i> Rekapitulasi Sah
                            </span>
                            <span class="badge bg-success-subtle text-success small">Tersinkronisasi</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted small">Tingkat Dokumen Aktif:</span>
                            <strong class="text-success">{{ $totalDocs > 0 ? round(($activeDocs / $totalDocs) * 100, 1) : 0 }}%</strong>
                        </div>
                        <div class="progress mb-3" style="height: 8px;">
                            <div class="progress-bar bg-success" style="width: {{ $totalDocs > 0 ? round(($activeDocs / $totalDocs) * 100) : 0 }}%"></div>
                        </div>
                        <div class="vstack gap-2 small">
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">Nota Kesepahaman (MoU):</span>
                                <strong class="text-primary">{{ $mouCount }} Naskah</strong>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">Perjanjian Kerja Sama (MoA):</span>
                                <strong class="text-success">{{ $moaCount }} Naskah</strong>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">Implementation Arrangement:</span>
                                <strong class="text-warning">{{ $iaCount }} Naskah</strong>
                            </div>
                        </div>
                        <div class="mt-3 pt-2 border-top text-center">
                            <a href="#ringkasan" class="text-decoration-none small text-primary fw-semibold">
                                Lihat Rincian Ringkasan Data <i class="bi bi-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TOP 4 PRIMARY KPI METRIC CARDS -->
            <div class="row g-3 mt-3">
                <div class="col-6 col-lg-3">
                    <div class="kpi-stat-card">
                        <div class="kpi-top-row">
                            <div class="kpi-icon-wrap bg-primary-subtle text-primary">
                                <i class="bi bi-folder-check"></i>
                            </div>
                            <span class="badge bg-primary-subtle text-primary small">Total Naskah</span>
                        </div>
                        <div class="kpi-value">{{ $totalDocs }}</div>
                        <div class="kpi-label">Dokumen Kerja Sama</div>
                        <div class="kpi-subtext">{{ $mouCount }} MoU • {{ $moaCount }} MoA • {{ $iaCount }} IA</div>
                    </div>
                </div>

                <div class="col-6 col-lg-3">
                    <div class="kpi-stat-card">
                        <div class="kpi-top-row">
                            <div class="kpi-icon-wrap bg-success-subtle text-success">
                                <i class="bi bi-shield-check"></i>
                            </div>
                            <span class="badge bg-success-subtle text-success small">Status Sah</span>
                        </div>
                        <div class="kpi-value text-success">{{ $activeDocs }}</div>
                        <div class="kpi-label">Kerja Sama Aktif</div>
                        <div class="kpi-subtext">Memiliki kekuatan hukum & berlaku saat ini</div>
                    </div>
                </div>

                <div class="col-6 col-lg-3">
                    <div class="kpi-stat-card">
                        <div class="kpi-top-row">
                            <div class="kpi-icon-wrap bg-info-subtle text-info">
                                <i class="bi bi-buildings"></i>
                            </div>
                            <span class="badge bg-info-subtle text-info small">Institusi Sah</span>
                        </div>
                        <div class="kpi-value text-dark">{{ $totalPartners }}</div>
                        <div class="kpi-label">Mitra Terverifikasi</div>
                        <div class="kpi-subtext">{{ $nationalPartners }} Nasional • {{ $internationalPartners }} Internasional</div>
                    </div>
                </div>

                <div class="col-6 col-lg-3">
                    <div class="kpi-stat-card">
                        <div class="kpi-top-row">
                            <div class="kpi-icon-wrap bg-warning-subtle text-warning">
                                <i class="bi bi-check2-all"></i>
                            </div>
                            <span class="badge bg-warning-subtle text-dark small">Realisasi</span>
                        </div>
                        <div class="kpi-value text-dark">{{ $totalActivities }}</div>
                        <div class="kpi-label">Kegiatan MBKM & IKU 6</div>
                        <div class="kpi-subtext">{{ $completedActivities }} Selesai • {{ $runningActivities }} Sedang Berjalan</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- SECTION 1: GRAFIK & VISUALISASI STATISTIK KEMITRAAN (FOKUS UTAMA) -->
    <section class="py-5" id="grafik" style="background-color: #f8fafc;">
        <div class="container py-3">
            <div class="section-header text-center">
                <span class="section-badge"><i class="bi bi-pie-chart-fill me-1"></i> Analitik Visual Interaktif</span>
                <h3 class="section-title">Grafik & Statistik Kemitraan Kampus</h3>
                <p class="section-desc mx-auto">
                    Visualisasi data dokumen kemitraan Tridharma, proporsi jenis naskah, klasifikasi sektor mitra, dan sebaran unit pengusul di Universitas Kristen Papua.
                </p>
            </div>

            <!-- ROW 1 OF CHARTS: COMPOSITION & PARTNER CATEGORIES -->
            <div class="row g-4 mb-4">
                <!-- CHART 1: KOMPOSISI JENIS DOKUMEN -->
                <div class="col-lg-5">
                    <div class="chart-box">
                        <div class="chart-header">
                            <h6 class="chart-title">
                                <i class="bi bi-pie-chart text-primary"></i> Komposisi Jenis Dokumen
                            </h6>
                            <span class="badge bg-light text-secondary border small">{{ $totalDocs }} Naskah</span>
                        </div>
                        <div id="chartDocTypes" style="min-height: 270px;"></div>
                        <div class="d-flex justify-content-around text-center pt-2 mt-2 border-top small">
                            <div>
                                <span class="badge bg-primary px-2 py-0.5">MoU</span>
                                <div class="fw-bold text-dark mt-1">{{ $mouCount }} ({{ $totalDocs > 0 ? round(($mouCount/$totalDocs)*100) : 0 }}%)</div>
                            </div>
                            <div>
                                <span class="badge bg-success px-2 py-0.5">MoA</span>
                                <div class="fw-bold text-dark mt-1">{{ $moaCount }} ({{ $totalDocs > 0 ? round(($moaCount/$totalDocs)*100) : 0 }}%)</div>
                            </div>
                            <div>
                                <span class="badge bg-warning text-dark px-2 py-0.5">IA</span>
                                <div class="fw-bold text-dark mt-1">{{ $iaCount }} ({{ $totalDocs > 0 ? round(($iaCount/$totalDocs)*100) : 0 }}%)</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- CHART 2: SEKTOR & KATEGORI MITRA -->
                <div class="col-lg-7">
                    <div class="chart-box">
                        <div class="chart-header">
                            <h6 class="chart-title">
                                <i class="bi bi-bar-chart text-success"></i> Distribusi Kategori Mitra Kerjasama
                            </h6>
                            <span class="badge bg-light text-secondary border small">{{ $totalPartners }} Mitra Sah</span>
                        </div>
                        <div id="chartPartnerCategories" style="min-height: 270px;"></div>
                    </div>
                </div>
            </div>

            <!-- ROW 2 OF CHARTS: FACULTY/UNIT DISTRIBUTION & STATUS OVERVIEW -->
            <div class="row g-4">
                <!-- CHART 3: SEBARAN KERJASAMA PER UNIT / FAKULTAS -->
                <div class="col-lg-7">
                    <div class="chart-box">
                        <div class="chart-header">
                            <h6 class="chart-title">
                                <i class="bi bi-diagram-3 text-info"></i> Sebaran Dokumen per Fakultas / Unit
                            </h6>
                            <span class="badge bg-light text-secondary border small">Unit Pengusul Utama</span>
                        </div>
                        <div id="chartDocsByUnit" style="min-height: 260px;"></div>
                    </div>
                </div>

                <!-- CHART 4: STATUS DOKUMEN REAL-TIME -->
                <div class="col-lg-5">
                    <div class="chart-box">
                        <div class="chart-header">
                            <h6 class="chart-title">
                                <i class="bi bi-check-circle text-warning"></i> Status Dokumen Real-Time
                            </h6>
                            <span class="badge bg-light text-secondary border small">Status Hukum</span>
                        </div>
                        <div id="chartDocStatus" style="min-height: 260px;"></div>
                        <div class="vstack gap-1.5 pt-2 mt-2 border-top text-muted" style="font-size: 0.76rem;">
                            <div class="d-flex justify-content-between">
                                <span><i class="bi bi-circle-fill text-success me-1"></i> Aktif Berlaku Sah:</span>
                                <strong class="text-dark">{{ $activeDocs }} Naskah</strong>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span><i class="bi bi-circle-fill text-info me-1"></i> Dalam Telaah & Persetujuan:</span>
                                <strong class="text-dark">{{ $reviewDocs + $verifiedDocs }} Naskah</strong>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span><i class="bi bi-circle-fill text-secondary me-1"></i> Selesai Masa Berlaku:</span>
                                <strong class="text-dark">{{ $expiredDocs }} Naskah</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- SECTION 2: RINGKASAN DATA & REKAPITULASI RESMI (SINKRON DENGAN SISTEM) -->
    <section class="py-5 bg-white border-top border-bottom" id="ringkasan">
        <div class="container py-3">
            <div class="section-header text-center">
                <span class="section-badge"><i class="bi bi-card-checklist me-1"></i> Rekapitulasi Data</span>
                <h3 class="section-title">Ringkasan Kinerja Kemitraan UNIPAR</h3>
                <p class="section-desc mx-auto">
                    Data agregat resmi yang disinkronkan secara langsung dengan modul pelaporan eksekutif SIM-KERMA UNIPAR.
                </p>
            </div>

            <div class="row g-4">
                <!-- REKAP 1: DOKUMEN & MASA BERLAKU -->
                <div class="col-md-4">
                    <div class="summary-metric-card">
                        <div class="d-flex align-items-center gap-2.5 mb-3 pb-2 border-bottom">
                            <div class="bg-primary text-white rounded-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; font-size: 1rem;">
                                <i class="bi bi-file-earmark-check"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold text-dark mb-0 font-size-14">Rekapitulasi Naskah</h6>
                                <span class="text-muted" style="font-size: 0.72rem;">Legalitas & Masa Berlaku</span>
                            </div>
                        </div>
                        <div class="vstack gap-2.5 small">
                            <div class="d-flex justify-content-between align-items-center p-2 rounded bg-light">
                                <span class="text-muted">Total Seluruh Naskah:</span>
                                <strong class="text-dark">{{ $totalDocs }} Dokumen</strong>
                            </div>
                            <div class="d-flex justify-content-between align-items-center p-2 rounded bg-light">
                                <span class="text-muted">Naskah Aktif Sah:</span>
                                <strong class="text-success">{{ $activeDocs }} Dokumen ({{ $totalDocs > 0 ? round(($activeDocs/$totalDocs)*100, 1) : 0 }}%)</strong>
                            </div>
                            <div class="d-flex justify-content-between align-items-center p-2 rounded bg-light">
                                <span class="text-muted">Dalam Proses Telaah:</span>
                                <strong class="text-info">{{ $reviewDocs + $verifiedDocs }} Dokumen</strong>
                            </div>
                            <div class="d-flex justify-content-between align-items-center p-2 rounded bg-light">
                                <span class="text-muted">Selesai / Kedaluwarsa:</span>
                                <strong class="text-danger">{{ $expiredDocs }} Dokumen</strong>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- REKAP 2: JANGKAUAN & PROFIL MITRA -->
                <div class="col-md-4">
                    <div class="summary-metric-card">
                        <div class="d-flex align-items-center gap-2.5 mb-3 pb-2 border-bottom">
                            <div class="bg-success text-white rounded-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; font-size: 1rem;">
                                <i class="bi bi-globe-americas"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold text-dark mb-0 font-size-14">Jangkauan Kolaboratif</h6>
                                <span class="text-muted" style="font-size: 0.72rem;">Cakupan Wilayah Mitra</span>
                            </div>
                        </div>
                        <div class="vstack gap-2.5 small">
                            <div class="d-flex justify-content-between align-items-center p-2 rounded bg-light">
                                <span class="text-muted">Total Mitra Terverifikasi:</span>
                                <strong class="text-dark">{{ $totalPartners }} Institusi</strong>
                            </div>
                            <div class="d-flex justify-content-between align-items-center p-2 rounded bg-light">
                                <span class="text-muted">Mitra Nasional / Lokal:</span>
                                <strong class="text-primary">{{ $nationalPartners }} Institusi ({{ $totalPartners > 0 ? round(($nationalPartners/$totalPartners)*100, 1) : 0 }}%)</strong>
                            </div>
                            <div class="d-flex justify-content-between align-items-center p-2 rounded bg-light">
                                <span class="text-muted">Mitra Internasional:</span>
                                <strong class="text-warning">{{ $internationalPartners }} Institusi ({{ $totalPartners > 0 ? round(($internationalPartners/$totalPartners)*100, 1) : 0 }}%)</strong>
                            </div>
                            <div class="d-flex justify-content-between align-items-center p-2 rounded bg-light">
                                <span class="text-muted">Status Legalitas Mitra:</span>
                                <strong class="text-success"><i class="bi bi-patch-check-fill me-1"></i>100% Sah</strong>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- REKAP 3: REALISASI IKU 6 -->
                <div class="col-md-4">
                    <div class="summary-metric-card">
                        <div class="d-flex align-items-center gap-2.5 mb-3 pb-2 border-bottom">
                            <div class="bg-warning text-dark rounded-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; font-size: 1rem;">
                                <i class="bi bi-mortarboard-fill"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold text-dark mb-0 font-size-14">Implementasi IKU 6</h6>
                                <span class="text-muted" style="font-size: 0.72rem;">Kegiatan Riil Lapangan</span>
                            </div>
                        </div>
                        <div class="vstack gap-2.5 small">
                            <div class="d-flex justify-content-between align-items-center p-2 rounded bg-light">
                                <span class="text-muted">Total Agenda Kegiatan:</span>
                                <strong class="text-dark">{{ $totalActivities }} Program</strong>
                            </div>
                            <div class="d-flex justify-content-between align-items-center p-2 rounded bg-light">
                                <span class="text-muted">Telah Selesai 100%:</span>
                                <strong class="text-success">{{ $completedActivities }} Program</strong>
                            </div>
                            <div class="d-flex justify-content-between align-items-center p-2 rounded bg-light">
                                <span class="text-muted">Sedang Berjalan (Active):</span>
                                <strong class="text-primary">{{ $runningActivities }} Program</strong>
                            </div>
                            <div class="d-flex justify-content-between align-items-center p-2 rounded bg-light">
                                <span class="text-muted">Tahap Perencanaan:</span>
                                <strong class="text-secondary">{{ $pendingActivities }} Program</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- SECTION 3: DIREKTORI DOKUMEN PUBLIK TERBUKA -->
    <section class="py-5" id="dokumen" style="background-color: #f8fafc;">
        <div class="container py-3">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end mb-4 gap-3">
                <div>
                    <span class="section-badge"><i class="bi bi-table me-1"></i> Arsip Resmi</span>
                    <h3 class="section-title mb-1">Direktori Naskah Kerja Sama</h3>
                    <p class="section-desc mb-0">Informasi publik ringkas naskah kerja sama yang telah disahkan dan terdaftar di Universitas Kristen Papua.</p>
                </div>
                <!-- Search Box -->
                <div class="w-100" style="max-width: 340px;">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" id="filterDocInput" class="form-control bg-white border-start-0" placeholder="Cari mitra, nomor naskah, unit...">
                    </div>
                </div>
            </div>

            <!-- TABLE OF DOCUMENTS -->
            <div class="card border rounded-3 overflow-hidden shadow-2xs">
                <div class="table-responsive">
                    <table class="uni-table align-middle" id="publicDocsTable">
                        <thead>
                            <tr>
                                <th style="width: 16%;">No. Naskah Resmi</th>
                                <th style="width: 10%;">Jenis</th>
                                <th style="width: 32%;">Judul & Ruang Lingkup Kerja Sama</th>
                                <th style="width: 22%;">Mitra Kolaborator</th>
                                <th style="width: 10%;">Unit Pengusul</th>
                                <th style="width: 10%;" class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($recentDocuments as $doc)
                                <tr>
                                    <td>
                                        <div class="fw-bold text-dark font-monospace" style="font-size: 0.8rem;">
                                            {{ $doc->doc_number ?: $doc->doc_id }}
                                        </div>
                                        <div class="text-muted" style="font-size: 0.7rem;">
                                            Ref: {{ $doc->doc_id }}
                                        </div>
                                    </td>
                                    <td>
                                        @if (str_contains($doc->doc_type, 'MoU'))
                                            <span class="badge bg-primary px-2 py-1" style="font-size: 0.72rem;">MoU</span>
                                        @elseif (str_contains($doc->doc_type, 'MoA'))
                                            <span class="badge bg-success px-2 py-1" style="font-size: 0.72rem;">MoA</span>
                                        @else
                                            <span class="badge bg-warning text-dark px-2 py-1" style="font-size: 0.72rem;">IA</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="fw-semibold text-dark text-truncate" style="max-width: 360px;" title="{{ $doc->title ?: $doc->scope }}">
                                            {{ $doc->title ?: ($doc->scope ?: 'Perjanjian Kerja Sama Kemitraan Tridharma') }}
                                        </div>
                                        <div class="text-muted text-truncate" style="font-size: 0.74rem; max-width: 360px;">
                                            <i class="bi bi-clock-history me-1"></i> Masa Berlaku: {{ $doc->validity ?: '1 Tahun' }}
                                            @if ($doc->start_date && $doc->end_date)
                                                ({{ \Carbon\Carbon::parse($doc->start_date)->format('Y') }} - {{ \Carbon\Carbon::parse($doc->end_date)->format('Y') }})
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark" style="font-size: 0.84rem;">
                                            {{ $doc->partner_name }}
                                        </div>
                                        <div class="text-muted" style="font-size: 0.72rem;">
                                            <i class="bi bi-tag me-1"></i> {{ $doc->partner_category }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="small text-secondary" style="font-size: 0.78rem;">
                                            {{ $doc->unit }}
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        @if ($doc->status === 'ACTIVE')
                                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1" style="font-size: 0.72rem;">
                                                <i class="bi bi-check-circle-fill me-1"></i>AKTIF SAH
                                            </span>
                                        @elseif ($doc->status === 'VERIFIED')
                                            <span class="badge bg-info-subtle text-info border border-info-subtle px-2 py-1" style="font-size: 0.72rem;">
                                                <i class="bi bi-check2-all me-1"></i>VERIFIED
                                            </span>
                                        @elseif ($doc->status === 'REVIEW')
                                            <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1" style="font-size: 0.72rem;">
                                                <i class="bi bi-hourglass-split me-1"></i>REVIEW
                                            </span>
                                        @elseif ($doc->status === 'EXPIRED')
                                            <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2 py-1" style="font-size: 0.72rem;">
                                                SELESAI
                                            </span>
                                        @else
                                            <span class="badge bg-light text-dark border px-2 py-1" style="font-size: 0.72rem;">
                                                {{ $doc->status }}
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">
                                        Belum ada dokumen yang dipublikasikan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            
            <div class="mt-3 text-end">
                <span class="text-muted small">Menampilkan {{ count($recentDocuments) }} naskah terbaru dari total {{ $totalDocs }} dokumen terdaftar.</span>
            </div>
        </div>
    </section>

    <!-- SECTION 4: DIREKTORI MITRA KERJASAMA STRATEGIS -->
    <section class="py-5 bg-white" id="mitra">
        <div class="container py-3">
            <div class="section-header text-center">
                <span class="section-badge"><i class="bi bi-globe me-1"></i> Jaringan Kolaboratif</span>
                <h3 class="section-title">Mitra Kerja Sama Strategis</h3>
                <p class="section-desc mx-auto">
                    Kemitraan berkesinambungan bersama industri nasional, badan usaha milik negara, lembaga riset, dan perguruan tinggi bereputasi tinggi.
                </p>
            </div>

            <div class="row g-3">
                @forelse ($featuredPartners as $pt)
                    <div class="col-md-6 col-lg-3">
                        <div class="partner-pill">
                            <div class="partner-icon-box">
                                @if (str_contains(strtolower($pt->category), 'luar negeri') || str_contains(strtolower($pt->scope_coverage), 'luar negeri'))
                                    <i class="bi bi-globe-americas text-primary"></i>
                                @elseif (str_contains(strtolower($pt->category), 'bumn'))
                                    <i class="bi bi-building text-success"></i>
                                @elseif (str_contains(strtolower($pt->category), 'pemerintah'))
                                    <i class="bi bi-bank text-warning"></i>
                                @else
                                    <i class="bi bi-briefcase text-dark"></i>
                                @endif
                            </div>
                            <div class="overflow-hidden">
                                <div class="fw-bold text-dark text-truncate" style="font-size: 0.88rem;" title="{{ $pt->name }}">
                                    {{ $pt->name }}
                                </div>
                                <div class="text-muted text-truncate" style="font-size: 0.72rem;">
                                    {{ $pt->category ?: 'Mitra Strategis' }}
                                </div>
                                <div class="text-primary mt-1" style="font-size: 0.72rem; font-weight: 600;">
                                    <i class="bi bi-file-earmark-check"></i> {{ $pt->documents_count }} Dokumen Kerja Sama
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center text-muted small py-4">Belum ada daftar mitra.</div>
                @endforelse
            </div>
        </div>
    </section>

    <!-- CALL TO ACTION FOR INTERNAL SIVITAS (LOGIN ADMIN / SIVITAS) -->
    <section class="py-5" style="background: linear-gradient(135deg, #0b1f3a 0%, #173868 100%); color: white;">
        <div class="container text-center py-3">
            <span class="badge bg-white bg-opacity-10 text-white px-3 py-1.5 rounded-pill small fw-semibold mb-3">
                <i class="bi bi-shield-lock-fill text-warning me-1"></i> Portal Sivitas Akademika & Administrasi
            </span>
            <h2 class="fw-bold mb-3" style="font-size: 2rem; letter-spacing: -0.02em;">
                Masuk ke Sistem Pengelolaan Kemitraan UNIPAR
            </h2>
            <p class="text-light text-opacity-75 mx-auto mb-4" style="max-width: 650px; font-size: 1rem;">
                Dosen pengusul, pengelola prodi, tim telaah hukum, dan pimpinan rektorat dapat masuk untuk menyusun draf naskah baru, memeriksa legalitas, memberikan persetujuan, atau mencatat realisasi kegiatan MBKM.
            </p>
            <div class="d-flex justify-content-center gap-3">
                @if (auth()->check())
                    <a href="{{ route('simkerma.index') }}" class="btn btn-warning btn-lg px-4 py-2.5 fw-bold text-dark shadow">
                        <i class="bi bi-speedometer2 me-1.5"></i> Masuk Dashboard SIMKERMA
                    </a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-warning btn-lg px-4 py-2.5 fw-bold text-dark shadow">
                        <i class="bi bi-box-arrow-in-right me-1.5"></i> Login Sivitas / Admin
                    </a>
                @endif
                <a href="#dokumen" class="btn btn-outline-light btn-lg px-4 py-2.5 fw-semibold">
                    <i class="bi bi-files me-1.5"></i> Lihat Direktori Naskah
                </a>
            </div>
        </div>
    </section>

    <!-- FOOTER INSTITUSIONAL PERGURUAN TINGGI -->
    <footer class="uni-footer">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-5">
                    <div class="d-flex align-items-center gap-2.5 mb-3">
                        <div class="brand-emblem" style="width: 38px; height: 38px; font-size: 1.15rem;">
                            <i class="bi bi-buildings"></i>
                        </div>
                        <div>
                            <div class="fw-bold text-white font-size-15">UNIVERSITAS KRISTEN PAPUA</div>
                            <div class="text-muted" style="font-size: 0.7rem; font-weight: 600;">SIM-KERMA UNIPAR</div>
                        </div>
                    </div>
                    <p class="text-light text-opacity-60 small pe-lg-4 mb-3">
                        Sistem Informasi Manajemen Kerja Sama (SIM-KERMA) Universitas Kristen Papua dirancang untuk memfasilitasi tata kelola kemitraan Tridharma yang akuntabel, transparan, dan mendukung akselerasi Indikator Kinerja Utama (IKU 6) Kemendikbudristek.
                    </p>
                    <div class="small text-light text-opacity-75">
                        <i class="bi bi-geo-alt-fill text-warning me-1"></i> Sorong, Papua Barat Daya, Indonesia
                    </div>
                </div>

                <div class="col-6 col-lg-3">
                    <div class="footer-heading">Navigasi Portal</div>
                    <a href="#beranda" class="footer-link">Beranda</a>
                    <a href="#grafik" class="footer-link">Grafik Statistik</a>
                    <a href="#ringkasan" class="footer-link">Ringkasan Data</a>
                    <a href="#dokumen" class="footer-link">Direktori Naskah</a>
                    <a href="#mitra" class="footer-link">Mitra Kolaborasi</a>
                </div>

                <div class="col-6 col-lg-4">
                    <div class="footer-heading">Biro Kerja Sama UNIPAR</div>
                    <div class="small text-light text-opacity-75 mb-2">
                        Gedung Rektorat Lt. 2, Kampus Terpadu UNIPAR<br>
                        Kota Sorong, Papua Barat Daya
                    </div>
                    <div class="small text-light text-opacity-75 mb-1">
                        <i class="bi bi-envelope me-1.5 text-warning"></i> kerjasama@unipar.ac.id
                    </div>
                    <div class="small text-light text-opacity-75 mb-3">
                        <i class="bi bi-telephone me-1.5 text-warning"></i> (0951) 321-4567
                    </div>
                    <a href="{{ route('login') }}" class="btn btn-outline-light btn-sm fw-semibold">
                        <i class="bi bi-shield-lock me-1"></i> Login Internal Kampus
                    </a>
                </div>
            </div>

            <div class="footer-bottom d-flex flex-column flex-md-row justify-content-between align-items-center gap-2">
                <div>
                    © 2026 Universitas Kristen Papua (UNIPAR). Seluruh Hak Cipta Dilindungi Undang-Undang.
                </div>
                <div class="text-muted small">
                    Standar IKU 6 Kemitraan Program Studi Kemendikbudristek RI
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap 5 Bundle JS -->
    <script src="{{ asset('partial/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <!-- ApexCharts JS -->
    <script src="{{ asset('partial/libs/apexcharts/apexcharts.min.js') }}"></script>

    <!-- APEXCHARTS CONFIGURATION & INTERACTIVITY -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // 1. CHART KOMPOSISI JENIS DOKUMEN (DONUT CHART)
            const typeLabels = @json($chartTypeLabels);
            const typeSeries = @json($chartTypeSeries);

            const optionsType = {
                chart: {
                    type: 'donut',
                    height: 270,
                    fontFamily: 'Plus Jakarta Sans, sans-serif'
                },
                series: typeSeries,
                labels: typeLabels,
                colors: ['#1d4ed8', '#10b981', '#f59e0b'],
                legend: {
                    position: 'bottom',
                    fontSize: '12px'
                },
                dataLabels: {
                    enabled: true,
                    formatter: function (val) {
                        return Math.round(val) + "%";
                    }
                },
                plotOptions: {
                    pie: {
                        donut: {
                            size: '65%',
                            labels: {
                                show: true,
                                total: {
                                    show: true,
                                    label: 'Total Naskah',
                                    fontSize: '13px',
                                    fontWeight: 700,
                                    color: '#0b1f3a',
                                    formatter: function (w) {
                                        return w.globals.seriesTotals.reduce((a, b) => a + b, 0);
                                    }
                                }
                            }
                        }
                    }
                },
                tooltip: {
                    y: {
                        formatter: function (val) {
                            return val + " Naskah";
                        }
                    }
                }
            };
            new ApexCharts(document.querySelector("#chartDocTypes"), optionsType).render();

            // 2. CHART DISTRIBUSI KATEGORI MITRA (BAR CHART)
            const catLabels = @json($chartCategoryLabels);
            const catSeries = @json($chartCategorySeries);

            const optionsCategory = {
                chart: {
                    type: 'bar',
                    height: 270,
                    toolbar: { show: false },
                    fontFamily: 'Plus Jakarta Sans, sans-serif'
                },
                plotOptions: {
                    bar: {
                        borderRadius: 6,
                        columnWidth: '45%',
                        distributed: true,
                        dataLabels: {
                            position: 'top'
                        }
                    }
                },
                colors: ['#2563eb', '#10b981', '#06b6d4', '#f59e0b', '#8b5cf6'],
                series: [{
                    name: 'Jumlah Mitra',
                    data: catSeries
                }],
                xaxis: {
                    categories: catLabels,
                    labels: {
                        rotate: -15,
                        style: { fontSize: '11px' }
                    }
                },
                yaxis: {
                    title: { text: 'Institusi Mitra' },
                    tickAmount: 3
                },
                legend: { show: false },
                dataLabels: {
                    enabled: true,
                    offsetY: -18,
                    style: { fontSize: '11px', colors: ["#334155"] }
                },
                grid: {
                    borderColor: '#f1f5f9'
                }
            };
            new ApexCharts(document.querySelector("#chartPartnerCategories"), optionsCategory).render();

            // 3. CHART DOKUMEN PER FAKULTAS / UNIT (HORIZONTAL BAR)
            const unitLabels = @json($chartUnitLabels);
            const unitSeries = @json($chartUnitSeries);

            const optionsUnit = {
                chart: {
                    type: 'bar',
                    height: 260,
                    toolbar: { show: false },
                    fontFamily: 'Plus Jakarta Sans, sans-serif'
                },
                plotOptions: {
                    bar: {
                        borderRadius: 4,
                        horizontal: true,
                        barHeight: '50%'
                    }
                },
                colors: ['#0f766e'],
                series: [{
                    name: 'Naskah Kerja Sama',
                    data: unitSeries
                }],
                xaxis: {
                    categories: unitLabels,
                    tickAmount: 3
                },
                dataLabels: {
                    enabled: true,
                    style: { fontSize: '11px' },
                    formatter: function (val) {
                        return val + " Naskah";
                    }
                },
                grid: {
                    borderColor: '#f1f5f9'
                }
            };
            new ApexCharts(document.querySelector("#chartDocsByUnit"), optionsUnit).render();

            // 4. CHART STATUS DOKUMEN REAL-TIME (DONUT CHART)
            const statusLabels = @json($chartStatusLabels);
            const statusSeries = @json($chartStatusSeries);

            const optionsStatus = {
                chart: {
                    type: 'donut',
                    height: 260,
                    fontFamily: 'Plus Jakarta Sans, sans-serif'
                },
                series: statusSeries,
                labels: statusLabels,
                colors: ['#10b981', '#f59e0b', '#06b6d4', '#ef4444'],
                legend: {
                    position: 'bottom',
                    fontSize: '12px'
                },
                dataLabels: {
                    enabled: true,
                    formatter: function (val) {
                        return Math.round(val) + "%";
                    }
                },
                plotOptions: {
                    pie: {
                        donut: {
                            size: '60%'
                        }
                    }
                }
            };
            new ApexCharts(document.querySelector("#chartDocStatus"), optionsStatus).render();

            // Instant Client-side Table Filter Script
            const filterInput = document.getElementById('filterDocInput');
            const table = document.getElementById('publicDocsTable');
            if (filterInput && table) {
                filterInput.addEventListener('keyup', function () {
                    const query = this.value.toLowerCase().trim();
                    const rows = table.querySelectorAll('tbody tr');
                    rows.forEach(row => {
                        const text = row.innerText.toLowerCase();
                        if (text.includes(query)) {
                            row.style.display = '';
                        } else {
                            row.style.display = 'none';
                        }
                    });
                });
            }
        });
    </script>
</body>
</html>
