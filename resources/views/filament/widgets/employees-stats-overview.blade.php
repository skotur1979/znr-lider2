<x-filament::widget>
    <x-filament::card>
        <div class="grid grid-cols-5 gap-4">
            @foreach (['column1', 'column2', 'column3', 'column4', 'column5'] as $column)
                <div class="space-y-4">
                    @foreach ($$column as $card)
                        {{ $card }}
                    @endforeach
                </div>
            @endforeach
        </div>
    </x-filament::card>
</x-filament::widget>
