<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

return new class extends Migration {
    public function up(): void
    {
        // Za svaki expense koji nema postavljen budget_id
        DB::table('expenses')
            ->whereNull('budget_id')
            ->orderBy('id')
            ->chunk(100, function ($expenses) {
                foreach ($expenses as $expense) {
                    $year = Carbon::parse($expense->created_at)->year;

                    $budget = DB::table('budgets')
                        ->where('godina', $year)
                        ->first();

                    if ($budget) {
                        DB::table('expenses')
                            ->where('id', $expense->id)
                            ->update(['budget_id' => $budget->id]);
                    }
                }
            });
    }

    public function down(): void
    {
        // Povratno možeš očistiti budget_id ako želiš
        DB::table('expenses')->update(['budget_id' => null]);
    }
};

