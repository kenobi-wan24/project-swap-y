@extends('layouts.guest')
@section('title', 'Services — SWAPY')

@section('content')
<div
    id="services-section-app"
    data-section='{{ $section }}'
    data-services='@json($services)'
    data-map-services='@json($mapServices)'
    data-total='{{ $total }}'
></div>
@endsection
