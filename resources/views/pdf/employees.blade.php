<!DOCTYPE html>
<html lang="hr">
<head>
    <meta charset="UTF-8">
    <title>Popis zaposlenika</title>
    <style>
    /* ispravno zatvoren @page + landscape */
    @page {
        margin: 20px;
        size: A4 landscape;
    }

    body { font-family: DejaVu Sans, sans-serif; font-size: 7px; }

    .header { width: 100%; font-size: 8px; margin-bottom: 8px; }
    .header td { width: 33%; text-align: center; }

    table { width: 100%; border-collapse: collapse; table-layout: fixed; }
    th, td { border: 1px solid #000; padding: 1px; word-wrap: break-word; vertical-align: top; text-align: center; }
    th { background-color: #f0f0f0; }

    .small-date { font-size: 6px; line-height: 1.1; }
    .cert-list  { font-size: 6px; line-height: 1.2; }
    .narrow     { font-size: 6px; }

    /* 1. kolona – redni broj: jako usko i prioritetno */
    th.w-index, td.w-index { width: 18px !important; max-width: 18px !important; overflow: hidden; white-space: nowrap; }
    col.col-index          { width: 18px !important; }

    .red    { background-color: rgb(255, 99, 71); }
    .yellow { background-color: rgb(255, 255, 0); }
    .white  { background-color: #ffffff; }
</style>
</head>
<body>

@php
    use Carbon\Carbon;

    function formatSplitDate($date) {
        if (!$date) return '';
        $d = $date instanceof Carbon ? $date : Carbon::parse($date);
        return $d->format('d.m.') . "\n" . $d->format('Y') . '.';
    }

    // crveno: prošlost; žuto: u sljedećih 30 dana; bijelo: ostalo ili prazno
    function dateClass($date) {
        if (!$date) return 'white';
        $d = $date instanceof Carbon ? $date : Carbon::parse($date);
        if ($d->isPast()) return 'red';
        if ($d->lessThanOrEqualTo(now()->addDays(30))) return 'yellow';
        return 'white';
    }

    // isti kriterij za certifikate (valid_until)
    function certClass($validUntil) {
        if (!$validUntil) return 'white';
        $d = $validUntil instanceof Carbon ? $validUntil : Carbon::parse($validUntil);
        if ($d->isPast()) return 'red';
        if ($d->lessThanOrEqualTo(now()->addDays(30))) return 'yellow';
        return 'white';
    }
@endphp

<table class="header">
    <tr>
        <td>{{ now()->format('d.m.Y.') }}</td>
        <td><strong>Popis zaposlenika</strong></td>
        <td style="text-align: right;">Stranica: <span class="page"></span> / <span class="pages"></span></td>
    </tr>
</table>

<table>
    <colgroup>
        <col class="col-index">   <!-- ⇦ fiksira širinu prve kolone -->
        <!-- ostale kolone prepuštamo da raspodijele ostatak širine -->
    </colgroup>
    <thead>
        <tr>
            <th class="w-index">Br.</th>
            <th>Prezime i ime</th>
            <th>Zanim.</th>
            <th>Šk. spr.</th>
            <th>Rođ. (datum/mj.)</th>
            <th>Rod.</th>
            <th>Adresa</th>
            <th>Spol</th>
            <th>Org. jedinica</th>
            <th>Vrsta ugovora</th>
            <th>OIB</th>
            <th>Tel.</th>
            <th>Email</th>
            <th>RM</th>
            <th>Datum zapos.</th>
            <th>Lij. od</th>
            <th>Lij. do</th>
            <th>Čl. 3.</th>
            <th>ZNR</th>
            <th>ZOP</th>
            <th>Izj. ZOP</th>
            <th>Evak.</th>
            <th>Pomoć od</th>
            <th>Pomoć do</th>
            <th>Tok. od</th>
            <th>Tok. do</th>
            <th>Ovla. od</th>
            <th>Ovla. do</th>
            <th>Certifikati</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($employees as $index => $e)
            <tr>
                <td class="narrow w-index">{{ $index + 1 }}</td>
                <td class="narrow">{{ $e->name }}</td>

                {{-- NOVA POLJA --}}
                <td class="narrow">{{ $e->job_title }}</td>
                <td class="narrow">{{ $e->education }}</td>
                <td class="narrow">{{ $e->place_of_birth }}</td>
                <td class="narrow">{{ $e->name_of_parents }}</td>

                <td class="narrow">{{ $e->address }}</td>
                <td class="narrow">{{ $e->gender }}</td>
                <td class="narrow">{{ $e->organization_unit }}</td>
                <td class="narrow">{{ $e->contract_type }}</td>
                <td class="narrow">{{ $e->OIB }}</td>
                <td class="narrow">{{ $e->phone }}</td>
                <td class="narrow">{{ $e->email }}</td>
                <td class="narrow">{{ $e->workplace }}</td>

                <td class="small-date">{!! nl2br(e(formatSplitDate($e->employeed_at))) !!}</td>

                <td class="small-date">{!! nl2br(e(formatSplitDate($e->medical_examination_valid_from))) !!}</td>
                <td class="small-date {{ dateClass($e->medical_examination_valid_until) }}">
                    {!! nl2br(e(formatSplitDate($e->medical_examination_valid_until))) !!}
                </td>

                <td class="narrow">{{ $e->article }}</td>
                <td class="small-date">{!! nl2br(e(formatSplitDate($e->occupational_safety_valid_from))) !!}</td>
                <td class="small-date">{!! nl2br(e(formatSplitDate($e->fire_protection_valid_from))) !!}</td>
                <td class="small-date">{!! nl2br(e(formatSplitDate($e->fire_protection_statement_at))) !!}</td>
                <td class="small-date">{!! nl2br(e(formatSplitDate($e->evacuation_valid_from))) !!}</td>

                {{-- PRVA POMOĆ OD / DO --}}
                <td class="small-date">{!! nl2br(e(formatSplitDate($e->first_aid_valid_from))) !!}</td>
                <td class="small-date {{ dateClass($e->first_aid_valid_until) }}">
                    {!! nl2br(e(formatSplitDate($e->first_aid_valid_until))) !!}
                </td>

                <td class="small-date">{!! nl2br(e(formatSplitDate($e->toxicology_valid_from))) !!}</td>
                <td class="small-date {{ dateClass($e->toxicology_valid_until) }}">
                    {!! nl2br(e(formatSplitDate($e->toxicology_valid_until))) !!}
                </td>

                <td class="small-date">{!! nl2br(e(formatSplitDate($e->employers_authorization_valid_from))) !!}</td>
                <td class="small-date {{ dateClass($e->employers_authorization_valid_until) }}">
                    {!! nl2br(e(formatSplitDate($e->employers_authorization_valid_until))) !!}
                </td>

                {{-- OSTALI CERTIFIKATI – svaki red obojen po "do" --}}
                <td class="cert-list">
                    @foreach ($e->certificates as $c)
                        @php $class = certClass($c->valid_until); @endphp
                        <div class="{{ $class }}">
                            <strong>{{ $c->title }}</strong><br>
                            <small>
                                {{ $c->valid_from ? \Carbon\Carbon::parse($c->valid_from)->format('d.m.Y.') : '-' }}
                                @if ($c->valid_until)
                                    – {{ \Carbon\Carbon::parse($c->valid_until)->format('d.m.Y.') }}
                                @endif
                            </small>
                        </div>
                    @endforeach
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

