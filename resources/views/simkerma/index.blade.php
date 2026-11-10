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
            <div class="card-header bg-white py-3 border-bottom d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2">
                <div>
                    <div class="d-flex align-items-center gap-2">
                        <h6 class="mb-0 fw-bold text-uppercase">
                            <i class="bi bi-building text-primary me-1"></i> Direktori &amp; Master Data Mitra
                        </h6>
                        <span class="badge bg-primary text-white font-monospace" style="font-size: 0.65rem;">{{ $partners->count() }} MITRA TERDAFTAR &bull; MYSQL</span>
                    </div>
                    <small class="text-muted">Penyimpanan Terpusat Profil, Alamat, Kontak, PIC, &amp; Riwayat Kemitraan Tridharma UNIPAR</small>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-primary btn-sm fw-semibold shadow-sm" data-bs-toggle="modal" data-bs-target="#createPartnerModal">
                        <i class="bi bi-plus-circle-fill me-1"></i> + Input Mitra Mandiri
                    </button>
                    <a href="{{ route('simkerma.index', ['menu' => 4, 'view' => 'new']) }}" class="btn btn-outline-dark btn-sm fw-semibold shadow-sm">
                        <i class="bi bi-file-earmark-plus me-1"></i> Ajukan Naskah Baru
                    </a>
                </div>
            </div>

            <!-- FILTER & PENCARIAN BERSIH SISI KLIEN -->
            <div class="card-body border-bottom bg-light py-2 px-3">
                <div class="row g-2 align-items-center">
                    <div class="col-12 col-md-5">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                            <input type="text" id="partnerSearchInput" class="form-control border-start-0" placeholder="Cari nama mitra, PIC, kota, atau bidang..." onkeyup="filterPartnersTable()">
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <select id="partnerCategoryFilter" class="form-select form-select-sm" onchange="filterPartnersTable()">
                            <option value="">-- Semua Kategori Mitra --</option>
                            <option value="DUDI / Swasta">DUDI / Swasta (Industri)</option>
                            <option value="Instansi Pemerintah">Instansi Pemerintah</option>
                            <option value="BUMN">BUMN / BUMD</option>
                            <option value="Perguruan Tinggi">Perguruan Tinggi</option>
                            <option value="Organisasi">Organisasi / Komunitas / Lainnya</option>
                        </select>
                    </div>
                    <div class="col-6 col-md-2">
                        <select id="partnerStatusFilter" class="form-select form-select-sm" onchange="filterPartnersTable()">
                            <option value="">-- Semua Status --</option>
                            <option value="Terverifikasi Sah">Terverifikasi Sah</option>
                            <option value="Peringatan Masa">Peringatan Masa</option>
                            <option value="Tahap FSM">Tahap FSM</option>
                            <option value="Mitra Terdaftar">Mitra Terdaftar</option>
                        </select>
                    </div>
                    <div class="col-12 col-md-2 text-md-end">
                        <button type="button" class="btn btn-outline-secondary btn-sm w-100" onclick="resetPartnerFilters()">
                            <i class="bi bi-arrow-counterclockwise me-1"></i> Reset
                        </button>
                    </div>
                </div>
            </div>

            <!-- TABEL PENGELOLAAN DATA MITRA PROPORSI RAPI -->
            <div class="table-responsive">
                <table class="table table-wireframe table-hover align-middle mb-0 small" id="partnerTable">
                    <thead class="bg-light">
                        <tr>
                            <th style="min-width: 160px;">Nama &amp; Profil Mitra</th>
                            <th style="min-width: 120px;">Kategori Mitra</th>
                            <th style="min-width: 140px;">Unit Kampus &amp; Mitra Rujukan</th>
                            <th style="min-width: 150px;">Lingkup Kerja Sama Utama</th>
                            <th style="min-width: 100px;">Riwayat Dokumen</th>
                            <th style="min-width: 105px;" class="text-center">Status Kemitraan</th>
                            <th style="min-width: 110px;" class="text-end">Aksi Terintegrasi</th>
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
                                <!-- 1. Nama & Profil Mitra -->
                                <td>
                                    <div class="fw-bold text-dark fs-6">{{ $partner->name }}</div>
                                    <div class="text-muted small mt-1" style="font-size: 0.72rem;">
                                        <i class="bi bi-geo-alt text-danger me-1"></i>{{ $partner->city ?: 'Kota Manado' }}
                                        <span class="badge bg-light text-primary border ms-1" style="font-size: 0.65rem;">{{ $partner->scope_coverage }}</span>
                                    </div>
                                </td>

                                <!-- 2. Kategori Mitra & Verifikasi -->
                                <td>
                                    <span class="badge bg-light text-dark border">{{ $partner->category ?: 'Kemitraan Strategis' }}</span>
                                    <div class="mt-1">
                                        <span class="badge bg-success-subtle text-success border border-success" style="font-size: 0.62rem;">
                                            <i class="bi bi-patch-check-fill me-1"></i>{{ $partner->status_verification ?: 'Profil Terverifikasi' }}
                                        </span>
                                    </div>
                                </td>

                                <!-- 3. Unit Kampus & Mitra Rujukan -->
                                <td>
                                    @if ($units->isNotEmpty())
                                        <div class="text-dark fw-semibold">{{ $units->join(', ') }}</div>
                                        <div class="text-muted small" style="font-size: 0.72rem;">
                                            Dokumen Terbaru: <span class="font-monospace text-primary">{{ $firstDoc->doc_number ?: $firstDoc->doc_id }}</span>
                                        </div>
                                    @else
                                        <div class="text-muted fst-italic" style="font-size: 0.75rem;">Belum ada naskah terkait</div>
                                        <div class="text-muted small" style="font-size: 0.70rem;">Siap diajukan di Menu 04</div>
                                    @endif
                                </td>

                                <!-- 4. Lingkup Kerja Sama Utama -->
                                <td>
                                    <div class="text-secondary" style="font-size: 0.75rem;">
                                        {{ Str::limit($partner->profile_desc ?: ($firstDoc ? $firstDoc->scope : 'Kerja sama kemitraan institusional Tridharma'), 85) }}
                                    </div>
                                </td>

                                <!-- 5. Riwayat Dokumen -->
                                <td>
                                    @if ($pDocs->isNotEmpty())
                                        <span class="badge bg-dark rounded-pill">{{ $pDocs->count() }} Dokumen</span>
                                        <div class="small text-muted mt-1" style="font-size: 0.7rem;">
                                            {{ $types->join(', ') }}
                                        </div>
                                    @else
                                        <span class="badge bg-light text-muted border">0 Dokumen</span>
                                    @endif
                                </td>

                                <!-- 6. Status Kemitraan FSM -->
                                <td class="text-center">
                                    @if ($hasActive)
                                        <span class="badge bg-success py-1 px-2 shadow-sm d-block"><i class="bi bi-check-circle-fill me-1"></i>Terverifikasi Sah</span>
                                    @elseif ($hasWarning)
                                        <span class="badge bg-warning text-dark py-1 px-2 border shadow-sm d-block"><i class="bi bi-exclamation-triangle-fill me-1"></i>Peringatan Masa</span>
                                    @elseif ($pDocs->isNotEmpty())
                                        <span class="badge bg-primary py-1 px-2 shadow-sm d-block"><i class="bi bi-hourglass-split me-1"></i>Tahap FSM</span>
                                    @else
                                        <span class="badge bg-secondary py-1 px-2 shadow-sm d-block"><i class="bi bi-journal-check me-1"></i>Mitra Terdaftar</span>
                                    @endif
                                </td>

                                <!-- 7. Aksi Terintegrasi -->
                                <td class="text-end">
                                    <button type="button" class="btn btn-warning btn-sm py-1 px-2 fw-semibold w-100 mb-1 shadow-sm text-dark" style="font-size: 0.72rem;" data-bs-toggle="modal" data-bs-target="#editPartnerModal_{{ $partner->id }}">
                                        <i class="bi bi-pencil-square me-1"></i>Edit Data Mitra
                                    </button>
                                    <button type="button" class="btn btn-dark btn-sm py-1 px-2 fw-semibold w-100 mb-1 shadow-sm" style="font-size: 0.72rem;" data-bs-toggle="modal" data-bs-target="#partnerModal_{{ $partner->id }}">
                                        <i class="bi bi-eye-fill text-warning me-1"></i>Profil &amp; Riwayat
                                    </button>
                                    @if ($firstDoc)
                                        <a href="{{ route('simkerma.index', ['menu' => 4, 'doc' => $firstDoc->doc_id]) }}" class="btn btn-outline-primary btn-sm py-0 px-2 w-100" style="font-size: 0.68rem;" title="Buka Dokumen Mitra di Menu 04">
                                            <i class="bi bi-box-arrow-up-right me-1"></i>Dokumen Menu 04
                                        </a>
                                    @else
                                        <a href="{{ route('simkerma.index', ['menu' => 4, 'view' => 'new', 'partner_name' => $partner->name]) }}" class="btn btn-outline-success btn-sm py-0 px-2 w-100" style="font-size: 0.68rem;" title="Ajukan Naskah Baru dengan Mitra Ini">
                                            <i class="bi bi-plus-lg me-1"></i>Ajukan Naskah
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr id="emptyPartnerRow">
                                <td colspan="7" class="text-center py-4 text-muted">Belum ada data mitra terdaftar.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- MODAL INPUT MANDIRI DATA MITRA BARU --}}
        <div class="modal fade" id="createPartnerModal" tabindex="-1" aria-labelledby="createPartnerModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg">
                    <form method="POST" action="{{ route('simkerma.store-partner') }}">
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
                                <div class="col-md-7">
                                    <label class="form-label fw-bold text-dark">Nama Lembaga / Instansi Mitra <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control form-control-sm" placeholder="Contoh: PT Industri Maju Bersama" required>
                                </div>
                                <div class="col-md-5">
                                    <label class="form-label fw-bold text-dark">Kategori Mitra <span class="text-danger">*</span></label>
                                    <select name="category" class="form-select form-select-sm" required>
                                        <option value="DUDI / Swasta">DUDI / Swasta (Industri)</option>
                                        <option value="Instansi Pemerintah">Instansi Pemerintah</option>
                                        <option value="BUMN / BUMD">BUMN / BUMD</option>
                                        <option value="Perguruan Tinggi">Perguruan Tinggi</option>
                                        <option value="Organisasi / Komunitas / Lainnya">Organisasi / Komunitas / Lainnya</option>
                                    </select>
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
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content border-0 shadow-lg">
                        <form method="POST" action="{{ route('simkerma.update-partner') }}">
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
                                                        <div>{{ $doc->validity }}</div>
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
                                <button type="button" class="btn btn-warning btn-sm fw-semibold text-dark" data-bs-dismiss="modal" data-bs-toggle="modal" data-bs-target="#editPartnerModal_{{ $partner->id }}">
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
        }

        function resetPartnerFilters() {
            document.getElementById('partnerSearchInput').value = '';
            document.getElementById('partnerCategoryFilter').value = '';
            document.getElementById('partnerStatusFilter').value = '';
            filterPartnersTable();
        }
        </script>
        @endpush

    {{-- ===================================================================================== --}}
    {{-- 3. PENGELOLAAN DOKUMEN KERJA SAMA (REPOSITORI ARSIP DINAMIS DARI MYSQL)               --}}
    {{-- ===================================================================================== --}}
    @elseif ($activeMenu === 3)
        <div class="card card-wireframe shadow-sm border">
            <div class="card-header bg-white py-3 border-bottom d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2">
                <div>
                    <div class="d-flex align-items-center gap-2">
                        <h6 class="mb-0 fw-bold text-uppercase">
                            <i class="bi bi-file-earmark-text text-primary me-1"></i> Repositori Dokumen Resmi Kerja Sama
                        </h6>
                        <span class="badge bg-primary text-white font-monospace" style="font-size: 0.65rem;">{{ $documents->count() }} NASKAH TERDAFTAR</span>
                    </div>
                    <small class="text-muted">Penyimpanan Terorganisasi Naskah MoU, MoA/PKS, IA, Proposal, Laporan &amp; Riwayat Versi FSM</small>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('simkerma.index', ['menu' => 4, 'view' => 'new']) }}" class="btn btn-dark btn-sm fw-semibold shadow-sm">
                        <i class="bi bi-cloud-upload me-1 text-warning"></i> Unggah / Ajukan Naskah Baru
                    </a>
                </div>
            </div>

            <!-- TAB FILTER KATEGORI DOKUMEN SISI KLIEN -->
            <div class="px-3 pt-2 bg-light border-bottom">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                    <ul class="nav nav-tabs border-0 small fw-semibold" id="repoTabs">
                        <li class="nav-item">
                            <button class="nav-link active bg-white text-dark py-2 px-3 repo-tab-btn" data-type="ALL">Semua Naskah ({{ $documents->count() }})</button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link text-muted py-2 px-3 repo-tab-btn" data-type="MoU">MoU (Nota Kesepahaman)</button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link text-muted py-2 px-3 repo-tab-btn" data-type="MoA">PKS / MoA (Fakultas)</button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link text-muted py-2 px-3 repo-tab-btn" data-type="IA">IA (Implementasi Prodi)</button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link text-muted py-2 px-3 repo-tab-btn" data-type="Paket">Paket Terpadu (PKM)</button>
                        </li>
                    </ul>
                    <div class="pb-2">
                        <input type="text" id="repoSearchInput" class="form-control form-control-sm" placeholder="Cari no surat, mitra, judul..." style="max-width: 260px;">
                    </div>
                </div>
            </div>

            <!-- TABEL REPOSITORI DOKUMEN DINAMIS -->
            <div class="table-responsive">
                <table class="table table-wireframe table-hover align-middle mb-0 small" id="repoTable">
                    <thead class="bg-light">
                        <tr>
                            <th style="min-width: 170px;">Nomor Dokumen &amp; Jenis Naskah</th>
                            <th style="min-width: 180px;">Judul Kerja Sama &amp; Mitra</th>
                            <th style="min-width: 130px;">Tanggal TTD &amp; Masa Berlaku</th>
                            <th style="min-width: 130px;">Pihak Penandatangan</th>
                            <th style="min-width: 130px;">Berkas &amp; Riwayat Audit</th>
                            <th style="min-width: 110px;" class="text-center">Aksi Dokumen</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($documents as $doc)
                            @php
                                $statusBadgeClass = match($doc->status) {
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
                            <tr class="repo-row" data-type="{{ $doc->doc_type }}">
                                <td>
                                    <div class="fw-bold text-dark">
                                        {{ $doc->doc_number ?: $doc->doc_id }}
                                        <span class="badge bg-light text-dark border" style="font-size: 0.65rem;">Pihak I</span>
                                    </div>
                                    @if (!empty($doc->partner_doc_number))
                                        <div class="small text-secondary" style="font-size: 0.72rem;">
                                            Pihak II: <strong>{{ $doc->partner_doc_number }}</strong>
                                        </div>
                                    @endif
                                    <div class="mt-1 d-flex align-items-center gap-1">
                                        <span class="badge bg-dark" style="font-size: 0.65rem;">{{ $doc->doc_type }}</span>
                                        <span class="badge {{ $statusBadgeClass }}" style="font-size: 0.65rem;">{{ $doc->status }}</span>
                                    </div>
                                </td>
                                <td>
                                    <strong class="text-dark">{{ Str::limit($doc->scope, 85) }}</strong>
                                    <div class="text-muted small mt-1">
                                        <i class="bi bi-building me-1"></i>Mitra: <strong>{{ $doc->partner_name }}</strong>
                                        <span class="text-secondary">({{ $doc->partner_category }})</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="text-dark">
                                        TTD: {{ $doc->date_signed ? $doc->date_signed->format('d M Y') : ($doc->date_approved ? $doc->date_approved->format('d M Y') : 'Draf Formal') }}
                                    </div>
                                    <div class="text-muted small">Durasi: {{ $doc->validity ?: '1 Tahun' }}</div>
                                </td>
                                <td>
                                    <div class="text-dark fw-semibold">Rektor UNIPAR / Pimpinan</div>
                                    <div class="text-muted small">&amp; Pimpinan {{ $doc->partner_name }}</div>
                                </td>
                                <td>
                                    <div class="mb-1">
                                        <i class="bi bi-file-earmark-pdf text-danger me-1"></i>
                                        <span>{{ $doc->signed_file ?: ($doc->draft_file ?: 'naskah_kerjasama.pdf') }}</span>
                                    </div>
                                    <small class="badge bg-light text-secondary border">
                                        <i class="bi bi-clock-history me-1"></i>{{ $doc->auditTrails->count() }} Jejak Audit Tercatat
                                    </small>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-1">
                                        <button type="button" class="btn btn-outline-dark btn-sm py-0 px-2" style="font-size: 0.68rem;" title="Perbesar QR Code" onclick="showQrModal('{{ $doc->reg_id }}', '{{ $doc->doc_number ?: $doc->doc_id }}', '{{ addslashes(str_replace(["\r", "\n"], ' ', $doc->scope)) }}', '{{ addslashes($doc->partner_name) }}', '{{ $doc->status }}', '{{ $doc->date_signed ? $doc->date_signed->format('d M Y') : date('d M Y') }}', '{{ addslashes($doc->partner_doc_number ?? '') }}')">
                                            <i class="bi bi-qr-code me-1"></i>QR
                                        </button>
                                        <a href="{{ route('simkerma.verify', ['reg_id' => $doc->reg_id]) }}" target="_blank" class="btn btn-outline-primary btn-sm py-0 px-2" style="font-size: 0.68rem;" title="Lembar Verifikasi Publik">
                                            <i class="bi bi-shield-check"></i>
                                        </a>
                                        <a href="{{ route('simkerma.index', ['menu' => 4, 'doc' => $doc->doc_id]) }}" class="btn btn-dark btn-sm py-0 px-2" style="font-size: 0.68rem;" title="Kelola Alur FSM">
                                            <i class="bi bi-sliders"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">Belum ada dokumen kemitraan terdaftar.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @push('scripts')
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tabs = document.querySelectorAll('.repo-tab-btn');
            const rows = document.querySelectorAll('.repo-row');
            const search = document.getElementById('repoSearchInput');

            function filterRepo() {
                const activeTab = document.querySelector('.repo-tab-btn.active');
                const t = activeTab ? activeTab.getAttribute('data-type') : 'ALL';
                const q = search ? search.value.toLowerCase().trim() : '';

                rows.forEach(function(row) {
                    const rowType = row.getAttribute('data-type') || '';
                    const matchType = (t === 'ALL' || rowType.includes(t));
                    const matchSearch = (!q || row.innerText.toLowerCase().includes(q));

                    row.style.display = (matchType && matchSearch) ? '' : 'none';
                });
            }

            tabs.forEach(function(btn) {
                btn.addEventListener('click', function() {
                    tabs.forEach(b => {
                        b.classList.remove('active', 'bg-white', 'text-dark');
                        b.classList.add('text-muted');
                    });
                    this.classList.remove('text-muted');
                    this.classList.add('active', 'bg-white', 'text-dark');
                    filterRepo();
                });
            });

            if (search) {
                search.addEventListener('input', filterRepo);
            }
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
        {{-- 1. FORMULIR PENGAJUAN MOU BARU (DEDICATED UNTUK DOKUMEN MOU SAJA)                     --}}
        {{-- ===================================================================================== --}}
        @if ($isCreatingNew)

            <div class="card card-wireframe mb-4 shadow-sm border">
                <div class="card-header bg-white py-3 border-bottom d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
                    <div>
                        <h5 class="card-title mb-0 fw-bold text-dark">
                            <i class="bi bi-file-earmark-plus text-primary me-2"></i> Pengajuan MoU (Nota Kesepahaman Baru)
                        </h5>
                        <div class="small text-muted">
                            Pengajuan naskah Nota Kesepahaman (MoU) induk kemitraan institusi diterbitkan dengan status awal <strong>DRAFT</strong> untuk penelaahan internal naskah.
                        </div>
                    </div>
                    <a href="{{ route('simkerma.index', ['menu' => 4]) }}" class="btn btn-outline-secondary btn-sm fw-semibold">
                        <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar Pengajuan
                    </a>
                </div>
                <div class="card-body p-4">

                    <form method="POST" action="{{ route('simkerma.store-jalur1') }}" enctype="multipart/form-data" class="small">
                        @csrf
                        <input type="hidden" name="doc_type" value="MoU">
                        <input type="hidden" name="parent_mou" value="-">

                        <!-- 1. JUDUL / TOPIK KERJA SAMA MOU -->
                        <div class="row mb-3 align-items-center">
                            <label class="col-md-3 col-form-label fw-bold text-dark">
                                Judul / Topik Kerja Sama MoU : <span class="text-danger">*</span>
                            </label>
                            <div class="col-md-9">
                                <input type="text" name="title" class="form-control" required>
                            </div>
                        </div>

                        <!-- 2. IDENTITAS PIHAK PERTAMA (UNIPAR) -->
                        <div class="row mb-3 align-items-center">
                            <label class="col-md-3 col-form-label fw-bold text-dark">
                                Unit Pengusul (Pihak I) : <span class="text-danger">*</span>
                            </label>
                            <div class="col-md-9">
                                <select name="unit" class="form-select" required>
                                    <option>Program Studi Teknik Informatika</option>
                                    <option>Fakultas Ilmu Komputer</option>
                                    <option>Fakultas Ekonomi dan Bisnis</option>
                                    <option>LPPM Universitas Parna Raya</option>
                                    <option>Biro Administrasi Akademik &amp; Kemahasiswaan</option>
                                    <option>Rektorat Universitas Parna Raya</option>
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3 align-items-center">
                            <label class="col-md-3 col-form-label fw-bold text-dark">
                                Penandatangan Pihak I :
                            </label>
                            <div class="col-md-9">
                                <input type="text" name="first_party_signer" class="form-control">
                            </div>
                        </div>

                        <!-- 3. IDENTITAS PIHAK KEDUA (MITRA) -->
                        <div class="row mb-3 align-items-center">
                            <label class="col-md-3 col-form-label fw-bold text-dark">
                                Nama Mitra (Pihak II) : <span class="text-danger">*</span>
                            </label>
                            <div class="col-md-9">
                                <input type="text" name="partner_name" class="form-control" list="partnerDatalist" required>
                                <datalist id="partnerDatalist">
                                    @foreach ($partners as $p)
                                        <option value="{{ $p->name }}">{{ $p->category }} &bull; {{ $p->city ?: 'Manado' }}</option>
                                    @endforeach
                                </datalist>
                            </div>
                        </div>

                        <div class="row mb-3 align-items-center">
                            <label class="col-md-3 col-form-label fw-bold text-dark">
                                Kategori / Sektor Mitra : <span class="text-danger">*</span>
                            </label>
                            <div class="col-md-9">
                                <select name="category" class="form-select" required>
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

                        <div class="row mb-3 align-items-center">
                            <label class="col-md-3 col-form-label fw-bold text-dark">
                                Penandatangan Pihak II (Mitra) :
                            </label>
                            <div class="col-md-9">
                                <input type="text" name="second_party_signer" class="form-control">
                            </div>
                        </div>

                        <div class="row mb-3 align-items-center">
                            <label class="col-md-3 col-form-label fw-bold text-dark">
                                Nomor Naskah Mitra :
                                <span class="badge bg-light text-secondary border fw-normal ms-1">Opsional</span>
                            </label>
                            <div class="col-md-9">
                                <input type="text" name="partner_doc_number" class="form-control">
                                <div class="form-text text-muted">
                                    Opsional pada fase inisiasi DRAFT/SUBMIT. Wajib dilengkapi saat penandatanganan fisik naskah (ACTIVE).
                                </div>
                            </div>
                        </div>

                        <!-- 4. UPLOAD DRAFT DOKUMEN MOU (PDF) -->
                        <div class="row mb-3 align-items-center">
                            <label class="col-md-3 col-form-label fw-bold text-dark">
                                Upload Draf Naskah MoU (PDF) : <span class="text-danger">*</span>
                            </label>
                            <div class="col-md-9">
                                <input type="file" name="draft_file" class="form-control" accept=".pdf" required>
                                <div class="form-text text-muted">Format berkas: <strong>PDF (.pdf)</strong>. Berkas draf awal untuk ditelaah dan diverifikasi oleh Bagian Kerja Sama/Legal.</div>
                            </div>
                        </div>

                        <!-- 5. PERIODE BERLAKU (NORMALISASI: MULAI & MASA BERLAKU TAHUN -> BERAKHIR COMPUTED) -->
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
                                <div class="form-text text-muted">Masa berlaku standar naskah MoU perguruan tinggi berkisar antara 3 hingga 5 tahun.</div>
                            </div>
                        </div>

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

                        <!-- 6. NARAHUBUNG (PIC) KEDUA PIHAK -->
                        <div class="row mb-3 align-items-center">
                            <label class="col-md-3 col-form-label fw-bold text-dark">
                                PIC Pihak Pertama (UNIPAR) :
                            </label>
                            <div class="col-md-9">
                                <input type="text" name="pic_first_party" class="form-control">
                            </div>
                        </div>

                        <div class="row mb-3 align-items-center">
                            <label class="col-md-3 col-form-label fw-bold text-dark">
                                PIC Pihak Kedua (Mitra) :
                            </label>
                            <div class="col-md-9">
                                <input type="text" name="pic_second_party" class="form-control">
                            </div>
                        </div>

                        <!-- 7. DESKRIPSI / URGENSI KERJA SAMA -->
                        <div class="row mb-4 align-items-start">
                            <label class="col-md-3 col-form-label fw-bold text-dark pt-1">
                                Deskripsi / Urgensi Kerja Sama :
                            </label>
                            <div class="col-md-9">
                                <textarea name="description" class="form-control" rows="3"></textarea>
                            </div>
                        </div>

                        <!-- TOMBOL AKSI SIMPAN -->
                        <div class="row pt-2">
                            <div class="col-md-9 offset-md-3">
                                <button type="submit" class="btn btn-primary fw-bold px-4 py-2 shadow-sm">
                                    <i class="bi bi-save me-1"></i> Simpan Sebagai DRAF MoU
                                </button>
                                <a href="{{ route('simkerma.index', ['menu' => 4]) }}" class="btn btn-outline-secondary ms-2 px-3 py-2">Batal</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <script>
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

                    <form method="POST" action="{{ route('simkerma.store-jalur1') }}" enctype="multipart/form-data" class="small">
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
                                    <option>Program Studi Teknik Informatika</option>
                                    <option>Fakultas Ilmu Komputer</option>
                                    <option>Fakultas Ekonomi dan Bisnis</option>
                                    <option>LPPM Universitas Parna Raya</option>
                                    <option>Biro Administrasi Akademik &amp; Kemahasiswaan</option>
                                    <option>Rektorat Universitas Parna Raya</option>
                                </select>
                            </div>
                        </div>

                        <!-- 4. MITRA (PREFILLED DARI INDUK) -->
                        <div class="row mb-3 align-items-center">
                            <label class="col-md-3 col-form-label fw-bold text-dark">
                                Nama Mitra (Pihak II) : <span class="text-danger">*</span>
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

                    <form method="POST" action="{{ route('simkerma.store-legacy') }}" enctype="multipart/form-data" class="small">
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

                        <!-- 3. NOMOR NASKAH PIHAK I (UNIPAR) & PIHAK II (MITRA) -->
                        <div class="row mb-3 align-items-center">
                            <label class="col-md-3 col-form-label fw-bold text-dark">
                                Nomor Naskah UNIPAR : <span class="text-danger">*</span>
                            </label>
                            <div class="col-md-4">
                                <input type="text" name="doc_number" class="form-control font-monospace" required>
                                <div class="text-muted" style="font-size: 0.72rem;">Nomor resmi yang tercantum pada naskah fisik institusi.</div>
                            </div>
                            <label class="col-md-2 col-form-label fw-bold text-dark text-md-end">
                                Nomor Naskah Mitra :
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
                                    <option>Fakultas Ilmu Komputer</option>
                                    <option>Fakultas Ekonomi dan Bisnis</option>
                                    <option>LPPM Universitas Parna Raya</option>
                                    <option>Biro Administrasi Akademik &amp; Kemahasiswaan</option>
                                    <option>Rektorat Universitas Parna Raya</option>
                                </select>
                            </div>
                            <label class="col-md-2 col-form-label fw-bold text-dark text-md-end">
                                Kategori Mitra : <span class="text-danger">*</span>
                            </label>
                            <div class="col-md-3">
                                <select name="category" class="form-select" required>
                                    <option>DUDI / Swasta</option>
                                    <option>Instansi Pemerintah</option>
                                    <option>BUMN / BUMD</option>
                                    <option>Perguruan Tinggi Dalam Negeri</option>
                                    <option>Perguruan Tinggi Luar Negeri</option>
                                    <option>Organisasi Non-Profit / NGO</option>
                                </select>
                            </div>
                        </div>

                        <!-- 6. NAMA MITRA -->
                        <div class="row mb-3 align-items-center">
                            <label class="col-md-3 col-form-label fw-bold text-dark">
                                Nama Lembaga Mitra : <span class="text-danger">*</span>
                            </label>
                            <div class="col-md-9">
                                <input type="text" name="partner_name" class="form-control" required>
                            </div>
                        </div>

                        <!-- 7. PENANDATANGAN PIHAK I & PIHAK II -->
                        <div class="row mb-3 align-items-center">
                            <label class="col-md-3 col-form-label fw-bold text-dark">
                                Penandatangan Pihak I : <span class="text-danger">*</span>
                            </label>
                            <div class="col-md-4">
                                <input type="text" name="first_party_signer" class="form-control" required>
                            </div>
                            <label class="col-md-2 col-form-label fw-bold text-dark text-md-end">
                                Penandatangan Pihak II : <span class="text-danger">*</span>
                            </label>
                            <div class="col-md-3">
                                <input type="text" name="second_party_signer" class="form-control" required>
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
                        <li class="breadcrumb-item active text-dark fw-medium" aria-current="page">Detail Dokumen</li>
                    </ol>
                </nav>

                <!-- HEADER BAR (TITLE & ACTION BUTTONS) -->
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div class="d-flex align-items-center gap-2">
                        <a href="{{ route('simkerma.index', ['menu' => 4]) }}" class="btn btn-outline-secondary btn-sm bg-white rounded-3 d-flex align-items-center justify-content-center shadow-2xs" style="width: 32px; height: 32px; border-color: #cbd5e1;" title="Kembali ke Daftar Pengajuan">
                            <i class="bi bi-arrow-left text-dark"></i>
                        </a>
                        <h5 class="fw-bold mb-0 text-dark" style="font-size: 1.15rem;">Detail Dokumen Kerja Sama</h5>
                    </div>

                    <div class="d-flex align-items-center gap-2">
                        @if ($currentDoc->status === 'ACTIVE' || $currentDoc->status === 'WARNING' || $currentDoc->status === 'EXPIRED')
                            <form method="POST" action="{{ route('simkerma.renew-draft') }}" class="d-inline" onsubmit="return confirm('Inisiasi pembaruan/reaktivasi dokumen {{ $currentDoc->doc_id }}?\n\nDokumen akan kembali ke status DRAFT untuk penyusunan dan penelaahan ketentuan naskah.');">
                                @csrf
                                <input type="hidden" name="doc_id" value="{{ $currentDoc->doc_id }}">
                                <button type="submit" class="btn btn-warning btn-sm fw-semibold px-2.5 py-1.5 shadow-2xs d-flex align-items-center gap-1.5 text-dark" style="font-size: 0.8rem;">
                                    <i class="bi bi-arrow-repeat"></i> Perpanjang / Reaktivasi ke Draf
                                </button>
                            </form>
                        @endif

                        <!-- Dropdown Tambah Dokumen Turunan -->
                        <div class="dropdown">
                            <button class="btn btn-primary btn-sm fw-semibold px-3 py-1.5 shadow-sm dropdown-toggle d-flex align-items-center gap-1.5" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="font-size: 0.8rem; background-color: #3b82f6; border-color: #3b82f6;">
                                <i class="bi bi-plus-lg"></i> Tambah Dokumen Turunan
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow border-0 small py-1" style="min-width: 220px;">
                                <li>
                                    <a class="dropdown-item py-1.5 px-3 d-flex align-items-center gap-2" href="{{ route('simkerma.index', ['menu' => 4, 'view' => 'new_derive', 'parent_id' => $currentDoc->doc_id, 'derive_type' => 'MoA / PKS']) }}">
                                        <i class="bi bi-file-earmark-text text-primary fs-6"></i>
                                        <div>
                                            <div class="fw-bold">MoA / PKS</div>
                                            <div class="text-muted" style="font-size: 0.7rem;">Perjanjian Kerja Sama Tingkat Fakultas</div>
                                        </div>
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item py-1.5 px-3 d-flex align-items-center gap-2" href="{{ route('simkerma.index', ['menu' => 4, 'view' => 'new_derive', 'parent_id' => $currentDoc->doc_id, 'derive_type' => 'IA']) }}">
                                        <i class="bi bi-file-earmark-check text-success fs-6"></i>
                                        <div>
                                            <div class="fw-bold">IA</div>
                                            <div class="text-muted" style="font-size: 0.7rem;">Implementation Arrangement Tingkat Prodi</div>
                                        </div>
                                    </a>
                                </li>
                            </ul>
                        </div>

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
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- SUMMARY BANNER CARD (PERSIS UI/detail_MoU.png) -->
                <div class="card border rounded-3 bg-white mb-2 shadow-2xs">
                    <div class="card-body p-2 px-3">
                        <div class="d-flex align-items-center justify-content-between gap-3">
                            <!-- Left Group: Icon + Title info -->
                            <div class="d-flex align-items-center gap-3 text-truncate">
                                <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 44px; height: 44px; background-color: #eff6ff; border: 1px solid #dbeafe;">
                                    <i class="bi bi-file-earmark-text text-primary fs-3"></i>
                                </div>
                                <div class="text-truncate">
                                    <div class="d-flex align-items-center gap-2 mb-0.5">
                                        <span class="badge rounded-pill text-white fw-bold px-2 py-0.5" style="background-color: #1e3a8a; font-size: 0.68rem;">{{ $currentDoc->doc_type }}</span>
                                        <span class="text-muted" style="font-size: 0.76rem;">{{ $currentDoc->doc_type === 'MoU' ? 'Memorandum of Understanding' : ($currentDoc->doc_type === 'IA' ? 'Implementation Arrangement' : 'Memorandum of Agreement') }}</span>
                                        @if ($currentDoc->is_legacy)
                                            <span class="badge rounded-pill bg-dark text-warning border border-warning px-2 py-0.5" style="font-size: 0.68rem;">
                                                <i class="bi bi-clock-history me-1"></i> Data Lampau
                                            </span>
                                        @endif
                                    </div>
                                    <h5 class="fw-bold text-dark mb-0 text-truncate" style="letter-spacing: -0.3px; font-size: 1.15rem;">
                                        {{ $currentDoc->doc_number ?: $currentDoc->doc_id }}
                                    </h5>
                                    <div class="text-secondary text-truncate" style="font-size: 0.78rem;">
                                        <span>Universitas Parna Raya Manado</span>
                                        <span class="text-muted mx-1">&times;</span>
                                        <span class="text-dark fw-medium">{{ $currentDoc->partner_name }}</span>
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

                                <!-- KARTU 1: INFORMASI UMUM -->
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
                                                        <td class="text-muted py-0.5 ps-0 pe-2 text-nowrap" style="width: 150px;">Jenis Dokumen</td>
                                                        <td class="py-0.5 px-1 text-muted" style="width: 10px;">:</td>
                                                        <td class="text-dark fw-medium py-0.5">{{ $currentDoc->doc_type }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-muted py-0.5 ps-0 pe-2 text-nowrap">Mitra Kerja Sama</td>
                                                        <td class="py-0.5 px-1 text-muted">:</td>
                                                        <td class="text-dark fw-medium py-0.5">{{ $currentDoc->partner_name }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-muted py-0.5 ps-0 pe-2 text-nowrap">Unit Pengusul</td>
                                                        <td class="py-0.5 px-1 text-muted">:</td>
                                                        <td class="text-dark fw-medium py-0.5">{{ $currentDoc->unit }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-muted py-0.5 ps-0 pe-2 text-nowrap">Ruang Lingkup</td>
                                                        <td class="py-0.5 px-1 text-muted">:</td>
                                                        <td class="text-dark fw-medium py-0.5 text-truncate" style="max-width: 480px;">{{ $currentDoc->scope ?: $currentDoc->title }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-muted py-0.5 ps-0 pe-2 text-nowrap">Masa Berlaku</td>
                                                        <td class="py-0.5 px-1 text-muted">:</td>
                                                        <td class="text-dark fw-medium py-0.5">
                                                            @if (!empty($currentDoc->start_date) && !empty($currentDoc->end_date))
                                                                {{ \Carbon\Carbon::parse($currentDoc->start_date)->translatedFormat('d F Y') }} &ndash; {{ \Carbon\Carbon::parse($currentDoc->end_date)->translatedFormat('d F Y') }} ({{ trim(str_ireplace("Tahun", "", $currentDoc->validity ?: "1")) }} Tahun)
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
                                                                <i class="bi bi-bell-fill me-1"></i> H-90 Hari (3 Bulan)
                                                            </span>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <!-- KARTU 2: ALUR PROSES (FINITE STATE MACHINE) -->
                                @php
                                    // Lookup Jejak Audit untuk masing-masing fase alur
                                    $auditDraft = $currentDoc->auditTrails->firstWhere('event', 'CREATE_DRAFT') 
                                        ?? $currentDoc->auditTrails->sortBy('id')->first();
                                    $auditSubmit = $currentDoc->auditTrails->firstWhere('event', 'SUBMIT');
                                    $auditVerify = $currentDoc->auditTrails->firstWhere('event', 'VERIFY');
                                    $auditActive = $currentDoc->auditTrails->firstWhere('event', 'ACTIVATE');
                                    $auditExpire = $currentDoc->auditTrails->firstWhere('event', 'EXPIRE');

                                    // Tanggal masing-masing node FSM
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

                                    $isNode5Active = ($docStatus === 'ACTIVE');
                                    $isNode5Warning = ($docStatus === 'WARNING');
                                    $isNode5Done = in_array($docStatus, ['ACTIVE', 'WARNING', 'EXPIRED']);
                                    $dateNode5 = $isNode5Done 
                                        ? ($currentDoc->start_date ? \Carbon\Carbon::parse($currentDoc->start_date)->format('d M Y') : ($currentDoc->date_signed ? \Carbon\Carbon::parse($currentDoc->date_signed)->format('d M Y') : ''))
                                        : '';

                                    $isNode6Active = ($docStatus === 'EXPIRED');
                                    $dateNode6 = $isNode6Active 
                                        ? ($currentDoc->end_date ? \Carbon\Carbon::parse($currentDoc->end_date)->format('d M Y') : '')
                                        : '';

                                    // Lebar garis progres FSM horizontal
                                    $progressWidth = match($docStatus) {
                                        'DRAFT' => '0%',
                                        'REVIEW' => '40%',
                                        'VERIFIED' => '60%',
                                        'ACTIVE', 'WARNING' => '80%',
                                        'EXPIRED' => '100%',
                                        'CANCELLED' => '0%',
                                        default => '0%'
                                    };
                                    $progressColor = ($docStatus === 'EXPIRED') ? '#ef4444' : '#22c55e';

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
                                            <i class="bi bi-layers text-primary"></i> Alur Proses (Finite State Machine)
                                        </div>
                                        <button type="button" class="btn btn-outline-secondary btn-sm py-0.5 px-2 fw-semibold d-flex align-items-center gap-1" style="font-size: 0.72rem; border-color: #cbd5e1;" data-bs-toggle="modal" data-bs-target="#fsmModal_{{ $currentDoc->id }}">
                                            <i class="bi bi-diagram-3"></i> Lihat Detail FSM
                                        </button>
                                    </div>
                                    <div class="card-body py-2 px-3">
                                        <!-- HORIZONTAL STEPPER PERSIS MOCKUP detail_MoU.png -->
                                        <div class="stepper-wrap position-relative py-1 px-2 mb-2">
                                            <!-- Connecting line background & active line -->
                                            <div class="position-absolute" style="top: 15px; left: 45px; right: 45px; height: 3px; background-color: #e2e8f0; z-index: 1;">
                                                <div style="width: {{ $progressWidth }}; height: 100%; background-color: {{ $progressColor }}; transition: width 0.3s ease;"></div>
                                            </div>

                                            <div class="d-flex justify-content-between position-relative" style="z-index: 2;">
                                                <!-- Node 1: Draft -->
                                                <div class="text-center" style="width: 75px;">
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
                                                <div class="text-center" style="width: 85px;">
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
                                                <div class="text-center" style="width: 80px;">
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
                                                <div class="text-center" style="width: 100px;">
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
                                                <div class="text-center" style="width: 75px;">
                                                    @if ($isNode5Active)
                                                        <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto text-white shadow-2xs" style="width: 24px; height: 24px; font-size: 0.7rem; background-color: #22c55e; border: 2px solid #bbf7d0; box-shadow: 0 0 0 3px #dcfce7;">
                                                            <i class="bi bi-calendar-check"></i>
                                                        </div>
                                                        <div class="fw-bold text-success mt-1" style="font-size: 0.72rem;">Active</div>
                                                        @if ($dateNode5)
                                                            <div class="text-success fw-medium" style="font-size: 0.65rem;">{{ $dateNode5 }}</div>
                                                        @endif
                                                    @elseif ($isNode5Warning)
                                                        <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto text-white shadow-2xs" style="width: 24px; height: 24px; font-size: 0.7rem; background-color: #f59e0b; border: 2px solid #fde68a; box-shadow: 0 0 0 3px #fef3c7;">
                                                            <i class="bi bi-bell-fill"></i>
                                                        </div>
                                                        <div class="fw-bold text-warning mt-1" style="font-size: 0.72rem;">Warning</div>
                                                        @if ($dateNode5)
                                                            <div class="text-warning fw-medium" style="font-size: 0.65rem;">{{ $dateNode5 }}</div>
                                                        @endif
                                                    @elseif ($isNode5Done)
                                                        <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto text-white shadow-2xs" style="width: 24px; height: 24px; font-size: 0.7rem; background-color: #22c55e;">
                                                            <i class="bi bi-check"></i>
                                                        </div>
                                                        <div class="fw-semibold text-dark mt-1" style="font-size: 0.72rem;">Active</div>
                                                        @if ($dateNode5)
                                                            <div class="text-muted" style="font-size: 0.65rem;">{{ $dateNode5 }}</div>
                                                        @endif
                                                    @else
                                                        <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto bg-white border shadow-2xs" style="width: 24px; height: 24px; font-size: 0.7rem; border-color: #cbd5e1 !important; color: #94a3b8 !important;">
                                                            <i class="bi bi-calendar"></i>
                                                        </div>
                                                        <div class="text-muted mt-1" style="font-size: 0.72rem;">Active</div>
                                                    @endif
                                                </div>

                                                <!-- Node 6: Selesai -->
                                                <div class="text-center" style="width: 75px;">
                                                    @if ($isNode6Active)
                                                        <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto text-white shadow-2xs" style="width: 24px; height: 24px; font-size: 0.7rem; background-color: #ef4444; border: 2px solid #fecaca; box-shadow: 0 0 0 3px #fee2e2;">
                                                            <i class="bi bi-clock-history"></i>
                                                        </div>
                                                        <div class="fw-bold text-danger mt-1" style="font-size: 0.72rem;">Selesai</div>
                                                        @if ($dateNode6)
                                                            <div class="text-danger fw-medium" style="font-size: 0.65rem;">{{ $dateNode6 }}</div>
                                                        @endif
                                                    @else
                                                        <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto bg-white border shadow-2xs" style="width: 24px; height: 24px; font-size: 0.7rem; border-color: #cbd5e1 !important; color: #94a3b8 !important;">
                                                            <i class="bi bi-circle"></i>
                                                        </div>
                                                        <div class="text-muted mt-1" style="font-size: 0.72rem;">Selesai</div>
                                                    @endif
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
                                        <div class="table-responsive border-0 shadow-none mb-0">
                                            <table class="table table-hover align-middle mb-0" style="font-size: 0.76rem;">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th class="ps-3 py-1 text-muted fw-semibold" style="width: 40px;">No.</th>
                                                        <th class="py-1 text-muted fw-semibold">Nomor Dokumen</th>
                                                        <th class="py-1 text-muted fw-semibold">Jenis</th>
                                                        <th class="py-1 text-muted fw-semibold">Unit Pelaksana</th>
                                                        <th class="py-1 text-muted fw-semibold">Status</th>
                                                        <th class="pe-3 py-1 text-end text-muted fw-semibold" style="width: 80px;">Aksi</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse ($childDocs as $cIdx => $cDoc)
                                                        <tr>
                                                            <td class="ps-3 py-1 text-muted">{{ $cIdx + 1 }}</td>
                                                            <td class="py-1 fw-bold text-dark">{{ $cDoc->doc_number ?: $cDoc->doc_id }}</td>
                                                            <td class="py-1">
                                                                @if (stripos($cDoc->doc_type, 'MoA') !== false)
                                                                    <span class="badge rounded-pill fw-semibold px-2 py-0.5" style="background-color: #e0f2fe; color: #0284c7; font-size: 0.7rem;">MoA / PKS</span>
                                                                @else
                                                                    <span class="badge rounded-pill fw-semibold px-2 py-0.5" style="background-color: #dcfce7; color: #16a34a; font-size: 0.7rem;">IA</span>
                                                                @endif
                                                            </td>
                                                            <td class="py-1 text-secondary">{{ $cDoc->unit }}</td>
                                                            <td class="py-1">
                                                                @if ($cDoc->status === 'ACTIVE')
                                                                    <span class="badge rounded-pill fw-semibold px-2 py-0.5" style="background-color: #dcfce7; color: #16a34a; font-size: 0.7rem;"><i class="bi bi-check me-0.5"></i> ACTIVE</span>
                                                                @else
                                                                    <span class="badge rounded-pill fw-semibold px-2 py-0.5" style="background-color: #e0f2fe; color: #0284c7; font-size: 0.7rem;"><i class="bi bi-clock me-0.5"></i> {{ $cDoc->status }}</span>
                                                                @endif
                                                            </td>
                                                            <td class="pe-3 py-1 text-end">
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
                                    </div>
                                </div>

                            </div>

                            <!-- KOLOM KANAN (4-5 COLS) -->
                            <div class="col-lg-4">

                                <!-- KARTU 1: DOKUMEN & LAMPIRAN -->
                                <div class="card border rounded-3 bg-white mb-2 shadow-2xs">
                                    <div class="card-header bg-white py-1.5 px-3 border-bottom d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center gap-2 fw-bold text-dark" style="font-size: 0.85rem;">
                                            <i class="bi bi-paperclip text-primary"></i> Dokumen &amp; Lampiran
                                        </div>
                                    </div>
                                    <div class="card-body py-2 px-3">
                                        <!-- 1. Draf Naskah PDF -->
                                        <div class="d-flex align-items-center justify-content-between py-1 px-2 border rounded-2 mb-1.5 bg-light" style="font-size: 0.76rem;">
                                            <div class="d-flex align-items-center gap-2 text-truncate me-2">
                                                <div class="rounded-2 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 28px; height: 28px; background-color: #fee2e2; color: #dc2626;">
                                                    <i class="bi bi-file-earmark-pdf-fill"></i>
                                                </div>
                                                <div class="text-truncate">
                                                    <div class="fw-bold text-dark text-truncate" style="font-size: 0.76rem;">Draf MoU &ndash; {{ $currentDoc->partner_name }}.pdf</div>
                                                    <div class="text-muted" style="font-size: 0.68rem;">1,2 MB &bull; Diunggah {{ \Carbon\Carbon::parse($currentDoc->created_at)->format('d M Y') }}</div>
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-center gap-1 flex-shrink-0">
                                                <a href="{{ asset($currentDoc->draft_file ?: 'templates/Template_MoU_UNIPAR.docx') }}" target="_blank" class="btn btn-light border btn-sm py-0.5 px-1.5 shadow-2xs" title="Lihat">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <a href="{{ asset($currentDoc->draft_file ?: 'templates/Template_MoU_UNIPAR.docx') }}" download class="btn btn-light border btn-sm py-0.5 px-1.5 shadow-2xs" title="Unduh">
                                                    <i class="bi bi-download"></i>
                                                </a>
                                            </div>
                                        </div>

                                        <!-- 2. Naskah Final Bertandatangan -->
                                        <div class="d-flex align-items-center justify-content-between py-1 px-2 border rounded-2 mb-1.5 bg-light" style="font-size: 0.76rem;">
                                            <div class="d-flex align-items-center gap-2 text-truncate me-2">
                                                <div class="rounded-2 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 28px; height: 28px; background-color: #fee2e2; color: #dc2626;">
                                                    <i class="bi bi-file-earmark-pdf-fill"></i>
                                                </div>
                                                <div class="text-truncate">
                                                    <div class="fw-bold text-dark text-truncate" style="font-size: 0.76rem;">MoU Final (Bertandatangan).pdf</div>
                                                    <div class="text-muted" style="font-size: 0.68rem;">2,4 MB &bull; Diunggah {{ $currentDoc->date_signed ? \Carbon\Carbon::parse($currentDoc->date_signed)->format('d M Y') : '03 Nov 2026' }}</div>
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-center gap-1 flex-shrink-0">
                                                <a href="{{ asset($currentDoc->signed_file ?: ($currentDoc->draft_file ?: 'templates/Template_MoU_UNIPAR.docx')) }}" target="_blank" class="btn btn-light border btn-sm py-0.5 px-1.5 shadow-2xs" title="Lihat">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <a href="{{ asset($currentDoc->signed_file ?: ($currentDoc->draft_file ?: 'templates/Template_MoU_UNIPAR.docx')) }}" download class="btn btn-light border btn-sm py-0.5 px-1.5 shadow-2xs" title="Unduh">
                                                    <i class="bi bi-download"></i>
                                                </a>
                                            </div>
                                        </div>

                                        <!-- 3. Notula Pembahasan (Word) -->
                                        <div class="d-flex align-items-center justify-content-between py-1 px-2 border rounded-2 mb-0 bg-light" style="font-size: 0.76rem;">
                                            <div class="d-flex align-items-center gap-2 text-truncate me-2">
                                                <div class="rounded-2 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 28px; height: 28px; background-color: #e0f2fe; color: #0284c7;">
                                                    <i class="bi bi-file-earmark-word-fill"></i>
                                                </div>
                                                <div class="text-truncate">
                                                    <div class="fw-bold text-dark text-truncate" style="font-size: 0.76rem;">Notula Pembahasan.docx</div>
                                                    <div class="text-muted" style="font-size: 0.68rem;">412 KB &bull; Diunggah 02 Nov 2026</div>
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-center gap-1 flex-shrink-0">
                                                <a href="{{ asset('templates/Template_MoU_UNIPAR.docx') }}" target="_blank" class="btn btn-light border btn-sm py-0.5 px-1.5 shadow-2xs" title="Lihat">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <a href="{{ asset('templates/Template_MoU_UNIPAR.docx') }}" download class="btn btn-light border btn-sm py-0.5 px-1.5 shadow-2xs" title="Unduh">
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
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="fw-bold mb-0 text-dark small"><i class="bi bi-files text-primary me-1"></i> Manajemen Berkas &amp; Pratinjau Naskah</h6>
                                <button type="button" class="btn btn-primary btn-sm fw-semibold py-1 px-2.5" style="font-size: 0.75rem;" data-bs-toggle="modal" data-bs-target="#uploadDocModal_{{ $currentDoc->id }}">
                                    <i class="bi bi-upload me-1"></i> Unggah Berkas Baru
                                </button>
                            </div>
                            <div class="row g-2">
                                <div class="col-md-6">
                                    <div class="p-2 border rounded-2 bg-light">
                                        <h6 class="fw-bold text-dark mb-1 small"><i class="bi bi-file-earmark-pdf text-danger me-1"></i>Naskah Draf Pengusul</h6>
                                        @if ($currentDoc->draft_file && file_exists(public_path($currentDoc->draft_file)))
                                            <div class="ratio ratio-16x9 border bg-white mb-1.5 rounded-1 overflow-hidden" style="max-height: 250px;">
                                                <iframe src="{{ asset($currentDoc->draft_file) }}" title="Preview Draf"></iframe>
                                            </div>
                                            <div class="d-flex gap-1.5">
                                                <a href="{{ asset($currentDoc->draft_file) }}" target="_blank" class="btn btn-outline-secondary btn-sm py-0.5 px-2" style="font-size: 0.72rem;"><i class="bi bi-box-arrow-up-right me-1"></i>Buka Tab Baru</a>
                                                <a href="{{ asset($currentDoc->draft_file) }}" download class="btn btn-secondary btn-sm py-0.5 px-2" style="font-size: 0.72rem;"><i class="bi bi-download me-1"></i>Unduh PDF</a>
                                            </div>
                                        @else
                                            <div class="text-muted py-3 text-center small">Berkas naskah draf belum diunggah.</div>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="p-2 border rounded-2 bg-light">
                                        <h6 class="fw-bold text-dark mb-1 small"><i class="bi bi-file-earmark-check text-success me-1"></i>Naskah Asli / Terverifikasi QR Code</h6>
                                        @if ($currentDoc->signed_file && file_exists(public_path($currentDoc->signed_file)))
                                            <div class="ratio ratio-16x9 border bg-white mb-1.5 rounded-1 overflow-hidden" style="max-height: 250px;">
                                                <iframe src="{{ asset($currentDoc->signed_file) }}" title="Preview Naskah Asli"></iframe>
                                            </div>
                                            <div class="d-flex gap-1.5">
                                                <a href="{{ asset($currentDoc->signed_file) }}" target="_blank" class="btn btn-outline-success btn-sm py-0.5 px-2" style="font-size: 0.72rem;"><i class="bi bi-box-arrow-up-right me-1"></i>Buka Tab Baru</a>
                                                <a href="{{ asset($currentDoc->signed_file) }}" download class="btn btn-success btn-sm py-0.5 px-2" style="font-size: 0.72rem;"><i class="bi bi-download me-1"></i>Unduh Naskah Asli</a>
                                            </div>
                                        @elseif ($currentDoc->draft_file && file_exists(public_path($currentDoc->draft_file)) && in_array($currentDoc->status, ['REVIEW', 'VERIFIED', 'ACTIVE', 'WARNING', 'EXPIRED']))
                                            <div class="ratio ratio-16x9 border bg-white mb-1.5 rounded-1 overflow-hidden" style="max-height: 250px;">
                                                <iframe src="{{ asset($currentDoc->draft_file) }}" title="Preview Naskah QR Code"></iframe>
                                            </div>
                                            <div class="d-flex gap-1.5">
                                                <a href="{{ asset($currentDoc->draft_file) }}" target="_blank" class="btn btn-outline-primary btn-sm py-0.5 px-2" style="font-size: 0.72rem;"><i class="bi bi-box-arrow-up-right me-1"></i>Buka Tab Baru</a>
                                                <a href="{{ asset($currentDoc->draft_file) }}" download class="btn btn-primary btn-sm py-0.5 px-2" style="font-size: 0.72rem;"><i class="bi bi-download me-1"></i>Unduh Naskah Ber-QR</a>
                                            </div>
                                        @else
                                            <div class="text-muted py-3 text-center small">Naskah asli bertandatangan basah belum diunggah. Naskah dapat diunggah pada tahap status VERIFIED &rarr; ACTIVE.</div>
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
                                    <h6 class="fw-bold mb-0 text-dark small">Daftar Dokumen Turunan (MoA &amp; IA)</h6>
                                    <div class="text-muted" style="font-size: 0.72rem;">Naskah turunan pelaksanaan yang berinduk secara hukum ke dokumen MoU ini</div>
                                </div>
                                <div class="d-flex gap-1.5">
                                    <a href="{{ route('simkerma.index', ['menu' => 4, 'view' => 'new_derive', 'parent_id' => $currentDoc->doc_id, 'derive_type' => 'MoA / PKS']) }}" class="btn btn-primary btn-sm fw-semibold py-1 px-2.5" style="font-size: 0.75rem;">
                                        <i class="bi bi-plus-lg me-1"></i> + Tambah MoA / PKS
                                    </a>
                                    <a href="{{ route('simkerma.index', ['menu' => 4, 'view' => 'new_derive', 'parent_id' => $currentDoc->doc_id, 'derive_type' => 'IA']) }}" class="btn btn-success btn-sm fw-semibold py-1 px-2.5" style="font-size: 0.75rem;">
                                        <i class="bi bi-plus-lg me-1"></i> + Tambah IA
                                    </a>
                                </div>
                            </div>
                            @if ($childDocs->isNotEmpty())
                                <div class="table-responsive border-0">
                                    <table class="table table-bordered table-hover align-middle mb-0" style="font-size: 0.76rem;">
                                        <thead class="table-light">
                                            <tr>
                                                <th class="py-1">Jenis</th>
                                                <th class="py-1">Nomor Dokumen</th>
                                                <th class="py-1">Judul Naskah</th>
                                                <th class="py-1">Unit Pelaksana</th>
                                                <th class="py-1">Mitra</th>
                                                <th class="py-1">Status FSM</th>
                                                <th class="py-1 text-end">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($childDocs as $cDoc)
                                                <tr>
                                                    <td class="py-1">
                                                        @if (stripos($cDoc->doc_type, 'MoA') !== false)
                                                            <span class="badge rounded-pill fw-semibold px-2 py-0.5" style="background-color: #e0f2fe; color: #0284c7; font-size: 0.7rem;">MoA / PKS</span>
                                                        @else
                                                            <span class="badge rounded-pill fw-semibold px-2 py-0.5" style="background-color: #dcfce7; color: #16a34a; font-size: 0.7rem;">IA</span>
                                                        @endif
                                                    </td>
                                                    <td class="py-1 fw-bold text-dark">{{ $cDoc->doc_number ?: $cDoc->doc_id }}</td>
                                                    <td class="py-1 text-truncate" style="max-width: 250px;">{{ $cDoc->title }}</td>
                                                    <td class="py-1 text-secondary">{{ $cDoc->unit }}</td>
                                                    <td class="py-1 text-dark">{{ $cDoc->partner_name }}</td>
                                                    <td class="py-1">
                                                        <span class="badge {{ $stepperList[$cDoc->status][1] ?? 'bg-secondary' }} px-2 py-0.5" style="font-size: 0.7rem;">
                                                            {{ $cDoc->status }}
                                                        </span>
                                                    </td>
                                                    <td class="py-1 text-end">
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
                                    <div class="text-muted" style="font-size: 0.72rem;">Gunakan tombol di atas untuk menambahkan perjanjian kerja sama turunan (MoA / IA)</div>
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
                                    <div class="text-muted" style="font-size: 0.72rem;">Kegiatan riil operasional yang mengimplementasikan kerja sama ini</div>
                                </div>
                                <button type="button" class="btn btn-primary btn-sm fw-semibold py-1 px-2.5" style="font-size: 0.75rem;" data-bs-toggle="modal" data-bs-target="#createActivityModal">
                                    <i class="bi bi-plus-lg me-1"></i> Catat Kegiatan Baru
                                </button>
                            </div>
                            @if ($currentDoc->activities->isNotEmpty())
                                <div class="table-responsive border-0">
                                    <table class="table table-bordered table-hover align-middle mb-0" style="font-size: 0.76rem;">
                                        <thead class="table-light">
                                            <tr>
                                                <th class="py-1">Nama Kegiatan</th>
                                                <th class="py-1">Bentuk Implementasi</th>
                                                <th class="py-1">Tanggal</th>
                                                <th class="py-1">Unit Pelaksana</th>
                                                <th class="py-1">Realisasi Anggaran</th>
                                                <th class="py-1">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($currentDoc->activities as $act)
                                                <tr>
                                                    <td class="py-1 fw-bold text-dark">{{ $act->title }}</td>
                                                    <td class="py-1"><span class="badge bg-light text-dark border">{{ $act->type }}</span></td>
                                                    <td class="py-1">{{ \Carbon\Carbon::parse($act->activity_date)->format('d M Y') }}</td>
                                                    <td class="py-1">{{ $act->unit }}</td>
                                                    <td class="py-1 text-dark fw-semibold">Rp {{ number_format($act->budget_spent, 0, ',', '.') }}</td>
                                                    <td class="py-1"><span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-0.5">{{ $act->status }}</span></td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-4 bg-light rounded-2 border">
                                    <i class="bi bi-calendar-x text-muted fs-3"></i>
                                    <div class="fw-semibold text-dark mt-1 small">Belum Ada Catatan Pelaksanaan</div>
                                    <div class="text-muted" style="font-size: 0.72rem;">Klik tombol "Catat Kegiatan Baru" untuk merekam implementasi Tri Dharma perguruan tinggi.</div>
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
                                    <div class="text-muted" style="font-size: 0.72rem;">Catatan mutlak setiap transisi status dan aksi pejabat berwenang di Universitas Parna Raya</div>
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
            <!-- MODAL EDIT INFORMASI UMUM DOKUMEN                                                     -->
            <!-- ===================================================================================== -->
            <div class="modal fade" id="editInfoModal_{{ $currentDoc->id }}" tabindex="-1" aria-labelledby="editInfoModalLabel_{{ $currentDoc->id }}" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 shadow">
                        <div class="modal-header py-2 px-3 bg-light border-bottom">
                            <h6 class="modal-title fw-bold text-dark" id="editInfoModalLabel_{{ $currentDoc->id }}"><i class="bi bi-pencil-square text-primary me-1.5"></i> Edit Informasi Kerja Sama</h6>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form method="POST" action="{{ route('simkerma.update-document') }}">
                            @csrf
                            <input type="hidden" name="doc_id" value="{{ $currentDoc->doc_id }}">
                            <div class="modal-body p-3 small">
                                <div class="mb-2">
                                    <label class="form-label text-muted fw-semibold mb-0.5">Judul / Ruang Lingkup</label>
                                    <input type="text" name="title" class="form-control form-control-sm" value="{{ $currentDoc->title }}" required>
                                </div>
                                <div class="row g-2 mb-2">
                                    <div class="col-6">
                                        <label class="form-label text-muted fw-semibold mb-0.5">Unit Pengusul</label>
                                        <input type="text" name="unit" class="form-control form-control-sm" value="{{ $currentDoc->unit }}" required>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label text-muted fw-semibold mb-0.5">Mitra Kerja Sama</label>
                                        <input type="text" name="partner_name" class="form-control form-control-sm" value="{{ $currentDoc->partner_name }}" required>
                                    </div>
                                </div>
                                <div class="row g-2 mb-2">
                                    <div class="col-6">
                                        <label class="form-label text-muted fw-semibold mb-0.5">Tanggal Mulai</label>
                                        <input type="date" name="start_date" class="form-control form-control-sm" value="{{ $currentDoc->start_date }}">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label text-muted fw-semibold mb-0.5">Tanggal Berakhir</label>
                                        <input type="date" name="end_date" class="form-control form-control-sm" value="{{ $currentDoc->end_date }}">
                                    </div>
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
            <!-- MODAL FSM WORKFLOW ENGINE INTERAKTIF UNTUK DOKUMEN INI                                 -->
            <!-- ===================================================================================== -->
            <div class="modal fade" id="fsmModal_{{ $currentDoc->id }}" tabindex="-1" aria-labelledby="fsmModalLabel_{{ $currentDoc->id }}" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                    <div class="modal-content border-0 shadow-lg">
                        <div class="modal-header bg-dark text-white py-2 px-3">
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-warning text-dark fw-bold">{{ $currentDoc->doc_id }}</span>
                                <div>
                                    <h6 class="modal-title fw-bold mb-0" id="fsmModalLabel_{{ $currentDoc->id }}">{{ $currentDoc->partner_name }}</h6>
                                    <small class="text-white-50">{{ $currentDoc->doc_number ?: 'Registrasi: ' . $currentDoc->reg_id }} &bull; {{ $currentDoc->doc_type }}</small>
                                </div>
                            </div>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>

                        <div class="modal-body p-3">
                            <!-- Posisi Siklus Hidup FSM -->
                            <div class="p-2 bg-light rounded border mb-2.5">
                                <div class="small fw-bold text-muted mb-1"><i class="bi bi-diagram-2 me-1"></i>Posisi Siklus Hidup FSM:</div>
                                <div class="d-flex flex-wrap align-items-center gap-1.5">
                                    @foreach ($stepperList as $stKey => $stData)
                                        <span class="badge {{ $stKey === $currentDoc->status ? $stData[1] . ' shadow-sm py-1 px-2' : 'bg-light text-muted border py-1 px-2' }}" style="font-size: 0.72rem;">
                                            <i class="bi {{ $stData[0] }} me-1"></i> {{ $stKey }}
                                        </span>
                                        @if (!$loop->last)
                                            <i class="bi bi-chevron-right text-muted" style="font-size: 0.65rem;"></i>
                                        @endif
                                    @endforeach
                                </div>
                            </div>

                            <!-- Form Eksekusi Transisi FSM -->
                            <form method="POST" action="{{ route('simkerma.transition') }}" id="fsmForm_{{ $currentDoc->id }}" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="doc_id" value="{{ $currentDoc->doc_id }}">

                                <div class="row g-2 mb-2">
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold text-dark mb-0.5">Wewenang Peran Eksekutor (RBAC):</label>
                                        <select name="actor_role" class="form-select form-select-sm" required>
                                            <option value="{{ $activeRole }}" selected>{{ $activeRole }} (Peran Sesi Aktif)</option>
                                            <option value="UNIT">UNIT (Unit Kerja / Prodi Pengusul)</option>
                                            <option value="LEGAL">LEGAL (Bagian Hukum / Pemeriksa Draf)</option>
                                            <option value="LEADER">LEADER (Pimpinan: Rektor / Warek)</option>
                                            <option value="ADMIN">ADMIN (Staf Pengelola SIM-KERMA)</option>
                                            <option value="MASTER">MASTER (Hak Akses Administrator Penuh)</option>
                                            <option value="SYSTEM">SYSTEM (Cron Scheduler / Task Engine)</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold text-dark mb-0.5">Nama Pejabat / Validator:</label>
                                        <input type="text" name="actor_name" class="form-control form-control-sm" value="{{ auth()->check() ? auth()->user()->name : 'Pejabat UNIPAR' }}" required>
                                    </div>
                                </div>

                                <div class="mb-2">
                                    <label class="form-label small fw-bold text-dark mb-0.5">Pilih Aksi / Event Transisi FSM yang Sah:</label>
                                    @if (!empty($availableEvents))
                                        <div class="d-flex flex-column gap-1.5">
                                            @foreach ($availableEvents as $eKey => $eData)
                                                <label class="p-2 border rounded-2 d-flex align-items-center justify-content-between cursor-pointer hover-shadow transition-all bg-white" style="cursor: pointer;">
                                                    <div class="d-flex align-items-center gap-2">
                                                        <input class="form-check-input mt-0" type="radio" name="event" value="{{ $eKey }}" required {{ $loop->first ? 'checked' : '' }}>
                                                        <div>
                                                            <div class="fw-bold text-dark small">
                                                                <i class="bi {{ $eData['icon'] }} me-1 text-primary"></i> {{ $eData['label'] }}
                                                            </div>
                                                            <div class="text-muted" style="font-size: 0.7rem;">
                                                                Transisi status: <span class="badge bg-secondary-subtle text-secondary">{{ $currentDoc->status }}</span> &rarr; <span class="badge bg-primary-subtle text-primary fw-bold">{{ $eData['to'] }}</span> &bull; Wewenang: {{ implode(', ', $eData['roles']) }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <span class="badge bg-light text-dark border font-monospace" style="font-size: 0.7rem;">{{ $eKey }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="alert alert-secondary py-2 px-3 small mb-0">
                                            <i class="bi bi-lock-fill me-1"></i> Tidak ada transisi status lanjutan yang diizinkan dari status <strong>{{ $currentDoc->status }}</strong>.
                                        </div>
                                    @endif
                                </div>

                                <div class="mb-2">
                                    <label class="form-label small fw-bold text-dark mb-0.5">Catatan Disposisi / Hasil Telaah (Terekam di Audit Trail):</label>
                                    <textarea name="notes" rows="2" class="form-control form-control-sm" placeholder="Masukkan catatan resmi verifikasi..."></textarea>
                                </div>

                                <div class="modal-footer py-2 px-0 bg-transparent border-top mt-2 d-flex justify-content-between">
                                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
                                    @if (!empty($availableEvents))
                                        <button type="submit" class="btn btn-primary btn-sm fw-bold px-3">
                                            <i class="bi bi-play-circle-fill me-1"></i> Eksekusi Transisi FSM
                                        </button>
                                    @endif
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>


@else

            {{-- ================================================================================= --}}
            {{-- VERSI 2.0: TABEL LENGKAP SEMUA PENGAJUAN KERJA SAMA DENGAN DEDICATED QR CODE CELL --}}
            {{-- Menggantikan Dropdown Menu Sesuai Permintaan Jordan (Rinci, Menurun ke Bawah)      --}}
            {{-- ================================================================================= --}}

            <!-- HEADER TABEL & FILTER PENCARIAN CEPAT -->
            <div class="card card-wireframe mb-4">
                <div class="card-header bg-white py-3 border-bottom d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
                    <div>
                        <h6 class="mb-0 fw-bold text-uppercase">
                            <i class="bi bi-table text-primary me-1"></i> Daftar Seluruh Pengajuan &amp; Persetujuan Kerja Sama
                        </h6>
                        <small class="text-muted">
                            Menampilkan seluruh dokumen kerja sama terdaftar secara vertikal beserta status siklus hidup FSM.
                        </small>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-light text-dark border px-3 py-2 fw-semibold">
                            Total: <span id="visibleDocCount" class="text-primary fw-bold">{{ $documents->count() }}</span> Dokumen
                        </span>
                        <a href="{{ route('simkerma.index', ['menu' => 4, 'view' => 'legacy']) }}" class="btn btn-outline-dark btn-sm fw-bold shadow-sm text-nowrap">
                            <i class="bi bi-clock-history me-1"></i> + Catat Kerja Sama Lampau
                        </a>
                        <a href="{{ route('simkerma.index', ['menu' => 4, 'view' => 'new_mou']) }}" class="btn btn-primary btn-sm fw-bold shadow-sm text-nowrap">
                            <i class="bi bi-plus-circle-fill me-1"></i> + Pengajuan MoU Baru
                        </a>
                    </div>
                </div>

                <!-- FORM PENCARIAN & FILTER DINAMIS -->
                <div class="card-body bg-light border-bottom py-3">
                    <div class="row g-2">
                        <div class="col-md-5">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-white"><i class="bi bi-search text-muted"></i></span>
                                <input type="text" id="tableFilterInput" class="form-control" placeholder="Cari nomor dokumen / nama mitra / prodi unit..." onkeyup="filterDocumentsTable()">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <select id="tableStatusFilter" class="form-select form-select-sm" onchange="filterDocumentsTable()">
                                <option value="">-- Semua Status FSM (7 Status) --</option>
                                <option value="DRAFT">1. DRAFT (Konsep Awal)</option>
                                <option value="REVIEW">2. REVIEW (Pemeriksaan Legal)</option>
                                <option value="VERIFIED">3. VERIFIED (Lolos Verifikasi Legal)</option>
                                <option value="ACTIVE">4. ACTIVE (Berlaku Sah / Teken Basah)</option>
                                <option value="WARNING">5. WARNING (Peringatan Dini H-90)</option>
                                <option value="EXPIRED">6. EXPIRED (Kedaluwarsa)</option>
                                <option value="CANCELLED">7. CANCELLED (Dibatalkan)</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select id="tableTypeFilter" class="form-select form-select-sm" onchange="filterDocumentsTable()">
                                <option value="">-- Semua Jenis Dokumen --</option>
                                <option value="MoU">MoU (Nota Kesepahaman)</option>
                                <option value="MoA">MoA / PKS (Perjanjian)</option>
                                <option value="IA">IA (Implementation)</option>
                                <option value="Paket">Paket Terpadu (PKM)</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="button" class="btn btn-outline-secondary btn-sm w-100 fw-semibold" onclick="resetFilters()">
                                <i class="bi bi-arrow-counterclockwise me-1"></i> Reset Filter
                            </button>
                        </div>
                    </div>
                </div>

                <!-- TABEL DOKUMEN RINCI MENURUN KE BAWAH -->
                <div class="table-responsive">
                    <table class="table table-wireframe table-hover align-middle mb-0 small">
                        <thead>
                            <tr>
                                <th style="min-width: 150px;">Nomor Dokumen &amp; Jenis</th>
                                <th style="min-width: 150px;">Mitra Kerja Sama</th>
                                <th style="min-width: 170px;">Unit Pengusul &amp; Ruang Lingkup</th>
                                <th style="min-width: 120px;">Masa Berlaku &amp; Peringatan</th>
                                <th style="min-width: 120px;" class="text-center">Status</th>
                                <th style="min-width: 120px;" class="text-end">Aksi &amp; Disposisi</th>
                            </tr>
                        </thead>
                        <tbody id="documentsTableBody">
                            @forelse ($documents as $doc)
                                @php
                                    $stBadge = match($doc->status) {
                                        'DRAFT' => 'bg-secondary text-white',
                                        'REVIEW' => 'bg-info text-dark',
                                        'VERIFIED' => 'bg-primary text-white',
                                        'ACTIVE' => 'bg-success text-white',
                                        'WARNING' => 'bg-warning text-dark',
                                        'EXPIRED' => 'bg-danger text-white',
                                        'CANCELLED' => 'bg-dark text-white',
                                        default => 'bg-secondary text-white'
                                    };

                                    $docAvailEvents = $fullFsmMatrix[$doc->status] ?? [];
                                @endphp
                                <tr class="doc-row" data-status="{{ $doc->status }}" data-type="{{ $doc->doc_type }}">
                                    <!-- 1. NOMOR DOKUMEN & JENIS -->
                                    <td>
                                        <div class="fw-bold text-dark">
                                            {{ $doc->doc_number ?: 'Dalam Proses' }}
                                            <span class="badge bg-light text-dark border" style="font-size: 0.62rem;">Pihak I</span>
                                        </div>
                                        @if (!empty($doc->title))
                                            <div class="fw-semibold text-primary mt-1" style="font-size: 0.78rem; line-height: 1.25;">
                                                <i class="bi bi-file-earmark-text-fill me-1"></i>{{ $doc->title }}
                                            </div>
                                        @endif
                                        @if (!empty($doc->partner_doc_number))
                                            <div class="small text-secondary" style="font-size: 0.72rem;">
                                                <i class="bi bi-file-earmark-text text-primary me-1"></i>Pihak II: <strong>{{ $doc->partner_doc_number }}</strong>
                                            </div>
                                        @endif
                                        <div class="d-flex flex-wrap gap-1 my-1">
                                            <span class="badge bg-dark" style="font-size: 0.68rem;">{{ $doc->doc_type }}</span>
                                            <span class="badge bg-light text-dark border" style="font-size: 0.65rem;">{{ $doc->track }}</span>
                                            @if ($doc->is_legacy)
                                                <span class="badge bg-dark text-warning border border-warning" style="font-size: 0.65rem;">
                                                    <i class="bi bi-clock-history me-1"></i>Data Lampau
                                                </span>
                                            @endif
                                        </div>
                                        @if (!empty($doc->parent_mou) && $doc->parent_mou !== '-')
                                            <div class="small text-primary" style="font-size: 0.72rem;">
                                                <i class="bi bi-link-45deg me-1"></i>Induk: <strong>{{ $doc->parent_mou }}</strong>
                                            </div>
                                        @else
                                            <div class="small text-muted" style="font-size: 0.7rem;">
                                                <i class="bi bi-shield-check me-1"></i>Dokumen Payung Utama
                                            </div>
                                        @endif
                                    </td>

                                    <!-- 2. MITRA KERJA SAMA & KATEGORI -->
                                    <td>
                                        <strong class="text-primary d-block">{{ $doc->partner_name }}</strong>
                                        <span class="text-muted d-block" style="font-size: 0.74rem;">
                                            <i class="bi bi-building me-1"></i>{{ $doc->partner_category }}
                                        </span>
                                        @if (!empty($doc->second_party_signer))
                                            <div class="text-secondary small mt-1" style="font-size: 0.7rem;">
                                                <i class="bi bi-person me-1"></i>TTD: {{ $doc->second_party_signer }}
                                            </div>
                                        @endif
                                        @if (!empty($doc->pic_second_party))
                                            <div class="text-muted small" style="font-size: 0.68rem;">
                                                <i class="bi bi-telephone me-1"></i>PIC: {{ $doc->pic_second_party }}
                                            </div>
                                        @endif
                                    </td>

                                    <!-- 3. UNIT PENGUSUL & RUANG LINGKUP -->
                                    <td>
                                        <div class="fw-semibold text-dark">{{ $doc->unit }}</div>
                                        @if (!empty($doc->first_party_signer))
                                            <div class="text-secondary small" style="font-size: 0.7rem;">
                                                <i class="bi bi-person-badge me-1"></i>TTD: {{ $doc->first_party_signer }}
                                            </div>
                                        @endif
                                        @if (!empty($doc->pic_first_party))
                                            <div class="text-muted small" style="font-size: 0.68rem;">
                                                <i class="bi bi-telephone me-1"></i>PIC: {{ $doc->pic_first_party }}
                                            </div>
                                        @endif
                                        @if (!empty($doc->collab_unit))
                                            <div class="text-muted" style="font-size: 0.72rem;">Kolaborator: {{ $doc->collab_unit }}</div>
                                        @endif
                                        <div class="text-secondary small mt-1" style="max-width: 250px; white-space: normal;">
                                            {{ \Illuminate\Support\Str::limit($doc->scope, 85) }}
                                        </div>
                                        @if (!empty($doc->nominal_value))
                                            <div class="mt-1">
                                                <span class="badge bg-success-subtle text-success-emphasis border" style="font-size: 0.65rem;">
                                                    <i class="bi bi-cash-stack me-1"></i>{{ $doc->nominal_value }}
                                                </span>
                                            </div>
                                        @endif
                                    </td>

                                    <!-- 4. MASA BERLAKU & AMBANG BATAS DINI -->
                                    <td>
                                        <div><strong>{{ $doc->validity ?: '1 Tahun' }}</strong></div>
                                        @if (!empty($doc->start_date) && !empty($doc->end_date))
                                            <div class="text-muted" style="font-size: 0.7rem;">
                                                {{ $doc->start_date->format('d/m/Y') }} &ndash; {{ $doc->end_date->format('d/m/Y') }}
                                            </div>
                                        @elseif (!empty($doc->date_signed))
                                            <div class="text-muted" style="font-size: 0.72rem;">TTD: {{ $doc->date_signed->format('d/m/Y') }}</div>
                                        @endif
                                        <div class="mt-1">
                                            <span class="badge bg-warning-subtle text-dark border" style="font-size: 0.68rem;" title="Ambang Batas Peringatan Dini">
                                                <i class="bi bi-bell-fill text-warning me-1"></i>{{ $doc->warning_threshold ?? 'H-90 Hari' }}
                                            </span>
                                        </div>
                                    </td>

                                    <!-- 5. STATUS SIKLUS FSM & DOKUMEN PDF RESMI -->
                                    <td class="text-center p-2" style="vertical-align: middle;">
                                        @php
                                            $pdfUrl = null;
                                            $pdfTitle = '';
                                            $pdfBtnClass = '';
                                            $pdfIcon = '';

                                            if ($doc->status === 'DRAFT') {
                                                // Status DRAFT: jika ada berkas naskah draf awal
                                                if (!empty($doc->draft_file)) {
                                                    $fPath = str_starts_with($doc->draft_file, 'uploads/') ? $doc->draft_file : 'uploads/documents/' . $doc->draft_file;
                                                    if (file_exists(public_path($fPath))) {
                                                        $pdfUrl = asset($fPath);
                                                        $pdfTitle = 'Buka Naskah Draf Awal (PDF)';
                                                        $pdfBtnClass = 'btn-outline-secondary';
                                                        $pdfIcon = 'bi bi-file-earmark-pdf';
                                                    }
                                                }
                                            } elseif ($doc->status === 'REVIEW') {
                                                // Status REVIEW: dokumen PDF telaah ber-QR Code
                                                if (!empty($doc->draft_file)) {
                                                    $fPath = str_starts_with($doc->draft_file, 'uploads/') ? $doc->draft_file : 'uploads/documents/' . $doc->draft_file;
                                                    if (file_exists(public_path($fPath))) {
                                                        $pdfUrl = asset($fPath);
                                                        $pdfTitle = 'Buka Dokumen PDF Telaah Review (Ber-QR Code)';
                                                        $pdfBtnClass = 'btn-outline-danger';
                                                        $pdfIcon = 'bi bi-file-earmark-pdf-fill text-danger';
                                                    }
                                                }
                                            } elseif ($doc->status === 'VERIFIED') {
                                                // Status VERIFIED: dokumen PDF telaah ber-QR Code yang siap dicetak & ditandatangani fisik
                                                $rawPdf = $doc->signed_file ?: $doc->draft_file;
                                                if (!empty($rawPdf)) {
                                                    $fPath = str_starts_with($rawPdf, 'uploads/') ? $rawPdf : 'uploads/documents/' . $rawPdf;
                                                    if (file_exists(public_path($fPath))) {
                                                        $pdfUrl = asset($fPath);
                                                        $pdfTitle = 'Buka Naskah Terverifikasi (Siap Cetak & Teken Basah)';
                                                        $pdfBtnClass = 'btn-outline-primary text-primary';
                                                        $pdfIcon = 'bi bi-file-earmark-lock-fill text-primary';
                                                    }
                                                }
                                            } elseif ($doc->status === 'ACTIVE') {
                                                // Status ACTIVE: dokumen resmi yang sudah ditandatangani
                                                $rawSigned = $doc->signed_file ?: $doc->draft_file;
                                                if (!empty($rawSigned)) {
                                                    $fPath = str_starts_with($rawSigned, 'uploads/') ? $rawSigned : 'uploads/documents/' . $rawSigned;
                                                    if (file_exists(public_path($fPath))) {
                                                        $pdfUrl = asset($fPath);
                                                        $pdfTitle = 'Buka Dokumen PDF Tertandatangan Sah (Aktif)';
                                                        $pdfBtnClass = 'btn-outline-success text-success';
                                                        $pdfIcon = 'bi bi-file-earmark-check-fill';
                                                    }
                                                }
                                            } elseif ($doc->status === 'WARNING') {
                                                // Status WARNING: dokumen resmi aktif dalam masa peringatan dini
                                                $rawSigned = $doc->signed_file ?: $doc->draft_file;
                                                if (!empty($rawSigned)) {
                                                    $fPath = str_starts_with($rawSigned, 'uploads/') ? $rawSigned : 'uploads/documents/' . $rawSigned;
                                                    if (file_exists(public_path($fPath))) {
                                                        $pdfUrl = asset($fPath);
                                                        $pdfTitle = 'Buka Dokumen PDF Tertandatangan (Peringatan Dini)';
                                                        $pdfBtnClass = 'btn-outline-warning text-warning-emphasis';
                                                        $pdfIcon = 'bi bi-file-earmark-check-fill text-warning';
                                                    }
                                                }
                                            } elseif ($doc->status === 'EXPIRED') {
                                                // Status EXPIRED: dokumen resmi yang telah kedaluwarsa
                                                $rawSigned = $doc->signed_file ?: $doc->draft_file;
                                                if (!empty($rawSigned)) {
                                                    $fPath = str_starts_with($rawSigned, 'uploads/') ? $rawSigned : 'uploads/documents/' . $rawSigned;
                                                    if (file_exists(public_path($fPath))) {
                                                        $pdfUrl = asset($fPath);
                                                        $pdfTitle = 'Buka Dokumen PDF Tertandatangan (Arsip Kedaluwarsa)';
                                                        $pdfBtnClass = 'btn-outline-secondary';
                                                        $pdfIcon = 'bi bi-file-earmark-text text-secondary';
                                                    }
                                                }
                                            } elseif ($doc->status === 'CANCELLED') {
                                                // Status CANCELLED: dokumen yang dibatalkan
                                                $rawCancelled = $doc->draft_file ?: $doc->signed_file;
                                                if (!empty($rawCancelled)) {
                                                    $fPath = str_starts_with($rawCancelled, 'uploads/') ? $rawCancelled : 'uploads/documents/' . $rawCancelled;
                                                    if (file_exists(public_path($fPath))) {
                                                        $pdfUrl = asset($fPath);
                                                        $pdfTitle = 'Buka Dokumen PDF (Dibatalkan)';
                                                        $pdfBtnClass = 'btn-outline-dark text-muted';
                                                        $pdfIcon = 'bi bi-file-earmark-x';
                                                    }
                                                }
                                            }
                                        @endphp

                                        <!-- Status Badge & Jejak Audit -->
                                        <span class="badge {{ $stBadge }} py-1 px-2 d-block fw-bold shadow-sm mb-1" style="font-size: 0.72rem;">
                                            {{ $doc->status }}
                                        </span>
                                        <div class="badge bg-light text-dark border font-monospace mb-1" style="font-size: 0.62rem;">
                                            {{ $doc->reg_id }}
                                        </div>

                                        <!-- Tombol Pintas: Buka Verifikasi & Naskah PDF -->
                                        <div class="d-flex justify-content-center gap-1 mb-1">
                                            <a href="{{ route('simkerma.verify', ['reg_id' => $doc->reg_id]) }}" target="_blank" class="btn btn-outline-primary btn-sm py-0 px-2" style="font-size: 0.65rem;" title="Buka Lembar Verifikasi Publik">
                                                <i class="bi bi-box-arrow-up-right me-1"></i>Verifikasi
                                            </a>
                                            @if ($pdfUrl)
                                                <a href="{{ $pdfUrl }}" target="_blank" class="btn {{ $pdfBtnClass }} btn-sm py-0 px-1" style="font-size: 0.65rem;" title="{{ $pdfTitle }}">
                                                    <i class="{{ $pdfIcon }}"></i>
                                                </a>
                                            @endif
                                        </div>

                                        <div class="text-muted small" style="font-size: 0.66rem;">
                                            <i class="bi bi-clock-history me-1"></i>{{ $doc->auditTrails->count() }} Jejak Audit
                                        </div>
                                    </td>

                                    <!-- 7. AKSI FSM & DISPOSISI -->
                                    <td class="text-end">
                                        <a href="{{ route('simkerma.index', ['menu' => 4, 'view' => 'detail', 'doc' => $doc->doc_id]) }}" class="btn btn-primary btn-sm fw-bold w-100 mb-1 shadow-sm py-1" style="font-size: 0.75rem;">
                                            <i class="bi bi-eye me-1"></i> Detail
                                        </a>
                                        <button type="button" class="btn btn-outline-dark btn-sm py-0 px-2 w-100 mb-1" data-bs-toggle="modal" data-bs-target="#fsmModal_{{ $doc->id }}" style="font-size: 0.65rem;" title="Buka Konsol FSM Cepat">
                                            <i class="bi bi-sliders me-1 text-warning"></i> Kelola FSM
                                        </button>
                                        
                                        @if ($doc->status === 'ACTIVE' || $doc->status === 'WARNING' || $doc->status === 'EXPIRED')
                                            <form method="POST" action="{{ route('simkerma.renew-draft') }}" class="w-100 mb-1" onsubmit="return confirm('Inisiasi pembaruan/reaktivasi naskah {{ $doc->doc_id }}? Status akan kembali ke DRAFT.');">
                                                @csrf
                                                <input type="hidden" name="doc_id" value="{{ $doc->doc_id }}">
                                                <button type="submit" class="btn btn-outline-warning btn-sm py-0 px-2 w-100 text-dark fw-semibold" style="font-size: 0.65rem;" title="Perpanjang / Reaktivasi (Kembali ke DRAFT)">
                                                    <i class="bi bi-arrow-repeat me-1 text-warning"></i> Perpanjang / Reaktivasi
                                                </button>
                                            </form>
                                        @endif
                                        
                                        <div class="d-flex gap-1 justify-content-end">
                                            @if ($doc->status === 'DRAFT')
                                                <form method="POST" action="{{ route('simkerma.delete-draft') }}" class="d-inline" onsubmit="return confirm('KONFIRMASI HAPUS DRAF:\n\nApakah Anda yakin ingin menghapus draf naskah {{ $doc->doc_id }} ({{ addslashes($doc->partner_name) }}) secara permanen?\n\nPERHATIAN: Draf yang dihapus tidak dapat dipulihkan.')">
                                                    @csrf
                                                    <input type="hidden" name="doc_id" value="{{ $doc->doc_id }}">
                                                    <button type="submit" class="btn btn-outline-danger btn-sm py-0 px-2" style="font-size: 0.68rem;" title="Hapus Draf Dokumen Ini">
                                                        <i class="bi bi-trash3"></i>
                                                    </button>
                                                </form>
                                            @endif

                                            @if ($activeRole === 'MASTER')
                                                <form method="POST" action="{{ route('simkerma.reset-draft') }}" class="d-inline" onsubmit="return confirm('Reset status dokumen {{ $doc->doc_id }} ke DRAFT?')">
                                                    @csrf
                                                    <input type="hidden" name="doc_id" value="{{ $doc->doc_id }}">
                                                    <button type="submit" class="btn btn-outline-danger btn-sm py-0 px-2" style="font-size: 0.68rem;" title="Reset ke DRAFT">
                                                        <i class="bi bi-arrow-counterclockwise"></i>
                                                    </button>
                                                </form>
                                            @endif
                                            
                                            @if ($doc->status === 'ACTIVE')
                                                @if (stripos($doc->doc_type, 'MoU') !== false)
                                                    <a href="{{ route('simkerma.index', ['menu' => 4, 'view' => 'new', 'parent_id' => $doc->doc_id, 'derive_type' => 'MoA']) }}" class="btn btn-outline-primary btn-sm py-0 px-2" style="font-size: 0.68rem;" title="+ Turunkan MoA">
                                                        + MoA
                                                    </a>
                                                @elseif (stripos($doc->doc_type, 'MoA') !== false)
                                                    <a href="{{ route('simkerma.index', ['menu' => 4, 'view' => 'new', 'parent_id' => $doc->doc_id, 'derive_type' => 'IA']) }}" class="btn btn-outline-primary btn-sm py-0 px-2" style="font-size: 0.68rem;" title="+ Turunkan IA">
                                                        + IA
                                                    </a>
                                                @endif
                                            @endif
                                        </div>
                                    </td>
                                </tr>

                                <!-- MODAL DEDICATED KONSOL FSM UNTUK DOKUMEN INI -->
                                <div class="modal fade" id="fsmModal_{{ $doc->id }}" tabindex="-1" aria-labelledby="fsmModalLabel_{{ $doc->id }}" aria-hidden="true">
                                    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                                        <div class="modal-content border-0 shadow-lg">
                                            <div class="modal-header bg-dark text-white py-3">
                                                <div class="d-flex align-items-center gap-2">
                                                    <span class="badge bg-warning text-dark fw-bold">{{ $doc->doc_id }}</span>
                                                    <div>
                                                        <h6 class="modal-title fw-bold mb-0" id="fsmModalLabel_{{ $doc->id }}">{{ $doc->partner_name }}</h6>
                                                        <small class="text-white-50">{{ $doc->doc_number ?: 'Registrasi: ' . $doc->reg_id }} &bull; {{ $doc->doc_type }}</small>
                                                    </div>
                                                </div>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>

                                            <div class="modal-body p-4">
                                                
                                                <!-- Stepper Mini Siklus Status FSM Dokumen Ini -->
                                                <div class="p-2 bg-light rounded border mb-3">
                                                    <div class="small fw-bold text-muted mb-1"><i class="bi bi-diagram-2 me-1"></i>Posisi Siklus Hidup FSM:</div>
                                                    <div class="d-flex align-items-center overflow-x-auto gap-1 py-1" style="font-size: 0.68rem;">
                                                        @foreach ($stepperList as $stKey => $stMeta)
                                                            @php $isThisActive = ($stKey === $doc->status); @endphp
                                                            <span class="badge {{ $isThisActive ? 'bg-dark text-white border border-warning shadow-sm fw-bold' : 'bg-white text-muted border' }}">
                                                                {{ $stMeta[0] }}
                                                            </span>
                                                            @if (!$loop->last)
                                                                <i class="bi bi-chevron-right text-muted" style="font-size: 0.6rem;"></i>
                                                            @endif
                                                        @endforeach
                                                    </div>
                                                </div>

                                                <!-- Judul Kerja Sama Banner -->
                                                @if (!empty($doc->title))
                                                    <div class="alert alert-primary bg-primary-subtle text-primary-emphasis border border-primary-subtle p-3 mb-3 small">
                                                        <div class="text-uppercase fw-bold" style="font-size: 0.7rem;"><i class="bi bi-file-earmark-text-fill me-1"></i>Judul / Nama Kerja Sama:</div>
                                                        <div class="fw-bold fs-6">{{ $doc->title }}</div>
                                                    </div>
                                                @endif

                                                <!-- 2 Kolom Ringkasan Informasi Dokumen -->
                                                <div class="row g-3 mb-3 small">
                                                    <div class="col-sm-6">
                                                        <div class="bg-light p-3 rounded border h-100">
                                                            <div class="text-muted fw-bold mb-1" style="font-size: 0.72rem;"><i class="bi bi-info-circle me-1"></i>IDENTITAS DOKUMEN &amp; PENGUSUL:</div>
                                                            <div><strong>No. Reg:</strong> {{ $doc->reg_id }}</div>
                                                            <div><strong>No. Naskah (Pihak I):</strong> {{ $doc->doc_number ?: '-' }}</div>
                                                            <div><strong>No. Naskah (Pihak II):</strong> {{ $doc->partner_doc_number ?: '-' }}</div>
                                                            <div><strong>Jalur:</strong> {{ $doc->track }}</div>
                                                            <div><strong>Unit Pengusul:</strong> {{ $doc->unit }}</div>
                                                            @if (!empty($doc->first_party_signer))
                                                                <div><strong>Penandatangan Pihak I:</strong> {{ $doc->first_party_signer }}</div>
                                                            @endif
                                                            @if (!empty($doc->pic_first_party))
                                                                <div><strong>PIC Pihak I:</strong> {{ $doc->pic_first_party }}</div>
                                                            @endif
                                                            @if (!empty($doc->collab_unit))
                                                                <div><strong>Kolaborator:</strong> {{ $doc->collab_unit }}</div>
                                                            @endif
                                                            <div><strong>Dokumen Induk:</strong> {{ $doc->parent_mou ?: '-' }}</div>
                                                            <div><strong>Format Kertas:</strong> <span class="badge bg-secondary-subtle text-secondary-emphasis border">{{ $doc->paper_size ?: 'A4' }} ({{ ($doc->paper_size ?: 'A4') === 'F4' ? '215 &times; 330 mm' : '210 &times; 297 mm' }})</span></div>
                                                            <div><strong>Posisi QR Code:</strong> <span class="text-success fw-bold"><i class="bi bi-qr-code me-1"></i>Ujung Kanan Bawah Hal. Akhir</span></div>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <div class="bg-light p-3 rounded border h-100">
                                                            <div class="text-muted fw-bold mb-1" style="font-size: 0.72rem;"><i class="bi bi-building me-1"></i>MITRA, PERIODE &amp; ANGGARAN:</div>
                                                            <div><strong>Mitra:</strong> {{ $doc->partner_name }} ({{ $doc->partner_category }})</div>
                                                            @if (!empty($doc->second_party_signer))
                                                                <div><strong>Penandatangan Pihak II:</strong> {{ $doc->second_party_signer }}</div>
                                                            @endif
                                                            @if (!empty($doc->pic_second_party))
                                                                <div><strong>PIC Pihak II:</strong> {{ $doc->pic_second_party }}</div>
                                                            @endif
                                                            <div><strong>Periode:</strong> 
                                                                @if (!empty($doc->start_date) && !empty($doc->end_date))
                                                                    {{ $doc->start_date->format('d/m/Y') }} s/d {{ $doc->end_date->format('d/m/Y') }}
                                                                @else
                                                                    -
                                                                @endif
                                                            </div>
                                                            <div><strong>Masa Berlaku:</strong> {{ $doc->validity }}</div>
                                                            <div><strong>Ambang Batas Peringatan:</strong> <span class="text-warning text-dark fw-bold">{{ $doc->warning_threshold ?? 'H-90 Hari' }}</span></div>
                                                            @if (!empty($doc->nominal_value))
                                                                <div><strong>Nominal / Anggaran:</strong> <span class="badge bg-success-subtle text-success-emphasis border">{{ $doc->nominal_value }}</span></div>
                                                            @endif
                                                            <div><strong>Status Aktif:</strong> <span class="badge {{ $stBadge }}">{{ $doc->status }}</span></div>

                                                            <!-- Berkas Naskah Ber-QR Code & PDF -->
                                                            <div class="mt-2 pt-2 border-top">
                                                                <div class="text-muted fw-bold mb-1" style="font-size: 0.68rem;">BERKAS &amp; KEABSAHAN DOKUMEN:</div>
                                                                <div class="d-flex flex-wrap gap-1">
                                                                    @if (!empty($doc->docx_path) && file_exists(public_path($doc->docx_path)))
                                                                        <a href="{{ asset($doc->docx_path) }}" class="btn btn-outline-primary btn-sm py-1 px-2" style="font-size: 0.72rem;" download>
                                                                            <i class="bi bi-file-earmark-word-fill me-1 text-primary"></i>Naskah Word (.docx)
                                                                        </a>
                                                                    @endif
                                                                    @if (!empty($doc->draft_file) && file_exists(public_path($doc->draft_file)))
                                                                        <a href="{{ asset($doc->draft_file) }}" target="_blank" class="btn btn-outline-danger btn-sm py-1 px-2" style="font-size: 0.72rem;">
                                                                            <i class="bi bi-file-earmark-pdf-fill me-1 text-danger"></i>Naskah PDF
                                                                        </a>
                                                                    @endif
                                                                    <a href="{{ route('simkerma.verify', $doc->reg_id) }}" target="_blank" class="btn btn-outline-success btn-sm py-1 px-2" style="font-size: 0.72rem;">
                                                                        <i class="bi bi-qr-code me-1 text-success"></i>Cek QR Code
                                                                    </a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="p-2 border rounded bg-white">
                                                            <span class="text-muted fw-bold d-block" style="font-size: 0.72rem;">Ruang Lingkup / Substansi:</span>
                                                            <div class="text-dark">{{ $doc->scope }}</div>
                                                        </div>
                                                    </div>
                                                    @if (!empty($doc->description))
                                                        <div class="col-12">
                                                            <div class="p-2 border rounded bg-white">
                                                                <span class="text-muted fw-bold d-block" style="font-size: 0.72rem;">Deskripsi / Ringkasan Kerja Sama:</span>
                                                                <div class="text-secondary small">{{ $doc->description }}</div>
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>

                                                <!-- FORM TRANSISI FSM SESUAI PERAN AKTIF -->
                                                <div class="card border mb-3">
                                                    <div class="card-header bg-white py-2 px-3 d-flex justify-content-between align-items-center">
                                                        <span class="fw-bold small text-dark">
                                                            <i class="bi bi-play-circle text-success me-1"></i> Form Eksekusi Transisi Status FSM
                                                        </span>
                                                        <span class="badge {{ $activeRole === 'MASTER' ? 'bg-danger' : 'bg-primary' }}">
                                                            Peran Anda: {{ $activeRole }}
                                                        </span>
                                                    </div>
                                                    <div class="card-body p-3">
                                                        <form method="POST" action="{{ route('simkerma.transition') }}" enctype="multipart/form-data">
                                                            @csrf
                                                            <input type="hidden" name="doc_id" value="{{ $doc->doc_id }}">

                                                            @if ($doc->status === 'DRAFT')
                                                                <!-- KOTAK PENJELASAN OTOMASI QR CODE & UNGGAH DRAF NASKAH -->
                                                                <div class="mb-3 p-3 bg-white rounded border border-primary-subtle shadow-sm small">
                                                                    <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                                                                        <span class="fw-bold text-primary">
                                                                            <i class="bi bi-qr-code-scan me-1"></i> Penyiapan Naskah &amp; Otomasi QR Code
                                                                        </span>
                                                                        <span class="badge bg-primary-subtle text-primary border border-primary" style="font-size: 0.65rem;">
                                                                            Otomasi Sistem
                                                                        </span>
                                                                    </div>
                                                                    <p class="text-muted mb-2" style="font-size: 0.73rem;">
                                                                        Ketika Anda menekan tombol <strong>Ajukan Naskah ke Meja Pemeriksaan (SUBMIT)</strong>:
                                                                        <br>&bull; Sistem secara otomatis menyematkan stempel QR Code resmi langsung ke naskah dokumen PDF yang dikirim oleh pengusul pada <strong>bagian ujung kanan bawah halaman terakhir dokumen</strong> (mendukung format kertas <strong>A4</strong> dan <strong>F4 / Folio</strong>) tanpa menimpa atau mengubah isi substansi naskah.
                                                                        <br>&bull; Naskah draf ber-QR Code tersebut langsung siap ditelaah kelayakan hukumnya oleh pimpinan.
                                                                    </p>
                                                                    <div class="mb-2">
                                                                        <label class="form-label fw-bold text-dark d-block mb-1" style="font-size: 0.73rem;">
                                                                            <i class="bi bi-aspect-ratio text-primary me-1"></i> Format Ukuran Kertas Dokumen:
                                                                        </label>
                                                                        <select name="paper_size" class="form-select form-select-sm">
                                                                            <option value="A4" {{ ($doc->paper_size ?? 'A4') === 'A4' ? 'selected' : '' }}>Kertas A4 (210 &times; 297 mm) &mdash; Standar</option>
                                                                            <option value="F4" {{ ($doc->paper_size ?? 'A4') === 'F4' ? 'selected' : '' }}>Kertas F4 / Folio (215 &times; 330 mm)</option>
                                                                        </select>
                                                                        <small class="text-muted d-block mt-1" style="font-size: 0.68rem;">
                                                                            Penempatan QR Code disesuaikan secara dinamis di pojok kanan bawah halaman terakhir sesuai ukuran kertas yang dipilih.
                                                                        </small>
                                                                    </div>
                                                                    <div>
                                                                        <label class="form-label fw-bold text-dark d-block mb-1" style="font-size: 0.73rem;">
                                                                            <i class="bi bi-file-earmark-pdf text-danger me-1"></i> Unggah Naskah PDF Revisi Pengusul (Opsional):
                                                                        </label>
                                                                        <input type="file" name="draft_file" class="form-control form-control-sm mb-1" accept=".pdf">
                                                                        <small class="text-muted d-block" style="font-size: 0.68rem;">
                                                                            Jika dikosongkan, sistem akan membubuhkan QR Code langsung pada naskah draf PDF yang telah Anda unggah pada saat pembuatan draf.
                                                                        </small>
                                                                    </div>
                                                                </div>
                                                            @endif

                                                            @if ($doc->status === 'REVIEW')
                                                                <!-- KOTAK PRATINJAU & UNDUH BERKAS NASKAH REVIEW (PDF BER-QR CODE) -->
                                                                <div class="alert alert-info border-info p-3 rounded-3 shadow-sm d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mb-3">
                                                                    <div class="d-flex align-items-center">
                                                                        <div class="bg-info text-dark p-2 rounded-3 me-3 d-flex align-items-center justify-content-center shadow-sm" style="width: 44px; height: 44px; min-width: 44px;">
                                                                            <i class="bi bi-file-earmark-pdf-fill fs-3 text-white"></i>
                                                                        </div>
                                                                        <div>
                                                                            <div class="fw-bold text-dark" style="font-size: 0.85rem;">Berkas Naskah Draf Telaah Legal (PDF Ber-QR Code)</div>
                                                                            <div class="text-muted" style="font-size: 0.72rem;">
                                                                                Stempel QR Code dokumen telah dipasangkan di bagian ujung kanan bawah halaman terakhir dokumen (format {{ $doc->paper_size ?: 'A4' }}). Tim Legal menelaah kelayakan substansi sebelum verifikasi.
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="d-flex flex-wrap gap-2 text-nowrap">
                                                                        @if (!empty($doc->draft_file) && file_exists(public_path($doc->draft_file)))
                                                                            <a href="{{ asset($doc->draft_file) }}" target="_blank" class="btn btn-danger btn-sm fw-bold px-3 py-2 shadow-sm">
                                                                                <i class="bi bi-eye-fill me-1"></i> Buka Naskah PDF Ber-QR Code
                                                                            </a>
                                                                        @endif
                                                                        @if (!empty($doc->docx_path) && file_exists(public_path($doc->docx_path)))
                                                                            <a href="{{ asset($doc->docx_path) }}" class="btn btn-outline-primary btn-sm fw-bold px-3 py-2 shadow-sm" download>
                                                                                <i class="bi bi-file-earmark-word-fill me-1"></i> Unduh Word (.docx)
                                                                            </a>
                                                                        @endif
                                                                    </div>
                                                                </div>

                                                                <!-- 1. CHECKLIST TELAAH SUBSTANSI & MANFAAT KERJA SAMA (STANDAR DIKTI & IKU 6) -->
                                                                <div class="mb-3 p-3 bg-white rounded border border-primary-subtle shadow-sm small">
                                                                    <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                                                                        <span class="fw-bold text-primary">
                                                                            <i class="bi bi-clipboard2-check-fill me-1"></i> Penelaahan Substansi &amp; Mutu Hukum (Tim Legal)
                                                                        </span>
                                                                        <span class="badge bg-primary-subtle text-primary border border-primary" style="font-size: 0.65rem;">
                                                                            Evaluasi Mutu &amp; IKU 6
                                                                        </span>
                                                                    </div>
                                                                    <div class="text-muted mb-2" style="font-size: 0.72rem;">
                                                                        Tim Legal menelaah kesesuaian Tridharma, keadilan klausul hukum, dan proyeksi manfaat. Jika lolos telaah, klik <strong>Verifikasi Kelayakan Draf (VERIFY)</strong> untuk membekukan naskah dan beralih ke status <strong>VERIFIED</strong>.
                                                                    </div>
                                                                    <div class="list-group list-group-flush border rounded mb-2">
                                                                        <label class="list-group-item d-flex align-items-start gap-2 py-2 px-3 bg-light-subtle">
                                                                            <input class="form-check-input flex-shrink-0 mt-1" type="checkbox" name="substance_tridharma" value="1" checked>
                                                                            <div>
                                                                                <strong class="d-block text-dark" style="font-size: 0.75rem;">1. Kesesuaian Substansi Tridharma &amp; Kurikulum MBKM</strong>
                                                                                <span class="text-muted" style="font-size: 0.68rem;">Muatan kegiatan selaras dengan kurikulum prodi (Pendidikan/Magang, Riset Dosen, atau Pengabdian PKM).</span>
                                                                            </div>
                                                                        </label>
                                                                        <label class="list-group-item d-flex align-items-start gap-2 py-2 px-3 bg-light-subtle">
                                                                            <input class="form-check-input flex-shrink-0 mt-1" type="checkbox" name="legal_fairness" value="1" checked>
                                                                            <div>
                                                                                <strong class="d-block text-dark" style="font-size: 0.75rem;">2. Keadilan Klausul Hukum &amp; Hak Kewajiban Berimbang</strong>
                                                                                <span class="text-muted" style="font-size: 0.68rem;">Hak mahasiswa dan dosen terlindungi, kepemilikan HAKI jelas, serta tidak membebani anggaran kampus secara sepihak.</span>
                                                                            </div>
                                                                        </label>
                                                                        <label class="list-group-item d-flex align-items-start gap-2 py-2 px-3 bg-light-subtle">
                                                                            <input class="form-check-input flex-shrink-0 mt-1" type="checkbox" name="iku6_benefit" value="1" checked>
                                                                            <div>
                                                                                <strong class="d-block text-dark" style="font-size: 0.75rem;">3. Kontribusi Manfaat Nyata Capaian IKU 6 Perguruan Tinggi</strong>
                                                                                <span class="text-muted" style="font-size: 0.68rem;">Mendukung rekognisi SKS, praktisi mengajar, sertifikat kompetensi mahasiswa, atau publikasi ilmiah.</span>
                                                                            </div>
                                                                        </label>
                                                                    </div>
                                                                    <small class="text-muted d-block" style="font-size: 0.68rem;">
                                                                        <i class="bi bi-info-circle text-primary me-1"></i> Verifikasi ini akan membekukan teks (Freeze Text). Penandatanganan fisik &amp; meterai dilakukan pada status VERIFIED.
                                                                    </small>
                                                                </div>
                                                            @endif

                                                            @if ($doc->status === 'VERIFIED')
                                                                <!-- KOTAK STATUS VERIFIED: DOKUMEN SIAP DICETAK & DITANDATANGANI FISIK -->
                                                                <div class="alert alert-primary border-primary p-3 rounded-3 shadow-sm d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mb-3">
                                                                    <div class="d-flex align-items-center">
                                                                        <div class="bg-primary text-white p-2 rounded-3 me-3 d-flex align-items-center justify-content-center shadow-sm" style="width: 44px; height: 44px; min-width: 44px;">
                                                                            <i class="bi bi-patch-check-fill fs-3"></i>
                                                                        </div>
                                                                        <div>
                                                                            <div class="fw-bold text-dark" style="font-size: 0.85rem;">Naskah Lolos Verifikasi Legal (Siap Cetak &amp; Penandatanganan Fisik)</div>
                                                                            <div class="text-muted" style="font-size: 0.72rem;">
                                                                                Teks naskah telah terkunci (Freeze Text). Silakan unduh/cetak berkas PDF ber-QR Code resmi berikut untuk ditandatangani basah oleh Rektor dan dibubuhi meterai fisik Rp10.000.
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="d-flex flex-wrap gap-2 text-nowrap">
                                                                        @php
                                                                            $rawVerifiedPdf = $doc->signed_file ?: $doc->draft_file;
                                                                        @endphp
                                                                        @if (!empty($rawVerifiedPdf))
                                                                            @php
                                                                                $vfPath = str_starts_with($rawVerifiedPdf, 'uploads/') ? $rawVerifiedPdf : 'uploads/documents/' . $rawVerifiedPdf;
                                                                            @endphp
                                                                            @if (file_exists(public_path($vfPath)))
                                                                                <a href="{{ asset($vfPath) }}" target="_blank" class="btn btn-danger btn-sm fw-bold px-3 py-2 shadow-sm">
                                                                                    <i class="bi bi-printer-fill me-1"></i> Buka / Cetak PDF Ber-QR
                                                                                </a>
                                                                            @endif
                                                                        @endif
                                                                    </div>
                                                                </div>

                                                                <!-- FORM PENGESAHAN OLEH PIMPINAN (LEADER): UPLOAD SCAN HASIL TEKEN BASAH + NOMOR MITRA -->
                                                                <div class="mb-3 p-3 bg-light rounded border border-success-subtle shadow-sm small">
                                                                    <div class="fw-bold text-dark mb-1 d-flex justify-content-between align-items-center">
                                                                        <span class="text-success"><i class="bi bi-pen-fill me-1"></i> Pengesahan Naskah Fisik &amp; Aktivasi (Pimpinan Rektorat):</span>
                                                                        <span class="badge bg-success-subtle text-success border border-success" style="font-size: 0.65rem;">Diisi untuk Pengesahan (ACTIVATE)</span>
                                                                    </div>
                                                                    <div class="text-muted mb-2" style="font-size: 0.7rem;">
                                                                        Setelah proses tanda tangan basah &amp; bea meterai Rp10.000 selesai, input nomor naskah mitra dan unggah pindaian (scan) berkas PDF untuk mengaktifkan dokumen menjadi status <strong>ACTIVE</strong>.
                                                                    </div>
                                                                    <div class="row g-2 mb-2">
                                                                        <div class="col-sm-6">
                                                                            <label class="form-label fw-bold text-dark" style="font-size: 0.75rem;">Nomor Naskah Pihak I (UNIPAR):</label>
                                                                            <input type="text" name="doc_number" class="form-control form-control-sm bg-white" value="{{ $doc->doc_number }}">
                                                                        </div>
                                                                        <div class="col-sm-6">
                                                                            <label class="form-label fw-bold text-dark" style="font-size: 0.75rem;">Nomor Naskah Pihak II (Mitra): <span class="text-danger">*</span></label>
                                                                            <input type="text" name="partner_doc_number" class="form-control form-control-sm bg-white" value="{{ $doc->partner_doc_number }}" placeholder="Nomor resmi dari pihak mitra" required>
                                                                        </div>
                                                                    </div>
                                                                    <div class="row g-2 mb-2">
                                                                        <div class="col-sm-6">
                                                                            <label class="form-label fw-bold text-dark" style="font-size: 0.75rem;">
                                                                                <i class="bi bi-calendar-event text-primary me-1"></i> Tanggal Mulai Berlaku: <span class="text-danger">*</span>
                                                                            </label>
                                                                            <input type="date" name="start_date" class="form-control form-control-sm bg-white" value="{{ $doc->start_date ? \Carbon\Carbon::parse($doc->start_date)->format('Y-m-d') : date('Y-m-d') }}" required>
                                                                        </div>
                                                                        <div class="col-sm-6">
                                                                            <label class="form-label fw-bold text-dark" style="font-size: 0.75rem;">
                                                                                <i class="bi bi-hourglass-split text-primary me-1"></i> Masa Berlaku Dokumen: <span class="text-danger">*</span>
                                                                            </label>
                                                                            <input type="text" name="validity" class="form-control form-control-sm bg-white" value="{{ $doc->validity ?: '5 Tahun' }}" placeholder="Contoh: 3 Tahun atau 5 Tahun" required>
                                                                        </div>
                                                                    </div>
                                                                    <div class="row g-2 mb-2">
                                                                        <div class="col-sm-6">
                                                                            <label class="form-label fw-bold text-dark d-block" style="font-size: 0.75rem;">
                                                                                <i class="bi bi-calendar-check text-primary me-1"></i> Tanggal Penandatanganan Fisik:
                                                                            </label>
                                                                            <input type="date" name="date_signed" class="form-control form-control-sm bg-white" value="{{ $doc->date_signed ? $doc->date_signed->format('Y-m-d') : date('Y-m-d') }}">
                                                                        </div>
                                                                        <div class="col-sm-6">
                                                                            <label class="form-label fw-bold text-dark d-block" style="font-size: 0.75rem;">
                                                                                <i class="bi bi-file-earmark-check text-success me-1"></i> Unggah Scan Naskah Bertanda Tangan Basah &amp; Bermeterai (PDF):
                                                                            </label>
                                                                            <input type="file" name="signed_scan_file" class="form-control form-control-sm" accept=".pdf">
                                                                        </div>
                                                                    </div>
                                                                    <small class="text-muted" style="font-size: 0.68rem;">* Jika pimpinan membatalkan verifikasi atau meminta revisi, gunakan opsi <strong>Kembalikan ke Draf</strong> tanpa perlu mengunggah berkas.</small>
                                                                </div>
                                                            @endif

                                                            @if ($doc->status === 'WARNING' || $doc->status === 'EXPIRED')
                                                                <div class="mb-3 p-3 bg-warning-subtle rounded border border-warning small">
                                                                    <div class="fw-bold text-dark mb-2 d-flex align-items-center justify-content-between">
                                                                        <span>
                                                                            <i class="bi bi-arrow-repeat text-warning-emphasis me-1"></i> 
                                                                            Inisiasi Naskah Perpanjangan / Adendum (Status: {{ $doc->status }}):
                                                                        </span>
                                                                        <span class="badge bg-warning text-dark border">Kembali ke Status DRAF</span>
                                                                    </div>
                                                                    <p class="text-muted small mb-2" style="font-size: 0.72rem;">
                                                                        Sesuai prinsip <strong>Anti-Bypass FSM</strong>, perpanjangan masa aktif (adendum) atau reaktivasi naskah kedaluwarsa akan mengembalikan dokumen ke status <strong>DRAFT</strong> agar draf klausul baru dapat disesuaikan dan ditelaah ulang oleh Tim Legal (REVIEW) sebelum disahkan pimpinan.
                                                                    </p>
                                                                    <div class="row g-2 mb-2">
                                                                        <div class="col-sm-6">
                                                                            <label class="form-label fw-bold text-dark" style="font-size: 0.75rem;">
                                                                                Nomor Dokumen Pihak 1 (UNIPAR):
                                                                            </label>
                                                                            <input type="text" name="doc_number" class="form-control form-control-sm bg-white" value="{{ $doc->doc_number }}" placeholder="Nomor naskah perpanjangan UNIPAR">
                                                                        </div>
                                                                        <div class="col-sm-6">
                                                                            <label class="form-label fw-bold text-dark" style="font-size: 0.75rem;">
                                                                                Nomor Dokumen Pihak 2 (Mitra):
                                                                            </label>
                                                                            <input type="text" name="partner_doc_number" class="form-control form-control-sm bg-white" value="{{ $doc->partner_doc_number }}" placeholder="Nomor naskah perpanjangan dari mitra">
                                                                        </div>
                                                                    </div>
                                                                    <div class="row g-2">
                                                                        <div class="col-sm-6">
                                                                            <label class="form-label fw-bold text-dark" style="font-size: 0.75rem;">Usulan Masa Berlaku Baru:</label>
                                                                            <input type="text" name="new_validity" class="form-control form-control-sm bg-white" value="{{ $doc->validity ? $doc->validity.' (Diperpanjang)' : '1 Tahun Perpanjangan' }}">
                                                                        </div>
                                                                        <div class="col-sm-6">
                                                                            <label class="form-label fw-bold text-dark" style="font-size: 0.75rem;">Unggah Draf Baru (PDF / DOCX):</label>
                                                                            <input type="file" name="draft_file" class="form-control form-control-sm mb-1" accept=".pdf,.docx">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endif

                                                            <div class="mb-3">
                                                                <label class="form-label fw-bold small text-muted">Catatan Disposisi / Alasan Penolakan / Rekomendasi Pejabat:</label>
                                                                <textarea name="notes" class="form-control form-control-sm" rows="2" placeholder="Opsional: Tuliskan alasan penolakan jika dikembalikan ke draf, atau catatan arahan pengesahan..."></textarea>
                                                            </div>

                                                            <div>
                                                                <label class="form-label fw-bold small text-muted d-block">Aksi Transisi yang Tersedia (Status: {{ $doc->status }}):</label>
                                                                @if (empty($docAvailEvents))
                                                                    <div class="alert alert-secondary py-2 small mb-0 text-center">
                                                                        <i class="bi bi-info-circle me-1"></i> Dokumen berada pada status akhir (Terminal State). Tidak ada transisi lanjutan.
                                                                    </div>
                                                                @else
                                                                    <div class="d-grid gap-2">
                                                                        @foreach ($docAvailEvents as $evKey => $evData)
                                                                            @php
                                                                                $isRoleAllowed = ($activeRole === 'MASTER' || in_array($activeRole, $evData['roles'], true));
                                                                                $btnLabelText = $evData['label'];
                                                                                if ($evKey === 'TRIGGER_WARN_EXPIRY' && !empty($doc->warning_threshold)) {
                                                                                    $btnLabelText = 'Jalankan Pemeriksaan Masa Berlaku (' . $doc->warning_threshold . ')';
                                                                                }

                                                                                $confirmJs = '';
                                                                                if ($evKey === 'SUBMIT') {
                                                                                    $confirmJs = "return confirm('KONFIRMASI PENGAJUAN KE MEJA PEMERIKSAAN (REVIEW):\\n\\nDokumen: " . $doc->doc_id . " - " . addslashes($doc->partner_name) . "\\nJenis: " . $doc->doc_type . "\\nUnit Pengusul: " . addslashes($doc->unit) . "\\n\\nPastikan seluruh draf naskah dan data kemitraan telah diperiksa dengan cermat sebelum diajukan ke pimpinan/tim verifikator.\\n\\nApakah Anda yakin ingin mengajukan naskah ini ke meja pemeriksaan?');";
                                                                                } elseif ($evKey === 'VERIFY') {
                                                                                    $confirmJs = "return confirm('KONFIRMASI VERIFIKASI KELAYAKAN DRAF (VERIFY):\\n\\nDokumen: " . $doc->doc_id . " - " . addslashes($doc->partner_name) . "\\nJenis: " . $doc->doc_type . "\\n\\nNaskah telah memenuhi telaah mutu, substansi kurikulum MBKM, dan klausul hukum.\\n\\nApakah Anda yakin ingin MEMVERIFIKASI naskah ini sehingga status menjadi VERIFIED (Terkunci untuk penandatanganan fisik)?');";
                                                                                } elseif ($evKey === 'CANCEL') {
                                                                                    $confirmJs = "return confirm('KONFIRMASI PEMBATALAN PENGAJUAN (CANCEL):\\n\\nDokumen: " . $doc->doc_id . " - " . addslashes($doc->partner_name) . "\\n\\nPERINGATAN: Naskah kerja sama ini akan dialihkan ke status CANCELLED secara permanen dan tidak dapat diproses lebih lanjut.\\n\\nApakah Anda benar-benar yakin ingin membatalkan usulan ini?');";
                                                                                } elseif ($evKey === 'ACTIVATE') {
                                                                                    $confirmJs = "return confirm('KONFIRMASI PENGESAHAN KERJA SAMA (ACTIVATE):\\n\\nDokumen: " . $doc->doc_id . " - " . addslashes($doc->partner_name) . "\\nJenis: " . $doc->doc_type . "\\n\\nNaskah fisik telah ditandatangani basah oleh Rektor dan mitra serta dibubuhi meterai Rp10.000.\\n\\nApakah Anda yakin ingin MENGESAHKAN dokumen ini menjadi status ACTIVE (Berlaku Sah)?');";
                                                                                } elseif ($evKey === 'RETURN_TO_DRAFT') {
                                                                                    $confirmJs = "return confirm('KONFIRMASI PENGEMBALIAN KE DRAF PENGUSUL (RETURN TO DRAFT):\\n\\nDokumen: " . $doc->doc_id . " - " . addslashes($doc->partner_name) . "\\n\\nNaskah akan dikembalikan ke Unit Pengusul untuk direvisi.\\n\\nApakah Anda yakin ingin mengembalikan naskah ini ke status DRAFT?');";
                                                                                }
                                                                            @endphp
                                                                            <button type="submit" name="event" value="{{ $evKey }}" class="btn btn-sm {{ $evData['btn'] }} py-2 text-start d-flex justify-content-between align-items-center shadow-sm" {{ $isRoleAllowed ? '' : 'disabled' }} {!! $confirmJs ? 'onclick="'.$confirmJs.'"' : '' !!}>
                                                                                <span><i class="bi {{ $evData['icon'] }} me-2"></i>{{ $btnLabelText }}</span>
                                                                                <div>
                                                                                    @if (!$isRoleAllowed)
                                                                                        <span class="badge bg-light text-danger border me-1" style="font-size: 0.65rem;">Wewenang: {{ implode(', ', $evData['roles']) }}</span>
                                                                                    @endif
                                                                                    <span class="badge bg-light text-dark border" style="font-size: 0.65rem;">{{ $evKey }}</span>
                                                                                </div>
                                                                            </button>
                                                                        @endforeach
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>

                                                <!-- RIWAYAT IMMUTABLE AUDIT TRAIL DOKUMEN INI -->
                                                <div>
                                                    <span class="fw-bold small text-muted d-block mb-1">
                                                        <i class="bi bi-clock-history me-1"></i> Jejak Audit Resmi ({{ $doc->auditTrails->count() }} Log Tercatat):
                                                    </span>
                                                    <div class="border rounded p-2 bg-light small" style="max-height: 140px; overflow-y: auto;">
                                                        @foreach ($doc->auditTrails as $audit)
                                                            <div class="border-bottom pb-1 mb-1">
                                                                <div class="d-flex justify-content-between text-muted" style="font-size: 0.7rem;">
                                                                    <span class="fw-bold text-dark">{{ $audit->actor }} [{{ $audit->role }}]</span>
                                                                    <span>{{ $audit->timestamp_wita }}</span>
                                                                </div>
                                                                <div style="font-size: 0.74rem;">
                                                                    <span class="badge bg-secondary" style="font-size: 0.62rem;">{{ $audit->event }}</span>
                                                                    <span class="text-muted">{{ $audit->from_status }} &rarr; {{ $audit->to_status }}:</span>
                                                                    {{ $audit->notes }}
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>

                                            </div>
                                            <div class="modal-footer bg-light py-2 px-3 d-flex justify-content-between">
                                                <div class="d-flex gap-2">
                                                    <a href="{{ route('simkerma.verify', ['reg_id' => $doc->reg_id]) }}" target="_blank" class="btn btn-outline-dark btn-sm">
                                                        <i class="bi bi-box-arrow-up-right me-1"></i> Buka Lembar Verifikasi
                                                    </a>
                                                    @if ($doc->status === 'DRAFT')
                                                        <form method="POST" action="{{ route('simkerma.delete-draft') }}" class="d-inline" onsubmit="return confirm('KONFIRMASI PENGHAPUSAN DRAF:\n\nApakah Anda yakin ingin menghapus draf naskah {{ $doc->doc_id }} ({{ addslashes($doc->partner_name) }}) secara permanen?\n\nPERHATIAN: Draf yang dihapus tidak dapat dipulihkan kembali.')">
                                                            @csrf
                                                            <input type="hidden" name="doc_id" value="{{ $doc->doc_id }}">
                                                            <button type="submit" class="btn btn-outline-danger btn-sm">
                                                                <i class="bi bi-trash3 me-1"></i> Hapus Draf
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            @empty
                                <tr id="tableEmptyRow">
                                    <td colspan="6" class="text-center py-5 text-muted">
                                        <i class="bi bi-folder2-open fs-1 d-block mb-2 text-secondary"></i>
                                        <strong class="text-dark">Belum Ada Pengajuan Dokumen Kerja Sama</strong>
                                        <div class="small mt-1">Gunakan tombol <strong>+ Pengajuan MoU Baru</strong> di atas untuk membuat usulan dokumen MoU baru.</div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        @endif

        {{-- SCRIPT FILTER PENCARIAN SISI KLIEN --}}
        @if (!$isCreatingNew && !$isCreatingDerive && !$isCreatingLegacy && !$isViewingDetail)
        @push('scripts')
        <script>
        // Fitur Filter & Pencarian Cepat Sisi Klien
        function filterDocumentsTable() {
            const query = (document.getElementById('tableFilterInput').value || '').toLowerCase().trim();
            const statusFilter = document.getElementById('tableStatusFilter').value;
            const typeFilter = (document.getElementById('tableTypeFilter').value || '').toLowerCase();
            const rows = document.querySelectorAll('#documentsTableBody tr.doc-row');

            let visibleCount = 0;
            rows.forEach(function(row) {
                const text = row.innerText.toLowerCase();
                const rowStatus = row.getAttribute('data-status') || '';
                const rowType = (row.getAttribute('data-type') || '').toLowerCase();

                const matchesQuery = !query || text.includes(query);
                const matchesStatus = !statusFilter || rowStatus === statusFilter;
                const matchesType = !typeFilter || rowType.includes(typeFilter);

                if (matchesQuery && matchesStatus && matchesType) {
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
        }

        function resetFilters() {
            document.getElementById('tableFilterInput').value = '';
            document.getElementById('tableStatusFilter').value = '';
            document.getElementById('tableTypeFilter').value = '';
            filterDocumentsTable();
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
                    <small class="text-muted">Memastikan Perjanjian Benar-Benar Dilaksanakan Berdasarkan Naskah Rujukan Resmi (MoU / MoA / IA / Paket Terpadu)</small>
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
                <table class="table table-wireframe table-hover align-middle mb-0 small" id="activityTable">
                    <thead class="bg-light">
                        <tr>
                            <th style="min-width: 190px;">Daftar Kegiatan &amp; Dokumen Rujukan (Menu 04)</th>
                            <th style="min-width: 130px;">Mitra &amp; Unit PJ</th>
                            <th style="min-width: 110px;">Target &amp; Realisasi</th>
                            <th style="min-width: 120px;">Peserta / Pihak Terlibat</th>
                            <th style="min-width: 130px;">Luaran IKU 6</th>
                            <th style="min-width: 100px;">Kontribusi / Anggaran</th>
                            <th style="min-width: 90px;">Bukti Berkas</th>
                            <th style="min-width: 105px;" class="text-center">Status &amp; QR FSM</th>
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
                                            <strong>No. Pihak I:</strong> {{ $doc->doc_number ?: $doc->doc_id }}
                                        </div>
                                        @if (!empty($doc->partner_doc_number))
                                            <div class="text-secondary">
                                                <strong>No. Pihak II:</strong> {{ $doc->partner_doc_number }}
                                            </div>
                                        @endif
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
                                    <span class="badge bg-light text-secondary border mb-1" style="font-size: 0.65rem;">{{ $doc->partner_category ?? 'Mitra Eksternal' }}</span>
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
                                    <div class="text-dark">{{ $act->parties_involved ?: '-' }}</div>
                                </td>

                                <!-- 5. LUARAN IKU 6 -->
                                <td>
                                    <div class="text-dark">{{ $act->outputs ?: '-' }}</div>
                                </td>

                                <!-- 6. ANGGARAN / KONTRIBUSI -->
                                <td>
                                    <span class="badge bg-light text-dark border">{{ $act->budget_info ?: '-' }}</span>
                                </td>

                                <!-- 7. BUKTI BERKAS -->
                                <td>
                                    @if ($act->evidence_file)
                                        <a href="#" class="text-decoration-none text-dark fw-bold d-block mb-1" style="font-size: 0.74rem;">
                                            <i class="bi bi-file-earmark-pdf text-danger me-1"></i>{{ $act->evidence_file }}
                                        </a>
                                    @else
                                        <span class="text-muted small">-</span>
                                    @endif
                                    @if ($act->evidence_photos)
                                        <span class="badge bg-light text-secondary border" style="font-size: 0.65rem;">
                                            <i class="bi bi-image me-1"></i>Foto Kegiatan
                                        </span>
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
                                        <strong>Masa Berlaku:</strong> {{ $wDoc->validity }}
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

@endsection
