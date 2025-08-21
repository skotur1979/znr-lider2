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
        Schema::create('fires', function (Blueprint $table) {

            $table->id();

            $table->string('place', 255)->notNull();
            $table->string('type', 255)->nullable();
            $table->string('factory_number/year_of_production', 125)->nullable();
            $table->string('serial_label_number', 125)->nullable();

            $table->date('examination_valid_from')->notNull();
            $table->date('examination_valid_until')->notNull();
            $table->string('service', 125)->nullable();
            $table->date('regular_examination_valid_from')->notNull();

            $table->string('visible', 255)->nullable();
            $table->string('remark', 1024)->nullable();
            $table->string('action', 1024)->nullable();

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
        Schema::dropIfExists('fires');
    }
};