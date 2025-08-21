<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('medical_referrals', function (Blueprint $table) {
            $table->string('referral_number')->nullable(); // Broj
            $table->date('referral_date')->nullable();     // Datum
            $table->string('employer_oib')->nullable();    // OIB poslodavca
            $table->string('work_years_in_job')->nullable(); // Staž na poslovima
        });
    }

    public function down(): void
    {
        Schema::table('medical_referrals', function (Blueprint $table) {
            $table->dropColumn([
                'referral_number',
                'referral_date',
                'employer_oib',
                'total_years',
                'work_years_in_job',
            ]);
        });
    }
};
