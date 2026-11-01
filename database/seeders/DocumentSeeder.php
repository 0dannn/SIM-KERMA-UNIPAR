<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Document;
use App\Models\AuditTrail;
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
                    'unit' => $d['unit'] ?? 'Universitas Parna Raya',
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
        }
    }
}
