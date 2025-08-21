<h2 class="text-xl font-bold">Godina: {{ $godina }}</h2>

<div class="grid grid-cols-3 gap-4 mb-4">
    <div class="bg-gray-800 p-4 rounded">
        <strong>Ukupno troškova:</strong><br>
        {{ number_format($ukupnoTroskova, 2, ',', '.') }} €
    </div>
    <div class="bg-gray-800 p-4 rounded">
        <strong>Budžet:</strong><br>
        {{ number_format($ukupniBudget, 2, ',', '.') }} €
    </div>
    <div class="bg-gray-800 p-4 rounded">
        <strong>Preostalo:</strong><br>
        {{ number_format($razlika, 2, ',', '.') }} €
    </div>
</div>

@if ($grupiraniTroskovi->count())
    <h3 class="text-lg font-semibold">Troškovi po mjesecima</h3>
    @foreach ($grupiraniTroskovi as $mjesec)
        <p>{{ $mjesec->mjesec }}: {{ number_format($mjesec->ukupno, 2, ',', '.') }} €</p>
    @endforeach
@endif
