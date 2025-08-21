<x-filament::card>
    <h2 class="text-lg font-bold mb-4">RA-1 Uputnice</h2>

    @if ($record->medicalReferrals->isEmpty())
        <p>Nema unesenih uputnica.</p>
    @else
        <table class="w-full text-sm table-auto">
            <thead class="bg-gray-100 dark:bg-gray-800">
                <tr>
                    <th class="p-2 text-left">Datum</th>
                    <th class="p-2 text-left">Opis poslova</th>
                    <th class="p-2 text-left">Strojevi, alati</th>
                    <th class="p-2 text-left">Mjesto rada</th>
                    <th class="p-2 text-left">Organizacija rada</th>
                    <th class="p-2 text-left">Aktivnosti</th>
                    <th class="p-2 text-left">Štetnosti</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($record->medicalReferrals as $referral)
                    <tr class="border-b border-gray-200 dark:border-gray-700">
                        <td class="p-2">{{ \Carbon\Carbon::parse($referral->date)->format('d.m.Y.') }}</td>
                        <td class="p-2">{{ $referral->job_description }}</td>
                        <td class="p-2">{{ $referral->tools }}</td>
                        <td class="p-2">{{ $referral->location_conditions }}</td>
                        <td class="p-2">{{ $referral->organization }}</td>
                        <td class="p-2">{{ $referral->activity }}</td>
                        <td class="p-2">{{ $referral->hazards }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</x-filament::card>
















