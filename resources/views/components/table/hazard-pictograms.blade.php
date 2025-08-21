@php
    $pictograms = is_array($getState()) ? $getState() : [];
@endphp

@foreach($pictograms as $p)
    <img src="{{ asset('piktogrami/' . strtolower($p) . '.gif') }}"
     alt="{{ $p }}"
     title="{{ $p }}"
     style="width: 32px; height: 32px; display: inline-block; margin-right: 4px;" />
@endforeach

