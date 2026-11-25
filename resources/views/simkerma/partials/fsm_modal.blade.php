@php
    // Lookup berkas dokumen naskah (PDF/DOCX)
    $pdfDocUrl = null;
    $rawFile = $doc->signed_file ?: $doc->draft_file;
    if (!empty($rawFile)) {
        $fPath = str_starts_with($rawFile, 'uploads/') ? $rawFile : 'uploads/documents/' . $rawFile;
        if (file_exists(public_path($fPath))) {
            $pdfDocUrl = asset($fPath);
        }
    }

    $docxDocUrl = null;
    if (!empty($doc->docx_path)) {
        $dxPath = str_starts_with($doc->docx_path, 'uploads/') ? $doc->docx_path : $doc->docx_path;
        if (file_exists(public_path($dxPath))) {
            $docxDocUrl = asset($dxPath);
        }
    }

    // 6 Tahapan Workflow FSM Sesuai UI/FSM-UI.html
    $wfStages = [
        0 => ['key' => 'DRAFT', 'label' => 'Draft', 'icon' => 'bi-file-earmark-text', 'mat' => 'description'],
        1 => ['key' => 'REVIEW', 'label' => 'Verifikasi', 'icon' => 'bi-search', 'mat' => 'search'],
        2 => ['key' => 'VERIFIED', 'label' => 'Persetujuan', 'icon' => 'bi-shield-check', 'mat' => 'verified'],
        3 => ['key' => 'SIGNING', 'label' => 'Penandatanganan', 'icon' => 'bi-pen', 'mat' => 'edit'],
        4 => ['key' => 'ACTIVE', 'label' => 'Aktif', 'icon' => 'bi-check-circle', 'mat' => 'check_circle'],
        5 => ['key' => 'EXPIRED', 'label' => 'Selesai', 'icon' => 'bi-clock-history', 'mat' => 'history'],
    ];

    $curStatus = $doc->status;
    $activeIdx = match($curStatus) {
        'DRAFT' => 0,
        'REVIEW' => 1,
        'VERIFIED' => 3, // Pada status VERIFIED, telaah disetujui & sedang penandatanganan fisik
        'ACTIVE', 'WARNING' => 4,
        'EXPIRED' => 5,
        'CANCELLED' => -1,
        default => 0
    };

    $isWarningStatus = ($curStatus === 'WARNING');
    $fsmMatrixRow = $fullFsmMatrix[$curStatus] ?? [];
    $canFsmEvent = function (string $ev) use ($activeRole, $fsmMatrixRow): bool {
        if (in_array($activeRole, ['MASTER', 'ADMIN'], true)) {
            return true;
        }
        $allowed = $fsmMatrixRow[$ev]['roles'] ?? [];
        return in_array($activeRole, $allowed, true);
    };
    $hasAnyTransitionAuth = false;
    foreach (array_keys($fsmMatrixRow) as $evKey) {
        if ($canFsmEvent($evKey)) {
            $hasAnyTransitionAuth = true;
            break;
        }
    }

    $canDelete = ($doc->status === 'DRAFT' || $activeRole === 'MASTER' || $activeRole === 'ADMIN');
@endphp

