@extends('layouts.guest')

@section('title', $item['title'] . ' - SWAPY')

@section('content')
<div
    id="item-detail-app"
    data-item="{{ json_encode($item) }}"
    data-guest="{{ auth()->check() ? 'false' : 'true' }}"
></div>
@endsection

@push('scripts')
<script type="module" src="{{ asset('js/components/global/ItemDetailPage.js') }}"></script>
@endpush