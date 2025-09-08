<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Test;
use App\Models\TestAttempt;
use App\Models\AttemptAnswer;
use App\Models\Employee;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class TestForm extends Component
{
    public Test $test;

    public string $ime_prezime = '';
    public string $radno_mjesto = '';
    public $datum_rodjenja = '';   // 'Y-m-d' ili prazno
    public array $odgovori = [];

    public bool $submitted = false;
    public ?float $rezultat = null;
    public bool $prolaz = false;

    public function mount(Test $test): void
    {
        $this->test = $test->load('questions.answers');

        if (Auth::check()) {
            $employee = Employee::where('user_id', Auth::id())->first();

            if ($employee) {
                $this->ime_prezime    = (string) ($employee->ime_prezime ?? '');
                $this->radno_mjesto   = (string) ($employee->radno_mjesto ?? '');
                $this->datum_rodjenja = $employee->datum_rodjenja
                    ? Carbon::parse($employee->datum_rodjenja)->format('Y-m-d')
                    : '';
            } else {
                // Fallback: ako nema Employee zapisa, pokušaj s korisničkim imenom
                $this->ime_prezime = (string) (Auth::user()->name ?? '');
            }
        }
    }

    public function submit(): void
    {
        $this->validate([
            'ime_prezime'    => 'required|string',
            'radno_mjesto'   => 'nullable|string',
            'datum_rodjenja' => 'nullable|date',
        ]);

        try {
            $bodovi = 0;
            $ukupnoPitanja = $this->test->questions->count();

            foreach ($this->test->questions as $pitanje) {
                $tocni = $pitanje->answers
                    ->where('is_correct', true)
                    ->pluck('id')
                    ->sort()
                    ->values();

                $odabrani = collect($this->odgovori[$pitanje->id] ?? [])
                    ->map(fn ($i) => (int) $i)
                    ->sort()
                    ->values();

                if ($tocni->count() > 0 && $tocni->values()->all() === $odabrani->values()->all()) {
                    $bodovi++;
                }
            }

            $postotak = $ukupnoPitanja > 0 ? ($bodovi / $ukupnoPitanja) * 100 : 0.0;
            $prolaz = $postotak >= (float) $this->test->minimalni_prolaz;

            $attempt = TestAttempt::create([
                'test_id'        => $this->test->id,
                'user_id'        => Auth::check() ? Auth::id() : null,
                'ime_prezime'    => $this->ime_prezime,
                'radno_mjesto'   => $this->radno_mjesto,
                'datum_rodjenja' => $this->datum_rodjenja ?: null,
                'bodovi_osvojeni'=> $bodovi,
                'rezultat'       => $postotak,
                'prolaz'         => $prolaz,
            ]);

            foreach ($this->odgovori as $questionId => $answerIds) {
                foreach ((array) $answerIds as $answerId) {
                    AttemptAnswer::create([
                        'test_attempt_id' => $attempt->id,
                        'question_id'     => (int) $questionId,
                        'answer_id'       => (int) $answerId,
                    ]);
                }
            }

            $this->rezultat  = $postotak;
            $this->prolaz    = $prolaz;
            $this->submitted = true;
        } catch (\Throwable $e) {
            session()->flash('error', 'Greška: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.test-form')
            ->layout('filament::components.layouts.app', [
                'title'       => 'Test: ' . ($this->test->naziv ?? 'Test'),
                'breadcrumbs' => [], // sprječava grešku s breadcrumbovima
            ]);
    }
}
