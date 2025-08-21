@php
    $ukupno = $getRecord()->items->count();
    $danas = now();
    $uskoro = $getRecord()->items->filter(fn ($item) =>
        $item->valid_until &&
        \Carbon\Carbon::parse($item->valid_until)->isAfter($danas) &&
        \Carbon\Carbon::parse($item->valid_until)->diffInDays($danas) <= 30
    )->count();

    $istekli = $getRecord()->items->filter(fn ($item) =>
        $item->valid_until &&
        \Carbon\Carbon::parse($item->valid_until)->isBefore($danas)
    )->count();
@endphp

<div class="text-center leading-tight space-y-0.5">
    <div class="text-base font-semibold text-white">{{ $ukupno }}</div>

    @if ($uskoro > 0)
        <div class="text-sm text-yellow-400">🟡 {{ $uskoro }} uskoro</div>
    @endif

    @if ($istekli > 0)
        <div class="text-sm text-red-500">🔴 {{ $istekli }} isteklo</div>
    @endif
</div>














