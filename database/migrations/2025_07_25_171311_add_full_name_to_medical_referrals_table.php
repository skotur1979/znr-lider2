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
        Schema::table('medical_referrals', function (Blueprint $table) {
        $table->string('full_name')->nullable();
    });
}

public function down(): void
{
    Schema::table('medical_referrals', function (Blueprint $table) {
        $table->dropColumn('full_name');
    });
}
};
