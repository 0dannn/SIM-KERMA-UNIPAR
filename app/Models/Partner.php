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
     * Relasi ke seluruh dokumen kerja sama terkait (One-to-Many)
     */
    public function documents(): HasMany
    {
        return $this->hasMany(Document::class, 'partner_name', 'name')->orderBy('id', 'desc');
    }
}
