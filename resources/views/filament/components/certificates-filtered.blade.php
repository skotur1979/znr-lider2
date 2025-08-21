@php
    use Carbon\Carbon;

    $certificates = $getRecord()->certificates;
@endphp

@if ($certificates->isEmpty())
    <span class="text-gray-400">Nema edukacija</span>
@else
    <ul class="space-y-1 text-sm">
        @foreach ($certificates as $certificate)
            @php
                $validUntil = $certificate->valid_until ? Carbon::parse($certificate->valid_until) : null;
                $validFrom = $certificate->valid_from ? Carbon::parse($certificate->valid_from)->format('d.m.Y.') : null;
                $danaDoIsteka = $validUntil ? now()->diffInDays($validUntil, false) : null;

                $stil = 'color: white;';
                $prikaz = '';

                if (! $validUntil && $validFrom) {
                    $prikaz = "{$certificate->title} – od {$validFrom}";
                } elseif ($danaDoIsteka < 0) {
                    $stil = 'color: #f87171; font-weight: 600;';
                    $prikaz = "{$certificate->title} – {$validUntil->format('d.m.Y.')} (isteklo prije " . abs($danaDoIsteka) . " dana)";
                } elseif ($danaDoIsteka <= 30) {
                    $stil = 'color: #facc15; font-weight: 600;';
                    $prikaz = "{$certificate->title} – {$validUntil->format('d.m.Y.')} (ističe za {$danaDoIsteka} dana)";
                } elseif ($validUntil) {
                    $prikaz = "{$certificate->title} – {$validUntil->format('d.m.Y.')}";
                }
            @endphp

            <li style="{{ $stil }}">
                {!! $prikaz !!}
            </li>
        @endforeach
    </ul>
@endif

















































































