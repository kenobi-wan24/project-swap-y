@extends('layouts.guest')
@section('title', 'Garage Sales — SWAPY')

@section('content')
<div
    id="garage-sale-section-app"
    data-section='{{ $section }}'
    data-sellers='@json($sellers)'
    data-map-sellers='@json($mapSellers)'
    data-total='{{ $total }}'
></div>
@endsection
