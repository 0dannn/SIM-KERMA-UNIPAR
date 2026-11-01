<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Activity extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    /**
     * Relasi ke Dokumen Kerja Sama Rujukan (Many-to-One)
     */
    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }
}
