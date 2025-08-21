<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Zaposlenici</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #999; padding: 8px; text-align: left; }
        th { background-color: #f0f0f0; }
    </style>
</head>
<body>
    <h2>Popis zaposlenika</h2>
    <table>
        <thead>
            <tr>
                <th>Ime</th>
                <th>Prezime</th>
                <th>Odjel</th>
                <th>Pozicija</th>
                <th>Datum Zapošljavanja</th>
            </tr>
        </thead>
        <tbody>
            @foreach($employees as $employee)
                <tr>
                    <td>{{ $employee->ime }}</td>
                    <td>{{ $employee->prezime }}</td>
                    <td>{{ $employee->odjel }}</td>
                    <td>{{ $employee->pozicija }}</td>
                    <td>{{ \Carbon\Carbon::parse($employee->datum_zaposljavanja)->format('d.m.Y') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>









