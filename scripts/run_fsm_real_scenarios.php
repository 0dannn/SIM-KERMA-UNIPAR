<?php

/**
 * SKRIP UJI INTERAKTIF & MULTI-SKENARIO KASUS NYATA FSM 7-STATUS
 * SISTEM INFORMASI DOKUMEN KERJA SAMA (SIMKERMA) UNIPAR MANADO
 * 
 * Menguji 8 skenario dunia nyata dengan variasi kasus, kemungkinan, dan batas toleransi kegagalan.
 */

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Document;
use App\Models\AuditTrail;
use App\Models\Activity;
use App\Models\Partner;
use App\Services\FSMWorkflowEngineService;
use App\Exceptions\IllegalTransitionException;
use Illuminate\Support\Facades\DB;

$fsm = app(FSMWorkflowEngineService::class);

echo "\n================================================================================\n";
echo "    PENGUJIAN KOMPREHENSIF MULTI-SKENARIO FSM 7-STATUS (SIMKERMA UNIPAR)        \n";
echo "    Model: DRAFT -> REVIEW -> VERIFIED -> ACTIVE -> WARNING -> EXPIRED          \n";
echo "    Eksekusi: " . date('d F Y, H:i:s') . " WITA                                 \n";
echo "================================================================================\n\n";

$passCount = 0;
$failCount = 0;

function runTestCase(string $caseName, callable $fn) {
    global $passCount, $failCount;
    echo "--------------------------------------------------------------------------------\n";
    echo "[$caseName]\n";
    echo "--------------------------------------------------------------------------------\n";
    try {
        DB::beginTransaction();
        $fn();
        DB::rollBack();
        echo ">> HASIL: [PASS / BERHASIL DIVERIFIKASI]\n\n";
        $passCount++;
    } catch (\Throwable $e) {
        DB::rollBack();
        echo ">> HASIL: [FAIL / GAGAL] - " . $e->getMessage() . " di baris " . $e->getLine() . "\n\n";
        $failCount++;
    }
}

// -----------------------------------------------------------------------------
// SKENARIO 1: Happy Path MoA Magang Industri dengan PT Telkom Indonesia
// -----------------------------------------------------------------------------
runTestCase("KASUS 1: Alur Normal Jalur 1 - MoA Magang MBKM PT Telkom (DRAFT -> REVIEW -> VERIFIED -> ACTIVE)", function() use ($fsm) {
    echo "1. Unit Pengusul (Prodi TI) membuat Draf MoA baru...\n";
    $doc = Document::create([
        'doc_id' => 'UNIPAR-2026-TELKOM',
        'reg_id' => 'REG-TELKOM-2026',
        'doc_number' => '510/UNIPAR/MoA/X/2026',
        'track' => 'Jalur 1: Kemitraan Bertahap',
        'doc_type' => 'MoA / PKS',
        'partner_name' => 'PT Telkom Indonesia (Persero) Tbk',
        'partner_category' => 'BUMN',
        'unit' => 'Program Studi Teknik Informatika',
        'scope' => 'Penyelenggaraan Program Magang Bersertifikat MBKM & Laboratorium Cloud Computing',
        'validity' => '2 Tahun',
        'status' => 'DRAFT'
    ]);
    echo "   -> Status: {$doc->status}\n";

    echo "2. Unit Pengusul mengajukan naskah ke meja pemeriksaan (SUBMIT)...\n";
    $fsm->transition($doc, 'SUBMIT', 'UNIT', 'Koordinator MBKM TI');
    $doc->refresh();
    echo "   -> Status: {$doc->status} (Menunggu telaah Tim Legal)\n";
    if ($doc->status !== 'REVIEW') throw new Exception("Status gagal beralih ke REVIEW");

    echo "3. Tim Legal memeriksa mutu hukum, MBKM, dan IKU 6, lalu memverifikasi (VERIFY)...\n";
    $fsm->transition($doc, 'VERIFY', 'LEGAL', 'Tim Legal Rektorat', 'Naskah bebas dari klausul penalti, hak mahasiswa terjamin');
    $doc->refresh();
    echo "   -> Status: {$doc->status} (Teks naskah dibekukan / Freeze Text)\n";
    echo "   -> Tanggal Approved: {$doc->date_approved}\n";
    if ($doc->status !== 'VERIFIED') throw new Exception("Status gagal beralih ke VERIFIED");
    if (empty($doc->date_approved)) throw new Exception("Tanggal persetujuan hukum tidak tercatat");

    echo "4. Pimpinan Rektorat menandatangani basah, menempel meterai Rp10.000, dan mengaktifkan (ACTIVATE)...\n";
    $fsm->transition($doc, 'ACTIVATE', 'LEADER', 'Dr. Rosdiana Simbolon (Rektor)', 'Naskah sah aktif bertanda tangan basah', [
        'partner_doc_number' => 'TELKOM-SULUT/PKS/2026/01',
        'signed_file' => 'scan_moa_telkom_sah_meterai.pdf'
    ]);
    $doc->refresh();
    echo "   -> Status: {$doc->status}\n";
    echo "   -> Nomor Mitra: {$doc->partner_doc_number}\n";
    echo "   -> Berkas Scan: {$doc->signed_file}\n";
    echo "   -> Tanggal TTD: {$doc->date_signed}\n";
    if ($doc->status !== 'ACTIVE') throw new Exception("Status gagal beralih ke ACTIVE");
    if ($doc->partner_doc_number !== 'TELKOM-SULUT/PKS/2026/01') throw new Exception("Nomor mitra gagal disimpan");

    $auditCount = AuditTrail::where('document_id', $doc->id)->count();
    echo "   -> Total Jejak Audit Tercatat: {$auditCount} log resmi.\n";
    if ($auditCount !== 3) throw new Exception("Audit trail tidak lengkap (diharapkan 3 log)");
});

