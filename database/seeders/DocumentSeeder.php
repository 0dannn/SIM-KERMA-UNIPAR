<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Document;
use App\Models\AuditTrail;
use App\Models\Partner;
use App\Models\Activity;
use Illuminate\Support\Facades\File;

class DocumentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jsonPath = base_path('../convo-3 31 Oct 2026 continous (Internal Chat)/Web_Graybox/data/simkerma_db.json');
        if (!File::exists($jsonPath)) {
            $jsonPath = base_path('../main progress & result/Web_App/data/simkerma_db.json');
        }

        if (File::exists($jsonPath)) {
            $data = json_decode(File::get($jsonPath), true);
            $documents = $data['documents'] ?? [];
            foreach ($documents as $d) {
                $doc = Document::create([
                    'doc_id' => $d['id'],
                    'reg_id' => $d['reg_id'],
                    'doc_number' => $d['doc_number'] ?? null,
                    'track' => $d['track'] ?? 'Jalur 1: Kemitraan Bertahap',
                    'doc_type' => $d['doc_type'] ?? 'MoU',
                    'parent_mou' => $d['parent_mou'] ?? '-',
                    'unit' => $d['unit'] ?? 'Universitas Kristen Papua',
                    'collab_unit' => $d['collab_unit'] ?? null,
                    'partner_name' => $d['partner_name'] ?? 'Mitra',
                    'partner_category' => $d['partner_category'] ?? 'DUDI / Swasta',
                    'scope' => $d['scope'] ?? null,
                    'students_data' => $d['students_data'] ?? null,
                    'validity' => $d['validity'] ?? '1 Tahun',
                    'warning_threshold' => $d['warning_threshold'] ?? 'H-90 Hari (3 Bulan)',
                    'status' => $d['status'] ?? 'DRAFT',
                    'date_approved' => !empty($d['date_approved']) ? $d['date_approved'] : null,
                    'date_signed' => !empty($d['date_signed']) ? $d['date_signed'] : null,
                    'draft_file' => $d['draft_file'] ?? null,
                    'signed_file' => $d['signed_file'] ?? null,
                    'notes' => $d['notes'] ?? null,
                ]);

                if (!empty($d['audit_trail'])) {
                    foreach ($d['audit_trail'] as $at) {
                        AuditTrail::create([
                            'document_id' => $doc->id,
                            'actor' => $at['actor'] ?? 'Sistem',
                            'role' => $at['role'] ?? 'SYSTEM',
                            'event' => $at['event'] ?? 'INFO',
                            'from_status' => $at['from_status'] ?? '-',
                            'to_status' => $at['to_status'] ?? '-',
                            'notes' => $at['notes'] ?? null,
                            'timestamp_wita' => $at['timestamp'] ?? now()->format('Y-m-d H:i:s') . ' WITA',
                        ]);
                    }
                }
            }
            return;
        }

        // Fallback default realistic data if JSON is not present
        if (Document::count() > 0) {
            return;
        }

        $partnersData = [
            [
                'name' => 'PT Telekomunikasi Indonesia (Persero) Tbk',
                'category' => 'BUMN / BUMD',
                'scope_coverage' => 'Dalam Negeri (Nasional/Lokal)',
                'address' => 'Jl. Japati No. 1, Bandung & Regional Sorong Papua Barat Daya',
                'city' => 'Kota Sorong',
                'phone' => '(0951) 321-789',
                'email' => 'kemitraan.telkom@telkom.co.id',
                'pic_name' => 'Bambang Trianto, S.T., M.M.',
                'pic_title' => 'Manager Enterprise & Government Service',
                'pic_contact' => '0812-4455-8899',
                'profile_desc' => 'Perusahaan BUMN penyedia jaringan telekomunikasi dan ekosistem digital nasional terbesar di Indonesia.',
                'status_verification' => 'Terverifikasi Sah',
            ],
            [
                'name' => 'PT Sasa Inti Minahasa Selatan',
                'category' => 'DUDI / Swasta',
                'scope_coverage' => 'Dalam Negeri (Nasional/Lokal)',
                'address' => 'Kawasan Industri Gasing & Perwakilan Sorong',
                'city' => 'Kota Sorong',
                'phone' => '(0951) 880-552',
                'email' => 'hrd.kemitraan@sasainti.co.id',
                'pic_name' => 'Hendra Setiawan, S.E.',
                'pic_title' => 'Head of Corporate HR & Academic Partnership',
                'pic_contact' => '0813-2233-4455',
                'profile_desc' => 'Perusahaan manufaktur FMCG terkemuka dengan komitmen tinggi dalam penyediaan magang MBKM dan riset otomasi industri.',
                'status_verification' => 'Terverifikasi Sah',
            ],
            [
                'name' => 'Universiti Teknologi Malaysia (UTM)',
                'category' => 'Perguruan Tinggi Luar Negeri',
                'scope_coverage' => 'Luar Negeri (Internasional)',
                'address' => 'Skudai, 81310 Johor Bahru, Johor, Malaysia',
                'city' => 'Johor Bahru',
                'phone' => '+60 7-553 3333',
                'email' => 'international@utm.my',
                'pic_name' => 'Prof. Dr. Ahmad Zulkifli bin Ismail',
                'pic_title' => 'Director of Global Education & Research Collaboration',
                'pic_contact' => '+60 12-345 6789',
                'profile_desc' => 'Perguruan tinggi riset teknik terkemuka di Asia Tenggara (Top 200 QS World University Rankings).',
                'status_verification' => 'Terverifikasi Sah',
            ],
            [
                'name' => 'National University of Singapore (NUS) - Ocean Research Centre',
                'category' => 'Perguruan Tinggi Luar Negeri',
                'scope_coverage' => 'Luar Negeri (Internasional)',
                'address' => '21 Lower Kent Ridge Rd, Singapore 119077',
                'city' => 'Singapore',
                'phone' => '+65 6516 6666',
                'email' => 'ocean.research@nus.edu.sg',
                'pic_name' => 'Dr. Eleanor Vance, Ph.D.',
                'pic_title' => 'Senior Marine Research Lead',
                'pic_contact' => '+65 9123 4567',
                'profile_desc' => 'Lembaga riset kelautan tropis terdepan di dunia yang berkolaborasi dalam konservasi ekosistem laut Papua Barat.',
                'status_verification' => 'Terverifikasi Sah',
            ],
            [
                'name' => 'PT Telekomunikasi Indonesia International (Telin)',
                'category' => 'DUDI / Swasta',
                'scope_coverage' => 'Luar Negeri (Internasional)',
                'address' => 'Telkom Landmark Tower Lt. 16, Jl. Gatot Subroto Kav. 52, Jakarta Selatan',
                'city' => 'Jakarta Selatan',
                'phone' => '(021) 521-5123',
                'email' => 'partnership@telin.net',
                'pic_name' => 'Rian Hidayat, M.Kom.',
                'pic_title' => 'Vice President Cloud Infrastructure Solutions',
                'pic_contact' => '0811-9876-5432',
                'profile_desc' => 'Anak perusahaan TelkomGroup yang mengelola kabel laut internasional dan infrastruktur komputasi awan global.',
                'status_verification' => 'Terverifikasi Sah',
            ],
            [
                'name' => 'UPTD Bapelkes Dinas Kesehatan Provinsi Sulawesi Utara',
                'category' => 'Pemerintah / Instansi Daerah',
                'scope_coverage' => 'Dalam Negeri (Nasional/Lokal)',
                'address' => 'Jl. Malalayang Satu Barat, Manado',
                'city' => 'Kota Manado',
                'phone' => '(0431) 831-234',
                'email' => 'bapelkes.sulut@kemkes.go.id',
                'pic_name' => 'Ns. Maria Magdalena, S.Kep., M.Kes.',
                'pic_title' => 'Kepala Seksi Sertifikasi & Penjaminan Mutu',
                'pic_contact' => '0812-4422-9988',
                'profile_desc' => 'Balai pelatihan dan uji kompetensi tenaga kesehatan resmi di bawah Dinas Kesehatan Provinsi.',
                'status_verification' => 'Terverifikasi Sah',
            ],
            [
                'name' => 'PT Bank Pembangunan Daerah Sulawesi Utara Gorontalo (Bank SulutGo)',
                'category' => 'BUMN / BUMD',
                'scope_coverage' => 'Dalam Negeri (Nasional/Lokal)',
                'address' => 'Jl. Sam Ratulangi No. 9, Manado & Kantor Cabang Sorong',
                'city' => 'Kota Sorong',
                'phone' => '(0431) 863-123',
                'email' => 'corsec@banksulutgo.co.id',
                'pic_name' => 'Grace Rumengan, S.E., M.M.',
                'pic_title' => 'Pemimpin Divisi Hubungan Kelembagaan',
                'pic_contact' => '0813-5566-7788',
                'profile_desc' => 'Bank devisa daerah penyedia solusi transaksi perbankan terpadu dan mitra beasiswa pendidikan tinggi.',
                'status_verification' => 'Terverifikasi Sah',
            ],
            [
                'name' => 'Dinas Komunikasi, Informatika, Persandian dan Statistik',
                'category' => 'Pemerintah / Instansi Daerah',
                'scope_coverage' => 'Dalam Negeri (Nasional/Lokal)',
                'address' => 'Kompleks Kantor Gubernur Papua Barat Daya, Kota Sorong',
                'city' => 'Kota Sorong',
                'phone' => '(0951) 325-101',
                'email' => 'diskominfo@papuabaratdayaprov.go.id',
                'pic_name' => 'Alexander Mambrasar, S.STP., M.Si.',
                'pic_title' => 'Kepala Bidang Keamanan Informasi & Sandi',
                'pic_contact' => '0821-9876-1234',
                'profile_desc' => 'Instansi pemerintah provinsi penanggung jawab transformasi digital dan Computer Security Incident Response Team (CSIRT).',
                'status_verification' => 'Terverifikasi Sah',
            ],
        ];

        foreach ($partnersData as $pData) {
            Partner::updateOrCreate(['name' => $pData['name']], $pData);
        }

        $defaultDocs = [
            [
                'doc_id' => 'UNIPAR-2026-001',
                'reg_id' => 'REG-UNIPAR-2026-001',
                'doc_number' => '012/UNIPAR/MoU/I/2026',
                'track' => 'Jalur 1: Kemitraan Bertahap',
                'doc_type' => 'MoU',
                'parent_mou' => '-',
                'unit' => 'Rektorat Universitas Kristen Papua',
                'collab_unit' => 'Fakultas Teknik & Fakultas Ilmu Komputer',
                'partner_name' => 'PT Telekomunikasi Indonesia (Persero) Tbk',
                'partner_category' => 'BUMN / BUMD',
                'scope' => 'Kerja sama strategis payung institusional dalam penyelenggaraan Tridharma Perguruan Tinggi, penguatan konektivitas kampus merdeka, penyediaan program magang bersertifikat MBKM, dan hilirisasi produk digital sivitas akademika.',
                'validity' => '5 Tahun',
                'status' => 'ACTIVE',
                'date_approved' => '2026-01-10',
                'date_signed' => '2026-01-15',
                'start_date' => '2026-01-15',
                'end_date' => '2031-01-15',
                'first_party_signer' => 'Dr. Rosdiana Simbolon, SE., S.Kom., M.Si',
                'first_party_position' => 'Rektor Universitas Kristen Papua',
                'second_party_signer' => 'Dharmawan Rusli, S.T., M.M.',
                'second_party_position' => 'Executive Vice President Telkom Regional',
                'notes' => 'Naskah MoU payung resmi disahkan di hadapan Senat Universitas Kristen Papua.',
                'trails' => [
                    ['actor' => 'Unit Rektorat', 'role' => 'UNIT', 'event' => 'SUBMIT', 'from' => 'DRAFT', 'to' => 'REVIEW', 'notes' => 'Pengajuan draf nota kesepahaman payung'],
                    ['actor' => 'Tim Legal UNIPAR', 'role' => 'LEGAL', 'event' => 'VERIFY', 'from' => 'REVIEW', 'to' => 'VERIFIED', 'notes' => 'Klausul hukum dan MBKM telah ditelaah dan memenuhi standar IKU 6'],
                    ['actor' => 'Rektor UNIPAR', 'role' => 'LEADER', 'event' => 'ACTIVATE', 'from' => 'VERIFIED', 'to' => 'ACTIVE', 'notes' => 'Penandatanganan basah dan pengesahan bermeterai sah'],
                ]
            ],
            [
                'doc_id' => 'UNIPAR-2026-058',
                'reg_id' => 'REG-UNIPAR-2026-058',
                'doc_number' => '045/UNIPAR/MoA/II/2026',
                'track' => 'Jalur 1: Kemitraan Bertahap',
                'doc_type' => 'MoA',
                'parent_mou' => '012/UNIPAR/MoU/I/2026',
                'unit' => 'Program Studi S1 Teknik Informatika',
                'collab_unit' => 'Fakultas Teknik',
                'partner_name' => 'PT Sasa Inti Minahasa Selatan',
                'partner_category' => 'DUDI / Swasta',
                'scope' => 'Penyelenggaraan Program Magang Industri Manufaktur Kelas Pekerja, Riset Terapan Rekayasa Perangkat Lunak Otomasi Finite State Machine, serta Perekrutan Langsung Lulusan Berprestasi.',
                'validity' => '1 Tahun',
                'status' => 'ACTIVE',
                'date_approved' => '2026-01-20',
                'date_signed' => '2026-01-25',
                'start_date' => '2026-01-25',
                'end_date' => '2027-01-25',
                'first_party_signer' => 'Dekan Fakultas Teknik UNIPAR',
                'first_party_position' => 'Dekan Fakultas Teknik UNIPAR',
                'second_party_signer' => 'Hendra Setiawan, S.E.',
                'second_party_position' => 'Head of Corporate HR PT Sasa Inti',
                'notes' => 'MoA turunan aktif untuk penempatan magang industri MBKM IKU 6.',
                'trails' => [
                    ['actor' => 'Prodi TI', 'role' => 'UNIT', 'event' => 'SUBMIT', 'from' => 'DRAFT', 'to' => 'REVIEW', 'notes' => 'Pengajuan naskah MoA magang industri'],
                    ['actor' => 'Biro Hukum & Kerjasama', 'role' => 'LEGAL', 'event' => 'VERIFY', 'from' => 'REVIEW', 'to' => 'VERIFIED', 'notes' => 'Persetujuan klausul kompensasi dan jam kerja mahasiswa'],
                    ['actor' => 'Rektorat UNIPAR', 'role' => 'ADMIN', 'event' => 'ACTIVATE', 'from' => 'VERIFIED', 'to' => 'ACTIVE', 'notes' => 'Dokumen ditandatangani dan aktif beroperasi'],
                ]
            ],
            [
                'doc_id' => 'UNIPAR-2026-006',
                'reg_id' => 'REG-UNIPAR-2026-006',
                'doc_number' => '028/UNIPAR/MoA/II/2026',
                'track' => 'Jalur 1: Kemitraan Bertahap',
                'doc_type' => 'MoA',
                'parent_mou' => '012/UNIPAR/MoU/I/2026',
                'unit' => 'Program Studi S1 Teknik Informatika',
                'collab_unit' => 'Laboratorium Komputer Terpadu',
                'partner_name' => 'PT Telekomunikasi Indonesia International (Telin)',
                'partner_category' => 'DUDI / Swasta',
                'scope' => 'Pelaksanaan On-Site Internship & Deployment Cloud Gateway Infrastruktur Internasional, Pengujian Rute Jaringan Lintas Negara, serta Fasilitasi Uji Sertifikasi Cloud Kompetensi Mahasiswa.',
                'validity' => '3 Tahun',
                'status' => 'ACTIVE',
                'date_approved' => '2026-02-15',
                'date_signed' => '2026-02-20',
                'start_date' => '2026-02-20',
                'end_date' => '2029-02-20',
                'first_party_signer' => 'Dr. Rosdiana Simbolon, SE., S.Kom., M.Si',
                'first_party_position' => 'Rektor Universitas Kristen Papua',
                'second_party_signer' => 'Rian Hidayat, M.Kom.',
                'second_party_position' => 'VP Cloud Solutions PT Telin',
                'notes' => 'Dukungan penuh sertifikasi kompetensi industri internasional.',
                'trails' => [
                    ['actor' => 'Prodi TI', 'role' => 'UNIT', 'event' => 'SUBMIT', 'from' => 'DRAFT', 'to' => 'REVIEW', 'notes' => 'Draf MoA sertifikasi cloud dikirim'],
                    ['actor' => 'Legal Officer', 'role' => 'LEGAL', 'event' => 'VERIFY', 'from' => 'REVIEW', 'to' => 'VERIFIED', 'notes' => 'Telaah hukum selesai'],
                    ['actor' => 'Pimpinan', 'role' => 'LEADER', 'event' => 'ACTIVATE', 'from' => 'VERIFIED', 'to' => 'ACTIVE', 'notes' => 'Naskah sah bertanda tangan'],
                ]
            ],
            [
                'doc_id' => 'UNIPAR-2026-INTL-01',
                'reg_id' => 'REG-UNIPAR-2026-INTL-01',
                'doc_number' => '002/UNIPAR/MoU/INTL/2026',
                'track' => 'Jalur 1: Kemitraan Bertahap',
                'doc_type' => 'MoU',
                'parent_mou' => '-',
                'unit' => 'Kantor Urusan Internasional (KUI)',
                'collab_unit' => 'Seluruh Fakultas di Lingkungan UNIPAR',
                'partner_name' => 'Universiti Teknologi Malaysia (UTM)',
                'partner_category' => 'Perguruan Tinggi Luar Negeri',
                'scope' => 'Academic & Scientific Collaboration, Joint International Research, International Credit Transfer Program, Visiting Professors, and Co-hosting Annual International Conferences.',
                'validity' => '5 Tahun',
                'status' => 'ACTIVE',
                'date_approved' => '2026-01-05',
                'date_signed' => '2026-01-12',
                'start_date' => '2026-01-12',
                'end_date' => '2031-01-12',
                'first_party_signer' => 'Dr. Rosdiana Simbolon, SE., S.Kom., M.Si',
                'first_party_position' => 'Rektor Universitas Kristen Papua',
                'second_party_signer' => 'Prof. Dr. Ahmad Zulkifli bin Ismail',
                'second_party_position' => 'Director of Global Affairs UTM Malaysia',
                'notes' => 'Kemitraan internasional QS Top 200 pemenuhan IKU 6 dan akreditasi internasional.',
                'trails' => [
                    ['actor' => 'KUI UNIPAR', 'role' => 'UNIT', 'event' => 'SUBMIT', 'from' => 'DRAFT', 'to' => 'REVIEW', 'notes' => 'Naskah bilingual MoU internasional diserahkan'],
                    ['actor' => 'Tim Legal', 'role' => 'LEGAL', 'event' => 'VERIFY', 'from' => 'REVIEW', 'to' => 'VERIFIED', 'notes' => 'Pemeriksaan klausul hukum internasional disetujui'],
                    ['actor' => 'Rektor UNIPAR', 'role' => 'LEADER', 'event' => 'ACTIVATE', 'from' => 'VERIFIED', 'to' => 'ACTIVE', 'notes' => 'Penandatanganan bersama secara daring dan pos diplomatik'],
                ]
            ],
            [
                'doc_id' => 'UNIPAR-2026-INTL-02',
                'reg_id' => 'REG-UNIPAR-2026-INTL-02',
                'doc_number' => '003/UNIPAR/MoA/INTL/2026',
                'track' => 'Jalur 1: Kemitraan Bertahap',
                'doc_type' => 'MoA',
                'parent_mou' => '002/UNIPAR/MoU/INTL/2026',
                'unit' => 'Fakultas Perikanan dan Ilmu Kelautan',
                'collab_unit' => 'Pusat Studi Biodiversitas Papua',
                'partner_name' => 'National University of Singapore (NUS) - Ocean Research Centre',
                'partner_category' => 'Perguruan Tinggi Luar Negeri',
                'scope' => 'Tropical Marine Ecology Joint Research Expedition in Raja Ampat waters, International Co-Author Publications in Scopus Q1 Journals, and Laboratory Fellowship for UNIPAR Faculty.',
                'validity' => '3 Tahun',
                'status' => 'ACTIVE',
                'date_approved' => '2026-02-01',
                'date_signed' => '2026-02-08',
                'start_date' => '2026-02-08',
                'end_date' => '2029-02-08',
                'first_party_signer' => 'Dekan Fakultas Perikanan & Kelautan UNIPAR',
                'first_party_position' => 'Dekan FPK UNIPAR',
                'second_party_signer' => 'Dr. Eleanor Vance, Ph.D.',
                'second_party_position' => 'Senior Research Fellow NUS Singapore',
                'notes' => 'Kolaborasi riset bereputasi global dengan pendanaan konsorsium riset kelautan.',
                'trails' => [
                    ['actor' => 'FPK UNIPAR', 'role' => 'UNIT', 'event' => 'SUBMIT', 'from' => 'DRAFT', 'to' => 'REVIEW', 'notes' => 'Pengajuan proposal MoA riset kelautan bersama'],
                    ['actor' => 'Legal & Research Board', 'role' => 'LEGAL', 'event' => 'VERIFY', 'from' => 'REVIEW', 'to' => 'VERIFIED', 'notes' => 'Kelayakan etik riset dan klausul HKI disetujui'],
                    ['actor' => 'Rektorat', 'role' => 'LEADER', 'event' => 'ACTIVATE', 'from' => 'VERIFIED', 'to' => 'ACTIVE', 'notes' => 'Perjanjian kerja sama diresmikan'],
                ]
            ],
            [
                'doc_id' => 'UNIPAR-2026-008',
                'reg_id' => 'REG-UNIPAR-2026-008',
                'doc_number' => '033/UNIPAR/IA/III/2026',
                'track' => 'Jalur 1: Kemitraan Bertahap',
                'doc_type' => 'IA',
                'parent_mou' => '012/UNIPAR/MoU/I/2026',
                'unit' => 'Program Studi S1 Ilmu Kesehatan Masyarakat',
                'collab_unit' => 'Fakultas Kesehatan',
                'partner_name' => 'UPTD Bapelkes Dinas Kesehatan Provinsi Sulawesi Utara',
                'partner_category' => 'Pemerintah / Instansi Daerah',
                'scope' => 'Pelatihan & Uji Kompetensi Penjaminan Mutu Layanan Kesehatan Kerja bagi Mahasiswa Tingkat Akhir, Sertifikasi Profesi Kemenkes RI, serta Kuliah Umum Epidemiologi Lapangan.',
                'validity' => '1 Tahun',
                'status' => 'ACTIVE',
                'date_approved' => '2026-02-05',
                'date_signed' => '2026-02-10',
                'start_date' => '2026-02-10',
                'end_date' => '2027-02-10',
                'first_party_signer' => 'Ketua Program Studi Kesmas UNIPAR',
                'first_party_position' => 'Kaprodi Kesmas UNIPAR',
                'second_party_signer' => 'Ns. Maria Magdalena, S.Kep., M.Kes.',
                'second_party_position' => 'Kepala Seksi UPTD Bapelkes',
                'notes' => 'Realisasi IA telah meluluskan 42 mahasiswa dengan sertifikat Kemenkes resmi.',
                'trails' => [
                    ['actor' => 'Prodi Kesmas', 'role' => 'UNIT', 'event' => 'SUBMIT', 'from' => 'DRAFT', 'to' => 'REVIEW', 'notes' => 'Pengajuan naskah IA pelatihan'],
                    ['actor' => 'Tim Legal', 'role' => 'LEGAL', 'event' => 'VERIFY', 'from' => 'REVIEW', 'to' => 'VERIFIED', 'notes' => 'Dokumen terverifikasi'],
                    ['actor' => 'Pimpinan Fakultas', 'role' => 'ADMIN', 'event' => 'ACTIVATE', 'from' => 'VERIFIED', 'to' => 'ACTIVE', 'notes' => 'Diaktifkan dan berjalan'],
                ]
            ],
            [
                'doc_id' => 'UNIPAR-2026-022',
                'reg_id' => 'REG-UNIPAR-2026-022',
                'doc_number' => '052/UNIPAR/IA/IV/2026',
                'track' => 'Jalur 1: Kemitraan Bertahap',
                'doc_type' => 'IA',
                'parent_mou' => '012/UNIPAR/MoU/I/2026',
                'unit' => 'Program Studi S1 Sistem Informasi',
                'collab_unit' => 'Unit Pelaksana Teknis Komputer',
                'partner_name' => 'Dinas Komunikasi, Informatika, Persandian dan Statistik',
                'partner_category' => 'Pemerintah / Instansi Daerah',
                'scope' => 'Penyelenggaraan Bootcamp Cyber Security, Pendampingan Penyusunan SOP Penanganan Insiden CSIRT Pemprov, serta Penempatan Asisten Laboratorium Mahasiswa.',
                'validity' => '1 Tahun',
                'status' => 'ACTIVE',
                'date_approved' => '2026-04-01',
                'date_signed' => '2026-04-05',
                'start_date' => '2026-04-05',
                'end_date' => '2027-04-05',
                'first_party_signer' => 'Kaprodi Sistem Informasi UNIPAR',
                'first_party_position' => 'Kaprodi SI UNIPAR',
                'second_party_signer' => 'Alexander Mambrasar, S.STP., M.Si.',
                'second_party_position' => 'Kabid Sandi Diskominfo Pemprov',
                'notes' => 'Implementasi kegiatan lapangan bernilai IKU 6 tinggi.',
                'trails' => [
                    ['actor' => 'Unit Pengusul SI', 'role' => 'UNIT', 'event' => 'SUBMIT', 'from' => 'DRAFT', 'to' => 'REVIEW', 'notes' => 'Draf IA diserahkan'],
                    ['actor' => 'Biro Kerjasama', 'role' => 'LEGAL', 'event' => 'VERIFY', 'from' => 'REVIEW', 'to' => 'VERIFIED', 'notes' => 'Telaah legalitas selesai'],
                    ['actor' => 'Rektorat', 'role' => 'LEADER', 'event' => 'ACTIVATE', 'from' => 'VERIFIED', 'to' => 'ACTIVE', 'notes' => 'Dokumen resmi aktif'],
                ]
            ],
            [
                'doc_id' => 'UNIPAR-2026-015',
                'reg_id' => 'REG-UNIPAR-2026-015',
                'doc_number' => '019/UNIPAR/MoA/II/2026',
                'track' => 'Jalur 1: Kemitraan Bertahap',
                'doc_type' => 'MoA',
                'parent_mou' => '012/UNIPAR/MoU/I/2026',
                'unit' => 'Biro Keuangan & Sistem Informasi UNIPAR',
                'collab_unit' => 'Biro Administrasi Akademik & Kemahasiswaan',
                'partner_name' => 'PT Bank Pembangunan Daerah Sulawesi Utara Gorontalo (Bank SulutGo)',
                'partner_category' => 'BUMN / BUMD',
                'scope' => 'Integrasi Layanan Host-to-Host (H2H) Pembayaran SPP Online Mahasiswa, Penyediaan Mesin EDC Kasir Kampus, Program Magang Perbankan Syariah/Konvensional, serta Beasiswa CSR Bagi Mahasiswa Kurang Mampu.',
                'validity' => '3 Tahun',
                'status' => 'REVIEW',
                'date_approved' => null,
                'date_signed' => null,
                'start_date' => '2026-03-01',
                'end_date' => '2029-03-01',
                'first_party_signer' => 'Wakil Rektor II Bidang Keuangan & SDM',
                'first_party_position' => 'Wakil Rektor II UNIPAR',
                'second_party_signer' => 'Grace Rumengan, S.E., M.M.',
                'second_party_position' => 'Pemimpin Divisi Hubungan Kelembagaan',
                'notes' => 'Sedang dalam penelaahan tim hukum kampus terkait klausul SLA sistem dan biaya transaksi.',
                'trails' => [
                    ['actor' => 'Biro Keuangan', 'role' => 'UNIT', 'event' => 'SUBMIT', 'from' => 'DRAFT', 'to' => 'REVIEW', 'notes' => 'Pengajuan naskah kerja sama H2H pembayaran perbankan'],
                ]
            ],
            [
                'doc_id' => 'UNIPAR-2026-042',
                'reg_id' => 'REG-UNIPAR-2026-042',
                'doc_number' => '088/UNIPAR/MoA/V/2026',
                'track' => 'Jalur 1: Kemitraan Bertahap',
                'doc_type' => 'MoA',
                'parent_mou' => '012/UNIPAR/MoU/I/2026',
                'unit' => 'Program Studi S1 Teknik Informatika',
                'collab_unit' => 'Unit Inovasi & Inkubator Bisnis',
                'partner_name' => 'PT Industri Inovasi Digital',
                'partner_category' => 'DUDI / Swasta',
                'scope' => 'Penyelarasan Kurikulum Berbasis Kecerdasan Buatan (AI), Pengadaan Perangkat Laboratorium Smart Computing, serta Kuliah Dosen Praktisi Industri 4.0.',
                'validity' => '2 Tahun',
                'status' => 'VERIFIED',
                'date_approved' => '2026-05-10',
                'date_signed' => null,
                'start_date' => '2026-06-01',
                'end_date' => '2028-06-01',
                'first_party_signer' => 'Dekan Fakultas Teknik UNIPAR',
                'first_party_position' => 'Dekan FT UNIPAR',
                'second_party_signer' => 'Direktur Utama PT Industri Inovasi Digital',
                'second_party_position' => 'Direktur Utama',
                'notes' => 'Telah disetujui tim legal dan pimpinan, menjadwalkan penandatanganan seremonial pekan depan.',
                'trails' => [
                    ['actor' => 'Prodi TI', 'role' => 'UNIT', 'event' => 'SUBMIT', 'from' => 'DRAFT', 'to' => 'REVIEW', 'notes' => 'Pengajuan naskah kurikulum AI'],
                    ['actor' => 'Biro Legal', 'role' => 'LEGAL', 'event' => 'VERIFY', 'from' => 'REVIEW', 'to' => 'VERIFIED', 'notes' => 'Lolos verifikasi naskah dan siap penandatanganan'],
                ]
            ],
            [
                'doc_id' => 'UNIPAR-2026-031',
                'reg_id' => 'REG-UNIPAR-2026-031',
                'doc_number' => '067/UNIPAR/IA/V/2025',
                'track' => 'Dokumen Lampau (Legacy)',
                'is_legacy' => true,
                'doc_type' => 'IA',
                'parent_mou' => '-',
                'unit' => 'Lembaga Penelitian & Pengabdian kepada Masyarakat (LPPM)',
                'collab_unit' => 'Program Studi S1 Teknik Informatika',
                'partner_name' => 'Dinas Komunikasi, Informatika, Persandian dan Statistik',
                'partner_category' => 'Pemerintah / Instansi Daerah',
                'scope' => 'Digitalisasi Pelayanan Administrasi Surat Warga Desa dan Pelatihan Literasi Komputer bagi Perangkat Desa.',
                'validity' => '1 Tahun',
                'status' => 'EXPIRED',
                'date_approved' => '2025-01-05',
                'date_signed' => '2025-01-10',
                'start_date' => '2025-01-10',
                'end_date' => '2026-01-10',
                'first_party_signer' => 'Ketua LPPM UNIPAR',
                'first_party_position' => 'Ketua LPPM UNIPAR',
                'second_party_signer' => 'Kepala Dinas Kominfo',
                'second_party_position' => 'Kepala Dinas',
                'notes' => 'Masa berlaku dokumen telah selesai penuh, luaran telah diserahterimakan 100%.',
                'trails' => [
                    ['actor' => 'LPPM', 'role' => 'UNIT', 'event' => 'SUBMIT', 'from' => 'DRAFT', 'to' => 'REVIEW', 'notes' => 'Draf pengabdian diserahkan'],
                    ['actor' => 'Biro Kerjasama', 'role' => 'LEGAL', 'event' => 'VERIFY', 'from' => 'REVIEW', 'to' => 'VERIFIED', 'notes' => 'Disetujui'],
                    ['actor' => 'Pimpinan', 'role' => 'LEADER', 'event' => 'ACTIVATE', 'from' => 'VERIFIED', 'to' => 'ACTIVE', 'notes' => 'Naskah sah aktif'],
                    ['actor' => 'Sistem Kronos', 'role' => 'SYSTEM', 'event' => 'EXPIRE', 'from' => 'ACTIVE', 'to' => 'EXPIRED', 'notes' => 'Kedaluwarsa alami setelah masa berlaku 1 tahun selesai'],
                ]
            ],
        ];

        foreach ($defaultDocs as $item) {
            $trails = $item['trails'] ?? [];
            unset($item['trails']);

            $doc = Document::create($item);

            foreach ($trails as $t) {
                AuditTrail::create([
                    'document_id' => $doc->id,
                    'actor' => $t['actor'],
                    'role' => $t['role'],
                    'event' => $t['event'],
                    'from_status' => $t['from'],
                    'to_status' => $t['to'],
                    'notes' => $t['notes'],
                    'timestamp_wita' => now()->format('Y-m-d H:i:s') . ' WITA',
                ]);
            }
        }

        // Jalankan ActivitySeeder untuk mengisi data kegiatan realisasi IKU 6
        $this->call(ActivitySeeder::class);
    }
}
