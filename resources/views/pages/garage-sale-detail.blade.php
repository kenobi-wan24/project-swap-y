@extends('layouts.guest')
@section('title', 'Garage Sale — SWAPY')

@section('content')
<div
    id="garage-sale-detail-app"
    data-sale='@json($sale)'
></div>
@endsection