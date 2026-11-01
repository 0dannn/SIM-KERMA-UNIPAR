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
        Schema::table('documents', function (Blueprint $table) {
            if (!Schema::hasColumn('documents', 'docx_path')) {
                $table->string('docx_path')->nullable()->after('signed_file');
            }
            if (!Schema::hasColumn('documents', 'qr_code_path')) {
                $table->string('qr_code_path')->nullable()->after('docx_path');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropColumn(['docx_path', 'qr_code_path']);
        });
    }
};
