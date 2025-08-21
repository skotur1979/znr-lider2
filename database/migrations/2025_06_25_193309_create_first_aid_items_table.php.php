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
        Schema::create('first_aid_items', function (Blueprint $table) {
    $table->id();
    $table->foreignId('first_aid_kit_id')->constrained()->onDelete('cascade');
    $table->string('material_type');
    $table->string('purpose');
    $table->date('valid_until');
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
