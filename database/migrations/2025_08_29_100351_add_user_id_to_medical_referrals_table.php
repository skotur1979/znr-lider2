<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('medical_referrals', function (Blueprint $table) {
            if (!Schema::hasColumn('medical_referrals', 'user_id')) {
                $table->unsignedBigInteger('user_id')->nullable()->after('id');
            }
        });

        $fallbackUserId = DB::table('users')->min('id');

        if ($fallbackUserId) {
            DB::statement("
                UPDATE medical_referrals mr
                LEFT JOIN users u ON u.id = mr.user_id
                SET mr.user_id = {$fallbackUserId}
                WHERE mr.user_id IS NULL OR u.id IS NULL
            ");
        }

        Schema::table('medical_referrals', function (Blueprint $table) {
            $table->foreign('user_id', 'medical_referrals_user_id_foreign')
                ->references('id')->on('users')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('medical_referrals', function (Blueprint $table) {
            try {
                $table->dropForeign('medical_referrals_user_id_foreign');
            } catch (\Throwable $e) {}
            if (Schema::hasColumn('medical_referrals', 'user_id')) {
                $table->dropColumn('user_id');
            }
        });
    }
};
