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
        Schema::create('miscellaneouses', function (Blueprint $table) {

            $table->id();

            $table->string('name', 512)->notNull();
            $table->string('examiner', 256)->nullable();
            $table->integer('category_id')->notNull();
            $table->string('report_number')->nullable();

            $table->date('examination_valid_from')->notNull();
            $table->date('examination_valid_until')->notNull();

            $table->string('remark',1024)->nullable();

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
        Schema::dropIfExists('miscellaneouses');
    }
};
