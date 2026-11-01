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
        Schema::create('audit_trails', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained('documents')->onDelete('cascade');
            $table->string('actor');
            $table->string('role'); // UNIT, LEGAL, LEADER, SYSTEM, MASTER
            $table->string('event'); // CREATE_DRAFT, SUBMIT_LEGAL_REVIEW, APPROVE_LEGAL, etc.
            $table->string('from_status');
            $table->string('to_status');
            $table->text('notes')->nullable();
            $table->string('timestamp_wita');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_trails');
    }
};
