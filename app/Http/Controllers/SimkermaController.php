<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Document;
use App\Models\DocumentAttachment;
use App\Models\AuditTrail;
use App\Models\Activity;
use App\Models\Partner;
use App\Models\AppNotification;
use App\Services\FSMWorkflowEngineService;
use App\Services\DocxQrStampService;
use App\Exceptions\IllegalTransitionException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SimkermaController extends Controller
{
    protected FSMWorkflowEngineService $fsmService;
    protected DocxQrStampService $docxStampService;

    public function __construct(FSMWorkflowEngineService $fsmService, DocxQrStampService $docxStampService)
    {
        $this->fsmService = $fsmService;
        $this->docxStampService = $docxStampService;
    }

    /**
     * Halaman Utama SIMKERMA (Mendukung Menu 1 s.d. 7)
     */
    public function index(Request $request)
    {
        $activeMenu = (int)$request->query('menu', 4);
        if ($activeMenu < 1 || $activeMenu > 7) {
            $activeMenu = 4;
        }

        // Ambil peran aktif dari auth atau session (default: ADMIN / MASTER)
        if (auth()->check() && !session()->has('active_role')) {
            session(['active_role' => auth()->user()->role]);
        }
        $activeRole = session('active_role', auth()->check() ? auth()->user()->role : 'ADMIN');

        // Data untuk Menu 4 (Pengajuan dan Persetujuan Kerja Sama)
        $documents = Document::with(['auditTrails', 'activities', 'attachments'])->orderBy('id', 'desc')->get();
        
        $selectedDocId = $request->query('doc');
        if (!$selectedDocId) {
            $selectedDocId = session('selected_doc_id');
        }

        $currentDoc = null;
        if ($selectedDocId) {
            $currentDoc = $documents->firstWhere('doc_id', $selectedDocId) ?: $documents->firstWhere('id', $selectedDocId);
        }
        if (!$currentDoc && $documents->isNotEmpty()) {
            $currentDoc = $documents->first();
            $selectedDocId = $currentDoc->doc_id;
        }

        if ($currentDoc) {
            session(['selected_doc_id' => $currentDoc->doc_id]);
        }

        // Mode Tampilan: List Dokumen, Formulir Pengajuan MoU, Formulir Turunan, atau Detail Dokumen
        $viewMode = $request->query('view', 'list');
        $isCreatingNew = ($viewMode === 'new' || $viewMode === 'new_mou');
        $isCreatingDerive = ($viewMode === 'new_derive');
        $isCreatingLegacy = ($viewMode === 'legacy' || $viewMode === 'new_legacy');
        $isViewingDetail = ($viewMode === 'detail' && $currentDoc !== null);

        // Pre-fill Turunan Dokumen (Hierarki IKU 6)
        $prefillParentId = $request->query('parent_id', '');
        $prefillDeriveType = $request->query('derive_type', 'MoA / PKS');
        $parentDocForDerive = null;
        $prefillPartnerName = '';
        $prefillCategory = 'DUDI / Swasta';
        $prefillParentDocNumber = '-';

        if (!empty($prefillParentId)) {
            $parentDocForDerive = Document::where('doc_id', $prefillParentId)
                ->orWhere('doc_number', $prefillParentId)
                ->first();
            if ($parentDocForDerive) {
                $prefillPartnerName = $parentDocForDerive->partner_name;
                $prefillCategory = $parentDocForDerive->partner_category;
                $prefillParentDocNumber = $parentDocForDerive->doc_number ?: $parentDocForDerive->doc_id;
            }
        }

        // Autocomplete list untuk Dokumen Induk (MoU / MoA / Naskah Payung Sah)
        // Hanya menyertakan dokumen yang berstatus sah (ACTIVE / VERIFIED)
        $registeredMous = Document::where(function ($q) {
                $q->where('doc_type', 'like', '%MoU%')
                  ->orWhere('doc_type', 'like', '%MoA%')
                  ->orWhere('doc_number', 'like', '%MoU%')
                  ->orWhere('doc_number', 'like', '%MoA%');
            })
            ->whereIn('status', ['ACTIVE', 'VERIFIED'])
            ->orderBy('id', 'desc')
            ->get();

        // Relasi Parent & Child untuk Dokumen yang Sedang Diperiksa
        $parentDoc = null;
        $childDocs = collect();
        if ($currentDoc) {
            if (!empty($currentDoc->parent_mou) && $currentDoc->parent_mou !== '-') {
                $parentDoc = Document::where('doc_number', $currentDoc->parent_mou)
                    ->orWhere('doc_id', $currentDoc->parent_mou)
                    ->first();
            }

            $curNum = trim((string)$currentDoc->doc_number);
            $curId = trim((string)$currentDoc->doc_id);
            $childDocs = Document::where('id', '!=', $currentDoc->id)
                ->where(function ($q) use ($curNum, $curId) {
                    $q->where('parent_mou', $curId);
                    if (!empty($curNum) && $curNum !== '-') {
                        $q->orWhere('parent_mou', $curNum);
                    }
                })->get();
        }

        // Matriks FSM untuk dokumen saat ini & seluruh status
        $availEvents = $currentDoc ? $this->fsmService->getAvailableEvents($currentDoc->status) : [];
        $fullFsmMatrix = $this->fsmService->getFullMatrix();

        // Data Kegiatan Pemantauan Pelaksanaan Kerja Sama (Menu 5 - Sinkron dengan Menu 4)
        $activities = Activity::with('document')->orderBy('id', 'desc')->get();

        // Master Data Mitra (Menu 2 - Sinkron dengan seluruh menu)
        $partners = Partner::with('documents')->orderBy('name', 'asc')->get();

        $features = self::getFeatures();
        $current = $features[$activeMenu];

        return view('simkerma.index', compact(
            'features',
            'current',
            'activeMenu',
            'activeRole',
            'documents',
            'activities',
            'partners',
            'currentDoc',
            'selectedDocId',
            'viewMode',
            'isCreatingNew',
            'isCreatingDerive',
            'isCreatingLegacy',
            'isViewingDetail',
            'prefillParentId',
            'prefillDeriveType',
            'parentDocForDerive',
            'prefillPartnerName',
            'prefillCategory',
            'prefillParentDocNumber',
            'registeredMous',
            'parentDoc',
            'childDocs',
            'availEvents',
            'fullFsmMatrix'
        ));
    }

    /**
     * Definisi 7 Fitur Utama Sistem Informasi Kerja Sama Sesuai Dokumen Resmi Institusi
     */
    public static function getFeatures(): array
    {
        return [
            1 => [
                'id'          => 1,
                'title'       => 'Dashboard Kerja Sama',
                'subtitle'    => 'Menampilkan ringkasan kondisi kerja sama perguruan tinggi secara cepat.',
                'tag'         => 'WF-01',
                'icon'        => 'bi-speedometer2',
                'subfeatures' => [
                    'Jumlah kerja sama aktif, berakhir, dan dalam proses.',
                    'Jumlah mitra berdasarkan kategori.',
                    'Kerja sama berdasarkan fakultas, UPPS, program studi, dan unit pengusul.',
                    'Kerja sama yang segera berakhir.',
                    'Realisasi kegiatan, luaran, dan capaian kerja sama.'
                ]
            ],
            2 => [
                'id'          => 2,
                'title'       => 'Pengelolaan Data Mitra',
                'subtitle'    => 'Menyimpan identitas dan riwayat mitra kerja sama secara terpusat.',
                'tag'         => 'WF-02',
                'icon'        => 'bi-building',
                'subfeatures' => [
                    'Nama dan profil mitra.',
                    'Jenis mitra: perguruan tinggi, pemerintah, industri, rumah sakit, sekolah, lembaga penelitian, dan organisasi lainnya.',
                    'Alamat, kontak, serta penanggung jawab mitra.',
                    'Lingkup kerja sama.',
                    'Riwayat kerja sama sebelumnya.',
                    'Status verifikasi dan kelengkapan data mitra.'
                ]
            ],
            3 => [
                'id'          => 3,
                'title'       => 'Pengelolaan Dokumen Kerja Sama',
                'subtitle'    => 'Menjadi repositori dokumen resmi yang terorganisasi dan mudah ditelusuri.',
                'tag'         => 'WF-03',
                'icon'        => 'bi-file-earmark-text',
                'subfeatures' => [
                    'MoU atau nota kesepahaman.',
                    'PKS atau perjanjian kerja sama.',
                    'IA (Implementation Agreement) atau dokumen implementasi, sesuai nomenklatur kampus.',
                    'Proposal dan rencana kegiatan.',
                    'Dokumen pendukung serta laporan pelaksanaan.',
                    'Nomor dokumen, tanggal penandatanganan, masa berlaku, dan pihak penandatangan.',
                    'Unggah, unduh, pencarian, serta riwayat perubahan dokumen.'
                ]
            ],
            4 => [
                'id'          => 4,
                'title'       => 'Pengajuan dan Persetujuan Kerja Sama',
                'subtitle'    => 'Mengelola alur pengajuan dari unit pengusul hingga persetujuan pejabat yang berwenang.',
                'tag'         => 'WF-04',
                'icon'        => 'bi-check2-circle',
                'subfeatures' => [
                    'Pengajuan kerja sama oleh fakultas, prodi, atau unit.',
                    'Pemeriksaan kelengkapan administrasi.',
                    'Penelaahan substansi dan manfaat kerja sama.',
                    'Persetujuan atau penolakan dengan alasan yang tercatat.',
                    'Status pengajuan dan notifikasi kepada pengusul.',
                    'Riwayat persetujuan dan catatan revisi.'
                ]
            ],
            5 => [
                'id'          => 5,
                'title'       => 'Pemantauan Pelaksanaan Kerja Sama',
                'subtitle'    => 'Bidang Kerja Sama dapat mengetahui apakah perjanjian benar-benar dilaksanakan.',
                'tag'         => 'WF-05',
                'icon'        => 'bi-calendar-check',
                'subfeatures' => [
                    'Daftar kegiatan berdasarkan dokumen kerja sama.',
                    'Unit penanggung jawab dan jadwal kegiatan.',
                    'Target dan realisasi kegiatan.',
                    'Peserta atau pihak yang terlibat.',
                    'Luaran dan manfaat yang dihasilkan.',
                    'Anggaran atau kontribusi mitra jika relevan.',
                    'Unggah bukti kegiatan, seperti laporan, foto, sertifikat, atau publikasi.',
                    'Status kegiatan: belum dimulai, berjalan, selesai, atau tertunda.'
                ]
            ],
            6 => [
                'id'          => 6,
                'title'       => 'Monitoring Masa Berlaku dan Evaluasi',
                'subtitle'    => 'Membantu Biro Kerja Sama mengidentifikasi perjanjian yang perlu ditindaklanjuti.',
                'tag'         => 'WF-06',
                'icon'        => 'bi-clock-history',
                'subfeatures' => [
                    'Pengingat sebelum masa berlaku berakhir.',
                    'Daftar kerja sama tanpa realisasi kegiatan.',
                    'Evaluasi capaian terhadap target.',
                    'Catatan kendala dan rencana tindak lanjut.',
                    'Rekomendasi perpanjangan, revisi, atau penghentian.',
                    'Riwayat evaluasi setiap kerja sama.'
                ]
            ],
            7 => [
                'id'          => 7,
                'title'       => 'Laporan dan Analitik',
                'subtitle'    => 'Menghasilkan informasi yang dapat digunakan pimpinan untuk mengambil keputusan dan mendukung pelaporan institusi.',
                'tag'         => 'WF-07',
                'icon'        => 'bi-graph-up',
                'subfeatures' => [
                    'Rekapitulasi kerja sama aktif dan tidak aktif.',
                    'Laporan berdasarkan tahun, mitra, unit, dan jenis kerja sama.',
                    'Laporan rencana dan realisasi kegiatan.',
                    'Laporan luaran kerja sama.',
                    'Ekspor laporan ke Excel atau PDF.',
                    'Rekapitulasi data.'
                ]
            ]
        ];
    }

    /**
     * Ganti Peran Aktor Aktif (UNIT, LEGAL, LEADER, SYSTEM, MASTER)
     */
    public function changeRole(Request $request)
    {
        $allowed = ['UNIT', 'LEGAL', 'LEADER', 'SYSTEM', 'MASTER', 'ADMIN'];
        $role = $request->input('role', 'ADMIN');
        if (in_array($role, $allowed, true)) {
            session(['active_role' => $role]);
        }

        return redirect()->route('simkerma.index', ['menu' => 4])
            ->with('flash', [
                'type' => 'info',
                'msg' => 'Peran aktif dialihkan ke: ' . session('active_role')
            ]);
    }

    /**
     * Pilih Dokumen yang Sedang Ditinjau
     */
    public function selectDocument(Request $request)
    {
        $docId = $request->input('doc_id');
        session(['selected_doc_id' => $docId]);
        return redirect()->route('simkerma.index', ['menu' => 4, 'doc' => $docId]);
    }

    /**
     * Generate ID Dokumen dan Registrasi Unik Bebas Tabrakan (Collision-Proof)
     */
    private function generateUniqueDocId(): array
    {
        $existingDocIds = Document::pluck('doc_id');
        $maxNum = 0;
        foreach ($existingDocIds as $idStr) {
            if (preg_match('/UNIPAR-2026-(\d+)/', $idStr, $m)) {
                $num = (int)$m[1];
                if ($num > $maxNum) {
                    $maxNum = $num;
                }
            }
        }

        $nextNum = max($maxNum + 1, Document::count() + 1);
        do {
            $newId = sprintf('UNIPAR-2026-%03d', $nextNum);
            $regId = 'REG-' . $newId;
            $nextNum++;
        } while (Document::where('doc_id', $newId)->orWhere('reg_id', $regId)->exists());

        return [$newId, $regId];
    }

    /**
     * Simpan Pengajuan Baru Jalur 1 (Kemitraan Bertahap)
     */
    public function storeJalur1(Request $request)
    {
        $request->validate([
            'partner_name' => 'required|string|max:255',
            'doc_type' => 'required|string|max:50',
            'draft_file' => 'nullable|file|mimes:pdf,docx|max:15360',
        ], [
            'partner_name.required' => 'Nama mitra kerja sama wajib diisi.',
            'draft_file.mimes' => 'Format berkas draf harus berupa PDF (.pdf) atau Word (.docx).',
            'draft_file.max' => 'Ukuran berkas draf maksimal 15 MB.',
        ]);

        $docType = $request->input('doc_type', 'MoU');
        $rawParentMou = trim((string)$request->input('parent_mou', ''));

        // Validasi: Jika jenis dokumen adalah MoA / PKS atau IA, dokumen induk wajib dipilih
        if ($docType !== 'MoU' && (empty($rawParentMou) || $rawParentMou === '-')) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Untuk pengajuan dokumen ' . $docType . ', wajib memilih Dokumen Induk (MoU / MoA)!');
        }

        [$newId, $regId] = $this->generateUniqueDocId();
        $typePrefix = ($docType === 'MoU') ? 'MoU' : (($docType === 'IA') ? 'IA' : 'MoA');
        $partnerName = $request->input('partner_name', 'Mitra Baru');
        $inputDocNumber = trim((string)$request->input('doc_number', ''));
        $inputPartnerDocNumber = trim((string)$request->input('partner_doc_number', ''));

        // Jika nomor pihak tidak diisi, gunakan tanda "-" (jangan mengisi nomor naskah acak/asal-asalan)
        $docNumber = ($inputDocNumber !== '') ? $inputDocNumber : '-';
        $partnerDocNumber = ($inputPartnerDocNumber !== '') ? $inputPartnerDocNumber : '-';

        $parentMou = ($docType === 'MoU' || empty($rawParentMou)) ? '-' : $rawParentMou;
        $activeRole = session('active_role', 'MASTER');

        $title = $request->input('title', 'Kerja Sama ' . $docType . ' dengan ' . $partnerName);

        $firstPartySigner = $request->input('first_party_signer');
        $firstPartyPosition = $request->input('first_party_position');
        $secondPartySigner = $request->input('second_party_signer');
        $secondPartyPosition = $request->input('second_party_position');
        $picFirstParty = $request->input('pic_first_party');
        $picSecondParty = $request->input('pic_second_party');
        $startDate = $request->input('start_date') ?: null;
        $endDate = $request->input('end_date') ?: null;
        $nominalValue = $request->input('nominal_value');
        $description = $request->input('description');

        // Normalisasi Masa Berlaku (Tahun) & Tanggal Berakhir Otomatis
        $validityYears = $request->input('validity_years');
        $validity = $request->input('validity');
        if (!empty($validityYears)) {
            $validity = ((int)$validityYears) . ' Tahun';
            if ($startDate && empty($endDate)) {
                try {
                    $endDate = \Carbon\Carbon::parse($startDate)->addYears((int)$validityYears)->format('Y-m-d');
                } catch (\Exception $e) {}
            }
        } elseif (empty($validity)) {
            if ($startDate && $endDate) {
                try {
                    $start = \Carbon\Carbon::parse($startDate);
                    $end = \Carbon\Carbon::parse($endDate);
                    $years = $start->diffInYears($end);
                    if ($years >= 1) {
                        $validity = $years . ' Tahun';
                    } else {
                        $months = $start->diffInMonths($end);
                        $validity = ($months > 0 ? $months : 1) . ' Bulan';
                    }
                } catch (\Exception $e) {
                    $validity = null;
                }
            } else {
                $validity = null;
            }
        }

        // Ruang Lingkup Tridharma / Kemitraan (Multi-select Checkbox)
        $rawScope = $request->input('scope');
        if (is_array($rawScope) && count($rawScope) > 0) {
            $scope = implode(', ', $rawScope);
        } elseif (is_string($rawScope) && trim($rawScope) !== '') {
            $scope = trim($rawScope);
        } else {
            $scope = 'Pendidikan, Penelitian, Pengabdian kepada Masyarakat, Pengembangan SDM';
        }

        // Default Ukuran Kertas Dokumen (A4) dan Ambang Batas Peringatan Dini (H-90)
        $paperSize = $request->input('paper_size', 'A4') ?: 'A4';
        $warningThreshold = 'H-90 Hari (3 Bulan)';

        $doc = DB::transaction(function () use (
            $request, $newId, $regId, $typePrefix, $docType, $partnerName,
            $docNumber, $partnerDocNumber, $parentMou, $activeRole, $title,
            $firstPartySigner, $firstPartyPosition, $secondPartySigner, $secondPartyPosition, $picFirstParty,
            $picSecondParty, $startDate, $endDate, $nominalValue,
            $description, $validity, $scope, $paperSize, $warningThreshold
        ) {
            $document = Document::create([
                'doc_id' => $newId,
                'reg_id' => $regId,
                'doc_number' => $docNumber,
                'partner_doc_number' => $partnerDocNumber,
                'track' => 'Jalur 1: Kemitraan Bertahap',
                'title' => $title,
                'unit' => $request->input('unit') ?: 'Program Studi Teknik Informatika',
                'first_party_signer' => $firstPartySigner,
                'first_party_position' => $firstPartyPosition,
                'collab_unit' => null,
                'partner_name' => $partnerName,
                'partner_category' => $request->input('category') ?: 'DUDI / Swasta',
                'second_party_signer' => $secondPartySigner,
                'second_party_position' => $secondPartyPosition,
                'pic_first_party' => $picFirstParty,
                'pic_second_party' => $picSecondParty,
                'doc_type' => $docType,
                'paper_size' => $paperSize,
                'parent_mou' => $parentMou ?: '-',
                'scope' => $scope,
                'description' => $description,
                'validity' => $validity,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'nominal_value' => $nominalValue,
                'warning_threshold' => $warningThreshold,
                'status' => 'DRAFT',
                'draft_file' => 'draf_' . strtolower(str_replace(' ', '_', $newId)) . '.pdf',
                'notes' => 'Penyusunan naskah draf awal oleh unit pengusul.',
            ]);

            // Handle Berkas Upload Draf Pengusul (PDF / DOCX)
            if ($request->hasFile('draft_file')) {
                $uploadedDraft = $request->file('draft_file');
                $ext = strtolower($uploadedDraft->getClientOriginalExtension());
                $dest = public_path('uploads/documents');
                if (!file_exists($dest)) {
                    mkdir($dest, 0777, true);
                }
                $filename = 'draf_' . strtolower(str_replace([' ', '/', '-'], '_', $newId)) . '_' . time() . '.' . $ext;
                $uploadedDraft->move($dest, $filename);
                if ($ext === 'docx') {
                    $document->docx_path = 'uploads/documents/' . $filename;
                } else {
                    $document->draft_file = 'uploads/documents/' . $filename;
                }
                $document->save();
            }

            // Handle Lampiran Opsional (Proposal, Rencana Kegiatan, & Dokumen Pendukung)
            if ($request->hasFile('proposal_file')) {
                $this->saveDocumentAttachment($document, $request->file('proposal_file'), 'PROPOSAL', 'Proposal Pengajuan Kerja Sama', $activeRole);
            }
            if ($request->hasFile('rencana_file')) {
                $this->saveDocumentAttachment($document, $request->file('rencana_file'), 'RENCANA_KEGIATAN', 'Rencana Kegiatan / TOR', $activeRole);
            }
            if ($request->hasFile('supporting_files')) {
                foreach ((array) $request->file('supporting_files') as $supFile) {
                    if ($supFile && $supFile->isValid()) {
                        $this->saveDocumentAttachment($document, $supFile, 'DOKUMEN_PENDUKUNG', null, $activeRole);
                    }
                }
            }

            AuditTrail::create([
                'document_id' => $document->id,
                'actor' => 'Unit Pengusul (' . $document->unit . ')',
                'role' => $activeRole,
                'event' => 'CREATE_DRAFT',
                'from_status' => '-',
                'to_status' => 'DRAFT',
                'notes' => 'Penyusunan naskah draf baru ' . $docType . ' bersama ' . $partnerName . ($parentMou !== '-' ? ' (Menginduk ke: ' . $parentMou . ')' : '') . '. Berkas draf siap diajukan untuk penelaahan legal.',
                'timestamp_wita' => now()->format('Y-m-d H:i:s') . ' WITA',
            ]);

            // Sinkronisasi otomatis ke Master Data Mitra (Menu 2)
            Partner::syncFromDocument(
                $partnerName,
                $request->input('category', 'DUDI / Swasta'),
                $picSecondParty,
                $scope
            );

            return $document;
        });

        session(['selected_doc_id' => $doc->doc_id]);

        $this->createNotification(
            $doc,
            'Pengajuan Dokumen Baru',
            "Draf {$doc->doc_type} baru ({$doc->doc_id}) bersama mitra {$doc->partner_name} berhasil dibuat oleh {$doc->unit}.",
            'info',
            'bi-file-earmark-plus'
        );

        return redirect()->route('simkerma.index', ['menu' => 4, 'view' => 'detail', 'doc' => $doc->doc_id])
            ->with('flash', [
                'type' => 'success',
                'msg' => 'Dokumen ' . $docType . ' baru ' . $doc->doc_id . ' (' . $doc->doc_number . ') berhasil dibuat dengan status DRAFT.'
            ]);
    }

    /**
     * Simpan Pengajuan Baru Dokumen Lampau (Legacy / Jalur Terpadu Historis)
     */
    public function storeJalur2(Request $request)
    {
        $request->validate([
            'partner_name' => 'required|string|max:255',
            'signed_file' => 'nullable|file|mimes:pdf|max:15360',
            'draft_file' => 'nullable|file|mimes:pdf,docx|max:15360',
        ], [
            'partner_name.required' => 'Nama mitra kerja sama lampau wajib diisi.',
            'signed_file.mimes' => 'Format berkas scan naskah asli harus berupa PDF (.pdf).',
            'signed_file.max' => 'Ukuran berkas scan naskah asli maksimal 15 MB.',
            'draft_file.mimes' => 'Format berkas draf harus berupa PDF (.pdf) atau Word (.docx).',
            'draft_file.max' => 'Ukuran berkas draf maksimal 15 MB.',
        ]);

        [$newId, $regId] = $this->generateUniqueDocId();
        $partnerName = $request->input('partner_name', 'Mitra Kerja Sama Lampau');
        $rawPartnerDocNumber = trim((string)$request->input('partner_doc_number', ''));
        $partnerDocNumber = ($rawPartnerDocNumber !== '') ? $rawPartnerDocNumber : '-';
        $unitMain = $request->input('unit_main', 'Fakultas Ilmu Komputer');
        $unitCollab = $request->input('unit_collab', 'Fakultas Ekonomi dan Bisnis');
        $topic = $request->input('topic', 'Pelaksanaan Program Kerja Sama');
        $duration = $request->input('duration', '1 Tahun');
        $activeRole = session('active_role', 'MASTER');

        $title = $request->input('title', 'Dokumen Lampau: ' . $topic);
        $firstPartySigner = $request->input('first_party_signer');
        $firstPartyPosition = $request->input('first_party_position');
        $secondPartySigner = $request->input('second_party_signer');
        $secondPartyPosition = $request->input('second_party_position');
        $picFirstParty = $request->input('pic_first_party');
        $picSecondParty = $request->input('pic_second_party');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $nominalValue = $request->input('nominal_value');
        $description = $request->input('description');

        $doc = DB::transaction(function () use (
            $request, $newId, $regId, $partnerName, $partnerDocNumber,
            $unitMain, $unitCollab, $topic, $duration, $activeRole,
            $title, $firstPartySigner, $firstPartyPosition, $secondPartySigner, $secondPartyPosition, $picFirstParty,
            $picSecondParty, $startDate, $endDate, $nominalValue, $description
        ) {
            do {
                $docNumber = sprintf('%03d/UNIPAR/IA/X/2026', rand(360, 499));
            } while (Document::where('doc_number', $docNumber)->exists());

            $document = Document::create([
                'doc_id' => $newId,
                'reg_id' => $regId,
                'doc_number' => $docNumber,
                'partner_doc_number' => $partnerDocNumber,
                'track' => 'Dokumen Lampau (Legacy)',
                'is_legacy' => true,
                'title' => $title,
                'unit' => $unitMain,
                'first_party_signer' => $firstPartySigner,
                'first_party_position' => $firstPartyPosition,
                'collab_unit' => $unitCollab,
                'partner_name' => $partnerName,
                'partner_category' => $request->input('category') ?: 'DUDI / Swasta',
                'second_party_signer' => $secondPartySigner,
                'second_party_position' => $secondPartyPosition,
                'pic_first_party' => $picFirstParty,
                'pic_second_party' => $picSecondParty,
                'doc_type' => $request->input('doc_type', 'IA'),
                'paper_size' => $request->input('paper_size') ?: 'A4',
                'parent_mou' => '-',
                'scope' => $topic,
                'description' => $description,
                'students_data' => $request->input('students_data') ?: 'Mahasiswa UNIPAR',
                'validity' => ($duration ?: '1 Tahun'),
                'start_date' => $startDate ?: null,
                'end_date' => $endDate ?: null,
                'nominal_value' => $nominalValue,
                'warning_threshold' => $request->input('warning_threshold') ?: 'H-90 Hari (3 Bulan)',
                'status' => 'DRAFT',
                'draft_file' => 'legacy_' . strtolower(str_replace(' ', '_', $newId)) . '.pdf',
                'notes' => 'Pencatatan berkas dokumen lampau (legacy) oleh unit pengusul.',
            ]);

            // Handle Berkas Upload Draf Pengusul (PDF / DOCX)
            if ($request->hasFile('draft_file')) {
                $uploadedDraft = $request->file('draft_file');
                $ext = strtolower($uploadedDraft->getClientOriginalExtension());
                $dest = public_path('uploads/documents');
                if (!file_exists($dest)) {
                    mkdir($dest, 0777, true);
                }
                $filename = 'draf_' . strtolower(str_replace([' ', '/', '-'], '_', $newId)) . '_' . time() . '.' . $ext;
                $uploadedDraft->move($dest, $filename);
                if ($ext === 'docx') {
                    $document->docx_path = 'uploads/documents/' . $filename;
                } else {
                    $document->draft_file = 'uploads/documents/' . $filename;
                }
                $document->save();
            }

            // Handle Lampiran Opsional (Proposal, Rencana Kegiatan, & Dokumen Pendukung)
            if ($request->hasFile('proposal_file')) {
                $this->saveDocumentAttachment($document, $request->file('proposal_file'), 'PROPOSAL', 'Proposal Pengajuan Kerja Sama', $activeRole);
            }
            if ($request->hasFile('rencana_file')) {
                $this->saveDocumentAttachment($document, $request->file('rencana_file'), 'RENCANA_KEGIATAN', 'Rencana Kegiatan / TOR', $activeRole);
            }
            if ($request->hasFile('supporting_files')) {
                foreach ((array) $request->file('supporting_files') as $supFile) {
                    if ($supFile && $supFile->isValid()) {
                        $this->saveDocumentAttachment($document, $supFile, 'DOKUMEN_PENDUKUNG', null, $activeRole);
                    }
                }
            }

            AuditTrail::create([
                'document_id' => $document->id,
                'actor' => 'Unit Pengusul Utama (' . $unitMain . ')',
                'role' => $activeRole,
                'event' => 'CREATE_DRAFT',
                'from_status' => '-',
                'to_status' => 'DRAFT',
                'notes' => 'Penyusunan dokumen lampau (legacy) bersama ' . $partnerName . '. QR Code registrasi resmi diterbitkan dalam status DRAFT.',
                'timestamp_wita' => now()->format('Y-m-d H:i:s') . ' WITA',
            ]);

            // Sinkronisasi otomatis ke Master Data Mitra (Menu 2)
            Partner::syncFromDocument(
                $partnerName,
                $request->input('category', 'DUDI / Swasta'),
                $picSecondParty,
                $topic
            );

            return $document;
        });

        session(['selected_doc_id' => $doc->doc_id]);

        return redirect()->route('simkerma.index', ['menu' => 4, 'doc' => $doc->doc_id])
            ->with('flash', [
                'type' => 'success',
                'msg' => 'Dokumen lampau baru ' . $doc->doc_id . ' berhasil dibuat dengan status DRAFT. QR Code registrasi resmi aktif.'
            ]);
    }

    /**
     * Eksekusi Transisi Status FSM Formal
     */
    public function transition(Request $request)
    {
        $request->validate([
            'signed_scan_file' => 'nullable|file|mimes:pdf|max:15360',
            'draft_file' => 'nullable|file|mimes:pdf,docx|max:15360',
            'draft_docx_file' => 'nullable|file|mimes:docx|max:15360',
        ], [
            'signed_scan_file.mimes' => 'Format naskah fisik hasil scan harus berupa PDF (.pdf).',
            'signed_scan_file.max' => 'Ukuran berkas scan fisik maksimal 15 MB.',
            'draft_file.mimes' => 'Format berkas draf harus berupa PDF (.pdf) atau Word (.docx).',
            'draft_file.max' => 'Ukuran berkas draf maksimal 15 MB.',
            'draft_docx_file.mimes' => 'Format draf naskah harus berupa DOCX (.docx).',
            'draft_docx_file.max' => 'Ukuran berkas DOCX maksimal 15 MB.',
        ]);

        $docId = $request->input('doc_id');
        $event = $request->input('event');
        $notes = $request->input('notes', 'Disposisi keputusan FSM dieksekusi oleh pejabat berwenang');
        $activeRole = session('active_role', 'MASTER');

        $document = Document::where('doc_id', $docId)->orWhere('id', $docId)->firstOrFail();

        $actorTitle = match ($activeRole) {
            'LEGAL' => 'Tim Telaah Legal',
            'LEADER' => 'Pimpinan Rektorat',
            'SYSTEM' => 'Scheduler Otomasi',
            'MASTER' => 'Role Master (Super Admin)',
            'ADMIN' => 'Administrator SIMKERMA',
            default => 'Unit Pengusul (' . $document->unit . ')',
        };

        try {
            $oldStatus = $document->status;
            $additionalData = [];
            
            // Catat nomor Pihak I jika diperbarui
            if ($request->filled('doc_number')) {
                $additionalData['doc_number'] = $request->input('doc_number');
            }
            
            // Catat nomor Pihak II (Mitra)
            if ($request->filled('partner_doc_number')) {
                $additionalData['partner_doc_number'] = $request->input('partner_doc_number');
            }

            // Catat masa berlaku baru jika perpanjangan atau aktivasi
            if ($request->filled('new_validity')) {
                $additionalData['validity'] = $request->input('new_validity');
            } elseif ($request->filled('validity')) {
                $additionalData['validity'] = $request->input('validity');
            }
            if ($request->filled('start_date')) {
                $additionalData['start_date'] = $request->input('start_date');
            }
            if ($request->filled('end_date')) {
                $additionalData['end_date'] = $request->input('end_date');
            } elseif ($request->filled('start_date') && ($request->filled('validity') || $request->filled('new_validity'))) {
                try {
                    $vVal = $request->input('validity') ?: $request->input('new_validity');
                    preg_match('/(\d+)/', (string)$vVal, $vm);
                    $vYears = isset($vm[1]) ? (int)$vm[1] : 1;
                    $additionalData['end_date'] = \Carbon\Carbon::parse($request->input('start_date'))->addYears($vYears)->format('Y-m-d');
                } catch (\Throwable $e) {}
            }
            if ($request->filled('date_signed')) {
                $additionalData['date_signed'] = $request->input('date_signed');
            }

            // Handle Berkas Upload Dokumen (PDF)
            if ($request->hasFile('signed_scan_file')) {
                $uploaded = $request->file('signed_scan_file');
                $dest = public_path('uploads/documents');
                if (!file_exists($dest)) {
                    mkdir($dest, 0777, true);
                }
                $filename = 'naskah_' . strtolower(str_replace([' ', '/', '-'], '_', $document->doc_id)) . '_' . time() . '.' . $uploaded->getClientOriginalExtension();
                $uploaded->move($dest, $filename);
                $additionalData['signed_file'] = 'uploads/documents/' . $filename;
            }

            // Handle Berkas Upload Draf (PDF/DOCX) pada Inisiasi Adendum / Reaktivasi (RENEW)
            if ($event === 'RENEW' && $request->hasFile('draft_file')) {
                $uploadedDraft = $request->file('draft_file');
                $ext = strtolower($uploadedDraft->getClientOriginalExtension());
                $dest = public_path('uploads/documents');
                if (!file_exists($dest)) {
                    mkdir($dest, 0777, true);
                }
                $filename = 'draf_adendum_' . strtolower(str_replace([' ', '/', '-'], '_', $document->doc_id)) . '_' . time() . '.' . $ext;
                $uploadedDraft->move($dest, $filename);
                if ($ext === 'docx') {
                    $document->docx_path = 'uploads/documents/' . $filename;
                } else {
                    $document->draft_file = 'uploads/documents/' . $filename;
                }
                $document->save();
            }

            // Pengolahan khusus untuk event VERIFY (Persetujuan Pimpinan & Telaah Mutu Hukum)
            if ($event === 'VERIFY') {
                $hasTridharma = $request->has('substance_tridharma');
                $hasLegal = $request->has('legal_fairness');
                $hasIku6 = $request->has('iku6_benefit');

                $evalItems = [];
                $evalItems[] = 'Tridharma MBKM: ' . ($hasTridharma ? '✓ Memenuhi' : '-');
                $evalItems[] = 'Klausul Hukum: ' . ($hasLegal ? '✓ Berimbang' : '-');
                $evalItems[] = 'Capaian IKU 6: ' . ($hasIku6 ? '✓ Berdampak' : '-');
                $evalSummary = '[Evaluasi Mutu: ' . implode(', ', $evalItems) . ']';

                $customNotes = trim((string)$request->input('notes', ''));
                if ($customNotes !== '' && $customNotes !== 'Disposisi keputusan FSM dieksekusi oleh pejabat berwenang') {
                    $notes = $evalSummary . ' ' . $customNotes;
                } else {
                    $notes = $evalSummary . ' Draf naskah disetujui, teks dibekukan (Freeze Text), dan siap dicetak untuk proses penandatanganan fisik oleh Pimpinan.';
                }
            }

            // Pengolahan khusus untuk event ACTIVATE (Pengesahan Fisik & Pengaktifan Naskah)
            if ($event === 'ACTIVATE') {
                $hasSigned = $request->has('physical_signed');
                $hasStamp = $request->has('physical_stamp_meterai');

                $physicalItems = [];
                $physicalItems[] = 'TTD Basah: ' . ($hasSigned ? '✓ Lengkap' : '-');
                $physicalItems[] = 'Meterai & Stempel: ' . ($hasStamp ? '✓ Sah Bermeterai' : '-');
                $physSummary = '[Legalitas Fisik: ' . implode(', ', $physicalItems) . ']';

                $customNotes = trim((string)$request->input('notes', ''));
                if ($customNotes !== '' && $customNotes !== 'Disposisi keputusan FSM dieksekusi oleh pejabat berwenang') {
                    $notes = $physSummary . ' ' . $customNotes;
                } else {
                    $partnerNum = !empty($additionalData['partner_doc_number']) ? $additionalData['partner_doc_number'] : $document->partner_doc_number;
                    $notes = $physSummary . ' Naskah sah bertanda tangan basah & bermeterai aktif. Nomor Pihak I (UNIPAR): ' . ($document->doc_number ?: $document->doc_id) . (!empty($partnerNum) ? ', Nomor Pihak II (Mitra): ' . $partnerNum : '');
                }
            }

            $this->fsmService->transition($document, $event, $activeRole, $actorTitle, $notes, $additionalData);

            // Jika transisi SUBMIT (Pengajuan Draf ke Meja Pemeriksaan REVIEW Pimpinan):
            // Pasangkan stempel QR Code dokumen ke naskah yang dikirim oleh pengusul (format PDF)
            // di bagian ujung kanan bawah halaman terakhir dokumen (mendukung format kertas A4 dan F4).
            $stampSuccessMsg = '';
            if ($event === 'SUBMIT') {
                $customPath = null;
                if ($request->hasFile('draft_file')) {
                    $uploadedFile = $request->file('draft_file');
                    $customPath = $uploadedFile->getRealPath();
                } elseif ($request->hasFile('draft_docx_file')) {
                    $uploadedFile = $request->file('draft_docx_file');
                    $customPath = $uploadedFile->getRealPath();
                } elseif (!empty($document->draft_file) && file_exists(public_path($document->draft_file))) {
                    $customPath = public_path($document->draft_file);
                } elseif (!empty($document->docx_path) && file_exists(public_path($document->docx_path))) {
                    $customPath = public_path($document->docx_path);
                }

                $paperSize = $request->input('paper_size', $document->paper_size ?: 'A4');
                
                try {
                    $stampResult = $this->docxStampService->stampDocument($document, $customPath, $paperSize);
                    $stampSuccessMsg = " Naskah draf yang Anda kirim telah dibubuhi stempel QR Code dinamis resmi SIMKERMA UNIPAR di bagian ujung kanan bawah halaman terakhir (kertas {$paperSize}) dan siap ditelaah pimpinan.";
                } catch (\Throwable $e) {
                    \Illuminate\Support\Facades\Log::error("Gagal stempel QR Code dokumen: " . $e->getMessage());
                }
            }

            // Kirim notifikasi sistem in-app
            $notifTitle = match($event) {
                'SUBMIT' => 'Pengajuan Draf ke Pemeriksaan',
                'VERIFY' => 'Draf Disetujui Pimpinan / Legal',
                'RETURN_TO_DRAFT' => 'Draf Dikembalikan untuk Revisi',
                'ACTIVATE' => 'Pengesahan Naskah Aktif Sah',
                'CANCEL' => 'Pengusulan Naskah Dibatalkan',
                'RENEW' => 'Inisiasi Perpanjangan Adendum',
                default => 'Pembaruan Status Dokumen'
            };
            $notifType = match($event) {
                'VERIFY', 'ACTIVATE' => 'success',
                'RETURN_TO_DRAFT' => 'warning',
                'CANCEL' => 'danger',
                default => 'primary'
            };
            $notifIcon = match($event) {
                'SUBMIT' => 'bi-send-fill',
                'VERIFY' => 'bi-patch-check-fill',
                'RETURN_TO_DRAFT' => 'bi-arrow-return-left',
                'ACTIVATE' => 'bi-check-circle-fill',
                'CANCEL' => 'bi-x-octagon-fill',
                'RENEW' => 'bi-arrow-repeat',
                default => 'bi-bell-fill'
            };
            $notifMsg = "Naskah {$document->doc_id} ({$document->partner_name}) beralih status: {$oldStatus} → {$document->status} oleh {$actorTitle}.";
            if ($event === 'RETURN_TO_DRAFT' && $request->filled('notes')) {
                $notifMsg .= " Catatan revisi: " . Str::limit($request->input('notes'), 120);
            }
            $this->createNotification($document, $notifTitle, $notifMsg, $notifType, $notifIcon);

            $routeParams = ['menu' => 4, 'doc' => $document->doc_id];
            if ($request->filled('redirect_view')) {
                $routeParams['view'] = $request->input('redirect_view');
            }

            return redirect()->route('simkerma.index', $routeParams)
                ->with('flash', [
                    'type' => 'success',
                    'msg' => "Status dokumen {$document->doc_id} berhasil ditransisikan: {$oldStatus} &rarr; {$document->status} [{$event}].{$stampSuccessMsg} Status QR Code terbaharui otomatis."
                ]);
        } catch (IllegalTransitionException $e) {
            $errParams = ['menu' => 4, 'doc' => $document->doc_id];
            if ($request->filled('redirect_view')) {
                $errParams['view'] = $request->input('redirect_view');
            }
            return redirect()->route('simkerma.index', $errParams)
                ->with('flash', [
                    'type' => 'danger',
                    'msg' => $e->getMessage()
                ]);
        }
    }

    /**
     * Reset Status Dokumen ke DRAFT (Khusus Role Master & Administrator)
     */
    public function resetDraft(Request $request)
    {
        $activeRole = session('active_role', auth()->check() ? auth()->user()->role : 'GUEST');
        if (!in_array($activeRole, ['MASTER', 'ADMIN'])) {
            return redirect()->route('simkerma.index', ['menu' => 4])
                ->with('flash', [
                    'type' => 'danger',
                    'msg' => 'Akses Ditolak! Fitur reset dokumen ke DRAFT hanya dapat dilakukan oleh Role MASTER atau Administrator.'
                ]);
        }

        $docId = $request->input('doc_id');
        $document = Document::where('doc_id', $docId)->orWhere('id', $docId)->firstOrFail();

        $prevStatus = $document->status;
        $document->status = 'DRAFT';
        $document->signed_file = null;
        $document->date_signed = null;
        $document->date_approved = null;
        $document->save();

        AuditTrail::create([
            'document_id' => $document->id,
            'actor' => 'Pejabat Berwenang (' . $activeRole . ')',
            'role' => $activeRole,
            'event' => 'RESET_TO_DRAFT',
            'from_status' => $prevStatus,
            'to_status' => 'DRAFT',
            'notes' => 'Reset status dokumen kembali ke konsep awal (DRAFT) untuk pengujian alur FSM dari awal.',
            'timestamp_wita' => now()->format('Y-m-d H:i:s') . ' WITA',
        ]);

        return redirect()->route('simkerma.index', ['menu' => 4, 'doc' => $document->doc_id])
            ->with('flash', [
                'type' => 'warning',
                'msg' => "Dokumen {$document->doc_id} berhasil direset ke status DRAFT. Anda dapat menguji kembali alur FSM dari awal."
            ]);
    }

    /**
     * Hapus Naskah Dokumen (Role MASTER diizinkan menghapus pada semua status)
     */
    public function deleteDraft(Request $request)
    {
        $docId = $request->input('doc_id');
        $document = Document::where('doc_id', $docId)->orWhere('id', $docId)->firstOrFail();
        $activeRole = session('active_role', auth()->check() ? auth()->user()->role : 'ADMIN');

        // Aturan Integritas FSM: DRAFT boleh dihapus oleh siapapun yang berwenang.
        // Khusus Role MASTER / ADMIN diberikan hak khusus untuk menghapus pada SEMUA status (Master Bypass / Super Admin Privilege).
        if ($document->status !== 'DRAFT' && $activeRole !== 'MASTER' && $activeRole !== 'ADMIN') {
            return redirect()->route('simkerma.index', ['menu' => 4, 'doc' => $document->doc_id])
                ->with('flash', [
                    'type' => 'danger',
                    'msg' => "Penghapusan Ditolak! Dokumen {$document->doc_id} berstatus {$document->status}. Sesuai prinsip integritas FSM dan audit institusi, naskah yang sudah melewati tahap DRAFT hanya dapat dihapus oleh Role MASTER / Administrator."
                ]);
        }

        $deletedDocId = $document->doc_id;
        $partnerName = $document->partner_name;
        $statusBeforeDelete = $document->status;

        // Bersihkan data terkait dalam transaksi aman
        DB::transaction(function () use ($document) {
            // Hapus file draft fisik jika ada
            // Catatan: draft_file/signed_file/docx_path disimpan sebagai 'uploads/documents/xxx.pdf'
            // atau hanya 'xxx.pdf', jadi perlu penanganan kedua kasus
            $filesToDelete = [$document->draft_file, $document->signed_file, $document->docx_path];
            foreach ($filesToDelete as $filePath) {
                if (!$filePath) continue;
                // Jika path sudah mengandung 'uploads/documents/', gunakan langsung
                if (str_starts_with($filePath, 'uploads/')) {
                    $fullPath = public_path($filePath);
                } else {
                    $fullPath = public_path('uploads/documents/' . $filePath);
                }
                if (file_exists($fullPath)) {
                    @unlink($fullPath);
                }
            }
            // Hapus aktivitas terkait jika ada
            if (method_exists($document, 'activities')) {
                $document->activities()->delete();
            }
            // Hapus audit trails terkait
            $document->auditTrails()->delete();
            // Hapus dokumen dari basis data
            $document->delete();
        });

        // Hapus session selected_doc_id jika dokumen ini sedang dipilih
        if (session('selected_doc_id') === $deletedDocId) {
            session()->forget('selected_doc_id');
        }

        $successMsg = ($statusBeforeDelete === 'DRAFT')
            ? "Draf dokumen {$deletedDocId} bersama mitra {$partnerName} berhasil dihapus permanen dari sistem."
            : "Dokumen {$deletedDocId} (Status: {$statusBeforeDelete}) bersama mitra {$partnerName} berhasil dihapus permanen oleh Hak Akses MASTER.";

        return redirect()->route('simkerma.index', ['menu' => 4])
            ->with('flash', [
                'type' => 'success',
                'msg' => $successMsg
            ]);
    }

    /**
     * Hapus Semua Pengajuan dan Persetujuan Dokumen Kerja Sama (Fitur Mode Pengembangan - Khusus MASTER)
     */
    public function resetAllDocuments(Request $request)
    {
        $activeRole = session('active_role', auth()->check() ? auth()->user()->role : 'GUEST');
        if (!in_array($activeRole, ['MASTER', 'ADMIN'])) {
            return redirect()->route('simkerma.index', ['menu' => 4])
                ->with('flash', [
                    'type' => 'danger',
                    'msg' => 'Akses Ditolak! Fitur reset semua dokumen hanya dapat diakses oleh Role MASTER / Administrator.'
                ]);
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Activity::truncate();
        AuditTrail::truncate();
        Document::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Bersihkan berkas fisik yang pernah diunggah/dibuat di public/uploads/documents (kecuali .gitkeep)
        $docDir = public_path('uploads/documents');
        if (is_dir($docDir)) {
            $files = glob($docDir . '/*');
            foreach ($files as $file) {
                if (is_file($file) && basename($file) !== '.gitkeep') {
                    @unlink($file);
                }
            }
        }

        session()->forget('selected_doc_id');

        return redirect()->route('simkerma.index', ['menu' => 4])
            ->with('flash', [
                'type' => 'success',
                'msg' => 'Semua data pengajuan dan persetujuan kerja sama beserta log jejak audit berhasil dibersihkan (Mode Pengembangan).'
            ]);
    }

    /**
     * Halaman Verifikasi Publik QR Code
     */
    public function verify(Request $request, $reg_id = null)
    {
        $reg = $reg_id ?: $request->query('reg');
        $document = Document::with('auditTrails')
            ->where('reg_id', $reg)
            ->orWhere('doc_id', $reg)
            ->first();

        // Cari turunan jika dokumen ini adalah induk
        $childDerivatives = collect();
        if ($document) {
            $curNum = $document->doc_number;
            $curId = $document->doc_id;
            $childDerivatives = Document::where('id', '!=', $document->id)
                ->where(function ($q) use ($curNum, $curId) {
                    $q->where('parent_mou', $curNum)
                      ->orWhere('parent_mou', $curId);
                })->get();
        }

        return view('simkerma.verify', compact('document', 'childDerivatives', 'reg'));
    }

    /**
     * Pencatatan Realisasi Kegiatan Pelaksanaan Kerja Sama (Menu 5 - Sinkron dengan Dokumen Menu 4)
     */
    public function storeActivity(Request $request)
    {
        $request->validate([
            'document_id' => 'required|exists:documents,id',
            'activity_name' => 'required|string|max:255',
            'execution_status' => 'required|string',
            'evidence_file_upload' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png|max:15360',
            'evidence_photos_upload' => 'nullable|file|mimes:jpg,jpeg,png,pdf,zip|max:15360',
        ]);

        $document = Document::findOrFail($request->input('document_id'));
        $activeRole = session('active_role', 'MASTER');

        $actDest = public_path('uploads/activities');
        if (!file_exists($actDest)) {
            mkdir($actDest, 0777, true);
        }

        $evidenceFile = $request->input('evidence_file') ?: 'laporan_kegiatan.pdf';
        if ($request->hasFile('evidence_file_upload')) {
            $evFile = $request->file('evidence_file_upload');
            $evExt = strtolower($evFile->getClientOriginalExtension());
            $evName = 'laporan_' . strtolower(str_replace([' ', '/', '-'], '_', $document->doc_id)) . '_' . time() . '_' . Str::random(4) . '.' . $evExt;
            $evFile->move($actDest, $evName);
            $evidenceFile = 'uploads/activities/' . $evName;

            // Sinkronkan juga ke lampiran repositori dokumen (Menu 3)
            DocumentAttachment::create([
                'document_id' => $document->id,
                'category' => 'LAPORAN_PELAKSANAAN',
                'title' => 'Laporan: ' . $request->input('activity_name'),
                'file_path' => $evidenceFile,
                'original_name' => $evFile->getClientOriginalName(),
                'file_size' => @filesize($actDest . '/' . $evName) ?: null,
                'uploaded_by' => $activeRole,
            ]);
        }

        $evidencePhotos = $request->input('evidence_photos');
        if ($request->hasFile('evidence_photos_upload')) {
            $phFile = $request->file('evidence_photos_upload');
            $phExt = strtolower($phFile->getClientOriginalExtension());
            $phName = 'foto_' . strtolower(str_replace([' ', '/', '-'], '_', $document->doc_id)) . '_' . time() . '_' . Str::random(4) . '.' . $phExt;
            $phFile->move($actDest, $phName);
            $evidencePhotos = 'uploads/activities/' . $phName;
        }

        $activity = Activity::create([
            'document_id' => $document->id,
            'activity_name' => $request->input('activity_name'),
            'schedule_start' => $request->input('schedule_start') ?: now()->format('d M Y'),
            'schedule_end' => $request->input('schedule_end'),
            'target_qty' => $request->input('target_qty'),
            'realized_qty' => $request->input('realized_qty'),
            'parties_involved' => $request->input('parties_involved') ?: 'Mahasiswa & Mitra ' . $document->partner_name,
            'outputs' => $request->input('outputs') ?: 'Laporan Pelaksanaan Kegiatan',
            'budget_info' => $request->input('budget_info') ?: '-',
            'evidence_file' => $evidenceFile,
            'evidence_photos' => $evidencePhotos,
            'execution_status' => $request->input('execution_status', 'SEDANG_BERJALAN'),
            'notes' => $request->input('notes'),
        ]);

        // Catat ke Immutable Audit Trail dokumen rujukan
        AuditTrail::create([
            'document_id' => $document->id,
            'actor' => 'Bidang Kerja Sama (' . $activeRole . ')',
            'role' => $activeRole,
            'event' => 'RECORD_ACTIVITY',
            'from_status' => $document->status,
            'to_status' => $document->status,
            'notes' => 'Pencatatan realisasi kegiatan pelaksanaan: ' . $activity->activity_name . ' (' . $activity->execution_status . ').',
            'timestamp_wita' => now()->format('Y-m-d H:i:s') . ' WITA',
        ]);

        if ($request->boolean('redirect_to_detail')) {
            return redirect()->route('simkerma.index', ['menu' => 4, 'view' => 'detail', 'doc' => $document->doc_id, 'tab' => 'act'])
                ->with('flash', [
                    'type' => 'success',
                    'msg' => "Kegiatan '{$activity->activity_name}' berhasil dicatat pada naskah {$document->doc_id} ({$document->partner_name})."
                ]);
        }

        return redirect()->route('simkerma.index', ['menu' => 5])
            ->with('flash', [
                'type' => 'success',
                'msg' => "Kegiatan '{$activity->activity_name}' berhasil dicatat dan disinkronkan dengan dokumen {$document->doc_id} ({$document->partner_name})."
            ]);
    }

    /**
     * Pencatatan Hasil Evaluasi & Rekomendasi Dokumen (Menu 6)
     */
    public function storeEvaluation(Request $request)
    {
        $request->validate([
            'document_id' => 'required|exists:documents,id',
            'evaluation_score' => 'required|string',
            'recommendation' => 'required|string',
        ]);

        $document = Document::findOrFail($request->input('document_id'));
        $activeRole = session('active_role', 'MASTER');

        AuditTrail::create([
            'document_id' => $document->id,
            'actor' => 'Biro Kerja Sama (' . $activeRole . ')',
            'role' => $activeRole,
            'event' => 'EVALUATION_RECORDED',
            'from_status' => $document->status,
            'to_status' => $document->status,
            'notes' => 'Hasil Evaluasi: ' . $request->input('evaluation_score') . '. Rekomendasi: ' . $request->input('recommendation') . '. Kendala: ' . ($request->input('notes') ?: 'Tidak ada kendala'),
            'timestamp_wita' => now()->format('Y-m-d H:i:s') . ' WITA',
        ]);

        return redirect()->route('simkerma.index', ['menu' => 6])
            ->with('flash', [
                'type' => 'success',
                'msg' => "Hasil evaluasi dokumen {$document->doc_id} ({$document->partner_name}) berhasil disimpan ke dalam jejak audit permanen."
            ]);
    }

    /**
     * Input Mandiri Data Mitra Baru (Menu 02)
     */
    public function storePartner(Request $request)
    {
        $isAjax = $request->ajax() || $request->wantsJson() || $request->input('is_ajax');

        $name = trim((string)$request->input('name', ''));
        $category = $request->input('category', 'DUDI / Swasta');

        if (empty($name)) {
            if ($isAjax) {
                return response()->json(['success' => false, 'message' => 'Nama mitra wajib diisi.'], 422);
            }
            return redirect()->back()->with('flash', ['type' => 'danger', 'msg' => 'Nama mitra wajib diisi.']);
        }

        // Cek jika nama mitra sudah pernah terdaftar di database
        $existing = Partner::where('name', $name)->first();
        if ($existing) {
            if ($isAjax) {
                return response()->json([
                    'success' => true,
                    'message' => "Mitra '{$existing->name}' sudah terdaftar di Master Data Mitra (Menu 02).",
                    'partner' => $existing,
                    'is_existing' => true
                ]);
            }
            return redirect()->route('simkerma.index', ['menu' => 2])
                ->with('flash', [
                    'type' => 'info',
                    'msg' => "Mitra '{$existing->name}' sudah terdaftar di Master Data Mitra."
                ]);
        }

        $activeRole = session('active_role', auth()->check() ? auth()->user()->role : 'ADMIN');
        $canVerify = in_array($activeRole, ['MASTER', 'ADMIN', 'LEGAL'], true);
        $cleanOrNull = fn(string $key): ?string => trim((string)$request->input($key, '')) !== '' ? trim((string)$request->input($key)) : null;

        $scopeCoverage = $cleanOrNull('scope_coverage')
            ?: ((stripos($name, 'international') !== false || stripos($category, 'luar') !== false)
                ? 'Internasional'
                : 'Dalam Negeri (Nasional/Lokal)');

        $requestedVerification = $cleanOrNull('status_verification');
        $statusVerification = ($canVerify && in_array($requestedVerification, Partner::VERIFICATION_STATUSES, true))
            ? $requestedVerification
            : 'Belum Diverifikasi';

        $partner = Partner::create([
            'name' => $name,
            'category' => $category,
            'scope_coverage' => $scopeCoverage,
            'city' => $cleanOrNull('city'),
            'address' => $cleanOrNull('address'),
            'phone' => $cleanOrNull('phone'),
            'email' => $cleanOrNull('email'),
            'pic_name' => $cleanOrNull('pic_name'),
            'pic_title' => $cleanOrNull('pic_title'),
            'pic_contact' => $cleanOrNull('pic_contact'),
            'profile_desc' => $cleanOrNull('profile_desc'),
            'status_verification' => $statusVerification,
        ]);

        if ($isAjax) {
            return response()->json([
                'success' => true,
                'message' => "Data mitra baru '{$partner->name}' berhasil didaftarkan ke Master Data Mitra (Menu 02).",
                'partner' => $partner,
                'is_existing' => false
            ]);
        }

        return redirect()->route('simkerma.index', ['menu' => 2])
            ->with('flash', [
                'type' => 'success',
                'msg' => "Data mitra baru '{$partner->name}' berhasil didaftarkan secara mandiri ke basis data SIMKERMA."
            ]);
    }

    /**
     * Perbarui Profil & Data Mitra serta Sinkronkan ke Seluruh Dokumen Terkait
     */
    public function updatePartner(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:partners,id',
            'name' => 'required|string|max:255',
            'category' => 'required|string',
            'email' => 'nullable|email|max:255',
            'status_verification' => 'nullable|string|in:' . implode(',', Partner::VERIFICATION_STATUSES),
        ]);

        $activeRole = session('active_role', auth()->check() ? auth()->user()->role : 'ADMIN');
        $partner = Partner::findOrFail($request->input('id'));
        $oldName = $partner->name;
        $newName = trim((string)$request->input('name'));
        $newCategory = trim((string)$request->input('category'));
        $cleanOrNull = fn(string $key): ?string => trim((string)$request->input($key, '')) !== '' ? trim((string)$request->input($key)) : null;

        $nextVerification = $partner->status_verification ?: 'Belum Diverifikasi';
        if ($request->filled('status_verification') && in_array($activeRole, ['MASTER', 'ADMIN', 'LEGAL'], true)) {
            $nextVerification = $request->input('status_verification');
        }

        $partner->update([
            'name' => $newName,
            'category' => $newCategory,
            'scope_coverage' => $cleanOrNull('scope_coverage') ?: $partner->scope_coverage,
            'city' => $request->has('city') ? $cleanOrNull('city') : $partner->city,
            'address' => $request->has('address') ? $cleanOrNull('address') : $partner->address,
            'phone' => $request->has('phone') ? $cleanOrNull('phone') : $partner->phone,
            'email' => $request->has('email') ? $cleanOrNull('email') : $partner->email,
            'pic_name' => $request->has('pic_name') ? $cleanOrNull('pic_name') : $partner->pic_name,
            'pic_title' => $request->has('pic_title') ? $cleanOrNull('pic_title') : $partner->pic_title,
            'pic_contact' => $request->has('pic_contact') ? $cleanOrNull('pic_contact') : $partner->pic_contact,
            'profile_desc' => $request->has('profile_desc') ? $cleanOrNull('profile_desc') : $partner->profile_desc,
            'status_verification' => $nextVerification,
        ]);

        // Sinkronisasi ke seluruh dokumen di MySQL jika nama atau kategori mitra berubah
        $affectedCount = 0;
        if ($oldName !== $newName || $partner->wasChanged('category')) {
            $affectedDocs = Document::where('partner_name', $oldName)->get();
            $affectedCount = $affectedDocs->count();

            Document::where('partner_name', $oldName)->update([
                'partner_name' => $newName,
                'partner_category' => $newCategory,
            ]);

            // Catat ke Audit Trail dokumen terkait
            $activeRole = session('active_role', 'MASTER');
            foreach ($affectedDocs as $doc) {
                AuditTrail::create([
                    'document_id' => $doc->id,
                    'actor' => 'Pengelola Data Mitra (' . $activeRole . ')',
                    'role' => $activeRole,
                    'event' => 'PARTNER_DATA_UPDATED',
                    'from_status' => $doc->status,
                    'to_status' => $doc->status,
                    'notes' => "Pembaruan profil master mitra: Nama '{$oldName}' -> '{$newName}', Kategori '{$newCategory}'. Tersinkron otomatis ke seluruh dokumen.",
                    'timestamp_wita' => now()->format('Y-m-d H:i:s') . ' WITA',
                ]);
            }
        }

        $syncMsg = $affectedCount > 0 ? " dan berhasil disinkronkan ke {$affectedCount} naskah dokumen terkait di SIMKERMA." : ".";

        return redirect()->route('simkerma.index', ['menu' => 2])
            ->with('flash', [
                'type' => 'success',
                'msg' => "Profil data mitra '{$partner->name}' berhasil diperbarui{$syncMsg}"
            ]);
    }

    /**
     * Hapus Data Mitra dari Master Data (Menu 2)
     */
    public function deletePartner(Request $request)
    {
        $activeRole = session('active_role', auth()->check() ? auth()->user()->role : 'ADMIN');
        if (!in_array($activeRole, ['MASTER', 'ADMIN'], true)) {
            return redirect()->route('simkerma.index', ['menu' => 2])
                ->with('flash', [
                    'type' => 'danger',
                    'msg' => 'Akses Ditolak! Hanya Role MASTER atau Administrator yang diizinkan menghapus data mitra.'
                ]);
        }

        $request->validate([
            'id' => 'required|exists:partners,id',
        ]);

        $partner = Partner::findOrFail($request->input('id'));
        $partnerName = $partner->name;

        // Cek apakah mitra memiliki dokumen kerja sama aktif
        $docCount = Document::where('partner_name', $partnerName)->count();
        if ($docCount > 0) {
            return redirect()->route('simkerma.index', ['menu' => 2])
                ->with('flash', [
                    'type' => 'danger',
                    'msg' => "Gagal menghapus: Mitra '{$partnerName}' masih terhubung dengan {$docCount} dokumen kerja sama di Menu 04. Hapus atau pindahkan dokumen terkait terlebih dahulu."
                ]);
        }

        $partner->delete();

        return redirect()->route('simkerma.index', ['menu' => 2])
            ->with('flash', [
                'type' => 'success',
                'msg' => "Data mitra '{$partnerName}' berhasil dihapus dari direktori master data."
            ]);
    }

    /**
     * Perbarui Informasi Dokumen Kerja Sama dari Menu Detail
     */
    public function updateDocument(Request $request)
    {
        $docId = $request->input('doc_id');
        $document = Document::where('doc_id', $docId)->orWhere('id', $docId)->firstOrFail();
        $activeRole = session('active_role', auth()->check() ? auth()->user()->role : 'ADMIN');

        // Freeze Text Protection: Dokumen berstatus VERIFIED atau ACTIVE tidak diperkenankan
        // mengubah identitas legal (Nomor Dokumen, Mitra, Jenis Dokumen) kecuali oleh Role MASTER / ADMIN.
        $isFrozen = in_array($document->status, ['VERIFIED', 'ACTIVE']) && !in_array($activeRole, ['MASTER', 'ADMIN']);

        $startDate = $request->input('start_date') ?: $document->start_date;
        $endDate = $request->input('end_date') ?: $document->end_date;
        $validityYears = $request->input('validity_years');
        $validity = $request->input('validity', $document->validity);

        if (!empty($validityYears)) {
            $validity = ((int)$validityYears) . ' Tahun';
            if ($startDate && empty($request->input('end_date'))) {
                try {
                    $endDate = \Carbon\Carbon::parse($startDate)->addYears((int)$validityYears)->format('Y-m-d');
                } catch (\Exception $e) {}
            }
        }

        $category = $request->input('category') ?: $request->input('partner_category', $document->partner_category);

        $updateData = [
            'title' => $request->input('title', $document->title),
            'unit' => $request->input('unit', $document->unit),
            'first_party_signer' => $request->input('first_party_signer', $document->first_party_signer),
            'first_party_position' => $request->input('first_party_position', $document->first_party_position),
            'partner_name' => $isFrozen ? $document->partner_name : $request->input('partner_name', $document->partner_name),
            'partner_category' => $category,
            'second_party_signer' => $request->input('second_party_signer', $document->second_party_signer),
            'second_party_position' => $request->input('second_party_position', $document->second_party_position),
            'pic_first_party' => $request->input('pic_first_party', $document->pic_first_party),
            'pic_second_party' => $request->input('pic_second_party', $document->pic_second_party),
            'scope' => $request->input('scope', $document->scope),
            'description' => $request->input('description', $document->description),
            'start_date' => $startDate,
            'end_date' => $endDate,
            'validity' => $validity,
            'warning_threshold' => $request->input('warning_threshold', $document->warning_threshold),
        ];

        if (!$isFrozen && $request->has('doc_number')) {
            $val = trim((string)$request->input('doc_number', ''));
            $updateData['doc_number'] = ($val !== '') ? $val : '-';
        }
        if (!$isFrozen && $request->has('partner_doc_number')) {
            $val = trim((string)$request->input('partner_doc_number', ''));
            $updateData['partner_doc_number'] = ($val !== '') ? $val : '-';
        }
        if ($request->has('parent_mou')) {
            $pVal = trim((string)$request->input('parent_mou', ''));
            $updateData['parent_mou'] = ($pVal !== '') ? $pVal : '-';
        }
        if (!$isFrozen && $request->has('doc_type')) {
            $updateData['doc_type'] = $request->input('doc_type');
        }

        $fieldLabels = [
            'title' => 'Judul',
            'doc_type' => 'Jenis',
            'parent_mou' => 'Induk',
            'doc_number' => 'No. UNIPAR',
            'partner_doc_number' => 'No. Mitra',
            'unit' => 'Unit',
            'partner_name' => 'Mitra',
            'first_party_signer' => 'Penandatangan UNIPAR',
            'second_party_signer' => 'Penandatangan Mitra',
            'validity' => 'Masa Berlaku',
            'start_date' => 'Tgl Mulai',
            'end_date' => 'Tgl Akhir',
        ];
        $diffItems = [];
        foreach ($fieldLabels as $key => $label) {
            if (!array_key_exists($key, $updateData)) {
                continue;
            }
            $oldRaw = $document->{$key};
            $oldStr = ($oldRaw instanceof \Carbon\CarbonInterface) ? $oldRaw->format('Y-m-d') : trim((string)($oldRaw ?? ''));
            $newStr = trim((string)($updateData[$key] ?? ''));
            if ($oldStr !== $newStr) {
                $diffItems[] = $label . ': "' . ($oldStr ?: '-') . '" → "' . ($newStr ?: '-') . '"';
            }
        }

        $document->update($updateData);

        // Sinkronisasi nama/kategori mitra jika diubah
        if (!$isFrozen && $request->filled('partner_name')) {
            Partner::updateOrCreate(
                ['name' => $request->input('partner_name')],
                ['category' => $category]
            );
        }

        $changeSummary = count($diffItems) > 0
            ? ' Perubahan: [' . implode('; ', $diffItems) . '].'
            : ' (Tidak ada perubahan nilai field).';

        AuditTrail::create([
            'document_id' => $document->id,
            'actor' => 'Pengguna (' . $activeRole . ')',
            'role' => $activeRole,
            'event' => 'UPDATE_INFO',
            'from_status' => $document->status,
            'to_status' => $document->status,
            'notes' => 'Pembaruan informasi dokumen kerja sama.' . $changeSummary . ($isFrozen ? ' (Field substansi legal dibekukan)' : ''),
            'timestamp_wita' => now()->format('Y-m-d H:i:s') . ' WITA',
        ]);

        return redirect()->route('simkerma.index', ['menu' => 4, 'view' => 'detail', 'doc' => $document->doc_id])
            ->with('flash', [
                'type' => 'success',
                'msg' => 'Informasi umum dokumen ' . $document->doc_id . ' berhasil diperbarui.'
            ]);
    }

    /**
     * Unggah Berkas Dokumen (Draf, Naskah Asli, Proposal, Rencana Kegiatan, Pendukung, atau Laporan Pelaksanaan)
     */
    public function uploadDocumentFile(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png|max:15360',
            'file_type' => 'required|in:draft,signed,proposal,rencana_kegiatan,dokumen_pendukung,laporan_pelaksanaan',
        ], [
            'file.required' => 'Silakan pilih berkas yang akan diunggah.',
            'file.mimes' => 'Format berkas diperbolehkan: PDF, Word, Excel, atau JPG/PNG.',
            'file.max' => 'Ukuran berkas maksimal 15 MB.',
        ]);

        $docId = $request->input('doc_id');
        $document = Document::where('doc_id', $docId)->orWhere('id', $docId)->firstOrFail();
        $fileType = $request->input('file_type', 'draft');
        $activeRole = session('active_role', 'ADMIN');

        $dest = public_path('uploads/documents');
        if (!file_exists($dest)) {
            mkdir($dest, 0777, true);
        }

        $uploadedFile = $request->file('file');
        $typeLabel = 'Berkas';

        if ($fileType === 'draft' && $uploadedFile) {
            $ext = strtolower($uploadedFile->getClientOriginalExtension());
            $filename = 'draf_' . strtolower(str_replace([' ', '/', '-'], '_', $document->doc_id)) . '_' . time() . '.' . $ext;
            $uploadedFile->move($dest, $filename);
            if ($ext === 'docx' || $ext === 'doc') {
                $document->docx_path = 'uploads/documents/' . $filename;
            } else {
                $document->draft_file = 'uploads/documents/' . $filename;
            }
            $document->save();
            $typeLabel = 'Naskah Draf Pengusul';
        } elseif ($fileType === 'signed' && $uploadedFile) {
            $ext = strtolower($uploadedFile->getClientOriginalExtension());
            $filename = 'signed_' . strtolower(str_replace([' ', '/', '-'], '_', $document->doc_id)) . '_' . time() . '.' . $ext;
            $uploadedFile->move($dest, $filename);
            $document->signed_file = 'uploads/documents/' . $filename;
            $document->save();
            $typeLabel = 'Naskah Asli Bertandatangan';
        } elseif ($uploadedFile) {
            $categoryMap = [
                'proposal' => ['PROPOSAL', 'Proposal Kerja Sama'],
                'rencana_kegiatan' => ['RENCANA_KEGIATAN', 'Rencana Kegiatan / TOR'],
                'dokumen_pendukung' => ['DOKUMEN_PENDUKUNG', 'Dokumen Pendukung'],
                'laporan_pelaksanaan' => ['LAPORAN_PELAKSANAAN', 'Laporan Pelaksanaan'],
            ];
            [$categoryEnum, $typeLabel] = $categoryMap[$fileType] ?? ['DOKUMEN_PENDUKUNG', 'Dokumen Pendukung'];
            $customTitle = trim((string)$request->input('attachment_title', ''));
            $this->saveDocumentAttachment($document, $uploadedFile, $categoryEnum, $customTitle ?: null, $activeRole);
        }

        AuditTrail::create([
            'document_id' => $document->id,
            'actor' => 'Pengguna (' . $activeRole . ')',
            'role' => $activeRole,
            'event' => 'UPLOAD_FILE',
            'from_status' => $document->status,
            'to_status' => $document->status,
            'notes' => 'Pengunggahan berkas ' . $typeLabel . ' (' . $uploadedFile->getClientOriginalName() . ') berhasil.',
            'timestamp_wita' => now()->format('Y-m-d H:i:s') . ' WITA',
        ]);

        if ((int)$request->input('redirect_menu') === 3) {
            return redirect()->route('simkerma.index', [
                    'menu' => 3,
                    'open_attachments' => $document->doc_id,
                ])
                ->with('flash', [
                    'type' => 'success',
                    'msg' => $typeLabel . ' untuk ' . $document->doc_id . ' berhasil diunggah.'
                ]);
        }

        return redirect()->route('simkerma.index', ['menu' => 4, 'view' => 'detail', 'doc' => $document->doc_id, 'tab' => 'docs'])
            ->with('flash', [
                'type' => 'success',
                'msg' => $typeLabel . ' berhasil diunggah.'
            ]);
    }

    /**
     * Hapus Lampiran Pendukung / Proposal / Rencana Kegiatan dari Repositori
     */
    public function deleteDocumentAttachment(Request $request)
    {
        $request->validate([
            'attachment_id' => 'required|exists:document_attachments,id',
        ]);

        $attachment = DocumentAttachment::with('document')->findOrFail($request->input('attachment_id'));
        $document = $attachment->document;
        $activeRole = session('active_role', 'ADMIN');

        if ($attachment->file_path) {
            $fullPath = public_path($attachment->file_path);
            if (file_exists($fullPath) && is_file($fullPath)) {
                @unlink($fullPath);
            }
        }

        $deletedTitle = $attachment->title ?: $attachment->original_name ?: $attachment->category_label;
        $attachment->delete();

        if ($document) {
            AuditTrail::create([
                'document_id' => $document->id,
                'actor' => 'Pengguna (' . $activeRole . ')',
                'role' => $activeRole,
                'event' => 'DELETE_ATTACHMENT',
                'from_status' => $document->status,
                'to_status' => $document->status,
                'notes' => 'Penghapusan lampiran: ' . $deletedTitle . '.',
                'timestamp_wita' => now()->format('Y-m-d H:i:s') . ' WITA',
            ]);
        }

        if ((int)$request->input('redirect_menu') === 3) {
            return redirect()->route('simkerma.index', [
                    'menu' => 3,
                    'open_attachments' => $document ? $document->doc_id : null,
                ])
                ->with('flash', ['type' => 'warning', 'msg' => "Lampiran '{$deletedTitle}' berhasil dihapus."]);
        }

        return redirect()->route('simkerma.index', ['menu' => 4, 'view' => 'detail', 'doc' => $document ? $document->doc_id : null, 'tab' => 'docs'])
            ->with('flash', ['type' => 'warning', 'msg' => "Lampiran '{$deletedTitle}' berhasil dihapus."]);
    }

    /**
     * Helper terpusat untuk menyimpan berkas lampiran multi-kategori (Proposal, Rencana, Pendukung)
     */
    private function saveDocumentAttachment(Document $document, \Illuminate\Http\UploadedFile $file, string $category, ?string $title, string $actor): DocumentAttachment
    {
        $dest = public_path('uploads/attachments');
        if (!file_exists($dest)) {
            mkdir($dest, 0777, true);
        }

        $ext = strtolower($file->getClientOriginalExtension());
        $origName = $file->getClientOriginalName();
        $size = $file->getSize();
        $safePrefix = strtolower(str_replace([' ', '/', '-'], '_', $document->doc_id . '_' . $category));
        $filename = $safePrefix . '_' . time() . '_' . Str::random(4) . '.' . $ext;
        $file->move($dest, $filename);

        return DocumentAttachment::create([
            'document_id' => $document->id,
            'category' => $category,
            'title' => $title ?: pathinfo($origName, PATHINFO_FILENAME),
            'file_path' => 'uploads/attachments/' . $filename,
            'original_name' => $origName,
            'file_size' => $size,
            'uploaded_by' => $actor,
        ]);
    }

    /**
     * Simpan Pencatatan Dokumen Kerja Sama Lampau (Legacy / Pra-Sistem)
     */
    public function storeLegacy(Request $request)
    {
        // Validasi field wajib pencatatan kerja sama lampau
        $firstPartySigner = trim((string)$request->input('first_party_signer'));
        $secondPartySigner = trim((string)$request->input('second_party_signer'));

        if (empty($firstPartySigner) || empty($secondPartySigner)) {
            return redirect()->back()->with('flash', [
                'type' => 'danger',
                'msg' => 'Gagal menyimpan: Penandatangan Pihak I dan Penandatangan Pihak II wajib diisi.'
            ]);
        }

        if (!$request->hasFile('signed_file')) {
            return redirect()->back()->with('flash', [
                'type' => 'danger',
                'msg' => 'Gagal menyimpan: Berkas Scan Naskah Asli (PDF) wajib diunggah untuk pencatatan kerja sama lampau.'
            ]);
        }

        $uploadedFile = $request->file('signed_file');
        $ext = strtolower($uploadedFile->getClientOriginalExtension());
        if ($ext !== 'pdf') {
            return redirect()->back()->with('flash', [
                'type' => 'danger',
                'msg' => 'Gagal menyimpan: Berkas Scan Naskah Asli harus berformat PDF.'
            ]);
        }

        [$newId, $regId] = $this->generateUniqueDocId();
        $docType = $request->input('doc_type', 'MoU');
        $partnerName = $request->input('partner_name', 'Mitra Lampau');
        $rawDocNumber = trim((string)$request->input('doc_number', ''));
        $docNumber = ($rawDocNumber !== '') ? $rawDocNumber : '-';
        $rawPartnerDocNumber = trim((string)$request->input('partner_doc_number', ''));
        $partnerDocNumber = ($rawPartnerDocNumber !== '') ? $rawPartnerDocNumber : '-';
        $title = $request->input('title', 'Kerja Sama ' . $docType . ' dengan ' . $partnerName);
        $activeRole = session('active_role', 'MASTER');

        $startDate = $request->input('start_date') ?: null;
        $endDate = $request->input('end_date') ?: null;
        $validityYears = $request->input('validity_years');
        $validity = $request->input('validity');

        if (!empty($validityYears)) {
            $validity = ((int)$validityYears) . ' Tahun';
            if ($startDate && empty($endDate)) {
                try {
                    $endDate = \Carbon\Carbon::parse($startDate)->addYears((int)$validityYears)->format('Y-m-d');
                } catch (\Exception $e) {}
            }
        } elseif (empty($validity)) {
            if ($startDate && $endDate) {
                try {
                    $start = \Carbon\Carbon::parse($startDate);
                    $end = \Carbon\Carbon::parse($endDate);
                    $years = $start->diffInYears($end);
                    if ($years >= 1) {
                        $validity = $years . ' Tahun';
                    } else {
                        $months = $start->diffInMonths($end);
                        $validity = ($months > 0 ? $months : 1) . ' Bulan';
                    }
                } catch (\Exception $e) {
                    $validity = '3 Tahun';
                }
            } else {
                $validity = '3 Tahun';
            }
        }

        // Tentukan Status Awal Dokumen Lampau:
        // Jika tanggal berakhir sudah lewat -> EXPIRED, jika masih berlaku -> ACTIVE
        $today = now()->format('Y-m-d');
        $initialStatus = 'ACTIVE';
        if ($endDate && $endDate < $today) {
            $initialStatus = 'EXPIRED';
        }

        // Ruang Lingkup Tridharma / Kemitraan (Multi-select Checkbox)
        $rawScope = $request->input('scope');
        if (is_array($rawScope) && count($rawScope) > 0) {
            $scope = implode(', ', $rawScope);
        } elseif (is_string($rawScope) && trim($rawScope) !== '') {
            $scope = trim($rawScope);
        } else {
            $scope = 'Pendidikan, Penelitian, Pengabdian kepada Masyarakat, Pengembangan SDM';
        }

        $paperSize = $request->input('paper_size', 'A4') ?: 'A4';
        $warningThreshold = 'H-90 Hari (3 Bulan)';
        $unit = $request->input('unit') ?: 'Biro Kerja Sama & Urusan Internasional (BKUI)';
        $category = $request->input('category') ?: 'DUDI / Swasta';
        $parentMou = $request->input('parent_mou') ?: '-';

        $doc = DB::transaction(function () use (
            $request, $newId, $regId, $docType, $partnerName, $docNumber,
            $partnerDocNumber, $parentMou, $activeRole, $title,
            $firstPartySigner, $secondPartySigner, $startDate, $endDate,
            $validity, $scope, $paperSize, $warningThreshold, $initialStatus,
            $unit, $category
        ) {
            $document = Document::create([
                'doc_id' => $newId,
                'reg_id' => $regId,
                'doc_number' => $docNumber,
                'partner_doc_number' => $partnerDocNumber,
                'track' => 'Dokumen Lampau (Legacy)',
                'title' => $title,
                'unit' => $unit,
                'first_party_signer' => $firstPartySigner,
                'collab_unit' => null,
                'partner_name' => $partnerName,
                'partner_category' => $category,
                'second_party_signer' => $secondPartySigner,
                'pic_first_party' => $request->input('pic_first_party'),
                'pic_second_party' => $request->input('pic_second_party'),
                'doc_type' => $docType,
                'paper_size' => $paperSize,
                'parent_mou' => $parentMou ?: '-',
                'scope' => $scope,
                'description' => $request->input('description') ?: 'Dokumen kerja sama lampau yang telah ditandatangani sebelum implementasi SIMKERMA UNIPAR.',
                'validity' => $validity,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'date_approved' => $startDate,
                'date_signed' => $startDate,
                'nominal_value' => $request->input('nominal_value'),
                'warning_threshold' => $warningThreshold,
                'status' => $initialStatus,
                'is_legacy' => true,
                'draft_file' => 'legacy_' . strtolower(str_replace([' ', '/', '-'], '_', $newId)) . '.pdf',
                'signed_file' => null,
                'notes' => 'Pencatatan arsip kerja sama lampau (pra-sistem).',
            ]);

            // Handle Berkas Upload Scan Asli (PDF)
            if ($request->hasFile('signed_file')) {
                $uploaded = $request->file('signed_file');
                $ext = strtolower($uploaded->getClientOriginalExtension());
                $dest = public_path('uploads/documents');
                if (!file_exists($dest)) {
                    mkdir($dest, 0777, true);
                }
                $filename = 'signed_legacy_' . strtolower(str_replace([' ', '/', '-'], '_', $newId)) . '_' . time() . '.' . $ext;
                $uploaded->move($dest, $filename);
                $document->signed_file = 'uploads/documents/' . $filename;
                $document->save();
            }

            // Handle Lampiran Opsional (Proposal, Rencana Kegiatan, & Dokumen Pendukung)
            if ($request->hasFile('proposal_file')) {
                $this->saveDocumentAttachment($document, $request->file('proposal_file'), 'PROPOSAL', 'Proposal Pengajuan Kerja Sama', $activeRole);
            }
            if ($request->hasFile('rencana_file')) {
                $this->saveDocumentAttachment($document, $request->file('rencana_file'), 'RENCANA_KEGIATAN', 'Rencana Kegiatan / TOR', $activeRole);
            }
            if ($request->hasFile('supporting_files')) {
                foreach ((array) $request->file('supporting_files') as $supFile) {
                    if ($supFile && $supFile->isValid()) {
                        $this->saveDocumentAttachment($document, $supFile, 'DOKUMEN_PENDUKUNG', null, $activeRole);
                    }
                }
            }

            AuditTrail::create([
                'document_id' => $document->id,
                'actor' => 'Pencatatan Dokumen Lampau (' . $activeRole . ')',
                'role' => $activeRole,
                'event' => 'RECORD_HISTORICAL_DATA',
                'from_status' => '-',
                'to_status' => $initialStatus,
                'notes' => 'Pencatatan naskah kerja sama lampau (pra-sistem) ' . $docType . ' bersama ' . $partnerName . ' (' . $docNumber . '). Status awal ditetapkan: ' . $initialStatus . '.',
                'timestamp_wita' => now()->format('Y-m-d H:i:s') . ' WITA',
            ]);

            // Sinkronisasi otomatis ke Master Data Mitra (Menu 2)
            Partner::syncFromDocument(
                $partnerName,
                $category,
                $request->input('pic_second_party'),
                $scope
            );

            return $document;
        });

        session(['selected_doc_id' => $doc->doc_id]);

        $this->createNotification(
            $doc,
            'Pengajuan Dokumen Baru',
            "Draf {$doc->doc_type} baru ({$doc->doc_id}) bersama mitra {$doc->partner_name} berhasil dibuat oleh {$doc->unit}.",
            'info',
            'bi-file-earmark-plus'
        );

        return redirect()->route('simkerma.index', ['menu' => 4, 'view' => 'detail', 'doc' => $doc->doc_id])
            ->with('flash', [
                'type' => 'success',
                'msg' => 'Kerja sama lampau ' . $doc->doc_id . ' (' . $doc->doc_number . ') berhasil dicatat dengan status ' . $doc->status . '.'
            ]);
    }

    /**
     * Reaktivasi / Perpanjangan Dokumen Kembali ke Status DRAFT
     */
    public function renewDraft(Request $request)
    {
        $docId = $request->input('doc_id');
        $document = Document::where('doc_id', $docId)->orWhere('id', $docId)->firstOrFail();
        $activeRole = session('active_role', 'MASTER');
        $actorTitle = match ($activeRole) {
            'LEGAL' => 'Tim Telaah Legal',
            'LEADER' => 'Pimpinan Rektorat',
            'SYSTEM' => 'Scheduler Otomasi',
            'MASTER' => 'Role Master (Super Admin)',
            'ADMIN' => 'Administrator SIMKERMA',
            default => 'Unit Pengusul (' . $document->unit . ')',
        };

        $notes = $request->input('notes', 'Inisiasi perpanjangan / reaktivasi naskah kerja sama kembali ke status DRAFT.');

        try {
            $this->fsmService->transition($document, 'RENEW', $activeRole, $actorTitle, $notes);

            $this->createNotification(
                $document,
                'Inisiasi Perpanjangan / Reaktivasi',
                "Dokumen {$document->doc_id} berhasil diperpanjang / direaktivasi kembali ke status DRAFT oleh {$actorTitle}.",
                'info',
                'bi-arrow-repeat'
            );

            return redirect()->route('simkerma.index', ['menu' => 4, 'view' => 'detail', 'doc' => $document->doc_id])
                ->with('flash', [
                    'type' => 'success',
                    'msg' => 'Dokumen ' . $document->doc_id . ' berhasil diperpanjang / direaktivasi kembali ke status DRAFT. Anda dapat memperbarui draf dan ketentuan naskah.'
                ]);
        } catch (\Exception $e) {
            return redirect()->route('simkerma.index', ['menu' => 4, 'view' => 'detail', 'doc' => $document->doc_id])
                ->with('flash', [
                    'type' => 'danger',
                    'msg' => 'Gagal memperbarui status: ' . $e->getMessage()
                ]);
        }
    }

    /**
     * API Pencarian Direktori Perguruan Tinggi (PDDikti) untuk Hybrid Smart Combobox
     */
    public function searchUniversities(Request $request)
    {
        $query = strtolower(trim($request->query('q', '')));
        $limit = (int)$request->query('limit', 25);
        if ($limit < 1 || $limit > 100) {
            $limit = 25;
        }

        $phpPath = database_path('data/perguruan_tinggi.php');
        if (!file_exists($phpPath)) {
            $phpPath = storage_path('app/perguruan_tinggi.php');
        }

        if (file_exists($phpPath)) {
            $allPt = require $phpPath;
        } else {
            $jsonPath = storage_path('app/perguruan_tinggi.json');
            $allPt = file_exists($jsonPath) ? (json_decode(file_get_contents($jsonPath), true) ?: []) : [];
        }

        if (empty($allPt)) {
            return response()->json([]);
        }

        if (empty($query)) {
            // Berikan saran prioritas: Kampus lokal Sulawesi Utara & mitra populer
            $priorities = [
                'universitas sam ratulangi',
                'universitas negeri manado',
                'universitas parna raya',
                'universitas katolik de la salle',
                'universitas pembangunan indonesia',
                'universitas prisma',
                'universitas sariputra indonesia tomohon',
                'politeknik negeri manado',
                'universitas bina mandiri gorontalo',
                'universitas ichsan gorontalo',
                'universitas indonesia',
                'institut teknologi bandung',
                'universitas gadjah mada',
                'universitas telkom',
            ];

            $results = [];
            foreach ($allPt as $pt) {
                $nameLower = strtolower($pt['nama'] ?? '');
                $provLower = strtolower($pt['provinsi'] ?? '');
                if (in_array($nameLower, $priorities) || str_contains($provLower, 'sulawesi utara')) {
                    $results[] = [
                        'id' => $pt['nama'],
                        'text' => $pt['nama'],
                        'nama' => $pt['nama'],
                        'kode' => $pt['kode'] ?? '',
                        'bentuk' => $pt['bentuk'] ?? 'Universitas',
                        'kota' => $pt['kabupatenKota'] ?? 'Kota Manado',
                        'provinsi' => $pt['provinsi'] ?? 'Sulawesi Utara',
                        'kelompok' => $pt['kelompok'] ?? 'PTS',
                    ];
                    if (count($results) >= $limit) {
                        break;
                    }
                }
            }
            return response()->json($results);
        }

        $results = [];
        $words = array_filter(explode(' ', $query));

        foreach ($allPt as $pt) {
            $name = $pt['nama'] ?? '';
            $kota = $pt['kabupatenKota'] ?? '';
            $prov = $pt['provinsi'] ?? '';
            $kode = $pt['kode'] ?? '';
            $singkatan = $pt['singkatan'] ?? '';

            $searchStr = strtolower($name . ' ' . $kota . ' ' . $prov . ' ' . $kode . ' ' . $singkatan);

            $matched = true;
            foreach ($words as $w) {
                if (!str_contains($searchStr, $w)) {
                    $matched = false;
                    break;
                }
            }

            if ($matched) {
                $results[] = [
                    'id' => $name,
                    'text' => $name,
                    'nama' => $name,
                    'kode' => $kode,
                    'bentuk' => $pt['bentuk'] ?? 'Universitas',
                    'kota' => $kota,
                    'provinsi' => $prov,
                    'kelompok' => $pt['kelompok'] ?? 'PTS',
                ];
                if (count($results) >= $limit) {
                    break;
                }
            }
        }

        return response()->json($results);
    }

    /**
     * API Pencarian Master Data Mitra (Menu 02) untuk Mitra Non-PT / Swasta / Pemda / Luar Negeri
     */
    public function searchPartners(Request $request)
    {
        $query = trim((string)$request->query('q', ''));
        $category = trim((string)$request->query('category', ''));
        $limit = (int)$request->query('limit', 25);
        if ($limit < 1 || $limit > 100) $limit = 25;

        $dbQuery = Partner::query();

        if (!empty($query)) {
            $dbQuery->where(function($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('city', 'like', "%{$query}%")
                  ->orWhere('category', 'like', "%{$query}%");
            });
        }

        if (!empty($category) && stripos($category, 'semua') === false) {
            $dbQuery->where('category', $category);
        }

        $partners = $dbQuery->orderBy('name', 'asc')->limit($limit)->get();

        $results = $partners->map(function($p) {
            return [
                'id' => $p->name,
                'text' => $p->name,
                'nama' => $p->name,
                'kode' => 'ID-' . str_pad($p->id, 4, '0', STR_PAD_LEFT),
                'bentuk' => $p->category,
                'kota' => $p->city ?: 'Kota Manado',
                'provinsi' => $p->scope_coverage,
                'kelompok' => 'MITRA-UNIPAR',
                'source' => 'DATABASE_MASTER'
            ];
        });

        return response()->json($results);
    }

    /**
     * Helper untuk membuat notifikasi sistem in-app
     */
    protected function createNotification($document, string $title, string $message, string $type = 'info', string $icon = 'bi-bell-fill', string $targetRole = 'ALL'): AppNotification
    {
        return AppNotification::create([
            'document_id' => $document?->id,
            'doc_id' => $document?->doc_id,
            'title' => $title,
            'message' => $message,
            'type' => $type,
            'icon' => $icon,
            'link' => $document ? route('simkerma.index', ['menu' => 4, 'view' => 'detail', 'doc' => $document->doc_id]) : null,
            'target_role' => $targetRole,
            'is_read' => false,
        ]);
    }

    /**
     * Tandai satu notifikasi sebagai dibaca dan redirect ke dokumen terkait
     */
    public function markNotificationAsRead(Request $request, $id = null)
    {
        if ($id) {
            $notif = AppNotification::find($id);
            if ($notif) {
                $notif->update(['is_read' => true]);
                if ($notif->link) {
                    return redirect($notif->link);
                }
            }
        }
        return back();
    }

    /**
     * Tandai seluruh notifikasi sebagai telah dibaca
     */
    public function markAllNotificationsAsRead()
    {
        AppNotification::where('is_read', false)->update(['is_read' => true]);
        return back()->with('flash', [
            'type' => 'success',
            'msg' => 'Seluruh pemberitahuan sistem telah ditandai sebagai dibaca.'
        ]);
    }
}
