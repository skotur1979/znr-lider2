<?php

namespace App\Livewire;

use App\Models\Test;
use App\Models\TestAttempt;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Livewire\Component;

class AvailableTests extends Component
{
    public array $solvedTestIds = [];
    public $tests;

    public function mount(): void
    {
        $user = Auth::user();

        // Svi testovi – SORTIRAJ po 'naziv' (ne 'name')
        $this->tests = Test::query()
            ->when(Schema::hasColumn('tests', 'naziv'), fn($q) => $q->orderBy('naziv'))
            ->get();

        // ID-evi testova koje je user riješio (admin = svi koji imaju attempt)
        $this->solvedTestIds = $user->isAdmin()
            ? TestAttempt::pluck('test_id')->unique()->toArray()
            : TestAttempt::where('user_id', $user->id)->pluck('test_id')->unique()->toArray();
    }

    public function render()
    {
        return view('livewire.available-tests');
    }
}
