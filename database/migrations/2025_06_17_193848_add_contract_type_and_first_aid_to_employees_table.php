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
    public function up(): void
{
    Schema::table('employees', function (Blueprint $table) {
        $table->string('contract_type')->nullable();
        $table->date('first_aid_valid_until')->nullable();
    });
}

public function down(): void
{
    Schema::table('employees', function (Blueprint $table) {
        $table->dropColumn(['contract_type', 'first_aid_valid_until']);
    });
}
};