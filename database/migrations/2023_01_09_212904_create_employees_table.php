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
        Schema::create('employees', function (Blueprint $table) {

            $table->id();

            $table->string('name')->notNull();
            $table->string('address', 512)->nullable();
            $table->string('OIB')->nullable();
            $table->string('phone')->nullable();
            $table->string('workplace')->nullable();
            $table->date('employeed_at')->nullable();

            // Liječnički pregled (od-do)
            // notNull
            $table->date('medical_examination_valid_from')->nullable();
            $table->date('medical_examination_valid_until')->nullable();
            // Članak zakona
            $table->string('article', 256)->nullable();

            // Napomena
            // nullable
            $table->string('remark', 1000)->nullable();

            // Zaštita na radu (polaganje) od-do
            // notNull
            $table->date('occupational_safety_valid_from')->nullable();

            // Zaštita od požara (ZOP - polaganje) od-do
            // notNull
            $table->date('fire_protection_valid_from')->nullable();
            $table->date('fire_protection_statement_at')->nullable();
            $table->date('evacuation_valid_from')->nullable();

            // Osposobljen za prvu pomoć (+ datum osposobljavanja)
            // nullable
            $table->date('first_aid_valid_from')->nullable();

            // Toksikologija (polaganje) + datumi od - do
            // nullable
            $table->date('toxicology_valid_from')->nullable();
            $table->date('toxicology_valid_until')->nullable();

            // Rukovanje sa zapaljivim materijalima (polaganje) + datumi od - do
            // nullable
            $table->date('handling_flammable_materials_valid_from')->nullable();
            $table->date('handling_flammable_materials_valid_until')->nullable();

            // Ovlaštenik poslodavca vrijedi do
            $table->date('employers_authorization_valid_from')->nullable();
            $table->date('employers_authorization_valid_until')->nullable();

            // Timestamps
            $table->softDeletes();
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
        Schema::dropIfExists('employees');
    }
};
