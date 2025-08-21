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
        Schema::create('chemicals', function (Blueprint $table) {
    $table->id();
    $table->string('product_name');
    $table->string('cas_number')->nullable();
    $table->string('ufi_number')->nullable();
    $table->json('hazard_pictograms')->nullable(); // više piktograma
    $table->text('h_statements')->nullable();
    $table->text('p_statements')->nullable();
    $table->string('usage_location')->nullable();
    $table->string('annual_quantity')->nullable();
    $table->date('stl_hzjz')->nullable();
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
        Schema::dropIfExists('chemicals');
    }
};
