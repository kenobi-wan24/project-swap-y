{{-- filepath: resources/views/pages/how-it-works.blade.php --}}
@extends('layouts.guest')
@section('title', 'How It Works - SWAPY')

@section('content')
<div
    id="how-it-works-app"
    data-guides="{{ json_encode($guides) }}"
    data-featured="{{ json_encode($featured) }}"
></div>
@endsection