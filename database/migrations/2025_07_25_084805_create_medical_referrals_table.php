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
    public function up()
    {
        Schema::create('medical_referrals', function (Blueprint $table) {
    $table->id();
    $table->foreignId('employee_id')->constrained()->onDelete('cascade');

    $table->date('date')->nullable(); // Datum uputnice
    $table->text('job_description')->nullable(); // Opis poslova
    $table->text('tools')->nullable(); // Strojevi, alati
    $table->text('location_conditions')->nullable(); // Mjesto rada
    $table->text('organization')->nullable(); // Organizacija rada
    $table->text('activity')->nullable(); // Aktivnosti
    $table->text('hazards')->nullable(); // Štetnosti
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('medical_referrals');
    }
};
