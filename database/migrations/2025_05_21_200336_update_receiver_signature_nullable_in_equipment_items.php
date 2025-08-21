<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('personal_protective_equipment_items', function (Blueprint $table) {
            // Prvo obriši postojeći stupac
            $table->dropColumn('receiver_signature');
        });

        Schema::table('personal_protective_equipment_items', function (Blueprint $table) {
            // Zatim ga ponovno dodaj kao nullable
            $table->text('receiver_signature')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('personal_protective_equipment_items', function (Blueprint $table) {
            // Vrati ga kao NOT NULL ako se migracija vrati unazad
            $table->dropColumn('receiver_signature');
            $table->text('receiver_signature'); // bez nullable
        });
    }
};

