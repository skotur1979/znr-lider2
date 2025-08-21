<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Test;

class AvailableTests extends Component
{
    public function render()
    {
        $tests = Test::all(); // Dodaj ->where('active', true) ako koristiš aktivne testove

        return view('livewire.available-tests', compact('tests'));
    }
}