// -----------------------------------------------------------------------------
// SKENARIO 2: Penolakan Redaksional & Siklus Perbaikan Klausul dengan RS Siloam
// -----------------------------------------------------------------------------
runTestCase("KASUS 2: Penolakan Klausul Legal & Siklus Revisi (REVIEW -> DRAFT -> REVIEW -> VERIFIED -> ACTIVE)", function() use ($fsm) {
    $doc = Document::create([
        'doc_id' => 'UNIPAR-2026-SILOAM',
        'reg_id' => 'REG-SILOAM-2026',
        'doc_number' => '511/UNIPAR/MoU/X/2026',
        'track' => 'Jalur 1: Kemitraan Bertahap',
        'doc_type' => 'MoU',
        'partner_name' => 'RS Siloam Manado',
        'partner_category' => 'DUDI / Swasta',
        'unit' => 'Fakultas Ilmu Komputer',
        'status' => 'DRAFT'
    ]);

    echo "1. Pengusul mengajukan draf awal (SUBMIT)...\n";
    $fsm->transition($doc, 'SUBMIT', 'UNIT', 'Pengusul Fakultas');
    $doc->refresh();
    echo "   -> Status: {$doc->status}\n";

    echo "2. Tim Legal menemukan klausul memberatkan, mengembalikan draf (RETURN_TO_DRAFT)...\n";
    $fsm->transition($doc, 'RETURN_TO_DRAFT', 'LEGAL', 'Biro Hukum', 'Pasal 6 ayat 2 tentang penalti pembatalan sepihak wajib dihapus.');
    $doc->refresh();
    echo "   -> Status: {$doc->status} (Kembali ke Unit Pengusul)\n";
    if ($doc->status !== 'DRAFT') throw new Exception("Status gagal kembali ke DRAFT");

    echo "3. Unit Pengusul merevisi redaksi pasal dan mengajukan kembali (SUBMIT)...\n";
    $fsm->transition($doc, 'SUBMIT', 'UNIT', 'Pengusul Fakultas', 'Pasal 6 ayat 2 telah direvisi menjadi kesepakatan musyawarah mufakat.');
    $doc->refresh();
    echo "   -> Status: {$doc->status}\n";

    echo "4. Tim Legal menelaah ulang naskah revisi dan menyetujui (VERIFY)...\n";
    $fsm->transition($doc, 'VERIFY', 'LEGAL', 'Biro Hukum', 'Klausul perbaikan telah sesuai prinsip keadilan hukum.');
    $doc->refresh();
    echo "   -> Status: {$doc->status} (Lolos telaah hukum)\n";
    if ($doc->status !== 'VERIFIED') throw new Exception("Status gagal beralih ke VERIFIED");

    echo "5. Pimpinan mengesahkan naskah bertanda tangan basah (ACTIVATE)...\n";
    $fsm->transition($doc, 'ACTIVATE', 'LEADER', 'Rektor UNIPAR', 'Pengesahan naskah MoU sah', [
        'partner_doc_number' => 'SILOAM-MND/DIR/2026/89'
    ]);
    $doc->refresh();
    echo "   -> Status Akhir: {$doc->status}\n";
    if ($doc->status !== 'ACTIVE') throw new Exception("Status gagal aktif setelah revisi");

    $events = AuditTrail::where('document_id', $doc->id)->pluck('event')->toArray();
    echo "   -> Rantai Peristiwa Audit: " . implode(' -> ', $events) . "\n";
});

