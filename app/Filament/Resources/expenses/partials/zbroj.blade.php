<x-filament::section>
    <div class="space-y-6">
        <div class="bg-white dark:bg-gray-800 shadow rounded-xl p-6">
            <h2 class="text-lg font-bold mb-4">Troškovi po mjesecima (Godina: {{ $godina }})</h2>

            <table class="w-full text-sm border">
                <thead class="bg-gray-100 dark:bg-gray-700">
                    <tr>
                        <th class="p-2 text-left">Mjesec</th>
                        <th class="p-2 text-right">Ukupno troškova (€)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach (($grupiraniTroskovi[$godina] ?? []) as $item)
                        <tr class="border-t dark:border-gray-600">
                            <td class="p-2">{{ $item->mjesec }}</td>
                            <td class="p-2 text-right">{{ number_format($item->ukupno, 2, ',', '.') }} €</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="mt-6 text-sm">
                <p><strong>Ukupno troškova:</strong> {{ number_format($ukupnoTroskova, 2, ',', '.') }} €</p>
                <p><strong>Stanje budžeta:</strong> {{ number_format($ukupniBudget, 2, ',', '.') }} €</p>
                <p>
                    <strong>Preostalo:</strong>
                    <span class="{{ $razlika < 0 ? 'text-red-500 font-bold' : 'text-green-500 font-semibold' }}">
                        {{ number_format($razlika, 2, ',', '.') }} €
                    </span>
                </p>
            </div>
        </div>
    </div>
</x-filament::section>







