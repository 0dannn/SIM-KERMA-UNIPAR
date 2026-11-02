# DOKUMEN MASTER HANDOVER & KONSOLIDASI KONTEKS PROYEK SKRIPSI
## SISTEM INFORMASI DOKUMEN KERJA SAMA BERBASIS FINITE STATE MACHINE (SIMKERMA)
### UNIVERSITAS PARNA RAYA (UNIPAR) MANADO — TAHUN 2026

> **CATATAN UNTUK AI PADA SESI / PERANGKAT LAIN (HANDOVER RESMI TERKINI CONVO 5 MENUJU CONVO 6):**  
> Dokumen ini memuat seluruh rekam jejak keputusan metodologis, arsitektur perangkat lunak, model matematis otomata, skema basis data, implementasi stempel QR Code OpenXML, sistem autentikasi multi-peran, adaptasi antarmuka pengguna (UI), dan repositori GitHub dari skripsi mahasiswa **Jordan Immanuel Pusung**. Gunakan seluruh konteks di bawah ini sebagai dasar utama untuk melanjutkan bimbingan, revisi naskah Word, penyusunan skenario demo, maupun pembuatan slide presentasi tanpa perlu bertanya ulang dari nol.

---

## 1. Identitas Akademik & Tim Penelitian

* **Nama Mahasiswa:** Jordan Immanuel Pusung
* **NIM:** 2355201011
* **Program Studi:** S1 Teknik Informatika
* **Perguruan Tinggi:** Universitas Parna Raya (UNIPAR) Manado
* **Tahun Akademik:** 2026
* **Komisi Pembimbing:**
  1. **Pembimbing I:** Victor B.C. Tuilan, S.Pd., M.Pd.  
     *(Keahlian: Tata kelola kelembagaan, kurikulum, penjaminan mutu, regulasi Dikti & IKU 6).*
  2. **Pembimbing II:** Franky G.C. Manoppo, ST., M.Kom.  
     *(Keahlian: Rekayasa perangkat lunak, Finite State Machine, arsitektur basis data, otomasi & keamanan sistem).*
* **Judul Skripsi Ditetapkan:**  
  **"Rancang Bangun Sistem Informasi Dokumen Kerja Sama Berbasis Finite State Machine di Universitas Parna Raya"**  
  *(FSM dipertahankan pada judul sebagai kontribusi keilmuan Teknik Informatika/Otomata, sedangkan Contract Lifecycle Management/CLM diposisikan sebagai domain masalah bisnis pada Bab I & II).*

---

## 2. Fondasi Metodologis & Keputusan Akademis Penting

1. **Eliminasi Total SHA-256 Hash:**
   * Seluruh klaim penggunaan hash SHA-256 untuk memvalidasi dokumen fisik bertanda tangan basah dan bermeterai telah **dihapus 100%** dari proposal dan aplikasi.
   * *Alasan Akademis:* Pemindaian scanner fisik rentan terhadap *scanner noise*, variasi resolusi DPI, dan efek *avalanche* hash kriptografi, sehingga hash file PDF hasil scan pasti berbeda setiap kali discan ulang.
   * *Solusi Pengganti:* Penerapan **FSM Guard Condition** (verifikasi administratif & upload scan berkas sah bertanda tangan basah dan bermeterai sebelum status aktif) dipadukan dengan **Dynamic QR Code Registrasi Dokumen** yang terhubung langsung ke laman verifikasi publik resmi institusi (`/verify/{reg_id}`).

2. **Dual-Track Workflow Kemitraan (Berdasarkan 12 Dokumen Faktual UNIPAR):**
   * **Jalur 1: Kemitraan Bertahap (Standard Stage-Gate):** Pengusulan naskah secara berjenjang dari draf awal $\rightarrow$ MoU $\rightarrow$ MoA $\rightarrow$ IA.
   * **Jalur 2: Paket Terpadu Tridharma & PKM (One-Gate Expedited):** Pengusulan serentak 1 paket berkas MoU, MoA, dan IA yang telah ditandatangani basah oleh pimpinan institusi mitra di lokasi tempat mahasiswa bekerja/magang, dengan masa berlaku 1 tahun dan keterlibatan multi-unit kolaborator IKU 6.

3. **Metode Evaluasi Kuantitatif (Skala Likert Sugiyono 2019):**
   * Standar evaluasi menggunakan **Kuesioner Skala Likert 5 Poin** (Sugiyono, 2019; Arikunto, 2013), menggantikan instrumen SUS generik.
   * Formula:
     $$P = \frac{\sum X}{\sum X_{\text{maks}}} \times 100\%$$
   * Terdiri dari 15 butir pernyataan pada 3 dimensi (*Usability*, *Efisiensi Interaksi FSM*, dan *Kepuasan Pengguna UX*) untuk 4 kelompok aktor: Unit Pengusul, Tim Hukum/Biro Kerja Sama, Pimpinan/Rektorat, dan Auditor/LPM.

