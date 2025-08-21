@php
    $realizirano = $getRecord()->expenses->where('realizirano', true)->sum('iznos');
    $budget = $getRecord()->ukupni_budget;
    $razlika = $budget - $realizirano;
@endphp

<span class="{{ $razlika < 0 ? 'text-red-600' : 'text-green-600' }}">
    {{ number_format($razlika, 2, ',', '.') }} €
</span>
















































