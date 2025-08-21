@php
    $pdfs = is_array($getState()) ? $getState() : json_decode($getState(), true);
@endphp

@if ($pdfs && is_array($pdfs))
    <ul class="space-y-1">
        @foreach ($pdfs as $pdf)
            <li>
                <a href="{{ Storage::url($pdf) }}" target="_blank" class="text-primary-600 hover:underline">
                    📎 {{ basename($pdf) }}
                </a>
            </li>
        @endforeach
    </ul>
@else
    <span class="text-gray-500">Nema priloga</span>
@endif