---

## 3. Formalisasi Matematis Model Finite State Machine (FSM)

Sistem dimodelkan secara matematis sebagai 6-Tuple:
$$M = (Q, \Sigma, \delta, q_0, F, G)$$

* **$Q$ (Himpunan Status Diskret Operasional MySQL):**
  $$Q = \{\text{DRAFT}, \text{REVIEW}, \text{ACTIVE}, \text{WARNING}, \text{EXPIRED}, \text{CANCELLED}\}$$
* **$\Sigma$ (Himpunan Simbol Masukan / Event Pemicu):**
  $$\Sigma = \{\text{SUBMIT}, \text{CANCEL}, \text{RETURN\_TO\_DRAFT}, \text{ACTIVATE}, \text{TRIGGER\_WARNING}, \text{RENEW}, \text{EXPIRE}\}$$
* **$q_0$ (Status Awal):** $\text{DRAFT}$
* **$F$ (Status Akhir / Terminal State):** $\{\text{EXPIRED}, \text{CANCELLED}\}$
* **$\delta$ (Matriks 8 Transisi Legal Formal):**
  1. $\delta(\text{DRAFT}, \text{SUBMIT}) = \text{REVIEW}$
  2. $\delta(\text{DRAFT}, \text{CANCEL}) = \text{CANCELLED}$
  3. $\delta(\text{REVIEW}, \text{RETURN\_TO\_DRAFT}) = \text{DRAFT}$
  4. $\delta(\text{REVIEW}, \text{ACTIVATE}) = \text{ACTIVE}$
  5. $\delta(\text{ACTIVE}, \text{TRIGGER\_WARNING}) = \text{WARNING}$ (Otomatis saat ambang H-90 / H-60)
  6. $\delta(\text{WARNING}, \text{RENEW}) = \text{ACTIVE}$
  7. $\delta(\text{WARNING}, \text{EXPIRE}) = \text{EXPIRED}$
  8. $\delta(\text{EXPIRED}, \text{RENEW}) = \text{ACTIVE}$
* **$G$ (Guard Conditions & Anti-Bypass Rules):**
  * Transisi $\text{REVIEW} \rightarrow \text{ACTIVE}$ mewajibkan: input Nomor Surat Pihak Mitra (`partner_doc_number`) dan unggah scan PDF berkas sah bertanda tangan basah dan bermeterai (`signed_scan_file`).
  * Penolakan naskah $\text{REVIEW} \rightarrow \text{DRAFT}$ bersifat *Zero-Field Reject* (tidak mengunci field nomor mitra atau scan).
  * Penghapusan berkas naskah HANYA diizinkan pada status $\text{DRAFT}$. Naskah berstatus selain draf dikunci permanen demi integritas rekam jejak (*Immutable Audit Trail*).

---

## 4. Arsitektur Perangkat Lunak & Spesifikasi Teknis

