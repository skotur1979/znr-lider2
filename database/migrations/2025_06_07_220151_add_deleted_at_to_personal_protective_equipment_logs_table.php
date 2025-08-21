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
    Schema::table('personal_protective_equipment_logs', function (Blueprint $table) {
        $table->softDeletes();
    });
}

public function down()
{
    Schema::table('personal_protective_equipment_logs', function (Blueprint $table) {
        $table->dropSoftDeletes();
    });
}
};