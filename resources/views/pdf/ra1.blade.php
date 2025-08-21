<!DOCTYPE html>
<html lang="hr">
<head>
    <meta charset="UTF-8">
    <style>
        @page { margin: 0cm; }
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11pt;
            position: relative;
            width: 21cm;
            height: 29.7cm;
            margin: 0;
            padding: 0;
        }
        .field {
            position: absolute;
            white-space: nowrap;
        }
        .checkbox {
            position: absolute;
            font-weight: bold;
        }
        .oib span {
            display: inline-block;
            width: 0.6cm;
            text-align: center;
        }
    </style>
</head>
<body>
    {{-- Broj i datum uputnice --}}
    <div class="field" style="top:1.90cm; left:15.00cm">{{ $referral->referral_number }}</div>
    <div class="field" style="top:2.50cm; left:15.00cm">{{ optional($referral->referral_date)->format('d.m.Y.') }}</div>

    {{-- Poslodavac --}}
    <div class="field" style="top:3.30cm; left:3.60cm">{{ $referral->employer_name }}</div>
    <div class="field oib" style="top:4.20cm; left:3.80cm">
        @foreach(str_split($referral->employer_oib ?? '') as $digit)
            <span>{{ $digit }}</span>
        @endforeach
    </div>

    {{-- Osobni podaci --}}
    <div class="field" style="top:5.90cm; left:3.60cm">{{ $referral->employee?->name }}</div>
    <div class="field" style="top:6.45cm; left:3.60cm">{{ $referral->place_of_birth }}</div>
    <div class="field oib" style="top:7.00cm; left:3.80cm">
        @foreach(str_split($referral->oib ?? '') as $digit)
            <span>{{ $digit }}</span>
        @endforeach
    </div>

    {{-- Podaci o zaposlenju --}}
    <div class="field" style="top:8.00cm; left:3.60cm">{{ $referral->job_title }}</div>
    <div class="field" style="top:8.50cm; left:3.60cm">{{ $referral->education }}</div>
    <div class="field" style="top:9.00cm; left:3.60cm">{{ optional($referral->employment_date)->format('d.m.Y.') }}</div>

    {{-- Sekcije s checkboxovima --}}
    <div class="field" style="top:9.60cm; left:3.60cm">{{ implode(', ', (array) $referral->workplace_location) }}</div>
    <div class="field" style="top:10.30cm; left:3.60cm">{{ implode(', ', (array) $referral->organization) }}</div>
    <div class="field" style="top:11.00cm; left:3.60cm">{{ implode(', ', (array) $referral->body_position) }}</div>
    <div class="field" style="top:12.00cm; left:3.60cm">{{ implode(', ', (array) $referral->loads) }}</div>
    <div class="field" style="top:13.00cm; left:3.60cm">{{ implode(', ', (array) $referral->hazards) }}</div>
    <div class="field" style="top:13.80cm; left:3.60cm">{{ $referral->tools }}</div>
    <div class="field" style="top:14.50cm; left:3.60cm">{{ $referral->job_tasks }}</div>

    {{-- Vrsta pregleda --}}
    <div class="field" style="top:15.30cm; left:3.60cm">{{ implode(', ', (array) $referral->exam_type) }}</div>
    <div class="field" style="top:16.00cm; left:3.60cm">{{ optional($referral->last_exam_date)->format('d.m.Y.') }}</div>
    <div class="field" style="top:16.50cm; left:3.60cm">{{ $referral->last_exam_reference }}</div>
    <div class="field" style="top:17.20cm; left:3.60cm">{{ $referral->short_description }}</div>
    <div class="field" style="top:18.00cm; left:3.60cm">{{ $referral->law_reference }}</div>
    <div class="field" style="top:18.80cm; left:3.60cm">{{ $referral->special_conditions }}</div>
    <div class="field" style="top:19.50cm; left:3.60cm">{{ $referral->name_of_parents }}</div>

    {{-- Checkbox primjer s logikom --}}
    @if(in_array('stojeći', (array) $referral->body_position))
        <div class="checkbox" style="top:11.00cm; left:3.00cm">X</div>
    @endif

    {{-- Tereti --}}
    @if($referral->lifting_enabled)
        <div class="checkbox" style="top:22.00cm; left:3.60cm">X</div>
        <div class="field" style="top:22.00cm; left:5.00cm">{{ $referral->lifting_weight }}</div>
    @endif
    @if($referral->carrying_enabled)
        <div class="checkbox" style="top:22.50cm; left:3.60cm">X</div>
        <div class="field" style="top:22.50cm; left:5.00cm">{{ $referral->carrying_weight }}</div>
    @endif
    @if($referral->pushing_enabled)
        <div class="checkbox" style="top:23.00cm; left:3.60cm">X</div>
        <div class="field" style="top:23.00cm; left:5.00cm">{{ $referral->pushing_weight }}</div>
    @endif
</body>
</html>