// -----------------------------------------------------------------------------
// SKENARIO 3: Pembatalan Pengusulan oleh Unit Pengusul (DRAFT -> CANCELLED)
// -----------------------------------------------------------------------------
runTestCase("KASUS 3: Pembatalan Naskah (CANCEL) & Proteksi Terminal State", function() use ($fsm) {
    $doc = Document::create([
        'doc_id' => 'UNIPAR-2026-CANCEL-TEST',
        'reg_id' => 'REG-CANCEL-2026',
        'track' => 'Jalur 1: Kemitraan Bertahap',
        'doc_type' => 'IA',
        'partner_name' => 'Startup Lokal Sulut',
        'partner_category' => 'DUDI / Swasta',
        'unit' => 'Teknik Informatika',
        'status' => 'DRAFT'
    ]);

    echo "1. Unit Pengusul membatalkan usulan naskah (CANCEL)...\n";
    $fsm->transition($doc, 'CANCEL', 'UNIT', 'Unit Pengusul', 'Mitra mengundurkan diri karena kendala restrukturisasi bisnis.');
    $doc->refresh();
    echo "   -> Status: {$doc->status} (Terminal State)\n";
    if ($doc->status !== 'CANCELLED') throw new Exception("Status gagal beralih ke CANCELLED");

    echo "2. Uji Coba: Mencoba mengeksekusi event lanjutan dari CANCELLED (SUBMIT / ACTIVATE)...\n";
    $blocked = false;
    try {
        $fsm->transition($doc, 'SUBMIT', 'UNIT', 'Unit Pengusul');
    } catch (IllegalTransitionException $e) {
        $blocked = true;
        echo "   -> Sukses Terblokir: " . $e->getMessage() . "\n";
    }
    if (!$blocked) throw new Exception("Pintu terminal state bocor! Seharusnya tidak boleh ada aksi dari CANCELLED.");
});

// -----------------------------------------------------------------------------
// SKENARIO 4: Penolakan oleh Pimpinan di Meja VERIFIED (VERIFIED -> DRAFT)
// -----------------------------------------------------------------------------
runTestCase("KASUS 4: Penolakan di Meja Pimpinan Setelah Lolos Legal (VERIFIED -> DRAFT)", function() use ($fsm) {
    $doc = Document::create([
        'doc_id' => 'UNIPAR-2026-MANDIRI',
        'reg_id' => 'REG-MANDIRI-2026',
        'track' => 'Jalur 1: Kemitraan Bertahap',
        'doc_type' => 'MoU',
        'partner_name' => 'PT Bank Mandiri (Persero) Tbk',
        'partner_category' => 'BUMN',
        'unit' => 'Biro Kerja Sama',
        'status' => 'DRAFT'
    ]);

    $fsm->transition($doc, 'SUBMIT', 'UNIT', 'Pengusul Biro');
    $fsm->transition($doc, 'VERIFY', 'LEGAL', 'Tim Legal');
    $doc->refresh();
    echo "1. Dokumen telah berstatus: {$doc->status} (Telah lolos verifikasi legal)\n";

    echo "2. Pimpinan Rektorat menemukan ketidaksesuaian jadwal kuota magang mahasiswa...\n";
    $fsm->transition($doc, 'RETURN_TO_DRAFT', 'LEADER', 'Dr. Rosdiana Simbolon', 'Jadwal kuota pelaksanaan diundur ke semester genap, mohon lampiran disesuaikan.');
    $doc->refresh();
    echo "   -> Status berhasil dikembalikan ke: {$doc->status}\n";
    if ($doc->status !== 'DRAFT') throw new Exception("Pimpinan gagal mengembalikan status dari VERIFIED ke DRAFT");

    $lastLog = AuditTrail::where('document_id', $doc->id)->latest('id')->first();
    echo "   -> Log Disposisi Pimpinan: [{$lastLog->from_status} -> {$lastLog->to_status}] {$lastLog->notes}\n";
});

