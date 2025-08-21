<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('medical_referrals', function (Blueprint $table) {
            if (!Schema::hasColumn('medical_referrals', 'full_name')) {
                $table->string('full_name')->nullable();
            }

            if (!Schema::hasColumn('medical_referrals', 'job_tasks')) {
                $table->text('job_tasks')->nullable();
            }

            if (!Schema::hasColumn('medical_referrals', 'short_description')) {
                $table->text('short_description')->nullable();
            }

            if (!Schema::hasColumn('medical_referrals', 'last_exam_date')) {
                $table->date('last_exam_date')->nullable();
            }

            if (!Schema::hasColumn('medical_referrals', 'last_exam_reference')) {
                $table->string('last_exam_reference')->nullable();
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
                'full_name',
                'job_tasks',
                'short_description',
                'last_exam_date',
                'last_exam_reference',
                'exam_type',
            ]);
        });
    }
};

