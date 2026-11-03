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
                            <th style="min-width: 200px;">Nama &amp; Profil Mitra</th>
                            <th style="min-width: 140px;">Kategori Mitra</th>
                            <th style="min-width: 180px;">Unit Kampus &amp; Mitra Rujukan</th>
                            <th style="min-width: 200px;">Lingkup Kerja Sama Utama</th>
                            <th style="min-width: 130px;">Riwayat Dokumen</th>
                            <th style="min-width: 130px;" class="text-center">Status Kemitraan</th>
                            <th style="min-width: 140px;" class="text-end">Aksi Terintegrasi</th>
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
                            <th style="min-width: 220px;">Nomor Dokumen &amp; Jenis Naskah</th>
                            <th style="min-width: 240px;">Judul Kerja Sama &amp; Mitra</th>
                            <th style="min-width: 170px;">Tanggal TTD &amp; Masa Berlaku</th>
                            <th style="min-width: 160px;">Pihak Penandatangan</th>
                            <th style="min-width: 160px;">Berkas &amp; Riwayat Audit</th>
                            <th style="min-width: 140px;" class="text-center">Aksi Dokumen</th>
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

        <!-- BILAH KENDALI UTAMA: PERAN AKTOR OTENTIK DENGAN ROLE MASTER -->
        <div class="card card-wireframe mb-4">
            <div class="card-body p-3 bg-white">
                <div class="row align-items-center g-3">
                    <div class="col-lg-6">
                        <div class="d-flex align-items-center gap-2">
                            <div class="bg-primary text-white p-2 rounded">
                                <i class="bi bi-shield-check fs-5"></i>
                            </div>
                            <div>
                                <h6 class="mb-0 fw-bold">PENGAJUAN DAN PERSETUJUAN KERJA SAMA</h6>
                                <div class="small text-muted">Mesin FSM 9 Status &amp; Keterlacakan Hierarki Kemitraan (IKU 6) &bull; MySQL Relasional</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <form method="POST" action="{{ route('simkerma.change-role') }}" class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-lg-end gap-2">
                            @csrf
                            <span class="small fw-bold text-muted text-nowrap"><i class="bi bi-person-badge me-1"></i>Peran Aktif:</span>
                            <div class="btn-group shadow-sm w-100 w-sm-auto" role="group">
                                <button type="submit" name="role" value="UNIT" class="btn btn-sm {{ $activeRole === 'UNIT' ? 'btn-primary text-white fw-bold' : 'btn-outline-primary' }}" title="Unit Pengusul (Prodi / Fakultas / LPPM)">
                                    UNIT
                                </button>
                                <button type="submit" name="role" value="LEGAL" class="btn btn-sm {{ $activeRole === 'LEGAL' ? 'btn-warning text-dark fw-bold' : 'btn-outline-warning text-dark' }}" title="Tim Telaah Hukum &amp; Kemitraan">
                                    LEGAL
                                </button>
                                <button type="submit" name="role" value="LEADER" class="btn btn-sm {{ $activeRole === 'LEADER' ? 'btn-success text-white fw-bold' : 'btn-outline-success' }}" title="Pimpinan Institusi (Rektorat / Dekanat)">
                                    LEADER
                                </button>
                                <button type="submit" name="role" value="SYSTEM" class="btn btn-sm {{ $activeRole === 'SYSTEM' ? 'btn-dark text-white fw-bold' : 'btn-outline-dark' }}" title="Scheduler Otomasi Sistem (Cron Worker)">
                                    SYSTEM
                                </button>
                                <button type="submit" name="role" value="ADMIN" class="btn btn-sm {{ in_array($activeRole, ['ADMIN', 'MASTER'], true) ? 'btn-danger text-white fw-bold' : 'btn-outline-danger' }}" title="Role Admin / Master (Super Admin / Akses Bypass Semua Langkah)">
                                    <i class="bi bi-star-fill me-1"></i> ADMIN
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- PITA STEPPER 7 STATUS SIKLUS HIDUP FSM -->
            <div class="card-footer bg-light border-top p-2 px-3">
                <div class="d-flex align-items-center justify-content-between overflow-x-auto py-1" style="min-width: 720px; font-size: 0.72rem;">
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
                    @foreach ($stepperList as $stKey => $stMeta)
                        <div class="d-flex align-items-center gap-1">
                            <span class="badge {{ ($currentDoc && $currentDoc->status === $stKey) ? $stMeta[1] : 'bg-light text-dark border' }}" style="font-size: 0.68rem;">
                                {{ $stMeta[0] }}
                            </span>
                            @if (!$loop->last)
                                <i class="bi bi-chevron-right text-muted small" style="font-size: 0.65rem;"></i>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- JIKA MODE INPUT PENGAJUAN BARU SEDANG AKTIF --}}
        @if ($isCreatingNew)

            <div class="card card-wireframe mb-4">
                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title mb-0 fw-bold text-uppercase">Formulir Pengajuan Dokumen Kerja Sama Baru</h6>
                        <div class="small text-muted">Pengajuan baru akan diterbitkan dengan status awal <strong>DRAFT</strong> lengkap dengan QR Code registrasi resmi.</div>
                    </div>
                    <a href="{{ route('simkerma.index', ['menu' => 4]) }}" class="btn btn-outline-secondary btn-sm fw-semibold">
                        <i class="bi bi-arrow-left me-1"></i> Batal / Kembali ke Tabel Pengajuan
                    </a>
                </div>
                <div class="card-body p-4">

                    <!-- Pemberitahuan Kontekstual Jika Menurunkan dari Dokumen Induk -->
                    @if ($parentDocForDerive)
                        <div class="alert alert-info py-2 px-3 small mb-4 d-flex align-items-center gap-2 border shadow-sm">
                            <i class="bi bi-diagram-3-fill fs-5 text-primary"></i>
                            <div>
                                <strong>Menurunkan Dokumen dari Induk Aktif:</strong> Naskah baru ini akan otomatis diikat secara hukum ke dokumen induk <strong>{{ $parentDocForDerive->doc_number }}</strong> bersama mitra <strong>{{ $parentDocForDerive->partner_name }}</strong>.
                            </div>
                        </div>
                    @endif
                    
                    <!-- Banner Unduh Template Dokumen Word Draf MoU Resmi -->
                    <div class="card border-primary-subtle bg-primary-subtle bg-opacity-10 mb-4 shadow-sm">
                        <div class="card-body p-3 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                            <div class="d-flex align-items-center gap-3">
                                <div class="bg-primary text-white p-2 rounded-3 shadow-sm d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; min-width: 48px;">
                                    <i class="bi bi-file-earmark-word-fill fs-3"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold text-dark mb-1">
                                        Template Dokumen Naskah Draf MoU Resmi (.docx) &mdash; Format Bersih (Tanpa QR Code)
                                    </h6>
                                    <p class="text-muted small mb-0">
                                        Unduh template naskah Word resmi ini untuk menyusun draf kerja sama. Area tanda tangan telah disiapkan bersih tanpa gambar QR Code. Ketika draf naskah diajukan ke meja pemeriksaan (<strong>SUBMIT &rarr; REVIEW</strong>), sistem secara otomatis memasangkan stempel QR Code dokumen ke naskah Word tepat di atas tulisan <strong>Pihak Pertama</strong> (Universitas Parna Raya Manado) dengan ukuran 150&times;150 px tetap presisi, membiarkan area di atas nama Rektor tetap leluasa untuk tanda tangan fisik, serta menerbitkan berkas PDF resmi.
                                    </p>
                                </div>
                            </div>
                            <div class="text-nowrap">
                                <a href="{{ asset('templates/Template_MoU_UNIPAR.docx') }}" class="btn btn-primary btn-sm fw-bold px-3 py-2 shadow-sm" download="Template_MoU_UNIPAR.docx">
                                    <i class="bi bi-download me-1"></i> Unduh Template Draf (.docx)
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Tab Jalur 1 vs Jalur 2 -->
                    <ul class="nav nav-pills nav-fill bg-light p-1 rounded border mb-4 small" id="jalurTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active py-2 fw-bold" id="jalur1-tab" data-bs-toggle="pill" data-bs-target="#jalur1-pane" type="button" role="tab" aria-controls="jalur1-pane" aria-selected="true">
                                <i class="bi bi-file-earmark-text me-1"></i> Jalur 1: Kemitraan Bertahap (MoU / MoA / IA)
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link py-2 fw-bold" id="jalur2-tab" data-bs-toggle="pill" data-bs-target="#jalur2-pane" type="button" role="tab" aria-controls="jalur2-pane" aria-selected="false">
                                <i class="bi bi-box-seam me-1"></i> Jalur 2: Paket Terpadu Tridharma &amp; PKM
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content" id="jalurTabContent">
                        <!-- JALUR 1: KEMITRAAN BERTAHAP -->
                        <div class="tab-pane fade show active" id="jalur1-pane" role="tabpanel" aria-labelledby="jalur1-tab">
                            <form method="POST" action="{{ route('simkerma.store-jalur1') }}" enctype="multipart/form-data" class="small">
                                @csrf

                                <!-- 1. JUDUL / NAMA KERJA SAMA -->
                                <div class="mb-3">
                                    <label class="form-label fw-bold text-dark fs-6 mb-1">
                                        1. Judul / Nama Kerja Sama: <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" name="title" class="form-control" placeholder="Contoh: Perjanjian Kerja Sama Penyelenggaraan Program Merdeka Belajar Kampus Merdeka dan Penguatan Tridharma..." required>
                                    <div class="form-text text-muted">Tuliskan nama atau judul resmi naskah kerja sama yang diajukan.</div>
                                </div>

                                <!-- 2. JENIS DOKUMEN (MoU / MoA / IA) -->
                                <div class="mb-3">
                                    <label class="form-label fw-bold text-dark fs-6 mb-1">
                                        2. Jenis Dokumen: <span class="text-danger">*</span>
                                    </label>
                                    <select name="doc_type" id="inputDocType" class="form-select" onchange="handleDocTypeChange(this.value)" required>
                                        <option value="MoU" {{ ($prefillDeriveType === 'MoU') ? 'selected' : '' }}>MoU (Nota Kesepahaman Induk)</option>
                                        <option value="MoA / PKS" {{ ($prefillDeriveType === 'MoA' || $prefillDeriveType === 'MoA / PKS') ? 'selected' : '' }}>MoA / PKS (Perjanjian Kerja Sama)</option>
                                        <option value="IA" {{ ($prefillDeriveType === 'IA') ? 'selected' : '' }}>IA (Implementation Arrangement)</option>
                                    </select>
                                    <div class="form-text text-muted">Pilih tingkatan dokumen formal sesuai hierarki kerja sama Tridharma.</div>
                                </div>

                                <!-- 2b. DOKUMEN INDUK RUJUKAN (JIKA MoA / IA) -->
                                <div class="mb-3" id="groupParentMou">
                                    <label class="form-label fw-bold text-dark fs-6 mb-1">
                                        Dokumen Induk Rujukan (Khusus MoA / IA):
                                    </label>
                                    <input type="text" name="parent_mou" id="inputParentMou" class="form-control" value="{{ $prefillParentDocNumber ?: '-' }}" list="registeredMouList" placeholder="Pilih nomor dokumen MoU rujukan atau ketik '-' jika MoU mandiri">
                                    <datalist id="registeredMouList">
                                        <option value="-">
                                        @foreach ($registeredMous as $mouItem)
                                            <option value="{{ $mouItem->doc_number }}">
                                        @endforeach
                                    </datalist>
                                    <div class="form-text text-muted">Untuk dokumen MoA atau IA, pilih naskah MoU payung yang menjadi dasar hukumnya.</div>
                                </div>

                                <!-- 3. IDENTITAS PIHAK PERTAMA (PENGUSUL) -->
                                <div class="mb-3">
                                    <label class="form-label fw-bold text-dark fs-6 mb-1">
                                        3. Identitas Pihak Pertama (Pengusul - UNIPAR): <span class="text-danger">*</span>
                                    </label>
                                    <div class="p-3 bg-light rounded border">
                                        <div class="mb-2">
                                            <label class="form-label fw-semibold small mb-1">Unit Pengusul (Fakultas / Program Studi / Lembaga): <span class="text-danger">*</span></label>
                                            <select name="unit" class="form-select form-select-sm">
                                                <option>Program Studi Teknik Informatika</option>
                                                <option>Fakultas Ilmu Komputer</option>
                                                <option>Fakultas Ekonomi dan Bisnis</option>
                                                <option>LPPM Universitas Parna Raya</option>
                                                <option>Biro Administrasi Akademik &amp; Kemahasiswaan</option>
                                                <option>Rektorat Universitas Parna Raya</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="form-label fw-semibold small mb-1">Pejabat / Penandatangan Pihak Pertama:</label>
                                            <input type="text" name="first_party_signer" class="form-control form-control-sm" placeholder="Contoh: Rektor Universitas Parna Raya / Dekan Fakultas Ilmu Komputer">
                                        </div>
                                    </div>
                                </div>

                                <!-- 4. IDENTITAS PIHAK KEDUA (MITRA) -->
                                <div class="mb-3">
                                    <label class="form-label fw-bold text-dark fs-6 mb-1">
                                        4. Identitas Pihak Kedua (Mitra Kerja Sama): <span class="text-danger">*</span>
                                    </label>
                                    <div class="p-3 bg-light rounded border">
                                        <div class="mb-2">
                                            <label class="form-label fw-semibold small mb-1">Nama Instansi / Badan Usaha Mitra: <span class="text-danger">*</span></label>
                                            <input type="text" name="partner_name" class="form-control form-control-sm" value="{{ $prefillPartnerName }}" list="partnerDatalist" placeholder="Pilih mitra terdaftar atau ketik instansi baru..." required>
                                            <datalist id="partnerDatalist">
                                                @foreach ($partners as $p)
                                                    <option value="{{ $p->name }}">{{ $p->category }} &bull; {{ $p->city ?: 'Manado' }}</option>
                                                @endforeach
                                            </datalist>
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label fw-semibold small mb-1">Kategori / Sektor Mitra:</label>
                                            <select name="category" class="form-select form-select-sm">
                                                <option {{ $prefillCategory === 'DUDI / Swasta' ? 'selected' : '' }}>DUDI / Swasta</option>
                                                <option {{ $prefillCategory === 'BUMN' ? 'selected' : '' }}>BUMN</option>
                                                <option {{ $prefillCategory === 'Instansi Pemerintah' ? 'selected' : '' }}>Instansi Pemerintah</option>
                                                <option {{ $prefillCategory === 'Perguruan Tinggi' ? 'selected' : '' }}>Perguruan Tinggi</option>
                                                <option {{ $prefillCategory === 'Lembaga Keagamaan / Komunitas' ? 'selected' : '' }}>Lembaga Keagamaan / Komunitas</option>
                                                <option {{ $prefillCategory === 'Kesehatan' ? 'selected' : '' }}>Kesehatan</option>
                                                <option>Lainnya</option>
                                            </select>
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label fw-semibold small mb-1">Pejabat / Penandatangan Pihak Kedua:</label>
                                            <input type="text" name="second_party_signer" class="form-control form-control-sm" placeholder="Contoh: Direktur Utama PT ... / Kepala Balai ... / Dekan ...">
                                        </div>
                                        <div>
                                            <label class="form-label fw-semibold small mb-1">Nomor Surat / Naskah Pihak Kedua (Mitra) [Opsional]:</label>
                                            <input type="text" name="partner_doc_number" class="form-control form-control-sm" placeholder="Nomor resmi dari instansi mitra (jika sudah ada)">
                                        </div>
                                    </div>
                                </div>

                                <!-- 5. RUANG LINGKUP / BENTUK KEGIATAN -->
                                <div class="mb-3">
                                    <label class="form-label fw-bold text-dark fs-6 mb-1">
                                        5. Ruang Lingkup / Bentuk Kegiatan: <span class="text-danger">*</span>
                                    </label>
                                    <textarea name="scope" class="form-control" rows="3" placeholder="Rincikan bentuk kerja sama atau lingkup kegiatan konkret (misalnya: pertukaran mahasiswa, magang industri MBKM, penelitian kolaboratif, pengabdian masyarakat, dsb.)..." required></textarea>
                                </div>

                                <!-- 6. UPLOAD DRAFT DOKUMEN -->
                                <div class="mb-3">
                                    <label class="form-label fw-bold text-dark fs-6 mb-1">
                                        6. Upload Draft Dokumen (Word .docx / PDF):
                                    </label>
                                    <input type="file" name="draft_file" class="form-control" accept=".docx,.pdf,.doc">
                                    <div class="form-text text-muted">Format berkas didukung: .docx (disarankan menggunakan template resmi) atau .pdf. QR Code akan disematkan otomatis saat naskah diajukan.</div>
                                </div>

                                <!-- 7. TANGGAL MULAI & TANGGAL BERAKHIR -->
                                <div class="mb-3">
                                    <label class="form-label fw-bold text-dark fs-6 mb-1">
                                        7. Tanggal Mulai &amp; Tanggal Berakhir Kerja Sama:
                                    </label>
                                    <div class="p-3 bg-light rounded border">
                                        <div class="mb-2">
                                            <label class="form-label fw-semibold small mb-1">Tanggal Mulai Berlaku:</label>
                                            <input type="date" name="start_date" id="inputStartDate" class="form-control form-control-sm" value="{{ date('Y-m-d') }}" onchange="calculateDuration()">
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label fw-semibold small mb-1">Tanggal Berakhir:</label>
                                            <input type="date" name="end_date" id="inputEndDate" class="form-control form-control-sm" value="{{ date('Y-m-d', strtotime('+1 year')) }}" onchange="calculateDuration()">
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label fw-semibold small mb-1">Masa Berlaku / Jangka Waktu:</label>
                                            <input type="text" name="validity" id="inputValidity" class="form-control form-control-sm" value="1 Tahun" placeholder="Contoh: 1 Tahun, 3 Tahun, 5 Tahun">
                                        </div>
                                        <div>
                                            <label class="form-label fw-semibold small mb-1">Ambang Batas Peringatan Dini (Warning Threshold):</label>
                                            <select name="warning_threshold" class="form-select form-select-sm">
                                                <option value="H-90 Hari (3 Bulan)" selected>H-90 Hari (3 Bulan) &mdash; Sesuai Klausul MoU Baku UNIPAR</option>
                                                <option value="H-60 Hari (2 Bulan)">H-60 Hari (2 Bulan) &mdash; Standar Kemendikbudristek</option>
                                                <option value="H-30 Hari (1 Bulan)">H-30 Hari (1 Bulan) &mdash; Kemitraan Singkat</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <!-- 8. PIC / KONTAK NARAHUBUNG KEDUA PIHAK -->
                                <div class="mb-3">
                                    <label class="form-label fw-bold text-dark fs-6 mb-1">
                                        8. PIC / Kontak Narahubung Kedua Pihak:
                                    </label>
                                    <div class="p-3 bg-light rounded border">
                                        <div class="mb-2">
                                            <label class="form-label fw-semibold small mb-1">PIC Pihak Pertama (Pengusul UNIPAR):</label>
                                            <input type="text" name="pic_first_party" class="form-control form-control-sm" placeholder="Contoh: Dr. Ir. John Doe, M.Kom. (0812-44xx-xxxx / john@unipar.ac.id)">
                                        </div>
                                        <div>
                                            <label class="form-label fw-semibold small mb-1">PIC Pihak Kedua (Mitra Kerja Sama):</label>
                                            <input type="text" name="pic_second_party" class="form-control form-control-sm" placeholder="Contoh: Jane Smith, S.T. - Kepala Divisi HR (0813-55xx-xxxx / hr@mitra.co.id)">
                                        </div>
                                    </div>
                                </div>

                                <!-- 9. NILAI / NOMINAL DANA (JIKA ADA) -->
                                <div class="mb-3">
                                    <label class="form-label fw-bold text-dark fs-6 mb-1">
                                        9. Nilai / Nominal Dana (Jika Ada):
                                    </label>
                                    <input type="text" name="nominal_value" class="form-control" placeholder="Contoh: Rp 50.000.000 / Non-finansial (In-kind) / Hibah Riset">
                                    <div class="form-text text-muted">Cantumkan komitmen pendanaan kerja sama, kontribusi fasilitas (in-kind), atau kosongkan jika tidak ada dana.</div>
                                </div>

                                <!-- 10. DESKRIPSI / RINGKASAN -->
                                <div class="mb-4">
                                    <label class="form-label fw-bold text-dark fs-6 mb-1">
                                        10. Deskripsi / Ringkasan:
                                    </label>
                                    <textarea name="description" class="form-control" rows="3" placeholder="Tuliskan ringkasan singkat latar belakang pengajuan, tujuan strategis, dan urgensi kerja sama kemitraan ini..."></textarea>
                                </div>

                                <!-- TOMBOL AKSI SIMPAN -->
                                <div class="pt-2">
                                    <button type="submit" class="btn btn-dark btn-sm fw-bold px-4 py-2 shadow-sm">
                                        <i class="bi bi-save me-1"></i> Simpan Sebagai DRAF (Terbitkan QR Code)
                                    </button>
                                    <a href="{{ route('simkerma.index', ['menu' => 4]) }}" class="btn btn-outline-secondary btn-sm ms-2">Batal</a>
                                </div>
                            </form>
                        </div>

                        <!-- JALUR 2: PAKET TERPADU TRIDHARMA & PKM -->
                        <div class="tab-pane fade" id="jalur2-pane" role="tabpanel" aria-labelledby="jalur2-tab">
                            <form method="POST" action="{{ route('simkerma.store-jalur2') }}" enctype="multipart/form-data" class="small">
                                @csrf

                                <!-- 1. JUDUL / NAMA KERJA SAMA PKM -->
                                <div class="mb-3">
                                    <label class="form-label fw-bold text-dark fs-6 mb-1">
                                        1. Judul / Nama Program Paket Terpadu PKM: <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" name="title" class="form-control" placeholder="Contoh: Paket Terpadu Pengabdian Kepada Masyarakat & Kemitraan MBKM..." required>
                                </div>

                                <!-- 2. NAMA KEGIATAN / TOPIK PKM -->
                                <div class="mb-3">
                                    <label class="form-label fw-bold text-dark fs-6 mb-1">
                                        2. Nama Kegiatan / Topik Spesifik PKM: <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" name="topic" class="form-control" placeholder="Contoh: Digitalisasi UMKM dan Pemberdayaan Komunitas Pesisir..." required>
                                </div>

                                <!-- 3. IDENTITAS PIHAK PERTAMA (PENGUSUL) -->
                                <div class="mb-3">
                                    <label class="form-label fw-bold text-dark fs-6 mb-1">
                                        3. Identitas Pihak Pertama (Pengusul & Kolaborator): <span class="text-danger">*</span>
                                    </label>
                                    <div class="p-3 bg-light rounded border">
                                        <div class="mb-2">
                                            <label class="form-label fw-semibold small mb-1">Unit Pengusul Utama:</label>
                                            <select name="unit_main" class="form-select form-select-sm">
                                                <option>Program Studi Teknik Informatika</option>
                                                <option selected>Fakultas Ilmu Komputer</option>
                                                <option>Fakultas Ekonomi dan Bisnis</option>
                                                <option>LPPM Universitas Parna Raya</option>
                                            </select>
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label fw-semibold small mb-1">Unit / Fakultas Kolaborator:</label>
                                            <select name="unit_collab" class="form-select form-select-sm">
                                                <option>-</option>
                                                <option>Fakultas Ilmu Komputer</option>
                                                <option selected>Fakultas Ekonomi dan Bisnis</option>
                                                <option>LPPM Universitas Parna Raya</option>
                                                <option>Program Studi Teknik Informatika</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="form-label fw-semibold small mb-1">Pejabat / Penandatangan Pihak Pertama:</label>
                                            <input type="text" name="first_party_signer" class="form-control form-control-sm" placeholder="Contoh: Dekan Fakultas Ilmu Komputer / Ketua LPPM">
                                        </div>
                                    </div>
                                </div>

                                <!-- 4. IDENTITAS PIHAK KEDUA (MITRA) -->
                                <div class="mb-3">
                                    <label class="form-label fw-bold text-dark fs-6 mb-1">
                                        4. Identitas Pihak Kedua (Mitra PKM): <span class="text-danger">*</span>
                                    </label>
                                    <div class="p-3 bg-light rounded border">
                                        <div class="mb-2">
                                            <label class="form-label fw-semibold small mb-1">Nama Instansi / Badan Usaha Mitra: <span class="text-danger">*</span></label>
                                            <input type="text" name="partner_name" class="form-control form-control-sm" list="partnerDatalist" placeholder="Pilih mitra terdaftar atau ketik baru..." required>
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label fw-semibold small mb-1">Kategori Mitra:</label>
                                            <select name="category" class="form-select form-select-sm">
                                                <option selected>DUDI / Swasta</option>
                                                <option>BUMN</option>
                                                <option>Instansi Pemerintah</option>
                                                <option>Lembaga Keagamaan / Komunitas</option>
                                                <option>Kesehatan</option>
                                            </select>
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label fw-semibold small mb-1">Pejabat / Penandatangan Pihak Kedua:</label>
                                            <input type="text" name="second_party_signer" class="form-control form-control-sm" placeholder="Contoh: Kepala Desa / Pimpinan Yayasan / Direktur">
                                        </div>
                                        <div>
                                            <label class="form-label fw-semibold small mb-1">Nomor Surat / Naskah Pihak Kedua (Mitra) [Opsional]:</label>
                                            <input type="text" name="partner_doc_number" class="form-control form-control-sm" placeholder="Nomor resmi dari instansi mitra (jika sudah ada)">
                                        </div>
                                    </div>
                                </div>

                                <!-- 5. TANGGAL MULAI & TANGGAL BERAKHIR -->
                                <div class="mb-3">
                                    <label class="form-label fw-bold text-dark fs-6 mb-1">
                                        5. Tanggal Mulai &amp; Tanggal Berakhir PKM:
                                    </label>
                                    <div class="p-3 bg-light rounded border">
                                        <div class="mb-2">
                                            <label class="form-label fw-semibold small mb-1">Tanggal Mulai:</label>
                                            <input type="date" name="start_date" id="inputPkmStartDate" class="form-control form-control-sm" value="{{ date('Y-m-d') }}">
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label fw-semibold small mb-1">Tanggal Berakhir:</label>
                                            <input type="date" name="end_date" id="inputPkmEndDate" class="form-control form-control-sm" value="{{ date('Y-m-d', strtotime('+1 year')) }}">
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label fw-semibold small mb-1">Jangka Waktu:</label>
                                            <select name="duration" class="form-select form-select-sm">
                                                <option selected>1 Tahun</option>
                                                <option>6 Bulan</option>
                                                <option>2 Tahun</option>
                                                <option>3 Tahun</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="form-label fw-semibold small mb-1">Ambang Batas Peringatan Dini (Warning Threshold):</label>
                                            <select name="warning_threshold" class="form-select form-select-sm">
                                                <option value="H-90 Hari (3 Bulan)" selected>H-90 Hari (3 Bulan) &mdash; Sesuai Klausul MoU Baku</option>
                                                <option value="H-60 Hari (2 Bulan)">H-60 Hari (2 Bulan) &mdash; Standar Kemendikbudristek</option>
                                                <option value="H-30 Hari (1 Bulan)">H-30 Hari (1 Bulan) &mdash; Kemitraan Singkat</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <!-- 6. PIC / KONTAK NARAHUBUNG KEDUA PIHAK -->
                                <div class="mb-3">
                                    <label class="form-label fw-bold text-dark fs-6 mb-1">
                                        6. PIC / Kontak Narahubung Kedua Pihak:
                                    </label>
                                    <div class="p-3 bg-light rounded border">
                                        <div class="mb-2">
                                            <label class="form-label fw-semibold small mb-1">PIC Pihak Pertama (UNIPAR):</label>
                                            <input type="text" name="pic_first_party" class="form-control form-control-sm" placeholder="Contoh: Ketua Tim Pengabdi / Dosen Pembimbing (0812-xxxx-xxxx)">
                                        </div>
                                        <div>
                                            <label class="form-label fw-semibold small mb-1">PIC Pihak Kedua (Mitra):</label>
                                            <input type="text" name="pic_second_party" class="form-control form-control-sm" placeholder="Contoh: Narahubung Komunitas / Mitra Lapangan (0813-xxxx-xxxx)">
                                        </div>
                                    </div>
                                </div>

                                <!-- 7. DATA MAHASISWA & PESERTA -->
                                <div class="mb-3">
                                    <label class="form-label fw-bold text-dark fs-6 mb-1">
                                        7. Data Mahasiswa / Peserta Terlibat: <span class="text-danger">*</span>
                                    </label>
                                    <textarea name="students_data" class="form-control" rows="2" placeholder="Nama mahasiswa, NIM, dan program studi yang diterjunkan dalam program..." required></textarea>
                                </div>

                                <!-- 8. NILAI / NOMINAL DANA (JIKA ADA) -->
                                <div class="mb-3">
                                    <label class="form-label fw-bold text-dark fs-6 mb-1">
                                        8. Nilai / Nominal Dana (Jika Ada):
                                    </label>
                                    <input type="text" name="nominal_value" class="form-control" placeholder="Contoh: Rp 25.000.000 (Hibah Internal PKM LPPM) / Non-finansial">
                                </div>

                                <!-- 9. DESKRIPSI / RINGKASAN KEGIATAN -->
                                <div class="mb-3">
                                    <label class="form-label fw-bold text-dark fs-6 mb-1">
                                        9. Deskripsi / Ringkasan Pelaksanaan:
                                    </label>
                                    <textarea name="description" class="form-control" rows="3" placeholder="Ringkasan target luaran, skema kegiatan Tridharma, dan manfaat bagi mitra sasaran..."></textarea>
                                </div>

                                <!-- 10. UPLOAD DRAF NASKAH -->
                                <div class="mb-4">
                                    <label class="form-label fw-bold text-dark fs-6 mb-1">
                                        10. Upload Berkas Draf Paket (Word / PDF):
                                    </label>
                                    <input type="file" name="draft_file" class="form-control" accept=".docx,.pdf,.doc">
                                    <div class="form-text text-muted">Unggah naskah draf terpadu yang memuat kesepakatan Tridharma &amp; PKM bersama.</div>
                                </div>

                                <!-- TOMBOL AKSI SIMPAN -->
                                <div class="pt-2">
                                    <button type="submit" class="btn btn-dark btn-sm fw-bold px-4 py-2 shadow-sm">
                                        <i class="bi bi-box-seam me-1"></i> Simpan Sebagai Paket DRAF (Terbitkan QR Code)
                                    </button>
                                    <a href="{{ route('simkerma.index', ['menu' => 4]) }}" class="btn btn-outline-secondary btn-sm ms-2">Batal</a>
                                </div>
                            </form>
                        </div>
                    </div>

                </div>
            </div>

            <script>
            function handleDocTypeChange(val) {
                const parentGroup = document.getElementById('groupParentMou');
                const parentSelect = document.getElementById('inputParentMou');
                if (!parentSelect) return;
                if (val === 'MoU') {
                    parentSelect.value = '-';
                    if (parentGroup) parentGroup.style.display = 'none';
                } else {
                    if (parentGroup) parentGroup.style.display = 'block';
                    if (parentSelect.value === '-') parentSelect.value = '';
                }
            }

            function calculateDuration() {
                const startEl = document.getElementById('inputStartDate');
                const endEl = document.getElementById('inputEndDate');
                const validityEl = document.getElementById('inputValidity');
                if (!startEl || !endEl || !validityEl) return;
                if (!startEl.value || !endEl.value) return;

                const d1 = new Date(startEl.value);
                const d2 = new Date(endEl.value);
                if (d2 <= d1) return;

                let months = (d2.getFullYear() - d1.getFullYear()) * 12 + (d2.getMonth() - d1.getMonth());
                if (d2.getDate() < d1.getDate()) {
                    months--;
                }

                if (months >= 12) {
                    const years = Math.floor(months / 12);
                    const remMonths = months % 12;
                    validityEl.value = years + ' Tahun' + (remMonths > 0 ? ' ' + remMonths + ' Bulan' : '');
                } else if (months > 0) {
                    validityEl.value = months + ' Bulan';
                } else {
                    const diffDays = Math.ceil((d2 - d1) / (1000 * 60 * 60 * 24));
                    validityEl.value = diffDays + ' Hari';
                }
            }
            document.addEventListener('DOMContentLoaded', function() {
                const docTypeEl = document.getElementById('inputDocType');
                if (docTypeEl) {
                    handleDocTypeChange(docTypeEl.value);
                }
            });
            </script>

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
                            Menampilkan seluruh dokumen kerja sama terdaftar secara vertikal dengan QR Code real-time pada setiap baris.
                        </small>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-light text-dark border px-3 py-2 fw-semibold">
                            Total: <span id="visibleDocCount" class="text-primary fw-bold">{{ $documents->count() }}</span> Dokumen
                        </span>
                        <a href="{{ route('simkerma.index', ['menu' => 4, 'view' => 'new']) }}" class="btn btn-primary btn-sm fw-bold shadow-sm text-nowrap">
                            <i class="bi bi-plus-circle-fill me-1"></i> + Tambah Pengajuan Baru
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
                                <th style="min-width: 170px;">Nomor Dokumen &amp; Jenis</th>
                                <th style="min-width: 180px;">Mitra Kerja Sama</th>
                                <th style="min-width: 200px;">Unit Pengusul &amp; Ruang Lingkup</th>
                                <th style="min-width: 140px;">Masa Berlaku &amp; Peringatan</th>
                                <th style="min-width: 155px;" class="text-center">Status</th>
                                <th style="min-width: 150px;" class="text-end">Aksi &amp; Disposisi</th>
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

                                        <!-- QR Code Resmi di Atas Status -->
                                        <div class="p-1 border rounded bg-white shadow-sm d-inline-block mb-1">
                                            <div class="row-qr-canvas" data-reg-id="{{ $doc->reg_id }}" style="width: 64px; height: 64px; display: flex; align-items: center; justify-content: center; margin: 0 auto;"></div>
                                        </div>

                                        <!-- Deretan Tombol Aksi: Perbesar QR, Buka Verifikasi, & Buka Naskah PDF -->
                                        <div class="d-flex justify-content-center gap-1 mb-2">
                                            <button type="button" class="btn btn-outline-dark btn-sm py-0 px-1" style="font-size: 0.62rem;" title="Perbesar QR Code" onclick="showQrModal('{{ $doc->reg_id }}', '{{ $doc->doc_number ?: 'Dalam Proses' }}', '{{ addslashes(str_replace(["\r", "\n"], ' ', $doc->scope)) }}', '{{ addslashes($doc->partner_name) }}', '{{ $doc->status }}', '{{ $doc->date_signed ? $doc->date_signed->format('d M Y') : ($doc->date_approved ? $doc->date_approved->format('d M Y') : date('d M Y')) }}', '{{ addslashes($doc->partner_doc_number ?? '') }}')">
                                                <i class="bi bi-arrows-fullscreen"></i>
                                            </button>
                                            <a href="{{ route('simkerma.verify', ['reg_id' => $doc->reg_id]) }}" target="_blank" class="btn btn-outline-primary btn-sm py-0 px-1" style="font-size: 0.62rem;" title="Buka Lembar Verifikasi Publik">
                                                <i class="bi bi-box-arrow-up-right"></i>
                                            </a>
                                            @if ($pdfUrl)
                                                <a href="{{ $pdfUrl }}" target="_blank" class="btn {{ $pdfBtnClass }} btn-sm py-0 px-1" style="font-size: 0.62rem;" title="{{ $pdfTitle }}">
                                                    <i class="{{ $pdfIcon }}"></i>
                                                </a>
                                            @endif
                                        </div>

                                        <!-- Status Badge & Jejak Audit di Bawah Tombol -->
                                        <span class="badge {{ $stBadge }} py-1 px-2 d-block fw-bold shadow-sm" style="font-size: 0.72rem;">
                                            {{ $doc->status }}
                                        </span>
                                        <div class="badge bg-light text-dark border mt-1 font-monospace" style="font-size: 0.62rem;">
                                            {{ $doc->reg_id }}
                                        </div>
                                        <div class="text-muted small mt-1" style="font-size: 0.66rem;">
                                            <i class="bi bi-clock-history me-1"></i>{{ $doc->auditTrails->count() }} Jejak Audit
                                        </div>
                                    </td>

                                    <!-- 7. AKSI FSM & DISPOSISI -->
                                    <td class="text-end">
                                        <button type="button" class="btn btn-dark btn-sm fw-bold w-100 mb-1 shadow-sm py-1" data-bs-toggle="modal" data-bs-target="#fsmModal_{{ $doc->id }}" style="font-size: 0.75rem;">
                                            <i class="bi bi-sliders me-1 text-warning"></i> Kelola Alur FSM
                                        </button>
                                        
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
                                                                        <br>&bull; Sistem secara otomatis menyematkan stempel QR Code dokumen ke berkas Word pada tabel tanda tangan tanpa garis tepat di atas tulisan <strong>Pihak Pertama</strong> (Universitas Parna Raya Manado) dengan ukuran 150&times;150 px tetap presisi dan tidak bergeser.
                                                                        <br>&bull; Berkas Word tersebut otomatis dikonversi menjadi dokumen PDF resmi untuk ditelaah oleh pimpinan.
                                                                    </p>
                                                                    <div>
                                                                        <label class="form-label fw-bold text-dark d-block mb-1" style="font-size: 0.73rem;">
                                                                            <i class="bi bi-file-earmark-word text-primary me-1"></i> Unggah Naskah Word (.docx) Revisi Pengusul (Opsional):
                                                                        </label>
                                                                        <input type="file" name="draft_docx_file" class="form-control form-control-sm mb-1" accept=".docx">
                                                                        <small class="text-muted d-block" style="font-size: 0.68rem;">
                                                                            Jika Anda telah mengedit naskah dari template, unggah di sini. Jika dikosongkan, sistem akan mengotomasi berkas dari <em>Template Resmi MoU UNIPAR</em> yang terisi nomor naskah dan nama mitra.
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
                                                                                Stempel QR Code dokumen telah dipasangkan pada naskah Word di atas kolom penandatanganan Rektor UNIPAR. Tim Legal menelaah kelayakan substansi sebelum verifikasi.
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
                                                                            <label class="form-label fw-bold text-dark" style="font-size: 0.75rem;">Nomor Naskah Pihak II (Mitra):</label>
                                                                            <input type="text" name="partner_doc_number" class="form-control form-control-sm bg-white" value="{{ $doc->partner_doc_number }}" placeholder="Nomor resmi dari pihak mitra">
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
                                                                            Pembaruan Naskah Perpanjangan / Adendum (Status: {{ $doc->status }}):
                                                                        </span>
                                                                        <span class="badge bg-warning text-dark border">Ambang Batas H-90</span>
                                                                    </div>
                                                                    <div class="row g-2 mb-2">
                                                                        <div class="col-sm-6">
                                                                            <label class="form-label fw-bold text-dark" style="font-size: 0.75rem;">
                                                                                Nomor Dokumen Pihak 1 (UNIPAR): <span class="text-danger">*</span>
                                                                            </label>
                                                                            <input type="text" name="doc_number" class="form-control form-control-sm bg-white" value="{{ $doc->doc_number }}" placeholder="Nomor naskah perpanjangan UNIPAR" required>
                                                                        </div>
                                                                        <div class="col-sm-6">
                                                                            <label class="form-label fw-bold text-dark" style="font-size: 0.75rem;">
                                                                                Nomor Dokumen Pihak 2 (Mitra): <span class="text-danger">*</span>
                                                                            </label>
                                                                            <input type="text" name="partner_doc_number" class="form-control form-control-sm bg-white" value="{{ $doc->partner_doc_number }}" placeholder="Nomor naskah perpanjangan dari mitra" required>
                                                                        </div>
                                                                    </div>
                                                                    <div class="mb-2">
                                                                        <label class="form-label fw-bold text-dark d-block" style="font-size: 0.75rem;">
                                                                            <i class="bi bi-cloud-arrow-up text-primary me-1"></i>
                                                                            Upload Dokumen Perpanjangan / Adendum (PDF): <span class="text-danger">*</span>
                                                                        </label>
                                                                        <input type="file" name="signed_scan_file" class="form-control form-control-sm mb-1" accept=".pdf">
                                                                        <small class="text-muted" style="font-size: 0.7rem;">
                                                                            Unggah berkas scan PDF naskah perpanjangan bertanda tangan basah dan bermeterai dari kedua belah pihak.
                                                                        </small>
                                                                    </div>
                                                                    <div class="row g-2">
                                                                        <div class="col-sm-6">
                                                                            <label class="form-label fw-bold text-dark" style="font-size: 0.75rem;">Masa Berlaku Perpanjangan:</label>
                                                                            <input type="text" name="new_validity" class="form-control form-control-sm bg-white" value="{{ $doc->validity ? $doc->validity.' (Diperpanjang)' : '1 Tahun Perpanjangan' }}">
                                                                        </div>
                                                                        <div class="col-sm-6">
                                                                            <label class="form-label fw-bold text-dark" style="font-size: 0.75rem;">Tanggal Pengesahan Baru:</label>
                                                                            <input type="date" name="date_signed" class="form-control form-control-sm bg-white" value="{{ date('Y-m-d') }}">
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
                                        <div class="small mt-1">Gunakan tombol <strong>+ Tambah Pengajuan Baru</strong> di atas untuk membuat usulan dokumen baru.</div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        @endif

        {{-- SCRIPT GENERATOR QR CODE UNTUK SEMUA BARIS TABEL & FILTER PENCARIAN --}}
        @if (!$isCreatingNew)
        @push('scripts')
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Render QR Code untuk setiap baris tabel secara mandiri
            document.querySelectorAll('.row-qr-canvas').forEach(function(canvasBox) {
                const regId = canvasBox.getAttribute('data-reg-id');
                if (regId) {
                    canvasBox.innerHTML = '';
                    const verifyUrl = window.location.origin + '/verify/' + encodeURIComponent(regId);
                    try {
                        new QRCode(canvasBox, {
                            text: verifyUrl,
                            width: 68,
                            height: 68,
                            colorDark: "#0f172a",
                            colorLight: "#ffffff",
                            correctLevel: QRCode.CorrectLevel.M
                        });
                    } catch(e) {
                        canvasBox.innerHTML = '<i class="bi bi-qr-code text-dark fs-2"></i>';
                    }
                }
            });
        });

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
                            <th style="min-width: 250px;">Daftar Kegiatan &amp; Dokumen Rujukan (Menu 04)</th>
                            <th style="min-width: 170px;">Mitra &amp; Unit PJ</th>
                            <th style="min-width: 140px;">Target &amp; Realisasi</th>
                            <th style="min-width: 160px;">Peserta / Pihak Terlibat</th>
                            <th style="min-width: 170px;">Luaran IKU 6</th>
                            <th style="min-width: 130px;">Kontribusi / Anggaran</th>
                            <th style="min-width: 110px;">Bukti Berkas</th>
                            <th style="min-width: 130px;" class="text-center">Status &amp; QR FSM</th>
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
