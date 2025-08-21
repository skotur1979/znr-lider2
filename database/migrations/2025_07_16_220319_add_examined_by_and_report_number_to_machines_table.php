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
    Schema::table('machines', function (Blueprint $table) {
        $table->string('examined_by')->nullable();
        $table->string('report_number')->nullable();
    });
}

public function down()
{
    Schema::table('machines', function (Blueprint $table) {
        $table->dropColumn(['examined_by', 'report_number']);
    });
}

};
