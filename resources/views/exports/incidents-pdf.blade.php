<!DOCTYPE html>
<html lang="hr">
<head>
    <meta charset="UTF-8">
    <title>Popis Incidenata</title>
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
            margin-bottom: 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        th, td {
            border: 1px solid #000;
            padding: 2px;
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
    </style>
</head>
<body>

<table class="header">
    <tr>
        <td>{{ now()->format('d.m.Y.') }}</td>
        <td><strong>Popis Incidenata</strong></td>
        <td style="text-align: right;">Stranica: <span class="page"></span> / <span class="pages"></span></td>
    </tr>
</table>

<table>
    <thead>
        <tr>
            <th style="width: 15%;">Lokacija</th>
            <th style="width: 10%;">Vrsta incidenta</th>
            <th style="width: 12%;">Datum nastanka</th>
            <th style="width: 12%;">Izgubljeni dani</th>
            <th style="width: 18%;">Ozlijeđeni dio tijela</th>
            <th style="width: 20%;">Napomena</th>
            <th style="width: 13%;">Slika</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($incidents as $incident)
            <tr>
                <td>{{ $incident->location }}</td>
                <td>{{ $incident->type_of_incident }}</td>
                <td>{{ \Carbon\Carbon::parse($incident->date_occurred)->format('d.m.Y') }}</td>
                <td>{{ $incident->working_days_lost }}</td>
                <td>{{ $incident->injured_body_part }}</td>
                <td>{{ $incident->other }}</td>
                <td>
                    @if ($incident->image_path && file_exists(public_path('storage/' . $incident->image_path)))
                        <img src="{{ public_path('storage/' . $incident->image_path) }}" alt="slika">
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









