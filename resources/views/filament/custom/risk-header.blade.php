<div class="flex items-right justify-between">
    <!-- Naslov + tooltip -->
    <div class="flex items-center gap-2 relative" x-data="{ open: false }">
        <h1 class="text-2xl font-bold text-white">Procjene Rizika</h1>

        <!-- Ikonica (slovo "i" u krugu) -->
        <div
            class="w-6 h-6 flex items-center justify-right rounded-full bg-blue-600 text-white text-sm font-bold cursor-pointer relative"
            @mouseenter="open = true"
            @mouseleave="open = false"
        >
            i
            <!-- Tooltip -->
            <div
                x-show="open"
                x-transition
                class="absolute right-full top-1/2 -translate-y-1/2 ml-3 z-50 bg-gray-900 text-white text-xs px-3 py-2 rounded shadow-lg max-w-[240px] whitespace-normal text-left"
                style="display: none;"
            >
                Dodaju se podaci o izrađenim procjenama rizika i revizijama!
            </div>
        </div>
    </div>

    <!-- Gumb -->
    <x-filament::button
        tag="a"
        href="{{ route('filament.resources.risk-assessments.create') }}"
        color="primary"
    >
        Nova Procjena rizika
    </x-filament::button>
</div>















