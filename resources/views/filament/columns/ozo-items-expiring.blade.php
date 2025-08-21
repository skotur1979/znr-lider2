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
            @php
                $end = $item->end_date ? Carbon::parse($item->end_date) : null;
                $danaDoIsteka = $end ? now()->diffInDays($end, false) : null;

                $stil = 'color: inherit;';
                if ($danaDoIsteka < 0) {
                    $stil = 'color: #f87171; font-weight: 600;';
                } elseif ($danaDoIsteka <= 30) {
                    $stil = 'color: #facc15; font-weight: 600;';
                }
            @endphp

            @if ($end)
                <li style="{{ $stil }}">
                    {{ $end->format('d.m.Y.') }}
                    <span class="text-sm">
                        (za {{ $danaDoIsteka }} dana)
                    </span>
                </li>
            @endif
        @endforeach
    </ul>
@endif
















































