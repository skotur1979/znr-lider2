@php
    use Carbon\Carbon;
@endphp

<table>
    <thead>
        <tr>
            <th>R.b.</th>
            <th>Naziv</th>
            <th>HRN EN</th>
            <th>Veličina</th>
            <th>Rok (mj)</th>
            <th>Izdano</th>
            <th>Istek</th>
            <th>Datum vraćanja</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($record->items as $index => $item)
            @php
                $today = now();
                $issued = $item->issue_date ? Carbon::parse($item->issue_date) : null;
                $duration = $item->duration_months ?? 0;
                $istek = $issued ? $issued->copy()->addMonths($duration) : null;

                $backgroundColor = '';
                if ($istek) {
                    if ($istek->lt($today)) {
                        $backgroundColor = 'background-color:#FF6347'; // crveno
                    } elseif ($istek->lte($today->copy()->addDays(30))) {
                        $backgroundColor = 'background-color:#FFFF00'; // žuto
                    }
                }
            @endphp
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $item->equipment_name }}</td>
                <td>{{ $item->standard }}</td>
                <td>{{ $item->size }}</td>
                <td>{{ $item->duration_months }}</td>
                <td>{{ $issued ? $issued->format('d.m.Y.') : '' }}</td>
                <td style="{{ $backgroundColor }}">{{ $istek ? $istek->format('d.m.Y.') : '' }}</td>
                <td>{{ $item->return_date ? Carbon::parse($item->return_date)->format('d.m.Y.') : '' }}</td>
            </tr>
        @endforeach
    </tbody>
</table>










