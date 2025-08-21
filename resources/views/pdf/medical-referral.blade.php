<!DOCTYPE html>
<html lang="hr">
<head>
    <meta charset="UTF-8">
    <title>RA-1 Uputnica</title>
    <style>
        @page { margin: 20px; }
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
            table-layout: fixed;
        }
        td, th {
            border: 1px solid #000;
            padding: 5px;
            vertical-align: top;
        }
        .no-border {
            border: none;
        }
        .section-title {
            background-color: #eee;
            font-weight: bold;
            text-align: center;
        }
        .checkbox {
            display: inline-block;
            width: 14px;
            height: 14px;
            border: 1px solid #000;
            margin-right: 6px;
        }
        .row-title {
            width: 30%;
            font-weight: bold;
        }
    </style>
</head>
<body>

    <h2 style="text-align:center; margin-bottom: 15px;">RA-1 UPUTNICA ZA MEDICINU RADA</h2>

    <table>
        <tr>
            <td class="row-title">Ime i prezime</td>
            <td>{{ $employee->name }}</td>
        </tr>
        <tr>
            <td class="row-title">Datum uputnice</td>
            <td>{{ \Carbon\Carbon::parse($referral->date)->format('d.m.Y.') }}</td>
        </tr>
        <tr>
            <td class="row-title">Radno mjesto</td>
            <td>{{ $employee->workplace }}</td>
        </tr>
        <tr>
            <td class="row-title">Organizacijska jedinica</td>
            <td>{{ $employee->organization_unit }}</td>
        </tr>
        <tr>
            <td class="row-title">Opis posla</td>
            <td>{{ $referral->job_description }}</td>
        </tr>
    </table>

    <table>
        <tr><th colspan="2" class="section-title">Mjesto rada i uvjeti</th></tr>
        @foreach($referral->workplace_conditions ?? [] as $item)
            <tr><td colspan="2">☑ {{ $item }}</td></tr>
        @endforeach
    </table>

    <table>
        <tr><th colspan="2" class="section-title">Organizacija rada</th></tr>
        @foreach($referral->work_organization ?? [] as $item)
            <tr><td colspan="2">☑ {{ $item }}</td></tr>
        @endforeach
    </table>

    <table>
        <tr><th colspan="2" class="section-title">Položaj tijela pri radu</th></tr>
        @foreach($referral->body_position ?? [] as $item)
            <tr><td colspan="2">☑ {{ $item }}</td></tr>
        @endforeach
    </table>

    <table>
        <tr><th colspan="2" class="section-title">Štetnosti / uvjeti rada</th></tr>
        @foreach($referral->hazards_conditions ?? [] as $item)
            <tr><td colspan="2">☑ {{ $item }}</td></tr>
        @endforeach
    </table>

    <table>
        <tr>
            <td class="row-title">Strojevi, alati, oprema</td>
            <td>{{ $referral->tools }}</td>
        </tr>
        <tr>
            <td class="row-title">Posebni uvjeti rada</td>
            <td>{{ $referral->location_conditions }}</td>
        </tr>
        <tr>
            <td class="row-title">Vrsta aktivnosti</td>
            <td>{{ $referral->activity }}</td>
        </tr>
    </table>

    <div style="margin-top: 40px; text-align: right;">
        ___________________________<br>
        <em>Potpis odgovorne osobe</em>
    </div>

</body>
</html>


