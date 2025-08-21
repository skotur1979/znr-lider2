@php
    $ukupnoTroskova = $record->expenses()->sum('iznos');
    $razlika = $record->ukupni_budget - $ukupnoTroskova;
    $boja = $razlika < 0 ? 'text-red-600 font-bold' : 'text-green-600 font-semibold';
@endphp

<span class="{{ $boja }}">
    {{ number_format($razlika, 2, ',', '.') }} €
</span>
















































