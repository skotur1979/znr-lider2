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
    Schema::create('questions', function (Blueprint $table) {
        $table->id();
        $table->foreignId('test_id')->constrained()->onDelete('cascade');
        $table->text('tekst');
        $table->boolean('visestruki_odgovori')->default(false);
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
        Schema::dropIfExists('questions');
    }
};
