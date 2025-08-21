<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1) Drop FK ako postoji (bez DBAL-a je ok)
        Schema::table('medical_referrals', function (Blueprint $table) {
            try {
                $table->dropForeign(['employee_id']);
            } catch (\Throwable $e) {
                // ignoriraj ako ne postoji
            }
        });

        // 2) Promijeni stupac na NULL preko raw SQL-a (nema DBAL-a)
        DB::statement('ALTER TABLE `medical_referrals` MODIFY `employee_id` BIGINT UNSIGNED NULL');

        // 3) (Opcionalno) vrati FK s ON DELETE SET NULL
        Schema::table('medical_referrals', function (Blueprint $table) {
            try {
                $table->foreign('employee_id')
                    ->references('id')->on('employees')
                    ->nullOnDelete();
            } catch (\Throwable $e) {
                // ako već postoji ili nešto — preskoči
            }
        });
    }

    public function down(): void
    {
        // 1) Drop FK
        Schema::table('medical_referrals', function (Blueprint $table) {
            try {
                $table->dropForeign(['employee_id']);
            } catch (\Throwable $e) {
            }
        });

        // 2) Vrati NOT NULL (PAZI: ovo padne ako imaš NULL vrijednosti!)
        DB::statement('ALTER TABLE `medical_referrals` MODIFY `employee_id` BIGINT UNSIGNED NOT NULL');

        // (po želji) ponovno dodaš FK s restrict/cascade
        Schema::table('medical_referrals', function (Blueprint $table) {
            try {
                $table->foreign('employee_id')
                    ->references('id')->on('employees')
                    ->restrictOnDelete();
            } catch (\Throwable $e) {
            }
        });
    }
};