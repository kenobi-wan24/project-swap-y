@extends('layouts.guest')
@section('title', 'Dashboard — SWAPY')

@section('content')
<div id="dashboard-app" data-user="{{ json_encode($user) }}"></div>
@endsection