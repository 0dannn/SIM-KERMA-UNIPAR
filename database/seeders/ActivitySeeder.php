<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Activity;
use App\Models\Document;

class ActivitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Bersihkan tabel activities sebelum seeding
        Activity::truncate();

        $activitiesData = [
            [
                'doc_id_match' => 'UNIPAR-2026-058',
                'activity_name' => 'Magang Industri Manufaktur & Riset Otomasi Sistem FSM',
                'schedule_start' => '01 Feb 2026',
                'schedule_end' => '31 Jul 2026',
                'target_qty' => '2 Mahasiswa',
                'realized_qty' => '2 Mahasiswa (100%)',
                'parties_involved' => 'Jordan Immanuel Pusung & Rekan Mahasiswa TI, Pembimbing Lapangan PT Sasa Inti',
                'outputs' => 'Laporan Magang Industri, Naskah Skripsi, Prototype Sistem FSM SIMKERMA',
                'budget_info' => 'Uang Saku Mitra: Rp 45.000.000',
                'evidence_file' => 'laporan_magang_sasa_inti_2026.pdf',
                'evidence_photos' => 'dokumentasi_pabrik_sasa.jpg',
                'execution_status' => 'SEDANG_BERJALAN',
                'notes' => 'Pelaksanaan program magang kelas pekerja berjalan sangat memuaskan, realisasi IKU 6 terverifikasi penuh.'
            ],
            [
                'doc_id_match' => 'UNIPAR-2026-006',
                'activity_name' => 'On-Site Internship & Deployment Cloud Gateway Infrastruktur Internasional',
                'schedule_start' => '01 Mar 2026',
                'schedule_end' => '31 Agu 2026',
                'target_qty' => '5 Mahasiswa',
                'realized_qty' => '5 Mahasiswa (100%)',
                'parties_involved' => '5 Mahasiswa Fakultas Ilmu Komputer UNIPAR & Senior Network Engineers PT Telin',
                'outputs' => 'Laporan Kinerja Jaringan, 5 Sertifikasi Kompetensi Jaringan Cloud',
                'budget_info' => 'Kontribusi Mitra: Rp 30.000.000',
                'evidence_file' => 'laporan_kegiatan_telin.pdf',
                'evidence_photos' => 'foto_lab_telin.jpg',
                'execution_status' => 'SEDANG_BERJALAN',
                'notes' => 'Mahasiswa terlibat langsung dalam operasional monitoring routing lintas negara.'
            ],
            [
                'doc_id_match' => 'UNIPAR-2026-008',
                'activity_name' => 'Pelatihan & Uji Kompetensi Penjaminan Mutu Layanan Kesehatan Kerja',
                'schedule_start' => '10 Feb 2026',
                'schedule_end' => '15 Mei 2026',
                'target_qty' => '40 Peserta',
                'realized_qty' => '42 Peserta (105%)',
                'parties_involved' => 'Instruktur UPTD Bapelkes Sulut, Dosen Pembina, & Mahasiswa Lintas Prodi',
                'outputs' => '42 Sertifikat Kompetensi Kemenkes RI, Modul Pelatihan Praktik',
                'budget_info' => 'DIPA Bapelkes Sulut: Rp 25.000.000',
                'evidence_file' => 'laporan_akhir_bapelkes.pdf',
                'evidence_photos' => 'dokumentasi_bapelkes.jpg',
                'execution_status' => 'SELESAI',
                'notes' => 'Kegiatan telah selesai 100%, seluruh sertifikat kelulusan telah diserahkan.'
            ],
            [
                'doc_id_match' => 'UNIPAR-2026-042',
                'activity_name' => 'Penyelarasan Kurikulum AI & Pengembangan Laboratorium Industri Cerdas',
                'schedule_start' => '15 Jan 2026',
                'schedule_end' => '15 Des 2026',
                'target_qty' => '1 Kurikulum & 20 Peserta',
                'realized_qty' => '1 Draf Kurikulum & 16 Peserta',
                'parties_involved' => 'Dosen Prodi TI UNIPAR & Tim AI Expert PT Industri Inovasi Digital',
                'outputs' => 'Buku Pedoman Kurikulum MBKM AI, Modul Praktikum Machine Learning',
                'budget_info' => 'Fasilitas Lab & Hibah Mitra: Rp 35.000.000',
                'evidence_file' => 'pedoman_kurikulum_ai_unipar.pdf',
                'evidence_photos' => 'workshop_kurikulum.jpg',
                'execution_status' => 'SEDANG_BERJALAN',
                'notes' => 'Menunggu pengesahan akhir Senat Akademik dan penandatanganan basah pimpinan.'
            ],
            [
                'doc_id_match' => 'UNIPAR-2026-022',
                'activity_name' => 'Bootcamp Cyber Security & Pendampingan Keamanan Siber Daerah',
                'schedule_start' => '05 Apr 2026',
                'schedule_end' => '05 Jun 2026',
                'target_qty' => '30 Peserta',
                'realized_qty' => '28 Peserta (93%)',
                'parties_involved' => 'Tim Diskominfo Sulut, Dosen Jaringan & Keamanan Data, Mahasiswa TI',
                'outputs' => 'SOP Penanganan Insiden Keamanan Informasi Daerah, Sertifikat Peserta',
                'budget_info' => 'Kolaborasi APBD & Kampus: Rp 15.000.000',
                'evidence_file' => 'laporan_bootcamp_csirt.pdf',
                'evidence_photos' => 'bootcamp_diskominfo.jpg',
                'execution_status' => 'SELESAI',
                'notes' => 'Seluruh target luaran tercapai dengan evaluasi predikat memuaskan.'
            ],
            [
                'doc_id_match' => 'UNIPAR-2026-031',
                'activity_name' => 'Program PKM Mandiri: Digitalisasi Pelayanan Surat & Administrasi Warga',
                'schedule_start' => '01 Jan 2025',
                'schedule_end' => '31 Des 2025',
                'target_qty' => '1 Sistem Web Desa & 15 Staf',
                'realized_qty' => '1 Sistem Web Desa & 15 Staf (100%)',
                'parties_involved' => 'Mahasiswa Mandiri TI UNIPAR & Seluruh Perangkat Desa Wori Minut',
                'outputs' => 'Sistem Informasi Pelayanan Surat Desa, Berita Acara Serah Terima, Piagam',
                'budget_info' => 'Swadaya PKM & Dana Desa: Rp 12.000.000',
                'evidence_file' => 'laporan_pengabdian_desa_wori.pdf',
                'evidence_photos' => 'serah_terima_web_desa.jpg',
                'execution_status' => 'SELESAI',
                'notes' => 'Masa berlaku kerja sama 1 tahun telah selesai, sistem terus digunakan secara mandiri oleh desa.'
            ],
            [
                'doc_id_match' => 'UNIPAR-2026-015',
                'activity_name' => 'Integrasi Gateway Pembayaran Host-to-Host (H2H) Biaya Kuliah Online',
                'schedule_start' => '01 Nov 2026',
                'schedule_end' => '31 Des 2026',
                'target_qty' => '1 Payment Gateway & 1.200 Mhs',
                'realized_qty' => 'Tahap Sandbox API',
                'parties_involved' => 'Biro Keuangan UNIPAR & Tim Pengembangan TI Bank SulutGo',
                'outputs' => 'Modul Integrasi API H2H, Standar Operasional Prosedur Pembayaran',
                'budget_info' => 'Investasi Infrastruktur IT oleh Bank',
                'evidence_file' => 'prospektus_h2h_bank_sulutgo.pdf',
                'evidence_photos' => 'meeting_api_h2h.jpg',
                'execution_status' => 'BELUM_DIMULAI',
                'notes' => 'Menunggu penelaahan legalitas kontrak MoA dari Bagian Hukum UNIPAR.'
            ]
        ];

        foreach ($activitiesData as $data) {
            $doc = Document::where('doc_id', $data['doc_id_match'])
                ->orWhere('id', $data['doc_id_match'])
                ->first();

            if ($doc) {
                Activity::create([
                    'document_id' => $doc->id,
                    'activity_name' => $data['activity_name'],
                    'schedule_start' => $data['schedule_start'],
                    'schedule_end' => $data['schedule_end'],
                    'target_qty' => $data['target_qty'],
                    'realized_qty' => $data['realized_qty'],
                    'parties_involved' => $data['parties_involved'],
                    'outputs' => $data['outputs'],
                    'budget_info' => $data['budget_info'],
                    'evidence_file' => $data['evidence_file'],
                    'evidence_photos' => $data['evidence_photos'],
                    'execution_status' => $data['execution_status'],
                    'notes' => $data['notes'],
                ]);
            }
        }
    }
}
