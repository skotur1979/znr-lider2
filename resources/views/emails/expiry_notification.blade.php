<!DOCTYPE html>
<html lang="hr">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: sans-serif; background: #f9f9f9; padding: 20px; color: #333; }
        h2 { color: #2d3748; }
        h3 { margin-top: 30px; color: #4a5568; }
        ul { padding-left: 20px; }
        li { margin-bottom: 10px; }
        .section {
            background: #ffffff;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .expired { color: red; font-weight: bold; }
        .warning { color: orange; font-weight: bold; }
    </style>
</head>
<body>
    <h2>📬 Obavijest o isteku rokova u idućih 30 dana</h2>

    {{-- Zaposlenici --}}
    @if ($data['zaposlenici']->isNotEmpty())
        <div class="section">
            <h3>👷 Zaposlenici</h3>
            <ul>
                @foreach ($data['zaposlenici'] as $zaposlenik)
                    @php
                        $datum = \Carbon\Carbon::parse($zaposlenik->medical_examination_valid_until);
                        $dani = now()->diffInDays($datum, false);
                        $boja = $dani < 0 ? 'expired' : ($dani <= 7 ? 'warning' : '');
                    @endphp
                    <li class="{{ $boja }}">
                        🧍 <strong>{{ $zaposlenik->full_name ?? 'Nepoznato ime' }}</strong> —
                        Liječnički pregled ističe:
                        <strong>{{ $datum->format('d.m.Y') }}</strong>
                        ({{ $dani < 0 ? 'isteklo prije ' . abs($dani) . ' dana' : 'ističe za ' . $dani . ' dana' }})
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Strojevi --}}
    @if ($data['strojevi']->isNotEmpty())
        <div class="section">
            <h3>🛠️ Strojevi</h3>
            <ul>
                @foreach ($data['strojevi'] as $stroj)
                    @php
                        $datum = \Carbon\Carbon::parse($stroj->examination_valid_until);
                        $dani = now()->diffInDays($datum, false);
                        $boja = $dani < 0 ? 'expired' : ($dani <= 7 ? 'warning' : '');
                    @endphp
                    <li class="{{ $boja }}">
                        ⚙️ <strong>{{ $stroj->machine_name ?? 'Nepoznat stroj' }}</strong> —
                        Ispitni rok ističe:
                        <strong>{{ $datum->format('d.m.Y') }}</strong>
                        ({{ $dani < 0 ? 'isteklo prije ' . abs($dani) . ' dana' : 'ističe za ' . $dani . ' dana' }})
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Vatrogasni aparati --}}
    @if ($data['vatrogasni']->isNotEmpty())
        <div class="section">
            <h3>🧯 Vatrogasni aparati</h3>
            <ul>
                @foreach ($data['vatrogasni'] as $aparat)
                    @php
                        $datum = \Carbon\Carbon::parse($aparat->examination_valid_until);
                        $dani = now()->diffInDays($datum, false);
                        $boja = $dani < 0 ? 'expired' : ($dani <= 7 ? 'warning' : '');
                    @endphp
                    <li class="{{ $boja }}">
                        🔥 <strong>{{ $aparat->label ?? 'Vatrogasni aparat' }}</strong> —
                        Ispitni rok ističe:
                        <strong>{{ $datum->format('d.m.Y') }}</strong>
                        ({{ $dani < 0 ? 'isteklo prije ' . abs($dani) . ' dana' : 'ističe za ' . $dani . ' dana' }})
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Ostalo --}}
    @if ($data['ostalo']->isNotEmpty())
        <div class="section">
            <h3>📦 Ostala oprema</h3>
            <ul>
                @foreach ($data['ostalo'] as $stavka)
                    @php
                        $datum = \Carbon\Carbon::parse($stavka->examination_valid_until);
                        $dani = now()->diffInDays($datum, false);
                        $boja = $dani < 0 ? 'expired' : ($dani <= 7 ? 'warning' : '');
                    @endphp
                    <li class="{{ $boja }}">
                        📌 <strong>{{ $stavka->name ?? 'Nepoznata stavka' }}</strong> —
                        Ispitni rok ističe:
                        <strong>{{ $datum->format('d.m.Y') }}</strong>
                        ({{ $dani < 0 ? 'isteklo prije ' . abs($dani) . ' dana' : 'ističe za ' . $dani . ' dana' }})
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    <p style="margin-top: 30px; color: #888;">Ova poruka je generirana automatski. Molimo ne odgovarajte na nju.</p>
</body>
</html>













