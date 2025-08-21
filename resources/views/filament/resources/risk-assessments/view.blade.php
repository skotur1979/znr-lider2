<x-filament::page>
    {{-- Podaci o procjeni --}}
    <x-filament::section>
        <x-slot name="heading">Podaci o procjeni rizika</x-slot>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
            <div><strong>Tvrtka:</strong> {{ $record->tvrtka }}</div>
            <div><strong>OIB tvrtke:</strong> {{ $record->oib_tvrtke }}</div>
            <div><strong>Adresa tvrtke:</strong> {{ $record->adresa_tvrtke }}</div>
            <div><strong>Broj procjene:</strong> {{ $record->broj_procjene }}</div>
            <div><strong>Datum izrade:</strong> {{ $record->datum_izrade?->format('d.m.Y') }}</div>
            <div><strong>Datum prihvaćanja:</strong> {{ $record->datum_prihvacanja?->format('d.m.Y') }}</div>
        </div>
    </x-filament::section>

    {{-- Sudionici --}}
    <x-filament::section class="mt-6">
        <x-slot name="heading">Sudionici izrade</x-slot>

        @forelse ($record->participants as $participant)
            <div class="border p-3 rounded mb-2 bg-gray-50 dark:bg-gray-800">
                <strong>{{ $participant->ime_prezime }}</strong> – {{ $participant->uloga }}
                @if ($participant->napomena)
                    <div class="text-sm text-gray-500 mt-1">Napomena: {{ $participant->napomena }}</div>
                @endif
            </div>
        @empty
            <p class="text-gray-500">Nema sudionika.</p>
        @endforelse
    </x-filament::section>

    {{-- Revizije --}}
    <x-filament::section class="mt-6">
        <x-slot name="heading">Revizije</x-slot>

        @forelse ($record->revisions as $rev)
            <div class="border p-3 rounded mb-2 bg-gray-50 dark:bg-gray-800">
                <strong>Revizija:</strong> {{ $rev->revizija_broj }} |
                <strong>Datum:</strong> {{ $rev->datum_izrade->format('d.m.Y') }}
            </div>
        @empty
            <p class="text-gray-500">Nema revizija.</p>
        @endforelse
    </x-filament::section>

    {{-- Prilozi --}}
    <x-filament::section class="mt-6">
        <x-slot name="heading">Prilozi</x-slot>

        @forelse ($record->attachments as $attachment)
            <div class="border p-3 rounded mb-2 bg-gray-50 dark:bg-gray-800 flex justify-between items-center">
                <div>
                    <strong>{{ $attachment->naziv }}</strong>
                </div>
                <a 
                    href="{{ Storage::url($attachment->file_path) }}" 
                    target="_blank"
                    class="text-blue-600 hover:underline"
                >Preuzmi</a>
            </div>
        @empty
            <p class="text-gray-500">Nema priloga.</p>
        @endforelse
    </x-filament::section>
</x-filament::page>





