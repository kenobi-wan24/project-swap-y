{{-- resources/views/partials/navigation.blade.php --}}

<style>
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
    .nav-link:hover { color: #3A3330; background-color: #f9fafb; }
    .nav-link.active { color: #ED730C; font-weight: 600; background-color: transparent; }
    .nav-link.active::after {
        content: '';
        position: absolute;
        bottom: -1px;
        left: 12px;
        right: 12px;
        height: 2px;
        background-color: #ED730C;
        border-radius: 2px 2px 0 0;
    }
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
    .nav-link-mobile:hover { background-color: #f9fafb; }
    .nav-link-mobile.active { background-color: #fff4ec; color: #ED730C; font-weight: 600; }

    .nav-icon-btn {
        width: 36px; height: 36px;
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
</style>

<header style="background:#fff; border-bottom:1px solid #f3f4f6; position:sticky; top:0; z-index:50;">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">

            {{-- LOGO --}}
            <a href="{{ route('home') }}" style="text-decoration:none;" class="flex items-center flex-shrink-0">
                <img src="{{ asset('images/logo-swapy.png') }}" alt="SWAPY" style="height:60px;object-fit:contain;display:block;">
            </a>

            {{-- DESKTOP NAV LINKS --}}
            @php
                $navLinks = [
                    ['label' => 'Home',        'route' => 'home',         'href' => route('home')],
                    ['label' => 'Browse',       'route' => 'browse',       'href' => route('browse')],
                    ['label' => 'Garage Sale',  'route' => 'garage-sale',  'href' => route('garage-sale')],
                    ['label' => 'Services',     'route' => 'services',     'href' => route('services')],
                    ['label' => 'How It Works', 'route' => 'how-it-works', 'href' => route('how-it-works')],
                ];
                $currentRoute = Route::currentRouteName();
            @endphp

            <nav class="hidden md:flex items-center" style="gap:2px;">
                @foreach ($navLinks as $link)
                    <a href="{{ $link['href'] }}"
                       class="nav-link {{ $currentRoute === $link['route'] ? 'active' : '' }}">
                        {{ $link['label'] }}
                    </a>
                @endforeach
            </nav>

            {{-- DESKTOP AUTH --}}
            <div class="hidden md:flex items-center" style="gap:4px;">
                @auth

                    {{-- Notification icon --}}
                    <button class="nav-icon-btn">
                        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                    </button>

                    {{-- Message icon --}}
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

                            {{-- User info --}}
                            <div style="display:flex;align-items:center;gap:12px;padding:16px;border-bottom:1px solid #f3f4f6;">
                                <div style="width:42px;height:42px;border-radius:50%;background:#ED730C;color:#fff;font-weight:700;font-size:0.85rem;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}{{ strtoupper(substr(strstr(auth()->user()->name, ' ') ?: ' ', 1, 1)) }}
                                </div>
                                <div style="min-width:0;">
                                    <p style="font-size:0.9rem;font-weight:700;color:#3A3330;margin:0;line-height:1.3;">{{ auth()->user()->name }}</p>
                                    <p style="font-size:0.78rem;color:#9ca3af;margin:2px 0 0;">&#64;{{ auth()->user()->username }}</p>
                                </div>
                            </div>

                            {{-- Main links --}}
                            <div style="padding:6px 0;">
                                <a href="{{ route('dashboard') }}" class="dropdown-item">
                                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="flex-shrink:0;opacity:0.45;">
                                        <rect x="3" y="3" width="7" height="7" rx="1" stroke-width="2"/><rect x="14" y="3" width="7" height="7" rx="1" stroke-width="2"/><rect x="3" y="14" width="7" height="7" rx="1" stroke-width="2"/><rect x="14" y="14" width="7" height="7" rx="1" stroke-width="2"/>
                                    </svg>
                                    Dashboard
                                </a>
                                <a href="{{ route('user.store', auth()->user()->username) }}" class="dropdown-item">
                                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="flex-shrink:0;opacity:0.45;">
                                        <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M3 9l1-5h16l1 5M3 9h18M3 9v11a1 1 0 001 1h16a1 1 0 001-1V9"/>
                                    </svg>
                                    My Store
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

                            {{-- Upgrade to Premium --}}
                            <div style="padding:6px 0;border-top:1px solid #f3f4f6;">
                                <a href="#" class="dropdown-item premium">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="#ED730C" style="flex-shrink:0;">
                                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                    </svg>
                                    Upgrade to Premium
                                </a>
                            </div>

                            {{-- Logout --}}
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

            {{-- MOBILE HAMBURGER --}}
            <button id="mobile-menu-btn" onclick="toggleMobileMenu()"
                aria-label="Toggle navigation menu" aria-expanded="false"
                style="display:none;padding:8px;border-radius:8px;background:none;border:none;cursor:pointer;color:#6b7280;"
                class="mobile-hamburger">
                <svg id="icon-open" width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
                <svg id="icon-close" width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display:none;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>

        </div>
    </div>

    {{-- MOBILE MENU --}}
    <div id="mobile-menu" style="display:none;border-top:1px solid #f3f4f6;background:#fff;">
        <div style="padding:12px 16px 16px;display:flex;flex-direction:column;gap:4px;">
            @foreach ($navLinks as $link)
                <a href="{{ $link['href'] }}"
                   class="nav-link-mobile {{ $currentRoute === $link['route'] ? 'active' : '' }}">
                    {{ $link['label'] }}
                </a>
            @endforeach
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

<style>
    @media (max-width: 767px) {
        .mobile-hamburger { display:block !important; }
    }
</style>

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

    function toggleUserMenu() {
        var d = document.getElementById('user-dropdown');
        d.style.display = d.style.display === 'none' ? 'block' : 'none';
    }

    document.addEventListener('click', function(e) {
        var wrap = document.getElementById('user-menu-wrap');
        if (wrap && !wrap.contains(e.target)) {
            var d = document.getElementById('user-dropdown');
            if (d) d.style.display = 'none';
        }
    });
</script>