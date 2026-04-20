{{-- resources/views/partials/navigation.blade.php --}}

{{-- ===== NAVBAR STYLES — vanilla CSS, no Tailwind dependency ===== --}}
<style>
    /* Active nav link: orange text + underline drawn below the link */
    .nav-link {
        position: relative;
        padding: 6px 12px;
        font-size: 0.875rem;
        font-weight: 500;
        color: #6b7280;
        text-decoration: none;
        border-radius: 8px;
        transition: color 0.15s ease, background-color 0.15s ease;
    }
    .nav-link:hover {
        color: #3A3330;
        background-color: #f9fafb;
    }
    .nav-link.active {
        color: #ED730C;
        font-weight: 600;
        background-color: transparent;
    }
    /* The underline sits just below the link text, inside the sticky header */
    .nav-link.active::after {
        content: '';
        position: absolute;
        bottom: -1px;           /* flush with the header's bottom border */
        left: 12px;
        right: 12px;
        height: 2px;
        background-color: #ED730C;
        border-radius: 2px 2px 0 0;
    }

    /* Mobile active state */
    .nav-link-mobile {
        display: flex;
        align-items: center;
        padding: 10px 12px;
        border-radius: 12px;
        font-size: 0.875rem;
        font-weight: 500;
        color: #4b5563;
        text-decoration: none;
        transition: background-color 0.15s ease, color 0.15s ease;
    }
    .nav-link-mobile:hover {
        background-color: #f9fafb;
    }
    .nav-link-mobile.active {
        background-color: #fff4ec;
        color: #ED730C;
        font-weight: 600;
    }
</style>

<header style="background:#fff; border-bottom:1px solid #f3f4f6; position:sticky; top:0; z-index:50;">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">

            {{-- ===== BRAND LOGO + NAME ===== --}}
            <a href="{{ route('home') }}" class="flex items-center style="gap:2px;" flex-shrink-0" style="text-decoration:none;">
                {{-- Logo icon: larger than the text --}}
                <img
                    src="{{ asset('images/logo-swapy.png') }}"
                    alt="SWAPY"
                    style="height:60px; object-fit:contain; display:block;"
                >
                {{-- Brand name: primary color, extra bold, slightly smaller than logo --}}
            </a>

            {{-- ===== DESKTOP NAV LINKS ===== --}}
            @php
                $navLinks = [
                    ['label' => 'Home',         'route' => 'home',         'href' => route('home')],
                    ['label' => 'Browse',        'route' => 'browse',       'href' => route('browse')],
                    ['label' => 'Garage Sale',   'route' => 'garage-sale',  'href' => route('garage-sale')],
                    ['label' => 'Services',      'route' => 'services',     'href' => route('services')],
                    ['label' => 'How It Works',  'route' => 'how-it-works', 'href' => route('how-it-works')],
                ];
                $currentRoute = Route::currentRouteName();
            @endphp

            <nav class="hidden md:flex items-center" style="gap:2px;">
                @foreach ($navLinks as $link)
                    <a
                        href="{{ $link['href'] }}"
                        class="nav-link {{ $currentRoute === $link['route'] ? 'active' : '' }}"
                    >{{ $link['label'] }}</a>
                @endforeach
            </nav>

            {{-- ===== DESKTOP AUTH BUTTONS ===== --}}
            <div class="hidden md:flex items-center" style="gap:8px;">
                @auth
                    <a href="{{ route('dashboard') }}"
                       style="font-size:0.875rem;font-weight:500;color:#4b5563;padding:6px 12px;text-decoration:none;transition:color .15s;"
                       onmouseover="this.style.color='#3A3330'"
                       onmouseout="this.style.color='#4b5563'">
                        Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}"
                       style="font-size:0.875rem;font-weight:500;color:#4b5563;padding:6px 12px;text-decoration:none;transition:color .15s;"
                       onmouseover="this.style.color='#3A3330'"
                       onmouseout="this.style.color='#4b5563'">
                        Login
                    </a>
                    <a href="{{ route('register') }}"
                       style="font-size:0.875rem;font-weight:700;color:#fff;background:#ED730C;padding:8px 22px;border-radius:999px;text-decoration:none;transition:background .15s;"
                       onmouseover="this.style.background='#d4620a'"
                       onmouseout="this.style.background='#ED730C'">
                        Sign Up
                    </a>
                @endauth
            </div>

            {{-- ===== MOBILE HAMBURGER ===== --}}
            <button
                id="mobile-menu-btn"
                onclick="toggleMobileMenu()"
                aria-label="Toggle navigation menu"
                aria-expanded="false"
                style="display:none; padding:8px; border-radius:8px; background:none; border:none; cursor:pointer; color:#6b7280;"
                class="mobile-hamburger"
            >
                <svg id="icon-open" width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
                <svg id="icon-close" width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display:none;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>

        </div>
    </div>

    {{-- ===== MOBILE MENU PANEL ===== --}}
    <div id="mobile-menu" style="display:none; border-top:1px solid #f3f4f6; background:#fff;">
        <div style="padding:12px 16px 16px; display:flex; flex-direction:column; gap:4px;">

            @foreach ($navLinks as $link)
                <a
                    href="{{ $link['href'] }}"
                    class="nav-link-mobile {{ $currentRoute === $link['route'] ? 'active' : '' }}"
                >{{ $link['label'] }}</a>
            @endforeach

            {{-- Auth row --}}
            <div style="border-top:1px solid #f3f4f6; padding-top:12px; margin-top:8px; display:flex; gap:10px;">
                @auth
                    <a href="{{ route('dashboard') }}"
                       style="flex:1;text-align:center;font-size:0.875rem;font-weight:700;color:#fff;background:#ED730C;border-radius:999px;padding:10px 0;text-decoration:none;">
                        Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}"
                       style="flex:1;text-align:center;font-size:0.875rem;font-weight:500;color:#4b5563;border:1px solid #e5e7eb;border-radius:999px;padding:10px 0;text-decoration:none;">
                        Login
                    </a>
                    <a href="{{ route('register') }}"
                       style="flex:1;text-align:center;font-size:0.875rem;font-weight:700;color:#fff;background:#ED730C;border-radius:999px;padding:10px 0;text-decoration:none;">
                        Sign Up
                    </a>
                @endauth
            </div>

        </div>
    </div>

</header>

{{-- ===== Responsive: show hamburger on mobile ===== --}}
<style>
    @media (max-width: 767px) {
        .mobile-hamburger { display:block !important; }
    }
</style>

{{-- ===== Vanilla JS mobile toggle ===== --}}
<script>
    function toggleMobileMenu() {
        var menu   = document.getElementById('mobile-menu');
        var btn    = document.getElementById('mobile-menu-btn');
        var open   = document.getElementById('icon-open');
        var close  = document.getElementById('icon-close');
        var isOpen = menu.style.display !== 'none';

        menu.style.display  = isOpen ? 'none' : 'block';
        open.style.display  = isOpen ? 'block' : 'none';
        close.style.display = isOpen ? 'none' : 'block';
        btn.setAttribute('aria-expanded', String(!isOpen));
    }
</script>