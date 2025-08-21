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
        Schema::table('chemicals', function (Blueprint $table) {
        $table->json('attachments')->nullable();
    });
}

public function down()
{
    Schema::table('chemicals', function (Blueprint $table) {
        $table->dropColumn('attachments');
    });
}
};
