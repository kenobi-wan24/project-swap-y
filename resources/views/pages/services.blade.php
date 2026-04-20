@extends('layouts.guest')
@section('title', 'Services - SWAPY')

@section('content')
<div
    id="services-app"
    data-services="{{ json_encode($services) }}"
></div>
@endsection