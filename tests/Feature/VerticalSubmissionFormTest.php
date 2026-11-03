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
}
