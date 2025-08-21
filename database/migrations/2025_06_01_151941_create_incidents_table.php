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
    Schema::create('incidents', function (Blueprint $table) {
        $table->id();
        $table->string('location');
        $table->enum('type_of_incident', ['LTA', 'MTA', 'FAA']);
        $table->enum('permanent_or_temporary', ['Permanent', 'Temporary']);
        $table->date('date_occurred')->nullable();
        $table->date('date_of_return')->nullable();
        $table->integer('working_days_lost')->nullable();
        $table->text('causes_of_injury')->nullable();
        $table->text('accident_injury_type')->nullable();
        $table->string('injured_body_part')->nullable();
        $table->string('image_path')->nullable(); // za sliku
        $table->string('other')->nullable(); // ime i pozicija
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
        Schema::dropIfExists('incidents');
    }
};
