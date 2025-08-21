<!DOCTYPE html>
<html lang="hr">
<head>
    <meta charset="UTF-8">
    <title>Izvještaj o Zapažanjima</title>
    <style>
        @page {
            margin: 20px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 7px;
        }

        .header {
            width: 100%;
            font-size: 8px;
            margin-bottom: 8px;
        }

        .header td {
            width: 33%;
            text-align: center;
        }

        h2 {
            text-align: center;
            margin-bottom: 8px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        th, td {
            border: 1px solid #000;
            padding: 1px;
            word-wrap: break-word;
            vertical-align: top;
            text-align: center;
        }

        th {
            background-color: #f0f0f0;
        }

        img {
            width: 40px;
            height: auto;
        }

        .crveno { background-color: rgb(255, 99, 71); }
        .zuto { background-color: rgb(255, 255, 0); }
        .zeleno { background-color: rgb(144, 238, 144); }
        .bijelo { background-color: #ffffff; }

    </style>
</head>
<body>

@php
    use Carbon\Carbon;

    function statusHR($status) {
        return match (strtolower($status)) {
            'not started' => 'Nije započeto',
            'in progress' => 'U tijeku',
            'complete' => 'Završeno',
            default => $status,
        };
    }

    function statusBoja($translatedStatus) {
        return match ($translatedStatus) {
            'U tijeku' => 'zuto',
            'Završeno' => 'zeleno',
            'Nije započeto' => 'crveno',
            default => 'bijelo',
        };
    }
@endphp

<table class="header">
    <tr>
        <td>{{ now()->format('d.m.Y.') }}</td>
        <td><strong>IZVJEŠTAJ O ZAPAŽANJIMA</strong></td>
        <td style="text-align: right;">Stranica: <span class="page"></span> / <span class="pages"></span></td>
    </tr>
</table>

<table>
    <thead>
        <tr>
            <th style="width: 7%;">Datum</th>
            <th style="width: 9%;">Vrsta</th>
            <th style="width: 8%;">Lokacija</th>
            <th style="width: 12%;">Opis</th>
            <th style="width: 10%;">Opasnost</th>
            <th style="width: 10%;">Radnja</th>
            <th style="width: 10%;">Odg. osoba</th>
            <th style="width: 7%;">Rok</th>
            <th style="width: 7%;">Status</th>
            <th style="width: 10%;">Komentar</th>
            <th style="width: 10%;">Slika</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($observations as $obs)
            @php
                $translatedStatus = statusHR($obs->status);
                $statusClass = statusBoja($translatedStatus);
            @endphp
            <tr>
                <td>{{ \Carbon\Carbon::parse($obs->incident_date)->format('d.m.Y') }}</td>
                <td>{{ $obs->observation_type }}</td>
                <td>{{ $obs->location }}</td>
                <td>{{ $obs->item }}</td>
                <td>{{ $obs->potential_incident_type }}</td>
                <td>{{ $obs->action }}</td>
                <td>{{ $obs->responsible }}</td>
                <td>
                    {{ $obs->target_date ? \Carbon\Carbon::parse($obs->target_date)->format('d.m.Y') : '-' }}
                </td>
                <td class="{{ $statusClass }}">{{ $translatedStatus }}</td>
                <td>{{ \Illuminate\Support\Str::limit($obs->comments, 50) }}</td>
                <td>
                    @if ($obs->picture_path && file_exists(public_path('storage/' . $obs->picture_path)))
                        <img src="{{ public_path('storage/' . $obs->picture_path) }}" alt="slika">
                    @else
                        -
                    @endif
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

<script type="text/php">
    if (isset($pdf)) {
        $pdf->page_script(function ($pageNumber, $pageCount, $pdf) {
            $text = "Stranica: $pageNumber / $pageCount";
            $pdf->text(510, 20, $text, null, 9);
        });
    }
</script>

</body>
</html>