<!-- MODAL FSM WORKFLOW ENGINE (UI/FSM-UI.html) UNTUK DOKUMEN: {{ $doc->doc_id }} -->
<div class="modal fade" id="fsmModal_{{ $doc->id }}" tabindex="-1" aria-labelledby="fsmModalLabel_{{ $doc->id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable fsm-modal-dialog">
        <div class="modal-content fsm-modal-content border-0 shadow-lg">

            <!-- Modal Header (Deep Royal Violet) -->
            <div class="fsm-header d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-3">
                    <div class="fsm-header-icon shadow-sm">
                        <i class="bi bi-file-earmark-text fs-5"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold text-white mb-0" id="fsmModalLabel_{{ $doc->id }}" style="font-size: 1.15rem; letter-spacing: -0.2px;">
                            {{ $doc->title ?: ($doc->scope ?: 'Detail Pengajuan Kerja Sama') }}
                        </h5>
                        <p class="text-white-50 mb-0 d-flex align-items-center flex-wrap gap-2 mt-0.5" style="font-size: 0.8rem;">
                            <span class="badge bg-white text-dark fw-bold px-1.5 py-0.5" style="font-size: 0.7rem;">{{ $doc->doc_type }}</span>
                            <span class="font-monospace">{{ $doc->doc_number ?: 'No. Reg: ' . $doc->reg_id }}</span>
                            <span>&bull;</span>
                            <span class="text-white">{{ $doc->partner_name }}</span>
                        </p>
                    </div>
                </div>
                <button type="button" class="fsm-close-btn" data-bs-dismiss="modal" aria-label="Tutup detail pengajuan">
                    <i class="bi bi-x-lg fs-5"></i>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="modal-body p-3 p-md-4">

                <!-- 1. 6-Stage Horizontal Workflow Stepper Sesuai FSM-UI.html -->
                <nav aria-label="Tahapan pengajuan kerja sama" class="w-100 pt-1 pb-3 mb-2">
                    <div class="position-relative">
                        <div class="d-none d-sm-block position-absolute" style="top: 16px; left: 45px; right: 45px; height: 2px; background-color: #E2E4EC; z-index: 1;" aria-hidden="true"></div>
                        <ol class="list-unstyled row g-0 text-center position-relative mb-0" style="z-index: 2;">
                            @foreach ($wfStages as $sIdx => $stg)
                                <li class="col-4 col-sm-2 d-flex flex-column align-items-center position-relative mb-2 mb-sm-0">
                                    @if ($activeIdx === -1 && $sIdx === 0)
                                        <div class="rounded-circle d-flex align-items-center justify-content-center text-white shadow-sm" style="width: 32px; height: 32px; background-color: #DC2626; font-size: 0.85rem;">
                                            <i class="bi bi-x-lg"></i>
                                        </div>
                                        <span class="mt-2 px-2.5 py-0.5 rounded text-white fw-bold" style="background-color: #DC2626; font-size: 0.72rem;">Dibatalkan</span>
                                    @elseif ($sIdx === $activeIdx)
                                        <!-- Tahap Aktif -->
                                        @if ($isWarningStatus)
                                            <div class="rounded-circle d-flex align-items-center justify-content-center text-dark shadow-sm" style="width: 32px; height: 32px; background-color: #f59e0b; font-size: 0.85rem;">
                                                <i class="bi bi-bell-fill"></i>
                                            </div>
                                            <span class="mt-2 px-2.5 py-0.5 rounded text-dark fw-bold shadow-2xs" style="background-color: #fbbf24; font-size: 0.72rem;">Peringatan (H-90)</span>
                                        @else
                                            <div class="rounded-circle d-flex align-items-center justify-content-center text-white shadow-sm" style="width: 32px; height: 32px; background-color: #5156be; font-size: 0.85rem;">
                                                <i class="bi {{ $stg['icon'] }}"></i>
                                            </div>
                                            <span class="mt-2 px-3 py-0.5 rounded text-white fw-bold shadow-2xs" style="background-color: #5156be; font-size: 0.72rem;">{{ $stg['label'] }}</span>
                                        @endif
                                    @elseif ($sIdx < $activeIdx)
                                        <!-- Tahap Selesai Dilalui -->
                                        <div class="rounded-circle d-flex align-items-center justify-content-center text-white" style="width: 24px; height: 24px; margin-top: 4px; background-color: #5156be; font-size: 0.75rem;">
                                            <i class="bi bi-check-lg"></i>
                                        </div>
                                        <span class="mt-2 fw-semibold" style="color: #5156be; font-size: 0.72rem;">{{ $stg['label'] }}</span>
                                    @else
                                        <!-- Tahap Belum Aktif -->
                                        <div class="rounded-circle d-flex align-items-center justify-content-center bg-white border" style="width: 24px; height: 24px; margin-top: 4px; border-color: #E2E4EC !important; border-width: 2px !important;"></div>
                                        <span class="mt-2" style="color: #5E6472; font-size: 0.72rem;">{{ $stg['label'] }}</span>
                                    @endif
                                </li>
                            @endforeach
                        </ol>
                    </div>
                </nav>

                <!-- 2. Highlight Banner: Judul Kerja Sama -->
                <div class="fsm-title-banner d-flex align-items-center gap-3 mb-3">
                    <div class="fsm-header-icon shadow-none" style="border: 1px solid rgba(81, 86, 190, 0.2); color: #5156be;">
                        <i class="bi bi-file-earmark-text fs-5"></i>
                    </div>
                    <div class="d-flex flex-column gap-0.5">
                        <span style="font-size: 0.72rem; color: #5E6472; font-weight: 500;">Judul Kerja Sama</span>
                        <h6 class="mb-0 fw-bold" style="color: #2b2e6b; font-size: 0.96rem;">{{ $doc->title ?: 'Naskah Kerja Sama Kemitraan' }}</h6>
                    </div>
                </div>

                <!-- 3. Two-Column Metadata Cards -->
                <div class="row g-3 mb-3">
                    <!-- Left Card: Informasi Dokumen -->
                    <div class="col-md-6">
                        <div class="fsm-card-elevated h-100">
                            <div class="card-header">
                                <i class="bi bi-bank fs-6"></i>
                                <span>Informasi Dokumen</span>
                            </div>
                            <div class="card-body d-flex flex-column gap-2">
                                <div class="d-flex align-items-baseline">
                                    <span class="text-muted flex-shrink-0" style="width: 140px;">No. Registrasi</span>
                                    <span class="text-muted me-2">:</span>
                                    <span class="text-dark fw-medium font-monospace">{{ $doc->reg_id }}</span>
                                </div>
                                <div class="d-flex align-items-baseline">
                                    <span class="text-muted flex-shrink-0" style="width: 140px;">Jenis Dokumen</span>
                                    <span class="text-muted me-2">:</span>
                                    <span class="text-dark fw-medium">{{ $doc->doc_type }}</span>
                                </div>
                                <div class="d-flex align-items-baseline">
                                    <span class="text-muted flex-shrink-0" style="width: 140px;">Unit Pengusul</span>
                                    <span class="text-muted me-2">:</span>
                                    <span class="text-dark fw-medium">{{ $doc->unit }}</span>
                                </div>
                                <div class="d-flex align-items-baseline">
                                    <span class="text-muted flex-shrink-0" style="width: 140px;">Penandatangan UNIPAR</span>
                                    <span class="text-muted me-2">:</span>
                                    <span class="text-dark fw-medium">
                                        {{ $doc->first_party_signer ?: 'Rektor Universitas Parna Raya' }}
                                        @if (!empty($doc->first_party_position))
                                            <small class="text-muted">({{ $doc->first_party_position }})</small>
                                        @endif
                                    </span>
                                </div>
                                <div class="d-flex align-items-baseline">
                                    <span class="text-muted flex-shrink-0" style="width: 140px;">Penandatangan Mitra</span>
                                    <span class="text-muted me-2">:</span>
                                    <span class="text-dark fw-medium">
                                        {{ $doc->second_party_signer ?: ($doc->pic_second_party ?: 'Pimpinan Mitra') }}
                                        @if (!empty($doc->second_party_position))
                                            <small class="text-muted">({{ $doc->second_party_position }})</small>
                                        @endif
                                    </span>
                                </div>
                                @if (!empty($doc->doc_number))
                                    <div class="d-flex align-items-baseline">
                                        <span class="text-muted flex-shrink-0" style="width: 140px;">No. Naskah (Pihak I)</span>
                                        <span class="text-muted me-2">:</span>
                                        <span class="text-dark fw-medium">{{ $doc->doc_number }}</span>
                                    </div>
                                @endif
                                @if (!empty($doc->partner_doc_number))
                                    <div class="d-flex align-items-baseline">
                                        <span class="text-muted flex-shrink-0" style="width: 140px;">No. Naskah (Pihak II)</span>
                                        <span class="text-muted me-2">:</span>
                                        <span class="text-dark fw-medium">{{ $doc->partner_doc_number }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Right Card: Informasi Mitra & Periode -->
                    <div class="col-md-6">
                        <div class="fsm-card-elevated h-100">
                            <div class="card-header">
                                <i class="bi bi-people-fill fs-6"></i>
                                <span>Informasi Mitra &amp; Periode</span>
                            </div>
                            <div class="card-body d-flex flex-column justify-content-between gap-3">
                                <div class="d-flex flex-column gap-2">
                                    <div class="d-flex align-items-baseline">
                                        <span class="text-muted flex-shrink-0" style="width: 110px;">Nama Mitra</span>
                                        <span class="text-muted me-2">:</span>
                                        <span class="text-dark fw-medium">{{ $doc->partner_name }}</span>
                                    </div>
                                    <div class="d-flex align-items-baseline">
                                        <span class="text-muted flex-shrink-0" style="width: 110px;">Kategori</span>
                                        <span class="text-muted me-2">:</span>
                                        <span class="text-dark fw-medium">{{ $doc->partner_category ?: 'DUDI / Swasta' }}</span>
                                    </div>
                                    <div class="d-flex align-items-baseline">
                                        <span class="text-muted flex-shrink-0" style="width: 110px;">Masa Berlaku</span>
                                        <span class="text-muted me-2">:</span>
                                        <span class="text-dark fw-medium">{{ $doc->validity ?: 'Belum Ditetapkan' }}</span>
                                    </div>
                                    <div class="d-flex align-items-baseline">
                                        <span class="text-muted flex-shrink-0" style="width: 110px;">Periode</span>
                                        <span class="text-muted me-2">:</span>
                                        <span class="text-dark fw-medium">
                                            @if (!empty($doc->start_date) && !empty($doc->end_date))
                                                {{ $doc->start_date->format('d M Y') }} &ndash; {{ $doc->end_date->format('d M Y') }}
                                            @else
                                                -
                                            @endif
                                        </span>
                                    </div>
                                </div>

                                <div class="pt-1 d-flex flex-wrap gap-2">
                                    @if ($pdfDocUrl)
                                        <a href="{{ $pdfDocUrl }}" target="_blank" class="fsm-btn-pdf shadow-2xs">
                                            <i class="bi bi-file-earmark-pdf-fill fs-6"></i>
                                            <span>Naskah Dokumen (PDF)</span>
                                        </a>
                                    @else
                                        <button type="button" class="btn btn-sm d-inline-flex align-items-center gap-1 text-muted border" style="background-color: #F8F9FC; border-color: #E2E4EC !important; font-size: 0.8rem;" disabled>
                                            <i class="bi bi-file-earmark-pdf"></i>
                                            <span>Naskah Dokumen (PDF Belum Diunggah)</span>
                                        </button>
                                    @endif

                                    @if ($docxDocUrl)
                                        <a href="{{ $docxDocUrl }}" download class="btn btn-sm btn-outline-primary d-inline-flex align-items-center gap-1" style="font-size: 0.8rem; border-radius: 8px;">
                                            <i class="bi bi-file-earmark-word-fill"></i>
                                            <span>Word (.docx)</span>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 4. Scope Card: Ruang Lingkup -->
                <div class="card border rounded p-3 mb-3 shadow-none" style="border-color: #E2E4EC !important; background-color: #ffffff;">
                    <div class="d-flex align-items-start gap-3">
                        <div class="mt-0.5" style="color: #5156be;">
                            <i class="bi bi-crosshair fs-5"></i>
                        </div>
                        <div class="d-flex flex-column gap-1">
                            <h6 class="fw-bold mb-0" style="color: #5156be; font-size: 0.9rem;">Ruang Lingkup</h6>
                            <p class="mb-0 text-dark" style="font-size: 0.82rem; line-height: 1.45;">
                                {{ $doc->scope ?: ($doc->description ?: 'Pendidikan, Penelitian, Pengabdian kepada Masyarakat, dan Pengembangan SDM.') }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Form Transisi Status FSM -->
                <form method="POST" action="{{ route('simkerma.transition') }}" id="fsmForm_{{ $doc->id }}" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="doc_id" value="{{ $doc->doc_id }}">
                    <input type="hidden" name="actor_role" value="{{ $activeRole }}">
                    <input type="hidden" name="actor_name" value="{{ auth()->check() ? auth()->user()->name : 'Pejabat UNIPAR' }}">
                    @if (request()->query('view') === 'detail')
                        <input type="hidden" name="redirect_view" value="detail">
                    @endif

                    @if ($isWarningStatus)
                        <div class="alert alert-warning py-2 px-3 small mb-3 d-flex align-items-center gap-2 border-warning-subtle">
                            <i class="bi bi-exclamation-triangle-fill fs-5 text-warning-emphasis"></i>
                            <div>
                                <strong>Peringatan Dini Masa Berlaku (WARNING H-90):</strong> Masa berlaku dokumen mendekati akhir. Segera inisiasi perpanjangan atau adendum ke status <strong>DRAFT</strong>.
                            </div>
                        </div>
                    @endif

                    <!-- FORM KHUSUS STATUS REVIEW (Checklist Telaah Substansi & Mutu Hukum) -->
                    @if ($doc->status === 'REVIEW')
                        @if ($canFsmEvent('VERIFY') || $canFsmEvent('RETURN_TO_DRAFT'))
                        <div class="fsm-card-elevated mb-3">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bi bi-clipboard2-check-fill fs-6"></i>
                                    <span>Penelaahan Mutu &amp; Persetujuan Pimpinan / Tim Legal</span>
                                </div>
                                <span class="badge" style="background-color: #5156be; font-size: 0.65rem;">Evaluasi Mutu &amp; IKU 6</span>
                            </div>
                            <div class="card-body">
                                <div class="text-muted mb-2" style="font-size: 0.74rem;">
                                    Pimpinan Rektorat &amp; Tim Legal menelaah kepatuhan Tridharma, keadilan klausul hukum, proyeksi capaian IKU 6, serta mengonfirmasi Nomor Naskah resmi UNIPAR sebelum naskah dibekukan untuk penandatanganan fisik.
                                </div>

                                <!-- ALOKASI / KONFIRMASI NOMOR NASKAH RESMI UNIPAR -->
                                <div class="mb-3 p-2.5 rounded border bg-light">
                                    <label class="form-label fw-bold text-dark mb-1 d-flex justify-content-between align-items-center" style="font-size: 0.75rem;">
                                        <span><i class="bi bi-hash text-primary me-1"></i> Nomor Naskah Resmi UNIPAR (Pihak I):</span>
                                        <span class="badge bg-white text-secondary border fw-normal">Diterbitkan Tata Usaha / Rektorat</span>
                                    </label>
                                    <input type="text" name="doc_number" class="form-control form-control-sm font-monospace bg-white" 
                                           value="{{ (!empty($doc->doc_number) && $doc->doc_number !== '-') ? $doc->doc_number : '' }}" 
                                           placeholder="Contoh: 045/UNIPAR/MoA/X/2026 (Kosongkan jika tetap menggunakan nomor draf)">
                                    <div class="text-muted mt-1" style="font-size: 0.68rem;">
                                        Nomor naskah resmi ini akan dicetak pada lembar fisik sebelum proses penandatanganan basah oleh Pimpinan.
                                    </div>
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
                                            <span class="text-muted" style="font-size: 0.68rem;">Hak mahasiswa dan dosen terlindungi, kepemilikan HAKI jelas, serta tidak membebani anggaran sepihak.</span>
                                        </div>
                                    </label>
                                    <label class="list-group-item d-flex align-items-start gap-2 py-2 px-3 bg-light-subtle">
                                        <input class="form-check-input flex-shrink-0 mt-1" type="checkbox" name="iku6_benefit" value="1" checked>
                                        <div>
                                            <strong class="d-block text-dark" style="font-size: 0.75rem;">3. Kontribusi Manfaat Nyata Capaian IKU 6 Perguruan Tinggi</strong>
                                            <span class="text-muted" style="font-size: 0.68rem;">Mendukung rekognisi SKS, praktisi mengajar, sertifikat kompetensi mahasiswa, atau publikasi.</span>
                                        </div>
                                    </label>
                                </div>
                                <small class="text-muted d-block" style="font-size: 0.7rem;">
                                    <i class="bi bi-info-circle text-primary me-1"></i> Persetujuan draf akan membekukan teks (Freeze Text). Penandatanganan fisik dilakukan pada status <strong>VERIFIED</strong>.
                                </small>
                            </div>
                        </div>
                        @else
                        <div class="alert alert-info py-2 px-3 small mb-3 d-flex align-items-center gap-2">
                            <i class="bi bi-shield-lock text-primary fs-5"></i>
                            <div>
                                <strong>Menunggu Penelaahan &amp; Persetujuan:</strong> Peran aktif Anda (<code>{{ $activeRole }}</code>) hanya dapat melihat status. Persetujuan/pengembalian pada tahap <strong>REVIEW</strong> dilakukan oleh peran <strong>LEADER</strong> atau <strong>LEGAL</strong>.
                            </div>
                        </div>
                        @endif
                    @endif

                    <!-- FORM KHUSUS STATUS VERIFIED (Pengesahan Fisik: Nomor Mitra, Tanggal Mulai, Scan PDF Asli) -->
                    @if ($doc->status === 'VERIFIED')
                        @if ($canFsmEvent('ACTIVATE'))
                        @php
                            $calcEndDate = null;
                            if (!empty($doc->end_date)) {
                                $calcEndDate = \Carbon\Carbon::parse($doc->end_date)->format('Y-m-d');
                            } else {
                                $stDate = $doc->start_date ? \Carbon\Carbon::parse($doc->start_date) : now();
                                preg_match('/(\d+)/', (string)($doc->validity ?: '3'), $vm);
                                $yrs = isset($vm[1]) ? (int)$vm[1] : 3;
                                $calcEndDate = $stDate->copy()->addYears($yrs)->format('Y-m-d');
                            }
                        @endphp
                        <div class="fsm-card-elevated mb-3">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bi bi-pen-fill fs-6"></i>
                                    <span>Pengesahan Naskah Fisik &amp; Aktivasi (Pimpinan Rektorat)</span>
                                </div>
                                <span class="badge" style="background-color: #5156be; font-size: 0.65rem;">Pengesahan Sah (ACTIVATE)</span>
                            </div>
                            <div class="card-body">
                                <div class="text-muted mb-2" style="font-size: 0.74rem;">
                                    Setelah naskah ditandatangani basah oleh Rektor dan mitra serta dibubuhi meterai fisik Rp10.000, lengkapi nomor naskah Pihak II (Mitra) dan unggah berkas scan untuk aktivasi.
                                </div>

                                <!-- CHECKLIST KEPATUHAN PENGESAHAN FISIK -->
                                <div class="mb-3">
                                    <div class="fw-bold text-dark mb-1 d-flex align-items-center gap-1" style="font-size: 0.75rem;">
                                        <i class="bi bi-shield-check text-primary"></i> Checklist Kepatuhan Berkas Fisik:
                                    </div>
                                    <div class="list-group list-group-flush border rounded">
                                        <label class="list-group-item d-flex align-items-start gap-2 py-2 px-3 bg-light-subtle">
                                            <input class="form-check-input flex-shrink-0 mt-0.5" type="checkbox" name="physical_signed" value="1" checked required>
                                            <div>
                                                <strong class="d-block text-dark" style="font-size: 0.74rem;">1. Penandatanganan Basah Kedua Belah Pihak</strong>
                                                <span class="text-muted" style="font-size: 0.68rem;">Naskah fisik telah ditandatangani asli oleh pejabat berwenang dari UNIPAR dan pihak Mitra.</span>
                                            </div>
                                        </label>
                                        <label class="list-group-item d-flex align-items-start gap-2 py-2 px-3 bg-light-subtle">
                                            <input class="form-check-input flex-shrink-0 mt-0.5" type="checkbox" name="physical_stamp_meterai" value="1" checked required>
                                            <div>
                                                <strong class="d-block text-dark" style="font-size: 0.74rem;">2. Pembubuhan Meterai Sah (Rp10.000) &amp; Cap Stempel Resmi</strong>
                                                <span class="text-muted" style="font-size: 0.68rem;">Naskah telah dibubuhi meterai fisik/e-Meterai dan cap stempel basah resmi dari kedua lembaga.</span>
                                            </div>
                                        </label>
                                    </div>
                                </div>

                                <div class="row g-2 mb-2">
                                    <div class="col-sm-6">
                                        <label class="form-label fw-bold text-dark mb-1" style="font-size: 0.75rem;">Nomor Naskah Pihak I (UNIPAR):</label>
                                        <input type="text" name="doc_number" class="form-control form-control-sm bg-white font-monospace" value="{{ $doc->doc_number }}">
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label fw-bold text-dark mb-1" style="font-size: 0.75rem;">Nomor Naskah Pihak II (Mitra): <span class="text-danger">*</span></label>
                                        <input type="text" name="partner_doc_number" class="form-control form-control-sm bg-white font-monospace" value="{{ $doc->partner_doc_number }}" placeholder="Nomor resmi dari mitra" required>
                                        <div class="text-muted mt-0.5" style="font-size: 0.68rem;">Ketik '-' jika instansi mitra tidak menerbitkan nomor naskah internal.</div>
                                    </div>
                                </div>
                                <div class="row g-2 mb-2">
                                    <div class="col-sm-4">
                                        <label class="form-label fw-bold text-dark mb-1" style="font-size: 0.75rem;">
                                            <i class="bi bi-calendar-event text-primary me-1"></i> Tanggal Mulai: <span class="text-danger">*</span>
                                        </label>
                                        <input type="date" name="start_date" id="modal_start_date_{{ $doc->id }}" class="form-control form-control-sm bg-white" value="{{ $doc->start_date ? \Carbon\Carbon::parse($doc->start_date)->format('Y-m-d') : date('Y-m-d') }}" onchange="calcModalEndDate_{{ $doc->id }}()" required>
                                    </div>
                                    <div class="col-sm-4">
                                        <label class="form-label fw-bold text-dark mb-1" style="font-size: 0.75rem;">
                                            <i class="bi bi-hourglass-split text-primary me-1"></i> Masa Berlaku: <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" name="validity" id="modal_validity_{{ $doc->id }}" class="form-control form-control-sm bg-white" value="{{ $doc->validity ?: '3 Tahun' }}" placeholder="Contoh: 3 Tahun" oninput="calcModalEndDate_{{ $doc->id }}()" required>
                                    </div>
                                    <div class="col-sm-4">
                                        <label class="form-label fw-bold text-dark mb-1" style="font-size: 0.75rem;">
                                            <i class="bi bi-calendar-check text-success me-1"></i> Tanggal Berakhir:
                                        </label>
                                        <input type="date" name="end_date" id="modal_end_date_{{ $doc->id }}" class="form-control form-control-sm bg-light" value="{{ $calcEndDate }}" title="Terhitung otomatis berdasarkan tanggal mulai dan masa berlaku">
                                    </div>
                                </div>
                                <div class="row g-2 mb-2">
                                    <div class="col-sm-6">
                                        <label class="form-label fw-bold text-dark mb-1" style="font-size: 0.75rem;">
                                            <i class="bi bi-calendar-check text-primary me-1"></i> Tanggal Penandatanganan Fisik:
                                        </label>
                                        <input type="date" name="date_signed" class="form-control form-control-sm bg-white" value="{{ $doc->date_signed ? $doc->date_signed->format('Y-m-d') : date('Y-m-d') }}">
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label fw-bold text-dark mb-1" style="font-size: 0.75rem;">
                                            <i class="bi bi-file-earmark-check text-success me-1"></i> Unggah Scan TTD Basah &amp; Meterai (PDF):
                                        </label>
                                        <input type="file" name="signed_scan_file" class="form-control form-control-sm" accept=".pdf">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <script>
                            function calcModalEndDate_{{ $doc->id }}() {
                                var s = document.getElementById('modal_start_date_{{ $doc->id }}').value;
                                var v = document.getElementById('modal_validity_{{ $doc->id }}').value;
                                if (!s) return;
                                var match = v.match(/\d+/);
                                var years = match ? parseInt(match[0]) : 1;
                                var parts = s.split('-');
                                if (parts.length === 3) {
                                    var y = parseInt(parts[0]) + years;
                                    var endVal = y + '-' + parts[1] + '-' + parts[2];
                                    var endEl = document.getElementById('modal_end_date_{{ $doc->id }}');
                                    if (endEl) endEl.value = endVal;
                                }
                            }
                        </script>
                        @else
                        <div class="alert alert-info py-2 px-3 small mb-3 d-flex align-items-center gap-2">
                            <i class="bi bi-shield-lock text-primary fs-5"></i>
                            <div>
                                <strong>Menunggu Pengesahan Fisik:</strong> Peran aktif Anda (<code>{{ $activeRole }}</code>) hanya dapat melihat status. Aktivasi naskah pada tahap <strong>VERIFIED</strong> dilakukan oleh peran <strong>LEADER</strong>.
                            </div>
                        </div>
                        @endif
                    @endif

                    <!-- FORM KHUSUS STATUS WARNING / EXPIRED (Perpanjangan / Adendum) -->
                    @if (($doc->status === 'WARNING' || $doc->status === 'EXPIRED') && $canFsmEvent('RENEW'))
                        <div class="fsm-card-elevated mb-3">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bi bi-arrow-repeat fs-6"></i>
                                    <span>Inisiasi Naskah Perpanjangan / Adendum</span>
                                </div>
                                <span class="badge bg-warning text-dark border">Kembali ke DRAF</span>
                            </div>
                            <div class="card-body">
                                <p class="text-muted small mb-2" style="font-size: 0.74rem;">
                                    Perpanjangan masa aktif (adendum) akan mengembalikan naskah ke status <strong>DRAFT</strong> untuk penyesuaian klausul baru sebelum ditelaah ulang oleh Tim Legal.
                                </p>
                                <div class="row g-2 mb-2">
                                    <div class="col-sm-6">
                                        <label class="form-label fw-bold text-dark mb-1" style="font-size: 0.75rem;">Usulan Masa Berlaku Baru:</label>
                                        <input type="text" name="new_validity" class="form-control form-control-sm bg-white" value="{{ $doc->validity ? $doc->validity . ' (Diperpanjang)' : '1 Tahun Perpanjangan' }}">
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label fw-bold text-dark mb-1" style="font-size: 0.75rem;">Unggah Draf Baru (PDF / DOCX):</label>
                                        <input type="file" name="draft_file" class="form-control form-control-sm" accept=".pdf,.docx">
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- 5. Disposition / Notes Input Card Sesuai FSM-UI.html -->
                    @if ($hasAnyTransitionAuth)
                    <div class="fsm-card-elevated mb-3">
                        <div class="card-header">
                            <i class="bi bi-chat-left-text fs-6"></i>
                            <label for="catatan-disposisi-{{ $doc->id }}" class="mb-0">Catatan / Disposisi</label>
                        </div>
                        <div class="card-body p-3">
                            <div class="position-relative">
                                <textarea id="catatan-disposisi-{{ $doc->id }}" name="notes" rows="3" maxlength="500" placeholder="Tuliskan catatan, arahan, atau alasan..." class="form-control" style="border-color: #E2E4EC; font-size: 0.85rem; resize: none;" oninput="var c = document.getElementById('char-counter-{{ $doc->id }}'); if (c) c.textContent = this.value.length + ' / 500';"></textarea>
                                <span id="char-counter-{{ $doc->id }}" class="position-absolute text-muted" style="bottom: 8px; right: 12px; font-size: 0.7rem; font-family: monospace;">0 / 500</span>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- 6. Riwayat Jejak Audit (Collapsible) -->
                    <div class="mb-3">
                        <button class="btn btn-sm btn-link p-0 text-decoration-none fw-semibold d-flex align-items-center gap-1 text-muted" type="button" data-bs-toggle="collapse" data-bs-target="#auditCollapse_{{ $doc->id }}" aria-expanded="false" style="font-size: 0.78rem;">
                            <i class="bi bi-clock-history"></i>
                            <span>Riwayat Jejak Audit Resmi ({{ $doc->auditTrails->count() }} Log)</span>
                            <i class="bi bi-chevron-down" style="font-size: 0.65rem;"></i>
                        </button>
                        <div class="collapse mt-2" id="auditCollapse_{{ $doc->id }}">
                            <div class="border rounded p-2 bg-light small" style="max-height: 140px; overflow-y: auto; font-size: 0.74rem;">
                                @forelse ($doc->auditTrails->sortByDesc('id') as $audit)
                                    <div class="border-bottom pb-1 mb-1">
                                        <div class="d-flex justify-content-between text-muted" style="font-size: 0.68rem;">
                                            <span class="fw-bold text-dark">{{ $audit->actor }} [{{ $audit->role }}]</span>
                                            <span>{{ $audit->timestamp_wita }}</span>
                                        </div>
                                        <div class="text-dark">
                                            <span class="badge bg-secondary" style="font-size: 0.6rem;">{{ $audit->event }}</span>
                                            <span class="text-muted">{{ $audit->from_status }} &rarr; {{ $audit->to_status }}:</span>
                                            {{ $audit->notes }}
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-muted text-center py-1">Belum ada riwayat jejak audit.</div>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <!-- 7. Footer Action Bar (Sesuai FSM-UI.html & Terproteksi RBAC) -->
                    <div class="pt-2 border-top d-flex flex-wrap align-items-center justify-content-between gap-2">
                        <div class="small text-muted">
                            <i class="bi bi-person-badge text-primary me-1"></i>Peran Aktif: <strong class="text-dark">{{ $activeRole }}</strong>
                            @if (!$hasAnyTransitionAuth && $doc->status !== 'CANCELLED')
                                <span class="ms-1 text-warning-emphasis">(Hanya Lihat)</span>
                            @endif
                        </div>

                        <div class="d-flex flex-wrap align-items-center justify-content-end gap-2">
                            <!-- Tombol Hapus Draft / Dokumen (Destructive Button di Footer) -->
                            @if ($canDelete)
                                <button type="button" class="fsm-btn-destructive" onclick="if (confirm('KONFIRMASI PENGHAPUSAN DOKUMEN:\n\nApakah Anda yakin ingin menghapus naskah {{ $doc->doc_id }} ({{ addslashes($doc->partner_name) }}) secara permanen?\n\nPERHATIAN: Tindakan ini tidak dapat dibatalkan.')) { document.getElementById('deleteForm_{{ $doc->id }}').submit(); }">
                                    <i class="bi bi-trash fs-6"></i>
                                    <span>{{ $doc->status === 'DRAFT' ? 'Hapus Draft' : 'Hapus Dokumen' }}</span>
                                </button>
                            @endif

                            <!-- Tombol Kembalikan (Secondary Button) -->
                            @if (in_array($doc->status, ['REVIEW', 'VERIFIED']) && $canFsmEvent('RETURN_TO_DRAFT'))
                                <button type="submit" name="event" value="RETURN_TO_DRAFT" class="fsm-btn-secondary" onclick="return confirm('KONFIRMASI PENGEMBALIAN KE DRAF:\n\nApakah Anda yakin ingin mengembalikan naskah {{ $doc->doc_id }} ke status DRAFT?')">
                                    <i class="bi bi-arrow-counterclockwise fs-6"></i>
                                    <span>Kembalikan</span>
                                </button>
                            @else
                                <button type="button" class="fsm-btn-secondary" data-bs-dismiss="modal">
                                    <i class="bi bi-x-lg fs-6"></i>
                                    <span>Tutup</span>
                                </button>
                            @endif

                            <!-- Tombol Aksi Utama (Primary Button Sesuai Status FSM & RBAC) -->
                            @if ($doc->status === 'DRAFT' && $canFsmEvent('SUBMIT'))
                                <button type="submit" name="event" value="SUBMIT" class="fsm-btn-primary shadow-sm" onclick="return confirm('KONFIRMASI PENGAJUAN KE MEJA PEMERIKSAAN:\n\nAjukan naskah {{ $doc->doc_id }} ke meja pemeriksaan/telaah legal?')">
                                    <i class="bi bi-send fs-6"></i>
                                    <span>Ajukan ke Pemeriksaan</span>
                                </button>
                            @elseif ($doc->status === 'REVIEW' && $canFsmEvent('VERIFY'))
                                <button type="submit" name="event" value="VERIFY" class="fsm-btn-primary shadow-sm" onclick="return confirm('KONFIRMASI PERSETUJUAN & VERIFIKASI DRAF:\n\nNaskah telah memenuhi evaluasi mutu Tridharma & regulasi. Setujui dan bekukan teks naskah untuk proses penandatanganan fisik oleh Pimpinan?')">
                                    <i class="bi bi-patch-check-fill fs-6"></i>
                                    <span>Setujui &amp; Verifikasi Kelayakan Draf</span>
                                </button>
                            @elseif ($doc->status === 'VERIFIED' && $canFsmEvent('ACTIVATE'))
                                <button type="submit" name="event" value="ACTIVATE" class="fsm-btn-primary shadow-sm" onclick="return confirm('KONFIRMASI PENGESAHAN DOKUMEN:\n\nSahkan dan aktifkan naskah {{ $doc->doc_id }} menjadi status ACTIVE (Berlaku Sah)?')">
                                    <i class="bi bi-check-circle-fill fs-6"></i>
                                    <span>Sahkan &amp; Aktifkan</span>
                                </button>
                            @elseif (($doc->status === 'WARNING' || $doc->status === 'EXPIRED') && $canFsmEvent('RENEW'))
                                <button type="submit" name="event" value="RENEW" class="fsm-btn-primary shadow-sm">
                                    <i class="bi bi-arrow-repeat fs-6"></i>
                                    <span>Inisiasi Perpanjangan</span>
                                </button>
                            @elseif ($doc->status === 'ACTIVE')
                                @if ($canFsmEvent('RENEW'))
                                    <button type="submit" name="event" value="RENEW" class="fsm-btn-secondary" onclick="return confirm('Inisiasi adendum / perpanjangan dokumen {{ $doc->doc_id }}?\n\nStatus akan kembali ke DRAFT.')">
                                        <i class="bi bi-arrow-repeat fs-6"></i>
                                        <span>Inisiasi Adendum</span>
                                    </button>
                                @endif
                                @if (!empty($fullFsmMatrix['ACTIVE']['TRIGGER_WARNING']) && $canFsmEvent('TRIGGER_WARNING'))
                                    <button type="submit" name="event" value="TRIGGER_WARNING" class="btn btn-warning text-dark fw-bold d-inline-flex align-items-center gap-1 shadow-sm" style="border-radius: 8px; padding: 10px 16px;">
                                        <i class="bi bi-bell-fill"></i>
                                        <span>Pemeriksaan Masa Berlaku</span>
                                    </button>
                                @endif
                            @endif
                        </div>
                    </div>
                </form>

                <!-- Form Tersembunyi untuk Hapus Draft / Dokumen (Tanpa Form Bersarang) -->
                @if ($canDelete)
                    <form method="POST" action="{{ route('simkerma.delete-draft') }}" id="deleteForm_{{ $doc->id }}" class="d-none">
                        @csrf
                        <input type="hidden" name="doc_id" value="{{ $doc->doc_id }}">
                    </form>
                @endif

            </div>
        </div>
    </div>
</div>
