@extends('layouts.guest')
@section('title', 'Dashboard — SWAPY')

@section('content')
<div
    id="dashboard-app"
    data-user='@json($userData)'
    data-stats='@json($stats)'
    data-my-listings='@json($myListings)'
    data-listings='@json($listings)'
    data-swap-requests='@json($swapRequests)'
    data-smart-matches='@json($smartMatches)'
    data-messages='@json($messages)'
></div>
@endsection