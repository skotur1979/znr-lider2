<div class="max-w-5xl mx-auto p-6 space-y-6">

    {{-- Force white inputs even in dark mode (and Chrome autofill) --}}
    <style>
        .force-light-input {
            background-color: #ffffff !important;
            color: #111827 !important;               /* text-gray-900 */
            border-color: #D1D5DB !important;        /* border-gray-300 */
        }
        .force-light-input::placeholder { color: #9CA3AF; } /* placeholder-gray-400 */

        /* Chrome autofill */
        .force-light-input:-webkit-autofill,
        .force-light-input:-webkit-autofill:hover,
        .force-light-input:-webkit-autofill:focus {
            -webkit-text-fill-color: #111827;
            -webkit-box-shadow: 0 0 0px 1000px #ffffff inset;
            transition: background-color 5000s ease-in-out 0s;
        }
    </style>

    {{-- Naslov --}}
    <div class="flex items-center gap-3">
        <div class="text-primary-400 text-2xl">📝</div>
        <h1 class="text-2xl font-semibold">Test: {{ $test->naziv }}</h1>
    </div>

    {{-- Poruka o grešci (ako postoji) --}}
    @if (session()->has('error'))
        <x-filament::card class="bg-gray-900/60 border-gray-800">
            <div class="text-danger-400 font-medium">
                {{ session('error') }}
            </div>
        </x-filament::card>
    @endif

    @if (! $submitted)
        {{-- Osnovni podaci --}}
        <x-filament::card class="bg-gray-900/60 border-gray-800">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="text-sm text-gray-300">Ime i prezime</label>
                    <input
                        type="text"
                        wire:model="ime_prezime"
                        class="force-light-input mt-1 w-full rounded-lg border
                               focus:ring-primary-500 focus:!border-primary-500"
                        required
                    >
                    @error('ime_prezime') <span class="text-danger-400 text-xs">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="text-sm text-gray-300">Radno mjesto</label>
                    <input
                        type="text"
                        wire:model="radno_mjesto"
                        class="force-light-input mt-1 w-full rounded-lg border
                               focus:ring-primary-500 focus:!border-primary-500"
                    >
                </div>

                <div>
                    <label class="text-sm text-gray-300">Datum rođenja</label>
                    <input
                        type="date"
                        wire:model="datum_rodjenja"
                        class="force-light-input mt-1 w-full rounded-lg border
                               focus:ring-primary-500 focus:!border-primary-500"
                    >
                </div>
            </div>
        </x-filament::card>

        {{-- Pitanja --}}
        <form wire:submit.prevent="submit" class="space-y-4">
            @foreach ($test->questions as $question)
                <x-filament::card class="bg-gray-900/60 border-gray-800">
                    <p class="font-medium mb-3">{{ $loop->iteration }}. {{ $question->tekst }}</p>

                    {{-- Slika pitanja (opcionalno) --}}
                    @if ($question->slika_path)
                        <img
                            src="{{ asset('storage/' . $question->slika_path) }}"
                            alt="Slika uz pitanje"
                            class="mb-3 max-w-sm rounded-lg border border-gray-800"
                        >
                    @endif

                    <div class="flex flex-wrap gap-6">
                        @foreach ($question->answers as $answer)
                            <label class="flex flex-col items-center gap-2 w-36">
                                @if ($question->visestruki_odgovori)
                                    <input
                                        type="checkbox"
                                        wire:model="odgovori.{{ $question->id }}"
                                        value="{{ $answer->id }}"
                                        class="rounded text-primary-500 focus:ring-primary-600 bg-gray-900 border-gray-700"
                                    >
                                @else
                                    <input
                                        type="radio"
                                        wire:model="odgovori.{{ $question->id }}"
                                        value="{{ $answer->id }}"
                                        class="rounded text-primary-500 focus:ring-primary-600 bg-gray-900 border-gray-700"
                                    >
                                @endif

                                @if ($answer->slika_path)
                                    <img
                                        src="{{ asset('storage/' . $answer->slika_path) }}"
                                        alt="Slika odgovora"
                                        class="w-32 h-32 object-contain border border-gray-800 rounded-md bg-gray-950"
                                    >
                                @endif

                                <span class="text-sm text-center text-gray-200">{{ $answer->tekst }}</span>
                            </label>
                        @endforeach
                    </div>
                </x-filament::card>
            @endforeach

            {{-- Akcije --}}
            <div class="flex items-center justify-end gap-3">
                <x-filament::button
                    tag="a"
                    color="gray"
                    href="{{ url('/available-tests-page') }}"
                >
                    ← Povratak na testove
                </x-filament::button>

                <x-filament::button type="submit" color="primary">
                    Pošalji test
                </x-filament::button>
            </div>
        </form>
    @else
        {{-- Rezultat nakon slanja --}}
        <x-filament::card class="bg-gray-900/60 border-gray-800">
            <div class="text-center space-y-3">
                <h2 class="text-2xl font-semibold">Rezultat: {{ round($rezultat, 2) }}%</h2>

                @if ($prolaz)
                    <p class="text-success-400 font-medium">🎉 Čestitamo! Test je položen.</p>
                @else
                    <p class="text-danger-400 font-medium">❌ Nažalost, test nije položen.</p>
                @endif

                <div class="pt-2">
                    <x-filament::button tag="a" color="gray" href="{{ url('/available-tests-page') }}">
                        ← Povratak na testove
                    </x-filament::button>
                </div>
            </div>
        </x-filament::card>
    @endif
</div>

