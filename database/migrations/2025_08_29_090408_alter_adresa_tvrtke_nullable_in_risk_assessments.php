<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // prilagodi tip/dužinu ako ti kolona nije VARCHAR(191)
        DB::statement("ALTER TABLE `risk_assessments` 
            MODIFY `adresa_tvrtke` VARCHAR(191) NULL");
    }

    public function down(): void
    {
        // očisti potencijalne NULL vrijednosti prije vraćanja na NOT NULL
        DB::statement("UPDATE `risk_assessments` 
            SET `adresa_tvrtke` = '' WHERE `adresa_tvrtke` IS NULL");
        DB::statement("ALTER TABLE `risk_assessments` 
            MODIFY `adresa_tvrtke` VARCHAR(191) NOT NULL");
    }
};