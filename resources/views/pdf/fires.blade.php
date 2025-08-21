<!DOCTYPE html>
<html lang="hr">
<head>
    <meta charset="UTF-8">
    <title>Popis vatrogasnih aparata</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 9px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            table-layout: fixed;
        }

        th, td {
            border: 1px solid black;
            padding: 2px;
            text-align: center;
            vertical-align: middle;
            word-wrap: break-word;
        }

        th {
            background-color: #f0f0f0;
        }

        .expired {
            background-color: #FF6347;
        }

        .expiring {
            background-color: #FFFF00;
        }
    </style>
</head>
<body>
    <table>
        <tr>
            <td style="width: 33%;">{{ now()->format('d.m.Y.') }}</td>
            <td style="width: 34%; text-align: center;"><strong>Popis vatrogasnih aparata</strong></td>
            <td style="width: 33%; text-align: right;"></td>
        </tr>
    </table>

    <table>
        <thead>
            <tr>
                <th style="width: 3%;">Br.</th>
                <th style="width: 10%;">Mjesto</th>
                <th style="width: 5%;">Tip</th>
                <th style="width: 10%;">Tvorn. broj</th>
                <th style="width: 10%;">Ser. broj</th>
                <th style="width: 9%;">Periodički servis</th>
                <th style="width: 8%;">Vrijedi do</th>
                <th style="width: 10%;">Serviser</th>
                <th style="width: 9%;">Redovni pregled</th>
                <th style="width: 7%;">Uočljivost</th>
                <th style="width: 9%;">Nedostaci</th>
                <th style="width: 10%;">Otklanjanje</th>
            </tr>
        </thead>
        <tbody>
            @foreach($fires as $index => $fire)
                @php
                    $validUntil = $fire->examination_valid_until ? \Carbon\Carbon::parse($fire->examination_valid_until) : null;
                    $today = \Carbon\Carbon::today();
                    $validUntilClass = '';

                    if ($validUntil) {
                        if ($validUntil->isPast()) {
                            $validUntilClass = 'expired';
                        } elseif ($validUntil->diffInDays($today) <= 30) {
                            $validUntilClass = 'expiring';
                        }
                    }

                    $regularDate = $fire->regular_examination_valid_from ? \Carbon\Carbon::parse($fire->regular_examination_valid_from) : null;
                @endphp
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $fire->place }}</td>
                    <td>{{ $fire->type }}</td>
                    <td>{{ $fire->getAttribute('factory_number/year_of_production') }}</td>
                    <td>{{ $fire->serial_label_number }}</td>
                    <td>{{ optional($fire->examination_valid_from)->format('d.m.Y.') }}</td>
                    <td class="{{ $validUntilClass }}">
                        {{ $validUntil ? $validUntil->format('d.m.Y.') : '' }}
                    </td>
                    <td>{{ $fire->service }}</td>
                    <td>{{ $regularDate ? $regularDate->format('d.m.Y.') : '' }}</td>
                    <td>{{ $fire->visible }}</td>
                    <td>{{ $fire->remark }}</td>
                    <td>{{ $fire->action }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <script type="text/php">
        if (isset($pdf)) {
            $pdf->page_script(function($pageNumber, $pageCount, $pdf) {
                $pdf->text(510, 20, "Stranica: $pageNumber / $pageCount", null, 9);
            });
        }
    </script>
</body>
</html>





