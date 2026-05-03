@extends('layouts.guest')
@section('title', 'Dashboard — SWAPY')

@section('content')
<div
    id="dashboard-app"
    data-user='@json($userData)'
    data-stats='@json($stats)'
    data-matches='@json([])'
    data-swaps='@json([])'
    data-messages='@json([])'
    data-credibility='@json([])'
    data-my-items='@json($myItems)'
></div>
@endsection