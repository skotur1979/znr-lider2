@php
    use Carbon\Carbon;
    $certificates = $getRecord()->certificates_expiring;
@endphp

@if ($certificates->isEmpty())
    <span class="text-gray-400">Nema uskoro ističućih edukacija</span>
@else
    <ul class="space-y-1">
        @foreach ($certificates as $certificate)
            <li style="color: gold; font-weight: bold;">
                {{ $certificate->title }} – {{ \Carbon\Carbon::parse($certificate->valid_until)->format('d.m.Y.') }}
            </li>
        @endforeach
    </ul>
@endif







































































