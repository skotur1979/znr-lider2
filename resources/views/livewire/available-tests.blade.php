<div class="space-y-4">
    @foreach ($tests as $test)
        <x-filament::card>
            <div class="flex justify-between items-center">
                <div>
                    <div class="font-semibold">{{ $test->naziv }}</div>
                    <div class="text-sm opacity-70">
                        Minimalni prolaz:
                        {{ $test->pass_percentage ?? $test->minimalni_prolaz ?? '—' }}%
                    </div>
                </div>

                @if (in_array($test->id, $this->solvedTestIds ?? []))
                    <span class="text-green-500 font-semibold">✅ Riješeno</span>
                @else
                    {{-- koristi tvoju postojeću imenovanu rutu "testovi.pokreni" --}}
                    <x-filament::button
                        tag="a"
                        href="{{ route('testovi.pokreni', ['test' => $test->id]) }}"
                    >
                        Pokreni test
                    </x-filament::button>
                @endif
            </div>
        </x-filament::card>
    @endforeach
</div>

