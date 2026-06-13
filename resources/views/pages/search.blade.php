@extends('layouts.guest')
@section('title', ($q ? $q . ' — ' : '') . 'Search — SWAPY')

@section('content')
<div
    id="search-app"
    data-q="{{ $q }}"
    data-location="{{ $location }}"
    data-results='@json($results)'
></div>
@endsection
