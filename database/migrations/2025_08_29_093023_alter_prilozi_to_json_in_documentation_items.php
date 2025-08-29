<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Ako ti je kolona drugačijeg tipa, pretvori je u JSON NULL
        DB::statement("ALTER TABLE `documentation_items` MODIFY `prilozi` JSON NULL");
    }

    public function down(): void
    {
        // Vrati na TEXT NULL ako treba
        DB::statement("ALTER TABLE `documentation_items` MODIFY `prilozi` TEXT NULL");
    }
};