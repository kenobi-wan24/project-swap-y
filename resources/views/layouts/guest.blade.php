<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SWAPY — Swap Anything. Zero Cash Needed.')</title>

    {{-- Google Fonts: DM Sans --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700&display=swap" rel="stylesheet">

    {{-- Vite: Tailwind CSS + app JS --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('head')
</head>
<body class="bg-gray-50 font-sans antialiased" style="color: #3A3330; overflow-x: hidden; max-width: 100vw;">

    {{-- ===== NAVIGATION — Blade partial, always renders, no Vue needed ===== --}}
    @include('partials.navigation')

    {{-- ===== MAIN PAGE CONTENT ===== --}}
    <main>
        @yield('content')
    </main>

    {{-- ===== FOOTER ===== --}}
    @include('partials.footer')

    {{-- ===== PAGE-SPECIFIC SCRIPTS (Vue components per page if needed) ===== --}}
    @stack('scripts')

</body>
</html>