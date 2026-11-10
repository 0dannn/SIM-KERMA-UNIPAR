<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Document;
use App\Models\AuditTrail;
use App\Models\Activity;
use App\Models\Partner;
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
        $documents = Document::with(['auditTrails', 'activities'])->orderBy('id', 'desc')->get();
        
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

        // Autocomplete list untuk Dokumen Induk MoU
        $registeredMous = Document::where('doc_type', 'like', '%MoU%')
            ->orWhere('doc_number', 'like', '%MoU%')
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

            $curNum = $currentDoc->doc_number;
            $curId = $currentDoc->doc_id;
            $childDocs = Document::where('id', '!=', $currentDoc->id)
                ->where(function ($q) use ($curNum, $curId) {
                    $q->where('parent_mou', $curNum)
                      ->orWhere('parent_mou', $curId);
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
        [$newId, $regId] = $this->generateUniqueDocId();
        $docType = $request->input('doc_type', 'MoA / PKS');
        $typePrefix = ($docType === 'MoU') ? 'MoU' : (($docType === 'IA') ? 'IA' : 'MoA');
        $partnerName = $request->input('partner_name', 'Mitra Baru');
        $partnerDocNumber = $request->input('partner_doc_number');
        $rawParentMou = trim((string)$request->input('parent_mou', ''));
        $parentMou = ($docType === 'MoU' || empty($rawParentMou)) ? '-' : $rawParentMou;
        $activeRole = session('active_role', 'MASTER');

        $title = $request->input('title', 'Kerja Sama ' . $docType . ' dengan ' . $partnerName);

        $firstPartySigner = $request->input('first_party_signer');

        $secondPartySigner = $request->input('second_party_signer');
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
            $partnerDocNumber, $parentMou, $activeRole, $title,
            $firstPartySigner, $secondPartySigner, $picFirstParty,
            $picSecondParty, $startDate, $endDate, $nominalValue,
            $description, $validity, $scope, $paperSize, $warningThreshold
        ) {
            do {
                $docNumber = sprintf('%03d/UNIPAR/%s/X/2026', rand(360, 499), $typePrefix);
            } while (Document::where('doc_number', $docNumber)->exists());

            $document = Document::create([
                'doc_id' => $newId,
                'reg_id' => $regId,
                'doc_number' => $docNumber,
                'partner_doc_number' => $partnerDocNumber ?: null,
                'track' => 'Jalur 1: Kemitraan Bertahap',
                'title' => $title,
                'unit' => $request->input('unit') ?: 'Program Studi Teknik Informatika',
                'first_party_signer' => $firstPartySigner,
                'collab_unit' => null,
                'partner_name' => $partnerName,
                'partner_category' => $request->input('category') ?: 'DUDI / Swasta',
                'second_party_signer' => $secondPartySigner,
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
            Partner::firstOrCreate(
                ['name' => $partnerName],
                [
                    'category' => $request->input('category', 'DUDI / Swasta'),
                    'scope_coverage' => (stripos($partnerName, 'international') !== false || stripos($request->input('category', ''), 'luar') !== false) ? 'Internasional' : 'Dalam Negeri (Nasional/Lokal)',
                    'city' => 'Kota Manado',
                    'address' => 'Kawasan Bisnis & Industri Perwakilan Sulawesi Utara, Kota Manado',
                    'phone' => '(0431) 880-1234',
                    'email' => 'kemitraan@' . (Str::slug($partnerName) ?: 'mitra') . '.co.id',
                    'pic_name' => $picSecondParty ?: 'Narahubung Kemitraan',
                    'pic_title' => 'Divisi Kerja Sama',
                    'pic_contact' => '0812-4400-5500',
                    'profile_desc' => 'Mitra institusi terdaftar pada basis data SIMKERMA UNIPAR dengan ruang lingkup: ' . $scope,
                    'status_verification' => 'Terverifikasi Sah'
                ]
            );

            return $document;
        });

        session(['selected_doc_id' => $doc->doc_id]);

        return redirect()->route('simkerma.index', ['menu' => 4, 'view' => 'detail', 'doc' => $doc->doc_id])
            ->with('flash', [
                'type' => 'success',
                'msg' => 'Dokumen ' . $docType . ' baru ' . $doc->doc_id . ' (' . $doc->doc_number . ') berhasil dibuat dengan status DRAFT.'
            ]);
    }

    /**
     * Simpan Pengajuan Baru Jalur 2 (Paket Terpadu Tridharma & PKM)
     */
    public function storeJalur2(Request $request)
    {
        [$newId, $regId] = $this->generateUniqueDocId();
        $partnerName = $request->input('partner_name', 'Mitra PKM Terpadu');
        $partnerDocNumber = $request->input('partner_doc_number');
        $unitMain = $request->input('unit_main', 'Fakultas Ilmu Komputer');
        $unitCollab = $request->input('unit_collab', 'Fakultas Ekonomi dan Bisnis');
        $topic = $request->input('topic', 'Pelaksanaan Program PKM Kolaboratif');
        $duration = $request->input('duration', '1 Tahun');
        $activeRole = session('active_role', 'MASTER');

        $title = $request->input('title', 'Paket Terpadu PKM: ' . $topic);
        $firstPartySigner = $request->input('first_party_signer');
        $secondPartySigner = $request->input('second_party_signer');
        $picFirstParty = $request->input('pic_first_party');
        $picSecondParty = $request->input('pic_second_party');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $nominalValue = $request->input('nominal_value');
        $description = $request->input('description');

        $doc = DB::transaction(function () use (
            $request, $newId, $regId, $partnerName, $partnerDocNumber,
            $unitMain, $unitCollab, $topic, $duration, $activeRole,
            $title, $firstPartySigner, $secondPartySigner, $picFirstParty,
            $picSecondParty, $startDate, $endDate, $nominalValue, $description
        ) {
            do {
                $docNumber = sprintf('%03d/UNIPAR/PKM/X/2026', rand(360, 499));
            } while (Document::where('doc_number', $docNumber)->exists());

            $document = Document::create([
                'doc_id' => $newId,
                'reg_id' => $regId,
                'doc_number' => $docNumber,
                'partner_doc_number' => $partnerDocNumber,
                'track' => 'Jalur 2: Paket Terpadu PKM',
                'title' => $title,
                'unit' => $unitMain,
                'first_party_signer' => $firstPartySigner,
                'collab_unit' => $unitCollab,
                'partner_name' => $partnerName,
                'partner_category' => $request->input('category') ?: 'DUDI / Swasta',
                'second_party_signer' => $secondPartySigner,
                'pic_first_party' => $picFirstParty,
                'pic_second_party' => $picSecondParty,
                'doc_type' => 'Paket Terpadu (MoU + MoA + IA)',
                'paper_size' => $request->input('paper_size') ?: 'A4',
                'parent_mou' => '-',
                'scope' => 'Paket Terpadu 1 Pintu: ' . $topic,
                'description' => $description,
                'students_data' => $request->input('students_data') ?: 'Mahasiswa Peserta PKM UNIPAR',
                'validity' => ($duration ?: '1 Tahun') . ' (Siklus PKM)',
                'start_date' => $startDate ?: null,
                'end_date' => $endDate ?: null,
                'nominal_value' => $nominalValue,
                'warning_threshold' => $request->input('warning_threshold') ?: 'H-90 Hari (3 Bulan)',
                'status' => 'DRAFT',
                'draft_file' => 'paket_pkm_' . strtolower(str_replace(' ', '_', $newId)) . '.pdf',
                'notes' => 'Penyusunan berkas paket terpadu PKM (MoU, MoA, IA) oleh unit pengusul.',
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

            AuditTrail::create([
                'document_id' => $document->id,
                'actor' => 'Unit Pengusul Utama (' . $unitMain . ')',
                'role' => $activeRole,
                'event' => 'CREATE_DRAFT',
                'from_status' => '-',
                'to_status' => 'DRAFT',
                'notes' => 'Penyusunan paket terpadu Tridharma & PKM bersama ' . $partnerName . '. QR Code registrasi resmi diterbitkan dalam status DRAFT.',
                'timestamp_wita' => now()->format('Y-m-d H:i:s') . ' WITA',
            ]);

            // Sinkronisasi otomatis ke Master Data Mitra (Menu 2)
            Partner::firstOrCreate(
                ['name' => $partnerName],
                [
                    'category' => $request->input('category', 'DUDI / Swasta'),
                    'scope_coverage' => (stripos($partnerName, 'international') !== false || stripos($request->input('category', ''), 'luar') !== false) ? 'Internasional' : 'Dalam Negeri (Nasional/Lokal)',
                    'city' => 'Kota Manado',
                    'address' => 'Kawasan Bisnis & Industri Perwakilan Sulawesi Utara, Kota Manado',
                    'phone' => '(0431) 880-1234',
                    'email' => 'kemitraan@' . (Str::slug($partnerName) ?: 'mitra') . '.co.id',
                    'pic_name' => $picSecondParty ?: 'Narahubung Kemitraan',
                    'pic_title' => 'Divisi Kerja Sama',
                    'pic_contact' => '0812-4400-5500',
                    'profile_desc' => 'Mitra institusi terdaftar pada basis data SIMKERMA UNIPAR dengan ruang lingkup: ' . $topic,
                    'status_verification' => 'Terverifikasi Sah'
                ]
            );

            return $document;
        });

        session(['selected_doc_id' => $doc->doc_id]);

        return redirect()->route('simkerma.index', ['menu' => 4, 'doc' => $doc->doc_id])
            ->with('flash', [
                'type' => 'success',
                'msg' => 'Paket terpadu baru ' . $doc->doc_id . ' berhasil dibuat dengan status DRAFT. QR Code registrasi resmi aktif.'
            ]);
    }

    /**
     * Eksekusi Transisi Status FSM Formal
     */
    public function transition(Request $request)
    {
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
                $additionalData['signed_file'] = $filename;
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
     * Reset Status Dokumen ke DRAFT (Khusus Role Master)
     */
    public function resetDraft(Request $request)
    {
        $docId = $request->input('doc_id');
        $activeRole = session('active_role', 'MASTER');
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
     * Hapus Naskah Dokumen (HANYA DIIZINKAN UNTUK STATUS DRAFT)
     */
    public function deleteDraft(Request $request)
    {
        $docId = $request->input('doc_id');
        $document = Document::where('doc_id', $docId)->orWhere('id', $docId)->firstOrFail();

        // Aturan Integritas FSM: HANYA DRAFT YANG BOLEH DIHAPUS
        if ($document->status !== 'DRAFT') {
            return redirect()->route('simkerma.index', ['menu' => 4, 'doc' => $document->doc_id])
                ->with('flash', [
                    'type' => 'danger',
                    'msg' => "Penghapusan Ditolak! Dokumen {$document->doc_id} berstatus {$document->status}. Sesuai prinsip integritas FSM dan audit institusi, naskah yang sudah melewati tahap DRAFT tidak boleh dihapus dari basis data."
                ]);
        }

        $deletedDocId = $document->doc_id;
        $partnerName = $document->partner_name;

        // Bersihkan data terkait dalam transaksi aman
        DB::transaction(function () use ($document) {
            // Hapus file draft fisik jika ada
            if ($document->draft_file && file_exists(public_path('uploads/documents/' . $document->draft_file))) {
                @unlink(public_path('uploads/documents/' . $document->draft_file));
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

        return redirect()->route('simkerma.index', ['menu' => 4])
            ->with('flash', [
                'type' => 'success',
                'msg' => "Draf dokumen {$deletedDocId} bersama mitra {$partnerName} berhasil dihapus permanen dari sistem."
            ]);
    }

    /**
     * Hapus Semua Pengajuan dan Persetujuan Dokumen Kerja Sama (Fitur Mode Pengembangan)
     */
    public function resetAllDocuments(Request $request)
    {
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
        ]);

        $document = Document::findOrFail($request->input('document_id'));
        $activeRole = session('active_role', 'MASTER');

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
            'evidence_file' => $request->input('evidence_file') ?: 'laporan_kegiatan.pdf',
            'evidence_photos' => $request->input('evidence_photos'),
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
        $request->validate([
            'name' => 'required|string|max:255|unique:partners,name',
            'category' => 'required|string',
        ]);

        $name = trim($request->input('name'));
        $category = $request->input('category', 'DUDI / Swasta');
        $scopeCoverage = $request->input('scope_coverage') ?: ((stripos($name, 'international') !== false || stripos($category, 'luar') !== false) ? 'Internasional' : 'Dalam Negeri (Nasional/Lokal)');

        $partner = Partner::create([
            'name' => $name,
            'category' => $category,
            'scope_coverage' => $scopeCoverage,
            'city' => $request->input('city') ?: 'Kota Manado',
            'address' => $request->input('address') ?: 'Kawasan Bisnis & Industri Perwakilan Sulawesi Utara, Kota Manado',
            'phone' => $request->input('phone') ?: '(0431) 880-1234',
            'email' => $request->input('email') ?: ('kemitraan@' . (Str::slug($name) ?: 'mitra') . '.co.id'),
            'pic_name' => $request->input('pic_name') ?: 'Bpk/Ibu Narahubung Kemitraan',
            'pic_title' => $request->input('pic_title') ?: 'Divisi Kerja Sama & Hubungan Eksternal',
            'pic_contact' => $request->input('pic_contact') ?: '0812-4400-5500',
            'profile_desc' => $request->input('profile_desc') ?: ('Mitra institusi terdaftar pada basis data SIMKERMA UNIPAR bidang: ' . $category),
            'status_verification' => 'Terverifikasi Sah',
        ]);

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
        ]);

        $partner = Partner::findOrFail($request->input('id'));
        $oldName = $partner->name;
        $newName = trim($request->input('name'));
        $newCategory = $request->input('category');

        $partner->update([
            'name' => $newName,
            'category' => $newCategory,
            'scope_coverage' => $request->input('scope_coverage', $partner->scope_coverage),
            'city' => $request->input('city', $partner->city),
            'address' => $request->input('address', $partner->address),
            'phone' => $request->input('phone', $partner->phone),
            'email' => $request->input('email', $partner->email),
            'pic_name' => $request->input('pic_name', $partner->pic_name),
            'pic_title' => $request->input('pic_title', $partner->pic_title),
            'pic_contact' => $request->input('pic_contact', $partner->pic_contact),
            'profile_desc' => $request->input('profile_desc', $partner->profile_desc),
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
     * Perbarui Informasi Dokumen Kerja Sama dari Menu Detail
     */
    public function updateDocument(Request $request)
    {
        $docId = $request->input('doc_id');
        $document = Document::where('doc_id', $docId)->orWhere('id', $docId)->firstOrFail();

        $document->update([
            'title' => $request->input('title', $document->title),
            'unit' => $request->input('unit', $document->unit),
            'first_party_signer' => $request->input('first_party_signer', $document->first_party_signer),
            'partner_name' => $request->input('partner_name', $document->partner_name),
            'partner_category' => $request->input('partner_category', $document->partner_category),
            'second_party_signer' => $request->input('second_party_signer', $document->second_party_signer),
            'scope' => $request->input('scope', $document->scope),
            'description' => $request->input('description', $document->description),
            'start_date' => $request->input('start_date', $document->start_date),
            'end_date' => $request->input('end_date', $document->end_date),
            'validity' => $request->input('validity', $document->validity),
            'warning_threshold' => $request->input('warning_threshold', $document->warning_threshold),
        ]);

        $activeRole = session('active_role', 'ADMIN');
        AuditTrail::create([
            'document_id' => $document->id,
            'actor' => 'Pengguna (' . $activeRole . ')',
            'role' => $activeRole,
            'event' => 'UPDATE_INFO',
            'from_status' => $document->status,
            'to_status' => $document->status,
            'notes' => 'Pembaruan informasi umum dokumen kerja sama melalui menu detail.',
            'timestamp_wita' => now()->format('Y-m-d H:i:s') . ' WITA',
        ]);

        return redirect()->route('simkerma.index', ['menu' => 4, 'view' => 'detail', 'doc' => $document->doc_id])
            ->with('flash', [
                'type' => 'success',
                'msg' => 'Informasi umum dokumen ' . $document->doc_id . ' berhasil diperbarui.'
            ]);
    }

    /**
     * Unggah Berkas Dokumen (Draf Revisi atau Naskah Asli) dari Menu Detail
     */
    public function uploadDocumentFile(Request $request)
    {
        $docId = $request->input('doc_id');
        $document = Document::where('doc_id', $docId)->orWhere('id', $docId)->firstOrFail();
        $fileType = $request->input('file_type', 'draft');

        $dest = public_path('uploads/documents');
        if (!file_exists($dest)) {
            mkdir($dest, 0777, true);
        }

        if ($fileType === 'draft' && $request->hasFile('file')) {
            $file = $request->file('file');
            $ext = strtolower($file->getClientOriginalExtension());
            $filename = 'draf_' . strtolower(str_replace([' ', '/', '-'], '_', $document->doc_id)) . '_' . time() . '.' . $ext;
            $file->move($dest, $filename);
            $document->draft_file = 'uploads/documents/' . $filename;
            $document->save();
        } elseif ($fileType === 'signed' && $request->hasFile('file')) {
            $file = $request->file('file');
            $ext = strtolower($file->getClientOriginalExtension());
            $filename = 'signed_' . strtolower(str_replace([' ', '/', '-'], '_', $document->doc_id)) . '_' . time() . '.' . $ext;
            $file->move($dest, $filename);
            $document->signed_file = 'uploads/documents/' . $filename;
            $document->save();
        }

        $activeRole = session('active_role', 'ADMIN');
        AuditTrail::create([
            'document_id' => $document->id,
            'actor' => 'Pengguna (' . $activeRole . ')',
            'role' => $activeRole,
            'event' => 'UPLOAD_FILE',
            'from_status' => $document->status,
            'to_status' => $document->status,
            'notes' => 'Pengunggahan berkas ' . ($fileType === 'signed' ? 'Naskah Asli Bertandatangan' : 'Draf Revisi') . ' berhasil.',
            'timestamp_wita' => now()->format('Y-m-d H:i:s') . ' WITA',
        ]);

        return redirect()->route('simkerma.index', ['menu' => 4, 'view' => 'detail', 'doc' => $document->doc_id])
            ->with('flash', [
                'type' => 'success',
                'msg' => 'Berkas ' . ($fileType === 'signed' ? 'naskah asli' : 'draf revisi') . ' berhasil diunggah.'
            ]);
    }

    /**
     * Simpan Pencatatan Dokumen Kerja Sama Lampau (Legacy / Pra-Sistem)
     */
    public function storeLegacy(Request $request)
    {
        [$newId, $regId] = $this->generateUniqueDocId();
        $docType = $request->input('doc_type', 'MoU');
        $partnerName = $request->input('partner_name', 'Mitra Lampau');
        $docNumber = $request->input('doc_number');
        if (empty($docNumber)) {
            $prefix = ($docType === 'MoU') ? 'MoU' : (($docType === 'IA') ? 'IA' : 'MoA');
            $docNumber = sprintf('%03d/UNIPAR/%s/LEGACY/%s', rand(100, 299), $prefix, date('Y'));
        }
        $partnerDocNumber = $request->input('partner_doc_number');
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
        $firstPartySigner = $request->input('first_party_signer') ?: ($docType === 'MoU' ? 'Rektor Universitas Parna Raya' : 'Dekan Fakultas');
        $secondPartySigner = $request->input('second_party_signer') ?: 'Pimpinan Mitra Terkait';
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
                'partner_doc_number' => $partnerDocNumber ?: null,
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
            Partner::firstOrCreate(
                ['name' => $partnerName],
                [
                    'category' => $category,
                    'scope_coverage' => (stripos($partnerName, 'international') !== false || stripos($category, 'luar') !== false) ? 'Internasional' : 'Dalam Negeri (Nasional/Lokal)',
                    'city' => 'Kota Manado',
                    'address' => 'Kawasan Bisnis & Industri Perwakilan Sulawesi Utara, Kota Manado',
                    'phone' => '(0431) 880-1234',
                    'email' => 'kemitraan@' . (\Illuminate\Support\Str::slug($partnerName) ?: 'mitra') . '.co.id',
                    'pic_name' => $request->input('pic_second_party') ?: 'Narahubung Kemitraan',
                    'pic_title' => 'Divisi Kerja Sama',
                    'pic_contact' => '0812-4400-5500',
                    'profile_desc' => 'Mitra institusi terdaftar pada basis data SIMKERMA UNIPAR dengan ruang lingkup: ' . $scope,
                    'status_verification' => 'Terverifikasi Sah'
                ]
            );

            return $document;
        });

        session(['selected_doc_id' => $doc->doc_id]);

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
}
