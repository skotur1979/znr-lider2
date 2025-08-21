<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE risk_revisions MODIFY revizija_broj VARCHAR(255) NULL');
        DB::statement('ALTER TABLE risk_revisions MODIFY datum_izrade DATE NULL');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE risk_revisions MODIFY revizija_broj VARCHAR(255) NOT NULL');
        DB::statement('ALTER TABLE risk_revisions MODIFY datum_izrade DATE NOT NULL');
    }
};
