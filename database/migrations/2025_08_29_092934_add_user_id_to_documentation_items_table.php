<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('documentation_items', function (Blueprint $table) {
            if (! Schema::hasColumn('documentation_items', 'user_id')) {
                $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete()->after('id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('documentation_items', function (Blueprint $table) {
            if (Schema::hasColumn('documentation_items', 'user_id')) {
                $table->dropConstrainedForeignId('user_id');
            }
        });
    }
};