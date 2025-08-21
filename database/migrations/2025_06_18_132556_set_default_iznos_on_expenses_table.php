<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Postavi default 0 za 'iznos'
        DB::statement("ALTER TABLE expenses MODIFY COLUMN iznos DECIMAL(10,2) NOT NULL DEFAULT 0");
    }

    public function down(): void
    {
        // Ukloni default ako želiš vratiti na prijašnje stanje
        DB::statement("ALTER TABLE expenses MODIFY COLUMN iznos DECIMAL(10,2) NOT NULL");
    }
};
