<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Ako već postoji kolona, obriši je
        if (Schema::hasColumn('questions', 'visestruki_odgovori')) {
            Schema::table('questions', function (Blueprint $table) {
                $table->dropColumn('visestruki_odgovori');
            });
        }

        // Ponovno dodaj s točnim tipom (npr. boolean, default false)
        Schema::table('questions', function (Blueprint $table) {
            $table->boolean('visestruki_odgovori')->default(false);
        });
    }

    public function down(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->dropColumn('visestruki_odgovori');
        });
    }
};