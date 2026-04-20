<script setup>
// filepath: resources/js/components/global/HowItWorksPage.vue
import { ref, computed } from 'vue'

const el         = document.getElementById('how-it-works-app')
const allGuides  = ref(JSON.parse(el?.dataset.guides   || '[]'))
const featured   = ref(JSON.parse(el?.dataset.featured || '{}'))

// ── filter tabs ───────────────────────────────────────────────────────────────
const tabs       = ['All Tutorials', 'Getting Started', 'How To Swap', 'Multi-Leg', 'Safety']
const activeTab  = ref('All Tutorials')

const filteredGuides = computed(() =>
    activeTab.value === 'All Tutorials'
        ? allGuides.value
        : allGuides.value.filter(g => g.category === activeTab.value)
)

// ── newsletter ────────────────────────────────────────────────────────────────
const email         = ref('')
const subscribed    = ref(false)
const subLoading    = ref(false)

async function subscribe() {
    if (!email.value || !email.value.includes('@')) return
    subLoading.value = true
    // TODO (Backend): POST /api/newsletter { email }
    await new Promise(r => setTimeout(r, 700))
    subscribed.value = true
    subLoading.value = false
}

// ── guide carousel scroll ─────────────────────────────────────────────────────
const carouselRef = ref(null)
function scrollCarousel(dir) {
    if (!carouselRef.value) return
    carouselRef.value.scrollBy({ left: dir * 280, behavior: 'smooth' })
}
</script>

