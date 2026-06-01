{{-- resources/views/partials/navigation.blade.php --}}

<style>
    /* ── BASE NAV LINK ── */
    .nav-link {
        position: relative;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 16px;
        font-size: 0.875rem;
        font-weight: 600;
        color: #222;
        text-decoration: none;
        border-radius: 999px;
        transition: background 0.15s ease, color 0.15s ease;
        cursor: pointer;
        border: none;
        background: none;
        font-family: inherit;
        white-space: nowrap;
        letter-spacing: -0.01em;
    }
    .nav-link:hover { background: #f5f5f5; }
    .nav-link.active { color: #ED730C; }
    .nav-link.active::after {
        content: '';
        position: absolute;
        bottom: -5px;
        left: 16px;
        right: 16px;
        height: 3px;
        background: #ED730C;
        border-radius: 3px 3px 0 0;
    }

    /* ── DUAL-STATE ICON WRAPPER ── */
    .nav-icon-wrap {
        position: relative;
        width: 34px;
        height: 34px;
        flex-shrink: 0;
        transition: transform 0.2s ease;
    }
    .nav-link:hover .nav-icon-wrap {
        transform: translateY(-2px) scale(1.1);
    }
    .nav-icon-wrap img {
        position: absolute;
        top: 0; left: 0;
        width: 34px;
        height: 34px;
        object-fit: contain;
        transition: opacity 0.25s ease;
    }
    .nav-icon-wrap .icon-inactive { opacity: 1; }
    .nav-icon-wrap .icon-active   { opacity: 0; }

    /* active state */
    .nav-link.active .nav-icon-wrap .icon-inactive { opacity: 0; }
    .nav-link.active .nav-icon-wrap .icon-active   { opacity: 1; }

    /* hover state (not already active) */
    .nav-link:hover:not(.active) .nav-icon-wrap .icon-inactive { opacity: 0; }
    .nav-link:hover:not(.active) .nav-icon-wrap .icon-active   { opacity: 1; }

    /* ── 3D CLICK ANIMATION ── */
    @keyframes icon-press {
        0%   { transform: translateY(0px) scale(1) rotateY(0deg); }
        15%  { transform: translateY(2px) scale(0.88) rotateY(20deg); }
        40%  { transform: translateY(-6px) scale(1.18) rotateY(-15deg); }
        65%  { transform: translateY(-2px) scale(1.08) rotateY(8deg); }
        82%  { transform: translateY(-4px) scale(1.13) rotateY(-5deg); }
        100% { transform: translateY(-2px) scale(1.1) rotateY(0deg); }
    }
    .nav-link.icon-clicked .nav-icon-wrap {
        animation: icon-press 0.45s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
    }

    /* ── HEADER ── */
    #main-header {
        background: #fff;
        border-bottom: 2px solid #EBEBEB;
        position: sticky;
        top: 0;
        z-index: 50;
        transform: translateZ(0);
        will-change: transform;
        backface-visibility: hidden;
        transition: box-shadow 0.3s ease;
    }
    

    .header-row {
        display: grid;
        grid-template-columns: 1fr auto 1fr;
        align-items: center;
        height: 80px;
        max-width: 82rem;
        margin: 0 auto;
        padding: 0 2.5rem;
    }
    .header-right {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 8px;
    }

    /* ── HAMBURGER PILL ── */
    .hamburger-pill {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 6px 8px 6px 12px;
        border: 1px solid transparent;
        border-radius: 999px;
        background: transparent;
        cursor: pointer;
        transition: border-color 0.2s ease, background 0.2s ease, box-shadow 0.2s ease;
        position: relative;
    }
    .hamburger-pill:hover {
        border-color: #ddd;
        background: #fff;
        box-shadow: 0 2px 12px rgba(0,0,0,0.12);
    }
    .hamburger-pill svg { color: #333; flex-shrink: 0; }

    /* ── HAMBURGER DROPDOWN ── */
    #hamburger-dropdown {
        display: none;
        position: absolute;
        top: calc(100% + 10px);
        right: 0;
        width: 280px;
        background: #fff;
        border-radius: 20px;
        box-shadow: 0 8px 40px rgba(0,0,0,0.14);
        border: 1px solid #ebebeb;
        z-index: 99;
        overflow-y: auto;
        max-height: calc(100vh - 120px);
    }

    .ham-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 20px;
        font-size: 0.875rem;
        font-weight: 500;
        color: #333;
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
    .ham-item:hover { background: #f7f7f7; }
    .ham-item.bold { font-weight: 700; }
    .ham-item.danger { color: #ef4444; }
    .ham-item.premium { color: #ED730C; font-weight: 700; }
    .ham-divider {
        height: 1px;
        background: #f0f0f0;
        margin: 4px 0;
    }

    /* ── MOBILE NAV LINK ── */
    .nav-link-mobile {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 11px 14px;
        border-radius: 12px;
        font-size: 0.9rem;
        font-weight: 500;
        color: #333;
        text-decoration: none;
        transition: background 0.15s ease, color 0.15s ease;
    }
    .nav-link-mobile:hover { background: #f7f7f7; }
    .nav-link-mobile.active { background: #fff4ec; color: #ED730C; font-weight: 600; }
    .nav-link-mobile .nav-icon-wrap {
        width: 28px;
        height: 28px;
        flex-shrink: 0;
    }
    .nav-link-mobile .nav-icon-wrap img {
        width: 28px;
        height: 28px;
    }
    .nav-link-mobile.active .nav-icon-wrap .icon-inactive { opacity: 0; }
    .nav-link-mobile.active .nav-icon-wrap .icon-active   { opacity: 1; }
    .nav-link-mobile img { width: 28px; height: 28px; object-fit: contain; }

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
        color: #555;
        transition: background 0.15s;
    }
    .nav-icon-btn:hover { background: #f0f0f0; }

    @media (max-width: 767px) {
        .desktop-nav { display: none !important; }
        .mobile-hamburger { display: flex !important; }
        .header-row { padding: 0 1.25rem; height: 68px; }
    }
    @media (min-width: 768px) {
        .mobile-only { display: none !important; }
    }

    /* ── STICKY SEARCH SLOT ── */
    #nav-sticky-search {
        overflow: hidden;
        max-height: 0;
        opacity: 0;
        transition: max-height 0.4s cubic-bezier(0.4,0,0.2,1), opacity 0.3s ease, padding 0.4s cubic-bezier(0.4,0,0.2,1);
        padding: 0 2.5rem;
        background: #fff;
    }
    #nav-sticky-search.open {
        max-height: 100px;
        opacity: 1;
        padding: 12px 2.5rem 16px;
        border-bottom: 2px solid #EBEBEB;
    }
    /* Suppress the header's own bottom border when search row is visible */
    #main-header.scrolled { border-bottom-color: transparent; }
    @media (max-width: 767px) {
        #nav-sticky-search.open { padding: 12px 1.25rem 14px; }
    }
</style>

<header id="main-header">
    <div class="header-row">

        {{-- LOGO --}}
        <a href="{{ auth()->check() ? route('browse') : route('home') }}"
           style="text-decoration:none;flex-shrink:0;display:flex;align-items:center;">
            <img src="{{ asset('images/logo-swapy.png') }}" alt="SWAPY"
                 style="height:48px;object-fit:contain;display:block;">
        </a>

        @php $currentRoute = Route::currentRouteName(); @endphp

        {{-- CENTER NAV (desktop) --}}
        <nav class="desktop-nav hidden md:flex items-center" style="gap:2px;">

            {{-- ITEMS --}}
            <a href="{{ route('browse') }}"
               class="nav-link {{ $currentRoute === 'browse' ? 'active' : '' }}">
                <span class="nav-icon-wrap">
                    <img class="icon-inactive" src="{{ asset('images/icons/items-inactive.png') }}" alt="">
                    <img class="icon-active"   src="{{ asset('images/icons/items-active.png') }}"   alt="">
                </span>
                Items
            </a>

            {{-- HOMES --}}
            <a href="#"
               class="nav-link {{ $currentRoute === 'homes' ? 'active' : '' }}">
                <span class="nav-icon-wrap">
                    <img class="icon-inactive" src="{{ asset('images/icons/homes-inactive.png') }}" alt="">
                    <img class="icon-active"   src="{{ asset('images/icons/homes-active.png') }}"   alt="">
                </span>
                Homes
            </a>

            {{-- GARAGE SALE --}}
            <a href="{{ route('garage-sale') }}"
               class="nav-link {{ $currentRoute === 'garage-sale' ? 'active' : '' }}">
                <span class="nav-icon-wrap">
                    <img class="icon-inactive" src="{{ asset('images/icons/garage-sale-inactive.png') }}" alt="">
                    <img class="icon-active"   src="{{ asset('images/icons/garage-sale-active.png') }}"   alt="">
                </span>
                Garage Sale
            </a>

            {{-- SERVICES --}}
            <a href="{{ route('services') }}"
               class="nav-link {{ $currentRoute === 'services' ? 'active' : '' }}">
                <span class="nav-icon-wrap">
                    <img class="icon-inactive" src="{{ asset('images/icons/services-inactive.png') }}" alt="">
                    <img class="icon-active"   src="{{ asset('images/icons/services-active.png') }}"   alt="">
                </span>
                Services
            </a>

        </nav>

        {{-- RIGHT SIDE --}}
        <div class="header-right desktop-nav hidden md:flex">

            {{-- POST A SWAP CTA --}}
            @auth
                <a href="{{ route('dashboard') }}"
                   style="font-size:0.875rem;font-weight:700;color:#222;background:transparent;padding:10px 16px;border-radius:999px;text-decoration:none;transition:background .2s, color .2s;white-space:nowrap;"
                   onmouseover="this.style.background='#ED730C';this.style.color='#fff'"
                   onmouseout="this.style.background='transparent';this.style.color='#222'">
                    Post a Swap
                </a>
            @else
                <a href="{{ route('login') }}"
                   style="font-size:0.875rem;font-weight:700;color:#222;background:transparent;padding:10px 16px;border-radius:999px;text-decoration:none;transition:background .2s, color .2s;white-space:nowrap;"
                   onmouseover="this.style.background='#ED730C';this.style.color='#fff'"
                   onmouseout="this.style.background='transparent';this.style.color='#222'">
                    Post a Swap
                </a>
            @endauth

            {{-- HAMBURGER PILL (always visible) --}}
            <div style="position:relative;" id="hamburger-wrap">
                <button class="hamburger-pill" onclick="toggleHamburger()">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                    @auth
                        <div style="width:32px;height:32px;border-radius:50%;background:#ED730C;color:#fff;font-weight:700;font-size:0.75rem;display:flex;align-items:center;justify-content:center;letter-spacing:0.03em;">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}{{ strtoupper(substr(strstr(auth()->user()->name, ' ') ?: ' ', 1, 1)) }}
                        </div>
                    @else
                        <div style="width:32px;height:32px;border-radius:50%;background:#f0f0f0;display:flex;align-items:center;justify-content:center;">
                            <svg width="16" height="16" fill="none" stroke="#888" viewBox="0 0 24 24">
                                <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2M12 11a4 4 0 100-8 4 4 0 000 8z"/>
                            </svg>
                        </div>
                    @endauth
                </button>

                {{-- HAMBURGER DROPDOWN --}}
                <div id="hamburger-dropdown">

                    @auth
                        {{-- User info --}}
                        <div style="display:flex;align-items:center;gap:12px;padding:16px 20px;border-bottom:1px solid #f0f0f0;">
                            <div style="width:44px;height:44px;border-radius:50%;background:#ED730C;color:#fff;font-weight:700;font-size:0.875rem;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}{{ strtoupper(substr(strstr(auth()->user()->name, ' ') ?: ' ', 1, 1)) }}
                            </div>
                            <div>
                                <p style="font-size:0.9rem;font-weight:700;color:#111;margin:0;">{{ auth()->user()->name }}</p>
                                <p style="font-size:0.78rem;color:#aaa;margin:2px 0 0;">&#64;{{ auth()->user()->username }}</p>
                            </div>
                        </div>

                        <div style="padding:6px 0;">
                            <a href="#" class="ham-item">
                                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="opacity:0.45;flex-shrink:0;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                </svg>
                                Notifications
                            </a>
                            <a href="#" class="ham-item">
                                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="opacity:0.45;flex-shrink:0;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                </svg>
                                Messages
                            </a>
                            <a href="{{ route('how-it-works') }}" class="ham-item">
                                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="opacity:0.45;flex-shrink:0;">
                                    <circle cx="12" cy="12" r="10" stroke-width="2"/><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M9.09 9a3 3 0 015.83 1c0 2-3 3-3 3M12 17h.01"/>
                                </svg>
                                How It Works
                            </a>
                            <a href="#" class="ham-item">
                                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="opacity:0.45;flex-shrink:0;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                </svg>
                                Matches
                            </a>
                        </div>

                        <div class="ham-divider"></div>

                        <div style="padding:6px 0;">
                            <a href="{{ route('user.store', auth()->user()->username) }}" class="ham-item bold">
                                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="opacity:0.45;flex-shrink:0;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                                My Store
                            </a>
                            <a href="{{ route('dashboard') }}" class="ham-item">
                                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="opacity:0.45;flex-shrink:0;">
                                    <rect x="3" y="3" width="7" height="7" rx="1" stroke-width="2"/><rect x="14" y="3" width="7" height="7" rx="1" stroke-width="2"/><rect x="3" y="14" width="7" height="7" rx="1" stroke-width="2"/><rect x="14" y="14" width="7" height="7" rx="1" stroke-width="2"/>
                                </svg>
                                Dashboard
                            </a>
                            <a href="{{ route('profile') }}" class="ham-item">
                                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="opacity:0.45;flex-shrink:0;">
                                    <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2M12 11a4 4 0 100-8 4 4 0 000 8z"/>
                                </svg>
                                My Profile
                            </a>
                            <a href="#" class="ham-item">
                                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="opacity:0.45;flex-shrink:0;">
                                    <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                Friends
                            </a>
                            <a href="#" class="ham-item">
                                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="opacity:0.45;flex-shrink:0;">
                                    <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><circle cx="12" cy="12" r="3" stroke-width="2"/>
                                </svg>
                                Settings
                            </a>
                        </div>

                        <div class="ham-divider"></div>

                        <div style="padding:6px 0;">
                            <a href="#" class="ham-item premium">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="#ED730C" style="flex-shrink:0;">
                                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                </svg>
                                Upgrade to Premium
                            </a>
                        </div>

                        <div class="ham-divider"></div>

                        <div style="padding:6px 0;">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="ham-item danger">
                                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="opacity:0.5;flex-shrink:0;">
                                        <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h6a2 2 0 012 2v1"/>
                                    </svg>
                                    Log Out
                                </button>
                            </form>
                        </div>

                    @else

                        <div style="padding:6px 0;">
                            <a href="{{ route('how-it-works') }}" class="ham-item bold">
                                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="opacity:0.45;flex-shrink:0;">
                                    <circle cx="12" cy="12" r="10" stroke-width="2"/><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M9.09 9a3 3 0 015.83 1c0 2-3 3-3 3M12 17h.01"/>
                                </svg>
                                How It Works
                            </a>
                        </div>

                        <div class="ham-divider"></div>

                        <div style="padding:6px 0;">
                            <a href="{{ route('login') }}" class="ham-item bold">
                                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="opacity:0.45;flex-shrink:0;">
                                    <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                                </svg>
                                Log in
                            </a>
                            <a href="{{ route('register') }}" class="ham-item" style="color:#ED730C;font-weight:700;">
                                <svg width="16" height="16" fill="none" stroke="#ED730C" viewBox="0 0 24 24" style="flex-shrink:0;">
                                    <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M16 21v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2M12 11a4 4 0 100-8 4 4 0 000 8zM19 8v6M22 11h-6"/>
                                </svg>
                                Sign Up
                            </a>
                        </div>

                    @endauth
                </div>
            </div>

        </div>

        {{-- MOBILE HAMBURGER --}}
        <button id="mobile-menu-btn" onclick="toggleMobileMenu()"
            aria-label="Toggle navigation menu"
            style="display:none;padding:8px 10px;border-radius:999px;background:none;border:1px solid #ddd;cursor:pointer;color:#333;align-items:center;gap:8px;"
            class="mobile-hamburger">
            <svg id="icon-open" width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
            <svg id="icon-close" width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display:none;">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>

    </div>

    {{-- STICKY SEARCH SLOT — Vue teleports the search bar here on scroll --}}
    <div id="nav-sticky-search">
        <div style="max-width:82rem;margin:0 auto;"></div>
    </div>

    {{-- MOBILE MENU --}}
    <div id="mobile-menu" style="display:none;border-top:1px solid #ebebeb;background:#fff;">
        <div style="padding:12px 16px 20px;display:flex;flex-direction:column;gap:4px;">

            <a href="{{ route('browse') }}" class="nav-link-mobile {{ $currentRoute === 'browse' ? 'active' : '' }}">
                <span class="nav-icon-wrap">
                    <img class="icon-inactive" src="{{ asset('images/icons/items-inactive.png') }}" alt="">
                    <img class="icon-active"   src="{{ asset('images/icons/items-active.png') }}"   alt="">
                </span>
                Items
            </a>
            <a href="#" class="nav-link-mobile">
                <span class="nav-icon-wrap">
                    <img class="icon-inactive" src="{{ asset('images/icons/homes-inactive.png') }}" alt="">
                    <img class="icon-active"   src="{{ asset('images/icons/homes-active.png') }}"   alt="">
                </span>
                Homes
            </a>
            <a href="{{ route('garage-sale') }}" class="nav-link-mobile {{ $currentRoute === 'garage-sale' ? 'active' : '' }}">
                <span class="nav-icon-wrap">
                    <img class="icon-inactive" src="{{ asset('images/icons/garage-sale-inactive.png') }}" alt="">
                    <img class="icon-active"   src="{{ asset('images/icons/garage-sale-active.png') }}"   alt="">
                </span>
                Garage Sale
            </a>
            <a href="{{ route('services') }}" class="nav-link-mobile {{ $currentRoute === 'services' ? 'active' : '' }}">
                <span class="nav-icon-wrap">
                    <img class="icon-inactive" src="{{ asset('images/icons/services-inactive.png') }}" alt="">
                    <img class="icon-active"   src="{{ asset('images/icons/services-active.png') }}"   alt="">
                </span>
                Services
            </a>
            <a href="{{ route('how-it-works') }}" class="nav-link-mobile {{ $currentRoute === 'how-it-works' ? 'active' : '' }}">
                <img src="{{ asset('images/icons/how-it-works.png') }}" alt=""> How It Works
            </a>

            @auth
                <a href="#" class="nav-link-mobile">
                    <img src="{{ asset('images/icons/matches.png') }}" alt=""> Matches
                </a>
            @endauth

            <div style="border-top:1px solid #ebebeb;padding-top:14px;margin-top:8px;display:flex;gap:10px;">
                @auth
                    <a href="{{ route('dashboard') }}"
                       style="flex:1;text-align:center;font-size:0.875rem;font-weight:700;color:#fff;background:#ED730C;border-radius:999px;padding:11px 0;text-decoration:none;">
                        Dashboard
                    </a>
                    <form method="POST" action="{{ route('logout') }}" style="flex:1;">
                        @csrf
                        <button type="submit"
                            style="width:100%;font-size:0.875rem;font-weight:600;color:#ef4444;border:1.5px solid #fecaca;border-radius:999px;padding:11px 0;background:none;cursor:pointer;font-family:inherit;">
                            Log Out
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}"
                       style="flex:1;text-align:center;font-size:0.875rem;font-weight:600;color:#333;border:1.5px solid #ddd;border-radius:999px;padding:11px 0;text-decoration:none;">
                        Log in
                    </a>
                    <a href="{{ route('register') }}"
                       style="flex:1;text-align:center;font-size:0.875rem;font-weight:700;color:#fff;background:#ED730C;border-radius:999px;padding:11px 0;text-decoration:none;">
                        Sign Up
                    </a>
                @endauth
            </div>

        </div>
    </div>

</header>

<script>
    var header = document.getElementById('main-header');

    window.addEventListener('scroll', function () {
        header.classList.toggle('scrolled', window.scrollY > 10);
    }, { passive: true });

    document.querySelectorAll('.nav-link').forEach(function(link) {
        link.addEventListener('click', function() {
            var self = this;
            self.classList.remove('icon-clicked');
            void self.offsetWidth;
            self.classList.add('icon-clicked');
            setTimeout(function() { self.classList.remove('icon-clicked'); }, 450);
        });
    });

    function toggleHamburger() {
        var d = document.getElementById('hamburger-dropdown');
        d.style.display = d.style.display === 'block' ? 'none' : 'block';
    }

    function toggleMobileMenu() {
        var menu   = document.getElementById('mobile-menu');
        var open   = document.getElementById('icon-open');
        var close  = document.getElementById('icon-close');
        var isOpen = menu.style.display !== 'none';
        menu.style.display  = isOpen ? 'none' : 'block';
        open.style.display  = isOpen ? 'block' : 'none';
        close.style.display = isOpen ? 'none' : 'block';
    }

    document.addEventListener('click', function (e) {
        var wrap = document.getElementById('hamburger-wrap');
        if (wrap && !wrap.contains(e.target)) {
            document.getElementById('hamburger-dropdown').style.display = 'none';
        }
    });
</script>