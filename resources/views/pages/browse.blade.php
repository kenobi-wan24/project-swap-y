@extends('layouts.guest')
@section('title', 'Browse Items - SWAPY')

@section('content')
<div
    id="browse-app"
    data-listings='@json($listings)'
    data-featured='@json($featured)'
    data-total='{{ $total }}'
></div>
@endsection