<!DOCTYPE html>
<html lang="hr">
<head>
    <meta charset="UTF-8">
    <title>RA-1 Uputnice</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; }
        .uputnica { margin-bottom: 30px; border: 1px solid #000; padding: 10px; }
        h2 { font-size: 14px; margin-bottom: 5px; }
    </style>
</head>
<body>
    @foreach ($referrals as $referral)
        <div class="uputnica">
            <h2>RA-1 Uputnica</h2>
            <strong>Zaposlenik:</strong> {{ $referral->employee->name }}<br>
            <strong>Datum:</strong> {{ \Carbon\Carbon::parse($referral->date)->format('d.m.Y.') }}<br>
            <strong>Opis posla:</strong> {{ $referral->job_description }}<br>
            <strong>Alat:</strong> {{ $referral->tools }}<br>
            <strong>Uvjeti:</strong> {{ $referral->location_conditions }}<br>
            <strong>Organizacija:</strong> {{ $referral->organization }}<br>
            <strong>Aktivnosti:</strong> {{ $referral->activity }}<br>
            <strong>Opasnosti:</strong> {{ $referral->hazards }}<br>
        </div>
    @endforeach
</body>
</html>
