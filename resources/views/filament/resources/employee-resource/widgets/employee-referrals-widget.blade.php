<div class="space-y-4">
    <h2 class="text-lg font-semibold">RA-1 Uputnice</h2>

    <div class="space-y-2">
        @forelse ($this->getReferrals() as $referral)
            <div class="border p-2 rounded text-sm bg-gray-100 dark:bg-gray-800">
                <strong>Datum:</strong> {{ \Carbon\Carbon::parse($referral->date)->format('d.m.Y.') }}<br>
                <strong>Opis posla:</strong> {{ $referral->job_description }}
            </div>
        @empty
            <p class="text-sm text-gray-500 dark:text-gray-400">Nema unesenih uputnica.</p>
        @endforelse
    </div>

    <a
        href="{{ \App\Filament\Resources\MedicalReferralResource::getUrl('create', ['employee_id' => $this->record->id]) }}"
        class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-500 focus:outline-none focus:border-primary-700 focus:ring focus:ring-primary-200 active:bg-primary-600 disabled:opacity-25 transition"
    >
        Nova RA-1 Uputnica
    </a>
</div>



