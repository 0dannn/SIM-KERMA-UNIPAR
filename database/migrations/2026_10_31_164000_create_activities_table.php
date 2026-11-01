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
        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained('documents')->onDelete('cascade');
            $table->string('activity_name'); // Nama Kegiatan Pelaksanaan
            $table->string('schedule_start')->nullable(); // Tanggal Mulai
            $table->string('schedule_end')->nullable(); // Tanggal Selesai
            $table->string('target_qty')->nullable(); // Target Capaian (Misal: 20 Mahasiswa)
            $table->string('realized_qty')->nullable(); // Realisasi Capaian (Misal: 18 Mahasiswa)
            $table->text('parties_involved')->nullable(); // Mahasiswa, Dosen, & Praktisi Mitra
            $table->text('outputs')->nullable(); // Luaran IKU 6: Laporan, Sertifikat, Prototype
            $table->string('budget_info')->nullable(); // Kontribusi Mitra / Biaya Operasional
            $table->string('evidence_file')->nullable(); // Berkas Laporan PDF
            $table->string('evidence_photos')->nullable(); // Foto / Dokumentasi Lapangan
            $table->string('execution_status')->default('SEDANG_BERJALAN'); // BELUM_DIMULAI, SEDANG_BERJALAN, SELESAI, TERTUNDA
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activities');
    }
};
