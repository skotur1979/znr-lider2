<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Ovo pretpostavlja da koristiš MySQL
        DB::statement('ALTER TABLE risk_participants MODIFY ime_prezime VARCHAR(255) NULL');
        DB::statement('ALTER TABLE risk_participants MODIFY uloga VARCHAR(255) NULL');
        DB::statement('ALTER TABLE risk_participants MODIFY napomena TEXT NULL');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE risk_participants MODIFY ime_prezime VARCHAR(255) NOT NULL');
        DB::statement('ALTER TABLE risk_participants MODIFY uloga VARCHAR(255) NOT NULL');
        DB::statement('ALTER TABLE risk_participants MODIFY napomena TEXT NOT NULL');
    }
};

