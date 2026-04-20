{{-- resources/views/partials/footer.blade.php --}}
<style>
    .footer-link {
        font-size: 0.875rem;
        color: #6b7280;
        text-decoration: none;
        transition: color 0.15s ease;
        display: block;
        padding: 3px 0;
    }
    .footer-link:hover { color: #ED730C; }

    .footer-col-title {
        font-size: 0.875rem;
        font-weight: 700;
        color: #3A3330;
        margin-bottom: 16px;
        letter-spacing: 0.01em;
    }

    .social-btn {
        width: 36px;
        height: 36px;
        border-radius: 8px;
        border: 1px solid #e5e7eb;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: #6b7280;
        text-decoration: none;
        transition: border-color 0.15s, color 0.15s, background 0.15s;
        flex-shrink: 0;
    }
    .social-btn:hover {
        border-color: #ED730C;
        color: #ED730C;
        background: #fff4ec;
    }
</style>

<footer style="background:#fff; border-top:1px solid #f3f4f6; margin-top:5rem;">

    {{-- ===== MAIN FOOTER CONTENT ===== --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" style="padding-top:56px; padding-bottom:48px;">
        <div style="display:grid; grid-template-columns:1fr; gap:40px;">

            {{-- On md+: brand col (wider) + 4 link cols --}}
            <div style="display:grid; grid-template-columns:repeat(1,1fr); gap:40px;" class="footer-grid">

                {{-- ===== LEFT: Brand + tagline + socials ===== --}}
                <div>
                    {{-- Logo + name (matches navbar) --}}
                    <a href="{{ route('home') }}" style="display:inline-flex; align-items:center; gap:1px; text-decoration:none; margin-bottom:16px;">
                        <img
                            src="{{ asset('images/logo-swapy.png') }}"
                            alt="SWAPY"
                            style="height:75px; object-fit:contain; display:block;"
                        >
                    </a>

                    {{-- Tagline --}}
                    <p style="font-size:0.875rem; color:#6b7280; line-height:1.65; max-width:240px; margin-bottom:24px;">
                        The modern way to swap. From everyday items to real estate, connect with swappers worldwide and swap value without cash.
                    </p>

                    {{-- Social icons --}}
                    <div style="display:flex; gap:8px; flex-wrap:wrap;">
                        {{-- Facebook --}}
                        <a href="#" class="social-btn" aria-label="Facebook">
                            <svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"/>
                            </svg>
                        </a>
                        {{-- Twitter / X --}}
                        <a href="#" class="social-btn" aria-label="Twitter">
                            <svg width="15" height="15" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                            </svg>
                        </a>
                        {{-- Instagram --}}
                        <a href="#" class="social-btn" aria-label="Instagram">
                            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                <rect x="2" y="2" width="20" height="20" rx="5" ry="5"/>
                                <circle cx="12" cy="12" r="4"/>
                                <circle cx="17.5" cy="6.5" r="1" fill="currentColor" stroke="none"/>
                            </svg>
                        </a>
                        {{-- LinkedIn --}}
                        <a href="#" class="social-btn" aria-label="LinkedIn">
                            <svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M16 8a6 6 0 016 6v7h-4v-7a2 2 0 00-2-2 2 2 0 00-2 2v7h-4v-7a6 6 0 016-6zM2 9h4v12H2z"/>
                                <circle cx="4" cy="4" r="2"/>
                            </svg>
                        </a>
                    </div>
                </div>

                {{-- ===== RIGHT: 4 link columns ===== --}}
                <div style="display:grid; grid-template-columns:repeat(2,1fr); gap:32px;" class="footer-links-grid">

                    {{-- Platform --}}
                    <div>
                        <div class="footer-col-title">Platform</div>
                        <a href="{{ route('browse') }}"      class="footer-link">Browse Items</a>
                        <a href="{{ route('how-it-works') }}" class="footer-link">How It Works</a>
                        <a href="#pricing"                   class="footer-link">Pricing</a>
                        <a href="#"                          class="footer-link">Mobile App</a>
                    </div>

                    {{-- Support --}}
                    <div>
                        <div class="footer-col-title">Support</div>
                        <a href="#" class="footer-link">Help Center</a>
                        <a href="#" class="footer-link">Safety Tips</a>
                        <a href="#" class="footer-link">Contact Us</a>
                        <a href="#" class="footer-link">Report Issue</a>
                    </div>

                    {{-- Company --}}
                    <div>
                        <div class="footer-col-title">Company</div>
                        <a href="#" class="footer-link">About Us</a>
                        <a href="#" class="footer-link">Careers</a>
                        <a href="#" class="footer-link">Press</a>
                        <a href="#" class="footer-link">Partners</a>
                    </div>

                    {{-- Legal --}}
                    <div>
                        <div class="footer-col-title">Legal</div>
                        <a href="#" class="footer-link">Privacy Policy</a>
                        <a href="#" class="footer-link">Terms of Service</a>
                        <a href="#" class="footer-link">Cookie Policy</a>
                    </div>

                </div>

            </div>
        </div>
    </div>

    {{-- ===== BOTTOM BAR ===== --}}
    <div style="border-top:1px solid #f3f4f6;">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" style="padding-top:20px; padding-bottom:20px; text-align:center;">
            <p style="font-size:0.8125rem; color:#9ca3af;">
                &copy; {{ date('Y') }} SWAPY. All rights reserved.
            </p>
        </div>
    </div>

</footer>

{{-- ===== Responsive grid layout ===== --}}
<style>
    @media (min-width: 768px) {
        .footer-grid {
            grid-template-columns: 280px 1fr !important;
        }
        .footer-links-grid {
            grid-template-columns: repeat(4, 1fr) !important;
        }
    }
</style>