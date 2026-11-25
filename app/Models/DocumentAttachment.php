<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentAttachment extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    /**
     * Relasi balik ke Dokumen Kerja Sama induk
     */
    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }

    /**
     * Label kategori yang ramah pengguna
     */
    public function getCategoryLabelAttribute(): string
    {
        return match ($this->category) {
            'PROPOSAL' => 'Proposal Kerja Sama',
            'RENCANA_KEGIATAN' => 'Rencana Kegiatan',
            'DOKUMEN_PENDUKUNG' => 'Dokumen Pendukung',
            'LAPORAN_PELAKSANAAN' => 'Laporan Pelaksanaan',
            default => $this->category,
        };
    }
}
