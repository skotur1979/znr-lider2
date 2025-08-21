<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('employees', function (Blueprint $t) {
            $t->string('job_title')->nullable();
            $t->string('education')->nullable();
            $t->string('place_of_birth')->nullable();
            $t->string('name_of_parents')->nullable();
        });
    }
    public function down(): void {
        Schema::table('employees', function (Blueprint $t) {
            $t->dropColumn(['job_title','education','place_of_birth','name_of_parents']);
        });
    }
};
