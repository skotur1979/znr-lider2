@php
    use Carbon\Carbon;
    $certificates = $getRecord()->certificates_all;
@endphp

@if ($certificates->isEmpty())
    <span class="text-gray-400">Nema edukacija</span>
@else
    <ul class="space-y-1">
        @foreach ($certificates as $certificate)
            @php
                $validUntil = Carbon::parse($certificate->valid_until);
                $style = match ($certificate->highlight ?? 'white') {
                    'red' => 'color: red; font-weight: bold;',
                    'gold' => 'color: gold; font-weight: bold;',
                    default => 'color: white;',
                };
            @endphp

            <li style="{{ $style }}">
                {{ $certificate->title }} – {{ $validUntil->format('d.m.Y.') }}
            </li>
        @endforeach
    </ul>
@endif

















