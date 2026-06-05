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
        /* CRITICAL: allow dropdowns to overflow the header */
        overflow: visible;
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
        z-index: 200;
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


    /* ── MOBILE BOTTOM TAB BAR ── */
    #mobile-bottom-nav {
        display: none;
        position: fixed;
        bottom: 0; left: 0; right: 0;
        height: 64px;
        background: #fff;
        border-top: 1px solid #EBEBEB;
        z-index: 100;
        align-items: stretch;
        justify-content: space-around;
        padding-bottom: env(safe-area-inset-bottom);
        box-shadow: 0 -4px 20px rgba(0,0,0,0.06);
    }
    .mob-tab {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        flex: 1;
        gap: 3px;
        text-decoration: none;
        color: #9ca3af;
        font-size: 0.62rem;
        font-weight: 600;
        letter-spacing: 0.02em;
        transition: color 0.15s;
        -webkit-tap-highlight-color: transparent;
        padding: 8px 0 6px;
    }
    .mob-tab--active { color: #ED730C; }
    .mob-tab-icon {
        position: relative;
        width: 26px; height: 26px;
        flex-shrink: 0;
    }
    .mob-tab-icon img {
        position: absolute;
        top: 0; left: 0;
        width: 26px; height: 26px;
        object-fit: contain;
        transition: opacity 0.2s;
    }
    .mob-tab-icon .icon-inactive { opacity: 1; }
    .mob-tab-icon .icon-active   { opacity: 0; }
    .mob-tab--active .mob-tab-icon .icon-inactive { opacity: 0; }
    .mob-tab--active .mob-tab-icon .icon-active   { opacity: 1; }
    .mob-tab-label { line-height: 1; }

    /* ── Post CTA tab ── */
    .mob-tab--post { color: #ED730C; }
    .mob-tab--post .mob-tab-label { color: #ED730C; font-weight: 700; }
    .mob-tab-post-btn {
        width: 46px; height: 46px;
        background: #ED730C;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        box-shadow: 0 4px 14px rgba(237,115,12,0.4);
        transition: transform 0.15s, box-shadow 0.15s;
        margin-bottom: 1px;
    }
    .mob-tab--post:active .mob-tab-post-btn {
        transform: scale(0.93);
        box-shadow: 0 2px 8px rgba(237,115,12,0.3);
    }

    /* ── MOBILE SEARCH PILL ── */
    .mobile-search-pill {
        display: none;
        align-items: center;
        height: 40px;
        background: #f5f5f5;
        border: 1.5px solid #e8e8e8;
        border-radius: 999px;
        overflow: hidden;
        cursor: pointer;
        transition: box-shadow 0.2s ease, border-color 0.2s ease;
        text-decoration: none;
    }
    .mobile-search-pill:hover {
        border-color: #ccc;
        box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    }
    .mobile-search-pill-text {
        flex: 1;
        padding: 0 14px;
        font-size: 0.82rem;
        color: #aaa;
        font-weight: 500;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        min-width: 0;
    }
    .mobile-search-pill-btn {
        height: 100%;
        padding: 0 14px;
        background: #ED730C;
        color: #fff;
        font-size: 0.8rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 5px;
        white-space: nowrap;
        flex-shrink: 0;
    }

    /* ── MOBILE AVATAR DROPDOWN ── */
    #mobile-hamburger-dropdown {
        display: none;
        position: fixed;
        top: 70px;
        right: 16px;
        left: auto;
        width: 280px;
        background: #fff;
        border-radius: 20px;
        box-shadow: 0 8px 40px rgba(0,0,0,0.18);
        border: 1px solid #ebebeb;
        z-index: 999;
        overflow-y: auto;
        max-height: calc(100vh - 150px);
    }

    @media (max-width: 767px) {
        #mobile-bottom-nav { display: flex; }
        body { padding-bottom: calc(64px + env(safe-area-inset-bottom)); }
        .desktop-nav { display: none !important; }
        .mobile-hamburger { display: flex !important; }
        .header-row {
            display: flex;
            align-items: center;
            gap: 10px;
            height: 60px;
            padding: 0 1.25rem;
        }
        /* hide the static pill — Vue sticky search takes over on scroll */
        .mobile-search-pill { display: none; }
        /* allow sticky search to show on mobile */
        #nav-sticky-search { display: block !important; }
    }

    @media (min-width: 768px) {
        .mobile-only { display: none !important; }
        .mobile-hamburger { display: none !important; }
    }

    /* ── STICKY SEARCH SLOT ── */
    #nav-sticky-search {
        overflow: hidden;
        max-height: 0;
        opacity: 0;
        transition: max-height 0.4s cubic-bezier(0.4,0,0.2,1), opacity 0.3s ease, padding 0.4s cubic-bezier(0.4,0,0.2,1);
        padding: 0 2.5rem;
        background: #fff;
        /* ensure inputs inside are always interactive */
        pointer-events: none;
    }
    #nav-sticky-search.open {
        max-height: 100px;
        opacity: 1;
        padding: 12px 2.5rem 16px;
        border-bottom: 2px solid #EBEBEB;
        pointer-events: auto;
    }
    /* Suppress the header's own bottom border when search row is visible */
    #main-header.scrolled { border-bottom-color: transparent; }
    @media (max-width: 767px) {
        #nav-sticky-search.open { padding: 10px 1.25rem 12px; }
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

        {{-- CENTER NAV (desktop only) --}}
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
<<<<<<< HEAD
            <a href="{{ route('homes') }}"
=======
            <a href="{{route('homes')}}"
>>>>>>> ba696a518a782006352c6256afa01e7b2beea661
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
                Garage Sales
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

        {{-- RIGHT SIDE (desktop) --}}
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

            {{-- HAMBURGER + AVATAR PILL (desktop) --}}
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

                {{-- DESKTOP DROPDOWN --}}
                <div id="hamburger-dropdown">
                    @auth
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
                                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="opacity:0.45;flex-shrink:0;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                                Notifications
                            </a>
                            <a href="#" class="ham-item">
                                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="opacity:0.45;flex-shrink:0;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                                Messages
                            </a>
                            <a href="{{ route('how-it-works') }}" class="ham-item">
                                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="opacity:0.45;flex-shrink:0;"><circle cx="12" cy="12" r="10" stroke-width="2"/><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M9.09 9a3 3 0 015.83 1c0 2-3 3-3 3M12 17h.01"/></svg>
                                How It Works
                            </a>
                            <a href="#" class="ham-item">
                                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="opacity:0.45;flex-shrink:0;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                                Matches
                            </a>
                        </div>
                        <div class="ham-divider"></div>
                        <div style="padding:6px 0;">
                            <a href="{{ route('user.store', auth()->user()->username) }}" class="ham-item bold">
                                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="opacity:0.45;flex-shrink:0;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                My Store
                            </a>
                            <a href="{{ route('dashboard') }}" class="ham-item">
                                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="opacity:0.45;flex-shrink:0;"><rect x="3" y="3" width="7" height="7" rx="1" stroke-width="2"/><rect x="14" y="3" width="7" height="7" rx="1" stroke-width="2"/><rect x="3" y="14" width="7" height="7" rx="1" stroke-width="2"/><rect x="14" y="14" width="7" height="7" rx="1" stroke-width="2"/></svg>
                                Dashboard
                            </a>
                            <a href="{{ route('profile') }}" class="ham-item">
                                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="opacity:0.45;flex-shrink:0;"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2M12 11a4 4 0 100-8 4 4 0 000 8z"/></svg>
                                My Profile
                            </a>
                            <a href="#" class="ham-item">
                                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="opacity:0.45;flex-shrink:0;"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                Friends
                            </a>
                            <a href="#" class="ham-item">
                                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="opacity:0.45;flex-shrink:0;"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><circle cx="12" cy="12" r="3" stroke-width="2"/></svg>
                                Settings
                            </a>
                        </div>
                        <div class="ham-divider"></div>
                        <div style="padding:6px 0;">
                            <a href="#" class="ham-item premium">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="#ED730C" style="flex-shrink:0;"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                                Upgrade to Premium
                            </a>
                        </div>
                        <div class="ham-divider"></div>
                        <div style="padding:6px 0;">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="ham-item danger">
                                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="opacity:0.5;flex-shrink:0;"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h6a2 2 0 012 2v1"/></svg>
                                    Log Out
                                </button>
                            </form>
                        </div>
                    @else
                        <div style="padding:6px 0;">
                            <a href="{{ route('how-it-works') }}" class="ham-item bold">
                                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="opacity:0.45;flex-shrink:0;"><circle cx="12" cy="12" r="10" stroke-width="2"/><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M9.09 9a3 3 0 015.83 1c0 2-3 3-3 3M12 17h.01"/></svg>
                                How It Works
                            </a>
                        </div>
                        <div class="ham-divider"></div>
                        <div style="padding:6px 0;">
                            <a href="{{ route('login') }}" class="ham-item bold">
                                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="opacity:0.45;flex-shrink:0;"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                                Log in
                            </a>
                            <a href="{{ route('register') }}" class="ham-item" style="color:#ED730C;font-weight:700;">
                                <svg width="16" height="16" fill="none" stroke="#ED730C" viewBox="0 0 24 24" style="flex-shrink:0;"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M16 21v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2M12 11a4 4 0 100-8 4 4 0 000 8zM19 8v6M22 11h-6"/></svg>
                                Sign Up
                            </a>
                        </div>
                    @endauth
                </div>
            </div>

        </div>

        {{-- MOBILE: simplified search pill (flex: 1 fills the space between logo and avatar) --}}
        <a href="{{ route('browse') }}" class="mobile-search-pill">
            <span class="mobile-search-pill-text">Search swaps...</span>
            <span class="mobile-search-pill-btn">
                <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <circle cx="11" cy="11" r="8"/><path stroke-linecap="round" d="M21 21l-4.35-4.35"/>
                </svg>
                Search
            </span>
        </a>

        {{-- MOBILE: avatar pill --}}
        <div class="mobile-hamburger" style="display:none;position:relative;flex-shrink:0;margin-left:auto;" id="mobile-hamburger-wrap">
            <button class="hamburger-pill" onclick="toggleMobileHamburger()" style="padding:5px 8px;border:1px solid #eee;">
                @auth
                    <div style="width:32px;height:32px;border-radius:50%;background:#ED730C;color:#fff;font-weight:700;font-size:0.75rem;display:flex;align-items:center;justify-content:center;">
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
        </div>

        {{-- NOTE: #mobile-hamburger-dropdown is rendered AFTER </header> below --}}
        

    </div>

    {{-- STICKY SEARCH SLOT — Vue teleports the search bar here on scroll --}}
    <div id="nav-sticky-search">
        <div style="max-width:82rem;margin:0 auto;"></div>
    </div>

</header>

{{-- ═══════════════════════════════════════════
     MOBILE PROFILE DROPDOWN — outside <header> so position:fixed is not trapped
     by the header's transform:translateZ(0) stacking context
═══════════════════════════════════════════ --}}
<div id="mobile-hamburger-dropdown" style="display:none;">

                @auth
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
                            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="opacity:0.45;flex-shrink:0;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                            Notifications
                        </a>
                        <a href="#" class="ham-item">
                            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="opacity:0.45;flex-shrink:0;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                            Messages
                        </a>
                        <a href="{{ route('how-it-works') }}" class="ham-item">
                            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="opacity:0.45;flex-shrink:0;"><circle cx="12" cy="12" r="10" stroke-width="2"/><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M9.09 9a3 3 0 015.83 1c0 2-3 3-3 3M12 17h.01"/></svg>
                            How It Works
                        </a>
                        <a href="#" class="ham-item">
                            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="opacity:0.45;flex-shrink:0;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                            Matches
                        </a>
                    </div>
                    <div class="ham-divider"></div>
                    <div style="padding:6px 0;">
                        <a href="{{ route('user.store', auth()->user()->username) }}" class="ham-item bold">
                            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="opacity:0.45;flex-shrink:0;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            My Store
                        </a>
                        <a href="{{ route('dashboard') }}" class="ham-item">
                            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="opacity:0.45;flex-shrink:0;"><rect x="3" y="3" width="7" height="7" rx="1" stroke-width="2"/><rect x="14" y="3" width="7" height="7" rx="1" stroke-width="2"/><rect x="3" y="14" width="7" height="7" rx="1" stroke-width="2"/><rect x="14" y="14" width="7" height="7" rx="1" stroke-width="2"/></svg>
                            Dashboard
                        </a>
                        <a href="{{ route('profile') }}" class="ham-item">
                            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="opacity:0.45;flex-shrink:0;"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2M12 11a4 4 0 100-8 4 4 0 000 8z"/></svg>
                            My Profile
                        </a>
                        <a href="#" class="ham-item">
                            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="opacity:0.45;flex-shrink:0;"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            Friends
                        </a>
                        <a href="#" class="ham-item">
                            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="opacity:0.45;flex-shrink:0;"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><circle cx="12" cy="12" r="3" stroke-width="2"/></svg>
                            Settings
                        </a>
                    </div>
                    <div class="ham-divider"></div>
                    <div style="padding:6px 0;">
                        <a href="#" class="ham-item premium">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="#ED730C" style="flex-shrink:0;"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                            Upgrade to Premium
                        </a>
                    </div>
                    <div class="ham-divider"></div>
                    <div style="padding:6px 0;">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="ham-item danger">
                                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="opacity:0.5;flex-shrink:0;"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h6a2 2 0 012 2v1"/></svg>
                                Log Out
                            </button>
                        </form>
                    </div>
                @else
                    <div style="padding:6px 0;">
                        <a href="{{ route('how-it-works') }}" class="ham-item bold">
                            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="opacity:0.45;flex-shrink:0;"><circle cx="12" cy="12" r="10" stroke-width="2"/><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M9.09 9a3 3 0 015.83 1c0 2-3 3-3 3M12 17h.01"/></svg>
                            How It Works
                        </a>
                    </div>
                    <div class="ham-divider"></div>
                    <div style="padding:6px 0;">
                        <a href="{{ route('login') }}" class="ham-item bold">
                            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="opacity:0.45;flex-shrink:0;"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                            Log in
                        </a>
                        <a href="{{ route('register') }}" class="ham-item" style="color:#ED730C;font-weight:700;">
                            <svg width="16" height="16" fill="none" stroke="#ED730C" viewBox="0 0 24 24" style="flex-shrink:0;"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M16 21v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2M12 11a4 4 0 100-8 4 4 0 000 8zM19 8v6M22 11h-6"/></svg>
                            Sign Up
                        </a>
                    </div>
                @endauth
</div>

<nav id="mobile-bottom-nav" aria-label="Mobile navigation">

    {{-- Items --}}
    <a href="{{ route('browse') }}" class="mob-tab {{ $currentRoute === 'browse' ? 'mob-tab--active' : '' }}">
        <span class="mob-tab-icon">
            <img class="icon-inactive" src="{{ asset('images/icons/items-inactive.png') }}" alt="">
            <img class="icon-active"   src="{{ asset('images/icons/items-active.png') }}"   alt="">
        </span>
        <span class="mob-tab-label">Items</span>
    </a>

    {{-- Homes --}}
    <a href="{{ route('homes') }}" class="mob-tab {{ $currentRoute === 'homes' ? 'mob-tab--active' : '' }}">
        <span class="mob-tab-icon">
            <img class="icon-inactive" src="{{ asset('images/icons/homes-inactive.png') }}" alt="">
            <img class="icon-active"   src="{{ asset('images/icons/homes-active.png') }}"   alt="">
        </span>
        <span class="mob-tab-label">Homes</span>
    </a>

    {{-- Post a Swap — center CTA --}}
    <a href="{{ auth()->check() ? route('dashboard') : route('login') }}" class="mob-tab mob-tab--post">
        <span class="mob-tab-post-btn">
            <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
            </svg>
        </span>
        <span class="mob-tab-label">Post</span>
    </a>

    {{-- Garage Sales --}}
    <a href="{{ route('garage-sale') }}" class="mob-tab {{ $currentRoute === 'garage-sale' ? 'mob-tab--active' : '' }}">
        <span class="mob-tab-icon">
            <img class="icon-inactive" src="{{ asset('images/icons/garage-sale-inactive.png') }}" alt="">
            <img class="icon-active"   src="{{ asset('images/icons/garage-sale-active.png') }}"   alt="">
        </span>
        <span class="mob-tab-label">Garage</span>
    </a>

    {{-- Services --}}
    <a href="{{ route('services') }}" class="mob-tab {{ $currentRoute === 'services' ? 'mob-tab--active' : '' }}">
        <span class="mob-tab-icon">
            <img class="icon-inactive" src="{{ asset('images/icons/services-inactive.png') }}" alt="">
            <img class="icon-active"   src="{{ asset('images/icons/services-active.png') }}"   alt="">
        </span>
        <span class="mob-tab-label">Services</span>
    </a>

</nav>

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

    function toggleMobileHamburger() {
        var d = document.getElementById('mobile-hamburger-dropdown');
        d.style.display = d.style.display === 'block' ? 'none' : 'block';
    }

    document.addEventListener('click', function (e) {
        // Desktop hamburger
        var desktopWrap = document.getElementById('hamburger-wrap');
        var desktopDrop = document.getElementById('hamburger-dropdown');
        if (desktopDrop && desktopWrap && !desktopWrap.contains(e.target)) {
            desktopDrop.style.display = 'none';
        }
        // Mobile avatar dropdown
        var mobileWrap = document.getElementById('mobile-hamburger-wrap');
        var mobileDrop = document.getElementById('mobile-hamburger-dropdown');
        if (mobileDrop && mobileWrap && !mobileWrap.contains(e.target)) {
            mobileDrop.style.display = 'none';
        }
    });
</script>