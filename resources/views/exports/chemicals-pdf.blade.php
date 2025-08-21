<!DOCTYPE html>
<html lang="hr">
<head>
    <meta charset="UTF-8">
    <title>Popis Kemikalija</title>
    <style>
        @page {
            margin: 20px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 7px;
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

        .header {
            width: 100%;
            font-size: 8px;
            margin-bottom: 8px;
        }

        .header td {
            width: 33%;
            text-align: center;
        }
    </style>
</head>
<body>

    <table class="header">
        <tr>
            <td>{{ now()->format('d.m.Y.') }}</td>
            <td><strong>POPIS KEMIKALIJA</strong></td>
            <td style="text-align: right;">Stranica: <span class="page"></span> / <span class="pages"></span></td>
        </tr>
    </table>

    <table>
        <thead>
            <tr>
                <th style="width: 10%;">Ime proizvoda</th>
                <th style="width: 6%;">CAS</th>
                <th style="width: 6%;">UFI</th>
                <th style="width: 9%;">Piktogrami</th>
                <th style="width: 10%;">H oznake</th>
                <th style="width: 10%;">P oznake</th>
                <th style="width: 10%;">Mjesto upotrebe</th>
                <th style="width: 6%;">Količina</th>
                <th style="width: 7%;">GVI / KGVI</th>
                <th style="width: 8%;">VOC</th>
                <th style="width: 8%;">STL – HZJZ</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($chemicals as $chemical)
                <tr>
                    <td>{{ $chemical->product_name }}</td>
                    <td>{{ $chemical->cas_number }}</td>
                    <td>{{ $chemical->ufi_number }}</td>
                    <td>
                        @php
                            $pictos = is_array($chemical->hazard_pictograms)
                                ? $chemical->hazard_pictograms
                                : (is_string($chemical->hazard_pictograms) ? explode(',', $chemical->hazard_pictograms) : []);
                        @endphp
                        {{ implode(', ', $pictos) }}
                    </td>
                    <td>
                        @php
                            $h = is_array($chemical->h_statements)
                                ? $chemical->h_statements
                                : (is_string($chemical->h_statements) ? explode(',', $chemical->h_statements) : []);
                        @endphp
                        {{ implode(', ', $h) }}
                    </td>
                    <td>
                        @php
                            $p = is_array($chemical->p_statements)
                                ? $chemical->p_statements
                                : (is_string($chemical->p_statements) ? explode(',', $chemical->p_statements) : []);
                        @endphp
                        {{ implode(', ', $p) }}
                    </td>
                    <td>{{ $chemical->usage_location }}</td>
                    <td>{{ $chemical->annual_quantity }}</td>
                    <td>{{ $chemical->gvi_kgvi }}</td>
                    <td>{{ $chemical->voc }}</td>
                    <td>
                        {{ $chemical->stl_hzjz ? \Carbon\Carbon::parse($chemical->stl_hzjz)->format('d.m.Y.') : '-' }}
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















