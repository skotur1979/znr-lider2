<!DOCTYPE html>
<html lang="hr">
<head>
    <meta charset="UTF-8">
    <title>Popis ostalih ispitivanja</title>
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

        .red { background-color: rgb(255, 99, 71); }
        .yellow { background-color: rgb(255, 255, 0); }
        .white { background-color: #ffffff; }
    </style>
</head>
<body>

@php
    use Carbon\Carbon;

    function validUntilClass($date) {
        if (!$date) return 'white';
        $carbonDate = Carbon::parse($date);
        $today = Carbon::today();

        if ($carbonDate->isPast()) {
            return 'red';
        } elseif ($carbonDate->diffInDays($today) <= 30) {
            return 'yellow';
        }

        return 'white';
    }
@endphp

<table class="header">
    <tr>
        <td>{{ now()->format('d.m.Y.') }}</td>
        <td><strong>Popis ostalih ispitivanja</strong></td>
        <td style="text-align: right;">Stranica: <span class="page"></span> / <span class="pages"></span></td>
    </tr>
</table>

<table>
    <thead>
        <tr>
            <th style="width: 5%;">Br.</th>
            <th style="width: 14%;">Naziv</th>
            <th style="width: 14%;">Kategorija</th>
            <th style="width: 10%;">Ispitao</th>
            <th style="width: 10%;">Broj izvještaja</th>
            <th style="width: 12%;">Vrijedi od</th>
            <th style="width: 12%;">Vrijedi do</th>
            <th style="width: 13%;">Lokacija</th>
            <th style="width: 10%;">Napomena</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($miscellaneouses as $index => $miscellaneous)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $miscellaneous->name }}</td>
                <td>{{ $miscellaneous->category->name ?? '' }}</td>
                <td>{{ $miscellaneous->examiner }}</td>
                <td>{{ $miscellaneous->report_number }}</td>
                <td>{{ optional($miscellaneous->examination_valid_from)->format('d.m.Y.') }}</td>
                <td class="{{ validUntilClass($miscellaneous->examination_valid_until) }}">
                    {{ optional($miscellaneous->examination_valid_until)->format('d.m.Y.') }}
                </td>
                <td>{{ $miscellaneous->location }}</td>
                <td>{{ $miscellaneous->remark }}</td>
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






















