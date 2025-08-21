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
        Schema::create('risk_participants', function (Blueprint $table) {
    $table->id();
    $table->foreignId('risk_assessment_id')->constrained()->onDelete('cascade');
    $table->string('ime_prezime');
    $table->string('uloga');
    $table->text('napomena')->nullable();
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
