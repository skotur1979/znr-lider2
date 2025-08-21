<div class="max-w-4xl mx-auto px-6 py-8 bg-white shadow rounded-lg space-y-8">
    <h1 class="text-3xl font-bold text-center text-gray-800">📝 Test: {{ $test->naziv }}</h1>

    @if (session()->has('error'))
        <div class="bg-red-100 text-red-800 px-4 py-2 rounded border border-red-300 text-center">
            {{ session('error') }}
        </div>
    @endif

    @if (!$submitted)
        <form wire:submit.prevent="submit" class="space-y-6">

            {{-- Osnovni podaci --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Ime i prezime</label>
                    <input type="text" wire:model="ime_prezime" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                    @error('ime_prezime') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Radno mjesto</label>
                    <input type="text" wire:model="radno_mjesto" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Datum rođenja</label>
                    <input type="date" wire:model="datum_rodjenja" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>

            {{-- Pitanja --}}
            <div class="space-y-6">
                @foreach ($test->questions as $question)
                    <div class="border border-gray-300 rounded-md p-4 bg-gray-50 shadow-sm">
                        <p class="font-semibold text-lg text-gray-800 mb-3">
                            {{ $loop->iteration }}. {{ $question->tekst }}
                        </p>

                        {{-- Slika pitanja ako postoji --}}
                        @if ($question->slika_path)
                            <div class="mb-3">
                                <img src="{{ asset('storage/' . $question->slika_path) }}" alt="Slika uz pitanje" class="max-w-xs rounded shadow">
                            </div>
                        @endif

                        <div class="flex flex-wrap gap-6">
    @foreach ($question->answers as $answer)
        <label class="flex flex-col items-center space-y-2 w-36">
            @if ($question->visestruki_odgovori)
                <input type="checkbox"
                       wire:model="odgovori.{{ $question->id }}"
                       value="{{ $answer->id }}"
                       class="rounded text-blue-600">
            @else
                <input type="radio"
                       wire:model="odgovori.{{ $question->id }}"
                       value="{{ $answer->id }}"
                       class="rounded text-blue-600">
            @endif

            @if ($answer->slika_path)
                <img src="{{ asset('storage/' . $answer->slika_path) }}"
                     alt="Slika odgovora"
                     class="w-32 h-32 object-contain border border-gray-300 rounded">
            @endif

            <span class="text-sm text-center">{{ $answer->tekst }}</span>
        </label>
    @endforeach
</div>

                    </div>
                @endforeach
            </div>

            {{-- Gumb za slanje --}}
            <div class="text-center">
                <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-2 rounded shadow">
                    ✅ Pošalji test
                </button>
            </div>
        </form>
    @else
        {{-- Prikaz rezultata --}}
        <div class="bg-green-50 border border-green-200 rounded-lg p-6 text-center shadow">
            <h2 class="text-2xl font-bold text-green-700 mb-4">Rezultat: {{ round($rezultat, 2) }}%</h2>

            @if ($prolaz)
                <p class="text-green-600 font-semibold text-lg">🎉 Čestitamo! Test je položen.</p>
            @else
                <p class="text-red-600 font-semibold text-lg">❌ Nažalost, test nije položen.</p>
            @endif

            <div class="mt-6">
                <a href="{{ url('/available-tests-page') }}"
                   class="inline-block bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded">
                    ← Povratak na testove
                </a>
            </div>
        </div>
    @endif
</div>






