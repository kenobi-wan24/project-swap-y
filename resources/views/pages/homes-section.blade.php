@extends('layouts.guest')
@section('title', 'Homes — SWAPY')

@section('content')
<div
    id="homes-section-app"
    data-section='{{ $section }}'
    data-homes='@json($homes)'
    data-map-homes='@json($mapHomes)'
    data-total='{{ $total }}'
></div>
@endsection
