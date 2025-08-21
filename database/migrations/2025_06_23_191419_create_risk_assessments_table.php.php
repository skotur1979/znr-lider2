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
    Schema::create('risk_assessments', function (Blueprint $table) {
    $table->id();
    $table->string('tvrtka');
    $table->string('oib_tvrtke');
    $table->string('adresa_tvrtke');
    $table->string('broj_procjene');
    $table->date('datum_izrade');
    $table->date('datum_prihvacanja')->nullable();
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
        //
    }
};
