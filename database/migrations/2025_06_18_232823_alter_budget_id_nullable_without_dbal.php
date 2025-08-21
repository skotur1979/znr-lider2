<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    public function up(): void
    {
        DB::statement('ALTER TABLE expenses MODIFY budget_id BIGINT UNSIGNED NULL;');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE expenses MODIFY budget_id BIGINT UNSIGNED NOT NULL;');
    }
};
