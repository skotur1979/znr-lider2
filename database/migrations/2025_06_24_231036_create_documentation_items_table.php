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
    Schema::create('documentation_items', function (Blueprint $table) {
        $table->id();
        $table->string('naziv');
        $table->string('tvrtka')->nullable();
        $table->date('datum_izrade')->nullable();
        $table->string('status_napomena')->nullable();
        $table->json('prilozi')->nullable();
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
        Schema::dropIfExists('documentation_items');
    }
};
