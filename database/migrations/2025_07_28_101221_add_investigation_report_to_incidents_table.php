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
    Schema::table('incidents', function (Blueprint $table) {
        $table->string('investigation_report')->nullable()->after('image_path');
    });
}

public function down()
{
    Schema::table('incidents', function (Blueprint $table) {
        $table->dropColumn('investigation_report');
    });
}
};