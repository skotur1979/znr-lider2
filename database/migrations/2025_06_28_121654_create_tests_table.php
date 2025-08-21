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
    Schema::create('tests', function (Blueprint $table) {
        $table->id();
        $table->string('naziv');
        $table->string('sifra')->unique();
        $table->text('opis')->nullable();
        $table->unsignedTinyInteger('minimalni_prolaz')->default(75); // npr. 75%
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
        Schema::dropIfExists('tests');
    }
};
