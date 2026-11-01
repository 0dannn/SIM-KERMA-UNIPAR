# DOKUMEN MASTER HANDOVER & KONSOLIDASI KONTEKS PROYEK SKRIPSI
## SISTEM INFORMASI DOKUMEN KERJA SAMA BERBASIS FINITE STATE MACHINE (SIMKERMA)
### UNIVERSITAS PARNA RAYA (UNIPAR) MANADO — TAHUN 2026

> **CATATAN UNTUK AI PADA SESI / PERANGKAT LAIN (HANDOVER RESMI DARI CONVO 4 KE CONVO 5):**  
> Dokumen ini memuat seluruh rekam jejak keputusan metodologis, arsitektur perangkat lunak, model matematis otomata, skema basis data, implementasi stempel QR Code OpenXML, adaptasi antarmuka pengguna (UI), dan repositori GitHub dari skripsi mahasiswa **Jordan Immanuel Pusung**. Gunakan seluruh konteks di bawah ini sebagai dasar utama untuk melanjutkan bimbingan, revisi naskah Word, penyusunan skenario demo, maupun pembuatan slide presentasi tanpa perlu bertanya ulang dari nol.

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
* **Bahasa Pemrograman:** PHP 8.2.12
* **Basis Data:** MySQL (`simkerma_db`) pada port default `3306`
* **Antarmuka Frontend:** Blade Template, Bootstrap 5.3.3, Bootstrap Icons, Font *Public Sans*
* **Repositori GitHub:** [https://github.com/0dannn/SIM-KERMA-UNIPAR.git](https://github.com/0dannn/SIM-KERMA-UNIPAR.git) (Branch `main`, Commit `06f71da`)

### Skema Tabel Basis Data MySQL (`simkerma_db`):
1. **`documents`:** Menyimpan naskah kerja sama (`id`, `doc_id`, `reg_id`, `doc_number`, `partner_doc_number`, `track`, `doc_type`, `parent_mou`, `unit`, `collab_unit`, `partner_name`, `partner_category`, `scope`, `validity`, `warning_threshold`, `status`, `date_approved`, `date_signed`, `draft_file`, `signed_file`, `docx_path`, `notes`, `timestamps`).
2. **`partners`:** Master data direktori mitra (`id`, `name`, `category`, `scope_coverage`, `address`, `city`, `phone`, `email`, `pic_name`, `pic_title`, `pic_contact`, `profile_desc`, `status_verification`, `timestamps`).
3. **`audit_trails`:** Rekam jejak matematis FSM tak terhapuskan (`id`, `document_id`, `actor`, `role`, `event`, `from_status`, `to_status`, `notes`, `timestamp_wita`, `timestamps`).
4. **`activities`:** Pencatatan realisasi kegiatan kerja sama di lapangan (`id`, `document_id`, `activity_name`, `schedule_start`, `schedule_end`, `target_qty`, `realized_qty`, `parties_involved`, `outputs`, `budget_info`, `evidence_file`, `evidence_photos`, `execution_status`, `notes`, `timestamps`).

---

## 5. Fitur Mutakhir: Template Baru, Engine QR Stamping, & Desain UI

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

### C. Kolom Tabel Status & Tombol Dinamis PDF (Menu 04)
* Header tabel disederhanakan menjadi **"Status"**.
* Tombol PDF mikro disandingkan tepat di samping tombol **"Buka Lembar Verifikasi Publik"** (`btn-sm py-0 px-1`, `font-size: 0.62rem`).
* Responsif terhadap seluruh 6 status FSM:
  * `DRAFT`: Tombol PDF tidak ditampilkan jika draf belum diajukan/diunggah.
  * `REVIEW`: Tombol merah outline (`btn-outline-danger`) membuka PDF telaah ber-QR Code.
  * `ACTIVE`: Tombol hijau outline (`btn-outline-success`) membuka PDF naskah sah bertanda tangan basah.
  * `WARNING`: Tombol kuning outline (`btn-outline-warning`) membuka PDF naskah masa peringatan dini.
  * `EXPIRED`: Tombol abu-abu outline (`btn-outline-secondary`) membuka arsip naskah kedaluwarsa.
  * `CANCELLED`: Tombol abu gelap outline (`btn-outline-dark`) membuka naskah yang dibatalkan.

### D. Adaptasi Desain Tampilan UI Modern Sesuai Acuan Figma (`UI_Reference.png`)
* **Palet Warna:** Oranye Hangat institusional (`#ea580c` / `#f97316`) sebagai brand primary, latar belakang bersih (`#f8fafc`), dan sidebar dark slate (`#1e2430`).
* **Kontainer & Card:** Putih bersih, garis batas halus (`#e2e8f0`), rounded corner 12px, soft shadow (`0 1px 3px rgba(0,0,0,0.04)`).
* **Desain Tabel:** Header `#f8fafc`, padding sel lapang 12–14px, baris hover halus `#fafbfc`.
* **Status Badges:** Format rounded-pill dengan warna pastel lembut (`#dcfce7` hijau, `#fef3c7` kuning, `#e0f2fe` biru, `#fee2e2` merah, `#f1f5f9` abu-abu).
* **Integritas Konten:** Seluruh teks asli naskah, form modal, alur FSM, dan fungsi sistem dipertahankan 100% tanpa ada yang diubah substansinya.

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
D:\CLI\Skripsi\
├── SIMKERMA_Laravel11\                              <-- Proyek Laravel 11 (Terkoneksi GitHub)
│   ├── app\Http\Controllers\SimkermaController.php  <-- Controller Utama (CRUD, FSM, Partner, Export)
│   ├── app\Services\FSMWorkflowEngineService.php    <-- Core Mesin FSM & Guard Condition
│   ├── app\Services\DocxQrStampService.php          <-- Engine Stempel QR 500px & Word COM Export PDF
│   ├── app\Models\Document.php                      <-- Model Dokumen Naskah
│   ├── app\Models\Partner.php                       <-- Model Master Data Mitra
│   ├── app\Models\AuditTrail.php                    <-- Model Audit Trail FSM
│   ├── app\Models\Activity.php                      <-- Model Kegiatan Pelaksanaan
│   ├── database\migrations\                         <-- Berkas Migrasi MySQL
│   ├── public\templates\Template_MoU_UNIPAR.docx    <-- Berkas Draf Template MoU Baku Publik
│   ├── resources\views\layouts\app.blade.php        <-- Layout Induk (Warna Oranye & Slate Sidebar)
│   ├── resources\views\simkerma\index.blade.php     <-- View Utama 7 Menu Modern
│   ├── resources\views\simkerma\verify.blade.php    <-- View Publik Verifikasi QR Code
│   ├── docs\UI\UI_Reference.png                     <-- Aset Gambar Referensi Desain
│   ├── docs\Form Template\                          <-- Aset Master Template Draf Dokumen
│   ├── README.md                                    <-- Panduan Proyek Komprehensif
│   └── routes\web.php                               <-- Seluruh Rute HTTP Aplikasi
│
├── convo-4 01 Nov 2026 continous (Internal Chat)\
│   ├── convo-4.txt                                  <-- Master Log Perkembangan Lengkap Convo 4
│   ├── AI_HANDOVER_CONTEXT_LENGKAP.md               <-- Berkas Handover Ini
│   ├── INSTRUMEN_EVALUASI_LIKERT.md                 <-- Instrumen Kuesioner Evaluasi Skripsi
│   └── Proposal\
│       ├── Proposal - Main.docx                     <-- MASTER ASLI (TIDAK BOLEH DIUBAH)
│       └── Proposal - Main - Revisi.docx            <-- Salinan Revisi Terkini Siap Bimbingan
```

---

## 8. Panduan Menjalankan Sistem di Komputer Baru / Perangkat Lain

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

5. **Nyalakan Server Pengembang Laravel:**
   ```bash
   php artisan serve --host=127.0.0.1 --port=8000
   ```

6. **Buka Aplikasi di Peramban:**
   * URL Aplikasi: [http://127.0.0.1:8000](http://127.0.0.1:8000)
   * Direktori Mitra (Menu 02): [http://127.0.0.1:8000/?menu=2](http://127.0.0.1:8000/?menu=2)
   * Pengajuan FSM (Menu 04): [http://127.0.0.1:8000/?menu=4](http://127.0.0.1:8000/?menu=4)

---

## 9. Agenda & Tugas Prioritas Menuju Sesi Berikutnya (Convo 5)

1. **Penyusunan Skenario Panduan Demonstrasi Sistem (Alur Demo Interaktif 7–10 Menit):**
   * Menyusun panduan peragaan aplikasi di depan Pak Victor Tuilan dan Pak Franky Manoppo (alur pengajuan naskah baru, telaah mutu pimpinan, pengesahan berkas fisik ber-meterai, scan QR code via HP, deteksi peringatan kedaluwarsa H-90, dan pelacakan audit trail).
2. **Penyelarasan Naskah Word Skripsi (`Proposal - Main - Revisi.docx`):**
   * Memastikan sinkronisasi 100% pada:
     - Subbab 1.3: 5 butir batasan masalah resmi.
     - Subbab 2.3 & 3.4: Formalisasi model FSM 6 status, 8 transisi, diagram *statechart*, dan eliminasi SHA-256.
     - Bab IV: Matriks pengujian transisi (*State Transition Testing*) dan instrumen Likert Sugiyono.
3. **Penyusunan Bahan Paparan / Slide Presentasi Proposal:**
   * Menyiapkan poin ringkasan eksekutif untuk mempertahankan kebaruan ilmiah FSM di hadapan komisi penguji.

---

## 10. Template Prompt Handover untuk AI di Perangkat Lain

> **Salin dan tempelkan teks berikut ke sesi chat AI yang baru:**
```text
Halo AI, saya Jordan Immanuel Pusung (NIM: 2355201011), mahasiswa S1 Teknik Informatika Universitas Parna Raya (UNIPAR) Manado. Saya sedang menyelesaikan skripsi:
"Rancang Bangun Sistem Informasi Dokumen Kerja Sama Berbasis Finite State Machine di Universitas Parna Raya"
Pembimbing I: Victor B.C. Tuilan, S.Pd., M.Pd. | Pembimbing II: Franky G.C. Manoppo, ST., M.Kom.

Seluruh kode sumber aplikasi nyata telah selesai dibangun menggunakan Laravel 11 + MySQL (simkerma_db) dan sudah tersimpan di repositori GitHub resmi saya:
https://github.com/0dannn/SIM-KERMA-UNIPAR

Konteks penting yang TIDAK BOLEH diubah atau dikurangi:
1. FSM 6 Status Nyata di MySQL: DRAFT, REVIEW, ACTIVE, WARNING, EXPIRED, CANCELLED (bukan mock/simulasi).
2. Guard Condition Berkas Fisik: Transisi ke status ACTIVE mewajibkan unggah scan PDF naskah sah bertanda tangan basah dan bermeterai serta nomor surat mitra.
3. Hashing SHA-256 telah 100% DIELIMINASI dan digantikan oleh FSM Guard + Dynamic QR Code Registrasi Dokumen ke portal /verify/{reg_id}.
4. Dual-Track Workflow: Jalur 1 (Bertahap Draf -> MoU -> MoA -> IA) dan Jalur 2 (Paket Terpadu 1 Tahun PKM/Tridharma).
5. Evaluasi Kuantitatif menggunakan Skala Likert 5 Poin (Sugiyono, 2019; Arikunto, 2013), bukan SUS.
6. Engine QR Stamping (DocxQrStampService.php) menyematkan QR 500px (cx="765387" cy="765387" EMU) tepat di atas teks "Pihak Pertama" di template naskah baku "Template_MoU_UNIPAR.docx". Area di atas Dr. Rosdiana Simbolon, SE., S.Kom., M.Si harus 100% bersih untuk tanda tangan basah & cap stempel pimpinan.
7. Desain UI telah diselaraskan dengan warna oranye (#ea580c), sidebar dark slate (#1e2430), container bersih, dan tabel modern.

Sekarang kita berada di sesi lanjutan (Convo 5). Tolong baca master log di folder "convo-4 01 Nov 2026 continous (Internal Chat)/convo-4.txt" dan panduan handover "AI_HANDOVER_CONTEXT_LENGKAP.md", lalu bantu saya mengerjakan agenda selanjutnya.
```
