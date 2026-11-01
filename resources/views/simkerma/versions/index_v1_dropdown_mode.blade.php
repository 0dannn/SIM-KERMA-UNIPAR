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
        
        <!-- 4 KARTU RINGKASAN STATUS KERJA SAMA -->
        <div class="row g-3 mb-4">
            <div class="col-sm-6 col-xl-3">
                <div class="card card-wireframe h-100 p-3 border-top border-3 border-dark">
                    <span class="text-muted small text-uppercase fw-semibold">Total Kerja Sama</span>
                    <div class="d-flex align-items-center justify-content-between mt-2">
                        <h2 class="fw-bold mb-0 text-dark">128</h2>
                        <span class="bg-light p-2 rounded text-dark fs-5"><i class="bi bi-folder2"></i></span>
                    </div>
                    <small class="text-muted mt-2">Seluruh Dokumen Kemitraan</small>
                </div>
            </div>

            <div class="col-sm-6 col-xl-3">
                <div class="card card-wireframe h-100 p-3 border-top border-3 border-success">
                    <span class="text-muted small text-uppercase fw-semibold">Kerja Sama Aktif</span>
                    <div class="d-flex align-items-center justify-content-between mt-2">
                        <h2 class="fw-bold mb-0 text-success">84</h2>
                        <span class="badge badge-status-active fs-6 py-2 px-3">Aktif</span>
                    </div>
                    <small class="text-muted mt-2">Berlaku Sah di Kampus</small>
                </div>
            </div>

            <div class="col-sm-6 col-xl-3">
                <div class="card card-wireframe h-100 p-3 border-top border-3 border-primary">
                    <span class="text-muted small text-uppercase fw-semibold">Dalam Proses (Pipeline)</span>
                    <div class="d-flex align-items-center justify-content-between mt-2">
                        <h2 class="fw-bold mb-0 text-primary">26</h2>
                        <span class="badge badge-status-process fs-6 py-2 px-3">Proses</span>
                    </div>
                    <small class="text-muted mt-2">Draf &amp; Verifikasi</small>
                </div>
            </div>

            <div class="col-sm-6 col-xl-3">
                <div class="card card-wireframe h-100 p-3 border-top border-3 border-danger">
                    <span class="text-muted small text-uppercase fw-semibold">Berakhir / Expired</span>
                    <div class="d-flex align-items-center justify-content-between mt-2">
                        <h2 class="fw-bold mb-0 text-danger">18</h2>
                        <span class="badge badge-status-expired fs-6 py-2 px-3">Selesai</span>
                    </div>
                    <small class="text-muted mt-2">Perlu Evaluasi &amp; Adendum</small>
                </div>
            </div>
        </div>

        <!-- 2 KOLOM: MITRA KATEGORI & DISTRIBUSI UNIT -->
        <div class="row g-4 mb-4">
            <!-- Jumlah Mitra Berdasarkan Kategori -->
            <div class="col-lg-6">
                <div class="card card-wireframe h-100">
                    <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                        <h6 class="card-title mb-0 fw-bold text-uppercase">Jumlah Mitra Berdasarkan Kategori</h6>
                        <span class="tag-wireframe">KATEGORI MITRA</span>
                    </div>
                    <div class="card-body p-0">
                        <ul class="list-group list-group-flush small">
                            <li class="list-group-item d-flex justify-content-between align-items-center py-2 px-3">
                                <span><i class="bi bi-briefcase me-2 text-muted"></i> Dunia Usaha &amp; Dunia Industri (DUDI)</span>
                                <span class="badge bg-dark rounded-pill">42 Mitra</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center py-2 px-3">
                                <span><i class="bi bi-bank me-2 text-muted"></i> Instansi Pemerintah (Kementerian / Pemda)</span>
                                <span class="badge bg-dark rounded-pill">28 Mitra</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center py-2 px-3">
                                <span><i class="bi bi-mortarboard me-2 text-muted"></i> Perguruan Tinggi Lain (Akademik)</span>
                                <span class="badge bg-dark rounded-pill">25 Mitra</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center py-2 px-3">
                                <span><i class="bi bi-hospital me-2 text-muted"></i> Rumah Sakit &amp; Lembaga Kesehatan</span>
                                <span class="badge bg-dark rounded-pill">14 Mitra</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center py-2 px-3">
                                <span><i class="bi bi-book me-2 text-muted"></i> Sekolah &amp; Lembaga Pendidikan</span>
                                <span class="badge bg-dark rounded-pill">11 Mitra</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center py-2 px-3">
                                <span><i class="bi bi-compass me-2 text-muted"></i> Lembaga Riset &amp; Organisasi Lainnya</span>
                                <span class="badge bg-dark rounded-pill">8 Mitra</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Kerja Sama Berdasarkan Fakultas, UPPS, Program Studi, & Unit Pengusul -->
            <div class="col-lg-6">
                <div class="card card-wireframe h-100">
                    <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                        <h6 class="card-title mb-0 fw-bold text-uppercase">Distribusi Fakultas, UPPS, &amp; Unit Pengusul</h6>
                        <span class="tag-wireframe">UNIT PENGUSUL</span>
                    </div>
                    <div class="card-body p-0">
                        <ul class="list-group list-group-flush small">
                            <li class="list-group-item d-flex justify-content-between align-items-center py-2 px-3">
                                <span>Fakultas Ilmu Komputer (FASILKOM / Prodi TI)</span>
                                <span class="fw-bold">38 Dokumen (MoA &amp; IA)</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center py-2 px-3">
                                <span>Fakultas Ekonomi &amp; Bisnis</span>
                                <span class="fw-bold">31 Dokumen (MoA &amp; IA)</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center py-2 px-3">
                                <span>Fakultas Teknik</span>
                                <span class="fw-bold">26 Dokumen (MoA &amp; IA)</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center py-2 px-3">
                                <span>Lembaga Penelitian &amp; Pengabdian (LPPM)</span>
                                <span class="fw-bold">18 Dokumen (MoU &amp; MoA)</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center py-2 px-3">
                                <span>Rektorat, Biro Administrasi &amp; Ormawa</span>
                                <span class="fw-bold">15 Dokumen</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2 KOLOM: SEGERA BERAKHIR & REALISASI KEGIATAN -->
        <div class="row g-4">
            <!-- Kerja Sama yang Segera Berakhir -->
            <div class="col-lg-6">
                <div class="card card-wireframe h-100">
                    <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                        <h6 class="card-title mb-0 fw-bold text-uppercase text-dark">
                            <i class="bi bi-exclamation-triangle text-warning me-1"></i> Kerja Sama Segera Berakhir
                        </h6>
                        <span class="badge badge-status-warning">H-60 HARI</span>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-wireframe table-hover align-middle mb-0 small">
                            <thead>
                                <tr>
                                    <th>Mitra</th>
                                    <th>Jenis</th>
                                    <th>Tgl Berakhir</th>
                                    <th>Sisa Waktu</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="fw-bold">Pemerintah Kota Manado</td>
                                    <td><span class="badge bg-light text-dark border">MoU</span></td>
                                    <td>30 Nov 2026</td>
                                    <td><span class="badge badge-status-warning">31 Hari</span></td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">PT Bank SulutGo</td>
                                    <td><span class="badge bg-light text-dark border">MoA</span></td>
                                    <td>15 Des 2026</td>
                                    <td><span class="badge badge-status-warning">46 Hari</span></td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">PT Telkom Indonesia</td>
                                    <td><span class="badge bg-light text-dark border">MoU</span></td>
                                    <td>28 Des 2026</td>
                                    <td><span class="badge badge-status-warning">59 Hari</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Realisasi Kegiatan, Luaran, dan Capaian Kerja Sama -->
            <div class="col-lg-6">
                <div class="card card-wireframe h-100">
                    <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                        <h6 class="card-title mb-0 fw-bold text-uppercase">Realisasi, Luaran &amp; Capaian (IKU 6)</h6>
                        <span class="tag-wireframe">CAPAIAN KINERJA</span>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <div class="d-flex justify-content-between small mb-1">
                                <span class="fw-semibold">Program Magang Mahasiswa Bersertifikat (MBKM)</span>
                                <span class="fw-bold">78% Tercapai</span>
                            </div>
                            <div class="progress" style="height: 10px;">
                                <div class="progress-bar bg-dark" style="width: 78%"></div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="d-flex justify-content-between small mb-1">
                                <span class="fw-semibold">Kuliah Tamu &amp; Dosen Praktisi Industri</span>
                                <span class="fw-bold">92% Tercapai</span>
                            </div>
                            <div class="progress" style="height: 10px;">
                                <div class="progress-bar bg-dark" style="width: 92%"></div>
                            </div>
                        </div>

                        <div>
                            <div class="d-flex justify-content-between small mb-1">
                                <span class="fw-semibold">Riset Terapan &amp; Publikasi Bersama</span>
                                <span class="fw-bold">64% Tercapai</span>
                            </div>
                            <div class="progress" style="height: 10px;">
                                <div class="progress-bar bg-dark" style="width: 64%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    {{-- ===================================================================================== --}}
    {{-- 2. PENGELOLAAN DATA MITRA                                                             --}}
    {{-- ===================================================================================== --}}
    @elseif ($activeMenu === 2)
        
        <div class="card card-wireframe">
            <div class="card-header bg-white py-3 border-bottom d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2">
                <div>
                    <h6 class="mb-0 fw-bold text-uppercase">Repositori &amp; Profil Mitra Kerja Sama</h6>
                    <small class="text-muted">Penyimpanan Terpusat Profil, Kontak, dan Riwayat Kemitraan</small>
                </div>
                <button class="btn btn-dark btn-sm fw-semibold">
                    <i class="bi bi-plus-lg me-1"></i> Tambah Mitra Baru
                </button>
            </div>

            <!-- FILTER & PENCARIAN -->
            <div class="card-body border-bottom bg-light py-3">
                <div class="row g-2">
                    <div class="col-md-4">
                        <input type="text" class="form-control form-control-sm" placeholder="Cari nama mitra / penanggung jawab...">
                    </div>
                    <div class="col-md-3">
                        <select class="form-select form-select-sm">
                            <option value="">-- Semua Jenis Mitra --</option>
                            <option>Perguruan Tinggi</option>
                            <option>Pemerintah</option>
                            <option>Industri / DUDI</option>
                            <option>Rumah Sakit</option>
                            <option>Sekolah</option>
                            <option>Lembaga Penelitian</option>
                            <option>Organisasi Lainnya</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select form-select-sm">
                            <option value="">-- Status Verifikasi Data --</option>
                            <option>Terverifikasi Lengkap</option>
                            <option>Belum Lengkap</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-outline-dark btn-sm w-100 fw-semibold">
                            <i class="bi bi-funnel me-1"></i> Filter
                        </button>
                    </div>
                </div>
            </div>

            <!-- TABEL PENGELOLAAN DATA MITRA -->
            <div class="table-responsive">
                <table class="table table-wireframe table-hover align-middle mb-0 small">
                    <thead>
                        <tr>
                            <th>Nama &amp; Profil Mitra</th>
                            <th>Jenis Mitra</th>
                            <th>Alamat, Kontak &amp; Penanggung Jawab</th>
                            <th>Lingkup Kerja Sama</th>
                            <th>Riwayat Kerja Sama</th>
                            <th>Status Verifikasi</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <div class="fw-bold text-dark">PT Industri Inovasi Digital</div>
                                <div class="text-muted small">Solusi Perangkat Lunak &amp; Kecerdasan Artifisial</div>
                            </td>
                            <td><span class="badge bg-light text-dark border">Industri / DUDI</span></td>
                            <td>
                                Jl. Boulevard No. 8 Manado<br>
                                <span class="text-muted">PIC: Bpk. Hendra S. (0812-3456-7890)</span>
                            </td>
                            <td>Magang MBKM, Riset AI, Kuliah Praktisi</td>
                            <td><span class="badge bg-dark">3 Dokumen</span> (MoU, MoA, IA)</td>
                            <td><span class="badge bg-light text-dark border fw-bold">✓ Lengkap</span></td>
                            <td class="text-end">
                                <button class="btn btn-outline-dark btn-sm py-0 px-2">Detail</button>
                                <button class="btn btn-outline-secondary btn-sm py-0 px-2">Edit</button>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="fw-bold text-dark">Pemerintah Kota Manado</div>
                                <div class="text-muted small">Pemerintah Daerah Tingkat II</div>
                            </td>
                            <td><span class="badge bg-light text-dark border">Pemerintah</span></td>
                            <td>
                                Kantor Walikota, Jl. Tikala Ares<br>
                                <span class="text-muted">PIC: Bagian Kerja Sama Pemkot</span>
                            </td>
                            <td>Smart City, Pengabdian Masyarakat, Tata Kelola</td>
                            <td><span class="badge bg-dark">2 Dokumen</span> (MoU, MoA)</td>
                            <td><span class="badge bg-light text-dark border fw-bold">✓ Lengkap</span></td>
                            <td class="text-end">
                                <button class="btn btn-outline-dark btn-sm py-0 px-2">Detail</button>
                                <button class="btn btn-outline-secondary btn-sm py-0 px-2">Edit</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

    {{-- ===================================================================================== --}}
    {{-- 3. PENGELOLAAN DOKUMEN KERJA SAMA                                                     --}}
    {{-- ===================================================================================== --}}
    @elseif ($activeMenu === 3)
        
        <div class="card card-wireframe">
            <div class="card-header bg-white py-3 border-bottom d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2">
                <div>
                    <h6 class="mb-0 fw-bold text-uppercase">Repositori Dokumen Resmi Kerja Sama</h6>
                    <small class="text-muted">Penyimpanan Terorganisasi MoU, PKS/MoA, IA, Proposal &amp; Laporan</small>
                </div>
                <button class="btn btn-dark btn-sm fw-semibold">
                    <i class="bi bi-cloud-upload me-1"></i> Unggah Dokumen Baru
                </button>
            </div>

            <!-- TAB KATEGORI DOKUMEN -->
            <div class="px-3 pt-3 bg-light border-bottom">
                <ul class="nav nav-tabs border-0 small fw-semibold">
                    <li class="nav-item">
                        <a class="nav-link active bg-white text-dark" href="#">Semua Dokumen</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-muted" href="#">MoU (Nota Kesepahaman)</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-muted" href="#">PKS (Perjanjian Kerja Sama)</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-muted" href="#">IA (Implementation Agreement)</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-muted" href="#">Proposal &amp; Rencana Kegiatan</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-muted" href="#">Dokumen Pendukung &amp; Laporan</a>
                    </li>
                </ul>
            </div>

            <!-- SEARCH BAR & FILTER -->
            <div class="card-body border-bottom py-3">
                <div class="row g-2">
                    <div class="col-md-5">
                        <input type="text" class="form-control form-control-sm" placeholder="Cari nomor dokumen / judul / nama mitra...">
                    </div>
                    <div class="col-md-3">
                        <input type="date" class="form-control form-control-sm" title="Tanggal Penandatanganan">
                    </div>
                    <div class="col-md-2">
                        <select class="form-select form-select-sm">
                            <option value="">-- Pihak TTD --</option>
                            <option>Rektor UNIPAR</option>
                            <option>Dekan Fakultas</option>
                            <option>Ketua Program Studi</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-outline-dark btn-sm w-100 fw-semibold">
                            <i class="bi bi-search me-1"></i> Cari
                        </button>
                    </div>
                </div>
            </div>

            <!-- TABEL REPOSITORI DOKUMEN -->
            <div class="table-responsive">
                <table class="table table-wireframe table-hover align-middle mb-0 small">
                    <thead>
                        <tr>
                            <th>Nomor Dokumen &amp; Jenis</th>
                            <th>Judul Kerja Sama &amp; Mitra</th>
                            <th>Tanggal TTD &amp; Masa Berlaku</th>
                            <th>Pihak Penandatangan</th>
                            <th>Berkas &amp; Riwayat Perubahan</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <div class="fw-bold text-dark">UNIPAR/MOU/2026/001</div>
                                <span class="badge bg-dark">MoU</span>
                            </td>
                            <td>
                                <strong class="text-dark">Nota Kesepahaman Kemitraan Bidang Tridharma dan AI</strong>
                                <div class="text-muted small">Mitra: PT Industri Inovasi Digital</div>
                            </td>
                            <td>
                                TTD: 15 Jan 2026<br>
                                <span class="text-muted">s/d 15 Jan 2029 (3 Tahun)</span>
                            </td>
                            <td>
                                Rektor UNIPAR &amp; Direktur PT IID
                            </td>
                            <td>
                                <i class="bi bi-file-earmark-pdf text-danger me-1"></i>
                                <span>mou_pt_iid_signed.pdf</span><br>
                                <small class="text-muted">3 Versi Perubahan Tercatat</small>
                            </td>
                            <td class="text-end">
                                <button class="btn btn-outline-primary btn-sm py-0 px-2 me-1" onclick="showQrModal('REG-UNIPAR-2026-001', 'UNIPAR/MOU/2026/001', 'Nota Kesepahaman Kemitraan Bidang Tridharma dan AI', 'PT Industri Inovasi Digital', 'ACTIVE', '15 Jan 2026')"><i class="bi bi-qr-code"></i> QR</button>
                                <button class="btn btn-outline-dark btn-sm py-0 px-2"><i class="bi bi-download"></i> Unduh</button>
                                <button class="btn btn-outline-secondary btn-sm py-0 px-2"><i class="bi bi-clock-history"></i> Riwayat</button>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="fw-bold text-dark">UNIPAR/MOA/2026/004</div>
                                <span class="badge bg-secondary">PKS / MoA</span>
                            </td>
                            <td>
                                <strong class="text-dark">Perjanjian Kerja Sama Program Magang Merdeka dan Riset AI</strong>
                                <div class="text-muted small">Mitra: PT Industri Inovasi Digital</div>
                            </td>
                            <td>
                                TTD: 01 Nov 2026<br>
                                <span class="text-muted">s/d 01 Nov 2028 (2 Tahun)</span>
                            </td>
                            <td>
                                Dekan Fasilkom &amp; GM HRD PT IID
                            </td>
                            <td>
                                <i class="bi bi-file-earmark-pdf text-danger me-1"></i>
                                <span>pks_magang_fasilkom.pdf</span><br>
                                <small class="text-muted">2 Versi Perubahan Tercatat</small>
                            </td>
                            <td class="text-end">
                                <button class="btn btn-outline-dark btn-sm py-0 px-2"><i class="bi bi-download"></i> Unduh</button>
                                <button class="btn btn-outline-secondary btn-sm py-0 px-2"><i class="bi bi-clock-history"></i> Riwayat</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

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
                                <button type="submit" name="role" value="MASTER" class="btn btn-sm {{ $activeRole === 'MASTER' ? 'btn-danger text-white fw-bold' : 'btn-outline-danger' }}" title="Role Master (Super Admin / Akses Bypass Semua Langkah)">
                                    <i class="bi bi-star-fill me-1"></i> MASTER
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- PITA STEPPER 9 STATUS SIKLUS HIDUP FSM -->
            @if (!$isCreatingNew && $currentDoc)
            <div class="card-footer bg-light border-top p-2 px-3">
                <div class="d-flex align-items-center justify-content-between overflow-x-auto py-1" style="min-width: 820px; font-size: 0.72rem;">
                    @php
                        $stepperList = [
                            'DRAFT' => ['1. DRAFT', 'bg-secondary'],
                            'LEGAL_REVIEW' => ['2. LEGAL REVIEW', 'bg-info text-dark'],
                            'REVISION' => ['3. REVISION', 'bg-warning text-dark'],
                            'APPROVED' => ['4. APPROVED', 'bg-primary'],
                            'ACTIVE' => ['5. ACTIVE', 'bg-success'],
                            'WARNING_EXPIRY' => ['6. WARNING', 'bg-warning text-dark'],
                            'EXPIRED' => ['7. EXPIRED', 'bg-danger'],
                            'ADDENDUM' => ['8. ADDENDUM', 'bg-primary'],
                            'CANCELLED' => ['9. CANCELLED', 'bg-dark']
                        ];
                        $currSt = $currentDoc ? $currentDoc->status : 'DRAFT';
                        $stepIdx = 0;
                    @endphp
                    @foreach ($stepperList as $stKey => $stMeta)
                        @php
                            $stepIdx++;
                            $isStepActive = ($stKey === $currSt);
                        @endphp
                        <div class="d-flex align-items-center gap-1 {{ $isStepActive ? 'fw-bold' : 'text-muted' }}">
                            <span class="badge {{ $isStepActive ? 'bg-dark text-white border border-warning shadow-sm' : 'bg-light text-secondary border' }}" style="font-size: 0.68rem;">
                                {{ $stMeta[0] }}
                            </span>
                            @if ($stepIdx < count($stepperList) && $stKey !== 'CANCELLED')
                                <i class="bi bi-chevron-right text-muted small" style="font-size: 0.65rem;"></i>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
            @endif
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
                        <i class="bi bi-arrow-left me-1"></i> Batal / Kembali ke Pemeriksaan
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
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Unit Pengusul:</label>
                                        <select name="unit" class="form-select form-select-sm">
                                            <option>Program Studi Teknik Informatika</option>
                                            <option>Fakultas Ilmu Komputer</option>
                                            <option>Fakultas Ekonomi dan Bisnis</option>
                                            <option>LPPM Universitas Parna Raya</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Nama Instansi / Mitra:</label>
                                        <input type="text" name="partner_name" class="form-control form-control-sm" value="{{ $prefillPartnerName }}" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-bold">Kategori Mitra:</label>
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
                                    <div class="col-md-4">
                                        <label class="form-label fw-bold">Jenis Dokumen:</label>
                                        <select name="doc_type" id="inputDocType" class="form-select form-select-sm" onchange="handleDocTypeChange(this.value)">
                                            <option value="MoU" {{ ($prefillDeriveType === 'MoU') ? 'selected' : '' }}>MoU (Nota Kesepahaman Induk)</option>
                                            <option value="MoA / PKS" {{ ($prefillDeriveType === 'MoA' || $prefillDeriveType === 'MoA / PKS') ? 'selected' : '' }}>MoA / PKS (Perjanjian Kerja Sama)</option>
                                            <option value="IA" {{ ($prefillDeriveType === 'IA') ? 'selected' : '' }}>IA (Implementation Arrangement)</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-bold">Dokumen Induk Rujukan:</label>
                                        <input type="text" name="parent_mou" id="inputParentMou" class="form-control form-control-sm" value="{{ $prefillParentDocNumber }}" list="registeredMouList">
                                        <datalist id="registeredMouList">
                                            <option value="-">
                                            @foreach ($registeredMous as $mouItem)
                                                <option value="{{ $mouItem->doc_number }}">
                                            @endforeach
                                        </datalist>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Jangka Waktu Kerja Sama:</label>
                                        <select name="validity" class="form-select form-select-sm">
                                            <option selected>1 Tahun</option>
                                            <option>2 Tahun</option>
                                            <option>3 Tahun</option>
                                            <option>5 Tahun</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Ambang Batas Peringatan Dini (Warning Threshold):</label>
                                        <select name="warning_threshold" class="form-select form-select-sm">
                                            <option value="H-90 Hari (3 Bulan)" selected>H-90 Hari (3 Bulan) &mdash; Sesuai Klausul MoU Baku</option>
                                            <option value="H-60 Hari (2 Bulan)">H-60 Hari (2 Bulan) &mdash; Standar Kemendikbudristek</option>
                                            <option value="H-30 Hari (1 Bulan)">H-30 Hari (1 Bulan) &mdash; Kemitraan Singkat</option>
                                        </select>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label fw-bold">Substansi &amp; Ruang Lingkup:</label>
                                        <textarea name="scope" class="form-control form-control-sm" rows="3" required></textarea>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label fw-bold">Unggah Draf Naskah (PDF / DOCX):</label>
                                        <input type="file" name="draft_file" class="form-control form-control-sm">
                                    </div>
                                    <div class="col-12 pt-2">
                                        <button type="submit" class="btn btn-dark btn-sm fw-bold px-4 py-2">
                                            <i class="bi bi-save me-1"></i> Simpan Sebagai DRAF (Terbitkan QR Code)
                                        </button>
                                        <a href="{{ route('simkerma.index', ['menu' => 4]) }}" class="btn btn-outline-secondary btn-sm ms-2">Batal</a>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <!-- JALUR 2: PAKET TERPADU TRIDHARMA & PKM -->
                        <div class="tab-pane fade" id="jalur2-pane" role="tabpanel" aria-labelledby="jalur2-tab">
                            <form method="POST" action="{{ route('simkerma.store-jalur2') }}" enctype="multipart/form-data" class="small">
                                @csrf
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Unit Pengusul Utama:</label>
                                        <select name="unit_main" class="form-select form-select-sm">
                                            <option>Program Studi Teknik Informatika</option>
                                            <option selected>Fakultas Ilmu Komputer</option>
                                            <option>Fakultas Ekonomi dan Bisnis</option>
                                            <option>LPPM Universitas Parna Raya</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Unit / Fakultas Kolaborator:</label>
                                        <select name="unit_collab" class="form-select form-select-sm">
                                            <option>-</option>
                                            <option>Fakultas Ilmu Komputer</option>
                                            <option selected>Fakultas Ekonomi dan Bisnis</option>
                                            <option>LPPM Universitas Parna Raya</option>
                                            <option>Program Studi Teknik Informatika</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Nama Instansi / Mitra:</label>
                                        <input type="text" name="partner_name" class="form-control form-control-sm" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Kategori Mitra:</label>
                                        <select name="category" class="form-select form-select-sm">
                                            <option selected>DUDI / Swasta</option>
                                            <option>BUMN</option>
                                            <option>Instansi Pemerintah</option>
                                            <option>Lembaga Keagamaan / Komunitas</option>
                                            <option>Kesehatan</option>
                                        </select>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label fw-bold">Nama Kegiatan / Topik PKM:</label>
                                        <input type="text" name="topic" class="form-control form-control-sm" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Tanggal Penandatanganan:</label>
                                        <input type="date" name="date_signed" class="form-control form-control-sm" value="{{ date('Y-m-d') }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Jangka Waktu:</label>
                                        <select name="duration" class="form-select form-select-sm">
                                            <option selected>1 Tahun</option>
                                            <option>6 Bulan</option>
                                            <option>2 Tahun</option>
                                            <option>3 Tahun</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Ambang Batas Peringatan Dini (Warning Threshold):</label>
                                        <select name="warning_threshold" class="form-select form-select-sm">
                                            <option value="H-90 Hari (3 Bulan)" selected>H-90 Hari (3 Bulan) &mdash; Sesuai Klausul MoU Baku</option>
                                            <option value="H-60 Hari (2 Bulan)">H-60 Hari (2 Bulan) &mdash; Standar Kemendikbudristek</option>
                                            <option value="H-30 Hari (1 Bulan)">H-30 Hari (1 Bulan) &mdash; Kemitraan Singkat</option>
                                        </select>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label fw-bold">Data Mahasiswa / Peserta:</label>
                                        <textarea name="students_data" class="form-control form-control-sm" rows="2" required></textarea>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-bold">Unggah Berkas Scan MoU (PDF):</label>
                                        <input type="file" name="file_mou" class="form-control form-control-sm">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-bold">Unggah Berkas Scan MoA (PDF):</label>
                                        <input type="file" name="file_moa" class="form-control form-control-sm">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-bold">Unggah Berkas Scan IA (PDF):</label>
                                        <input type="file" name="file_ia" class="form-control form-control-sm">
                                    </div>
                                    <div class="col-12 pt-2">
                                        <button type="submit" class="btn btn-dark btn-sm fw-bold px-4 py-2">
                                            <i class="bi bi-box-seam me-1"></i> Simpan Sebagai Paket DRAF (Terbitkan QR Code)
                                        </button>
                                        <a href="{{ route('simkerma.index', ['menu' => 4]) }}" class="btn btn-outline-secondary btn-sm ms-2">Batal</a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                </div>
            </div>

            <script>
            function handleDocTypeChange(val) {
                const parentSelect = document.getElementById('inputParentMou');
                if (!parentSelect) return;
                if (val === 'MoU') {
                    parentSelect.value = '-';
                }
            }
            </script>

        @else

            <!-- BILAH NAVIGASI DOKUMEN, TOMBOL TAMBAH, & TOMBOL RESET KE DRAFT -->
            <div class="card card-wireframe mb-4">
                <div class="card-body p-3 bg-white">
                    <div class="row align-items-center g-3">
                        
                        <!-- Dropdown Pemilih Dokumen -->
                        <div class="col-md-6 col-lg-7">
                            <form method="POST" action="{{ route('simkerma.select-doc') }}" class="d-flex align-items-center gap-2">
                                @csrf
                                <label class="form-label fw-bold small text-muted text-nowrap mb-0">
                                    <i class="bi bi-folder2-open me-1"></i>Pilih Dokumen:
                                </label>
                                <select name="doc_id" class="form-select form-select-sm" onchange="this.form.submit()">
                                    @foreach ($documents as $dOption)
                                        <option value="{{ $dOption->doc_id }}" {{ $dOption->doc_id === $selectedDocId ? 'selected' : '' }}>
                                            [{{ $dOption->track }}] {{ $dOption->doc_id }} - {{ $dOption->partner_name }} ({{ $dOption->status }})
                                        </option>
                                    @endforeach
                                </select>
                            </form>
                        </div>

                        <!-- Tombol Aksi Tambah Pengajuan Baru & Reset ke DRAFT -->
                        <div class="col-md-6 col-lg-5 text-md-end d-flex justify-content-md-end gap-2">
                            <a href="{{ route('simkerma.index', ['menu' => 4, 'view' => 'new']) }}" class="btn btn-primary btn-sm fw-bold shadow-sm">
                                <i class="bi bi-plus-circle-fill me-1"></i> Tambah Pengajuan Baru
                            </a>

                            @if ($currentDoc)
                                <form method="POST" action="{{ route('simkerma.reset-draft') }}" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin mereset status dokumen {{ $currentDoc->doc_id }} kembali ke status DRAFT?')">
                                    @csrf
                                    <input type="hidden" name="doc_id" value="{{ $currentDoc->doc_id }}">
                                    <button type="submit" class="btn btn-outline-danger btn-sm fw-bold shadow-sm" title="Kembalikan dokumen ini ke status DRAFT untuk simulasi ulang dari awal">
                                        <i class="bi bi-arrow-counterclockwise me-1"></i> Reset ke DRAFT
                                    </button>
                                </form>
                            @endif
                        </div>

                    </div>
                </div>
            </div>

            @if ($currentDoc)

                <div class="row g-4">
                    <!-- KOLOM KIRI: RINCIAN DOKUMEN & POHON RELASI HIERARKI KEMITRAAN (IKU 6) -->
                    <div class="col-lg-7">
                        <div class="card card-wireframe mb-4">
                            <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="badge bg-dark me-1">{{ $currentDoc->doc_id }}</span>
                                    <span class="badge bg-secondary">{{ $currentDoc->track }}</span>
                                </div>
                                @php
                                    $stBadge = 'bg-secondary';
                                    if ($currentDoc->status === 'DRAFT') $stBadge = 'bg-secondary text-white';
                                    elseif ($currentDoc->status === 'LEGAL_REVIEW') $stBadge = 'bg-info text-dark';
                                    elseif ($currentDoc->status === 'REVISION') $stBadge = 'bg-warning text-dark';
                                    elseif ($currentDoc->status === 'APPROVED') $stBadge = 'bg-primary text-white';
                                    elseif ($currentDoc->status === 'ACTIVE') $stBadge = 'bg-success text-white';
                                    elseif ($currentDoc->status === 'WARNING_EXPIRY') $stBadge = 'bg-warning text-dark';
                                    elseif ($currentDoc->status === 'EXPIRED') $stBadge = 'bg-danger text-white';
                                    elseif ($currentDoc->status === 'ADDENDUM') $stBadge = 'bg-primary text-white';
                                @endphp
                                <span class="badge {{ $stBadge }} px-3 py-1 fs-6 fw-bold">
                                    {{ $currentDoc->status }}
                                </span>
                            </div>

                            <div class="card-body">
                                <h5 class="fw-bold text-primary mb-1">{{ $currentDoc->partner_name }}</h5>
                                <div class="small text-muted mb-3">
                                    <i class="bi bi-file-earmark-text me-1"></i>{{ $currentDoc->doc_type }}
                                    &bull; <i class="bi bi-buildings me-1"></i>{{ $currentDoc->unit }}
                                    @if (!empty($currentDoc->collab_unit))
                                        &bull; <i class="bi bi-diagram-3 me-1"></i>Kolaborator: {{ $currentDoc->collab_unit }}
                                    @endif
                                </div>

                                <div class="bg-light p-3 rounded border mb-3 small">
                                    <div class="row g-2">
                                        <div class="col-sm-6">
                                            <span class="text-muted d-block" style="font-size: 0.72rem;">Nomor Dokumen:</span>
                                            <strong>{{ $currentDoc->doc_number ?: 'Belum Registrasi' }}</strong>
                                        </div>
                                        <div class="col-sm-6">
                                            <span class="text-muted d-block" style="font-size: 0.72rem;">Masa Berlaku:</span>
                                            <strong>{{ $currentDoc->validity ?: '3 Tahun' }}</strong>
                                        </div>
                                        <div class="col-sm-6 border-top pt-2">
                                            <span class="text-muted d-block" style="font-size: 0.72rem;">Dokumen Induk Rujukan:</span>
                                            <strong>
                                                @if (!empty($currentDoc->parent_mou) && $currentDoc->parent_mou !== '-')
                                                    <span class="text-primary"><i class="bi bi-link-45deg me-1"></i>{{ $currentDoc->parent_mou }}</span>
                                                @else
                                                    <span class="text-muted">- (Dokumen Payung Utama)</span>
                                                @endif
                                            </strong>
                                        </div>
                                        <div class="col-sm-6 border-top pt-2">
                                            <span class="text-muted d-block" style="font-size: 0.72rem;">Kategori Mitra:</span>
                                            <strong>{{ $currentDoc->partner_category ?: 'DUDI / Swasta' }}</strong>
                                        </div>
                                        <div class="col-sm-6 border-top pt-2">
                                            <span class="text-muted d-block" style="font-size: 0.72rem;">Peringatan Dini (Threshold):</span>
                                            <strong class="text-warning text-dark"><i class="bi bi-bell-fill text-warning me-1"></i>{{ $currentDoc->warning_threshold ?? 'H-90 Hari (3 Bulan)' }}</strong>
                                        </div>
                                        <div class="col-12 border-top pt-2">
                                            <span class="text-muted d-block" style="font-size: 0.72rem;">Ruang Lingkup / Substansi:</span>
                                            <div class="text-dark">{{ $currentDoc->scope }}</div>
                                        </div>
                                        @if (!empty($currentDoc->students_data))
                                            <div class="col-12 border-top pt-2">
                                                <span class="text-muted d-block" style="font-size: 0.72rem;">Data Peserta / Mahasiswa PKM:</span>
                                                <div>{{ $currentDoc->students_data }}</div>
                                            </div>
                                        @endif
                                        @if (!empty($currentDoc->signed_file))
                                            <div class="col-12 border-top pt-2">
                                                <span class="text-muted d-block" style="font-size: 0.72rem;">Eksemplar Fisik Bermeterai:</span>
                                                <span class="text-success"><i class="bi bi-file-earmark-check-fill me-1"></i>{{ $currentDoc->signed_file }} (Terverifikasi)</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <!-- WIDGET POHON RELASI DOKUMEN KEMITRAAN (MoU -> MoA -> IA HIERARKI IKU 6) -->
                                <div class="border rounded p-3 bg-white mb-3 shadow-sm">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="fw-bold small text-dark"><i class="bi bi-diagram-3-fill text-primary me-1"></i>Keterlacakan Hierarki Kemitraan (IKU 6):</span>
                                        
                                        <!-- Tombol Pintas Kontekstual untuk Menurunkan Dokumen jika Induk Aktif -->
                                        @if ($currentDoc->status === 'ACTIVE')
                                            @if (stripos($currentDoc->doc_type, 'MoU') !== false)
                                                <a href="{{ route('simkerma.index', ['menu' => 4, 'view' => 'new', 'parent_id' => $currentDoc->doc_id, 'derive_type' => 'MoA']) }}" class="btn btn-outline-primary btn-sm py-1 px-2 fw-bold" style="font-size: 0.72rem;">
                                                    <i class="bi bi-plus-circle me-1"></i> + Turunkan MoA (Fakultas)
                                                </a>
                                            @elseif (stripos($currentDoc->doc_type, 'MoA') !== false)
                                                <a href="{{ route('simkerma.index', ['menu' => 4, 'view' => 'new', 'parent_id' => $currentDoc->doc_id, 'derive_type' => 'IA']) }}" class="btn btn-outline-primary btn-sm py-1 px-2 fw-bold" style="font-size: 0.72rem;">
                                                    <i class="bi bi-plus-circle me-1"></i> + Turunkan IA (Prodi/Lab)
                                                </a>
                                            @endif
                                        @endif
                                    </div>

                                    <div class="bg-light p-2 rounded small">
                                        
                                        <!-- Jika dokumen ini memiliki Induk (MoA/IA) -->
                                        @if ($parentDoc)
                                            <div class="mb-2 pb-2 border-bottom">
                                                <div class="text-muted d-block" style="font-size: 0.7rem;">DINAUNGI OLEH DOKUMEN INDUK:</div>
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <span class="badge bg-dark me-1">{{ $parentDoc->doc_type }}</span>
                                                        <strong>{{ $parentDoc->doc_number ?: $parentDoc->doc_id }}</strong>
                                                        <div class="text-muted" style="font-size: 0.72rem;">{{ $parentDoc->partner_name }}</div>
                                                    </div>
                                                    <a href="{{ route('simkerma.index', ['menu' => 4, 'doc' => $parentDoc->doc_id]) }}" class="btn btn-sm btn-outline-dark py-0 px-2" style="font-size: 0.7rem;">
                                                        Lihat Induk
                                                    </a>
                                                </div>
                                            </div>
                                        @endif

                                        <!-- Dokumen yang sedang aktif diperiksa saat ini -->
                                        <div class="p-2 bg-white rounded border d-flex justify-content-between align-items-center my-1 shadow-sm">
                                            <div>
                                                <span class="badge bg-primary me-1">{{ $currentDoc->doc_type }}</span>
                                                <strong>{{ $currentDoc->doc_number ?: $currentDoc->doc_id }}</strong> (Dokumen Ini)
                                                <div class="text-muted" style="font-size: 0.72rem;">{{ $currentDoc->unit }}</div>
                                            </div>
                                            <span class="badge {{ $stBadge }}">{{ $currentDoc->status }}</span>
                                        </div>

                                        <!-- Jika dokumen ini memiliki Turunan (Anak Dokumen) -->
                                        <div class="mt-2 pt-2 border-top">
                                            <div class="text-muted d-block" style="font-size: 0.7rem;">DOKUMEN TURUNAN TERDAFTAR (CHILD DOCUMENTS):</div>
                                            @if ($childDocs->isNotEmpty())
                                                <div class="list-group list-group-flush mt-1">
                                                    @foreach ($childDocs as $ch)
                                                        <div class="list-group-item d-flex justify-content-between align-items-center bg-transparent py-1 px-1">
                                                            <div>
                                                                <i class="bi bi-arrow-return-right text-muted me-1"></i>
                                                                <span class="badge bg-secondary me-1">{{ $ch->doc_type }}</span>
                                                                <strong>{{ $ch->doc_number ?: $ch->doc_id }}</strong>
                                                                <span class="text-muted" style="font-size: 0.72rem;">({{ $ch->unit }})</span>
                                                            </div>
                                                            <div class="d-flex align-items-center gap-1">
                                                                <span class="badge bg-light text-dark border" style="font-size: 0.65rem;">{{ $ch->status }}</span>
                                                                <a href="{{ route('simkerma.index', ['menu' => 4, 'doc' => $ch->doc_id]) }}" class="btn btn-sm btn-outline-primary py-0 px-2" style="font-size: 0.68rem;">
                                                                    Periksa
                                                                </a>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @else
                                                <div class="text-muted fst-italic py-1" style="font-size: 0.72rem;">
                                                    Belum ada dokumen turunan yang didaftarkan dari naskah ini.
                                                    @if ($currentDoc->status !== 'ACTIVE')
                                                        <span class="text-warning d-block">* Dokumen turunan baru dapat diterbitkan setelah naskah ini berstatus AKTIF (Sah &amp; Bermeterai).</span>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>

                                    </div>
                                </div>

                                <!-- RIWAYAT AUDIT TRAIL PERMANEN BERSTEMPEL WAKTU -->
                                <div>
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="fw-bold small text-muted"><i class="bi bi-clock-history me-1"></i>Immutable Audit Trail (Jejak Rekam Transisi MySQL):</span>
                                        <span class="badge bg-light text-dark border" style="font-size: 0.68rem;">{{ $currentDoc->auditTrails->count() }} Log Tercatat</span>
                                    </div>
                                    <div class="border rounded p-2 bg-light small" style="max-height: 180px; overflow-y: auto;">
                                        @foreach ($currentDoc->auditTrails as $log)
                                            <div class="border-bottom pb-2 mb-2">
                                                <div class="d-flex justify-content-between text-muted" style="font-size: 0.72rem;">
                                                    <span class="fw-bold text-dark">{{ $log->actor }} [{{ $log->role }}]</span>
                                                    <span>{{ $log->timestamp_wita }}</span>
                                                </div>
                                                <div style="font-size: 0.78rem;">
                                                    <span class="badge bg-secondary" style="font-size: 0.65rem;">{{ $log->event }}</span>
                                                    <span class="text-muted">{{ $log->from_status }} &rarr; {{ $log->to_status }}:</span>
                                                    {{ $log->notes }}
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                    <!-- KOLOM KANAN: KOTAK QR CODE NYATA & AKSI FORM TRANSISI FSM -->
                    <div class="col-lg-5">
                        
                        <!-- KOTAK QR CODE RESMI DINAMIS -->
                        <div class="card card-wireframe mb-4">
                            <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                                <h6 class="card-title mb-0 fw-bold small text-uppercase">
                                    <i class="bi bi-qr-code text-primary me-1"></i> QR Code Registrasi Resmi
                                </h6>
                                <span class="badge bg-light text-dark border">{{ $currentDoc->reg_id }}</span>
                            </div>
                            <div class="card-body p-3 text-center">
                                
                                <!-- Canvas QR Code Dinamis -->
                                <div class="d-flex justify-content-center my-2">
                                    <div id="activeDocQrCanvas" class="p-2 border rounded bg-white shadow-sm d-inline-block" style="min-width: 104px; min-height: 104px;"></div>
                                </div>

                                <div class="mb-2">
                                    @php
                                        $stLabel = 'SEDANG DI-REVIEW';
                                        if ($currentDoc->status === 'DRAFT') $stLabel = 'DRAFT (KONSEP AWAL)';
                                        elseif ($currentDoc->status === 'APPROVED') $stLabel = 'APPROVED (DISETUJUI)';
                                        elseif ($currentDoc->status === 'ACTIVE') $stLabel = 'ACTIVE (SAH & BERMETERAI)';
                                        elseif ($currentDoc->status === 'WARNING_EXPIRY') $stLabel = 'MENDEKATI KEDALUWARSA';
                                        elseif ($currentDoc->status === 'EXPIRED') $stLabel = 'KEDALUWARSA (EXPIRED)';
                                        elseif ($currentDoc->status === 'ADDENDUM') $stLabel = 'PROSES ADENDUM';
                                        elseif ($currentDoc->status === 'REVISION') $stLabel = 'PERBAIKAN REVISI';
                                    @endphp
                                    <span class="badge {{ $stBadge }} py-1 px-3 fs-6">
                                        Status: {{ $stLabel }}
                                    </span>
                                </div>

                                <div class="text-muted small mb-3" style="font-size: 0.72rem;">
                                    Pindai QR ini via kamera ponsel untuk memverifikasi keabsahan dokumen real-time pada peladen kampus.
                                </div>

                                <div class="d-flex justify-content-center gap-2">
                                    <a href="{{ route('simkerma.verify', ['reg_id' => $currentDoc->reg_id]) }}" target="_blank" class="btn btn-outline-dark btn-sm py-1 px-3 fw-semibold" style="font-size: 0.76rem;">
                                        <i class="bi bi-box-arrow-up-right me-1"></i> Buka Verifikasi Publik
                                    </a>
                                    <button type="button" class="btn btn-outline-primary btn-sm py-1 px-3 fw-semibold" style="font-size: 0.76rem;" onclick="window.open('{{ route('simkerma.verify', ['reg_id' => $currentDoc->reg_id]) }}', '_blank')">
                                        <i class="bi bi-printer me-1"></i> Cetak Lembar Verifikasi
                                    </button>
                                </div>

                            </div>
                        </div>

                        <!-- KOTAK AKSI TRANSISI FSM FORMAL -->
                        <div class="card card-wireframe">
                            <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                                <h6 class="card-title mb-0 fw-bold small text-uppercase">
                                    <i class="bi bi-play-circle text-success me-1"></i> Eksekusi Aksi FSM
                                </h6>
                                <span class="badge {{ $activeRole === 'MASTER' ? 'bg-danger' : 'bg-primary' }}">
                                    AKTOR: {{ $activeRole }}
                                </span>
                            </div>
                            <div class="card-body p-3">
                                
                                <form method="POST" action="{{ route('simkerma.transition') }}" enctype="multipart/form-data">
                                    @csrf
                                    <input type="hidden" name="doc_id" value="{{ $currentDoc->doc_id }}">

                                    <!-- Jika status APPROVED, sediakan unggah scan fisik bertanda tangan basah & bermeterai -->
                                    @if ($currentDoc->status === 'APPROVED')
                                        <div class="mb-3 p-3 bg-light rounded border">
                                            <label class="form-label fw-bold small text-dark d-block">
                                                <i class="bi bi-file-earmark-check-fill text-primary me-1"></i>
                                                Unggah Berkas Scan Fisik Bertanda Tangan &amp; Bermeterai (PDF):
                                            </label>
                                            <input type="file" name="signed_scan_file" class="form-control form-control-sm mb-2" {{ in_array($activeRole, ['LEADER', 'MASTER'], true) ? 'required' : 'disabled' }}>
                                        </div>
                                    @endif

                                    <div class="mb-3">
                                        <label class="form-label fw-bold small text-muted">Catatan Keputusan / Disposisi Pejabat:</label>
                                        <textarea name="notes" class="form-control form-control-sm" rows="2"></textarea>
                                    </div>

                                    <div class="mb-2">
                                        <label class="form-label fw-bold small text-muted d-block">Aksi Transisi yang Diizinkan (Status: {{ $currentDoc->status }}):</label>
                                        <div class="d-grid gap-2">
                                            @if (empty($availEvents))
                                                <div class="alert alert-secondary py-2 small mb-0 text-center">
                                                    <i class="bi bi-info-circle me-1"></i> Dokumen berada pada status akhir (Terminal State). Tidak ada transisi lanjutan.
                                                </div>
                                            @else
                                                @foreach ($availEvents as $evKey => $evData)
                                                    @php
                                                        $isRoleAllowed = ($activeRole === 'MASTER' || in_array($activeRole, $evData['roles'], true));
                                                        $btnLabelText = $evData['label'];
                                                        if ($evKey === 'TRIGGER_WARN_EXPIRY' && !empty($currentDoc->warning_threshold)) {
                                                            $btnLabelText = 'Jalankan Pemeriksaan Masa Berlaku (' . $currentDoc->warning_threshold . ')';
                                                        }
                                                    @endphp
                                                    <button type="submit" name="event" value="{{ $evKey }}" class="btn btn-sm {{ $evData['btn'] }} py-2 text-start d-flex justify-content-between align-items-center shadow-sm" {{ $isRoleAllowed ? '' : 'disabled' }}>
                                                        <span><i class="bi {{ $evData['icon'] }} me-2"></i>{{ $btnLabelText }}</span>
                                                        <div>
                                                            @if (!$isRoleAllowed)
                                                                <span class="badge bg-light text-danger border me-1" style="font-size: 0.65rem;">Wewenang: {{ implode(', ', $evData['roles']) }}</span>
                                                            @endif
                                                            <span class="badge bg-light text-dark border" style="font-size: 0.65rem;">{{ $evKey }}</span>
                                                        </div>
                                                    </button>
                                                @endforeach
                                            @endif
                                        </div>
                                    </div>
                                </form>

                            </div>
                        </div>

                    </div>
                </div>

            @else
                <div class="alert alert-secondary py-4 text-center">
                    <i class="bi bi-folder2-open fs-1 d-block text-muted mb-2"></i>
                    <div class="fw-bold">Belum Ada Dokumen Terpilih</div>
                    <div class="small text-muted mb-3">Pangkalan data tidak memiliki dokumen atau dokumen belum dipilih.</div>
                    <a href="{{ route('simkerma.index', ['menu' => 4, 'view' => 'new']) }}" class="btn btn-primary btn-sm fw-bold">
                        <i class="bi bi-plus-circle me-1"></i> Buat Pengajuan Baru Sekarang
                    </a>
                </div>
            @endif

        @endif

        {{-- SCRIPT GENERATOR QR CODE KHUSUS HALAMAN MENU 4 --}}
        @if (!$isCreatingNew && $currentDoc)
        @push('scripts')
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            const qrBox = document.getElementById('activeDocQrCanvas');
            if (qrBox) {
                qrBox.innerHTML = '';
                const verifyUrl = window.location.origin + '/verify/' + encodeURIComponent('{{ $currentDoc->reg_id }}');
                try {
                    new QRCode(qrBox, {
                        text: verifyUrl,
                        width: 104,
                        height: 104,
                        colorDark: "#0f172a",
                        colorLight: "#ffffff",
                        correctLevel: QRCode.CorrectLevel.M
                    });
                } catch(e) {
                    qrBox.innerHTML = '<i class="bi bi-qr-code text-dark fs-2"></i>';
                }
            }
        });
        </script>
        @endpush
        @endif

    {{-- ===================================================================================== --}}
    {{-- 5. PEMANTAUAN PELAKSANAAN KERJA SAMA                                                  --}}
    {{-- ===================================================================================== --}}
    @elseif ($activeMenu === 5)
        
        <div class="card card-wireframe">
            <div class="card-header bg-white py-3 border-bottom d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2">
                <div>
                    <h6 class="mb-0 fw-bold text-uppercase">Pemantauan Pelaksanaan Kerja Sama</h6>
                    <small class="text-muted">Memastikan Perjanjian Benar-Benar Dilaksanakan Melalui Bukti &amp; Realisasi Riil</small>
                </div>
                <button class="btn btn-dark btn-sm fw-semibold">
                    <i class="bi bi-plus-lg me-1"></i> Catat Kegiatan Baru
                </button>
            </div>

            <!-- FILTER STATUS KEGIATAN -->
            <div class="card-body border-bottom bg-light py-2">
                <div class="d-flex flex-wrap align-items-center gap-2 small">
                    <span class="text-muted fw-bold me-2">Status Kegiatan:</span>
                    <button class="btn btn-dark btn-sm py-1 px-3">Semua</button>
                    <button class="btn btn-outline-secondary btn-sm py-1 px-3">Belum Dimulai</button>
                    <button class="btn btn-outline-secondary btn-sm py-1 px-3">Sedang Berjalan</button>
                    <button class="btn btn-outline-secondary btn-sm py-1 px-3">Selesai</button>
                    <button class="btn btn-outline-secondary btn-sm py-1 px-3">Tertunda</button>
                </div>
            </div>

            <!-- TABEL PELAKSANAAN KEGIATAN -->
            <div class="table-responsive">
                <table class="table table-wireframe table-hover align-middle mb-0 small">
                    <thead>
                        <tr>
                            <th>Daftar Kegiatan &amp; Dokumen Rujukan</th>
                            <th>Unit PJ &amp; Jadwal</th>
                            <th>Target &amp; Realisasi</th>
                            <th>Peserta / Pihak Terlibat</th>
                            <th>Luaran &amp; Manfaat</th>
                            <th>Anggaran / Kontribusi</th>
                            <th>Bukti Kegiatan</th>
                            <th>Status Kegiatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <strong class="text-dark">Magang Industri AI Semester Ganjil</strong>
                                <div class="text-muted small">Ref: UNIPAR/MOA/2026/004</div>
                            </td>
                            <td>
                                Teknik Informatika<br>
                                <span class="text-muted">01 Nov 2026 - 28 Feb 2027</span>
                            </td>
                            <td>
                                Target: 20 Mahasiswa<br>
                                <span class="fw-bold">Realisasi: 18 Mahasiswa</span>
                            </td>
                            <td>
                                18 Mahasiswa &amp; 3 Mentor PT IID
                            </td>
                            <td>
                                Laporan Magang, 2 Sertifikat Kompetensi, 1 Purwarupa AI
                            </td>
                            <td>
                                Uang Saku Mitra:<br>Rp 45.000.000
                            </td>
                            <td>
                                <a href="#" class="text-decoration-none text-dark fw-bold d-block"><i class="bi bi-file-earmark-pdf"></i> Laporan.pdf</a>
                                <a href="#" class="text-decoration-none text-muted small"><i class="bi bi-image"></i> Foto &amp; Sertifikat</a>
                            </td>
                            <td>
                                <span class="badge bg-secondary">Sedang Berjalan</span>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <strong class="text-dark">Kuliah Praktisi: Machine Learning Ops</strong>
                                <div class="text-muted small">Ref: UNIPAR/IA/2026/012</div>
                            </td>
                            <td>
                                Teknik Informatika<br>
                                <span class="text-muted">18 Nov 2026</span>
                            </td>
                            <td>
                                Target: 100 Peserta<br>
                                <span class="fw-bold">Realisasi: 124 Peserta</span>
                            </td>
                            <td>
                                Mahasiswa &amp; Dosen FASILKOM
                            </td>
                            <td>
                                Modul Ajar MLOps &amp; Rekaman Sesi
                            </td>
                            <td>
                                Kontribusi Kampus:<br>Rp 5.000.000
                            </td>
                            <td>
                                <a href="#" class="text-decoration-none text-dark fw-bold d-block"><i class="bi bi-file-earmark-pdf"></i> Presensi.pdf</a>
                                <a href="#" class="text-decoration-none text-muted small"><i class="bi bi-image"></i> Foto Kegiatan</a>
                            </td>
                            <td>
                                <span class="badge bg-dark">Selesai</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

    {{-- ===================================================================================== --}}
    {{-- 6. MONITORING MASA BERLAKU DAN EVALUASI                                               --}}
    {{-- ===================================================================================== --}}
    @elseif ($activeMenu === 6)
        
        <div class="row g-4">
            <!-- Pengingat & Identifikasi Tindak Lanjut -->
            <div class="col-lg-6">
                <div class="card card-wireframe h-100">
                    <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-0 fw-bold text-uppercase">Pengingat Masa Berlaku</h6>
                            <small class="text-muted">Membantu Biro Kerja Sama Mengidentifikasi Perjanjian Krusial</small>
                        </div>
                        <span class="tag-wireframe">PENGAWASAN</span>
                    </div>
                    <div class="card-body">
                        
                        <label class="form-label fw-bold small">Daftar Pengingat Sebelum Masa Berlaku Berakhir:</label>
                        
                        <div class="card bg-light border mb-3">
                            <div class="card-body p-3 small">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <strong class="text-dark">Pemerintah Kota Manado (MoU)</strong>
                                    <span class="badge bg-dark">Sisa 31 Hari</span>
                                </div>
                                <div class="text-muted">Nomor: UNIPAR/MOU/2022/019 &bull; Masa Aktif s/d 30 Nov 2026</div>
                                <div class="mt-2 text-dark">Rekomendasi Tindak Lanjut: <strong>Perpanjangan (Inisiasi Adendum)</strong></div>
                                <div class="mt-3 d-flex gap-2">
                                    <button class="btn btn-dark btn-sm py-1 px-3 fw-bold">Inisiasi Adendum</button>
                                    <button class="btn btn-outline-secondary btn-sm py-1 px-2">Kirim Pengingat Mitra</button>
                                </div>
                            </div>
                        </div>

                        <div class="border-top pt-3 mt-3">
                            <label class="form-label fw-bold small text-dark">
                                <i class="bi bi-exclamation-circle text-danger me-1"></i> Daftar Kerja Sama Tanpa Realisasi Kegiatan:
                            </label>
                            <div class="alert alert-light border small text-muted mb-0">
                                <div>&bull; <strong>MoU Yayasan Pendidikan X</strong> (Aktif 8 bulan, belum ada MoA/IA tindak lanjut).</div>
                                <div class="mt-1">&bull; <strong>PKS Lembaga Pelatihan Y</strong> (Aktif 5 bulan, target realisasi kegiatan 0%).</div>
                                <div class="small text-secondary mt-2">Dapat diterbitkan surat teguran komitmen atau rekomendasi penghentian kerja sama.</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Evaluasi Capaian & Rekomendasi Biro -->
            <div class="col-lg-6">
                <div class="card card-wireframe h-100">
                    <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-0 fw-bold text-uppercase">Evaluasi Capaian Terhadap Target</h6>
                            <small class="text-muted">Catatan Kendala, Rekomendasi &amp; Riwayat Evaluasi</small>
                        </div>
                        <span class="tag-wireframe">FORM EVALUASI</span>
                    </div>
                    <div class="card-body">
                        <form class="small">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Pilih Dokumen yang Dievaluasi:</label>
                                <select class="form-select form-select-sm">
                                    <option>UNIPAR/MOU/2022/019 - Pemerintah Kota Manado</option>
                                    <option>UNIPAR/MOA/2026/004 - PT Industri Inovasi Digital</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Evaluasi Capaian Terhadap Target:</label>
                                <select class="form-select form-select-sm">
                                    <option>Target Tercapai Penuh (Sangat Baik)</option>
                                    <option>Target Tercapai Sebagian (Cukup)</option>
                                    <option>Target Belum Tercapai (Kurang)</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Catatan Kendala &amp; Rencana Tindak Lanjut:</label>
                                <textarea class="form-control form-control-sm" rows="3" placeholder="Tuliskan kendala komunikasi, kendala pembiayaan, atau hambatan operasional lapangan..."></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Rekomendasi Akhir Biro Kerja Sama:</label>
                                <div class="d-flex gap-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="rekomendasi" id="rek1" checked>
                                        <label class="form-check-label" for="rek1">Perpanjangan</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="rekomendasi" id="rek2">
                                        <label class="form-check-label" for="rek2">Revisi Klausul</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="rekomendasi" id="rek3">
                                        <label class="form-check-label" for="rek3">Penghentian</label>
                                    </div>
                                </div>
                            </div>
                            <button type="button" class="btn btn-dark btn-sm w-100 fw-bold py-2">
                                Simpan Riwayat Evaluasi Dokumen
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    {{-- ===================================================================================== --}}
    {{-- 7. LAPORAN DAN ANALITIK                                                               --}}
    {{-- ===================================================================================== --}}
    @elseif ($activeMenu === 7)
        
        <div class="card card-wireframe">
            <div class="card-header bg-white py-3 border-bottom d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2">
                <div>
                    <h6 class="mb-0 fw-bold text-uppercase">Pusat Laporan &amp; Analitik Kemitraan</h6>
                    <small class="text-muted">Mendukung Pengambilan Keputusan Pimpinan &amp; Akreditasi Institusi</small>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-dark btn-sm fw-semibold">
                        <i class="bi bi-file-earmark-excel me-1"></i> Ekspor Excel (.xlsx)
                    </button>
                    <button class="btn btn-outline-secondary btn-sm fw-semibold">
                        <i class="bi bi-file-earmark-pdf me-1"></i> Ekspor PDF (.pdf)
                    </button>
                </div>
            </div>

            <!-- FILTER KRITERIA LAPORAN -->
            <div class="card-body border-bottom bg-light py-3">
                <div class="row g-2 small">
                    <div class="col-md-3">
                        <label class="form-label fw-bold mb-1">Berdasarkan Tahun:</label>
                        <select class="form-select form-select-sm">
                            <option>Tahun 2026 (TS)</option>
                            <option>Tahun 2025 (TS-1)</option>
                            <option>Tahun 2024 (TS-2)</option>
                            <option>Semua Tahun</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold mb-1">Berdasarkan Unit / Fakultas:</label>
                        <select class="form-select form-select-sm">
                            <option>Semua Unit</option>
                            <option>Fakultas Ilmu Komputer</option>
                            <option>Fakultas Ekonomi &amp; Bisnis</option>
                            <option>Fakultas Teknik</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold mb-1">Berdasarkan Jenis Kerja Sama:</label>
                        <select class="form-select form-select-sm">
                            <option>Semua Jenis</option>
                            <option>MoU (Universitas)</option>
                            <option>PKS / MoA (Fakultas)</option>
                            <option>IA (Kegiatan Prodi)</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button class="btn btn-dark btn-sm w-100 fw-bold">
                            <i class="bi bi-filter me-1"></i> Tampilkan Rekapitulasi
                        </button>
                    </div>
                </div>
            </div>

            <!-- 2 KARTU REKAPITULASI DUA SISI -->
            <div class="card-body border-bottom">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded border small">
                            <h6 class="fw-bold text-dark text-uppercase border-bottom pb-2 mb-2" style="font-size: 0.8rem;">
                                Rekapitulasi Kerja Sama Aktif &amp; Tidak Aktif
                            </h6>
                            <div class="d-flex justify-content-between mb-1">
                                <span>Kerja Sama Aktif Sah:</span>
                                <strong class="text-dark">84 Dokumen (65.6%)</strong>
                            </div>
                            <div class="d-flex justify-content-between mb-1">
                                <span>Kerja Sama Tidak Aktif / Berakhir:</span>
                                <strong class="text-muted">18 Dokumen (14.1%)</strong>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span>Kerja Sama Dalam Proses:</span>
                                <strong>26 Dokumen (20.3%)</strong>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded border small">
                            <h6 class="fw-bold text-dark text-uppercase border-bottom pb-2 mb-2" style="font-size: 0.8rem;">
                                Laporan Rencana vs Realisasi &amp; Luaran
                            </h6>
                            <div class="d-flex justify-content-between mb-1">
                                <span>Total Rencana Kegiatan:</span>
                                <strong class="text-dark">52 Agenda</strong>
                            </div>
                            <div class="d-flex justify-content-between mb-1">
                                <span>Realisasi Kegiatan Terlaksana:</span>
                                <strong class="text-dark">44 Agenda (84.6%)</strong>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span>Laporan Luaran Dihasilkan:</span>
                                <strong class="text-dark">38 Bukti Laporan</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TABEL PRATINJAU REKAPITULASI DATA -->
            <div class="table-responsive">
                <table class="table table-wireframe table-hover align-middle mb-0 small">
                    <thead>
                        <tr>
                            <th>Tahun</th>
                            <th>Nama Mitra</th>
                            <th>Unit Pengusul</th>
                            <th>Jenis</th>
                            <th>Rencana Kegiatan</th>
                            <th>Realisasi Kegiatan</th>
                            <th>Status Kerja Sama</th>
                            <th>Luaran</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>2026</td>
                            <td class="fw-bold text-dark">PT Industri Inovasi Digital</td>
                            <td>Teknik Informatika</td>
                            <td><span class="badge bg-light text-dark border">MoA</span></td>
                            <td>Magang 20 Mahasiswa</td>
                            <td>18 Mahasiswa</td>
                            <td><span class="badge bg-dark">Aktif</span></td>
                            <td>Laporan &amp; Purwarupa AI</td>
                        </tr>
                        <tr>
                            <td>2026</td>
                            <td class="fw-bold text-dark">Pemerintah Kota Manado</td>
                            <td>LPPM UNIPAR</td>
                            <td><span class="badge bg-light text-dark border">MoU</span></td>
                            <td>Smart City &amp; Tata Kota</td>
                            <td>3 Pelatihan</td>
                            <td><span class="badge bg-secondary">Aktif (Warning)</span></td>
                            <td>Modul &amp; Naskah Kebijakan</td>
                        </tr>
                        <tr>
                            <td>2025</td>
                            <td class="fw-bold text-dark">PT Bank SulutGo</td>
                            <td>Ekonomi &amp; Bisnis</td>
                            <td><span class="badge bg-light text-dark border">MoA</span></td>
                            <td>Magang Perbankan</td>
                            <td>15 Mahasiswa</td>
                            <td><span class="badge bg-dark">Aktif</span></td>
                            <td>Laporan Magang &amp; Sertifikat</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

    @endif

@endsection
