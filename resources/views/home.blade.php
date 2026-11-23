<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIKERMA - Universitas Parna Raya</title>
    
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
            --uni-teal: #0d9488;
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
            background-color: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid var(--uni-border);
            padding: 12px 0;
            transition: all 0.3s ease;
        }

        .brand-logo-area {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
        }

        .brand-emblem {
            width: 44px;
            height: 44px;
            background: linear-gradient(135deg, var(--uni-navy) 0%, var(--uni-blue) 100%);
            color: #fff;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
            box-shadow: 0 4px 12px rgba(11, 31, 58, 0.2);
        }

        .brand-text h1 {
            font-size: 1.15rem;
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
        }

        .nav-link-custom {
            color: #475569;
            font-size: 0.88rem;
            font-weight: 600;
            padding: 8px 14px;
            border-radius: 8px;
            transition: all 0.2s ease;
            text-decoration: none;
        }

        .nav-link-custom:hover {
            color: var(--uni-blue);
            background-color: var(--uni-blue-subtle);
        }

        /* Hero / Masthead Section */
        .hero-section {
            background: radial-gradient(circle at 80% 20%, rgba(29, 78, 216, 0.07) 0%, transparent 50%),
                        linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
            padding: 60px 0 50px 0;
            border-bottom: 1px solid var(--uni-border);
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background-color: #eff6ff;
            color: #1e40af;
            font-size: 0.78rem;
            font-weight: 700;
            padding: 6px 14px;
            border-radius: 9999px;
            border: 1px solid #bfdbfe;
            margin-bottom: 18px;
        }

        .hero-title {
            font-size: 2.3rem;
            font-weight: 800;
            color: var(--uni-navy);
            line-height: 1.25;
            letter-spacing: -0.03em;
            margin-bottom: 16px;
        }

        .hero-subtitle {
            font-size: 1.05rem;
            color: #475569;
            line-height: 1.6;
            margin-bottom: 24px;
        }

        /* SIKERMA UNJ Style Stat Cards */
        .stat-card-hover {
            transition: transform 0.25s ease, box-shadow 0.25s ease;
            background: #ffffff;
            border: 1px solid #e2e8f0 !important;
        }

        .stat-card-hover:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 24px -6px rgba(11, 31, 58, 0.12) !important;
            border-color: #cbd5e1 !important;
        }

        .divider {
            border: none;
            border-radius: 9999px;
        }

        /* Custom Chart Tabs */
        .custom-chart-tabs .nav-link {
            color: #64748b;
            border-radius: 8px;
            padding: 9px 18px;
            font-size: 0.85rem;
            transition: all 0.2s ease;
            border: 1px solid transparent;
        }

        .custom-chart-tabs .nav-link:hover {
            color: var(--uni-navy);
            background-color: #f1f5f9;
        }

        .custom-chart-tabs .nav-link.active {
            color: #ffffff;
            background-color: var(--uni-navy);
            box-shadow: 0 4px 10px rgba(11, 31, 58, 0.2);
        }

        /* Table Info Styling */
        #table-info-kerjasama th {
            font-size: 0.82rem;
            font-weight: 700;
            padding: 10px 12px;
        }

        #table-info-kerjasama td {
            font-size: 0.82rem;
            padding: 10px 12px;
        }

        .pagination-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.82rem;
            margin-top: 15px;
        }

        /* Footer */
        .uni-footer {
            background-color: var(--uni-navy);
            color: #cbd5e1;
            padding: 55px 0 25px 0;
            border-top: 1px solid rgba(255, 255, 255, 0.08);
        }

        .footer-heading {
            color: #ffffff;
            font-size: 0.92rem;
            font-weight: 700;
            margin-bottom: 16px;
            letter-spacing: -0.01em;
        }

        .footer-link {
            color: #94a3b8;
            font-size: 0.84rem;
            text-decoration: none;
            display: block;
            margin-bottom: 8px;
            transition: color 0.2s ease;
        }

        .footer-link:hover {
            color: #ffffff;
            padding-left: 3px;
        }

        .footer-bottom {
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            padding-top: 20px;
            margin-top: 40px;
            font-size: 0.8rem;
            color: #94a3b8;
        }
    </style>
