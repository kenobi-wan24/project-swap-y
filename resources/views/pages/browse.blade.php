@extends('layouts.guest')
@section('title', 'Browse Items - SWAPY')

@section('content')

<div id="browse-app" data-listings="{{ json_encode($listings) }}">
    <browse-page />
</div>

@endsection

@push('scripts')
<script type="module">
    import { createApp }  from 'vue'
    import BrowsePage     from '/resources/js/components/browse/BrowsePage.vue'
    createApp({ components: { BrowsePage } }).mount('#browse-app')
</script>
@endpush