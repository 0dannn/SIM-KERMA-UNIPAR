# LAPORAN AUDIT & PERBAIKAN BUG SISTEM INFORMASI KERJA SAMA (SIM-KERMA UNIPAR)
**Tanggal Audit:** 20 November 2026  
**Status Pengujian:** Lulus (0 Error / 0 Bug Aktif)

---

## 1. Ringkasan Eksekutif
Berdasarkan proses *deep debugging* dan pengujian statis maupun dinamis (*runtime*) pada seluruh alur kerja sistem, ditemukan **4 bug utama** yang mencakup aspek User Interface (DOM), Eloquent Relationship Model, standarisasi penomoran naskah pihak kedua, dan manajemen penyimpanan berkas fisik (*file storage leak*). 

Semua bug tersebut telah diperbaiki secara tuntas dan diverifikasi melalui skrip otomatis `debug_test.php` dan `comprehensive_admin_test.php`.

---

## 2. Rincian Bug dan Solusi Perbaikan

### Bug 1: Sisa Teks `(Finite State Machine)` Masih Ada di Sumber HTML
* **Lokasi:** `resources/views/simkerma/index.blade.php` (Baris 2241)
* **Masalah:** Komentar HTML `<!-- KARTU 2: ALUR PROSES (FINITE STATE MACHINE) -->` masih terbawa dan ter-render ke browser/DOM client.
* **Solusi Perbaikan:** Mengganti komentar HTML menjadi `<!-- KARTU 2: ALUR PROSES STATUS KERJA SAMA -->` sehingga tidak ada lagi frasa teknis FSM yang bocor ke DOM.

### Bug 2: Relasi `Document::children()` Tidak Ter-scope dengan Benar
* **Lokasi:** `app/Models/Document.php`
* **Masalah:** Deklarasi relasi `children()` menggunakan `orWhere('parent_mou', $this->doc_id)` secara terbuka tanpa *query grouping*, berpotensi mengacaukan query SQL global pada dokumen turunan.
* **Solusi Perbaikan:** Mengisolasi klausa `orWhere` dalam closure grouping:
  ```php
  public function children()
  {
      return $this->hasMany(Document::class, 'parent_mou', 'doc_number')
          ->orWhere(function ($query) {
              $query->where('parent_mou', $this->doc_id)
                    ->where('id', '!=', $this->id);
          });
  }
  ```

### Bug 3: Nomor Naskah Pihak 2 pada Jalur 2 (Paket Terpadu PKM) Tidak Default `"-"`
* **Lokasi:** `app/Http/Controllers/SimkermaController.php` (Fungsi `storeJalur2`)
* **Masalah:** Saat nomor naskah mitra dikosongkan pada form Jalur 2, nilainya tersimpan sebagai `NULL` atau string kosong alih-alih tanda `"-"` (berbeda dengan perilaku Jalur 1).
* **Solusi Perbaikan:** Menambahkan normalisasi input:
  ```php
  $rawPartnerDocNumber = trim((string)$request->input('partner_doc_number', ''));
  $partnerDocNumber = ($rawPartnerDocNumber !== '') ? $rawPartnerDocNumber : '-';
  ```

### Bug 4: Berkas Fisik Tidak Terhapus saat Draf Dihapus (*Double Path Bug*)
* **Lokasi:** `app/Http/Controllers/SimkermaController.php` (Fungsi `deleteDraft`)
* **Masalah:** Path berkas yang disimpan di DB sudah mengandung awalan `uploads/documents/`. Pemanggilan `public_path('uploads/documents/' . $document->draft_file)` menghasilkan path ganda `public/uploads/documents/uploads/documents/filename.pdf`, menyebabkan `file_exists()` selalu gagal dan file sampah tertinggal permanen di server.
* **Solusi Perbaikan:** Mengecek apakah path sudah diawali `uploads/` sebelum menggabungkan path direktori:
  ```php
  $filesToDelete = [$document->draft_file, $document->signed_file, $document->docx_path];
  foreach ($filesToDelete as $filePath) {
      if (!$filePath) continue;
      $fullPath = str_starts_with($filePath, 'uploads/')
          ? public_path($filePath)
          : public_path('uploads/documents/' . $filePath);
      if (file_exists($fullPath)) {
          @unlink($fullPath);
      }
  }
  ```

---

## 3. Catatan Peringatan Teknis (*Warnings*) untuk Pengembangan Lanjutan
1. **Hardcoded Tahun 2026:** Pada `generateUniqueDocId()`, format ID dokumen menggunakan pola `UNIPAR-2026-xxx`. Disarankan menggunakan `date('Y')` agar dinamis untuk pergantian tahun.
2. **Generasi Nomor Acak Jalur 2:** Penggunaan `rand(360, 499)` untuk nomor naskah PKM disarankan dialihkan ke sequence urut agar tidak terjadi potensi collision.
3. **Validasi Request Form:** Disarankan menambahkan `FormRequest` atau `$request->validate()` eksplisit pada fungsi `storeJalur1`.
4. **Timestamp Audit Dokumen Lampau:** Pada pencatatan arsip lampau (`storeLegacy`), `timestamp_wita` dapat diselaraskan dengan tanggal historis penandatanganan dokumen naskah asli.
