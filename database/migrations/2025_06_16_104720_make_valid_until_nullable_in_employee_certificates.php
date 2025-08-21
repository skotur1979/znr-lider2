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
    Schema::table('employee_certificates', function (Blueprint $table) {
        $table->dropColumn('valid_until');
    });

    Schema::table('employee_certificates', function (Blueprint $table) {
        $table->date('valid_until')->nullable();
    });
}

public function down(): void
{
    Schema::table('employee_certificates', function (Blueprint $table) {
        $table->dropColumn('valid_until');
    });

    Schema::table('employee_certificates', function (Blueprint $table) {
        $table->date('valid_until'); // NOT NULL by default
    });
}
};