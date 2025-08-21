@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto py-6">

    <h2 class="text-2xl font-bold mb-4">Rješenje testa: {{ $attempt->test->naziv }}</h2>

    <div class="mb-4 space-y-1">
        <p><strong>Ime i prezime:</strong> {{ $attempt->ime_prezime }}</p>
        <p><strong>Radno mjesto:</strong> {{ $attempt->radno_mjesto }}</p>
        <p><strong>Datum rođenja:</strong> {{ \Carbon\Carbon::parse($attempt->datum_rodjenja)->format('d.m.Y.') }}</p>
        <p><strong>Bodovi:</strong> {{ $attempt->bodovi_osvojeni }}</p>
        <p><strong>Rezultat:</strong> {{ $attempt->rezultat }}%</p>
        <p><strong>Prolaz:</strong>
            @if($attempt->prolaz)
                <span class="text-green-600 font-semibold">DA</span>
            @else
                <span class="text-red-600 font-semibold">NE</span>
            @endif
        </p>
    </div>

    @foreach ($attempt->test->questions as $index => $question)
        <div class="mb-6 p-4 border rounded-md bg-white shadow">
            <h4 class="font-semibold mb-3">{{ $index + 1 }}. {{ $question->tekst }}</h4>

            <div class="flex flex-wrap gap-4">
                @foreach ($question->answers as $answer)
                    @php
                        $isSelected = $attempt->odgovori->contains(fn ($odg) => $odg->answer_id === $answer->id);
                        $isCorrect = $answer->is_correct;
                    @endphp

                    <label class="flex flex-col items-center p-3 border rounded-md w-40 text-center
                        @if ($isSelected && $isCorrect) border-green-500 bg-green-100
                        @elseif ($isSelected && !$isCorrect) border-red-500 bg-red-100
                        @else border-gray-300 bg-gray-50 @endif
                    ">
                        <input type="radio" disabled {{ $isSelected ? 'checked' : '' }} class="mb-2">

                        @if ($answer->slika_path)
                            <img src="{{ asset('storage/' . $answer->slika_path) }}" class="h-24 object-contain mb-2">
                        @endif

                        <div>{{ $answer->tekst }}</div>

                        @if ($isSelected && $isCorrect)
                            <span class="text-green-600 mt-1">✅ Točan odgovor</span>
                        @elseif ($isSelected && !$isCorrect)
                            <span class="text-red-600 mt-1">❌ Netočan odgovor</span>
                        @elseif (!$isSelected && $isCorrect)
                            <span class="text-green-600 mt-1">(Točan, nije označen)</span>
                        @endif
                    </label>
                @endforeach
            </div>
        </div>
    @endforeach

    <div class="mt-8 text-center flex flex-col items-center space-y-4">
        <a href="{{ url()->previous() }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded inline-block">
            ← Natrag na listu
        </a>
    <a href="{{ route('test-attempts.download', $attempt) }}" class="btn btn-primary">📄 Preuzmi PDF</a>
</div>

        </a>
    </div>
</div>
@endsection