// -----------------------------------------------------------------------------
// SKENARIO 5: Siklus Masa Berlaku Penuh (ACTIVE -> WARNING -> RENEW & EXPIRE -> RENEW)
// -----------------------------------------------------------------------------
runTestCase("KASUS 5: Siklus Peringatan H-90, Kedaluwarsa Alami, & Adendum Pemulihan", function() use ($fsm) {
    $doc = Document::create([
        'doc_id' => 'UNIPAR-2026-LIFECYCLE',
        'reg_id' => 'REG-LIFECYCLE-2026',
        'track' => 'Jalur 1: Kemitraan Bertahap',
        'doc_type' => 'MoA / PKS',
        'partner_name' => 'Pemerintah Kota Manado',
        'partner_category' => 'Pemerintah',
        'unit' => 'LPPM UNIPAR',
        'validity' => '1 Tahun',
        'status' => 'ACTIVE',
        'date_signed' => now()->subMonths(9)->toDateString()
    ]);
    echo "1. Status Dokumen Awal: {$doc->status}\n";

    echo "2. Scheduler sistem mendeteksi sisa masa berlaku <= 90 hari (TRIGGER_WARNING)...\n";
    $fsm->transition($doc, 'TRIGGER_WARNING', 'SYSTEM', 'Scheduler Otomasi');
    $doc->refresh();
    echo "   -> Status Dokumen: {$doc->status} (Ambang Batas H-90 Terpicu)\n";
    if ($doc->status !== 'WARNING') throw new Exception("Status gagal beralih ke WARNING");

    echo "3. Kemungkinan A: Pihak kampus dan mitra menyepakati perpanjangan adendum (RENEW)...\n";
    $fsm->transition($doc, 'RENEW', 'UNIT', 'LPPM UNIPAR', 'Inisiasi Adendum Perpanjangan 2 Tahun', [
        'validity' => '2 Tahun (Adendum)',
        'partner_doc_number' => 'PEMKOT-MND/ADND/2026/01'
    ]);
    $doc->refresh();
    echo "   -> Status Dokumen: {$doc->status} (Masa berlaku baru: {$doc->validity})\n";
    if ($doc->status !== 'ACTIVE' || $doc->validity !== '2 Tahun (Adendum)') throw new Exception("Gagal perpanjangan naskah");

    echo "4. Kemungkinan B: Masa adendum berakhir alami tanpa perpanjangan susulan (EXPIRE)...\n";
    $fsm->transition($doc, 'TRIGGER_WARNING', 'SYSTEM', 'Scheduler Otomasi');
    $fsm->transition($doc, 'EXPIRE', 'SYSTEM', 'Scheduler Otomasi', 'Masa berlaku naskah adendum telah habis.');
    $doc->refresh();
    echo "   -> Status Dokumen: {$doc->status}\n";
    if ($doc->status !== 'EXPIRED') throw new Exception("Gagal mengalihkan ke EXPIRED");

    echo "5. Kemungkinan C: Pasca-kedaluwarsa 3 bulan, disepakati reaktivasi naskah (RENEW)...\n";
    $fsm->transition($doc, 'RENEW', 'LEADER', 'Rektor UNIPAR', 'Reaktivasi naskah pasca-kedaluwarsa');
    $doc->refresh();
    echo "   -> Status Dokumen: {$doc->status} (Kembali Sah Aktif)\n";
    if ($doc->status !== 'ACTIVE') throw new Exception("Gagal reaktivasi naskah expired");
});

