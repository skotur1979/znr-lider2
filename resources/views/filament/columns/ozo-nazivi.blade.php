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
    <ul class="space-y-1 text-sm">
        @foreach ($items as $item)
            <li>{{ ucfirst($item->equipment_name) }}</li>
        @endforeach
    </ul>
@endif














































