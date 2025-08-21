<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    
    public function up(): void
    {
        Schema::table('fires', function (Blueprint $table) {
            $table->json('pdf')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('fires', function (Blueprint $table) {
            $table->dropColumn('pdf');
        });
    }
};