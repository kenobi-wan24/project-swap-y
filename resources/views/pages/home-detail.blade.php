@extends('layouts.guest')
@section('title', 'Home Listing — SWAPY')

@section('content')
<div
    id="home-detail-app"
    data-home='@json($home)'
></div>
@endsection