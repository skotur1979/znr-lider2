<div class="max-w-2xl mx-auto mt-6 space-y-4">
    <h1 class="text-xl font-bold">Dostupni testovi</h1>

    @forelse($tests as $test)
        <div class="border p-4 rounded shadow">
            <h2 class="text-lg font-semibold">{{ $test->naziv }}</h2>
            <p class="text-sm text-gray-500">Minimalni prolaz: {{ $test->minimalni_prolaz }}%</p>

            <a href="{{ route('testovi.pokreni', $test->id) }}" class="inline-block mt-2 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                Pokreni test
            </a>
        </div>
    @empty
        <p>Nema dostupnih testova.</p>
    @endforelse
</div>
