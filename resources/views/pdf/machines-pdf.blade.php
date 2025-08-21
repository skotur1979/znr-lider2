<!DOCTYPE html>
<html lang="hr">
<head>
    <meta charset="UTF-8">
    <title>Izvoz Strojeva</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 6px;
            border: 1px solid #aaa;
            text-align: left;
        }
        th {
            background-color: #f0f0f0;
        }
        h2 {
            margin-bottom: 10px;
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
                <th>Tvor.broj</th>
                <th>Datum ispitivanja</th>
                <th>Ispitivanje vrijedi do</th>
                <th>Lokacija</th>
            </tr>
        </thead>
        <tbody>
            @foreach($machines as $m)
                <tr>
                    <td>{{ $m->naziv }}</td>
                    <td>{{ $m->proizvodac }}</td>
                    <td>{{ $m->tvor_broj }}</td>
                    <td>{{ $m->datum_ispitivanja }}</td>
                    <td>{{ $m->ispitivanje_vrijedi_do }}</td>
                    <td>{{ $m->lokacija }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>








