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
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap CSS (Minia BS 5.0.2) -->
    <link href="{{ asset('partial/css/bootstrap.min.css') }}" rel="stylesheet">
    <!-- Icons CSS -->
    <link href="{{ asset('partial/css/icons.min.css') }}" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- ApexCharts CSS -->
    <link href="{{ asset('partial/libs/apexcharts/apexcharts.css') }}" rel="stylesheet">

    <style>
        :root {
            --uni-navy: #0b1f3a;
            --uni-navy-dark: #071529;
            --uni-navy-soft: #eef2f8;
            --uni-teal: #0f766e;
            --uni-amber: #92400e;
            --uni-gold: #d97706;
            --uni-border: #cbd5e1;
            --uni-border-soft: #e2e8f0;
            --uni-bg: #f4f6f9;
            --uni-text: #1e293b;
            --uni-muted: #475569;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background-color: var(--uni-bg);
            color: var(--uni-text);
            font-size: 0.92rem;
            line-height: 1.55;
        }

        /* Kontras WCAG 2.1 AA atas Bootstrap Minia */
        .text-muted,
        .text-secondary {
            color: #475569 !important;
        }
        .text-primary {
            color: #1e40af !important;
        }
        .text-success {
            color: #0f766e !important;
        }
        .btn-uni-primary {
            background-color: var(--uni-navy);
            border-color: var(--uni-navy);
            color: #ffffff !important;
        }
        .btn-uni-primary:hover,
        .btn-uni-primary:focus {
            background-color: #143258;
            border-color: #143258;
            color: #ffffff !important;
        }
        .btn-uni-outline {
            background-color: #ffffff;
            border: 1px solid var(--uni-navy);
            color: var(--uni-navy) !important;
        }
        .btn-uni-outline:hover,
        .btn-uni-outline:focus {
            background-color: var(--uni-navy);
            color: #ffffff !important;
        }

        /* Navbar Rapi 1 Baris */
        .uni-navbar {
            background-color: #ffffff;
            border-bottom: 1px solid var(--uni-border-soft);
            padding: 10px 0;
            box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04);
        }
        .brand-logo-area {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            flex-shrink: 0;
        }
        .brand-text h1 {
            font-size: 0.98rem;
            font-weight: 800;
            color: var(--uni-navy);
            margin: 0;
            line-height: 1.2;
            white-space: nowrap;
        }
        .brand-text p {
            font-size: 0.75rem;
            color: var(--uni-muted);
            margin: 0;
            font-weight: 600;
            white-space: nowrap;
        }
        .uni-nav-links {
            display: flex;
            align-items: center;
            gap: 4px;
        }
        .nav-link-custom {
            color: #334155;
            font-size: 0.85rem;
            font-weight: 600;
            padding: 7px 12px;
            border-radius: 4px;
            text-decoration: none;
            white-space: nowrap;
            display: inline-flex;
            align-items: center;
        }
        .nav-link-custom:hover,
        .nav-link-custom:focus {
            color: var(--uni-navy);
            background-color: var(--uni-navy-soft);
        }
        .nav-actions {
            flex-shrink: 0;
        }
        .nav-actions .btn {
            white-space: nowrap;
            font-size: 0.82rem;
            padding: 6px 13px;
            border-radius: 4px;
        }

        /* Banner Masthead Kampus Klasik (Ringkas, Solid Navy, Non-AI) */
        .uni-banner {
            background-color: var(--uni-navy);
            background-image: linear-gradient(135deg, var(--uni-navy-dark) 0%, #122c50 100%);
            color: #ffffff;
            padding: 40px 0;
            border-bottom: 3px solid var(--uni-gold);
        }
        .uni-banner .btn-outline-light {
            color: #ffffff !important;
            border-color: rgba(255, 255, 255, 0.55) !important;
        }
        .uni-banner .btn-outline-light:hover {
            background-color: #ffffff !important;
            color: var(--uni-navy) !important;
        }
        .uni-banner-title {
            font-size: 1.85rem;
            font-weight: 800;
            letter-spacing: -0.02em;
            color: #ffffff;
            margin-bottom: 6px;
        }
        .uni-banner-sub {
            font-size: 0.95rem;
            color: #cbd5e1;
            margin-bottom: 0;
        }
        .verify-inline-box {
            background-color: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 6px;
            padding: 14px;
        }
        .verify-inline-label {
            font-size: 0.78rem;
            font-weight: 600;
            color: #e2e8f0;
            margin-bottom: 6px;
            display: block;
        }
        .verify-inline-btn {
            background-color: #f59e0b;
            border-color: #f59e0b;
            color: #0b1f3a !important;
            font-weight: 700;
        }
        .verify-inline-btn:hover {
            background-color: #fbbf24;
            border-color: #fbbf24;
            color: #0b1f3a !important;
        }
        .verify-sample-link {
            font-size: 0.75rem;
            color: #fde68a;
            background: transparent;
            border: none;
            padding: 0;
            text-decoration: underline;
            cursor: pointer;
        }

        /* Kartu Statistik Ringkas (#stats) */
        .stat-card-clean {
            background-color: #ffffff;
            border: 1px solid var(--uni-border-soft);
            border-radius: 6px;
            padding: 18px 20px;
            height: 100%;
            transition: border-color 0.15s ease;
        }
        .stat-card-clean:hover {
            border-color: #94a3b8;
        }
        .stat-card-label {
            font-size: 0.82rem;
            font-weight: 700;
            color: var(--uni-muted);
            text-transform: uppercase;
            letter-spacing: 0.03em;
        }
        .stat-card-val {
            font-size: 2rem;
            font-weight: 800;
            line-height: 1.15;
            margin: 6px 0 4px 0;
        }
        .stat-card-sub {
            font-size: 0.8rem;
            color: var(--uni-muted);
        }

        /* Chart Tabs */
        .custom-chart-tabs {
            border-bottom: 1px solid var(--uni-border-soft);
            padding-bottom: 10px;
            gap: 6px;
        }
        .custom-chart-tabs .nav-link {
            color: var(--uni-muted);
            background-color: #f1f5f9;
            border-radius: 4px;
            padding: 7px 14px;
            font-size: 0.83rem;
            font-weight: 600;
            border: 1px solid var(--uni-border-soft);
        }
        .custom-chart-tabs .nav-link:hover {
            color: var(--uni-navy);
            background-color: #e2e8f0;
        }
        .custom-chart-tabs .nav-link.active {
            color: #ffffff;
            background-color: var(--uni-navy);
            border-color: var(--uni-navy);
        }

        /* Badge Status & Jenis Kompatibel Bootstrap 5.0.2 */
        .badge-home {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-size: 0.75rem;
            font-weight: 700;
            padding: 3px 8px;
            border-radius: 4px;
            line-height: 1.25;
        }
        .badge-home-mou { background-color: #dbeafe; color: #1e3a8a; border: 1px solid #bfdbfe; }
        .badge-home-moa { background-color: #ccfbf1; color: #115e59; border: 1px solid #99f6e4; }
        .badge-home-ia  { background-color: #fef3c7; color: #92400e; border: 1px solid #fde68a; }

        .badge-st-active   { background-color: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
        .badge-st-verified { background-color: #dbeafe; color: #1e40af; border: 1px solid #bfdbfe; }
        .badge-st-review   { background-color: #fef3c7; color: #92400e; border: 1px solid #fde68a; }
        .badge-st-draft    { background-color: #f1f5f9; color: #334155; border: 1px solid #cbd5e1; }
        .badge-st-expired  { background-color: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }

        /* Tabel Klasik (Serasi Menu 3 & Menu 4) */
        #table-info-kerjasama {
            border-color: var(--uni-border-soft);
        }
        #table-info-kerjasama thead th {
            font-size: 0.8rem;
            font-weight: 700;
            padding: 10px;
            border-color: var(--uni-border-soft);
            white-space: nowrap;
            color: var(--uni-navy);
        }
        #table-info-kerjasama tbody td {
            font-size: 0.82rem;
            padding: 10px;
            border-color: var(--uni-border-soft);
        }
        .pagination-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
            font-size: 0.82rem;
            margin-top: 14px;
            padding-top: 12px;
            border-top: 1px solid var(--uni-border-soft);
        }
        .home-page-btn {
            min-width: 30px;
            height: 30px;
            padding: 0 8px;
            font-size: 0.8rem;
            font-weight: 600;
            border-radius: 4px;
            border: 1px solid var(--uni-border);
            background-color: #ffffff;
            color: var(--uni-navy);
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        .home-page-btn:hover:not(:disabled) {
            background-color: var(--uni-navy-soft);
            border-color: var(--uni-navy);
        }
        .home-page-btn.active {
            background-color: var(--uni-navy);
            border-color: var(--uni-navy);
            color: #ffffff;
        }
        .home-page-btn:disabled {
            opacity: 0.45;
            cursor: not-allowed;
        }

        /* Footer Ringkas */
        .uni-footer {
            background-color: var(--uni-navy);
            color: #cbd5e1;
            padding: 28px 0 20px 0;
            border-top: 3px solid var(--uni-gold);
            font-size: 0.83rem;
        }
        .uni-footer a {
            color: #e2e8f0;
            text-decoration: none;
        }
        .uni-footer a:hover {
            color: #ffffff;
            text-decoration: underline;
        }
    </style>
</head>
<body id="page-top">
    @php
        $mouActiveCount = $allDocuments->filter(fn($d) => stripos((string)$d->doc_type, 'MoU') !== false && $d->status === 'ACTIVE')->count();
        $moaActiveCount = $allDocuments->filter(fn($d) => stripos((string)$d->doc_type, 'MoA') !== false && $d->status === 'ACTIVE')->count();
        $iaActiveCount  = $allDocuments->filter(fn($d) => stripos((string)$d->doc_type, 'IA') !== false && $d->status === 'ACTIVE')->count();
        $totalActiveCount = $allDocuments->where('status', 'ACTIVE')->count();

        $sampleVerifyDoc = $allDocuments->first(fn($d) => $d->status === 'ACTIVE' && !empty($d->reg_id)) ?: $allDocuments->first();
        $sampleRegCode = $sampleVerifyDoc ? ($sampleVerifyDoc->reg_id ?: $sampleVerifyDoc->doc_id) : 'UNIPAR-2023-055';
    @endphp

    <!-- MAIN NAVBAR -->
    <nav class="uni-navbar sticky-top">
        <div class="container d-flex align-items-center justify-content-between gap-2">
            <a href="#page-top" class="brand-logo-area">
                <svg viewBox="0 0 40 40" width="38" height="38" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                    <path d="M20 2L36 8V20.5C36 29.5 29.2 35.8 20 38.5C10.8 35.8 4 29.5 4 20.5V8L20 2Z" fill="#0b1f3a"/>
                    <path d="M20 4.5L33.5 9.6V20.4C33.5 28 27.8 33.4 20 35.8C12.2 33.4 6.5 28 6.5 20.4V9.6L20 4.5Z" fill="none" stroke="#d97706" stroke-width="1.3"/>
                    <path d="M13.5 15H26.5M16 19.5H24M20 12V28" stroke="#ffffff" stroke-width="1.8" stroke-linecap="round"/>
                </svg>
                <div class="brand-text">
                    <h1>UNIVERSITAS PARNA RAYA</h1>
                    <p>SIKERMA &bull; Sistem Informasi Kerja Sama</p>
                </div>
            </a>

            <div class="d-none d-lg-flex uni-nav-links">
                <a href="#page-top" class="nav-link-custom">Beranda</a>
                <a href="#stats" class="nav-link-custom">Statistik</a>
                <a href="#graphic-stat" class="nav-link-custom">Grafik</a>
                <a href="#info" class="nav-link-custom">Dokumen</a>
                <a href="#contact" class="nav-link-custom">Kontak</a>
            </div>

            <div class="d-flex align-items-center gap-2 nav-actions">
                @if (auth()->check())
                    <a href="{{ route('simkerma.index') }}" class="btn btn-uni-primary btn-sm fw-bold">
                        <i class="bi bi-speedometer2 me-1"></i> Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-uni-outline btn-sm fw-semibold">
                        Masuk
                    </a>
                    <a href="{{ route('simkerma.index') }}" class="btn btn-uni-primary btn-sm fw-bold">
                        Portal SIM-KERMA
                    </a>
                @endif
            </div>
        </div>
    </nav>

    <!-- BANNER PORTAL RESMI (RINGKAS, TANPA COPYWRITING AI) -->
    <header class="uni-banner">
        <div class="container">
            <div class="row align-items-center g-4">
                <div class="col-lg-7">
                    <h2 class="uni-banner-title">Sistem Informasi Kerja Sama UNIPAR</h2>
                    <p class="uni-banner-sub">Direktori publik dokumen MoU, MoA/PKS, dan IA Universitas Parna Raya Manado.</p>
                    <div class="d-flex flex-wrap gap-2 mt-3">
                        <a href="#info" class="btn btn-light btn-sm fw-bold px-3 py-2" style="color: var(--uni-navy);">
                            Daftar Dokumen ({{ $totalDocs }})
                        </a>
                        <a href="{{ route('simkerma.index', ['menu' => 4, 'view' => 'new']) }}" class="btn btn-outline-light btn-sm fw-semibold px-3 py-2">
                            Ajukan Kerja Sama
                        </a>
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="verify-inline-box">
                        <form action="{{ route('simkerma.verify') }}" method="GET">
                            <label for="homeVerifyInput" class="verify-inline-label">
                                <i class="bi bi-qr-code-scan me-1"></i> Verifikasi Nomor Registrasi / ID Dokumen
                            </label>
                            <div class="input-group input-group-sm">
                                <input type="text" name="reg" id="homeVerifyInput" class="form-control" placeholder="Contoh: {{ $sampleRegCode }}" required>
                                <button type="submit" class="btn verify-inline-btn px-3">Periksa</button>
                            </div>
                        </form>
                        <div class="mt-2 d-flex align-items-center justify-content-between">
                            <button type="button" class="verify-sample-link" onclick="fillSampleVerify('{{ $sampleRegCode }}')">
                                Isi contoh: {{ $sampleRegCode }}
                            </button>
                            <span style="font-size: 0.75rem; color: #cbd5e1;">Biro Kerja Sama (BKUI)</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- 1. STATISTIK RINGKAS -->
    <section class="pt-4 pb-3" id="stats">
        <div class="container">
            <div class="row g-3">
                <div class="col-md-3 col-6">
                    <div class="stat-card-clean">
                        <div class="stat-card-label">Total Dokumen</div>
                        <div class="stat-card-val" style="color: var(--uni-navy);">{{ $totalDocs }}</div>
                        <div class="stat-card-sub">{{ $totalActiveCount }} berstatus aktif</div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="stat-card-clean">
                        <div class="stat-card-label">MoU</div>
                        <div class="stat-card-val" style="color: #1e40af;">{{ $mouCount }}</div>
                        <div class="stat-card-sub">Nota Kesepahaman ({{ $mouActiveCount }} aktif)</div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="stat-card-clean">
                        <div class="stat-card-label">MoA / PKS</div>
                        <div class="stat-card-val" style="color: var(--uni-teal);">{{ $moaCount }}</div>
                        <div class="stat-card-sub">Perjanjian Kerja Sama ({{ $moaActiveCount }} aktif)</div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="stat-card-clean">
                        <div class="stat-card-label">IA</div>
                        <div class="stat-card-val" style="color: var(--uni-amber);">{{ $iaCount }}</div>
                        <div class="stat-card-sub">Implementation ({{ $iaActiveCount }} aktif)</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 2. GRAFIK KEMITRAAN -->
    <section class="py-3" id="graphic-stat">
        <div class="container">
            <div class="card border shadow-sm">
                <div class="card-body p-4">
                    <div class="row g-4 align-items-center">
                        <div class="col-lg-4">
                            <h3 class="h6 fw-bold text-center text-dark mb-3">Komposisi Dokumen</h3>
                            <div id="document-partnership"></div>
                        </div>
                        <div class="col-lg-8">
                            <div id="document-per-year"></div>
                        </div>
                    </div>

                    <hr class="my-4" style="border-color: var(--uni-border-soft); opacity: 1;">

                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mb-3">
                        <h3 class="h6 fw-bold text-dark mb-0">Sebaran Kemitraan per Tahun</h3>
                        <div class="d-flex align-items-center gap-2">
                            <label for="yearFilterSelect" class="fw-semibold text-dark text-nowrap mb-0" style="font-size: 0.82rem;">Tahun:</label>
                            <select name="year" id="yearFilterSelect" class="form-select form-select-sm" style="min-width: 135px;">
                                <option value="all">Semua Tahun</option>
                                @foreach ($availableYears as $yr)
                                    <option value="{{ $yr }}">{{ $yr }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <ul class="nav nav-pills custom-chart-tabs mb-3" id="statsTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="tab-unit" data-bs-toggle="pill" data-bs-target="#pane-unit" type="button" role="tab">Unit Pengusul</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="tab-cat" data-bs-toggle="pill" data-bs-target="#pane-cat" type="button" role="tab">Klasifikasi Mitra</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="tab-nat" data-bs-toggle="pill" data-bs-target="#pane-nat" type="button" role="tab">Asal Mitra</button>
                        </li>
                    </ul>

                    <div class="tab-content" id="statsTabsContent">
                        <div class="tab-pane fade show active" id="pane-unit" role="tabpanel">
                            <div class="p-3 border rounded" style="background-color: #f8fafc;">
                                <div id="chart-faculty-partnership" style="min-height: 340px;"></div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="pane-cat" role="tabpanel">
                            <div class="p-3 border rounded" style="background-color: #f8fafc;">
                                <div id="chart-partner-classification" style="min-height: 340px;"></div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="pane-nat" role="tabpanel">
                            <div class="p-3 border rounded" style="background-color: #f8fafc;">
                                <div id="chart-partner-nationality" style="min-height: 340px;"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 3. DAFTAR DOKUMEN KERJA SAMA (KLASIK & BERPAGINASI) -->
    <section class="py-3" id="info">
        <div class="container">
            <div class="card border shadow-sm">
                <div class="card-header bg-white py-3">
                    <div class="row g-2 align-items-end">
                        <div class="col-lg-3 col-md-12">
                            <h2 class="h6 fw-bold text-dark mb-0">Daftar Dokumen Kerja Sama</h2>
                        </div>
                        <div class="col-lg-2 col-md-4 col-sm-6">
                            <label for="filterJenisDok" class="form-label fw-semibold mb-1" style="font-size: 0.78rem;">Jenis</label>
                            <select id="filterJenisDok" class="form-select form-select-sm">
                                <option value="">Semua Jenis</option>
                                <option value="MoU">MoU</option>
                                <option value="MoA">MoA / PKS</option>
                                <option value="IA">IA</option>
                            </select>
                        </div>
                        <div class="col-lg-2 col-md-4 col-sm-6">
                            <label for="filterStatusDok" class="form-label fw-semibold mb-1" style="font-size: 0.78rem;">Status</label>
                            <select id="filterStatusDok" class="form-select form-select-sm">
                                <option value="">Semua Status</option>
                                <option value="ACTIVE">Aktif</option>
                                <option value="VERIFIED">Terverifikasi</option>
                                <option value="REVIEW">Review</option>
                                <option value="DRAFT">Draf</option>
                                <option value="EXPIRED">Kedaluwarsa</option>
                            </select>
                        </div>
                        <div class="col-lg-5 col-md-4 col-sm-12">
                            <label for="searchTableInput" class="form-label fw-semibold mb-1" style="font-size: 0.78rem;">Pencarian</label>
                            <div class="input-group input-group-sm">
                                <input type="text" id="searchTableInput" class="form-control" placeholder="Cari judul, mitra, atau nomor...">
                                <button type="button" id="btnResetHomeFilter" class="btn btn-outline-secondary d-none" title="Reset Filter">Reset</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table id="table-info-kerjasama" class="table table-sm table-striped table-hover table-bordered align-middle w-100 mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center" style="width: 5%;">No.</th>
                                    <th class="text-center" style="width: 11%;">Jenis</th>
                                    <th style="width: 40%;">Judul &amp; Mitra Kerja Sama</th>
                                    <th class="text-center" style="width: 11%;">Mulai</th>
                                    <th class="text-center" style="width: 11%;">Berakhir</th>
                                    <th class="text-center" style="width: 12%;">Status</th>
                                    <th class="text-center" style="width: 10%;">Detail</th>
                                </tr>
                            </thead>
                            <tbody id="tableBodyDocuments">
                                @forelse ($allDocuments as $index => $doc)
                                    @php
                                        $typeBadgeClass = 'badge-home-mou';
                                        $docTypeName = 'MoU';
                                        if (str_contains((string)$doc->doc_type, 'MoA')) {
                                            $typeBadgeClass = 'badge-home-moa';
                                            $docTypeName = 'MoA';
                                        } elseif (str_contains((string)$doc->doc_type, 'IA')) {
                                            $typeBadgeClass = 'badge-home-ia';
                                            $docTypeName = 'IA';
                                        }

                                        $statusBadge = '<span class="badge-home badge-st-draft">DRAF</span>';
                                        if ($doc->status === 'ACTIVE') {
                                            $statusBadge = '<span class="badge-home badge-st-active">AKTIF</span>';
                                        } elseif ($doc->status === 'VERIFIED') {
                                            $statusBadge = '<span class="badge-home badge-st-verified">VERIFIED</span>';
                                        } elseif ($doc->status === 'REVIEW') {
                                            $statusBadge = '<span class="badge-home badge-st-review">REVIEW</span>';
                                        } elseif ($doc->status === 'EXPIRED') {
                                            $statusBadge = '<span class="badge-home badge-st-expired">KEDALUWARSA</span>';
                                        }
                                    @endphp
                                    <tr class="home-doc-row" data-jenis="{{ $docTypeName }}" data-status="{{ $doc->status }}">
                                        <td class="text-center text-muted fw-semibold row-seq-num">{{ $index + 1 }}</td>
                                        <td class="text-center">
                                            <span class="badge-home {{ $typeBadgeClass }}">{{ $docTypeName }}</span>
                                        </td>
                                        <td>
                                            <div class="fw-semibold text-dark" style="font-size: 0.84rem; line-height: 1.35;">
                                                {{ $doc->title ?: ($doc->scope ?: 'Kerja Sama Kemitraan Tridharma') }}
                                            </div>
                                            <div class="text-muted" style="font-size: 0.78rem;">
                                                {{ $doc->partner_name }} &bull; {{ $doc->unit ?: 'Universitas' }}
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
                                                    class="btn btn-uni-outline btn-sm px-2 py-1 fw-semibold btn-detail-doc"
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
                                                    data-secondsigner="{{ $doc->second_party_signer ?: 'Pimpinan Mitra' }}">
                                                Detail
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4 text-muted">Belum ada dokumen kerja sama.</td>
                                    </tr>
                                @endforelse

                                <tr id="homeEmptyFilterRow" class="d-none">
                                    <td colspan="7" class="text-center py-4 text-muted">
                                        <div class="fw-semibold text-dark mb-1">Dokumen tidak ditemukan.</div>
                                        <button type="button" class="btn btn-uni-outline btn-sm" onclick="resetHomeTableFilters()">Reset Filter</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="pagination-container">
                        <div class="d-flex flex-wrap align-items-center gap-2 text-muted">
                            <span>Menampilkan <strong id="displayedDocCount" class="text-dark">{{ count($allDocuments) }}</strong> dari <strong>{{ count($allDocuments) }}</strong> dokumen</span>
                            <span>&bull;</span>
                            <div class="d-inline-flex align-items-center gap-1">
                                <label for="homePerPageSelect" class="mb-0">Baris:</label>
                                <select id="homePerPageSelect" class="form-select form-select-sm" style="width: auto; font-size: 0.8rem;">
                                    <option value="10" selected>10</option>
                                    <option value="25">25</option>
                                    <option value="50">50</option>
                                    <option value="ALL">Semua</option>
                                </select>
                            </div>
                        </div>
                        <div id="homePaginationBtns" class="d-flex align-items-center gap-1 flex-wrap"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- MODAL DETAIL KERJA SAMA -->
    <div class="modal fade" id="modal-detail-kerjasama" tabindex="-1" aria-labelledby="modalDetailKerjasamaLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content rounded-3 border-0 shadow">
                <div class="modal-header text-white" style="background-color: var(--uni-navy); border-bottom: 2px solid var(--uni-gold);">
                    <h5 class="modal-title fw-bold" id="modalDetailKerjasamaLabel">Detail Dokumen Kerja Sama</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3 pb-3 border-bottom">
                        <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                            <span class="badge-home badge-home-mou font-monospace" id="modalDetailType">MoU</span>
                            <span class="badge-home badge-st-active" id="modalDetailStatus">AKTIF</span>
                            <span class="badge-home badge-st-draft font-monospace" id="modalDetailDocId">UNIPAR-2026-001</span>
                        </div>
                        <h5 class="fw-bold text-dark mb-1" id="modalDetailTitle">Judul Kerja Sama</h5>
                        <p class="text-muted mb-0" style="font-size: 0.85rem;">Mitra: <strong id="modalDetailPartner">-</strong> (<span id="modalDetailCategory">-</span>)</p>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="p-3 rounded bg-light border">
                                <div class="text-muted fw-semibold" style="font-size: 0.78rem;">Nomor Naskah Pihak I</div>
                                <div class="fw-bold text-dark font-monospace mt-1" id="modalDetailDocNum">-</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 rounded bg-light border">
                                <div class="text-muted fw-semibold" style="font-size: 0.78rem;">Nomor Naskah Pihak II</div>
                                <div class="fw-bold text-dark font-monospace mt-1" id="modalDetailPartnerDocNum">-</div>
                            </div>
                        </div>
                        <div class="col-md-6" style="font-size: 0.85rem;">
                            <span class="text-muted">Unit Pengusul:</span>
                            <div class="fw-bold text-dark" id="modalDetailUnit">-</div>
                        </div>
                        <div class="col-md-6" style="font-size: 0.85rem;">
                            <span class="text-muted">Masa Berlaku:</span>
                            <div class="fw-bold text-dark" id="modalDetailValidity">-</div>
                        </div>
                        <div class="col-md-6" style="font-size: 0.85rem;">
                            <span class="text-muted">Tanggal Mulai:</span>
                            <div class="fw-semibold text-dark" id="modalDetailStart">-</div>
                        </div>
                        <div class="col-md-6" style="font-size: 0.85rem;">
                            <span class="text-muted">Tanggal Berakhir:</span>
                            <div class="fw-semibold text-dark" id="modalDetailEnd">-</div>
                        </div>
                        <div class="col-md-6" style="font-size: 0.85rem;">
                            <span class="text-muted">Penandatangan Pihak I:</span>
                            <div class="fw-semibold text-dark" id="modalDetailFirstSigner">-</div>
                        </div>
                        <div class="col-md-6" style="font-size: 0.85rem;">
                            <span class="text-muted">Penandatangan Pihak II:</span>
                            <div class="fw-semibold text-dark" id="modalDetailSecondSigner">-</div>
                        </div>
                        <div class="col-12" style="font-size: 0.85rem;">
                            <span class="text-muted">Ruang Lingkup:</span>
                            <div class="p-2 bg-light border rounded mt-1" id="modalDetailScope">-</div>
                        </div>
                        <div class="col-12" style="font-size: 0.85rem;">
                            <span class="text-muted">Deskripsi:</span>
                            <div class="p-2 bg-light border rounded mt-1" id="modalDetailDesc">-</div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top d-flex justify-content-between">
                    <a href="#" id="modalVerifyBtn" target="_blank" class="btn btn-uni-primary btn-sm fw-bold">
                        <i class="bi bi-qr-code-scan me-1"></i> Cek Sertifikat Verifikasi
                    </a>
                    <button type="button" class="btn btn-uni-outline btn-sm fw-semibold" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <!-- 4. KONTAK & LOKASI (RINGKAS) -->
    <section class="pt-3 pb-5" id="contact">
        <div class="container">
            <div class="card border shadow-sm">
                <div class="card-body p-4">
                    <div class="row g-4 align-items-stretch">
                        <div class="col-lg-5 d-flex flex-column justify-content-between">
                            <div>
                                <h2 class="h5 fw-bold text-dark mb-3">Kontak Biro Kerja Sama (BKUI)</h2>
                                <div class="mb-3" style="font-size: 0.86rem;">
                                    <div class="fw-bold text-dark">Alamat</div>
                                    <div class="text-muted">Gedung Rektorat Lt. 2, Universitas Parna Raya<br>Jl. Sam Ratulangi No. 1, Manado 95111</div>
                                </div>
                                <div class="mb-3" style="font-size: 0.86rem;">
                                    <div class="fw-bold text-dark">Jam Layanan</div>
                                    <div class="text-muted">Senin &ndash; Jumat, 08:00 &ndash; 16:00 WITA</div>
                                </div>
                                <div class="mb-3" style="font-size: 0.86rem;">
                                    <div class="fw-bold text-dark">Email Resmi</div>
                                    <div class="text-muted">kerjasama@unipar.ac.id</div>
                                </div>
                            </div>
                            <div class="pt-2 border-top d-flex gap-2 flex-wrap">
                                <a href="{{ route('login') }}" class="btn btn-uni-outline btn-sm fw-semibold">Login Staf</a>
                                <a href="{{ route('simkerma.index', ['menu' => 4, 'view' => 'new']) }}" class="btn btn-uni-primary btn-sm fw-semibold">Ajukan Kerja Sama</a>
                            </div>
                        </div>
                        <div class="col-lg-7">
                            <div class="rounded overflow-hidden border h-100" style="min-height: 250px;">
                                <iframe
                                    title="Peta Lokasi Kampus Universitas Parna Raya Manado"
                                    src="https://maps.google.com/maps?q=Jalan+Sam+Ratulangi+Manado+Sulawesi+Utara&t=&z=15&ie=UTF8&iwloc=&output=embed"
                                    width="100%"
                                    height="100%"
                                    style="border:0; min-height: 250px;"
                                    allowfullscreen=""
                                    loading="lazy"
                                    referrerpolicy="no-referrer-when-downgrade">
                                </iframe>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FOOTER RINGKAS -->
    <footer class="uni-footer">
        <div class="container d-flex flex-column flex-md-row justify-content-between align-items-center gap-2">
            <div>
                <strong class="text-white">SIKERMA Universitas Parna Raya</strong> &mdash; Manado, Sulawesi Utara
            </div>
            <div class="d-flex flex-wrap gap-3">
                <a href="#stats">Statistik</a>
                <a href="#graphic-stat">Grafik</a>
                <a href="#info">Dokumen</a>
                <a href="#contact">Kontak</a>
            </div>
            <div>
                &copy; {{ date('Y') }} UNIPAR
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="{{ asset('partial/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('partial/libs/apexcharts/apexcharts.min.js') }}"></script>

    <script>
        function fillSampleVerify(code) {
            const inp = document.getElementById('homeVerifyInput');
            if (inp) {
                inp.value = code;
                inp.focus();
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            const donutLabels = @json($chartTypeLabels);
            const donutSeries = @json($chartTypeSeries);

            new ApexCharts(document.querySelector("#document-partnership"), {
                chart: { type: 'donut', height: 330, fontFamily: 'Plus Jakarta Sans, sans-serif' },
                series: donutSeries,
                labels: donutLabels,
                colors: ['#1e40af', '#0f766e', '#92400e'],
                legend: { show: true, position: 'bottom', fontSize: '13px', labels: { colors: '#334155' } },
                dataLabels: { enabled: true, formatter: val => Math.round(val) + "%" },
                plotOptions: {
                    pie: {
                        donut: {
                            size: '62%',
                            labels: {
                                show: true,
                                total: {
                                    show: true,
                                    label: 'Total',
                                    fontSize: '13px',
                                    fontWeight: 700,
                                    color: '#0b1f3a',
                                    formatter: w => w.globals.seriesTotals.reduce((a, b) => a + b, 0)
                                }
                            }
                        }
                    }
                },
                tooltip: { y: { formatter: val => val + " Dokumen" } }
            }).render();

            new ApexCharts(document.querySelector("#document-per-year"), {
                chart: { type: 'bar', height: 330, toolbar: { show: false }, fontFamily: 'Plus Jakarta Sans, sans-serif' },
                title: { text: "Dokumen Kerja Sama Per Tahun (5 Tahun Terakhir)", align: 'center', style: { fontSize: '13px', fontWeight: '700', color: '#0b1f3a' } },
                series: @json($docsPerYearSeries),
                colors: ['#1e40af', '#0f766e', '#92400e'],
                plotOptions: { bar: { horizontal: false, columnWidth: '48%', borderRadius: 3 } },
                dataLabels: { enabled: true, offsetY: -18, style: { fontSize: '11px', colors: ["#334155"] } },
                stroke: { show: true, width: 2, colors: ['transparent'] },
                xaxis: { categories: @json($yearsRange) },
                yaxis: { title: { text: 'Jumlah' }, tickAmount: 4 },
                tooltip: { y: { formatter: val => val + " Dokumen" } },
                legend: { position: 'bottom' }
            }).render();

            const allYearStats = @json($interactiveStats);

            const chartFaculty = new ApexCharts(document.querySelector("#chart-faculty-partnership"), {
                chart: { type: 'bar', height: 340, toolbar: { show: false }, fontFamily: 'Plus Jakarta Sans, sans-serif' },
                plotOptions: { bar: { horizontal: true, borderRadius: 3, barHeight: '55%' } },
                colors: ['#1e40af'],
                series: [{ name: 'Dokumen', data: (allYearStats['all'] && allYearStats['all'].units) ? allYearStats['all'].units.series : [] }],
                xaxis: { categories: (allYearStats['all'] && allYearStats['all'].units) ? allYearStats['all'].units.labels : [], tickAmount: 4 },
                dataLabels: { enabled: true, style: { fontSize: '11px' } },
                tooltip: { y: { formatter: val => val + " Dokumen" } }
            });
            chartFaculty.render();

            const chartClassification = new ApexCharts(document.querySelector("#chart-partner-classification"), {
                chart: { type: 'bar', height: 340, toolbar: { show: false }, fontFamily: 'Plus Jakarta Sans, sans-serif' },
                plotOptions: { bar: { horizontal: true, borderRadius: 3, barHeight: '55%' } },
                colors: ['#0f766e'],
                series: [{ name: 'Mitra', data: (allYearStats['all'] && allYearStats['all'].categories) ? allYearStats['all'].categories.series : [] }],
                xaxis: { categories: (allYearStats['all'] && allYearStats['all'].categories) ? allYearStats['all'].categories.labels : [], tickAmount: 4 },
                dataLabels: { enabled: true, style: { fontSize: '11px' } },
                tooltip: { y: { formatter: val => val + " Mitra" } }
            });
            chartClassification.render();

            const chartNationality = new ApexCharts(document.querySelector("#chart-partner-nationality"), {
                chart: { type: 'bar', height: 340, toolbar: { show: false }, fontFamily: 'Plus Jakarta Sans, sans-serif' },
                plotOptions: { bar: { horizontal: true, borderRadius: 3, barHeight: '48%' } },
                colors: ['#0b1f3a'],
                series: [{ name: 'Mitra', data: (allYearStats['all'] && allYearStats['all'].nationalities) ? allYearStats['all'].nationalities.series : [] }],
                xaxis: { categories: (allYearStats['all'] && allYearStats['all'].nationalities) ? allYearStats['all'].nationalities.labels : [], tickAmount: 4 },
                dataLabels: { enabled: true, style: { fontSize: '11px' } },
                tooltip: { y: { formatter: val => val + " Mitra" } }
            });
            chartNationality.render();

            const yearSelect = document.getElementById('yearFilterSelect');
            if (yearSelect) {
                yearSelect.addEventListener('change', function () {
                    const dataObj = allYearStats[this.value] || allYearStats['all'];
                    if (dataObj) {
                        chartFaculty.updateOptions({ xaxis: { categories: dataObj.units.labels }, series: [{ data: dataObj.units.series }] });
                        chartClassification.updateOptions({ xaxis: { categories: dataObj.categories.labels }, series: [{ data: dataObj.categories.series }] });
                        chartNationality.updateOptions({ xaxis: { categories: dataObj.nationalities.labels }, series: [{ data: dataObj.nationalities.series }] });
                    }
                });
            }

            const statsTabs = document.getElementById('statsTabs');
            if (statsTabs) {
                statsTabs.addEventListener('shown.bs.tab', () => window.dispatchEvent(new Event('resize')));
            }

            // Table Filter & Pagination
            const filterJenis = document.getElementById('filterJenisDok');
            const filterStatus = document.getElementById('filterStatusDok');
            const searchInput = document.getElementById('searchTableInput');
            const btnReset = document.getElementById('btnResetHomeFilter');
            const perPageSelect = document.getElementById('homePerPageSelect');
            const paginationBtns = document.getElementById('homePaginationBtns');
            const emptyRow = document.getElementById('homeEmptyFilterRow');
            const tableRows = Array.from(document.querySelectorAll('#tableBodyDocuments tr.home-doc-row'));
            const countDisplay = document.getElementById('displayedDocCount');
            let currentHomePage = 1;

            function renderHomeTable(resetPage = false) {
                if (resetPage) currentHomePage = 1;
                const jenisVal = (filterJenis ? filterJenis.value : '').toLowerCase().trim();
                const statusVal = (filterStatus ? filterStatus.value : '').toUpperCase().trim();
                const searchVal = (searchInput ? searchInput.value : '').toLowerCase().trim();

                if (btnReset) {
                    btnReset.classList.toggle('d-none', !(jenisVal || statusVal || searchVal));
                }

                const matched = tableRows.filter(row => {
                    const rowJenis = (row.getAttribute('data-jenis') || '').toLowerCase();
                    const rowStatus = (row.getAttribute('data-status') || '').toUpperCase();
                    const rowText = row.innerText.toLowerCase();
                    return (!jenisVal || rowJenis === jenisVal) &&
                           (!statusVal || rowStatus === statusVal) &&
                           (!searchVal || rowText.includes(searchVal));
                });

                const totalMatched = matched.length;
                if (countDisplay) countDisplay.innerText = totalMatched;

                if (totalMatched === 0) {
                    tableRows.forEach(r => r.style.display = 'none');
                    if (emptyRow) emptyRow.classList.remove('d-none');
                    if (paginationBtns) paginationBtns.innerHTML = '';
                    return;
                }
                if (emptyRow) emptyRow.classList.add('d-none');

                const perPageRaw = perPageSelect ? perPageSelect.value : '10';
                const pageSize = perPageRaw === 'ALL' ? totalMatched : Math.max(1, parseInt(perPageRaw, 10) || 10);
                const totalPages = Math.max(1, Math.ceil(totalMatched / pageSize));
                if (currentHomePage > totalPages) currentHomePage = totalPages;

                const startIdx = (currentHomePage - 1) * pageSize;
                const endIdx = startIdx + pageSize;
                let seq = startIdx + 1;

                tableRows.forEach(r => r.style.display = 'none');
                matched.slice(startIdx, endIdx).forEach(r => {
                    r.style.display = '';
                    const numCell = r.querySelector('.row-seq-num');
                    if (numCell) numCell.innerText = seq++;
                });

                if (paginationBtns) {
                    paginationBtns.innerHTML = '';
                    if (totalPages > 1) {
                        const addBtn = (label, targetPage, isActive, isDisabled) => {
                            const b = document.createElement('button');
                            b.type = 'button';
                            b.className = 'home-page-btn' + (isActive ? ' active' : '');
                            b.innerText = label;
                            b.disabled = !!isDisabled;
                            b.addEventListener('click', () => {
                                currentHomePage = targetPage;
                                renderHomeTable(false);
                            });
                            paginationBtns.appendChild(b);
                        };
                        addBtn('‹', currentHomePage - 1, false, currentHomePage === 1);
                        for (let p = 1; p <= totalPages; p++) {
                            if (p === 1 || p === totalPages || Math.abs(p - currentHomePage) <= 1) {
                                addBtn(String(p), p, p === currentHomePage, false);
                            } else if (Math.abs(p - currentHomePage) === 2) {
                                const ell = document.createElement('span');
                                ell.className = 'px-1 text-muted';
                                ell.innerText = '...';
                                paginationBtns.appendChild(ell);
                            }
                        }
                        addBtn('›', currentHomePage + 1, false, currentHomePage === totalPages);
                    }
                }
            }

            window.resetHomeTableFilters = function () {
                if (filterJenis) filterJenis.value = '';
                if (filterStatus) filterStatus.value = '';
                if (searchInput) searchInput.value = '';
                renderHomeTable(true);
            };

            if (filterJenis) filterJenis.addEventListener('change', () => renderHomeTable(true));
            if (filterStatus) filterStatus.addEventListener('change', () => renderHomeTable(true));
            if (searchInput) searchInput.addEventListener('input', () => renderHomeTable(true));
            if (perPageSelect) perPageSelect.addEventListener('change', () => renderHomeTable(true));
            if (btnReset) btnReset.addEventListener('click', window.resetHomeTableFilters);

            renderHomeTable(true);

            // Modal Detail
            document.querySelectorAll('.btn-detail-doc').forEach(btn => {
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
