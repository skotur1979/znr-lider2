@php
use Carbon\Carbon;

$pregled = request('tableFilters.pregled');
$items = $getRecord()->items;

if ($pregled === 'istek') {
    $items = $items->filter(fn($item) =>
        $item->end_date && Carbon::parse($item->end_date)->isBetween(now(), now()->addDays(30))
    );
}
@endphp

@if ($items->isEmpty())
    <span class="text-gray-400">-</span>
@else
    <ul class="space-y-2 text-sm">
        @foreach ($items as $item)
            <li>
                📅 {{ Carbon::parse($item->issue_date)->format('d.m.Y.') }}

                @if ($item->return_date)
                    <br>🔁 Povrat: {{ Carbon::parse($item->return_date)->format('d.m.Y.') }}
                @endif

                {{-- Uklonjeno: prikaz potpisa --}}
            </li>
        @endforeach
    </ul>
@endif















































