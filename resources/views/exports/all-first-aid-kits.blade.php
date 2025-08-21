<!DOCTYPE html>
<html lang="hr">
<head>
    <meta charset="UTF-8">
    <title>Izvještaj svih ormarića prve pomoći</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #000; }
        h1, h2 { text-align: center; }
        h2 { margin-top: 40px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; margin-bottom: 30px; }
        th, td { border: 1px solid #000; padding: 6px; text-align: left; }
        .note { margin-bottom: 20px; }

        .expired { background-color: #FF6347; }   /* crvena */
        .expiring { background-color: #FFFF00; }  /* žuta */
    </style>
</head>
<body>
    <h1>Izvještaj - Svi ormarići prve pomoći</h1>

    @foreach($kits as $kit)
    <table style="page-break-inside: avoid;">
        <thead>
            <tr>
                <th colspan="3" style="text-align: left; background-color: #fff; border: none;">
                    <h2 style="margin-bottom: 0;">Ormarić: {{ $kit->location }}</h2>
                    <p style="margin: 0;"><strong>Pregled obavljen:</strong> {{ \Carbon\Carbon::parse($kit->inspected_at)->format('d.m.Y.') }}</p>
                    @if($kit->note)
                        <p style="margin: 0;"><strong>Napomena:</strong> {{ $kit->note }}</p>
                    @endif
                </th>
            </tr>
            <tr>
                <th style="border: 1px solid #000;">Vrsta materijala</th>
                <th style="border: 1px solid #000;">Namjena</th>
                <th style="border: 1px solid #000;">Vrijedi do</th>
            </tr>
        </thead>
        <tbody>
            @forelse($kit->items as $item)
                @php
                    $validUntil = $item->valid_until ? \Carbon\Carbon::parse($item->valid_until) : null;
                    $today = \Carbon\Carbon::today();
                    $style = '';

                    if ($validUntil) {
                        if ($validUntil->isPast()) {
                            $style = 'background-color: #FF6347;';
                        } elseif ($validUntil->diffInDays($today) <= 30) {
                            $style = 'background-color: #FFFF00;';
                        }
                    }
                @endphp
                <tr>
                    <td style="border: 1px solid #000;">{{ $item->material_type }}</td>
                    <td style="border: 1px solid #000;">{{ $item->purpose }}</td>
                    <td style="border: 1px solid #000; {{ $style }}">
                        {{ $validUntil ? $validUntil->format('d.m.Y.') : 'N/A' }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="3">Nema stavki.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
@endforeach
</body>
</html>








