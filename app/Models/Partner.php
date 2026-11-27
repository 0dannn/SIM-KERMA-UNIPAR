<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Partner extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    /**
     * Daftar kategori resmi mitra kerja sama (WF-02 & kompatibilitas master DB).
     */
    public const OFFICIAL_CATEGORIES = [
        'Perguruan Tinggi Dalam Negeri' => 'Perguruan Tinggi Dalam Negeri',
        'Perguruan Tinggi Luar Negeri' => 'Perguruan Tinggi Luar Negeri',
        'Pemerintah / Instansi Daerah' => 'Instansi Pemerintah / Pemda',
        'DUDI / Swasta' => 'Industri / DUDI / Swasta',
        'BUMN / BUMD' => 'BUMN / BUMD',
        'Rumah Sakit / Fasilitas Kesehatan' => 'Rumah Sakit / Fasilitas Kesehatan',
        'Sekolah / Madrasah' => 'Sekolah / Madrasah',
        'Lembaga Penelitian / Riset' => 'Lembaga Penelitian / Riset',
        'Organisasi Profesi / Asosiasi' => 'Organisasi Profesi / Asosiasi',
        'Organisasi / Komunitas / Lainnya' => 'Organisasi / Komunitas / Lainnya',
    ];

    /**
     * Kelompok kategori untuk dropdown filter Menu 2 (mencakup 7 sektor resmi WF-02).
     */
    public const FILTER_CATEGORY_GROUPS = [
        'Perguruan Tinggi',
        'Pemerintah',
        'Industri / DUDI',
        'BUMN / BUMD',
        'Rumah Sakit',
        'Sekolah',
        'Lembaga Penelitian',
        'Organisasi / Lainnya',
    ];

    /**
     * Status verifikasi kelengkapan profil mitra (WF-02 Poin 6).
     */
    public const VERIFICATION_STATUSES = [
        'Terverifikasi Sah',
        'Belum Diverifikasi',
        'Perlu Pembaruan',
    ];

    /**
     * Placeholder legacy yang tidak dihitung sebagai data kontak riil.
     */
    private const PLACEHOLDER_VALUES = [
        '(0431) 880-1234',
        '0812-4400-5500',
        'bpk/ibu narahubung kemitraan',
        'narahubung kemitraan',
        'kawasan bisnis & industri perwakilan sulawesi utara, kota manado',
    ];

    /**
     * Relasi ke seluruh dokumen kerja sama terkait (One-to-Many)
     */
    public function documents(): HasMany
    {
        return $this->hasMany(Document::class, 'partner_name', 'name')->orderBy('id', 'desc');
    }

    /**
     * Memetakan kategori DB spesifik ke kelompok kategori resmi WF-02 untuk filter seragam.
     */
    public static function normalizeCategoryGroup(?string $category): string
    {
        $val = strtolower(trim((string) $category));
        return match (true) {
            str_contains($val, 'perguruan tinggi') => 'Perguruan Tinggi',
            str_contains($val, 'pemerintah') || str_contains($val, 'pemda') => 'Pemerintah',
            str_contains($val, 'bumn') || str_contains($val, 'bumd') => 'BUMN / BUMD',
            str_contains($val, 'dudi') || str_contains($val, 'swasta') || str_contains($val, 'industri') => 'Industri / DUDI',
            str_contains($val, 'rumah sakit') || str_contains($val, 'kesehatan') || str_contains($val, 'puskesmas') => 'Rumah Sakit',
            str_contains($val, 'sekolah') || str_contains($val, 'madrasah') || str_contains($val, 'smk') || str_contains($val, 'sma') => 'Sekolah',
            str_contains($val, 'penelitian') || str_contains($val, 'riset') || str_contains($val, 'brin') => 'Lembaga Penelitian',
            default => 'Organisasi / Lainnya',
        };
    }

    /**
     * Memetakan kelompok kategori resmi ke kelas warna badge & ikon (Menu 2).
     */
    public function getCategoryBadgeMetaAttribute(): array
    {
        return match (self::normalizeCategoryGroup($this->category)) {
            'Perguruan Tinggi' => ['class' => 'badge-cat-pt', 'icon' => 'bi-mortarboard-fill'],
            'Pemerintah' => ['class' => 'badge-cat-gov', 'icon' => 'bi-bank'],
            'Industri / DUDI', 'BUMN / BUMD' => ['class' => 'badge-cat-ind', 'icon' => 'bi-building-gear'],
            'Rumah Sakit' => ['class' => 'badge-cat-eduhealth', 'icon' => 'bi-hospital'],
            'Sekolah' => ['class' => 'badge-cat-eduhealth', 'icon' => 'bi-book'],
            'Lembaga Penelitian' => ['class' => 'badge-cat-eduhealth', 'icon' => 'bi-microscope'],
            default => ['class' => 'badge-cat-org', 'icon' => 'bi-people-fill'],
        };
    }

    /**
     * Menghitung skor kelengkapan profil mitra (Alamat, Kota, Telp, Email, Nama PIC, Kontak PIC).
     */
    public function getCompletenessInfoAttribute(): array
    {
        $checks = [
            'Kota' => $this->city,
            'Alamat' => $this->address,
            'Telepon' => $this->phone,
            'Email' => $this->email,
            'Nama PIC' => $this->pic_name,
            'Kontak PIC' => $this->pic_contact,
        ];

        $filled = 0;
        $missing = [];
        foreach ($checks as $label => $raw) {
            $clean = trim((string) $raw);
            $isPlaceholder = $clean === '' || in_array(strtolower($clean), self::PLACEHOLDER_VALUES, true);
            if ($isPlaceholder) {
                $missing[] = $label;
            } else {
                $filled++;
            }
        }

        $total = count($checks);
        $percent = (int) round(($filled / $total) * 100);
        if ($percent >= 85) {
            $statusLabel = 'Lengkap';
            $badgeClass = 'badge-soft-success border-success-subtle';
        } elseif ($percent >= 50) {
            $statusLabel = 'Cukup';
            $badgeClass = 'badge-soft-warning border-warning-subtle';
        } else {
            $statusLabel = 'Perlu Dilengkapi';
            $badgeClass = 'badge-soft-danger border-danger-subtle';
        }

        return [
            'filled' => $filled,
            'total' => $total,
            'percent' => $percent,
            'label' => $statusLabel,
            'badge_class' => $badgeClass,
            'missing' => $missing,
        ];
    }

    /**
     * Sinkronisasi atau pembuatan entri mitra otomatis saat dokumen diajukan dari Menu 4.
     */
    public static function syncFromDocument(
        string $partnerName,
        ?string $category,
        ?string $picName = null,
        ?string $scopeDesc = null
    ): self {
        $cat = trim((string) ($category ?: 'DUDI / Swasta'));
        $isIntl = stripos($partnerName, 'international') !== false || stripos($cat, 'luar') !== false;

        return self::firstOrCreate(
            ['name' => $partnerName],
            [
                'category' => $cat,
                'scope_coverage' => $isIntl ? 'Internasional' : 'Dalam Negeri (Nasional/Lokal)',
                'pic_name' => trim((string) $picName) !== '' ? trim((string) $picName) : null,
                'profile_desc' => $scopeDesc ? ('Mitra institusi kerja sama dengan fokus: ' . $scopeDesc) : null,
                'status_verification' => 'Belum Diverifikasi',
            ]
        );
    }
}
