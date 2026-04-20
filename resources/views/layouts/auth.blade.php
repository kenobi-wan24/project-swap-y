<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SWAPY — Sign In')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;0,9..40,800&family=Inter:wght@400;500;600;700;800;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'DM Sans', sans-serif;
            color: #3A3330;
            height: 100vh;
            overflow: hidden;
            display: flex;
        }

        /* ── LEFT PANEL ── point 3: 50% width */
        .auth-left {
            width: 50%;
            flex-shrink: 0;
            position: relative;
            overflow: hidden;
            display: none;
        }
        @media (min-width: 900px) {
            .auth-left { display: block; }
            .auth-right { width: 50% !important; }
        }

        .auth-left-bg {
            position: absolute;
            inset: 0;
            background-image: url('/images/friends-swapping.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }
        /* Dark overlay so glass card text stays readable */
        .auth-left-bg::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(
                to bottom,
                rgba(0,0,0,0.50) 0%,
                rgba(0,0,0,0.35) 40%,
                rgba(0,0,0,0.75) 100%
            );
        }

        /* point 2: lighter, more readable glass card; center-bottom position */
        .auth-left-card {
            position: absolute;
            bottom: 60px;
            left: 36px;
            right: 36px;
            background: rgba(255,255,255,0.15);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border: 1px solid rgba(255,255,255,0.3);
            border-radius: 24px;
            padding: 36px 32px;
        }

        /* ── RIGHT PANEL ── point 4: pure white */
        .auth-right {
            width: 100%;
            background: #ffffff;
            display: flex;
            flex-direction: column;
            overflow-y: auto;
            position: relative;
        }

        /* point 5: more breathing room at top, larger logo */
        .auth-right-topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 32px 48px;
            flex-shrink: 0;
        }

        /* Form container */
        .auth-form-wrap {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px 48px;
        }
        .auth-form-inner {
            width: 100%;
            max-width: 460px;
        }

        /* point 6: larger tab text, more tab spacing */
        .auth-tabs {
            display: flex;
            border-bottom: 1.5px solid #e5e7eb;
            margin-bottom: 36px;
        }
        .auth-tab-btn {
            font-size: 1.1rem;
            font-weight: 600;
            font-family: 'DM Sans', sans-serif;
            background: none;
            border: none;
            padding: 0 0 16px 0;
            margin-right: 36px;
            cursor: pointer;
            color: #9ca3af;
            position: relative;
            transition: color 0.15s;
        }
        .auth-tab-btn.active { color: #ED730C; }
        .auth-tab-btn.active::after {
            content: '';
            position: absolute;
            bottom: -1.5px;
            left: 0; right: 0;
            height: 2.5px;
            background: #ED730C;
            border-radius: 2px 2px 0 0;
        }

        /* point 7: visible border at rest, taller padding */
        .field-label {
            display: block;
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: #6b7280;
            margin-bottom: 7px;
        }
        .field-input {
            width: 100%;
            padding: 15px 16px;
            background: #f3f4f6;
            border: 1.5px solid #e5e7eb;
            border-radius: 12px;
            font-size: 0.9rem;
            font-family: 'DM Sans', sans-serif;
            color: #3A3330;
            outline: none;
            transition: border-color 0.15s, background 0.15s;
        }
        .field-input:focus {
            border-color: #ED730C;
            background: #fff;
        }
        .field-input::placeholder { color: #b0b7c3; }

        /* Username availability badge */
        .avail-badge {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 0.65rem;
            font-weight: 700;
            padding: 3px 8px;
            border-radius: 6px;
            letter-spacing: .04em;
        }
        .avail-ok  { background: #d1fae5; color: #065f46; }
        .avail-no  { background: #fee2e2; color: #991b1b; }
        .avail-chk { background: #f3f4f6; color: #6b7280; }

        /* point 8: pill button, clear arrow */
        .btn-primary {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            width: 100%;
            padding: 16px;
            background: #ED730C;
            color: #fff;
            font-size: 1rem;
            font-weight: 700;
            font-family: 'DM Sans', sans-serif;
            border: none;
            border-radius: 999px;
            cursor: pointer;
            transition: background 0.15s;
            letter-spacing: 0.01em;
        }
        .btn-primary:hover { background: #d4620a; }
        .btn-primary:disabled { opacity: 0.7; cursor: not-allowed; }

        /* point 10: equal 50/50 OAuth buttons */
        .oauth-row {
            display: flex;
            gap: 12px;
        }
        .btn-oauth {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 14px 16px;
            background: #fff;
            border: 1.5px solid #e5e7eb;
            border-radius: 12px;
            font-size: 0.875rem;
            font-weight: 600;
            font-family: 'DM Sans', sans-serif;
            color: #3A3330;
            cursor: pointer;
            transition: border-color 0.15s, background 0.15s;
            text-decoration: none;
            min-width: 0;
        }
        .btn-oauth:hover {
            border-color: #d1d5db;
            background: #f9fafb;
        }

        /* point 9: OR divider */
        .or-divider {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 16px;
        }
        .or-divider::before,
        .or-divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #e5e7eb;
        }
        .or-divider span {
            font-size: 0.72rem;
            font-weight: 700;
            color: #9ca3af;
            letter-spacing: .08em;
            text-transform: uppercase;
            white-space: nowrap;
        }

        /* Footer bottom bar */
        .auth-bottom-bar {
            padding: 16px 48px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-shrink: 0;
        }
        @media (max-width: 600px) {
            .auth-right-topbar, .auth-form-wrap, .auth-bottom-bar { padding-left: 20px; padding-right: 20px; }
        }
    </style>
</head>
<body>

    {{-- ══ LEFT DECORATIVE PANEL ══ --}}
    <div class="auth-left">
        <div class="auth-left-bg"></div>

        {{-- Glass card with tagline --}}
        <div class="auth-left-card">
            <h2 style="font-size:1.75rem;font-weight:800;color:#fff;line-height:1.2;margin-bottom:12px;">
                Swap Anything.<br>
                <span style="color:#ED730C;">Zero Cash Needed</span>
            </h2>
            <p style="font-size:0.875rem;color:rgba(255,255,255,0.75);line-height:1.65;margin-bottom:20px;">
                Join the world's most sophisticated peer-to-peer exchange ecosystem. Experience the Kinetic Exchange where value is always in motion.
            </p>

            {{-- Matching now indicator --}}
            <div style="display:flex;align-items:center;gap:12px;">
                {{-- Stacked avatars --}}
                <div style="display:flex;align-items:center;">
                    <div style="width:34px;height:34px;border-radius:50%;background:#0d9488;border:2px solid rgba(255,255,255,0.4);display:flex;align-items:center;justify-content:center;font-size:0.7rem;font-weight:700;color:#fff;">VS</div>
                    <div style="width:34px;height:34px;border-radius:50%;background:#ED730C;border:2px solid rgba(255,255,255,0.4);margin-left:-10px;display:flex;align-items:center;justify-content:center;font-size:0.7rem;font-weight:700;color:#fff;">MC</div>
                    <div style="width:34px;height:34px;border-radius:50%;background:#149189;border:2px solid rgba(255,255,255,0.4);margin-left:-10px;display:flex;align-items:center;justify-content:center;font-size:0.65rem;font-weight:800;color:#fff;">+2k</div>
                </div>
                <span style="font-size:0.8rem;font-weight:700;color:#149189;letter-spacing:.04em;text-transform:uppercase;">Matching Now</span>
            </div>
        </div>
    </div>

    {{-- ══ RIGHT FORM PANEL ══ --}}
    <div class="auth-right">

        {{-- Top bar: logo + back to home --}}
        <div class="auth-right-topbar">
            <a href="{{ route('home') }}" style="display:inline-flex;align-items:center;text-decoration:none;">
                <img src="{{ asset('images/logo-swapy.png') }}" alt="SWAPY" style="height:64px;object-fit:contain;">
            </a>

            <a href="{{ route('home') }}"
                style="font-size:0.875rem;font-weight:600;color:#149189;text-decoration:none;"
                onmouseover="this.style.color='#0d6b64'"
                onmouseout="this.style.color='#149189'">
                    Back to Home
            </a>
        </div>

        {{-- Form area --}}
        <div class="auth-form-wrap">
            <div class="auth-form-inner">
                @yield('auth-form')
            </div>
        </div>

        {{-- Bottom copyright bar --}}
        <div class="auth-bottom-bar">
            <span style="font-size:0.78rem;color:#9ca3af;">&copy; {{ date('Y') }} SWAPY.</span>
            <div style="display:flex;gap:20px;">
                <a href="#" style="font-size:0.78rem;color:#9ca3af;text-decoration:none;font-weight:600;letter-spacing:.04em;text-transform:uppercase;">Privacy</a>
                <a href="#" style="font-size:0.78rem;color:#9ca3af;text-decoration:none;font-weight:600;letter-spacing:.04em;text-transform:uppercase;">Terms</a>
                <a href="#" style="font-size:0.78rem;color:#9ca3af;text-decoration:none;font-weight:600;letter-spacing:.04em;text-transform:uppercase;">Cookies</a>
            </div>
        </div>

    </div>

    @stack('scripts')
</body>
</html>