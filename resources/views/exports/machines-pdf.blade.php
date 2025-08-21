<!DOCTYPE html>
<html lang="hr">
<head>
    <meta charset="UTF-8">
    <title>Popis strojeva</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }

        th, td {
            border: 1px solid #333;
            padding: 5px;
        }

        th {
            background-color: #eee;
        }
    </style>
</head>
<body>
    <h2>Popis strojeva</h2>
    <table>
        <thead>
            <tr>
                <th>Naziv</th>
                <th>Proizvođač</th>
                <th>Tvor. broj</th>
                <th>Datum ispitivanja</th>
                <th>Vrijedi do</th>
                <th>Ispitao</th>
                <th>Broj izvještaja</th>
                <th>Lokacija</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($machines as $m)
                <tr>
                    <td>{{ $m->naziv }}</td>
                    <td>{{ $m->proizvodac }}</td>
                    <td>{{ $m->tvor_broj }}</td>
                    <td>{{ \Carbon\Carbon::parse($m->datum_ispitivanja)->format('d.m.Y.') }}</td>
                    <td>{{ \Carbon\Carbon::parse($m->ispitivanje_vrijedi_do)->format('d.m.Y.') }}</td>
                    <td>{{ $m->ispitao }}</td>
                    <td>{{ $m->broj_izvjestaja }}</td>
                    <td>{{ $m->lokacija }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>