</head>
<body id="page-top">

    <!-- TOP INSTITUTIONAL BAR -->
    <div class="top-inst-bar">
        <div class="container d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-3">
                <span><i class="bi bi-geo-alt-fill text-warning me-1"></i> Kampus Utama: Jl. Sam Ratulangi No. 1, Manado, Sulawesi Utara</span>
                <span class="d-none d-md-inline text-secondary">|</span>
                <span class="d-none d-md-inline"><i class="bi bi-envelope-fill me-1"></i> kerjasama@unipar.ac.id</span>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-light text-dark fw-bold px-2 py-1 font-monospace" style="font-size: 0.72rem;">
                    <i class="bi bi-award-fill text-primary me-1"></i> IKU 6 Kemendikbudristek
                </span>
            </div>
        </div>
    </div>

    <!-- MAIN NAVBAR -->
    <nav class="uni-navbar sticky-top">
        <div class="container d-flex align-items-center justify-content-between">
            <a href="#page-top" class="brand-logo-area">
                <div class="brand-emblem">
                    <i class="bi bi-buildings"></i>
                </div>
                <div class="brand-text">
                    <h1>UNIVERSITAS PARNA RAYA</h1>
                    <p>SIKERMA • Sistem Informasi Kerja Sama</p>
                </div>
            </a>

            <!-- Navigation Links -->
            <div class="d-none d-lg-flex align-items-center gap-2">
                <a href="#page-top" class="nav-link-custom">Beranda</a>
                <a href="#stats" class="nav-link-custom">Statistik</a>
                <a href="#graphic-stat" class="nav-link-custom">Grafik Kemitraan</a>
                <a href="#info" class="nav-link-custom">Dokumen Kerja Sama</a>
                <a href="#contact" class="nav-link-custom">Kontak</a>
            </div>

            <!-- Action Buttons -->
            <div class="d-flex align-items-center gap-2">
                @if (auth()->check())
                    <a href="{{ route('simkerma.index') }}" class="btn btn-primary btn-sm fw-bold px-3 py-1.5 d-flex align-items-center gap-1.5 shadow-sm" style="background-color: var(--uni-navy); border-color: var(--uni-navy);">
                        <i class="bi bi-speedometer2"></i> Dashboard SIM-KERMA
                    </a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-outline-primary btn-sm fw-semibold px-3 py-1.5 d-flex align-items-center gap-1.5">
                        <i class="bi bi-box-arrow-in-right"></i> Masuk
                    </a>
                    <a href="{{ route('simkerma.index') }}" class="btn btn-primary btn-sm fw-bold px-3 py-1.5 d-flex align-items-center gap-1.5 shadow-sm" style="background-color: var(--uni-navy); border-color: var(--uni-navy);">
                        <i class="bi bi-shield-lock-fill"></i> Portal SIM-KERMA
                    </a>
                @endif
            </div>
        </div>
    </nav>

    <!-- HERO / MASTHEAD SECTION -->
    <header class="hero-section">
        <div class="container">
            <div class="row align-items-center g-4">
                <div class="col-lg-8">
                    <div class="hero-badge">
                        <i class="bi bi-mortarboard-fill text-primary"></i> Portal Resmi Kerja Sama & Kemitraan Universitas Parna Raya
                    </div>
                    <h2 class="hero-title">
                        Sistem Informasi Kerja Sama<br>Universitas Parna Raya
                    </h2>
                    <p class="hero-subtitle">
                        "Memajukan Inovasi Melalui Kolaborasi, Mengembangkan Keunggulan Melalui Kemitraan" — SIKERMA UNIPAR hadir sebagai jembatan kolaborasi untuk membangun masa depan bersama melalui kemitraan yang berkelanjutan dalam mendukung Tridharma Perguruan Tinggi dan program MBKM.
                    </p>
                    <div class="d-flex flex-wrap align-items-center gap-3">
                        <a href="#stats" class="btn btn-primary btn-md px-4 py-2 fw-bold shadow-sm d-flex align-items-center gap-2" style="background-color: var(--uni-navy); border-color: var(--uni-navy);">
                            <i class="bi bi-bar-chart-fill"></i> Jelajahi Statistik
                        </a>
                        <a href="#info" class="btn btn-outline-secondary btn-md px-4 py-2 fw-bold bg-white d-flex align-items-center gap-2" style="border-color: #cbd5e1; color: var(--uni-navy);">
                            <i class="bi bi-file-earmark-text"></i> Daftar Dokumen Kerja Sama
                        </a>
                    </div>
                </div>

                <div class="col-lg-4 text-center">
                    <div class="bg-white border rounded-4 p-4 shadow-sm text-start" style="border-color: #e2e8f0;">
                        <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                            <span class="fw-bold text-dark font-size-13 text-uppercase">
                                <i class="bi bi-check-circle-fill text-success me-1"></i> Ringkasan Sistem
                            </span>
                            <span class="badge bg-success-subtle text-success small">Terverifikasi</span>
                        </div>
                        <div class="mb-3">
                            <span class="text-muted small">Total Dokumen Kerja Sama Terdaftar:</span>
                            <h3 class="fw-bold text-dark mt-1 mb-0">{{ $totalDocs }} Naskah</h3>
                        </div>
                        <div class="vstack gap-2 small pt-2 border-top">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted">MoU (Nota Kesepahaman):</span>
                                <strong class="badge bg-primary px-2 py-1">{{ $mouCount }}</strong>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted">MoA (Perjanjian Kerja Sama):</span>
                                <strong class="badge bg-success px-2 py-1">{{ $moaCount }}</strong>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted">IA (Implementation Arrangement):</span>
                                <strong class="badge bg-warning text-dark px-2 py-1">{{ $iaCount }}</strong>
                            </div>
                        </div>
                        <div class="mt-3 pt-2 border-top text-center">
                            <a href="#graphic-stat" class="text-decoration-none small text-primary fw-semibold">
                                Lihat Grafik Analitik <i class="bi bi-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- 1. STATISTIK DOKUMEN KERJA SAMA (SEDERHANA: HANYA MoU, MoA, IA SEPERTI SIKERMA UNJ) -->
    <section class="py-5" id="stats">
        <div class="container px-4 px-lg-5">
            <h2 class="text-center mt-0 fw-bold text-dark">Statistik Dokumen Kerja Sama</h2>
            <p class="text-center text-muted mb-0">Bagian ini memberikan gambaran umum tentang kegiatan kerja sama Universitas Parna Raya.</p>
            <hr class="divider mx-auto my-3" style="width: 50px; height: 3px; background-color: var(--uni-navy); opacity: 1;">
            
            <div class="row gx-4 gx-lg-5 text-center mt-4 justify-content-center">
                <!-- Card MoU -->
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card h-100 shadow-sm border-0 py-4 stat-card-hover rounded-4">
                        <div class="card-body">
                            <h5 class="card-title fw-bold text-dark mb-1">MoU</h5>
                            <p class="card-text text-muted small mb-3">Memorandum of Understanding</p>
                            <h2 class="fw-bold text-primary mb-0 display-5">{{ $mouCount }}</h2>
                        </div>
                    </div>
                </div>

                <!-- Card MoA -->
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card h-100 shadow-sm border-0 py-4 stat-card-hover rounded-4">
                        <div class="card-body">
                            <h5 class="card-title fw-bold text-dark mb-1">MoA</h5>
                            <p class="card-text text-muted small mb-3">Memorandum of Agreement</p>
                            <h2 class="fw-bold text-success mb-0 display-5">{{ $moaCount }}</h2>
                        </div>
                    </div>
                </div>

                <!-- Card IA -->
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card h-100 shadow-sm border-0 py-4 stat-card-hover rounded-4">
                        <div class="card-body">
                            <h5 class="card-title fw-bold text-dark mb-1">IA</h5>
                            <p class="card-text text-muted small mb-3">Implementation Arrangement</p>
                            <h2 class="fw-bold text-warning mb-0 display-5">{{ $iaCount }}</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 2. GRAFIK DOKUMEN: KOMPOSISI, PER TAHUN, UNIT, KLASIFIKASI & KEWARGANEGARAAN (SESUAI SIKERMA UNJ) -->
    <section class="py-5" id="graphic-stat" style="background-color: #f8fafc;">
        <div class="container px-4 px-lg-5">
            <div class="bg-white rounded-4 shadow-sm border p-4 p-lg-5 mb-4">
                <!-- Row 1: Komposisi Jenis Dokumen & Distribusi Per Tahun (5 Tahun Terakhir) -->
                <div class="row g-4 align-items-center">
                    <div class="col-lg-4 col-md-12 mb-4">
                        <h6 class="fw-bold text-dark text-center mb-3">
                            <i class="bi bi-pie-chart-fill text-primary me-1"></i> Komposisi Jenis Dokumen
                        </h6>
                        <div id="document-partnership"></div>
                    </div>
                    <div class="col-lg-8 col-md-12 mb-4">
                        <div id="document-per-year"></div>
                    </div>
                </div>

                <hr class="my-4 text-muted opacity-25">

                <!-- Row 2: Statistik Dokumen Berdasarkan Tahun (Filter Dropdown & Tabs) -->
                <div class="row">
                    <div class="col-12">
                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
                            <div>
                                <h5 class="fw-bold text-dark mb-1">
                                    <i class="bi bi-bar-chart-steps text-primary me-2"></i>Statistik Dokumen Kerja Sama berdasarkan Tahun
                                </h5>
                                <p class="text-muted small mb-0">Pilih tahun untuk menyaring grafik dokumen berdasarkan unit pengusul, klasifikasi mitra, dan kewarganegaraan.</p>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <label for="yearFilterSelect" class="fw-semibold text-secondary small text-nowrap mb-0">Pilih Tahun :</label>
                                <select name="year" id="yearFilterSelect" class="form-select form-select-sm" style="min-width: 140px;">
                                    <option value="all">Semua Tahun</option>
                                    @foreach ($availableYears as $yr)
                                        <option value="{{ $yr }}">{{ $yr }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Nav Tabs (Sesuai Struktur SIKERMA UNJ) -->
                        <ul class="nav nav-pills custom-chart-tabs mb-4" id="statsTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active fw-semibold" id="tab-unit" data-bs-toggle="pill" data-bs-target="#pane-unit" type="button" role="tab">
                                    <i class="bi bi-mortarboard me-1.5"></i> Grafik Dokumen berdasarkan Unit Pengusul
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link fw-semibold" id="tab-cat" data-bs-toggle="pill" data-bs-target="#pane-cat" type="button" role="tab">
                                    <i class="bi bi-tags me-1.5"></i> Grafik Dokumen berdasarkan Klasifikasi Mitra
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link fw-semibold" id="tab-nat" data-bs-toggle="pill" data-bs-target="#pane-nat" type="button" role="tab">
                                    <i class="bi bi-globe2 me-1.5"></i> Grafik Dokumen berdasarkan Kewarganegaraan Mitra
                                </button>
                            </li>
                        </ul>

                        <!-- Tab Panes -->
                        <div class="tab-content" id="statsTabsContent">
                            <!-- Tab 1: Unit Pengusul -->
                            <div class="tab-pane fade show active" id="pane-unit" role="tabpanel">
                                <div class="p-3 border rounded-3 bg-light bg-opacity-50">
                                    <div id="chart-faculty-partnership" style="min-height: 380px;"></div>
                                </div>
                            </div>
                            <!-- Tab 2: Klasifikasi Mitra -->
                            <div class="tab-pane fade" id="pane-cat" role="tabpanel">
                                <div class="p-3 border rounded-3 bg-light bg-opacity-50">
                                    <div id="chart-partner-classification" style="min-height: 380px;"></div>
                                </div>
                            </div>
                            <!-- Tab 3: Kewarganegaraan Mitra -->
                            <div class="tab-pane fade" id="pane-nat" role="tabpanel">
                                <div class="p-3 border rounded-3 bg-light bg-opacity-50">
                                    <div id="chart-partner-nationality" style="min-height: 380px;"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 3. DAFTAR DOKUMEN KERJA SAMA UNIVERSITAS (SESUAI SIKERMA UNJ: TABEL, FILTER, & DETAIL MODAL) -->
    <section class="py-5" id="info" style="background-color: #f9fafb;">
        <div class="container px-4 px-lg-5">
            <h2 class="text-center mt-0 fw-bold text-dark">Daftar Dokumen Kerja Sama Universitas Parna Raya</h2>
            <p class="text-center text-muted mb-0">Bagian ini menyediakan daftar dokumen kerja sama Universitas Parna Raya.</p>
            <hr class="divider mx-auto my-3" style="width: 50px; height: 3px; background-color: var(--uni-navy); opacity: 1;">

            <div class="card border-0 shadow-sm rounded-4 overflow-hidden mt-4">
                <div class="card-body p-3 p-lg-4">
                    <!-- Filter Controls -->
                    <div class="row g-2 mb-3 align-items-end">
                        <div class="col-md-3 col-sm-6">
                            <label class="small text-muted fw-semibold mb-1">Jenis Kerja Sama:</label>
                            <select id="filterJenisDok" class="form-select form-select-sm">
                                <option value="">Semua Jenis</option>
                                <option value="MoU">MoU (Nota Kesepahaman)</option>
                                <option value="MoA">MoA (Perjanjian Kerja Sama)</option>
                                <option value="IA">IA (Implementation Arrangement)</option>
                            </select>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <label class="small text-muted fw-semibold mb-1">Status Naskah:</label>
                            <select id="filterStatusDok" class="form-select form-select-sm">
                                <option value="">Semua Status</option>
                                <option value="ACTIVE">Aktif Sah</option>
                                <option value="VERIFIED">Terverifikasi</option>
                                <option value="REVIEW">Dalam Review</option>
                                <option value="DRAFT">Draf</option>
                                <option value="EXPIRED">Kedaluwarsa / Selesai</option>
                            </select>
                        </div>
                        <div class="col-md-6 col-sm-12">
                            <label class="small text-muted fw-semibold mb-1">Cari Judul / Mitra / Nomor:</label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-white"><i class="bi bi-search text-muted"></i></span>
                                <input type="text" id="searchTableInput" class="form-control" placeholder="Ketik kata kunci untuk memfilter tabel langsung...">
                            </div>
                        </div>
                    </div>

                    <!-- Table Data -->
                    <div class="table-responsive">
                        <table id="table-info-kerjasama" class="table table-sm table-hover table-bordered align-middle w-100" style="font-size: 0.85rem;">
                            <thead style="background-color: var(--uni-navy); color: #ffffff;">
                                <tr>
                                    <th class="text-center" style="width: 4%;">No.</th>
                                    <th class="text-center" style="width: 14%;">Jenis Kerja Sama</th>
                                    <th style="width: 36%;">Judul Kerja Sama</th>
                                    <th class="text-center text-nowrap" style="width: 12%;">Tanggal Mulai</th>
                                    <th class="text-center text-nowrap" style="width: 12%;">Tanggal Berakhir</th>
                                    <th class="text-center" style="width: 12%;">Status</th>
                                    <th class="text-center" style="width: 10%;">Detail</th>
                                </tr>
                            </thead>
                            <tbody id="tableBodyDocuments">
                                @forelse ($allDocuments as $index => $doc)
                                    @php
                                        $typeBadgeClass = 'bg-primary';
                                        $docTypeName = 'MoU';
                                        if (str_contains($doc->doc_type, 'MoA')) {
                                            $typeBadgeClass = 'bg-success';
                                            $docTypeName = 'MoA';
                                        } elseif (str_contains($doc->doc_type, 'IA')) {
                                            $typeBadgeClass = 'bg-warning text-dark';
                                            $docTypeName = 'IA';
                                        }

                                        $statusBadge = '<span class="badge bg-secondary">DRAFT</span>';
                                        if ($doc->status === 'ACTIVE') {
                                            $statusBadge = '<span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1"><i class="bi bi-check-circle-fill me-1"></i>AKTIF</span>';
                                        } elseif ($doc->status === 'VERIFIED') {
                                            $statusBadge = '<span class="badge bg-info-subtle text-info border border-info-subtle px-2 py-1"><i class="bi bi-check2-all me-1"></i>VERIFIED</span>';
                                        } elseif ($doc->status === 'REVIEW') {
                                            $statusBadge = '<span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1"><i class="bi bi-hourglass-split me-1"></i>REVIEW</span>';
                                        } elseif ($doc->status === 'EXPIRED') {
                                            $statusBadge = '<span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2 py-1">KEDALUWARSA</span>';
                                        }
                                    @endphp
                                    <tr data-jenis="{{ $docTypeName }}" data-status="{{ $doc->status }}">
                                        <td class="text-center text-muted fw-bold">{{ $index + 1 }}</td>
                                        <td class="text-center">
                                            <span class="badge {{ $typeBadgeClass }} px-2 py-1" style="font-size: 0.75rem;">
                                                {{ $docTypeName }}
                                            </span>
                                            <div class="text-muted" style="font-size: 0.68rem; margin-top: 2px;">{{ $doc->doc_type }}</div>
                                        </td>
                                        <td>
                                            <div class="fw-bold text-dark" style="font-size: 0.86rem;">
                                                {{ $doc->title ?: ($doc->scope ?: 'Kerja Sama Kemitraan Tridharma') }}
                                            </div>
                                            <div class="text-secondary small mt-0.5">
                                                <i class="bi bi-building me-1 text-primary"></i> <strong>{{ $doc->partner_name }}</strong> 
                                                <span class="text-muted">({{ $doc->unit }})</span>
                                            </div>
                                            <div class="text-muted font-monospace" style="font-size: 0.7rem;">
                                                No: {{ $doc->doc_number ?: '-' }}
                                            </div>
                                        </td>
                                        <td class="text-center text-nowrap">
                                            {{ $doc->start_date ? \Carbon\Carbon::parse($doc->start_date)->format('d/m/Y') : '-' }}
                                        </td>
                                        <td class="text-center text-nowrap">
                                            {{ $doc->end_date ? \Carbon\Carbon::parse($doc->end_date)->format('d/m/Y') : '-' }}
                                        </td>
                                        <td class="text-center text-nowrap">
                                            {!! $statusBadge !!}
                                        </td>
                                        <td class="text-center">
                                            <button type="button" 
                                                    class="btn btn-outline-primary btn-sm px-2.5 py-1 fw-semibold d-inline-flex align-items-center gap-1 btn-detail-doc"
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#modal-detail-kerjasama"
                                                    data-id="{{ $doc->id }}"
                                                    data-docid="{{ $doc->doc_id }}"
                                                    data-regid="{{ $doc->reg_id }}"
                                                    data-title="{{ $doc->title ?: $doc->scope }}"
                                                    data-doctype="{{ $doc->doc_type }}"
                                                    data-docnum="{{ $doc->doc_number ?: '-' }}"
                                                    data-partnerdocnum="{{ $doc->partner_doc_number ?: '-' }}"
                                                    data-partner="{{ $doc->partner_name }}"
                                                    data-category="{{ $doc->partner_category }}"
                                                    data-unit="{{ $doc->unit }}"
                                                    data-status="{{ $doc->status }}"
                                                    data-start="{{ $doc->start_date ? \Carbon\Carbon::parse($doc->start_date)->format('d F Y') : '-' }}"
                                                    data-end="{{ $doc->end_date ? \Carbon\Carbon::parse($doc->end_date)->format('d F Y') : '-' }}"
                                                    data-validity="{{ $doc->validity ?: '-' }}"
                                                    data-scope="{{ $doc->scope ?: '-' }}"
                                                    data-desc="{{ $doc->description ?: '-' }}"
                                                    data-firstsigner="{{ $doc->first_party_signer ?: 'Rektor Universitas Parna Raya' }}"
                                                    data-secondsigner="{{ $doc->second_party_signer ?: 'Pimpinan Mitra' }}"
                                                    title="Lihat Detail Kerja Sama">
                                                <i class="bi bi-eye-fill"></i> Detail
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4 text-muted">
                                            Belum ada dokumen kerja sama yang dipublikasikan.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mt-3 pt-2 border-top text-muted small">
                        <div>
                            Menampilkan <strong><span id="displayedDocCount">{{ count($allDocuments) }}</span></strong> dari total <strong>{{ count($allDocuments) }}</strong> naskah kerja sama
                        </div>
                        <div class="mt-2 mt-md-0">
                            <span class="badge bg-light text-dark border">
                                <i class="bi bi-shield-check text-success me-1"></i> Data Resmi Terverifikasi SIKERMA UNIPAR
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- MODAL DETAIL KERJA SAMA (PERSIS SEPERTI SIKERMA UNJ) -->
    <div class="modal fade" id="modal-detail-kerjasama" tabindex="-1" aria-labelledby="modalDetailKerjasamaLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content rounded-4 border-0 shadow">
                <div class="modal-header text-white" style="background-color: var(--uni-navy); border-bottom: 2px solid var(--uni-blue);">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-file-earmark-text-fill font-size-18 text-warning"></i>
                        <h5 class="modal-title fw-bold" id="modalDetailKerjasamaLabel">Detail Dokumen Kerja Sama</h5>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3 pb-3 border-bottom">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="badge bg-primary px-2.5 py-1 font-monospace" id="modalDetailType">MoU</span>
                            <span class="badge bg-success-subtle text-success border px-2.5 py-1" id="modalDetailStatus">AKTIF</span>
                            <span class="badge bg-light text-dark border font-monospace" id="modalDetailDocId">UNIPAR-2026-001</span>
                        </div>
                        <h5 class="fw-bold text-dark mb-1" id="modalDetailTitle">Judul Kerja Sama</h5>
                        <p class="text-muted small mb-0"><i class="bi bi-building text-primary me-1"></i> Mitra: <strong id="modalDetailPartner">-</strong> (<span id="modalDetailCategory">-</span>)</p>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="p-3 rounded-3 bg-light border">
                                <div class="text-muted small fw-semibold">Nomor Naskah Pihak I (UNIPAR):</div>
                                <div class="fw-bold text-dark font-monospace mt-1" id="modalDetailDocNum">-</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 rounded-3 bg-light border">
                                <div class="text-muted small fw-semibold">Nomor Naskah Pihak II (Mitra):</div>
                                <div class="fw-bold text-dark font-monospace mt-1" id="modalDetailPartnerDocNum">-</div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="small">
                                <span class="text-muted">Unit Pengusul:</span>
                                <div class="fw-bold text-dark mt-0.5" id="modalDetailUnit">-</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="small">
                                <span class="text-muted">Masa Berlaku:</span>
                                <div class="fw-bold text-dark mt-0.5" id="modalDetailValidity">-</div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="small">
                                <span class="text-muted">Tanggal Mulai:</span>
                                <div class="fw-semibold text-dark mt-0.5" id="modalDetailStart">-</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="small">
                                <span class="text-muted">Tanggal Berakhir:</span>
                                <div class="fw-semibold text-dark mt-0.5" id="modalDetailEnd">-</div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="small">
                                <span class="text-muted">Penandatangan Pihak I:</span>
                                <div class="fw-semibold text-dark mt-0.5" id="modalDetailFirstSigner">-</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="small">
                                <span class="text-muted">Penandatangan Pihak II:</span>
                                <div class="fw-semibold text-dark mt-0.5" id="modalDetailSecondSigner">-</div>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="small">
                                <span class="text-muted">Ruang Lingkup Kerja Sama:</span>
                                <div class="p-2.5 rounded-2 bg-light border mt-1" id="modalDetailScope" style="font-size: 0.84rem;">-</div>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="small">
                                <span class="text-muted">Deskripsi / Ringkasan Kemitraan:</span>
                                <div class="p-2.5 rounded-2 bg-light border mt-1" id="modalDetailDesc" style="font-size: 0.84rem;">-</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top d-flex justify-content-between">
                    <div>
                        <a href="#" id="modalVerifyBtn" target="_blank" class="btn btn-outline-success btn-sm fw-bold">
                            <i class="bi bi-qr-code-scan me-1"></i> Periksa Sertifikat & QR Code Publik
                        </a>
                    </div>
                    <button type="button" class="btn btn-secondary btn-sm fw-semibold" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <!-- 4. SECTION KONTAK (SESUAI SIKERMA UNJ) -->
    <section class="py-5" id="contact" style="background-color: #ffffff;">
        <div class="container px-4 px-lg-5">
            <div class="row gx-4 gx-lg-5 justify-content-center">
                <div class="col-lg-8 text-center">
                    <h2 class="mt-0 fw-bold text-dark">Kami Menyambut Pertanyaan Anda</h2>
                    <hr class="divider mx-auto my-3" style="width: 50px; height: 3px; background-color: var(--uni-navy); opacity: 1;">
                    <p class="text-muted mb-5">Jika Anda tertarik untuk memulai kerja sama baru dengan kami, silakan hubungi Biro Kerja Sama Universitas Parna Raya dan kami akan merespons secepatnya.</p>
                </div>
            </div>

            <div class="row g-4 align-items-stretch">
                <!-- Info Kolom Kontak -->
                <div class="col-lg-6">
                    <div class="bg-light p-4 rounded-4 border h-100">
                        <div class="mb-4">
                            <h5 class="fw-bold text-dark mb-1"><i class="bi bi-geo-alt-fill text-danger me-2"></i>Alamat Kantor</h5>
                            <p class="text-muted small mb-0">
                                Biro Kerja Sama & Urusan Internasional (BKUI)<br>
                                Gedung Rektorat Lt. 2, Kampus Terpadu Universitas Parna Raya<br>
                                Jl. Sam Ratulangi No. 1, Kota Manado, Sulawesi Utara 95111
                            </p>
                        </div>

                        <div class="mb-4">
                            <h5 class="fw-bold text-dark mb-1"><i class="bi bi-clock-fill text-primary me-2"></i>Jam Operasional</h5>
                            <p class="text-muted small mb-0">
                                Senin - Jumat: 08:00 - 16:00 WITA<br>
                                Sabtu, Minggu & Hari Libur Nasional: Tutup
                            </p>
                        </div>

                        <div class="mb-4">
                            <h5 class="fw-bold text-dark mb-1"><i class="bi bi-telephone-fill text-success me-2"></i>Kontak & Email Resmi</h5>
                            <p class="text-muted small mb-1"><i class="bi bi-telephone me-1.5 text-secondary"></i> Telepon: (0431) 880-1234 / 880-5678</p>
                            <p class="text-muted small mb-0"><i class="bi bi-envelope me-1.5 text-secondary"></i> Email: kerjasama@unipar.ac.id | info@unipar.ac.id</p>
                        </div>

                        <div class="pt-2">
                            @if (!auth()->check())
                                <a href="{{ route('login') }}" class="btn btn-outline-dark btn-sm fw-bold">
                                    <i class="bi bi-shield-lock me-1"></i> Login Portal Internal Kampus
                                </a>
                            @else
                                <a href="{{ route('simkerma.index') }}" class="btn btn-dark btn-sm fw-bold">
                                    <i class="bi bi-speedometer2 me-1"></i> Buka Aplikasi SIM-KERMA
                                </a>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Kolom Peta Kampus -->
                <div class="col-lg-6">
                    <div class="rounded-4 overflow-hidden border shadow-sm h-100" style="min-height: 320px;">
                        <iframe 
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3988.484556488737!2d124.8385!3d1.4748!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zMcKwMjgnMjkuMyJOIDEyNMKwNTAnMTguNiJF!5e0!3m2!1sid!2sid!4v1700000000000!5m2!1sid!2sid" 
                            width="100%" 
                            height="100%" 
                            style="border:0; min-height: 320px;" 
                            allowfullscreen="" 
                            loading="lazy" 
                            referrerpolicy="no-referrer-when-downgrade">
                        </iframe>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FOOTER -->
    <footer class="uni-footer">
        <div class="container px-4 px-lg-5">
            <div class="row g-4">
                <div class="col-lg-5">
                    <div class="d-flex align-items-center gap-2.5 mb-3">
                        <div class="brand-emblem" style="width: 38px; height: 38px; font-size: 1.15rem;">
                            <i class="bi bi-buildings"></i>
                        </div>
                        <div>
                            <div class="fw-bold text-white font-size-15">UNIVERSITAS PARNA RAYA</div>
                            <div class="text-muted" style="font-size: 0.7rem; font-weight: 600;">SIKERMA UNIPAR</div>
                        </div>
                    </div>
                    <p class="text-light text-opacity-60 small pe-lg-4 mb-3">
                        Sistem Informasi Manajemen Kerja Sama (SIKERMA) Universitas Parna Raya dirancang untuk memfasilitasi tata kelola kemitraan Tridharma yang akuntabel, transparan, dan mendukung akselerasi Indikator Kinerja Utama (IKU 6) Kemendikbudristek.
                    </p>
                    <div class="small text-light text-opacity-75">
                        <i class="bi bi-geo-alt-fill text-warning me-1"></i> Manado, Sulawesi Utara, Indonesia
                    </div>
                </div>

                <div class="col-6 col-lg-3">
                    <div class="footer-heading">Navigasi Portal</div>
                    <a href="#page-top" class="footer-link">Beranda</a>
                    <a href="#stats" class="footer-link">Statistik</a>
                    <a href="#graphic-stat" class="footer-link">Grafik Kemitraan</a>
                    <a href="#info" class="footer-link">Dokumen Kerja Sama</a>
                    <a href="#contact" class="footer-link">Kontak</a>
                </div>

                <div class="col-6 col-lg-4">
                    <div class="footer-heading">Biro Kerja Sama UNIPAR</div>
                    <div class="small text-light text-opacity-75 mb-2">
                        Gedung Rektorat Lt. 2, Kampus Terpadu UNIPAR<br>
                        Kota Manado, Sulawesi Utara
                    </div>
                    <div class="small text-light text-opacity-75 mb-1">
                        <i class="bi bi-envelope me-1.5 text-warning"></i> kerjasama@unipar.ac.id
                    </div>
                    <div class="small text-light text-opacity-75 mb-3">
                        <i class="bi bi-telephone me-1.5 text-warning"></i> (0431) 880-1234
                    </div>
                    @if (!auth()->check())
                        <a href="{{ route('login') }}" class="btn btn-outline-light btn-sm fw-semibold">
                            <i class="bi bi-shield-lock me-1"></i> Login Internal Kampus
                        </a>
                    @else
                        <a href="{{ route('simkerma.index') }}" class="btn btn-outline-light btn-sm fw-semibold">
                            <i class="bi bi-speedometer2 me-1"></i> Dashboard SIM-KERMA
                        </a>
                    @endif
                </div>
            </div>

            <div class="footer-bottom d-flex flex-column flex-md-row justify-content-between align-items-center gap-2">
                <div>
                    © 2026 Universitas Parna Raya (UNIPAR). Seluruh Hak Cipta Dilindungi Undang-Undang.
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

    <!-- SCRIPTS GRAFIK & INTERAKTIVITAS (PERSIS SEPERTI SIKERMA UNJ) -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // ==========================================
            // 1. DONUT CHART: KOMPOSISI JENIS DOKUMEN
            // ==========================================
            const donutLabels = @json($chartTypeLabels);
            const donutSeries = @json($chartTypeSeries);

            const optionsDonut = {
                chart: {
                    type: 'donut',
                    height: 360,
                    fontFamily: 'Plus Jakarta Sans, sans-serif'
                },
                series: donutSeries,
                labels: donutLabels,
                colors: ['#0d6efd', '#198754', '#ffc107'],
                legend: {
                    show: true,
                    position: 'bottom',
                    fontSize: '13px'
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
                            size: '62%',
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
                            return val + " Dokumen";
                        }
                    }
                }
            };
            const chartDonut = new ApexCharts(document.querySelector("#document-partnership"), optionsDonut);
            chartDonut.render();

            // ==========================================
            // 2. BAR CHART: DISTRIBUSI PER TAHUN (5 TAHUN)
            // ==========================================
            const yearsRange = @json($yearsRange);
            const docsPerYearSeries = @json($docsPerYearSeries);

            const optionsBarYear = {
                chart: {
                    type: 'bar',
                    height: 360,
                    toolbar: { show: false },
                    fontFamily: 'Plus Jakarta Sans, sans-serif'
                },
                title: {
                    text: "Grafik Distribusi Dokumen Kerja Sama Per Tahun (5 Tahun Terakhir)",
                    align: 'center',
                    style: {
                        fontSize: '13px',
                        fontWeight: '700',
                        color: '#0b1f3a'
                    }
                },
                series: docsPerYearSeries,
                colors: ['#0d6efd', '#198754', '#ffc107'],
                plotOptions: {
                    bar: {
                        horizontal: false,
                        columnWidth: '50%',
                        borderRadius: 4
                    }
                },
                dataLabels: {
                    enabled: true,
                    offsetY: -18,
                    style: {
                        fontSize: '11px',
                        colors: ["#334155"]
                    }
                },
                stroke: {
                    show: true,
                    width: 2,
                    colors: ['transparent']
                },
                xaxis: {
                    categories: yearsRange
                },
                yaxis: {
                    title: { text: 'Jumlah Dokumen' },
                    tickAmount: 4
                },
                fill: { opacity: 1 },
                tooltip: {
                    y: {
                        formatter: function (val) {
                            return val + " Kerjasama";
                        }
                    }
                },
                legend: {
                    position: 'bottom'
                }
            };
            const chartBarYear = new ApexCharts(document.querySelector("#document-per-year"), optionsBarYear);
            chartBarYear.render();

            // ==========================================================
            // 3. STATISTIK INTERAKTIF BERDASARKAN TAHUN (APEXCHARTS)
            //    - Unit Pengusul
            //    - Klasifikasi Mitra
            //    - Kewarganegaraan Mitra
            // ==========================================================
            const allYearStats = @json($interactiveStats);

            // A. Chart Unit Pengusul (Horizontal Bar)
            let chartFaculty;
            const optionsFaculty = {
                chart: {
                    type: 'bar',
                    height: 380,
                    toolbar: { show: false },
                    fontFamily: 'Plus Jakarta Sans, sans-serif'
                },
                plotOptions: {
                    bar: {
                        horizontal: true,
                        borderRadius: 4,
                        barHeight: '55%'
                    }
                },
                colors: ['#0d6efd'],
                series: [{
                    name: 'Jumlah Dokumen',
                    data: (allYearStats['all'] && allYearStats['all'].units) ? allYearStats['all'].units.series : []
                }],
                xaxis: {
                    categories: (allYearStats['all'] && allYearStats['all'].units) ? allYearStats['all'].units.labels : [],
                    title: { text: 'Jumlah Dokumen Kerja Sama' },
                    tickAmount: 4
                },
                yaxis: {
                    title: { text: 'Unit Pengusul / Fakultas' }
                },
                dataLabels: {
                    enabled: true,
                    style: { fontSize: '11px' },
                    formatter: function (val) {
                        return val + " Naskah";
                    }
                },
                tooltip: {
                    y: {
                        formatter: function (val) {
                            return val + " Dokumen";
                        }
                    }
                }
            };
            chartFaculty = new ApexCharts(document.querySelector("#chart-faculty-partnership"), optionsFaculty);
            chartFaculty.render();

            // B. Chart Klasifikasi Mitra (Horizontal Bar)
            let chartClassification;
            const optionsClassification = {
                chart: {
                    type: 'bar',
                    height: 380,
                    toolbar: { show: false },
                    fontFamily: 'Plus Jakarta Sans, sans-serif'
                },
                plotOptions: {
                    bar: {
                        horizontal: true,
                        borderRadius: 4,
                        barHeight: '55%'
                    }
                },
                colors: ['#e67343'],
                series: [{
                    name: 'Jumlah Mitra',
                    data: (allYearStats['all'] && allYearStats['all'].categories) ? allYearStats['all'].categories.series : []
                }],
                xaxis: {
                    categories: (allYearStats['all'] && allYearStats['all'].categories) ? allYearStats['all'].categories.labels : [],
                    title: { text: 'Jumlah Mitra Kerja Sama' },
                    tickAmount: 4
                },
                yaxis: {
                    title: { text: 'Klasifikasi Mitra' }
                },
                dataLabels: {
                    enabled: true,
                    style: { fontSize: '11px' },
                    formatter: function (val) {
                        return val + " Mitra";
                    }
                },
                tooltip: {
                    y: {
                        formatter: function (val) {
                            return val + " Mitra Kerja Sama";
                        }
                    }
                }
            };
            chartClassification = new ApexCharts(document.querySelector("#chart-partner-classification"), optionsClassification);
            chartClassification.render();

            // C. Chart Kewarganegaraan Mitra (Horizontal Bar)
            let chartNationality;
            const optionsNationality = {
                chart: {
                    type: 'bar',
                    height: 380,
                    toolbar: { show: false },
                    fontFamily: 'Plus Jakarta Sans, sans-serif'
                },
                plotOptions: {
                    bar: {
                        horizontal: true,
                        borderRadius: 4,
                        barHeight: '50%'
                    }
                },
                colors: ['#9a55ed'],
                series: [{
                    name: 'Jumlah Mitra',
                    data: (allYearStats['all'] && allYearStats['all'].nationalities) ? allYearStats['all'].nationalities.series : []
                }],
                xaxis: {
                    categories: (allYearStats['all'] && allYearStats['all'].nationalities) ? allYearStats['all'].nationalities.labels : [],
                    title: { text: 'Jumlah Mitra Kerja Sama' },
                    tickAmount: 4
                },
                yaxis: {
                    title: { text: 'Asal Negara / Kewarganegaraan' }
                },
                dataLabels: {
                    enabled: true,
                    style: { fontSize: '11px' },
                    formatter: function (val) {
                        return val + " Mitra";
                    }
                },
                tooltip: {
                    y: {
                        formatter: function (val) {
                            return val + " Mitra Kerja Sama";
                        }
                    }
                }
            };
            chartNationality = new ApexCharts(document.querySelector("#chart-partner-nationality"), optionsNationality);
            chartNationality.render();

            // Handle Filter Tahun Dropdown Change
            const yearSelect = document.getElementById('yearFilterSelect');
            if (yearSelect) {
                yearSelect.addEventListener('change', function () {
                    const selYear = this.value;
                    const dataObj = allYearStats[selYear] || allYearStats['all'];

                    if (dataObj && chartFaculty && chartClassification && chartNationality) {
                        // Update Unit
                        chartFaculty.updateOptions({
                            xaxis: { categories: dataObj.units.labels },
                            series: [{ data: dataObj.units.series }]
                        });

                        // Update Classification
                        chartClassification.updateOptions({
                            xaxis: { categories: dataObj.categories.labels },
                            series: [{ data: dataObj.categories.series }]
                        });

                        // Update Nationality
                        chartNationality.updateOptions({
                            xaxis: { categories: dataObj.nationalities.labels },
                            series: [{ data: dataObj.nationalities.series }]
                        });
                    }
                });
            }

            // Tab Shown Event Listener (Resize charts to prevent glitch on hidden tabs)
            const statsTabs = document.getElementById('statsTabs');
            if (statsTabs) {
                statsTabs.addEventListener('shown.bs.tab', function () {
                    window.dispatchEvent(new Event('resize'));
                });
            }

            // ==========================================================
            // 4. DAFTAR DOKUMEN TABLE: FILTER INTERAKTIF & SEARCH
            // ==========================================================
            const filterJenis = document.getElementById('filterJenisDok');
            const filterStatus = document.getElementById('filterStatusDok');
            const searchInput = document.getElementById('searchTableInput');
            const tableRows = document.querySelectorAll('#tableBodyDocuments tr');
            const countDisplay = document.getElementById('displayedDocCount');

            function applyTableFilters() {
                const jenisVal = (filterJenis ? filterJenis.value : '').toLowerCase().trim();
                const statusVal = (filterStatus ? filterStatus.value : '').toUpperCase().trim();
                const searchVal = (searchInput ? searchInput.value : '').toLowerCase().trim();

                let visibleCount = 0;

                tableRows.forEach(row => {
                    const rowJenis = (row.getAttribute('data-jenis') || '').toLowerCase();
                    const rowStatus = (row.getAttribute('data-status') || '').toUpperCase();
                    const rowText = row.innerText.toLowerCase();

                    let matchJenis = (!jenisVal || rowJenis === jenisVal);
                    let matchStatus = (!statusVal || rowStatus === statusVal);
                    let matchSearch = (!searchVal || rowText.includes(searchVal));

                    if (matchJenis && matchStatus && matchSearch) {
                        row.style.display = '';
                        visibleCount++;
                    } else {
                        row.style.display = 'none';
                    }
                });

                if (countDisplay) {
                    countDisplay.innerText = visibleCount;
                }
            }

            if (filterJenis) filterJenis.addEventListener('change', applyTableFilters);
            if (filterStatus) filterStatus.addEventListener('change', applyTableFilters);
            if (searchInput) searchInput.addEventListener('keyup', applyTableFilters);

            // ==========================================================
            // 5. MODAL DETAIL DOKUMEN KERJA SAMA (POPULATE ON CLICK)
            // ==========================================================
            const detailButtons = document.querySelectorAll('.btn-detail-doc');
            detailButtons.forEach(btn => {
                btn.addEventListener('click', function () {
                    document.getElementById('modalDetailTitle').innerText = this.getAttribute('data-title') || '-';
                    document.getElementById('modalDetailType').innerText = this.getAttribute('data-doctype') || '-';
                    document.getElementById('modalDetailStatus').innerText = this.getAttribute('data-status') || '-';
                    document.getElementById('modalDetailDocId').innerText = this.getAttribute('data-docid') || '-';
                    document.getElementById('modalDetailPartner').innerText = this.getAttribute('data-partner') || '-';
                    document.getElementById('modalDetailCategory').innerText = this.getAttribute('data-category') || '-';
                    document.getElementById('modalDetailDocNum').innerText = this.getAttribute('data-docnum') || '-';
                    document.getElementById('modalDetailPartnerDocNum').innerText = this.getAttribute('data-partnerdocnum') || '-';
                    document.getElementById('modalDetailUnit').innerText = this.getAttribute('data-unit') || '-';
                    document.getElementById('modalDetailValidity').innerText = this.getAttribute('data-validity') || '-';
                    document.getElementById('modalDetailStart').innerText = this.getAttribute('data-start') || '-';
                    document.getElementById('modalDetailEnd').innerText = this.getAttribute('data-end') || '-';
                    document.getElementById('modalDetailFirstSigner').innerText = this.getAttribute('data-firstsigner') || '-';
                    document.getElementById('modalDetailSecondSigner').innerText = this.getAttribute('data-secondsigner') || '-';
                    document.getElementById('modalDetailScope').innerText = this.getAttribute('data-scope') || '-';
                    document.getElementById('modalDetailDesc').innerText = this.getAttribute('data-desc') || '-';

                    // Update Verify Link
                    const regId = this.getAttribute('data-regid');
                    const verifyBtn = document.getElementById('modalVerifyBtn');
                    if (verifyBtn && regId) {
                        verifyBtn.href = '/verify/' + regId;
                        verifyBtn.style.display = 'inline-flex';
                    } else if (verifyBtn) {
                        verifyBtn.style.display = 'none';
                    }
                });
            });
        });
    </script>
</body>
</html>
