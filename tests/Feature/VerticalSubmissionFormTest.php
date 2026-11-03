<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Document;
use App\Models\AuditTrail;
use App\Models\Partner;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class VerticalSubmissionFormTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Test pengajuan draf baru Jalur 1 dengan 10 field lengkap (menurun ke bawah).
     */
    public function test_jalur1_submission_with_all_10_fields(): void
    {
        $payload = [
            'title' => 'Perjanjian Kerja Sama Riset Kecerdasan Buatan & IoT Agrikultur',
            'doc_type' => 'MoA / PKS',
            'parent_mou' => '-',
            'unit' => 'Fakultas Ilmu Komputer',
            'first_party_signer' => 'Prof. Dr. Ir. Rektor UNIPAR',
            'partner_name' => 'PT Solusi Cerdas Nusantara',
            'category' => 'DUDI / Swasta',
            'second_party_signer' => 'Budi Santoso, M.Kom. (Direktur Eksekutif)',
            'partner_doc_number' => '077/SCN/DIR/XI/2026',
            'scope' => 'Pengembangan ekosistem smart farming berbasis IoT dan pelatihan magang industri mahasiswa MBKM',
            'start_date' => '2026-11-01',
            'end_date' => '2028-11-01',
            'validity' => '2 Tahun',
            'warning_threshold' => 'H-90 Hari (3 Bulan)',
            'pic_first_party' => 'Dr. Alan Turing, M.Kom. (0812-1111-2222 / alan@unipar.ac.id)',
            'pic_second_party' => 'Siti Rahmawati, S.Kom. (0813-3333-4444 / partner@solusicerdas.co.id)',
            'nominal_value' => 'Rp 150.000.000 (Hibah Riset & Magang)',
            'description' => 'Kerja sama strategis 2 tahun untuk hilirisasi teknologi kecerdasan buatan dan pemenuhan IKU 6 UNIPAR',
        ];

        $response = $this->post(route('simkerma.store-jalur1'), $payload);

        $response->assertStatus(302);

        // Ambil dokumen yang baru dibuat
        $doc = Document::where('partner_name', 'PT Solusi Cerdas Nusantara')->first();
        $this->assertNotNull($doc, 'Dokumen harus tersimpan di basis data.');

        // Verifikasi ke-10 field tersimpan dengan tepat
        $this->assertEquals('Perjanjian Kerja Sama Riset Kecerdasan Buatan & IoT Agrikultur', $doc->title);
        $this->assertEquals('MoA / PKS', $doc->doc_type);
        $this->assertEquals('Fakultas Ilmu Komputer', $doc->unit);
        $this->assertEquals('Prof. Dr. Ir. Rektor UNIPAR', $doc->first_party_signer);
        $this->assertEquals('PT Solusi Cerdas Nusantara', $doc->partner_name);
        $this->assertEquals('DUDI / Swasta', $doc->partner_category);
        $this->assertEquals('Budi Santoso, M.Kom. (Direktur Eksekutif)', $doc->second_party_signer);
        $this->assertEquals('077/SCN/DIR/XI/2026', $doc->partner_doc_number);
        $this->assertEquals('Pengembangan ekosistem smart farming berbasis IoT dan pelatihan magang industri mahasiswa MBKM', $doc->scope);
        $this->assertEquals('2026-11-01', $doc->start_date->format('Y-m-d'));
        $this->assertEquals('2028-11-01', $doc->end_date->format('Y-m-d'));
        $this->assertEquals('2 Tahun', $doc->validity);
        $this->assertEquals('Dr. Alan Turing, M.Kom. (0812-1111-2222 / alan@unipar.ac.id)', $doc->pic_first_party);
        $this->assertEquals('Siti Rahmawati, S.Kom. (0813-3333-4444 / partner@solusicerdas.co.id)', $doc->pic_second_party);
        $this->assertEquals('Rp 150.000.000 (Hibah Riset & Magang)', $doc->nominal_value);
        $this->assertEquals('Kerja sama strategis 2 tahun untuk hilirisasi teknologi kecerdasan buatan dan pemenuhan IKU 6 UNIPAR', $doc->description);
        $this->assertEquals('DRAFT', $doc->status);

        // Verifikasi halaman verifikasi QR Code menampilkan field-field baru
        $verifyResponse = $this->get(route('simkerma.verify', ['reg_id' => $doc->reg_id]));
        $verifyResponse->assertStatus(200);
        $verifyResponse->assertSee('Perjanjian Kerja Sama Riset Kecerdasan Buatan &amp; IoT Agrikultur', false);
        $verifyResponse->assertSee('PT Solusi Cerdas Nusantara');
        $verifyResponse->assertSee('Budi Santoso, M.Kom.');
        $verifyResponse->assertSee('Prof. Dr. Ir. Rektor UNIPAR');
        $verifyResponse->assertSee('Rp 150.000.000');
        $verifyResponse->assertSee('Dr. Alan Turing');
        $verifyResponse->assertSee('Siti Rahmawati');

        // Verifikasi tabel Menu 4 memuat judul kerja sama dan mitra
        $menuResponse = $this->get('/?menu=4');
        $menuResponse->assertStatus(200);
        $menuResponse->assertSee('PT Solusi Cerdas Nusantara');
        $menuResponse->assertSee('Perjanjian Kerja Sama Riset Kecerdasan Buatan &amp; IoT Agrikultur', false);
        $menuResponse->assertSee('Rp 150.000.000');
    }

    /**
     * Test pengajuan draf baru Jalur 2 (Paket Terpadu PKM) dengan 10 field lengkap.
     */
    public function test_jalur2_submission_with_all_fields(): void
    {
        $payload = [
            'title' => 'Program Kolaborasi PKM Desa Binaan Pesisir Manado',
            'topic' => 'Pemberdayaan Nelayan Pesisir Melalui Teknologi Solar Dryer',
            'unit_main' => 'Fakultas Ilmu Komputer',
            'unit_collab' => 'Fakultas Ekonomi dan Bisnis',
            'first_party_signer' => 'Dekan Fakultas Ilmu Komputer',
            'partner_name' => 'Koperasi Nelayan Bahari Manado',
            'category' => 'Lembaga Keagamaan / Komunitas',
            'second_party_signer' => 'Ketua Koperasi Nelayan Bahari',
            'partner_doc_number' => '012/KNB/PKM/2026',
            'start_date' => '2026-11-01',
            'end_date' => '2027-11-01',
            'duration' => '1 Tahun',
            'pic_first_party' => 'Ketua Tim Dosen Pengabdi (0811-2233-4455)',
            'pic_second_party' => 'Sekretaris Koperasi (0812-9988-7766)',
            'students_data' => '10 Mahasiswa Prodi TI & Akuntansi',
            'nominal_value' => 'Rp 30.000.000',
            'description' => 'Paket Terpadu 1 Pintu untuk percepatan PKM Kolaboratif Multidisiplin',
        ];

        $response = $this->post(route('simkerma.store-jalur2'), $payload);
        $response->assertStatus(302);

        $doc = Document::where('partner_name', 'Koperasi Nelayan Bahari Manado')->first();
        $this->assertNotNull($doc);
        $this->assertEquals('Program Kolaborasi PKM Desa Binaan Pesisir Manado', $doc->title);
        $this->assertEquals('Rp 30.000.000', $doc->nominal_value);
        $this->assertEquals('Dekan Fakultas Ilmu Komputer', $doc->first_party_signer);
        $this->assertEquals('Ketua Koperasi Nelayan Bahari', $doc->second_party_signer);
    }

    /**
     * Test penempatan QR Code di bagian ujung kanan bawah halaman terakhir dokumen
     * serta dukungan ukuran kertas A4 dan F4 (Folio).
     */
    public function test_qr_code_placement_and_paper_size_a4_and_f4(): void
    {
        $payloadF4 = [
            'title' => 'Pengujian Kerja Sama Format Kertas F4 (Folio)',
            'doc_type' => 'MoU',
            'paper_size' => 'F4',
            'parent_mou' => '-',
            'unit' => 'Program Studi Teknik Informatika',
            'partner_name' => 'PT Mitra Pengujian F4',
            'category' => 'DUDI / Swasta',
            'scope' => 'Uji coba penempatan QR Code pada kertas F4',
            'validity' => '1 Tahun',
        ];

        $response = $this->post(route('simkerma.store-jalur1'), $payloadF4);
        $response->assertStatus(302);

        $doc = Document::where('partner_name', 'PT Mitra Pengujian F4')->first();
        $this->assertNotNull($doc);
        $this->assertEquals('F4', $doc->paper_size);

        $stampService = app(\App\Services\DocxQrStampService::class);

        // 1. Uji Stempel pada Kertas F4
        $resF4 = $stampService->stampMoU($doc, null, 'F4');
        $this->assertTrue($resF4['success']);
        $this->assertEquals('F4', $resF4['paper_size']);
        $this->assertFileExists(public_path($resF4['docx_path']));

        // Periksa OpenXML berkas Word F4
        $zipF4 = new \ZipArchive();
        $this->assertTrue($zipF4->open(public_path($resF4['docx_path'])));
        $xmlF4 = $zipF4->getFromName('word/document.xml');
        $zipF4->close();

        // Verifikasi dimensi kertas F4 (12189 x 18708 dxa)
        $this->assertStringContainsString('w:w="12189"', $xmlF4);
        $this->assertStringContainsString('w:h="18708"', $xmlF4);

        // Verifikasi penempatan QR Code di ujung kanan bawah (relativeFrom="margin", align right & bottom)
        $this->assertStringContainsString('<wp:positionH relativeFrom="margin"><wp:align>right</wp:align></wp:positionH>', $xmlF4);
        $this->assertStringContainsString('<wp:positionV relativeFrom="margin"><wp:align>bottom</wp:align></wp:positionV>', $xmlF4);

        // 2. Uji Stempel pada Kertas A4
        $resA4 = $stampService->stampMoU($doc, null, 'A4');
        $this->assertTrue($resA4['success']);
        $this->assertEquals('A4', $resA4['paper_size']);
        $this->assertFileExists(public_path($resA4['docx_path']));

        $zipA4 = new \ZipArchive();
        $this->assertTrue($zipA4->open(public_path($resA4['docx_path'])));
        $xmlA4 = $zipA4->getFromName('word/document.xml');
        $zipA4->close();

        // Verifikasi dimensi kertas A4 (11906 x 16838 dxa)
        $this->assertStringContainsString('w:w="11906"', $xmlA4);
        $this->assertStringContainsString('w:h="16838"', $xmlA4);

        // Verifikasi penempatan QR Code di ujung kanan bawah
        $this->assertStringContainsString('<wp:positionH relativeFrom="margin"><wp:align>right</wp:align></wp:positionH>', $xmlA4);
        $this->assertStringContainsString('<wp:positionV relativeFrom="margin"><wp:align>bottom</wp:align></wp:positionV>', $xmlA4);
    }

    /**
     * Test pengajuan MoA / PKS dengan parent_mou kosong (null) tidak boleh menyebabkan error database.
     */
    public function test_moa_submission_with_empty_parent_mou_defaults_safely(): void
    {
        $payload = [
            'title' => 'Kerja Sama MoA Tanpa Dokumen Induk (Mandiri)',
            'partner_name' => 'PT Mitra Tanpa Induk',
            'doc_type' => 'MoA / PKS',
            'paper_size' => 'A4',
            'parent_mou' => '', // string kosong yang dikonversi middleware ke null
            'scope' => 'Pelaksanaan kegiatan magang mandiri',
            'start_date' => '2026-11-03',
            'end_date' => '2027-11-03',
        ];

        $response = $this->post(route('simkerma.store-jalur1'), $payload);
        $response->assertStatus(302);

        $doc = Document::where('partner_name', 'PT Mitra Tanpa Induk')->first();
        $this->assertNotNull($doc, 'Dokumen harus berhasil dibuat tanpa error Integrity constraint violation.');
        $this->assertEquals('-', $doc->parent_mou, 'parent_mou harus default ke tanda strip -');
        $this->assertEquals('MoA / PKS', $doc->doc_type);
        $this->assertEquals('A4', $doc->paper_size);
    }
}
