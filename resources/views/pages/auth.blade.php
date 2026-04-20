@extends('layouts.auth')
@section('title', 'Sign In - SWAPY')

@section('auth-form')
<div
    id="auth-app"
    data-default-tab="{{ request()->routeIs('login') ? 'login' : 'register' }}"
></div>
@endsection