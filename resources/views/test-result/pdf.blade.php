<!DOCTYPE html>
<html lang="hr">
<head>
    <meta charset="UTF-8">
    <title>Rješenje testa</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        h2 { font-size: 18px; margin-bottom: 10px; }
        .meta p { margin: 2px 0; }
        .question { margin-bottom: 20px; }
        .answers { display: flex; flex-wrap: wrap; gap: 10px; }
        .answer {
            width: 180px;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 6px;
            text-align: center;
        }
        .correct { background-color: #e6ffe6; border-color: #33cc33; }
        .wrong { background-color: #ffe6e6; border-color: #cc0000; }
        .image { max-height: 100px; margin-bottom: 6px; }
    </style>
</head>
<body>
    <h2>Rješenje testa: {{ $attempt->test->naziv }}</h2>

    <div class="meta">
        <p><strong>Ime i prezime:</strong> {{ $attempt->ime_prezime }}</p>
        <p><strong>Radno mjesto:</strong> {{ $attempt->radno_mjesto }}</p>
        <p><strong>Datum rođenja:</strong> {{ \Carbon\Carbon::parse($attempt->datum_rodjenja)->format('d.m.Y.') }}</p>
        <p><strong>Bodovi:</strong> {{ $attempt->bodovi_osvojeni }}</p>
        <p><strong>Rezultat:</strong> {{ $attempt->rezultat }}%</p>
        <p><strong>Prolaz:</strong> {{ $attempt->prolaz ? 'DA' : 'NE' }}</p>
    </div>

    <hr>

    @foreach ($attempt->test->questions as $index => $question)
        <div class="question">
            <p><strong>{{ $index + 1 }}. {{ $question->tekst }}</strong></p>
            <div class="answers">
                @foreach ($question->answers as $answer)
                    @php
                        $isSelected = $attempt->odgovori->contains(fn ($odg) => $odg->answer_id === $answer->id);
                        $isCorrect = $answer->is_correct;
                        $class = '';
                        if ($isSelected && $isCorrect) $class = 'answer correct';
                        elseif ($isSelected && !$isCorrect) $class = 'answer wrong';
                        else $class = 'answer';
                    @endphp

                    <div class="{{ $class }}">
                        @if ($answer->slika_path)
                            <img class="image" src="{{ public_path('storage/' . $answer->slika_path) }}" alt="Slika">
                        @endif

                        <div>{{ $answer->tekst }}</div>

                        @if ($isSelected && $isCorrect)
                            <div>✅ Točan odgovor</div>
                        @elseif ($isSelected && !$isCorrect)
                            <div>❌ Netočan odgovor</div>
                        @elseif (!$isSelected && $isCorrect)
                            <div>(Točan, nije označen)</div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endforeach
</body>
</html>






