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
                    @endphp
                    @foreach ($stepperList as $stKey => $stMeta)
                        <div class="d-flex align-items-center gap-1">
                            <span class="badge bg-light text-dark border" style="font-size: 0.68rem;">
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
                                        <label class="form-label fw-bold">Nomor Surat / Naskah Pihak Kedua (Mitra):</label>
                                        <input type="text" name="partner_doc_number" class="form-control form-control-sm" placeholder="Nomor resmi dari instansi mitra (jika sudah ada)">
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
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Nomor Surat / Naskah Pihak Kedua (Mitra):</label>
                                        <input type="text" name="partner_doc_number" class="form-control form-control-sm" placeholder="Nomor resmi dari instansi mitra (jika sudah ada)">
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
                                <option value="">-- Semua Status FSM --</option>
                                <option value="DRAFT">1. DRAFT (Konsep Awal)</option>
                                <option value="LEGAL_REVIEW">2. LEGAL REVIEW (Telaah Hukum)</option>
                                <option value="REVISION">3. REVISION (Perbaikan Klausul)</option>
                                <option value="APPROVED">4. APPROVED (Teks Dikunci)</option>
                                <option value="ACTIVE">5. ACTIVE (Sah &amp; Bermeterai)</option>
                                <option value="WARNING_EXPIRY">6. WARNING (Peringatan Dini)</option>
                                <option value="EXPIRED">7. EXPIRED (Kedaluwarsa)</option>
                                <option value="ADDENDUM">8. ADDENDUM (Pembaruan)</option>
                                <option value="CANCELLED">9. CANCELLED (Dibatalkan)</option>
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
                                <th style="width: 120px;" class="text-center">QR Code Resmi</th>
                                <th style="min-width: 170px;">Nomor Dokumen &amp; Jenis</th>
                                <th style="min-width: 180px;">Mitra Kerja Sama</th>
                                <th style="min-width: 200px;">Unit Pengusul &amp; Ruang Lingkup</th>
                                <th style="min-width: 140px;">Masa Berlaku &amp; Peringatan</th>
                                <th style="min-width: 130px;" class="text-center">Status FSM</th>
                                <th style="min-width: 150px;" class="text-end">Aksi &amp; Disposisi</th>
                            </tr>
                        </thead>
                        <tbody id="documentsTableBody">
                            @forelse ($documents as $doc)
                                @php
                                    $stBadge = match($doc->status) {
                                        'DRAFT' => 'bg-secondary text-white',
                                        'LEGAL_REVIEW' => 'bg-info text-dark',
                                        'REVISION' => 'bg-warning text-dark',
                                        'APPROVED' => 'bg-primary text-white',
                                        'ACTIVE' => 'bg-success text-white',
                                        'WARNING_EXPIRY' => 'bg-warning text-dark',
                                        'EXPIRED' => 'bg-danger text-white',
                                        'ADDENDUM' => 'bg-primary text-white',
                                        'CANCELLED' => 'bg-dark text-white',
                                        default => 'bg-secondary text-white'
                                    };

                                    $docAvailEvents = $fullFsmMatrix[$doc->status] ?? [];
                                @endphp
                                <tr class="doc-row" data-status="{{ $doc->status }}" data-type="{{ $doc->doc_type }}">
                                    <!-- 1. DEDICATED QR CODE CONTAINER PER ROW -->
                                    <td class="text-center p-2">
                                        <div class="p-1 border rounded bg-white shadow-sm d-inline-block">
                                            <div class="row-qr-canvas" data-reg-id="{{ $doc->reg_id }}" style="width: 68px; height: 68px; display: flex; align-items: center; justify-content: center; margin: 0 auto;"></div>
                                        </div>
                                        <div class="badge bg-light text-dark border mt-1 font-monospace" style="font-size: 0.65rem;">
                                            {{ $doc->reg_id }}
                                        </div>
                                        <div class="d-flex justify-content-center gap-1 mt-1">
                                            <button type="button" class="btn btn-outline-dark btn-sm py-0 px-1" style="font-size: 0.65rem;" title="Perbesar QR Code" onclick="showQrModal('{{ $doc->reg_id }}', '{{ $doc->doc_number ?: 'Dalam Proses' }}', '{{ addslashes(str_replace(["\r", "\n"], ' ', $doc->scope)) }}', '{{ addslashes($doc->partner_name) }}', '{{ $doc->status }}', '{{ $doc->date_signed ? $doc->date_signed->format('d M Y') : ($doc->date_approved ? $doc->date_approved->format('d M Y') : date('d M Y')) }}', '{{ addslashes($doc->partner_doc_number ?? '') }}')">
                                                <i class="bi bi-arrows-fullscreen"></i>
                                            </button>
                                            <a href="{{ route('simkerma.verify', ['reg_id' => $doc->reg_id]) }}" target="_blank" class="btn btn-outline-primary btn-sm py-0 px-1" style="font-size: 0.65rem;" title="Buka Lembar Verifikasi Publik">
                                                <i class="bi bi-box-arrow-up-right"></i>
                                            </a>
                                        </div>
                                    </td>

                                    <!-- 2. NOMOR DOKUMEN & JENIS -->
                                    <td>
                                        <div class="fw-bold text-dark">
                                            {{ $doc->doc_number ?: 'Dalam Proses' }}
                                            <span class="badge bg-light text-dark border" style="font-size: 0.62rem;">Pihak I</span>
                                        </div>
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

                                    <!-- 3. MITRA KERJA SAMA & KATEGORI -->
                                    <td>
                                        <strong class="text-primary d-block">{{ $doc->partner_name }}</strong>
                                        <span class="text-muted" style="font-size: 0.74rem;">
                                            <i class="bi bi-building me-1"></i>{{ $doc->partner_category }}
                                        </span>
                                    </td>

                                    <!-- 4. UNIT PENGUSUL & RUANG LINGKUP -->
                                    <td>
                                        <div class="fw-semibold text-dark">{{ $doc->unit }}</div>
                                        @if (!empty($doc->collab_unit))
                                            <div class="text-muted" style="font-size: 0.72rem;">Kolaborator: {{ $doc->collab_unit }}</div>
                                        @endif
                                        <div class="text-secondary small mt-1" style="max-width: 250px; white-space: normal;">
                                            {{ \Illuminate\Support\Str::limit($doc->scope, 85) }}
                                        </div>
                                    </td>

                                    <!-- 5. MASA BERLAKU & AMBANG BATAS DINI -->
                                    <td>
                                        <div><strong>{{ $doc->validity ?: '1 Tahun' }}</strong></div>
                                        @if (!empty($doc->date_signed))
                                            <div class="text-muted" style="font-size: 0.72rem;">TTD: {{ $doc->date_signed }}</div>
                                        @endif
                                        <div class="mt-1">
                                            <span class="badge bg-warning-subtle text-dark border" style="font-size: 0.68rem;" title="Ambang Batas Peringatan Dini">
                                                <i class="bi bi-bell-fill text-warning me-1"></i>{{ $doc->warning_threshold ?? 'H-90 Hari' }}
                                            </span>
                                        </div>
                                    </td>

                                    <!-- 6. STATUS SIKLUS FSM -->
                                    <td class="text-center">
                                        <span class="badge {{ $stBadge }} py-2 px-2 d-block fw-bold shadow-sm" style="font-size: 0.74rem;">
                                            {{ $doc->status }}
                                        </span>
                                        <div class="text-muted small mt-1" style="font-size: 0.68rem;">
                                            <i class="bi bi-clock-history me-1"></i>{{ $doc->auditTrails->count() }} Jejak Audit
                                        </div>
                                    </td>

                                    <!-- 7. AKSI FSM & DISPOSISI -->
                                    <td class="text-end">
                                        <button type="button" class="btn btn-dark btn-sm fw-bold w-100 mb-1 shadow-sm py-1" data-bs-toggle="modal" data-bs-target="#fsmModal_{{ $doc->id }}" style="font-size: 0.75rem;">
                                            <i class="bi bi-sliders me-1 text-warning"></i> Kelola Alur FSM
                                        </button>
                                        
                                        <div class="d-flex gap-1 justify-content-end">
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

                                                <!-- 2 Kolom Ringkasan Informasi Dokumen -->
                                                <div class="row g-3 mb-3 small">
                                                    <div class="col-sm-6">
                                                        <div class="bg-light p-3 rounded border h-100">
                                                            <div class="text-muted fw-bold" style="font-size: 0.72rem;">IDENTITAS DOKUMEN:</div>
                                                            <div><strong>No. Reg:</strong> {{ $doc->reg_id }}</div>
                                                            <div><strong>No. Naskah (Pihak I):</strong> {{ $doc->doc_number ?: '-' }}</div>
                                                            <div><strong>No. Naskah (Pihak II):</strong> {{ $doc->partner_doc_number ?: '-' }}</div>
                                                            <div><strong>Jalur:</strong> {{ $doc->track }}</div>
                                                            <div><strong>Unit Pengusul:</strong> {{ $doc->unit }}</div>
                                                            @if (!empty($doc->collab_unit))
                                                                <div><strong>Kolaborator:</strong> {{ $doc->collab_unit }}</div>
                                                            @endif
                                                            <div><strong>Dokumen Induk:</strong> {{ $doc->parent_mou ?: '-' }}</div>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <div class="bg-light p-3 rounded border h-100">
                                                            <div class="text-muted fw-bold" style="font-size: 0.72rem;">KETENTUAN &amp; STATUS:</div>
                                                            <div><strong>Masa Berlaku:</strong> {{ $doc->validity }}</div>
                                                            <div><strong>Peringatan Dini:</strong> <span class="text-warning text-dark fw-bold">{{ $doc->warning_threshold ?? 'H-90 Hari' }}</span></div>
                                                            <div><strong>Status Aktif:</strong> <span class="badge {{ $stBadge }}">{{ $doc->status }}</span></div>
                                                            @if (!empty($doc->signed_file))
                                                                <div class="text-success mt-1"><i class="bi bi-file-earmark-check-fill me-1"></i>Scan Fisik Bermeterai Terverifikasi</div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="p-2 border rounded bg-white">
                                                            <span class="text-muted fw-bold d-block" style="font-size: 0.72rem;">Ruang Lingkup / Substansi:</span>
                                                            <div class="text-dark">{{ $doc->scope }}</div>
                                                        </div>
                                                    </div>
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

                                                            @if ($doc->status === 'APPROVED')
                                                                <div class="mb-3 p-3 bg-light rounded border small">
                                                                    <div class="fw-bold text-dark mb-2">
                                                                        <i class="bi bi-pen-fill text-primary me-1"></i> Pengesahan Naskah Fisik &amp; Nomor Pihak Kedua (Mitra):
                                                                    </div>
                                                                    <div class="row g-2 mb-2">
                                                                        <div class="col-sm-6">
                                                                            <label class="form-label fw-bold text-dark" style="font-size: 0.75rem;">Nomor Naskah Pihak I (UNIPAR):</label>
                                                                            <input type="text" class="form-control form-control-sm bg-white" value="{{ $doc->doc_number }}" readonly>
                                                                        </div>
                                                                        <div class="col-sm-6">
                                                                            <label class="form-label fw-bold text-dark" style="font-size: 0.75rem;">Nomor Naskah Pihak II (Mitra):</label>
                                                                            <input type="text" name="partner_doc_number" class="form-control form-control-sm" value="{{ $doc->partner_doc_number }}" placeholder="Nomor resmi dari instansi mitra" {{ in_array($activeRole, ['LEADER', 'MASTER'], true) ? 'required' : 'disabled' }}>
                                                                        </div>
                                                                    </div>
                                                                    <label class="form-label fw-bold text-dark d-block" style="font-size: 0.75rem;">
                                                                        <i class="bi bi-file-earmark-check text-primary me-1"></i>
                                                                        Unggah Berkas Scan Bertanda Tangan &amp; Bermeterai (PDF):
                                                                    </label>
                                                                    <input type="file" name="signed_scan_file" class="form-control form-control-sm mb-1" {{ in_array($activeRole, ['LEADER', 'MASTER'], true) ? 'required' : 'disabled' }}>
                                                                </div>
                                                            @endif

                                                            <div class="mb-3">
                                                                <label class="form-label fw-bold small text-muted">Catatan Disposisi / Keputusan Pejabat:</label>
                                                                <textarea name="notes" class="form-control form-control-sm" rows="2"></textarea>
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
                                                <a href="{{ route('simkerma.verify', ['reg_id' => $doc->reg_id]) }}" target="_blank" class="btn btn-outline-dark btn-sm">
                                                    <i class="bi bi-box-arrow-up-right me-1"></i> Buka Lembar Verifikasi
                                                </a>
                                                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            @empty
                                <tr id="tableEmptyRow">
                                    <td colspan="7" class="text-center py-5 text-muted">
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
            $totalActiveDocs = $documents->whereIn('status', ['ACTIVE', 'WARNING_EXPIRY'])->count();
            $totalActivities = $activities->count();
            $runningActivities = $activities->where('execution_status', 'SEDANG_BERJALAN')->count();
            $completedActivities = $activities->where('execution_status', 'SELESAI')->count();
        @endphp

        <!-- KARTU METRIK RINGKASAN PELAKSANAAN KERJA SAMA (REAL-TIME DARI MYSQL) -->
        <div class="row g-3 mb-4">
            <div class="col-6 col-md-3">
                <div class="card border shadow-sm p-3 bg-white h-100">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle bg-success-subtle text-success d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                            <i class="bi bi-file-earmark-check fs-4"></i>
                        </div>
                        <div>
                            <div class="text-muted small fw-semibold text-uppercase" style="font-size: 0.72rem;">Dokumen Berlaku (Menu 04)</div>
                            <h4 class="mb-0 fw-bold text-dark">{{ $totalActiveDocs }} <span class="fs-6 fw-normal text-muted">Naskah</span></h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card border shadow-sm p-3 bg-white h-100">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                            <i class="bi bi-play-circle fs-4"></i>
                        </div>
                        <div>
                            <div class="text-muted small fw-semibold text-uppercase" style="font-size: 0.72rem;">Kegiatan Sedang Berjalan</div>
                            <h4 class="mb-0 fw-bold text-dark">{{ $runningActivities }} <span class="fs-6 fw-normal text-muted">Program</span></h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card border shadow-sm p-3 bg-white h-100">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle bg-dark-subtle text-dark d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                            <i class="bi bi-check2-all fs-4"></i>
                        </div>
                        <div>
                            <div class="text-muted small fw-semibold text-uppercase" style="font-size: 0.72rem;">Kegiatan Selesai &amp; Arsip</div>
                            <h4 class="mb-0 fw-bold text-dark">{{ $completedActivities }} <span class="fs-6 fw-normal text-muted">Program</span></h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card border shadow-sm p-3 bg-white h-100">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle bg-warning-subtle text-warning d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                            <i class="bi bi-award fs-4"></i>
                        </div>
                        <div>
                            <div class="text-muted small fw-semibold text-uppercase" style="font-size: 0.72rem;">Total Realisasi Capaian</div>
                            <h4 class="mb-0 fw-bold text-dark">{{ $totalActivities }} <span class="fs-6 fw-normal text-muted">Realisasi</span></h4>
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
                    <button type="button" class="btn btn-dark btn-sm fw-semibold shadow-sm" data-bs-toggle="modal" data-bs-target="#modalCreateActivity">
                        <i class="bi bi-plus-lg me-1 text-warning"></i> Catat Kegiatan Baru
                    </button>
                </div>
            </div>

            <!-- FILTER & SEARCH BAR SISI KLIEN -->
            <div class="card-body border-bottom bg-light py-2">
                <div class="row g-2 align-items-center justify-content-between">
                    <div class="col-12 col-md-auto">
                        <div class="d-flex flex-wrap align-items-center gap-1 small">
                            <span class="text-muted fw-bold me-2" style="font-size: 0.78rem;">Status Pelaksanaan:</span>
                            <button type="button" class="btn btn-dark btn-sm py-1 px-3 filter-act-btn active" data-filter="ALL">Semua ({{ $totalActivities }})</button>
                            <button type="button" class="btn btn-outline-secondary btn-sm py-1 px-2 filter-act-btn" data-filter="SEDANG_BERJALAN">Sedang Berjalan ({{ $runningActivities }})</button>
                            <button type="button" class="btn btn-outline-secondary btn-sm py-1 px-2 filter-act-btn" data-filter="SELESAI">Selesai ({{ $completedActivities }})</button>
                            <button type="button" class="btn btn-outline-secondary btn-sm py-1 px-2 filter-act-btn" data-filter="BELUM_DIMULAI">Belum Dimulai</button>
                            <button type="button" class="btn btn-outline-secondary btn-sm py-1 px-2 filter-act-btn" data-filter="TERTUNDA">Tertunda</button>
                        </div>
                    </div>
                    <div class="col-12 col-md-4">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                            <input type="text" id="activitySearchInput" class="form-control border-start-0" placeholder="Cari kegiatan, mitra, no surat...">
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
                                    'LEGAL_REVIEW' => 'bg-info text-dark',
                                    'REVISION' => 'bg-warning text-dark',
                                    'APPROVED' => 'bg-primary text-white',
                                    'ACTIVE' => 'bg-success text-white',
                                    'WARNING_EXPIRY' => 'bg-warning text-dark',
                                    'EXPIRED' => 'bg-danger text-white',
                                    'ADDENDUM' => 'bg-primary text-white',
                                    'CANCELLED' => 'bg-dark text-white',
                                    default => 'bg-secondary text-white'
                                };

                                $actStatusBadge = match($act->execution_status) {
                                    'SEDANG_BERJALAN' => 'bg-success text-white',
                                    'SELESAI' => 'bg-dark text-white',
                                    'BELUM_DIMULAI' => 'bg-secondary text-white',
                                    'TERTUNDA' => 'bg-warning text-dark',
                                    default => 'bg-primary text-white'
                                };

                                $actStatusLabel = match($act->execution_status) {
                                    'SEDANG_BERJALAN' => 'Sedang Berjalan',
                                    'SELESAI' => 'Selesai / Terarsip',
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
