@extends('layouts.guest')
@section('title', 'Browse Items - SWAPY')

@section('content')
<div
    id="items-app"
    data-listings='@json($listings)'
    data-featured='@json($featured)'
    data-total='{{ $total }}'
></div>
@endsection