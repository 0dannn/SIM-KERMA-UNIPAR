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
     * Mengadopsi tata letak & visualisasi standar SIKERMA UNJ (https://sikerma.unj.ac.id/)
     * Menampilkan:
     * 1. Statistik ringkas & sederhana hanya MoU, MoA, dan IA.
     * 2. Grafik dokumen berdasarkan Unit Pengusul, Klasifikasi Mitra, dan Kewarganegaraan Mitra.
     * 3. Daftar tabel dokumen kerja sama universitas interaktif lengkap dengan modal detail.
     */
    public function index(Request $request)
    {
        // Backward-compatibility: jika ada parameter menu/view/doc, arahkan ke aplikasi internal SIMKERMA
        if ($request->has('menu') || $request->has('view') || $request->has('doc') || $request->has('action')) {
            return redirect()->route('simkerma.index', $request->all());
        }

        // 1. STATISTIK DOKUMEN KERJA SAMA (SEDERHANA: HANYA MoU, MoA, IA - SEPERTI SIKERMA UNJ)
        $mouCount = Document::where('doc_type', 'like', '%MoU%')->count();
        $moaCount = Document::where('doc_type', 'like', '%MoA%')->count();
        $iaCount = Document::where('doc_type', 'like', '%IA%')->count();
        $totalDocs = Document::count();

        // 2. GRAFIK KOMPOSISI DOKUMEN (DONUT CHART APEXCHARTS)
        $chartTypeLabels = ['MoU', 'MoA', 'IA'];
        $chartTypeSeries = [$mouCount, $moaCount, $iaCount];

        // 3. GRAFIK DISTRIBUSI DOKUMEN KERJA SAMA PER TAHUN (5 TAHUN TERAKHIR)
        $currentYear = (int)date('Y');
        $yearsRange = range($currentYear - 4, $currentYear); // e.g. 2022 s.d. 2026

        $mouPerYear = [];
        $moaPerYear = [];
        $iaPerYear = [];

        foreach ($yearsRange as $yr) {
            $mouPerYear[] = Document::where('doc_type', 'like', '%MoU%')
                ->where(function ($q) use ($yr) {
                    $q->whereYear('start_date', $yr)
                      ->orWhereYear('date_signed', $yr)
                      ->orWhereYear('created_at', $yr);
                })->count();

            $moaPerYear[] = Document::where('doc_type', 'like', '%MoA%')
                ->where(function ($q) use ($yr) {
                    $q->whereYear('start_date', $yr)
                      ->orWhereYear('date_signed', $yr)
                      ->orWhereYear('created_at', $yr);
                })->count();

            $iaPerYear[] = Document::where('doc_type', 'like', '%IA%')
                ->where(function ($q) use ($yr) {
                    $q->whereYear('start_date', $yr)
                      ->orWhereYear('date_signed', $yr)
                      ->orWhereYear('created_at', $yr);
                })->count();
        }

        $docsPerYearSeries = [
            ['name' => 'MoU', 'data' => $mouPerYear],
            ['name' => 'MoA', 'data' => $moaPerYear],
            ['name' => 'IA', 'data' => $iaPerYear],
        ];

        // 4. STATISTIK DOKUMEN BERDASARKAN TAHUN (INTERAKTIF: UNIT, KLASIFIKASI, KEWARGANEGARAAN)
        // Ambil daftar tahun unik dari basis data
        $dbYears = Document::select(DB::raw('DISTINCT COALESCE(YEAR(start_date), YEAR(date_signed), YEAR(created_at), 2026) as yr'))
            ->orderByDesc('yr')
            ->pluck('yr')
            ->map(fn($y) => (int)$y)
            ->filter()
            ->unique()
            ->values()
            ->toArray();

        $availableYears = array_unique(array_merge($dbYears, $yearsRange));
        rsort($availableYears);

        // Helper closure untuk membentuk dataset per unit, klasifikasi mitra, dan kewarganegaraan
        $buildYearStats = function ($targetYear = 'all') {
            $query = Document::query();
            if ($targetYear !== 'all') {
                $query->where(function ($q) use ($targetYear) {
                    $q->whereYear('start_date', $targetYear)
                      ->orWhereYear('date_signed', $targetYear)
                      ->orWhereYear('created_at', $targetYear);
                });
            }

            $docs = $query->get();

            // A. Dokumen berdasarkan Unit Pengusul / Fakultas
            $unitsCount = [];
            foreach ($docs as $d) {
                $u = $d->unit ?: 'Lainnya / Universitas';
                $unitsCount[$u] = ($unitsCount[$u] ?? 0) + 1;
            }
            arsort($unitsCount);
            $topUnits = array_slice($unitsCount, 0, 8, true);
            $unitLabels = array_keys($topUnits);
            $unitSeries = array_values($topUnits);

            // B. Dokumen berdasarkan Klasifikasi Mitra
            $catCount = [];
            foreach ($docs as $d) {
                $c = $d->partner_category ?: 'DUDI / Swasta';
                $catCount[$c] = ($catCount[$c] ?? 0) + 1;
            }
            arsort($catCount);
            $catLabels = array_keys($catCount);
            $catSeries = array_values($catCount);

            // C. Dokumen berdasarkan Kewarganegaraan Mitra (Dalam Negeri vs Luar Negeri)
            $natCount = [
                'Dalam Negeri (Indonesia)' => 0,
                'Luar Negeri (Internasional)' => 0,
            ];
            foreach ($docs as $d) {
                $pName = strtolower($d->partner_name ?? '');
                $pCat = strtolower($d->partner_category ?? '');
                if (str_contains($pName, 'international') || str_contains($pCat, 'luar negeri') || str_contains($pCat, 'international')) {
                    $natCount['Luar Negeri (Internasional)']++;
                } else {
                    $natCount['Dalam Negeri (Indonesia)']++;
                }
            }
            $natLabels = array_keys($natCount);
            $natSeries = array_values($natCount);

            return [
                'units' => ['labels' => $unitLabels, 'series' => $unitSeries],
                'categories' => ['labels' => $catLabels, 'series' => $catSeries],
                'nationalities' => ['labels' => $natLabels, 'series' => $natSeries],
            ];
        };

        $interactiveStats = [
            'all' => $buildYearStats('all')
        ];
        foreach ($availableYears as $yr) {
            $interactiveStats[(string)$yr] = $buildYearStats($yr);
        }

        // 5. DAFTAR DOKUMEN KERJA SAMA LENGKAP (UNTUK TABEL SIKERMA)
        $allDocuments = Document::orderBy('id', 'desc')->get();

        return view('home', compact(
            'mouCount',
            'moaCount',
            'iaCount',
            'totalDocs',
            'chartTypeLabels',
            'chartTypeSeries',
            'yearsRange',
            'docsPerYearSeries',
            'availableYears',
            'interactiveStats',
            'allDocuments'
        ));
    }
}
