<div class="flex justify-end mb-4 space-x-2">
    @foreach ($actions as $action)
        {!! $action->render() !!}
    @endforeach
</div>

<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mt-4">
    <div class="rounded p-4 text-center border dark:border-blue-500 bg-white dark:bg-gray-900 h-24 flex flex-col justify-center">
        <div class="text-sm text-gray-500 dark:text-gray-300 font-bold uppercase">Godina</div>
        <div class="text-xl font-bold text-gray-700 dark:text-white">{{ $selectedYear }}</div>
    </div>

    <div class="rounded p-4 text-center bg-white dark:bg-gray-800 shadow h-24 flex flex-col justify-center">
        <div class="text-sm text-gray-500 dark:text-gray-300 font-bold uppercase">Ukupno</div>
        <div class="text-3xl font-bold text-black dark:text-white">{{ $ukupno }}</div>
    </div>

    <div class="rounded p-4 text-center bg-white dark:bg-gray-800 shadow h-24 flex flex-col justify-center">
        <div class="text-sm text-gray-500 dark:text-gray-300 font-bold uppercase">LTA</div>
        <div class="text-3xl font-bold text-red-600 dark:text-red-400">{{ $lta }}</div>
    </div>

    <div class="rounded p-4 text-center bg-white dark:bg-gray-800 shadow h-24 flex flex-col justify-center">
        <div class="text-sm text-gray-500 dark:text-gray-300 font-bold uppercase">MTA</div>
        <div class="text-3xl font-bold text-yellow-600 dark:text-yellow-400">{{ $mta }}</div>
    </div>

    <div class="rounded p-4 text-center bg-white dark:bg-gray-800 shadow h-24 flex flex-col justify-center">
        <div class="text-sm text-gray-500 dark:text-gray-300 font-bold uppercase">FAA</div>
        <div class="text-3xl font-bold text-green-600 dark:text-green-400">{{ $faa }}</div>
    </div>

    @if(!is_null($daysSinceLastLta))
    <div class="rounded p-4 text-center bg-white dark:bg-gray-800 shadow h-32 flex flex-col justify-center ring-1 ring-emerald-500/30">
        <div class="text-base md:text-lg text-gray-700 dark:text-gray-200 font-bold uppercase tracking-wide">
            Dana bez ozljede (LTA)
        </div>

        {{-- FORSIRANA VELIČINA BROJA u px da nadjača sve stilove --}}
        <div class="font-extrabold text-emerald-600 dark:text-emerald-400 leading-none animate-pulse"
             style="font-size: 30px; line-height: 1;">
            {{ $daysSinceLastLta }}
        </div>

        <div class="text-[9px] md:text-[10px] mt-1 text-gray-500 dark:text-gray-400">
            od {{ $lastLtaAt->translatedFormat('j. M Y') }}
        </div>
    </div>
@endif
</div>
