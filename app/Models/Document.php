<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Document extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'date_approved' => 'date',
        'date_signed' => 'date',
    ];

    /**
     * Relasi ke Audit Trail (One-to-Many)
     */
    public function auditTrails(): HasMany
    {
        return $this->hasMany(AuditTrail::class)->orderBy('id', 'desc');
    }

    /**
     * Relasi ke Pelaksanaan Kegiatan Kemitraan (One-to-Many)
     */
    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class)->orderBy('id', 'desc');
    }

    /**
     * Relasi ke Master Data Mitra (BelongsTo)
     */
    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class, 'partner_name', 'name');
    }

    /**
     * Relasi ke Dokumen Turunan (Child Documents / Hierarki IKU 6)
     */
    public function children()
    {
        return $this->hasMany(Document::class, 'parent_mou', 'doc_number')
            ->orWhere('parent_mou', $this->doc_id);
    }
}
