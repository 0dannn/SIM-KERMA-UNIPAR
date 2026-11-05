<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Document;
use App\Models\AuditTrail;
use App\Models\User;
use App\Services\FSMWorkflowEngineService;
use App\Exceptions\IllegalTransitionException;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class FSMComprehensiveWorkflowTest extends TestCase
{
    use DatabaseTransactions;

    protected FSMWorkflowEngineService $fsmService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->fsmService = app(FSMWorkflowEngineService::class);
    }

    /**
     * KASUS 1: Alur Normal / Jalur Mulus (Happy Path)
     * DRAFT -> SUBMIT (UNIT) -> REVIEW -> VERIFY (LEGAL) -> VERIFIED -> ACTIVATE (LEADER) -> ACTIVE
     */
    public function test_kasus_1_happy_path_kemitraan_telkom(): void
    {
        $doc = Document::create([
            'doc_id' => 'UNIPAR-TEST-KASUS-01',
            'reg_id' => 'REG-UNIPAR-TEST-01',
            'doc_number' => '501/UNIPAR/MoA/X/2026',
            'track' => 'Jalur 1: Kemitraan Bertahap',
            'doc_type' => 'MoA / PKS',
            'partner_name' => 'PT Telkom Indonesia (Persero) Tbk',
            'partner_category' => 'BUMN',
            'unit' => 'Program Studi Teknik Informatika',
            'scope' => 'Penyelenggaraan Program Magang MBKM dan Sertifikasi Cloud AI Mahasiswa',
            'validity' => '2 Tahun',
            'warning_threshold' => 'H-90 Hari (3 Bulan)',
            'status' => 'DRAFT'
        ]);

        $this->assertEquals('DRAFT', $doc->status);

        // 1. UNIT melakukan pengajuan naskah draf ke meja pemeriksaan
        $this->fsmService->transition($doc, 'SUBMIT', 'UNIT', 'Ketua Prodi TI');
        $doc->refresh();
        $this->assertEquals('REVIEW', $doc->status);

        // 2. Tim LEGAL melakukan telaah mutu kurikulum MBKM & hukum, lalu memverifikasi
        $this->fsmService->transition($doc, 'VERIFY', 'LEGAL', 'Biro Hukum & Tata Laksana UNIPAR', 'Klausul hukum berimbang, HAKI aman, dan selaras capaian IKU 6');
        $doc->refresh();
        $this->assertEquals('VERIFIED', $doc->status);
        $this->assertNotNull($doc->date_approved);

        // 3. Pimpinan Rektorat (LEADER) menandatangani basah, menempel meterai Rp10.000, dan mengaktifkan
        $this->fsmService->transition($doc, 'ACTIVATE', 'LEADER', 'Dr. Rosdiana Simbolon (Rektor)', 'Naskah resmi bertanda tangan basah dan bermeterai sah telah diaktifkan', [
            'partner_doc_number' => 'TELKOM/REG/X/2026/04',
            'signed_file' => 'signed_scan_telkom_mbkm_2026.pdf',
            'date_signed' => now()->toDateString()
        ]);
        $doc->refresh();
        $this->assertEquals('ACTIVE', $doc->status);
        $this->assertEquals('TELKOM/REG/X/2026/04', $doc->partner_doc_number);
        $this->assertEquals('signed_scan_telkom_mbkm_2026.pdf', $doc->signed_file);
        $this->assertNotNull($doc->date_signed);

        // Verifikasi Jejak Audit Trail Lengkap
        $trails = AuditTrail::where('document_id', $doc->id)->orderBy('id')->get();
        $this->assertCount(3, $trails);
        $this->assertEquals('SUBMIT', $trails[0]->event);
        $this->assertEquals('VERIFY', $trails[1]->event);
        $this->assertEquals('ACTIVATE', $trails[2]->event);
        $this->assertEquals('VERIFIED', $trails[1]->to_status);
        $this->assertEquals('ACTIVE', $trails[2]->to_status);
    }

    /**
     * KASUS 2: Penolakan Klausul oleh Tim Legal & Siklus Revisi Pengusul
     * DRAFT -> SUBMIT -> REVIEW -> RETURN_TO_DRAFT (LEGAL) -> DRAFT -> SUBMIT -> REVIEW -> VERIFY -> VERIFIED
     */
    public function test_kasus_2_rejection_and_revision_loop_siloam(): void
    {
        $doc = Document::create([
            'doc_id' => 'UNIPAR-TEST-KASUS-02',
            'reg_id' => 'REG-UNIPAR-TEST-02',
            'track' => 'Jalur 1: Kemitraan Bertahap',
            'doc_type' => 'MoU',
            'partner_name' => 'RS Siloam Hospitals Manado',
            'partner_category' => 'DUDI / Swasta',
            'unit' => 'Fakultas Ilmu Komputer',
            'scope' => 'Praktek Kerja Lapangan Mahasiswa',
            'status' => 'DRAFT'
        ]);

        // Submit oleh unit
        $this->fsmService->transition($doc, 'SUBMIT', 'UNIT', 'Pengusul Fakultas');
        $doc->refresh();
        $this->assertEquals('REVIEW', $doc->status);

        // Tim Legal menolak karena ada klausul biaya memberatkan kampus
        $rejectNote = 'Pasal 4 ayat 2 mewajibkan kampus menanggung biaya uji lab sepihak. Mohon direvisi agar berimbang.';
        $this->fsmService->transition($doc, 'RETURN_TO_DRAFT', 'LEGAL', 'Tim Telaah Legal', $rejectNote);
        $doc->refresh();
        $this->assertEquals('DRAFT', $doc->status);

        // Unit Pengusul merevisi draf dan mengajukan kembali
        $this->fsmService->transition($doc, 'SUBMIT', 'UNIT', 'Pengusul Fakultas', 'Draf telah diperbaiki sesuai arahan Tim Legal pada pasal 4 ayat 2');
        $doc->refresh();
        $this->assertEquals('REVIEW', $doc->status);

        // Tim Legal menelaah kembali dan menyetujui (VERIFY)
        $this->fsmService->transition($doc, 'VERIFY', 'LEGAL', 'Tim Telaah Legal', 'Klausul perbaikan telah sesuai dan adil bagi kedua pihak');
        $doc->refresh();
        $this->assertEquals('VERIFIED', $doc->status);

        // Audit Trail harus merekam seluruh siklus penolakan
        $trails = AuditTrail::where('document_id', $doc->id)->pluck('event')->toArray();
        $this->assertEquals(['SUBMIT', 'RETURN_TO_DRAFT', 'SUBMIT', 'VERIFY'], $trails);
    }

    /**
     * KASUS 3: Pembatalan Pengusulan Dokumen (DRAFT -> CANCELLED)
     * Memastikan CANCELLED adalah Terminal State yang kebal manipulasi aksi
     */
    public function test_kasus_3_cancellation_and_terminal_state_protection(): void
    {
        $doc = Document::create([
            'doc_id' => 'UNIPAR-TEST-KASUS-03',
            'reg_id' => 'REG-UNIPAR-TEST-03',
            'track' => 'Jalur 1: Kemitraan Bertahap',
            'doc_type' => 'IA',
            'partner_name' => 'Startup Inovasi Digital Manado',
            'partner_category' => 'DUDI / Swasta',
            'unit' => 'Teknik Informatika',
            'status' => 'DRAFT'
        ]);

        // Unit membatalkan usulan
        $this->fsmService->transition($doc, 'CANCEL', 'UNIT', 'Unit Pengusul', 'Mitra mengalami perubahan manajemen internal dan belum siap bekerjasama');
        $doc->refresh();
        $this->assertEquals('CANCELLED', $doc->status);

        // Uji kekebalan: tidak ada event yang boleh dieksekusi dari CANCELLED
        $this->expectException(IllegalTransitionException::class);
        $this->fsmService->transition($doc, 'SUBMIT', 'UNIT', 'Unit Pengusul');
    }

    /**
     * KASUS 4: Penolakan oleh Pimpinan di Meja VERIFIED (VERIFIED -> DRAFT)
     * Pimpinan menemukan kendala kuota/jadwal saat naskah sudah lolos telaah legal
     */
    public function test_kasus_4_leader_return_to_draft_from_verified(): void
    {
        $doc = Document::create([
            'doc_id' => 'UNIPAR-TEST-KASUS-04',
            'reg_id' => 'REG-UNIPAR-TEST-04',
            'track' => 'Jalur 1: Kemitraan Bertahap',
            'doc_type' => 'MoU',
            'partner_name' => 'PT Bank Mandiri (Persero) Tbk',
            'partner_category' => 'BUMN',
            'unit' => 'Biro Kerja Sama UNIPAR',
            'status' => 'DRAFT'
        ]);

        $this->fsmService->transition($doc, 'SUBMIT', 'UNIT', 'Pengusul Biro');
        $this->fsmService->transition($doc, 'VERIFY', 'LEGAL', 'Tim Legal');
        $doc->refresh();
        $this->assertEquals('VERIFIED', $doc->status);

        // Rektor mengembalikan naskah ke DRAFT karena arahan kuota
        $leaderNote = 'Jadwal pelaksanaan magang diundur ke semester genap. Mohon lampiran waktu disesuaikan terlebih dahulu.';
        $this->fsmService->transition($doc, 'RETURN_TO_DRAFT', 'LEADER', 'Dr. Rosdiana Simbolon (Rektor)', $leaderNote);
        $doc->refresh();
        $this->assertEquals('DRAFT', $doc->status);

        $lastTrail = AuditTrail::where('document_id', $doc->id)->latest('id')->first();
        $this->assertEquals('RETURN_TO_DRAFT', $lastTrail->event);
        $this->assertEquals('VERIFIED', $lastTrail->from_status);
        $this->assertEquals('DRAFT', $lastTrail->to_status);
    }

    /**
     * KASUS 5: Siklus Masa Berlaku (ACTIVE -> WARNING -> RENEW & EXPIRE -> RENEW)
     */
    public function test_kasus_5_lifecycle_warning_renew_and_expiry(): void
    {
        $doc = Document::create([
            'doc_id' => 'UNIPAR-TEST-KASUS-05',
            'reg_id' => 'REG-UNIPAR-TEST-05',
            'track' => 'Jalur 1: Kemitraan Bertahap',
            'doc_type' => 'MoA / PKS',
            'partner_name' => 'Pemerintah Kota Manado',
            'partner_category' => 'Pemerintah',
            'unit' => 'LPPM UNIPAR',
            'validity' => '1 Tahun',
            'warning_threshold' => 'H-90 Hari (3 Bulan)',
            'status' => 'ACTIVE',
            'date_signed' => now()->subMonths(9)->toDateString()
        ]);

        // 1. Ambang batas peringatan terpicu oleh scheduler sistem
        $this->fsmService->transition($doc, 'TRIGGER_WARNING', 'SYSTEM', 'Scheduler Otomasi');
        $doc->refresh();
        $this->assertEquals('WARNING', $doc->status);

        // 2. Skenario A: Inisiasi Adendum (RENEW) oleh Unit kembali ke DRAFT, lalu melewati alur penuh telaah hukum
        $this->fsmService->transition($doc, 'RENEW', 'UNIT', 'LPPM UNIPAR', 'Inisiasi Adendum Perpanjangan 2 Tahun', [
            'validity' => '2 Tahun (Adendum)',
            'partner_doc_number' => 'PEMKOT-MND/ADND/2026/01'
        ]);
        $doc->refresh();
        $this->assertEquals('DRAFT', $doc->status);
        $this->assertEquals('2 Tahun (Adendum)', $doc->validity);

        // Alur telaah adendum: SUBMIT -> REVIEW -> VERIFY -> VERIFIED -> ACTIVATE -> ACTIVE
        $this->fsmService->transition($doc, 'SUBMIT', 'UNIT', 'LPPM UNIPAR');
        $doc->refresh();
        $this->assertEquals('REVIEW', $doc->status);

        $this->fsmService->transition($doc, 'VERIFY', 'LEGAL', 'Tim Legal UNIPAR');
        $doc->refresh();
        $this->assertEquals('VERIFIED', $doc->status);

        $this->fsmService->transition($doc, 'ACTIVATE', 'LEADER', 'Rektor UNIPAR', 'Pengesahan Naskah Adendum Bermeterai', [
            'signed_file' => 'adendum_signed_scan.pdf'
        ]);
        $doc->refresh();
        $this->assertEquals('ACTIVE', $doc->status);

        // 3. Skenario B: Peringatan terpicu lagi dan dibiarkan hingga kedaluwarsa alami
        $this->fsmService->transition($doc, 'TRIGGER_WARNING', 'SYSTEM', 'Scheduler Otomasi');
        $this->fsmService->transition($doc, 'EXPIRE', 'SYSTEM', 'Scheduler Otomasi', 'Masa berlaku naskah kerja sama telah berakhir secara alami');
        $doc->refresh();
        $this->assertEquals('EXPIRED', $doc->status);

        // 4. Skenario C: Reaktivasi naskah yang sudah kedaluwarsa kembali ke DRAFT untuk penelaahan ulang
        $this->fsmService->transition($doc, 'RENEW', 'LEADER', 'Rektor UNIPAR', 'Reaktivasi naskah pasca-kedaluwarsa melalui adendum pembaruan');
        $doc->refresh();
        $this->assertEquals('DRAFT', $doc->status);

        // Draf reaktivasi ditelaah kembali hingga aktif
        $this->fsmService->transition($doc, 'SUBMIT', 'UNIT', 'LPPM UNIPAR');
        $this->fsmService->transition($doc, 'VERIFY', 'LEGAL', 'Tim Legal UNIPAR');
        $this->fsmService->transition($doc, 'ACTIVATE', 'LEADER', 'Rektor UNIPAR', 'Pengesahan Naskah Reaktivasi Sah', [
            'signed_file' => 'reaktivasi_signed_scan.pdf'
        ]);
        $doc->refresh();
        $this->assertEquals('ACTIVE', $doc->status);
    }

    /**
     * KASUS 6: Uji Keamanan Hak Akses (RBAC Attack Simulation)
     * Menguji penolakan peran yang tidak berwenang
     */
    public function test_kasus_6_rbac_security_violations_are_blocked(): void
    {
        $doc = Document::create([
            'doc_id' => 'UNIPAR-TEST-KASUS-06',
            'reg_id' => 'REG-UNIPAR-TEST-06',
            'track' => 'Jalur 1: Kemitraan Bertahap',
            'doc_type' => 'MoU',
            'partner_name' => 'Mitra Uji RBAC',
            'partner_category' => 'DUDI / Swasta',
            'unit' => 'Teknik Informatika',
            'status' => 'REVIEW'
        ]);

        // 1. UNIT mencoba melakukan VERIFY (Hanya LEGAL, ADMIN, MASTER yang berhak)
        try {
            $this->fsmService->transition($doc, 'VERIFY', 'UNIT', 'Mahasiswa / Dosen Pengusul');
            $this->fail('Harusnya melempar IllegalTransitionException untuk RBAC UNIT -> VERIFY');
        } catch (IllegalTransitionException $e) {
            $this->assertStringContainsString('Pelanggaran RBAC', $e->getMessage());
        }

        // 2. Ubah status ke VERIFIED, lalu LEGAL mencoba melakukan ACTIVATE (Hanya LEADER yang berhak)
        $doc->status = 'VERIFIED';
        $doc->save();

        try {
            $this->fsmService->transition($doc, 'ACTIVATE', 'LEGAL', 'Staf Legal');
            $this->fail('Harusnya melempar IllegalTransitionException untuk RBAC LEGAL -> ACTIVATE');
        } catch (IllegalTransitionException $e) {
            $this->assertStringContainsString('Pelanggaran RBAC', $e->getMessage());
        }

        // 3. Peran ADMIN dan MASTER memiliki wewenang superuser
        $this->fsmService->transition($doc, 'ACTIVATE', 'ADMIN', 'Administrator Sistem');
        $doc->refresh();
        $this->assertEquals('ACTIVE', $doc->status);
    }

    /**
     * KASUS 7: Uji Anti-Bypass Guard (Lompatan Status Ilegal)
     */
    public function test_kasus_7_anti_bypass_illegal_jumps_are_blocked(): void
    {
        $doc = Document::create([
            'doc_id' => 'UNIPAR-TEST-KASUS-07',
            'reg_id' => 'REG-UNIPAR-TEST-07',
            'track' => 'Jalur 1: Kemitraan Bertahap',
            'doc_type' => 'MoU',
            'partner_name' => 'Mitra Uji Bypass',
            'partner_category' => 'DUDI / Swasta',
            'unit' => 'Teknik Informatika',
            'status' => 'DRAFT'
        ]);

        // Percobaan 1: DRAFT langsung melompat ke ACTIVE
        try {
            $this->fsmService->transition($doc, 'ACTIVATE', 'LEADER', 'Rektor');
            $this->fail('Harusnya melempar IllegalTransitionException: DRAFT -> ACTIVATE');
        } catch (IllegalTransitionException $e) {
            $this->assertStringContainsString('Pelanggaran Alur FSM', $e->getMessage());
        }

        // Percobaan 2: DRAFT langsung melompat ke VERIFIED
        try {
            $this->fsmService->transition($doc, 'VERIFY', 'LEGAL', 'Tim Legal');
            $this->fail('Harusnya melempar IllegalTransitionException: DRAFT -> VERIFY');
        } catch (IllegalTransitionException $e) {
            $this->assertStringContainsString('Pelanggaran Alur FSM', $e->getMessage());
        }

        // Percobaan 3: REVIEW langsung melompat ke ACTIVE tanpa melewati VERIFIED
        $doc->status = 'REVIEW';
        $doc->save();
        try {
            $this->fsmService->transition($doc, 'ACTIVATE', 'LEADER', 'Rektor');
            $this->fail('Harusnya melempar IllegalTransitionException: REVIEW -> ACTIVATE');
        } catch (IllegalTransitionException $e) {
            $this->assertStringContainsString('Pelanggaran Alur FSM', $e->getMessage());
        }
    }

    /**
     * KASUS 8: Uji HTTP Controller via Web Request (/simkerma/transition)
     * Menguji Controller, Session Flash, dan Proteksi Delete Non-DRAFT
     */
    public function test_kasus_8_http_controller_transition_and_delete_protection(): void
    {
        $doc = Document::create([
            'doc_id' => 'UNIPAR-TEST-KASUS-08',
            'reg_id' => 'REG-UNIPAR-TEST-08',
            'track' => 'Jalur 1: Kemitraan Bertahap',
            'doc_type' => 'MoA / PKS',
            'partner_name' => 'PT Bank SulutGo (BSG)',
            'partner_category' => 'BUMD',
            'unit' => 'Teknik Informatika',
            'status' => 'REVIEW'
        ]);

        // Simulasikan request HTTP POST dari Tim Legal untuk event VERIFY
        $response = $this->withSession(['active_role' => 'LEGAL'])
            ->post(route('simkerma.transition'), [
                'doc_id' => $doc->doc_id,
                'event' => 'VERIFY',
                'notes' => 'Lolos telaah legal dari antarmuka Web UI'
            ]);

        $response->assertRedirect(route('simkerma.index', ['menu' => 4, 'doc' => $doc->doc_id]));
        $doc->refresh();
        $this->assertEquals('VERIFIED', $doc->status);

        // Coba hapus dokumen yang berstatus VERIFIED -> Harus Ditolak!
        $deleteResponse = $this->withSession(['active_role' => 'MASTER'])
            ->post(route('simkerma.delete-draft'), [
                'doc_id' => $doc->doc_id
            ]);

        $deleteResponse->assertSessionHas('flash.type', 'danger');
        $this->assertDatabaseHas('documents', ['doc_id' => $doc->doc_id]);
    }

    /**
     * KASUS 9: Fitur Pembersihan Mode Pengembangan (Reset All Documents)
     */
    public function test_kasus_9_reset_all_documents_clears_database(): void
    {
        Document::create([
            'doc_id' => 'DOC-RESET-TEST',
            'reg_id' => 'REG-RESET-TEST',
            'doc_number' => '999/UNIPAR/MoA/X/2026',
            'track' => 'Jalur 1: Kemitraan Bertahap',
            'doc_type' => 'MoA / PKS',
            'partner_name' => 'Mitra Uji Reset',
            'partner_category' => 'DUDI / Swasta',
            'unit' => 'Program Studi Teknik Informatika',
            'scope' => 'Uji Coba Reset Semua Dokumen',
            'status' => 'DRAFT'
        ]);

        $this->assertDatabaseHas('documents', ['doc_id' => 'DOC-RESET-TEST']);

        $response = $this->post(route('simkerma.reset-all-documents'));
        $response->assertRedirect(route('simkerma.index', ['menu' => 4]));
        $response->assertSessionHas('flash.type', 'success');

        $this->assertEquals(0, Document::count());
    }
}
