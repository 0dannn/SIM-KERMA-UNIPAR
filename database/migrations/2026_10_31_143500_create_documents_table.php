<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->string('doc_id')->unique(); // e.g. UNIPAR-2026-042
            $table->string('reg_id')->unique(); // e.g. REG-UNIPAR-2026-042
            $table->string('doc_number')->nullable(); // e.g. 341/UNIPAR/MoU/X/2026
            $table->string('track'); // Jalur 1: Kemitraan Bertahap / Jalur 2: Paket Terpadu PKM
            $table->string('doc_type'); // MoU / MoA / IA / Paket Terpadu
            $table->string('parent_mou')->default('-'); // Rujukan Dokumen Induk (IKU 6)
            $table->string('unit'); // Unit Pengusul Utama
            $table->string('collab_unit')->nullable(); // Unit Kolaborator Multi-Fakultas
            $table->string('partner_name'); // Nama Mitra
            $table->string('partner_category'); // DUDI, BUMN, Pemerintah, dll.
            $table->text('scope')->nullable(); // Substansi & Ruang Lingkup
            $table->text('students_data')->nullable(); // Data Peserta PKM
            $table->string('validity')->default('1 Tahun'); // Jangka Waktu
            $table->string('warning_threshold')->default('H-90 Hari (3 Bulan)'); // Ambang Batas Peringatan
            $table->enum('status', [
                'DRAFT',
                'LEGAL_REVIEW',
                'REVISION',
                'APPROVED',
                'ACTIVE',
                'WARNING_EXPIRY',
                'EXPIRED',
                'ADDENDUM',
                'CANCELLED'
            ])->default('DRAFT');
            $table->date('date_approved')->nullable();
            $table->date('date_signed')->nullable();
            $table->string('draft_file')->nullable();
            $table->string('signed_file')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
