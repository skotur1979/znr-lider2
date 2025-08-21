<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE first_aid_items MODIFY valid_until DATE NULL');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE first_aid_items MODIFY valid_until DATE NOT NULL');
    }
};