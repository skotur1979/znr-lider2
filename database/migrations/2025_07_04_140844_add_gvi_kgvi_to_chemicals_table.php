<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
{
    Schema::table('chemicals', function (Blueprint $table) {
        $table->string('gvi_kgvi')->nullable()->after('annual_quantity');
    });
}

public function down(): void
{
    Schema::table('chemicals', function (Blueprint $table) {
        $table->dropColumn('gvi_kgvi');
    });
}

};