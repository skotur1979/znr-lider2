<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('personal_protective_equipment_logs', function (Blueprint $table) {
            $table->id();
            $table->string('user_last_name');
            $table->string('user_oib');
            $table->string('workplace')->nullable();
            $table->string('organization_unit')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('personal_protective_equipment_logs');
    }
};