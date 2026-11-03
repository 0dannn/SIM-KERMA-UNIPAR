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
            $table->string('title')->nullable()->after('partner_name');
            $table->string('first_party_signer')->nullable()->after('unit');
            $table->string('second_party_signer')->nullable()->after('partner_category');
            $table->string('pic_first_party')->nullable()->after('first_party_signer');
            $table->string('pic_second_party')->nullable()->after('second_party_signer');
            $table->date('start_date')->nullable()->after('validity');
            $table->date('end_date')->nullable()->after('start_date');
            $table->string('nominal_value')->nullable()->after('end_date');
            $table->text('description')->nullable()->after('scope');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropColumn([
                'title',
                'first_party_signer',
                'second_party_signer',
                'pic_first_party',
                'pic_second_party',
                'start_date',
                'end_date',
                'nominal_value',
                'description',
            ]);
        });
    }
};
