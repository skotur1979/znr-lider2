<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $defaultBudgetId = DB::table('budgets')->value('id') ?? 1;

        Schema::table('expenses', function (Blueprint $table) use ($defaultBudgetId) {
            DB::statement("ALTER TABLE expenses ALTER COLUMN budget_id SET DEFAULT $defaultBudgetId");
        });

        DB::table('expenses')
            ->whereNull('budget_id')
            ->update(['budget_id' => $defaultBudgetId]);
    }

    public function down(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            DB::statement("ALTER TABLE expenses ALTER COLUMN budget_id DROP DEFAULT");
        });
    }
};
