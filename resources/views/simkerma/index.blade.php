@extends('layouts.app')

@section('content')

    <!-- Flash Notification -->
    @if (session('flash'))
        <div class="alert alert-{{ session('flash')['type'] }} alert-dismissible fade show mb-4 shadow-sm" role="alert">
            <div class="d-flex align-items-center">
                <i class="bi bi-{{ session('flash')['type'] === 'success' ? 'check-circle-fill' : (session('flash')['type'] === 'danger' ? 'exclamation-octagon-fill' : 'info-circle-fill') }} me-2 fs-5"></i>
                <div><strong>Konfirmasi Sistem:</strong> {!! session('flash')['msg'] !!}</div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Validation Error Notification -->
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show mb-4 shadow-sm" role="alert">
            <div class="d-flex align-items-start gap-2">
                <i class="bi bi-exclamation-octagon-fill fs-5 mt-1"></i>
                <div>
                    <strong>Validasi Gagal:</strong>
                    @if ($errors->count() === 1)
                        {{ $errors->first() }}
                    @else
                        <ul class="mb-0 ps-3 mt-1">
                            @foreach ($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- ===================================================================================== --}}
    {{-- 1. DASHBOARD KERJA SAMA                                                               --}}
    {{-- ===================================================================================== --}}
    @if ($activeMenu === 1)
        @php
            $totalDocs = $documents->count();
            $activeDocs = $documents->where('status', 'ACTIVE')->count();
            $processDocs = $documents->whereIn('status', ['DRAFT', 'REVIEW', 'VERIFIED'])->count();
            $expiredDocs = $documents->where('status', 'EXPIRED')->count();
            $warningDocs = $documents->where('status', 'WARNING')->count();
            $attentionCount = $warningDocs + $expiredDocs;

            $mouDashCount = $documents->filter(fn($d) => stripos((string)$d->doc_type, 'MoU') !== false)->count();
            $moaDashCount = $documents->filter(fn($d) => stripos((string)$d->doc_type, 'MoA') !== false || stripos((string)$d->doc_type, 'PKS') !== false)->count();
            $iaDashCount = $documents->filter(fn($d) => stripos((string)$d->doc_type, 'IA') !== false)->count();

            // Distribusi Kategori Mitra (diurutkan terbanyak & memuat jumlah mitra unik + dokumen)
            $categoriesGroup = $documents
                ->groupBy(fn($d) => trim((string)$d->partner_category) !== '' ? trim((string)$d->partner_category) : 'Kategori Umum')
                ->sortByDesc(fn($g) => $g->count());

            // Distribusi Unit Pengusul (diurutkan terbanyak + fallback jika kosong)
            $unitsGroup = $documents
                ->groupBy(fn($d) => trim((string)$d->unit) !== '' ? trim((string)$d->unit) : 'Unit Belum Ditentukan')
                ->sortByDesc(fn($g) => $g->count());

            // Kerja Sama Peringatan Dini (WARNING) & Kedaluwarsa (EXPIRED) yang butuh atensi
            $attentionList = $documents->whereIn('status', ['WARNING', 'EXPIRED'])
                ->sortBy(fn($d) => [
                    $d->status === 'WARNING' ? 0 : 1,
                    $d->end_date ? $d->end_date->timestamp : 9999999999,
                ])
                ->take(5);

            // Realisasi IKU 6 dari activities
            $totalActs = $activities->count();
            $completedActs = $activities->where('execution_status', 'SELESAI')->count();
            $runningActs = $activities->where('execution_status', 'SEDANG_BERJALAN')->count();
            $evidenceActsCount = $activities->filter(fn($a) => !empty($a->evidence_file))->count();
            $actProgressPercent = $totalActs > 0 ? round((($completedActs + ($runningActs * 0.7)) / $totalActs) * 100) : 0;

            $attentionTargetStatus = $warningDocs > 0 ? 'WARNING' : ($expiredDocs > 0 ? 'EXPIRED' : 'ALL');
        @endphp

        <!-- 4 KARTU RINGKASAN STATUS KERJA SAMA (REAL-TIME & KLIK UNTUK DRILL-DOWN) -->
        <div class="row g-3 mb-4">
            <div class="col-sm-6 col-xl-3">
                <a href="{{ route('simkerma.index', ['menu' => 3, 'reset_filters' => 1]) }}" class="card card-wireframe dash-kpi-card h-100 p-3 border-top border-3 border-dark shadow-sm mb-0" title="Buka semua dokumen di Repositori Menu 03">
                    <span class="text-muted small text-uppercase fw-semibold d-block">Total Kerja Sama</span>
                    <div class="d-flex align-items-center justify-content-between mt-2">
                        <h2 class="fw-bold mb-0 text-dark">{{ $totalDocs }}</h2>
                        <span class="dash-kpi-icon bg-soft-secondary text-dark"><i class="bi bi-folder2-open"></i></span>
                    </div>
                    <small class="text-muted d-block mt-2">MoU: {{ $mouDashCount }} &bull; MoA: {{ $moaDashCount }} &bull; IA: {{ $iaDashCount }}</small>
                </a>
            </div>

            <div class="col-sm-6 col-xl-3">
                <a href="{{ route('simkerma.index', ['menu' => 3, 'filter_status' => 'ACTIVE']) }}" class="card card-wireframe dash-kpi-card h-100 p-3 border-top border-3 border-success shadow-sm mb-0" title="Saring dokumen Aktif di Repositori Menu 03">
                    <span class="text-muted small text-uppercase fw-semibold d-block">Kerja Sama Aktif</span>
                    <div class="d-flex align-items-center justify-content-between mt-2">
                        <h2 class="fw-bold mb-0 text-success">{{ $activeDocs }}</h2>
                        <span class="dash-kpi-icon bg-soft-success text-success"><i class="bi bi-patch-check-fill"></i></span>
                    </div>
                    <small class="text-muted d-block mt-2">Sah Berlaku &amp; Bertanda Tangan</small>
                </a>
            </div>

            <div class="col-sm-6 col-xl-3">
                <a href="{{ route('simkerma.index', ['menu' => 4]) }}" class="card card-wireframe dash-kpi-card h-100 p-3 border-top border-3 border-primary shadow-sm mb-0" title="Kelola pengajuan &amp; persetujuan di Menu 04">
                    <span class="text-muted small text-uppercase fw-semibold d-block">Dalam Proses (Pipeline)</span>
                    <div class="d-flex align-items-center justify-content-between mt-2">
                        <h2 class="fw-bold mb-0 text-primary">{{ $processDocs }}</h2>
                        <span class="dash-kpi-icon bg-soft-primary text-primary"><i class="bi bi-hourglass-split"></i></span>
                    </div>
                    <small class="text-muted d-block mt-2">Draf, Telaah Legal &amp; Terverifikasi</small>
                </a>
            </div>

            <div class="col-sm-6 col-xl-3">
                <a href="{{ route('simkerma.index', ['menu' => 3, 'filter_status' => $attentionTargetStatus]) }}" class="card card-wireframe dash-kpi-card h-100 p-3 border-top border-3 border-warning shadow-sm mb-0" title="Lihat naskah yang perlu perpanjangan/evaluasi di Menu 03">
                    <span class="text-muted small text-uppercase fw-semibold d-block">Peringatan &amp; Kedaluwarsa</span>
                    <div class="d-flex align-items-center justify-content-between mt-2">
                        <h2 class="fw-bold mb-0 kpi-val-warning">{{ $attentionCount }}</h2>
                        <span class="dash-kpi-icon bg-soft-warning" style="color: #805300;"><i class="bi bi-exclamation-triangle-fill"></i></span>
                    </div>
                    <small class="text-muted d-block mt-2">{{ $warningDocs }} Peringatan &bull; {{ $expiredDocs }} Kedaluwarsa</small>
                </a>
            </div>
        </div>

        <!-- 2 KOLOM: MITRA KATEGORI & DISTRIBUSI UNIT -->
        <div class="row g-4 mb-4">
            <!-- Jumlah Mitra & Dokumen Berdasarkan Kategori -->
            <div class="col-lg-6">
                <div class="card card-wireframe h-100 shadow-sm">
                    <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center gap-2">
                        <h6 class="card-title mb-0 fw-bold text-uppercase">Kategori Mitra &amp; Dokumen</h6>
                        <a href="{{ route('simkerma.index', ['menu' => 2]) }}" class="badge badge-naskah-soft text-decoration-none">
                            {{ $categoriesGroup->count() }} Kategori &bull; {{ $partners->count() }} Mitra
                        </a>
                    </div>
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush small">
                            @forelse ($categoriesGroup as $catName => $catDocs)
                                @php
                                    $catPct = $totalDocs > 0 ? round(($catDocs->count() / $totalDocs) * 100) : 0;
                                    $catPartnerCount = $catDocs->pluck('partner_name')->filter()->unique()->count();
                                @endphp
                                <a href="{{ route('simkerma.index', ['menu' => 3, 'filter_category' => strtolower($catName)]) }}" class="list-group-item list-group-item-action dash-dist-link py-2 px-3" title="Saring dokumen kategori {{ $catName }} di Repositori">
                                    <div class="d-flex justify-content-between align-items-center gap-2 mb-1">
                                        <span class="text-dark">
                                            <i class="bi bi-briefcase me-2 text-primary"></i>
                                            <strong>{{ $catName }}</strong>
                                        </span>
                                        <span class="badge badge-naskah-soft rounded-pill flex-shrink-0">
                                            {{ $catPartnerCount }} Mitra &bull; {{ $catDocs->count() }} Dokumen
                                        </span>
                                    </div>
                                    <div class="dash-dist-bar">
                                        <div class="bg-primary h-100" style="width: {{ max($catPct, 2) }}%;"></div>
                                    </div>
                                </a>
                            @empty
                                <div class="list-group-item text-muted py-3 px-3 text-center">Belum ada kategori mitra tercatat.</div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <!-- Kerja Sama Berdasarkan Fakultas, UPPS, Program Studi, & Unit Pengusul -->
            <div class="col-lg-6">
                <div class="card card-wireframe h-100 shadow-sm">
                    <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center gap-2">
                        <h6 class="card-title mb-0 fw-bold text-uppercase">Distribusi Fakultas &amp; Unit Pengusul Kampus</h6>
                        <span class="badge badge-naskah-soft">{{ $unitsGroup->count() }} Unit/Prodi</span>
                    </div>
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush small">
                            @forelse ($unitsGroup as $unitName => $uDocs)
                                @php
                                    $unitPct = $totalDocs > 0 ? round(($uDocs->count() / $totalDocs) * 100) : 0;
                                    $unitUrl = $unitName !== 'Unit Belum Ditentukan'
                                        ? route('simkerma.index', ['menu' => 3, 'filter_unit' => strtolower($unitName)])
                                        : route('simkerma.index', ['menu' => 3, 'reset_filters' => 1]);
                                @endphp
                                <a href="{{ $unitUrl }}" class="list-group-item list-group-item-action dash-dist-link py-2 px-3" title="Saring dokumen dari {{ $unitName }} di Repositori">
                                    <div class="d-flex justify-content-between align-items-center gap-2 mb-1">
                                        <span class="text-dark">
                                            <i class="bi bi-mortarboard me-2 text-primary"></i>
                                            <strong>{{ $unitName }}</strong>
                                        </span>
                                        <span class="badge badge-naskah-soft rounded-pill flex-shrink-0">
                                            {{ $uDocs->count() }} Dokumen ({{ $unitPct }}%)
                                        </span>
                                    </div>
                                    <div class="dash-dist-bar">
                                        <div class="bg-primary h-100" style="width: {{ max($unitPct, 2) }}%;"></div>
                                    </div>
                                </a>
                            @empty
                                <div class="list-group-item text-muted py-3 px-3 text-center">Belum ada unit pengusul tercatat.</div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2 KOLOM: SEGERA BERAKHIR / KEDALUWARSA & REALISASI KEGIATAN -->
        <div class="row g-4">
            <!-- Kerja Sama Perlu Atensi (Segera Berakhir & Kedaluwarsa) -->
            <div class="col-lg-6">
                <div class="card card-wireframe h-100 shadow-sm">
                    <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center gap-2">
                        <h6 class="card-title mb-0 fw-bold text-uppercase text-dark">
                            <i class="bi bi-exclamation-triangle me-1" style="color: #9a5b00;"></i> Perjanjian Perlu Atensi (Peringatan &amp; Kedaluwarsa)
                        </h6>
                        <a href="{{ route('simkerma.index', ['menu' => 6]) }}" class="badge bg-warning text-dark text-decoration-none" title="Buka Menu 06 (Masa Berlaku &amp; Evaluasi)">
                            {{ $attentionCount }} Naskah
                        </a>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-wireframe table-hover align-middle mb-0 small">
                            <thead>
                                <tr>
                                    <th>Mitra Kerja Sama</th>
                                    <th>Jenis</th>
                                    <th>Berakhir</th>
                                    <th>Status &amp; Tindak Lanjut</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($attentionList as $wDoc)
                                    <tr>
                                        <td>
                                            <div class="fw-bold text-dark">{{ $wDoc->partner_name }}</div>
                                            <div class="small text-muted font-monospace" style="font-size: 0.68rem;">{{ $wDoc->doc_number ?: $wDoc->doc_id }}</div>
                                        </td>
                                        <td><span class="badge bg-light text-dark border">{{ $wDoc->doc_type }}</span></td>
                                        <td>
                                            <div class="text-dark fw-medium">{{ $wDoc->end_date ? $wDoc->end_date->translatedFormat('d M Y') : '-' }}</div>
                                            <div class="text-muted" style="font-size: 0.68rem;">{{ $wDoc->validity ?: '1 Tahun' }}</div>
                                        </td>
                                        <td>
                                            @if ($wDoc->status === 'WARNING')
                                                <span class="badge badge-soft-warning border border-warning-subtle">{{ $wDoc->warning_threshold ?: 'Segera Berakhir' }}</span>
                                            @else
                                                <span class="badge badge-soft-danger border border-danger-subtle">Kedaluwarsa</span>
                                            @endif
                                            <a href="{{ route('simkerma.index', ['menu' => 4, 'view' => 'detail', 'doc' => $wDoc->doc_id]) }}" class="btn btn-outline-primary btn-sm py-0 px-2 ms-1" style="font-size: 0.68rem;" title="Buka Detail Naskah di Menu 04" aria-label="Kelola naskah {{ $wDoc->doc_number ?: $wDoc->doc_id }}">
                                                <i class="bi bi-box-arrow-up-right me-1"></i>Kelola
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-3">Seluruh perjanjian masih berlaku aman (tidak ada peringatan dini atau kedaluwarsa).</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if ($attentionCount > 5)
                        <div class="card-footer bg-white py-2 px-3 border-top d-flex justify-content-between align-items-center small text-muted">
                            <span>Menampilkan 5 dari {{ $attentionCount }} perjanjian perlu atensi</span>
                            <a href="{{ route('simkerma.index', ['menu' => 6]) }}" class="text-primary fw-semibold text-decoration-none">
                                Lihat Semua di Menu 06 <i class="bi bi-arrow-right"></i>
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Realisasi Kegiatan, Luaran, dan Capaian Kerja Sama -->
            <div class="col-lg-6">
                <div class="card card-wireframe h-100 shadow-sm">
                    <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                        <h6 class="card-title mb-0 fw-bold text-uppercase">Realisasi, Luaran &amp; Capaian (IKU 6)</h6>
                        <span class="badge badge-naskah-soft">{{ $totalActs }} Agenda</span>
                    </div>
                    <div class="card-body d-flex flex-column justify-content-between">
                        <div>
                            <div class="mb-3">
                                <div class="d-flex justify-content-between small mb-1">
                                    <span class="fw-semibold">Tingkat Penyelesaian Program Kemitraan (Menu 05)</span>
                                    <span class="fw-bold text-primary">{{ $actProgressPercent }}% Tercapai</span>
                                </div>
                                <div class="progress" style="height: 10px;">
                                    <div class="progress-bar bg-primary" style="width: {{ $actProgressPercent }}%"></div>
                                </div>
                                <small class="text-muted d-block mt-1">
                                    {{ $completedActs }} program selesai, {{ $runningActs }} sedang berjalan dari {{ $totalActs }} total agenda.
                                    @if ($runningActs > 0)
                                        <span class="ms-1" title="Program berstatus berjalan dihitung bobot 70% menuju penyelesaian">(bobot berjalan 70%)</span>
                                    @endif
                                </small>
                            </div>

                            <div class="p-3 bg-light rounded border mb-3 small">
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="text-muted">Total Kegiatan Pelaksanaan:</span>
                                    <strong class="text-dark">{{ $totalActs }} Agenda Tridharma &amp; PKM</strong>
                                </div>
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="text-muted">Kegiatan Aktif Lapangan:</span>
                                    <strong class="text-success">{{ $runningActs }} Program Berjalan</strong>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted">Bukti Laporan Terverifikasi:</span>
                                    <strong class="text-dark">{{ $evidenceActsCount }} Berkas Laporan</strong>
                                </div>
                            </div>
                        </div>

                        <div class="text-end">
                            <a href="{{ route('simkerma.index', ['menu' => 5]) }}" class="btn btn-outline-primary btn-sm fw-semibold">
                                <i class="bi bi-calendar-check me-1"></i> Buka Menu 05 (Pemantauan Pelaksanaan)
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    {{-- ===================================================================================== --}}
    {{-- 2. PENGELOLAAN DATA MITRA (MASTER DATA & DIREKTORI RESMI SINKRON PENUH DENGAN MYSQL)   --}}
    {{-- ===================================================================================== --}}
    @elseif ($activeMenu === 2)
        @php
            $canVerifyPartner = in_array($activeRole, ['MASTER', 'ADMIN', 'LEGAL'], true);
            $canDeletePartner = in_array($activeRole, ['MASTER', 'ADMIN'], true);
            $partnerPayloads = [];
        @endphp
        <div class="card card-wireframe shadow-sm border mb-4">
            <!-- 1. KONSOL FILTER BERLABEL DI CARD-HEADER (SESUAI SPESIFIKASI WF-02) -->
            <div class="card-header partner-filter-bar py-3">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold mb-1 text-secondary small">Jenis / Kategori Mitra (Resmi)</label>
                        <select id="partnerCategoryFilter" class="form-select form-select-sm" onchange="filterPartnersTable()">
                            <option value="">Semua Jenis Mitra</option>
                            @foreach (\App\Models\Partner::FILTER_CATEGORY_GROUPS as $grp)
                                <option value="{{ $grp }}">{{ $grp }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold mb-1 text-secondary small">Status Kemitraan &amp; Verifikasi</label>
                        <select id="partnerStatusFilter" class="form-select form-select-sm" onchange="filterPartnersTable()">
                            <option value="">Semua Status</option>
                            <optgroup label="Status Naskah Kemitraan">
                                <option value="Aktif Sah">Aktif / Sah (Naskah Berlaku)</option>
                                <option value="Tahap FSM">Tahap FSM (Dalam Proses)</option>
                                <option value="Peringatan Masa">Peringatan Masa</option>
                                <option value="Mitra Terdaftar">Terdaftar (Belum Ada Naskah)</option>
                            </optgroup>
                            <optgroup label="Status Verifikasi &amp; Kelengkapan Data">
                                <option value="VER_Terverifikasi Sah">Terverifikasi Sah</option>
                                <option value="VER_Belum Diverifikasi">Belum Diverifikasi</option>
                                <option value="VER_Perlu Pembaruan">Perlu Pembaruan</option>
                                <option value="COMP_INCOMPLETE">Profil Perlu Dilengkapi (&lt; 85%)</option>
                            </optgroup>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold mb-1 text-secondary small">Pencarian Mitra, PIC &amp; Kota</label>
                        <div class="input-group input-group-sm">
                            <input type="text" id="partnerSearchInput" class="form-control" placeholder="Cari mitra, PIC, kota... (Tekan /)" oninput="filterPartnersTable()">
                            <button type="button" class="btn btn-outline-secondary" onclick="resetPartnerFilters()" title="Bersihkan Pencarian" aria-label="Bersihkan Pencarian">
                                <i class="bi bi-x-circle"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. TOOLBAR AKSI RINGKAS (FLUSH) -->
            <div class="partner-toolbar px-3 py-2 bg-white border-bottom d-flex flex-wrap align-items-center justify-content-between gap-2">
                <div class="d-flex flex-wrap align-items-center gap-2">
                    <button type="button" class="btn btn-primary btn-sm fw-semibold shadow-sm" data-bs-toggle="modal" data-bs-target="#createPartnerModal">
                        <i class="bi bi-plus-lg me-1"></i> Tambah Mitra
                    </button>
                    <a href="{{ route('simkerma.index', ['menu' => 4, 'view' => 'new']) }}" class="btn btn-outline-dark btn-sm fw-semibold shadow-sm">
                        <i class="bi bi-file-earmark-plus me-1"></i> Ajukan Naskah Baru
                    </a>
                    <button type="button" id="btnPartnerExportExcel" class="btn btn-success btn-sm fw-semibold shadow-sm text-white" onclick="exportFilteredPartnersToExcel()" title="Ekspor Data Mitra Terfilter ke Excel">
                        <i class="bi bi-file-earmark-excel me-1"></i> Excel
                    </button>
                    <button type="button" class="btn btn-outline-secondary btn-sm fw-semibold shadow-2xs gap-1" onclick="resetPartnerFilters()" title="Reset seluruh filter, pencarian &amp; pengurutan">
                        <i class="bi bi-arrow-counterclockwise"></i><span>Reset Filter</span>
                        <span class="badge bg-primary text-white d-none" id="partnerActiveFilterBadge" style="font-size: 0.72rem; padding: 2px 5px;">0</span>
                    </button>
                </div>
                <div>
                    <span class="badge bg-light text-dark border px-3 py-1.5 fw-semibold" style="font-size: 0.75rem;">
                        Total: <span id="partnerVisibleCount" class="fw-bold text-primary">{{ $partners->count() }}</span> Mitra Terdaftar
                    </span>
                </div>
            </div>

            <!-- 3. TABEL PENGELOLAAN DATA MITRA (FLUSH LANGSUNG DI CARD + ERGONOMIS TANPA GARIS KISI VERTIKAL) -->
            <div class="table-responsive">
                <table class="w-100 table table-striped table-hover align-middle mb-0" id="partnerTable">
                    <colgroup>
                        <col style="width: 4%;">
                        <col style="width: 22%;">
                        <col style="width: 16%;">
                        <col style="width: 14%;">
                        <col style="width: 14%;">
                        <col style="width: 9%;">
                        <col style="width: 10%;">
                        <col style="width: 11%;">
                    </colgroup>
                    <thead class="table-light">
                        <tr>
                            <th>No.</th>
                            <th class="partner-sortable sort-active" data-sort-key="name" data-sort-dir="asc" tabindex="0" role="button" title="Klik untuk mengurutkan Nama Mitra (A-Z / Z-A)">Mitra, Domisili &amp; PIC <i class="bi bi-sort-up partner-sort-icon"></i></th>
                            <th class="partner-sortable" data-sort-key="completeness" tabindex="0" role="button" title="Klik untuk mengurutkan Kelengkapan Profil">Jenis &amp; Data <i class="bi bi-arrow-down-up partner-sort-icon"></i></th>
                            <th>Unit Terkait</th>
                            <th>Lingkup Kerma</th>
                            <th class="partner-sortable" data-sort-key="docs" tabindex="0" role="button" title="Klik untuk mengurutkan Jumlah Naskah">Naskah <i class="bi bi-arrow-down-up partner-sort-icon"></i></th>
                            <th class="partner-sortable" data-sort-key="status" tabindex="0" role="button" title="Klik untuk mengurutkan Status Kemitraan">Status <i class="bi bi-arrow-down-up partner-sort-icon"></i></th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($partners as $partner)
                            @php
                                $pDocs = $partner->documents;
                                $firstDoc = $pDocs->first();
                                $hasActive = $pDocs->where('status', 'ACTIVE')->isNotEmpty();
                                $hasWarning = $pDocs->where('status', 'WARNING')->isNotEmpty();
                                $types = $pDocs->pluck('doc_type')->filter()->unique()->values();
                                $units = $pDocs->pluck('unit')->filter()->unique()->values();
                                $docScopes = $pDocs->pluck('scope')->filter(fn($s) => trim((string)$s) !== '')->unique()->values();
                                $scopeSummary = $docScopes->isNotEmpty()
                                    ? $docScopes->join(', ')
                                    : ($partner->profile_desc ?: 'Belum ada rincian lingkup kerja sama');

                                $stLabel = $hasActive ? 'Aktif Sah' : ($hasWarning ? 'Peringatan Masa' : ($pDocs->isNotEmpty() ? 'Tahap FSM' : 'Mitra Terdaftar'));
                                $catGroup = \App\Models\Partner::normalizeCategoryGroup($partner->category);
                                $catMeta = $partner->category_badge_meta;
                                $comp = $partner->completeness_info;
                                $compBadgeClass = $comp['percent'] >= 85 ? 'badge-comp-neutral' : $comp['badge_class'];
                                $verStatus = $partner->status_verification ?: 'Belum Diverifikasi';
                                $verBadgeClass = match($verStatus) {
                                    'Terverifikasi Sah' => 'badge-soft-success border-success-subtle',
                                    'Perlu Pembaruan' => 'badge-soft-warning border-warning-subtle',
                                    default => 'badge-soft-secondary border-secondary-subtle',
                                };

                                $partnerSearchIndex = strtolower(trim(implode(' ', [
                                    $partner->name,
                                    $partner->category,
                                    $catGroup,
                                    $partner->city,
                                    $partner->address,
                                    $partner->phone,
                                    $partner->email,
                                    $partner->pic_name,
                                    $partner->pic_title,
                                    $partner->pic_contact,
                                    $scopeSummary,
                                    $units->join(' '),
                                    $pDocs->pluck('doc_number')->join(' '),
                                ])));

                                $partnerPayloads[$partner->id] = [
                                    'id' => $partner->id,
                                    'name' => $partner->name,
                                    'category' => $partner->category,
                                    'scope_coverage' => $partner->scope_coverage ?: 'Dalam Negeri (Nasional/Lokal)',
                                    'city' => $partner->city,
                                    'address' => $partner->address,
                                    'phone' => $partner->phone,
                                    'email' => $partner->email,
                                    'pic_name' => $partner->pic_name,
                                    'pic_title' => $partner->pic_title,
                                    'pic_contact' => $partner->pic_contact,
                                    'profile_desc' => $partner->profile_desc,
                                    'status_verification' => $verStatus,
                                    'completeness' => $comp,
                                    'units' => $units->all(),
                                    'types' => $types->all(),
                                    'st_label' => $stLabel,
                                    'has_active' => $hasActive,
                                    'has_warning' => $hasWarning,
                                    'new_doc_url' => route('simkerma.index', ['menu' => 4, 'view' => 'new', 'partner_name' => $partner->name]),
                                    'docs' => $pDocs->map(function($d) {
                                        return [
                                            'doc_id' => $d->doc_id,
                                            'doc_number' => $d->doc_number,
                                            'reg_id' => $d->reg_id,
                                            'doc_type' => $d->doc_type,
                                            'scope' => $d->scope,
                                            'unit' => $d->unit,
                                            'validity' => $d->validity,
                                            'date_signed' => $d->date_signed ? \Carbon\Carbon::parse($d->date_signed)->format('d M Y') : null,
                                            'status' => $d->status,
                                            'url' => route('simkerma.index', ['menu' => 4, 'view' => 'detail', 'doc' => $d->doc_id]),
                                        ];
                                    })->values()->all(),
                                ];
                            @endphp
                            @php
                                $statusRank = $hasActive ? 4 : ($hasWarning ? 3 : ($pDocs->isNotEmpty() ? 2 : 1));
                            @endphp
                            <tr class="partner-row"
                                data-category="{{ $partner->category }}"
                                data-category-group="{{ $catGroup }}"
                                data-status="{{ $stLabel }}"
                                data-verification="{{ $verStatus }}"
                                data-complete="{{ $comp['percent'] >= 85 ? '1' : '0' }}"
                                data-sort-name="{{ strtolower(trim((string)$partner->name)) }}"
                                data-sort-completeness="{{ $comp['percent'] }}"
                                data-sort-docs="{{ $pDocs->count() }}"
                                data-sort-status="{{ $statusRank }}"
                                data-search="{{ $partnerSearchIndex }}"
                                data-export-name="{{ $partner->name }}"
                                data-export-city="{{ $partner->city ?: '-' }}"
                                data-export-scope-coverage="{{ $partner->scope_coverage === 'Internasional' ? 'Internasional' : 'Nasional/Lokal' }}"
                                data-export-pic-name="{{ $partner->pic_name ?: '-' }}"
                                data-export-pic-contact="{{ $partner->pic_contact ?: '-' }}"
                                data-export-category="{{ $partner->category ?: 'Belum Ditentukan' }}"
                                data-export-verification="{{ $verStatus }}"
                                data-export-completeness="{{ $comp['percent'] }}%"
                                data-export-units="{{ $units->isNotEmpty() ? $units->join(', ') : '-' }}"
                                data-export-doc-scope="{{ $scopeSummary }}"
                                data-export-doc-count="{{ $pDocs->count() }}"
                                data-export-doc-types="{{ $types->isNotEmpty() ? $types->join(', ') : '-' }}"
                                data-export-status="{{ $stLabel }}">
                                <!-- 1. No. -->
                                <td class="text-center text-muted fw-semibold partner-row-num" style="font-size: 0.78rem;">{{ $loop->iteration }}</td>

                                <!-- 2. Mitra, Domisili & PIC -->
                                <td>
                                    <div class="fw-bold text-dark" style="font-size: 0.85rem; line-height: 1.35;">{{ $partner->name }}</div>
                                    <div class="text-muted mt-1" style="font-size: 0.75rem; line-height: 1.35;">
                                        <i class="bi bi-geo-alt text-secondary me-1"></i>{{ $partner->city ?: 'Kota belum diisi' }}
                                        @if ($partner->scope_coverage === 'Internasional')
                                            <span class="badge badge-scope-intl ms-1" style="font-size: 0.72rem;"><i class="bi bi-globe2 me-1"></i>Internasional</span>
                                        @else
                                            <span class="text-muted">&bull; Nasional/Lokal</span>
                                        @endif
                                    </div>
                                    <div class="text-muted mt-0.5" style="font-size: 0.75rem; line-height: 1.35; overflow-wrap: anywhere;" title="PIC: {{ $partner->pic_name ?: '-' }} {{ $partner->pic_contact ? '(' . $partner->pic_contact . ')' : '' }}">
                                        <i class="bi bi-person-badge text-primary me-1"></i>
                                        @if ($partner->pic_name)
                                            <span class="text-dark fw-medium">{{ $partner->pic_name }}</span>
                                            @if ($partner->pic_contact)
                                                <span>&bull; {{ $partner->pic_contact }}</span>
                                            @endif
                                        @else
                                            <span class="fst-italic">PIC belum dilengkapi</span>
                                        @endif
                                    </div>
                                </td>

                                <!-- 3. Jenis & Kelengkapan Data -->
                                <td class="text-center">
                                    <span class="badge {{ $catMeta['class'] }} text-wrap d-inline-block" style="font-size: 0.72rem; max-width: 100%; line-height: 1.3;">
                                        <i class="bi {{ $catMeta['icon'] }} me-1"></i>{{ $partner->category ?: 'Belum Ditentukan' }}
                                    </span>
                                    <div class="mt-1 d-flex justify-content-center align-items-center gap-1 flex-wrap">
                                        <span class="badge border {{ $verBadgeClass }}" style="font-size: 0.72rem;" title="Status Verifikasi Profil: {{ $verStatus }}">
                                            <i class="bi bi-{{ $verStatus === 'Terverifikasi Sah' ? 'patch-check-fill' : ($verStatus === 'Perlu Pembaruan' ? 'exclamation-circle' : 'clock') }} me-0.5"></i>{{ $verStatus === 'Terverifikasi Sah' ? 'Terverifikasi' : $verStatus }}
                                        </span>
                                        @if ($comp['percent'] < 85)
                                            <span class="badge border {{ $compBadgeClass }}" style="font-size: 0.72rem;" title="Kelengkapan Data ({{ $comp['filled'] }}/{{ $comp['total'] }}) — Kurang: {{ implode(', ', $comp['missing']) }}">
                                                Data {{ $comp['percent'] }}%
                                            </span>
                                        @endif
                                    </div>
                                    @if ($comp['percent'] >= 85)
                                        <div class="text-muted mt-1" style="font-size: 0.72rem;" title="Kelengkapan Data ({{ $comp['filled'] }}/{{ $comp['total'] }})">
                                            Data {{ $comp['percent'] }}%
                                        </div>
                                    @endif
                                </td>

                                <!-- 4. Unit Terkait -->
                                <td>
                                    @if ($units->isNotEmpty())
                                        @if ($units->count() > 2)
                                            <div class="text-dark fw-semibold" style="font-size: 0.75rem; line-height: 1.35;" title="{{ $units->join(', ') }}">
                                                {{ $units->take(2)->join(', ') }}
                                                <span class="badge bg-light text-secondary border font-monospace" style="font-size: 0.72rem;">+{{ $units->count() - 2 }}</span>
                                            </div>
                                        @else
                                            <div class="text-dark fw-semibold" style="font-size: 0.75rem; line-height: 1.35;">{{ $units->join(', ') }}</div>
                                        @endif
                                        <div class="text-muted mt-0.5" style="font-size: 0.72rem; line-height: 1.3; overflow-wrap: anywhere;" title="{{ $firstDoc->doc_number ?: $firstDoc->doc_id }}">
                                            Dok: <span class="font-monospace text-primary">{{ $firstDoc->doc_number ?: $firstDoc->doc_id }}</span>
                                        </div>
                                    @else
                                        <div class="text-muted fst-italic" style="font-size: 0.75rem;">Belum ada naskah</div>
                                        <div class="text-muted" style="font-size: 0.72rem;">Siap diajukan di Menu 04</div>
                                    @endif
                                </td>

                                <!-- 5. Lingkup Kerma -->
                                <td>
                                    <div class="text-secondary" style="font-size: 0.75rem; line-height: 1.4;" title="{{ $scopeSummary }}">
                                        {{ Str::limit($scopeSummary, 72) }}
                                    </div>
                                </td>

                                <!-- 6. Naskah Kerma -->
                                <td class="text-center">
                                    @if ($pDocs->isNotEmpty())
                                        <span class="badge badge-naskah-soft rounded-pill" style="font-size: 0.72rem;"><i class="bi bi-file-earmark-text me-1"></i>{{ $pDocs->count() }} Naskah</span>
                                        <div class="text-muted mt-1" style="font-size: 0.72rem;">
                                            {{ $types->join(', ') }}
                                        </div>
                                    @else
                                        <span class="badge bg-light text-muted border" style="font-size: 0.72rem;">0 Naskah</span>
                                    @endif
                                </td>

                                <!-- 7. Status Kemitraan -->
                                <td class="text-center">
                                    @if ($hasActive)
                                        <span class="badge badge-soft-success border border-success-subtle py-1 px-1.5 d-inline-block text-nowrap fw-semibold" style="font-size: 0.72rem;"><i class="bi bi-check-circle-fill me-1"></i>Aktif / Sah</span>
                                    @elseif ($hasWarning)
                                        <span class="badge badge-soft-warning border border-warning-subtle py-1 px-1.5 d-inline-block text-nowrap fw-semibold" style="font-size: 0.72rem;"><i class="bi bi-exclamation-triangle-fill me-1"></i>Peringatan</span>
                                    @elseif ($pDocs->isNotEmpty())
                                        <span class="badge badge-soft-primary border border-primary-subtle py-1 px-1.5 d-inline-block text-nowrap fw-semibold" style="font-size: 0.72rem;"><i class="bi bi-hourglass-split me-1"></i>Tahap FSM</span>
                                    @else
                                        <span class="badge badge-soft-secondary border border-secondary-subtle py-1 px-1.5 d-inline-block text-nowrap fw-semibold" style="font-size: 0.72rem;"><i class="bi bi-journal-check me-1"></i>Terdaftar</span>
                                    @endif
                                </td>

                                <!-- 8. Aksi -->
                                <td class="text-center">
                                    <div class="d-inline-flex justify-content-center align-items-center gap-1 flex-nowrap">
                                        <button type="button" class="btn btn-partner-profile btn-sm px-2 fw-semibold partner-action-btn gap-1 js-open-partner-profile" data-partner-id="{{ $partner->id }}" style="font-size: 0.75rem;" title="Lihat Profil &amp; Riwayat Dokumen">
                                            <i class="bi bi-eye"></i><span>Profil</span>
                                        </button>
                                        <div class="dropdown">
                                            <button class="btn btn-outline-secondary btn-sm px-1.5 shadow-2xs partner-action-btn" type="button" data-bs-toggle="dropdown" data-bs-boundary="viewport" aria-expanded="false" style="font-size: 0.75rem;" title="Opsi Tindakan Lainnya" aria-label="Opsi tindakan untuk {{ $partner->name }}">
                                                <i class="bi bi-three-dots-vertical"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end shadow border-0 small py-1" style="font-size: 0.78rem; min-width: 175px;">
                                                <li>
                                                    <button type="button" class="dropdown-item py-1.5 px-3 text-dark d-flex align-items-center gap-2 js-open-partner-edit" data-partner-id="{{ $partner->id }}">
                                                        <i class="bi bi-pencil-square text-warning"></i> Edit Data Mitra
                                                    </button>
                                                </li>
                                                @if ($firstDoc)
                                                    <li>
                                                        <a class="dropdown-item py-1.5 px-3 text-primary d-flex align-items-center gap-2" href="{{ route('simkerma.index', ['menu' => 4, 'view' => 'detail', 'doc' => $firstDoc->doc_id]) }}">
                                                            <i class="bi bi-box-arrow-up-right"></i> Dokumen Menu 04
                                                        </a>
                                                    </li>
                                                @else
                                                    <li>
                                                        <a class="dropdown-item py-1.5 px-3 text-success d-flex align-items-center gap-2" href="{{ route('simkerma.index', ['menu' => 4, 'view' => 'new', 'partner_name' => $partner->name]) }}">
                                                            <i class="bi bi-plus-lg"></i> Ajukan Naskah Baru
                                                        </a>
                                                    </li>
                                                @endif
                                                @if ($canDeletePartner)
                                                    <li><hr class="dropdown-divider my-1"></li>
                                                    <li>
                                                        <form method="POST" action="{{ route('simkerma.delete-partner') }}" class="m-0" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data mitra \'{{ addslashes($partner->name) }}\' dari master data?');">
                                                            @csrf
                                                            <input type="hidden" name="id" value="{{ $partner->id }}">
                                                            <button type="submit" class="dropdown-item py-1.5 px-3 text-danger d-flex align-items-center gap-2">
                                                                <i class="bi bi-trash"></i> Hapus Mitra
                                                            </button>
                                                        </form>
                                                    </li>
                                                @endif
                                            </ul>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr id="emptyPartnerRow">
                                <td colspan="8" class="text-center py-4 text-muted">Belum ada data mitra terdaftar.</td>
                            </tr>
                        @endforelse
                        <!-- BARIS EMPTY STATE KETIKA PENCARIAN / FILTER TIDAK COCOK -->
                        <tr id="noPartnerMatchRow" style="display: none;">
                            <td colspan="8" class="text-center py-4 text-muted">
                                <i class="bi bi-search fs-3 d-block mb-1 text-secondary"></i>
                                <div>Tidak ada data mitra yang cocok dengan filter atau kata kunci pencarian.</div>
                                <div class="mt-2">
                                    <button type="button" class="btn btn-outline-primary btn-sm py-1 px-3" onclick="resetPartnerFilters()">
                                        <i class="bi bi-arrow-counterclockwise me-1"></i> Reset Pencarian
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- 4. CARD FOOTER DENGAN KONTROL PAGINATION (KONSISTEN MENU 3) -->
            <div class="card-footer bg-white py-2 px-3 border-top d-flex flex-column flex-sm-row justify-content-between align-items-center gap-2 small text-muted">
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <span>Menampilkan <strong id="partnerFooterVisibleCount" class="text-dark">{{ $partners->count() }}</strong> mitra</span>
                    <span class="text-secondary">&bull;</span>
                    <div class="d-flex align-items-center gap-1">
                        <span>Tampilkan:</span>
                        <select id="partnerPerPage" class="form-select form-select-sm py-0 px-2" style="width: auto; height: 30px; font-size: 0.76rem;">
                            <option value="15" selected>15</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="ALL">Semua</option>
                        </select>
                        <span>baris</span>
                    </div>
                </div>
                <div id="partnerPaginationWrapper" class="d-flex align-items-center gap-1"></div>
            </div>
        </div>

        {{-- MODAL INPUT MANDIRI DATA MITRA BARU --}}
        <div class="modal fade" id="createPartnerModal" tabindex="-1" aria-labelledby="createPartnerModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content border-0 shadow-lg">
                    <form method="POST" action="{{ route('simkerma.store-partner') }}" class="d-flex flex-column overflow-hidden h-100">
                        @csrf
                        <div class="modal-header bg-dark text-white py-3">
                            <div class="d-flex align-items-center gap-2">
                                <div class="p-2 bg-light rounded text-dark"><i class="bi bi-building-add fs-4"></i></div>
                                <div>
                                    <h6 class="modal-title fw-bold mb-0" id="createPartnerModalLabel">Input Mandiri Data Mitra Baru</h6>
                                    <small class="text-white-50">Pendaftaran Master Data Mitra Kemitraan Tridharma UNIPAR</small>
                                </div>
                            </div>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>

                        <div class="modal-body p-4 small">
                            <div class="alert alert-info py-2 px-3 mb-3 d-flex align-items-center gap-2" style="font-size: 0.78rem;">
                                <i class="bi bi-info-circle-fill fs-5 text-primary"></i>
                                <div>Mitra yang didaftarkan di sini akan tersimpan di basis data MySQL dan langsung tersinkron di Menu 04 untuk pemilihan cepat saat menyusun pengajuan baru.</div>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-5">
                                    <label class="form-label fw-bold text-dark">Kategori Mitra <span class="text-danger">*</span></label>
                                    <select name="category" id="modal_partner_category" class="form-select form-select-sm" required>
                                        @foreach (\App\Models\Partner::OFFICIAL_CATEGORIES as $catVal => $catLabel)
                                            <option value="{{ $catVal }}" {{ $catVal === 'Perguruan Tinggi Dalam Negeri' ? 'selected' : '' }}>{{ $catLabel }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-7">
                                    <label class="form-label fw-bold text-dark">Nama Lembaga / Instansi Mitra <span class="text-danger">*</span></label>
                                    <div class="hybrid-partner-combobox position-relative">
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text bg-white border-end-0">
                                                <i class="partner-input-icon bi bi-mortarboard-fill text-primary"></i>
                                            </span>
                                            <input type="text" name="name" id="modal_partner_name" class="form-control form-control-sm border-start-0 smart-partner-input" 
                                                   placeholder="Ketik nama kampus (contoh: Sam Ratulangi, ITB, Parna Raya)..." 
                                                   autocomplete="off" required>
                                            <button class="btn btn-outline-secondary btn-clear-partner d-none" type="button" title="Hapus / Reset Pilihan"><i class="bi bi-x-lg"></i></button>
                                        </div>
                                        <!-- Floating Dropdown Suggestions -->
                                        <div class="partner-dropdown-menu shadow-lg bg-white border rounded mt-1 d-none position-absolute w-100" style="z-index: 1060; max-height: 240px; overflow-y: auto;"></div>
                                        <!-- Verified Info Banner -->
                                        <div class="partner-verified-info small mt-1 d-none"></div>
                                        <!-- Helper Tools -->
                                        <div class="d-flex flex-wrap justify-content-between align-items-center mt-1" style="font-size: 0.72rem;">
                                            <small class="partner-mode-desc text-muted"><i class="bi bi-database-check text-success me-1"></i>Direktori PDDikti (4.650+ PT)</small>
                                            <a href="javascript:void(0)" class="text-decoration-none text-primary btn-toggle-manual"><i class="bi bi-pencil-square me-1"></i>Ketik Manual</a>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-dark">Cakupan Wilayah</label>
                                    <select name="scope_coverage" class="form-select form-select-sm">
                                        <option value="Dalam Negeri (Nasional/Lokal)">Dalam Negeri (Nasional/Lokal)</option>
                                        <option value="Internasional">Internasional</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-dark">Kota / Domisili Kantor</label>
                                    <input type="text" name="city" class="form-control form-control-sm" placeholder="Contoh: Kota Manado / Minahasa Utara">
                                </div>

                                <div class="col-12">
                                    <label class="form-label fw-bold text-dark">Alamat Lengkap Kantor</label>
                                    <textarea name="address" class="form-control form-control-sm" rows="2" placeholder="Contoh: Kawasan Bisnis Megamas Blok 2C No. 15, Jl. Piere Tendean, Kota Manado"></textarea>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-dark">Nomor Telepon Kantor Resmi</label>
                                    <input type="text" name="phone" class="form-control form-control-sm" placeholder="Contoh: (0431) 882-9901">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-dark">Email Resmi Kemitraan</label>
                                    <input type="email" name="email" class="form-control form-control-sm" placeholder="Contoh: kemitraan@mitra.co.id">
                                </div>

                                <div class="col-12 pt-2 border-top">
                                    <h6 class="fw-bold text-primary mb-2" style="font-size: 0.85rem;"><i class="bi bi-person-lines-fill me-1"></i> Data Penanggung Jawab (PIC Mitra)</h6>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-bold text-dark">Nama PIC Mitra</label>
                                    <input type="text" name="pic_name" class="form-control form-control-sm" placeholder="Contoh: Bpk. Ir. Dave Kaligis, M.T.">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold text-dark">Jabatan PIC</label>
                                    <input type="text" name="pic_title" class="form-control form-control-sm" placeholder="Contoh: HR &amp; Partnership Manager">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold text-dark">No. WhatsApp / HP PIC</label>
                                    <input type="text" name="pic_contact" class="form-control form-control-sm" placeholder="Contoh: 0812-4411-9988">
                                </div>

                                <div class="col-12">
                                    <label class="form-label fw-bold text-dark">Deskripsi Profil Lembaga Mitra</label>
                                    <textarea name="profile_desc" class="form-control form-control-sm" rows="2" placeholder="Contoh: Perusahaan pengembang perangkat lunak dan talenta industri digital..."></textarea>
                                </div>
                                @if ($canVerifyPartner)
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold text-dark">Status Verifikasi Data Mitra</label>
                                        <select name="status_verification" class="form-select form-select-sm">
                                            @foreach (\App\Models\Partner::VERIFICATION_STATUSES as $vStat)
                                                <option value="{{ $vStat }}" {{ $vStat === 'Belum Diverifikasi' ? 'selected' : '' }}>{{ $vStat }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="modal-footer bg-light py-2 px-3 d-flex justify-content-between">
                            <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary btn-sm fw-semibold shadow-sm">
                                <i class="bi bi-check-circle-fill me-1"></i> Simpan Data Mitra Baru
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- MODAL DINAMIS TUNGGAL MENU 2 (EDIT & PROFIL RIWAYAT MITRA) --}}
        <script id="partnerModalPayloads" type="application/json">@json($partnerPayloads)</script>

        <!-- SHARED MODAL EDIT DATA MITRA -->
        <div class="modal fade" id="sharedPartnerEditModal" tabindex="-1" aria-labelledby="sharedPartnerEditLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content border-0 shadow-lg">
                    <form method="POST" action="{{ route('simkerma.update-partner') }}" class="d-flex flex-column overflow-hidden h-100">
                        @csrf
                        <input type="hidden" name="id" id="spe_id" value="">
                        <div class="modal-header bg-warning py-3 text-dark">
                            <div class="d-flex align-items-center gap-2">
                                <div class="p-2 bg-dark rounded text-warning"><i class="bi bi-pencil-square fs-4"></i></div>
                                <div>
                                    <h6 class="modal-title fw-bold mb-0" id="sharedPartnerEditLabel">Edit Data Mitra</h6>
                                    <small style="color: #343a40;">Sinkronisasi Master Data Mitra ke Seluruh Naskah di SIMKERMA</small>
                                </div>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>

                        <div class="modal-body p-4 small">
                            <div class="alert alert-warning py-2 px-3 mb-3 d-flex align-items-center gap-2" style="font-size: 0.78rem;">
                                <i class="bi bi-exclamation-triangle-fill fs-5 text-dark"></i>
                                <div>
                                    <strong>Integritas Sinkronisasi Otomatis:</strong> Pembaruan nama atau kategori mitra akan disinkronkan ke seluruh dokumen di Menu 1, Menu 3, Menu 4, Menu 5, dan Audit Trail.
                                </div>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-7">
                                    <label class="form-label fw-bold text-dark">Nama Lembaga / Instansi Mitra <span class="text-danger">*</span></label>
                                    <input type="text" name="name" id="spe_name" class="form-control form-control-sm" required>
                                </div>
                                <div class="col-md-5">
                                    <label class="form-label fw-bold text-dark">Kategori Mitra <span class="text-danger">*</span></label>
                                    <select name="category" id="spe_category" class="form-select form-select-sm" required>
                                        @foreach (\App\Models\Partner::OFFICIAL_CATEGORIES as $catVal => $catLabel)
                                            <option value="{{ $catVal }}">{{ $catLabel }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-dark">Cakupan Wilayah</label>
                                    <select name="scope_coverage" id="spe_scope_coverage" class="form-select form-select-sm">
                                        <option value="Dalam Negeri (Nasional/Lokal)">Dalam Negeri (Nasional/Lokal)</option>
                                        <option value="Internasional">Internasional</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-dark">Kota / Domisili Kantor</label>
                                    <input type="text" name="city" id="spe_city" class="form-control form-control-sm">
                                </div>

                                <div class="col-12">
                                    <label class="form-label fw-bold text-dark">Alamat Lengkap Kantor</label>
                                    <textarea name="address" id="spe_address" class="form-control form-control-sm" rows="2"></textarea>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-dark">Nomor Telepon Kantor Resmi</label>
                                    <input type="text" name="phone" id="spe_phone" class="form-control form-control-sm">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-dark">Email Resmi Kemitraan</label>
                                    <input type="email" name="email" id="spe_email" class="form-control form-control-sm">
                                </div>

                                <div class="col-12 pt-2 border-top">
                                    <h6 class="fw-bold text-primary mb-2" style="font-size: 0.85rem;"><i class="bi bi-person-lines-fill me-1"></i> Data Penanggung Jawab (PIC Mitra)</h6>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-bold text-dark">Nama PIC Mitra</label>
                                    <input type="text" name="pic_name" id="spe_pic_name" class="form-control form-control-sm">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold text-dark">Jabatan PIC</label>
                                    <input type="text" name="pic_title" id="spe_pic_title" class="form-control form-control-sm">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold text-dark">No. WhatsApp / HP PIC</label>
                                    <input type="text" name="pic_contact" id="spe_pic_contact" class="form-control form-control-sm">
                                </div>

                                <div class="col-md-8">
                                    <label class="form-label fw-bold text-dark">Deskripsi Profil Lembaga Mitra</label>
                                    <textarea name="profile_desc" id="spe_profile_desc" class="form-control form-control-sm" rows="2"></textarea>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold text-dark">Status Verifikasi Profil</label>
                                    @if ($canVerifyPartner)
                                        <select name="status_verification" id="spe_status_verification" class="form-select form-select-sm">
                                            @foreach (\App\Models\Partner::VERIFICATION_STATUSES as $vStat)
                                                <option value="{{ $vStat }}">{{ $vStat }}</option>
                                            @endforeach
                                        </select>
                                    @else
                                        <input type="text" id="spe_status_verification_ro" class="form-control form-control-sm bg-light" readonly>
                                        <div class="text-muted mt-1" style="font-size: 0.68rem;">Verifikasi dikelola oleh Role LEGAL / ADMIN / MASTER.</div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer bg-light py-2 px-3 d-flex justify-content-between">
                            <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-warning btn-sm fw-semibold shadow-sm text-dark">
                                <i class="bi bi-save-fill me-1"></i> Perbarui &amp; Sinkronkan Data Mitra
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- SHARED MODAL DETAIL PROFIL & RIWAYAT MITRA -->
        <div class="modal fade" id="sharedPartnerProfileModal" tabindex="-1" aria-labelledby="sharedPartnerProfileLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header bg-dark text-white py-3">
                        <div class="d-flex align-items-center gap-2">
                            <div class="p-2 bg-light rounded text-dark"><i class="bi bi-building fs-4"></i></div>
                            <div>
                                <h6 class="modal-title fw-bold mb-0" id="sharedPartnerProfileLabel">-</h6>
                                <small class="text-white-50" id="spp_subtitle">-</small>
                            </div>
                        </div>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body p-4" id="spp_body">
                        <!-- Diisi dinamis oleh JS -->
                    </div>

                    <div class="modal-footer bg-light py-2 px-3 d-flex justify-content-between">
                        <div class="d-flex flex-wrap gap-2">
                            <button type="button" class="btn btn-warning btn-sm fw-semibold text-dark" id="spp_btn_edit">
                                <i class="bi bi-pencil-square me-1"></i> Edit Data Mitra
                            </button>
                            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="window.print()">
                                <i class="bi bi-printer me-1"></i> Cetak Profil Mitra
                            </button>
                            <a href="#" id="spp_btn_new_doc" class="btn btn-outline-primary btn-sm">
                                <i class="bi bi-plus-circle me-1"></i> Ajukan Naskah Baru
                            </a>
                        </div>
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>

        @push('scripts')
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            let partnerPayloads = {};
            try {
                const raw = document.getElementById('partnerModalPayloads');
                if (raw && raw.textContent) partnerPayloads = JSON.parse(raw.textContent);
            } catch (e) {}

            function escapePartnerHtml(str) {
                return String(str ?? '')
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;')
                    .replace(/'/g, '&#39;');
            }

            function getPartnerModal(el) {
                if (!el || typeof bootstrap === 'undefined') return null;
                return bootstrap.Modal.getInstance(el) || new bootstrap.Modal(el);
            }

            window.openPartnerEditModal = function(partnerId) {
                const p = partnerPayloads[String(partnerId)];
                const modalEl = document.getElementById('sharedPartnerEditModal');
                if (!p || !modalEl) return;

                document.getElementById('sharedPartnerEditLabel').innerText = 'Edit Data Mitra: ' + (p.name || '-');
                document.getElementById('spe_id').value = p.id || '';
                document.getElementById('spe_name').value = p.name || '';

                const catSel = document.getElementById('spe_category');
                if (catSel) {
                    const targetCat = p.category || '';
                    let found = Array.from(catSel.options).some(opt => opt.value === targetCat);
                    if (!found && targetCat) {
                        const legacyOpt = document.createElement('option');
                        legacyOpt.value = targetCat;
                        legacyOpt.text = targetCat + ' (Data Eksisting)';
                        catSel.appendChild(legacyOpt);
                    }
                    catSel.value = targetCat;
                }

                const scopeSel = document.getElementById('spe_scope_coverage');
                if (scopeSel) scopeSel.value = p.scope_coverage || 'Dalam Negeri (Nasional/Lokal)';

                document.getElementById('spe_city').value = p.city || '';
                document.getElementById('spe_address').value = p.address || '';
                document.getElementById('spe_phone').value = p.phone || '';
                document.getElementById('spe_email').value = p.email || '';
                document.getElementById('spe_pic_name').value = p.pic_name || '';
                document.getElementById('spe_pic_title').value = p.pic_title || '';
                document.getElementById('spe_pic_contact').value = p.pic_contact || '';
                document.getElementById('spe_profile_desc').value = p.profile_desc || '';

                const verSel = document.getElementById('spe_status_verification');
                if (verSel) verSel.value = p.status_verification || 'Belum Diverifikasi';
                const verRo = document.getElementById('spe_status_verification_ro');
                if (verRo) verRo.value = p.status_verification || 'Belum Diverifikasi';

                const inst = getPartnerModal(modalEl);
                if (inst) inst.show();
            };

            window.openPartnerProfileModal = function(partnerId) {
                const p = partnerPayloads[String(partnerId)];
                const modalEl = document.getElementById('sharedPartnerProfileModal');
                if (!p || !modalEl) return;

                document.getElementById('sharedPartnerProfileLabel').innerText = p.name || '-';
                document.getElementById('spp_subtitle').innerText = (p.category || '-') + ' \u2022 ' + (p.scope_coverage || '-');
                document.getElementById('spp_btn_new_doc').href = p.new_doc_url || '#';
                document.getElementById('spp_btn_edit').onclick = function() {
                    switchPartnerModal('sharedPartnerProfileModal', 'sharedPartnerEditModal', function() {
                        window.openPartnerEditModal(partnerId);
                    });
                };

                const comp = p.completeness || { filled: 0, total: 6, percent: 0, label: 'Perlu Dilengkapi', missing: [] };
                const verBadgeCls = p.status_verification === 'Terverifikasi Sah'
                    ? 'badge-soft-success border-success-subtle'
                    : (p.status_verification === 'Perlu Pembaruan' ? 'badge-soft-warning border-warning-subtle' : 'badge-soft-secondary border-secondary-subtle');
                const statusBadgeHtml = p.has_active
                    ? '<span class="badge bg-success"><i class="bi bi-check-circle-fill me-1"></i>Terverifikasi Sah &amp; Berjalan</span>'
                    : (p.has_warning
                        ? '<span class="badge bg-warning text-dark"><i class="bi bi-exclamation-triangle-fill me-1"></i>Masa Berlaku Mendekati Ambang Batas</span>'
                        : (p.docs && p.docs.length > 0
                            ? '<span class="badge bg-info text-white"><i class="bi bi-hourglass-split me-1"></i>Dalam Proses Alur FSM</span>'
                            : '<span class="badge bg-secondary"><i class="bi bi-journal-check me-1"></i>Mitra Terdaftar di Sistem</span>'));

                const missingNote = (comp.missing && comp.missing.length > 0)
                    ? `<div class="text-danger mt-1" style="font-size: 0.7rem;"><i class="bi bi-exclamation-circle me-1"></i>Belum lengkap: ${escapePartnerHtml(comp.missing.join(', '))}</div>`
                    : `<div class="text-success mt-1" style="font-size: 0.7rem;"><i class="bi bi-check-circle me-1"></i>Seluruh field identitas, kontak &amp; PIC telah terisi lengkap.</div>`;

                const docBadgeMap = {
                    'DRAFT': 'badge-soft-secondary border border-secondary-subtle',
                    'REVIEW': 'badge-soft-info border border-info-subtle',
                    'VERIFIED': 'badge-soft-primary border border-primary-subtle',
                    'ACTIVE': 'badge-soft-success border border-success-subtle',
                    'WARNING': 'badge-soft-warning border border-warning-subtle',
                    'EXPIRED': 'badge-soft-danger border border-danger-subtle',
                    'CANCELLED': 'bg-dark text-white'
                };
                const docRowsHtml = (p.docs && p.docs.length > 0)
                    ? p.docs.map(function(d) {
                        const bCls = docBadgeMap[d.status] || 'bg-secondary text-white';
                        return `
                            <tr>
                                <td>
                                    <div class="fw-bold text-dark">${escapePartnerHtml(d.doc_number || 'Dalam Proses')}</div>
                                    <span class="badge bg-light text-muted border font-monospace" style="font-size: 0.65rem;">${escapePartnerHtml(d.reg_id)}</span>
                                </td>
                                <td><span class="badge bg-dark" style="font-size: 0.68rem;">${escapePartnerHtml(d.doc_type)}</span></td>
                                <td>
                                    <div class="text-secondary" style="max-width: 220px;">${escapePartnerHtml((d.scope || '-').slice(0, 80))}</div>
                                    <div class="text-muted" style="font-size: 0.68rem;">Unit: ${escapePartnerHtml(d.unit || '-')}</div>
                                </td>
                                <td>
                                    <div>${escapePartnerHtml(d.validity || 'Belum Ditetapkan')}</div>
                                    ${d.date_signed ? `<small class="text-muted" style="font-size: 0.68rem;">TTD: ${escapePartnerHtml(d.date_signed)}</small>` : ''}
                                </td>
                                <td class="text-center"><span class="badge ${bCls} py-1 px-2 fw-bold" style="font-size: 0.7rem;">${escapePartnerHtml(d.status)}</span></td>
                                <td class="text-end">
                                    <a href="${escapePartnerHtml(d.url)}" class="btn btn-outline-dark btn-sm py-0 px-2" style="font-size: 0.68rem;" title="Kelola Alur FSM"><i class="bi bi-sliders me-1"></i>FSM</a>
                                </td>
                            </tr>
                        `;
                    }).join('')
                    : '<tr><td colspan="6" class="text-center py-3 text-muted">Belum ada naskah kerja sama yang tercatat untuk mitra ini.</td></tr>';

                document.getElementById('spp_body').innerHTML = `
                    <div class="card bg-light border mb-3">
                        <div class="card-header bg-white py-2 px-3 fw-bold small text-primary d-flex flex-wrap justify-content-between align-items-center gap-1">
                            <span><i class="bi bi-info-circle-fill me-1"></i> Identitas &amp; Profil Lembaga Mitra</span>
                            <div class="d-flex align-items-center gap-1">
                                <span class="badge border ${escapePartnerHtml(comp.badge_class || 'badge-soft-secondary')}" style="font-size: 0.65rem;">Kelengkapan: ${comp.filled}/${comp.total} (${comp.percent}%)</span>
                                <span class="badge border ${verBadgeCls}" style="font-size: 0.65rem;">${escapePartnerHtml(p.status_verification)}</span>
                            </div>
                        </div>
                        <div class="card-body p-3 small">
                            <div class="row g-3">
                                <div class="col-sm-6">
                                    <div class="text-muted fw-bold" style="font-size: 0.72rem;">NAMA INSTANSI RESMI:</div>
                                    <div class="fw-bold text-dark fs-6">${escapePartnerHtml(p.name)}</div>
                                    <div class="mt-2 text-muted fw-bold" style="font-size: 0.72rem;">JENIS &amp; KATEGORI MITRA:</div>
                                    <div><span class="badge bg-light text-dark border">${escapePartnerHtml(p.category || '-')}</span> &bull; ${escapePartnerHtml(p.scope_coverage)}</div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="text-muted fw-bold" style="font-size: 0.72rem;">ALAMAT LENGKAP KANTOR:</div>
                                    <div class="text-dark">${escapePartnerHtml(p.address || (p.city ? ('Kota/Kab: ' + p.city) : 'Belum dilengkapi'))}</div>
                                    <div class="mt-2 text-muted fw-bold" style="font-size: 0.72rem;">KONTAK RESMI PERUSAHAAN / INSTANSI:</div>
                                    <div>
                                        <i class="bi bi-telephone-fill text-primary me-1"></i> ${escapePartnerHtml(p.phone || 'Belum diisi')}<br>
                                        <i class="bi bi-envelope-fill text-danger me-1"></i> ${p.email ? `<a href="mailto:${escapePartnerHtml(p.email)}" class="text-decoration-none">${escapePartnerHtml(p.email)}</a>` : 'Belum diisi'}
                                    </div>
                                </div>
                                <div class="col-12 pt-1 border-top">
                                    <div class="text-muted fw-bold" style="font-size: 0.72rem;">PROFIL &amp; BIDANG OPERASIONAL MITRA:</div>
                                    <div class="text-secondary mt-1">${escapePartnerHtml(p.profile_desc || 'Belum ada deskripsi profil mitra.')}</div>
                                    ${missingNote}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card bg-light border mb-3">
                        <div class="card-header bg-white py-2 px-3 fw-bold small text-dark d-flex justify-content-between align-items-center">
                            <span><i class="bi bi-person-badge-fill text-primary me-1"></i> Penanggung Jawab (PIC Mitra) &amp; Relasi Kampus</span>
                            <span class="badge bg-primary text-white" style="font-size: 0.65rem;">PIC MITRA</span>
                        </div>
                        <div class="card-body p-3 small">
                            <div class="row g-2">
                                <div class="col-sm-6">
                                    <div class="p-2 bg-white rounded border">
                                        <div class="text-muted fw-bold" style="font-size: 0.7rem;">PENANGGUNG JAWAB DARI PIHAK MITRA:</div>
                                        <div class="fw-bold text-dark fs-6 mt-1">${escapePartnerHtml(p.pic_name || 'Belum diisi')}</div>
                                        <div class="text-muted">${escapePartnerHtml(p.pic_title || '-')}</div>
                                        <div class="mt-2">
                                            <span class="badge badge-soft-success border border-success-subtle">
                                                <i class="bi bi-whatsapp me-1"></i> Kontak PIC: ${escapePartnerHtml(p.pic_contact || 'Belum diisi')}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="p-2 bg-white rounded border">
                                        <div class="text-muted fw-bold" style="font-size: 0.7rem;">UNIT PENGUSUL DI UNIPAR:</div>
                                        <div class="fw-bold text-primary mt-1">${(p.units && p.units.length > 0) ? escapePartnerHtml(p.units.join(', ')) : 'Belum Ada Naskah Diajukan'}</div>
                                        <div class="text-muted mt-2" style="font-size: 0.7rem;">Status Kemitraan:</div>
                                        <div>${statusBadgeHtml}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card border mb-0">
                        <div class="card-header bg-white py-2 px-3 fw-bold small text-dark d-flex justify-content-between align-items-center">
                            <span><i class="bi bi-clock-history text-primary me-1"></i> Riwayat Kerja Sama (${p.docs ? p.docs.length : 0} Naskah)</span>
                            <span class="badge bg-dark rounded-pill">${(p.types && p.types.length > 0) ? escapePartnerHtml(p.types.join(', ')) : '0 Dokumen'}</span>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-sm table-hover align-middle mb-0 small" style="font-size: 0.75rem;">
                                <thead class="table-light">
                                    <tr>
                                        <th>No. Registrasi &amp; Surat</th>
                                        <th>Jenis</th>
                                        <th>Ruang Lingkup Kegiatan</th>
                                        <th>Masa Berlaku</th>
                                        <th class="text-center">Status FSM</th>
                                        <th class="text-end">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>${docRowsHtml}</tbody>
                            </table>
                        </div>
                    </div>
                `;

                const inst = getPartnerModal(modalEl);
                if (inst) inst.show();
            };

            const partnerTbody = document.querySelector('#partnerTable tbody');
            const partnerRows = Array.from(document.querySelectorAll('#partnerTable tbody tr.partner-row'));
            const sortableHeaders = document.querySelectorAll('#partnerTable thead th.partner-sortable');
            const noMatchRow = document.getElementById('noPartnerMatchRow');
            const visibleBadge = document.getElementById('partnerVisibleCount');
            const footerVisibleEl = document.getElementById('partnerFooterVisibleCount');
            const perPageSelect = document.getElementById('partnerPerPage');
            const paginationWrapper = document.getElementById('partnerPaginationWrapper');
            const partnerExcelBtn = document.getElementById('btnPartnerExportExcel');
            const partnerActiveFilterBadge = document.getElementById('partnerActiveFilterBadge');

            const PARTNER_STATE_KEY = 'simkerma_partner_state_v1';
            let partnerPage = 1;
            let partnerSortKey = 'name';
            let partnerSortDir = 'asc';

            partnerRows.forEach((r, idx) => r.dataset.origIdx = String(idx));

            function savePartnerState() {
                try {
                    sessionStorage.setItem(PARTNER_STATE_KEY, JSON.stringify({
                        q: document.getElementById('partnerSearchInput')?.value || '',
                        cat: document.getElementById('partnerCategoryFilter')?.value || '',
                        status: document.getElementById('partnerStatusFilter')?.value || '',
                        perPage: perPageSelect ? perPageSelect.value : '15',
                        sortKey: partnerSortKey,
                        sortDir: partnerSortDir,
                        page: partnerPage
                    }));
                } catch (e) {}
            }

            function restorePartnerState() {
                try {
                    const raw = sessionStorage.getItem(PARTNER_STATE_KEY);
                    if (!raw) return;
                    const st = JSON.parse(raw);
                    if (!st || typeof st !== 'object') return;
                    const sEl = document.getElementById('partnerSearchInput');
                    const cEl = document.getElementById('partnerCategoryFilter');
                    const stEl = document.getElementById('partnerStatusFilter');
                    if (sEl && typeof st.q === 'string') sEl.value = st.q;
                    if (cEl && typeof st.cat === 'string') cEl.value = st.cat;
                    if (stEl && typeof st.status === 'string') stEl.value = st.status;
                    if (perPageSelect && st.perPage) perPageSelect.value = st.perPage;
                    if (st.sortKey) partnerSortKey = st.sortKey;
                    if (st.sortDir) partnerSortDir = st.sortDir;
                    if (st.page && Number.isInteger(st.page) && st.page >= 1) partnerPage = st.page;
                } catch (e) {}
            }

            function updatePartnerSortUI() {
                sortableHeaders.forEach(function(th) {
                    const key = th.getAttribute('data-sort-key');
                    const icon = th.querySelector('.partner-sort-icon');
                    th.classList.remove('sort-active');
                    if (icon) icon.className = 'bi partner-sort-icon bi-arrow-down-up';
                    if (key === partnerSortKey) {
                        th.classList.add('sort-active');
                        th.setAttribute('data-sort-dir', partnerSortDir);
                        if (icon) {
                            icon.className = 'bi partner-sort-icon ' + (partnerSortDir === 'desc' ? 'bi-sort-down' : 'bi-sort-up');
                        }
                    } else {
                        th.removeAttribute('data-sort-dir');
                    }
                });
            }

            function renderPartnerPagination(totalPages) {
                if (!paginationWrapper) return;
                if (totalPages <= 1) {
                    paginationWrapper.innerHTML = '';
                    return;
                }
                let html = `<button type="button" class="btn btn-sm btn-outline-secondary py-1 px-2 ${partnerPage === 1 ? 'disabled opacity-50' : ''}" data-page="${partnerPage - 1}" aria-label="Halaman sebelumnya"><i class="bi bi-chevron-left"></i></button>`;
                for (let p = 1; p <= totalPages; p++) {
                    if (p === 1 || p === totalPages || (p >= partnerPage - 1 && p <= partnerPage + 1)) {
                        html += `<button type="button" class="btn btn-sm ${p === partnerPage ? 'btn-primary' : 'btn-outline-secondary'} py-1 px-2 fw-semibold" data-page="${p}" aria-label="Halaman ${p}">${p}</button>`;
                    } else if (p === partnerPage - 2 || p === partnerPage + 2) {
                        html += `<span class="px-1 text-muted">&hellip;</span>`;
                    }
                }
                html += `<button type="button" class="btn btn-sm btn-outline-secondary py-1 px-2 ${partnerPage === totalPages ? 'disabled opacity-50' : ''}" data-page="${partnerPage + 1}" aria-label="Halaman berikutnya"><i class="bi bi-chevron-right"></i></button>`;
                paginationWrapper.innerHTML = html;

                paginationWrapper.querySelectorAll('button[data-page]').forEach(function(btn) {
                    btn.addEventListener('click', function() {
                        const target = parseInt(this.getAttribute('data-page'), 10);
                        if (!isNaN(target) && target >= 1 && target <= totalPages) {
                            partnerPage = target;
                            window.renderPartnersTable(false);
                        }
                    });
                });
            }

            let lastMatchedPartners = partnerRows;

            window.renderPartnersTable = function(resetPage = false) {
                if (resetPage) partnerPage = 1;

                const query = (document.getElementById('partnerSearchInput')?.value || '').toLowerCase().trim();
                const categoryFilter = (document.getElementById('partnerCategoryFilter')?.value || '').toLowerCase().trim();
                const statusFilter = (document.getElementById('partnerStatusFilter')?.value || '').trim();

                const matched = partnerRows.filter(function(row) {
                    const searchText = (row.getAttribute('data-search') || row.innerText).toLowerCase();
                    const rowCatGroup = (row.getAttribute('data-category-group') || '').toLowerCase().trim();
                    const rowStatus = (row.getAttribute('data-status') || '').trim();
                    const rowVer = (row.getAttribute('data-verification') || '').trim();
                    const rowComplete = row.getAttribute('data-complete') === '1';

                    const matchesQuery = !query || searchText.includes(query);
                    const matchesCat = !categoryFilter || rowCatGroup === categoryFilter;

                    let matchesStatus = true;
                    if (statusFilter) {
                        if (statusFilter.startsWith('VER_')) {
                            matchesStatus = (rowVer === statusFilter.slice(4));
                        } else if (statusFilter === 'COMP_INCOMPLETE') {
                            matchesStatus = !rowComplete;
                        } else {
                            matchesStatus = (rowStatus === statusFilter);
                        }
                    }

                    return matchesQuery && matchesCat && matchesStatus;
                });

                matched.sort(function(a, b) {
                    let cmp = 0;
                    if (partnerSortKey === 'name') {
                        const na = a.dataset.sortName || '';
                        const nb = b.dataset.sortName || '';
                        cmp = partnerSortDir === 'desc' ? nb.localeCompare(na, 'id') : na.localeCompare(nb, 'id');
                    } else if (partnerSortKey === 'completeness') {
                        const ca = parseInt(a.dataset.sortCompleteness || '0', 10);
                        const cb = parseInt(b.dataset.sortCompleteness || '0', 10);
                        cmp = partnerSortDir === 'desc' ? (cb - ca) : (ca - cb);
                    } else if (partnerSortKey === 'docs') {
                        const da = parseInt(a.dataset.sortDocs || '0', 10);
                        const db = parseInt(b.dataset.sortDocs || '0', 10);
                        cmp = partnerSortDir === 'desc' ? (db - da) : (da - db);
                    } else if (partnerSortKey === 'status') {
                        const sa = parseInt(a.dataset.sortStatus || '0', 10);
                        const sb = parseInt(b.dataset.sortStatus || '0', 10);
                        cmp = partnerSortDir === 'desc' ? (sb - sa) : (sa - sb);
                    }
                    return cmp !== 0 ? cmp : parseInt(a.dataset.origIdx, 10) - parseInt(b.dataset.origIdx, 10);
                });

                lastMatchedPartners = matched;
                const activeFilterCount = (query ? 1 : 0) + (categoryFilter ? 1 : 0) + (statusFilter ? 1 : 0);
                if (partnerActiveFilterBadge) {
                    if (activeFilterCount > 0) {
                        partnerActiveFilterBadge.textContent = String(activeFilterCount);
                        partnerActiveFilterBadge.classList.remove('d-none');
                    } else {
                        partnerActiveFilterBadge.classList.add('d-none');
                    }
                }
                const totalMatched = matched.length;
                if (visibleBadge) visibleBadge.textContent = totalMatched;
                if (footerVisibleEl) footerVisibleEl.textContent = totalMatched;
                if (partnerExcelBtn) {
                    const isEmpty = (totalMatched === 0);
                    partnerExcelBtn.disabled = isEmpty;
                    partnerExcelBtn.classList.toggle('opacity-50', isEmpty);
                    partnerExcelBtn.title = isEmpty
                        ? 'Tidak ada data mitra terfilter untuk diekspor'
                        : 'Ekspor Data Mitra Terfilter ke Excel';
                }

                if (totalMatched === 0) {
                    partnerRows.forEach(r => r.style.display = 'none');
                    if (noMatchRow) noMatchRow.style.display = '';
                    if (paginationWrapper) paginationWrapper.innerHTML = '';
                    savePartnerState();
                    return;
                }

                if (noMatchRow) noMatchRow.style.display = 'none';

                if (partnerTbody) {
                    matched.forEach(r => partnerTbody.appendChild(r));
                    if (noMatchRow) partnerTbody.appendChild(noMatchRow);
                }

                const perPageVal = perPageSelect ? perPageSelect.value : '15';
                const perPage = (perPageVal === 'ALL') ? totalMatched : parseInt(perPageVal, 10);
                const totalPages = Math.ceil(totalMatched / perPage) || 1;
                if (partnerPage > totalPages) partnerPage = totalPages;

                const startIdx = (partnerPage - 1) * perPage;
                const endIdx = startIdx + perPage;

                partnerRows.forEach(r => r.style.display = 'none');
                matched.forEach(function(row, idx) {
                    const visible = (idx >= startIdx && idx < endIdx);
                    row.style.display = visible ? '' : 'none';
                    const numCell = row.querySelector('.partner-row-num');
                    if (numCell) numCell.textContent = String(idx + 1);
                });

                renderPartnerPagination(totalPages);
                savePartnerState();
            };

            window.filterPartnersTable = function() {
                window.renderPartnersTable(true);
            };

            window.resetPartnerFilters = function() {
                const searchInput = document.getElementById('partnerSearchInput');
                const catFilter = document.getElementById('partnerCategoryFilter');
                const statFilter = document.getElementById('partnerStatusFilter');

                if (searchInput) searchInput.value = '';
                if (catFilter) catFilter.value = '';
                if (statFilter) statFilter.value = '';
                partnerSortKey = 'name';
                partnerSortDir = 'asc';
                partnerPage = 1;
                updatePartnerSortUI();
                try { sessionStorage.removeItem(PARTNER_STATE_KEY); } catch (e) {}
                window.renderPartnersTable(true);
            };

            window.exportFilteredPartnersToExcel = function() {
                if (!lastMatchedPartners || !lastMatchedPartners.length) return;
                const headers = [
                    'No.',
                    'Nama Mitra',
                    'Kota / Domisili',
                    'Cakupan Wilayah',
                    'Nama PIC',
                    'Kontak PIC',
                    'Kategori Resmi',
                    'Status Verifikasi',
                    'Kelengkapan Data',
                    'Unit Terkait UNIPAR',
                    'Lingkup Kerja Sama',
                    'Jumlah Naskah',
                    'Jenis Dokumen',
                    'Status Kemitraan'
                ];
                let html = '<table border="1"><thead><tr>';
                headers.forEach(h => { html += '<th>' + escapePartnerHtml(h) + '</th>'; });
                html += '</tr></thead><tbody>';

                lastMatchedPartners.forEach(function(row, idx) {
                    const ds = row.dataset;
                    const cols = [
                        String(idx + 1),
                        ds.exportName || '-',
                        ds.exportCity || '-',
                        ds.exportScopeCoverage || '-',
                        ds.exportPicName || '-',
                        ds.exportPicContact || '-',
                        ds.exportCategory || '-',
                        ds.exportVerification || '-',
                        ds.exportCompleteness || '-',
                        ds.exportUnits || '-',
                        ds.exportDocScope || '-',
                        ds.exportDocCount || '0',
                        ds.exportDocTypes || '-',
                        ds.exportStatus || '-'
                    ];
                    html += '<tr>' + cols.map(c => '<td>' + escapePartnerHtml(c) + '</td>').join('') + '</tr>';
                });
                html += '</tbody></table>';

                const blob = new Blob(['\ufeff', html], { type: 'application/vnd.ms-excel;charset=utf-8;' });
                const url = URL.createObjectURL(blob);
                const a = document.createElement('a');
                const today = new Date().toISOString().slice(0, 10);
                a.href = url;
                a.download = `Data_Master_Mitra_${today}.xls`;
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
                URL.revokeObjectURL(url);
            };

            sortableHeaders.forEach(function(th) {
                function triggerPartnerSort() {
                    const key = th.getAttribute('data-sort-key');
                    if (partnerSortKey === key) {
                        partnerSortDir = (partnerSortDir === 'desc') ? 'asc' : 'desc';
                    } else {
                        partnerSortKey = key;
                        partnerSortDir = (key === 'name') ? 'asc' : 'desc';
                    }
                    updatePartnerSortUI();
                    window.renderPartnersTable(true);
                }
                th.addEventListener('click', triggerPartnerSort);
                th.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter' || e.key === ' ') {
                        e.preventDefault();
                        triggerPartnerSort();
                    }
                });
            });

            if (perPageSelect) {
                perPageSelect.addEventListener('change', function() {
                    window.renderPartnersTable(true);
                });
            }

            if (partnerTbody) {
                partnerTbody.addEventListener('click', function(e) {
                    const profBtn = e.target.closest('.js-open-partner-profile');
                    if (profBtn) {
                        window.openPartnerProfileModal(profBtn.getAttribute('data-partner-id'));
                        return;
                    }
                    const editBtn = e.target.closest('.js-open-partner-edit');
                    if (editBtn) {
                        window.openPartnerEditModal(editBtn.getAttribute('data-partner-id'));
                    }
                });
            }

            restorePartnerState();
            updatePartnerSortUI();
            window.renderPartnersTable(false);

            // Pintasan keyboard '/' untuk langsung fokus ke kotak pencarian mitra
            document.addEventListener('keydown', function(e) {
                if (e.key !== '/' || e.ctrlKey || e.metaKey || e.altKey) return;
                const tag = (document.activeElement?.tagName || '').toUpperCase();
                if (tag === 'INPUT' || tag === 'TEXTAREA' || tag === 'SELECT' || document.activeElement?.isContentEditable) return;
                const searchEl = document.getElementById('partnerSearchInput');
                if (searchEl && searchEl.offsetParent !== null) {
                    e.preventDefault();
                    searchEl.focus();
                    searchEl.select();
                }
            });
        });

        function switchPartnerModal(closeModalId, openModalId, onReady) {
            const closeEl = document.getElementById(closeModalId);
            const openEl = document.getElementById(openModalId);
            if (!closeEl || !openEl) return;

            const closeInst = bootstrap.Modal.getInstance(closeEl);
            if (closeInst) {
                const handler = function() {
                    closeEl.removeEventListener('hidden.bs.modal', handler);
                    if (typeof onReady === 'function') onReady();
                    else (bootstrap.Modal.getInstance(openEl) || new bootstrap.Modal(openEl)).show();
                };
                closeEl.addEventListener('hidden.bs.modal', handler);
                closeInst.hide();
            } else {
                if (typeof onReady === 'function') onReady();
                else (bootstrap.Modal.getInstance(openEl) || new bootstrap.Modal(openEl)).show();
            }
        }
        </script>
        @endpush

    {{-- ===================================================================================== --}}
    {{-- 3. PENGELOLAAN DOKUMEN KERJA SAMA (REPOSITORI ARSIP DINAMIS DARI MYSQL)               --}}
    {{-- ===================================================================================== --}}
    @elseif ($activeMenu === 3)
        @php
            $mouCount = $documents->filter(fn($d) => stripos($d->doc_type, 'MoU') !== false)->count();
            $moaCount = $documents->filter(fn($d) => stripos($d->doc_type, 'MoA') !== false)->count();
            $iaCount = $documents->filter(fn($d) => stripos($d->doc_type, 'IA') !== false)->count();
            $legacyCount = $documents->filter(fn($d) => $d->is_legacy || stripos($d->track, 'Lampau') !== false || stripos($d->track, 'legacy') !== false)->count();

            $repoUnits = $documents->pluck('unit')->filter(fn($v) => trim((string)$v) !== '')->unique()->sort()->values();
            $repoCategories = $documents->pluck('partner_category')->filter(fn($v) => trim((string)$v) !== '')->unique()->sort()->values();
            $repoYears = $documents->map(function($d) {
                $dt = $d->date_signed ?: ($d->date_approved ?: $d->created_at);
                return $dt ? $dt->format('Y') : null;
            })->filter()->unique()->sortDesc()->values();

            $resolvePublicDocFile = function (?string $relPath): ?array {
                if (empty($relPath)) return null;
                $p1 = public_path($relPath);
                if (file_exists($p1) && is_file($p1)) {
                    $b = @filesize($p1) ?: 0;
                    return [
                        'url' => asset($relPath),
                        'name' => basename($relPath),
                        'size_bytes' => $b,
                        'size_text' => $b >= 1048576 ? round($b / 1048576, 1) . ' MB' : round($b / 1024, 0) . ' KB',
                    ];
                }
                $p2 = public_path('uploads/documents/' . $relPath);
                if (file_exists($p2) && is_file($p2)) {
                    $b = @filesize($p2) ?: 0;
                    return [
                        'url' => asset('uploads/documents/' . $relPath),
                        'name' => basename($relPath),
                        'size_bytes' => $b,
                        'size_text' => $b >= 1048576 ? round($b / 1048576, 1) . ' MB' : round($b / 1024, 0) . ' KB',
                    ];
                }
                return null;
            };

            $repoStatsSigned = 0;
            $repoStatsDraftOnly = 0;
            $repoStatsProposal = 0;
            $repoStatsReport = 0;
            $repoStatsNoMain = 0;
            $repoPayloads = [];
            $repoDocIdMap = [];
        @endphp

        <div class="card card-wireframe shadow-sm border mb-4">
            <div class="card-header bg-white py-3 border-bottom d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2">
                <div>
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <h6 class="mb-0 fw-bold text-uppercase">
                            <i class="bi bi-file-earmark-text text-primary me-1"></i> Repositori Dokumen Resmi Kerja Sama
                        </h6>
                        <span class="badge bg-primary text-white font-monospace" style="font-size: 0.72rem;">{{ $documents->count() }} NASKAH TERDAFTAR</span>
                    </div>
                    <small class="text-muted" style="font-size: 0.75rem;">Penyimpanan Terorganisasi Naskah MoU, MoA/PKS, IA, Proposal, Dokumen Pendukung &amp; Laporan Pelaksanaan</small>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('simkerma.index', ['menu' => 4, 'view' => 'new']) }}" class="btn btn-primary btn-sm fw-semibold shadow-sm repo-cta-primary">
                        <i class="bi bi-cloud-upload me-1"></i> Unggah / Ajukan Naskah Baru
                    </a>
                </div>
            </div>

            <!-- KONSOL FILTER & TAB KATEGORI DOKUMEN -->
            <div class="repo-filter-bar">
                <div class="d-flex flex-wrap align-items-end justify-content-between gap-2">
                    <!-- Tab Kategori Dokumen (Menempel ke Border Bawah + Scroll Horizontal Mobile) -->
                    <ul class="nav nav-tabs small fw-semibold flex-nowrap" id="repoTabs" style="white-space: nowrap; max-width: 100%;">
                        <li class="nav-item">
                            <button class="nav-link active repo-tab-btn" data-type="ALL">
                                Semua ({{ $documents->count() }})
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link repo-tab-btn" data-type="MoU">
                                <span class="repo-type-dot" style="background-color: #5156be;"></span>MoU ({{ $mouCount }})
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link repo-tab-btn" data-type="MoA">
                                <span class="repo-type-dot" style="background-color: #0d9488;"></span>PKS / MoA ({{ $moaCount }})
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link repo-tab-btn" data-type="IA">
                                <span class="repo-type-dot" style="background-color: #7e22ce;"></span>IA ({{ $iaCount }})
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link repo-tab-btn" data-type="Legacy">
                                Lampau ({{ $legacyCount }})
                            </button>
                        </li>
                    </ul>

                    <!-- Pencarian & Tombol Kontrol Lanjutan (Tinggi Seragam 31px) -->
                    <div class="repo-controls d-flex align-items-center gap-2 flex-wrap">
                        <div class="input-group input-group-sm repo-search-group">
                            <span class="input-group-text bg-white border-end-0 text-muted">
                                <i class="bi bi-search"></i>
                            </span>
                            <input type="text" id="repoSearchInput" class="form-control border-start-0 border-end-0" placeholder="Cari nomor, mitra, berkas... (Tekan /)" autocomplete="off">
                            <button class="btn btn-outline-secondary border-start-0 d-none" type="button" id="btnRepoClearSearch" title="Hapus Pencarian" aria-label="Hapus Pencarian">
                                <i class="bi bi-x-lg"></i>
                            </button>
                        </div>
                        <button type="button" class="btn btn-outline-secondary btn-sm btn-repo-xs d-inline-flex align-items-center gap-1 bg-white" id="btnToggleRepoAdvancedFilter" style="height: 31px;" title="Filter Lanjutan (Unit, Kategori, Tahun, Kelengkapan Berkas, Status)">
                            <i class="bi bi-funnel"></i><span>Filter</span>
                            <span class="badge bg-primary text-white d-none" id="repoActiveFilterBadge" style="font-size: 0.72rem; padding: 2px 5px;">0</span>
                        </button>
                        <button type="button" class="btn btn-outline-secondary btn-sm btn-repo-xs d-inline-flex align-items-center gap-1 bg-white d-none" id="btnRepoResetInline" onclick="resetRepoFilters()" style="height: 31px;" title="Reset Semua Filter">
                            <i class="bi bi-arrow-counterclockwise"></i><span>Reset</span>
                        </button>
                        <button type="button" class="btn btn-success btn-sm btn-repo-xs d-inline-flex align-items-center gap-1 text-white" id="btnRepoExportExcel" onclick="exportFilteredRepoToExcel()" style="height: 31px;" title="Ekspor Data Terfilter ke Excel">
                            <i class="bi bi-file-earmark-excel"></i><span>Excel</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- PANEL FILTER LANJUTAN (COLLAPSIBLE) -->
            <div class="px-3 py-2 bg-white border-bottom d-none" id="repoAdvancedFilterPanel">
                <div class="row g-2 align-items-end">
                    <div class="col-6 col-md-3">
                        <label class="form-label mb-1 text-muted fw-semibold" style="font-size: 0.72rem;">Unit / Fakultas Pengusul</label>
                        <select id="repoUnitFilter" class="form-select form-select-sm" style="font-size: 0.78rem;">
                            <option value="ALL">Semua Unit / Fakultas</option>
                            @foreach ($repoUnits as $uName)
                                <option value="{{ strtolower(trim($uName)) }}">{{ $uName }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-6 col-md-3">
                        <label class="form-label mb-1 text-muted fw-semibold" style="font-size: 0.72rem;">Kategori Mitra</label>
                        <select id="repoCategoryFilter" class="form-select form-select-sm" style="font-size: 0.78rem;">
                            <option value="ALL">Semua Kategori</option>
                            @foreach ($repoCategories as $catName)
                                <option value="{{ strtolower(trim($catName)) }}">{{ $catName }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-6 col-md-2">
                        <label class="form-label mb-1 text-muted fw-semibold" style="font-size: 0.72rem;">Tahun TTD</label>
                        <select id="repoYearFilter" class="form-select form-select-sm" style="font-size: 0.78rem;">
                            <option value="ALL">Semua Tahun</option>
                            @foreach ($repoYears as $yr)
                                <option value="{{ $yr }}">{{ $yr }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-6 col-md-2">
                        <label class="form-label mb-1 text-muted fw-semibold" style="font-size: 0.72rem;">Kelengkapan Berkas</label>
                        <select id="repoFileFilter" class="form-select form-select-sm" style="font-size: 0.78rem;">
                            <option value="ALL">Semua Berkas</option>
                            <option value="HAS_SIGNED">Naskah Sah (PDF Signed)</option>
                            <option value="DRAFT_ONLY">Baru Naskah Draf</option>
                            <option value="HAS_FILE">Semua Naskah Utama (Draf / Sah)</option>
                            <option value="NO_MAIN_FILE">Belum Ada Naskah Utama</option>
                            <option value="HAS_PROPOSAL">Ada Proposal / Rencana</option>
                            <option value="HAS_SUPPORTING">Ada Dokumen Pendukung</option>
                            <option value="HAS_REPORT">Ada Laporan Pelaksanaan</option>
                            <option value="NO_FILE">Kosong Total (Tanpa Berkas)</option>
                        </select>
                    </div>
                    <div class="col-12 col-md-2">
                        <label class="form-label mb-1 text-muted fw-semibold" style="font-size: 0.72rem;">Status Dokumen</label>
                        <select id="repoStatusFilter" class="form-select form-select-sm" style="font-size: 0.78rem;" title="Filter berdasarkan status dokumen">
                            <option value="ALL">Semua Status</option>
                            <option value="ACTIVE">Aktif (ACTIVE)</option>
                            <option value="WARNING">Peringatan</option>
                            <option value="EXPIRED">Kedaluwarsa</option>
                            <option value="VERIFIED">Terverifikasi</option>
                            <option value="REVIEW">Telaah</option>
                            <option value="DRAFT">Draf</option>
                            <option value="CANCELLED">Dibatalkan</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- ISI UTAMA CARD (TERINSPIRASI BINGKAI CARD-BODY & TABEL KLASIK MENU 4) -->
            <div class="card-body">
                <!-- RINGKASAN CEPAT KELENGKAPAN ARSIP & BADGE TOTAL ALA MENU 4 -->
                <div class="mb-3 d-flex flex-wrap align-items-center justify-content-between gap-2 small" id="repoCompletenessStrip">
                    <div class="d-flex flex-wrap align-items-center gap-1.5">
                        <span class="text-muted fw-semibold me-1" style="font-size: 0.75rem;">Ringkasan Arsip:</span>
                        <button type="button" class="btn btn-sm repo-kpi-chip border btn-repo-xs d-inline-flex align-items-center gap-1 js-quick-file-filter" data-file-filter="HAS_SIGNED" title="Filter dokumen yang sudah memiliki Naskah Sah PDF">
                            <span class="badge badge-soft-success py-0.5 px-1.5" id="kpiSignedCount" style="font-size: 0.72rem;">0</span>
                            <span>Naskah Sah</span>
                        </button>
                        <button type="button" class="btn btn-sm repo-kpi-chip border btn-repo-xs d-inline-flex align-items-center gap-1 js-quick-file-filter" data-file-filter="DRAFT_ONLY" title="Filter dokumen yang baru memiliki berkas draf">
                            <span class="badge badge-soft-primary py-0.5 px-1.5" id="kpiDraftCount" style="font-size: 0.72rem;">0</span>
                            <span>Baru Draf</span>
                        </button>
                        <button type="button" class="btn btn-sm repo-kpi-chip border btn-repo-xs d-inline-flex align-items-center gap-1 js-quick-file-filter" data-file-filter="HAS_PROPOSAL" title="Filter dokumen yang memiliki Proposal atau Rencana Kegiatan">
                            <span class="badge badge-soft-info py-0.5 px-1.5" id="kpiProposalCount" style="font-size: 0.72rem;">0</span>
                            <span>Ada Proposal/TOR</span>
                        </button>
                        <button type="button" class="btn btn-sm repo-kpi-chip border btn-repo-xs d-inline-flex align-items-center gap-1 js-quick-file-filter" data-file-filter="HAS_REPORT" title="Filter dokumen yang memiliki Laporan Pelaksanaan">
                            <span class="badge badge-soft-success py-0.5 px-1.5" id="kpiReportCount" style="font-size: 0.72rem;">0</span>
                            <span>Ada Laporan</span>
                        </button>
                        <button type="button" class="btn btn-sm repo-kpi-chip border btn-repo-xs d-inline-flex align-items-center gap-1 js-quick-file-filter" data-file-filter="NO_MAIN_FILE" title="Filter dokumen yang belum memiliki naskah utama (PDF/DOCX)">
                            <span class="badge badge-soft-warning py-0.5 px-1.5" id="kpiNoMainCount" style="font-size: 0.72rem;">0</span>
                            <span>Tanpa Naskah Utama</span>
                        </button>
                    </div>
                    <div>
                        <span class="badge bg-light text-dark border px-3 py-2 fw-semibold">
                            Total: <span id="repoVisibleCountTop" class="fw-bold text-primary">{{ $documents->count() }}</span> Dokumen
                        </span>
                    </div>
                </div>

                <!-- TABEL REPOSITORI DOKUMEN DINAMIS -->
                <div class="table-responsive" style="border: 0;">
                    <table class="w-100 table table-bordered table-striped table-hover table-sm align-middle mb-0" id="repoTable">
                        <colgroup>
                            <col style="width: 17%;">
                            <col style="width: 26%;">
                            <col style="width: 14%;">
                            <col style="width: 15%;">
                            <col style="width: 16%;">
                            <col style="width: 12%;">
                        </colgroup>
                        <thead class="table-light">
                        <tr>
                            <th class="repo-sortable" data-sort-key="doc_number" tabindex="0" role="button" title="Klik untuk mengurutkan berdasarkan Nomor Dokumen">Nomor &amp; Jenis <i class="bi bi-arrow-down-up repo-sort-icon"></i></th>
                            <th class="repo-sortable" data-sort-key="title" tabindex="0" role="button" title="Klik untuk mengurutkan berdasarkan Judul Kerja Sama (A-Z / Z-A)">Judul Kerja Sama &amp; Mitra <i class="bi bi-arrow-down-up repo-sort-icon"></i></th>
                            <th class="repo-sortable sort-active" data-sort-key="date" data-sort-dir="desc" tabindex="0" role="button" title="Klik untuk mengurutkan Tanggal TTD (Terbaru / Terlama)">TTD &amp; Durasi <i class="bi bi-sort-down repo-sort-icon"></i></th>
                            <th>Penandatangan</th>
                            <th class="repo-sortable" data-sort-key="audit" tabindex="0" role="button" title="Klik untuk mengurutkan berdasarkan Jumlah Riwayat">Berkas &amp; Audit <i class="bi bi-arrow-down-up repo-sort-icon"></i></th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($documents as $doc)
                            @php
                                $statusMeta = match($doc->status) {
                                    'DRAFT' => ['Draf', 'badge-soft-secondary'],
                                    'REVIEW' => ['Telaah', 'badge-soft-info'],
                                    'VERIFIED' => ['Terverifikasi', 'badge-soft-primary'],
                                    'ACTIVE' => ['Aktif', 'badge-soft-success'],
                                    'WARNING' => ['Peringatan', 'badge-soft-warning text-dark'],
                                    'EXPIRED' => ['Kedaluwarsa', 'badge-soft-danger'],
                                    'CANCELLED' => ['Dibatalkan', 'bg-dark text-white'],
                                    default => [$doc->status, 'badge-soft-secondary']
                                };
                                $statusLabel = $statusMeta[0];
                                $statusBadgeClass = $statusMeta[1];

                                $docTypeUpper = strtoupper((string)$doc->doc_type);
                                if (str_contains($docTypeUpper, 'MOU')) {
                                    $docTypeBadgeClass = 'badge-doc-mou';
                                    $docTypeIcon = 'bi-award-fill';
                                    $docTypeTooltip = 'MoU — Nota Kesepahaman (Payung Institusi)';
                                } elseif (str_contains($docTypeUpper, 'MOA') || str_contains($docTypeUpper, 'PKS')) {
                                    $docTypeBadgeClass = 'badge-doc-moa';
                                    $docTypeIcon = 'bi-file-earmark-ruled';
                                    $docTypeTooltip = 'MoA / PKS — Perjanjian Kerja Sama (Turunan Unit/Fakultas)';
                                } elseif (str_contains($docTypeUpper, 'IA')) {
                                    $docTypeBadgeClass = 'badge-doc-ia';
                                    $docTypeIcon = 'bi-lightning-charge-fill';
                                    $docTypeTooltip = 'IA — Implementation Arrangement (Pelaksanaan Kegiatan)';
                                } else {
                                    $docTypeBadgeClass = 'bg-light text-dark border';
                                    $docTypeIcon = 'bi-file-earmark-text';
                                    $docTypeTooltip = $doc->doc_type;
                                }

                                // Verifikasi fisik berkas utama (signed_file -> draft_file -> docx_path)
                                $signedInfo = $resolvePublicDocFile($doc->signed_file);
                                $draftInfo = $resolvePublicDocFile($doc->draft_file);
                                $docxInfo = $resolvePublicDocFile($doc->docx_path);

                                $primaryFileInfo = $signedInfo ?: ($draftInfo ?: $docxInfo);
                                $hasRealFile = ($primaryFileInfo !== null);
                                $hasSignedFile = ($signedInfo !== null);

                                if ($signedInfo) {
                                    $primaryFileShort = 'PDF Sah';
                                    $primaryFileIcon = 'bi-file-earmark-pdf-fill text-success';
                                    $repoStatsSigned++;
                                } elseif ($draftInfo) {
                                    $primaryFileShort = 'Draf PDF';
                                    $primaryFileIcon = 'bi-file-earmark-pdf-fill text-danger';
                                    $repoStatsDraftOnly++;
                                } elseif ($docxInfo) {
                                    $primaryFileShort = 'Draf DOCX';
                                    $primaryFileIcon = 'bi-file-earmark-word-fill text-primary';
                                    $repoStatsDraftOnly++;
                                } else {
                                    $primaryFileShort = '-';
                                    $primaryFileIcon = 'bi-file-earmark';
                                    $repoStatsNoMain++;
                                }

                                // Normalisasi Penandatangan Pihak I (hindari duplikasi jabatan seperti "Rektor Universitas Parna Raya" + "Rektor")
                                $signer1 = trim((string)($doc->first_party_signer ?: 'Rektor UNIPAR'));
                                $pos1 = trim((string)($doc->first_party_position ?: ''));
                                $showPos1 = ($pos1 !== '' && stripos($signer1, $pos1) === false);

                                $signer2 = trim((string)($doc->second_party_signer ?: ('Pimpinan ' . $doc->partner_name)));
                                $isLegacyDoc = ($doc->is_legacy || stripos((string)$doc->track, 'Lampau') !== false || stripos((string)$doc->track, 'legacy') !== false);
                                $docDateRef = $doc->date_signed ?: ($doc->date_approved ?: $doc->created_at);
                                $docYearAttr = $docDateRef ? $docDateRef->format('Y') : '';
                                $docSortTs = $docDateRef ? $docDateRef->timestamp : 0;
                                $docEndTs = $doc->end_date ? $doc->end_date->timestamp : 9999999999;

                                // Hitung kelengkapan lampiran multi-berkas (Proposal, Rencana, Pendukung, Laporan)
                                $extraActReports = $doc->activities->filter(fn($a) => !empty($a->evidence_file) && !$doc->attachments->contains('file_path', $a->evidence_file));
                                $totalAttachments = $doc->attachments->count() + $extraActReports->count();
                                $hasProposal = $doc->attachments->whereIn('category', ['PROPOSAL', 'RENCANA_KEGIATAN'])->isNotEmpty();
                                $hasSupporting = $doc->attachments->where('category', 'DOKUMEN_PENDUKUNG')->isNotEmpty();
                                $hasReport = ($doc->attachments->where('category', 'LAPORAN_PELAKSANAAN')->isNotEmpty() || $extraActReports->isNotEmpty());

                                if ($hasProposal) $repoStatsProposal++;
                                if ($hasReport) $repoStatsReport++;

                                $docNumDisplay = (!empty($doc->doc_number) && $doc->doc_number !== '-') ? $doc->doc_number : '-';
                                $partnerNumDisplay = (!empty($doc->partner_doc_number) && $doc->partner_doc_number !== '-') ? $doc->partner_doc_number : '-';
                                $dateDisplay = $doc->date_signed ? $doc->date_signed->translatedFormat('d M Y') : ($doc->date_approved ? $doc->date_approved->translatedFormat('d M Y') : 'Draf Formal');
                                $validityDisplay = $doc->validity ?: '1 Tahun';
                                $endDateDisplay = $doc->end_date ? $doc->end_date->translatedFormat('d M Y') : '-';

                                // Teks indeks pencarian gabungan (mencakup doc_id, reg_id, nomor, judul, mitra, penandatangan, dan nama lampiran)
                                $attachmentNamesStr = $doc->attachments->map(fn($a) => trim(($a->title ?: '') . ' ' . ($a->original_name ?: '') . ' ' . ($a->category_label ?: '')))->implode(' ');
                                $extraActStr = $extraActReports->map(fn($e) => trim(($e->activity_name ?: '') . ' ' . basename((string)$e->evidence_file)))->implode(' ');
                                $searchIndex = strtolower(trim(implode(' ', [
                                    $doc->doc_id,
                                    $doc->reg_id,
                                    $doc->doc_number,
                                    $doc->partner_doc_number,
                                    $doc->doc_type,
                                    $doc->title,
                                    $doc->scope,
                                    $doc->partner_name,
                                    $doc->partner_category,
                                    $doc->unit,
                                    $signer1,
                                    $pos1,
                                    $signer2,
                                    $doc->second_party_position,
                                    $attachmentNamesStr,
                                    $extraActStr,
                                ])));

                                // Susun payload JSON ringan untuk modal dinamis bersama
                                $repoDocIdMap[$doc->doc_id] = $doc->id;
                                $repoPayloads[$doc->id] = [
                                    'id' => $doc->id,
                                    'doc_id' => $doc->doc_id,
                                    'doc_type' => $doc->doc_type,
                                    'doc_number' => $docNumDisplay !== '-' ? $docNumDisplay : $doc->doc_id,
                                    'partner_name' => $doc->partner_name,
                                    'detail_url' => route('simkerma.index', ['menu' => 4, 'view' => 'detail', 'doc' => $doc->doc_id]),
                                    'draft_file' => $draftInfo,
                                    'docx_file' => $docxInfo,
                                    'signed_file' => $signedInfo,
                                    'attachments' => $doc->attachments->map(function($att) {
                                        $fileFull = $att->file_path ? public_path($att->file_path) : null;
                                        $exists = ($fileFull && file_exists($fileFull) && is_file($fileFull));
                                        return [
                                            'id' => $att->id,
                                            'category' => $att->category,
                                            'category_label' => $att->category_label,
                                            'title' => $att->title ?: $att->original_name,
                                            'original_name' => $att->original_name,
                                            'size_text' => $att->file_size ? ($att->file_size >= 1048576 ? round($att->file_size / 1048576, 1) . ' MB' : round($att->file_size / 1024, 0) . ' KB') : null,
                                            'uploaded_by' => $att->uploaded_by,
                                            'url' => $exists ? asset($att->file_path) : null,
                                        ];
                                    })->values(),
                                    'extra_reports' => $extraActReports->map(function($eAct) {
                                        $eExists = file_exists(public_path($eAct->evidence_file)) && is_file(public_path($eAct->evidence_file));
                                        return [
                                            'activity_name' => $eAct->activity_name,
                                            'file_name' => basename($eAct->evidence_file),
                                            'execution_status' => $eAct->execution_status,
                                            'url' => $eExists ? asset($eAct->evidence_file) : null,
                                        ];
                                    })->values(),
                                    'audit_trails' => $doc->auditTrails->sortByDesc('id')->map(function($rTrail) {
                                        return [
                                            'timestamp_wita' => $rTrail->timestamp_wita,
                                            'actor' => $rTrail->actor,
                                            'role' => $rTrail->role,
                                            'event' => $rTrail->event,
                                            'from_status' => $rTrail->from_status,
                                            'to_status' => $rTrail->to_status,
                                            'notes' => $rTrail->notes,
                                        ];
                                    })->values(),
                                ];
                            @endphp
                            <tr class="repo-row"
                                data-id="{{ $doc->id }}"
                                data-doc-id="{{ $doc->doc_id }}"
                                data-type="{{ $doc->doc_type }}"
                                data-status="{{ $doc->status }}"
                                data-legacy="{{ $doc->is_legacy ? '1' : '0' }}"
                                data-track="{{ $doc->track }}"
                                data-unit="{{ strtolower(trim((string)$doc->unit)) }}"
                                data-category="{{ strtolower(trim((string)$doc->partner_category)) }}"
                                data-year="{{ $docYearAttr }}"
                                data-has-file="{{ $hasRealFile ? '1' : '0' }}"
                                data-has-signed="{{ $hasSignedFile ? '1' : '0' }}"
                                data-has-proposal="{{ $hasProposal ? '1' : '0' }}"
                                data-has-supporting="{{ $hasSupporting ? '1' : '0' }}"
                                data-has-report="{{ $hasReport ? '1' : '0' }}"
                                data-sort-date="{{ $docSortTs }}"
                                data-end-date="{{ $docEndTs }}"
                                data-doc-number="{{ strtolower(trim((string)$doc->doc_number)) }}"
                                data-audit-count="{{ $doc->auditTrails->count() }}"
                                data-title="{{ strtolower(trim((string)($doc->title ?: $doc->scope))) }}"
                                data-search="{{ $searchIndex }}"
                                data-export-doc-num="{{ $docNumDisplay }}"
                                data-export-partner-num="{{ $partnerNumDisplay }}"
                                data-export-type="{{ $doc->doc_type }}"
                                data-export-status="{{ $statusLabel }} ({{ $doc->status }})"
                                data-export-title="{{ $doc->title ?: $doc->scope }}"
                                data-export-partner="{{ $doc->partner_name }}"
                                data-export-category="{{ $doc->partner_category }}"
                                data-export-unit="{{ $doc->unit ?: '-' }}"
                                data-export-date="{{ $dateDisplay }}"
                                data-export-validity="{{ $validityDisplay }}"
                                data-export-end-date="{{ $endDateDisplay }}"
                                data-export-signer1="{{ $signer1 }}{{ $showPos1 ? ' (' . $pos1 . ')' : '' }}"
                                data-export-signer2="{{ $signer2 }}"
                                data-export-main-file="{{ $hasRealFile ? ($primaryFileShort . ' (' . $primaryFileInfo['size_text'] . ')') : 'Belum ada' }}"
                                data-export-attachments="{{ $totalAttachments }}"
                                data-export-audit="{{ $doc->auditTrails->count() }}">
                                <td>
                                    <div style="font-size: 0.75rem; line-height: 1.35;">
                                        <span class="text-muted">I:</span>
                                        <strong class="text-dark">{{ $docNumDisplay }}</strong>
                                    </div>
                                    <div class="text-muted mt-1" style="font-size: 0.72rem; line-height: 1.35;">
                                        <span>II:</span>
                                        <span class="{{ $partnerNumDisplay !== '-' ? 'text-dark fw-medium' : 'text-muted' }}">{{ $partnerNumDisplay }}</span>
                                    </div>
                                    <div class="mt-1 d-flex align-items-center gap-1 flex-wrap">
                                        <span class="badge {{ $docTypeBadgeClass }} badge-repo-compact d-inline-flex align-items-center gap-1" title="{{ $docTypeTooltip }}">
                                            <i class="bi {{ $docTypeIcon }}" style="font-size: 0.72rem;"></i>{{ $doc->doc_type }}
                                        </span>
                                        <span class="badge {{ $statusBadgeClass }} badge-repo-compact" title="Status FSM: {{ $doc->status }}">{{ $statusLabel }}</span>
                                        @if ($isLegacyDoc)
                                            <span class="badge repo-legacy-badge badge-repo-compact" title="Dokumen Arsip Lampau (Pra-Sistem)"><i class="bi bi-archive me-1"></i>Lampau</span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <a href="{{ route('simkerma.index', ['menu' => 4, 'view' => 'detail', 'doc' => $doc->doc_id]) }}" class="repo-doc-title fw-semibold text-dark text-decoration-none d-block mb-1" style="line-height: 1.35; font-size: 0.82rem;" title="Klik untuk membuka detail dokumen: {{ $doc->title ?: $doc->scope }}">
                                        {{ Str::limit($doc->title ?: $doc->scope, 110) }}
                                    </a>
                                    <div class="text-muted repo-partner-wrap" style="font-size: 0.75rem;" title="{{ $doc->partner_name }}">
                                        <i class="bi bi-building me-1 text-secondary"></i><strong class="text-dark">{{ $doc->partner_name }}</strong>
                                    </div>
                                    <div class="text-muted mt-0.5" style="font-size: 0.72rem; line-height: 1.35;" title="{{ $doc->partner_category }}{{ $doc->unit ? ' • ' . $doc->unit : '' }}">
                                        <span>{{ $doc->partner_category }}</span>
                                        @if ($doc->unit)
                                            <span>&bull; {{ $doc->unit }}</span>
                                        @endif
                                    </div>
                                    @if (!empty($doc->parent_mou) && trim((string)$doc->parent_mou) !== '-')
                                        <div class="text-primary text-truncate mt-0.5" style="font-size: 0.72rem;" title="Dokumen Induk / Payung: {{ $doc->parent_mou }}">
                                            <i class="bi bi-arrow-return-right me-1"></i>Induk: {{ $doc->parent_mou }}
                                        </div>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="text-dark fw-medium" style="font-size: 0.75rem;">
                                        {{ $dateDisplay }}
                                    </div>
                                    <div class="text-muted" style="font-size: 0.72rem;">{{ $validityDisplay }}</div>
                                    @if ($doc->end_date)
                                        <div class="text-muted" style="font-size: 0.72rem;" title="Berakhir pada {{ $doc->end_date->translatedFormat('d F Y') }}">
                                            s.d. {{ $endDateDisplay }}
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <div style="font-size: 0.75rem; line-height: 1.35;">
                                        <span class="text-muted">UNIPAR:</span>
                                        <span class="text-dark fw-semibold">{{ $signer1 }}</span>
                                    </div>
                                    @if ($showPos1)
                                        <div class="text-muted" style="font-size: 0.72rem; line-height: 1.3;">{{ $pos1 }}</div>
                                    @endif
                                    <div class="mt-1 pt-1 border-top" style="font-size: 0.75rem; line-height: 1.35;">
                                        <span class="text-muted">Mitra:</span>
                                        <span class="text-dark">{{ $signer2 }}</span>
                                    </div>
                                </td>
                                <td class="text-center">
                                    @if ($hasRealFile)
                                        <div class="d-inline-flex align-items-center justify-content-center gap-1 text-nowrap">
                                            <a href="{{ $primaryFileInfo['url'] }}" target="_blank" class="repo-file-link d-inline-flex align-items-center text-dark fw-semibold text-decoration-none" style="font-size: 0.75rem;" title="Pratinjau / Buka: {{ $primaryFileInfo['name'] }}">
                                                <i class="bi {{ $primaryFileIcon }} me-1"></i>
                                                <span>{{ $primaryFileShort }} ({{ $primaryFileInfo['size_text'] }})</span>
                                            </a>
                                            <a href="{{ $primaryFileInfo['url'] }}" download class="text-primary ps-1 d-inline-flex align-items-center justify-content-center" style="font-size: 0.78rem; min-width: 24px; min-height: 24px;" title="Unduh {{ $primaryFileInfo['name'] }}" aria-label="Unduh {{ $primaryFileInfo['name'] }}">
                                                <i class="bi bi-download"></i>
                                            </a>
                                        </div>
                                    @else
                                        <div>
                                            <button type="button" class="badge badge-soft-warning text-warning-emphasis border border-warning-subtle repo-inline-pill js-open-repo-attachments" data-doc-id="{{ $doc->id }}" title="Belum ada naskah utama, klik untuk unggah">
                                                <i class="bi bi-cloud-upload"></i><span>Belum ada naskah</span>
                                            </button>
                                        </div>
                                    @endif
                                    <div class="mt-1 d-flex justify-content-center align-items-center gap-1.5 flex-wrap">
                                        <button type="button" class="badge {{ $totalAttachments > 0 ? 'badge-soft-primary border border-primary-subtle' : 'bg-light text-muted border' }} text-decoration-none repo-inline-pill js-open-repo-attachments" data-doc-id="{{ $doc->id }}" title="Lihat &amp; Kelola Proposal, TOR, Dokumen Pendukung &amp; Laporan">
                                            <i class="bi bi-paperclip"></i><span>{{ $totalAttachments > 0 ? $totalAttachments . ' Lampiran' : '+ Lampiran' }}</span>
                                        </button>
                                        <button type="button" class="btn btn-light border text-muted text-decoration-none repo-inline-pill js-open-repo-audit" data-doc-id="{{ $doc->id }}" title="Lihat Riwayat Perubahan Dokumen">
                                            <i class="bi bi-clock-history text-primary"></i><span>{{ $doc->auditTrails->count() }} Riwayat</span>
                                        </button>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center align-items-center gap-1 flex-nowrap">
                                        <a href="{{ route('simkerma.index', ['menu' => 4, 'view' => 'detail', 'doc' => $doc->doc_id]) }}" class="btn btn-partner-profile btn-repo-xs repo-action-btn fw-semibold gap-1" title="Buka Detail Dokumen &amp; Alur FSM" aria-label="Buka Detail Dokumen {{ $docNumDisplay !== '-' ? $docNumDisplay : $doc->doc_id }}">
                                            <i class="bi bi-folder2-open"></i><span>Detail</span>
                                        </a>
                                        <a href="{{ route('simkerma.verify', ['reg_id' => $doc->reg_id]) }}" target="_blank" class="btn btn-outline-secondary btn-repo-xs repo-action-btn repo-action-icon text-primary bg-white" title="Lembar Verifikasi Publik" aria-label="Lembar Verifikasi Publik {{ $docNumDisplay !== '-' ? $docNumDisplay : $doc->doc_id }}">
                                            <i class="bi bi-shield-check"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">Belum ada dokumen kemitraan terdaftar.</td>
                            </tr>
                        @endforelse

                        <!-- Baris Fallback Saat Hasil Filter / Pencarian Kosong -->
                        <tr id="noRepoMatchRow" class="d-none">
                            <td colspan="6" class="text-center py-5 text-muted">
                                <div class="py-3">
                                    <i class="bi bi-search text-secondary display-6 mb-2 d-block"></i>
                                    <h6 class="fw-bold text-dark mb-1">Tidak Ada Dokumen yang Cocok</h6>
                                    <p class="small text-muted mb-3">Tidak ditemukan naskah kerja sama dengan kata kunci atau filter status yang dipilih.</p>
                                    <button type="button" class="btn btn-outline-primary btn-sm px-3" onclick="resetRepoFilters()">
                                        <i class="bi bi-arrow-counterclockwise me-1"></i> Reset Semua Filter
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
                </div>
            </div>

            <!-- CARD FOOTER DENGAN KONTROL PAGINATION -->
            <div class="card-footer bg-white py-2 px-3 border-top d-flex flex-column flex-sm-row justify-content-between align-items-center gap-2 small text-muted">
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <span>Menampilkan <strong id="repoVisibleCount" class="text-dark">{{ $documents->count() }}</strong> naskah</span>
                    <span class="text-secondary">&bull;</span>
                    <div class="d-flex align-items-center gap-1">
                        <span>Tampilkan:</span>
                        <select id="repoPerPage" class="form-select form-select-sm py-0 px-2" style="width: auto; height: 30px; font-size: 0.76rem;">
                            <option value="15">15</option>
                            <option value="25" selected>25</option>
                            <option value="50">50</option>
                            <option value="ALL">Semua</option>
                        </select>
                        <span>baris</span>
                    </div>
                </div>
                <div id="repoPaginationWrapper" class="d-flex align-items-center gap-1">
                    <!-- Tombol Pagination di-populate via JavaScript -->
                </div>
            </div>
        </div>

        {{-- ================================================================================= --}}
        {{-- MODAL TUNGGAL DINAMIS REPOSITORI MENU 3 (SKALABEL & STATEFUL)                      --}}
        {{-- ================================================================================= --}}
        <script id="repoModalPayloads" type="application/json">@json(['byId' => $repoPayloads, 'byDocId' => $repoDocIdMap])</script>

        <!-- Modal 1: Repositori Berkas & Lampiran Dokumen (Shared) -->
        <div class="modal fade" id="sharedRepoAttachmentsModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header py-2.5 px-3 bg-light border-bottom">
                        <div>
                            <h6 class="modal-title fw-bold text-dark mb-0">
                                <i class="bi bi-paperclip text-primary me-1"></i> Berkas &amp; Lampiran Dokumen
                            </h6>
                            <div class="text-muted mt-1" style="font-size: 0.72rem;" id="srmAttSubtitle">
                                <span class="badge badge-soft-primary border border-primary-subtle" id="srmAttType">-</span>
                                <span id="srmAttMeta">-</span>
                            </div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-3 small">
                        <!-- Bagian A: Naskah Resmi (Draf & Final Bertandatangan) -->
                        <div class="mb-3">
                            <div class="fw-bold text-dark mb-2 d-flex align-items-center gap-1.5" style="font-size: 0.78rem;">
                                <i class="bi bi-file-earmark-text text-primary"></i>
                                <span>Naskah Utama (<span id="srmMainType">-</span>)</span>
                            </div>
                            <div class="row g-2">
                                <div class="col-md-6">
                                    <div class="p-2 border rounded bg-light h-100 d-flex align-items-center justify-content-between gap-2" id="srmDraftBox">
                                        <!-- Diisi dinamis oleh JS -->
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="p-2 border rounded bg-light h-100 d-flex align-items-center justify-content-between gap-2" id="srmSignedBox">
                                        <!-- Diisi dinamis oleh JS -->
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Bagian B: Proposal, Rencana Kegiatan, Dokumen Pendukung & Laporan Pelaksanaan -->
                        <div class="mb-3">
                            <div class="fw-bold text-dark mb-2 d-flex align-items-center justify-content-between" style="font-size: 0.78rem;">
                                <span><i class="bi bi-folder2-open text-primary me-1"></i> Proposal, Rencana Kegiatan, Dokumen Pendukung &amp; Laporan</span>
                                <span class="badge bg-light text-dark border" id="srmAttCountBadge">0 Berkas</span>
                            </div>
                            <div id="srmAttTableContainer">
                                <!-- Diisi dinamis oleh JS -->
                            </div>
                        </div>

                        <!-- Bagian C: Form Unggah Cepat -->
                        <div class="p-3 border rounded bg-light">
                            <div class="fw-bold text-dark mb-2" style="font-size: 0.78rem;">
                                <i class="bi bi-cloud-arrow-up text-primary me-1"></i> Unggah Berkas / Lampiran Baru
                            </div>
                            <form method="POST" action="{{ route('simkerma.upload-document-file') }}" enctype="multipart/form-data" id="srmUploadForm">
                                @csrf
                                <input type="hidden" name="doc_id" id="srmUploadDocId" value="">
                                <input type="hidden" name="redirect_menu" value="3">
                                <div class="row g-2 align-items-end">
                                    <div class="col-md-4">
                                        <label class="form-label text-muted fw-semibold mb-1" style="font-size: 0.72rem;">Jenis Berkas</label>
                                        <select name="file_type" id="srmUploadFileType" class="form-select form-select-sm" required>
                                            <option value="proposal">Proposal Kerja Sama</option>
                                            <option value="rencana_kegiatan">Rencana Kegiatan / TOR</option>
                                            <option value="dokumen_pendukung">Dokumen Pendukung (Notula/Surat/dll)</option>
                                            <option value="laporan_pelaksanaan">Laporan Pelaksanaan Kegiatan</option>
                                            <option value="draft">Naskah Draf Pengusul (PDF/DOCX)</option>
                                            <option value="signed">Naskah Sah Bertandatangan (PDF)</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4" id="srmTitleFieldWrap">
                                        <label class="form-label text-muted fw-semibold mb-1" style="font-size: 0.72rem;">Nama / Keterangan (Opsional)</label>
                                        <input type="text" name="attachment_title" id="srmUploadTitle" class="form-control form-control-sm" placeholder="Contoh: Proposal MBKM / Notula Rapat">
                                    </div>
                                    <div class="col-md-4 d-none" id="srmMainNoteWrap">
                                        <div class="p-2 rounded border bg-white text-muted" style="font-size: 0.7rem; line-height: 1.3;">
                                            <i class="bi bi-info-circle text-primary me-1"></i>Berkas akan memperbarui <strong>Naskah Utama</strong> dokumen ini.
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label text-muted fw-semibold mb-1" style="font-size: 0.72rem;">Pilih File (Maks. 15 MB)</label>
                                        <div class="input-group input-group-sm">
                                            <input type="file" name="file" id="srmUploadFileInput" class="form-control form-control-sm" required accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png">
                                            <button type="submit" id="srmUploadSubmitBtn" class="btn btn-primary fw-semibold px-2.5" title="Unggah Berkas Sekarang" aria-label="Unggah Berkas Sekarang">
                                                <i class="bi bi-upload"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="modal-footer py-2 px-3 bg-light border-top">
                        <a href="#" id="srmAttDetailLink" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-folder2-open me-1"></i> Buka Halaman Detail
                        </a>
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal 2: Riwayat Perubahan & Jejak Audit Dokumen (Shared) -->
        <div class="modal fade" id="sharedRepoAuditModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header py-2.5 px-3 bg-light border-bottom">
                        <div>
                            <h6 class="modal-title fw-bold text-dark mb-0">
                                <i class="bi bi-clock-history text-primary me-1"></i> Riwayat Perubahan Dokumen (<span id="srmAuditCount">0</span> Rekaman)
                            </h6>
                            <div class="text-muted mt-1" style="font-size: 0.72rem;" id="srmAuditSubtitle">-</div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-3 small" id="srmAuditBody">
                        <!-- Diisi dinamis oleh JS -->
                    </div>
                    <div class="modal-footer py-2 px-3 bg-light border-top">
                        <a href="#" id="srmAuditDetailLink" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-folder2-open me-1"></i> Buka Halaman Detail
                        </a>
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>

        @push('scripts')
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            const REPO_STATE_KEY = 'simkerma_repo_state_v1';
            const tabs = document.querySelectorAll('.repo-tab-btn');
            const tbody = document.querySelector('#repoTable tbody');
            const rows = Array.from(document.querySelectorAll('.repo-row'));
            const search = document.getElementById('repoSearchInput');
            const clearSearchBtn = document.getElementById('btnRepoClearSearch');
            const statusFilter = document.getElementById('repoStatusFilter');
            const unitFilter = document.getElementById('repoUnitFilter');
            const categoryFilter = document.getElementById('repoCategoryFilter');
            const yearFilter = document.getElementById('repoYearFilter');
            const fileFilter = document.getElementById('repoFileFilter');
            const quickFileButtons = document.querySelectorAll('.js-quick-file-filter');
            const sortableHeaders = document.querySelectorAll('#repoTable thead th.repo-sortable');
            const advPanel = document.getElementById('repoAdvancedFilterPanel');
            const toggleAdvBtn = document.getElementById('btnToggleRepoAdvancedFilter');
            const activeBadge = document.getElementById('repoActiveFilterBadge');
            const resetInlineBtn = document.getElementById('btnRepoResetInline');
            const noMatchRow = document.getElementById('noRepoMatchRow');
            const visibleCountEl = document.getElementById('repoVisibleCount');
            const visibleCountTopEl = document.getElementById('repoVisibleCountTop');
            const perPageSelect = document.getElementById('repoPerPage');
            const paginationWrapper = document.getElementById('repoPaginationWrapper');
            const exportExcelBtn = document.getElementById('btnRepoExportExcel');

            // Payload Modal & CSRF Token
            let payloadsData = { byId: {}, byDocId: {} };
            try {
                const payloadRaw = document.getElementById('repoModalPayloads');
                if (payloadRaw && payloadRaw.textContent) {
                    payloadsData = JSON.parse(payloadRaw.textContent);
                }
            } catch (e) {}
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
            const deleteAttachmentUrl = @json(route('simkerma.delete-attachment'));

            let currentPage = 1;
            let currentSortKey = 'date';
            let currentSortDir = 'desc';

            // Hitung & tampilkan Ringkasan Arsip KPI Strip
            function updateCompletenessStrip() {
                let cSigned = 0, cDraft = 0, cProposal = 0, cReport = 0, cNoMain = 0;
                rows.forEach(function(r) {
                    const hasFile = r.dataset.hasFile === '1';
                    const hasSigned = r.dataset.hasSigned === '1';
                    if (hasSigned) cSigned++;
                    else if (hasFile) cDraft++;
                    else cNoMain++;
                    if (r.dataset.hasProposal === '1') cProposal++;
                    if (r.dataset.hasReport === '1') cReport++;
                });
                const setVal = (id, v) => { const el = document.getElementById(id); if (el) el.innerText = String(v); };
                setVal('kpiSignedCount', cSigned);
                setVal('kpiDraftCount', cDraft);
                setVal('kpiProposalCount', cProposal);
                setVal('kpiReportCount', cReport);
                setVal('kpiNoMainCount', cNoMain);
            }
            updateCompletenessStrip();

            // Simpan urutan awal server untuk fallback stabil
            rows.forEach((r, idx) => r.dataset.origIdx = String(idx));

            function escapeHtml(str) {
                return String(str ?? '')
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;')
                    .replace(/'/g, '&#39;');
            }

            function getBsModal(el) {
                if (!el || typeof bootstrap === 'undefined') return null;
                return bootstrap.Modal.getInstance(el) || new bootstrap.Modal(el);
            }

            function saveRepoState() {
                try {
                    const activeTab = document.querySelector('.repo-tab-btn.active');
                    const state = {
                        tab: activeTab ? activeTab.getAttribute('data-type') : 'ALL',
                        q: search ? search.value : '',
                        status: statusFilter ? statusFilter.value : 'ALL',
                        unit: unitFilter ? unitFilter.value : 'ALL',
                        category: categoryFilter ? categoryFilter.value : 'ALL',
                        year: yearFilter ? yearFilter.value : 'ALL',
                        file: fileFilter ? fileFilter.value : 'ALL',
                        perPage: perPageSelect ? perPageSelect.value : '25',
                        sortKey: currentSortKey,
                        sortDir: currentSortDir,
                        page: currentPage
                    };
                    sessionStorage.setItem(REPO_STATE_KEY, JSON.stringify(state));
                } catch (e) {}
            }

            function restoreRepoState() {
                try {
                    const urlObj = new URL(window.location.href);
                    const pStatus = urlObj.searchParams.get('filter_status');
                    const pCat = urlObj.searchParams.get('filter_category');
                    const pUnit = urlObj.searchParams.get('filter_unit');
                    const pType = urlObj.searchParams.get('filter_type');
                    const pReset = urlObj.searchParams.get('reset_filters');

                    if (pStatus || pCat || pUnit || pType || pReset) {
                        tabs.forEach(b => b.classList.toggle('active', b.getAttribute('data-type') === (pType || 'ALL')));
                        if (search) search.value = '';
                        if (statusFilter) statusFilter.value = pStatus || 'ALL';
                        if (categoryFilter) categoryFilter.value = pCat || 'ALL';
                        if (unitFilter) unitFilter.value = pUnit || 'ALL';
                        if (yearFilter) yearFilter.value = 'ALL';
                        if (fileFilter) fileFilter.value = 'ALL';
                        currentPage = 1;

                        const hasAdvUrl = !!(pStatus || pCat || pUnit);
                        if (hasAdvUrl && advPanel && toggleAdvBtn) {
                            advPanel.classList.remove('d-none');
                            toggleAdvBtn.classList.add('active');
                        }
                        ['filter_status', 'filter_category', 'filter_unit', 'filter_type', 'reset_filters'].forEach(k => urlObj.searchParams.delete(k));
                        window.history.replaceState({}, document.title, urlObj.pathname + urlObj.search);
                        return;
                    }

                    const raw = sessionStorage.getItem(REPO_STATE_KEY);
                    if (!raw) return;
                    const state = JSON.parse(raw);
                    if (!state || typeof state !== 'object') return;

                    if (state.tab) {
                        tabs.forEach(b => b.classList.toggle('active', b.getAttribute('data-type') === state.tab));
                    }
                    if (search && typeof state.q === 'string') search.value = state.q;
                    if (statusFilter && state.status) statusFilter.value = state.status;
                    if (unitFilter && state.unit) unitFilter.value = state.unit;
                    if (categoryFilter && state.category) categoryFilter.value = state.category;
                    if (yearFilter && state.year) yearFilter.value = state.year;
                    if (fileFilter && state.file) fileFilter.value = state.file;
                    if (perPageSelect && state.perPage) perPageSelect.value = state.perPage;
                    if (state.sortKey) currentSortKey = state.sortKey;
                    if (state.sortDir) currentSortDir = state.sortDir;
                    if (state.page && Number.isInteger(state.page) && state.page >= 1) currentPage = state.page;

                    // Buka panel filter lanjutan otomatis jika ada filter lanjutan aktif
                    const hasAdv = (unitFilter && unitFilter.value !== 'ALL')
                        || (categoryFilter && categoryFilter.value !== 'ALL')
                        || (yearFilter && yearFilter.value !== 'ALL')
                        || (fileFilter && fileFilter.value !== 'ALL')
                        || (statusFilter && statusFilter.value !== 'ALL');
                    if (hasAdv && advPanel && toggleAdvBtn) {
                        advPanel.classList.remove('d-none');
                        toggleAdvBtn.classList.add('active');
                    }
                } catch (e) {}
            }

            function updateSortHeadersUI() {
                sortableHeaders.forEach(function(th) {
                    const key = th.getAttribute('data-sort-key');
                    const icon = th.querySelector('.repo-sort-icon');
                    th.classList.remove('sort-active');
                    if (icon) {
                        icon.className = 'bi repo-sort-icon bi-arrow-down-up';
                    }
                    if (key === currentSortKey) {
                        th.classList.add('sort-active');
                        th.setAttribute('data-sort-dir', currentSortDir);
                        if (icon) {
                            icon.className = 'bi repo-sort-icon ' + (currentSortDir === 'desc' ? 'bi-sort-down' : 'bi-sort-up');
                        }
                    } else {
                        th.removeAttribute('data-sort-dir');
                    }
                });
            }

            function getFilteredAndSortedRows() {
                const activeTab = document.querySelector('.repo-tab-btn.active');
                const t = activeTab ? activeTab.getAttribute('data-type') : 'ALL';
                const s = statusFilter ? statusFilter.value : 'ALL';
                const u = unitFilter ? unitFilter.value : 'ALL';
                const c = categoryFilter ? categoryFilter.value : 'ALL';
                const y = yearFilter ? yearFilter.value : 'ALL';
                const f = fileFilter ? fileFilter.value : 'ALL';
                const q = search ? search.value.toLowerCase().trim() : '';

                const matched = rows.filter(function(row) {
                    const rowType = row.getAttribute('data-type') || '';
                    const rowStatus = row.getAttribute('data-status') || '';
                    const rowUnit = row.getAttribute('data-unit') || '';
                    const rowCat = row.getAttribute('data-category') || '';
                    const rowYear = row.getAttribute('data-year') || '';
                    const rowHasFile = row.getAttribute('data-has-file') === '1';
                    const rowHasSigned = row.getAttribute('data-has-signed') === '1';
                    const rowHasProposal = row.getAttribute('data-has-proposal') === '1';
                    const rowHasSupporting = row.getAttribute('data-has-supporting') === '1';
                    const rowHasReport = row.getAttribute('data-has-report') === '1';
                    const isLegacy = row.getAttribute('data-legacy') === '1'
                        || (row.getAttribute('data-track') || '').toLowerCase().includes('lampau')
                        || (row.getAttribute('data-track') || '').toLowerCase().includes('legacy');

                    let matchType = false;
                    if (t === 'ALL') {
                        matchType = true;
                    } else if (t === 'Legacy') {
                        matchType = isLegacy;
                    } else {
                        matchType = rowType.includes(t);
                    }

                    const matchStatus = (s === 'ALL') || (rowStatus === s);
                    const matchUnit = (u === 'ALL') || (rowUnit === u);
                    const matchCategory = (c === 'ALL') || (rowCat === c);
                    const matchYear = (y === 'ALL') || (rowYear === y);
                    const matchFile = (f === 'ALL')
                        || (f === 'HAS_SIGNED' && rowHasSigned)
                        || (f === 'DRAFT_ONLY' && rowHasFile && !rowHasSigned)
                        || (f === 'HAS_FILE' && rowHasFile)
                        || (f === 'NO_MAIN_FILE' && !rowHasFile)
                        || (f === 'HAS_PROPOSAL' && rowHasProposal)
                        || (f === 'HAS_SUPPORTING' && rowHasSupporting)
                        || (f === 'HAS_REPORT' && rowHasReport)
                        || (f === 'NO_FILE' && !rowHasFile && !rowHasProposal && !rowHasSupporting && !rowHasReport);

                    const searchText = row.getAttribute('data-search') || row.innerText.toLowerCase();
                    const matchSearch = (!q || searchText.includes(q));

                    return matchType && matchStatus && matchUnit && matchCategory && matchYear && matchFile && matchSearch;
                });

                matched.sort(function(a, b) {
                    let cmp = 0;
                    if (currentSortKey === 'date') {
                        const va = parseInt(a.dataset.sortDate || '0', 10);
                        const vb = parseInt(b.dataset.sortDate || '0', 10);
                        cmp = currentSortDir === 'desc' ? (vb - va) : (va - vb);
                    } else if (currentSortKey === 'title') {
                        const ta = a.dataset.title || '';
                        const tb = b.dataset.title || '';
                        cmp = currentSortDir === 'desc' ? tb.localeCompare(ta, 'id') : ta.localeCompare(tb, 'id');
                    } else if (currentSortKey === 'doc_number') {
                        const na = a.dataset.docNumber || '';
                        const nb = b.dataset.docNumber || '';
                        cmp = currentSortDir === 'desc' ? nb.localeCompare(na, 'id') : na.localeCompare(nb, 'id');
                    } else if (currentSortKey === 'audit') {
                        const aa = parseInt(a.dataset.auditCount || '0', 10);
                        const ab = parseInt(b.dataset.auditCount || '0', 10);
                        cmp = currentSortDir === 'desc' ? (ab - aa) : (aa - ab);
                    }
                    return cmp !== 0 ? cmp : parseInt(a.dataset.origIdx, 10) - parseInt(b.dataset.origIdx, 10);
                });

                return matched;
            }

            function updateFilterIndicators() {
                const activeTab = document.querySelector('.repo-tab-btn.active');
                const t = activeTab ? activeTab.getAttribute('data-type') : 'ALL';
                const s = statusFilter ? statusFilter.value : 'ALL';
                const u = unitFilter ? unitFilter.value : 'ALL';
                const c = categoryFilter ? categoryFilter.value : 'ALL';
                const y = yearFilter ? yearFilter.value : 'ALL';
                const f = fileFilter ? fileFilter.value : 'ALL';
                const q = search ? search.value.trim() : '';

                const quickChipFilters = new Set(['HAS_SIGNED', 'DRAFT_ONLY', 'HAS_PROPOSAL', 'HAS_REPORT', 'NO_MAIN_FILE']);
                const isAdvFileFilter = (f !== 'ALL') && (!quickChipFilters.has(f) || (advPanel && !advPanel.classList.contains('d-none')));

                let advCount = 0;
                if (u !== 'ALL') advCount++;
                if (c !== 'ALL') advCount++;
                if (y !== 'ALL') advCount++;
                if (isAdvFileFilter) advCount++;
                if (s !== 'ALL') advCount++;

                if (activeBadge && toggleAdvBtn) {
                    if (advCount > 0) {
                        activeBadge.innerText = advCount;
                        activeBadge.classList.remove('d-none');
                        toggleAdvBtn.classList.add('border-primary', 'text-primary');
                    } else {
                        activeBadge.classList.add('d-none');
                        toggleAdvBtn.classList.remove('border-primary', 'text-primary');
                    }
                }

                // Sinkronkan highlight chip Ringkasan Arsip
                quickFileButtons.forEach(function(btn) {
                    const targetFilter = btn.getAttribute('data-file-filter');
                    const isMatch = (f === targetFilter);
                    btn.classList.toggle('active', isMatch);
                });

                const hasAnyFilter = (t !== 'ALL' || q.length > 0 || advCount > 0 || f !== 'ALL');
                if (resetInlineBtn) {
                    resetInlineBtn.classList.toggle('d-none', !hasAnyFilter);
                }
            }

            function renderRepo() {
                updateFilterIndicators();
                const repoTableEl = document.getElementById('repoTable');
                const activeTabType = document.querySelector('.repo-tab-btn.active')?.getAttribute('data-type') || 'ALL';
                if (repoTableEl) {
                    repoTableEl.classList.toggle('hide-legacy-badges', activeTabType === 'Legacy');
                }
                const matched = getFilteredAndSortedRows();
                const totalMatched = matched.length;

                if (clearSearchBtn) {
                    clearSearchBtn.classList.toggle('d-none', !(search && search.value.trim().length > 0));
                }

                if (visibleCountEl) {
                    visibleCountEl.innerText = totalMatched;
                }
                if (visibleCountTopEl) {
                    visibleCountTopEl.innerText = totalMatched;
                }

                if (exportExcelBtn) {
                    const isEmpty = (totalMatched === 0);
                    exportExcelBtn.disabled = isEmpty;
                    exportExcelBtn.classList.toggle('opacity-50', isEmpty);
                    exportExcelBtn.title = isEmpty
                        ? 'Tidak ada data terfilter untuk diekspor'
                        : 'Ekspor Data Terfilter ke Excel';
                }

                if (totalMatched === 0) {
                    rows.forEach(r => r.style.display = 'none');
                    if (noMatchRow) noMatchRow.classList.remove('d-none');
                    if (paginationWrapper) paginationWrapper.innerHTML = '';
                    saveRepoState();
                    return;
                }

                if (noMatchRow) noMatchRow.classList.add('d-none');

                if (tbody) {
                    matched.forEach(r => tbody.appendChild(r));
                    if (noMatchRow) tbody.appendChild(noMatchRow);
                }

                const perPageVal = perPageSelect ? perPageSelect.value : '25';
                const perPage = (perPageVal === 'ALL') ? totalMatched : parseInt(perPageVal, 10);
                const totalPages = Math.ceil(totalMatched / perPage) || 1;

                if (currentPage > totalPages) {
                    currentPage = totalPages;
                }

                const startIndex = (currentPage - 1) * perPage;
                const endIndex = startIndex + perPage;

                rows.forEach(r => r.style.display = 'none');

                matched.forEach(function(row, idx) {
                    row.style.display = (idx >= startIndex && idx < endIndex) ? '' : 'none';
                });

                renderPagination(totalPages);
                saveRepoState();
            }

            function renderPagination(totalPages) {
                if (!paginationWrapper) return;
                if (totalPages <= 1) {
                    paginationWrapper.innerHTML = '';
                    return;
                }

                let html = '';
                html += `<button type="button" class="btn btn-sm btn-outline-secondary py-1 px-2 ${currentPage === 1 ? 'disabled opacity-50' : ''}" data-page="${currentPage - 1}" aria-label="Halaman sebelumnya"><i class="bi bi-chevron-left"></i></button>`;

                for (let p = 1; p <= totalPages; p++) {
                    if (p === 1 || p === totalPages || (p >= currentPage - 1 && p <= currentPage + 1)) {
                        html += `<button type="button" class="btn btn-sm ${p === currentPage ? 'btn-primary' : 'btn-outline-secondary'} py-1 px-2 fw-semibold" data-page="${p}" aria-label="Halaman ${p}">${p}</button>`;
                    } else if (p === currentPage - 2 || p === currentPage + 2) {
                        html += `<span class="px-1 text-muted">&hellip;</span>`;
                    }
                }

                html += `<button type="button" class="btn btn-sm btn-outline-secondary py-1 px-2 ${currentPage === totalPages ? 'disabled opacity-50' : ''}" data-page="${currentPage + 1}" aria-label="Halaman berikutnya"><i class="bi bi-chevron-right"></i></button>`;

                paginationWrapper.innerHTML = html;

                paginationWrapper.querySelectorAll('button[data-page]').forEach(function(btn) {
                    btn.addEventListener('click', function() {
                        const targetPage = parseInt(this.getAttribute('data-page'), 10);
                        if (!isNaN(targetPage) && targetPage >= 1 && targetPage <= totalPages) {
                            currentPage = targetPage;
                            renderRepo();
                        }
                    });
                });
            }

            // Buka Modal Berkas & Lampiran Dinamis
            function openAttachmentsModal(docNumericId) {
                const doc = payloadsData.byId[String(docNumericId)];
                const modalEl = document.getElementById('sharedRepoAttachmentsModal');
                if (!doc || !modalEl) return;

                document.getElementById('srmAttType').innerText = doc.doc_type || '-';
                document.getElementById('srmAttMeta').innerText = ' ' + (doc.doc_number || doc.doc_id) + ' \u2022 ' + (doc.partner_name || '-');
                document.getElementById('srmMainType').innerText = doc.doc_type || '-';
                document.getElementById('srmAttDetailLink').href = doc.detail_url || '#';
                document.getElementById('srmUploadDocId').value = doc.doc_id || '';

                // Reset input unggah
                const fileInput = document.getElementById('srmUploadFileInput');
                const titleInput = document.getElementById('srmUploadTitle');
                const submitBtn = document.getElementById('srmUploadSubmitBtn');
                if (fileInput) fileInput.value = '';
                if (titleInput) titleInput.value = '';
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="bi bi-upload"></i>';
                }

                // Render Card Draf (PDF / DOCX)
                const draftBox = document.getElementById('srmDraftBox');
                const draftFiles = [];
                if (doc.draft_file) draftFiles.push({ label: 'PDF', ...doc.draft_file });
                if (doc.docx_file) draftFiles.push({ label: 'DOCX', ...doc.docx_file });

                if (draftFiles.length > 0) {
                    const names = draftFiles.map(f => escapeHtml(f.name) + ' (' + escapeHtml(f.size_text) + ')').join(' | ');
                    const btns = draftFiles.map(f =>
                        `<a href="${escapeHtml(f.url)}" target="_blank" class="btn btn-outline-secondary btn-sm py-0.5 px-2" title="Lihat ${escapeHtml(f.label)}" aria-label="Lihat Naskah Draf ${escapeHtml(f.label)}"><i class="bi bi-eye"></i></a>` +
                        `<a href="${escapeHtml(f.url)}" download class="btn btn-secondary btn-sm py-0.5 px-2" title="Unduh ${escapeHtml(f.label)}" aria-label="Unduh Naskah Draf ${escapeHtml(f.label)}"><i class="bi bi-download"></i></a>`
                    ).join('');
                    draftBox.innerHTML = `
                        <div class="text-truncate me-2">
                            <div class="fw-semibold text-dark">Naskah Draf Pengusul</div>
                            <div class="text-muted text-truncate" style="font-size: 0.68rem;" title="${names}">${names}</div>
                        </div>
                        <div class="d-flex gap-1 flex-shrink-0">${btns}</div>
                    `;
                } else {
                    draftBox.innerHTML = `
                        <div class="text-truncate me-2">
                            <div class="fw-semibold text-dark">Naskah Draf Pengusul</div>
                            <div class="text-muted" style="font-size: 0.68rem;">Belum diunggah</div>
                        </div>
                        <span class="badge badge-soft-secondary border border-secondary-subtle">Kosong</span>
                    `;
                }

                // Render Card Naskah Sah
                const signedBox = document.getElementById('srmSignedBox');
                if (doc.signed_file) {
                    signedBox.innerHTML = `
                        <div class="text-truncate me-2">
                            <div class="fw-semibold text-dark">Naskah Sah Bertandatangan</div>
                            <div class="text-muted text-truncate" style="font-size: 0.68rem;" title="${escapeHtml(doc.signed_file.name)}">${escapeHtml(doc.signed_file.name)} (${escapeHtml(doc.signed_file.size_text)})</div>
                        </div>
                        <div class="d-flex gap-1 flex-shrink-0">
                            <a href="${escapeHtml(doc.signed_file.url)}" target="_blank" class="btn btn-outline-success btn-sm py-0.5 px-2" title="Lihat Naskah Sah" aria-label="Lihat Naskah Sah"><i class="bi bi-eye"></i></a>
                            <a href="${escapeHtml(doc.signed_file.url)}" download class="btn btn-success btn-sm py-0.5 px-2" title="Unduh Naskah Sah" aria-label="Unduh Naskah Sah"><i class="bi bi-download"></i></a>
                        </div>
                    `;
                } else {
                    signedBox.innerHTML = `
                        <div class="text-truncate me-2">
                            <div class="fw-semibold text-dark">Naskah Sah Bertandatangan</div>
                            <div class="text-muted" style="font-size: 0.68rem;">Belum diunggah</div>
                        </div>
                        <span class="badge badge-soft-warning text-warning-emphasis border border-warning-subtle">Menunggu</span>
                    `;
                }

                // Render Tabel Lampiran Multi-Kategori
                const attList = Array.isArray(doc.attachments) ? doc.attachments : [];
                const repList = Array.isArray(doc.extra_reports) ? doc.extra_reports : [];
                const totalItems = attList.length + repList.length;
                document.getElementById('srmAttCountBadge').innerText = totalItems + ' Berkas';

                const container = document.getElementById('srmAttTableContainer');
                if (totalItems === 0) {
                    container.innerHTML = `
                        <div class="p-3 border rounded bg-light text-center text-muted">
                            Belum ada berkas proposal, rencana kegiatan, dokumen pendukung, atau laporan pelaksanaan.
                        </div>
                    `;
                } else {
                    const catBadgeMap = {
                        'PROPOSAL': 'badge-soft-info text-info-emphasis border-info-subtle',
                        'RENCANA_KEGIATAN': 'badge-soft-primary border-primary-subtle',
                        'LAPORAN_PELAKSANAAN': 'badge-soft-success border-success-subtle'
                    };
                    let rowsHtml = '';
                    attList.forEach(function(att) {
                        const bCls = catBadgeMap[att.category] || 'badge-soft-secondary border-secondary-subtle';
                        const metaParts = [escapeHtml(att.original_name)];
                        if (att.size_text) metaParts.push(escapeHtml(att.size_text));
                        if (att.uploaded_by) metaParts.push('oleh ' + escapeHtml(att.uploaded_by));
                        const actionLinks = att.url
                            ? `<a href="${escapeHtml(att.url)}" target="_blank" class="btn btn-light border btn-sm py-0.5 px-1.5" title="Lihat" aria-label="Lihat ${escapeHtml(att.title)}"><i class="bi bi-eye"></i></a>
                               <a href="${escapeHtml(att.url)}" download class="btn btn-light border btn-sm py-0.5 px-1.5" title="Unduh" aria-label="Unduh ${escapeHtml(att.title)}"><i class="bi bi-download"></i></a>`
                            : `<span class="text-muted fst-italic me-1" style="font-size: 0.68rem;">Hilang</span>`;

                        rowsHtml += `
                            <tr>
                                <td class="py-1 px-2"><span class="badge border ${bCls}" style="font-size: 0.68rem;">${escapeHtml(att.category_label)}</span></td>
                                <td class="py-1 px-2">
                                    <div class="fw-semibold text-dark">${escapeHtml(att.title)}</div>
                                    <div class="text-muted" style="font-size: 0.68rem;">${metaParts.join(' &bull; ')}</div>
                                </td>
                                <td class="py-1 px-2 text-center text-nowrap">
                                    <div class="d-inline-flex align-items-center gap-1">
                                        ${actionLinks}
                                        <form method="POST" action="${escapeHtml(deleteAttachmentUrl)}" class="d-inline m-0">
                                            <input type="hidden" name="_token" value="${escapeHtml(csrfToken)}">
                                            <input type="hidden" name="attachment_id" value="${escapeHtml(att.id)}">
                                            <input type="hidden" name="redirect_menu" value="3">
                                            <button type="button" class="btn btn-light border btn-sm py-0.5 px-1.5 text-danger js-confirm-delete-att" data-armed="0" title="Hapus lampiran" aria-label="Hapus lampiran ${escapeHtml(att.title)}"><i class="bi bi-trash3"></i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        `;
                    });
                    repList.forEach(function(eAct) {
                        const actionLinks = eAct.url
                            ? `<a href="${escapeHtml(eAct.url)}" target="_blank" class="btn btn-light border btn-sm py-0.5 px-1.5" title="Lihat" aria-label="Lihat Laporan ${escapeHtml(eAct.activity_name)}"><i class="bi bi-eye"></i></a>
                               <a href="${escapeHtml(eAct.url)}" download class="btn btn-light border btn-sm py-0.5 px-1.5" title="Unduh" aria-label="Unduh Laporan ${escapeHtml(eAct.activity_name)}"><i class="bi bi-download"></i></a>`
                            : `<span class="text-muted fst-italic" style="font-size: 0.68rem;">Arsip Teks</span>`;
                        rowsHtml += `
                            <tr>
                                <td class="py-1 px-2"><span class="badge border badge-soft-success border-success-subtle" style="font-size: 0.65rem;">Laporan Pelaksanaan</span></td>
                                <td class="py-1 px-2">
                                    <div class="fw-semibold text-dark">${escapeHtml(eAct.activity_name)}</div>
                                    <div class="text-muted" style="font-size: 0.66rem;">${escapeHtml(eAct.file_name)} (${escapeHtml(eAct.execution_status)})</div>
                                </td>
                                <td class="py-1 px-2 text-center text-nowrap">
                                    <div class="d-inline-flex align-items-center gap-1">${actionLinks}</div>
                                </td>
                            </tr>
                        `;
                    });

                    container.innerHTML = `
                        <div class="table-responsive border rounded">
                            <table class="table table-sm table-hover align-middle mb-0" style="font-size: 0.74rem;">
                                <thead class="table-light">
                                    <tr>
                                        <th class="py-1 px-2">Kategori</th>
                                        <th class="py-1 px-2">Nama / Judul Berkas</th>
                                        <th class="py-1 px-2 text-center" style="width: 120px;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>${rowsHtml}</tbody>
                            </table>
                        </div>
                    `;
                }

                syncUploadTitleField();
                const inst = getBsModal(modalEl);
                if (inst) inst.show();
            }

            // Buka Modal Riwayat Perubahan & Audit Dinamis
            function openAuditModal(docNumericId) {
                const doc = payloadsData.byId[String(docNumericId)];
                const modalEl = document.getElementById('sharedRepoAuditModal');
                if (!doc || !modalEl) return;

                const trails = Array.isArray(doc.audit_trails) ? doc.audit_trails : [];
                document.getElementById('srmAuditCount').innerText = String(trails.length);
                document.getElementById('srmAuditSubtitle').innerText = (doc.doc_number || doc.doc_id) + ' \u2022 ' + (doc.partner_name || '-');
                document.getElementById('srmAuditDetailLink').href = doc.detail_url || '#';

                const bodyEl = document.getElementById('srmAuditBody');
                if (trails.length === 0) {
                    bodyEl.innerHTML = '<div class="text-center py-4 text-muted">Belum ada rekaman riwayat perubahan dokumen.</div>';
                } else {
                    const trHtml = trails.map(function(t) {
                        return `
                            <tr>
                                <td class="py-1.5 px-2 font-monospace text-nowrap">${escapeHtml(t.timestamp_wita)}</td>
                                <td class="py-1.5 px-2">
                                    <div class="fw-semibold text-dark">${escapeHtml(t.actor)}</div>
                                    <span class="badge badge-soft-secondary border border-secondary-subtle mt-1" style="font-size: 0.64rem;">${escapeHtml(t.role)}</span>
                                </td>
                                <td class="py-1.5 px-2 text-nowrap">
                                    <span class="badge bg-primary text-white font-monospace d-block mb-1" style="font-size: 0.64rem;">${escapeHtml(t.event)}</span>
                                    <span class="text-muted" style="font-size: 0.68rem;">${escapeHtml(t.from_status)} &rarr; <strong>${escapeHtml(t.to_status)}</strong></span>
                                </td>
                                <td class="py-1.5 px-2 text-muted">${escapeHtml(t.notes)}</td>
                            </tr>
                        `;
                    }).join('');
                    bodyEl.innerHTML = `
                        <div class="table-responsive border rounded">
                            <table class="table table-sm table-striped table-audit-modal align-middle mb-0" style="font-size: 0.74rem;">
                                <thead>
                                    <tr>
                                        <th class="py-1.5 px-2">Waktu (WITA)</th>
                                        <th class="py-1.5 px-2">Aktor &amp; Peran</th>
                                        <th class="py-1.5 px-2">Peristiwa &amp; Status</th>
                                        <th class="py-1.5 px-2">Catatan / Detail Perubahan</th>
                                    </tr>
                                </thead>
                                <tbody>${trHtml}</tbody>
                            </table>
                        </div>
                    `;
                }

                const inst = getBsModal(modalEl);
                if (inst) inst.show();
            }

            // Sinkronisasi input judul lampiran vs naskah utama pada modal unggah
            const uploadTypeSelect = document.getElementById('srmUploadFileType');
            const titleWrap = document.getElementById('srmTitleFieldWrap');
            const mainNoteWrap = document.getElementById('srmMainNoteWrap');
            function syncUploadTitleField() {
                if (!uploadTypeSelect || !titleWrap || !mainNoteWrap) return;
                const val = uploadTypeSelect.value;
                const isMainFile = (val === 'draft' || val === 'signed');
                titleWrap.classList.toggle('d-none', isMainFile);
                mainNoteWrap.classList.toggle('d-none', !isMainFile);
            }
            if (uploadTypeSelect) {
                uploadTypeSelect.addEventListener('change', syncUploadTitleField);
            }

            // Loading state tombol unggah agar tidak double submit
            const uploadForm = document.getElementById('srmUploadForm');
            if (uploadForm) {
                uploadForm.addEventListener('submit', function() {
                    const submitBtn = document.getElementById('srmUploadSubmitBtn');
                    if (submitBtn) {
                        submitBtn.disabled = true;
                        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';
                    }
                });
            }

            // Event delegation untuk tombol modal pada baris tabel
            if (tbody) {
                tbody.addEventListener('click', function(e) {
                    const attBtn = e.target.closest('.js-open-repo-attachments');
                    if (attBtn) {
                        openAttachmentsModal(attBtn.getAttribute('data-doc-id'));
                        return;
                    }
                    const audBtn = e.target.closest('.js-open-repo-audit');
                    if (audBtn) {
                        openAuditModal(audBtn.getAttribute('data-doc-id'));
                    }
                });
            }

            // Chip Ringkasan Arsip (Filter Cepat)
            quickFileButtons.forEach(function(btn) {
                btn.addEventListener('click', function() {
                    if (!fileFilter) return;
                    const target = this.getAttribute('data-file-filter');
                    fileFilter.value = (fileFilter.value === target) ? 'ALL' : target;
                    currentPage = 1;
                    renderRepo();
                });
            });

            // Toggle Filter Lanjutan
            if (toggleAdvBtn && advPanel) {
                toggleAdvBtn.addEventListener('click', function() {
                    advPanel.classList.toggle('d-none');
                    this.classList.toggle('active');
                });
            }

            // Tab click
            tabs.forEach(function(btn) {
                btn.addEventListener('click', function() {
                    tabs.forEach(b => b.classList.remove('active'));
                    this.classList.add('active');
                    currentPage = 1;
                    renderRepo();
                });
            });

            // Search input
            if (search) {
                search.addEventListener('input', function() {
                    currentPage = 1;
                    renderRepo();
                });
            }

            // Clear search
            if (clearSearchBtn && search) {
                clearSearchBtn.addEventListener('click', function() {
                    search.value = '';
                    currentPage = 1;
                    renderRepo();
                    search.focus();
                });
            }

            // Click & keyboard (Enter/Space) pada header tabel untuk mengurutkan
            sortableHeaders.forEach(function(th) {
                function triggerHeaderSort() {
                    const key = th.getAttribute('data-sort-key');
                    if (currentSortKey === key) {
                        currentSortDir = (currentSortDir === 'desc') ? 'asc' : 'desc';
                    } else {
                        currentSortKey = key;
                        currentSortDir = (key === 'date' || key === 'audit') ? 'desc' : 'asc';
                    }
                    updateSortHeadersUI();
                    currentPage = 1;
                    renderRepo();
                }
                th.addEventListener('click', triggerHeaderSort);
                th.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter' || e.key === ' ') {
                        e.preventDefault();
                        triggerHeaderSort();
                    }
                });
            });

            // Konfirmasi dua langkah inline untuk hapus lampiran (tanpa alert browser native)
            const srmAttContainer = document.getElementById('srmAttTableContainer');
            if (srmAttContainer) {
                srmAttContainer.addEventListener('click', function(e) {
                    const delBtn = e.target.closest('.js-confirm-delete-att');
                    if (!delBtn) return;
                    if (delBtn.getAttribute('data-armed') === '1') {
                        delBtn.disabled = true;
                        delBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';
                        delBtn.closest('form')?.submit();
                        return;
                    }
                    delBtn.setAttribute('data-armed', '1');
                    delBtn.classList.remove('btn-light', 'text-danger');
                    delBtn.classList.add('btn-danger', 'text-white');
                    delBtn.innerHTML = 'Yakin?';
                    delBtn.setAttribute('title', 'Klik sekali lagi untuk menghapus permanen');
                    setTimeout(function() {
                        if (!document.body.contains(delBtn) || delBtn.disabled) return;
                        delBtn.setAttribute('data-armed', '0');
                        delBtn.classList.remove('btn-danger', 'text-white');
                        delBtn.classList.add('btn-light', 'text-danger');
                        delBtn.innerHTML = '<i class="bi bi-trash3"></i>';
                        delBtn.setAttribute('title', 'Hapus lampiran');
                    }, 3500);
                });
            }

            // Bind change events for all select filters
            [statusFilter, unitFilter, categoryFilter, yearFilter, fileFilter, perPageSelect].forEach(function(el) {
                if (el) {
                    el.addEventListener('change', function() {
                        currentPage = 1;
                        renderRepo();
                    });
                }
            });

            // Global reset function
            window.resetRepoFilters = function() {
                if (search) search.value = '';
                if (statusFilter) statusFilter.value = 'ALL';
                if (unitFilter) unitFilter.value = 'ALL';
                if (categoryFilter) categoryFilter.value = 'ALL';
                if (yearFilter) yearFilter.value = 'ALL';
                if (fileFilter) fileFilter.value = 'ALL';
                currentSortKey = 'date';
                currentSortDir = 'desc';
                updateSortHeadersUI();
                tabs.forEach(b => b.classList.remove('active'));
                const allTab = document.querySelector('.repo-tab-btn[data-type="ALL"]');
                if (allTab) allTab.classList.add('active');
                currentPage = 1;
                try { sessionStorage.removeItem(REPO_STATE_KEY); } catch (e) {}
                renderRepo();
            };

            // Ekspor baris terfilter ke Excel (kolom terstruktur bersih tanpa teks tombol UI)
            window.exportFilteredRepoToExcel = function() {
                const matched = getFilteredAndSortedRows();
                if (!matched.length) return;

                const headers = [
                    'No. Naskah UNIPAR',
                    'No. Naskah Mitra',
                    'Jenis Dokumen',
                    'Status',
                    'Judul Kerja Sama',
                    'Mitra',
                    'Kategori Mitra',
                    'Unit Pengusul',
                    'Tanggal TTD / Penetapan',
                    'Masa Berlaku',
                    'Tanggal Berakhir',
                    'Penandatangan UNIPAR',
                    'Penandatangan Mitra',
                    'Naskah Utama',
                    'Jumlah Lampiran',
                    'Jumlah Riwayat Audit'
                ];

                let html = '<table border="1"><thead><tr>';
                headers.forEach(h => { html += '<th>' + escapeHtml(h) + '</th>'; });
                html += '</tr></thead><tbody>';

                matched.forEach(function(row) {
                    const ds = row.dataset;
                    const cols = [
                        ds.exportDocNum || '-',
                        ds.exportPartnerNum || '-',
                        ds.exportType || '-',
                        ds.exportStatus || '-',
                        ds.exportTitle || '-',
                        ds.exportPartner || '-',
                        ds.exportCategory || '-',
                        ds.exportUnit || '-',
                        ds.exportDate || '-',
                        ds.exportValidity || '-',
                        ds.exportEndDate || '-',
                        ds.exportSigner1 || '-',
                        ds.exportSigner2 || '-',
                        ds.exportMainFile || 'Belum ada',
                        ds.exportAttachments || '0',
                        ds.exportAudit || '0'
                    ];
                    html += '<tr>' + cols.map(c => '<td>' + escapeHtml(c) + '</td>').join('') + '</tr>';
                });
                html += '</tbody></table>';

                const blob = new Blob(['\ufeff', html], { type: 'application/vnd.ms-excel;charset=utf-8;' });
                const url = URL.createObjectURL(blob);
                const a = document.createElement('a');
                const today = new Date().toISOString().slice(0, 10);
                a.href = url;
                a.download = `Repositori_Dokumen_Kerja_Sama_${today}.xls`;
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
                URL.revokeObjectURL(url);
            };

            // Pulihkan state filter/tab/halaman & inisialisasi tabel
            restoreRepoState();
            updateSortHeadersUI();
            renderRepo();

            // Pintasan keyboard '/' untuk fokus langsung ke pencarian repositori
            document.addEventListener('keydown', function(e) {
                if (e.key !== '/' || e.ctrlKey || e.metaKey || e.altKey) return;
                const tag = (document.activeElement?.tagName || '').toUpperCase();
                if (tag === 'INPUT' || tag === 'TEXTAREA' || tag === 'SELECT' || document.activeElement?.isContentEditable) return;
                if (search && search.offsetParent !== null) {
                    e.preventDefault();
                    search.focus();
                    search.select();
                }
            });

            // Buka modal berkas otomatis pasca redirect unggah/hapus
            try {
                const urlObj = new URL(window.location.href);
                const openDocParam = urlObj.searchParams.get('open_attachments');
                if (openDocParam && payloadsData.byDocId && payloadsData.byDocId[openDocParam]) {
                    openAttachmentsModal(payloadsData.byDocId[openDocParam]);
                    urlObj.searchParams.delete('open_attachments');
                    window.history.replaceState({}, document.title, urlObj.pathname + urlObj.search);
                }
            } catch (e) {}
        });
        </script>
        @endpush

    {{-- ===================================================================================== --}}
    {{-- 4. PENGAJUAN DAN PERSETUJUAN KERJA SAMA (SISTEM RESMI AKTIF & REAL FSM ENGINE)        --}}
    {{-- ===================================================================================== --}}
    @elseif ($activeMenu === 4)
        @php
            $stepperList = [
                'DRAFT' => ['1. DRAFT', 'bg-secondary text-white'],
                'REVIEW' => ['2. REVIEW', 'bg-info text-dark'],
                'VERIFIED' => ['3. VERIFIED', 'bg-primary text-white'],
                'ACTIVE' => ['4. ACTIVE', 'bg-success text-white'],
                'WARNING' => ['5. WARNING', 'bg-warning text-dark'],
                'EXPIRED' => ['6. EXPIRED', 'bg-danger text-white'],
                'CANCELLED' => ['7. CANCELLED', 'bg-dark text-white']
            ];
        @endphp

        {{-- ===================================================================================== --}}
        {{-- 1. FORMULIR PENGAJUAN DOKUMEN BARU (MoU / MoA / IA DENGAN DOKUMEN INDUK)              --}}
        {{-- ===================================================================================== --}}
        @if ($isCreatingNew)

            <div class="card card-wireframe mb-4 shadow-sm border">
                <div class="card-header bg-white py-3 border-bottom d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
                    <div>
                        <h5 class="card-title mb-0 fw-bold text-dark">
                            <i class="bi bi-file-earmark-plus text-primary me-2"></i> Pengajuan Dokumen Kerja Sama Baru
                        </h5>
                        <div class="small text-muted">
                            Pengajuan naskah kerja sama kemitraan institusi (<strong>MoU</strong>, <strong>MoA / PKS</strong>, atau <strong>IA</strong>) diterbitkan dengan status awal <strong>DRAFT</strong> untuk penelaahan internal naskah. Naskah turunan (MoA / IA) wajib merujuk pada dokumen induk yang telah terdaftar.
                        </div>
                    </div>
                    <a href="{{ route('simkerma.index', ['menu' => 4]) }}" class="btn btn-outline-secondary btn-sm fw-semibold">
                        <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar Pengajuan
                    </a>
                </div>
                <div class="card-body p-4">

                    <form method="POST" action="{{ route('simkerma.store-jalur1') }}" enctype="multipart/form-data" class="form-kerma-submission">
                        @csrf

                        <!-- =================================================================== -->
                        <!-- SECTION 1: KLASIFIKASI & INFORMASI DOKUMEN KERJA SAMA                -->
                        <!-- =================================================================== -->
                        <div class="card border mb-4 shadow-sm" style="border-radius: 8px;">
                            <div class="card-header bg-light py-2 px-3 border-bottom d-flex align-items-center gap-2">
                                <span class="badge bg-primary text-white rounded-pill px-2 py-1"><i class="bi bi-file-earmark-text"></i> Bagian 1</span>
                                <h6 class="mb-0 fw-bold text-dark fs-6">Klasifikasi &amp; Informasi Dokumen Kerja Sama</h6>
                            </div>
                            <div class="card-body p-3 p-md-4">
                                <!-- JENIS DOKUMEN -->
                                <div class="row mb-3 align-items-center">
                                    <label class="col-md-3 col-form-label fw-bold text-dark">
                                        Jenis Dokumen Kerja Sama : <span class="text-danger">*</span>
                                    </label>
                                    <div class="col-md-9">
                                        <select name="doc_type" id="inputDocType" class="form-select" required onchange="toggleParentField()">
                                            <option value="MoU" selected>MoU (Nota Kesepahaman / Tingkat Institusi/Rektorat)</option>
                                            <option value="MoA / PKS">MoA / PKS (Perjanjian Kerja Sama / Tingkat Fakultas/UPPS)</option>
                                            <option value="IA">IA (Implementation Arrangement / Tingkat Program Studi/Unit)</option>
                                        </select>
                                        <div class="form-text text-muted">
                                            Pilih jenis naskah kerja sama yang diajukan. Untuk <strong>MoA / PKS</strong> dan <strong>IA</strong>, sistem mewajibkan pemilihan Dokumen Induk (MoU / MoA payung).
                                        </div>
                                    </div>
                                </div>

                                <!-- DOKUMEN INDUK (KONDISIONAL: JIKA MOA / IA) -->
                                <div class="row mb-3 align-items-center" id="parentDocRow" style="display: none;">
                                    <label class="col-md-3 col-form-label fw-bold text-dark">
                                        Dokumen Induk Rujukan : <span class="text-danger">*</span>
                                    </label>
                                    <div class="col-md-9">
                                        <select name="parent_mou" id="inputParentMou" class="form-select" onchange="autoFillFromParent()">
                                            <option value="">-- Pilih Dokumen Induk (MoU / MoA yang telah terdaftar) --</option>
                                            @foreach ($registeredMous as $rMou)
                                                <option value="{{ $rMou->doc_number ?: $rMou->doc_id }}" 
                                                        data-partner="{{ $rMou->partner_name }}"
                                                        data-category="{{ $rMou->partner_category }}"
                                                        data-scope="{{ $rMou->scope }}"
                                                        data-unit="{{ $rMou->unit }}">
                                                    [{{ $rMou->doc_type }}] {{ $rMou->doc_number ?: $rMou->doc_id }} &bull; {{ $rMou->partner_name }} ({{ Str::limit($rMou->title, 45) }})
                                                </option>
                                            @endforeach
                                        </select>
                                        <div class="form-text text-muted">
                                            <i class="bi bi-info-circle me-1"></i> Wajib untuk MoA/PKS dan IA sesuai tata kelola IKU 6. Data mitra akan otomatis diselaraskan dengan dokumen induk terpilih.
                                        </div>
                                    </div>
                                </div>

                                <!-- JUDUL / TOPIK KERJA SAMA -->
                                <div class="row mb-3 align-items-center">
                                    <label class="col-md-3 col-form-label fw-bold text-dark" id="labelDocTitle">
                                        Judul / Topik Kerja Sama : <span class="text-danger">*</span>
                                    </label>
                                    <div class="col-md-9">
                                        <input type="text" name="title" class="form-control" placeholder="Contoh: Kerja Sama Penyelenggaraan Tridharma Perguruan Tinggi..." required>
                                    </div>
                                </div>

                                <!-- NOMOR NASKAH PIHAK 1 (INTERNAL UNIPAR) & NOMOR NASKAH PIHAK 2 (MITRA) -->
                                <div class="row mb-3 align-items-center">
                                    <label class="col-md-3 col-form-label fw-bold text-dark">
                                        Nomor Naskah UNIPAR :
                                        <span class="badge bg-light text-secondary border fw-normal ms-1">Opsional</span>
                                    </label>
                                    <div class="col-md-9">
                                        <input type="text" name="doc_number" class="form-control font-monospace" placeholder="Kosongkan jika belum ada (otomatis diisi '-')">
                                        <div class="form-text text-muted">
                                            Nomor naskah resmi UNIPAR. Jika dikosongkan / belum diterbitkan, otomatis akan dicatat sebagai <code>-</code>.
                                        </div>
                                    </div>
                                </div>

                                <div class="row mb-3 align-items-center">
                                    <label class="col-md-3 col-form-label fw-bold text-dark">
                                        Nomor Naskah Mitra :
                                        <span class="badge bg-light text-secondary border fw-normal ms-1">Opsional</span>
                                    </label>
                                    <div class="col-md-9">
                                        <input type="text" name="partner_doc_number" class="form-control font-monospace" placeholder="Kosongkan jika belum ada (otomatis diisi '-')">
                                        <div class="form-text text-muted">
                                            Nomor naskah resmi dari pihak mitra luar / instansi mitra. Jika belum ada, otomatis akan dicatat sebagai <code>-</code>.
                                        </div>
                                    </div>
                                </div>

                                <!-- DESKRIPSI / URGENSI KERJA SAMA -->
                                <div class="row mb-0 align-items-start">
                                    <label class="col-md-3 col-form-label fw-bold text-dark pt-1">
                                        Deskripsi / Urgensi Kerja Sama :
                                    </label>
                                    <div class="col-md-9">
                                        <textarea name="description" class="form-control" rows="3" placeholder="Uraikan ringkasan substansi, tujuan, atau urgensi pelaksanaan kerja sama ini..."></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- =================================================================== -->
                        <!-- SECTION 2: IDENTITAS INTERNAL UNIPAR                                -->
                        <!-- =================================================================== -->
                        <div class="card border mb-4 shadow-sm" style="border-radius: 8px;">
                            <div class="card-header bg-light py-2 px-3 border-bottom d-flex align-items-center gap-2">
                                <span class="badge bg-primary text-white rounded-pill px-2 py-1"><i class="bi bi-building"></i> Bagian 2</span>
                                <h6 class="mb-0 fw-bold text-dark fs-6">Identitas Internal UNIPAR</h6>
                            </div>
                            <div class="card-body p-3 p-md-4">
                                <!-- UNIT PENGUSUL -->
                                <div class="row mb-3 align-items-center">
                                    <label class="col-md-3 col-form-label fw-bold text-dark">
                                        Unit Pengusul : <span class="text-danger">*</span>
                                    </label>
                                    <div class="col-md-9">
                                        <select name="unit" class="form-select" required>
                                            <option>Biro Kerja Sama &amp; Urusan Internasional (BKUI)</option>
                                            <option>Program Studi Teknik Informatika</option>
                                            <option>Program Studi Sistem Informasi</option>
                                            <option>Program Studi Sistem Komputer</option>
                                            <option>Program Studi Bisnis Digital</option>
                                            <option>Program Studi Kewirausahaan</option>
                                            <option>Program Studi Hukum Bisnis</option>
                                            <option>Fakultas Ilmu Komputer</option>
                                            <option>Fakultas Ekonomi dan Bisnis</option>
                                            <option>LPPM</option>
                                            <option>Biro Administrasi Akademik &amp; Kemahasiswaan</option>
                                            <option>Rektorat</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- PENANDATANGAN UNIPAR -->
                                <div class="row mb-3 align-items-center">
                                    <label class="col-md-3 col-form-label fw-bold text-dark">
                                        Penandatangan UNIPAR :
                                    </label>
                                    <div class="col-md-9">
                                        <input type="text" name="first_party_signer" class="form-control" placeholder="Nama lengkap &amp; gelar pejabat penandatangan dari UNIPAR">
                                    </div>
                                </div>

                                <!-- JABATAN PENANDATANGAN UNIPAR -->
                                <div class="row mb-3 align-items-center">
                                    <label class="col-md-3 col-form-label fw-bold text-dark">
                                        Jabatan Penandatangan :
                                    </label>
                                    <div class="col-md-9">
                                        <input type="text" name="first_party_position" class="form-control" placeholder="Contoh: Rektor, Dekan Fakultas Ilmu Komputer, Ketua LPPM">
                                    </div>
                                </div>

                                <!-- PIC UNIPAR -->
                                <div class="row mb-0 align-items-center">
                                    <label class="col-md-3 col-form-label fw-bold text-dark">
                                        PIC / Narahubung UNIPAR :
                                    </label>
                                    <div class="col-md-9">
                                        <input type="text" name="pic_first_party" class="form-control" placeholder="Nama &amp; kontak narahubung pelaksanaan dari internal kampus">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- =================================================================== -->
                        <!-- SECTION 3: IDENTITAS MITRA KERJA SAMA                               -->
                        <!-- =================================================================== -->
                        <div class="card border mb-4 shadow-sm" style="border-radius: 8px;">
                            <div class="card-header bg-light py-2 px-3 border-bottom d-flex align-items-center gap-2">
                                <span class="badge bg-primary text-white rounded-pill px-2 py-1"><i class="bi bi-people"></i> Bagian 3</span>
                                <h6 class="mb-0 fw-bold text-dark fs-6">Identitas Mitra Kerja Sama</h6>
                            </div>
                            <div class="card-body p-3 p-md-4">
                                <!-- KATEGORI MITRA -->
                                <div class="row mb-3 align-items-center">
                                    <label class="col-md-3 col-form-label fw-bold text-dark">
                                        Kategori / Sektor Mitra : <span class="text-danger">*</span>
                                    </label>
                                    <div class="col-md-9">
                                        <select name="category" id="category_jalur1" class="form-select" required>
                                            <option value="Perguruan Tinggi" selected>Perguruan Tinggi</option>
                                            <option value="DUDI / Swasta">DUDI / Swasta</option>
                                            <option value="BUMN">BUMN</option>
                                            <option value="Instansi Pemerintah">Instansi Pemerintah</option>
                                            <option value="Lembaga Keagamaan / Komunitas">Lembaga Keagamaan / Komunitas</option>
                                            <option value="Kesehatan">Kesehatan</option>
                                            <option value="Lainnya">Lainnya</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- NAMA MITRA -->
                                <div class="row mb-3 align-items-center">
                                    <label class="col-md-3 col-form-label fw-bold text-dark">
                                        Nama Mitra : <span class="text-danger">*</span>
                                    </label>
                                    <div class="col-md-9">
                                        <div class="hybrid-partner-combobox position-relative">
                                            <div class="input-group">
                                                <span class="input-group-text bg-white border-end-0">
                                                    <i class="partner-input-icon bi bi-mortarboard-fill text-primary fs-5"></i>
                                                </span>
                                                <input type="text" name="partner_name" id="partner_name_jalur1" class="form-control border-start-0 smart-partner-input" 
                                                       placeholder="Ketik nama kampus (contoh: Sam Ratulangi, ITB, Parna Raya)..." 
                                                       value="{{ request('partner_name') }}"
                                                       list="partnerDatalist"
                                                       autocomplete="off" required>
                                                <button class="btn btn-outline-secondary btn-clear-partner d-none" type="button" title="Hapus / Reset Pilihan"><i class="bi bi-x-lg"></i></button>
                                                <button class="btn btn-primary btn-sm px-3 fw-bold btn-open-quick-add" type="button" data-bs-toggle="modal" data-bs-target="#quickAddPartnerModal" title="Tambah Mitra Baru ke Master Data (Menu 02)">
                                                    <i class="bi bi-plus-lg me-1"></i> Tambah Mitra
                                                </button>
                                            </div>
                                            <datalist id="partnerDatalist">
                                                @foreach ($partners as $p)
                                                    <option value="{{ $p->name }}">{{ $p->category }} &bull; {{ $p->city ?: 'Manado' }}</option>
                                                @endforeach
                                            </datalist>
                                            <!-- Floating Dropdown Suggestions -->
                                            <div class="partner-dropdown-menu shadow-lg bg-white border rounded mt-1 d-none position-absolute w-100" style="z-index: 1050; max-height: 280px; overflow-y: auto;"></div>
                                            <!-- Verified Info Banner -->
                                            <div class="partner-verified-info small mt-1.5 d-none"></div>
                                            <!-- Helper Tools -->
                                            <div class="d-flex flex-wrap justify-content-between align-items-center mt-1">
                                                <small class="partner-mode-desc text-muted"><i class="bi bi-database-check text-success me-1"></i>Direktori Resmi PDDikti Kemendikbudristek (4.650+ PT)</small>
                                                <a href="javascript:void(0)" class="small text-decoration-none text-primary btn-toggle-manual"><i class="bi bi-pencil-square me-1"></i>Ketik Manual / Luar Negeri</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- PENANDATANGAN MITRA -->
                                <div class="row mb-3 align-items-center">
                                    <label class="col-md-3 col-form-label fw-bold text-dark">
                                        Penandatangan Mitra :
                                    </label>
                                    <div class="col-md-9">
                                        <input type="text" name="second_party_signer" class="form-control" placeholder="Nama lengkap &amp; gelar pejabat penandatangan dari pihak Mitra">
                                    </div>
                                </div>

                                <!-- JABATAN PENANDATANGAN MITRA -->
                                <div class="row mb-3 align-items-center">
                                    <label class="col-md-3 col-form-label fw-bold text-dark">
                                        Jabatan Penandatangan :
                                    </label>
                                    <div class="col-md-9">
                                        <input type="text" name="second_party_position" class="form-control" placeholder="Contoh: Direktur Utama, Rektor, Kepala Dinas, General Manager">
                                    </div>
                                </div>

                                <!-- PIC MITRA -->
                                <div class="row mb-0 align-items-center">
                                    <label class="col-md-3 col-form-label fw-bold text-dark">
                                        PIC / Narahubung Mitra :
                                    </label>
                                    <div class="col-md-9">
                                        <input type="text" name="pic_second_party" class="form-control" placeholder="Nama &amp; kontak narahubung dari pihak mitra">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- =================================================================== -->
                        <!-- SECTION 4: RUANG LINGKUP TRIDHARMA & NILAI KERJA SAMA                -->
                        <!-- =================================================================== -->
                        <div class="card border mb-4 shadow-sm" style="border-radius: 8px;">
                            <div class="card-header bg-light py-2 px-3 border-bottom d-flex align-items-center gap-2">
                                <span class="badge bg-primary text-white rounded-pill px-2 py-1"><i class="bi bi-diagram-3"></i> Bagian 4</span>
                                <h6 class="mb-0 fw-bold text-dark fs-6">Ruang Lingkup Kegiatan &amp; Nilai Kerja Sama</h6>
                            </div>
                            <div class="card-body p-3 p-md-4">
                                <!-- RUANG LINGKUP TRIDHARMA / MBKM -->
                                <div class="row mb-3 align-items-center">
                                    <label class="col-md-3 col-form-label fw-bold text-dark">
                                        Ruang Lingkup Kemitraan : <span class="text-danger">*</span>
                                    </label>
                                    <div class="col-md-9">
                                        <div class="d-flex flex-wrap gap-3 p-2.5 bg-light rounded border">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="scope[]" value="Pendidikan" id="new_scope1" checked>
                                                <label class="form-check-label fw-medium text-dark" for="new_scope1">Pendidikan / Pengajaran</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="scope[]" value="Penelitian" id="new_scope2" checked>
                                                <label class="form-check-label fw-medium text-dark" for="new_scope2">Penelitian &amp; Publikasi</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="scope[]" value="Pengabdian kepada Masyarakat" id="new_scope3" checked>
                                                <label class="form-check-label fw-medium text-dark" for="new_scope3">Pengabdian kepada Masyarakat (PkM)</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="scope[]" value="Pengembangan SDM" id="new_scope4" checked>
                                                <label class="form-check-label fw-medium text-dark" for="new_scope4">Pengembangan SDM &amp; MBKM</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- NILAI REALISASI KERJA SAMA (OPSIONAL) -->
                                <div class="row mb-3 align-items-center">
                                    <label class="col-md-3 col-form-label fw-bold text-dark">
                                        Perkiraan Nilai Kerja Sama :
                                        <span class="badge bg-light text-secondary border fw-normal ms-1">Opsional</span>
                                    </label>
                                    <div class="col-md-9">
                                        <div class="input-group">
                                            <span class="input-group-text bg-light text-muted">Rp</span>
                                            <input type="text" name="nominal_value" class="form-control" placeholder="Contoh: 50.000.000 (Kosongkan jika non-finansial)">
                                        </div>
                                        <div class="form-text text-muted">Diisi jika naskah kerja sama memiliki komitmen pendanaan kegiatan atau hibah.</div>
                                    </div>
                                </div>

                                <!-- DESKRIPSI RINGKASAN -->
                                <div class="row mb-0">
                                    <label class="col-md-3 col-form-label fw-bold text-dark">
                                        Ringkasan / Catatan Naskah :
                                    </label>
                                    <div class="col-md-9">
                                        <textarea name="description" class="form-control" rows="2" placeholder="Tuliskan pokok-pokok kesepakatan atau latar belakang inisiasi kerja sama ini..."></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- =================================================================== -->
                        <!-- SECTION 5: PERIODE MASA BERLAKU & BERKAS NASKAH                     -->
                        <!-- =================================================================== -->
                        <div class="card border mb-4 shadow-sm" style="border-radius: 8px;">
                            <div class="card-header bg-light py-2 px-3 border-bottom d-flex align-items-center gap-2">
                                <span class="badge bg-primary text-white rounded-pill px-2 py-1"><i class="bi bi-clock-history"></i> Bagian 5</span>
                                <h6 class="mb-0 fw-bold text-dark fs-6">Periode Masa Berlaku &amp; Berkas Naskah</h6>
                            </div>
                            <div class="card-body p-3 p-md-4">
                                <!-- TANGGAL MULAI BERLAKU -->
                                <div class="row mb-3 align-items-center">
                                    <label class="col-md-3 col-form-label fw-bold text-dark">
                                        Tanggal Mulai Berlaku :
                                        <span class="badge bg-light text-secondary border fw-normal ms-1">Opsional</span>
                                    </label>
                                    <div class="col-md-9">
                                        <input type="date" name="start_date" id="inputStartDate" class="form-control" onchange="calculateEndDate()">
                                        <div class="form-text text-muted">Opsional pada tahap draf awal; wajib ditetapkan saat transisi pengesahan/ACTIVE.</div>
                                    </div>
                                </div>

                                <!-- MASA BERLAKU TAHUN -->
                                <div class="row mb-3 align-items-center">
                                    <label class="col-md-3 col-form-label fw-bold text-dark">
                                        Masa Berlaku (Tahun) :
                                        <span class="badge bg-light text-secondary border fw-normal ms-1">Opsional</span>
                                    </label>
                                    <div class="col-md-9">
                                        <div class="input-group">
                                            <input type="number" min="1" max="10" name="validity_years" id="inputValidityYears" class="form-control" oninput="calculateEndDate()">
                                            <span class="input-group-text bg-light text-muted">Tahun</span>
                                        </div>
                                        <input type="hidden" name="validity" id="inputValidity">
                                        <div class="form-text text-muted">Masa berlaku standar naskah MoU/MoA perguruan tinggi berkisar antara 1 hingga 5 tahun.</div>
                                    </div>
                                </div>

                                <!-- TANGGAL BERAKHIR -->
                                <div class="row mb-3 align-items-center">
                                    <label class="col-md-3 col-form-label fw-bold text-dark">
                                        Tanggal Berakhir :
                                    </label>
                                    <div class="col-md-9">
                                        <input type="date" name="end_date" id="inputEndDate" class="form-control bg-light" readonly>
                                        <div class="form-text text-muted">
                                            <i class="bi bi-calculator me-1"></i> Terkalkulasi otomatis (read-only): <em>Tanggal Mulai</em> + <em>Masa Berlaku (Tahun)</em>.
                                        </div>
                                    </div>
                                </div>

                                <!-- UPLOAD DRAF NASKAH PDF -->
                                <div class="row mb-3 align-items-center">
                                    <label class="col-md-3 col-form-label fw-bold text-dark" id="labelUploadDraft">
                                        Upload Draf Naskah (PDF) : <span class="text-danger">*</span>
                                    </label>
                                    <div class="col-md-9">
                                        <input type="file" name="draft_file" class="form-control" accept=".pdf" required>
                                        <div class="form-text text-muted">Format berkas: <strong>PDF (.pdf)</strong>. Berkas draf awal untuk ditelaah dan diverifikasi oleh Bagian Kerja Sama/Legal.</div>
                                    </div>
                                </div>

                                <!-- UPLOAD PROPOSAL & RENCANA KEGIATAN (OPSIONAL) -->
                                <div class="row mb-3 align-items-center">
                                    <label class="col-md-3 col-form-label fw-bold text-dark">
                                        Proposal / Rencana Kegiatan :
                                        <span class="badge bg-light text-secondary border fw-normal ms-1">Opsional</span>
                                    </label>
                                    <div class="col-md-9">
                                        <div class="row g-2">
                                            <div class="col-sm-6">
                                                <input type="file" name="proposal_file" class="form-control form-control-sm" accept=".pdf,.doc,.docx">
                                                <div class="form-text text-muted" style="font-size: 0.72rem;">Berkas Proposal Kerja Sama (PDF/Word)</div>
                                            </div>
                                            <div class="col-sm-6">
                                                <input type="file" name="rencana_file" class="form-control form-control-sm" accept=".pdf,.doc,.docx,.xls,.xlsx">
                                                <div class="form-text text-muted" style="font-size: 0.72rem;">Rencana Kegiatan / TOR (PDF/Word/Excel)</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- UPLOAD DOKUMEN PENDUKUNG (MULTI-FILE OPSIONAL) -->
                                <div class="row mb-0 align-items-center">
                                    <label class="col-md-3 col-form-label fw-bold text-dark">
                                        Dokumen Pendukung :
                                        <span class="badge bg-light text-secondary border fw-normal ms-1">Opsional</span>
                                    </label>
                                    <div class="col-md-9">
                                        <input type="file" name="supporting_files[]" class="form-control form-control-sm" multiple accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png">
                                        <div class="form-text text-muted">Notula pembahasan, surat tugas, profil/legalitas mitra, dll. (Dapat memilih lebih dari satu file sekaligus).</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- TOMBOL AKSI SIMPAN -->
                        <div class="row pt-2">
                            <div class="col-12 d-flex justify-content-end gap-2">
                                <a href="{{ route('simkerma.index', ['menu' => 4]) }}" class="btn btn-outline-secondary px-4 py-2">
                                    <i class="bi bi-x-circle me-1"></i> Batal
                                </a>
                                <button type="submit" class="btn btn-primary fw-bold px-4 py-2 shadow-sm" id="btnSubmitSubmission">
                                    <i class="bi bi-save me-1"></i> Simpan Sebagai DRAF Dokumen
                                </button>
                            </div>
                        </div>
                    </form>                </div>
            </div>

            <script>
            function toggleParentField() {
                const docType = document.getElementById('inputDocType').value;
                const parentRow = document.getElementById('parentDocRow');
                const parentSelect = document.getElementById('inputParentMou');
                const btnSubmit = document.getElementById('btnSubmitSubmission');
                const labelTitle = document.getElementById('labelDocTitle');
                const labelDraft = document.getElementById('labelUploadDraft');

                if (docType === 'MoU') {
                    parentRow.style.display = 'none';
                    parentSelect.removeAttribute('required');
                    parentSelect.value = '';
                    if (btnSubmit) btnSubmit.innerHTML = '<i class="bi bi-save me-1"></i> Simpan Sebagai DRAF MoU';
                    if (labelTitle) labelTitle.innerHTML = 'Judul / Topik Kerja Sama MoU : <span class="text-danger">*</span>';
                    if (labelDraft) labelDraft.innerHTML = 'Upload Draf Naskah MoU (PDF) : <span class="text-danger">*</span>';
                } else {
                    parentRow.style.display = 'flex';
                    parentSelect.setAttribute('required', 'required');
                    if (btnSubmit) btnSubmit.innerHTML = '<i class="bi bi-save me-1"></i> Simpan Sebagai DRAF ' + docType;
                    if (labelTitle) labelTitle.innerHTML = 'Judul / Topik ' + docType + ' : <span class="text-danger">*</span>';
                    if (labelDraft) labelDraft.innerHTML = 'Upload Draf Naskah ' + docType + ' (PDF) : <span class="text-danger">*</span>';
                }
            }

            function autoFillFromParent() {
                const parentSelect = document.getElementById('inputParentMou');
                if (!parentSelect) return;
                const selectedOpt = parentSelect.options[parentSelect.selectedIndex];
                if (!selectedOpt || !selectedOpt.value) return;

                const partnerName = selectedOpt.getAttribute('data-partner');
                const category = selectedOpt.getAttribute('data-category');

                if (partnerName) {
                    const partnerInput = document.getElementById('partner_name_jalur1');
                    if (partnerInput && !partnerInput.value) {
                        partnerInput.value = partnerName;
                    }
                }
                if (category) {
                    const catSelect = document.getElementById('category_jalur1');
                    if (catSelect) {
                        for (let i = 0; i < catSelect.options.length; i++) {
                            if (catSelect.options[i].value === category) {
                                catSelect.selectedIndex = i;
                                break;
                            }
                        }
                    }
                }
            }

            function calculateEndDate() {
                const startEl = document.getElementById('inputStartDate');
                const yearsEl = document.getElementById('inputValidityYears');
                const endEl = document.getElementById('inputEndDate');
                const validityEl = document.getElementById('inputValidity');

                if (!startEl || !yearsEl || !endEl) return;

                const years = parseInt(yearsEl.value, 10);
                if (validityEl) {
                    validityEl.value = (years && !isNaN(years)) ? (years + ' Tahun') : '';
                }

                if (!startEl.value || isNaN(years) || years <= 0) {
                    endEl.value = '';
                    return;
                }

                const startDate = new Date(startEl.value);
                if (isNaN(startDate.getTime())) {
                    endEl.value = '';
                    return;
                }

                const endDate = new Date(startDate);
                endDate.setFullYear(endDate.getFullYear() + years);
                const yyyy = endDate.getFullYear();
                const mm = String(endDate.getMonth() + 1).padStart(2, '0');
                const dd = String(endDate.getDate()).padStart(2, '0');
                endEl.value = `${yyyy}-${mm}-${dd}`;
            }
            document.addEventListener('DOMContentLoaded', function() {
                toggleParentField();
                if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
                    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                    tooltipTriggerList.map(function (tooltipTriggerEl) {
                        return new bootstrap.Tooltip(tooltipTriggerEl);
                    });
                }
            });
            </script>

        {{-- ===================================================================================== --}}
        {{-- 2. FORMULIR PENGAJUAN DOKUMEN TURUNAN (MoA / IA) MENURUT INDUK MOU                    --}}
        {{-- ===================================================================================== --}}
        @elseif ($isCreatingDerive)

            <div class="card card-wireframe mb-4 shadow-sm border">
                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title mb-0 fw-bold text-dark">
                            <i class="bi bi-diagram-3-fill text-primary me-2"></i> Formulir Dokumen Turunan ({{ $prefillDeriveType }})
                        </h5>
                        <div class="small text-muted">
                            Menurunkan naskah turunan pelaksanaan dari dokumen induk <strong>{{ $prefillParentDocNumber ?: $prefillParentId }}</strong> bersama mitra <strong>{{ $prefillPartnerName }}</strong>.
                        </div>
                    </div>
                    <a href="{{ route('simkerma.index', ['menu' => 4, 'view' => 'detail', 'doc' => $prefillParentId]) }}" class="btn btn-outline-secondary btn-sm fw-semibold">
                        <i class="bi bi-arrow-left me-1"></i> Kembali ke Detail Dokumen Induk
                    </a>
                </div>
                <div class="card-body p-4">

                    <form method="POST" action="{{ route('simkerma.store-jalur1') }}" enctype="multipart/form-data" class="form-kerma-submission">
                        @csrf
                        <input type="hidden" name="parent_mou" value="{{ $prefillParentDocNumber ?: $prefillParentId }}">

                        <!-- DOKUMEN INDUK INFORMASI -->
                        <div class="alert alert-info py-2 px-3 small mb-4 d-flex align-items-center gap-2 border shadow-sm">
                            <i class="bi bi-link-45deg fs-4 text-primary"></i>
                            <div>
                                <strong>Menginduk ke Naskah Resmi:</strong> Dokumen turunan ini akan terikat secara hukum pada MoU Induk <strong>{{ $prefillParentDocNumber ?: $prefillParentId }}</strong> bersama mitra <strong>{{ $prefillPartnerName }}</strong>.
                            </div>
                        </div>

                        <!-- 1. JUDUL / NAMA KERJA SAMA TURUNAN -->
                        <div class="row mb-3 align-items-center">
                            <label class="col-md-3 col-form-label fw-bold text-dark">
                                Judul Naskah Turunan : <span class="text-danger">*</span>
                            </label>
                            <div class="col-md-9">
                                <input type="text" name="title" class="form-control" placeholder="Contoh: Perjanjian Kerja Sama Penyelenggaraan Program Magang Industri MBKM..." required>
                            </div>
                        </div>

                        <!-- 2. JENIS DOKUMEN TURUNAN -->
                        <div class="row mb-3 align-items-center">
                            <label class="col-md-3 col-form-label fw-bold text-dark">
                                Jenis Dokumen Turunan : <span class="text-danger">*</span>
                            </label>
                            <div class="col-md-9">
                                <select name="doc_type" class="form-select" required>
                                    <option value="MoA / PKS" {{ stripos($prefillDeriveType, 'MoA') !== false ? 'selected' : '' }}>MoA / PKS (Perjanjian Kerja Sama &mdash; Tingkat Fakultas)</option>
                                    <option value="IA" {{ stripos($prefillDeriveType, 'IA') !== false ? 'selected' : '' }}>IA (Implementation Arrangement &mdash; Tingkat Prodi/Lab)</option>
                                </select>
                            </div>
                        </div>

                        <!-- 3. IDENTITAS UNIT PELAKSANA -->
                        <div class="row mb-3 align-items-center">
                            <label class="col-md-3 col-form-label fw-bold text-dark">
                                Unit Pelaksana (Pihak I) : <span class="text-danger">*</span>
                            </label>
                            <div class="col-md-9">
                                <select name="unit" class="form-select" required>
                                    <option>Biro Kerja Sama &amp; Urusan Internasional (BKUI)</option>
                                    <option>Program Studi Teknik Informatika</option>
                                    <option>Program Studi Sistem Informasi</option>
                                    <option>Program Studi Sistem Komputer</option>
                                    <option>Program Studi Bisnis Digital</option>
                                    <option>Program Studi Kewirausahaan</option>
                                    <option>Program Studi Hukum Bisnis</option>
                                    <option>Fakultas Ilmu Komputer</option>
                                    <option>Fakultas Ekonomi dan Bisnis</option>
                                    <option>LPPM</option>
                                    <option>Biro Administrasi Akademik &amp; Kemahasiswaan</option>
                                    <option>Rektorat</option>
                                </select>
                            </div>
                        </div>

                        <!-- 4. MITRA (PREFILLED DARI INDUK) -->
                        <div class="row mb-3 align-items-center">
                            <label class="col-md-3 col-form-label fw-bold text-dark">
                                Nama Mitra : <span class="text-danger">*</span>
                            </label>
                            <div class="col-md-9">
                                <input type="text" name="partner_name" class="form-control bg-light" value="{{ $prefillPartnerName }}" required>
                            </div>
                        </div>

                        <div class="row mb-3 align-items-center">
                            <label class="col-md-3 col-form-label fw-bold text-dark">
                                Kategori Mitra :
                            </label>
                            <div class="col-md-9">
                                <input type="text" name="category" class="form-control bg-light" value="{{ $prefillCategory }}">
                            </div>
                        </div>

                        <!-- 5. RUANG LINGKUP SPESIFIK -->
                        <div class="row mb-3 align-items-start">
                            <label class="col-md-3 col-form-label fw-bold text-dark pt-1">
                                Ruang Lingkup Spesifik : <span class="text-danger">*</span>
                            </label>
                            <div class="col-md-9">
                                <textarea name="scope" class="form-control" rows="3" placeholder="Rincian bentuk kegiatan pelaksanaan kerja sama..." required></textarea>
                            </div>
                        </div>

                        <!-- 6. NOMINAL / NILAI DANA -->
                        <div class="row mb-3 align-items-center">
                            <label class="col-md-3 col-form-label fw-bold text-dark">
                                Nilai Anggaran / Dana (Jika Ada) :
                            </label>
                            <div class="col-md-9">
                                <input type="text" name="nominal_value" class="form-control" placeholder="Contoh: Rp 50.000.000 / Non-finansial (In-kind)">
                            </div>
                        </div>

                        <!-- 7. UPLOAD DRAFT PDF -->
                        <div class="row mb-3 align-items-center">
                            <label class="col-md-3 col-form-label fw-bold text-dark">
                                Upload Draf Dokumen (PDF) : <span class="text-danger">*</span>
                            </label>
                            <div class="col-md-9">
                                <input type="file" name="draft_file" class="form-control" accept=".pdf" required>
                            </div>
                        </div>

                        <!-- 7b. PROPOSAL, RENCANA KEGIATAN & DOKUMEN PENDUKUNG (OPSIONAL) -->
                        <div class="row mb-3 align-items-center">
                            <label class="col-md-3 col-form-label fw-bold text-dark">
                                Proposal &amp; Pendukung :
                                <span class="badge bg-light text-secondary border fw-normal ms-1">Opsional</span>
                            </label>
                            <div class="col-md-9">
                                <div class="row g-2">
                                    <div class="col-sm-6">
                                        <input type="file" name="proposal_file" class="form-control form-control-sm" accept=".pdf,.doc,.docx">
                                        <div class="text-muted mt-0.5" style="font-size: 0.7rem;">Proposal / Rencana Kegiatan (PDF/Word)</div>
                                    </div>
                                    <div class="col-sm-6">
                                        <input type="file" name="supporting_files[]" class="form-control form-control-sm" multiple accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png">
                                        <div class="text-muted mt-0.5" style="font-size: 0.7rem;">Dokumen Pendukung (Multi-file)</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 8. TANGGAL & DURASI -->
                        <div class="row mb-3 align-items-center">
                            <label class="col-md-3 col-form-label fw-bold text-dark">
                                Periode Berlaku :
                            </label>
                            <div class="col-md-9">
                                <div class="row g-2">
                                    <div class="col-sm-6">
                                        <input type="date" name="start_date" class="form-control" value="{{ date('Y-m-d') }}">
                                    </div>
                                    <div class="col-sm-6">
                                        <input type="date" name="end_date" class="form-control" value="{{ date('Y-m-d', strtotime('+1 year')) }}">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- TOMBOL AKSI -->
                        <div class="row pt-2">
                            <div class="col-md-9 offset-md-3">
                                <button type="submit" class="btn btn-primary fw-bold px-4 py-2 shadow-sm">
                                    <i class="bi bi-save me-1"></i> Simpan Draf Dokumen Turunan (Terbitkan QR Code)
                                </button>
                                <a href="{{ route('simkerma.index', ['menu' => 4, 'view' => 'detail', 'doc' => $prefillParentId]) }}" class="btn btn-outline-secondary ms-2 px-3 py-2">Batal</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

        {{-- ===================================================================================== --}}
        {{-- 3. FORMULIR PENCATATAN KERJA SAMA LAMPAU (LEGACY / PRA-SISTEM)                        --}}
        {{-- ===================================================================================== --}}
        @elseif ($isCreatingLegacy)

            <div class="card card-wireframe mb-4 shadow-sm border">
                <div class="card-header bg-white py-3 border-bottom d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
                    <div>
                        <h5 class="card-title mb-0 fw-bold text-dark">
                            <i class="bi bi-clock-history text-dark me-2"></i> Pencatatan Dokumen Kerja Sama Lampau (Arsip Pra-Sistem)
                        </h5>
                        <div class="small text-muted">
                            Pencatatan naskah kerja sama fisik (MoU / MoA / IA) yang telah ditandatangani sebelum implementasi SIM-KERMA. Status ditentukan otomatis (<strong>ACTIVE</strong> jika masa berlaku masih aktif, atau <strong>EXPIRED</strong> jika telah berakhir). Dokumen dapat sewaktu-waktu diperpanjang / direaktivasi kembali ke status <strong>DRAFT</strong>.
                        </div>
                    </div>
                    <a href="{{ route('simkerma.index', ['menu' => 4]) }}" class="btn btn-outline-secondary btn-sm fw-semibold">
                        <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar Pengajuan
                    </a>
                </div>
                <div class="card-body p-4">

                    <form method="POST" action="{{ route('simkerma.store-legacy') }}" enctype="multipart/form-data" class="form-kerma-submission">
                        @csrf

                        <!-- 1. JENIS DOKUMEN -->
                        <div class="row mb-3 align-items-center">
                            <label class="col-md-3 col-form-label fw-bold text-dark">
                                Jenis Dokumen Kerja Sama : <span class="text-danger">*</span>
                            </label>
                            <div class="col-md-9">
                                <select name="doc_type" id="legacy_doc_type" class="form-select" required onchange="toggleLegacyParentField()">
                                    <option value="MoU">MoU (Nota Kesepahaman / Tingkat Institusi/Rektorat)</option>
                                    <option value="MoA / PKS">MoA / PKS (Perjanjian Kerja Sama / Tingkat Fakultas/UPPS)</option>
                                    <option value="IA">IA (Implementation Arrangement / Tingkat Program Studi/Unit)</option>
                                </select>
                            </div>
                        </div>

                        <!-- 2. JUDUL / TOPIK KERJA SAMA -->
                        <div class="row mb-3 align-items-center">
                            <label class="col-md-3 col-form-label fw-bold text-dark">
                                Judul / Topik Kerja Sama : <span class="text-danger">*</span>
                            </label>
                            <div class="col-md-9">
                                <input type="text" name="title" class="form-control" required>
                            </div>
                        </div>

                        <!-- 3. NOMOR NASKAH PIHAK I & PIHAK II -->
                        <div class="row mb-3 align-items-center">
                            <label class="col-md-3 col-form-label fw-bold text-dark">
                                Nomor Naskah Pihak I : <span class="text-danger">*</span>
                            </label>
                            <div class="col-md-4">
                                <input type="text" name="doc_number" class="form-control font-monospace" required>
                                <div class="text-muted" style="font-size: 0.72rem;">Nomor resmi yang tercantum pada naskah fisik institusi.</div>
                            </div>
                            <label class="col-md-2 col-form-label fw-bold text-dark text-md-end">
                                Nomor Naskah Pihak II :
                            </label>
                            <div class="col-md-3">
                                <input type="text" name="partner_doc_number" class="form-control font-monospace">
                                <div class="text-muted" style="font-size: 0.72rem;">Opsional jika ada penomoran dari pihak mitra.</div>
                            </div>
                        </div>

                        <!-- 4. DOKUMEN INDUK MOU (JIKA MOA/IA) -->
                        <div class="row mb-3 align-items-center" id="legacy_parent_row" style="display: none;">
                            <label class="col-md-3 col-form-label fw-bold text-dark">
                                Dokumen Induk MoU :
                            </label>
                            <div class="col-md-9">
                                <select name="parent_mou" class="form-select">
                                    <option value="-">-- Tidak Menginduk / Dokumen Mandiri (-) --</option>
                                    @foreach ($registeredMous as $rMou)
                                        <option value="{{ $rMou->doc_number ?: $rMou->doc_id }}">{{ $rMou->doc_number ?: $rMou->doc_id }} - {{ $rMou->partner_name }} ({{ $rMou->title }})</option>
                                    @endforeach
                                </select>
                                <div class="text-muted" style="font-size: 0.72rem;">Pilih dokumen payung MoU jika naskah ini merupakan MoA atau IA turunan.</div>
                            </div>
                        </div>

                        <!-- 5. UNIT PENGUSUL / PELAKSANA & KATEGORI MITRA -->
                        <div class="row mb-3 align-items-center">
                            <label class="col-md-3 col-form-label fw-bold text-dark">
                                Unit Pengusul / Pelaksana : <span class="text-danger">*</span>
                            </label>
                            <div class="col-md-4">
                                <select name="unit" class="form-select" required>
                                    <option>Biro Kerja Sama &amp; Urusan Internasional (BKUI)</option>
                                    <option>Program Studi Teknik Informatika</option>
                                    <option>Program Studi Sistem Informasi</option>
                                    <option>Program Studi Sistem Komputer</option>
                                    <option>Program Studi Bisnis Digital</option>
                                    <option>Program Studi Kewirausahaan</option>
                                    <option>Program Studi Hukum Bisnis</option>
                                    <option>Fakultas Ilmu Komputer</option>
                                    <option>Fakultas Ekonomi dan Bisnis</option>
                                    <option>LPPM</option>
                                    <option>Biro Administrasi Akademik &amp; Kemahasiswaan</option>
                                    <option>Rektorat</option>
                                </select>
                            </div>
                            <label class="col-md-2 col-form-label fw-bold text-dark text-md-end">
                                Kategori Mitra : <span class="text-danger">*</span>
                            </label>
                            <div class="col-md-3">
                                <select name="category" id="category_legacy" class="form-select" required>
                                    <option value="Perguruan Tinggi Dalam Negeri" selected>Perguruan Tinggi Dalam Negeri</option>
                                    <option value="Perguruan Tinggi Luar Negeri">Perguruan Tinggi Luar Negeri</option>
                                    <option value="DUDI / Swasta">DUDI / Swasta</option>
                                    <option value="Instansi Pemerintah">Instansi Pemerintah</option>
                                    <option value="BUMN / BUMD">BUMN / BUMD</option>
                                    <option value="Organisasi Non-Profit / NGO">Organisasi Non-Profit / NGO</option>
                                </select>
                            </div>
                        </div>

                        <!-- 6. NAMA MITRA -->
                        <div class="row mb-3 align-items-center">
                            <label class="col-md-3 col-form-label fw-bold text-dark">
                                Nama Lembaga Mitra : <span class="text-danger">*</span>
                            </label>
                            <div class="col-md-9">
                                <div class="hybrid-partner-combobox position-relative">
                                    <div class="input-group">
                                        <span class="input-group-text bg-white border-end-0">
                                            <i class="partner-input-icon bi bi-mortarboard-fill text-primary fs-5"></i>
                                        </span>
                                        <input type="text" name="partner_name" id="partner_name_legacy" class="form-control border-start-0 smart-partner-input" 
                                               placeholder="Ketik nama kampus (contoh: Sam Ratulangi, ITB, Parna Raya)..." 
                                               list="partnerDatalistLegacy"
                                               autocomplete="off" required>
                                        <button class="btn btn-outline-secondary btn-clear-partner d-none" type="button" title="Hapus / Reset Pilihan"><i class="bi bi-x-lg"></i></button>
                                        <button class="btn btn-primary btn-sm px-3 fw-bold btn-open-quick-add" type="button" data-bs-toggle="modal" data-bs-target="#quickAddPartnerModal" title="Tambah Mitra Baru ke Master Data (Menu 02)">
                                            <i class="bi bi-plus-lg me-1"></i> Tambah Mitra
                                        </button>
                                    </div>
                                    <datalist id="partnerDatalistLegacy">
                                        @foreach ($partners as $p)
                                            <option value="{{ $p->name }}">{{ $p->category }} &bull; {{ $p->city ?: 'Manado' }}</option>
                                        @endforeach
                                    </datalist>
                                    <!-- Floating Dropdown Suggestions -->
                                    <div class="partner-dropdown-menu shadow-lg bg-white border rounded mt-1 d-none position-absolute w-100" style="z-index: 1050; max-height: 280px; overflow-y: auto;"></div>
                                    <!-- Verified Info Banner -->
                                    <div class="partner-verified-info small mt-1.5 d-none"></div>
                                    <!-- Helper Tools -->
                                    <div class="d-flex flex-wrap justify-content-between align-items-center mt-1">
                                        <small class="partner-mode-desc text-muted"><i class="bi bi-database-check text-success me-1"></i>Direktori Resmi PDDikti Kemendikbudristek (4.650+ PT)</small>
                                        <a href="javascript:void(0)" class="small text-decoration-none text-primary btn-toggle-manual"><i class="bi bi-pencil-square me-1"></i>Ketik Manual / Luar Negeri</a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 7. PENANDATANGAN PIHAK I & PIHAK II -->
                        <div class="row mb-3 align-items-center">
                            <label class="col-md-3 col-form-label fw-bold text-dark">
                                Penandatangan UNIPAR : <span class="text-danger">*</span>
                            </label>
                            <div class="col-md-4">
                                <input type="text" name="first_party_signer" class="form-control" placeholder="Nama & gelar" required>
                            </div>
                            <label class="col-md-2 col-form-label fw-bold text-dark text-md-end">
                                Jabatan :
                            </label>
                            <div class="col-md-3">
                                <input type="text" name="first_party_position" class="form-control" placeholder="Contoh: Rektor">
                            </div>
                        </div>

                        <div class="row mb-3 align-items-center">
                            <label class="col-md-3 col-form-label fw-bold text-dark">
                                Penandatangan Mitra : <span class="text-danger">*</span>
                            </label>
                            <div class="col-md-4">
                                <input type="text" name="second_party_signer" class="form-control" placeholder="Nama & gelar" required>
                            </div>
                            <label class="col-md-2 col-form-label fw-bold text-dark text-md-end">
                                Jabatan :
                            </label>
                            <div class="col-md-3">
                                <input type="text" name="second_party_position" class="form-control" placeholder="Contoh: Direktur Utama">
                            </div>
                        </div>

                        <!-- 8. TANGGAL MULAI, MASA BERLAKU, & TANGGAL BERAKHIR OTOMATIS -->
                        <div class="row mb-3 align-items-center">
                            <label class="col-md-3 col-form-label fw-bold text-dark">
                                Periode Masa Berlaku : <span class="text-danger">*</span>
                            </label>
                            <div class="col-md-9">
                                <div class="row g-2 align-items-center">
                                    <div class="col-sm-4">
                                        <label class="text-muted small mb-1">Tanggal Mulai / Penandatanganan:</label>
                                        <input type="date" name="start_date" id="legacy_start_date" class="form-control" value="{{ date('Y-m-d', strtotime('-2 years')) }}" required onchange="calculateLegacyEndDate()">
                                    </div>
                                    <div class="col-sm-3">
                                        <label class="text-muted small mb-1">Masa Berlaku (Tahun):</label>
                                        <input type="number" name="validity_years" id="legacy_validity_years" class="form-control" value="3" min="1" max="25" required onchange="calculateLegacyEndDate()" onkeyup="calculateLegacyEndDate()">
                                    </div>
                                    <div class="col-sm-5">
                                        <label class="text-muted small mb-1">Tanggal Berakhir (Otomatis):</label>
                                        <input type="date" name="end_date" id="legacy_end_date" class="form-control bg-light" readonly>
                                    </div>
                                </div>
                                <div class="mt-2 d-flex align-items-center gap-2">
                                    <span id="legacy_status_badge" class="badge bg-success px-2.5 py-1.5 shadow-sm">
                                        <i class="bi bi-check-circle-fill me-1"></i> ACTIVE (Berlaku Sah)
                                    </span>
                                    <span id="legacy_status_text" class="text-success small fw-medium">
                                        Status awal otomatis dihitung dari tanggal berakhir naskah.
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- 9. RUANG LINGKUP TRIDHARMA -->
                        <div class="row mb-3 align-items-center">
                            <label class="col-md-3 col-form-label fw-bold text-dark">
                                Ruang Lingkup Kemitraan :
                            </label>
                            <div class="col-md-9">
                                <div class="d-flex flex-wrap gap-3 p-2 bg-light rounded border">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="scope[]" value="Pendidikan" id="leg_scope1" checked>
                                        <label class="form-check-label" for="leg_scope1">Pendidikan</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="scope[]" value="Penelitian" id="leg_scope2" checked>
                                        <label class="form-check-label" for="leg_scope2">Penelitian</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="scope[]" value="Pengabdian kepada Masyarakat" id="leg_scope3" checked>
                                        <label class="form-check-label" for="leg_scope3">Pengabdian kepada Masyarakat</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="scope[]" value="Pengembangan SDM" id="leg_scope4" checked>
                                        <label class="form-check-label" for="leg_scope4">Pengembangan SDM</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 10. UPLOAD SCAN NASKAH ASLI (PDF) -->
                        <div class="row mb-3 align-items-center">
                            <label class="col-md-3 col-form-label fw-bold text-dark">
                                Scan Naskah Asli (PDF) : <span class="text-danger">*</span>
                            </label>
                            <div class="col-md-9">
                                <input type="file" name="signed_file" class="form-control" accept=".pdf" required>
                                <div class="text-muted mt-1" style="font-size: 0.72rem;">
                                    Unggah berkas scan naskah asli bertandatangan fisik (format PDF) untuk diarsipkan dalam repositori digital institusi.
                                </div>
                            </div>
                        </div>

                        <!-- 10b. PROPOSAL, RENCANA KEGIATAN & DOKUMEN PENDUKUNG (OPSIONAL) -->
                        <div class="row mb-3 align-items-center">
                            <label class="col-md-3 col-form-label fw-bold text-dark">
                                Proposal / Rencana Kegiatan :
                                <span class="badge bg-light text-secondary border fw-normal ms-1">Opsional</span>
                            </label>
                            <div class="col-md-9">
                                <div class="row g-2">
                                    <div class="col-sm-6">
                                        <input type="file" name="proposal_file" class="form-control form-control-sm" accept=".pdf,.doc,.docx">
                                        <div class="text-muted mt-1" style="font-size: 0.72rem;">Berkas Proposal Kerja Sama (PDF/Word)</div>
                                    </div>
                                    <div class="col-sm-6">
                                        <input type="file" name="rencana_file" class="form-control form-control-sm" accept=".pdf,.doc,.docx,.xls,.xlsx">
                                        <div class="text-muted mt-1" style="font-size: 0.72rem;">Rencana Kegiatan / TOR (PDF/Word/Excel)</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3 align-items-center">
                            <label class="col-md-3 col-form-label fw-bold text-dark">
                                Dokumen Pendukung :
                                <span class="badge bg-light text-secondary border fw-normal ms-1">Opsional</span>
                            </label>
                            <div class="col-md-9">
                                <input type="file" name="supporting_files[]" class="form-control form-control-sm" multiple accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png">
                                <div class="text-muted mt-1" style="font-size: 0.72rem;">
                                    Notula rapat, surat tugas, legalitas mitra, dll. (Dapat memilih beberapa file sekaligus).
                                </div>
                            </div>
                        </div>

                        <!-- 11. RINGKASAN DESKRIPSI -->
                        <div class="row mb-3">
                            <label class="col-md-3 col-form-label fw-bold text-dark">
                                Ringkasan / Catatan Naskah :
                            </label>
                            <div class="col-md-9">
                                <textarea name="description" class="form-control" rows="3"></textarea>
                            </div>
                        </div>

                        <!-- TOMBOL AKSI -->
                        <div class="row pt-2">
                            <div class="col-md-9 offset-md-3">
                                <button type="submit" class="btn btn-dark fw-bold px-4 py-2 shadow-sm">
                                    <i class="bi bi-clock-history me-1"></i> Simpan Arsip Kerja Sama Lampau
                                </button>
                                <a href="{{ route('simkerma.index', ['menu' => 4]) }}" class="btn btn-outline-secondary ms-2 px-3 py-2">Batal</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <script>
            function toggleLegacyParentField() {
                const docType = document.getElementById('legacy_doc_type').value;
                const parentRow = document.getElementById('legacy_parent_row');
                if (docType === 'MoU') {
                    parentRow.style.display = 'none';
                } else {
                    parentRow.style.display = 'flex';
                }
            }

            function calculateLegacyEndDate() {
                const startVal = document.getElementById('legacy_start_date').value;
                const yearsInput = document.getElementById('legacy_validity_years');
                const years = parseInt(yearsInput.value, 10);
                const endEl = document.getElementById('legacy_end_date');
                const badgeEl = document.getElementById('legacy_status_badge');
                const textEl = document.getElementById('legacy_status_text');

                if (!startVal || isNaN(years) || years < 1) return;

                const parts = startVal.split('-');
                if (parts.length !== 3) return;

                const startYear = parseInt(parts[0], 10);
                const startMonth = parseInt(parts[1], 10);
                const startDay = parseInt(parts[2], 10);

                const endYear = startYear + years;
                const endStr = `${endYear}-${String(startMonth).padStart(2, '0')}-${String(startDay).padStart(2, '0')}`;
                endEl.value = endStr;

                // Cek status terhadap tanggal hari ini
                const today = new Date();
                const todayStr = today.getFullYear() + '-' + String(today.getMonth() + 1).padStart(2, '0') + '-' + String(today.getDate()).padStart(2, '0');

                if (endStr >= todayStr) {
                    badgeEl.className = 'badge bg-success px-2.5 py-1.5 shadow-sm';
                    badgeEl.innerHTML = '<i class="bi bi-check-circle-fill me-1"></i> ACTIVE (Berlaku Sah)';
                    textEl.innerHTML = 'Dokumen masih dalam periode masa berlaku sah.';
                    textEl.className = 'text-success small fw-medium';
                } else {
                    badgeEl.className = 'badge bg-danger px-2.5 py-1.5 shadow-sm';
                    badgeEl.innerHTML = '<i class="bi bi-clock-history me-1"></i> EXPIRED (Kedaluwarsa)';
                    textEl.innerHTML = 'Masa berlaku dokumen telah lampau. Dokumen dapat langsung diperpanjang / direaktivasi ke DRAFT.';
                    textEl.className = 'text-danger small fw-medium';
                }
            }

            document.addEventListener('DOMContentLoaded', function() {
                calculateLegacyEndDate();
            });
            </script>

        {{-- ===================================================================================== --}}
        {{-- 4. DETAIL DOKUMEN KERJA SAMA (PERSIS SESUAI MOCKUP UI/detail_MoU.png)                 --}}
        {{-- ===================================================================================== --}}
        @elseif ($isViewingDetail && $currentDoc)

            @php
                $docStatus = $currentDoc->status;
                // Skala tahap alur FSM sesuai mockup
                $stepRank = match($docStatus) {
                    'DRAFT' => 1,
                    'REVIEW' => 2,
                    'VERIFIED' => 3,
                    'ACTIVE', 'WARNING' => 4,
                    'EXPIRED' => 5,
                    'CANCELLED' => 0,
                    default => 1,
                };
                $availableEvents = $availEvents ?? ($currentDoc ? app(\App\Services\FSMWorkflowEngineService::class)->getAvailableEvents($currentDoc->status) : []);
            @endphp

            <div class="detail-doc-shell">

                <!-- BREADCRUMBS -->
                <nav aria-label="breadcrumb" class="mb-2">
                    <ol class="breadcrumb mb-0 small" style="font-size: 0.78rem;">
                        <li class="breadcrumb-item"><a href="{{ route('simkerma.index', ['menu' => 1]) }}" class="text-decoration-none text-muted"><i class="bi bi-house me-1"></i></a></li>
                        <li class="breadcrumb-item"><a href="{{ route('simkerma.index', ['menu' => 4]) }}" class="text-decoration-none text-muted">Pengajuan &amp; Persetujuan</a></li>
                        <li class="breadcrumb-item active text-dark fw-medium text-truncate" style="max-width: 480px;" aria-current="page" title="{{ $currentDoc->title ?: 'Detail Dokumen' }}">{{ $currentDoc->title ?: 'Detail Dokumen' }}</li>
                    </ol>
                </nav>

                <!-- HEADER BAR (TITLE & ACTION BUTTONS) -->
                <div class="d-flex justify-content-between align-items-center flex-wrap detail-page-bar">
                    <div class="d-flex align-items-center gap-2.5">
                        <a href="{{ route('simkerma.index', ['menu' => 4]) }}" class="btn btn-outline-secondary btn-sm detail-back-btn shadow-2xs" title="Kembali ke Daftar Pengajuan">
                            <i class="bi bi-arrow-left"></i>
                        </a>
                        <h5 class="mb-0 detail-page-heading">Detail Dokumen Kerja Sama</h5>
                    </div>

                    <div class="d-flex align-items-center flex-wrap gap-2">
                        @if ($currentDoc->status === 'ACTIVE' || $currentDoc->status === 'WARNING' || $currentDoc->status === 'EXPIRED')
                            <form method="POST" action="{{ route('simkerma.renew-draft') }}" class="d-inline" onsubmit="return confirm('Inisiasi pembaruan/reaktivasi dokumen {{ $currentDoc->doc_id }}?\n\nDokumen akan kembali ke status DRAFT untuk penyusunan dan penelaahan ketentuan naskah.');">
                                @csrf
                                <input type="hidden" name="doc_id" value="{{ $currentDoc->doc_id }}">
                                <button type="submit" class="btn btn-warning btn-sm fw-semibold px-3 py-1.5 shadow-2xs d-flex align-items-center gap-1.5 text-dark" style="font-size: 0.8rem;">
                                    <i class="bi bi-arrow-repeat"></i> Perpanjang / Reaktivasi ke Draf
                                </button>
                            </form>
                        @endif

                        @php
                            $canDeriveChild = !in_array($currentDoc->status, ['CANCELLED', 'EXPIRED']);
                            $isParentUnverified = in_array($currentDoc->status, ['DRAFT', 'REVIEW']);
                        @endphp
                        <!-- Action Buttons Dinamis Berdasarkan Tingkat Dokumen (MoU / MoA / IA) -->
                        @if ($currentDoc->doc_type === 'MoU')
                            <!-- MoU: Dapat Menurunkan MoA (Fakultas) & IA (Prodi) -->
                            @if ($canDeriveChild)
                                <div class="dropdown">
                                    <button class="btn btn-primary btn-sm fw-semibold px-3 py-1.5 shadow-sm dropdown-toggle d-flex align-items-center gap-1.5" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="font-size: 0.8rem; background-color: #1e40af; border-color: #1e40af;">
                                        <i class="bi bi-plus-lg"></i> Tambah Dokumen Turunan
                                        @if ($isParentUnverified)
                                            <span class="badge bg-warning text-dark ms-1" style="font-size: 0.65rem;">Induk {{ $currentDoc->status }}</span>
                                        @endif
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow border-0 small py-1" style="min-width: 230px;">
                                        <li>
                                            <a class="dropdown-item py-2 px-3 d-flex align-items-center gap-2" href="{{ route('simkerma.index', ['menu' => 4, 'view' => 'new_derive', 'parent_id' => $currentDoc->doc_id, 'derive_type' => 'MoA / PKS']) }}">
                                                <i class="bi bi-file-earmark-text text-primary fs-6"></i>
                                                <div>
                                                    <div class="fw-bold">MoA / PKS</div>
                                                    <div class="text-muted" style="font-size: 0.72rem;">Tingkat Fakultas / Unit Kerja</div>
                                                </div>
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item py-2 px-3 d-flex align-items-center gap-2" href="{{ route('simkerma.index', ['menu' => 4, 'view' => 'new_derive', 'parent_id' => $currentDoc->doc_id, 'derive_type' => 'IA']) }}">
                                                <i class="bi bi-file-earmark-check text-success fs-6"></i>
                                                <div>
                                                    <div class="fw-bold">IA (Implementation)</div>
                                                    <div class="text-muted" style="font-size: 0.72rem;">Tingkat Prodi / Lab / Kegiatan</div>
                                                </div>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            @else
                                <button type="button" class="btn btn-outline-secondary btn-sm fw-semibold px-3 py-1.5 d-flex align-items-center gap-1.5" disabled title="Dokumen induk berstatus {{ $currentDoc->status }}, tidak dapat membuat turunan baru">
                                    <i class="bi bi-lock"></i> Turunan Terkunci ({{ $currentDoc->status }})
                                </button>
                            @endif
                        @elseif ($currentDoc->doc_type === 'IA')
                            <!-- IA: Dokumen Ujung Tombak Operasional (Mencatat Realisasi IKU 6) -->
                            <button type="button" class="btn btn-success btn-sm fw-semibold px-3 py-1.5 shadow-sm d-flex align-items-center gap-1.5" style="font-size: 0.8rem; background-color: #059669; border-color: #059669;" data-bs-toggle="modal" data-bs-target="#createActivityModal_{{ $currentDoc->id }}">
                                <i class="bi bi-calendar-plus"></i> + Catat Pelaksanaan (IKU 6)
                            </button>
                        @else
                            <!-- MoA / PKS: Menurunkan IA (Implementation Arrangement) -->
                            @if ($canDeriveChild)
                                <a href="{{ route('simkerma.index', ['menu' => 4, 'view' => 'new_derive', 'parent_id' => $currentDoc->doc_id, 'derive_type' => 'IA']) }}" class="btn btn-success btn-sm fw-semibold px-3 py-1.5 shadow-sm d-flex align-items-center gap-1.5" style="font-size: 0.8rem; background-color: #0284c7; border-color: #0284c7;">
                                    <i class="bi bi-plus-lg"></i> + Tambah IA (Implementation)
                                </a>
                            @else
                                <button type="button" class="btn btn-outline-secondary btn-sm fw-semibold px-3 py-1.5 d-flex align-items-center gap-1.5" disabled title="Dokumen induk berstatus {{ $currentDoc->status }}, tidak dapat membuat IA baru">
                                    <i class="bi bi-lock"></i> IA Terkunci ({{ $currentDoc->status }})
                                </button>
                            @endif
                        @endif

                        <!-- Dropdown Opsi Lainnya (...) -->
                        <div class="dropdown">
                            <button class="btn btn-light border btn-sm px-2.5 py-1.5 shadow-2xs" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="border-color: #cbd5e1; height: 34px;" title="Opsi Lainnya">
                                <i class="bi bi-three-dots"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow border-0 small py-1">
                                <li><a class="dropdown-item py-1.5 px-3" href="{{ route('simkerma.verify', ['reg_id' => $currentDoc->reg_id]) }}" target="_blank"><i class="bi bi-box-arrow-up-right me-2 text-primary"></i> Buka Verifikasi Publik</a></li>
                                @if ($currentDoc->draft_file && file_exists(public_path($currentDoc->draft_file)))
                                    <li><a class="dropdown-item py-1.5 px-3" href="{{ asset($currentDoc->draft_file) }}" target="_blank"><i class="bi bi-file-earmark-pdf me-2 text-danger"></i> Buka Naskah Draf PDF</a></li>
                                @endif
                                @if ($currentDoc->signed_file && file_exists(public_path($currentDoc->signed_file)))
                                    <li><a class="dropdown-item py-1.5 px-3" href="{{ asset($currentDoc->signed_file) }}" target="_blank"><i class="bi bi-file-earmark-check me-2 text-success"></i> Buka Naskah Asli PDF</a></li>
                                @endif
                                @if ($currentDoc->status === 'ACTIVE' || $currentDoc->status === 'WARNING' || $currentDoc->status === 'EXPIRED')
                                    <li>
                                        <form method="POST" action="{{ route('simkerma.renew-draft') }}" class="px-3 py-1" onsubmit="return confirm('Inisiasi pembaruan/reaktivasi dokumen {{ $currentDoc->doc_id }}?\n\nDokumen akan kembali ke status DRAFT.');">
                                            @csrf
                                            <input type="hidden" name="doc_id" value="{{ $currentDoc->doc_id }}">
                                            <button type="submit" class="btn btn-link p-0 text-warning-emphasis text-decoration-none small text-start w-100 fw-semibold">
                                                <i class="bi bi-arrow-repeat me-2"></i> Perpanjang / Reaktivasi ke Draf
                                            </button>
                                        </form>
                                    </li>
                                @endif
                                <li><hr class="dropdown-divider my-1"></li>
                                <li><a class="dropdown-item py-1.5 px-3" href="#" data-bs-toggle="modal" data-bs-target="#fsmModal_{{ $currentDoc->id }}"><i class="bi bi-sliders me-2 text-primary"></i> Kelola Transisi FSM</a></li>
                                @if ($currentDoc->status === 'DRAFT' || $activeRole === 'MASTER' || $activeRole === 'ADMIN')
                                    <li><hr class="dropdown-divider my-1"></li>
                                    <li>
                                        <form method="POST" action="{{ route('simkerma.delete-draft') }}" class="px-3 py-1" onsubmit="return confirm('KONFIRMASI PENGHAPUSAN DOKUMEN (Peran: {{ $activeRole }}):\n\nApakah Anda yakin ingin menghapus dokumen {{ $currentDoc->doc_id }} [Status: {{ $currentDoc->status }}] ({{ addslashes($currentDoc->partner_name) }}) secara permanen?\n\nPERHATIAN: Dokumen yang dihapus tidak dapat dipulihkan kembali.');">
                                            @csrf
                                            <input type="hidden" name="doc_id" value="{{ $currentDoc->doc_id }}">
                                            <button type="submit" class="btn btn-link p-0 text-danger text-decoration-none small text-start w-100 fw-semibold">
                                                <i class="bi bi-trash3 me-2"></i> {{ $currentDoc->status === 'DRAFT' ? 'Hapus Draf Ini' : 'Hapus Dokumen (Akses ' . $activeRole . ')' }}
                                            </button>
                                        </form>
                                    </li>
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- SUMMARY BANNER CARD -->
                <div class="card detail-card detail-summary-banner">
                    <div class="card-body">
                        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                            <!-- Left Group: Icon + Title info -->
                            <div class="d-flex align-items-start align-items-md-center gap-3 flex-grow-1" style="min-width: 0;">
                                <div class="detail-doc-icon" style="background-color: {{ $currentDoc->doc_type === 'MoU' ? '#eff6ff' : ($currentDoc->doc_type === 'IA' ? '#ecfdf5' : '#f0f9ff') }}; border: 1px solid {{ $currentDoc->doc_type === 'MoU' ? '#dbeafe' : ($currentDoc->doc_type === 'IA' ? '#a7f3d0' : '#bae6fd') }};">
                                    <i class="bi {{ $currentDoc->doc_type === 'MoU' ? 'bi-file-earmark-text text-primary' : ($currentDoc->doc_type === 'IA' ? 'bi-file-earmark-check text-success' : 'bi-file-earmark-richtext text-info') }} fs-3"></i>
                                </div>
                                <div class="flex-grow-1" style="min-width: 0;">
                                    <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                                        <span class="badge rounded-pill text-white fw-bold px-2.5 py-0.5" style="background-color: {{ $currentDoc->doc_type === 'MoU' ? '#1e3a8a' : ($currentDoc->doc_type === 'IA' ? '#059669' : '#0284c7') }}; font-size: 0.72rem;">{{ $currentDoc->doc_type }}</span>
                                        <span class="text-muted fw-medium" style="font-size: 0.8rem;">{{ $currentDoc->doc_type === 'MoU' ? 'Memorandum of Understanding' : ($currentDoc->doc_type === 'IA' ? 'Implementation Arrangement' : 'Memorandum of Agreement') }}</span>
                                        @if ($currentDoc->is_legacy)
                                            <span class="badge rounded-pill bg-dark text-warning border border-warning px-2 py-0.5" style="font-size: 0.7rem;">
                                                <i class="bi bi-clock-history me-1"></i> Data Lampau
                                            </span>
                                        @endif
                                    </div>
                                    <h4 class="detail-doc-title" title="{{ $currentDoc->title ?: ($currentDoc->scope ?: ($currentDoc->doc_number ?: $currentDoc->doc_id)) }}">
                                        {{ $currentDoc->title ?: ($currentDoc->scope ?: ($currentDoc->doc_number ?: $currentDoc->doc_id)) }}
                                    </h4>
                                    <div class="detail-doc-parties d-flex align-items-center flex-wrap gap-1.5">
                                        <span class="fw-semibold text-primary">Universitas Parna Raya (UNIPAR)</span>
                                        <span class="text-muted">&times;</span>
                                        <span class="text-dark fw-semibold">{{ $currentDoc->partner_name }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Right Group: Status Badge + Reg Code + Audit Trail -->
                            <div class="d-flex flex-row flex-md-column align-items-center align-items-md-end justify-content-between gap-2 flex-shrink-0 pt-2 pt-md-0 border-top border-md-0" style="border-color: #f1f5f9 !important;">
                                <div>
                                    @if ($currentDoc->status === 'ACTIVE')
                                        <span class="badge rounded-pill bg-success-subtle text-success border border-success-subtle px-3 py-1.5 fw-bold" style="font-size: 0.82rem;">
                                            <i class="bi bi-check-lg me-1"></i> ACTIVE
                                        </span>
                                    @elseif ($currentDoc->status === 'DRAFT')
                                        <span class="badge rounded-pill bg-secondary-subtle text-secondary border border-secondary-subtle px-3 py-1.5 fw-bold" style="font-size: 0.82rem;">
                                            <i class="bi bi-pencil-square me-1"></i> DRAFT
                                        </span>
                                    @elseif ($currentDoc->status === 'REVIEW')
                                        <span class="badge rounded-pill bg-info-subtle text-info-emphasis border border-info-subtle px-3 py-1.5 fw-bold" style="font-size: 0.82rem;">
                                            <i class="bi bi-clock-history me-1"></i> REVIEW
                                        </span>
                                    @elseif ($currentDoc->status === 'VERIFIED')
                                        <span class="badge rounded-pill bg-primary-subtle text-primary border border-primary-subtle px-3 py-1.5 fw-bold" style="font-size: 0.82rem;">
                                            <i class="bi bi-shield-check me-1"></i> VERIFIED
                                        </span>
                                    @elseif ($currentDoc->status === 'WARNING')
                                        <span class="badge rounded-pill bg-warning-subtle text-warning-emphasis border border-warning-subtle px-3 py-1.5 fw-bold" style="font-size: 0.82rem;">
                                            <i class="bi bi-exclamation-triangle me-1"></i> WARNING
                                        </span>
                                    @elseif ($currentDoc->status === 'EXPIRED')
                                        <span class="badge rounded-pill bg-danger-subtle text-danger border border-danger-subtle px-3 py-1.5 fw-bold" style="font-size: 0.82rem;">
                                            <i class="bi bi-clock me-1"></i> EXPIRED
                                        </span>
                                    @elseif ($currentDoc->status === 'CANCELLED')
                                        <span class="badge rounded-pill bg-secondary-subtle text-secondary border border-secondary-subtle px-3 py-1.5 fw-bold" style="font-size: 0.82rem;">
                                            <i class="bi bi-x-circle me-1"></i> CANCELLED
                                        </span>
                                    @else
                                        <span class="badge rounded-pill bg-secondary-subtle text-secondary border border-secondary-subtle px-3 py-1.5 fw-bold" style="font-size: 0.82rem;">
                                            {{ $currentDoc->status }}
                                        </span>
                                    @endif
                                </div>
                                <div class="text-end">
                                    <div class="text-dark fw-semibold font-monospace" style="font-size: 0.78rem;">{{ $currentDoc->reg_id }}</div>
                                    <div class="text-muted mt-0.5" style="font-size: 0.74rem;">
                                        <i class="bi bi-calendar-event me-1"></i> Jejak Audit: {{ $currentDoc->auditTrails->count() }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- PARENT DOCUMENT RELATION BANNER (JIKA MERUPAKAN DOKUMEN TURUNAN MoA / IA) -->
                @if ($currentDoc->doc_type !== 'MoU' && $parentDoc)
                    <div class="alert alert-primary py-2.5 px-3 rounded-3 mb-3 d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-2 shadow-2xs" style="font-size: 0.84rem; background-color: #f0fdf4; border-color: #bbf7d0; color: #166534;">
                        <div class="d-flex align-items-center gap-2.5">
                            <i class="bi bi-diagram-2-fill fs-5 text-success"></i>
                            <div>
                                <div class="fw-bold">Dokumen Induk (Rujukan Payung Hukum):</div>
                                <div>
                                    Naskah ini merupakan turunan operasional dari 
                                    <strong>[{{ $parentDoc->doc_type }}] {{ $parentDoc->title ?: $parentDoc->partner_name }}</strong> 
                                    (Nomor: <span class="font-monospace fw-semibold">{{ $parentDoc->doc_number ?: $parentDoc->doc_id }}</span>).
                                </div>
                            </div>
                        </div>
                        <a href="{{ route('simkerma.index', ['menu' => 4, 'view' => 'detail', 'doc' => $parentDoc->doc_id]) }}" class="btn btn-sm btn-outline-success fw-bold px-3 py-1.5 text-nowrap">
                            <i class="bi bi-arrow-left-circle me-1"></i> Buka Naskah Induk
                        </a>
                    </div>
                @elseif ($currentDoc->doc_type !== 'MoU' && !empty($currentDoc->parent_mou) && $currentDoc->parent_mou !== '-')
                    <div class="alert alert-warning py-2.5 px-3 rounded-3 mb-3 d-flex align-items-center justify-content-between gap-2 shadow-2xs" style="font-size: 0.84rem;">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-exclamation-circle text-warning-emphasis fs-5"></i>
                            <div>
                                <strong>Dokumen Induk (Rujukan):</strong> <span class="font-monospace fw-bold">{{ $currentDoc->parent_mou }}</span>
                                <span class="text-muted ms-1">(belum tertaut ke naskah induk di repositori)</span>
                            </div>
                        </div>
                    </div>
                @endif

                @php
                    $detailMainFileCount = (!empty($currentDoc->draft_file) ? 1 : 0) + (!empty($currentDoc->signed_file) ? 1 : 0) + (!empty($currentDoc->docx_path) ? 1 : 0);
                    $detailTabExtraReports = $currentDoc->activities->filter(fn($a) => !empty($a->evidence_file) && !$currentDoc->attachments->contains('file_path', $a->evidence_file));
                    $detailAllFilesCount = $detailMainFileCount + $currentDoc->attachments->count() + $detailTabExtraReports->count();
                @endphp

                <!-- NAV TABS (UNDERLINE TAB BAR MODERN & RESPONSIF) -->
                <ul class="nav nav-tabs detail-tabs" id="detailDocTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active fw-bold" id="tab-info-btn" data-tab-key="info" data-bs-toggle="tab" data-bs-target="#tab-info-pane" type="button" role="tab">
                            <i class="bi bi-info-circle"></i> Informasi &amp; Alur
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="tab-docs-btn" data-tab-key="docs" data-bs-toggle="tab" data-bs-target="#tab-docs-pane" type="button" role="tab">
                            <i class="bi bi-file-earmark-text"></i> Dokumen &amp; Lampiran <span class="badge bg-light text-dark border py-0 px-1.5">{{ $detailAllFilesCount }}</span>
                        </button>
                    </li>
                    @if ($currentDoc->doc_type !== 'IA')
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="tab-deriv-btn" data-tab-key="deriv" data-bs-toggle="tab" data-bs-target="#tab-deriv-pane" type="button" role="tab">
                                <i class="bi bi-diagram-3"></i> Dokumen Turunan <span class="badge bg-light text-dark border py-0 px-1.5">{{ $childDocs->count() }}</span>
                            </button>
                        </li>
                    @endif
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="tab-act-btn" data-tab-key="act" data-bs-toggle="tab" data-bs-target="#tab-act-pane" type="button" role="tab">
                            <i class="bi bi-list-task"></i> Pelaksanaan <span class="badge bg-light text-dark border py-0 px-1.5">{{ $currentDoc->activities->count() }}</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="tab-audit-btn" data-tab-key="audit" data-bs-toggle="tab" data-bs-target="#tab-audit-pane" type="button" role="tab">
                            <i class="bi bi-clock-history"></i> Riwayat &amp; Audit <span class="badge bg-light text-dark border py-0 px-1.5">{{ $currentDoc->auditTrails->count() }}</span>
                        </button>
                    </li>
                </ul>

                <!-- TAB PANES -->
                <div class="tab-content" id="detailDocTabContent">

                    <!-- TAB 1: INFORMASI & ALUR -->
                    <div class="tab-pane fade show active" id="tab-info-pane" role="tabpanel">
                        <div class="row g-3">

                            <!-- KOLOM KIRI -->
                            <div class="col-lg-7 col-xl-8">

                                <!-- KARTU 1: ALUR PROSES STATUS KERJA SAMA -->
                                @php
                                    // Lookup Jejak Audit untuk masing-masing fase alur
                                    $auditDraft = $currentDoc->auditTrails->firstWhere('event', 'CREATE_DRAFT') 
                                        ?? $currentDoc->auditTrails->sortBy('id')->first();
                                    $auditSubmit = $currentDoc->auditTrails->firstWhere('event', 'SUBMIT');
                                    $auditVerify = $currentDoc->auditTrails->firstWhere('event', 'VERIFY');
                                    $auditActive = $currentDoc->auditTrails->firstWhere('event', 'ACTIVATE');
                                    $auditWarning = $currentDoc->auditTrails->firstWhere('event', 'TRIGGER_WARNING');
                                    $auditExpire = $currentDoc->auditTrails->firstWhere('event', 'EXPIRE');

                                    // Cek apakah dokumen merupakan pengajuan lampau (legacy)
                                    $isLegacyDoc = (bool)(
                                        $currentDoc->is_legacy 
                                        || ($currentDoc->track && str_contains($currentDoc->track, 'Lampau'))
                                        || ($currentDoc->start_date && \Carbon\Carbon::parse($currentDoc->start_date)->year < \Carbon\Carbon::parse($currentDoc->created_at)->year)
                                    );

                                    $legacyBaseDate = $currentDoc->start_date 
                                        ? \Carbon\Carbon::parse($currentDoc->start_date)->format('d M Y')
                                        : ($currentDoc->date_signed 
                                            ? \Carbon\Carbon::parse($currentDoc->date_signed)->format('d M Y')
                                            : ($currentDoc->date_approved 
                                                ? \Carbon\Carbon::parse($currentDoc->date_approved)->format('d M Y') 
                                                : \Carbon\Carbon::parse($currentDoc->created_at)->format('d M Y')));

                                    // Tanggal masing-masing node alur proses (mengutamakan fakta audit, tanpa tanggal sintetis)
                                    if ($isLegacyDoc) {
                                        $dateNode1 = $legacyBaseDate;
                                        
                                        $isNode2Done = in_array($docStatus, ['REVIEW', 'VERIFIED', 'ACTIVE', 'WARNING', 'EXPIRED']) || $auditSubmit !== null;
                                        $dateNode2 = $isNode2Done ? $legacyBaseDate : '-';

                                        $isNode3Done = in_array($docStatus, ['VERIFIED', 'ACTIVE', 'WARNING', 'EXPIRED']) || $auditVerify !== null;
                                        $isNode3Active = ($docStatus === 'REVIEW');
                                        $dateNode3 = $isNode3Done ? $legacyBaseDate : ($isNode3Active ? 'Dalam Telaah' : '-');

                                        $isNode4Done = in_array($docStatus, ['ACTIVE', 'WARNING', 'EXPIRED']) || $auditActive !== null;
                                        $isNode4Active = ($docStatus === 'VERIFIED');
                                        $dateNode4 = $isNode4Done ? $legacyBaseDate : ($isNode4Active ? 'Siap Diteken' : '-');

                                        $isNode5Done = in_array($docStatus, ['ACTIVE', 'WARNING', 'EXPIRED']);
                                        $dateNode5 = $isNode5Done ? $legacyBaseDate : '-';
                                    } else {
                                        $dateNode1 = $auditDraft ? \Carbon\Carbon::parse($auditDraft->created_at)->format('d M Y') : \Carbon\Carbon::parse($currentDoc->created_at)->format('d M Y');
                                        
                                        $isNode2Done = in_array($docStatus, ['REVIEW', 'VERIFIED', 'ACTIVE', 'WARNING', 'EXPIRED']) || $auditSubmit !== null;
                                        $dateNode2 = $auditSubmit 
                                            ? \Carbon\Carbon::parse($auditSubmit->created_at)->format('d M Y') 
                                            : ($isNode2Done ? \Carbon\Carbon::parse($currentDoc->created_at)->format('d M Y') : '-');

                                        $isNode3Done = in_array($docStatus, ['VERIFIED', 'ACTIVE', 'WARNING', 'EXPIRED']) || $auditVerify !== null;
                                        $isNode3Active = ($docStatus === 'REVIEW');
                                        $dateNode3 = $auditVerify 
                                            ? \Carbon\Carbon::parse($auditVerify->created_at)->format('d M Y') 
                                            : ($isNode3Done ? ($currentDoc->date_approved ? \Carbon\Carbon::parse($currentDoc->date_approved)->format('d M Y') : 'Terverifikasi') : ($isNode3Active ? 'Dalam Telaah' : '-'));

                                        $isNode4Done = in_array($docStatus, ['ACTIVE', 'WARNING', 'EXPIRED']) || $auditActive !== null;
                                        $isNode4Active = ($docStatus === 'VERIFIED');
                                        $dateNode4 = $auditActive 
                                            ? \Carbon\Carbon::parse($auditActive->created_at)->format('d M Y') 
                                            : ($currentDoc->date_approved 
                                                ? \Carbon\Carbon::parse($currentDoc->date_approved)->format('d M Y') 
                                                : ($isNode4Done ? \Carbon\Carbon::parse($currentDoc->created_at)->format('d M Y') : ($isNode4Active ? 'Siap Diteken' : '-')));

                                        $isNode5Done = in_array($docStatus, ['ACTIVE', 'WARNING', 'EXPIRED']);
                                        $dateNode5 = $isNode5Done 
                                            ? ($currentDoc->start_date ? \Carbon\Carbon::parse($currentDoc->start_date)->format('d M Y') : ($currentDoc->date_signed ? \Carbon\Carbon::parse($currentDoc->date_signed)->format('d M Y') : '-'))
                                            : '-';
                                    }

                                    $isNode5Active = ($docStatus === 'ACTIVE');

                                    $isNode6Active = ($docStatus === 'WARNING');
                                    $isNode6Done = in_array($docStatus, ['WARNING', 'EXPIRED']);
                                    $dateNode6 = $isNode6Done 
                                        ? ($auditWarning ? \Carbon\Carbon::parse($auditWarning->created_at)->format('d M Y') : ($currentDoc->end_date ? \Carbon\Carbon::parse($currentDoc->end_date)->subDays(90)->format('d M Y') : 'H-90'))
                                        : '-';

                                    $isNode7Active = ($docStatus === 'EXPIRED');
                                    $dateNode7 = $isNode7Active 
                                        ? ($auditExpire ? \Carbon\Carbon::parse($auditExpire->created_at)->format('d M Y') : ($currentDoc->end_date ? \Carbon\Carbon::parse($currentDoc->end_date)->format('d M Y') : '-'))
                                        : '-';

                                    // Lebar garis progres alur horizontal (7 tahapan)
                                    $progressWidth = match($docStatus) {
                                        'DRAFT' => '0%',
                                        'REVIEW' => '33%',
                                        'VERIFIED' => '50%',
                                        'ACTIVE' => '67%',
                                        'WARNING' => '84%',
                                        'EXPIRED' => '100%',
                                        'CANCELLED' => '0%',
                                        default => '0%'
                                    };
                                    $progressColor = match($docStatus) {
                                        'EXPIRED' => '#ef4444',
                                        'WARNING' => '#f59e0b',
                                        default => '#22c55e'
                                    };

                                    // Konfigurasi Banner Keterangan Status Aktual
                                    $alertConfig = match($docStatus) {
                                        'DRAFT' => [
                                            'bg' => '#eff6ff',
                                            'border' => '#bfdbfe',
                                            'color' => '#1e40af',
                                            'icon' => 'bi-info-circle-fill text-primary',
                                            'text' => 'Dokumen berstatus DRAFT (Tahap 1: Inisiasi Pengusulan). Klausul naskah masih dapat disunting sebelum diajukan (SUBMIT) ke meja pemeriksaan.',
                                        ],
                                        'REVIEW' => [
                                            'bg' => '#eff6ff',
                                            'border' => '#bfdbfe',
                                            'color' => '#1e40af',
                                            'icon' => 'bi-hourglass-split text-primary',
                                            'text' => 'Dokumen berstatus REVIEW (Tahap 2: Telaah Mutu & Hukum). Naskah sedang diperiksa dan diverifikasi oleh LPPM / Bagian Kerja Sama.',
                                        ],
                                        'VERIFIED' => [
                                            'bg' => '#f0fdf4',
                                            'border' => '#bbf7d0',
                                            'color' => '#166534',
                                            'icon' => 'bi-patch-check-fill text-success',
                                            'text' => 'Dokumen berstatus VERIFIED (Tahap 3: Lolos Telaah Mutu). Naskah dibekukan (Freeze Text), siap dicetak dan ditandatangani oleh Rektor/Pimpinan.',
                                        ],
                                        'ACTIVE' => [
                                            'bg' => '#f0fdf4',
                                            'border' => '#bbf7d0',
                                            'color' => '#166534',
                                            'icon' => 'bi-check-circle-fill text-success',
                                            'text' => 'Dokumen telah disahkan dan sedang berlaku (Active)' . ($currentDoc->end_date ? ' sampai ' . \Carbon\Carbon::parse($currentDoc->end_date)->translatedFormat('d F Y') : '') . '.',
                                        ],
                                        'WARNING' => [
                                            'bg' => '#fffbeb',
                                            'border' => '#fde68a',
                                            'color' => '#92400e',
                                            'icon' => 'bi-exclamation-triangle-fill text-warning',
                                            'text' => 'Peringatan Dini Masa Berlaku (Warning): Dokumen mendekati akhir masa aktif (< 90 hari' . ($currentDoc->end_date ? ', berakhir ' . \Carbon\Carbon::parse($currentDoc->end_date)->translatedFormat('d F Y') : '') . '). Segera inisiasi adendum perpanjangan.',
                                        ],
                                        'EXPIRED' => [
                                            'bg' => '#fef2f2',
                                            'border' => '#fecaca',
                                            'color' => '#991b1b',
                                            'icon' => 'bi-x-circle-fill text-danger',
                                            'text' => 'Masa berlaku dokumen telah berakhir (Expired)' . ($currentDoc->end_date ? ' pada ' . \Carbon\Carbon::parse($currentDoc->end_date)->translatedFormat('d F Y') : '') . '. Naskah dapat direaktivasi dengan inisiasi draf baru.',
                                        ],
                                        'CANCELLED' => [
                                            'bg' => '#fef2f2',
                                            'border' => '#fecaca',
                                            'color' => '#991b1b',
                                            'icon' => 'bi-slash-circle-fill text-danger',
                                            'text' => 'Pengusulan dokumen kerja sama ini telah dibatalkan secara permanen (Cancelled).',
                                        ],
                                        default => [
                                            'bg' => '#f8fafc',
                                            'border' => '#e2e8f0',
                                            'color' => '#475569',
                                            'icon' => 'bi-info-circle text-secondary',
                                            'text' => 'Status alur dokumen saat ini: ' . $docStatus,
                                        ]
                                    };
                                @endphp

                                <div class="card detail-card">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <div class="detail-card-title">
                                            <i class="bi bi-layers text-primary"></i> Alur Proses
                                        </div>
                                        <button type="button" class="btn btn-outline-secondary btn-sm py-1 px-2.5 fw-semibold d-flex align-items-center gap-1" style="font-size: 0.76rem; border-color: #cbd5e1;" data-bs-toggle="modal" data-bs-target="#fsmModal_{{ $currentDoc->id }}">
                                            <i class="bi bi-diagram-3"></i> Lihat Detail Status
                                        </button>
                                    </div>
                                    <div class="card-body">
                                        <!-- HORIZONTAL STEPPER 7 TAHAPAN SIMETRIS -->
                                        <div class="detail-stepper-scroll">
                                            <div class="detail-stepper-track">
                                                <!-- Connecting line background & active line -->
                                                <div class="detail-stepper-line">
                                                    <div style="width: {{ $progressWidth }}; height: 100%; background-color: {{ $progressColor }}; transition: width 0.3s ease;"></div>
                                                </div>

                                                <div class="detail-stepper-grid">
                                                <!-- Node 1: Draft -->
                                                <div class="detail-step-node">
                                                    @if ($docStatus === 'CANCELLED')
                                                        <div class="detail-step-circle text-white shadow-2xs" style="background-color: #ef4444;">
                                                            <i class="bi bi-x-lg"></i>
                                                        </div>
                                                        <div class="detail-step-label fw-semibold text-danger">Draft</div>
                                                    @else
                                                        <div class="detail-step-circle text-white shadow-2xs" style="background-color: #22c55e; {{ $docStatus === 'DRAFT' ? 'border: 2px solid #bbf7d0; box-shadow: 0 0 0 3px #dcfce7;' : '' }}">
                                                            <i class="bi bi-check"></i>
                                                        </div>
                                                        <div class="detail-step-label fw-semibold text-dark">Draft</div>
                                                    @endif
                                                    <div class="detail-step-date">{{ $dateNode1 }}</div>
                                                </div>

                                                <!-- Node 2: Verifikasi Unit -->
                                                <div class="detail-step-node">
                                                    @if ($isNode2Done)
                                                        <div class="detail-step-circle text-white shadow-2xs" style="background-color: #22c55e;">
                                                            <i class="bi bi-check"></i>
                                                        </div>
                                                        <div class="detail-step-label fw-semibold text-dark">Verifikasi Unit</div>
                                                        <div class="detail-step-date">{{ $dateNode2 }}</div>
                                                    @else
                                                        <div class="detail-step-circle bg-white border shadow-2xs" style="border-color: #cbd5e1 !important; color: #94a3b8 !important;">
                                                            <i class="bi bi-circle"></i>
                                                        </div>
                                                        <div class="detail-step-label text-muted">Verifikasi Unit</div>
                                                        <div class="detail-step-date">-</div>
                                                    @endif
                                                </div>

                                                <!-- Node 3: Review LPPM -->
                                                <div class="detail-step-node">
                                                    @if ($isNode3Done)
                                                        <div class="detail-step-circle text-white shadow-2xs" style="background-color: #22c55e;">
                                                            <i class="bi bi-check"></i>
                                                        </div>
                                                        <div class="detail-step-label fw-semibold text-dark">Review LPPM</div>
                                                        <div class="detail-step-date">{{ $dateNode3 }}</div>
                                                    @elseif ($isNode3Active)
                                                        <div class="detail-step-circle text-white shadow-2xs" style="background-color: #3b82f6; border: 2px solid #bfdbfe; box-shadow: 0 0 0 3px #dbeafe;">
                                                            <i class="bi bi-hourglass-split"></i>
                                                        </div>
                                                        <div class="detail-step-label fw-bold text-primary">Review LPPM</div>
                                                        <div class="detail-step-date text-primary fw-medium">Sedang Telaah</div>
                                                    @else
                                                        <div class="detail-step-circle bg-white border shadow-2xs" style="border-color: #cbd5e1 !important; color: #94a3b8 !important;">
                                                            <i class="bi bi-circle"></i>
                                                        </div>
                                                        <div class="detail-step-label text-muted">Review LPPM</div>
                                                        <div class="detail-step-date">-</div>
                                                    @endif
                                                </div>

                                                <!-- Node 4: Persetujuan Pimpinan -->
                                                <div class="detail-step-node">
                                                    @if ($isNode4Done)
                                                        <div class="detail-step-circle text-white shadow-2xs" style="background-color: #22c55e;">
                                                            <i class="bi bi-check"></i>
                                                        </div>
                                                        <div class="detail-step-label fw-semibold text-dark">Persetujuan</div>
                                                        <div class="detail-step-date">{{ $dateNode4 }}</div>
                                                    @elseif ($isNode4Active)
                                                        <div class="detail-step-circle text-white shadow-2xs" style="background-color: #3b82f6; border: 2px solid #bfdbfe; box-shadow: 0 0 0 3px #dbeafe;">
                                                            <i class="bi bi-pen-fill"></i>
                                                        </div>
                                                        <div class="detail-step-label fw-bold text-primary">Persetujuan</div>
                                                        <div class="detail-step-date text-primary fw-medium">Siap Diteken</div>
                                                    @else
                                                        <div class="detail-step-circle bg-white border shadow-2xs" style="border-color: #cbd5e1 !important; color: #94a3b8 !important;">
                                                            <i class="bi bi-circle"></i>
                                                        </div>
                                                        <div class="detail-step-label text-muted">Persetujuan</div>
                                                        <div class="detail-step-date">-</div>
                                                    @endif
                                                </div>

                                                <!-- Node 5: Active -->
                                                <div class="detail-step-node">
                                                    @if ($isNode5Active)
                                                        <div class="detail-step-circle text-white shadow-2xs" style="background-color: #22c55e; border: 2px solid #bbf7d0; box-shadow: 0 0 0 3px #dcfce7;">
                                                            <i class="bi bi-check"></i>
                                                        </div>
                                                        <div class="detail-step-label fw-bold text-success">Active</div>
                                                        @if ($dateNode5 && $dateNode5 !== '-')
                                                            <div class="detail-step-date text-success fw-medium">{{ $dateNode5 }}</div>
                                                        @endif
                                                    @elseif ($isNode5Done)
                                                        <div class="detail-step-circle text-white shadow-2xs" style="background-color: #22c55e;">
                                                            <i class="bi bi-check"></i>
                                                        </div>
                                                        <div class="detail-step-label fw-semibold text-dark">Active</div>
                                                        @if ($dateNode5 && $dateNode5 !== '-')
                                                            <div class="detail-step-date">{{ $dateNode5 }}</div>
                                                        @endif
                                                    @else
                                                        <div class="detail-step-circle bg-white border shadow-2xs" style="border-color: #cbd5e1 !important; color: #94a3b8 !important;">
                                                            <i class="bi bi-circle"></i>
                                                        </div>
                                                        <div class="detail-step-label text-muted">Active</div>
                                                        <div class="detail-step-date">-</div>
                                                    @endif
                                                </div>

                                                <!-- Node 6: Peringatan -->
                                                <div class="detail-step-node">
                                                    @if ($isNode6Active)
                                                        <div class="detail-step-circle text-white shadow-2xs" style="background-color: #d97706; border: 2px solid #fde68a; box-shadow: 0 0 0 3px #fef3c7;">
                                                            <i class="bi bi-bell-fill"></i>
                                                        </div>
                                                        <div class="detail-step-label fw-bold detail-step-warning-text">Peringatan</div>
                                                        @if ($dateNode6 && $dateNode6 !== '-')
                                                            <div class="detail-step-date detail-step-warning-text fw-medium">{{ $dateNode6 }}</div>
                                                        @endif
                                                    @elseif ($isNode6Done)
                                                        <div class="detail-step-circle text-white shadow-2xs" style="background-color: #d97706;">
                                                            <i class="bi bi-bell"></i>
                                                        </div>
                                                        <div class="detail-step-label fw-semibold text-dark">Peringatan</div>
                                                        @if ($dateNode6 && $dateNode6 !== '-')
                                                            <div class="detail-step-date">{{ $dateNode6 }}</div>
                                                        @endif
                                                    @else
                                                        <div class="detail-step-circle bg-white border shadow-2xs" style="border-color: #cbd5e1 !important; color: #94a3b8 !important;">
                                                            <i class="bi bi-bell"></i>
                                                        </div>
                                                        <div class="detail-step-label text-muted">Peringatan</div>
                                                        <div class="detail-step-date">-</div>
                                                    @endif
                                                </div>

                                                <!-- Node 7: Selesai -->
                                                <div class="detail-step-node">
                                                    @if ($isNode7Active)
                                                        <div class="detail-step-circle text-white shadow-2xs" style="background-color: #ef4444; border: 2px solid #fecaca; box-shadow: 0 0 0 3px #fee2e2;">
                                                            <i class="bi bi-clock-history"></i>
                                                        </div>
                                                        <div class="detail-step-label fw-bold text-danger">Selesai</div>
                                                        @if ($dateNode7 && $dateNode7 !== '-')
                                                            <div class="detail-step-date text-danger fw-medium">{{ $dateNode7 }}</div>
                                                        @endif
                                                    @else
                                                        <div class="detail-step-circle bg-white border shadow-2xs" style="border-color: #cbd5e1 !important; color: #94a3b8 !important;">
                                                            <i class="bi bi-circle"></i>
                                                        </div>
                                                        <div class="detail-step-label text-muted">Selesai</div>
                                                        <div class="detail-step-date">-</div>
                                                    @endif
                                                </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Single Line Alert Strip Dinamis Sesuai Status FSM Aktual -->
                                        <div class="alert d-flex align-items-center py-2 px-3 mb-0 rounded-2" style="font-size: 0.83rem; line-height: 1.45; background-color: {{ $alertConfig['bg'] }}; border-color: {{ $alertConfig['border'] }}; color: {{ $alertConfig['color'] }};">
                                            <i class="bi {{ $alertConfig['icon'] }} me-2 fs-5 flex-shrink-0"></i>
                                            <span>{{ $alertConfig['text'] }}</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- KARTU 2: INFORMASI UMUM -->
                                @php
                                    $isFrozenUi = in_array($currentDoc->status, ['VERIFIED', 'ACTIVE']) && !in_array($activeRole, ['MASTER', 'ADMIN']);
                                    $canEditDocUi = !in_array($currentDoc->status, ['CANCELLED', 'EXPIRED']) || in_array($activeRole, ['MASTER', 'ADMIN']);
                                @endphp
                                <div class="card detail-card">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <div class="detail-card-title">
                                            <i class="bi bi-info-circle text-primary"></i> Informasi Umum
                                            @if ($isFrozenUi)
                                                <span class="badge badge-soft-warning text-warning-emphasis border border-warning-subtle" style="font-size: 0.68rem;" title="Identitas legal dibekukan pada tahap {{ $currentDoc->status }}">
                                                    <i class="bi bi-lock-fill me-0.5"></i>Freeze Text
                                                </span>
                                            @endif
                                        </div>
                                        @if ($canEditDocUi)
                                            <button type="button" class="btn btn-outline-secondary btn-sm py-1 px-2.5 fw-semibold d-flex align-items-center gap-1" style="font-size: 0.76rem; border-color: #cbd5e1;" data-bs-toggle="modal" data-bs-target="#editInfoModal_{{ $currentDoc->id }}">
                                                <i class="bi bi-pencil"></i> Edit
                                            </button>
                                        @endif
                                    </div>
                                    <div class="card-body">
                                        <div class="detail-kv-list">
                                            <div class="detail-kv-row">
                                                <div class="detail-kv-label">Jenis Dokumen</div>
                                                <div class="detail-kv-value">
                                                    <span class="badge rounded-pill fw-bold px-2.5 py-0.5" style="background-color: {{ $currentDoc->doc_type === 'MoU' ? '#1e3a8a' : ($currentDoc->doc_type === 'IA' ? '#059669' : '#0284c7') }}; font-size: 0.72rem; color: #fff;">{{ $currentDoc->doc_type }}</span>
                                                    <span class="text-secondary ms-1" style="font-size: 0.8rem;">({{ $currentDoc->doc_type === 'MoU' ? 'Memorandum of Understanding / Nota Kesepahaman' : ($currentDoc->doc_type === 'IA' ? 'Implementation Arrangement / Perjanjian Pelaksanaan' : 'Memorandum of Agreement / Perjanjian Kerja Sama') }})</span>
                                                </div>
                                            </div>

                                            @if ($currentDoc->doc_type !== 'MoU')
                                                <div class="detail-kv-row">
                                                    <div class="detail-kv-label">Dokumen Induk</div>
                                                    <div class="detail-kv-value">
                                                        @if ($parentDoc)
                                                            <a href="{{ route('simkerma.index', ['menu' => 4, 'view' => 'detail', 'doc' => $parentDoc->doc_id]) }}" class="text-primary text-decoration-none fw-semibold">
                                                                <i class="bi bi-link-45deg me-0.5"></i>[{{ $parentDoc->doc_type }}] {{ $parentDoc->title ?: $parentDoc->partner_name }} <span class="font-monospace text-muted fw-normal">({{ $parentDoc->doc_number ?: $parentDoc->doc_id }})</span>
                                                            </a>
                                                        @elseif (!empty($currentDoc->parent_mou) && $currentDoc->parent_mou !== '-')
                                                            <span class="font-monospace text-dark">{{ $currentDoc->parent_mou }}</span>
                                                        @else
                                                            <span class="text-muted fst-italic">-</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endif

                                            <div class="detail-kv-row">
                                                <div class="detail-kv-label">Nomor Naskah UNIPAR</div>
                                                <div class="detail-kv-value font-monospace">{{ (!empty($currentDoc->doc_number) && $currentDoc->doc_number !== '-') ? $currentDoc->doc_number : '-' }}</div>
                                            </div>

                                            <div class="detail-kv-row">
                                                <div class="detail-kv-label">Nomor Naskah Mitra</div>
                                                <div class="detail-kv-value font-monospace">{{ (!empty($currentDoc->partner_doc_number) && $currentDoc->partner_doc_number !== '-') ? $currentDoc->partner_doc_number : '-' }}</div>
                                            </div>

                                            <div class="detail-kv-row">
                                                <div class="detail-kv-label">Mitra Kerja Sama</div>
                                                <div class="detail-kv-value">
                                                    <strong>{{ $currentDoc->partner_name }}</strong>
                                                    @if (!empty($currentDoc->partner_category))
                                                        <span class="badge bg-light text-secondary border ms-1" style="font-size: 0.72rem;">{{ $currentDoc->partner_category }}</span>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="detail-kv-row">
                                                <div class="detail-kv-label">Unit Pengusul</div>
                                                <div class="detail-kv-value">{{ $currentDoc->unit }}</div>
                                            </div>

                                            <div class="detail-kv-row">
                                                <div class="detail-kv-label">Penandatangan UNIPAR</div>
                                                <div class="detail-kv-value">
                                                    {{ $currentDoc->first_party_signer ?: '-' }}
                                                    @if (!empty($currentDoc->first_party_position))
                                                        <span class="text-muted">({{ $currentDoc->first_party_position }})</span>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="detail-kv-row">
                                                <div class="detail-kv-label">Penandatangan Mitra</div>
                                                <div class="detail-kv-value">
                                                    {{ $currentDoc->second_party_signer ?: '-' }}
                                                    @if (!empty($currentDoc->second_party_position))
                                                        <span class="text-muted">({{ $currentDoc->second_party_position }})</span>
                                                    @endif
                                                </div>
                                            </div>

                                            @if (!empty($currentDoc->pic_first_party) || !empty($currentDoc->pic_second_party))
                                                <div class="detail-kv-row">
                                                    <div class="detail-kv-label">PIC / Narahubung</div>
                                                    <div class="detail-kv-value">
                                                        <span>UNIPAR: {{ $currentDoc->pic_first_party ?: '-' }}</span>
                                                        <span class="text-muted mx-1.5">&bull;</span>
                                                        <span>Mitra: {{ $currentDoc->pic_second_party ?: '-' }}</span>
                                                    </div>
                                                </div>
                                            @endif

                                            <div class="detail-kv-row">
                                                <div class="detail-kv-label">Ruang Lingkup</div>
                                                <div class="detail-kv-value">{{ $currentDoc->scope ?: $currentDoc->title }}</div>
                                            </div>

                                            <div class="detail-kv-row">
                                                <div class="detail-kv-label">Masa Berlaku</div>
                                                <div class="detail-kv-value">
                                                    @if (!empty($currentDoc->start_date) && !empty($currentDoc->end_date))
                                                        {{ \Carbon\Carbon::parse($currentDoc->start_date)->translatedFormat('d F Y') }} &ndash; {{ \Carbon\Carbon::parse($currentDoc->end_date)->translatedFormat('d F Y') }}
                                                        @if (!empty($currentDoc->validity))
                                                            <span class="text-secondary fw-normal">({{ $currentDoc->validity }})</span>
                                                        @endif
                                                    @else
                                                        <span class="text-muted fst-italic">{{ $currentDoc->validity ? $currentDoc->validity . ' (Tanggal Belum Ditetapkan)' : 'Belum Ditetapkan (Fase Draf)' }}</span>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="detail-kv-row">
                                                <div class="detail-kv-label">Peringatan</div>
                                                <div class="detail-kv-value">
                                                    <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-2.5 py-1 fw-semibold" style="font-size: 0.74rem;">
                                                        <i class="bi bi-bell-fill me-1"></i> H-90 Hari (3 Bulan sebelum berakhir)
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- KARTU 3: DOKUMEN TURUNAN -->
                                <div class="card detail-card mb-0">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <div class="detail-card-title">
                                            <i class="bi bi-layers text-primary"></i> Dokumen Turunan
                                            <span class="badge rounded-pill fw-semibold px-2.5 py-0.5" style="background-color: #e0e7ff; color: #4338ca; font-size: 0.72rem;">
                                                {{ $childDocs->count() }} Dokumen
                                            </span>
                                        </div>
                                    </div>
                                    <div class="card-body p-0">
                                        @if ($currentDoc->doc_type === 'IA')
                                            <div class="p-4 text-center text-muted">
                                                <i class="bi bi-shield-check text-success fs-4 d-block mb-1"></i>
                                                <div class="fw-semibold text-dark" style="font-size: 0.88rem;">Naskah IA merupakan Dokumen Tingkat Operasional Terakhir</div>
                                                <div style="font-size: 0.8rem;">Tidak menerbitkan turunan hukum lagi. Seluruh realisasi kegiatan dicatat langsung pada <strong>Tab Pelaksanaan (IKU 6)</strong>.</div>
                                            </div>
                                        @else
                                            <div class="table-responsive border-0 shadow-none mb-0">
                                                <table class="table detail-data-table table-hover align-middle mb-0">
                                                    <thead>
                                                        <tr>
                                                            <th class="text-center" style="width: 48px;">No.</th>
                                                            <th>Judul dan Nomor Dokumen</th>
                                                            <th class="text-center" style="width: 105px;">Jenis</th>
                                                            <th>Unit Pelaksana</th>
                                                            <th class="text-center" style="width: 120px;">Status</th>
                                                            <th class="text-center" style="width: 90px;">Aksi</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @forelse ($childDocs as $cIdx => $cDoc)
                                                            <tr>
                                                                <td class="text-center text-muted">{{ $loop->iteration }}</td>
                                                                <td>
                                                                    <div class="fw-bold text-dark">{{ $cDoc->title ?: 'Dokumen Turunan' }}</div>
                                                                    <small class="text-muted font-monospace" style="font-size: 0.74rem;">{{ $cDoc->doc_number ?: $cDoc->doc_id }}</small>
                                                                </td>
                                                                <td class="text-center">
                                                                    @if (stripos($cDoc->doc_type, 'MoA') !== false)
                                                                        <span class="badge rounded-pill fw-semibold px-2 py-0.5" style="background-color: #e0f2fe; color: #0284c7; font-size: 0.72rem;">MoA / PKS</span>
                                                                    @else
                                                                        <span class="badge rounded-pill fw-semibold px-2 py-0.5" style="background-color: #dcfce7; color: #16a34a; font-size: 0.72rem;">IA</span>
                                                                    @endif
                                                                </td>
                                                                <td class="text-secondary">{{ $cDoc->unit }}</td>
                                                                <td class="text-center">
                                                                    <span class="badge {{ $stepperList[$cDoc->status][1] ?? 'bg-secondary text-white' }} rounded-pill px-2.5 py-0.5" style="font-size: 0.72rem;">
                                                                        {{ $cDoc->status }}
                                                                    </span>
                                                                </td>
                                                                <td class="text-center text-nowrap">
                                                                    <a href="{{ route('simkerma.index', ['menu' => 4, 'view' => 'detail', 'doc' => $cDoc->doc_id]) }}" class="btn btn-light border btn-sm py-0.5 px-2 shadow-2xs" title="Lihat Detail">
                                                                        <i class="bi bi-eye"></i>
                                                                    </a>
                                                                    <div class="dropdown d-inline-block">
                                                                        <button class="btn btn-light border btn-sm py-0.5 px-2 shadow-2xs dropdown-toggle dropdown-toggle-no-caret" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                                            <i class="bi bi-three-dots"></i>
                                                                        </button>
                                                                        <ul class="dropdown-menu dropdown-menu-end shadow border-0 small py-1">
                                                                            <li><a class="dropdown-item py-1.5 px-3" href="{{ route('simkerma.index', ['menu' => 4, 'view' => 'detail', 'doc' => $cDoc->doc_id]) }}"><i class="bi bi-eye me-1.5"></i> Detail Dokumen</a></li>
                                                                            <li><a class="dropdown-item py-1.5 px-3" href="{{ route('simkerma.verify', ['reg_id' => $cDoc->reg_id]) }}" target="_blank"><i class="bi bi-qr-code-scan me-1.5"></i> Verifikasi QR</a></li>
                                                                        </ul>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        @empty
                                                            <tr>
                                                                <td colspan="6" class="text-center text-muted py-3">Belum ada dokumen turunan terdaftar.</td>
                                                            </tr>
                                                        @endforelse
                                                    </tbody>
                                                </table>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                            </div>

                            <!-- KOLOM KANAN -->
                            <div class="col-lg-5 col-xl-4">

                                <!-- KARTU 1: DOKUMEN & LAMPIRAN (BERKAS FISIK RIIL & TERVERIFIKASI) -->
                                @php
                                    $draftExists = false;
                                    $draftSizeText = '-';
                                    $draftPath = null;
                                    if (!empty($currentDoc->draft_file)) {
                                        $p1 = public_path($currentDoc->draft_file);
                                        $p2 = public_path('uploads/documents/' . $currentDoc->draft_file);
                                        if (file_exists($p1) && is_file($p1)) {
                                            $draftExists = true;
                                            $draftPath = asset($currentDoc->draft_file);
                                            $b = filesize($p1);
                                            $draftSizeText = $b >= 1048576 ? round($b / 1048576, 1) . ' MB' : round($b / 1024, 0) . ' KB';
                                        } elseif (file_exists($p2) && is_file($p2)) {
                                            $draftExists = true;
                                            $draftPath = asset('uploads/documents/' . $currentDoc->draft_file);
                                            $b = filesize($p2);
                                            $draftSizeText = $b >= 1048576 ? round($b / 1048576, 1) . ' MB' : round($b / 1024, 0) . ' KB';
                                        }
                                    }

                                    $signedExists = false;
                                    $signedSizeText = '-';
                                    $signedPath = null;
                                    if (!empty($currentDoc->signed_file)) {
                                        $sp1 = public_path($currentDoc->signed_file);
                                        $sp2 = public_path('uploads/documents/' . $currentDoc->signed_file);
                                        if (file_exists($sp1) && is_file($sp1)) {
                                            $signedExists = true;
                                            $signedPath = asset($currentDoc->signed_file);
                                            $sb = filesize($sp1);
                                            $signedSizeText = $sb >= 1048576 ? round($sb / 1048576, 1) . ' MB' : round($sb / 1024, 0) . ' KB';
                                        } elseif (file_exists($sp2) && is_file($sp2)) {
                                            $signedExists = true;
                                            $signedPath = asset('uploads/documents/' . $currentDoc->signed_file);
                                            $sb = filesize($sp2);
                                            $signedSizeText = $sb >= 1048576 ? round($sb / 1048576, 1) . ' MB' : round($sb / 1024, 0) . ' KB';
                                        }
                                    }

                                    $docxExists = false;
                                    $docxSizeText = '-';
                                    $docxPath = null;
                                    if (!empty($currentDoc->docx_path)) {
                                        $dp1 = public_path($currentDoc->docx_path);
                                        $dp2 = public_path('uploads/documents/' . $currentDoc->docx_path);
                                        if (file_exists($dp1) && is_file($dp1)) {
                                            $docxExists = true;
                                            $docxPath = asset($currentDoc->docx_path);
                                            $db = filesize($dp1);
                                            $docxSizeText = $db >= 1048576 ? round($db / 1048576, 1) . ' MB' : round($db / 1024, 0) . ' KB';
                                        } elseif (file_exists($dp2) && is_file($dp2)) {
                                            $docxExists = true;
                                            $docxPath = asset('uploads/documents/' . $currentDoc->docx_path);
                                            $db = filesize($dp2);
                                            $docxSizeText = $db >= 1048576 ? round($db / 1048576, 1) . ' MB' : round($db / 1024, 0) . ' KB';
                                        }
                                    }
                                @endphp

                                <div class="detail-card mb-3">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <div class="detail-card-title">
                                            <i class="bi bi-paperclip text-primary"></i> Dokumen &amp; Lampiran
                                        </div>
                                        @if ($currentDoc->status !== 'CANCELLED')
                                            <button type="button" class="btn btn-outline-primary btn-sm py-1 px-2 fw-bold" style="font-size: 0.78rem;" data-bs-toggle="modal" data-bs-target="#uploadDocModal_{{ $currentDoc->id }}" title="Unggah Berkas Baru">
                                                <i class="bi bi-cloud-arrow-up me-1"></i>Unggah
                                            </button>
                                        @endif
                                    </div>
                                    <div class="card-body">
                                        <!-- 1. Draf Naskah PDF -->
                                        <div class="detail-file-item">
                                            <div class="d-flex align-items-center gap-2 text-truncate me-2">
                                                <div class="detail-file-icon" style="background-color: {{ $draftExists ? '#fee2e2' : '#f1f5f9' }}; color: {{ $draftExists ? '#dc2626' : '#94a3b8' }};">
                                                    <i class="bi bi-file-earmark-pdf-fill"></i>
                                                </div>
                                                <div class="text-truncate">
                                                    <div class="detail-file-name text-truncate" title="{{ $draftExists ? basename($currentDoc->draft_file) : 'Draf PDF' }}">
                                                        {{ $draftExists ? basename($currentDoc->draft_file) : 'Draf ' . $currentDoc->doc_type . ' (PDF)' }}
                                                    </div>
                                                    <div class="detail-file-meta">
                                                        @if ($draftExists)
                                                            {{ $draftSizeText }} &bull; Diunggah {{ \Carbon\Carbon::parse($currentDoc->created_at)->format('d M Y') }}
                                                        @else
                                                            <span class="badge bg-secondary-subtle text-secondary border py-0 px-1">Belum ada berkas fisik</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-center gap-1 flex-shrink-0">
                                                @if ($draftExists)
                                                    <a href="{{ $draftPath }}" target="_blank" class="btn btn-light border btn-sm py-1 px-2 shadow-2xs" title="Lihat Draf Naskah">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    <a href="{{ $draftPath }}" download class="btn btn-light border btn-sm py-1 px-2 shadow-2xs" title="Unduh Draf Naskah">
                                                        <i class="bi bi-download"></i>
                                                    </a>
                                                @else
                                                    <button type="button" class="btn btn-light border btn-sm py-1 px-2 disabled text-muted opacity-50" title="Berkas draf belum diunggah">
                                                        <i class="bi bi-dash"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </div>

                                        <!-- 2. Naskah Final Bertandatangan -->
                                        <div class="detail-file-item">
                                            <div class="d-flex align-items-center gap-2 text-truncate me-2">
                                                <div class="detail-file-icon" style="background-color: {{ $signedExists ? '#dcfce7' : '#f1f5f9' }}; color: {{ $signedExists ? '#16a34a' : '#94a3b8' }};">
                                                    <i class="bi bi-patch-check-fill"></i>
                                                </div>
                                                <div class="text-truncate">
                                                    <div class="detail-file-name text-truncate" title="{{ $signedExists ? basename($currentDoc->signed_file) : 'Naskah Sah PDF' }}">
                                                        {{ $signedExists ? basename($currentDoc->signed_file) : $currentDoc->doc_type . ' Sah (Bertandatangan)' }}
                                                    </div>
                                                    <div class="detail-file-meta">
                                                        @if ($signedExists)
                                                            <span class="text-success fw-semibold"><i class="bi bi-check2-circle me-1"></i>Sah &amp; Berlaku</span> &bull; {{ $signedSizeText }}
                                                        @else
                                                            <span class="badge bg-warning-subtle text-warning-emphasis border py-0 px-1"><i class="bi bi-clock me-1"></i>Belum disahkan (Tahap {{ $currentDoc->status }})</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-center gap-1 flex-shrink-0">
                                                @if ($signedExists)
                                                    <a href="{{ $signedPath }}" target="_blank" class="btn btn-light border btn-sm py-1 px-2 shadow-2xs" title="Lihat Naskah Sah">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    <a href="{{ $signedPath }}" download class="btn btn-light border btn-sm py-1 px-2 shadow-2xs" title="Unduh Naskah Sah">
                                                        <i class="bi bi-download"></i>
                                                    </a>
                                                @else
                                                    <button type="button" class="btn btn-light border btn-sm py-1 px-2 disabled text-muted opacity-50" title="Naskah sah belum diunggah">
                                                        <i class="bi bi-dash"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </div>

                                        <!-- 3. Berkas Naskah DOCX / Template Baku -->
                                        <div class="detail-file-item">
                                            <div class="d-flex align-items-center gap-2 text-truncate me-2">
                                                <div class="detail-file-icon" style="background-color: #e0f2fe; color: #0284c7;">
                                                    <i class="bi bi-file-earmark-word-fill"></i>
                                                </div>
                                                <div class="text-truncate">
                                                    @if ($docxExists)
                                                        <div class="detail-file-name text-truncate" title="{{ basename($currentDoc->docx_path) }}">{{ basename($currentDoc->docx_path) }}</div>
                                                        <div class="detail-file-meta">{{ $docxSizeText }} &bull; Format Pengeditan Word</div>
                                                    @else
                                                        <div class="detail-file-name text-truncate">Template Baku Naskah UNIPAR.docx</div>
                                                        <div class="detail-file-meta">Template Acuan Resmi LPPM / Kerja Sama</div>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-center gap-1 flex-shrink-0">
                                                @php $wordFile = $docxExists ? $docxPath : asset('templates/Template_MoU_UNIPAR.docx'); @endphp
                                                <a href="{{ $wordFile }}" target="_blank" class="btn btn-light border btn-sm py-1 px-2 shadow-2xs" title="Buka">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <a href="{{ $wordFile }}" download class="btn btn-light border btn-sm py-1 px-2 shadow-2xs" title="Unduh Berkas Word">
                                                    <i class="bi bi-download"></i>
                                                </a>
                                            </div>
                                        </div>

                                        <!-- 4. Ringkasan Multi-Lampiran (Proposal, TOR, Pendukung, Laporan) -->
                                        @php
                                            $sideExtraCount = $currentDoc->attachments->count() + $detailTabExtraReports->count();
                                        @endphp
                                        <div class="detail-file-item mb-0 bg-white">
                                            <div class="text-truncate me-2" style="font-size: 0.8rem;">
                                                <i class="bi bi-folder2-open text-primary me-1"></i>
                                                <span class="fw-semibold text-dark">Proposal, TOR, Pendukung &amp; Laporan:</span>
                                                <span class="badge {{ $sideExtraCount > 0 ? 'badge-soft-primary border border-primary-subtle' : 'badge-soft-secondary border' }} ms-1" style="font-size: 0.72rem;">{{ $sideExtraCount }} Berkas</span>
                                            </div>
                                            <button type="button" class="btn btn-link p-0 text-primary fw-semibold text-decoration-none text-nowrap" style="font-size: 0.78rem;" onclick="document.getElementById('tab-docs-btn')?.click();">
                                                Lihat Semua &rarr;
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- KARTU 2: AKTIVITAS TERAKHIR -->
                                <div class="detail-card mb-0">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <div class="detail-card-title">
                                            <i class="bi bi-clock-history text-primary"></i> Aktivitas Terakhir
                                        </div>
                                        @if ($currentDoc->auditTrails->count() > 5)
                                            <button type="button" class="btn btn-link p-0 text-primary text-decoration-none fw-semibold" style="font-size: 0.76rem;" onclick="document.getElementById('tab-audit-btn')?.click();">
                                                Semua ({{ $currentDoc->auditTrails->count() }}) &rarr;
                                            </button>
                                        @endif
                                    </div>
                                    <div class="card-body">
                                        @php
                                            $recentTrails = $currentDoc->auditTrails->sortByDesc('id')->take(5);
                                        @endphp

                                        @if ($recentTrails->isNotEmpty())
                                            <div class="detail-timeline">
                                                @foreach ($recentTrails as $rIdx => $rTrail)
                                                    @php
                                                        $dotColor = match($rTrail->event) {
                                                            'ACTIVATE', 'VERIFY' => '#22c55e',
                                                            'SUBMIT' => '#3b82f6',
                                                            'RETURN_TO_DRAFT', 'TRIGGER_WARNING', 'DELETE_ATTACHMENT' => '#d97706',
                                                            'CANCEL', 'EXPIRE' => '#ef4444',
                                                            default => '#3b82f6'
                                                        };
                                                        $dotIcon = match($rTrail->event) {
                                                            'ACTIVATE' => 'bi-check',
                                                            'VERIFY' => 'bi-shield-check',
                                                            'SUBMIT' => 'bi-send',
                                                            'RETURN_TO_DRAFT' => 'bi-arrow-return-left',
                                                            'UPLOAD_FILE' => 'bi-cloud-upload',
                                                            'RECORD_ACTIVITY' => 'bi-calendar-check',
                                                            'UPDATE_INFO' => 'bi-pencil',
                                                            default => 'bi-circle'
                                                        };
                                                    @endphp
                                                    <div class="detail-timeline-item">
                                                        <div class="detail-timeline-dot" style="background-color: {{ $dotColor }};">
                                                            <i class="bi {{ $dotIcon }}"></i>
                                                        </div>
                                                        <div class="detail-timeline-note" title="{{ $rTrail->notes ?: $rTrail->event }}">{{ $rTrail->notes ?: $rTrail->event }}</div>
                                                        <div class="detail-timeline-meta">oleh {{ $rTrail->actor }} ({{ $rTrail->role }}) &bull; {{ $rTrail->timestamp_wita }}</div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <div class="text-center py-3 text-muted small">
                                                <i class="bi bi-clock-history d-block fs-5 mb-1"></i>
                                                Belum ada rekaman aktivitas audit pada dokumen ini.
                                            </div>
                                        @endif
                                    </div>
                                </div>

                            </div>

                        </div>
                    </div>

                    <!-- TAB 2: DOKUMEN & LAMPIRAN DETAIL -->
                    <div class="tab-pane fade" id="tab-docs-pane" role="tabpanel">
                        <div class="detail-card mb-3">
                            <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                                <div>
                                    <div class="detail-card-title"><i class="bi bi-files text-primary"></i> Manajemen Berkas &amp; Pratinjau Naskah {{ $currentDoc->doc_type }}</div>
                                    <div class="text-muted mt-1" style="font-size: 0.78rem;">Unggah atau periksa draf pengajuan serta naskah asli bertandatangan basah/elektronik</div>
                                </div>
                                <button type="button" class="btn btn-primary btn-sm fw-semibold py-1.5 px-3" style="font-size: 0.8rem;" data-bs-toggle="modal" data-bs-target="#uploadDocModal_{{ $currentDoc->id }}">
                                    <i class="bi bi-upload me-1"></i> Unggah Berkas Baru
                                </button>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <!-- Kolom 1: Draf Naskah Pengusul -->
                                    <div class="col-lg-6">
                                        @php
                                            $tabDraftPdfOk = $currentDoc->draft_file && file_exists(public_path($currentDoc->draft_file));
                                            $tabDraftDocxOk = $currentDoc->docx_path && file_exists(public_path($currentDoc->docx_path));
                                        @endphp
                                        <div class="p-3 border rounded-3 bg-light h-100 d-flex flex-column justify-content-between">
                                            <div>
                                                <div class="d-flex align-items-center justify-content-between mb-2">
                                                    <h6 class="fw-bold text-dark mb-0" style="font-size: 0.88rem;"><i class="bi bi-file-earmark-pdf text-danger me-1"></i>Naskah Draf Pengusul</h6>
                                                    @if ($tabDraftPdfOk || $tabDraftDocxOk)
                                                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1" style="font-size: 0.74rem;">Tersedia</span>
                                                    @else
                                                        <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2 py-1" style="font-size: 0.74rem;">Belum Diunggah</span>
                                                    @endif
                                                </div>
                                                @if ($tabDraftPdfOk)
                                                    <div class="detail-pdf-frame mb-2">
                                                        <iframe src="{{ asset($currentDoc->draft_file) }}" title="Preview Draf {{ $currentDoc->doc_type }}"></iframe>
                                                    </div>
                                                @elseif ($tabDraftDocxOk)
                                                    <div class="text-muted py-5 text-center small border rounded-3 bg-white mb-2">
                                                        <i class="bi bi-file-earmark-word-fill text-primary fs-2 d-block mb-1"></i>
                                                        <strong class="text-dark d-block">{{ basename($currentDoc->docx_path) }}</strong>
                                                        <small class="text-muted">Berkas Word (.docx) tersedia untuk diunduh.</small>
                                                    </div>
                                                @else
                                                    <div class="text-muted py-5 text-center small border rounded-3 bg-white mb-2">
                                                        <i class="bi bi-file-earmark-x text-secondary fs-3 d-block mb-1"></i>
                                                        Berkas naskah draf belum diunggah.
                                                    </div>
                                                @endif
                                            </div>
                                            @if ($tabDraftPdfOk || $tabDraftDocxOk)
                                                <div class="d-flex flex-wrap gap-2 pt-2 border-top">
                                                    @if ($tabDraftPdfOk)
                                                        <a href="{{ asset($currentDoc->draft_file) }}" target="_blank" class="btn btn-outline-secondary btn-sm py-1 px-2.5" style="font-size: 0.78rem;"><i class="bi bi-box-arrow-up-right me-1"></i>Buka Tab Baru</a>
                                                        <a href="{{ asset($currentDoc->draft_file) }}" download class="btn btn-secondary btn-sm py-1 px-2.5" style="font-size: 0.78rem;"><i class="bi bi-download me-1"></i>Unduh PDF</a>
                                                    @endif
                                                    @if ($tabDraftDocxOk)
                                                        <a href="{{ asset($currentDoc->docx_path) }}" download class="btn btn-outline-primary btn-sm py-1 px-2.5" style="font-size: 0.78rem;"><i class="bi bi-file-earmark-word me-1"></i>Unduh DOCX</a>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Kolom 2: Naskah Final Bertandatangan Resmi -->
                                    <div class="col-lg-6">
                                        <div class="p-3 border rounded-3 bg-light h-100 d-flex flex-column justify-content-between">
                                            <div>
                                                <div class="d-flex align-items-center justify-content-between mb-2">
                                                    <h6 class="fw-bold text-dark mb-0" style="font-size: 0.88rem;"><i class="bi bi-file-earmark-check text-success me-1"></i>Naskah Final Bertandatangan</h6>
                                                    @if ($currentDoc->signed_file && file_exists(public_path($currentDoc->signed_file)))
                                                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1" style="font-size: 0.74rem;">Sah &amp; Bertandatangan</span>
                                                    @else
                                                        <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2 py-1" style="font-size: 0.74rem;">Menunggu Naskah Final</span>
                                                    @endif
                                                </div>
                                                @if ($currentDoc->signed_file && file_exists(public_path($currentDoc->signed_file)))
                                                    <div class="detail-pdf-frame mb-2">
                                                        <iframe src="{{ asset($currentDoc->signed_file) }}" title="Preview Naskah Final {{ $currentDoc->doc_type }}"></iframe>
                                                    </div>
                                                @else
                                                    <div class="text-muted py-5 text-center small border rounded-3 bg-white mb-2">
                                                        <i class="bi bi-patch-exclamation detail-step-warning-text fs-3 d-block mb-1"></i>
                                                        Naskah bertandatangan belum diunggah.<br><small class="text-muted">Diunggah saat verifikasi final atau aktivasi naskah resmi.</small>
                                                    </div>
                                                @endif
                                            </div>
                                            @if ($currentDoc->signed_file && file_exists(public_path($currentDoc->signed_file)))
                                                <div class="d-flex gap-2 pt-2 border-top">
                                                    <a href="{{ asset($currentDoc->signed_file) }}" target="_blank" class="btn btn-outline-success btn-sm py-1 px-2.5" style="font-size: 0.78rem;"><i class="bi bi-box-arrow-up-right me-1"></i>Buka Tab Baru</a>
                                                    <a href="{{ asset($currentDoc->signed_file) }}" download class="btn btn-success btn-sm py-1 px-2.5" style="font-size: 0.78rem;"><i class="bi bi-download me-1"></i>Unduh PDF</a>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <!-- Bagian Bawah Tab 2: Proposal, Rencana Kegiatan, Dokumen Pendukung & Laporan Pelaksanaan -->
                                @php
                                    $detailExtraReports = $currentDoc->activities->filter(fn($a) => !empty($a->evidence_file) && !$currentDoc->attachments->contains('file_path', $a->evidence_file));
                                @endphp
                                <div class="mt-3 pt-3 border-top">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <h6 class="fw-bold mb-0 text-dark" style="font-size: 0.88rem;">
                                            <i class="bi bi-paperclip text-primary me-1"></i> Proposal, Rencana Kegiatan, Dokumen Pendukung &amp; Laporan Pelaksanaan
                                        </h6>
                                        <span class="badge bg-light text-dark border py-1 px-2" style="font-size: 0.75rem;">{{ $currentDoc->attachments->count() + $detailExtraReports->count() }} Berkas</span>
                                    </div>
                                    @if ($currentDoc->attachments->isNotEmpty() || $detailExtraReports->isNotEmpty())
                                        <div class="table-responsive border rounded-3">
                                            <table class="detail-data-table table-hover align-middle mb-0">
                                                <thead>
                                                    <tr>
                                                        <th style="width: 185px;">Kategori</th>
                                                        <th>Judul &amp; Nama Berkas</th>
                                                        <th class="text-center" style="width: 145px;">Aksi</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($currentDoc->attachments as $att)
                                                        @php
                                                            $attBadge = match($att->category) {
                                                                'PROPOSAL' => 'bg-info-subtle text-info-emphasis border-info-subtle',
                                                                'RENCANA_KEGIATAN' => 'bg-primary-subtle text-primary border-primary-subtle',
                                                                'LAPORAN_PELAKSANAAN' => 'bg-success-subtle text-success border-success-subtle',
                                                                default => 'bg-secondary-subtle text-secondary border-secondary-subtle',
                                                            };
                                                        @endphp
                                                        <tr>
                                                            <td><span class="badge border {{ $attBadge }} py-1 px-2" style="font-size: 0.74rem;">{{ $att->category_label }}</span></td>
                                                            <td>
                                                                <div class="fw-bold text-dark" style="font-size: 0.84rem;">{{ $att->title ?: $att->original_name }}</div>
                                                                <div class="text-muted" style="font-size: 0.75rem;">
                                                                    {{ $att->original_name }}
                                                                    @if ($att->file_size)
                                                                        &bull; {{ $att->file_size >= 1048576 ? round($att->file_size / 1048576, 1) . ' MB' : round($att->file_size / 1024, 0) . ' KB' }}
                                                                    @endif
                                                                    @if ($att->uploaded_by)
                                                                        &bull; oleh {{ $att->uploaded_by }}
                                                                    @endif
                                                                </div>
                                                            </td>
                                                            <td class="text-center text-nowrap">
                                                                <a href="{{ asset($att->file_path) }}" target="_blank" class="btn btn-outline-secondary btn-sm py-1 px-2" title="Lihat"><i class="bi bi-eye"></i></a>
                                                                <a href="{{ asset($att->file_path) }}" download class="btn btn-secondary btn-sm py-1 px-2" title="Unduh"><i class="bi bi-download"></i></a>
                                                                <form method="POST" action="{{ route('simkerma.delete-attachment') }}" class="d-inline" onsubmit="return confirm('Hapus berkas lampiran ini?');">
                                                                    @csrf
                                                                    <input type="hidden" name="attachment_id" value="{{ $att->id }}">
                                                                    <button type="submit" class="btn btn-outline-danger btn-sm py-1 px-2" title="Hapus"><i class="bi bi-trash3"></i></button>
                                                                </form>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                    @foreach ($detailExtraReports as $dRep)
                                                        @php $dRepExists = file_exists(public_path($dRep->evidence_file)) && is_file(public_path($dRep->evidence_file)); @endphp
                                                        <tr>
                                                            <td><span class="badge border bg-success-subtle text-success border-success-subtle py-1 px-2" style="font-size: 0.74rem;">Laporan Pelaksanaan</span></td>
                                                            <td>
                                                                <div class="fw-bold text-dark" style="font-size: 0.84rem;">{{ $dRep->activity_name }}</div>
                                                                <div class="text-muted" style="font-size: 0.75rem;">{{ basename($dRep->evidence_file) }} &bull; Status: {{ $dRep->execution_status }}</div>
                                                            </td>
                                                            <td class="text-center text-nowrap">
                                                                @if ($dRepExists)
                                                                    <a href="{{ asset($dRep->evidence_file) }}" target="_blank" class="btn btn-outline-success btn-sm py-1 px-2" title="Lihat"><i class="bi bi-eye"></i></a>
                                                                    <a href="{{ asset($dRep->evidence_file) }}" download class="btn btn-success btn-sm py-1 px-2" title="Unduh"><i class="bi bi-download"></i></a>
                                                                @else
                                                                    <span class="text-muted fst-italic" style="font-size: 0.75rem;">Arsip Nama Berkas</span>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <div class="text-muted text-center py-3 border rounded-3 bg-light small">
                                            Belum ada lampiran proposal, rencana kegiatan, dokumen pendukung, atau laporan pelaksanaan. Klik tombol <strong>Unggah Berkas Baru</strong> di atas untuk menambahkan.
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- TAB 3: DOKUMEN TURUNAN DETAIL -->
                    <div class="tab-pane fade" id="tab-deriv-pane" role="tabpanel">
                        <div class="detail-card mb-3">
                            <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                                <div>
                                    <div class="detail-card-title">Daftar Dokumen Turunan {{ $currentDoc->doc_type === 'MoU' ? '(MoA & IA)' : ($currentDoc->doc_type === 'IA' ? '(Pelaksanaan Operasional)' : '(IA)') }}</div>
                                    <div class="text-muted mt-1" style="font-size: 0.78rem;">Naskah turunan pelaksanaan yang berinduk secara hukum ke dokumen {{ $currentDoc->doc_type }} ini</div>
                                </div>
                                @if ($currentDoc->doc_type === 'MoU')
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('simkerma.index', ['menu' => 4, 'view' => 'new_derive', 'parent_id' => $currentDoc->doc_id, 'derive_type' => 'MoA / PKS']) }}" class="btn btn-primary btn-sm fw-semibold py-1.5 px-3" style="font-size: 0.8rem;">
                                            <i class="bi bi-plus-lg me-1"></i> + Tambah MoA / PKS
                                        </a>
                                        <a href="{{ route('simkerma.index', ['menu' => 4, 'view' => 'new_derive', 'parent_id' => $currentDoc->doc_id, 'derive_type' => 'IA']) }}" class="btn btn-success btn-sm fw-semibold py-1.5 px-3" style="font-size: 0.8rem;">
                                            <i class="bi bi-plus-lg me-1"></i> + Tambah IA
                                        </a>
                                    </div>
                                @elseif ($currentDoc->doc_type !== 'IA')
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('simkerma.index', ['menu' => 4, 'view' => 'new_derive', 'parent_id' => $currentDoc->doc_id, 'derive_type' => 'IA']) }}" class="btn btn-success btn-sm fw-semibold py-1.5 px-3" style="font-size: 0.8rem;">
                                            <i class="bi bi-plus-lg me-1"></i> + Tambah IA (Implementation)
                                        </a>
                                    </div>
                                @endif
                            </div>
                            <div class="card-body">
                                @if ($currentDoc->doc_type === 'IA')
                                    <div class="text-center py-4 bg-light rounded-3 border">
                                        <i class="bi bi-shield-check text-success fs-2 mb-2 d-inline-block"></i>
                                        <h6 class="fw-bold text-dark mb-1">Dokumen Tingkat Operasional Terakhir (Implementation Arrangement)</h6>
                                        <p class="text-muted mx-auto mb-3" style="max-width: 580px; font-size: 0.84rem;">
                                            Sesuai regulasi tata kelola kerja sama perguruan tinggi, dokumen <strong>Implementation Arrangement (IA)</strong> berada di tingkat Program Studi/Laboratorium dan tidak memiliki naskah turunan hukum lebih lanjut. Seluruh implementasi riil dicatat sebagai <strong>Catatan Pelaksanaan Kegiatan (IKU 6)</strong> pada tab di samping.
                                        </p>
                                        <button type="button" class="btn btn-primary btn-sm fw-semibold px-3 py-1.5" onclick="document.getElementById('tab-act-btn').click();">
                                            <i class="bi bi-list-task me-1"></i> Lihat Tab Pelaksanaan Kegiatan
                                        </button>
                                    </div>
                                @elseif ($childDocs->isNotEmpty())
                                    <div class="table-responsive border rounded-3">
                                        <table class="detail-data-table table-hover align-middle mb-0">
                                            <thead>
                                                <tr>
                                                    <th class="text-center" style="width: 48px;">No.</th>
                                                    <th class="text-center" style="width: 105px;">Jenis</th>
                                                    <th>Judul dan Nomor Dokumen</th>
                                                    <th>Unit Pelaksana</th>
                                                    <th>Mitra</th>
                                                    <th class="text-center" style="width: 125px;">Status</th>
                                                    <th class="text-center" style="width: 88px;">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($childDocs as $cIdx => $cDoc)
                                                    <tr>
                                                        <td class="text-center text-muted">{{ $cIdx + 1 }}</td>
                                                        <td class="text-center">
                                                            @if (stripos($cDoc->doc_type, 'MoA') !== false)
                                                                <span class="badge rounded-pill fw-semibold px-2 py-1" style="background-color: #e0f2fe; color: #0284c7; font-size: 0.74rem;">MoA / PKS</span>
                                                            @else
                                                                <span class="badge rounded-pill fw-semibold px-2 py-1" style="background-color: #dcfce7; color: #16a34a; font-size: 0.74rem;">IA</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <div class="fw-bold text-dark">{{ $cDoc->title ?: 'Dokumen Turunan' }}</div>
                                                            <div class="text-muted font-monospace" style="font-size: 0.75rem;">{{ $cDoc->doc_number ?: $cDoc->doc_id }}</div>
                                                        </td>
                                                        <td class="text-secondary">{{ $cDoc->unit }}</td>
                                                        <td class="text-dark">{{ $cDoc->partner_name }}</td>
                                                        <td class="text-center">
                                                            <span class="badge {{ $stepperList[$cDoc->status][1] ?? 'bg-secondary' }} px-2 py-1" style="font-size: 0.74rem;">
                                                                {{ $cDoc->status }}
                                                            </span>
                                                        </td>
                                                        <td class="text-center">
                                                            <a href="{{ route('simkerma.index', ['menu' => 4, 'view' => 'detail', 'doc' => $cDoc->doc_id]) }}" class="btn btn-outline-primary btn-sm py-1 px-2" style="font-size: 0.78rem;">
                                                                Detail
                                                            </a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="text-center py-4 bg-light rounded-3 border">
                                        <i class="bi bi-diagram-3 text-muted fs-3"></i>
                                        <div class="fw-semibold text-dark mt-1">Belum Ada Dokumen Turunan Terdaftar</div>
                                        <div class="text-muted" style="font-size: 0.78rem;">Gunakan tombol di atas untuk menambahkan perjanjian kerja sama turunan ({{ $currentDoc->doc_type === 'MoU' ? 'MoA / IA' : 'IA' }})</div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- TAB 4: PELAKSANAAN KEGIATAN -->
                    <div class="tab-pane fade" id="tab-act-pane" role="tabpanel">
                        <div class="detail-card mb-3">
                            <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                                <div>
                                    <div class="detail-card-title"><i class="bi bi-calendar2-check text-primary"></i> Catatan Pelaksanaan Kerja Sama (IKU 6)</div>
                                    <div class="text-muted mt-1" style="font-size: 0.78rem;">Kegiatan riil operasional yang mengimplementasikan naskah {{ $currentDoc->doc_type }} ini</div>
                                </div>
                                <button type="button" class="btn btn-primary btn-sm fw-semibold py-1.5 px-3" style="font-size: 0.8rem;" data-bs-toggle="modal" data-bs-target="#createActivityModal_{{ $currentDoc->id }}">
                                    <i class="bi bi-plus-lg me-1"></i> Catat Kegiatan Baru
                                </button>
                            </div>
                            <div class="card-body">
                                @if ($currentDoc->activities->isNotEmpty())
                                    <div class="table-responsive border rounded-3">
                                        <table class="detail-data-table table-hover align-middle mb-0">
                                            <thead>
                                                <tr>
                                                    <th class="text-center" style="width: 48px;">No.</th>
                                                    <th>Nama Kegiatan Pelaksanaan</th>
                                                    <th style="width: 145px;">Jadwal</th>
                                                    <th style="width: 160px;">Target &amp; Realisasi</th>
                                                    <th>Pihak Terlibat &amp; Luaran (IKU 6)</th>
                                                    <th class="text-center" style="width: 125px;">Bukti &amp; Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($currentDoc->activities as $actIdx => $act)
                                                    <tr>
                                                        <td class="text-center text-muted">{{ $loop->iteration }}</td>
                                                        <td>
                                                            <div class="fw-bold text-dark">{{ $act->activity_name }}</div>
                                                            @if ($act->budget_info && $act->budget_info !== '-')
                                                                <div class="text-muted mt-1" style="font-size: 0.75rem;"><i class="bi bi-cash-coin me-1"></i>{{ $act->budget_info }}</div>
                                                            @endif
                                                            @if ($act->notes)
                                                                <div class="text-muted mt-1" style="font-size: 0.75rem;">{{ $act->notes }}</div>
                                                            @endif
                                                        </td>
                                                        <td class="text-nowrap">
                                                            <div>{{ $act->schedule_start ?: '-' }}</div>
                                                            @if ($act->schedule_end)
                                                                <div class="text-muted" style="font-size: 0.75rem;">s/d {{ $act->schedule_end }}</div>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <div class="text-muted" style="font-size: 0.78rem;">Target: <strong class="text-dark">{{ $act->target_qty ?: '-' }}</strong></div>
                                                            <div class="text-muted" style="font-size: 0.78rem;">Realisasi: <strong class="text-success">{{ $act->realized_qty ?: '-' }}</strong></div>
                                                        </td>
                                                        <td>
                                                            <div class="text-secondary" style="font-size: 0.78rem;">{{ $act->parties_involved ?: '-' }}</div>
                                                            <div class="text-dark fw-medium mt-1" style="font-size: 0.78rem;"><i class="bi bi-award text-primary me-1"></i>{{ $act->outputs ?: '-' }}</div>
                                                        </td>
                                                        <td class="text-center">
                                                            @php
                                                                $actBadge = match($act->execution_status) {
                                                                    'SELESAI' => 'bg-success-subtle text-success border border-success-subtle',
                                                                    'SEDANG_BERJALAN' => 'bg-primary-subtle text-primary border border-primary-subtle',
                                                                    'BELUM_DIMULAI' => 'bg-secondary-subtle text-secondary border border-secondary-subtle',
                                                                    'TERTUNDA', 'TERKENDALA' => 'bg-warning-subtle text-warning-emphasis border border-warning-subtle',
                                                                    'DIBATALKAN' => 'bg-danger-subtle text-danger border border-danger-subtle',
                                                                    default => 'bg-secondary-subtle text-secondary border border-secondary-subtle'
                                                                };
                                                                $actEvOk = !empty($act->evidence_file) && file_exists(public_path($act->evidence_file)) && is_file(public_path($act->evidence_file));
                                                                $actPhOk = !empty($act->evidence_photos) && file_exists(public_path($act->evidence_photos)) && is_file(public_path($act->evidence_photos));
                                                            @endphp
                                                            <span class="badge {{ $actBadge }} px-2 py-1 d-inline-block mb-1" style="font-size: 0.72rem;">{{ $act->execution_status }}</span>
                                                            @if ($actEvOk || $actPhOk)
                                                                <div class="d-flex justify-content-center gap-1 mt-1">
                                                                    @if ($actEvOk)
                                                                        <a href="{{ asset($act->evidence_file) }}" target="_blank" class="btn btn-light border btn-sm py-1 px-2" title="Buka Laporan Pelaksanaan"><i class="bi bi-file-earmark-text text-primary"></i></a>
                                                                    @endif
                                                                    @if ($actPhOk)
                                                                        <a href="{{ asset($act->evidence_photos) }}" target="_blank" class="btn btn-light border btn-sm py-1 px-2" title="Buka Dokumentasi / Sertifikat"><i class="bi bi-image text-success"></i></a>
                                                                    @endif
                                                                </div>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="text-center py-4 bg-light rounded-3 border">
                                        <i class="bi bi-calendar-x text-muted fs-3"></i>
                                        <div class="fw-semibold text-dark mt-1">Belum Ada Catatan Pelaksanaan</div>
                                        <div class="text-muted" style="font-size: 0.78rem;">Klik tombol "Catat Kegiatan Baru" untuk merekam implementasi Tri Dharma perguruan tinggi yang merujuk pada naskah ini.</div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- TAB 5: RIWAYAT & AUDIT DETAIL -->
                    <div class="tab-pane fade" id="tab-audit-pane" role="tabpanel">
                        <div class="detail-card mb-3">
                            <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                                <div>
                                    <div class="detail-card-title"><i class="bi bi-shield-lock text-primary"></i> Jejak Audit Relasional FSM (Immutable Audit Trail)</div>
                                    <div class="text-muted mt-1" style="font-size: 0.78rem;">Catatan mutlak setiap transisi status dan aksi pejabat berwenang di Universitas Parna Raya (UNIPAR)</div>
                                </div>
                                <span class="badge bg-light text-dark border font-monospace py-1 px-2.5" style="font-size: 0.78rem;">
                                    Total: {{ $currentDoc->auditTrails->count() }} Rekaman
                                </span>
                            </div>
                            <div class="card-body">
                                @if ($currentDoc->auditTrails->isNotEmpty())
                                    <div class="table-responsive border rounded-3">
                                        <table class="detail-data-table table-striped align-middle mb-0">
                                            <thead class="detail-thead-dark">
                                                <tr>
                                                    <th class="text-center" style="width: 48px;">#</th>
                                                    <th style="width: 165px;">Waktu (WITA)</th>
                                                    <th style="width: 195px;">Aktor &amp; Peran</th>
                                                    <th style="width: 225px;">Event &amp; Transisi Status</th>
                                                    <th>Catatan Verifikasi / Disposisi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($currentDoc->auditTrails->sortByDesc('id') as $aIdx => $trail)
                                                    <tr>
                                                        <td class="text-center text-muted">{{ $loop->iteration }}</td>
                                                        <td class="font-monospace text-nowrap" style="font-size: 0.78rem;">{{ $trail->timestamp_wita }}</td>
                                                        <td>
                                                            <div class="fw-bold text-dark">{{ $trail->actor }}</div>
                                                            <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle py-0 px-1.5 mt-1" style="font-size: 0.72rem;">{{ $trail->role }}</span>
                                                        </td>
                                                        <td>
                                                            <div class="mb-1">
                                                                <span class="badge bg-primary text-white font-monospace py-1 px-2" style="font-size: 0.72rem;">{{ $trail->event }}</span>
                                                            </div>
                                                            <div class="d-flex align-items-center gap-1 flex-wrap" style="font-size: 0.72rem;">
                                                                <span class="badge bg-light text-dark border">{{ $trail->from_status }}</span>
                                                                <i class="bi bi-arrow-right text-muted"></i>
                                                                <span class="badge bg-success-subtle text-success border border-success-subtle">{{ $trail->to_status }}</span>
                                                            </div>
                                                        </td>
                                                        <td class="text-secondary">{{ $trail->notes ?: '-' }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="text-center py-4 bg-light rounded-3 border">
                                        <i class="bi bi-clock-history text-muted fs-3"></i>
                                        <div class="fw-semibold text-dark mt-1">Belum Ada Rekaman Jejak Audit</div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                </div>

            </div>

            <!-- ===================================================================================== -->
            <!-- MODAL EDIT INFORMASI UMUM DOKUMEN (SESUAI FIELD PENGAJUAN AWAL)                      -->
            <!-- ===================================================================================== -->
            @php
                $unitOptions = [
                    'Biro Kerja Sama & Urusan Internasional (BKUI)',
                    'Program Studi Teknik Informatika',
                    'Program Studi Sistem Informasi',
                    'Program Studi Sistem Komputer',
                    'Program Studi Bisnis Digital',
                    'Program Studi Kewirausahaan',
                    'Program Studi Hukum Bisnis',
                    'Fakultas Ilmu Komputer',
                    'Fakultas Ekonomi dan Bisnis',
                    'LPPM',
                    'Biro Administrasi Akademik & Kemahasiswaan',
                    'Rektorat'
                ];

                $categoryOptions = [
                    'Perguruan Tinggi',
                    'Perguruan Tinggi Dalam Negeri',
                    'Perguruan Tinggi Luar Negeri',
                    'DUDI / Swasta',
                    'BUMN',
                    'BUMN / BUMD',
                    'Instansi Pemerintah',
                    'Lembaga Keagamaan / Komunitas',
                    'Organisasi Non-Profit / NGO',
                    'Kesehatan',
                    'Lainnya'
                ];

                preg_match('/(\d+)/', (string)($currentDoc->validity ?? ''), $vMatch);
                $currentValYears = isset($vMatch[1]) ? (int)$vMatch[1] : '';
            @endphp
            <div class="modal fade" id="editInfoModal_{{ $currentDoc->id }}" tabindex="-1" aria-labelledby="editInfoModalLabel_{{ $currentDoc->id }}" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
                    <div class="modal-content border-0 shadow">
                        <div class="modal-header py-2.5 px-3 bg-light border-bottom">
                            <div>
                                <h6 class="modal-title fw-bold text-dark mb-0" id="editInfoModalLabel_{{ $currentDoc->id }}">
                                    <i class="bi bi-pencil-square text-primary me-1.5"></i> Edit Informasi Kerja Sama
                                </h6>
                                <div class="text-muted small" style="font-size: 0.72rem;">
                                    Sesuaikan data dokumen {{ $currentDoc->doc_number && $currentDoc->doc_number !== '-' ? $currentDoc->doc_number : $currentDoc->doc_id }} agar tetap sinkron dengan draf pengajuan.
                                </div>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form method="POST" action="{{ route('simkerma.update-document') }}" class="d-flex flex-column overflow-hidden flex-grow-1" style="min-height: 0;">
                            @csrf
                            <input type="hidden" name="doc_id" value="{{ $currentDoc->doc_id }}">
                            <div class="modal-body p-3.5 small">
                                @if ($isFrozenUi)
                                    <div class="alert alert-warning py-2 px-3 small mb-3 d-flex align-items-start gap-2 border-warning-subtle">
                                        <i class="bi bi-lock-fill text-warning-emphasis fs-5 mt-0.5"></i>
                                        <div>
                                            <strong>Proteksi Freeze Text Aktif ({{ $currentDoc->status }}):</strong>
                                            Identitas legal (Jenis Dokumen, Nomor Naskah, dan Nama Mitra) dikunci oleh sistem untuk peran <code>{{ $activeRole }}</code>. Hubungi <strong>ADMIN / MASTER</strong> jika perlu mengubah substansi legal.
                                        </div>
                                    </div>
                                @endif

                                <!-- 0. JENIS DOKUMEN & INDUK DOKUMEN -->
                                <div class="row g-2 mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold text-dark mb-1" style="font-size: 0.8rem;">
                                            Jenis Dokumen Kerja Sama : <span class="text-danger">*</span>
                                        </label>
                                        <select name="doc_type" id="edit_doc_type_{{ $currentDoc->id }}" class="form-select form-select-sm {{ $isFrozenUi ? 'bg-light' : '' }}" {{ $isFrozenUi ? 'disabled' : 'required' }} onchange="toggleEditParent_{{ $currentDoc->id }}()">
                                            <option value="MoU" {{ $currentDoc->doc_type === 'MoU' ? 'selected' : '' }}>MoU (Memorandum of Understanding / Universitas)</option>
                                            <option value="MoA / PKS" {{ stripos($currentDoc->doc_type, 'MoA') !== false ? 'selected' : '' }}>MoA / PKS (Perjanjian Kerja Sama / Fakultas)</option>
                                            <option value="IA" {{ $currentDoc->doc_type === 'IA' ? 'selected' : '' }}>IA (Implementation Arrangement / Prodi)</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6" id="edit_parent_box_{{ $currentDoc->id }}" style="{{ $currentDoc->doc_type === 'MoU' ? 'display: none;' : '' }}">
                                        <label class="form-label fw-bold text-dark mb-1" style="font-size: 0.8rem;">
                                            Dokumen Induk (Rujukan Payung Hukum) :
                                        </label>
                                        <select name="parent_mou" class="form-select form-select-sm">
                                            <option value="">-- Naskah Mandiri / Tanpa Induk --</option>
                                            @foreach ($registeredMous as $rm)
                                                @if ($rm->id !== $currentDoc->id)
                                                    <option value="{{ $rm->doc_number ?: $rm->doc_id }}" {{ ($currentDoc->parent_mou === ($rm->doc_number ?: $rm->doc_id) || $currentDoc->parent_mou === $rm->doc_id || $currentDoc->parent_mou === $rm->doc_number) ? 'selected' : '' }}>
                                                        [{{ $rm->doc_type }}] {{ $rm->partner_name }} - No: {{ $rm->doc_number ?: $rm->doc_id }}
                                                    </option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <!-- 1. JUDUL / TOPIK KERJA SAMA -->
                                <div class="mb-3">
                                    <label class="form-label fw-bold text-dark mb-1" style="font-size: 0.8rem;">
                                        Judul / Topik Kerja Sama : <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" name="title" class="form-control" value="{{ $currentDoc->title }}" required>
                                </div>

                                <!-- 2. IDENTITAS PIHAK PERTAMA (UNIPAR) -->
                                <div class="p-3 bg-light rounded-3 border mb-3">
                                    <div class="fw-bold text-dark mb-2 pb-1 border-bottom d-flex align-items-center gap-1.5" style="font-size: 0.82rem;">
                                        <i class="bi bi-building text-primary"></i> IDENTITAS PIHAK PERTAMA (UNIPAR)
                                    </div>
                                    <div class="row g-2 mb-2">
                                        <div class="col-md-6">
                                            <label class="form-label text-muted fw-semibold mb-0.5" style="font-size: 0.76rem;">
                                                Unit Pengusul : <span class="text-danger">*</span>
                                            </label>
                                            <select name="unit" class="form-select form-select-sm" required>
                                                @foreach ($unitOptions as $u)
                                                    <option value="{{ $u }}" {{ $currentDoc->unit === $u ? 'selected' : '' }}>{{ $u }}</option>
                                                @endforeach
                                                @if (!in_array($currentDoc->unit, $unitOptions) && !empty($currentDoc->unit))
                                                    <option value="{{ $currentDoc->unit }}" selected>{{ $currentDoc->unit }}</option>
                                                @endif
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label text-muted fw-semibold mb-0.5" style="font-size: 0.76rem;">
                                                Penandatangan UNIPAR :
                                            </label>
                                            <input type="text" name="first_party_signer" class="form-control form-control-sm" value="{{ $currentDoc->first_party_signer }}" placeholder="Nama lengkap & gelar penandatangan UNIPAR">
                                        </div>
                                    </div>
                                    <div class="row g-2 mb-2">
                                        <div class="col-md-6">
                                            <label class="form-label text-muted fw-semibold mb-0.5" style="font-size: 0.76rem;">
                                                Jabatan Penandatangan :
                                            </label>
                                            <input type="text" name="first_party_position" class="form-control form-control-sm" value="{{ $currentDoc->first_party_position }}" placeholder="Contoh: Rektor, Dekan Fakultas Ilmu Komputer">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label text-muted fw-semibold mb-0.5" style="font-size: 0.76rem;">
                                                PIC / Narahubung UNIPAR :
                                            </label>
                                            <input type="text" name="pic_first_party" class="form-control form-control-sm" value="{{ $currentDoc->pic_first_party }}" placeholder="Nama narahubung UNIPAR">
                                        </div>
                                    </div>
                                    <div class="row g-2">
                                        <div class="col-md-6">
                                            <label class="form-label text-muted fw-semibold mb-0.5" style="font-size: 0.76rem;">
                                                Nomor Naskah UNIPAR : <span class="badge bg-white text-secondary border fw-normal">Opsional</span>
                                                @if ($isFrozenUi)
                                                    <i class="bi bi-lock-fill text-warning ms-1" title="Dibekukan (Freeze Text)"></i>
                                                @endif
                                            </label>
                                            <input type="text" name="doc_number" class="form-control form-control-sm font-monospace {{ $isFrozenUi ? 'bg-light' : '' }}" value="{{ (!empty($currentDoc->doc_number) && $currentDoc->doc_number !== '-') ? $currentDoc->doc_number : '' }}" placeholder="-" {{ $isFrozenUi ? 'readonly' : '' }}>
                                            <div class="text-muted" style="font-size: 0.68rem;">Kosongkan jika belum ada (dicatat sebagai '-')</div>
                                        </div>
                                    </div>
                                </div>

                                <!-- 3. IDENTITAS PIHAK KEDUA (MITRA) -->
                                <div class="p-3 bg-light rounded-3 border mb-3">
                                    <div class="fw-bold text-dark mb-2 pb-1 border-bottom d-flex align-items-center gap-1.5" style="font-size: 0.82rem;">
                                        <i class="bi bi-briefcase text-primary"></i> IDENTITAS PIHAK KEDUA (MITRA)
                                    </div>
                                    <div class="row g-2 mb-2">
                                        <div class="col-md-6">
                                            <label class="form-label text-muted fw-semibold mb-0.5" style="font-size: 0.76rem;">
                                                Kategori / Sektor Mitra : <span class="text-danger">*</span>
                                            </label>
                                            <select name="category" id="edit_category_{{ $currentDoc->id }}" class="form-select form-select-sm" required>
                                                @foreach ($categoryOptions as $cat)
                                                    <option value="{{ $cat }}" {{ ($currentDoc->partner_category === $cat) ? 'selected' : '' }}>{{ $cat }}</option>
                                                @endforeach
                                                @if (!in_array($currentDoc->partner_category, $categoryOptions) && !empty($currentDoc->partner_category))
                                                    <option value="{{ $currentDoc->partner_category }}" selected>{{ $currentDoc->partner_category }}</option>
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row g-2 mb-2">
                                        <div class="col-12">
                                            <label class="form-label text-muted fw-semibold mb-0.5" style="font-size: 0.76rem;">
                                                Nama Lembaga Mitra : <span class="text-danger">*</span>
                                                @if ($isFrozenUi)
                                                    <i class="bi bi-lock-fill text-warning ms-1" title="Dibekukan (Freeze Text)"></i>
                                                @endif
                                            </label>
                                            <div class="{{ $isFrozenUi ? '' : 'hybrid-partner-combobox' }} position-relative">
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text bg-white border-end-0">
                                                        <i class="partner-input-icon bi bi-mortarboard-fill text-primary fs-5"></i>
                                                    </span>
                                                    <input type="text" name="partner_name" id="edit_partner_name_{{ $currentDoc->id }}" class="form-control border-start-0 {{ $isFrozenUi ? 'bg-light' : 'smart-partner-input' }}" 
                                                           placeholder="Ketik nama kampus (contoh: Sam Ratulangi, ITB, Parna Raya)..." 
                                                           value="{{ $currentDoc->partner_name }}"
                                                           list="editPartnerDatalist_{{ $currentDoc->id }}"
                                                           autocomplete="off" {{ $isFrozenUi ? 'readonly' : 'required' }}>
                                                    @if (!$isFrozenUi)
                                                        <button class="btn btn-outline-secondary btn-clear-partner {{ empty($currentDoc->partner_name) ? 'd-none' : '' }}" type="button" title="Hapus / Reset Pilihan"><i class="bi bi-x-lg"></i></button>
                                                        <button class="btn btn-primary btn-sm px-3 fw-bold btn-open-quick-add" type="button" data-bs-toggle="modal" data-bs-target="#quickAddPartnerModal" title="Tambah Mitra Baru ke Master Data (Menu 02)">
                                                            <i class="bi bi-plus-lg me-1"></i> Tambah Mitra
                                                        </button>
                                                    @endif
                                                </div>
                                                <datalist id="editPartnerDatalist_{{ $currentDoc->id }}">
                                                    @foreach ($partners as $p)
                                                        <option value="{{ $p->name }}">{{ $p->category }} &bull; {{ $p->city ?: 'Manado' }}</option>
                                                    @endforeach
                                                </datalist>
                                                <!-- Floating Dropdown Suggestions -->
                                                <div class="partner-dropdown-menu shadow-lg bg-white border rounded mt-1 d-none position-absolute w-100" style="z-index: 1060; max-height: 280px; overflow-y: auto;"></div>
                                                <!-- Verified Info Banner -->
                                                <div class="partner-verified-info small mt-1.5 {{ empty($currentDoc->partner_name) ? 'd-none' : '' }}">
                                                    @if (!empty($currentDoc->partner_name))
                                                        <div class="alert alert-success py-1 px-2.5 small mb-0 d-inline-flex align-items-center gap-2 border shadow-sm" style="font-size: 0.72rem;">
                                                            <i class="bi bi-patch-check-fill text-success fs-6"></i>
                                                            <div>
                                                                <strong>Mitra Terpilih:</strong> {{ $currentDoc->partner_name }}
                                                                @if (!empty($currentDoc->partner_category))
                                                                    &bull; {{ $currentDoc->partner_category }}
                                                                @endif
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                                <!-- Helper Tools -->
                                                <div class="d-flex flex-wrap justify-content-between align-items-center mt-1">
                                                    <small class="partner-mode-desc text-muted"><i class="bi bi-database-check text-success me-1"></i>Direktori Resmi PDDikti Kemendikbudristek (4.650+ PT)</small>
                                                    <a href="javascript:void(0)" class="small text-decoration-none text-primary btn-toggle-manual"><i class="bi bi-pencil-square me-1"></i>Ketik Manual / Luar Negeri</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row g-2 mb-2">
                                        <div class="col-md-6">
                                            <label class="form-label text-muted fw-semibold mb-0.5" style="font-size: 0.76rem;">
                                                Penandatangan Mitra :
                                            </label>
                                            <input type="text" name="second_party_signer" class="form-control form-control-sm" value="{{ $currentDoc->second_party_signer }}" placeholder="Nama lengkap & gelar penandatangan Mitra">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label text-muted fw-semibold mb-0.5" style="font-size: 0.76rem;">
                                                Jabatan Penandatangan :
                                            </label>
                                            <input type="text" name="second_party_position" class="form-control form-control-sm" value="{{ $currentDoc->second_party_position }}" placeholder="Contoh: Direktur Utama, Rektor, Kepala Dinas">
                                        </div>
                                    </div>
                                    <div class="row g-2">
                                        <div class="col-md-6">
                                            <label class="form-label text-muted fw-semibold mb-0.5" style="font-size: 0.76rem;">
                                                PIC / Narahubung Mitra :
                                            </label>
                                            <input type="text" name="pic_second_party" class="form-control form-control-sm" value="{{ $currentDoc->pic_second_party }}" placeholder="Nama narahubung Mitra">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label text-muted fw-semibold mb-0.5" style="font-size: 0.76rem;">
                                                Nomor Naskah Mitra : <span class="badge bg-white text-secondary border fw-normal">Opsional</span>
                                                @if ($isFrozenUi)
                                                    <i class="bi bi-lock-fill text-warning ms-1" title="Dibekukan (Freeze Text)"></i>
                                                @endif
                                            </label>
                                            <input type="text" name="partner_doc_number" class="form-control form-control-sm font-monospace {{ $isFrozenUi ? 'bg-light' : '' }}" value="{{ (!empty($currentDoc->partner_doc_number) && $currentDoc->partner_doc_number !== '-') ? $currentDoc->partner_doc_number : '' }}" placeholder="-" {{ $isFrozenUi ? 'readonly' : '' }}>
                                            <div class="text-muted" style="font-size: 0.68rem;">Kosongkan jika belum ada (dicatat sebagai '-')</div>
                                        </div>
                                    </div>
                                </div>

                                <!-- 4. PERIODE BERLAKU -->
                                <div class="p-3 bg-light rounded-3 border mb-3">
                                    <div class="fw-bold text-dark mb-2 pb-1 border-bottom d-flex align-items-center gap-1.5" style="font-size: 0.82rem;">
                                        <i class="bi bi-calendar3 text-primary"></i> PERIODE BERLAKU DOKUMEN
                                    </div>
                                    <div class="row g-2">
                                        <div class="col-md-4">
                                            <label class="form-label text-muted fw-semibold mb-0.5" style="font-size: 0.76rem;">
                                                Tanggal Mulai Berlaku :
                                            </label>
                                            <input type="date" name="start_date" id="editStartDate_{{ $currentDoc->id }}" class="form-control form-control-sm" value="{{ $currentDoc->start_date ? \Carbon\Carbon::parse($currentDoc->start_date)->format('Y-m-d') : '' }}" onchange="calcEditEndDate_{{ $currentDoc->id }}()">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label text-muted fw-semibold mb-0.5" style="font-size: 0.76rem;">
                                                Masa Berlaku (Tahun) :
                                            </label>
                                            <div class="input-group input-group-sm">
                                                <input type="number" min="1" max="10" name="validity_years" id="editValidityYears_{{ $currentDoc->id }}" class="form-control" value="{{ $currentValYears }}" oninput="calcEditEndDate_{{ $currentDoc->id }}()">
                                                <span class="input-group-text bg-white text-muted">Tahun</span>
                                            </div>
                                            <input type="hidden" name="validity" id="editValidity_{{ $currentDoc->id }}" value="{{ $currentDoc->validity }}">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label text-muted fw-semibold mb-0.5" style="font-size: 0.76rem;">
                                                Tanggal Berakhir :
                                            </label>
                                            <input type="date" name="end_date" id="editEndDate_{{ $currentDoc->id }}" class="form-control form-control-sm bg-white" value="{{ $currentDoc->end_date ? \Carbon\Carbon::parse($currentDoc->end_date)->format('Y-m-d') : '' }}">
                                        </div>
                                    </div>
                                </div>

                                <!-- 5. RUANG LINGKUP & DESKRIPSI -->
                                <div class="mb-2">
                                    <label class="form-label fw-bold text-dark mb-1" style="font-size: 0.8rem;">
                                        Ruang Lingkup Kegiatan :
                                    </label>
                                    <textarea name="scope" class="form-control form-control-sm" rows="2" placeholder="Bidang/kegiatan Tridharma...">{{ $currentDoc->scope }}</textarea>
                                </div>

                                <div class="mb-2">
                                    <label class="form-label fw-bold text-dark mb-1" style="font-size: 0.8rem;">
                                        Deskripsi / Urgensi Kerja Sama :
                                    </label>
                                    <textarea name="description" class="form-control form-control-sm" rows="2" placeholder="Catatan atau deskripsi kerja sama...">{{ $currentDoc->description }}</textarea>
                                </div>
                            </div>
                            <div class="modal-footer py-2 px-3 bg-light border-top">
                                <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-primary btn-sm fw-semibold"><i class="bi bi-check-lg me-1"></i> Simpan Perubahan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <script>
            function calcEditEndDate_{{ $currentDoc->id }}() {
                const startEl = document.getElementById('editStartDate_{{ $currentDoc->id }}');
                const yearsEl = document.getElementById('editValidityYears_{{ $currentDoc->id }}');
                const endEl = document.getElementById('editEndDate_{{ $currentDoc->id }}');
                const validityEl = document.getElementById('editValidity_{{ $currentDoc->id }}');

                if (!startEl || !yearsEl || !endEl) return;

                const years = parseInt(yearsEl.value, 10);
                if (validityEl) {
                    validityEl.value = (years && !isNaN(years)) ? (years + ' Tahun') : '';
                }

                if (!startEl.value || isNaN(years) || years <= 0) {
                    return;
                }

                const startDate = new Date(startEl.value);
                if (isNaN(startDate.getTime())) {
                    return;
                }

                const endDate = new Date(startDate);
                endDate.setFullYear(endDate.getFullYear() + years);
                const yyyy = endDate.getFullYear();
                const mm = String(endDate.getMonth() + 1).padStart(2, '0');
                const dd = String(endDate.getDate()).padStart(2, '0');
                endEl.value = `${yyyy}-${mm}-${dd}`;
            }

            function toggleEditParent_{{ $currentDoc->id }}() {
                const docTypeEl = document.getElementById('edit_doc_type_{{ $currentDoc->id }}');
                const parentBoxEl = document.getElementById('edit_parent_box_{{ $currentDoc->id }}');
                if (!docTypeEl || !parentBoxEl) return;
                if (docTypeEl.value === 'MoU') {
                    parentBoxEl.style.display = 'none';
                } else {
                    parentBoxEl.style.display = 'block';
                }
            }
            </script>

            <!-- ===================================================================================== -->
            <!-- MODAL UNGGAH BERKAS DOKUMEN                                                           -->
            <!-- ===================================================================================== -->
            <div class="modal fade" id="uploadDocModal_{{ $currentDoc->id }}" tabindex="-1" aria-labelledby="uploadDocModalLabel_{{ $currentDoc->id }}" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 shadow">
                        <div class="modal-header py-2 px-3 bg-light border-bottom">
                            <h6 class="modal-title fw-bold text-dark" id="uploadDocModalLabel_{{ $currentDoc->id }}"><i class="bi bi-cloud-arrow-up text-primary me-1.5"></i> Unggah Berkas Naskah</h6>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form method="POST" action="{{ route('simkerma.upload-document-file') }}" enctype="multipart/form-data" id="detailUploadForm_{{ $currentDoc->id }}">
                            @csrf
                            <input type="hidden" name="doc_id" value="{{ $currentDoc->doc_id }}">
                            <div class="modal-body p-3 small">
                                <div class="mb-2">
                                    <label class="form-label text-muted fw-semibold mb-0.5">Jenis Berkas</label>
                                    <select name="file_type" id="detailUploadFileType_{{ $currentDoc->id }}" class="form-select form-select-sm" required onchange="syncDetailUploadField_{{ $currentDoc->id }}()">
                                        <option value="draft">Naskah Draf Pengusul (PDF/DOCX)</option>
                                        <option value="signed">Naskah Asli Bertandatangan Basah &amp; Bermeterai (PDF)</option>
                                        <option value="proposal">Proposal Kerja Sama (PDF/Word)</option>
                                        <option value="rencana_kegiatan">Rencana Kegiatan / TOR (PDF/Word/Excel)</option>
                                        <option value="dokumen_pendukung">Dokumen Pendukung (Notula/Surat Tugas/Legalitas)</option>
                                    </select>
                                </div>
                                <div class="mb-2 d-none" id="detailUploadTitleWrap_{{ $currentDoc->id }}">
                                    <label class="form-label text-muted fw-semibold mb-0.5">Judul / Keterangan Berkas (Opsional)</label>
                                    <input type="text" name="attachment_title" class="form-control form-control-sm" placeholder="Contoh: Proposal Kerja Sama 2026 / Notula Rapat">
                                </div>
                                <div class="mb-2 p-2 rounded border bg-light text-muted" id="detailUploadMainNote_{{ $currentDoc->id }}" style="font-size: 0.72rem;">
                                    <i class="bi bi-info-circle text-primary me-1"></i>Berkas akan memperbarui <strong>Naskah Utama</strong> dokumen ini.
                                </div>
                                <div class="mb-2">
                                    <label class="form-label text-muted fw-semibold mb-0.5">Pilih Berkas (Maks. 15 MB)</label>
                                    <input type="file" name="file" class="form-control form-control-sm" required accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png">
                                </div>
                            </div>
                            <div class="modal-footer py-2 px-3 bg-light border-top">
                                <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                                <button type="submit" id="detailUploadSubmitBtn_{{ $currentDoc->id }}" class="btn btn-primary btn-sm fw-semibold"><i class="bi bi-upload me-1"></i> Unggah Berkas</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- ===================================================================================== -->
            <!-- MODAL CATAT KEGIATAN PELAKSANAAN DARI DETAIL DOKUMEN (TAB 4)                         -->
            <!-- ===================================================================================== -->
            <div class="modal fade" id="createActivityModal_{{ $currentDoc->id }}" tabindex="-1" aria-labelledby="createActivityModalLabel_{{ $currentDoc->id }}" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content border-0 shadow-lg">
                        <form action="{{ route('simkerma.store-activity') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="document_id" value="{{ $currentDoc->id }}">
                            <input type="hidden" name="redirect_to_detail" value="1">
                            <div class="modal-header bg-dark text-white py-3">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bi bi-calendar-plus text-warning fs-5"></i>
                                    <div>
                                        <h6 class="modal-title fw-bold mb-0 text-uppercase" id="createActivityModalLabel_{{ $currentDoc->id }}">Pencatatan Realisasi Pelaksanaan Kerja Sama</h6>
                                        <div class="small text-white-50" style="font-size: 0.72rem;">Merujuk pada dokumen: [{{ $currentDoc->doc_type }}] {{ $currentDoc->partner_name }} ({{ $currentDoc->doc_number ?: $currentDoc->doc_id }})</div>
                                    </div>
                                </div>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body p-4">
                                <div class="alert alert-info py-2 px-3 small border mb-3">
                                    <i class="bi bi-info-circle-fill me-1"></i> <strong>Realisasi IKU 6:</strong> Kegiatan ini akan langsung tercatat dan berelasi kuat dengan naskah <strong>{{ $currentDoc->doc_number ?: $currentDoc->doc_id }}</strong> ({{ $currentDoc->partner_name }}).
                                </div>

                                <div class="row g-3 small">
                                    <!-- Nama Kegiatan -->
                                    <div class="col-12">
                                        <label class="form-label fw-bold text-dark">Nama Kegiatan Pelaksanaan <span class="text-danger">*</span></label>
                                        <input type="text" name="activity_name" class="form-control form-control-sm" required placeholder="Contoh: Magang Industri MBKM Semester Genap / Workshop Kurikulum">
                                    </div>

                                    <!-- Jadwal Mulai & Selesai -->
                                    <div class="col-sm-6">
                                        <label class="form-label fw-bold text-dark">Jadwal Mulai</label>
                                        <input type="text" name="schedule_start" class="form-control form-control-sm" placeholder="Contoh: 01 Feb 2026" value="{{ now()->format('d M Y') }}">
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label fw-bold text-dark">Jadwal Selesai</label>
                                        <input type="text" name="schedule_end" class="form-control form-control-sm" placeholder="Contoh: 31 Jul 2026">
                                    </div>

                                    <!-- Target & Realisasi -->
                                    <div class="col-sm-6">
                                        <label class="form-label fw-bold text-dark">Target Kuantitatif</label>
                                        <input type="text" name="target_qty" class="form-control form-control-sm" placeholder="Contoh: 20 Mahasiswa">
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label fw-bold text-dark">Realisasi Lapangan</label>
                                        <input type="text" name="realized_qty" class="form-control form-control-sm" placeholder="Contoh: 18 Mahasiswa (90%)">
                                    </div>

                                    <!-- Pihak Terlibat -->
                                    <div class="col-12">
                                        <label class="form-label fw-bold text-dark">Pihak / Peserta Terlibat</label>
                                        <textarea name="parties_involved" class="form-control form-control-sm" rows="2" placeholder="Sebutkan mahasiswa, dosen pembina, dan mentor/staf mitra yang terlibat langsung">{{ $currentDoc->unit }} &amp; {{ $currentDoc->partner_name }}</textarea>
                                    </div>

                                    <!-- Luaran IKU 6 -->
                                    <div class="col-12">
                                        <label class="form-label fw-bold text-dark">Luaran &amp; Manfaat Riil (IKU 6)</label>
                                        <textarea name="outputs" class="form-control form-control-sm" rows="2" placeholder="Contoh: Laporan Magang, 2 Sertifikat Kompetensi, 1 Modul Ajar"></textarea>
                                    </div>

                                    <!-- Anggaran & Status Pelaksanaan -->
                                    <div class="col-sm-6">
                                        <label class="form-label fw-bold text-dark">Anggaran / Biaya Pelaksanaan</label>
                                        <input type="text" name="budget_info" class="form-control form-control-sm" placeholder="Contoh: Rp 15.000.000 (Mandiri / Hibah Mitra)">
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label fw-bold text-dark">Status Pelaksanaan <span class="text-danger">*</span></label>
                                        <select name="execution_status" class="form-select form-select-sm" required>
                                            <option value="SEDANG_BERJALAN" selected>Sedang Berjalan</option>
                                            <option value="SELESAI">Selesai / Terarsip</option>
                                            <option value="BELUM_DIMULAI">Belum Dimulai</option>
                                            <option value="TERTUNDA">Tertunda</option>
                                        </select>
                                    </div>

                                    <!-- Unggah Bukti Pelaksanaan (Sinkron Menu 03) -->
                                    <div class="col-sm-6">
                                        <label class="form-label fw-bold text-dark">Unggah Laporan Pelaksanaan (PDF/Word)</label>
                                        <input type="file" name="evidence_file_upload" class="form-control form-control-sm" accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png">
                                        <div class="text-muted mt-0.5" style="font-size: 0.68rem;">Otomatis tersimpan ke Repositori Menu 03</div>
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label fw-bold text-dark">Unggah Dokumentasi / Sertifikat (Opsional)</label>
                                        <input type="file" name="evidence_photos_upload" class="form-control form-control-sm" accept=".jpg,.jpeg,.png,.pdf,.zip">
                                    </div>

                                    <!-- Catatan -->
                                    <div class="col-12">
                                        <label class="form-label fw-bold text-dark">Catatan Tambahan</label>
                                        <textarea name="notes" class="form-control form-control-sm" rows="2" placeholder="Catatan atau evaluasi singkat pelaksanaan kegiatan..."></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer py-2.5 px-4 bg-light border-top">
                                <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-primary btn-sm fw-semibold"><i class="bi bi-check-lg me-1"></i> Simpan Catatan Kegiatan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- ===================================================================================== -->
            <!-- MODAL FSM WORKFLOW ENGINE INTERAKTIF UNTUK DOKUMEN INI (UI/FSM-UI.html)                -->
            <!-- ===================================================================================== -->
            @include('simkerma.partials.fsm_modal', ['doc' => $currentDoc, 'activeRole' => $activeRole, 'fullFsmMatrix' => $fullFsmMatrix ?? []])

            @push('scripts')
            <script>
            function syncDetailUploadField_{{ $currentDoc->id }}() {
                const sel = document.getElementById('detailUploadFileType_{{ $currentDoc->id }}');
                const titleWrap = document.getElementById('detailUploadTitleWrap_{{ $currentDoc->id }}');
                const mainNote = document.getElementById('detailUploadMainNote_{{ $currentDoc->id }}');
                if (!sel || !titleWrap || !mainNote) return;
                const isMain = (sel.value === 'draft' || sel.value === 'signed');
                titleWrap.classList.toggle('d-none', isMain);
                mainNote.classList.toggle('d-none', !isMain);
            }

            document.addEventListener('DOMContentLoaded', function() {
                syncDetailUploadField_{{ $currentDoc->id }}();

                const uploadForm = document.getElementById('detailUploadForm_{{ $currentDoc->id }}');
                if (uploadForm) {
                    uploadForm.addEventListener('submit', function() {
                        const btn = document.getElementById('detailUploadSubmitBtn_{{ $currentDoc->id }}');
                        if (btn) {
                            btn.disabled = true;
                            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>Mengunggah...';
                        }
                    });
                }

                // Persistensi Tab Aktif di Halaman Detail Dokumen (Query ?tab=... & sessionStorage)
                const tabKeyStorage = 'simkerma_detail_tab_' + @json($currentDoc->doc_id);
                const tabButtons = document.querySelectorAll('#detailDocTabs button[data-tab-key]');

                function activateDetailTab(key) {
                    if (!key) return;
                    const targetBtn = document.querySelector(`#detailDocTabs button[data-tab-key="${key}"]`);
                    if (targetBtn && typeof bootstrap !== 'undefined' && bootstrap.Tab) {
                        const tabInst = bootstrap.Tab.getInstance(targetBtn) || new bootstrap.Tab(targetBtn);
                        tabInst.show();
                    }
                }

                tabButtons.forEach(function(btn) {
                    btn.addEventListener('shown.bs.tab', function() {
                        const key = this.getAttribute('data-tab-key');
                        if (key) {
                            try { sessionStorage.setItem(tabKeyStorage, key); } catch (e) {}
                        }
                        // Sinkronkan styling bold/muted pada nav-link tab
                        tabButtons.forEach(function(b) {
                            const active = (b === btn);
                            b.classList.toggle('fw-bold', active);
                            b.classList.toggle('fw-medium', !active);
                            b.classList.toggle('text-muted', !active);
                        });
                    });
                });

                try {
                    const urlObj = new URL(window.location.href);
                    const queryTab = urlObj.searchParams.get('tab');
                    if (queryTab) {
                        activateDetailTab(queryTab);
                        sessionStorage.setItem(tabKeyStorage, queryTab);
                        urlObj.searchParams.delete('tab');
                        window.history.replaceState({}, document.title, urlObj.pathname + urlObj.search);
                    } else {
                        const savedTab = sessionStorage.getItem(tabKeyStorage);
                        if (savedTab) {
                            activateDetailTab(savedTab);
                        }
                    }
                } catch (e) {}
            });
            </script>
            @endpush


@else

            {{-- ================================================================================= --}}
            {{-- VERSI 2.0: TABEL LENGKAP SEMUA PENGAJUAN KERJA SAMA DENGAN DEDICATED QR CODE CELL --}}
            {{-- Menggantikan Dropdown Menu Sesuai Permintaan Jordan (Rinci, Menurun ke Bawah)      --}}
            {{-- ================================================================================= --}}

            <!-- HEADER TABEL & FILTER PENCARIAN (DESAIN KERMA LAMA) -->
            <div class="card">
                <!-- 1. FILTER DOKUMEN (GRID FILTER KERMA LAMA) -->
                <div class="card-header">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label fw-semibold mb-1">Jenis Kerja Sama</label>
                            <select class="form-select form-select-sm" id="tableTypeFilter" onchange="filterDocumentsTable()">
                                <option value="">Semua Jenis Kerja Sama</option>
                                <option value="MoU">MoU (Kesepahaman)</option>
                                <option value="MoA">MoA / PKS (Perjanjian Kerjasama)</option>
                                <option value="IA">IA (Implementation Arrangement)</option>
                                <option value="legacy">Dokumen Lampau (Legacy)</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold mb-1">Unit Pelaksana</label>
                            <select class="form-select form-select-sm" id="tableUnitFilter" onchange="filterDocumentsTable()">
                                <option value="">Semua Unit Pelaksana</option>
                                @foreach ($documents->pluck('unit')->filter()->unique()->sort() as $uName)
                                    <option value="{{ $uName }}">{{ $uName }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold mb-1">Status Kerja Sama</label>
                            <select class="form-select form-select-sm" id="tableStatusFilter" onchange="filterDocumentsTable()">
                                <option value="">Semua Status</option>
                                <option value="DRAFT">1. DRAFT (Konsep Awal)</option>
                                <option value="REVIEW">2. REVIEW (Pemeriksaan Legal)</option>
                                <option value="VERIFIED">3. VERIFIED (Lolos Verifikasi)</option>
                                <option value="ACTIVE">4. ACTIVE (Berlaku Sah)</option>
                                <option value="WARNING">5. WARNING (Peringatan H-90)</option>
                                <option value="EXPIRED">6. EXPIRED (Kedaluwarsa)</option>
                                <option value="CANCELLED">7. CANCELLED (Dibatalkan)</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold mb-1">Pencarian Teks</label>
                            <div class="input-group input-group-sm">
                                <input type="text" id="tableFilterInput" class="form-control form-control-sm" placeholder="Cari nomor, mitra, judul..." onkeyup="filterDocumentsTable()">
                                <button type="button" class="btn btn-outline-secondary" onclick="resetFilters()" title="Reset Filter">
                                    <i class="bi bi-x-circle"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2. TOOLBAR AKSI & TABEL DOKUMEN -->
                <div class="card-body">
                    <div class="mb-3 d-flex flex-wrap align-items-center justify-content-between gap-2">
                        <div class="d-flex flex-wrap align-items-center gap-2">
                            <a href="{{ route('simkerma.index', ['menu' => 4, 'view' => 'new_mou']) }}" class="btn btn-primary waves-effect waves-light" id="tambahBtn">
                                <i class="bi bi-plus-lg me-1"></i> Tambah
                            </a>
                            <a href="{{ route('simkerma.index', ['menu' => 4, 'view' => 'legacy']) }}" class="btn btn-secondary waves-effect waves-light">
                                <i class="bi bi-clock-history me-1"></i> Kerja Sama Lampau
                            </a>
                            <button type="button" class="btn btn-success waves-effect waves-light" id="export" onclick="exportTableToExcel('documentsTable', 'Data_Kerja_Sama')">
                                <i class="bi bi-file-earmark-excel me-1"></i> Excel
                            </button>
                            <button type="button" class="btn btn-info waves-effect waves-light text-white" onclick="resetFilters()">
                                <i class="bi bi-arrow-clockwise me-1"></i> Reload
                            </button>
                        </div>
                        <div>
                            <span class="badge bg-light text-dark border px-3 py-2 fw-semibold">
                                Total: <span id="visibleDocCount" class="fw-bold text-primary">{{ $documents->count() }}</span> Dokumen
                            </span>
                        </div>
                    </div>

                    <!-- TABEL KERMA LAMA FORMAT (6 KOLOM PERSIS) -->
                    <div class="table-responsive" style="border: 0">
                        <table id="documentsTable" class="w-100 table table-bordered table-striped table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center py-1 px-2" style="width: 50px; font-size: 0.82rem; line-height: 1.2;">No.</th>
                                    <th class="text-center py-1 px-2" style="font-size: 0.82rem; line-height: 1.2;">Judul</th>
                                    <th class="text-center py-1 px-2" style="font-size: 0.82rem; line-height: 1.2;">Unit Pelaksana</th>
                                    <th class="text-center py-1 px-2" style="font-size: 0.82rem; line-height: 1.2;">Masa Berlaku</th>
                                    <th class="text-center py-1 px-2" style="width: 140px; font-size: 0.82rem; line-height: 1.2;">Status</th>
                                    <th class="text-center py-1 px-2" style="width: 130px; font-size: 0.82rem; line-height: 1.2;">Action</th>
                                </tr>
                            </thead>
                            <tbody id="documentsTableBody">
                                @forelse ($documents as $doc)
                                    @php
                                        $stBadge = match($doc->status) {
                                            'DRAFT' => 'bg-secondary text-white',
                                            'REVIEW', 'LEGAL_REVIEW' => 'bg-info text-dark',
                                            'VERIFIED', 'APPROVED' => 'bg-primary text-white',
                                            'ACTIVE' => 'bg-success text-white',
                                            'WARNING', 'WARNING_EXPIRY' => 'bg-warning text-dark',
                                            'EXPIRED' => 'bg-danger text-white',
                                            'CANCELLED' => 'bg-dark text-white',
                                            default => 'bg-secondary text-white'
                                        };

                                        $docAvailEvents = $fullFsmMatrix[$doc->status] ?? [];

                                        $pdfUrl = null;
                                        $pdfTitle = '';
                                        $rawFile = $doc->signed_file ?: $doc->draft_file;
                                        if (!empty($rawFile)) {
                                            $fPath = str_starts_with($rawFile, 'uploads/') ? $rawFile : 'uploads/documents/' . $rawFile;
                                            if (file_exists(public_path($fPath))) {
                                                $pdfUrl = asset($fPath);
                                                $pdfTitle = 'Unduh Berkas ' . $doc->doc_type;
                                            }
                                        }
                                    @endphp
                                    <tr class="doc-row" data-status="{{ $doc->status }}" data-type="{{ $doc->doc_type }}" data-unit="{{ $doc->unit }}" data-legacy="{{ $doc->is_legacy ? '1' : '0' }}" data-track="{{ $doc->track }}">
                                        <!-- 1. No. -->
                                        <td class="text-center">{{ $loop->iteration }}</td>

                                        <!-- 2. Judul -->
                                        <td>
                                            <div class="small text-muted mb-1 d-flex flex-wrap align-items-center gap-2">
                                                <span><strong>No. Pihak I:</strong> {{ (!empty($doc->doc_number) && $doc->doc_number !== '-') ? $doc->doc_number : '-' }}</span>
                                                <span class="text-secondary"><span class="text-muted">|</span> <strong>No. Pihak II:</strong> {{ (!empty($doc->partner_doc_number) && $doc->partner_doc_number !== '-') ? $doc->partner_doc_number : '-' }}</span>
                                            </div>
                                            <a href="{{ route('simkerma.index', ['menu' => 4, 'view' => 'detail', 'doc' => $doc->doc_id]) }}" class="fw-bold text-dark text-decoration-none d-block mb-1" title="{{ $doc->title }}">
                                                {{ $doc->title ?: $doc->partner_name }}
                                            </a>
                                            <div class="d-flex flex-wrap gap-1 mb-1">
                                                <span class="badge bg-dark">{{ $doc->doc_type }}</span>
                                                <span class="badge bg-light text-dark border">{{ $doc->track }}</span>
                                            </div>
                                            @if (!empty($doc->parent_mou) && $doc->parent_mou !== '-')
                                                <div class="small text-primary">
                                                    <i class="bi bi-link-45deg me-1"></i>Induk: {{ $doc->parent_mou }}
                                                </div>
                                            @endif
                                        </td>

                                        <!-- 3. Unit Pelaksana -->
                                        <td>
                                            <div>
                                                <strong>Unit:</strong> {{ $doc->unit }}<br>
                                                <strong>Mitra:</strong> {{ $doc->partner_name }}
                                                @if (!empty($doc->partner_category))
                                                    <span class="badge bg-light text-secondary border ms-1" style="font-size: 0.65rem;">{{ $doc->partner_category }}</span>
                                                @endif
                                            </div>
                                        </td>

                                        <!-- 4. Masa Berlaku -->
                                        <td>
                                            @if (!empty($doc->start_date) && !empty($doc->end_date))
                                                <div class="fw-medium text-dark">{{ $doc->start_date->format('d/m/Y') }} s/d {{ $doc->end_date->format('d/m/Y') }}</div>
                                            @elseif (!empty($doc->date_signed))
                                                <div class="text-muted small">TTD: {{ $doc->date_signed->format('d/m/Y') }}</div>
                                            @else
                                                <div class="text-muted small">{{ $doc->validity ?: '1 Tahun' }}</div>
                                            @endif
                                            <div class="mt-1">
                                                <span class="badge bg-light text-dark border" style="font-size: 0.7rem;">
                                                    <i class="bi bi-calendar3 me-1"></i>{{ $doc->validity ?: '1 Tahun' }}
                                                </span>
                                            </div>
                                        </td>

                                        <!-- 5. Status -->
                                        <td class="text-center">
                                            <span class="badge {{ $stBadge }} py-1 px-2 d-inline-block fw-bold" style="font-size: 0.75rem;">
                                                {{ $doc->status }}
                                            </span>
                                        </td>

                                        <!-- 6. Action -->
                                        <td class="text-center align-middle">
                                            <div class="d-inline-flex align-items-center justify-content-center gap-1 flex-nowrap">
                                                <a href="{{ route('simkerma.index', ['menu' => 4, 'view' => 'detail', 'doc' => $doc->doc_id]) }}" 
                                                   class="btn btn-sm btn-info text-white p-0 d-inline-flex align-items-center justify-content-center shadow-sm" 
                                                   style="width: 31px; height: 31px; border-radius: 4px;" 
                                                   data-bs-toggle="tooltip" 
                                                   data-bs-placement="top" 
                                                   title="Lihat Detail Naskah">
                                                    <i class="bi bi-eye-fill fs-6"></i>
                                                </a>

                                                <button type="button" 
                                                        class="btn btn-sm btn-primary p-0 d-inline-flex align-items-center justify-content-center shadow-sm" 
                                                        style="width: 31px; height: 31px; border-radius: 4px;" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#fsmModal_{{ $doc->id }}" 
                                                        data-bs-placement="top" 
                                                        title="Kelola Transisi FSM &amp; Status">
                                                    <i class="bi bi-gear-fill fs-6"></i>
                                                </button>

                                                @if ($doc->status === 'DRAFT' || $activeRole === 'MASTER' || $activeRole === 'ADMIN')
                                                    <form method="POST" action="{{ route('simkerma.delete-draft') }}" class="d-inline m-0 p-0" onsubmit="return confirm('KONFIRMASI PENGHAPUSAN DOKUMEN (AKSES MASTER):\n\nApakah Anda yakin ingin menghapus dokumen {{ $doc->doc_id }} [Status: {{ $doc->status }}] ({{ addslashes($doc->partner_name) }}) secara permanen?\n\nPERHATIAN: Dokumen yang dihapus tidak dapat dipulihkan kembali.')">
                                                        @csrf
                                                        <input type="hidden" name="doc_id" value="{{ $doc->doc_id }}">
                                                        <button type="submit" 
                                                                class="btn btn-sm btn-danger p-0 d-inline-flex align-items-center justify-content-center shadow-sm" 
                                                                style="width: 31px; height: 31px; border-radius: 4px;" 
                                                                data-bs-toggle="tooltip" 
                                                                data-bs-placement="top" 
                                                                title="{{ $doc->status === 'DRAFT' ? 'Hapus Draf Dokumen' : 'Hapus Dokumen (Akses MASTER)' }}">
                                                            <i class="bi bi-trash-fill fs-6"></i>
                                                        </button>
                                                    </form>
                                                @else
                                                    <button type="button" 
                                                            class="btn btn-sm btn-light border p-0 d-inline-flex align-items-center justify-content-center text-muted disabled opacity-50" 
                                                            style="width: 31px; height: 31px; border-radius: 4px; cursor: not-allowed;" 
                                                            title="Hanya dapat dihapus pada status DRAFT atau oleh Role MASTER" 
                                                            disabled>
                                                        <i class="bi bi-trash fs-6"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>

                                <!-- MODAL DEDICATED KONSOL FSM UNTUK DOKUMEN INI (UI/FSM-UI.html) -->
                                @include('simkerma.partials.fsm_modal', ['doc' => $doc, 'activeRole' => $activeRole, 'fullFsmMatrix' => $fullFsmMatrix ?? []])

                            @empty
                                <tr id="tableEmptyRow">
                                    <td colspan="6" class="text-center py-5 text-muted">
                                        <i class="bi bi-folder2-open fs-1 d-block mb-2 text-secondary"></i>
                                        <strong class="text-dark">Belum Ada Pengajuan Dokumen Kerja Sama</strong>
                                        <div class="small mt-1">Gunakan tombol <strong>+ Pengajuan MoU Baru</strong> di atas untuk membuat usulan dokumen MoU baru.</div>
                                    </td>
                                </tr>
                            @endforelse

                            <!-- Empty State saat Filter / Pencarian tidak menghasilkan kecocokan -->
                            <tr id="noDocMatchRow" class="d-none">
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <div class="py-3">
                                        <i class="bi bi-search text-secondary display-6 mb-2 d-block"></i>
                                        <h6 class="fw-bold text-dark mb-1">Tidak Ada Dokumen yang Cocok</h6>
                                        <p class="small text-muted mb-3">Tidak ditemukan pengajuan dokumen kerja sama dengan kriteria filter atau kata kunci pencarian yang dipilih.</p>
                                        <button type="button" class="btn btn-outline-primary btn-sm px-3" onclick="resetFilters()">
                                            <i class="bi bi-arrow-counterclockwise me-1"></i> Reset Semua Filter
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        @endif

        {{-- SCRIPT FILTER PENCARIAN SISI KLIEN --}}
        @if (!$isCreatingNew && !$isCreatingDerive && !$isCreatingLegacy && !$isViewingDetail)
        @push('scripts')
        <script>
        // Fitur Filter & Pencarian Tabel Sisi Klien (Format Kerma Lama)
        function filterDocumentsTable() {
            const query = (document.getElementById('tableFilterInput')?.value || '').toLowerCase().trim();
            const statusFilter = (document.getElementById('tableStatusFilter')?.value || '').trim();
            const typeFilter = (document.getElementById('tableTypeFilter')?.value || '').toLowerCase().trim();
            const unitFilter = (document.getElementById('tableUnitFilter')?.value || '').toLowerCase().trim();
            const rows = document.querySelectorAll('#documentsTableBody tr.doc-row');

            let visibleCount = 0;
            rows.forEach(function(row) {
                const text = row.innerText.toLowerCase();
                const rowStatus = (row.getAttribute('data-status') || '').trim();
                const rowType = (row.getAttribute('data-type') || '').toLowerCase().trim();
                const rowUnit = (row.getAttribute('data-unit') || '').toLowerCase().trim();
                const isLegacy = row.getAttribute('data-legacy') === '1' || (row.getAttribute('data-track') || '').toLowerCase().includes('lampau') || (row.getAttribute('data-track') || '').toLowerCase().includes('legacy');

                const matchesQuery = !query || text.includes(query);
                let matchesStatus = !statusFilter || rowStatus === statusFilter;
                if (!matchesStatus && statusFilter) {
                    if (statusFilter === 'REVIEW' && (rowStatus === 'LEGAL_REVIEW' || rowStatus === 'REVIEW')) matchesStatus = true;
                    if (statusFilter === 'VERIFIED' && (rowStatus === 'APPROVED' || rowStatus === 'VERIFIED')) matchesStatus = true;
                    if (statusFilter === 'WARNING' && (rowStatus === 'WARNING_EXPIRY' || rowStatus === 'WARNING')) matchesStatus = true;
                }
                let matchesType = false;
                if (!typeFilter) {
                    matchesType = true;
                } else if (typeFilter === 'legacy') {
                    matchesType = isLegacy;
                } else {
                    matchesType = rowType.includes(typeFilter);
                }
                const matchesUnit = !unitFilter || rowUnit.includes(unitFilter);

                if (matchesQuery && matchesStatus && matchesType && matchesUnit) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });

            const countEl = document.getElementById('visibleDocCount');
            if (countEl) {
                countEl.innerText = visibleCount;
            }

            const noMatchRow = document.getElementById('noDocMatchRow');
            if (noMatchRow) {
                if (visibleCount === 0 && rows.length > 0) {
                    noMatchRow.classList.remove('d-none');
                } else {
                    noMatchRow.classList.add('d-none');
                }
            }
        }

        function resetFilters() {
            if (document.getElementById('tableFilterInput')) document.getElementById('tableFilterInput').value = '';
            if (document.getElementById('tableStatusFilter')) document.getElementById('tableStatusFilter').value = '';
            if (document.getElementById('tableTypeFilter')) document.getElementById('tableTypeFilter').value = '';
            if (document.getElementById('tableUnitFilter')) document.getElementById('tableUnitFilter').value = '';
            filterDocumentsTable();
        }

        function exportTableToExcel(tableID, filename = ''){
            const downloadLink = document.createElement("a");
            const dataType = 'application/vnd.ms-excel';
            const tableSelect = document.getElementById(tableID);
            if (!tableSelect) return;

            // Clone table and remove the action column (last column)
            const clone = tableSelect.cloneNode(true);
            clone.querySelectorAll('tr').forEach(row => {
                if (row.cells.length > 1) {
                    row.deleteCell(row.cells.length - 1);
                }
            });

            const tableHTML = clone.outerHTML.replace(/ /g, '%20');
            filename = filename ? filename + '.xls' : 'data_kerja_sama.xls';

            document.body.appendChild(downloadLink);
            if (navigator.msSaveOrOpenBlob) {
                const blob = new Blob(['\ufeff', clone.outerHTML], { type: dataType });
                navigator.msSaveOrOpenBlob(blob, filename);
            } else {
                downloadLink.href = 'data:' + dataType + ', ' + tableHTML;
                downloadLink.download = filename;
                downloadLink.click();
            }
            document.body.removeChild(downloadLink);
        }
        </script>
        @endpush
        @endif

    {{-- ===================================================================================== --}}
    {{-- 5. PEMANTAUAN PELAKSANAAN KERJA SAMA (SINKRON PENUH DENGAN MENU 04 & BASIS DATA MYSQL) --}}
    {{-- ===================================================================================== --}}
    @elseif ($activeMenu === 5)
        @php
            $totalActiveDocs = $documents->whereIn('status', ['ACTIVE', 'WARNING'])->count();
            $totalActivities = $activities->count();
            $runningActivities = $activities->where('execution_status', 'SEDANG_BERJALAN')->count();
            $completedActivities = $activities->where('execution_status', 'SELESAI')->count();
            $pendingActivities = $activities->where('execution_status', 'BELUM_DIMULAI')->count();
            $delayedActivities = $activities->where('execution_status', 'TERTUNDA')->count();
        @endphp

        <!-- 5 KARTU METRIK RINGKASAN PELAKSANAAN (PERSIS TAMPILAN UI_REFERENCE) -->
        <div class="row g-3 mb-4 row-cols-2 row-cols-md-3 row-cols-xl-5">
            <!-- 1. Total Kegiatan -->
            <div class="col">
                <div class="card border shadow-sm p-3 bg-white h-100">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 48px; height: 48px; background-color: #e0f2fe; color: #0284c7;">
                            <i class="bi bi-list-task fs-4"></i>
                        </div>
                        <div>
                            <div class="text-muted small fw-medium" style="font-size: 0.78rem;">Total Kegiatan</div>
                            <h3 class="mb-0 fw-bold text-dark">{{ $totalActivities }}</h3>
                        </div>
                    </div>
                </div>
            </div>
            <!-- 2. Berjalan -->
            <div class="col">
                <div class="card border shadow-sm p-3 bg-white h-100">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 48px; height: 48px; background-color: #dcfce7; color: #16a34a;">
                            <i class="bi bi-play-fill fs-3"></i>
                        </div>
                        <div>
                            <div class="text-muted small fw-medium" style="font-size: 0.78rem;">Berjalan</div>
                            <h3 class="mb-0 fw-bold text-dark">{{ $runningActivities }}</h3>
                        </div>
                    </div>
                </div>
            </div>
            <!-- 3. Belum Dimulai -->
            <div class="col">
                <div class="card border shadow-sm p-3 bg-white h-100">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 48px; height: 48px; background-color: #fef3c7; color: #d97706;">
                            <i class="bi bi-clock fs-4"></i>
                        </div>
                        <div>
                            <div class="text-muted small fw-medium" style="font-size: 0.78rem;">Belum Dimulai</div>
                            <h3 class="mb-0 fw-bold text-dark">{{ $pendingActivities }}</h3>
                        </div>
                    </div>
                </div>
            </div>
            <!-- 4. Selesai -->
            <div class="col">
                <div class="card border shadow-sm p-3 bg-white h-100">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 48px; height: 48px; background-color: #eff6ff; color: #2563eb;">
                            <i class="bi bi-check-lg fs-3"></i>
                        </div>
                        <div>
                            <div class="text-muted small fw-medium" style="font-size: 0.78rem;">Selesai</div>
                            <h3 class="mb-0 fw-bold text-dark">{{ $completedActivities }}</h3>
                        </div>
                    </div>
                </div>
            </div>
            <!-- 5. Tertunda -->
            <div class="col">
                <div class="card border shadow-sm p-3 bg-white h-100">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 48px; height: 48px; background-color: #fee2e2; color: #dc2626;">
                            <i class="bi bi-pause-fill fs-3"></i>
                        </div>
                        <div>
                            <div class="text-muted small fw-medium" style="font-size: 0.78rem;">Tertunda</div>
                            <h3 class="mb-0 fw-bold text-dark">{{ $delayedActivities }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card card-wireframe shadow-sm border mb-4">
            <div class="card-header bg-white py-3 border-bottom d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
                <div>
                    <div class="d-flex align-items-center gap-2">
                        <h6 class="mb-0 fw-bold text-uppercase text-dark">
                            <i class="bi bi-calendar-check text-primary me-1"></i> Pemantauan Pelaksanaan Kerja Sama
                        </h6>
                        <span class="badge bg-primary text-white font-monospace" style="font-size: 0.65rem;">SINKRON MENU 04 &bull; MYSQL</span>
                    </div>
                    <small class="text-muted">Memastikan Perjanjian Benar-Benar Dilaksanakan Berdasarkan Naskah Rujukan Resmi (MoU / MoA / IA / Dokumen Lampau)</small>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('simkerma.index', ['menu' => 4]) }}" class="btn btn-outline-secondary btn-sm fw-semibold">
                        <i class="bi bi-arrow-left-right me-1"></i> Buka Menu 04 (Pengajuan)
                    </a>
                    <button type="button" class="btn btn-primary btn-sm fw-semibold shadow-sm" data-bs-toggle="modal" data-bs-target="#modalCreateActivity">
                        <i class="bi bi-plus-lg me-1"></i> + Tambah Kegiatan
                    </button>
                </div>
            </div>

            <!-- FILTER & SEARCH BAR SISI KLIEN (SESUAI UI_REFERENCE) -->
            <div class="card-body border-bottom bg-white py-3">
                <div class="row g-2 align-items-center justify-content-between">
                    <div class="col-12 col-md-auto">
                        <div class="d-flex flex-wrap align-items-center gap-1 small">
                            <span class="text-muted fw-bold me-2" style="font-size: 0.78rem;">Status Pelaksanaan:</span>
                            <button type="button" class="btn btn-primary btn-sm py-1 px-3 filter-act-btn active" data-filter="ALL">Semua ({{ $totalActivities }})</button>
                            <button type="button" class="btn btn-outline-secondary btn-sm py-1 px-2 filter-act-btn" data-filter="SEDANG_BERJALAN">Berjalan ({{ $runningActivities }})</button>
                            <button type="button" class="btn btn-outline-secondary btn-sm py-1 px-2 filter-act-btn" data-filter="SELESAI">Selesai ({{ $completedActivities }})</button>
                            <button type="button" class="btn btn-outline-secondary btn-sm py-1 px-2 filter-act-btn" data-filter="BELUM_DIMULAI">Belum Dimulai ({{ $pendingActivities }})</button>
                            <button type="button" class="btn btn-outline-secondary btn-sm py-1 px-2 filter-act-btn" data-filter="TERTUNDA">Tertunda ({{ $delayedActivities }})</button>
                        </div>
                    </div>
                    <div class="col-12 col-md-4">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                            <input type="text" id="activitySearchInput" class="form-control border-start-0" placeholder="Cari kegiatan, mitra, atau judul...">
                        </div>
                    </div>
                </div>
            </div>

            <!-- TABEL REALISASI PELAKSANAAN KEGIATAN TERHUBUNG MENU 04 -->
            <div class="table-responsive">
                <table class="table table-wireframe table-hover table-sm align-middle mb-0 small" id="activityTable">
                    <thead class="bg-light">
                        <tr>
                            <th style="min-width: 165px;">Daftar Kegiatan &amp; Dokumen Rujukan (Menu 04)</th>
                            <th style="min-width: 115px;">Mitra &amp; Unit PJ</th>
                            <th style="min-width: 85px;">Target &amp; Realisasi</th>
                            <th style="min-width: 100px;">Peserta / Pihak Terlibat</th>
                            <th style="min-width: 105px;">Luaran IKU 6</th>
                            <th style="min-width: 95px;">Kontribusi / Anggaran</th>
                            <th style="min-width: 90px;">Bukti Berkas</th>
                            <th style="min-width: 90px;" class="text-center">Status &amp; QR FSM</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($activities as $act)
                            @php
                                $doc = $act->document;
                                $statusBadgeClass = match($doc->status ?? '') {
                                    'DRAFT' => 'bg-secondary text-white',
                                    'REVIEW' => 'bg-info text-dark',
                                    'VERIFIED' => 'bg-primary text-white',
                                    'ACTIVE' => 'bg-success text-white',
                                    'WARNING' => 'bg-warning text-dark',
                                    'EXPIRED' => 'bg-danger text-white',
                                    'CANCELLED' => 'bg-dark text-white',
                                    default => 'bg-secondary text-white'
                                };

                                $actStatusBadge = match($act->execution_status) {
                                    'SEDANG_BERJALAN' => 'bg-success text-success',
                                    'SELESAI' => 'bg-success text-success',
                                    'BELUM_DIMULAI' => 'bg-warning text-dark',
                                    'TERTUNDA' => 'bg-info text-dark',
                                    default => 'bg-secondary text-secondary'
                                };

                                $actStatusLabel = match($act->execution_status) {
                                    'SEDANG_BERJALAN' => 'Berjalan',
                                    'SELESAI' => 'Selesai',
                                    'BELUM_DIMULAI' => 'Belum Dimulai',
                                    'TERTUNDA' => 'Tertunda',
                                    default => $act->execution_status
                                };
                            @endphp
                            <tr class="activity-row" data-status="{{ $act->execution_status }}">
                                <!-- 1. KEGIATAN & DOKUMEN RUJUKAN (MENU 04) -->
                                <td>
                                    <div class="fw-bold text-dark fs-6 mb-1">{{ $act->activity_name }}</div>
                                    <div class="p-2 rounded bg-light border" style="font-size: 0.73rem;">
                                        <div class="d-flex align-items-center justify-content-between mb-1">
                                            <span class="text-muted fw-bold">DOKUMEN RUJUKAN:</span>
                                            <span class="badge {{ $statusBadgeClass }}" style="font-size: 0.62rem;">
                                                FSM: {{ $doc->status ?? '-' }}
                                            </span>
                                        </div>
                                        <div class="text-dark">
                                            <strong>No. Pihak I:</strong> {{ (!empty($doc->doc_number) && $doc->doc_number !== '-') ? $doc->doc_number : '-' }}
                                        </div>
                                        <div class="text-secondary">
                                            <strong>No. Pihak II:</strong> {{ (!empty($doc->partner_doc_number) && $doc->partner_doc_number !== '-') ? $doc->partner_doc_number : '-' }}
                                        </div>
                                        @if (($doc->status ?? '') === 'WARNING')
                                            <div class="alert alert-warning py-1 px-2 my-1 border-0 rounded text-dark" style="font-size: 0.68rem;">
                                                <i class="bi bi-clock-history text-warning-emphasis me-1"></i>
                                                <strong>Peringatan H-90:</strong> Masa berlaku naskah segera berakhir.
                                                <a href="{{ route('simkerma.index', ['menu' => 4, 'doc' => $doc->doc_id]) }}" class="d-block fw-bold text-dark text-decoration-underline mt-1" title="Buka form perpanjangan dokumen">
                                                    &rarr; Perpanjang (No. Pihak 1, Pihak 2 &amp; Upload PDF)
                                                </a>
                                            </div>
                                        @endif
                                        <div class="text-muted mt-1 d-flex align-items-center justify-content-between">
                                            <span>{{ $doc->doc_type ?? 'Dokumen Kerja Sama' }}</span>
                                            <a href="{{ route('simkerma.index', ['menu' => 4, 'doc' => $doc->doc_id]) }}" class="text-decoration-none fw-semibold text-primary" title="Buka dan kelola naskah ini di Menu 04">
                                                <i class="bi bi-box-arrow-up-right me-1"></i>Kelola di Menu 04
                                            </a>
                                        </div>
                                    </div>
                                </td>

                                <!-- 2. MITRA & UNIT PJ -->
                                <td>
                                    <div class="fw-bold text-dark">{{ $doc->partner_name ?? '-' }}</div>
                                    <span class="badge bg-light text-secondary border mb-1 text-wrap text-start" style="font-size: 0.65rem; max-width: 130px; line-height: 1.25;">{{ $doc->partner_category ?? 'Mitra Eksternal' }}</span>
                                    <div class="small text-muted">
                                        <i class="bi bi-person-badge me-1"></i>PJ: <strong>{{ $doc->unit ?? '-' }}</strong>
                                        @if(!empty($doc->collab_unit))
                                            <span class="d-block" style="font-size: 0.68rem;">+ {{ $doc->collab_unit }}</span>
                                        @endif
                                    </div>
                                    <div class="small text-secondary mt-1" style="font-size: 0.72rem;">
                                        <i class="bi bi-calendar-event me-1 text-primary"></i>{{ $act->schedule_start ?: '-' }}
                                        @if($act->schedule_end)
                                            s/d {{ $act->schedule_end }}
                                        @endif
                                    </div>
                                </td>

                                <!-- 3. TARGET & REALISASI -->
                                <td>
                                    <div class="text-muted" style="font-size: 0.72rem;">Target:</div>
                                    <div class="text-dark">{{ $act->target_qty ?: '-' }}</div>
                                    <div class="text-muted mt-1" style="font-size: 0.72rem;">Realisasi:</div>
                                    <div class="fw-bold text-success">{{ $act->realized_qty ?: '-' }}</div>
                                </td>

                                <!-- 4. PESERTA & PIHAK TERLIBAT -->
                                <td>
                                    <div class="text-dark small" style="max-width: 95px; line-height: 1.3;">{{ $act->parties_involved ?: '-' }}</div>
                                </td>

                                <!-- 5. LUARAN IKU 6 -->
                                <td>
                                    <div class="text-dark small" style="max-width: 95px; line-height: 1.3;">{{ $act->outputs ?: '-' }}</div>
                                </td>

                                <!-- 6. ANGGARAN / KONTRIBUSI -->
                                <td>
                                    <div class="small text-dark fw-medium" style="max-width: 100px; line-height: 1.3;">{{ $act->budget_info ?: '-' }}</div>
                                </td>

                                <!-- 7. BUKTI BERKAS -->
                                <td>
                                    @if ($act->evidence_file)
                                        @php
                                            $actFileName = basename($act->evidence_file);
                                        @endphp
                                        <div class="d-flex flex-column gap-1">
                                            <a href="{{ asset($act->evidence_file) }}" target="_blank" class="text-decoration-none text-dark fw-semibold d-inline-block text-truncate" style="font-size: 0.72rem; max-width: 85px; vertical-align: middle;" title="{{ $actFileName }}">
                                                <i class="bi bi-file-earmark-pdf text-danger me-1"></i>{{ $actFileName }}
                                            </a>
                                            @if ($act->evidence_photos)
                                                <span class="badge bg-light text-secondary border d-inline-block text-truncate" style="font-size: 0.62rem; max-width: 85px;">
                                                    <i class="bi bi-image me-1"></i>Foto
                                                </span>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-muted small">-</span>
                                    @endif
                                </td>

                                <!-- 8. STATUS & AKSI QR SINKRON -->
                                <td class="text-center">
                                    <span class="badge {{ $actStatusBadge }} mb-2 d-block">{{ $actStatusLabel }}</span>
                                    <div class="d-flex justify-content-center gap-1">
                                        @if ($doc)
                                            <button type="button" class="btn btn-outline-dark btn-sm py-0 px-2" style="font-size: 0.68rem;" title="Perbesar QR Code Naskah Resmi" onclick="showQrModal('{{ $doc->reg_id }}', '{{ $doc->doc_number ?: $doc->doc_id }}', '{{ addslashes(str_replace(["\r", "\n"], ' ', $doc->scope)) }}', '{{ addslashes($doc->partner_name) }}', '{{ $doc->status }}', '{{ $doc->date_signed ? $doc->date_signed->format('d M Y') : date('d M Y') }}', '{{ addslashes($doc->partner_doc_number ?? '') }}')">
                                                <i class="bi bi-qr-code me-1"></i>QR
                                            </button>
                                            <a href="{{ route('simkerma.verify', ['reg_id' => $doc->reg_id]) }}" target="_blank" class="btn btn-outline-primary btn-sm py-0 px-2" style="font-size: 0.68rem;" title="Lembar Verifikasi Publik">
                                                <i class="bi bi-shield-check"></i>
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4 text-muted">
                                    <i class="bi bi-folder-x fs-2 d-block mb-2"></i>
                                    Belum ada catatan pelaksanaan kegiatan. Klik tombol <strong>Catat Kegiatan Baru</strong> untuk menambahkan realisasi dari dokumen Menu 04.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- MODAL INTERAKTIF: CATAT REALISASI KEGIATAN BARU TERHUBUNG DOKUMEN MENU 04 -->
        <div class="modal fade" id="modalCreateActivity" tabindex="-1" aria-labelledby="modalCreateActivityLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content border-0 shadow-lg">
                    <form action="{{ route('simkerma.store-activity') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-header bg-dark text-white py-3">
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-calendar-plus text-warning fs-5"></i>
                                <h6 class="modal-title fw-bold mb-0 text-uppercase" id="modalCreateActivityLabel">Pencatatan Realisasi Pelaksanaan Kerja Sama</h6>
                            </div>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body p-4">
                            <div class="alert alert-info py-2 px-3 small border mb-3">
                                <i class="bi bi-info-circle-fill me-1"></i> <strong>Integrasi Dokumen Menu 04:</strong> Setiap kegiatan pelaksanaan wajib merujuk pada salah satu dokumen kemitraan yang telah terdaftar dalam sistem SIMKERMA UNIPAR.
                            </div>

                            <div class="row g-3 small">
                                <!-- Pilih Dokumen Rujukan -->
                                <div class="col-12">
                                    <label class="form-label fw-bold text-dark">Pilih Dokumen Rujukan Kerja Sama (Menu 04) <span class="text-danger">*</span></label>
                                    <select name="document_id" class="form-select form-select-sm" required>
                                        <option value="">-- Pilih Dokumen Rujukan Resmi --</option>
                                        @foreach ($documents as $d)
                                            <option value="{{ $d->id }}">
                                                [{{ $d->doc_id }}] {{ $d->partner_name }} &bull; {{ $d->doc_type }} (Status FSM: {{ $d->status }}) - No: {{ $d->doc_number ?: 'Dalam Proses' }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Nama Kegiatan -->
                                <div class="col-12">
                                    <label class="form-label fw-bold text-dark">Nama Kegiatan Pelaksanaan <span class="text-danger">*</span></label>
                                    <input type="text" name="activity_name" class="form-control form-control-sm" required placeholder="Contoh: Magang Industri AI Semester Ganjil Batch 1">
                                </div>

                                <!-- Jadwal Mulai & Selesai -->
                                <div class="col-sm-6">
                                    <label class="form-label fw-bold text-dark">Jadwal Mulai</label>
                                    <input type="text" name="schedule_start" class="form-control form-control-sm" placeholder="Contoh: 01 Feb 2026">
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label fw-bold text-dark">Jadwal Selesai</label>
                                    <input type="text" name="schedule_end" class="form-control form-control-sm" placeholder="Contoh: 31 Jul 2026">
                                </div>

                                <!-- Target & Realisasi -->
                                <div class="col-sm-6">
                                    <label class="form-label fw-bold text-dark">Target Kuantitatif</label>
                                    <input type="text" name="target_qty" class="form-control form-control-sm" placeholder="Contoh: 20 Mahasiswa">
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label fw-bold text-dark">Realisasi Lapangan</label>
                                    <input type="text" name="realized_qty" class="form-control form-control-sm" placeholder="Contoh: 18 Mahasiswa (90%)">
                                </div>

                                <!-- Pihak Terlibat -->
                                <div class="col-12">
                                    <label class="form-label fw-bold text-dark">Pihak / Peserta Terlibat</label>
                                    <textarea name="parties_involved" class="form-control form-control-sm" rows="2" placeholder="Sebutkan mahasiswa, dosen pembina, dan mentor/staf mitra yang terlibat langsung"></textarea>
                                </div>

                                <!-- Luaran IKU 6 -->
                                <div class="col-12">
                                    <label class="form-label fw-bold text-dark">Luaran &amp; Manfaat Riil (IKU 6)</label>
                                    <textarea name="outputs" class="form-control form-control-sm" rows="2" placeholder="Contoh: Laporan Magang, 2 Sertifikat Kompetensi, 1 Prototype Sistem Informasi"></textarea>
                                </div>

                                <!-- Anggaran & Status Pelaksanaan -->
                                <div class="col-sm-6">
                                    <label class="form-label fw-bold text-dark">Anggaran / Kontribusi Mitra</label>
                                    <input type="text" name="budget_info" class="form-control form-control-sm" placeholder="Contoh: Uang Saku Mitra Rp 45.000.000">
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label fw-bold text-dark">Status Pelaksanaan <span class="text-danger">*</span></label>
                                    <select name="execution_status" class="form-select form-select-sm" required>
                                        <option value="SEDANG_BERJALAN" selected>Sedang Berjalan</option>
                                        <option value="SELESAI">Selesai / Terarsip</option>
                                        <option value="BELUM_DIMULAI">Belum Dimulai</option>
                                        <option value="TERTUNDA">Tertunda</option>
                                    </select>
                                </div>

                                <!-- Bukti Berkas (Unggah File Fisik - Sinkron Menu 03) -->
                                <div class="col-sm-6">
                                    <label class="form-label fw-bold text-dark">Unggah Laporan Pelaksanaan (PDF/Word)</label>
                                    <input type="file" name="evidence_file_upload" class="form-control form-control-sm" accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png">
                                    <div class="text-muted mt-0.5" style="font-size: 0.68rem;">Otomatis tersimpan ke Repositori Menu 03</div>
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label fw-bold text-dark">Unggah Dokumentasi / Sertifikat (Opsional)</label>
                                    <input type="file" name="evidence_photos_upload" class="form-control form-control-sm" accept=".jpg,.jpeg,.png,.pdf,.zip">
                                </div>

                                <div class="col-12">
                                    <label class="form-label fw-bold text-dark">Catatan Tambahan</label>
                                    <textarea name="notes" class="form-control form-control-sm" rows="2" placeholder="Catatan evaluasi atau kendala pelaksanaan (opsional)"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer bg-light py-2 px-3">
                            <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary btn-sm fw-bold px-3">
                                <i class="bi bi-save me-1"></i> Simpan &amp; Sinkronkan ke Menu 04
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        @push('scripts')
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Filter Status Kegiatan Pelaksanaan
            const filterBtns = document.querySelectorAll('.filter-act-btn');
            const rows = document.querySelectorAll('.activity-row');
            const searchInput = document.getElementById('activitySearchInput');

            function applyFilters() {
                const activeBtn = document.querySelector('.filter-act-btn.active');
                const selectedFilter = activeBtn ? activeBtn.getAttribute('data-filter') : 'ALL';
                const query = searchInput ? searchInput.value.toLowerCase().trim() : '';

                rows.forEach(function(row) {
                    const rowStatus = row.getAttribute('data-status');
                    const text = row.innerText.toLowerCase();

                    const matchesStatus = (selectedFilter === 'ALL' || rowStatus === selectedFilter);
                    const matchesSearch = (!query || text.includes(query));

                    if (matchesStatus && matchesSearch) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            }

            filterBtns.forEach(function(btn) {
                btn.addEventListener('click', function() {
                    filterBtns.forEach(b => {
                        b.classList.remove('btn-dark', 'active');
                        b.classList.add('btn-outline-secondary');
                    });
                    this.classList.remove('btn-outline-secondary');
                    this.classList.add('btn-dark', 'active');
                    applyFilters();
                });
            });

            if (searchInput) {
                searchInput.addEventListener('input', applyFilters);
            }
        });
        </script>
        @endpush

    {{-- ===================================================================================== --}}
    {{-- 6. MONITORING MASA BERLAKU DAN EVALUASI                                               --}}
    {{-- ===================================================================================== --}}
    @elseif ($activeMenu === 6)
        @php
            $warningDocs = $documents->whereIn('status', ['WARNING', 'EXPIRED']);
            $noActivityDocs = $documents->filter(fn($d) => $d->activities->isEmpty());
            $evaluationLogs = $documents->flatMap->auditTrails->where('event', 'EVALUATION_RECORDED')->sortByDesc('id');
        @endphp

        <div class="row g-4">
            <!-- Kolom Kiri: Pengingat Masa Berlaku & Naskah Tanpa Realisasi -->
            <div class="col-lg-6">
                <div class="card card-wireframe h-100 shadow-sm border">
                    <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-0 fw-bold text-uppercase">Pengingat Masa Berlaku</h6>
                            <small class="text-muted">Membantu Biro Kerja Sama Mengidentifikasi Perjanjian Krusial</small>
                        </div>
                        <span class="tag-wireframe"><i class="bi bi-clock-history me-1"></i>PENGAWASAN H-90 / H-60</span>
                    </div>
                    <div class="card-body">
                        
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label class="form-label fw-bold small text-dark mb-0">Daftar Pengingat Sebelum Masa Berlaku Berakhir:</label>
                            <span class="badge bg-secondary">{{ $warningDocs->count() }} Dokumen Terdeteksi</span>
                        </div>
                        
                        @forelse ($warningDocs as $wDoc)
                            <div class="card bg-light border mb-3">
                                <div class="card-body p-3 small">
                                    <div class="d-flex justify-content-between align-items-start mb-1">
                                        <div>
                                            <strong class="text-dark fs-6">{{ $wDoc->partner_name }}</strong>
                                            <span class="badge bg-light text-dark border ms-1">{{ $wDoc->doc_type }}</span>
                                        </div>
                                        @if ($wDoc->status === 'WARNING')
                                            <span class="badge bg-warning text-dark border"><i class="bi bi-exclamation-triangle-fill me-1"></i>{{ $wDoc->warning_threshold ?? 'Peringatan Masa Berlaku' }}</span>
                                        @else
                                            <span class="badge bg-danger"><i class="bi bi-x-octagon-fill me-1"></i>Kedaluwarsa (Expired)</span>
                                        @endif
                                    </div>
                                    <div class="text-muted small">
                                        <strong>No. UNIPAR:</strong> {{ $wDoc->doc_number ?: $wDoc->doc_id }}
                                        @if (!empty($wDoc->partner_doc_number))
                                            &bull; <strong>No. Mitra:</strong> {{ $wDoc->partner_doc_number }}
                                        @endif
                                        <br>
                                        <strong>Masa Berlaku:</strong> {{ $wDoc->validity ?: 'Belum Ditetapkan' }}
                                        @if ($wDoc->date_signed)
                                            (Tgl Pengesahan: {{ \Carbon\Carbon::parse($wDoc->date_signed)->format('d M Y') }})
                                        @endif
                                    </div>
                                    <div class="mt-2 text-dark">
                                        Rekomendasi Tindak Lanjut: 
                                        @if ($wDoc->status === 'WARNING')
                                            <strong>Perpanjangan (Inisiasi Adendum / Perpanjangan Masa Berlaku)</strong>
                                        @else
                                            <strong>Evaluasi Penghentian atau Perjanjian Baru</strong>
                                        @endif
                                    </div>
                                    <div class="mt-3 d-flex flex-wrap gap-2">
                                        <a href="{{ route('simkerma.index', ['menu' => 4, 'doc' => $wDoc->doc_id]) }}" class="btn btn-dark btn-sm py-1 px-3 fw-bold">
                                            <i class="bi bi-arrow-repeat me-1"></i> Inisiasi Adendum di Menu 04
                                        </a>
                                        <button type="button" class="btn btn-outline-secondary btn-sm py-1 px-2" onclick="alert('Notifikasi surat pengingat resmi berhasil digenerate dan dikirimkan ke kontak mitra {{ addslashes($wDoc->partner_name) }}.')">
                                            <i class="bi bi-envelope me-1"></i> Kirim Pengingat Mitra
                                        </button>
                                        <a href="{{ route('simkerma.index', ['menu' => 5]) }}" class="btn btn-outline-dark btn-sm py-1 px-2">
                                            <i class="bi bi-calendar-check me-1"></i> Pantau Kegiatan
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="alert alert-light border small text-muted mb-3">
                                <i class="bi bi-check-circle text-success me-1"></i> Seluruh dokumen kerja sama saat ini berada dalam periode masa aktif normal. Tidak ada naskah yang memasuki ambang peringatan (H-90/H-60) atau kedaluwarsa.
                            </div>
                        @endforelse

                        <div class="border-top pt-3 mt-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label class="form-label fw-bold small text-dark mb-0">
                                    <i class="bi bi-exclamation-circle text-danger me-1"></i> Daftar Kerja Sama Tanpa Realisasi Kegiatan:
                                </label>
                                <span class="badge bg-light text-danger border">{{ $noActivityDocs->count() }} Naskah</span>
                            </div>

                            @if ($noActivityDocs->isNotEmpty())
                                <div class="alert alert-light border small text-muted mb-0">
                                    <ul class="mb-2 ps-3">
                                        @foreach ($noActivityDocs->take(5) as $nad)
                                            <li class="mb-1">
                                                <strong>{{ $nad->partner_name }}</strong> 
                                                <span class="badge bg-light text-dark border">{{ $nad->doc_type }}</span>
                                                <span class="text-secondary">({{ $nad->unit }})</span> &bull; 
                                                Status: <span class="fw-semibold text-dark">{{ $nad->status }}</span>
                                                <a href="{{ route('simkerma.index', ['menu' => 5]) }}" class="text-dark fw-bold ms-2 text-decoration-underline" title="Catat kegiatan untuk dokumen ini">
                                                    [ + Catat Realisasi ]
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                    @if ($noActivityDocs->count() > 5)
                                        <div class="small text-secondary mb-2 fst-italic">...dan {{ $noActivityDocs->count() - 5 }} dokumen kemitraan lainnya belum memiliki realisasi kegiatan tercatat.</div>
                                    @endif
                                    <div class="small text-danger border-top pt-2">
                                        <i class="bi bi-info-circle me-1"></i> Rekomendasi Biro Kerja Sama UNIPAR: Naskah tanpa realisasi kegiatan dapat diterbitkan surat konfirmasi komitmen atau direkomendasikan untuk penghentian (terminasi).
                                    </div>
                                </div>
                            @else
                                <div class="alert alert-success border small mb-0">
                                    <i class="bi bi-check2-all me-1"></i> Seluruh dokumen kerja sama telah memiliki catatan realisasi kegiatan pelaksanaan.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Kolom Kanan: Evaluasi Capaian Terhadap Target & Rekomendasi Biro -->
            <div class="col-lg-6">
                <div class="card card-wireframe h-100 shadow-sm border">
                    <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-0 fw-bold text-uppercase">Evaluasi Capaian Terhadap Target</h6>
                            <small class="text-muted">Catatan Kendala, Rekomendasi &amp; Riwayat Evaluasi</small>
                        </div>
                        <span class="tag-wireframe"><i class="bi bi-pencil-square me-1"></i>FORM EVALUASI</span>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('simkerma.store-evaluation') }}" method="POST" class="small">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label fw-bold">Pilih Dokumen yang Dievaluasi: <span class="text-danger">*</span></label>
                                <select name="document_id" class="form-select form-select-sm" required>
                                    <option value="" disabled selected>-- Pilih Dokumen Kemitraan UNIPAR --</option>
                                    @foreach ($documents as $doc)
                                        <option value="{{ $doc->id }}" {{ (isset($currentDoc) && $currentDoc->id === $doc->id) ? 'selected' : '' }}>
                                            {{ $doc->doc_id }} - {{ $doc->partner_name }} ({{ $doc->doc_type }} - {{ $doc->status }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Evaluasi Capaian Terhadap Target: <span class="text-danger">*</span></label>
                                <select name="evaluation_score" class="form-select form-select-sm" required>
                                    <option value="Target Tercapai Penuh (Sangat Baik - Capaian > 90%)">Target Tercapai Penuh (Sangat Baik - Capaian > 90%)</option>
                                    <option value="Target Tercapai Sebagian (Cukup - Capaian 50% - 90%)">Target Tercapai Sebagian (Cukup - Capaian 50% - 90%)</option>
                                    <option value="Target Belum Tercapai (Kurang - Capaian < 50%)">Target Belum Tercapai (Kurang - Capaian < 50%)</option>
                                    <option value="Belum Ada Realisasi Kegiatan (0%)">Belum Ada Realisasi Kegiatan (0%)</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Catatan Kendala &amp; Rencana Tindak Lanjut:</label>
                                <textarea name="notes" class="form-control form-control-sm" rows="3" placeholder="Tuliskan kendala komunikasi, kendala pembiayaan, atau hambatan operasional lapangan..."></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Rekomendasi Akhir Biro Kerja Sama: <span class="text-danger">*</span></label>
                                <div class="d-flex flex-wrap gap-3 pt-1">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="recommendation" id="rek1" value="Perpanjangan (Inisiasi Adendum)" checked>
                                        <label class="form-check-label fw-semibold" for="rek1">Perpanjangan</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="recommendation" id="rek2" value="Revisi Klausul (Penyesuaian MoA/IA)">
                                        <label class="form-check-label fw-semibold" for="rek2">Revisi Klausul</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="recommendation" id="rek3" value="Penghentian (Terminasi Kerja Sama)">
                                        <label class="form-check-label fw-semibold" for="rek3">Penghentian</label>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-dark btn-sm w-100 fw-bold py-2">
                                <i class="bi bi-save me-1"></i> Simpan Riwayat Evaluasi Dokumen
                            </button>
                        </form>

                        <div class="border-top pt-3 mt-4">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label class="form-label fw-bold small text-dark mb-0">
                                    <i class="bi bi-clock-history me-1"></i> Riwayat Evaluasi Tersimpan (Audit Trail):
                                </label>
                                <span class="badge bg-secondary">{{ $evaluationLogs->count() }} Catatan</span>
                            </div>

                            @if ($evaluationLogs->isNotEmpty())
                                <div class="list-group list-group-flush border rounded small" style="max-height: 220px; overflow-y: auto;">
                                    @foreach ($evaluationLogs as $evLog)
                                        <div class="list-group-item p-2">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <strong class="text-dark">{{ $evLog->document->partner_name ?? 'Dokumen #'.$evLog->document_id }}</strong>
                                                <small class="text-muted">{{ $evLog->timestamp_wita }}</small>
                                            </div>
                                            <div class="text-secondary mt-1">{{ $evLog->notes }}</div>
                                            <div class="text-muted small mt-1">Dicatat oleh: <em>{{ $evLog->actor }}</em></div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="alert alert-light border small text-muted mb-0">
                                    <i class="bi bi-info-circle me-1"></i> Belum ada riwayat evaluasi berkala yang dicatat. Gunakan formulir di atas untuk menyimpan evaluasi resmi pertama.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

    {{-- ===================================================================================== --}}
    {{-- 7. LAPORAN DAN ANALITIK (WF-07)                                                       --}}
    {{-- ===================================================================================== --}}
    @elseif ($activeMenu === 7)
        @php
            $totalAllDocs = $documents->count();
            $activeCount = $documents->where('status', 'ACTIVE')->count();
            $warningCount = $documents->where('status', 'WARNING')->count();
            $expiredCount = $documents->whereIn('status', ['EXPIRED', 'CANCELLED'])->count();
            $reviewCount = $documents->where('status', 'REVIEW')->count();
            $verifiedCount = $documents->where('status', 'VERIFIED')->count();

            $totalActs = $activities->count();
            $doneActs = $activities->where('execution_status', 'SELESAI')->count();
            $runningActs = $activities->where('execution_status', 'SEDANG_BERJALAN')->count();
            $pendingActs = $activities->whereIn('execution_status', ['BELUM_DIMULAI', 'TERTUNDA'])->count();
            $outputActs = $activities->whereNotNull('outputs')->where('outputs', '!=', '-')->count();

            $allYears = $documents->map(function($d) {
                return $d->date_signed ? \Carbon\Carbon::parse($d->date_signed)->format('Y') : ($d->created_at ? \Carbon\Carbon::parse($d->created_at)->format('Y') : '2026');
            })->unique()->sortDesc();
            $allUnits = $documents->pluck('unit')->unique()->filter()->sort();
            $allTypes = $documents->pluck('doc_type')->unique()->filter()->sort();
        @endphp

        <div class="card card-wireframe shadow-sm border">
            <div class="card-header bg-white py-3 border-bottom d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2">
                <div>
                    <h6 class="mb-0 fw-bold text-uppercase">Pusat Laporan &amp; Analitik Kemitraan UNIPAR</h6>
                    <small class="text-muted">Mendukung Pengambilan Keputusan Pimpinan &amp; Akreditasi Institusi (IKU 6 Kemendikbudristek)</small>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-dark btn-sm fw-semibold" id="btnExportCSV">
                        <i class="bi bi-file-earmark-excel me-1"></i> Ekspor CSV / Excel (.csv)
                    </button>
                    <button type="button" class="btn btn-outline-secondary btn-sm fw-semibold" onclick="window.print()">
                        <i class="bi bi-printer me-1"></i> Cetak Dokumen Resmi
                    </button>
                </div>
            </div>

            <!-- FILTER KRITERIA LAPORAN (SESUAI SPESIFIKASI DOKUMEN RESMI) -->
            <div class="card-body border-bottom bg-light py-3">
                <div class="row g-2 small">
                    <div class="col-md-3">
                        <label class="form-label fw-bold mb-1">Berdasarkan Periode / Tahun:</label>
                        <select id="reportFilterYear" class="form-select form-select-sm">
                            <option value="ALL">Semua Tahun</option>
                            @foreach ($allYears as $yr)
                                <option value="{{ $yr }}">Tahun {{ $yr }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold mb-1">Berdasarkan Unit / Fakultas:</label>
                        <select id="reportFilterUnit" class="form-select form-select-sm">
                            <option value="ALL">Semua Unit</option>
                            @foreach ($allUnits as $un)
                                <option value="{{ $un }}">{{ $un }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold mb-1">Berdasarkan Jenis Kerja Sama:</label>
                        <select id="reportFilterType" class="form-select form-select-sm">
                            <option value="ALL">Semua Jenis</option>
                            @foreach ($allTypes as $tp)
                                <option value="{{ $tp }}">{{ $tp }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="button" id="btnResetReportFilter" class="btn btn-outline-secondary btn-sm w-100 fw-bold">
                            <i class="bi bi-arrow-counterclockwise me-1"></i> Reset Kriteria
                        </button>
                    </div>
                </div>
            </div>

            <!-- 2 KARTU REKAPITULASI DUA SISI REAL-TIME -->
            <div class="card-body border-bottom">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded border small h-100">
                            <h6 class="fw-bold text-dark text-uppercase border-bottom pb-2 mb-2 d-flex justify-content-between align-items-center" style="font-size: 0.8rem;">
                                <span>Rekapitulasi Kerja Sama Aktif &amp; Tidak Aktif</span>
                                <span class="badge bg-dark">{{ $totalAllDocs }} Dokumen</span>
                            </h6>
                            <div class="d-flex justify-content-between mb-1">
                                <span>Kerja Sama Aktif Sah:</span>
                                <strong class="text-success">{{ $activeCount }} Dokumen ({{ $totalAllDocs > 0 ? round(($activeCount / $totalAllDocs) * 100, 1) : 0 }}%)</strong>
                            </div>
                            <div class="d-flex justify-content-between mb-1">
                                <span>Peringatan Masa Berlaku (Warning):</span>
                                <strong class="text-warning">{{ $warningCount }} Dokumen ({{ $totalAllDocs > 0 ? round(($warningCount / $totalAllDocs) * 100, 1) : 0 }}%)</strong>
                            </div>
                            <div class="d-flex justify-content-between mb-1">
                                <span>Kerja Sama Tidak Aktif / Berakhir:</span>
                                <strong class="text-danger">{{ $expiredCount }} Dokumen ({{ $totalAllDocs > 0 ? round(($expiredCount / $totalAllDocs) * 100, 1) : 0 }}%)</strong>
                            </div>
                            <div class="d-flex justify-content-between mb-1">
                                <span>Kerja Sama Dalam Pemeriksaan (Review):</span>
                                <strong class="text-info">{{ $reviewCount }} Dokumen ({{ $totalAllDocs > 0 ? round(($reviewCount / $totalAllDocs) * 100, 1) : 0 }}%)</strong>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span>Kerja Sama Terverifikasi Legal (Verified):</span>
                                <strong class="text-primary">{{ $verifiedCount }} Dokumen ({{ $totalAllDocs > 0 ? round(($verifiedCount / $totalAllDocs) * 100, 1) : 0 }}%)</strong>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded border small h-100">
                            <h6 class="fw-bold text-dark text-uppercase border-bottom pb-2 mb-2 d-flex justify-content-between align-items-center" style="font-size: 0.8rem;">
                                <span>Laporan Rencana vs Realisasi &amp; Luaran</span>
                                <span class="badge bg-dark">{{ $totalActs }} Agenda</span>
                            </h6>
                            <div class="d-flex justify-content-between mb-1">
                                <span>Total Rencana Agenda Kegiatan:</span>
                                <strong class="text-dark">{{ $totalActs }} Agenda</strong>
                            </div>
                            <div class="d-flex justify-content-between mb-1">
                                <span>Realisasi Kegiatan Terlaksana (Selesai):</span>
                                <strong class="text-success">{{ $doneActs }} Agenda ({{ $totalActs > 0 ? round(($doneActs / $totalActs) * 100, 1) : 0 }}%)</strong>
                            </div>
                            <div class="d-flex justify-content-between mb-1">
                                <span>Realisasi Sedang Berjalan di Lapangan:</span>
                                <strong class="text-primary">{{ $runningActs }} Agenda ({{ $totalActs > 0 ? round(($runningActs / $totalActs) * 100, 1) : 0 }}%)</strong>
                            </div>
                            <div class="d-flex justify-content-between mb-1">
                                <span>Belum Dimulai / Tertunda:</span>
                                <strong class="text-muted">{{ $pendingActs }} Agenda</strong>
                            </div>
                            <div class="d-flex justify-content-between border-top pt-1 mt-1">
                                <span>Laporan Luaran Dihasilkan:</span>
                                <strong class="text-dark">{{ $outputActs }} Dokumen Bukti Luaran</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TABEL PRATINJAU REKAPITULASI DATA REAL-TIME -->
            <div class="table-responsive">
                <table class="table table-wireframe table-hover align-middle mb-0 small" id="reportTable">
                    <thead>
                        <tr>
                            <th>Tahun</th>
                            <th>Nama Mitra &amp; Kategori</th>
                            <th>Unit Pengusul</th>
                            <th>Jenis Naskah</th>
                            <th>Rencana Kegiatan</th>
                            <th>Realisasi Kegiatan</th>
                            <th>Status FSM</th>
                            <th>Luaran Dihasilkan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($documents as $doc)
                            @php
                                $docYear = $doc->date_signed ? \Carbon\Carbon::parse($doc->date_signed)->format('Y') : ($doc->created_at ? \Carbon\Carbon::parse($doc->created_at)->format('Y') : '2026');
                                $docActs = $doc->activities;
                                $docOutputs = $docActs->pluck('outputs')->filter(fn($o) => !empty($o) && $o !== '-')->implode('; ');
                            @endphp
                            <tr data-year="{{ $docYear }}" data-unit="{{ $doc->unit }}" data-type="{{ $doc->doc_type }}">
                                <td><span class="badge bg-light text-dark border">{{ $docYear }}</span></td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $doc->partner_name }}</div>
                                    <small class="text-muted">{{ $doc->partner_category }} &bull; {{ $doc->doc_id }}</small>
                                </td>
                                <td>{{ $doc->unit }}</td>
                                <td>
                                    @if (str_contains($doc->doc_type, 'MoU'))
                                        <span class="badge bg-dark">MoU</span>
                                    @elseif (str_contains($doc->doc_type, 'MoA') || str_contains($doc->doc_type, 'PKS'))
                                        <span class="badge bg-secondary">MoA</span>
                                    @elseif (str_contains($doc->doc_type, 'IA'))
                                        <span class="badge bg-outline-dark border">IA</span>
                                    @else
                                        <span class="badge bg-light text-dark border">{{ $doc->doc_type }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($docActs->isNotEmpty())
                                        <strong>{{ $docActs->count() }} Agenda</strong>
                                        <div class="text-muted small text-truncate" style="max-width: 180px;" title="{{ $docActs->first()->activity_name }}">
                                            {{ $docActs->first()->activity_name }}
                                        </div>
                                    @else
                                        <span class="text-muted fst-italic">Belum ada agenda</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($docActs->isNotEmpty())
                                        @php
                                            $selesaiCount = $docActs->where('execution_status', 'SELESAI')->count();
                                            $berjalanCount = $docActs->where('execution_status', 'SEDANG_BERJALAN')->count();
                                        @endphp
                                        <span class="badge bg-light text-dark border">{{ $selesaiCount }} Selesai</span>
                                        @if ($berjalanCount > 0)
                                            <span class="badge bg-light text-secondary border">{{ $berjalanCount }} Berjalan</span>
                                        @endif
                                    @else
                                        <span class="badge bg-light text-secondary border">0%</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($doc->status === 'ACTIVE')
                                        <span class="badge bg-success">Aktif Sah</span>
                                    @elseif ($doc->status === 'WARNING')
                                        <span class="badge bg-warning text-dark border">Peringatan H-90</span>
                                    @elseif ($doc->status === 'EXPIRED')
                                        <span class="badge bg-danger">Kedaluwarsa</span>
                                    @elseif ($doc->status === 'VERIFIED')
                                        <span class="badge bg-primary text-white">Terverifikasi Legal</span>
                                    @elseif ($doc->status === 'REVIEW')
                                        <span class="badge bg-info text-dark">Pemeriksaan (Review)</span>
                                    @elseif ($doc->status === 'DRAFT')
                                        <span class="badge bg-secondary">Draf Awal</span>
                                    @elseif ($doc->status === 'CANCELLED')
                                        <span class="badge bg-dark">Dibatalkan</span>
                                    @else
                                        <span class="badge bg-light text-dark border">{{ $doc->status }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if (!empty($docOutputs))
                                        <span class="text-truncate d-inline-block" style="max-width: 200px;" title="{{ $docOutputs }}">
                                            {{ $docOutputs }}
                                        </span>
                                    @else
                                        <span class="text-muted fst-italic">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4 text-muted">Belum ada data dokumen kerja sama yang tersedia untuk laporan analitik.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @push('scripts')
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            var filterYear = document.getElementById('reportFilterYear');
            var filterUnit = document.getElementById('reportFilterUnit');
            var filterType = document.getElementById('reportFilterType');
            var btnReset = document.getElementById('btnResetReportFilter');
            var btnExport = document.getElementById('btnExportCSV');
            var rows = document.querySelectorAll('#reportTable tbody tr');

            function applyReportFilters() {
                var selectedYear = filterYear ? filterYear.value : 'ALL';
                var selectedUnit = filterUnit ? filterUnit.value : 'ALL';
                var selectedType = filterType ? filterType.value : 'ALL';

                rows.forEach(function(row) {
                    var rYear = row.getAttribute('data-year') || '';
                    var rUnit = row.getAttribute('data-unit') || '';
                    var rType = row.getAttribute('data-type') || '';

                    var matchYear = (selectedYear === 'ALL' || rYear === selectedYear);
                    var matchUnit = (selectedUnit === 'ALL' || rUnit === selectedUnit);
                    var matchType = (selectedType === 'ALL' || rType.indexOf(selectedType) !== -1);

                    if (matchYear && matchUnit && matchType) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            }

            if (filterYear) filterYear.addEventListener('change', applyReportFilters);
            if (filterUnit) filterUnit.addEventListener('change', applyReportFilters);
            if (filterType) filterType.addEventListener('change', applyReportFilters);

            if (btnReset) {
                btnReset.addEventListener('click', function() {
                    if (filterYear) filterYear.value = 'ALL';
                    if (filterUnit) filterUnit.value = 'ALL';
                    if (filterType) filterType.value = 'ALL';
                    applyReportFilters();
                });
            }

            if (btnExport) {
                btnExport.addEventListener('click', function() {
                    var csv = [];
                    var table = document.getElementById('reportTable');
                    if (!table) return;

                    var headers = [];
                    var ths = table.querySelectorAll('thead th');
                    ths.forEach(function(th) {
                        headers.push('"' + th.innerText.trim().replace(/"/g, '""') + '"');
                    });
                    csv.push(headers.join(','));

                    var bodyRows = table.querySelectorAll('tbody tr');
                    bodyRows.forEach(function(tr) {
                        if (tr.style.display !== 'none') {
                            var rowData = [];
                            var tds = tr.querySelectorAll('td');
                            tds.forEach(function(td) {
                                rowData.push('"' + td.innerText.trim().replace(/\r?\n|\r/g, ' ').replace(/"/g, '""') + '"');
                            });
                            csv.push(rowData.join(','));
                        }
                    });

                    var csvContent = 'data:text/csv;charset=utf-8,\uFEFF' + encodeURIComponent(csv.join('\n'));
                    var downloadAnchor = document.createElement('a');
                    downloadAnchor.setAttribute('href', csvContent);
                    downloadAnchor.setAttribute('download', 'Laporan_Rekapitulasi_SIMKERMA_UNIPAR_' + new Date().toISOString().slice(0, 10) + '.csv');
                    document.body.appendChild(downloadAnchor);
                    downloadAnchor.click();
                    downloadAnchor.remove();
                });
            }
        });
        </script>
        @endpush

    @endif

    {{-- MODAL TAMBAH MITRA BARU DARI FORM PENGAJUAN / DOKUMEN LAMPAU (TERSIMPAN KE MENU 02) --}}
    <div class="modal fade" id="quickAddPartnerModal" tabindex="-1" aria-labelledby="quickAddPartnerModalLabel" aria-hidden="true" style="z-index: 1070;">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <form id="quickAddPartnerForm" method="POST" action="{{ route('simkerma.store-partner') }}">
                    @csrf
                    <input type="hidden" name="is_ajax" value="1">
                    <div class="modal-header bg-dark text-white py-3">
                        <div class="d-flex align-items-center gap-2">
                            <div class="p-2 bg-light rounded text-dark"><i class="bi bi-building-add fs-4"></i></div>
                            <div>
                                <h6 class="modal-title fw-bold mb-0" id="quickAddPartnerModalLabel">Tambah Mitra Baru ke Master Data</h6>
                                <small class="text-white-50">Data mitra akan tersimpan di Menu 02 dan langsung terpilih di formulir ini</small>
                            </div>
                        </div>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body p-4 small">
                        <div class="alert alert-info py-2 px-3 mb-3 d-flex align-items-center gap-2" style="font-size: 0.78rem;">
                            <i class="bi bi-info-circle-fill fs-5 text-primary"></i>
                            <div>Mitra yang didaftarkan di sini akan tersimpan resmi di <strong>Data Master Mitra (Menu 02)</strong> dan dapat digunakan kembali untuk semua dokumen kerja sama lainnya.</div>
                        </div>

                        <div id="quickAddAlert" class="alert alert-danger d-none py-2 px-3 mb-3 small"></div>

                        <div class="row g-3">
                            <div class="col-md-7">
                                <label class="form-label fw-bold text-dark">Nama Lembaga / Instansi Mitra <span class="text-danger">*</span></label>
                                <input type="text" name="name" id="quick_partner_name" class="form-control" placeholder="Contoh: PT Telekomunikasi Selular / Pemkot Manado" required>
                            </div>
                            <div class="col-md-5">
                                <label class="form-label fw-bold text-dark">Kategori Mitra <span class="text-danger">*</span></label>
                                <select name="category" id="quick_partner_category" class="form-select" required>
                                    @foreach (\App\Models\Partner::OFFICIAL_CATEGORIES as $catVal => $catLabel)
                                        <option value="{{ $catVal }}" {{ $catVal === 'DUDI / Swasta' ? 'selected' : '' }}>{{ $catLabel }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold text-dark">Cakupan Wilayah</label>
                                <select name="scope_coverage" id="quick_partner_scope" class="form-select">
                                    <option value="Dalam Negeri (Nasional/Lokal)" selected>Dalam Negeri (Nasional/Lokal)</option>
                                    <option value="Internasional">Internasional</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-dark">Kota / Domisili Kantor</label>
                                <input type="text" name="city" id="quick_partner_city" class="form-control" placeholder="Contoh: Kota Manado / Minahasa Utara">
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-bold text-dark">Alamat Lengkap Kantor</label>
                                <textarea name="address" id="quick_partner_address" class="form-control" rows="2" placeholder="Alamat kantor resmi instansi/perusahaan mitra..."></textarea>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold text-dark">Nama Narahubung / PIC</label>
                                <input type="text" name="pic_name" id="quick_partner_pic_name" class="form-control" placeholder="Nama staf/pejabat penghubung mitra">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-dark">Kontak / Telepon / WhatsApp PIC</label>
                                <input type="text" name="pic_contact" id="quick_partner_pic_contact" class="form-control" placeholder="08xxxxxxxxxx">
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer bg-light py-2 px-4 d-flex justify-content-between align-items-center">
                        <span class="text-muted small"><i class="bi bi-shield-check text-success me-1"></i>Otomatis Tersimpan ke Menu 02</span>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-secondary btn-sm px-3" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" id="btnSubmitQuickPartner" class="btn btn-primary btn-sm px-3 fw-bold">
                                <i class="bi bi-save me-1"></i> Simpan ke Master Data Mitra (Menu 02)
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <style>
        .hybrid-partner-combobox .partner-dropdown-menu {
            position: absolute;
            top: 100%;
            left: 0;
            width: 100%;
            z-index: 1060;
            background: #ffffff;
            border: 1px solid #ced4da;
            border-radius: 0.375rem;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.12);
            max-height: 280px;
            overflow-y: auto;
        }
        .hybrid-partner-combobox .partner-item {
            cursor: pointer;
            transition: background-color 0.15s ease-in-out;
            border-bottom: 1px solid #f1f3f5;
        }
        .hybrid-partner-combobox .partner-item:hover,
        .hybrid-partner-combobox .partner-item.active {
            background-color: #eef2ff !important;
            color: #1e3a8a !important;
        }
        .hybrid-partner-combobox .smart-partner-input:focus {
            box-shadow: none;
            border-color: #86b7fe;
        }
    </style>
    <script>
    (function() {
        let currentActiveCombobox = null;

        function initHybridSmartCombobox(container) {
            if (!container || container.dataset.comboboxInitialized) return;
            container.dataset.comboboxInitialized = 'true';

            const input = container.querySelector('.smart-partner-input');
            const dropdown = container.querySelector('.partner-dropdown-menu');
            const clearBtn = container.querySelector('.btn-clear-partner');
            const quickAddBtn = container.querySelector('.btn-open-quick-add');
            const verifiedInfo = container.querySelector('.partner-verified-info');
            const toggleBtn = container.querySelector('.btn-toggle-manual');
            const modeBadge = container.querySelector('.partner-mode-desc');
            const form = container.closest('form');
            const categorySelect = form ? form.querySelector('[name="category"]') : null;
            const iconEl = container.querySelector('.partner-input-icon');

            let isManualMode = false;
            let debounceTimer = null;
            let activeIndex = -1;

            function isPtCategory() {
                if (!categorySelect) return true;
                const val = (categorySelect.value || '').toLowerCase();
                return val.includes('perguruan tinggi') && !val.includes('luar negeri');
            }

            function updateCategoryUI() {
                if (isPtCategory()) {
                    if (iconEl) iconEl.className = 'partner-input-icon bi bi-mortarboard-fill text-primary fs-5';
                    if (!isManualMode) {
                        input.placeholder = 'Ketik nama kampus (contoh: Sam Ratulangi, ITB, Parna Raya)...';
                        if (modeBadge) modeBadge.innerHTML = '<i class="bi bi-database-check text-success me-1"></i>Direktori Resmi PDDikti Kemendikbudristek (4.650+ PT)';
                        if (toggleBtn) {
                            toggleBtn.style.display = 'inline-block';
                            toggleBtn.innerHTML = '<i class="bi bi-pencil-square me-1"></i>Ketik Manual / Luar Negeri';
                        }
                    }
                } else {
                    if (iconEl) iconEl.className = 'partner-input-icon bi bi-building text-secondary fs-5';
                    input.placeholder = 'Ketik nama mitra dari Master Data (contoh: PT Telkom, Pemkot Manado)...';
                    if (modeBadge) modeBadge.innerHTML = '<i class="bi bi-database-fill text-primary me-1"></i>Database Master Mitra UNIPAR (Menu 02) &bull; Klik Tambah jika belum ada';
                    if (toggleBtn) toggleBtn.style.display = 'none';
                    closeDropdown();
                }
            }

            if (categorySelect) {
                categorySelect.addEventListener('change', function() {
                    isManualMode = false;
                    updateCategoryUI();
                });
            }

            // Quick add button handler
            if (quickAddBtn) {
                quickAddBtn.addEventListener('click', function(e) {
                    currentActiveCombobox = container;
                    openQuickAddModal(input.value.trim(), categorySelect ? categorySelect.value : '');
                });
            }

            function closeDropdown() {
                if (dropdown) {
                    dropdown.classList.add('d-none');
                    dropdown.innerHTML = '';
                }
                activeIndex = -1;
            }

            function openDropdown(items, queryStr) {
                if (!dropdown) return;
                const currentVal = input.value.trim();

                if (!items || items.length === 0) {
                    if (isPtCategory()) {
                        dropdown.innerHTML = `
                            <div class="p-3 text-center text-muted small">
                                <i class="bi bi-exclamation-circle text-warning fs-5 d-block mb-1"></i>
                                Nama kampus "<strong>${currentVal.replace(/</g, '&lt;')}</strong>" tidak ditemukan di direktori PDDikti.<br>
                                <div class="d-flex justify-content-center gap-2 mt-2">
                                    <button type="button" class="btn btn-outline-primary btn-sm btn-use-manual">
                                        <i class="bi bi-check2-circle me-1"></i>Gunakan Manual
                                    </button>
                                    <button type="button" class="btn btn-primary btn-sm btn-open-modal-empty">
                                        <i class="bi bi-plus-lg me-1"></i>Daftarkan ke Menu 02
                                    </button>
                                </div>
                            </div>
                        `;
                    } else {
                        dropdown.innerHTML = `
                            <div class="p-3 text-center text-muted small">
                                <i class="bi bi-building-dash text-secondary fs-5 d-block mb-1"></i>
                                Mitra "<strong>${currentVal.replace(/</g, '&lt;')}</strong>" belum ada di Master Data Mitra.<br>
                                <button type="button" class="btn btn-primary btn-sm mt-2 btn-open-modal-empty">
                                    <i class="bi bi-plus-lg me-1"></i>+ Daftarkan "${currentVal.replace(/</g, '&lt;')}" ke Master Data (Menu 02)
                                </button>
                            </div>
                        `;
                    }

                    const useManualBtn = dropdown.querySelector('.btn-use-manual');
                    if (useManualBtn) {
                        useManualBtn.addEventListener('click', function(e) {
                            e.preventDefault();
                            isManualMode = true;
                            closeDropdown();
                            if (verifiedInfo) {
                                verifiedInfo.innerHTML = `
                                    <span class="badge bg-light text-dark border">
                                        <i class="bi bi-pencil-fill text-secondary me-1"></i>Nama diinput manual (Non-Direktori)
                                    </span>
                                `;
                                verifiedInfo.classList.remove('d-none');
                            }
                            if (modeBadge) modeBadge.innerHTML = '<i class="bi bi-pencil text-warning me-1"></i>Mode Input Manual Aktif';
                            if (toggleBtn) toggleBtn.innerHTML = '<i class="bi bi-search me-1"></i>Kembali ke Pencarian PDDikti';
                        });
                    }

                    const openModalEmpty = dropdown.querySelector('.btn-open-modal-empty');
                    if (openModalEmpty) {
                        openModalEmpty.addEventListener('click', function(e) {
                            e.preventDefault();
                            currentActiveCombobox = container;
                            closeDropdown();
                            openQuickAddModal(currentVal, categorySelect ? categorySelect.value : '');
                        });
                    }

                    dropdown.classList.remove('d-none');
                    return;
                }

                let html = '<div class="list-group list-group-flush">';
                items.forEach((item, idx) => {
                    let badgeEl = '';
                    if (item.source === 'DATABASE_MASTER') {
                        badgeEl = `<span class="badge bg-success text-white">Menu 02 (Master)</span>`;
                    } else {
                        badgeEl = item.kelompok === 'PTN' ? '<span class="badge bg-primary text-white">PTN</span>' : (item.kelompok === 'PTK' ? '<span class="badge bg-info text-dark">PTK</span>' : '<span class="badge bg-secondary text-white">PTS</span>');
                    }

                    html += `
                        <a href="javascript:void(0)" class="list-group-item list-group-item-action py-2 px-3 partner-item text-start text-decoration-none" data-index="${idx}" data-name="${item.nama.replace(/"/g, '&quot;')}" data-kode="${item.kode || ''}" data-kota="${item.kota || ''}" data-provinsi="${item.provinsi || ''}" data-kelompok="${item.kelompok || ''}" data-source="${item.source || 'PDDIKTI'}">
                            <div class="d-flex justify-content-between align-items-center mb-0.5">
                                <strong class="text-dark">${item.nama}</strong>
                                ${badgeEl}
                            </div>
                            <div class="small text-muted d-flex flex-wrap align-items-center gap-2">
                                <span><i class="bi bi-geo-alt me-0.5"></i>${item.kota ? item.kota + ', ' : ''}${item.provinsi}</span>
                                ${item.kode ? `<span class="badge bg-light text-secondary border font-monospace" style="font-size:0.65rem;">Kode: ${item.kode}</span>` : ''}
                            </div>
                        </a>
                    `;
                });
                html += '</div>';
                dropdown.innerHTML = html;
                dropdown.classList.remove('d-none');

                dropdown.querySelectorAll('.partner-item').forEach(el => {
                    el.addEventListener('click', function(e) {
                        e.preventDefault();
                        selectPartner({
                            nama: this.getAttribute('data-name'),
                            kode: this.getAttribute('data-kode'),
                            kota: this.getAttribute('data-kota'),
                            provinsi: this.getAttribute('data-provinsi'),
                            kelompok: this.getAttribute('data-kelompok'),
                            source: this.getAttribute('data-source')
                        });
                    });
                });
            }

            function selectPartner(pt) {
                input.value = pt.nama;
                closeDropdown();
                if (clearBtn) clearBtn.classList.remove('d-none');

                // Autofill city if field exists in the form
                if (form && pt.kota) {
                    const cityInput = form.querySelector('[name="city"]');
                    if (cityInput && !cityInput.value) {
                        cityInput.value = pt.kota;
                    }
                }

                if (verifiedInfo) {
                    if (pt.source === 'DATABASE_MASTER') {
                        verifiedInfo.innerHTML = `
                            <div class="alert alert-success py-1 px-2.5 small mb-0 d-inline-flex align-items-center gap-2 border shadow-sm">
                                <i class="bi bi-patch-check-fill text-success fs-6"></i>
                                <div>
                                    <strong>Terdaftar di Master Data Mitra (Menu 02):</strong> 
                                    ${pt.kota ? `${pt.kota}, ` : ''}${pt.provinsi} &bull; 
                                    Status: <span class="badge bg-success">Sah &amp; Terverifikasi</span>
                                </div>
                            </div>
                        `;
                    } else {
                        verifiedInfo.innerHTML = `
                            <div class="alert alert-success py-1 px-2.5 small mb-0 d-inline-flex align-items-center gap-2 border shadow-sm">
                                <i class="bi bi-patch-check-fill text-success fs-6"></i>
                                <div>
                                    <strong>Terverifikasi PDDikti:</strong> 
                                    ${pt.kode ? `Kode: <code class="text-dark fw-bold">${pt.kode}</code> &bull; ` : ''}
                                    ${pt.kota ? `${pt.kota}, ` : ''}${pt.provinsi} &bull; 
                                    Status: <span class="badge bg-success">${pt.kelompok}</span>
                                </div>
                            </div>
                        `;
                    }
                    verifiedInfo.classList.remove('d-none');
                }
            }

            if (toggleBtn) {
                toggleBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    isManualMode = !isManualMode;
                    if (isManualMode) {
                        closeDropdown();
                        input.placeholder = 'Ketik bebas nama perguruan tinggi / lembaga...';
                        this.innerHTML = '<i class="bi bi-search me-1"></i>Kembali ke Pencarian PDDikti';
                        if (modeBadge) modeBadge.innerHTML = '<i class="bi bi-pencil text-warning me-1"></i>Mode Input Manual Aktif';
                        if (verifiedInfo) {
                            verifiedInfo.innerHTML = `
                                <span class="badge bg-light text-dark border">
                                    <i class="bi bi-pencil-fill text-secondary me-1"></i>Nama diinput manual (Non-Direktori)
                                </span>
                            `;
                            verifiedInfo.classList.remove('d-none');
                        }
                    } else {
                        updateCategoryUI();
                        if (verifiedInfo) verifiedInfo.classList.add('d-none');
                        if (input.value.trim().length >= 2) {
                            fetchPartners(input.value.trim());
                        }
                    }
                });
            }

            function fetchPartners(q) {
                let url = '';
                if (isPtCategory()) {
                    url = '/simkerma/api/universities?q=' + encodeURIComponent(q);
                } else {
                    const catVal = categorySelect ? categorySelect.value : '';
                    url = '/simkerma/api/partners?q=' + encodeURIComponent(q) + '&category=' + encodeURIComponent(catVal);
                }

                fetch(url)
                    .then(res => res.json())
                    .then(data => {
                        openDropdown(data, q);
                    })
                    .catch(err => {
                        console.error('Err fetchPartners:', err);
                    });
            }

            input.addEventListener('input', function() {
                const val = this.value.trim();
                if (val.length > 0) {
                    if (clearBtn) clearBtn.classList.remove('d-none');
                } else {
                    if (clearBtn) clearBtn.classList.add('d-none');
                    if (verifiedInfo) verifiedInfo.classList.add('d-none');
                }

                if (isManualMode) {
                    closeDropdown();
                    return;
                }

                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(() => {
                    if (val.length >= 2) {
                        fetchPartners(val);
                    } else if (val.length === 0) {
                        fetchPartners('');
                    } else {
                        closeDropdown();
                    }
                }, 220);
            });

            input.addEventListener('focus', function() {
                if (!isManualMode) {
                    if (this.value.trim().length >= 2) {
                        fetchPartners(this.value.trim());
                    } else if (this.value.trim().length === 0) {
                        fetchPartners('');
                    }
                }
            });

            if (clearBtn) {
                clearBtn.addEventListener('click', function() {
                    input.value = '';
                    input.focus();
                    clearBtn.classList.add('d-none');
                    if (verifiedInfo) verifiedInfo.classList.add('d-none');
                    closeDropdown();
                    if (!isManualMode) {
                        fetchPartners('');
                    }
                });
            }

            document.addEventListener('click', function(e) {
                if (!container.contains(e.target)) {
                    closeDropdown();
                }
            });

            input.addEventListener('keydown', function(e) {
                if (!dropdown || dropdown.classList.contains('d-none')) return;
                const items = dropdown.querySelectorAll('.partner-item');
                if (!items || items.length === 0) return;

                if (e.key === 'ArrowDown') {
                    e.preventDefault();
                    activeIndex = (activeIndex + 1) % items.length;
                    highlightItem(items);
                } else if (e.key === 'ArrowUp') {
                    e.preventDefault();
                    activeIndex = (activeIndex - 1 + items.length) % items.length;
                    highlightItem(items);
                } else if (e.key === 'Enter') {
                    if (activeIndex >= 0 && activeIndex < items.length) {
                        e.preventDefault();
                        items[activeIndex].click();
                    }
                } else if (e.key === 'Escape') {
                    closeDropdown();
                }
            });

            function highlightItem(items) {
                items.forEach((item, idx) => {
                    if (idx === activeIndex) {
                        item.classList.add('active');
                        item.scrollIntoView({ block: 'nearest' });
                    } else {
                        item.classList.remove('active');
                    }
                });
            }

            // Initial UI check
            updateCategoryUI();
        }

        // Robust Bootstrap 5.0 compatible modal helpers
        function getModalInstance(modalEl) {
            if (!modalEl) return null;
            try {
                if (window.bootstrap && bootstrap.Modal) {
                    if (typeof bootstrap.Modal.getInstance === 'function') {
                        const inst = bootstrap.Modal.getInstance(modalEl);
                        if (inst) return inst;
                    }
                    return new bootstrap.Modal(modalEl);
                }
            } catch (e) {
                console.warn('Bootstrap modal instance error:', e);
            }
            return null;
        }

        function showModalSafe(modalEl) {
            if (!modalEl) return;
            const inst = getModalInstance(modalEl);
            if (inst) {
                inst.show();
                return;
            }
            if (window.jQuery && typeof jQuery(modalEl).modal === 'function') {
                jQuery(modalEl).modal('show');
            }
        }

        function hideModalSafe(modalEl) {
            if (!modalEl) return;
            try {
                if (window.bootstrap && bootstrap.Modal) {
                    let inst = typeof bootstrap.Modal.getInstance === 'function' ? bootstrap.Modal.getInstance(modalEl) : null;
                    if (inst) {
                        inst.hide();
                    } else {
                        new bootstrap.Modal(modalEl).hide();
                    }
                }
            } catch (e) {}
            if (window.jQuery && typeof jQuery(modalEl).modal === 'function') {
                jQuery(modalEl).modal('hide');
            }
            setTimeout(() => {
                const anyOpen = document.querySelectorAll('.modal.show');
                if (anyOpen.length > 0) {
                    document.body.classList.add('modal-open');
                } else if (!modalEl.classList.contains('show')) {
                    document.querySelectorAll('.modal-backdrop').forEach(b => b.remove());
                    document.body.classList.remove('modal-open');
                    document.body.style.removeProperty('padding-right');
                    document.body.style.removeProperty('overflow');
                }
            }, 300);
        }

        function openQuickAddModal(prefillName, prefillCategory) {
            const modalEl = document.getElementById('quickAddPartnerModal');
            if (!modalEl) return;

            const nameInput = modalEl.querySelector('#quick_partner_name');
            const catSelect = modalEl.querySelector('#quick_partner_category');
            const alertBox = modalEl.querySelector('#quickAddAlert');
            if (alertBox) alertBox.classList.add('d-none');

            if (nameInput && prefillName) {
                nameInput.value = prefillName;
            }
            if (catSelect && prefillCategory) {
                for (let i = 0; i < catSelect.options.length; i++) {
                    if (catSelect.options[i].value === prefillCategory || catSelect.options[i].text.includes(prefillCategory)) {
                        catSelect.selectedIndex = i;
                        break;
                    }
                }
            }

            showModalSafe(modalEl);
        }

        // Listener saat Modal Quick Add dibuka (baik via data-bs-toggle maupun script)
        const quickModalEl = document.getElementById('quickAddPartnerModal');
        if (quickModalEl) {
            quickModalEl.addEventListener('show.bs.modal', function(e) {
                const alertBox = quickModalEl.querySelector('#quickAddAlert');
                if (alertBox) alertBox.classList.add('d-none');

                let trigger = e.relatedTarget;
                let container = null;
                if (trigger && typeof trigger.closest === 'function') {
                    container = trigger.closest('.hybrid-partner-combobox');
                }
                if (!container && currentActiveCombobox) {
                    container = currentActiveCombobox;
                }
                if (!container) {
                    container = document.querySelector('.hybrid-partner-combobox');
                }

                if (container) {
                    currentActiveCombobox = container;
                    const input = container.querySelector('.smart-partner-input');
                    const form = container.closest('form');
                    const catSelect = form ? form.querySelector('[name="category"]') : null;

                    const nameInput = quickModalEl.querySelector('#quick_partner_name');
                    const modalCatSelect = quickModalEl.querySelector('#quick_partner_category');

                    if (nameInput && input && input.value.trim()) {
                        nameInput.value = input.value.trim();
                    }
                    if (modalCatSelect && catSelect && catSelect.value) {
                        for (let i = 0; i < modalCatSelect.options.length; i++) {
                            if (modalCatSelect.options[i].value === catSelect.value || modalCatSelect.options[i].text.includes(catSelect.value)) {
                                modalCatSelect.selectedIndex = i;
                                break;
                            }
                        }
                    }
                }
            });
        }

        // Global delegated click listener for quick add button
        document.addEventListener('click', function(e) {
            const btn = e.target.closest('.btn-open-quick-add');
            if (btn) {
                const container = btn.closest('.hybrid-partner-combobox');
                if (container) {
                    currentActiveCombobox = container;
                    const input = container.querySelector('.smart-partner-input');
                    const form = container.closest('form');
                    const catSelect = form ? form.querySelector('[name="category"]') : null;
                    openQuickAddModal(input ? input.value.trim() : '', catSelect ? catSelect.value : '');
                }
            }
        });

        // Quick add form AJAX submission
        const quickForm = document.getElementById('quickAddPartnerForm');
        if (quickForm) {
            quickForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const submitBtn = document.getElementById('btnSubmitQuickPartner');
                const alertBox = document.getElementById('quickAddAlert');
                if (alertBox) alertBox.classList.add('d-none');

                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Menyimpan ke Menu 02...';
                }

                const formData = new FormData(this);

                fetch(this.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = '<i class="bi bi-save me-1"></i> Simpan ke Master Data Mitra (Menu 02)';
                    }

                    if (data.success && data.partner) {
                        const partner = data.partner;
                        // Close modal safely
                        const modalEl = document.getElementById('quickAddPartnerModal');
                        hideModalSafe(modalEl);

                        // Fill active combobox
                        const targetCombobox = currentActiveCombobox || document.querySelector('.hybrid-partner-combobox');
                        if (targetCombobox) {
                            const input = targetCombobox.querySelector('.smart-partner-input');
                            const verifiedInfo = targetCombobox.querySelector('.partner-verified-info');
                            const clearBtn = targetCombobox.querySelector('.btn-clear-partner');

                            if (input) input.value = partner.name;
                            if (clearBtn) clearBtn.classList.remove('d-none');

                            // Update datalist if present
                            const datalist = targetCombobox.querySelector('datalist');
                            if (datalist) {
                                const opt = document.createElement('option');
                                opt.value = partner.name;
                                opt.textContent = `${partner.category} • ${partner.city || 'Kota Manado'}`;
                                datalist.prepend(opt);
                            }

                            if (verifiedInfo) {
                                verifiedInfo.innerHTML = `
                                    <div class="alert alert-success py-1 px-2.5 small mb-0 d-inline-flex align-items-center gap-2 border shadow-sm">
                                        <i class="bi bi-patch-check-fill text-success fs-6"></i>
                                        <div>
                                            <strong>Tersimpan di Master Data Mitra (Menu 02):</strong> 
                                            ${partner.category} &bull; ${partner.city || 'Kota Manado'} &bull; Status: Sah
                                        </div>
                                    </div>
                                `;
                                verifiedInfo.classList.remove('d-none');
                            }

                            // Autofill city if field exists in the form
                            const form = targetCombobox.closest('form');
                            if (form && partner.city) {
                                const cityInput = form.querySelector('[name="city"]');
                                if (cityInput && !cityInput.value) {
                                    cityInput.value = partner.city;
                                }
                            }
                        }

                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Mitra Tersimpan di Menu 02!',
                                text: `Data mitra "${partner.name}" berhasil disimpan ke Master Data (Menu 02) dan langsung terpilih di formulir.`,
                                timer: 2500,
                                showConfirmButton: false
                            });
                        }
                    } else {
                        if (alertBox) {
                            alertBox.innerText = data.message || 'Gagal menyimpan data mitra.';
                            alertBox.classList.remove('d-none');
                        }
                    }
                })
                .catch(err => {
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = '<i class="bi bi-save me-1"></i> Simpan ke Master Data Mitra (Menu 02)';
                    }
                    if (alertBox) {
                        alertBox.innerText = 'Terjadi kesalahan sistem saat menyimpan mitra ke database.';
                        alertBox.classList.remove('d-none');
                    }
                });
            });
        }

        function initAllComboboxes() {
            document.querySelectorAll('.hybrid-partner-combobox').forEach(initHybridSmartCombobox);
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initAllComboboxes);
        } else {
            initAllComboboxes();
        }

        // Re-check when any modal opens (e.g. createPartnerModal or editInfoModal)
        document.addEventListener('shown.bs.modal', function() {
            initAllComboboxes();
        });
    })();
    </script>
    @endpush

@endsection