<template>
<div style="min-height:100vh; background:#f9fafb;">

    <!-- ══════════════════════════════════════════
         HERO SECTION
    ══════════════════════════════════════════ -->
    <section style="background:#fff; padding:48px 0 0;">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- "MASTER THE SWAP" badge -->
            <div style="margin-bottom:20px;">
                <span style="display:inline-flex;align-items:center;gap:6px;background:#149189;color:#fff;font-size:0.68rem;font-weight:800;padding:5px 12px;border-radius:999px;letter-spacing:.08em;text-transform:uppercase;">
                    <svg width="10" height="10" viewBox="0 0 24 24" fill="#fff">
                        <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Master the Swap
                </span>
            </div>

            <div style="display:flex;flex-wrap:wrap;gap:32px;align-items:flex-end;padding-bottom:36px;">

                <!-- Left: heading + description -->
                <div style="flex:1;min-width:280px;">
                    <h1 style="font-size:3.25rem;font-weight:800;color:#3A3330;line-height:1.08;margin-bottom:18px;">
                        Everything you<br>
                        need <span style="color:#ED730C;">to swap</span><br>
                        <span style="color:#ED730C;">smart.</span>
                    </h1>
                    <p style="font-size:0.9375rem;color:#6b7280;max-width:380px;line-height:1.65;">
                        From your first swap to expert multi-leg swaps, our visual guides help you navigate the SWAPY ecosystem with confidence.
                    </p>
                </div>

                <!-- Right: filter tab pills -->
                <div style="display:flex;flex-wrap:wrap;gap:8px;align-items:flex-start;padding-bottom:4px;">
                    <button
                        v-for="tab in tabs"
                        :key="tab"
                        @click="activeTab = tab"
                        :style="{
                            padding:'8px 18px',
                            borderRadius:'999px',
                            fontSize:'0.8125rem',
                            fontWeight:'600',
                            fontFamily:'\'DM Sans\',sans-serif',
                            cursor:'pointer',
                            border:'1.5px solid',
                            borderColor: activeTab===tab ? '#ED730C' : '#e5e7eb',
                            background:  activeTab===tab ? '#ED730C' : '#fff',
                            color:       activeTab===tab ? '#fff'    : '#4b5563',
                            transition:'all .15s',
                        }"
                    >{{ tab }}</button>
                </div>

            </div>
        </div>
    </section>

    <!-- ══════════════════════════════════════════
         FEATURED VIDEO SPLIT
    ══════════════════════════════════════════ -->
    <section style="background:#fff;padding-bottom:48px;border-bottom:1px solid #f3f4f6;">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div style="display:flex;gap:20px;align-items:stretch;flex-wrap:wrap;">

                <!-- Main featured video card -->
                <div style="flex:1;min-width:300px;border-radius:20px;overflow:hidden;position:relative;min-height:340px;background:#1a1a1a;">
                    <img
                        :src="featured.image"
                        :alt="featured.title"
                        style="width:100%;height:100%;object-fit:cover;position:absolute;inset:0;opacity:0.55;"
                    >
                    <!-- Overlay content -->
                    <div style="position:relative;z-index:1;height:100%;display:flex;flex-direction:column;justify-content:flex-end;padding:32px;">
                        <!-- Tag row -->
                        <div style="display:flex;align-items:center;gap:12px;margin-bottom:14px;">
                            <span style="background:#149189;color:#fff;font-size:0.65rem;font-weight:800;padding:4px 10px;border-radius:6px;letter-spacing:.06em;text-transform:uppercase;">
                                {{ featured.category }}
                            </span>
                            <span style="display:flex;align-items:center;gap:4px;font-size:0.78rem;color:rgba(255,255,255,0.8);">
                                <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6l4 2"/></svg>
                                {{ featured.duration }}
                            </span>
                        </div>
                        <h2 style="font-size:1.5rem;font-weight:800;color:#fff;line-height:1.2;margin-bottom:10px;">
                            {{ featured.title }}
                        </h2>
                        <p style="font-size:0.85rem;color:rgba(255,255,255,0.75);line-height:1.6;margin-bottom:20px;max-width:440px;">
                            {{ featured.description }}
                        </p>
                        <a
                            href="#"
                            style="display:inline-flex;align-items:center;gap:8px;background:#ED730C;color:#fff;font-size:0.875rem;font-weight:700;padding:12px 22px;border-radius:999px;text-decoration:none;width:fit-content;transition:background .15s;font-family:'DM Sans',sans-serif;"
                            onmouseover="this.style.background='#d4620a'"
                            onmouseout="this.style.background='#ED730C'"
                        >
                            Watch Full Tutorial
                            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                            </svg>
                        </a>
                    </div>
                </div>

                <!-- Right side panel -->
                <div style="width:260px;flex-shrink:0;display:flex;flex-direction:column;gap:16px;">

                    <!-- Safety & Verification card -->
                    <div style="background:#fff;border:1px solid #f3f4f6;border-radius:16px;padding:20px;flex:1;">
                        <div style="width:40px;height:40px;background:#e6f7f6;border-radius:10px;display:flex;align-items:center;justify-content:center;margin-bottom:12px;">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#149189" stroke-width="2" stroke-linecap="round">
                                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                            </svg>
                        </div>
                        <h3 style="font-size:0.9375rem;font-weight:700;color:#3A3330;margin-bottom:6px;">Safety & Verification</h3>
                        <p style="font-size:0.8rem;color:#9ca3af;margin-bottom:14px;line-height:1.5;">How to ensure every swap is secure and verified.</p>
                        <a href="#" style="font-size:0.75rem;font-weight:700;color:#149189;text-decoration:none;letter-spacing:.04em;text-transform:uppercase;display:flex;align-items:center;gap:4px;margin-bottom:2px;">
                            Safety Tips
                        </a>
                        <a href="#" style="font-size:0.75rem;font-weight:700;color:#ED730C;text-decoration:none;letter-spacing:.04em;display:flex;align-items:center;gap:4px;">
                            Watch
                            <svg width="10" height="10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    </div>

                    <!-- Promoting Your Listings thumbnail -->
                    <div style="background:#1a1a1a;border-radius:16px;overflow:hidden;position:relative;cursor:pointer;flex-shrink:0;height:140px;"
                        onmouseover="this.querySelector('img').style.opacity='0.5'"
                        onmouseout="this.querySelector('img').style.opacity='0.7'"
                    >
                        <img
                            src="https://images.unsplash.com/photo-1611532736597-de2d4265fba3?w=400&q=80"
                            alt="Promoting Your Listings"
                            style="width:100%;height:100%;object-fit:cover;opacity:0.7;transition:opacity .2s;"
                        >
                        <!-- Duration badge -->
                        <span style="position:absolute;bottom:8px;right:8px;background:rgba(0,0,0,0.7);color:#fff;font-size:0.65rem;font-weight:700;padding:2px 7px;border-radius:5px;">5:20</span>
                        <!-- Play button -->
                        <div style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;">
                            <div style="width:36px;height:36px;background:rgba(255,255,255,0.15);border:1.5px solid rgba(255,255,255,0.4);border-radius:50%;display:flex;align-items:center;justify-content:center;">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="#fff"><path d="M8 5v14l11-7z"/></svg>
                            </div>
                        </div>
                        <!-- Title overlay -->
                        <div style="position:absolute;bottom:0;left:0;right:0;padding:8px 12px;background:linear-gradient(transparent,rgba(0,0,0,0.7));">
                            <p style="font-size:0.78rem;font-weight:600;color:#fff;margin:0;">Promoting Your Listings</p>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </section>

    <!-- ══════════════════════════════════════════
         EXPLORE MORE GUIDES
    ══════════════════════════════════════════ -->
    <section style="padding:48px 0;">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Section header -->
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;">
                <h2 style="font-size:1.5rem;font-weight:800;color:#3A3330;">Explore More Guides</h2>
                <!-- Carousel nav arrows -->
                <div style="display:flex;gap:8px;">
                    <button
                        @click="scrollCarousel(-1)"
                        style="width:36px;height:36px;border-radius:50%;border:1.5px solid #e5e7eb;background:#fff;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:border-color .15s;"
                        onmouseover="this.style.borderColor='#ED730C'"
                        onmouseout="this.style.borderColor='#e5e7eb'"
                    >
                        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                    </button>
                    <button
                        @click="scrollCarousel(1)"
                        style="width:36px;height:36px;border-radius:50%;border:1.5px solid #e5e7eb;background:#fff;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:border-color .15s;"
                        onmouseover="this.style.borderColor='#ED730C'"
                        onmouseout="this.style.borderColor='#e5e7eb'"
                    >
                        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                    </button>
                </div>
            </div>

            <!-- Scrollable carousel -->
            <div
                ref="carouselRef"
                style="display:flex;gap:16px;overflow-x:auto;scroll-snap-type:x mandatory;padding-bottom:12px;-webkit-overflow-scrolling:touch;"
                class="hide-scrollbar"
            >
                <div
                    v-for="guide in filteredGuides"
                    :key="guide.id"
                    style="flex-shrink:0;width:240px;scroll-snap-align:start;"
                >
                    <!-- Thumbnail -->
                    <div style="position:relative;border-radius:12px;overflow:hidden;background:#1a1a1a;aspect-ratio:16/10;margin-bottom:12px;cursor:pointer;"
                        onmouseover="this.querySelector('.play-btn').style.transform='scale(1.1)'"
                        onmouseout="this.querySelector('.play-btn').style.transform='scale(1)'"
                    >
                        <img
                            :src="guide.thumbnail"
                            :alt="guide.title"
                            style="width:100%;height:100%;object-fit:cover;opacity:0.65;transition:opacity .2s;"
                            onmouseover="this.style.opacity='0.5'"
                            onmouseout="this.style.opacity='0.65'"
                        >
                        <!-- Duration -->
                        <span style="position:absolute;bottom:7px;right:8px;background:rgba(0,0,0,0.7);color:#fff;font-size:0.62rem;font-weight:700;padding:2px 6px;border-radius:4px;">
                            {{ guide.duration }}
                        </span>
                        <!-- Play button -->
                        <div class="play-btn" style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;transition:transform .2s;">
                            <div style="width:40px;height:40px;background:rgba(255,255,255,0.15);border:1.5px solid rgba(255,255,255,0.45);border-radius:50%;display:flex;align-items:center;justify-content:center;">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="#fff"><path d="M8 5v14l11-7z"/></svg>
                            </div>
                        </div>
                    </div>

                    <!-- Guide info -->
                    <div style="display:flex;gap:8px;align-items:flex-start;">
                        <!-- Category icon circle -->
                        <div style="width:28px;height:28px;border-radius:50%;background:#e6f7f6;flex-shrink:0;display:flex;align-items:center;justify-content:center;margin-top:2px;">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#149189" stroke-width="2" stroke-linecap="round">
                                <path d="M8 7h12m0 0l-4-4m4 4l-4 4M16 17H4m0 0l4 4m-4-4l4-4"/>
                            </svg>
                        </div>
                        <div style="flex:1;min-width:0;">
                            <h4 style="font-size:0.8375rem;font-weight:700;color:#3A3330;line-height:1.3;margin-bottom:3px;">{{ guide.title }}</h4>
                            <p style="font-size:0.75rem;color:#9ca3af;margin-bottom:6px;">{{ guide.views }} views · {{ guide.posted }}</p>
                            <a
                                href="#"
                                style="font-size:0.72rem;font-weight:800;color:#ED730C;text-decoration:none;letter-spacing:.06em;text-transform:uppercase;display:flex;align-items:center;gap:4px;"
                            >
                                Watch Now
                                <svg width="10" height="10" fill="none" stroke="#ED730C" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                            </a>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </section>

    <!-- ══════════════════════════════════════════
         CTA SECTION
    ══════════════════════════════════════════ -->
    <section style="background:#149189;padding:56px 0;margin-top:8px;">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div style="display:flex;gap:32px;flex-wrap:wrap;align-items:center;">

                <!-- Left: help text + buttons -->
                <div style="flex:1;min-width:280px;">
                    <h2 style="font-size:2rem;font-weight:800;color:#fff;margin-bottom:12px;line-height:1.15;">
                        Still have questions?
                    </h2>
                    <p style="font-size:0.9375rem;color:rgba(255,255,255,0.8);line-height:1.6;margin-bottom:28px;max-width:340px;">
                        Our community experts are available 24/7 to help you navigate your first swap or resolve any issues.
                    </p>
                    <div style="display:flex;gap:12px;flex-wrap:wrap;">
                        <a
                            href="#"
                            style="display:inline-flex;align-items:center;gap:7px;background:#1a1a1a;color:#fff;font-size:0.875rem;font-weight:700;padding:12px 22px;border-radius:999px;text-decoration:none;transition:background .15s;font-family:'DM Sans',sans-serif;"
                            onmouseover="this.style.background='#333'"
                            onmouseout="this.style.background='#1a1a1a'"
                        >
                            <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.09 9a3 3 0 015.83 1c0 2-3 3-3 3M12 17h.01"/></svg>
                            Visit Help Center
                        </a>
                        <a
                            href="#"
                            style="display:inline-flex;align-items:center;gap:7px;background:transparent;color:#fff;font-size:0.875rem;font-weight:700;padding:12px 22px;border-radius:999px;text-decoration:none;border:1.5px solid rgba(255,255,255,0.4);transition:border-color .15s;font-family:'DM Sans',sans-serif;"
                            onmouseover="this.style.borderColor='rgba(255,255,255,0.8)'"
                            onmouseout="this.style.borderColor='rgba(255,255,255,0.4)'"
                        >
                            Contact Support
                        </a>
                    </div>
                </div>

                <!-- Right: newsletter signup card -->
                <div style="background:#fff;border-radius:20px;padding:28px;width:340px;flex-shrink:0;">
                    <h3 style="font-size:1.0625rem;font-weight:700;color:#3A3330;margin-bottom:6px;">Weekly Swapping Tips</h3>
                    <p style="font-size:0.8375rem;color:#9ca3af;margin-bottom:18px;line-height:1.5;">
                        Join 15,000+ swappers receiving curated guides every Tuesday.
                    </p>
                    <!-- Success state -->
                    <div v-if="subscribed" style="background:#e6f7f6;border-radius:10px;padding:14px;text-align:center;">
                        <p style="font-size:0.875rem;font-weight:600;color:#149189;">You're subscribed! Check your inbox.</p>
                    </div>
                    <!-- Form -->
                    <div v-else style="display:flex;gap:8px;">
                        <input
                            v-model="email"
                            type="email"
                            placeholder="Enter your email"
                            style="flex:1;padding:11px 14px;border:1.5px solid #e5e7eb;border-radius:10px;font-size:0.875rem;font-family:'DM Sans',sans-serif;color:#3A3330;outline:none;transition:border-color .15s;min-width:0;"
                            @focus="$event.target.style.borderColor='#149189'"
                            @blur="$event.target.style.borderColor='#e5e7eb'"
                            @keyup.enter="subscribe"
                        >
                        <button
                            @click="subscribe"
                            :disabled="subLoading"
                            style="background:#ED730C;color:#fff;font-size:0.8125rem;font-weight:700;padding:11px 16px;border-radius:10px;border:none;cursor:pointer;font-family:'DM Sans',sans-serif;white-space:nowrap;transition:background .15s;flex-shrink:0;"
                            onmouseover="this.style.background='#d4620a'"
                            onmouseout="this.style.background='#ED730C'"
                        >
                            {{ subLoading ? '...' : 'Subscribe' }}
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </section>

</div>
</template>

<style>
.hide-scrollbar::-webkit-scrollbar { display: none; }
.hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
</style>