// -----------------------------------------------------------------------------
// SKENARIO 6: Uji Keamanan Hak Akses (RBAC Attack Simulation)
// -----------------------------------------------------------------------------
runTestCase("KASUS 6: Simulasi Penyerangan Hak Akses Peran (RBAC Guard Protection)", function() use ($fsm) {
    $doc = Document::create([
        'doc_id' => 'UNIPAR-2026-RBAC-ATTACK',
        'reg_id' => 'REG-RBAC-2026',
        'track' => 'Jalur 1: Kemitraan Bertahap',
        'doc_type' => 'MoU',
        'partner_name' => 'Mitra Uji Akses',
        'partner_category' => 'DUDI / Swasta',
        'unit' => 'Teknik Informatika',
        'status' => 'REVIEW'
    ]);

    echo "1. Simulasi: Pengguna peran UNIT mencoba mengeksekusi VERIFY (Hanya LEGAL/ADMIN)...\n";
    $unitBlocked = false;
    try {
        $fsm->transition($doc, 'VERIFY', 'UNIT', 'Mahasiswa Nakal');
    } catch (IllegalTransitionException $e) {
        $unitBlocked = true;
        echo "   -> Berhasil Ditolak: " . $e->getMessage() . "\n";
    }
    if (!$unitBlocked) throw new Exception("RBAC Jebol: UNIT berhasil mengeksekusi VERIFY!");

    echo "2. Dokumen beralih ke VERIFIED, lalu Tim LEGAL mencoba mengeksekusi ACTIVATE...\n";
    $doc->status = 'VERIFIED';
    $doc->save();

    $legalBlocked = false;
    try {
        $fsm->transition($doc, 'ACTIVATE', 'LEGAL', 'Staf Legal');
    } catch (IllegalTransitionException $e) {
        $legalBlocked = true;
        echo "   -> Berhasil Ditolak: " . $e->getMessage() . "\n";
    }
    if (!$legalBlocked) throw new Exception("RBAC Jebol: LEGAL berhasil mengeksekusi ACTIVATE tanda tangan Rektor!");

    echo "3. Verifikasi: Peran ADMIN (Superuser) diizinkan mengeksekusi ACTIVATE...\n";
    $fsm->transition($doc, 'ACTIVATE', 'ADMIN', 'Admin SIMKERMA');
    $doc->refresh();
    echo "   -> Status Dokumen: {$doc->status} (Superuser ADMIN Berhasil)\n";
    if ($doc->status !== 'ACTIVE') throw new Exception("Peran ADMIN gagal mengeksekusi aktivasi");
});

// -----------------------------------------------------------------------------
// SKENARIO 7: Uji Anti-Bypass Guard (Lompatan Status Ilegal)
// -----------------------------------------------------------------------------
runTestCase("KASUS 7: Simulasi Percobaan Lompatan Status Ilegal (Anti-Bypass Guard)", function() use ($fsm) {
    $doc = Document::create([
        'doc_id' => 'UNIPAR-2026-BYPASS-TEST',
        'reg_id' => 'REG-BYPASS-2026',
        'track' => 'Jalur 1: Kemitraan Bertahap',
        'doc_type' => 'MoU',
        'partner_name' => 'Mitra Uji Lompatan',
        'partner_category' => 'DUDI / Swasta',
        'unit' => 'Teknik Informatika',
        'status' => 'DRAFT'
    ]);

    echo "1. Mencoba langsung meloncat dari DRAFT ke ACTIVE tanpa pemeriksaan...\n";
    $jump1 = false;
    try {
        $fsm->transition($doc, 'ACTIVATE', 'LEADER', 'Rektor');
    } catch (IllegalTransitionException $e) {
        $jump1 = true;
        echo "   -> Ditolak: " . $e->getMessage() . "\n";
    }
    if (!$jump1) throw new Exception("Anti-Bypass Jebol: DRAFT berhasil loncat ke ACTIVE!");

    echo "2. Mencoba langsung meloncat dari DRAFT ke VERIFIED tanpa REVIEW...\n";
    $jump2 = false;
    try {
        $fsm->transition($doc, 'VERIFY', 'LEGAL', 'Tim Legal');
    } catch (IllegalTransitionException $e) {
        $jump2 = true;
        echo "   -> Ditolak: " . $e->getMessage() . "\n";
    }
    if (!$jump2) throw new Exception("Anti-Bypass Jebol: DRAFT berhasil loncat ke VERIFIED!");

    echo "3. Mencoba meloncat dari REVIEW langsung ke ACTIVE tanpa status VERIFIED...\n";
    $doc->status = 'REVIEW';
    $doc->save();
    $jump3 = false;
    try {
        $fsm->transition($doc, 'ACTIVATE', 'LEADER', 'Rektor');
    } catch (IllegalTransitionException $e) {
        $jump3 = true;
        echo "   -> Ditolak: " . $e->getMessage() . "\n";
    }
    if (!$jump3) throw new Exception("Anti-Bypass Jebol: REVIEW berhasil loncat ke ACTIVE tanpa VERIFIED!");
});

