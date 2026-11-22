<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Document;
use App\Models\Partner;
use App\Models\Activity;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    /**
     * Tampilkan Halaman Utama (Homepage / Portal Publik Kemitraan)
     * Sinkron 100% dengan data sistem internal SIM-KERMA UNIPAR
     */
    public function index(Request $request)
    {
        // Backward-compatibility: jika ada parameter menu/view/doc, arahkan ke aplikasi internal SIMKERMA
        if ($request->has('menu') || $request->has('view') || $request->has('doc') || $request->has('action')) {
            return redirect()->route('simkerma.index', $request->all());
        }

        // 1. STATISTIK DOKUMEN (SINKRON DENGAN MENU 4 & MENU 7)
        $totalDocs = Document::count();
        $activeDocs = Document::where('status', 'ACTIVE')->count();
        $warningDocs = Document::where('status', 'WARNING')->count();
        $reviewDocs = Document::where('status', 'REVIEW')->count();
        $verifiedDocs = Document::where('status', 'VERIFIED')->count();
        $draftDocs = Document::where('status', 'DRAFT')->count();
        $expiredDocs = Document::whereIn('status', ['EXPIRED', 'CANCELLED'])->count();

        // 2. STATISTIK JENIS NASKAH KERJA SAMA
        $mouCount = Document::where('doc_type', 'like', '%MoU%')->count();
        $moaCount = Document::where('doc_type', 'like', '%MoA%')->count();
        $iaCount = Document::where('doc_type', 'like', '%IA%')->count();

        // 3. STATISTIK MITRA (SINKRON DENGAN MENU 1 & MASTER DATA)
        $totalPartners = Partner::count();
        $nationalPartners = Partner::where(function ($q) {
            $q->where('scope_coverage', 'like', '%Dalam Negeri%')
              ->orWhere('scope_coverage', 'like', '%Nasional%')
              ->orWhere('scope_coverage', 'like', '%Lokal%');
        })->count();
        
        $internationalPartners = Partner::where(function ($q) {
            $q->where('scope_coverage', 'like', '%Luar Negeri%')
              ->orWhere('scope_coverage', 'like', '%Internasional%');
        })->count();

        // Kategori Mitra untuk Grafik Batang
        $partnerCategories = Partner::select('category', DB::raw('count(*) as total'))
            ->groupBy('category')
            ->orderByDesc('total')
            ->get();

        // Dokumen per Unit Pengusul / Fakultas untuk Grafik Horizontal
        $docsByUnit = Document::select('unit', DB::raw('count(*) as total'))
            ->groupBy('unit')
            ->orderByDesc('total')
            ->take(6)
            ->get();

        // Tren Dokumen per Tahun
        $docsByYear = Document::select(DB::raw('COALESCE(YEAR(date_signed), YEAR(created_at), 2026) as doc_year'), DB::raw('count(*) as total'))
            ->groupBy('doc_year')
            ->orderBy('doc_year', 'asc')
            ->get();

        // 4. STATISTIK KEGIATAN & IKU 6 (SINKRON DENGAN MENU 5)
        $totalActivities = Activity::count();
        $runningActivities = Activity::where('execution_status', 'SEDANG_BERJALAN')->count();
        $completedActivities = Activity::where('execution_status', 'SELESAI')->count();
        $pendingActivities = Activity::whereIn('execution_status', ['BELUM_DIMULAI', 'TERTUNDA'])->count();

        // 5. DIREKTORI DOKUMEN PUBLIK
        $recentDocuments = Document::orderBy('id', 'desc')->take(12)->get();

        // 6. MITRA TERHUBUNG UNGGULAN
        $featuredPartners = Partner::withCount('documents')
            ->orderByDesc('documents_count')
            ->take(8)
            ->get();

        // Data Siap Pakai untuk ApexCharts (JSON format)
        $chartTypeLabels = ['Nota Kesepahaman (MoU)', 'Perjanjian Kerja Sama (MoA)', 'Implementation Arrangement (IA)'];
        $chartTypeSeries = [$mouCount, $moaCount, $iaCount];

        $chartStatusLabels = ['Aktif Sah', 'Dalam Telaah (Review)', 'Disetujui Pimpinan', 'Kedaluwarsa / Selesai'];
        $chartStatusSeries = [$activeDocs, $reviewDocs, $verifiedDocs, $expiredDocs];

        $chartCategoryLabels = $partnerCategories->pluck('category')->toArray();
        $chartCategorySeries = $partnerCategories->pluck('total')->toArray();

        $chartUnitLabels = $docsByUnit->pluck('unit')->toArray();
        $chartUnitSeries = $docsByUnit->pluck('total')->toArray();

        return view('home', compact(
            'totalDocs',
            'activeDocs',
            'warningDocs',
            'reviewDocs',
            'verifiedDocs',
            'draftDocs',
            'expiredDocs',
            'mouCount',
            'moaCount',
            'iaCount',
            'totalPartners',
            'nationalPartners',
            'internationalPartners',
            'partnerCategories',
            'docsByUnit',
            'docsByYear',
            'totalActivities',
            'runningActivities',
            'completedActivities',
            'pendingActivities',
            'recentDocuments',
            'featuredPartners',
            'chartTypeLabels',
            'chartTypeSeries',
            'chartStatusLabels',
            'chartStatusSeries',
            'chartCategoryLabels',
            'chartCategorySeries',
            'chartUnitLabels',
            'chartUnitSeries'
        ));
    }
}
