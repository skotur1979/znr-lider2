<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // 1) Dodaj nullable kolonu (bez FK da ne pukne)
        Schema::table('first_aid_kits', function (Blueprint $table) {
            if (! Schema::hasColumn('first_aid_kits', 'user_id')) {
                $table->unsignedBigInteger('user_id')->nullable()->after('id');
            }
        });

        // 2) Backfill – uzmi nekog admina (ili prvog usera ako admina nema)
        $adminId = DB::table('users')->where('role', 'admin')->value('id')
            ?? DB::table('users')->where('is_admin', 1)->value('id')
            ?? DB::table('users')->orderBy('id')->value('id');

        if ($adminId) {
            DB::table('first_aid_kits')
                ->whereNull('user_id')
                ->update(['user_id' => $adminId]);
        }

        // 3) Sada sigurno možemo dodati FK (kolona može ostati nullable ili je
        //    možeš “stegnuti” u NOT NULL ako imaš doctrine/dbal i želiš ->change()).
        Schema::table('first_aid_kits', function (Blueprint $table) {
            // Ako već postoji FK, preskoči
            $table->foreign('user_id')
                  ->references('id')->on('users')
                  ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('first_aid_kits', function (Blueprint $table) {
            // skini FK ako postoji pa drop-aj kolonu
            try { $table->dropForeign(['user_id']); } catch (\Throwable $e) {}
            if (Schema::hasColumn('first_aid_kits', 'user_id')) {
                $table->dropColumn('user_id');
            }
        });
    }
};