<!DOCTYPE html> 
<html lang="hr">
<head>
    <meta charset="UTF-8">
    <title>Popis radne opreme</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th, td {
            border: 1px solid black;
            padding: 4px;
            text-align: center;
            vertical-align: middle;
        }

        th {
            background-color: #f0f0f0;
        }

        .expired {
            background-color: #FF6347; /* crvena */
        }

        .expiring {
            background-color: #FFFF00; /* žuta */
        }
    </style>
</head>
<body>
    <table>
        <tr>
            <td style="width: 33%;">{{ now()->format('d.m.Y.') }}</td>
            <td style="width: 34%; text-align: center;"><strong>Popis radne opreme</strong></td>
            <td style="width: 33%; text-align: right;"></td>
        </tr>
    </table>

    <table>
        <thead>
            <tr>
                <th>Br.</th>
                <th>Naziv</th>
                <th>Proizvođač</th>
                <th>Tvornički broj</th>
                <th>Inventarni broj</th>
                <th>Vrijedi od</th>
                <th>Vrijedi do</th>
                <th>Lokacija</th>
                <th>Napomena</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($machines as $index => $machine)
    @php
        $validFrom = $machine->examination_valid_from ? \Carbon\Carbon::parse($machine->examination_valid_from) : null;
        $validUntil = $machine->examination_valid_until ? \Carbon\Carbon::parse($machine->examination_valid_until) : null;
        $today = \Carbon\Carbon::today();

        $validUntilClass = '';

        if ($validUntil) {
            if ($validUntil->isPast()) {
                $validUntilClass = 'expired';
            } elseif ($validUntil->diffInDays($today) <= 30) {
                $validUntilClass = 'expiring';
            }
        }
    @endphp
    <tr>
        <td>{{ $index + 1 }}</td>
        <td>{{ $machine->name }}</td>
        <td>{{ $machine->manufacturer }}</td>
        <td>{{ $machine->factory_number }}</td>
        <td>{{ $machine->inventory_number }}</td>
        <td>
            {{ $validFrom ? $validFrom->format('d.m.Y.') : '' }}
        </td>
        <td class="{{ $validUntilClass }}">
            {{ $validUntil ? $validUntil->format('d.m.Y.') : '' }}
        </td>
        <td>{{ $machine->location }}</td>
        <td>{{ $machine->description }}</td>
    </tr>
@endforeach
        </tbody>
    </table>

    {{-- Numeracija stranica --}}
    <script type="text/php">
        if (isset($pdf)) {
            $pdf->page_script(function($pageNumber, $pageCount, $pdf) {
                $pdf->text(510, 20, "Stranica: $pageNumber / $pageCount", null, 9);
            });
        }
    </script>
</body>
</html>










