<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('personal_protective_equipment_items', function (Blueprint $table) {
            // Prvo ukloni foreign key constraint
            $table->dropForeign('personal_protective_equipment_items_log_id_foreign');

            // Zatim obriši sam stupac
            $table->dropColumn('log_id');
        });
    }

    public function down(): void
    {
        Schema::table('personal_protective_equipment_items', function (Blueprint $table) {
            // Vrati stupac ako radiš rollback
            $table->unsignedBigInteger('log_id')->nullable();

            // I ponovno dodaj foreign key ako trebaš
            $table->foreign('log_id')->references('id')->on('personal_protective_equipment_logs')->onDelete('cascade');
        });
    }
};
