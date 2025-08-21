<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('medical_referrals', function (Blueprint $table) {
            if (!Schema::hasColumn('medical_referrals', 'hazards')) {
                $table->json('hazards')->nullable();
            }

            if (!Schema::hasColumn('medical_referrals', 'loads')) {
                $table->json('loads')->nullable();
            }

            if (!Schema::hasColumn('medical_referrals', 'workplace_location')) {
                $table->json('workplace_location')->nullable();
            }

            if (!Schema::hasColumn('medical_referrals', 'organization')) {
                $table->json('organization')->nullable();
            }

            if (!Schema::hasColumn('medical_referrals', 'body_position')) {
                $table->json('body_position')->nullable();
            }

            if (!Schema::hasColumn('medical_referrals', 'exam_type')) {
                $table->json('exam_type')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('medical_referrals', function (Blueprint $table) {
            $table->dropColumn([
                'hazards',
                'loads',
                'workplace_location',
                'organization',
                'body_position',
                'exam_type',
            ]);
        });
    }
};
