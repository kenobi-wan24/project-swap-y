@extends('layouts.guest')

@section('title', $item['title'] . ' — SWAPY')

@section('content')
<div
    id="item-detail-app"
    data-item='@json($item)'
    data-guest="{{ auth()->check() ? 'false' : 'true' }}"
></div>
@endsection