* **Framework Backend:** Laravel 11.57.1
* **Bahasa Pemrograman:** PHP 8.2 / 8.4
* **Basis Data:** MySQL (`simkerma_db`) pada port default `3306`
* **Antarmuka Frontend:** Blade Template, Bootstrap 5.3.3, Bootstrap Icons, Font *Public Sans*
* **Repositori GitHub:** [https://github.com/0dannn/SIM-KERMA-UNIPAR.git](https://github.com/0dannn/SIM-KERMA-UNIPAR.git) (Branch `main`, Commit `ebf86b7`)

### Skema Tabel Basis Data MySQL (`simkerma_db`):
1. **`users`:** Akun pengguna multi-peran (`id`, `name`, `email`, `role`, `unit`, `phone`, `password`, `timestamps`).
2. **`documents`:** Menyimpan naskah kerja sama (`id`, `doc_id`, `reg_id`, `doc_number`, `partner_doc_number`, `track`, `doc_type`, `parent_mou`, `unit`, `collab_unit`, `partner_name`, `partner_category`, `scope`, `validity`, `warning_threshold`, `status`, `date_approved`, `date_signed`, `draft_file`, `signed_file`, `docx_path`, `notes`, `timestamps`).
3. **`partners`:** Master data direktori mitra (`id`, `name`, `category`, `scope_coverage`, `address`, `city`, `phone`, `email`, `pic_name`, `pic_title`, `pic_contact`, `profile_desc`, `status_verification`, `timestamps`).
4. **`audit_trails`:** Rekam jejak matematis FSM tak terhapuskan (`id`, `document_id`, `actor`, `role`, `event`, `from_status`, `to_status`, `notes`, `timestamp_wita`, `timestamps`).
5. **`activities`:** Pencatatan realisasi kegiatan kerja sama di lapangan (`id`, `document_id`, `activity_name`, `schedule_start`, `schedule_end`, `target_qty`, `realized_qty`, `parties_involved`, `outputs`, `budget_info`, `evidence_file`, `evidence_photos`, `execution_status`, `notes`, `timestamps`).

---

## 5. Fitur Mutakhir: Autentikasi Role, QR Stamping, & Desain UI Ramping

### A. Template Baru Draf Naskah MoU
* **Berkas Baku:** `Form Template/Template_MoU_UNIPAR.docx` (96.181 bytes).
* **Lokasi Publik:** `public/templates/Template_MoU_UNIPAR.docx`.
* **Karakteristik:** Template murni dan bersih tanpa placeholder QR Code (hanya memuat logo UNIPAR pada kop surat).

### B. Engine Otomasi Stempel QR Code (`DocxQrStampService.php`)
* **Resolusi Gambar QR Code:** $500 \times 500$ piksel (ultra jernih untuk dipindai kamera).
* **Dimensi XML OpenXML:** `cx="765387" cy="765387"` EMU (setara 200 / $\approx 2{,}12$ cm).
* **Posisi Presisi:** Diinjeksikan pada **Row 0, Cell 1 tepat di atas tulisan "Pihak Pertama"** dengan perataan tengah (`w:jc w:val="center"`).
* **Ruang Tanda Tangan Basah Rektor:** Baris di atas nama Rektor (**Dr. Rosdiana Simbolon, SE., S.Kom., M.Si**) dijamin 100% bersih tanpa gambar, memberikan ruang resmi untuk penandatanganan basah dan pembubuhan cap stempel institusi.
* **Konversi Otomatis:** Sistem mengekspor dokumen Word ber-QR Code menjadi berkas PDF telaah resmi secara instan via Microsoft Word COM native (`ExportAsFixedFormat`).

### C. Sistem Autentikasi Multi-Peran & Portal Login (`/login`)
* **Endpoint Login Resmi:** `GET /login`, `POST /login`, `GET /logout`.
* **Empat Akun Peran Terenkripsi Bcrypt:**
  1. `admin@unipar.ac.id` / `admin123` *(ADMIN - Superuser)*
  2. `unit@unipar.ac.id` / `unit123` *(UNIT - Prodi S1 TI)*
  3. `legal@unipar.ac.id` / `legal123` *(LEGAL - Biro Hukum)*
  4. `rektor@unipar.ac.id` / `rektor123` *(LEADER - Rektorat UNIPAR)*
* **Tombol Cepat Akun Demo (Quick-Fill):** Mengisi kredensial secara instan dalam satu klik untuk kemudahan demonstrasi sidang tanpa harus mengetik manual.
* **Profil Pengguna & Logout:** Dropdown profil aktif terpasang pada header atas dan kartu identitas pada footer sidebar.

### D. Optimasi Tata Letak Sidebar Ramping (Hemat 15% Ruang)
* **Pembersihan Nomor Menu:** Seluruh badge angka (01, 02, dst.) pada navigasi sidebar telah dihilangkan sehingga tampilan menjadi lebih elegan dan profesional.
* **Perampingan Lebar:** Lebar sidebar diperkecil 15% dari `280px` menjadi `238px`.
* **Keuntungan:** Area konten utama (tabel arsip dokumen, form pengusulan, matriks FSM) mendapatkan ruang ekstra ~42px, mencegah tabel tampak terhimpit.

---

## 6. Ringkasan Implementasi 7 Menu Utama SIMKERMA

| No | Nama Menu Institusional | Status Fitur & Kesiapan |
|---|---|---|
| **01** | **Dashboard Kerja Sama** | Menampilkan metrik real-time naskah aktif, peringatan masa, draf, sebaran kategori mitra, dan persentase capaian IKU 6 perguruan tinggi. |
| **02** | **Pengelolaan Data Mitra** | Master data mandiri berbasis tabel `partners`. Dilengkapi tombol `+ Input Mitra Mandiri`, `Edit Data Mitra`, modal detail Profil & Riwayat Lengkap, filter multi-parameter cerdas, serta sinkronisasi kaskade dua arah ke seluruh dokumen. |
| **03** | **Pengelolaan Dokumen Kerja Sama** | Repositori dinamis arsip naskah MoU, MoA/PKS, dan IA yang terorganisasi rapi. |
| **04** | **Pengajuan & Persetujuan Kerja Sama** | Mesin FSM transisi 6 status, QR Code terintegrasi di atas badge status, form Jalur 1 & Jalur 2 dengan `<datalist>` mitra terdaftar, Checklist Telaah Mutu (MBKM/IKU 6), form penolakan bebas hambatan (*zero-field reject*), dan fitur hapus khusus status DRAFT. |
| **05** | **Pemantauan Pelaksanaan Kerja Sama** | Deteksi ambang batas kedaluwarsa H-90/H-60, panel inisiasi adendum perpanjangan memfasilitasi nomor surat pihak 1 (UNIPAR) & pihak 2 (Mitra), 5 widget metrik pastel, serta form pencatatan kegiatan Tridharma. |
| **06** | **Evaluasi Pelaksanaan Kerja Sama** | Modul evaluasi dokumen kerja sama dan visualisasi instrumen kuesioner Skala Likert 5 Poin (Sugiyono, 2019). |
| **07** | **Laporan & Audit Trail** | Rekam jejak audit trail matematis FSM berstempel waktu WITA yang mencatat seluruh aktor, peristiwa, status awal, dan status tujuan secara permanen. |
| **Public** | **Verifikasi Publik QR Code** | Halaman `/verify/{reg_id}` dapat dipindai secara nyata menggunakan kamera smartphone tanpa perlu login. |

---

## 7. Lokasi Berkas Fisik & Struktur Direktori

```
Skripsi2/
├── SIM-KERMA-UNIPAR/                                <-- Proyek Utama Laravel 11 (Terkoneksi GitHub)
│   ├── app\Http\Controllers\SimkermaController.php  <-- Controller Utama (CRUD, FSM, Partner, Export)
│   ├── app\Http\Controllers\AuthController.php      <-- Controller Autentikasi (Login & Logout)
│   ├── app\Services\FSMWorkflowEngineService.php    <-- Core Mesin FSM & Guard Condition
│   ├── app\Services\DocxQrStampService.php          <-- Engine Stempel QR 500px & Word COM Export PDF
│   ├── app\Models\User.php                          <-- Model Akun Pengguna Multi-Role
│   ├── app\Models\Document.php                      <-- Model Dokumen Naskah
│   ├── app\Models\Partner.php                       <-- Model Master Data Mitra
│   ├── app\Models\AuditTrail.php                    <-- Model Audit Trail FSM
│   ├── app\Models\Activity.php                      <-- Model Kegiatan Pelaksanaan
│   ├── database\migrations\                         <-- Berkas Migrasi MySQL (Users, Documents, etc.)
│   ├── database\seeders\UserSeeder.php              <-- Seeder Akun Resmi 4 Peran
│   ├── public\templates\Template_MoU_UNIPAR.docx    <-- Berkas Draf Template MoU Baku Publik
│   ├── resources\views\auth\login.blade.php         <-- Tampilan Portal Login Modern
│   ├── resources\views\layouts\app.blade.php        <-- Layout Induk (Sidebar Ramping 238px & User Profile)
│   ├── resources\views\simkerma\index.blade.php     <-- View Utama 7 Menu Modern
│   ├── resources\views\simkerma\verify.blade.php    <-- View Publik Verifikasi QR Code
│   ├── docs\convo-4.txt                             <-- Master Log Convo 4
│   ├── docs\convo-5.txt                             <-- Master Log Convo 5
│   ├── README.md                                    <-- Panduan Proyek Komprehensif
│   └── routes\web.php                               <-- Seluruh Rute HTTP Aplikasi
│
├── convo-4 01 Nov 2026 continous (Internal Chat)\   <-- Arsip Sesi Convo 4
└── convo-5 02 Nov 2026 continous (Internal Chat)\   <-- Berkas Catatan Sesi Convo 5
```

---

## 8. Panduan Menjalankan Sistem di Komputer Baru

Jika berpindah ke komputer atau sesi AI yang baru:

1. **Kloning Repositori GitHub:**
   ```bash
   git clone https://github.com/0dannn/SIM-KERMA-UNIPAR.git
   cd SIM-KERMA-UNIPAR
   ```

2. **Pasang Dependensi PHP / Composer:**
   ```bash
   composer install
   ```

3. **Konfigurasi Environment `.env`:**
   ```bash
   copy .env.example .env
   php artisan key:generate
   ```
   *Pastikan MySQL (XAMPP) menyala pada port 3306 dengan konfigurasi `DB_DATABASE=simkerma_db`, `DB_USERNAME=root`, `DB_PASSWORD=` (kosong).*

4. **Jalankan Migrasi dan Database Seeder:**
   ```bash
   php artisan migrate:fresh --seed
   ```

5. **Nyalakan Peladen Web:**
   ```bash
   php -S 127.0.0.1:8000 -t public
   ```

6. **Buka Aplikasi di Peramban:**
   * Portal Login: [http://127.0.0.1:8000/login](http://127.0.0.1:8000/login)
   * Beranda Aplikasi: [http://127.0.0.1:8000](http://127.0.0.1:8000)

---

## 9. Agenda & Tugas Prioritas Menuju Sesi Berikutnya (Convo 6)

1. **Penyusunan Skenario Panduan Demonstrasi Sistem (Alur Demo Interaktif 7–10 Menit):**
   * Menyusun naskah peragaan langsung di hadapan dosen pembimbing (Pak Victor Tuilan dan Pak Franky Manoppo) dengan mensimulasikan login multi-peran (`UNIT` $\rightarrow$ `LEGAL` $\rightarrow$ `LEADER`).
2. **Penyelarasan Naskah Word Skripsi (`Proposal - Main - Revisi.docx`):**
   * Memastikan sinkronisasi 100% pada Bab I (5 butir Batasan Masalah), Bab II & III (FSM 6 status, 8 transisi formal, diagram statechart, dan eliminasi SHA-256), serta Bab IV (Matriks State Transition Testing dan Skala Likert Sugiyono).
3. **Penyusunan Bahan Paparan / Slide Presentasi Proposal:**
   * Menyiapkan poin ringkasan eksekutif untuk mempertahankan kebaruan ilmiah FSM di hadapan komisi penguji.

---

## 10. Template Prompt Handover untuk AI di Sesi Baru (Convo 6)

> **Salin dan tempelkan teks berikut ke sesi chat AI yang baru:**
```text
Halo AI, saya Jordan Immanuel Pusung (NIM: 2355201011), mahasiswa S1 Teknik Informatika Universitas Parna Raya (UNIPAR) Manado. Saya sedang menyelesaikan skripsi:
"Rancang Bangun Sistem Informasi Dokumen Kerja Sama Berbasis Finite State Machine di Universitas Parna Raya"
Pembimbing I: Victor B.C. Tuilan, S.Pd., M.Pd. | Pembimbing II: Franky G.C. Manoppo, ST., M.Kom.

Seluruh kode sumber aplikasi nyata telah selesai dibangun menggunakan Laravel 11 + MySQL (simkerma_db) dan sudah tersimpan di repositori GitHub resmi saya:
https://github.com/0dannn/SIM-KERMA-UNIPAR

Konteks penting yang TIDAK BOLEH diubah atau dikurangi:
1. FSM 6 Status Nyata di MySQL: DRAFT, REVIEW, ACTIVE, WARNING, EXPIRED, CANCELLED.
2. Guard Condition Berkas Fisik: Transisi ke status ACTIVE mewajibkan unggah scan PDF naskah sah bertanda tangan basah dan bermeterai serta nomor surat mitra (UU No. 10 Tahun 2020 Bea Meterai).
3. Hashing SHA-256 telah 100% DIELIMINASI dan digantikan oleh FSM Guard + Dynamic QR Code Registrasi Dokumen ke portal /verify/{reg_id}.
4. Dual-Track Workflow: Jalur 1 (Bertahap Draf -> MoU -> MoA -> IA) dan Jalur 2 (Paket Terpadu 1 Tahun PKM/Tridharma).
5. Evaluasi Kuantitatif menggunakan Skala Likert 5 Poin (Sugiyono, 2019; Arikunto, 2013), bukan SUS.
6. Engine QR Stamping (DocxQrStampService.php) menyematkan QR 500px tepat di atas teks "Pihak Pertama". Ruang di atas nama Rektor (Dr. Rosdiana Simbolon) 100% bersih untuk tanda tangan basah & stempel institusi.
7. Sistem Autentikasi Multi-Peran: Halaman /login dengan 4 akun resmi (ADMIN, UNIT, LEGAL, LEADER) dan tombol quick-fill akun demo.
8. Sidebar Ramping (238px): Telah bersih dari penomoran menu dan memberikan ruang ekstra ~42px bagi konten utama.

Sekarang kita berada di sesi lanjutan (Convo 6). Tolong baca master log di folder "SIM-KERMA-UNIPAR/docs/convo-5.txt" dan panduan handover "AI_HANDOVER_CONTEXT_LENGKAP.md", lalu bantu saya mengerjakan agenda selanjutnya.
```
