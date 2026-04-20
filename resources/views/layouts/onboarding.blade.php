{{-- filepath: resources/views/layouts/onboarding.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Getting Started — SWAPY')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:opsz,wght@9..40,400;9..40,500;9..40,600;9..40,700;9..40,800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        html, body { height: 100%; }
        body {
            font-family: 'DM Sans', sans-serif;
            color: #3A3330;
            background: #ffffff;
            min-height: 100vh;
        }

        /* Step dot progress indicator */
        .step-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            transition: all 0.35s cubic-bezier(.4,0,.2,1);
        }
        .step-dot.active {
            background: #ED730C;
            width: 24px;
            border-radius: 5px;
        }
        .step-dot.done   { background: #ED730C; }
        .step-dot.pending { background: #e5e7eb; }

        /* Smooth slide transitions between steps */
        .step-enter-active, .step-leave-active {
            transition: all 0.4s cubic-bezier(.4,0,.2,1);
        }
        .step-enter-from { opacity: 0; transform: translateX(40px); }
        .step-leave-to   { opacity: 0; transform: translateX(-40px); }
        .step-enter-to, .step-leave-from { opacity: 1; transform: translateX(0); }

        /* Range slider brand styling */
        input[type=range] {
            -webkit-appearance: none;
            appearance: none;
            width: 100%;
            height: 5px;
            border-radius: 3px;
            background: #e5e7eb;
            outline: none;
            cursor: pointer;
        }
        input[type=range]::-webkit-slider-thumb {
            -webkit-appearance: none;
            appearance: none;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: #ED730C;
            cursor: pointer;
            border: 3px solid #fff;
            box-shadow: 0 1px 6px rgba(237,115,12,0.4);
            transition: transform 0.15s;
        }
        input[type=range]::-webkit-slider-thumb:hover { transform: scale(1.15); }
        input[type=range]::-moz-range-thumb {
            width: 20px; height: 20px;
            border-radius: 50%;
            background: #ED730C;
            cursor: pointer;
            border: 3px solid #fff;
            box-shadow: 0 1px 6px rgba(237,115,12,0.4);
        }
    </style>
</head>
<body>
    <div id="onboarding-app" data-user="{{ json_encode($user ?? []) }}"></div>

    @stack('scripts')
</body>
</html>