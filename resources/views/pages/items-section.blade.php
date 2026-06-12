@extends('layouts.guest')
@section('title', 'Items - SWAPY')

@section('content')
<div
    id="items-section-app"
    data-section='{{ $section }}'
    data-listings='@json($listings)'
    data-total='{{ $total }}'
></div>
@endsection
