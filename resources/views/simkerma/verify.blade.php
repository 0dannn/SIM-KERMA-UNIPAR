@php
    $regId = $reg ?: ($document ? $document->reg_id : 'REG-UNIPAR-2026-042');
    
    $statusMap = [
        'DRAFT' => [
            'badge_text' => 'DRAFT (KONSEP AWAL)',
            'badge_class' => 'bg-secondary text-white',
            'header_title' => 'KONSEP NASKAH (DRAFT AWAL)',
            'icon' => 'bi-file-earmark-text',
            'color' => '#64748b',
            'alert_class' => 'alert-secondary',
            'alert_heading' => 'Draf Masih Dalam Tahap Penyusunan',
            'alert_desc' => 'Dokumen ini masih berupa usulan konsep awal dari Unit Pengusul dan belum diajukan ke tahapan telaah legal resmi. Dokumen belum memiliki kekuatan hukum.'
        ],
        'REVIEW' => [
            'badge_text' => 'SEDANG DI-REVIEW (MEJA PEMERIKSAAN PIMPINAN)',
            'badge_class' => 'bg-info text-dark',
            'header_title' => 'DALAM TELAAH SUBSTANSI & MANFAAT (REVIEW)',
            'icon' => 'bi-hourglass-split',
            'color' => '#0284c7',
            'alert_class' => 'alert-info',
            'alert_heading' => 'Naskah Draf Sedang Ditelaah Pimpinan Rektorat',
            'alert_desc' => 'Dokumen draf kemitraan ini telah disematkan stempel QR Code resmi dan sedang dalam penelaahan substansi & manfaat oleh pimpinan Universitas Parna Raya.'
        ],
        'LEGAL_REVIEW' => [
            'badge_text' => 'SEDANG DI-REVIEW (DALAM TELAAH HUKUM)',
            'badge_class' => 'bg-info text-dark',
            'header_title' => 'DALAM PROSES TELAAH HUKUM (LEGAL REVIEW)',
            'icon' => 'bi-hourglass-split',
            'color' => '#0284c7',
            'alert_class' => 'alert-info',
            'alert_heading' => 'Naskah Draf Sedang Diperiksa Tim Legal',
            'alert_desc' => 'Dokumen usulan kemitraan ini sedang dalam antrean pemeriksaan kepatuhan hukum dan klausul oleh Tim Telaah Legal Universitas Parna Raya. Dokumen BELUM DISETUJUI dan BELUM DAPAT DITANDATANGANI oleh para pihak.'
        ],
        'REVISION' => [
            'badge_text' => 'PERBAIKAN KLAUSUL (REVISION)',
            'badge_class' => 'bg-warning text-dark',
            'header_title' => 'DALAM PERBAIKAN KLAUSUL (REVISION)',
            'icon' => 'bi-arrow-repeat',
            'color' => '#d97706',
            'alert_class' => 'alert-warning',
            'alert_heading' => 'Naskah Memerlukan Perbaikan Redaksional',
            'alert_desc' => 'Tim Legal telah mengembalikan naskah kepada Unit Pengusul dengan catatan telaah klausul hukum. Naskah wajib diajukan ulang setelah perbaikan selesai.'
        ],
        'VERIFIED' => [
            'badge_text' => 'TERVERIFIKASI LEGAL (SIAP PENANDATANGANAN FISIK)',
            'badge_class' => 'bg-primary text-white',
            'header_title' => 'DRAF RESMI TELAH DIVERIFIKASI (FREEZE TEXT)',
            'icon' => 'bi-file-earmark-check-fill',
            'color' => '#2563eb',
            'alert_class' => 'alert-primary',
            'alert_heading' => 'Naskah Draf Lolos Verifikasi & Teks Dikunci',
            'alert_desc' => 'Pemeriksaan hukum & telaah kelayakan telah disetujui oleh Tim Legal. Teks naskah telah dibekukan (Freeze Text). Naskah fisik cetak ber-QR code ini siap ditandatangani basah dan dibubuhi meterai Rp10.000 oleh Rektor dan Pimpinan Mitra.'
        ],
        'APPROVED' => [
            'badge_text' => 'TERVERIFIKASI LEGAL (SIAP PENANDATANGANAN FISIK)',
            'badge_class' => 'bg-primary text-white',
            'header_title' => 'DRAF RESMI TELAH DISETUJUI (FREEZE TEXT)',
            'icon' => 'bi-file-earmark-check-fill',
            'color' => '#2563eb',
            'alert_class' => 'alert-primary',
            'alert_heading' => 'Naskah Draf Disetujui & Teks Dikunci',
            'alert_desc' => 'Pemeriksaan hukum telah selesai dan disetujui. Teks naskah telah dibekukan (Freeze Text). Naskah fisik cetak ber-QR code ini siap ditandatangani basah dan ditempeli meterai di hadapan pimpinan kedua belah pihak.'
        ],
        'ACTIVE' => [
            'badge_text' => 'AKTIF / SAH (BERLAKU RESMI & BERMETERAI)',
            'badge_class' => 'bg-success text-white',
            'header_title' => 'DOKUMEN RESMI SAH & BERMETERAI (ACTIVE)',
            'icon' => 'bi-patch-check-fill',
            'color' => '#059669',
            'alert_class' => 'alert-success',
            'alert_heading' => 'Dokumen Sah Berlaku Secara Legal',
            'alert_desc' => 'Eksemplar fisik asli bertanda tangan basah dan bermeterai sah telah diverifikasi keabsahannya serta tersimpan resmi dalam pangkalan data SIMKERMA Universitas Parna Raya.'
        ],
        'WARNING' => [
            'badge_text' => 'PERINGATAN MASA BERLAKU (' . strtoupper($document->warning_threshold ?? 'H-90 HARI') . ')',
            'badge_class' => 'bg-warning text-dark',
            'header_title' => 'PERINGATAN DINI: MASA BERLAKU HAMPIR HABIS (' . ($document->warning_threshold ?? 'H-90 HARI') . ')',
            'icon' => 'bi-exclamation-triangle-fill',
            'color' => '#ea580c',
            'alert_class' => 'alert-warning',
            'alert_heading' => 'Peringatan Masa Berlaku Kemitraan',
            'alert_desc' => 'Kerja sama masih aktif berlaku sah, namun sisa masa berlaku telah memasuki ambang batas peringatan dini (' . ($document->warning_threshold ?? 'H-90 Hari / 3 Bulan') . '). Diimbau segera mengambil keputusan perpanjangan, pengubahan, atau penghentian sesuai perjanjian.'
        ],
        'WARNING_EXPIRY' => [
            'badge_text' => 'PERINGATAN MASA BERLAKU (' . strtoupper($document->warning_threshold ?? 'H-90 HARI') . ')',
            'badge_class' => 'bg-warning text-dark',
            'header_title' => 'PERINGATAN DINI: MASA BERLAKU HAMPIR HABIS (' . ($document->warning_threshold ?? 'H-90 HARI') . ')',
            'icon' => 'bi-exclamation-triangle-fill',
            'color' => '#ea580c',
            'alert_class' => 'alert-warning',
            'alert_heading' => 'Peringatan Masa Berlaku Kemitraan',
            'alert_desc' => 'Kerja sama masih aktif berlaku sah, namun sisa masa berlaku telah memasuki ambang batas peringatan dini (' . ($document->warning_threshold ?? 'H-90 Hari / 3 Bulan') . '). Diimbau segera mengambil keputusan perpanjangan, pengubahan, atau penghentian sesuai Bab IV perjanjian.'
        ],
        'EXPIRED' => [
            'badge_text' => 'KEDALUWARSA (EXPIRED)',
            'badge_class' => 'bg-danger text-white',
            'header_title' => 'MASA BERLAKU TELAH BERAKHIR (EXPIRED)',
            'icon' => 'bi-x-octagon-fill',
            'color' => '#dc2626',
            'alert_class' => 'alert-danger',
            'alert_heading' => 'Perjanjian Kerja Sama Telah Kedaluwarsa',
            'alert_desc' => 'Masa perjanjian kerja sama ini telah habis secara formal dan tidak lagi memiliki kekuatan hukum operasional. Hubungan kemitraan dapat dipulihkan melalui alur penerbitan Adendum baru.'
        ],
        'ADDENDUM' => [
            'badge_text' => 'PROSES ADENDUM (PEMBARUAN)',
            'badge_class' => 'bg-primary text-white',
            'header_title' => 'DALAM PROSES PENERBITAN ADENDUM',
            'icon' => 'bi-arrow-clockwise',
            'color' => '#4f46e5',
            'alert_class' => 'alert-info',
            'alert_heading' => 'Inisiasi Perpanjangan Kerja Sama',
            'alert_desc' => 'Naskah adendum untuk memperpanjang durasi atau menyesuaikan klausul kemitraan sedang dalam tahapan penyusunan dan penelaahan resmi.'
        ],
        'CANCELLED' => [
            'badge_text' => 'DIBATALKAN (CANCELLED)',
            'badge_class' => 'bg-dark text-white',
            'header_title' => 'USULAN KERJA SAMA DIBATALKAN',
            'icon' => 'bi-slash-circle-fill',
            'color' => '#334155',
            'alert_class' => 'alert-dark',
            'alert_heading' => 'Proses Kemitraan Dihentikan Resmi',
            'alert_desc' => 'Proses usulan kerja sama ini telah dibatalkan atau ditolak secara resmi oleh pejabat berwenang dan berkas dinyatakan tidak berlaku.'
        ]
    ];

    $st = $document ? $document->status : 'LEGAL_REVIEW';
    $info = $statusMap[$st] ?? $statusMap['LEGAL_REVIEW'];
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Keaslian Dokumen - SIMKERMA UNIPAR</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { font-family: 'Public Sans', sans-serif; background-color: #f1f5f9; color: #1e293b; }
        .verify-card { border-radius: 12px; box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.08); border: 1px solid #cbd5e1; }
        .brand-header { background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); border-top-left-radius: 12px; border-top-right-radius: 12px; }
        .badge-verified { background-color: #ecfdf5; color: #065f46; border: 1px solid #a7f3d0; font-size: 0.82rem; padding: 6px 14px; border-radius: 20px; font-weight: 700; }
        .field-label { font-size: 0.74rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: #64748b; }
        .field-val { font-weight: 600; color: #0f172a; }
        @media print {
            body { background-color: #fff; padding: 0 !important; }
            .no-print { display: none !important; }
            .verify-card { box-shadow: none; border: 1px solid #000; }
        }
    </style>
</head>
<body class="py-4 py-md-5">
    <div class="container" style="max-width: 760px;">
        <div class="card verify-card bg-white overflow-hidden">
            <div class="brand-header p-4 text-white text-center">
                <div class="d-inline-flex align-items-center justify-content-center bg-warning text-dark rounded-circle p-2 mb-2" style="width: 48px; height: 48px;">
                    <i class="bi bi-mortarboard-fill fs-4"></i>
                </div>
                <h5 class="mb-0 fw-bold">UNIVERSITAS PARNA RAYA (UNIPAR)</h5>
                <div class="small text-white-50">SISTEM INFORMASI DOKUMEN KERJA SAMA (SIMKERMA)</div>
                <div class="mt-2">
                    <span class="badge bg-warning text-dark px-3 py-1 fw-bold">LEMBAR VERIFIKASI RESMI DOKUMEN</span>
                </div>
            </div>

            <div class="card-body p-4 p-md-5">
                
                <!-- Lencana & Status Real-Time -->
                <div class="text-center mb-4">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle p-3 mb-2" style="width: 72px; height: 72px; background-color: {{ $info['color'] }}1a; color: {{ $info['color'] }};">
                        <i class="bi {{ $info['icon'] }}" style="font-size: 2.5rem;"></i>
                    </div>
                    <h4 class="fw-bold text-dark mb-1">{{ $info['header_title'] }}</h4>
                    <div class="mb-2">
                        <span class="badge {{ $info['badge_class'] }} px-3 py-2 fs-6 fw-bold shadow-sm">
                            {{ $info['badge_text'] }}
                        </span>
                    </div>
                    <div class="badge-verified d-inline-flex align-items-center gap-1 mt-1">
                        <i class="bi bi-shield-fill-check"></i> NOMOR REGISTRASI RESMI: {{ $document ? $document->reg_id : $regId }}
                    </div>
                </div>

                <!-- Kotak Pemberitahuan Status Nyata -->
                <div class="alert {{ $info['alert_class'] }} py-3 px-3 small border mb-4">
                    <div class="fw-bold mb-1"><i class="bi bi-info-circle-fill me-1"></i> {{ $info['alert_heading'] }}:</div>
                    <div>{{ $info['alert_desc'] }}</div>
                </div>

                @if ($document)
                <!-- Data Rincian Dokumen Resmi Termasuk Dokumen Induk Rujukan -->
                <div class="bg-light p-3 p-md-4 rounded border mb-4">
                    <div class="row g-3 small">
                        @if (!empty($document->title))
                        <div class="col-12">
                            <div class="field-label">Judul / Nama Kerja Sama:</div>
                            <div class="field-val fs-6 text-primary fw-bold">{{ $document->title }}</div>
                        </div>
                        @endif
                        <div class="col-sm-6 {{ !empty($document->title) ? 'border-top pt-2' : '' }}">
                            <div class="field-label">Nomor Registrasi Sistem:</div>
                            <div class="field-val fs-6 text-primary">{{ $document->reg_id }}</div>
                        </div>
                        <div class="col-sm-6 {{ !empty($document->title) ? 'border-top pt-2' : '' }}">
                            <div class="field-label">Nomor Surat Pihak I (UNIPAR):</div>
                            <div class="field-val">{{ $document->doc_number ?: 'Dalam Proses Registrasi' }}</div>
                        </div>
                        <div class="col-sm-6 border-top pt-2">
                            <div class="field-label">Nomor Surat Pihak II (Mitra):</div>
                            <div class="field-val">
                                @if (!empty($document->partner_doc_number))
                                    <span class="text-dark fw-bold">{{ $document->partner_doc_number }}</span>
                                @else
                                    <span class="text-muted">- (Belum Dicatatkan)</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-sm-6 border-top pt-2">
                            <div class="field-label">Jenis Dokumen:</div>
                            <div class="field-val">{{ $document->doc_type }}</div>
                        </div>
                        <div class="col-sm-6 border-top pt-2">
                            <div class="field-label">Jalur Pengusulan:</div>
                            <div class="field-val">{{ $document->track }}</div>
                        </div>
                        <div class="col-sm-6 border-top pt-2">
                            <div class="field-label">Dokumen Induk Rujukan:</div>
                            <div class="field-val">
                                @if (!empty($document->parent_mou) && $document->parent_mou !== '-')
                                    <span class="text-primary"><i class="bi bi-link-45deg me-1"></i>{{ $document->parent_mou }}</span>
                                @else
                                    <span class="text-muted">- (Dokumen Payung Utama)</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-sm-6 border-top pt-2">
                            <div class="field-label">Masa Berlaku Perjanjian:</div>
                            <div class="field-val">
                                @if (!empty($document->start_date) && !empty($document->end_date))
                                    {{ $document->start_date->format('d M Y') }} s/d {{ $document->end_date->format('d M Y') }} ({{ $document->validity }})
                                @else
                                    {{ $document->validity ?: 'Belum Ditetapkan' }}
                                @endif
                            </div>
                        </div>
                        <div class="col-sm-6 border-top pt-2">
                            <div class="field-label">Ambang Batas Peringatan Dini:</div>
                            <div class="field-val text-warning text-dark"><i class="bi bi-bell-fill me-1"></i>{{ $document->warning_threshold ?: 'H-90 Hari (3 Bulan)' }}</div>
                        </div>
                        @if (!empty($document->nominal_value))
                        <div class="col-12 border-top pt-2">
                            <div class="field-label">Nilai / Nominal Dana Kerja Sama:</div>
                            <div class="field-val text-success fw-bold">{{ $document->nominal_value }}</div>
                        </div>
                        @endif
                        <div class="col-12 border-top pt-2">
                            <div class="field-label">Ruang Lingkup / Perihal:</div>
                            <div class="field-val fs-6 text-dark">{{ $document->scope }}</div>
                        </div>
                        @if (!empty($document->description))
                        <div class="col-12 border-top pt-2">
                            <div class="field-label">Deskripsi / Ringkasan:</div>
                            <div class="field-val text-secondary">{{ $document->description }}</div>
                        </div>
                        @endif
                        <div class="col-sm-6 border-top pt-2">
                            <div class="field-label">Nama Instansi / Mitra (Pihak II):</div>
                            <div class="field-val fw-bold text-dark">{{ $document->partner_name }}</div>
                            <div class="text-muted" style="font-size: 0.74rem;">{{ $document->partner_category }}</div>
                            @if (!empty($document->second_party_signer))
                                <div class="text-secondary small mt-1">Penandatangan: {{ $document->second_party_signer }}</div>
                            @endif
                            @if (!empty($document->pic_second_party))
                                <div class="text-muted small">PIC: {{ $document->pic_second_party }}</div>
                            @endif
                        </div>
                        <div class="col-sm-6 border-top pt-2">
                            <div class="field-label">Unit Pengusul Kampus (Pihak I):</div>
                            <div class="field-val fw-bold text-dark">{{ $document->unit }}</div>
                            @if (!empty($document->collab_unit))
                                <div class="text-muted" style="font-size: 0.74rem;">Kolaborator: {{ $document->collab_unit }}</div>
                            @endif
                            @if (!empty($document->first_party_signer))
                                <div class="text-secondary small mt-1">Penandatangan: {{ $document->first_party_signer }}</div>
                            @endif
                            @if (!empty($document->pic_first_party))
                                <div class="text-muted small">PIC: {{ $document->pic_first_party }}</div>
                            @endif
                        </div>
                        <div class="col-12 border-top pt-2">
                            <div class="field-label">Status Fisik &amp; Tanda Tangan:</div>
                            <div class="field-val">
                                @if ($document->status === 'ACTIVE')
                                    <span class="text-success"><i class="bi bi-check-circle-fill me-1"></i> Sah, Bertanda Tangan Basah &amp; Bermeterai</span>
                                @elseif ($document->status === 'VERIFIED' || $document->status === 'APPROVED')
                                    <span class="text-primary"><i class="bi bi-patch-check-fill me-1"></i> Lolos Verifikasi Legal (Menunggu TTD Basah &amp; Meterai Fisik)</span>
                                @elseif ($document->status === 'WARNING')
                                    <span class="text-warning-emphasis"><i class="bi bi-exclamation-triangle-fill me-1"></i> Sah Aktif (Peringatan Dini Masa Berlaku)</span>
                                @elseif ($document->status === 'EXPIRED')
                                    <span class="text-danger"><i class="bi bi-x-circle-fill me-1"></i> Kedaluwarsa (Masa Berlaku Habis)</span>
                                @elseif ($document->status === 'DRAFT')
                                    <span class="text-secondary"><i class="bi bi-pencil me-1"></i> Konsep Awal Draf</span>
                                @else
                                    <span class="text-secondary"><i class="bi bi-hourglass me-1"></i> Draf Dalam Pemeriksaan Legal</span>
                                @endif
                            </div>
                        </div>

                        @if (!empty($document->docx_path) || !empty($document->draft_file))
                        <div class="col-12 border-top pt-2">
                            <div class="field-label">Naskah Resmi Dokumen Kerja Sama:</div>
                            <div class="d-flex flex-wrap gap-2 mt-2">
                                @if (!empty($document->docx_path) && file_exists(public_path($document->docx_path)))
                                    <a href="{{ asset($document->docx_path) }}" class="btn btn-outline-primary btn-sm py-1 px-3 fw-bold shadow-sm" download>
                                        <i class="bi bi-file-earmark-word-fill me-1 text-primary"></i> Unduh Naskah Word Ber-QR Code (.docx)
                                    </a>
                                @endif
                                @if (!empty($document->draft_file) && file_exists(public_path($document->draft_file)))
                                    <a href="{{ asset($document->draft_file) }}" target="_blank" class="btn btn-outline-danger btn-sm py-1 px-3 fw-bold shadow-sm">
                                        <i class="bi bi-file-earmark-pdf-fill me-1 text-danger"></i> Buka / Unduh Naskah Resmi (.pdf)
                                    </a>
                                @endif
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Dokumen Turunan Terkait (Hierarki MoA & IA IKU 6) -->
                @if ($childDerivatives->isNotEmpty())
                <div class="card border mb-4 bg-light">
                    <div class="card-header bg-white py-2 px-3 fw-bold small text-uppercase">
                        <i class="bi bi-diagram-3-fill text-primary me-1"></i> Dokumen Turunan Terkait (MoA &amp; IA Kemitraan Ini):
                    </div>
                    <div class="card-body p-2">
                        <div class="list-group list-group-flush small">
                            @foreach ($childDerivatives as $cItem)
                                <div class="list-group-item d-flex justify-content-between align-items-center bg-transparent py-2">
                                    <div>
                                        <span class="badge bg-dark me-1">{{ $cItem->doc_type }}</span>
                                        <strong>{{ $cItem->doc_number ?: $cItem->doc_id }}</strong>
                                        <div class="text-muted" style="font-size: 0.72rem;">{{ $cItem->unit }} &bull; {{ $cItem->scope }}</div>
                                    </div>
                                    <span class="badge bg-secondary">{{ $cItem->status }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif

                <!-- Jejak Audit Resmi (Immutable Audit Trail) -->
                <div class="mb-4">
                    <h6 class="fw-bold small text-uppercase text-muted mb-2">
                        <i class="bi bi-clock-history me-1"></i> Jejak Audit Resmi (Immutable Audit Trail):
                    </h6>
                    <div class="border rounded p-2 bg-white small" style="max-height: 180px; overflow-y: auto;">
                        @if ($document->auditTrails->isNotEmpty())
                            <ul class="list-unstyled mb-0">
                                @foreach ($document->auditTrails as $audit)
                                    <li class="pb-2 mb-2 border-bottom">
                                        <div class="d-flex justify-content-between text-muted" style="font-size: 0.72rem;">
                                            <span class="fw-bold text-dark">{{ $audit->actor }} ({{ $audit->role }})</span>
                                            <span>{{ $audit->timestamp_wita }}</span>
                                        </div>
                                        <div style="font-size: 0.78rem;">
                                            <span class="badge bg-secondary" style="font-size: 0.65rem;">{{ $audit->event }}</span>
                                            <span class="text-muted">{{ $audit->from_status }} &rarr; {{ $audit->to_status }}:</span>
                                            {{ $audit->notes }}
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <div class="text-muted text-center py-2">Belum ada riwayat audit trail.</div>
                        @endif
                    </div>
                </div>

                @else
                <div class="alert alert-warning py-3 text-center mb-4">
                    <i class="bi bi-exclamation-triangle-fill fs-3 d-block text-warning mb-2"></i>
                    <strong>Dokumen Tidak Ditemukan</strong>
                    <div class="small">Nomor registrasi <code>{{ $regId }}</code> tidak terdaftar dalam pangkalan data resmi SIMKERMA.</div>
                </div>
                @endif

                <!-- Tindakan Halaman -->
                <div class="d-flex flex-column flex-sm-row justify-content-between align-items-center gap-2 pt-2 border-top no-print">
                    <a href="{{ route('simkerma.index', ['menu' => 4]) }}" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-arrow-left me-1"></i> Kembali ke SIMKERMA
                    </a>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-primary btn-sm fw-bold px-3" onclick="window.print()">
                            <i class="bi bi-printer me-1"></i> Cetak Lembar Verifikasi
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </div>
</body>
</html>
