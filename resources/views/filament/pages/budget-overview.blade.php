<x-filament::page>
    <div class="space-y-6">
        <h2 class="text-xl font-bold">Troškovi po godinama i mjesecima</h2>

        @php
            $mjesečniRedoslijed = [
                'Siječanj', 'Veljača', 'Ožujak', 'Travanj', 'Svibanj', 'Lipanj',
                'Srpanj', 'Kolovoz', 'Rujan', 'Listopad', 'Studeni', 'Prosinac'
            ];
        @endphp

        @foreach ($grupiraniTroskovi as $godina => $mjeseci)
            <div class="border rounded-xl shadow p-4 bg-white dark:bg-gray-800">
                <h3 class="text-lg font-semibold mb-2">Godina: {{ $godina }}</h3>

                <table class="w-full text-sm border">
                    <thead class="bg-gray-100 dark:bg-gray-700">
                        <tr>
                            <th class="p-2 text-left">Mjesec</th>
                            <th class="p-2 text-right">Ukupno troškova (€)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($mjesečniRedoslijed as $mj)
                            @php
                                $red = $mjeseci->firstWhere('mjesec', $mj);
                            @endphp
                            @if ($red)
                                <tr class="border-t dark:border-gray-600">
                                    <td class="p-2">{{ $red->mjesec }}</td>
                                    <td class="p-2 text-right">{{ number_format($red->ukupno, 2, ',', '.') }} €</td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endforeach
    </div>
</x-filament::page>