// -----------------------------------------------------------------------------
// SKENARIO 8: Sinkronisasi Kegiatan Pelaksanaan (Menu 05 - IKU 6 Real-time Sync)
// -----------------------------------------------------------------------------
runTestCase("KASUS 8: Sinkronisasi Dokumen ACTIVE dengan Pemantauan IKU 6 (Menu 05)", function() use ($fsm) {
    echo "1. Membuat dokumen kemitraan aktif dengan Balai Riset Industri...\n";
    $doc = Document::create([
        'doc_id' => 'UNIPAR-2026-RISET-IKU6',
        'reg_id' => 'REG-RISET-2026',
        'doc_number' => '515/UNIPAR/IA/X/2026',
        'track' => 'Jalur 1: Kemitraan Bertahap',
        'doc_type' => 'IA',
        'partner_name' => 'Balai Riset dan Standardisasi Industri Manado',
        'partner_category' => 'Pemerintah',
        'unit' => 'Program Studi Teknik Informatika',
        'scope' => 'Riset Kolaborasi Internet of Things (IoT) untuk Monitoring Mutu Pangan',
        'validity' => '1 Tahun',
        'status' => 'ACTIVE'
    ]);

    echo "2. Mencatat agenda pelaksanaan kegiatan Tridharma di lapangan...\n";
    $act = Activity::create([
        'document_id' => $doc->id,
        'activity_name' => 'Workshop Kalibrasi Sensor IoT & Uji Coba Lapangan Produk Pangan',
        'schedule_start' => '15 November 2026',
        'schedule_end' => '20 November 2026',
        'target_qty' => '30 Mahasiswa & 5 Dosen',
        'realized_qty' => '32 Mahasiswa & 5 Dosen (100%)',
        'parties_involved' => 'Dosen Prodi TI UNIPAR & Peneliti Ahli Baristand Manado',
        'outputs' => 'Prototipe Alat IoT Terkalibrasi & Artikel Jurnal Pengabdian',
        'budget_info' => 'Hibah Kemitraan Rp25.000.000',
        'execution_status' => 'SELESAI',
        'notes' => 'Kegiatan terlaksana dengan lancar memenuhi target capaian IKU 6.'
    ]);

    echo "3. Mencatat jejak audit relasional ke dokumen rujukan...\n";
    AuditTrail::create([
        'document_id' => $doc->id,
        'actor' => 'Bidang Kerja Sama (MASTER)',
        'role' => 'MASTER',
        'event' => 'RECORD_ACTIVITY',
        'from_status' => $doc->status,
        'to_status' => $doc->status,
        'notes' => 'Pencatatan realisasi kegiatan: ' . $act->activity_name . ' (' . $act->execution_status . ').',
        'timestamp_wita' => now()->format('Y-m-d H:i:s') . ' WITA',
    ]);

    $docActs = $doc->activities;
    echo "   -> Terhubung dengan Dokumen: {$doc->doc_id} ({$doc->partner_name})\n";
    echo "   -> Kegiatan Terverifikasi: {$docActs->count()} agenda\n";
    echo "   -> Luaran IKU 6 Tercatat: {$docActs->first()->outputs}\n";
    if ($docActs->count() !== 1) throw new Exception("Gagal relasi activity ke document");
});

echo "================================================================================\n";
echo "    RINGKASAN EKSEKUSI PENGUJIAN KOMPREHENSIF FSM 7-STATUS                      \n";
echo "    Total Kasus Nyata Diuji : " . ($passCount + $failCount) . "\n";
echo "    Lolos (PASS)            : {$passCount}\n";
echo "    Gagal (FAIL)            : {$failCount}\n";
echo "    Tingkat Keberhasilan    : " . round(($passCount / ($passCount + $failCount)) * 100, 2) . "%\n";
echo "================================================================================\n\n";

if ($failCount === 0) {
    echo "KESIMPULAN: Seluruh skenario alur nyata, penolakan revisi, pembatalan,\n";
    echo "            ambang peringatan H-90, keamanan RBAC, dan pencegahan anti-bypass\n";
    echo "            FSM 7-Status SIMKERMA UNIPAR terbukti 100% KOKOH, AMAN, & SIAP SIDANG!\n\n";
} else {
    echo "PERINGATAN: Terdapat kegagalan pengujian yang memerlukan investigasi lanjutan.\n\n";
}
