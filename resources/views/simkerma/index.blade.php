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

    {{-- ===================================================================================== --}}
    {{-- 1. DASHBOARD KERJA SAMA                                                               --}}
    {{-- ===================================================================================== --}}
    @if ($activeMenu === 1)
        @php
            $totalDocs = $documents->count();
            $activeDocs = $documents->where('status', 'ACTIVE')->count();
            $processDocs = $documents->whereIn('status', ['REVIEW', 'VERIFIED'])->count();
            $expiredDocs = $documents->where('status', 'EXPIRED')->count();
            $warningDocs = $documents->where('status', 'WARNING')->count();

            // Distribusi Kategori Mitra
            $categoriesGroup = $documents->groupBy('partner_category');

            // Distribusi Unit Pengusul
            $unitsGroup = $documents->groupBy('unit');

            // Kerja Sama yang Segera Berakhir (WARNING_EXPIRY atau dokumen aktif yang perlu atensi)
            $warningList = $documents->where('status', 'WARNING')->take(5);

            // Realisasi IKU 6 dari activities
            $totalActs = $activities->count();
            $completedActs = $activities->where('execution_status', 'SELESAI')->count();
            $runningActs = $activities->where('execution_status', 'SEDANG_BERJALAN')->count();
            $actProgressPercent = $totalActs > 0 ? round((($completedActs + ($runningActs * 0.7)) / $totalActs) * 100) : 0;
        @endphp

        <!-- 4 KARTU RINGKASAN STATUS KERJA SAMA (REAL-TIME DARI MYSQL) -->
        <div class="row g-3 mb-4">
            <div class="col-sm-6 col-xl-3">
                <div class="card card-wireframe h-100 p-3 border-top border-3 border-dark shadow-sm">
                    <span class="text-muted small text-uppercase fw-semibold">Total Kerja Sama</span>
                    <div class="d-flex align-items-center justify-content-between mt-2">
                        <h2 class="fw-bold mb-0 text-dark">{{ $totalDocs }}</h2>
                        <span class="bg-light p-2 rounded text-dark fs-5"><i class="bi bi-folder2"></i></span>
                    </div>
                    <small class="text-muted mt-2">Seluruh Dokumen Kemitraan Terdaftar</small>
                </div>
            </div>

            <div class="col-sm-6 col-xl-3">
                <div class="card card-wireframe h-100 p-3 border-top border-3 border-success shadow-sm">
                    <span class="text-muted small text-uppercase fw-semibold">Kerja Sama Aktif</span>
                    <div class="d-flex align-items-center justify-content-between mt-2">
                        <h2 class="fw-bold mb-0 text-success">{{ $activeDocs }}</h2>
                        <span class="badge bg-success fs-6 py-2 px-3">Sah Berlaku</span>
                    </div>
                    <small class="text-muted mt-2">Bertanda Tangan &amp; Bermeterai</small>
                </div>
            </div>

            <div class="col-sm-6 col-xl-3">
                <div class="card card-wireframe h-100 p-3 border-top border-3 border-primary shadow-sm">
                    <span class="text-muted small text-uppercase fw-semibold">Dalam Proses (Pipeline)</span>
                    <div class="d-flex align-items-center justify-content-between mt-2">
                        <h2 class="fw-bold mb-0 text-primary">{{ $processDocs }}</h2>
                        <span class="badge bg-primary fs-6 py-2 px-3">Siklus FSM</span>
                    </div>
                    <small class="text-muted mt-2">Draf, Telaah Legal &amp; Disetujui</small>
                </div>
            </div>

            <div class="col-sm-6 col-xl-3">
                <div class="card card-wireframe h-100 p-3 border-top border-3 border-warning shadow-sm">
                    <span class="text-muted small text-uppercase fw-semibold">Peringatan &amp; Kedaluwarsa</span>
                    <div class="d-flex align-items-center justify-content-between mt-2">
                        <h2 class="fw-bold mb-0 text-warning text-dark">{{ $warningDocs + $expiredDocs }}</h2>
                        <span class="badge bg-warning text-dark fs-6 py-2 px-3">Perlu Atensi</span>
                    </div>
                    <small class="text-muted mt-2">{{ $warningDocs }} Peringatan &bull; {{ $expiredDocs }} Kedaluwarsa</small>
                </div>
            </div>
        </div>

        <!-- 2 KOLOM: MITRA KATEGORI & DISTRIBUSI UNIT -->
        <div class="row g-4 mb-4">
            <!-- Jumlah Mitra Berdasarkan Kategori -->
            <div class="col-lg-6">
                <div class="card card-wireframe h-100 shadow-sm">
                    <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                        <h6 class="card-title mb-0 fw-bold text-uppercase">Jumlah Dokumen Berdasarkan Kategori Mitra</h6>
                        <span class="tag-wireframe">KATEGORI MITRA</span>
                    </div>
                    <div class="card-body p-0">
                        <ul class="list-group list-group-flush small">
                            @forelse ($categoriesGroup as $catName => $catDocs)
                                <li class="list-group-item d-flex justify-content-between align-items-center py-2 px-3">
                                    <span>
                                        <i class="bi bi-briefcase me-2 text-primary"></i>
                                        <strong>{{ $catName ?: 'Kategori Umum' }}</strong>
                                    </span>
                                    <span class="badge bg-dark rounded-pill">{{ $catDocs->count() }} Dokumen</span>
                                </li>
                            @empty
                                <li class="list-group-item text-muted py-2 px-3">Belum ada kategori mitra tercatat.</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Kerja Sama Berdasarkan Fakultas, UPPS, Program Studi, & Unit Pengusul -->
            <div class="col-lg-6">
                <div class="card card-wireframe h-100 shadow-sm">
                    <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                        <h6 class="card-title mb-0 fw-bold text-uppercase">Distribusi Fakultas &amp; Unit Pengusul Kampus</h6>
                        <span class="tag-wireframe">UNIT PENGUSUL</span>
                    </div>
                    <div class="card-body p-0">
                        <ul class="list-group list-group-flush small">
                            @forelse ($unitsGroup as $unitName => $uDocs)
                                <li class="list-group-item d-flex justify-content-between align-items-center py-2 px-3">
                                    <span>
                                        <i class="bi bi-mortarboard me-2 text-secondary"></i>
                                        <strong>{{ $unitName }}</strong>
                                    </span>
                                    <span class="fw-bold text-primary">{{ $uDocs->count() }} Dokumen</span>
                                </li>
                            @empty
                                <li class="list-group-item text-muted py-2 px-3">Belum ada unit pengusul tercatat.</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2 KOLOM: SEGERA BERAKHIR & REALISASI KEGIATAN -->
        <div class="row g-4">
            <!-- Kerja Sama yang Segera Berakhir -->
            <div class="col-lg-6">
                <div class="card card-wireframe h-100 shadow-sm">
                    <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                        <h6 class="card-title mb-0 fw-bold text-uppercase text-dark">
                            <i class="bi bi-exclamation-triangle text-warning me-1"></i> Perjanjian Dalam Ambang Batas Peringatan Dini
                        </h6>
                        <span class="badge bg-warning text-dark">H-90 / H-60 HARI</span>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-wireframe table-hover align-middle mb-0 small">
                            <thead>
                                <tr>
                                    <th>Mitra Kerja Sama</th>
                                    <th>Jenis</th>
                                    <th>Masa Berlaku</th>
                                    <th>Status &amp; Tindak Lanjut</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($warningList as $wDoc)
                                    <tr>
                                        <td>
                                            <div class="fw-bold text-dark">{{ $wDoc->partner_name }}</div>
                                            <div class="small text-muted font-monospace" style="font-size: 0.68rem;">{{ $wDoc->doc_number ?: $wDoc->doc_id }}</div>
                                        </td>
                                        <td><span class="badge bg-light text-dark border">{{ $wDoc->doc_type }}</span></td>
                                        <td>{{ $wDoc->validity ?: '1 Tahun' }}</td>
                                        <td>
                                            @if ($wDoc->status === 'WARNING')
                                                <span class="badge bg-warning text-dark">{{ $wDoc->warning_threshold ?: 'H-90 Hari' }}</span>
                                            @else
                                                <span class="badge bg-primary text-white">Approved</span>
                                            @endif
                                            <a href="{{ route('simkerma.index', ['menu' => 4, 'doc' => $wDoc->doc_id]) }}" class="btn btn-outline-dark btn-sm py-0 px-2 ms-1" style="font-size: 0.68rem;" title="Buka di Menu 04"><i class="bi bi-box-arrow-up-right me-1"></i>Kelola</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-3">Tidak ada dokumen dalam status peringatan dini.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Realisasi Kegiatan, Luaran, dan Capaian Kerja Sama -->
            <div class="col-lg-6">
                <div class="card card-wireframe h-100 shadow-sm">
                    <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                        <h6 class="card-title mb-0 fw-bold text-uppercase">Realisasi, Luaran &amp; Capaian (IKU 6)</h6>
                        <span class="tag-wireframe">CAPAIAN KINERJA</span>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <div class="d-flex justify-content-between small mb-1">
                                <span class="fw-semibold">Tingkat Penyelesaian Program Kemitraan (Menu 05)</span>
                                <span class="fw-bold text-primary">{{ $actProgressPercent }}% Tercapai</span>
                            </div>
                            <div class="progress" style="height: 10px;">
                                <div class="progress-bar bg-primary" style="width: {{ $actProgressPercent }}%"></div>
                            </div>
                            <small class="text-muted">{{ $completedActs }} program selesai, {{ $runningActs }} sedang berjalan dari {{ $totalActs }} total agenda.</small>
                        </div>

                        <div class="p-3 bg-light rounded border mb-2 small">
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
                                <strong class="text-dark">{{ $activities->whereNotNull('evidence_file')->count() }} Berkas Laporan</strong>
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
        <div class="card card-wireframe shadow-sm border mb-4">
            <!-- 1. KONSOL FILTER BERLABEL DI CARD-HEADER (PERSIS MENU 4) -->
            <div class="card-header bg-white py-3 border-bottom">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold mb-1 text-secondary small">Kategori Mitra</label>
                        <select id="partnerCategoryFilter" class="form-select form-select-sm" onchange="filterPartnersTable()">
                            <option value="">Semua Kategori Mitra</option>
                            <option value="DUDI / Swasta">DUDI / Swasta (Industri)</option>
                            <option value="Instansi Pemerintah">Instansi Pemerintah</option>
                            <option value="BUMN">BUMN / BUMD</option>
                            <option value="Perguruan Tinggi">Perguruan Tinggi</option>
                            <option value="Organisasi">Organisasi / Komunitas</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold mb-1 text-secondary small">Status Kemitraan</label>
                        <select id="partnerStatusFilter" class="form-select form-select-sm" onchange="filterPartnersTable()">
                            <option value="">Semua Status</option>
                            <option value="Terverifikasi Sah">Terverifikasi Sah</option>
                            <option value="Tahap FSM">Tahap FSM</option>
                            <option value="Peringatan Masa">Peringatan Masa</option>
                            <option value="Mitra Terdaftar">Mitra Terdaftar</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold mb-1 text-secondary small">Pencarian Mitra &amp; PIC</label>
                        <div class="input-group input-group-sm">
                            <input type="text" id="partnerSearchInput" class="form-control" placeholder="Cari nama mitra, PIC, kota, bidang..." onkeyup="filterPartnersTable()">
                            <button type="button" class="btn btn-outline-secondary" onclick="resetPartnerFilters()" title="Bersihkan Pencarian">
                                <i class="bi bi-x-circle"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. TOOLBAR AKSI & TABEL DATA MITRA (PERSIS MENU 4) -->
            <div class="card-body p-3">
                <div class="mb-3 d-flex flex-wrap align-items-center justify-content-between gap-2">
                    <div class="d-flex flex-wrap align-items-center gap-2">
                        <button type="button" class="btn btn-primary btn-sm fw-semibold shadow-sm" data-bs-toggle="modal" data-bs-target="#createPartnerModal">
                            <i class="bi bi-plus-lg me-1"></i> Tambah Mitra
                        </button>
                        <a href="{{ route('simkerma.index', ['menu' => 4, 'view' => 'new']) }}" class="btn btn-outline-dark btn-sm fw-semibold shadow-sm">
                            <i class="bi bi-file-earmark-plus me-1"></i> Ajukan Naskah Baru
                        </a>
                        <button type="button" class="btn btn-success btn-sm fw-semibold shadow-sm text-white" onclick="exportTableToExcel('partnerTable', 'Data_Master_Mitra')">
                            <i class="bi bi-file-earmark-excel me-1"></i> Excel
                        </button>
                        <button type="button" class="btn btn-info btn-sm fw-semibold shadow-sm text-white" onclick="resetPartnerFilters()">
                            <i class="bi bi-arrow-clockwise me-1"></i> Reload
                        </button>
                    </div>
                    <div>
                        <span class="badge bg-light text-dark border px-3 py-2 fw-semibold">
                            Total: <span id="partnerVisibleCount" class="fw-bold text-primary">{{ $partners->count() }}</span> Mitra Terdaftar
                        </span>
                    </div>
                </div>

                <!-- TABEL PENGELOLAAN DATA MITRA PROPORSI RAPI (SESUAI STANDAR MENU 4) -->
                <div class="table-responsive" style="border: 0">
                    <table class="w-100 table table-bordered table-striped table-hover align-middle mb-0" id="partnerTable">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center py-1 px-2" style="width: 45px; font-size: 0.82rem; line-height: 1.2;">No.</th>
                                <th class="text-start py-1 px-2" style="min-width: 160px; font-size: 0.82rem; line-height: 1.2;">Mitra / Instansi</th>
                                <th class="text-center py-1 px-2" style="width: 125px; font-size: 0.82rem; line-height: 1.2;">Kategori</th>
                                <th class="text-start py-1 px-2" style="min-width: 135px; font-size: 0.82rem; line-height: 1.2;">Unit Terkait</th>
                                <th class="text-start py-1 px-2" style="min-width: 145px; font-size: 0.82rem; line-height: 1.2;">Lingkup Kerma</th>
                                <th class="text-center py-1 px-2" style="width: 105px; font-size: 0.82rem; line-height: 1.2;">Naskah</th>
                                <th class="text-center py-1 px-2" style="width: 115px; font-size: 0.82rem; line-height: 1.2;">Status</th>
                                <th class="text-center py-1 px-2" style="width: 105px; font-size: 0.82rem; line-height: 1.2;">Aksi</th>
                            </tr>
                        </thead>
                    <tbody>
                        @forelse ($partners as $partner)
                            @php
                                $pDocs = $partner->documents;
                                $firstDoc = $pDocs->first();
                                $hasActive = $pDocs->where('status', 'ACTIVE')->isNotEmpty();
                                $hasWarning = $pDocs->where('status', 'WARNING')->isNotEmpty();
                                $types = $pDocs->pluck('doc_type')->unique();
                                $units = $pDocs->pluck('unit')->unique();

                                $stLabel = $hasActive ? 'Terverifikasi Sah' : ($hasWarning ? 'Peringatan Masa' : ($pDocs->isNotEmpty() ? 'Tahap FSM' : 'Mitra Terdaftar'));
                            @endphp
                            <tr class="partner-row" data-category="{{ $partner->category }}" data-status="{{ $stLabel }}">
                                <!-- 1. No. -->
                                <td class="text-center text-muted fw-semibold" style="font-size: 0.82rem;">{{ $loop->iteration }}</td>

                                <!-- 2. Mitra / Instansi -->
                                <td>
                                    <div class="fw-bold text-dark fs-6">{{ $partner->name }}</div>
                                    <div class="text-muted small mt-1" style="font-size: 0.72rem;">
                                        <i class="bi bi-geo-alt text-danger me-1"></i>{{ $partner->city ?: 'Kota Manado' }}
                                        <span class="badge bg-light text-primary border ms-1" style="font-size: 0.65rem;">{{ $partner->scope_coverage }}</span>
                                    </div>
                                </td>

                                <!-- 3. Kategori & Verifikasi -->
                                <td class="text-center">
                                    <span class="badge bg-light text-dark border text-wrap d-inline-block" style="font-size: 0.68rem; max-width: 120px; line-height: 1.25;">{{ $partner->category ?: 'Kemitraan Strategis' }}</span>
                                    <div class="mt-1">
                                        <span class="badge bg-success-subtle text-success border border-success" style="font-size: 0.62rem;">
                                            <i class="bi bi-patch-check-fill me-1"></i>{{ $partner->status_verification ?: 'Profil Terverifikasi' }}
                                        </span>
                                    </div>
                                </td>

                                <!-- 4. Unit Terkait -->
                                <td>
                                    @if ($units->isNotEmpty())
                                        @if ($units->count() > 2)
                                            <div class="text-dark fw-semibold" style="max-width: 140px;" title="{{ $units->join(', ') }}">
                                                {{ $units->take(2)->join(', ') }}
                                                <span class="badge bg-light text-secondary border font-monospace" style="font-size: 0.62rem;">+{{ $units->count() - 2 }}</span>
                                            </div>
                                        @else
                                            <div class="text-dark fw-semibold" style="max-width: 140px;">{{ $units->join(', ') }}</div>
                                        @endif
                                        <div class="text-muted small" style="font-size: 0.72rem;">
                                            Dokumen: <span class="font-monospace text-primary">{{ $firstDoc->doc_number ?: $firstDoc->doc_id }}</span>
                                        </div>
                                    @else
                                        <div class="text-muted fst-italic" style="font-size: 0.75rem;">Belum ada naskah terkait</div>
                                        <div class="text-muted small" style="font-size: 0.70rem;">Siap diajukan di Menu 04</div>
                                    @endif
                                </td>

                                <!-- 5. Lingkup Kerma -->
                                <td>
                                    <div class="text-secondary" style="font-size: 0.75rem; max-width: 150px; line-height: 1.35;">
                                        {{ Str::limit($partner->profile_desc ?: ($firstDoc ? $firstDoc->scope : 'Kerja sama kemitraan institusional Tridharma'), 75) }}
                                    </div>
                                </td>

                                <!-- 6. Naskah Kerma -->
                                <td class="text-center">
                                    @if ($pDocs->isNotEmpty())
                                        <span class="badge bg-dark rounded-pill">{{ $pDocs->count() }} Dokumen</span>
                                        <div class="small text-muted mt-1" style="font-size: 0.7rem;">
                                            {{ $types->join(', ') }}
                                        </div>
                                    @else
                                        <span class="badge bg-light text-muted border">0 Dokumen</span>
                                    @endif
                                </td>

                                <!-- 7. Status -->
                                <td class="text-center">
                                    @if ($hasActive)
                                        <span class="badge bg-success py-1 px-2 shadow-2xs d-inline-block text-nowrap fw-semibold" style="font-size: 0.68rem;"><i class="bi bi-check-circle-fill me-1"></i>Aktif / Sah</span>
                                    @elseif ($hasWarning)
                                        <span class="badge bg-warning text-dark py-1 px-2 border shadow-2xs d-inline-block text-nowrap fw-semibold" style="font-size: 0.68rem;"><i class="bi bi-exclamation-triangle-fill me-1"></i>Peringatan</span>
                                    @elseif ($pDocs->isNotEmpty())
                                        <span class="badge bg-primary py-1 px-2 shadow-2xs d-inline-block text-nowrap fw-semibold" style="font-size: 0.68rem;"><i class="bi bi-hourglass-split me-1"></i>Tahap FSM</span>
                                    @else
                                        <span class="badge bg-secondary py-1 px-2 shadow-2xs d-inline-block text-nowrap fw-semibold" style="font-size: 0.68rem;"><i class="bi bi-journal-check me-1"></i>Terdaftar</span>
                                    @endif
                                </td>

                                <!-- 8. Aksi -->
                                <td class="text-center">
                                    <div class="d-inline-flex justify-content-center align-items-center gap-1">
                                        <button type="button" class="btn btn-dark btn-sm py-1 px-2 fw-semibold shadow-2xs d-inline-flex align-items-center gap-1" style="font-size: 0.72rem;" data-bs-toggle="modal" data-bs-target="#partnerModal_{{ $partner->id }}" title="Lihat Profil &amp; Riwayat Dokumen">
                                            <i class="bi bi-eye-fill text-warning"></i><span>Profil</span>
                                        </button>
                                        <div class="dropdown">
                                            <button class="btn btn-outline-secondary btn-sm py-1 px-2 shadow-2xs" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="font-size: 0.72rem;" title="Opsi Tindakan Lainnya">
                                                <i class="bi bi-three-dots-vertical"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end shadow border-0 small py-1" style="font-size: 0.78rem; min-width: 175px;">
                                                <li>
                                                    <button type="button" class="dropdown-item py-1.5 px-3 text-dark d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#editPartnerModal_{{ $partner->id }}">
                                                        <i class="bi bi-pencil-square text-warning"></i> Edit Data Mitra
                                                    </button>
                                                </li>
                                                @if ($firstDoc)
                                                    <li>
                                                        <a class="dropdown-item py-1.5 px-3 text-primary d-flex align-items-center gap-2" href="{{ route('simkerma.index', ['menu' => 4, 'doc' => $firstDoc->doc_id]) }}">
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
                                        <option value="Perguruan Tinggi" selected>Perguruan Tinggi</option>
                                        <option value="DUDI / Swasta">DUDI / Swasta (Industri)</option>
                                        <option value="Instansi Pemerintah">Instansi Pemerintah</option>
                                        <option value="BUMN / BUMD">BUMN / BUMD</option>
                                        <option value="Organisasi / Komunitas / Lainnya">Organisasi / Komunitas / Lainnya</option>
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

        {{-- MODAL EDIT DATA MITRA & MODAL PROFIL RIWAYAT (DITEMPATKAN DI LUAR TABEL) --}}
        @foreach ($partners as $partner)
            @php
                $pDocs = $partner->documents;
                $firstDoc = $pDocs->first();
                $types = $pDocs->pluck('doc_type')->unique();
                $units = $pDocs->pluck('unit')->unique();
                $hasActive = $pDocs->where('status', 'ACTIVE')->isNotEmpty();
                $hasWarning = $pDocs->where('status', 'WARNING')->isNotEmpty();
            @endphp

            <!-- MODAL EDIT DATA MITRA -->
            <div class="modal fade" id="editPartnerModal_{{ $partner->id }}" tabindex="-1" aria-labelledby="editPartnerModalLabel_{{ $partner->id }}" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                    <div class="modal-content border-0 shadow-lg">
                        <form method="POST" action="{{ route('simkerma.update-partner') }}" class="d-flex flex-column overflow-hidden h-100">
                            @csrf
                            <input type="hidden" name="id" value="{{ $partner->id }}">
                            <div class="modal-header bg-warning py-3 text-dark">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="p-2 bg-dark rounded text-warning"><i class="bi bi-pencil-square fs-4"></i></div>
                                    <div>
                                        <h6 class="modal-title fw-bold mb-0" id="editPartnerModalLabel_{{ $partner->id }}">Edit Data Mitra: {{ $partner->name }}</h6>
                                        <small class="text-dark-50">Sinkronisasi Master Data Mitra ke Seluruh Naskah di SIMKERMA</small>
                                    </div>
                                </div>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>

                            <div class="modal-body p-4 small">
                                <div class="alert alert-warning py-2 px-3 mb-3 d-flex align-items-center gap-2" style="font-size: 0.78rem;">
                                    <i class="bi bi-exclamation-triangle-fill fs-5 text-dark"></i>
                                    <div>
                                        <strong>Integritas Sinkronisasi Otomatis:</strong> Pembaruan nama atau kategori mitra di formulir ini akan secara langsung disinkronkan ke seluruh naskah dokumen terkait di Menu 1 (Dashboard), Menu 3 (Repositori), Menu 4 (FSM), Menu 5 (Kegiatan), dan Menu 7 (Audit Trail).
                                    </div>
                                </div>

                                <div class="row g-3">
                                    <div class="col-md-7">
                                        <label class="form-label fw-bold text-dark">Nama Lembaga / Instansi Mitra <span class="text-danger">*</span></label>
                                        <input type="text" name="name" class="form-control form-control-sm" value="{{ $partner->name }}" required>
                                    </div>
                                    <div class="col-md-5">
                                        <label class="form-label fw-bold text-dark">Kategori Mitra <span class="text-danger">*</span></label>
                                        <select name="category" class="form-select form-select-sm" required>
                                            <option value="DUDI / Swasta" {{ $partner->category === 'DUDI / Swasta' ? 'selected' : '' }}>DUDI / Swasta (Industri)</option>
                                            <option value="Instansi Pemerintah" {{ $partner->category === 'Instansi Pemerintah' ? 'selected' : '' }}>Instansi Pemerintah</option>
                                            <option value="BUMN / BUMD" {{ $partner->category === 'BUMN / BUMD' || $partner->category === 'BUMN' ? 'selected' : '' }}>BUMN / BUMD</option>
                                            <option value="Perguruan Tinggi" {{ $partner->category === 'Perguruan Tinggi' ? 'selected' : '' }}>Perguruan Tinggi</option>
                                            <option value="Organisasi / Komunitas / Lainnya" {{ $partner->category === 'Organisasi / Komunitas / Lainnya' ? 'selected' : '' }}>Organisasi / Komunitas / Lainnya</option>
                                        </select>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-bold text-dark">Cakupan Wilayah</label>
                                        <select name="scope_coverage" class="form-select form-select-sm">
                                            <option value="Dalam Negeri (Nasional/Lokal)" {{ $partner->scope_coverage === 'Dalam Negeri (Nasional/Lokal)' ? 'selected' : '' }}>Dalam Negeri (Nasional/Lokal)</option>
                                            <option value="Internasional" {{ $partner->scope_coverage === 'Internasional' ? 'selected' : '' }}>Internasional</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold text-dark">Kota / Domisili Kantor</label>
                                        <input type="text" name="city" class="form-control form-control-sm" value="{{ $partner->city }}">
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label fw-bold text-dark">Alamat Lengkap Kantor</label>
                                        <textarea name="address" class="form-control form-control-sm" rows="2">{{ $partner->address }}</textarea>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-bold text-dark">Nomor Telepon Kantor Resmi</label>
                                        <input type="text" name="phone" class="form-control form-control-sm" value="{{ $partner->phone }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold text-dark">Email Resmi Kemitraan</label>
                                        <input type="email" name="email" class="form-control form-control-sm" value="{{ $partner->email }}">
                                    </div>

                                    <div class="col-12 pt-2 border-top">
                                        <h6 class="fw-bold text-primary mb-2" style="font-size: 0.85rem;"><i class="bi bi-person-lines-fill me-1"></i> Data Penanggung Jawab (PIC Mitra)</h6>
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label fw-bold text-dark">Nama PIC Mitra</label>
                                        <input type="text" name="pic_name" class="form-control form-control-sm" value="{{ $partner->pic_name }}">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-bold text-dark">Jabatan PIC</label>
                                        <input type="text" name="pic_title" class="form-control form-control-sm" value="{{ $partner->pic_title }}">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-bold text-dark">No. WhatsApp / HP PIC</label>
                                        <input type="text" name="pic_contact" class="form-control form-control-sm" value="{{ $partner->pic_contact }}">
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label fw-bold text-dark">Deskripsi Profil Lembaga Mitra</label>
                                        <textarea name="profile_desc" class="form-control form-control-sm" rows="2">{{ $partner->profile_desc }}</textarea>
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

            <!-- MODAL DETAIL PROFIL & RIWAYAT MITRA -->
            <div class="modal fade" id="partnerModal_{{ $partner->id }}" tabindex="-1" aria-labelledby="partnerModalLabel_{{ $partner->id }}" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                    <div class="modal-content border-0 shadow-lg">
                        <div class="modal-header bg-dark text-white py-3">
                            <div class="d-flex align-items-center gap-2">
                                <div class="p-2 bg-light rounded text-dark"><i class="bi bi-building fs-4"></i></div>
                                <div>
                                    <h6 class="modal-title fw-bold mb-0" id="partnerModalLabel_{{ $partner->id }}">{{ $partner->name }}</h6>
                                    <small class="text-white-50">{{ $partner->category }} &bull; {{ $partner->scope_coverage }}</small>
                                </div>
                            </div>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>

                        <div class="modal-body p-4">
                            <!-- KARTU 1: PROFIL & IDENTITAS LEMBAGA MITRA -->
                            <div class="card bg-light border mb-3">
                                <div class="card-header bg-white py-2 px-3 fw-bold small text-primary d-flex justify-content-between align-items-center">
                                    <span><i class="bi bi-info-circle-fill me-1"></i> Identitas &amp; Profil Lembaga Mitra</span>
                                    <span class="badge bg-success-subtle text-success border border-success" style="font-size: 0.65rem;">{{ $partner->status_verification }}</span>
                                </div>
                                <div class="card-body p-3 small">
                                    <div class="row g-3">
                                        <div class="col-sm-6">
                                            <div class="text-muted fw-bold" style="font-size: 0.72rem;">NAMA INSTANSI RESMI:</div>
                                            <div class="fw-bold text-dark fs-6">{{ $partner->name }}</div>
                                            <div class="mt-2 text-muted fw-bold" style="font-size: 0.72rem;">JENIS &amp; KATEGORI MITRA:</div>
                                            <div><span class="badge bg-light text-dark border">{{ $partner->category }}</span> &bull; {{ $partner->scope_coverage }}</div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="text-muted fw-bold" style="font-size: 0.72rem;">ALAMAT LENGKAP KANTOR:</div>
                                            <div class="text-dark">{{ $partner->address ?: 'Kawasan Bisnis & Industri, ' . ($partner->city ?: 'Kota Manado') }}</div>
                                            <div class="mt-2 text-muted fw-bold" style="font-size: 0.72rem;">KONTAK RESMI PERUSAHAAN / INSTANSI:</div>
                                            <div>
                                                <i class="bi bi-telephone-fill text-primary me-1"></i> {{ $partner->phone ?: '(0431) 880-1234' }}<br>
                                                <i class="bi bi-envelope-fill text-danger me-1"></i> <a href="mailto:{{ $partner->email }}" class="text-decoration-none">{{ $partner->email }}</a>
                                            </div>
                                        </div>
                                        <div class="col-12 pt-1 border-top">
                                            <div class="text-muted fw-bold" style="font-size: 0.72rem;">PROFIL &amp; BIDANG OPERASIONAL MITRA:</div>
                                            <div class="text-secondary mt-1">{{ $partner->profile_desc ?: 'Mitra institusi terdaftar pada basis data SIMKERMA UNIPAR.' }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- KARTU 2: PENANGGUNG JAWAB (PIC MITRA) & UNIT PENGUSUL UNIPAR -->
                            <div class="card bg-light border mb-3">
                                <div class="card-header bg-white py-2 px-3 fw-bold small text-dark d-flex justify-content-between align-items-center">
                                    <span><i class="bi bi-person-badge-fill text-primary me-1"></i> Penanggung Jawab (PIC Mitra) &amp; Relasi Kampus</span>
                                    <span class="badge bg-primary text-white" style="font-size: 0.65rem;">PIC AKTIF</span>
                                </div>
                                <div class="card-body p-3 small">
                                    <div class="row g-2">
                                        <div class="col-sm-6">
                                            <div class="p-2 bg-white rounded border">
                                                <div class="text-muted fw-bold" style="font-size: 0.7rem;">PENANGGUNG JAWAB DARI PIHAK MITRA:</div>
                                                <div class="fw-bold text-dark fs-6 mt-1">{{ $partner->pic_name ?: 'Bpk/Ibu Narahubung Kemitraan' }}</div>
                                                <div class="text-muted">{{ $partner->pic_title ?: 'Divisi Kerja Sama & Hubungan Eksternal' }}</div>
                                                <div class="mt-2">
                                                    <span class="badge bg-success-subtle text-success border border-success">
                                                        <i class="bi bi-whatsapp me-1"></i> WhatsApp: {{ $partner->pic_contact ?: '0812-4400-5500' }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="p-2 bg-white rounded border">
                                                <div class="text-muted fw-bold" style="font-size: 0.7rem;">UNIT PENGUSUL DI UNIPAR:</div>
                                                <div class="fw-bold text-primary mt-1">{{ $units->isNotEmpty() ? $units->join(', ') : 'Belum Ada Naskah Diajukan' }}</div>
                                                <div class="text-muted mt-2" style="font-size: 0.7rem;">Status Kemitraan:</div>
                                                <div>
                                                    @if ($hasActive)
                                                        <span class="badge bg-success"><i class="bi bi-check-circle-fill me-1"></i>Terverifikasi Sah &amp; Berjalan</span>
                                                    @elseif ($hasWarning)
                                                        <span class="badge bg-warning text-dark"><i class="bi bi-exclamation-triangle-fill me-1"></i>Masa Berlaku Mendekati Ambang Batas</span>
                                                    @elseif ($pDocs->isNotEmpty())
                                                        <span class="badge bg-info text-dark"><i class="bi bi-hourglass-split me-1"></i>Dalam Proses Alur FSM</span>
                                                    @else
                                                        <span class="badge bg-secondary"><i class="bi bi-journal-check me-1"></i>Mitra Terdaftar di Sistem</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- KARTU 3: TABEL RIWAYAT LENGKAP SELURUH NASKAH KERJA SAMA (MOU, MOA, IA) -->
                            <div class="card border">
                                <div class="card-header bg-white py-2 px-3 fw-bold small text-dark d-flex justify-content-between align-items-center">
                                    <span>
                                        <i class="bi bi-clock-history text-primary me-1"></i> 
                                        Riwayat Kerja Sama dengan {{ $partner->name }} ({{ $pDocs->count() }} Naskah)
                                    </span>
                                    <span class="badge bg-dark rounded-pill">{{ $types->isNotEmpty() ? $types->join(', ') : '0 Dokumen' }}</span>
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
                                        <tbody>
                                            @forelse ($pDocs as $doc)
                                                @php
                                                    $docBadge = match($doc->status) {
                                                        'DRAFT' => 'bg-secondary text-white',
                                                        'REVIEW' => 'bg-info text-dark',
                                                        'VERIFIED' => 'bg-primary text-white',
                                                        'ACTIVE' => 'bg-success text-white',
                                                        'WARNING' => 'bg-warning text-dark',
                                                        'EXPIRED' => 'bg-danger text-white',
                                                        'CANCELLED' => 'bg-dark text-white',
                                                        default => 'bg-secondary text-white'
                                                    };
                                                @endphp
                                                <tr>
                                                    <td>
                                                        <div class="fw-bold text-dark">{{ $doc->doc_number ?: 'Dalam Proses' }}</div>
                                                        <span class="badge bg-light text-muted border font-monospace" style="font-size: 0.65rem;">{{ $doc->reg_id }}</span>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-dark" style="font-size: 0.68rem;">{{ $doc->doc_type }}</span>
                                                    </td>
                                                    <td>
                                                        <div class="text-secondary" style="max-width: 220px;">{{ Str::limit($doc->scope, 65) }}</div>
                                                        <div class="text-muted" style="font-size: 0.68rem;">Unit: {{ $doc->unit }}</div>
                                                    </td>
                                                    <td>
                                                        <div>{{ $doc->validity ?: 'Belum Ditetapkan' }}</div>
                                                        @if ($doc->date_signed)
                                                            <small class="text-muted" style="font-size: 0.68rem;">TTD: {{ \Carbon\Carbon::parse($doc->date_signed)->format('d M Y') }}</small>
                                                        @endif
                                                    </td>
                                                    <td class="text-center">
                                                        <span class="badge {{ $docBadge }} py-1 px-2 fw-bold" style="font-size: 0.7rem;">
                                                            {{ $doc->status }}
                                                        </span>
                                                    </td>
                                                    <td class="text-end">
                                                        <a href="{{ route('simkerma.index', ['menu' => 4, 'doc' => $doc->doc_id]) }}" class="btn btn-outline-dark btn-sm py-0 px-2" style="font-size: 0.68rem;" title="Kelola Alur FSM">
                                                            <i class="bi bi-sliders me-1"></i>FSM
                                                        </a>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="6" class="text-center py-3 text-muted">Belum ada naskah kerja sama yang tercatat untuk mitra ini.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer bg-light py-2 px-3 d-flex justify-content-between">
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-warning btn-sm fw-semibold text-dark" onclick="switchPartnerModal('partnerModal_{{ $partner->id }}', 'editPartnerModal_{{ $partner->id }}')">
                                    <i class="bi bi-pencil-square me-1"></i> Edit Data Mitra
                                </button>
                                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="window.print()">
                                    <i class="bi bi-printer me-1"></i> Cetak Profil Mitra
                                </button>
                                <a href="{{ route('simkerma.index', ['menu' => 4, 'view' => 'new', 'partner_name' => $partner->name]) }}" class="btn btn-outline-primary btn-sm">
                                    <i class="bi bi-plus-circle me-1"></i> Ajukan Naskah Baru
                                </a>
                            </div>
                            <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach

        @push('scripts')
        <script>
        function filterPartnersTable() {
            const query = (document.getElementById('partnerSearchInput').value || '').toLowerCase().trim();
            const categoryFilter = (document.getElementById('partnerCategoryFilter').value || '').toLowerCase();
            const statusFilter = (document.getElementById('partnerStatusFilter').value || '').toLowerCase();
            const rows = document.querySelectorAll('#partnerTable tbody tr.partner-row');
            const noMatchRow = document.getElementById('noPartnerMatchRow');
            const visibleBadge = document.getElementById('partnerVisibleCount');

            let visibleCount = 0;
            rows.forEach(function(row) {
                const text = row.innerText.toLowerCase();
                const rowCat = (row.getAttribute('data-category') || '').toLowerCase();
                const rowStatus = (row.getAttribute('data-status') || '').toLowerCase();

                const matchesQuery = !query || text.includes(query);
                const matchesCat = !categoryFilter || rowCat.includes(categoryFilter);
                const matchesStatus = !statusFilter || rowStatus.includes(statusFilter);

                if (matchesQuery && matchesCat && matchesStatus) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });

            if (noMatchRow) {
                noMatchRow.style.display = (visibleCount === 0) ? '' : 'none';
            }
            if (visibleBadge) {
                visibleBadge.textContent = visibleCount;
            }
        }

        function resetPartnerFilters() {
            const searchInput = document.getElementById('partnerSearchInput');
            const catFilter = document.getElementById('partnerCategoryFilter');
            const statFilter = document.getElementById('partnerStatusFilter');

            if (searchInput) searchInput.value = '';
            if (catFilter) catFilter.value = '';
            if (statFilter) statFilter.value = '';
            filterPartnersTable();
        }

        function switchPartnerModal(closeModalId, openModalId) {
            const closeEl = document.getElementById(closeModalId);
            const openEl = document.getElementById(openModalId);
            if (!closeEl || !openEl) return;

            const closeInst = bootstrap.Modal.getInstance(closeEl);
            if (closeInst) {
                const handler = function() {
                    closeEl.removeEventListener('hidden.bs.modal', handler);
                    const openInst = bootstrap.Modal.getInstance(openEl) || new bootstrap.Modal(openEl);
                    openInst.show();
                };
                closeEl.addEventListener('hidden.bs.modal', handler);
                closeInst.hide();
            } else {
                const openInst = bootstrap.Modal.getInstance(openEl) || new bootstrap.Modal(openEl);
                openInst.show();
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
        @endphp

        <div class="card card-wireframe shadow-sm border mb-4">
            <div class="card-header bg-white py-3 border-bottom d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2">
                <div>
                    <div class="d-flex align-items-center gap-2">
                        <h6 class="mb-0 fw-bold text-uppercase">
                            <i class="bi bi-file-earmark-text text-primary me-1"></i> Repositori Dokumen Resmi Kerja Sama
                        </h6>
                        <span class="badge bg-primary text-white font-monospace" style="font-size: 0.65rem;">{{ $documents->count() }} NASKAH TERDAFTAR</span>
                    </div>
                    <small class="text-muted">Penyimpanan Terorganisasi Naskah MoU, MoA/PKS, IA, Dokumen Pendukung &amp; Verifikasi Digital</small>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('simkerma.index', ['menu' => 4, 'view' => 'new']) }}" class="btn btn-primary btn-sm fw-semibold shadow-sm">
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
                                MoU ({{ $mouCount }})
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link repo-tab-btn" data-type="MoA">
                                PKS / MoA ({{ $moaCount }})
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link repo-tab-btn" data-type="IA">
                                IA ({{ $iaCount }})
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
                        <div class="input-group input-group-sm" style="width: 245px;">
                            <span class="input-group-text bg-white border-end-0 text-muted">
                                <i class="bi bi-search"></i>
                            </span>
                            <input type="text" id="repoSearchInput" class="form-control border-start-0 border-end-0" placeholder="Cari no surat, mitra, judul..." autocomplete="off">
                            <button class="btn btn-outline-secondary border-start-0 d-none" type="button" id="btnRepoClearSearch" title="Hapus Pencarian">
                                <i class="bi bi-x-lg"></i>
                            </button>
                        </div>
                        <button type="button" class="btn btn-outline-secondary btn-sm btn-repo-xs d-inline-flex align-items-center gap-1 bg-white" id="btnToggleRepoAdvancedFilter" style="height: 31px;" title="Filter Lanjutan (Unit, Kategori, Tahun, File, Status)">
                            <i class="bi bi-funnel"></i><span>Filter</span>
                            <span class="badge bg-primary text-white d-none" id="repoActiveFilterBadge" style="font-size: 0.62rem; padding: 2px 5px;">0</span>
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
                        <label class="form-label mb-1 text-muted fw-semibold" style="font-size: 0.72rem;">Kelengkapan PDF</label>
                        <select id="repoFileFilter" class="form-select form-select-sm" style="font-size: 0.78rem;">
                            <option value="ALL">Semua Berkas</option>
                            <option value="HAS_FILE">Sudah Ada PDF</option>
                            <option value="NO_FILE">Belum Ada File</option>
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
                        </select>
                    </div>
                </div>
            </div>

            <!-- TABEL REPOSITORI DOKUMEN DINAMIS -->
            <div class="table-responsive">
                <table class="table table-wireframe table-hover table-sm align-middle mb-0 small" id="repoTable">
                    <colgroup>
                        <col style="width: 19%;">
                        <col style="width: 29%;">
                        <col style="width: 12%;">
                        <col style="width: 18%;">
                        <col style="width: 13%;">
                        <col style="width: 9%;">
                    </colgroup>
                    <thead class="bg-light">
                        <tr>
                            <th class="repo-sortable" data-sort-key="doc_number" title="Klik untuk mengurutkan berdasarkan Nomor Dokumen">Nomor &amp; Jenis <i class="bi bi-arrow-down-up repo-sort-icon"></i></th>
                            <th class="repo-sortable" data-sort-key="title" title="Klik untuk mengurutkan berdasarkan Judul Kerja Sama (A-Z / Z-A)">Judul Kerja Sama &amp; Mitra <i class="bi bi-arrow-down-up repo-sort-icon"></i></th>
                            <th class="repo-sortable sort-active" data-sort-key="date" data-sort-dir="desc" title="Klik untuk mengurutkan Tanggal TTD (Terbaru / Terlama)">TTD &amp; Durasi <i class="bi bi-sort-down repo-sort-icon"></i></th>
                            <th>Penandatangan</th>
                            <th class="repo-sortable" data-sort-key="audit" title="Klik untuk mengurutkan berdasarkan Jumlah Riwayat">Berkas &amp; Audit <i class="bi bi-arrow-down-up repo-sort-icon"></i></th>
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

                                // Verifikasi fisik berkas PDF riil di storage/public
                                $validFilePath = null;
                                $hasRealFile = false;
                                $fileLabel = 'Draf PDF';
                                $fileBadgeClass = 'badge-soft-primary';
                                $fileSizeText = '-';

                                if (!empty($doc->signed_file)) {
                                    $p1 = public_path($doc->signed_file);
                                    $p2 = public_path('uploads/documents/' . $doc->signed_file);
                                    if (file_exists($p1) && is_file($p1)) {
                                        $validFilePath = asset($doc->signed_file);
                                        $hasRealFile = true;
                                        $fileLabel = 'Naskah PDF';
                                        $fileBadgeClass = 'badge-soft-success';
                                        $b = filesize($p1);
                                        $fileSizeText = $b >= 1048576 ? round($b / 1048576, 1) . ' MB' : round($b / 1024, 0) . ' KB';
                                    } elseif (file_exists($p2) && is_file($p2)) {
                                        $validFilePath = asset('uploads/documents/' . $doc->signed_file);
                                        $hasRealFile = true;
                                        $fileLabel = 'Naskah PDF';
                                        $fileBadgeClass = 'badge-soft-success';
                                        $b = filesize($p2);
                                        $fileSizeText = $b >= 1048576 ? round($b / 1048576, 1) . ' MB' : round($b / 1024, 0) . ' KB';
                                    }
                                }

                                if (!$hasRealFile && !empty($doc->draft_file)) {
                                    $dp1 = public_path($doc->draft_file);
                                    $dp2 = public_path('uploads/documents/' . $doc->draft_file);
                                    if (file_exists($dp1) && is_file($dp1)) {
                                        $validFilePath = asset($doc->draft_file);
                                        $hasRealFile = true;
                                        $fileLabel = 'Draf PDF';
                                        $fileBadgeClass = 'badge-soft-primary';
                                        $b = filesize($dp1);
                                        $fileSizeText = $b >= 1048576 ? round($b / 1048576, 1) . ' MB' : round($b / 1024, 0) . ' KB';
                                    } elseif (file_exists($dp2) && is_file($dp2)) {
                                        $validFilePath = asset('uploads/documents/' . $doc->draft_file);
                                        $hasRealFile = true;
                                        $fileLabel = 'Draf PDF';
                                        $fileBadgeClass = 'badge-soft-primary';
                                        $b = filesize($dp2);
                                        $fileSizeText = $b >= 1048576 ? round($b / 1048576, 1) . ' MB' : round($b / 1024, 0) . ' KB';
                                    }
                                }

                                $displayFileName = basename($doc->signed_file ?: ($doc->draft_file ?: ($doc->doc_id . '.pdf')));

                                // Normalisasi Penandatangan Pihak I (hindari duplikasi jabatan seperti "Rektor Universitas Parna Raya" + "Rektor")
                                $signer1 = trim((string)($doc->first_party_signer ?: 'Rektor UNIPAR'));
                                $pos1 = trim((string)($doc->first_party_position ?: ''));
                                $showPos1 = ($pos1 !== '' && stripos($signer1, $pos1) === false);

                                $signer2 = trim((string)($doc->second_party_signer ?: ('Pimpinan ' . $doc->partner_name)));
                                $docDateRef = $doc->date_signed ?: ($doc->date_approved ?: $doc->created_at);
                                $docYearAttr = $docDateRef ? $docDateRef->format('Y') : '';
                                $docSortTs = $docDateRef ? $docDateRef->timestamp : 0;
                                $docEndTs = $doc->end_date ? $doc->end_date->timestamp : 9999999999;
                            @endphp
                            <tr class="repo-row" 
                                data-type="{{ $doc->doc_type }}" 
                                data-status="{{ $doc->status }}" 
                                data-legacy="{{ $doc->is_legacy ? '1' : '0' }}" 
                                data-track="{{ $doc->track }}"
                                data-unit="{{ strtolower(trim((string)$doc->unit)) }}"
                                data-category="{{ strtolower(trim((string)$doc->partner_category)) }}"
                                data-year="{{ $docYearAttr }}"
                                data-has-file="{{ $hasRealFile ? '1' : '0' }}"
                                data-sort-date="{{ $docSortTs }}"
                                data-end-date="{{ $docEndTs }}"
                                data-doc-number="{{ strtolower(trim((string)$doc->doc_number)) }}"
                                data-audit-count="{{ $doc->auditTrails->count() }}"
                                data-title="{{ strtolower(trim((string)($doc->title ?: $doc->scope))) }}">
                                <td>
                                    <div style="font-size: 0.74rem; line-height: 1.3;">
                                        <span class="text-muted">I:</span>
                                        <strong class="text-dark">{{ (!empty($doc->doc_number) && $doc->doc_number !== '-') ? $doc->doc_number : '-' }}</strong>
                                    </div>
                                    <div class="text-muted mt-1" style="font-size: 0.71rem; line-height: 1.3;">
                                        <span>II:</span>
                                        <span class="{{ (!empty($doc->partner_doc_number) && $doc->partner_doc_number !== '-') ? 'text-dark fw-medium' : 'text-muted' }}">{{ (!empty($doc->partner_doc_number) && $doc->partner_doc_number !== '-') ? $doc->partner_doc_number : '-' }}</span>
                                    </div>
                                    <div class="mt-1 d-flex align-items-center gap-1 flex-wrap">
                                        <span class="badge bg-light text-dark border badge-repo-compact">{{ $doc->doc_type }}</span>
                                        <span class="badge {{ $statusBadgeClass }} badge-repo-compact" title="Status FSM: {{ $doc->status }}">{{ $statusLabel }}</span>
                                    </div>
                                </td>
                                <td>
                                    <a href="{{ route('simkerma.index', ['menu' => 4, 'view' => 'detail', 'doc' => $doc->doc_id]) }}" class="repo-doc-title fw-semibold text-dark text-decoration-none d-block mb-1" style="line-height: 1.35; font-size: 0.80rem;" title="Klik untuk membuka detail dokumen: {{ $doc->title ?: $doc->scope }}">
                                        {{ Str::limit($doc->title ?: $doc->scope, 110) }}
                                    </a>
                                    <div class="text-muted text-truncate" style="font-size: 0.74rem;" title="{{ $doc->partner_name }}">
                                        <i class="bi bi-building me-1 text-primary"></i><strong class="text-dark">{{ $doc->partner_name }}</strong>
                                    </div>
                                    <div class="text-muted text-truncate" style="font-size: 0.70rem;" title="{{ $doc->partner_category }}{{ $doc->unit ? ' • ' . $doc->unit : '' }}">
                                        <span>{{ $doc->partner_category }}</span>
                                        @if ($doc->unit)
                                            <span>&bull; {{ $doc->unit }}</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="text-dark fw-medium" style="font-size: 0.76rem;">
                                        {{ $doc->date_signed ? $doc->date_signed->translatedFormat('d M Y') : ($doc->date_approved ? $doc->date_approved->translatedFormat('d M Y') : 'Draf Formal') }}
                                    </div>
                                    <div class="text-muted" style="font-size: 0.71rem;">{{ $doc->validity ?: '1 Tahun' }}</div>
                                    @if ($doc->end_date)
                                        <div class="text-muted" style="font-size: 0.67rem;" title="Berakhir pada {{ $doc->end_date->translatedFormat('d F Y') }}">
                                            s.d. {{ $doc->end_date->translatedFormat('d M Y') }}
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <div style="font-size: 0.74rem; line-height: 1.3;">
                                        <span class="text-muted">UNIPAR:</span>
                                        <span class="text-dark fw-semibold">{{ $signer1 }}</span>
                                    </div>
                                    @if ($showPos1)
                                        <div class="text-muted" style="font-size: 0.69rem; line-height: 1.25;">{{ $pos1 }}</div>
                                    @endif
                                    <div class="mt-1 pt-1 border-top" style="font-size: 0.73rem; line-height: 1.3;">
                                        <span class="text-muted">Mitra:</span>
                                        <span class="text-dark">{{ $signer2 }}</span>
                                    </div>
                                </td>
                                <td class="text-center">
                                    @if ($hasRealFile)
                                        <div class="d-inline-flex align-items-center justify-content-center gap-1 mb-1 text-nowrap">
                                            <a href="{{ $validFilePath }}" target="_blank" class="repo-file-link d-inline-flex align-items-center text-dark fw-semibold text-decoration-none" style="font-size: 0.74rem;" title="Pratinjau: {{ $displayFileName }}">
                                                <i class="bi bi-file-earmark-pdf-fill text-danger me-1"></i>
                                                <span>{{ $fileLabel === 'Naskah PDF' ? 'PDF' : 'Draf' }} ({{ $fileSizeText }})</span>
                                            </a>
                                            <a href="{{ $validFilePath }}" download class="text-primary ps-1" style="font-size: 0.78rem;" title="Unduh {{ $displayFileName }}">
                                                <i class="bi bi-download"></i>
                                            </a>
                                        </div>
                                        <div class="text-muted" style="font-size: 0.69rem;" title="Jumlah riwayat perubahan status (audit trail)">
                                            <i class="bi bi-clock-history me-1"></i>{{ $doc->auditTrails->count() }} Riwayat
                                        </div>
                                    @else
                                        <div class="text-muted fst-italic mb-1" style="font-size: 0.72rem;" title="Berkas naskah belum diunggah secara fisik">
                                            Belum ada file
                                        </div>
                                        <div class="text-muted" style="font-size: 0.69rem;">
                                            <i class="bi bi-clock-history me-1"></i>{{ $doc->auditTrails->count() }} Riwayat
                                        </div>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center align-items-center gap-1 flex-nowrap">
                                        <a href="{{ route('simkerma.verify', ['reg_id' => $doc->reg_id]) }}" target="_blank" class="btn btn-light border btn-repo-xs text-primary" title="Lembar Verifikasi Publik">
                                            <i class="bi bi-shield-check"></i>
                                        </a>
                                        <a href="{{ route('simkerma.index', ['menu' => 4, 'view' => 'detail', 'doc' => $doc->doc_id]) }}" class="btn btn-primary btn-repo-xs text-white" title="Buka Detail Dokumen &amp; Alur FSM">
                                            <i class="bi bi-folder2-open"></i>
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

            <!-- CARD FOOTER DENGAN KONTROL PAGINATION -->
            <div class="card-footer bg-white py-2 px-3 border-top d-flex flex-column flex-sm-row justify-content-between align-items-center gap-2 small text-muted">
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <span>Menampilkan <strong id="repoVisibleCount" class="text-dark">{{ $documents->count() }}</strong> naskah</span>
                    <span class="text-secondary">&bull;</span>
                    <div class="d-flex align-items-center gap-1">
                        <span>Tampilkan:</span>
                        <select id="repoPerPage" class="form-select form-select-sm py-0 px-2" style="width: auto; height: 26px; font-size: 0.75rem;">
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

        @push('scripts')
        <script>
        document.addEventListener('DOMContentLoaded', function() {
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
            const sortableHeaders = document.querySelectorAll('#repoTable thead th.repo-sortable');
            const advPanel = document.getElementById('repoAdvancedFilterPanel');
            const toggleAdvBtn = document.getElementById('btnToggleRepoAdvancedFilter');
            const activeBadge = document.getElementById('repoActiveFilterBadge');
            const resetInlineBtn = document.getElementById('btnRepoResetInline');
            const noMatchRow = document.getElementById('noRepoMatchRow');
            const visibleCountEl = document.getElementById('repoVisibleCount');
            const perPageSelect = document.getElementById('repoPerPage');
            const paginationWrapper = document.getElementById('repoPaginationWrapper');

            let currentPage = 1;
            let currentSortKey = 'date';
            let currentSortDir = 'desc';

            // Simpan urutan awal server untuk fallback stabil
            rows.forEach((r, idx) => r.dataset.origIdx = String(idx));

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
                    const isLegacy = row.getAttribute('data-legacy') === '1' || (row.getAttribute('data-track') || '').toLowerCase().includes('lampau') || (row.getAttribute('data-track') || '').toLowerCase().includes('legacy');
                    
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
                    const matchFile = (f === 'ALL') || (f === 'HAS_FILE' && rowHasFile) || (f === 'NO_FILE' && !rowHasFile);
                    const matchSearch = (!q || row.innerText.toLowerCase().includes(q));

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

                let advCount = 0;
                if (u !== 'ALL') advCount++;
                if (c !== 'ALL') advCount++;
                if (y !== 'ALL') advCount++;
                if (f !== 'ALL') advCount++;
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

                const hasAnyFilter = (t !== 'ALL' || q.length > 0 || advCount > 0);
                if (resetInlineBtn) {
                    if (hasAnyFilter) {
                        resetInlineBtn.classList.remove('d-none');
                    } else {
                        resetInlineBtn.classList.add('d-none');
                    }
                }
            }

            function renderRepo() {
                updateFilterIndicators();
                const matched = getFilteredAndSortedRows();
                const totalMatched = matched.length;

                // Tampilkan atau sembunyikan tombol clear search
                if (clearSearchBtn) {
                    if (search && search.value.trim().length > 0) {
                        clearSearchBtn.classList.remove('d-none');
                    } else {
                        clearSearchBtn.classList.add('d-none');
                    }
                }

                if (visibleCountEl) {
                    visibleCountEl.innerText = totalMatched;
                }

                if (totalMatched === 0) {
                    rows.forEach(r => r.style.display = 'none');
                    if (noMatchRow) noMatchRow.classList.remove('d-none');
                    if (paginationWrapper) paginationWrapper.innerHTML = '';
                    return;
                }

                if (noMatchRow) noMatchRow.classList.add('d-none');

                // Terapkan urutan DOM baru ke tbody
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
                    if (idx >= startIndex && idx < endIndex) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });

                renderPagination(totalPages);
            }

            function renderPagination(totalPages) {
                if (!paginationWrapper) return;
                if (totalPages <= 1) {
                    paginationWrapper.innerHTML = '';
                    return;
                }

                let html = '';
                html += `<button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2 ${currentPage === 1 ? 'disabled opacity-50' : ''}" data-page="${currentPage - 1}"><i class="bi bi-chevron-left"></i></button>`;

                for (let p = 1; p <= totalPages; p++) {
                    if (p === 1 || p === totalPages || (p >= currentPage - 1 && p <= currentPage + 1)) {
                        html += `<button type="button" class="btn btn-sm ${p === currentPage ? 'btn-primary' : 'btn-outline-secondary'} py-0 px-2 fw-semibold" data-page="${p}">${p}</button>`;
                    } else if (p === currentPage - 2 || p === currentPage + 2) {
                        html += `<span class="px-1 text-muted">&hellip;</span>`;
                    }
                }

                html += `<button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2 ${currentPage === totalPages ? 'disabled opacity-50' : ''}" data-page="${currentPage + 1}"><i class="bi bi-chevron-right"></i></button>`;

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

            // Click header tabel untuk mengurutkan (Terlama / Terbaru atau A-Z / Z-A)
            sortableHeaders.forEach(function(th) {
                th.addEventListener('click', function() {
                    const key = this.getAttribute('data-sort-key');
                    if (currentSortKey === key) {
                        currentSortDir = (currentSortDir === 'desc') ? 'asc' : 'desc';
                    } else {
                        currentSortKey = key;
                        currentSortDir = (key === 'date' || key === 'audit') ? 'desc' : 'asc';
                    }
                    updateSortHeadersUI();
                    currentPage = 1;
                    renderRepo();
                });
            });

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
                renderRepo();
            };

            // Ekspor baris hasil filter saat ini ke Excel (menghilangkan kolom Aksi)
            window.exportFilteredRepoToExcel = function() {
                const matched = getFilteredAndSortedRows();
                const theadRow = document.querySelector('#repoTable thead tr');
                if (!theadRow) return;

                let html = '<table border="1"><thead><tr>';
                const thCells = Array.from(theadRow.cells);
                for (let i = 0; i < thCells.length - 1; i++) {
                    html += '<th>' + thCells[i].innerText.trim() + '</th>';
                }
                html += '</tr></thead><tbody>';

                matched.forEach(function(row) {
                    html += '<tr>';
                    const tdCells = Array.from(row.cells);
                    for (let i = 0; i < tdCells.length - 1; i++) {
                        html += '<td>' + tdCells[i].innerText.replace(/\s+/g, ' ').trim() + '</td>';
                    }
                    html += '</tr>';
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

            // Inisialisasi awal
            renderRepo();
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
                                <div class="row mb-0 align-items-center">
                                    <label class="col-md-3 col-form-label fw-bold text-dark" id="labelUploadDraft">
                                        Upload Draf Naskah (PDF) : <span class="text-danger">*</span>
                                    </label>
                                    <div class="col-md-9">
                                        <input type="file" name="draft_file" class="form-control" accept=".pdf" required>
                                        <div class="form-text text-muted">Format berkas: <strong>PDF (.pdf)</strong>. Berkas draf awal untuk ditelaah dan diverifikasi oleh Bagian Kerja Sama/Legal.</div>
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

            <div class="detail-viewport-1080p">

                <!-- BREADCRUMBS -->
                <nav aria-label="breadcrumb" class="mb-1">
                    <ol class="breadcrumb mb-0 small" style="font-size: 0.75rem;">
                        <li class="breadcrumb-item"><a href="{{ route('simkerma.index', ['menu' => 1]) }}" class="text-decoration-none text-muted"><i class="bi bi-house me-1"></i></a></li>
                        <li class="breadcrumb-item"><a href="{{ route('simkerma.index', ['menu' => 4]) }}" class="text-decoration-none text-muted">Pengajuan &amp; Persetujuan</a></li>
                        <li class="breadcrumb-item active text-dark fw-medium text-truncate" style="max-width: 480px;" aria-current="page" title="{{ $currentDoc->title ?: 'Detail Dokumen' }}">{{ $currentDoc->title ?: 'Detail Dokumen' }}</li>
                    </ol>
                </nav>

                <!-- HEADER BAR (TITLE & ACTION BUTTONS) -->
                <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap gap-2">
                    <div class="d-flex align-items-center gap-2">
                        <a href="{{ route('simkerma.index', ['menu' => 4]) }}" class="btn btn-outline-secondary btn-sm bg-white rounded-3 d-flex align-items-center justify-content-center shadow-2xs" style="width: 32px; height: 32px; border-color: #cbd5e1;" title="Kembali ke Daftar Pengajuan">
                            <i class="bi bi-arrow-left text-dark"></i>
                        </a>
                        <h5 class="fw-bold mb-0 text-dark" style="font-size: 1.15rem;">Detail Dokumen Kerja Sama</h5>
                    </div>

                    <div class="d-flex align-items-center flex-wrap gap-2">
                        @if ($currentDoc->status === 'ACTIVE' || $currentDoc->status === 'WARNING' || $currentDoc->status === 'EXPIRED')
                            <form method="POST" action="{{ route('simkerma.renew-draft') }}" class="d-inline" onsubmit="return confirm('Inisiasi pembaruan/reaktivasi dokumen {{ $currentDoc->doc_id }}?\n\nDokumen akan kembali ke status DRAFT untuk penyusunan dan penelaahan ketentuan naskah.');">
                                @csrf
                                <input type="hidden" name="doc_id" value="{{ $currentDoc->doc_id }}">
                                <button type="submit" class="btn btn-warning btn-sm fw-semibold px-2.5 py-1.5 shadow-2xs d-flex align-items-center gap-1.5 text-dark" style="font-size: 0.8rem;">
                                    <i class="bi bi-arrow-repeat"></i> Perpanjang / Reaktivasi ke Draf
                                </button>
                            </form>
                        @endif

                        <!-- Action Buttons Dinamis Berdasarkan Tingkat Dokumen (MoU / MoA / IA) -->
                        @if ($currentDoc->doc_type === 'MoU')
                            <!-- MoU: Dapat Menurunkan MoA (Fakultas) & IA (Prodi) -->
                            <div class="dropdown">
                                <button class="btn btn-primary btn-sm fw-semibold px-3 py-1.5 shadow-sm dropdown-toggle d-flex align-items-center gap-1.5" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="font-size: 0.8rem; background-color: #1e40af; border-color: #1e40af;">
                                    <i class="bi bi-plus-lg"></i> Tambah Dokumen Turunan
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow border-0 small py-1" style="min-width: 220px;">
                                    <li>
                                        <a class="dropdown-item py-1.5 px-3 d-flex align-items-center gap-2" href="{{ route('simkerma.index', ['menu' => 4, 'view' => 'new_derive', 'parent_id' => $currentDoc->doc_id, 'derive_type' => 'MoA / PKS']) }}">
                                            <i class="bi bi-file-earmark-text text-primary fs-6"></i>
                                            <div>
                                                <div class="fw-bold">MoA / PKS</div>
                                                <div class="text-muted" style="font-size: 0.7rem;">Tingkat Fakultas / Unit Kerja</div>
                                            </div>
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item py-1.5 px-3 d-flex align-items-center gap-2" href="{{ route('simkerma.index', ['menu' => 4, 'view' => 'new_derive', 'parent_id' => $currentDoc->doc_id, 'derive_type' => 'IA']) }}">
                                            <i class="bi bi-file-earmark-check text-success fs-6"></i>
                                            <div>
                                                <div class="fw-bold">IA (Implementation)</div>
                                                <div class="text-muted" style="font-size: 0.7rem;">Tingkat Prodi / Lab / Kegiatan</div>
                                            </div>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        @elseif ($currentDoc->doc_type === 'IA')
                            <!-- IA: Dokumen Ujung Tombak Operasional (Mencatat Realisasi IKU 6) -->
                            <button type="button" class="btn btn-success btn-sm fw-semibold px-3 py-1.5 shadow-sm d-flex align-items-center gap-1.5" style="font-size: 0.8rem; background-color: #059669; border-color: #059669;" data-bs-toggle="modal" data-bs-target="#createActivityModal_{{ $currentDoc->id }}">
                                <i class="bi bi-calendar-plus"></i> + Catat Pelaksanaan (IKU 6)
                            </button>
                        @else
                            <!-- MoA / PKS: Menurunkan IA (Implementation Arrangement) -->
                            <a href="{{ route('simkerma.index', ['menu' => 4, 'view' => 'new_derive', 'parent_id' => $currentDoc->doc_id, 'derive_type' => 'IA']) }}" class="btn btn-success btn-sm fw-semibold px-3 py-1.5 shadow-sm d-flex align-items-center gap-1.5" style="font-size: 0.8rem; background-color: #0284c7; border-color: #0284c7;">
                                <i class="bi bi-plus-lg"></i> + Tambah IA (Implementation)
                            </a>
                        @endif

                        <!-- Dropdown Opsi Lainnya (...) -->
                        <div class="dropdown">
                            <button class="btn btn-light border btn-sm px-2.5 py-1.5 shadow-2xs" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="border-color: #cbd5e1;">
                                <i class="bi bi-three-dots"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow border-0 small py-1">
                                <li><a class="dropdown-item py-1 px-3" href="{{ route('simkerma.verify', ['reg_id' => $currentDoc->reg_id]) }}" target="_blank"><i class="bi bi-box-arrow-up-right me-2 text-primary"></i> Buka Verifikasi Publik</a></li>
                                @if ($currentDoc->draft_file && file_exists(public_path($currentDoc->draft_file)))
                                    <li><a class="dropdown-item py-1 px-3" href="{{ asset($currentDoc->draft_file) }}" target="_blank"><i class="bi bi-file-earmark-pdf me-2 text-danger"></i> Buka Naskah Draf PDF</a></li>
                                @endif
                                @if ($currentDoc->signed_file && file_exists(public_path($currentDoc->signed_file)))
                                    <li><a class="dropdown-item py-1 px-3" href="{{ asset($currentDoc->signed_file) }}" target="_blank"><i class="bi bi-file-earmark-check me-2 text-success"></i> Buka Naskah Asli PDF</a></li>
                                @endif
                                @if ($currentDoc->status === 'ACTIVE' || $currentDoc->status === 'WARNING' || $currentDoc->status === 'EXPIRED')
                                    <li>
                                        <form method="POST" action="{{ route('simkerma.renew-draft') }}" class="px-3 py-1" onsubmit="return confirm('Inisiasi pembaruan/reaktivasi dokumen {{ $currentDoc->doc_id }}?\n\nDokumen akan kembali ke status DRAFT.');">
                                            @csrf
                                            <input type="hidden" name="doc_id" value="{{ $currentDoc->doc_id }}">
                                            <button type="submit" class="btn btn-link p-0 text-warning text-decoration-none small text-start w-100 fw-semibold">
                                                <i class="bi bi-arrow-repeat me-2"></i> Perpanjang / Reaktivasi ke Draf
                                            </button>
                                        </form>
                                    </li>
                                @endif
                                <li><hr class="dropdown-divider my-1"></li>
                                <li><a class="dropdown-item py-1 px-3" href="#" data-bs-toggle="modal" data-bs-target="#fsmModal_{{ $currentDoc->id }}"><i class="bi bi-sliders me-2 text-warning"></i> Kelola Transisi FSM</a></li>
                                @if ($currentDoc->status === 'DRAFT' || $activeRole === 'MASTER' || $activeRole === 'ADMIN')
                                    <li><hr class="dropdown-divider my-1"></li>
                                    <li>
                                        <form method="POST" action="{{ route('simkerma.delete-draft') }}" class="px-3 py-1" onsubmit="return confirm('KONFIRMASI PENGHAPUSAN DOKUMEN (AKSES MASTER):\n\nApakah Anda yakin ingin menghapus dokumen {{ $currentDoc->doc_id }} [Status: {{ $currentDoc->status }}] ({{ addslashes($currentDoc->partner_name) }}) secara permanen?\n\nPERHATIAN: Dokumen yang dihapus tidak dapat dipulihkan kembali.');">
                                            @csrf
                                            <input type="hidden" name="doc_id" value="{{ $currentDoc->doc_id }}">
                                            <button type="submit" class="btn btn-link p-0 text-danger text-decoration-none small text-start w-100 fw-semibold">
                                                <i class="bi bi-trash3 me-2"></i> {{ $currentDoc->status === 'DRAFT' ? 'Hapus Draf Ini' : 'Hapus Dokumen (Akses MASTER)' }}
                                            </button>
                                        </form>
                                    </li>
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- SUMMARY BANNER CARD (PERSIS UI/detail_MoU.png) -->
                <div class="card border rounded-3 bg-white mb-2 shadow-2xs">
                    <div class="card-body p-2 px-3">
                        <div class="d-flex align-items-center justify-content-between gap-3">
                            <!-- Left Group: Icon + Title info -->
                            <div class="d-flex align-items-center gap-3 flex-grow-1" style="min-width: 0;">
                                <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 44px; height: 44px; background-color: {{ $currentDoc->doc_type === 'MoU' ? '#eff6ff' : ($currentDoc->doc_type === 'IA' ? '#ecfdf5' : '#f0f9ff') }}; border: 1px solid {{ $currentDoc->doc_type === 'MoU' ? '#dbeafe' : ($currentDoc->doc_type === 'IA' ? '#a7f3d0' : '#bae6fd') }};">
                                    <i class="bi {{ $currentDoc->doc_type === 'MoU' ? 'bi-file-earmark-text text-primary' : ($currentDoc->doc_type === 'IA' ? 'bi-file-earmark-check text-success' : 'bi-file-earmark-richtext text-info') }} fs-3"></i>
                                </div>
                                <div class="flex-grow-1" style="min-width: 0;">
                                    <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                                        <span class="badge rounded-pill text-white fw-bold px-2 py-0.5" style="background-color: {{ $currentDoc->doc_type === 'MoU' ? '#1e3a8a' : ($currentDoc->doc_type === 'IA' ? '#059669' : '#0284c7') }}; font-size: 0.68rem;">{{ $currentDoc->doc_type }}</span>
                                        <span class="text-muted small fw-medium" style="font-size: 0.76rem;">{{ $currentDoc->doc_type === 'MoU' ? 'Memorandum of Understanding' : ($currentDoc->doc_type === 'IA' ? 'Implementation Arrangement' : 'Memorandum of Agreement') }}</span>
                                        @if ($currentDoc->is_legacy)
                                            <span class="badge rounded-pill bg-dark text-warning border border-warning px-2 py-0.5" style="font-size: 0.68rem;">
                                                <i class="bi bi-clock-history me-1"></i> Data Lampau
                                            </span>
                                        @endif
                                    </div>
                                    <h5 class="fw-bold text-dark mb-1" style="letter-spacing: -0.3px; font-size: 1.15rem; line-height: 1.35;" title="{{ $currentDoc->title ?: ($currentDoc->scope ?: ($currentDoc->doc_number ?: $currentDoc->doc_id)) }}">
                                        {{ $currentDoc->title ?: ($currentDoc->scope ?: ($currentDoc->doc_number ?: $currentDoc->doc_id)) }}
                                    </h5>
                                    <div class="text-secondary d-flex align-items-center flex-wrap gap-1" style="font-size: 0.78rem;">
                                        <span class="fw-semibold text-primary">Universitas Parna Raya (UNIPAR)</span>
                                        <span class="text-muted mx-1">&times;</span>
                                        <span class="text-dark fw-semibold">{{ $currentDoc->partner_name }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Right Group: Status Badge + Reg Code + Audit Trail -->
                            <div class="text-end flex-shrink-0">
                                <div>
                                    @if ($currentDoc->status === 'ACTIVE')
                                        <span class="badge rounded-pill bg-success-subtle text-success border border-success-subtle px-3 py-1 fw-bold" style="font-size: 0.8rem;">
                                            <i class="bi bi-check-lg me-1"></i> ACTIVE
                                        </span>
                                    @elseif ($currentDoc->status === 'DRAFT')
                                        <span class="badge rounded-pill bg-secondary-subtle text-secondary border border-secondary-subtle px-3 py-1 fw-bold" style="font-size: 0.8rem;">
                                            <i class="bi bi-pencil-square me-1"></i> DRAFT
                                        </span>
                                    @elseif ($currentDoc->status === 'REVIEW')
                                        <span class="badge rounded-pill bg-info-subtle text-info border border-info-subtle px-3 py-1 fw-bold" style="font-size: 0.8rem;">
                                            <i class="bi bi-clock-history me-1"></i> REVIEW
                                        </span>
                                    @elseif ($currentDoc->status === 'VERIFIED')
                                        <span class="badge rounded-pill bg-primary-subtle text-primary border border-primary-subtle px-3 py-1 fw-bold" style="font-size: 0.8rem;">
                                            <i class="bi bi-shield-check me-1"></i> VERIFIED
                                        </span>
                                    @elseif ($currentDoc->status === 'WARNING')
                                        <span class="badge rounded-pill bg-warning-subtle text-warning-emphasis border border-warning-subtle px-3 py-1 fw-bold" style="font-size: 0.8rem;">
                                            <i class="bi bi-exclamation-triangle me-1"></i> WARNING
                                        </span>
                                    @elseif ($currentDoc->status === 'EXPIRED')
                                        <span class="badge rounded-pill bg-danger-subtle text-danger border border-danger-subtle px-3 py-1 fw-bold" style="font-size: 0.8rem;">
                                            <i class="bi bi-clock me-1"></i> EXPIRED
                                        </span>
                                    @elseif ($currentDoc->status === 'CANCELLED')
                                        <span class="badge rounded-pill bg-secondary-subtle text-secondary border border-secondary-subtle px-3 py-1 fw-bold" style="font-size: 0.8rem;">
                                            <i class="bi bi-x-circle me-1"></i> CANCELLED
                                        </span>
                                    @else
                                        <span class="badge rounded-pill bg-secondary-subtle text-secondary border border-secondary-subtle px-3 py-1 fw-bold" style="font-size: 0.8rem;">
                                            {{ $currentDoc->status }}
                                        </span>
                                    @endif
                                </div>
                                <div class="text-secondary small fw-semibold font-monospace mt-1" style="font-size: 0.72rem;">{{ $currentDoc->reg_id }}</div>
                                <div class="text-muted mt-0.5" style="font-size: 0.7rem;">
                                    <i class="bi bi-calendar-event me-1 text-secondary"></i> Jejak Audit: {{ $currentDoc->auditTrails->count() }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- PARENT DOCUMENT RELATION BANNER (JIKA MERUPAKAN DOKUMEN TURUNAN MoA / IA) -->
                @if ($currentDoc->doc_type !== 'MoU' && $parentDoc)
                    <div class="alert alert-primary py-2 px-3 rounded-3 mb-2 d-flex align-items-center justify-content-between shadow-2xs border-primary-subtle" style="font-size: 0.8rem; background-color: #f0fdf4; border-color: #bbf7d0; color: #166534;">
                        <div class="d-flex align-items-center gap-2">
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
                        <a href="{{ route('simkerma.index', ['menu' => 4, 'view' => 'detail', 'doc' => $parentDoc->doc_id]) }}" class="btn btn-sm btn-outline-success fw-bold px-2.5 py-1 text-nowrap">
                            <i class="bi bi-arrow-left-circle me-1"></i> Buka Naskah Induk
                        </a>
                    </div>
                @elseif ($currentDoc->doc_type !== 'MoU' && !empty($currentDoc->parent_mou) && $currentDoc->parent_mou !== '-')
                    <div class="alert alert-secondary py-2 px-3 rounded-3 mb-2 d-flex align-items-center gap-2 shadow-2xs" style="font-size: 0.8rem;">
                        <i class="bi bi-info-circle text-primary fs-5"></i>
                        <div>
                            <strong>Dokumen Induk (Rujukan):</strong> <span class="font-monospace fw-bold">{{ $currentDoc->parent_mou }}</span>
                        </div>
                    </div>
                @endif

                <!-- NAV TABS (5 TABS SESUAI MOCKUP detail_MoU.png) -->
                <ul class="nav nav-tabs border-bottom mb-2" id="detailDocTabs" role="tablist" style="font-size: 0.82rem;">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active fw-bold py-1 px-3 d-flex align-items-center gap-1.5" id="tab-info-btn" data-bs-toggle="tab" data-bs-target="#tab-info-pane" type="button" role="tab">
                            <i class="bi bi-info-circle text-primary"></i> Informasi &amp; Alur
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-medium text-muted py-1 px-3 d-flex align-items-center gap-1.5" id="tab-docs-btn" data-bs-toggle="tab" data-bs-target="#tab-docs-pane" type="button" role="tab">
                            <i class="bi bi-file-earmark-text"></i> Dokumen &amp; Lampiran
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-medium text-muted py-1 px-3 d-flex align-items-center gap-1.5" id="tab-deriv-btn" data-bs-toggle="tab" data-bs-target="#tab-deriv-pane" type="button" role="tab">
                            <i class="bi bi-diagram-3"></i> Dokumen Turunan ({{ $childDocs->count() }})
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-medium text-muted py-1 px-3 d-flex align-items-center gap-1.5" id="tab-act-btn" data-bs-toggle="tab" data-bs-target="#tab-act-pane" type="button" role="tab">
                            <i class="bi bi-list-task"></i> Pelaksanaan
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-medium text-muted py-1 px-3 d-flex align-items-center gap-1.5" id="tab-audit-btn" data-bs-toggle="tab" data-bs-target="#tab-audit-pane" type="button" role="tab">
                            <i class="bi bi-clock-history"></i> Riwayat &amp; Audit
                        </button>
                    </li>
                </ul>

                <!-- TAB PANES -->
                <div class="tab-content" id="detailDocTabContent">

                    <!-- TAB 1: INFORMASI & ALUR (2 KOLOM PERSIS MOCKUP detail_MoU.png) -->
                    <div class="tab-pane fade show active" id="tab-info-pane" role="tabpanel">
                        <div class="row g-2">

                            <!-- KOLOM KIRI (7-8 COLS) -->
                            <div class="col-lg-8">

                                <!-- KARTU 1: ALUR PROSES STATUS KERJA SAMA -->
                                @php
                                    // Lookup Jejak Audit untuk masing-masing fase alur
                                    $auditDraft = $currentDoc->auditTrails->firstWhere('event', 'CREATE_DRAFT') 
                                        ?? $currentDoc->auditTrails->sortBy('id')->first();
                                    $auditSubmit = $currentDoc->auditTrails->firstWhere('event', 'SUBMIT');
                                    $auditVerify = $currentDoc->auditTrails->firstWhere('event', 'VERIFY');
                                    $auditActive = $currentDoc->auditTrails->firstWhere('event', 'ACTIVATE');
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

                                    // Tanggal masing-masing node alur proses
                                    if ($isLegacyDoc) {
                                        // Untuk dokumen lampau, selaraskan tanggal status ke tanggal awal/efektif naskah lampau
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
                                            : ($currentDoc->date_approved 
                                                ? \Carbon\Carbon::parse($currentDoc->date_approved)->subDay()->format('d M Y') 
                                                : ($isNode3Done ? \Carbon\Carbon::parse($currentDoc->created_at)->format('d M Y') : ($isNode3Active ? 'Dalam Telaah' : '-')));

                                        $isNode4Done = in_array($docStatus, ['ACTIVE', 'WARNING', 'EXPIRED']) || $auditActive !== null;
                                        $isNode4Active = ($docStatus === 'VERIFIED');
                                        $dateNode4 = $currentDoc->date_approved 
                                            ? \Carbon\Carbon::parse($currentDoc->date_approved)->format('d M Y') 
                                            : ($auditActive 
                                                ? \Carbon\Carbon::parse($auditActive->created_at)->format('d M Y') 
                                                : ($isNode4Done ? \Carbon\Carbon::parse($currentDoc->created_at)->format('d M Y') : ($isNode4Active ? 'Siap Diteken' : '-')));

                                        $isNode5Done = in_array($docStatus, ['ACTIVE', 'WARNING', 'EXPIRED']);
                                        $dateNode5 = $isNode5Done 
                                            ? ($currentDoc->start_date ? \Carbon\Carbon::parse($currentDoc->start_date)->format('d M Y') : ($currentDoc->date_signed ? \Carbon\Carbon::parse($currentDoc->date_signed)->format('d M Y') : ''))
                                            : '-';
                                    }

                                    $isNode5Active = ($docStatus === 'ACTIVE');

                                    $isNode6Active = ($docStatus === 'WARNING');
                                    $isNode6Done = in_array($docStatus, ['WARNING', 'EXPIRED']);
                                    $dateNode6 = $isNode6Done 
                                        ? ($currentDoc->end_date ? \Carbon\Carbon::parse($currentDoc->end_date)->subDays(90)->format('d M Y') : 'H-90')
                                        : '-';

                                    $isNode7Active = ($docStatus === 'EXPIRED');
                                    $dateNode7 = $isNode7Active 
                                        ? ($currentDoc->end_date ? \Carbon\Carbon::parse($currentDoc->end_date)->format('d M Y') : ($auditExpire ? \Carbon\Carbon::parse($auditExpire->created_at)->format('d M Y') : ''))
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

                                <div class="card border rounded-3 bg-white mb-2 shadow-2xs">
                                    <div class="card-header bg-white py-1.5 px-3 border-bottom d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center gap-2 fw-bold text-dark" style="font-size: 0.85rem;">
                                            <i class="bi bi-layers text-primary"></i> Alur Proses
                                        </div>
                                        <button type="button" class="btn btn-outline-secondary btn-sm py-0.5 px-2 fw-semibold d-flex align-items-center gap-1" style="font-size: 0.72rem; border-color: #cbd5e1;" data-bs-toggle="modal" data-bs-target="#fsmModal_{{ $currentDoc->id }}">
                                            <i class="bi bi-diagram-3"></i> Lihat Detail Status
                                        </button>
                                    </div>
                                    <div class="card-body py-2 px-3">
                                        <!-- HORIZONTAL STEPPER 7 TAHAPAN -->
                                        <div class="overflow-x-auto pb-1 mb-2" style="-webkit-overflow-scrolling: touch;">
                                            <div class="stepper-wrap position-relative py-1 px-2" style="min-width: 580px;">
                                                <!-- Connecting line background & active line -->
                                                <div class="position-absolute" style="top: 15px; left: 35px; right: 35px; height: 3px; background-color: #e2e8f0; z-index: 1;">
                                                    <div style="width: {{ $progressWidth }}; height: 100%; background-color: {{ $progressColor }}; transition: width 0.3s ease;"></div>
                                                </div>

                                                <div class="d-flex justify-content-between position-relative" style="z-index: 2;">
                                                <!-- Node 1: Draft -->
                                                <div class="text-center" style="width: 70px;">
                                                    @if ($docStatus === 'CANCELLED')
                                                        <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto text-white shadow-2xs" style="width: 24px; height: 24px; font-size: 0.7rem; background-color: #ef4444;">
                                                            <i class="bi bi-x-lg"></i>
                                                        </div>
                                                        <div class="fw-semibold text-danger mt-1" style="font-size: 0.72rem;">Draft</div>
                                                    @else
                                                        <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto text-white shadow-2xs" style="width: 24px; height: 24px; font-size: 0.7rem; background-color: #22c55e; {{ $docStatus === 'DRAFT' ? 'border: 2px solid #bbf7d0; box-shadow: 0 0 0 3px #dcfce7;' : '' }}">
                                                            <i class="bi bi-check"></i>
                                                        </div>
                                                        <div class="fw-semibold text-dark mt-1" style="font-size: 0.72rem;">Draft</div>
                                                    @endif
                                                    <div class="text-muted" style="font-size: 0.65rem;">{{ $dateNode1 }}</div>
                                                </div>

                                                <!-- Node 2: Verifikasi Unit -->
                                                <div class="text-center" style="width: 80px;">
                                                    @if ($isNode2Done)
                                                        <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto text-white shadow-2xs" style="width: 24px; height: 24px; font-size: 0.7rem; background-color: #22c55e;">
                                                            <i class="bi bi-check"></i>
                                                        </div>
                                                        <div class="fw-semibold text-dark mt-1" style="font-size: 0.72rem;">Verifikasi Unit</div>
                                                        <div class="text-muted" style="font-size: 0.65rem;">{{ $dateNode2 }}</div>
                                                    @else
                                                        <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto bg-white border shadow-2xs" style="width: 24px; height: 24px; font-size: 0.7rem; border-color: #cbd5e1 !important; color: #94a3b8 !important;">
                                                            <i class="bi bi-circle"></i>
                                                        </div>
                                                        <div class="text-muted mt-1" style="font-size: 0.72rem;">Verifikasi Unit</div>
                                                        <div class="text-muted" style="font-size: 0.65rem;">-</div>
                                                    @endif
                                                </div>

                                                <!-- Node 3: Review LPPM -->
                                                <div class="text-center" style="width: 75px;">
                                                    @if ($isNode3Done)
                                                        <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto text-white shadow-2xs" style="width: 24px; height: 24px; font-size: 0.7rem; background-color: #22c55e;">
                                                            <i class="bi bi-check"></i>
                                                        </div>
                                                        <div class="fw-semibold text-dark mt-1" style="font-size: 0.72rem;">Review LPPM</div>
                                                        <div class="text-muted" style="font-size: 0.65rem;">{{ $dateNode3 }}</div>
                                                    @elseif ($isNode3Active)
                                                        <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto text-white shadow-2xs" style="width: 24px; height: 24px; font-size: 0.7rem; background-color: #3b82f6; border: 2px solid #bfdbfe; box-shadow: 0 0 0 3px #dbeafe;">
                                                            <i class="bi bi-hourglass-split"></i>
                                                        </div>
                                                        <div class="fw-bold text-primary mt-1" style="font-size: 0.72rem;">Review LPPM</div>
                                                        <div class="text-primary fw-medium" style="font-size: 0.65rem;">Sedang Telaah</div>
                                                    @else
                                                        <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto bg-white border shadow-2xs" style="width: 24px; height: 24px; font-size: 0.7rem; border-color: #cbd5e1 !important; color: #94a3b8 !important;">
                                                            <i class="bi bi-circle"></i>
                                                        </div>
                                                        <div class="text-muted mt-1" style="font-size: 0.72rem;">Review LPPM</div>
                                                        <div class="text-muted" style="font-size: 0.65rem;">-</div>
                                                    @endif
                                                </div>

                                                <!-- Node 4: Persetujuan Pimpinan -->
                                                <div class="text-center" style="width: 95px;">
                                                    @if ($isNode4Done)
                                                        <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto text-white shadow-2xs" style="width: 24px; height: 24px; font-size: 0.7rem; background-color: #22c55e;">
                                                            <i class="bi bi-check"></i>
                                                        </div>
                                                        <div class="fw-semibold text-dark mt-1" style="font-size: 0.72rem;">Persetujuan Pimpinan</div>
                                                        <div class="text-muted" style="font-size: 0.65rem;">{{ $dateNode4 }}</div>
                                                    @elseif ($isNode4Active)
                                                        <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto text-white shadow-2xs" style="width: 24px; height: 24px; font-size: 0.7rem; background-color: #3b82f6; border: 2px solid #bfdbfe; box-shadow: 0 0 0 3px #dbeafe;">
                                                            <i class="bi bi-pen-fill"></i>
                                                        </div>
                                                        <div class="fw-bold text-primary mt-1" style="font-size: 0.72rem;">Persetujuan Pimpinan</div>
                                                        <div class="text-primary fw-medium" style="font-size: 0.65rem;">Siap Diteken</div>
                                                    @else
                                                        <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto bg-white border shadow-2xs" style="width: 24px; height: 24px; font-size: 0.7rem; border-color: #cbd5e1 !important; color: #94a3b8 !important;">
                                                            <i class="bi bi-circle"></i>
                                                        </div>
                                                        <div class="text-muted mt-1" style="font-size: 0.72rem;">Persetujuan Pimpinan</div>
                                                        <div class="text-muted" style="font-size: 0.65rem;">-</div>
                                                    @endif
                                                </div>

                                                <!-- Node 5: Active -->
                                                <div class="text-center" style="width: 70px;">
                                                    @if ($isNode5Active)
                                                        <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto text-white shadow-2xs" style="width: 24px; height: 24px; font-size: 0.7rem; background-color: #22c55e; border: 2px solid #bbf7d0; box-shadow: 0 0 0 3px #dcfce7;">
                                                            <i class="bi bi-check"></i>
                                                        </div>
                                                        <div class="fw-bold text-success mt-1" style="font-size: 0.72rem;">Active</div>
                                                        @if ($dateNode5 && $dateNode5 !== '-')
                                                            <div class="text-success fw-medium" style="font-size: 0.65rem;">{{ $dateNode5 }}</div>
                                                        @endif
                                                    @elseif ($isNode5Done)
                                                        <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto text-white shadow-2xs" style="width: 24px; height: 24px; font-size: 0.7rem; background-color: #22c55e;">
                                                            <i class="bi bi-check"></i>
                                                        </div>
                                                        <div class="fw-semibold text-dark mt-1" style="font-size: 0.72rem;">Active</div>
                                                        @if ($dateNode5 && $dateNode5 !== '-')
                                                            <div class="text-muted" style="font-size: 0.65rem;">{{ $dateNode5 }}</div>
                                                        @endif
                                                    @else
                                                        <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto bg-white border shadow-2xs" style="width: 24px; height: 24px; font-size: 0.7rem; border-color: #cbd5e1 !important; color: #94a3b8 !important;">
                                                            <i class="bi bi-circle"></i>
                                                        </div>
                                                        <div class="text-muted mt-1" style="font-size: 0.72rem;">Active</div>
                                                        <div class="text-muted" style="font-size: 0.65rem;">-</div>
                                                    @endif
                                                </div>

                                                <!-- Node 6: Peringatan -->
                                                <div class="text-center" style="width: 70px;">
                                                    @if ($isNode6Active)
                                                        <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto text-white shadow-2xs" style="width: 24px; height: 24px; font-size: 0.7rem; background-color: #f59e0b; border: 2px solid #fde68a; box-shadow: 0 0 0 3px #fef3c7;">
                                                            <i class="bi bi-bell-fill"></i>
                                                        </div>
                                                        <div class="fw-bold text-warning mt-1" style="font-size: 0.72rem;">Peringatan</div>
                                                        @if ($dateNode6 && $dateNode6 !== '-')
                                                            <div class="text-warning fw-medium" style="font-size: 0.65rem;">{{ $dateNode6 }}</div>
                                                        @endif
                                                    @elseif ($isNode6Done)
                                                        <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto text-white shadow-2xs" style="width: 24px; height: 24px; font-size: 0.7rem; background-color: #f59e0b;">
                                                            <i class="bi bi-bell"></i>
                                                        </div>
                                                        <div class="fw-semibold text-dark mt-1" style="font-size: 0.72rem;">Peringatan</div>
                                                        @if ($dateNode6 && $dateNode6 !== '-')
                                                            <div class="text-muted" style="font-size: 0.65rem;">{{ $dateNode6 }}</div>
                                                        @endif
                                                    @else
                                                        <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto bg-white border shadow-2xs" style="width: 24px; height: 24px; font-size: 0.7rem; border-color: #cbd5e1 !important; color: #94a3b8 !important;">
                                                            <i class="bi bi-bell"></i>
                                                        </div>
                                                        <div class="text-muted mt-1" style="font-size: 0.72rem;">Peringatan</div>
                                                        <div class="text-muted" style="font-size: 0.65rem;">-</div>
                                                    @endif
                                                </div>

                                                <!-- Node 7: Selesai -->
                                                <div class="text-center" style="width: 70px;">
                                                    @if ($isNode7Active)
                                                        <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto text-white shadow-2xs" style="width: 24px; height: 24px; font-size: 0.7rem; background-color: #ef4444; border: 2px solid #fecaca; box-shadow: 0 0 0 3px #fee2e2;">
                                                            <i class="bi bi-clock-history"></i>
                                                        </div>
                                                        <div class="fw-bold text-danger mt-1" style="font-size: 0.72rem;">Selesai</div>
                                                        @if ($dateNode7 && $dateNode7 !== '-')
                                                            <div class="text-danger fw-medium" style="font-size: 0.65rem;">{{ $dateNode7 }}</div>
                                                        @endif
                                                    @else
                                                        <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto bg-white border shadow-2xs" style="width: 24px; height: 24px; font-size: 0.7rem; border-color: #cbd5e1 !important; color: #94a3b8 !important;">
                                                            <i class="bi bi-circle"></i>
                                                        </div>
                                                        <div class="text-muted mt-1" style="font-size: 0.72rem;">Selesai</div>
                                                        <div class="text-muted" style="font-size: 0.65rem;">-</div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                        <!-- Single Line Alert Strip Dinamis Sesuai Status FSM Aktual -->
                                        <div class="alert d-flex align-items-center py-1 px-2.5 mb-0 rounded-2" style="font-size: 0.78rem; background-color: {{ $alertConfig['bg'] }}; border-color: {{ $alertConfig['border'] }}; color: {{ $alertConfig['color'] }};">
                                            <i class="bi {{ $alertConfig['icon'] }} me-2 fs-6"></i>
                                            <span>{{ $alertConfig['text'] }}</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- KARTU 2: INFORMASI UMUM -->
                                <div class="card border rounded-3 bg-white mb-2 shadow-2xs">
                                    <div class="card-header bg-white py-1.5 px-3 border-bottom d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center gap-2 fw-bold text-dark" style="font-size: 0.85rem;">
                                            <i class="bi bi-info-circle text-primary"></i> Informasi Umum
                                        </div>
                                        <button type="button" class="btn btn-outline-secondary btn-sm py-0.5 px-2.5 fw-semibold d-flex align-items-center gap-1" style="font-size: 0.72rem; border-color: #cbd5e1;" data-bs-toggle="modal" data-bs-target="#editInfoModal_{{ $currentDoc->id }}">
                                            <i class="bi bi-pencil"></i> Edit
                                        </button>
                                    </div>
                                    <div class="card-body py-2 px-3">
                                        <div class="table-responsive border-0 shadow-none mb-0">
                                            <table class="table table-borderless table-sm mb-0" style="font-size: 0.78rem; line-height: 1.45;">
                                                <tbody>
                                                    <tr>
                                                        <td class="text-muted py-0.5 ps-0 pe-2 text-nowrap" style="width: 160px;">Jenis Dokumen</td>
                                                        <td class="py-0.5 px-1 text-muted" style="width: 10px;">:</td>
                                                        <td class="text-dark fw-medium py-0.5">
                                                            <span class="badge rounded-pill fw-bold px-2 py-0.5" style="background-color: {{ $currentDoc->doc_type === 'MoU' ? '#1e3a8a' : ($currentDoc->doc_type === 'IA' ? '#059669' : '#0284c7') }}; font-size: 0.72rem; color: #fff;">{{ $currentDoc->doc_type }}</span>
                                                            <span class="text-secondary small ms-1">({{ $currentDoc->doc_type === 'MoU' ? 'Memorandum of Understanding / Nota Kesepahaman' : ($currentDoc->doc_type === 'IA' ? 'Implementation Arrangement / Perjanjian Pelaksanaan' : 'Memorandum of Agreement / Perjanjian Kerja Sama') }})</span>
                                                        </td>
                                                    </tr>
                                                    @if ($currentDoc->doc_type !== 'MoU')
                                                        <tr>
                                                            <td class="text-muted py-0.5 ps-0 pe-2 text-nowrap">Dokumen Induk (Rujukan)</td>
                                                            <td class="py-0.5 px-1 text-muted">:</td>
                                                            <td class="text-dark fw-medium py-0.5">
                                                                @if ($parentDoc)
                                                                    <a href="{{ route('simkerma.index', ['menu' => 4, 'view' => 'detail', 'doc' => $parentDoc->doc_id]) }}" class="text-primary text-decoration-none fw-bold">
                                                                        <i class="bi bi-link-45deg me-0.5"></i>[{{ $parentDoc->doc_type }}] {{ $parentDoc->title ?: $parentDoc->partner_name }} <span class="font-monospace text-muted fw-normal">({{ $parentDoc->doc_number ?: $parentDoc->doc_id }})</span>
                                                                    </a>
                                                                @elseif (!empty($currentDoc->parent_mou) && $currentDoc->parent_mou !== '-')
                                                                    <span class="font-monospace text-dark">{{ $currentDoc->parent_mou }}</span>
                                                                @else
                                                                    <span class="text-muted fst-italic">-</span>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endif
                                                    <tr>
                                                        <td class="text-muted py-0.5 ps-0 pe-2 text-nowrap">Nomor Naskah UNIPAR</td>
                                                        <td class="py-0.5 px-1 text-muted">:</td>
                                                        <td class="text-dark fw-medium py-0.5 font-monospace">{{ (!empty($currentDoc->doc_number) && $currentDoc->doc_number !== '-') ? $currentDoc->doc_number : '-' }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-muted py-0.5 ps-0 pe-2 text-nowrap">Nomor Naskah Mitra</td>
                                                        <td class="py-0.5 px-1 text-muted">:</td>
                                                        <td class="text-dark fw-medium py-0.5 font-monospace">{{ (!empty($currentDoc->partner_doc_number) && $currentDoc->partner_doc_number !== '-') ? $currentDoc->partner_doc_number : '-' }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-muted py-0.5 ps-0 pe-2 text-nowrap">Mitra Kerja Sama</td>
                                                        <td class="py-0.5 px-1 text-muted">:</td>
                                                        <td class="text-dark fw-medium py-0.5">
                                                            <strong>{{ $currentDoc->partner_name }}</strong>
                                                            @if (!empty($currentDoc->partner_category))
                                                                <span class="badge bg-light text-secondary border ms-1" style="font-size: 0.68rem;">{{ $currentDoc->partner_category }}</span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-muted py-0.5 ps-0 pe-2 text-nowrap">Unit Pengusul / Pelaksana</td>
                                                        <td class="py-0.5 px-1 text-muted">:</td>
                                                        <td class="text-dark fw-medium py-0.5">{{ $currentDoc->unit }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-muted py-0.5 ps-0 pe-2 text-nowrap">Penandatangan UNIPAR</td>
                                                        <td class="py-0.5 px-1 text-muted">:</td>
                                                        <td class="text-dark fw-medium py-0.5">
                                                            {{ $currentDoc->first_party_signer ?: '-' }}
                                                            @if (!empty($currentDoc->first_party_position))
                                                                <span class="text-muted small">({{ $currentDoc->first_party_position }})</span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-muted py-0.5 ps-0 pe-2 text-nowrap">Penandatangan Mitra</td>
                                                        <td class="py-0.5 px-1 text-muted">:</td>
                                                        <td class="text-dark fw-medium py-0.5">
                                                            {{ $currentDoc->second_party_signer ?: '-' }}
                                                            @if (!empty($currentDoc->second_party_position))
                                                                <span class="text-muted small">({{ $currentDoc->second_party_position }})</span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                    @if (!empty($currentDoc->pic_first_party) || !empty($currentDoc->pic_second_party))
                                                        <tr>
                                                            <td class="text-muted py-0.5 ps-0 pe-2 text-nowrap">PIC / Narahubung</td>
                                                            <td class="py-0.5 px-1 text-muted">:</td>
                                                            <td class="text-dark fw-medium py-0.5">
                                                                <span>UNIPAR: {{ $currentDoc->pic_first_party ?: '-' }}</span>
                                                                <span class="text-muted mx-1">&bull;</span>
                                                                <span>Mitra: {{ $currentDoc->pic_second_party ?: '-' }}</span>
                                                            </td>
                                                        </tr>
                                                    @endif
                                                    <tr>
                                                        <td class="text-muted py-0.5 ps-0 pe-2 text-nowrap">Ruang Lingkup</td>
                                                        <td class="py-0.5 px-1 text-muted">:</td>
                                                        <td class="text-dark fw-medium py-0.5" style="max-width: 480px;">{{ $currentDoc->scope ?: $currentDoc->title }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-muted py-0.5 ps-0 pe-2 text-nowrap">Masa Berlaku</td>
                                                        <td class="py-0.5 px-1 text-muted">:</td>
                                                        <td class="text-dark fw-medium py-0.5">
                                                            @if (!empty($currentDoc->start_date) && !empty($currentDoc->end_date))
                                                                {{ \Carbon\Carbon::parse($currentDoc->start_date)->translatedFormat('d F Y') }} &ndash; {{ \Carbon\Carbon::parse($currentDoc->end_date)->translatedFormat('d F Y') }}
                                                                @if (!empty($currentDoc->validity))
                                                                    <span class="text-secondary fw-normal">({{ $currentDoc->validity }})</span>
                                                                @endif
                                                            @else
                                                                <span class="text-muted fst-italic">{{ $currentDoc->validity ? $currentDoc->validity . ' (Tanggal Belum Ditetapkan)' : 'Belum Ditetapkan (Fase Draf)' }}</span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-muted py-0.5 ps-0 pe-2 text-nowrap">Peringatan</td>
                                                        <td class="py-0.5 px-1 text-muted">:</td>
                                                        <td class="py-0.5">
                                                            <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-2 py-0.5 fw-semibold" style="font-size: 0.7rem;">
                                                                <i class="bi bi-bell-fill me-1"></i> H-90 Hari (3 Bulan sebelum berakhir)
                                                            </span>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <!-- KARTU 3: DOKUMEN TURUNAN -->
                                <div class="card border rounded-3 bg-white mb-2 shadow-2xs">
                                    <div class="card-header bg-white py-1.5 px-3 border-bottom d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center gap-2 fw-bold text-dark" style="font-size: 0.85rem;">
                                            <i class="bi bi-layers text-primary"></i> Dokumen Turunan
                                            <span class="badge rounded-pill fw-medium px-2 py-0.5" style="background-color: #e0e7ff; color: #4338ca; font-size: 0.7rem;">
                                                {{ $childDocs->count() }} Dokumen
                                            </span>
                                        </div>
                                    </div>
                                    <div class="card-body p-0">
                                        @if ($currentDoc->doc_type === 'IA')
                                            <div class="p-3 text-center text-muted small">
                                                <i class="bi bi-shield-check text-success fs-5 d-block mb-1"></i>
                                                <div class="fw-semibold text-dark">Naskah IA merupakan Dokumen Tingkat Operasional Terakhir</div>
                                                <div style="font-size: 0.72rem;">Tidak menerbitkan turunan hukum lagi. Seluruh realisasi kegiatan dicatat langsung pada <strong>Tab Pelaksanaan (IKU 6)</strong>.</div>
                                            </div>
                                        @else
                                            <div class="table-responsive border-0 shadow-none mb-0">
                                                <table class="table table-hover align-middle mb-0" style="font-size: 0.76rem;">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th class="py-1 text-center text-muted fw-semibold" style="width: 45px;">No.</th>
                                                            <th class="py-1 text-center text-muted fw-semibold">Judul dan Nomor Dokumen</th>
                                                            <th class="py-1 text-center text-muted fw-semibold" style="width: 100px;">Jenis</th>
                                                            <th class="py-1 text-center text-muted fw-semibold">Unit Pelaksana</th>
                                                            <th class="py-1 text-center text-muted fw-semibold" style="width: 120px;">Status</th>
                                                            <th class="pe-3 py-1 text-center text-muted fw-semibold" style="width: 80px;">Aksi</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @forelse ($childDocs as $cIdx => $cDoc)
                                                            <tr>
                                                                <td class="py-1 text-center text-muted">{{ $cIdx + 1 }}</td>
                                                                <td class="py-1">
                                                                    <div class="fw-bold text-dark">{{ $cDoc->title ?: 'Dokumen Turunan' }}</div>
                                                                    <small class="text-muted font-monospace">{{ $cDoc->doc_number ?: $cDoc->doc_id }}</small>
                                                                </td>
                                                                <td class="py-1 text-center">
                                                                    @if (stripos($cDoc->doc_type, 'MoA') !== false)
                                                                        <span class="badge rounded-pill fw-semibold px-2 py-0.5" style="background-color: #e0f2fe; color: #0284c7; font-size: 0.7rem;">MoA / PKS</span>
                                                                    @else
                                                                        <span class="badge rounded-pill fw-semibold px-2 py-0.5" style="background-color: #dcfce7; color: #16a34a; font-size: 0.7rem;">IA</span>
                                                                    @endif
                                                                </td>
                                                                <td class="py-1 text-center text-secondary">{{ $cDoc->unit }}</td>
                                                                <td class="py-1 text-center">
                                                                    @if ($cDoc->status === 'ACTIVE')
                                                                        <span class="badge rounded-pill fw-semibold px-2 py-0.5" style="background-color: #dcfce7; color: #16a34a; font-size: 0.7rem;"><i class="bi bi-check me-0.5"></i> ACTIVE</span>
                                                                    @else
                                                                        <span class="badge rounded-pill fw-semibold px-2 py-0.5" style="background-color: #e0f2fe; color: #0284c7; font-size: 0.7rem;"><i class="bi bi-clock me-0.5"></i> {{ $cDoc->status }}</span>
                                                                    @endif
                                                                </td>
                                                                <td class="pe-3 py-1 text-center">
                                                                    <a href="{{ route('simkerma.index', ['menu' => 4, 'view' => 'detail', 'doc' => $cDoc->doc_id]) }}" class="btn btn-light border btn-sm py-0.5 px-1.5 shadow-2xs" title="Lihat Detail">
                                                                        <i class="bi bi-eye"></i>
                                                                    </a>
                                                                    <div class="dropdown d-inline-block">
                                                                        <button class="btn btn-light border btn-sm py-0.5 px-1.5 shadow-2xs dropdown-toggle dropdown-toggle-no-caret" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                                            <i class="bi bi-three-dots"></i>
                                                                        </button>
                                                                        <ul class="dropdown-menu dropdown-menu-end shadow border-0 small py-1">
                                                                            <li><a class="dropdown-item py-1 px-3" href="{{ route('simkerma.index', ['menu' => 4, 'view' => 'detail', 'doc' => $cDoc->doc_id]) }}"><i class="bi bi-eye me-1.5"></i> Detail Dokumen</a></li>
                                                                            <li><a class="dropdown-item py-1 px-3" href="{{ route('simkerma.verify', ['reg_id' => $cDoc->reg_id]) }}" target="_blank"><i class="bi bi-qr-code-scan me-1.5"></i> Verifikasi QR</a></li>
                                                                        </ul>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        @empty
                                                            <tr>
                                                                <td colspan="6" class="text-center text-muted py-2 small">Belum ada dokumen turunan terdaftar.</td>
                                                            </tr>
                                                        @endforelse
                                                    </tbody>
                                                </table>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                            </div>

                            <!-- KOLOM KANAN (4-5 COLS) -->
                            <div class="col-lg-4">

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

                                <div class="card border rounded-3 bg-white mb-2 shadow-2xs">
                                    <div class="card-header bg-white py-1.5 px-3 border-bottom d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center gap-2 fw-bold text-dark" style="font-size: 0.85rem;">
                                            <i class="bi bi-paperclip text-primary"></i> Dokumen &amp; Lampiran
                                        </div>
                                        @if(in_array($currentDoc->status, ['DRAFT', 'REVIEW', 'VERIFIED']))
                                            <button type="button" class="btn btn-outline-primary btn-sm py-0 px-2 fw-bold" style="font-size: 0.72rem;" data-bs-toggle="modal" data-bs-target="#uploadDocModal_{{ $currentDoc->id }}" title="Unggah Berkas Baru">
                                                <i class="bi bi-cloud-arrow-up me-1"></i>Unggah
                                            </button>
                                        @endif
                                    </div>
                                    <div class="card-body py-2 px-3">
                                        <!-- 1. Draf Naskah PDF -->
                                        <div class="d-flex align-items-center justify-content-between py-1 px-2 border rounded-2 mb-1.5 bg-light" style="font-size: 0.76rem;">
                                            <div class="d-flex align-items-center gap-2 text-truncate me-2">
                                                <div class="rounded-2 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 28px; height: 28px; background-color: {{ $draftExists ? '#fee2e2' : '#f1f5f9' }}; color: {{ $draftExists ? '#dc2626' : '#94a3b8' }};">
                                                    <i class="bi bi-file-earmark-pdf-fill"></i>
                                                </div>
                                                <div class="text-truncate">
                                                    <div class="fw-bold text-dark text-truncate" style="font-size: 0.76rem;">Draf {{ $currentDoc->doc_type }} &ndash; {{ $currentDoc->partner_name }}.pdf</div>
                                                    <div class="text-muted" style="font-size: 0.68rem;">
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
                                                    <a href="{{ $draftPath }}" target="_blank" class="btn btn-light border btn-sm py-0.5 px-1.5 shadow-2xs" title="Lihat Draf Naskah">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    <a href="{{ $draftPath }}" download class="btn btn-light border btn-sm py-0.5 px-1.5 shadow-2xs" title="Unduh Draf Naskah">
                                                        <i class="bi bi-download"></i>
                                                    </a>
                                                @else
                                                    <button type="button" class="btn btn-light border btn-sm py-0.5 px-1.5 disabled text-muted opacity-50" title="Berkas draf belum diunggah">
                                                        <i class="bi bi-dash"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </div>

                                        <!-- 2. Naskah Final Bertandatangan -->
                                        <div class="d-flex align-items-center justify-content-between py-1 px-2 border rounded-2 mb-1.5 bg-light" style="font-size: 0.76rem;">
                                            <div class="d-flex align-items-center gap-2 text-truncate me-2">
                                                <div class="rounded-2 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 28px; height: 28px; background-color: {{ $signedExists ? '#dcfce7' : '#f1f5f9' }}; color: {{ $signedExists ? '#16a34a' : '#94a3b8' }};">
                                                    <i class="bi bi-patch-check-fill"></i>
                                                </div>
                                                <div class="text-truncate">
                                                    <div class="fw-bold text-dark text-truncate" style="font-size: 0.76rem;">{{ $currentDoc->doc_type }} Sah (Bertandatangan).pdf</div>
                                                    <div class="text-muted" style="font-size: 0.68rem;">
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
                                                    <a href="{{ $signedPath }}" target="_blank" class="btn btn-light border btn-sm py-0.5 px-1.5 shadow-2xs" title="Lihat Naskah Sah">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    <a href="{{ $signedPath }}" download class="btn btn-light border btn-sm py-0.5 px-1.5 shadow-2xs" title="Unduh Naskah Sah">
                                                        <i class="bi bi-download"></i>
                                                    </a>
                                                @else
                                                    <button type="button" class="btn btn-light border btn-sm py-0.5 px-1.5 disabled text-muted opacity-50" title="Naskah sah belum diunggah">
                                                        <i class="bi bi-dash"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </div>

                                        <!-- 3. Berkas Naskah DOCX / Template Baku -->
                                        <div class="d-flex align-items-center justify-content-between py-1 px-2 border rounded-2 mb-0 bg-light" style="font-size: 0.76rem;">
                                            <div class="d-flex align-items-center gap-2 text-truncate me-2">
                                                <div class="rounded-2 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 28px; height: 28px; background-color: #e0f2fe; color: #0284c7;">
                                                    <i class="bi bi-file-earmark-word-fill"></i>
                                                </div>
                                                <div class="text-truncate">
                                                    @if ($docxExists)
                                                        <div class="fw-bold text-dark text-truncate" style="font-size: 0.76rem;">Naskah Word Pengusul.docx</div>
                                                        <div class="text-muted" style="font-size: 0.68rem;">{{ $docxSizeText }} &bull; Format Pengeditan Word</div>
                                                    @else
                                                        <div class="fw-bold text-dark text-truncate" style="font-size: 0.76rem;">Template Baku {{ $currentDoc->doc_type }}_UNIPAR.docx</div>
                                                        <div class="text-muted" style="font-size: 0.68rem;">Template Resmi LPPM / Bagian Kerja Sama</div>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-center gap-1 flex-shrink-0">
                                                @php $wordFile = $docxExists ? $docxPath : asset('templates/Template_MoU_UNIPAR.docx'); @endphp
                                                <a href="{{ $wordFile }}" target="_blank" class="btn btn-light border btn-sm py-0.5 px-1.5 shadow-2xs" title="Buka">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <a href="{{ $wordFile }}" download class="btn btn-light border btn-sm py-0.5 px-1.5 shadow-2xs" title="Unduh Berkas Word">
                                                    <i class="bi bi-download"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- KARTU 2: AKTIVITAS TERAKHIR -->
                                <div class="card border rounded-3 bg-white mb-2 shadow-2xs">
                                    <div class="card-header bg-white py-1.5 px-3 border-bottom d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center gap-2 fw-bold text-dark" style="font-size: 0.85rem;">
                                            <i class="bi bi-clock-history text-primary"></i> Aktivitas Terakhir
                                        </div>
                                    </div>
                                    <div class="card-body py-2 px-3">
                                        <!-- Vertical Activity Timeline Matching UI/detail_MoU.png -->
                                        <div class="activity-timeline position-relative ps-3" style="border-left: 2px solid #e2e8f0; margin-left: 8px;">
                                            @php
                                                $recentTrails = $currentDoc->auditTrails->sortByDesc('id')->take(5);
                                            @endphp

                                            @if ($recentTrails->isNotEmpty())
                                                @foreach ($recentTrails as $rIdx => $rTrail)
                                                    @php
                                                        $dotColor = match($rTrail->event) {
                                                            'ACTIVATE', 'VERIFY' => '#22c55e',
                                                            'SUBMIT' => '#3b82f6',
                                                            'RETURN_TO_DRAFT', 'TRIGGER_WARNING' => '#eab308',
                                                            'CANCEL', 'EXPIRE' => '#ef4444',
                                                            default => '#3b82f6'
                                                        };
                                                        $dotIcon = match($rTrail->event) {
                                                            'ACTIVATE' => 'bi-check',
                                                            'VERIFY' => 'bi-shield-check',
                                                            'SUBMIT' => 'bi-send',
                                                            'RETURN_TO_DRAFT' => 'bi-arrow-return-left',
                                                            default => 'bi-circle'
                                                        };
                                                    @endphp
                                                    <div class="position-relative {{ $loop->last ? 'mb-0' : 'mb-2' }} ps-2">
                                                        <div class="position-absolute rounded-circle d-flex align-items-center justify-content-center text-white" style="width: 18px; height: 18px; left: -18px; top: 1px; background-color: {{ $dotColor }}; font-size: 0.65rem;">
                                                            <i class="bi {{ $dotIcon }}"></i>
                                                        </div>
                                                        <div class="fw-bold text-dark text-truncate" style="font-size: 0.76rem;">{{ $rTrail->notes ?: $rTrail->event }}</div>
                                                        <div class="text-muted" style="font-size: 0.68rem;">oleh {{ $rTrail->actor }} ({{ $rTrail->role }}) &bull; {{ $rTrail->timestamp_wita }}</div>
                                                    </div>
                                                @endforeach
                                            @else
                                                <!-- Mockup Standard Timeline Items -->
                                                <div class="position-relative mb-2 ps-2">
                                                    <div class="position-absolute rounded-circle d-flex align-items-center justify-content-center text-white" style="width: 18px; height: 18px; left: -18px; top: 1px; background-color: #22c55e; font-size: 0.65rem;">
                                                        <i class="bi bi-check"></i>
                                                    </div>
                                                    <div class="fw-bold text-dark" style="font-size: 0.76rem;">Dokumen diaktifkan (Active)</div>
                                                    <div class="text-muted" style="font-size: 0.68rem;">oleh Rektor &bull; 03 Nov 2026, 10.24</div>
                                                </div>

                                                <div class="position-relative mb-2 ps-2">
                                                    <div class="position-absolute rounded-circle d-flex align-items-center justify-content-center text-white" style="width: 18px; height: 18px; left: -18px; top: 1px; background-color: #3b82f6; font-size: 0.65rem;">
                                                        <i class="bi bi-file-earmark-check"></i>
                                                    </div>
                                                    <div class="fw-bold text-dark" style="font-size: 0.76rem;">Disetujui oleh Pimpinan</div>
                                                    <div class="text-muted" style="font-size: 0.68rem;">oleh Rektor &bull; 03 Nov 2026, 10.21</div>
                                                </div>

                                                <div class="position-relative mb-2 ps-2">
                                                    <div class="position-absolute rounded-circle d-flex align-items-center justify-content-center text-white" style="width: 18px; height: 18px; left: -18px; top: 1px; background-color: #8b5cf6; font-size: 0.65rem;">
                                                        <i class="bi bi-search"></i>
                                                    </div>
                                                    <div class="fw-bold text-dark" style="font-size: 0.76rem;">Direview oleh LPPM</div>
                                                    <div class="text-muted" style="font-size: 0.68rem;">oleh Kepala LPPM &bull; 02 Nov 2026, 15.37</div>
                                                </div>

                                                <div class="position-relative mb-2 ps-2">
                                                    <div class="position-absolute rounded-circle d-flex align-items-center justify-content-center text-white" style="width: 18px; height: 18px; left: -18px; top: 1px; background-color: #22c55e; font-size: 0.65rem;">
                                                        <i class="bi bi-check"></i>
                                                    </div>
                                                    <div class="fw-bold text-dark" style="font-size: 0.76rem;">Diverifikasi oleh Unit</div>
                                                    <div class="text-muted" style="font-size: 0.68rem;">oleh Kaprodi Teknik Informatika &bull; 01 Nov 2026, 14.20</div>
                                                </div>

                                                <div class="position-relative mb-0 ps-2">
                                                    <div class="position-absolute rounded-circle d-flex align-items-center justify-content-center text-white" style="width: 18px; height: 18px; left: -18px; top: 1px; background-color: #94a3b8; font-size: 0.65rem;">
                                                        <i class="bi bi-plus"></i>
                                                    </div>
                                                    <div class="fw-bold text-dark" style="font-size: 0.76rem;">Pengajuan dibuat</div>
                                                    <div class="text-muted" style="font-size: 0.68rem;">oleh Staf Prodi &bull; 01 Nov 2026, 09.16</div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                            </div>

                        </div>
                    </div>

                    <!-- TAB 2: DOKUMEN & LAMPIRAN DETAIL -->
                    <div class="tab-pane fade" id="tab-docs-pane" role="tabpanel">
                        <div class="card border rounded-3 p-3 bg-white mb-2 shadow-2xs">
                            <div class="d-flex justify-content-between align-items-center mb-2.5">
                                <div>
                                    <h6 class="fw-bold mb-0 text-dark small"><i class="bi bi-files text-primary me-1"></i> Manajemen Berkas &amp; Pratinjau Naskah {{ $currentDoc->doc_type }}</h6>
                                    <div class="text-muted" style="font-size: 0.72rem;">Unggah atau periksa draf pengajuan serta naskah asli bertandatangan basah/elektronik</div>
                                </div>
                                <button type="button" class="btn btn-primary btn-sm fw-semibold py-1 px-2.5" style="font-size: 0.75rem;" data-bs-toggle="modal" data-bs-target="#uploadDocModal_{{ $currentDoc->id }}">
                                    <i class="bi bi-upload me-1"></i> Unggah Berkas Baru
                                </button>
                            </div>
                            <div class="row g-3">
                                <!-- Kolom 1: Draf Naskah Pengusul -->
                                <div class="col-lg-6">
                                    <div class="p-2.5 border rounded-2 bg-light h-100 d-flex flex-column justify-content-between">
                                        <div>
                                            <div class="d-flex align-items-center justify-content-between mb-2">
                                                <h6 class="fw-bold text-dark mb-0 small"><i class="bi bi-file-earmark-pdf text-danger me-1"></i>Naskah Draf Pengusul</h6>
                                                @if ($currentDoc->draft_file && file_exists(public_path($currentDoc->draft_file)))
                                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-0.5" style="font-size: 0.68rem;">Tersedia</span>
                                                @else
                                                    <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2 py-0.5" style="font-size: 0.68rem;">Belum Diunggah</span>
                                                @endif
                                            </div>
                                            @if ($currentDoc->draft_file && file_exists(public_path($currentDoc->draft_file)))
                                                <div class="ratio ratio-16x9 border bg-white mb-2 rounded-1 overflow-hidden" style="max-height: 380px;">
                                                    <iframe src="{{ asset($currentDoc->draft_file) }}" title="Preview Draf {{ $currentDoc->doc_type }}"></iframe>
                                                </div>
                                            @else
                                                <div class="text-muted py-4 text-center small border rounded bg-white mb-2">
                                                    <i class="bi bi-file-earmark-x text-secondary fs-3 d-block mb-1"></i>
                                                    Berkas naskah draf belum diunggah.
                                                </div>
                                            @endif
                                        </div>
                                        @if ($currentDoc->draft_file && file_exists(public_path($currentDoc->draft_file)))
                                            <div class="d-flex gap-1.5 pt-1 border-top">
                                                <a href="{{ asset($currentDoc->draft_file) }}" target="_blank" class="btn btn-outline-secondary btn-sm py-0.5 px-2" style="font-size: 0.72rem;"><i class="bi bi-box-arrow-up-right me-1"></i>Buka Tab Baru</a>
                                                <a href="{{ asset($currentDoc->draft_file) }}" download class="btn btn-secondary btn-sm py-0.5 px-2" style="font-size: 0.72rem;"><i class="bi bi-download me-1"></i>Unduh PDF</a>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <!-- Kolom 2: Naskah Final Bertandatangan Resmi -->
                                <div class="col-lg-6">
                                    <div class="p-2.5 border rounded-2 bg-light h-100 d-flex flex-column justify-content-between">
                                        <div>
                                            <div class="d-flex align-items-center justify-content-between mb-2">
                                                <h6 class="fw-bold text-dark mb-0 small"><i class="bi bi-file-earmark-check text-success me-1"></i>Naskah Final Bertandatangan</h6>
                                                @if ($currentDoc->signed_file && file_exists(public_path($currentDoc->signed_file)))
                                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-0.5" style="font-size: 0.68rem;">Sah &amp; Bertandatangan</span>
                                                @else
                                                    <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2 py-0.5" style="font-size: 0.68rem;">Menunggu Naskah Final</span>
                                                @endif
                                            </div>
                                            @if ($currentDoc->signed_file && file_exists(public_path($currentDoc->signed_file)))
                                                <div class="ratio ratio-16x9 border bg-white mb-2 rounded-1 overflow-hidden" style="max-height: 380px;">
                                                    <iframe src="{{ asset($currentDoc->signed_file) }}" title="Preview Naskah Final {{ $currentDoc->doc_type }}"></iframe>
                                                </div>
                                            @else
                                                <div class="text-muted py-4 text-center small border rounded bg-white mb-2">
                                                    <i class="bi bi-patch-exclamation text-warning fs-3 d-block mb-1"></i>
                                                    Naskah bertandatangan belum diunggah.<br><small class="text-muted">Diunggah saat verifikasi final atau aktivasi naskah resmi.</small>
                                                </div>
                                            @endif
                                        </div>
                                        @if ($currentDoc->signed_file && file_exists(public_path($currentDoc->signed_file)))
                                            <div class="d-flex gap-1.5 pt-1 border-top">
                                                <a href="{{ asset($currentDoc->signed_file) }}" target="_blank" class="btn btn-outline-success btn-sm py-0.5 px-2" style="font-size: 0.72rem;"><i class="bi bi-box-arrow-up-right me-1"></i>Buka Tab Baru</a>
                                                <a href="{{ asset($currentDoc->signed_file) }}" download class="btn btn-success btn-sm py-0.5 px-2" style="font-size: 0.72rem;"><i class="bi bi-download me-1"></i>Unduh PDF</a>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- TAB 3: DOKUMEN TURUNAN DETAIL -->
                    <div class="tab-pane fade" id="tab-deriv-pane" role="tabpanel">
                        <div class="card border rounded-3 p-3 bg-white mb-2 shadow-2xs">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div>
                                    <h6 class="fw-bold mb-0 text-dark small">Daftar Dokumen Turunan {{ $currentDoc->doc_type === 'MoU' ? '(MoA & IA)' : ($currentDoc->doc_type === 'IA' ? '(Pelaksanaan Operasional)' : '(IA)') }}</h6>
                                    <div class="text-muted" style="font-size: 0.72rem;">Naskah turunan pelaksanaan yang berinduk secara hukum ke dokumen {{ $currentDoc->doc_type }} ini</div>
                                </div>
                                @if ($currentDoc->doc_type === 'MoU')
                                    <div class="d-flex gap-1.5">
                                        <a href="{{ route('simkerma.index', ['menu' => 4, 'view' => 'new_derive', 'parent_id' => $currentDoc->doc_id, 'derive_type' => 'MoA / PKS']) }}" class="btn btn-primary btn-sm fw-semibold py-1 px-2.5" style="font-size: 0.75rem;">
                                            <i class="bi bi-plus-lg me-1"></i> + Tambah MoA / PKS
                                        </a>
                                        <a href="{{ route('simkerma.index', ['menu' => 4, 'view' => 'new_derive', 'parent_id' => $currentDoc->doc_id, 'derive_type' => 'IA']) }}" class="btn btn-success btn-sm fw-semibold py-1 px-2.5" style="font-size: 0.75rem;">
                                            <i class="bi bi-plus-lg me-1"></i> + Tambah IA
                                        </a>
                                    </div>
                                @elseif ($currentDoc->doc_type !== 'IA')
                                    <div class="d-flex gap-1.5">
                                        <a href="{{ route('simkerma.index', ['menu' => 4, 'view' => 'new_derive', 'parent_id' => $currentDoc->doc_id, 'derive_type' => 'IA']) }}" class="btn btn-success btn-sm fw-semibold py-1 px-2.5" style="font-size: 0.75rem;">
                                            <i class="bi bi-plus-lg me-1"></i> + Tambah IA (Implementation)
                                        </a>
                                    </div>
                                @endif
                            </div>

                            @if ($currentDoc->doc_type === 'IA')
                                <div class="text-center py-4 bg-light rounded-2 border">
                                    <i class="bi bi-shield-check text-success fs-2 mb-2 d-inline-block"></i>
                                    <h6 class="fw-bold text-dark mb-1">Dokumen Tingkat Operasional Terakhir (Implementation Arrangement)</h6>
                                    <p class="text-muted mx-auto mb-3" style="max-width: 560px; font-size: 0.78rem;">
                                        Sesuai regulasi tata kelola kerja sama perguruan tinggi, dokumen <strong>Implementation Arrangement (IA)</strong> berada di tingkat Program Studi/Laboratorium dan tidak memiliki naskah turunan hukum lebih lanjut. Seluruh implementasi riil dicatat sebagai <strong>Catatan Pelaksanaan Kegiatan (IKU 6)</strong> pada tab di samping.
                                    </p>
                                    <button type="button" class="btn btn-primary btn-sm fw-semibold px-3 py-1.5" onclick="document.getElementById('tab-act-btn').click();">
                                        <i class="bi bi-list-task me-1"></i> Lihat Tab Pelaksanaan Kegiatan
                                    </button>
                                </div>
                            @elseif ($childDocs->isNotEmpty())
                                <div class="table-responsive border-0">
                                    <table class="table table-bordered table-hover align-middle mb-0" style="font-size: 0.76rem;">
                                        <thead class="table-light">
                                            <tr>
                                                <th class="py-1 text-center text-muted fw-semibold" style="width: 45px;">No.</th>
                                                <th class="py-1 text-center text-muted fw-semibold" style="width: 100px;">Jenis</th>
                                                <th class="py-1 text-center text-muted fw-semibold">Judul dan Nomor Dokumen</th>
                                                <th class="py-1 text-center text-muted fw-semibold">Unit Pelaksana</th>
                                                <th class="py-1 text-center text-muted fw-semibold">Mitra</th>
                                                <th class="py-1 text-center text-muted fw-semibold" style="width: 120px;">Status</th>
                                                <th class="py-1 text-center text-muted fw-semibold" style="width: 80px;">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($childDocs as $cIdx => $cDoc)
                                                <tr>
                                                    <td class="py-1 text-center text-muted">{{ $cIdx + 1 }}</td>
                                                    <td class="py-1 text-center">
                                                        @if (stripos($cDoc->doc_type, 'MoA') !== false)
                                                            <span class="badge rounded-pill fw-semibold px-2 py-0.5" style="background-color: #e0f2fe; color: #0284c7; font-size: 0.7rem;">MoA / PKS</span>
                                                        @else
                                                            <span class="badge rounded-pill fw-semibold px-2 py-0.5" style="background-color: #dcfce7; color: #16a34a; font-size: 0.7rem;">IA</span>
                                                        @endif
                                                    </td>
                                                    <td class="py-1">
                                                        <div class="fw-bold text-dark">{{ $cDoc->title ?: 'Dokumen Turunan' }}</div>
                                                        <small class="text-muted font-monospace">{{ $cDoc->doc_number ?: $cDoc->doc_id }}</small>
                                                    </td>
                                                    <td class="py-1 text-center text-secondary">{{ $cDoc->unit }}</td>
                                                    <td class="py-1 text-center text-dark">{{ $cDoc->partner_name }}</td>
                                                    <td class="py-1 text-center">
                                                        <span class="badge {{ $stepperList[$cDoc->status][1] ?? 'bg-secondary' }} px-2 py-0.5" style="font-size: 0.7rem;">
                                                            {{ $cDoc->status }}
                                                        </span>
                                                    </td>
                                                    <td class="py-1 text-center">
                                                        <a href="{{ route('simkerma.index', ['menu' => 4, 'view' => 'detail', 'doc' => $cDoc->doc_id]) }}" class="btn btn-outline-primary btn-sm py-0.5 px-2" style="font-size: 0.72rem;">
                                                            Detail
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-4 bg-light rounded-2 border">
                                    <i class="bi bi-diagram-3 text-muted fs-3"></i>
                                    <div class="fw-semibold text-dark mt-1 small">Belum Ada Dokumen Turunan Terdaftar</div>
                                    <div class="text-muted" style="font-size: 0.72rem;">Gunakan tombol di atas untuk menambahkan perjanjian kerja sama turunan ({{ $currentDoc->doc_type === 'MoU' ? 'MoA / IA' : 'IA' }})</div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- TAB 4: PELAKSANAAN KEGIATAN -->
                    <div class="tab-pane fade" id="tab-act-pane" role="tabpanel">
                        <div class="card border rounded-3 p-3 bg-white mb-2 shadow-2xs">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div>
                                    <h6 class="fw-bold mb-0 text-dark small"><i class="bi bi-calendar2-check text-primary me-1"></i> Catatan Pelaksanaan Kerja Sama (IKU 6)</h6>
                                    <div class="text-muted" style="font-size: 0.72rem;">Kegiatan riil operasional yang mengimplementasikan naskah {{ $currentDoc->doc_type }} ini</div>
                                </div>
                                <button type="button" class="btn btn-primary btn-sm fw-semibold py-1 px-2.5" style="font-size: 0.75rem;" data-bs-toggle="modal" data-bs-target="#createActivityModal_{{ $currentDoc->id }}">
                                    <i class="bi bi-plus-lg me-1"></i> Catat Kegiatan Baru
                                </button>
                            </div>
                            @if ($currentDoc->activities->isNotEmpty())
                                <div class="table-responsive border-0">
                                    <table class="table table-bordered table-hover align-middle mb-0" style="font-size: 0.76rem;">
                                        <thead class="table-light">
                                            <tr>
                                                <th class="py-1 text-center" style="width: 40px;">No.</th>
                                                <th class="py-1">Nama Kegiatan Pelaksanaan</th>
                                                <th class="py-1" style="width: 140px;">Jadwal Pelaksanaan</th>
                                                <th class="py-1">Pihak / Peserta Terlibat</th>
                                                <th class="py-1">Luaran (IKU 6)</th>
                                                <th class="py-1 text-center" style="width: 100px;">Anggaran</th>
                                                <th class="py-1 text-center" style="width: 110px;">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($currentDoc->activities as $actIdx => $act)
                                                <tr>
                                                    <td class="py-1 text-center text-muted">{{ $actIdx + 1 }}</td>
                                                    <td class="py-1">
                                                        <div class="fw-bold text-dark">{{ $act->activity_name }}</div>
                                                        @if ($act->notes)
                                                            <div class="text-muted" style="font-size: 0.68rem;">{{ $act->notes }}</div>
                                                        @endif
                                                    </td>
                                                    <td class="py-1 text-nowrap">
                                                        <span>{{ $act->schedule_start ?: '-' }}</span>
                                                        @if ($act->schedule_end)
                                                            <div class="text-muted" style="font-size: 0.68rem;">s/d {{ $act->schedule_end }}</div>
                                                        @endif
                                                    </td>
                                                    <td class="py-1 text-secondary">{{ $act->parties_involved ?: '-' }}</td>
                                                    <td class="py-1">{{ $act->outputs ?: '-' }}</td>
                                                    <td class="py-1 text-center fw-semibold text-dark">{{ $act->budget_info ?: '-' }}</td>
                                                    <td class="py-1 text-center">
                                                        @php
                                                            $actBadge = match($act->execution_status) {
                                                                'SELESAI' => 'bg-success-subtle text-success border border-success-subtle',
                                                                'SEDANG_BERJALAN' => 'bg-primary-subtle text-primary border border-primary-subtle',
                                                                'TERKENDALA' => 'bg-warning-subtle text-warning border border-warning-subtle',
                                                                'DIBATALKAN' => 'bg-danger-subtle text-danger border border-danger-subtle',
                                                                default => 'bg-secondary-subtle text-secondary border border-secondary-subtle'
                                                            };
                                                        @endphp
                                                        <span class="badge {{ $actBadge }} px-2 py-0.5" style="font-size: 0.68rem;">{{ $act->execution_status }}</span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-4 bg-light rounded-2 border">
                                    <i class="bi bi-calendar-x text-muted fs-3"></i>
                                    <div class="fw-semibold text-dark mt-1 small">Belum Ada Catatan Pelaksanaan</div>
                                    <div class="text-muted" style="font-size: 0.72rem;">Klik tombol "Catat Kegiatan Baru" untuk merekam implementasi Tri Dharma perguruan tinggi yang merujuk pada naskah ini.</div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- TAB 5: RIWAYAT & AUDIT DETAIL -->
                    <div class="tab-pane fade" id="tab-audit-pane" role="tabpanel">
                        <div class="card border rounded-3 p-3 bg-white mb-2 shadow-2xs">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div>
                                    <h6 class="fw-bold mb-0 text-dark small"><i class="bi bi-shield-lock text-primary me-1"></i> Jejak Audit Relasional FSM (Immutable Audit Trail)</h6>
                                    <div class="text-muted" style="font-size: 0.72rem;">Catatan mutlak setiap transisi status dan aksi pejabat berwenang di Universitas Parna Raya (UNIPAR)</div>
                                </div>
                                <span class="badge bg-light text-dark border font-monospace py-1 px-2" style="font-size: 0.72rem;">
                                    Total: {{ $currentDoc->auditTrails->count() }} Rekaman
                                </span>
                            </div>
                            @if ($currentDoc->auditTrails->isNotEmpty())
                                <div class="table-responsive border-0">
                                    <table class="table table-bordered table-striped align-middle mb-0" style="font-size: 0.76rem;">
                                        <thead class="table-dark">
                                            <tr>
                                                <th class="py-1" style="width: 50px;">#</th>
                                                <th class="py-1">Waktu (WITA)</th>
                                                <th class="py-1">Aktor</th>
                                                <th class="py-1">Peran</th>
                                                <th class="py-1">Event Transisi</th>
                                                <th class="py-1">Dari Status</th>
                                                <th class="py-1">Ke Status</th>
                                                <th class="py-1">Catatan Verifikasi / Disposisi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($currentDoc->auditTrails->sortByDesc('id') as $aIdx => $trail)
                                                <tr>
                                                    <td class="py-1 text-muted">{{ $aIdx + 1 }}</td>
                                                    <td class="py-1 font-monospace text-nowrap">{{ $trail->timestamp_wita }}</td>
                                                    <td class="py-1 fw-bold text-dark">{{ $trail->actor }}</td>
                                                    <td class="py-1"><span class="badge bg-secondary-subtle text-secondary py-0 px-1">{{ $trail->role }}</span></td>
                                                    <td class="py-1"><span class="badge bg-primary text-white font-monospace py-0 px-1">{{ $trail->event }}</span></td>
                                                    <td class="py-1"><span class="badge bg-light text-dark border">{{ $trail->from_status }}</span></td>
                                                    <td class="py-1"><span class="badge bg-success-subtle text-success border border-success-subtle">{{ $trail->to_status }}</span></td>
                                                    <td class="py-1 text-muted">{{ $trail->notes }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-4 bg-light rounded-2 border">
                                    <i class="bi bi-clock-history text-muted fs-3"></i>
                                    <div class="fw-semibold text-dark mt-1 small">Belum Ada Rekaman Jejak Audit</div>
                                </div>
                            @endif
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
                        <form method="POST" action="{{ route('simkerma.update-document') }}" class="d-flex flex-column" style="max-height: 85vh;">
                            @csrf
                            <input type="hidden" name="doc_id" value="{{ $currentDoc->doc_id }}">
                            <div class="modal-body p-3.5 small overflow-y-auto" style="max-height: calc(85vh - 120px); -webkit-overflow-scrolling: touch;">
                                
                                <!-- 0. JENIS DOKUMEN & INDUK DOKUMEN -->
                                <div class="row g-2 mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold text-dark mb-1" style="font-size: 0.8rem;">
                                            Jenis Dokumen Kerja Sama : <span class="text-danger">*</span>
                                        </label>
                                        <select name="doc_type" id="edit_doc_type_{{ $currentDoc->id }}" class="form-select form-select-sm" required onchange="toggleEditParent_{{ $currentDoc->id }}()">
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
                                            </label>
                                            <input type="text" name="doc_number" class="form-control form-control-sm font-monospace" value="{{ (!empty($currentDoc->doc_number) && $currentDoc->doc_number !== '-') ? $currentDoc->doc_number : '' }}" placeholder="-">
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
                                            </label>
                                            <div class="hybrid-partner-combobox position-relative">
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text bg-white border-end-0">
                                                        <i class="partner-input-icon bi bi-mortarboard-fill text-primary fs-5"></i>
                                                    </span>
                                                    <input type="text" name="partner_name" id="edit_partner_name_{{ $currentDoc->id }}" class="form-control border-start-0 smart-partner-input" 
                                                           placeholder="Ketik nama kampus (contoh: Sam Ratulangi, ITB, Parna Raya)..." 
                                                           value="{{ $currentDoc->partner_name }}"
                                                           list="editPartnerDatalist_{{ $currentDoc->id }}"
                                                           autocomplete="off" required>
                                                    <button class="btn btn-outline-secondary btn-clear-partner {{ empty($currentDoc->partner_name) ? 'd-none' : '' }}" type="button" title="Hapus / Reset Pilihan"><i class="bi bi-x-lg"></i></button>
                                                    <button class="btn btn-primary btn-sm px-3 fw-bold btn-open-quick-add" type="button" data-bs-toggle="modal" data-bs-target="#quickAddPartnerModal" title="Tambah Mitra Baru ke Master Data (Menu 02)">
                                                        <i class="bi bi-plus-lg me-1"></i> Tambah Mitra
                                                    </button>
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
                                            </label>
                                            <input type="text" name="partner_doc_number" class="form-control form-control-sm font-monospace" value="{{ (!empty($currentDoc->partner_doc_number) && $currentDoc->partner_doc_number !== '-') ? $currentDoc->partner_doc_number : '' }}" placeholder="-">
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
                        <form method="POST" action="{{ route('simkerma.upload-document-file') }}" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="doc_id" value="{{ $currentDoc->doc_id }}">
                            <div class="modal-body p-3 small">
                                <div class="mb-2">
                                    <label class="form-label text-muted fw-semibold mb-0.5">Jenis Berkas</label>
                                    <select name="file_type" class="form-select form-select-sm" required>
                                        <option value="draft">Naskah Draf Pengusul (PDF/DOCX)</option>
                                        <option value="signed">Naskah Asli Bertandatangan Basah &amp; Bermeterai (PDF)</option>
                                    </select>
                                </div>
                                <div class="mb-2">
                                    <label class="form-label text-muted fw-semibold mb-0.5">Pilih Berkas</label>
                                    <input type="file" name="file" class="form-control form-control-sm" required accept=".pdf,.doc,.docx">
                                </div>
                            </div>
                            <div class="modal-footer py-2 px-3 bg-light border-top">
                                <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-primary btn-sm fw-semibold"><i class="bi bi-upload me-1"></i> Unggah Berkas</button>
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
                        <form action="{{ route('simkerma.store-activity') }}" method="POST">
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
                                            <option value="SEDANG_BERJALAN" selected>SEDANG_BERJALAN</option>
                                            <option value="SELESAI">SELESAI</option>
                                            <option value="TERKENDALA">TERKENDALA</option>
                                            <option value="DIBATALKAN">DIBATALKAN</option>
                                        </select>
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
                    <form action="{{ route('simkerma.store-activity') }}" method="POST">
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

                                <!-- Bukti Berkas -->
                                <div class="col-sm-6">
                                    <label class="form-label fw-bold text-dark">Nama Berkas Bukti Laporan (PDF)</label>
                                    <input type="text" name="evidence_file" class="form-control form-control-sm" placeholder="Contoh: laporan_magang_sasa_2026.pdf">
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label fw-bold text-dark">Dokumentasi Foto / Sertifikat</label>
                                    <input type="text" name="evidence_photos" class="form-control form-control-sm" placeholder="Contoh: foto_kegiatan.jpg">
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
                                    <option value="DUDI / Swasta" selected>DUDI / Swasta (Industri)</option>
                                    <option value="Instansi Pemerintah">Instansi Pemerintah</option>
                                    <option value="BUMN / BUMD">BUMN / BUMD</option>
                                    <option value="Perguruan Tinggi Dalam Negeri">Perguruan Tinggi Dalam Negeri</option>
                                    <option value="Perguruan Tinggi Luar Negeri">Perguruan Tinggi Luar Negeri</option>
                                    <option value="Organisasi / Komunitas / Lainnya">Organisasi / Komunitas / Lainnya</option>
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
