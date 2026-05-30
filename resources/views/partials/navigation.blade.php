{{-- resources/views/partials/navigation.blade.php --}}

<style>
    /* ── BASE NAV LINK ── */
    .nav-link {
        position: relative;
        display: inline-flex;
        flex-direction: row;
        align-items: center;
        gap: 7px;
        padding: 6px 14px;
        font-size: 0.875rem;
        font-weight: 600;
        color: #4b5563;
        text-decoration: none;
        border-radius: 8px;
        transition: color 0.2s ease;
        cursor: pointer;
        border: none;
        background: none;
        font-family: inherit;
        white-space: nowrap;
    }
    .nav-link:hover { color: #1a1a1a; }
    .nav-link.active { color: #ED730C; }
    .nav-link.active::after {
        content: '';
        position: absolute;
        bottom: -6px;
        left: 14px;
        right: 14px;
        height: 2.5px;
        background-color: #ED730C;
        border-radius: 2px 2px 0 0;
        transition: opacity 0.3s ease;
    }
    .nav-link img {
        width: 30px;
        height: 30px;
        object-fit: contain;
        transition: transform 0.2s ease, width 0.28s cubic-bezier(0.4,0,0.2,1), height 0.28s cubic-bezier(0.4,0,0.2,1);
        flex-shrink: 0;
    }
    .nav-link:hover img { transform: translateY(-2px) scale(1.1); }

    /* ── 3D CLICK ANIMATION ── */
    @keyframes icon-press {
        0%   { transform: translateY(0px) scale(1) rotateY(0deg); }
        15%  { transform: translateY(2px) scale(0.88) rotateY(20deg); }
        40%  { transform: translateY(-6px) scale(1.18) rotateY(-15deg); }
        65%  { transform: translateY(-2px) scale(1.08) rotateY(8deg); }
        82%  { transform: translateY(-4px) scale(1.13) rotateY(-5deg); }
        100% { transform: translateY(-2px) scale(1.1) rotateY(0deg); }
    }
    .nav-link.icon-clicked img {
        animation: icon-press 0.45s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
    }

    /* ── LABEL HIDE: wrapper collapses width, inner fades+scales ── */
    .nav-link-label-wrap {
        display: inline-block;
        overflow: hidden;
        vertical-align: middle;
        max-width: 200px;
        transition: max-width 0.32s cubic-bezier(0.4, 0, 0.2, 1),
                    margin    0.32s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .nav-link-label {
        display: inline-block;
        white-space: nowrap;
        transform-origin: left center;
        transition:
            opacity   0.22s cubic-bezier(0.4, 0, 0.2, 1),
            transform 0.22s cubic-bezier(0.4, 0, 0.2, 1);
        opacity: 1;
        transform: scaleX(1);
    }
    #main-header.scrolled .nav-link-label-wrap {
        max-width: 0;
        margin-left: -7px;
    }
    #main-header.scrolled .nav-link-label {
        opacity: 0;
        transform: scaleX(0);
        pointer-events: none;
    }
    #main-header.scrolled .nav-link {
        gap: 0;
        padding: 6px 10px;
    }
    #main-header.scrolled .nav-link img {
        width: 30px;
        height: 30px;
    }
    #main-header.scrolled .nav-link.active::after {
        opacity: 0;
    }

    /* ── PILL SEPARATORS (only visible in scrolled/pill mode) ── */
    #nav-pill .nav-link + .nav-link::before {
        content: '';
        position: absolute;
        left: -3px;
        top: 20%;
        height: 60%;
        width: 1px;
        background-color: #e5e7eb;
        border-radius: 1px;
        opacity: 0;
        transition: opacity 0.35s cubic-bezier(0.4, 0, 0.2, 1);
    }
    #main-header.scrolled #nav-pill .nav-link + .nav-link::before {
        opacity: 1;
    }

    /* ── MOBILE NAV LINK ── */
    .nav-link-mobile {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 10px 12px;
        border-radius: 12px;
        font-size: 0.875rem;
        font-weight: 500;
        color: #4b5563;
        text-decoration: none;
        transition: background-color 0.15s ease, color 0.15s ease;
    }
    .nav-link-mobile:hover { background-color: #f9fafb; }
    .nav-link-mobile.active { background-color: #fff4ec; color: #ED730C; font-weight: 600; }
    .nav-link-mobile img { width: 26px; height: 26px; object-fit: contain; }

    /* ── ICON BUTTONS ── */
    .nav-icon-btn {
        width: 38px; height: 38px;
        border-radius: 50%;
        background: none;
        border: none;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #6b7280;
        transition: background 0.15s;
    }
    .nav-icon-btn:hover { background: #f3f4f6; }

    /* ── DROPDOWN ITEMS ── */
    .dropdown-item {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px 16px;
        font-size: 0.875rem;
        color: #4b5563;
        text-decoration: none;
        transition: background 0.1s;
        cursor: pointer;
        width: 100%;
        background: none;
        border: none;
        font-family: inherit;
        text-align: left;
        box-sizing: border-box;
    }
    .dropdown-item:hover { background: #f9fafb; }
    .dropdown-item.danger { color: #ef4444; }
    .dropdown-item.premium { color: #ED730C; font-weight: 600; }

    /* ── HEADER ── */
    #main-header {
        background: #fff;
        border-bottom: 1px solid #e5e7eb;
        position: sticky;
        top: 0;
        z-index: 50;
        transition: border-color 0.35s ease;
        transform: translateZ(0);
        will-change: transform;
        backface-visibility: hidden;
    }

    .header-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        height: 80px;
        max-width: 80rem;
        margin: 0 auto;
        padding: 0 2rem;
    }

    /* ── PILL: only center nav morphs ── */
    #nav-pill {
        display: flex;
        align-items: center;
        gap: 4px;
        padding: 0 6px;
        border-radius: 999px;
        min-width: 180px;
        justify-content: center;
        transition:
            background    0.35s cubic-bezier(0.4, 0, 0.2, 1),
            box-shadow    0.35s cubic-bezier(0.4, 0, 0.2, 1),
            border-color  0.35s cubic-bezier(0.4, 0, 0.2, 1),
            padding       0.35s cubic-bezier(0.4, 0, 0.2, 1),
            min-width     0.35s cubic-bezier(0.4, 0, 0.2, 1);
        border: 1.5px solid transparent;
    }
    #main-header.scrolled #nav-pill {
        background: #fff;
        box-shadow: 0 4px 24px rgba(0,0,0,0.10);
        border-color: #ececec;
        padding: 8px 18px;
        min-width: 0;
    }
    #main-header.scrolled {
        border-bottom-color: transparent;
    }

    @media (max-width: 767px) {
        .mobile-hamburger { display: block !important; }
        .header-row { padding: 0 1rem; }
    }
</style>

<header id="main-header">
    <div class="header-row">

        {{-- LOGO --}}
        <a href="{{ auth()->check() ? route('browse') : route('home') }}" style="text-decoration:none;flex-shrink:0;" class="flex items-center">
            <img src="{{ asset('images/logo-swapy.png') }}" alt="SWAPY" style="height:52px;object-fit:contain;display:block;">
        </a>

        @php
            $currentRoute = Route::currentRouteName();
        @endphp

        {{-- CENTER NAV PILL --}}
        <div id="nav-pill-wrap" class="hidden md:flex">
            <div id="nav-pill">

                @guest
                    <a href="{{ route('home') }}" class="nav-link {{ $currentRoute === 'home' ? 'active' : '' }}">
                        <img src="{{ asset('images/icons/home.png') }}" alt="">
                        <span class="nav-link-label-wrap"><span class="nav-link-label">Home</span></span>
                    </a>
                    <a href="{{ route('browse') }}" class="nav-link {{ $currentRoute === 'browse' ? 'active' : '' }}">
                        <img src="{{ asset('images/icons/browse.png') }}" alt="">
                        <span class="nav-link-label-wrap"><span class="nav-link-label">Browse</span></span>
                    </a>
                    <a href="{{ route('garage-sale') }}" class="nav-link {{ $currentRoute === 'garage-sale' ? 'active' : '' }}">
                        <img src="{{ asset('images/icons/garage-sale.png') }}" alt="">
                        <span class="nav-link-label-wrap"><span class="nav-link-label">Garage Sale</span></span>
                    </a>
                    <a href="{{ route('services') }}" class="nav-link {{ $currentRoute === 'services' ? 'active' : '' }}">
                        <img src="{{ asset('images/icons/services.png') }}" alt="">
                        <span class="nav-link-label-wrap"><span class="nav-link-label">Services</span></span>
                    </a>
                    <a href="{{ route('how-it-works') }}" class="nav-link {{ $currentRoute === 'how-it-works' ? 'active' : '' }}">
                        <img src="{{ asset('images/icons/how-it-works.png') }}" alt="">
                        <span class="nav-link-label-wrap"><span class="nav-link-label">How It Works</span></span>
                    </a>
                @endguest

                @auth
                    <a href="{{ route('browse') }}" class="nav-link {{ $currentRoute === 'browse' ? 'active' : '' }}">
                        <img src="{{ asset('images/icons/browse.png') }}" alt="">
                        <span class="nav-link-label-wrap"><span class="nav-link-label">Browse</span></span>
                    </a>
                    <a href="{{ route('garage-sale') }}" class="nav-link {{ $currentRoute === 'garage-sale' ? 'active' : '' }}">
                        <img src="{{ asset('images/icons/garage-sale.png') }}" alt="">
                        <span class="nav-link-label-wrap"><span class="nav-link-label">Garage Sale</span></span>
                    </a>
                    <a href="{{ route('services') }}" class="nav-link {{ $currentRoute === 'services' ? 'active' : '' }}">
                        <img src="{{ asset('images/icons/services.png') }}" alt="">
                        <span class="nav-link-label-wrap"><span class="nav-link-label">Services</span></span>
                    </a>
                    <a href="#" class="nav-link">
                        <img src="{{ asset('images/icons/matches.png') }}" alt="">
                        <span class="nav-link-label-wrap"><span class="nav-link-label">Matches</span></span>
                    </a>
                    <a href="{{ route('user.store', auth()->user()->username) }}"
                       class="nav-link {{ $currentRoute === 'user.store' ? 'active' : '' }}">
                        <img src="{{ asset('images/icons/my-store.png') }}" alt="">
                        <span class="nav-link-label-wrap"><span class="nav-link-label">My Store</span></span>
                    </a>
                @endauth

            </div>
        </div>

        {{-- RIGHT: AUTH ACTIONS --}}
        <div class="hidden md:flex items-center" style="gap:4px;">
            @auth

                <button class="nav-icon-btn">
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                </button>

                <button class="nav-icon-btn">
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                    </svg>
                </button>

                {{-- Avatar + Dropdown --}}
                <div style="position:relative;margin-left:4px;" id="user-menu-wrap">
                    <button onclick="toggleUserMenu()"
                        style="width:40px;height:40px;border-radius:50%;background:#ED730C;color:#fff;font-weight:700;font-size:0.8rem;border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}{{ strtoupper(substr(strstr(auth()->user()->name, ' ') ?: ' ', 1, 1)) }}
                    </button>

                    <div id="user-dropdown"
                        style="display:none;position:absolute;right:0;top:50px;width:248px;background:#fff;border-radius:16px;box-shadow:0 8px 32px rgba(0,0,0,0.13);border:1px solid #f3f4f6;z-index:99;overflow:hidden;">

                        <div style="display:flex;align-items:center;gap:12px;padding:16px;border-bottom:1px solid #f3f4f6;">
                            <div style="width:42px;height:42px;border-radius:50%;background:#ED730C;color:#fff;font-weight:700;font-size:0.85rem;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}{{ strtoupper(substr(strstr(auth()->user()->name, ' ') ?: ' ', 1, 1)) }}
                            </div>
                            <div style="min-width:0;">
                                <p style="font-size:0.9rem;font-weight:700;color:#3A3330;margin:0;line-height:1.3;">{{ auth()->user()->name }}</p>
                                <p style="font-size:0.78rem;color:#9ca3af;margin:2px 0 0;">&#64;{{ auth()->user()->username }}</p>
                            </div>
                        </div>

                        <div style="padding:6px 0;">
                            <a href="{{ route('dashboard') }}" class="dropdown-item">
                                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="flex-shrink:0;opacity:0.45;">
                                    <rect x="3" y="3" width="7" height="7" rx="1" stroke-width="2"/><rect x="14" y="3" width="7" height="7" rx="1" stroke-width="2"/><rect x="3" y="14" width="7" height="7" rx="1" stroke-width="2"/><rect x="14" y="14" width="7" height="7" rx="1" stroke-width="2"/>
                                </svg>
                                Dashboard
                            </a>
                            <a href="{{ route('profile') }}" class="dropdown-item">
                                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="flex-shrink:0;opacity:0.45;">
                                    <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2M12 11a4 4 0 100-8 4 4 0 000 8z"/>
                                </svg>
                                My Profile
                            </a>
                            <a href="#" class="dropdown-item">
                                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="flex-shrink:0;opacity:0.45;">
                                    <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                Friends
                            </a>
                            <a href="#" class="dropdown-item">
                                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="flex-shrink:0;opacity:0.45;">
                                    <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><circle cx="12" cy="12" r="3" stroke-width="2"/>
                                </svg>
                                Settings
                            </a>
                            <a href="#" class="dropdown-item">
                                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="flex-shrink:0;opacity:0.45;">
                                    <circle cx="12" cy="12" r="10" stroke-width="2"/><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M10 8l6 4-6 4V8z"/>
                                </svg>
                                Video Tutorials
                            </a>
                        </div>

                        <div style="padding:6px 0;border-top:1px solid #f3f4f6;">
                            <a href="#" class="dropdown-item premium">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="#ED730C" style="flex-shrink:0;">
                                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                </svg>
                                Upgrade to Premium
                            </a>
                        </div>

                        <div style="padding:6px 0;border-top:1px solid #f3f4f6;">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item danger">
                                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="flex-shrink:0;opacity:0.6;">
                                        <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h6a2 2 0 012 2v1"/>
                                    </svg>
                                    Log Out
                                </button>
                            </form>
                        </div>

                    </div>
                </div>

            @else
                <a href="{{ route('login') }}"
                   style="font-size:0.875rem;font-weight:500;color:#4b5563;padding:6px 12px;text-decoration:none;transition:color .15s;"
                   onmouseover="this.style.color='#1a1a1a'"
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

        {{-- MOBILE HAMBURGER --}}
        <button id="mobile-menu-btn" onclick="toggleMobileMenu()"
            aria-label="Toggle navigation menu" aria-expanded="false"
            style="display:none;padding:8px;border-radius:8px;background:none;border:none;cursor:pointer;color:#6b7280;"
            class="mobile-hamburger">
            <svg id="icon-open" width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
            <svg id="icon-close" width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display:none;">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>

    </div>

    {{-- MOBILE MENU --}}
    <div id="mobile-menu" style="display:none;border-top:1px solid #f3f4f6;background:#fff;">
        <div style="padding:12px 16px 16px;display:flex;flex-direction:column;gap:4px;">

            @guest
                <a href="{{ route('home') }}" class="nav-link-mobile {{ $currentRoute === 'home' ? 'active' : '' }}">
                    <img src="{{ asset('images/icons/home.png') }}" alt=""> Home
                </a>
            @endguest

            <a href="{{ route('browse') }}" class="nav-link-mobile {{ $currentRoute === 'browse' ? 'active' : '' }}">
                <img src="{{ asset('images/icons/browse.png') }}" alt=""> Browse
            </a>
            <a href="{{ route('garage-sale') }}" class="nav-link-mobile {{ $currentRoute === 'garage-sale' ? 'active' : '' }}">
                <img src="{{ asset('images/icons/garage-sale.png') }}" alt=""> Garage Sale
            </a>
            <a href="{{ route('services') }}" class="nav-link-mobile {{ $currentRoute === 'services' ? 'active' : '' }}">
                <img src="{{ asset('images/icons/services.png') }}" alt=""> Services
            </a>

            @guest
                <a href="{{ route('how-it-works') }}" class="nav-link-mobile {{ $currentRoute === 'how-it-works' ? 'active' : '' }}">
                    <img src="{{ asset('images/icons/how-it-works.png') }}" alt=""> How It Works
                </a>
            @endguest

            @auth
                <a href="#" class="nav-link-mobile">
                    <img src="{{ asset('images/icons/matches.png') }}" alt=""> Matches
                </a>
                <a href="{{ route('user.store', auth()->user()->username) }}"
                   class="nav-link-mobile {{ $currentRoute === 'user.store' ? 'active' : '' }}">
                    <img src="{{ asset('images/icons/my-store.png') }}" alt=""> My Store
                </a>
            @endauth

            <div style="border-top:1px solid #f3f4f6;padding-top:12px;margin-top:8px;display:flex;gap:10px;">
                @auth
                    <a href="{{ route('dashboard') }}"
                       style="flex:1;text-align:center;font-size:0.875rem;font-weight:700;color:#fff;background:#ED730C;border-radius:999px;padding:10px 0;text-decoration:none;">
                        Dashboard
                    </a>
                    <form method="POST" action="{{ route('logout') }}" style="flex:1;">
                        @csrf
                        <button type="submit"
                            style="width:100%;font-size:0.875rem;font-weight:600;color:#ef4444;border:1px solid #fecaca;border-radius:999px;padding:10px 0;background:none;cursor:pointer;font-family:inherit;">
                            Log Out
                        </button>
                    </form>
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

<script>
    var header = document.getElementById('main-header');

    // ── 3D icon click animation ──
    document.querySelectorAll('.nav-link').forEach(function(link) {
        link.addEventListener('click', function() {
            var self = this;
            self.classList.remove('icon-clicked');
            void self.offsetWidth; // force reflow to restart animation
            self.classList.add('icon-clicked');
            setTimeout(function() {
                self.classList.remove('icon-clicked');
            }, 450);
        });
    });

    // Scroll morph
    window.addEventListener('scroll', function () {
        if (window.scrollY > 50) {
            header.classList.add('scrolled');
        } else {
            header.classList.remove('scrolled');
        }
    }, { passive: true });

    function toggleMobileMenu() {
        var menu   = document.getElementById('mobile-menu');
        var open   = document.getElementById('icon-open');
        var close  = document.getElementById('icon-close');
        var btn    = document.getElementById('mobile-menu-btn');
        var isOpen = menu.style.display !== 'none';
        menu.style.display  = isOpen ? 'none' : 'block';
        open.style.display  = isOpen ? 'block' : 'none';
        close.style.display = isOpen ? 'none' : 'block';
        btn.setAttribute('aria-expanded', String(!isOpen));
    }

    function toggleUserMenu() {
        var d = document.getElementById('user-dropdown');
        d.style.display = d.style.display === 'none' ? 'block' : 'none';
    }

    document.addEventListener('click', function (e) {
        var userWrap = document.getElementById('user-menu-wrap');
        if (userWrap && !userWrap.contains(e.target)) {
            var ud = document.getElementById('user-dropdown');
            if (ud) ud.style.display = 'none';
        }
    });
</script>