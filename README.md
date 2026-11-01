# SIMKERMA UNIPAR &mdash; Sistem Informasi Dokumen Kerja Sama
### *Rancang Bangun Sistem Informasi Dokumen Kerja Sama Berbasis Finite State Machine di Universitas Parna Raya*

![Laravel](https://img.shields.io/badge/Laravel-11.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-8.2%2B-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-8.0%2B-4479A1?style=for-the-badge&logo=mysql&logoColor=white)
![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3-7952B3?style=for-the-badge&logo=bootstrap&logoColor=white)
![Status](https://img.shields.io/badge/Status-Skripsi%20Completed-success?style=for-the-badge)

---

## 👨‍🎓 Informasi Peneliti & Skripsi

* **Nama Mahasiswa:** Jordan Immanuel Pusung  
* **NIM:** 2355201011  
* **Program Studi:** S1 Teknik Informatika  
* **Fakultas:** Ilmu Komputer / Sains & Teknologi  
* **Institusi:** Universitas Parna Raya (UNIPAR) Manado  
* **Dosen Pembimbing / Rektor:** Dr. Rosdiana Simbolon, SE., S.Kom., M.Si  

---

## 📌 Ringkasan Sistem

**SIMKERMA UNIPAR** adalah platform tata kelola naskah kerja sama institusi perguruan tinggi yang dirancang menggunakan pemodelan **Finite State Machine (FSM)**. Sistem ini mengotomasi siklus hidup naskah kemitraan (MoU, MoA, IA, dan Paket Terpadu PKM) mulai dari draf awal, pemeriksaan pimpinan (*legal review*), penerbitan naskah resmi berstempel **QR Code Dinamis**, konversi otomatis ke **PDF resmi**, hingga pemantauan realisasi pelaksanaan kegiatan tridharma berbasis **Indikator Kinerja Utama 6 (IKU 6)** Kemendikbudristek.

---

## 🚀 Fitur Unggulan Sistem

### 1. Finite State Machine (FSM) Workflow Engine
Mengeliminasi ambiguitas status naskah dengan mesin transisi terstruktur:
* **`DRAFT`** &rarr; Konsep awal naskah oleh unit pengusul menggunakan template bersih baku.
* **`REVIEW`** &rarr; Penelaahan oleh meja pimpinan/rektorat; sistem otomatis membubuhkan stempel QR Code dan mengekspor dokumen ke PDF resmi.
* **`ACTIVE`** &rarr; Naskah disetujui, ditandatangani, dan berlaku sah secara hukum.
* **`WARNING`** &rarr; Deteksi dini otomatis kedaluwarsa naskah (H-90, H-60, H-30 hari) untuk inisiasi adendum/perpanjangan.
* **`EXPIRED`** &rarr; Masa berlaku dokumen telah selesai; naskah dialihkan ke arsip digital.
* **`CANCELLED`** &rarr; Pembatalan pengajuan naskah kerja sama secara permanen dengan rekam jejak audit.

### 2. Stempel QR Code Dinamis & Konversi PDF Otomatis
* Menggunakan integrasi **Word OpenXML (`ZipArchive`)** dan **Microsoft Word COM Engine**.
* Gambar QR Code di-generate dengan resolusi tajam **500 × 500 px** dan disematkan secara presisi pada tabel tanda tangan tepat **di atas tulisan "Pihak Pertama" (Row 0, Cell 1)** dengan ukuran tampilan XML Word `cx="765387" cy="765387"` (~2,12 cm).
* Area tanda tangan basah pimpinan (Row 2 di atas nama Rektor) tetap 100% bersih untuk tanda tangan basah fisik dan cap stempel basah universitas.
* Seketika saat event `SUBMIT` ditekan, Word COM mengonversi dokumen menjadi berkas **PDF resmi** yang menjadi naskah primer telaah.

### 3. Portal Verifikasi Publik Digital (`/verify/{reg_id}`)
* Setiap dokumen memiliki kode registrasi unik (misal: `REG-UNIPAR-2026-042`).
* QR code pada dokumen fisik/PDF dapat dipindai oleh publik atau mitra menggunakan kamera smartphone untuk membuka portal keabsahan resmi yang memuat metadata naskah, para pihak, masa berlaku, serta tombol unduh PDF dan Word resmi.

### 4. Hierarki Dokumen & Keterikatan IKU 6
* Mendukung hierarki bertahap (*Step-by-step*): **MoU** (Nota Kesepahaman Institusi) &rarr; **MoA / PKS** (Perjanjian Tingkat Fakultas/Prodi) &rarr; **IA** (Kegiatan Riil di Lapangan).
* Mendukung **Jalur 2 (Paket Terpadu PKM)**: Pengajuan 1 paket terpadu sekaligus untuk program magang industri dan implementasi tridharma.

### 5. Desain UI Modern (Figma UI Reference)
* Mengadopsi palet warna oranye hangat (*warm orange* `#ea580c`) sebagai aksen utama.
* Sidebar elegan *Dark Slate* (`#1e2430`) dengan navigasi aktif bersudut membulat.
* Kontainer kartu bersudut *radius 12px* dengan bayangan lembut (*soft ambient shadow*).
* Status lencana berformat *rounded-pill* dengan warna pastel lembut (berjalan, selesai, belum dimulai, tertunda).

---

## 🛠️ Kebutuhan Sistem & Dependensi

* **Sistem Operasi:** Windows 10/11 (direkomendasikan untuk fitur native Word COM konversi PDF)
* **Web Server:** Apache / Nginx (XAMPP / Laragon)
* **PHP:** Versi 8.2 atau lebih baru
  * Ekstensi PHP wajib: `pdo_mysql`, `gd`, `zip`, `com_dotnet`, `mbstring`, `openssl`
* **Basis Data:** MySQL 8.0+ / MariaDB 10.4+
* **Aplikasi Pendukung:** Microsoft Word (terpasang di server/PC lokal untuk COM Automation)

---

## 💻 Panduan Instalasi & Konfigurasi

### 1. Kloning Repositori
```bash
git clone https://github.com/0dannn/SIM-KERMA-UNIPAR.git
cd SIM-KERMA-UNIPAR
```

### 2. Instalasi Dependensi PHP
```bash
composer install
```

### 3. Konfigurasi Lingkungan (`.env`)
Salin berkas konfigurasi sampel:
```bash
cp .env.example .env
```
Sesuaikan konfigurasi basis data pada `.env`:
```env
APP_NAME="SIMKERMA UNIPAR"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=simkerma_db
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Buat Basis Data & Generate Kunci Aplikasi
Buat database bernama `simkerma_db` di phpMyAdmin / MySQL, lalu jalankan:
```bash
php artisan key:generate
php artisan migrate --seed
```

### 5. Jalankan Server Lokal
```bash
php artisan serve
```
Aplikasi kini dapat diakses melalui peramban web pada tautan:  
👉 **`http://127.0.0.1:8000`**

---

## 📂 Struktur Direktori Utama

```
SIM-KERMA-UNIPAR/
├── app/
│   ├── Http/Controllers/       # SimkermaController & FSM Transition Handler
│   ├── Models/                 # Document, Partner, Activity, AuditTrail
│   └── Services/               # FSMWorkflowEngineService & DocxQrStampService
├── database/
│   ├── migrations/             # Skema tabel documents, activities, partners, audit_trails
│   └── seeders/                # Seeder data inisial universitas & mitra industri
├── docs/                       # Dokumentasi arsitektur, UI reference, & Form Template
│   ├── Form Template/          # Template_MoU_UNIPAR.docx baku
│   └── UI/                     # UI_Reference.png desain Figma
├── public/
│   ├── templates/              # Berkas template naskah Word baku untuk unduhan pengguna
│   └── uploads/documents/      # Berkas naskah Word (.docx) & PDF (.pdf) ber-QR Code hasil stempel
├── resources/views/
│   ├── layouts/app.blade.php   # Tata letak utama, tema CSS oranye, & sidebar navigasi
│   └── simkerma/index.blade.php# 7 Menu Fitur Utama & konsol alur kerja FSM
└── routes/
    └── web.php                 # Rute simkerma.index, transition, dan verify publik
```

---

## 📜 Hak Cipta & Lisensi

Proyek ini dikembangkan oleh **Jordan Immanuel Pusung** untuk memenuhi syarat kelulusan Sarjana Komputer (S.Kom.) Program Studi Teknik Informatika, **Universitas Parna Raya (UNIPAR) Manado**.  
Dilisensikan di bawah lisensi [MIT](LICENSE).
