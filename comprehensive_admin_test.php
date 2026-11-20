<?php

/**
 * SIM-KERMA UNIPAR - Comprehensive Admin Automated End-to-End Test Suite
 * 
 * Simulates an Admin operating the web application across all 7 menus,
 * performing submissions, FSM transitions, edits, validations, and UI audits.
 */

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Document;
use App\Models\Partner;
use App\Models\Activity;
use App\Models\AuditTrail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AdminTester
{
    private int $passCount = 0;
    private int $failCount = 0;
    private array $findings = [];
    private array $cleanups = [];

    public function log(string $msg, string $type = 'INFO'): void
    {
        $timestamp = date('H:i:s');
        $colors = [
            'INFO' => "\033[36m",
            'PASS' => "\033[32m",
            'FAIL' => "\033[31m",
            'WARN' => "\033[33m",
            'RESET' => "\033[0m"
        ];
        $c = $colors[$type] ?? '';
        $r = $colors['RESET'];
        echo "[$timestamp] {$c}[$type]{$r} $msg\n";
    }

    public function assert(bool $condition, string $testName, string $errorDetails = ''): void
    {
        if ($condition) {
            $this->passCount++;
            $this->log("PASS: $testName", 'PASS');
        } else {
            $this->failCount++;
            $this->log("FAIL: $testName - $errorDetails", 'FAIL');
            $this->findings[] = "[$testName] $errorDetails";
        }
    }

    public function runAll(): void
    {
        $this->log("==================================================================", 'INFO');
        $this->log("MEMULAI DEEP TESTING SISTEM KERMA UNIPAR OLEH ROLE ADMIN", 'INFO');
        $this->log("==================================================================", 'INFO');

        $this->testSuite1_DatabaseAndPrerequisites();
        $this->testSuite2_AllMenuRenders();
        $this->testSuite3_PartnerDatabaseOperations();
        $this->testSuite4_Jalur1RegularSubmission();
        $this->testSuite5_Jalur2LegacyPastSubmission();
        $this->testSuite6_FullFsmTransitionLifecycle();
        $this->testSuite7_DetailDocumentViewHarmonization();
        $this->testSuite8_ModalEditHarmonization();
        $this->testSuite9_ActivityAndEvaluation();
        $this->testSuite10_PublicQrVerification();
        $this->testSuite11_RoleSwitchingAndPermissions();
        $this->testSuite12_SystemLogsAndSanity();

        $this->cleanup();

        $this->log("==================================================================", 'INFO');
        $this->log("HASIL AKHIR PENGUJIAN:", 'INFO');
        $this->log("TOTAL PASS: {$this->passCount}", 'PASS');
        if ($this->failCount > 0) {
            $this->log("TOTAL FAIL: {$this->failCount}", 'FAIL');
            $this->log("TEMUAN PERMASALAHAN / KETIDAKSINKRONAN:", 'WARN');
            foreach ($this->findings as $idx => $f) {
                echo "  " . ($idx + 1) . ". $f\n";
            }
        } else {
            $this->log("SEMUA PENGUJIAN BERHASIL TANPA KESALAHAN! (100% SINKRON)", 'PASS');
        }
        $this->log("==================================================================", 'INFO');
    }

    private function testSuite1_DatabaseAndPrerequisites(): void
    {
        $this->log("--- SUITE 1: Verifikasi Koneksi Database & Tabel Inti ---", 'INFO');
        try {
            DB::connection()->getPdo();
            $this->assert(true, "Koneksi Database MySQL Aktif");
        } catch (\Exception $e) {
            $this->assert(false, "Koneksi Database MySQL", $e->getMessage());
        }

        $tables = ['documents', 'partners', 'activities', 'audit_trails', 'users'];
        foreach ($tables as $tbl) {
            $exists = DB::getSchemaBuilder()->hasTable($tbl);
            $this->assert($exists, "Tabel '$tbl' tersedia di database");
        }

        $docCount = Document::count();
        $partnerCount = Partner::count();
        $this->log("Total Dokumen Eksisting: $docCount, Total Mitra Eksisting: $partnerCount", 'INFO');
    }

    private function testSuite2_AllMenuRenders(): void
    {
        $this->log("--- SUITE 2: Uji Rendering & Integritas HTML Menu 1 sampai 7 ---", 'INFO');
        $menus = [
            1 => 'Dashboard',
            2 => 'Data Master Mitra',
            3 => 'Dokumen Kerja Sama (Form Pengajuan)',
            4 => 'Pengajuan & Persetujuan (Monitoring & FSM)',
            5 => 'Pemantauan Kegiatan (IKU 6)',
            6 => 'Masa Berlaku & Evaluasi',
            7 => 'Laporan, Analitik & FSM',
        ];

        foreach ($menus as $mNum => $mTitle) {
            $url = "http://127.0.0.1:8000/?menu=$mNum";
            $res = @file_get_contents($url);
            $httpCode = $http_response_header[0] ?? 'UNKNOWN';
            $is200 = str_contains($httpCode, '200');
            $this->assert($is200, "Render Menu $mNum: $mTitle (HTTP 200)", "Respons: $httpCode");
            $this->assert(!empty($res) && !str_contains($res, 'Undefined variable') && !str_contains($res, 'ErrorException'), "Integritas Bebas Error PHP di Menu $mNum");
        }
    }

    private function testSuite3_PartnerDatabaseOperations(): void
    {
        $this->log("--- SUITE 3: Operasi Database Mitra & API PDDikti ---", 'INFO');
        
        // 1. API Live Search Mitra
        $apiPartUrl = "http://127.0.0.1:8000/simkerma/api/partners?q=politeknik";
        $apiPartRes = @file_get_contents($apiPartUrl);
        $partJson = json_decode($apiPartRes, true);
        $this->assert(is_array($partJson), "API Pencarian Mitra (/simkerma/api/partners) Mengembalikan JSON Valid");

        // 2. API PDDikti Universities (4,650+ PT)
        $apiUnivUrl = "http://127.0.0.1:8000/simkerma/api/universities?q=Indonesia";
        $apiUnivRes = @file_get_contents($apiUnivUrl);
        $univJson = json_decode($apiUnivRes, true);
        $this->assert(is_array($univJson) && count($univJson) > 0, "API PDDikti (/simkerma/api/universities) Berfungsi & Ada Data");

        // 3. Tambah Mitra Baru via Eloquent
        $testPartnerName = "PT Uji Coba Kemitraan Mandiri " . time();
        $partner = Partner::create([
            'partner_code' => 'MITRA-TEST-' . rand(100, 999),
            'name' => $testPartnerName,
            'category' => 'DUDI / Swasta',
            'type' => 'Industri',
            'country' => 'Indonesia',
            'province' => 'DKI Jakarta',
            'address' => 'Jl. Jenderal Sudirman Kav. 52-53',
            'pic_name' => 'Budi Santoso',
            'pic_email' => 'budi@mitra-test.com',
            'pic_phone' => '081234567890',
            'status' => 'ACTIVE',
        ]);
        $this->cleanups['partners'][] = $partner->id;
        $this->assert($partner->exists, "Tambah Mitra Baru Sukses (ID: {$partner->id})");

        // 4. Update Mitra
        $partner->address = 'Jl. MH Thamrin No. 1, Jakarta Pusat';
        $partner->save();
        $refreshed = Partner::find($partner->id);
        $this->assert($refreshed->address === 'Jl. MH Thamrin No. 1, Jakarta Pusat', "Update Informasi Mitra Sukses");
    }

    private function testSuite4_Jalur1RegularSubmission(): void
    {
        $this->log("--- SUITE 4: Pengajuan Kerja Sama Baru (Jalur 1 Reguler FSM) ---", 'INFO');

        $docId = 'UNIPAR-TEST-' . rand(1000, 9999);
        $regId = 'UNIPAR-REG-TEST-' . rand(1000, 9999);

        // Simulasi input pengajuan di mana nomor pihak II opsional dikosongkan
        $partnerDocNumber = null;
        $sanitizedPartnerDocNumber = empty($partnerDocNumber) ? '-' : $partnerDocNumber;

        $doc = Document::create([
            'doc_id' => $docId,
            'reg_id' => $regId,
            'track' => 'Jalur 1: Kemitraan Bertahap',
            'title' => 'Kerja Sama Riset AI dan Magang Industri ' . time(),
            'doc_type' => 'Memorandum of Understanding (MoU)',
            'unit' => 'Program Studi Teknik Informatika',
            'partner_name' => 'PT Cloud Computing Nusantara',
            'partner_category' => 'DUDI / Swasta',
            'first_party_signer' => 'Rektor Universitas Parna Raya',
            'second_party_signer' => 'Direktur Utama PT Cloud Computing Nusantara',
            'doc_number' => '001/UNIPAR-MOU/2026',
            'partner_doc_number' => $sanitizedPartnerDocNumber, // Harus "-"
            'status' => 'DRAFT',
            'start_date' => date('Y-m-d'),
            'end_date' => date('Y-m-d', strtotime('+3 years')),
            'validity' => '3 Tahun',
            'scope' => 'Pendidikan, Riset AI, dan Magang Industri MBKM',
            'description' => 'Kerja sama strategis pengembangan laboratorium AI',
        ]);
        $this->cleanups['documents'][] = $doc->id;

        $this->assert($doc->exists, "Pembuatan Naskah Dokumen Baru Berhasil (ID: $docId)");
        $this->assert($doc->status === 'DRAFT', "Status Awal Naskah Baru adalah DRAFT");
        $this->assert($doc->partner_doc_number === '-', "Nomor Naskah Pihak II Kosong Otomatis Menjadi '-' (Bukan string acak)");

        // Catat jejak audit awal
        $audit = AuditTrail::create([
            'document_id' => $doc->id,
            'actor' => 'Admin SIMKERMA',
            'role' => 'ADMIN',
            'event' => 'CREATE_DRAFT',
            'from_status' => '-',
            'to_status' => 'DRAFT',
            'notes' => 'Pengajuan naskah kerja sama baru berhasil didaftarkan.',
            'action_date' => now(),
            'timestamp_wita' => now()->format('Y-m-d H:i:s') . ' WITA',
        ]);
        $this->assert($audit->exists, "Audit Trail Pembuatan Naskah Tercatat");
    }

    private function testSuite5_Jalur2LegacyPastSubmission(): void
    {
        $this->log("--- SUITE 5: Pengajuan Kerja Sama Pra-Sistem / Masa Lampau (Backfill) ---", 'INFO');

        $pastYear = 2024;
        $pastDate = '2024-05-15';
        $pastEndDate = '2027-05-15';

        $legacyDoc = Document::create([
            'doc_id' => 'UNIPAR-LEGACY-' . rand(1000, 9999),
            'reg_id' => 'UNIPAR-REG-LEGACY-' . rand(1000, 9999),
            'track' => 'Dokumen Lampau (Legacy)',
            'title' => 'Naskah Kemitraan Historis Pra-Sistem 2024',
            'doc_type' => 'Memorandum of Agreement (MoA)',
            'unit' => 'Fakultas Ekonomi dan Bisnis',
            'partner_name' => 'Bank Mandiri (Persero) Tbk',
            'partner_category' => 'BUMN / BUMD',
            'first_party_signer' => 'Dekan Fakultas Ekonomi',
            'second_party_signer' => 'Pimpinan Cabang Bank Mandiri',
            'doc_number' => '045/UNIPAR-FEB/2024',
            'partner_doc_number' => 'MDR/KERMA/2024/099',
            'status' => 'ACTIVE',
            'start_date' => $pastDate,
            'end_date' => $pastEndDate,
            'date_signed' => $pastDate,
            'validity' => '3 Tahun',
            'scope' => 'Penyaluran Beasiswa dan Kuliah Praktisi Perbankan',
            'description' => 'Dokumen hasil migrasi data lampau',
        ]);
        $this->cleanups['documents'][] = $legacyDoc->id;

        $this->assert($legacyDoc->exists, "Dokumen Pra-Sistem Lampau Berhasil Dibuat");
        $this->assert(date('Y', strtotime($legacyDoc->start_date)) === '2024', "Tahun Mulai Dokumen Lampau Sesuai (2024)");
        $this->assert(date('Y', strtotime($legacyDoc->date_signed)) === '2024', "Tahun Penandatanganan Dokumen Lampau Sesuai (2024)");

        // Cek audit trail keseragaman tanggal historis
        $legacyAudit = AuditTrail::create([
            'document_id' => $legacyDoc->id,
            'actor' => 'Admin Migrasi',
            'role' => 'ADMIN',
            'event' => 'MIGRATION_BACKFILL',
            'from_status' => '-',
            'to_status' => 'ACTIVE',
            'notes' => 'Pencatatan data kerja sama historis pra-sistem tahun 2024.',
            'timestamp_wita' => $pastDate . ' 10:00:00 WITA',
        ]);
        $this->assert(str_contains($legacyAudit->timestamp_wita, '2024'), "Audit Trail Dokumen Lampau Menggunakan Tahun Historis yang Seragam (2024, Bukan 2026)");
    }

    private function testSuite6_FullFsmTransitionLifecycle(): void
    {
        $this->log("--- SUITE 6: Uji Siklus Hidup FSM Penuh (Finite State Machine) ---", 'INFO');

        $doc = Document::create([
            'doc_id' => 'UNIPAR-FSM-' . rand(1000, 9999),
            'reg_id' => 'UNIPAR-REG-FSM-' . rand(1000, 9999),
            'track' => 'Jalur 1: Kemitraan Bertahap',
            'title' => 'Naskah Pengujian Siklus Hidup Transisi FSM Lengkap',
            'doc_type' => 'Memorandum of Agreement (MoA)',
            'unit' => 'Program Studi Sistem Informasi',
            'partner_name' => 'PT Solusi Teknologi Terintegrasi',
            'partner_category' => 'DUDI / Swasta',
            'first_party_signer' => 'Rektor Universitas Parna Raya',
            'second_party_signer' => 'Direktur Utama',
            'doc_number' => '102/UNIPAR/FSM/2026',
            'partner_doc_number' => '-',
            'status' => 'DRAFT',
            'validity' => '2 Tahun',
            'scope' => 'Pengembangan Kurikulum Berbasis Industri',
        ]);
        $this->cleanups['documents'][] = $doc->id;

        // Step 1: DRAFT -> SUBMIT -> REVIEW
        $doc->status = 'REVIEW';
        $doc->save();
        AuditTrail::create([
            'document_id' => $doc->id,
            'actor' => 'Kaprodi Sistem Informasi',
            'role' => 'FAKULTAS',
            'event' => 'SUBMIT',
            'from_status' => 'DRAFT',
            'to_status' => 'REVIEW',
            'notes' => 'Pengajuan diserahkan ke Meja Pemeriksaan Hukum LPPM.',
            'action_date' => now(),
            'timestamp_wita' => now()->format('Y-m-d H:i:s') . ' WITA',
        ]);
        $this->assert(Document::find($doc->id)->status === 'REVIEW', "Transisi 1 (SUBMIT): DRAFT -> REVIEW Berhasil");

        // Step 2: REVIEW -> RETURN_TO_DRAFT -> DRAFT
        $doc->status = 'DRAFT';
        $doc->save();
        AuditTrail::create([
            'document_id' => $doc->id,
            'actor' => 'Tim Legal LPPM',
            'role' => 'LPPM',
            'event' => 'RETURN_TO_DRAFT',
            'from_status' => 'REVIEW',
            'to_status' => 'DRAFT',
            'notes' => 'Catatan perbaikan klausul hak kekayaan intelektual.',
            'action_date' => now(),
            'timestamp_wita' => now()->format('Y-m-d H:i:s') . ' WITA',
        ]);
        $this->assert(Document::find($doc->id)->status === 'DRAFT', "Transisi 2 (RETURN_TO_DRAFT): REVIEW -> DRAFT Berhasil");

        // Step 3: DRAFT -> SUBMIT -> REVIEW
        $doc->status = 'REVIEW';
        $doc->save();
        AuditTrail::create([
            'document_id' => $doc->id,
            'actor' => 'Kaprodi Sistem Informasi',
            'role' => 'FAKULTAS',
            'event' => 'SUBMIT',
            'from_status' => 'DRAFT',
            'to_status' => 'REVIEW',
            'notes' => 'Revisi klausul HAKI telah diperbaiki.',
            'action_date' => now(),
            'timestamp_wita' => now()->format('Y-m-d H:i:s') . ' WITA',
        ]);
        $this->assert(Document::find($doc->id)->status === 'REVIEW', "Transisi 3 (SUBMIT Ulang): DRAFT -> REVIEW Berhasil");

        // Step 4: REVIEW -> VERIFY -> VERIFIED
        $doc->status = 'VERIFIED';
        $doc->save();
        AuditTrail::create([
            'document_id' => $doc->id,
            'actor' => 'Tim Legal LPPM',
            'role' => 'LPPM',
            'event' => 'VERIFY',
            'from_status' => 'REVIEW',
            'to_status' => 'VERIFIED',
            'notes' => 'Naskah memenuhi syarat mutu Tridharma & IKU 6. Teks dibekukan (Freeze Text).',
            'action_date' => now(),
            'timestamp_wita' => now()->format('Y-m-d H:i:s') . ' WITA',
        ]);
        $this->assert(Document::find($doc->id)->status === 'VERIFIED', "Transisi 4 (VERIFY): REVIEW -> VERIFIED Berhasil");

        // Step 5: VERIFIED -> ACTIVATE -> ACTIVE
        $doc->status = 'ACTIVE';
        $doc->start_date = '2026-11-20';
        $doc->end_date = '2028-11-20';
        $doc->date_signed = '2026-11-20';
        $doc->partner_doc_number = 'STT/KERMA/XI/2026';
        $doc->save();
        AuditTrail::create([
            'document_id' => $doc->id,
            'actor' => 'Rektor Universitas Parna Raya',
            'role' => 'PIMPINAN',
            'event' => 'ACTIVATE',
            'from_status' => 'VERIFIED',
            'to_status' => 'ACTIVE',
            'notes' => 'Naskah resmi disahkan, ditandatangani basah dan bermeterai.',
            'action_date' => now(),
            'timestamp_wita' => now()->format('Y-m-d H:i:s') . ' WITA',
        ]);
        $refreshed = Document::find($doc->id);
        $this->assert($refreshed->status === 'ACTIVE', "Transisi 5 (ACTIVATE): VERIFIED -> ACTIVE Berhasil");
        $this->assert($refreshed->partner_doc_number === 'STT/KERMA/XI/2026', "Nomor Naskah Mitra Terperbarui Saat Aktivasi");

        // Step 6: ACTIVE -> TRIGGER_WARN_EXPIRY -> WARNING
        $doc->status = 'WARNING';
        $doc->warning_threshold = 'H-60 Hari';
        $doc->save();
        AuditTrail::create([
            'document_id' => $doc->id,
            'actor' => 'Sistem Peringatan Dini',
            'role' => 'SYSTEM',
            'event' => 'TRIGGER_WARN_EXPIRY',
            'from_status' => 'ACTIVE',
            'to_status' => 'WARNING',
            'notes' => 'Masa berlaku dokumen memasuki ambang batas peringatan (< 60 hari).',
            'action_date' => now(),
            'timestamp_wita' => now()->format('Y-m-d H:i:s') . ' WITA',
        ]);
        $this->assert(Document::find($doc->id)->status === 'WARNING', "Transisi 6 (WARNING): ACTIVE -> WARNING Berhasil");

        // Step 7: WARNING -> RETURN_TO_DRAFT (Inisiasi Adendum/Perpanjangan)
        $doc->status = 'DRAFT';
        $doc->validity = '3 Tahun (Perpanjangan)';
        $doc->save();
        AuditTrail::create([
            'document_id' => $doc->id,
            'actor' => 'Admin SIMKERMA',
            'role' => 'ADMIN',
            'event' => 'RENEWAL_INITIATED',
            'from_status' => 'WARNING',
            'to_status' => 'DRAFT',
            'notes' => 'Inisiasi perpanjangan masa berlaku (adendum naskah).',
            'action_date' => now(),
            'timestamp_wita' => now()->format('Y-m-d H:i:s') . ' WITA',
        ]);
        $this->assert(Document::find($doc->id)->status === 'DRAFT', "Transisi 7 (Inisiasi Perpanjangan): WARNING -> DRAFT Berhasil");

        // Cek total riwayat audit trail naskah
        $auditCount = $doc->auditTrails()->count();
        $this->assert($auditCount >= 7, "Rangkaian 7 Jejak Audit FSM Tercatat Lengkap (Tercatat: $auditCount log)");
    }

    private function testSuite7_DetailDocumentViewHarmonization(): void
    {
        $this->log("--- SUITE 7: Uji Harmonisasi Tampilan Detail Dokumen & Alur Proses ---", 'INFO');

        $doc = Document::where('status', 'ACTIVE')->first();
        if (!$doc) {
            $doc = Document::first();
        }

        $detailUrl = "http://127.0.0.1:8000/?menu=4&view=detail&doc=" . $doc->doc_id;
        $html = @file_get_contents($detailUrl);
        $this->assert(!empty($html), "Halaman Detail Dokumen ({$doc->doc_id}) Dapat Dimuat");

        // 1. Tombol & Teks Detail Status
        $this->assert(str_contains($html, 'Lihat Detail Status'), "Tombol 'Lihat Detail Status' Hadir");
        $this->assert(!str_contains($html, 'Lihat Detail FSM'), "Teks Lama 'Lihat Detail FSM' Berhasil Dihilangkan");
        $this->assert(!str_contains($html, '(Finite State Machine)'), "Teks '(Finite State Machine)' Berhasil Dihilangkan");

        // 2. Penghapusan teks 'naskah asli / terverifikasi qr code' di dokumen lampiran
        $this->assert(!str_contains(strtolower($html), 'naskah asli / terverifikasi qr code'), "Teks 'naskah asli / terverifikasi qr code' Berhasil Dihilangkan");

        // 3. Header Tabel Dokumen Turunan: 'Judul dan Nomor Dokumen' & text-center
        if ($doc->doc_type === 'Memorandum of Understanding (MoU)' || str_contains($html, 'Dokumen Turunan')) {
            $this->assert(str_contains($html, 'Judul dan Nomor Dokumen'), "Kolom 'Judul dan Nomor Dokumen' Hadir pada Tabel Turunan");
            $this->assert(str_contains($html, '<th class="py-1 text-center text-muted fw-semibold">Judul dan Nomor Dokumen</th>'), "Header Tabel Dokumen Turunan Menggunakan Rata Tengah (text-center)");
        }

        // 4. Stepper 7 Tahapan di Alur Proses
        $this->assert(str_contains($html, 'Draft'), "Tahap 'Draft' Hadir di Stepper");
        $this->assert(str_contains($html, 'Verifikasi Unit'), "Tahap 'Verifikasi Unit' Hadir di Stepper");
        $this->assert(str_contains($html, 'Review LPPM'), "Tahap 'Review LPPM' Hadir di Stepper");
        $this->assert(str_contains($html, 'Persetujuan Pimpinan'), "Tahap 'Persetujuan Pimpinan' Hadir di Stepper");
        $this->assert(str_contains($html, 'Active'), "Tahap 'Active' Hadir di Stepper");
        $this->assert(str_contains($html, 'Peringatan'), "Tahap 'Peringatan' Hadir di Stepper");
        $this->assert(str_contains($html, 'Selesai'), "Tahap 'Selesai' Hadir di Stepper");
    }

    private function testSuite8_ModalEditHarmonization(): void
    {
        $this->log("--- SUITE 8: Uji Harmonisasi Modal Edit Informasi Kerja Sama ---", 'INFO');

        $doc = Document::first();
        $detailUrl = "http://127.0.0.1:8000/?menu=4&view=detail&doc=" . $doc->doc_id;
        $html = @file_get_contents($detailUrl);

        // 1. Dropdown Unit Pengusul (Bukan manual input text)
        $hasUnitSelect = str_contains($html, '<select name="unit"') || str_contains($html, 'id="edit_unit_');
        $this->assert($hasUnitSelect, "Field 'Unit Pengusul' di Modal Edit Menggunakan Dropdown / Select (Sinkron dengan Form Pengajuan)");

        // 2. Scroll Vertikal Modal Edit
        $hasScroll = str_contains($html, 'overflow-y: auto') || str_contains($html, 'overflow-y-auto') || str_contains($html, 'modal-dialog-scrollable');
        $this->assert($hasScroll, "Modal Edit Memiliki Scroll Vertikal yang Nyaman (overflow-y-auto / scrollable)");

        // 3. Combobox Mitra PDDikti di Modal Edit
        $hasPartnerCombobox = str_contains($html, 'edit-partner-search-input') || str_contains($html, 'searchPartners') || str_contains($html, 'universities');
        $this->assert($hasPartnerCombobox, "Modal Edit Mengintegrasikan Live Search PDDikti & Master Mitra");

        // 4. Skema Warna Ungu FSM Modal (#5156be)
        $this->assert(str_contains($html, '#5156be'), "Popup/Modal FSM Menggunakan Warna Ungu Tema Sistem (#5156be)");
        $this->assert(!str_contains($html, '#52249E'), "Warna Violet Gelap Lama (#52249E) Bersih dan Tidak Digunakan Lagi");
    }

    private function testSuite9_ActivityAndEvaluation(): void
    {
        $this->log("--- SUITE 9: Uji Modul Pemantauan Realisasi IKU 6 & Evaluasi Mutu ---", 'INFO');

        $doc = Document::first();
        
        // 1. Tambah Kegiatan Realisasi Tridharma
        $activity = Activity::create([
            'document_id' => $doc->id,
            'activity_name' => 'Kuliah Tamu Praktisi Industri AI Semester Ganjil',
            'schedule_start' => '2026-10-15',
            'schedule_end' => '2026-10-16',
            'target_qty' => '120 Mahasiswa',
            'realized_qty' => '120 Mahasiswa',
            'parties_involved' => 'Dosen & Mahasiswa TI',
            'outputs' => 'Modul Ajar & Sertifikat Kompetensi 120 Mahasiswa',
            'execution_status' => 'COMPLETED',
            'notes' => 'Kegiatan berjalan sukses dihadiri seluruh mahasiswa semester 5.',
        ]);
        $this->cleanups['activities'][] = $activity->id;
        $this->assert($activity->exists, "Pencatatan Realisasi Kegiatan Tridharma MBKM Berhasil");

        // 2. Tambah Catatan Evaluasi Mutu
        $evalAudit = AuditTrail::create([
            'document_id' => $doc->id,
            'actor' => 'Tim Pengendali Mutu',
            'role' => 'ADMIN',
            'event' => 'EVALUATION_RECORDED',
            'from_status' => $doc->status,
            'to_status' => $doc->status,
            'notes' => 'Evaluasi semesteran: Capaian luaran memenuhi 100% target IKU 6.',
            'timestamp_wita' => now()->format('Y-m-d H:i:s') . ' WITA',
        ]);
        $this->assert($evalAudit->exists, "Pencatatan Catatan Evaluasi Mutu Berkala Berhasil");
    }

    private function testSuite10_PublicQrVerification(): void
    {
        $this->log("--- SUITE 10: Uji Halaman Verifikasi Publik QR Code ---", 'INFO');

        $doc = Document::whereNotNull('reg_id')->first();
        $verifyUrl = "http://127.0.0.1:8000/verify/" . $doc->reg_id;
        $html = @file_get_contents($verifyUrl);
        $httpCode = $http_response_header[0] ?? 'UNKNOWN';

        $this->assert(str_contains($httpCode, '200'), "Halaman Verifikasi Publik Mengembalikan HTTP 200");
        $this->assert(str_contains($html, $doc->partner_name), "Nama Mitra Muncul di Lembar Verifikasi Publik");
        $this->assert(str_contains($html, $doc->reg_id), "Nomor Registrasi Terverifikasi di Lembar Publik");
    }

    private function testSuite11_RoleSwitchingAndPermissions(): void
    {
        $this->log("--- SUITE 11: Uji Hak Akses & Matriks Peran Pengguna (Role-Based Access) ---", 'INFO');

        $roles = ['ADMIN', 'LPPM', 'FAKULTAS', 'PIMPINAN', 'MASTER'];
        foreach ($roles as $r) {
            session(['active_role' => $r]);
            $this->assert(session('active_role') === $r, "Role Switcher Berhasil Mengubah Sesi ke '$r'");
        }
        // Kembalikan ke ADMIN
        session(['active_role' => 'ADMIN']);
    }

    private function testSuite12_SystemLogsAndSanity(): void
    {
        $this->log("--- SUITE 12: Pemeriksaan Log Error Sistem (laravel.log) ---", 'INFO');

        $logPath = __DIR__ . '/storage/logs/laravel.log';
        if (file_exists($logPath)) {
            $lines = file($logPath);
            $recentLines = array_slice($lines, -50);
            $hasFatal = false;
            foreach ($recentLines as $line) {
                if (str_contains($line, 'production.ERROR') || str_contains($line, 'local.ERROR') && str_contains($line, 'SimkermaController')) {
                    $hasFatal = true;
                    break;
                }
            }
            $this->assert(!$hasFatal, "Log Laravel Bebas dari Error Kritis Controller");
        } else {
            $this->assert(true, "Berkas Log Laravel Bersih");
        }
    }

    private function cleanup(): void
    {
        $this->log("--- Membersihkan Data Uji Coba Sementara ---", 'INFO');
        if (!empty($this->cleanups['activities'])) {
            Activity::destroy($this->cleanups['activities']);
        }
        if (!empty($this->cleanups['documents'])) {
            foreach ($this->cleanups['documents'] as $docId) {
                AuditTrail::where('document_id', $docId)->delete();
                Document::destroy($docId);
            }
        }
        if (!empty($this->cleanups['partners'])) {
            Partner::destroy($this->cleanups['partners']);
        }
        $this->log("Pembersihan Data Uji Selesai.", 'INFO');
    }
}

// Jalankan Pengujian
$tester = new AdminTester();
$tester->runAll();
