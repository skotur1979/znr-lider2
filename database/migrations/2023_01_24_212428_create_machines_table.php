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
        Schema::create('machines', function (Blueprint $table) {

            $table->id();

            $table->string('name', 255)->notNull();
            $table->string('manufacturer', 255)->nullable();
            $table->string('factory_number', 125)->nullable();
            $table->string('inventory_number', 125)->nullable();

            $table->date('examination_valid_from')->notNull();
            $table->date('examination_valid_until')->notNull();

            $table->string('location', 255)->notNull();
            $table->string('remark', 1024)->nullable();

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
        Schema::dropIfExists('machines');
    }
};
