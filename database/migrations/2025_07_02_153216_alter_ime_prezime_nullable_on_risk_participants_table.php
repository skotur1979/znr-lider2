<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterImePrezimeNullableOnRiskParticipantsTable extends Migration
{
    public function up()
    {
        Schema::table('risk_participants', function (Blueprint $table) {
            // Prvo makni NOT NULL ograničenje (ručno preko raw SQL jer ne koristiš Doctrine)
            DB::statement('ALTER TABLE risk_participants MODIFY ime_prezime VARCHAR(255) NULL');
        });
    }

    public function down()
    {
        Schema::table('risk_participants', function (Blueprint $table) {
            // Vrati natrag kao NOT NULL ako bude trebalo
            DB::statement('ALTER TABLE risk_participants MODIFY ime_prezime VARCHAR(255) NOT NULL');
        });
    }
}