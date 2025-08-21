<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Test;
use App\Models\TestAttempt;
use App\Models\AttemptAnswer;
use Illuminate\Support\Facades\Auth;

class TestForm extends Component
{
    public Test $test;

    public string $ime_prezime = '';
    public string $radno_mjesto = '';
    public $datum_rodjenja = '';
    public array $odgovori = [];

    public bool $submitted = false;
    public ?float $rezultat = null;
    public bool $prolaz = false;

    public function mount(Test $test)
    {
        $this->test = $test->load('questions.answers');
    }

    public function submit()
    {
        $this->validate([
            'ime_prezime' => 'required|string',
            'radno_mjesto' => 'nullable|string',
            'datum_rodjenja' => 'nullable|date',
        ]);

        try {
            $bodovi = 0;
            $ukupnoPitanja = $this->test->questions->count();

            foreach ($this->test->questions as $pitanje) {
                $točni = $pitanje->answers->where('is_correct', true)->pluck('id')->sort()->values();
                $odabrani = collect($this->odgovori[$pitanje->id] ?? [])->map(fn ($i) => (int) $i)->sort()->values();

                if ($točni->count() > 0 && $točni->values()->all() === $odabrani->values()->all()) {
                    $bodovi++;
                }
            }

            $postotak = $ukupnoPitanja > 0 ? ($bodovi / $ukupnoPitanja) * 100 : 0;
            $prolaz = $postotak >= $this->test->minimalni_prolaz;

            $attempt = TestAttempt::create([
                'test_id' => $this->test->id,
                'user_id' => Auth::check() ? Auth::id() : null,
                'ime_prezime' => $this->ime_prezime,
                'radno_mjesto' => $this->radno_mjesto,
                'datum_rodjenja' => $this->datum_rodjenja,
                'bodovi_osvojeni' => $bodovi,
                'rezultat' => $postotak,
                'prolaz' => $prolaz,
            ]);

            foreach ($this->odgovori as $questionId => $answerIds) {
                foreach ((array) $answerIds as $answerId) {
                    AttemptAnswer::create([
                        'test_attempt_id' => $attempt->id,
                        'question_id' => $questionId,
                        'answer_id' => $answerId,
                    ]);
                }
            }

            $this->rezultat = $postotak;
            $this->prolaz = $prolaz;
            $this->submitted = true;
        } catch (\Throwable $e) {
            session()->flash('error', 'Greška: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.test-form')->extends('layouts.app');
    }
}