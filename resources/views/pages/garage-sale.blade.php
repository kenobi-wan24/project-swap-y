@extends('layouts.guest')
@section('title', 'Garage Sale - SWAPY')

@section('content')
<div id="garage-sale-app" data-sellers="{{ json_encode($sellers) }}"></div>
@endsection