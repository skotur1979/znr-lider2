<div class="space-y-2">
    @foreach ($record->items as $item)
        @php
            $bg = 'bg-white dark:bg-white/10'; // default ako nema datuma

            if ($item->valid_until) {
                $days = \Carbon\Carbon::now()->diffInDays($item->valid_until, false);

                if ($days < 0) {
                    $bg = 'bg-red-100 dark:bg-red-500/20';
                } elseif ($days <= 30) {
                    $bg = 'bg-yellow-100 dark:bg-yellow-500/20';
                }
            }
        @endphp

        <div class="p-2 rounded {{ $bg }}">
            <strong>{{ $item->material_type }}</strong> – {{ $item->purpose }}
            (vrijedi do: {{ \Carbon\Carbon::parse($item->valid_until)->format('d.m.Y') }})
        </div>
    @endforeach
</div>















