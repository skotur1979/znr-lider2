<!DOCTYPE html>
<html lang="hr">
<head>
    <meta charset="UTF-8">
    <title>Procjene Rizika</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
        }

        h1, h2 {
            margin-bottom: 5px;
        }

        .section {
            margin-bottom: 25px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
        }

        th, td {
            border: 1px solid #aaa;
            padding: 6px;
            text-align: left;
        }

        th {
            background-color: #f0f0f0;
        }

        ul {
            margin: 5px 0;
            padding-left: 18px;
        }
    </style>
</head>
<body>

    <h1>Procjene Rizika - Izvoz PDF</h1>
    <p>Datum izvoza: {{ now()->format('d.m.Y.') }}</p>

    @foreach($assessments as $assessment)
        <div class="section">
            <h2>Procjena br. {{ $assessment->broj_procjene }}</h2>

            <table>
                <tr>
                    <th>Tvrtka</th>
                    <td>{{ $assessment->tvrtka }}</td>
                    <th>OIB tvrtke</th>
                    <td>{{ $assessment->oib_tvrtke }}</td>
                </tr>
                <tr>
                    <th>Adresa</th>
                    <td>{{ $assessment->adresa_tvrtke }}</td>
                    <th>Vrsta procjene</th>
                    <td>{{ $assessment->vrsta_procjene }}</td>
                </tr>
                <tr>
                    <th>Datum izrade</th>
                    <td colspan="3">{{ \Carbon\Carbon::parse($assessment->datum_izrade)->format('d.m.Y.') }}</td>
                </tr>
            </table>

            @if ($assessment->participants->count())
                <h4>Sudionici izrade</h4>
                <table>
                    <thead>
                        <tr>
                            <th>Ime i prezime</th>
                            <th>Uloga</th>
                            <th>Napomena</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($assessment->participants as $participant)
                            <tr>
                                <td>{{ $participant->ime_prezime }}</td>
                                <td>{{ $participant->uloga }}</td>
                                <td>{{ $participant->napomena }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif

            @if ($assessment->revisions->count())
                <h4>Revizije</h4>
                <table>
                    <thead>
                        <tr>
                            <th>Broj revizije</th>
                            <th>Datum izrade</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($assessment->revisions as $revision)
                            <tr>
                                <td>{{ $revision->revizija_broj }}</td>
                                <td>{{ \Carbon\Carbon::parse($revision->datum_izrade)->format('d.m.Y.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif

            @if ($assessment->attachments->count())
                <h4>Prilozi</h4>
                <ul>
                    @foreach ($assessment->attachments as $attachment)
                        <li>{{ $attachment->naziv }}</li>
                    @endforeach
                </ul>
            @endif

            <hr>
        </div>
    @endforeach

</body>
</html>









