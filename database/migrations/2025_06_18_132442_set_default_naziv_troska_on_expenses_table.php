<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Ako koristiš MySQL
        DB::statement("ALTER TABLE expenses MODIFY COLUMN naziv_troska VARCHAR(255) NOT NULL DEFAULT 'Nedefinirano'");
    }

    public function down(): void
    {
        // Vrati natrag bez defaulta (ako je potrebno)
        DB::statement("ALTER TABLE expenses MODIFY COLUMN naziv_troska VARCHAR(255) NOT NULL");
    }
};
