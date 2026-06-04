@extends('layouts.guest')
@section('title', 'Homes — SWAPY')

@section('content')
<div
    id="homes-app"
    data-homes='@json($homes)'
></div>
@endsection