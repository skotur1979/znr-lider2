@php
use Carbon\Carbon;

$pregled = request('tableFilters.pregled.value');
$items = $getRecord()->certificates;

if ($pregled === 'istekle') {
    $items = $items->filter(fn($item) =>
        $item->valid_until && Carbon::parse($item->valid_until)->isBefore(now())
    );
} elseif ($pregled === 'uskoro') {
    $items = $items->filter(fn($item) =>
        $item->valid_until && Carbon::parse($item->valid_until)->isBetween(now(), now()->addDays(30))
    );
}
@endphp

@if ($items->isEmpty())
    <span class="text-gray-400">Nema edukacija</span>
@else
    <ul style="list-style: none; padding-left: 0;">
        @foreach ($items as $certificate)
            @php
                $validUntil = $certificate->valid_until ? Carbon::parse($certificate->valid_until) : null;
                $daysLeft = $validUntil?->diffInDays(now(), false);
                $style = 'color: white;';

                if ($daysLeft < 0) {
                    $style = 'color: red; font-weight: bold;';
                } elseif ($daysLeft <= 30) {
                    $style = 'color: gold; font-weight: bold;';
                }
            @endphp

            <li style="{{ $style }}">
                {{ $certificate->title }} – {{ $validUntil?->format('d.m.Y.') }}
                <span class="text-sm">
                    (za {{ $daysLeft }} dana)
                </span>
            </li>
        @endforeach
    </ul>
@endif








































































