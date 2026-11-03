<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Ubah tipe kolom status menjadi VARCHAR(50) agar mendukung status VERIFIED dan seluruh status FSM
        DB::statement("ALTER TABLE documents MODIFY COLUMN status VARCHAR(50) NOT NULL DEFAULT 'DRAFT'");

        // 2. Selaraskan data status arsip naskah agar konsisten dengan model 7 status FSM
        DB::table('documents')->where('status', 'APPROVED')->update(['status' => 'VERIFIED']);
        DB::table('documents')->where('status', 'LEGAL_REVIEW')->update(['status' => 'REVIEW']);
        DB::table('documents')->where('status', 'WARNING_EXPIRY')->update(['status' => 'WARNING']);
        DB::table('documents')->where('status', 'ADDENDUM')->update(['status' => 'WARNING']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE documents MODIFY COLUMN status VARCHAR(50) NOT NULL DEFAULT 'DRAFT'");
    }
};
