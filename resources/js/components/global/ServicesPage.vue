<script setup>
import { ref, computed } from 'vue'

const el          = document.getElementById('services-app')
const allServices = ref(JSON.parse(el?.dataset.services || '[]'))

// ── filter state ──────────────────────────────────────────────────────────────
const activeCategory  = ref('All Services')
const searchInput     = ref('')
const locationInput   = ref('')
const swapyMatchOnly  = ref(false)
const filterEnabled   = ref(false)

const categories = [
    'All Services',
    'Design & Creative',
    'Tech & Digital',
    'Education & Tutoring',
    'Home & Repair',
    'Business',
    'Creative',
]

// ── bookmarks ─────────────────────────────────────────────────────────────────
const bookmarked = ref(new Set())
function toggleBookmark(id) {
    const s = new Set(bookmarked.value)
    s.has(id) ? s.delete(id) : s.add(id)
    bookmarked.value = s
}

// ── reactive filter ───────────────────────────────────────────────────────────
const filtered = computed(() => {
    return allServices.value.filter(s => {
        if (activeCategory.value !== 'All Services' && s.category !== activeCategory.value) return false
        if (filterEnabled.value && swapyMatchOnly.value && !s.is_match) return false
        return true
    })
})

// ── match badge config ────────────────────────────────────────────────────────
function matchBadge(service) {
    const map = {
        'high_match':      { label: 'High Match',           color: '#149189', bg: '#e6f7f6' },
        'direct_match':    { label: 'Direct Match Available',color: '#149189', bg: '#e6f7f6' },
        'mutual_interest': { label: 'Mutual Interest Found', color: '#ED730C', bg: '#fff4ec' },
        'verified':        { label: 'Verified Provider',     color: '#149189', bg: '#e6f7f6' },
        'local_match':     { label: 'Local Match',           color: '#149189', bg: '#e6f7f6' },
        'swap_potential':  { label: null,                    color: '#ED730C', bg: '#fff4ec' },
    }
    return map[service.match_type] || null
}
</script>

<template>
<div style="min-height:100vh;background:#f9fafb;">

    <!-- ══════════════════════════════════════════
         HERO
    ══════════════════════════════════════════ -->
    <section style="padding:52px 24px 40px;background:#fff;text-align:center;border-bottom:1px solid #f3f4f6;">
        <div style="max-width:860px;margin:0 auto;">
            <div style="display:flex;align-items:baseline;gap:16px;flex-wrap:wrap;margin-bottom:28px;justify-content:center;">
                <h1 style="font-size:clamp(2.2rem,5vw,3.6rem);font-weight:900;line-height:1.05;letter-spacing:-.03em;color:#1A1A1A;margin:0;">Real skills.</h1>
                <h1 style="font-size:clamp(2.2rem,5vw,3.6rem);font-weight:900;line-height:1.05;letter-spacing:-.03em;color:#ED730C;margin:0;">Real people.</h1>
            </div>

            <!-- Desktop pill -->
            <div class="svc-search-desktop" style="max-width:760px;margin:0 auto 20px;background:#fff;border-radius:999px;display:flex;align-items:center;padding:6px 6px 6px 20px;box-shadow:0 8px 32px rgba(0,0,0,0.12);border:1.5px solid #EBEBEB;">
                <div style="flex:1;display:flex;align-items:center;gap:8px;border-right:1px solid #EBEBEB;padding-right:16px;">
                    <svg width="15" height="15" fill="none" stroke="#9ca3af" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
                    <input v-model="searchInput" type="text" placeholder="What are you looking for?"
                        style="border:none;outline:none;font-size:0.875rem;color:#1A1A1A;background:transparent;font-family:'DM Sans',sans-serif;width:100%;">
                </div>
                <div style="display:flex;align-items:center;gap:6px;padding:0 14px;">
                    <svg width="13" height="13" fill="#9ca3af" viewBox="0 0 24 24"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>
                    <input v-model="locationInput" type="text" placeholder="Location"
                        style="border:none;outline:none;font-size:0.875rem;color:#1A1A1A;background:transparent;font-family:'DM Sans',sans-serif;width:110px;">
                </div>
                <button style="background:#ED730C;color:#fff;border:none;border-radius:999px;padding:12px 28px;font-size:0.875rem;font-weight:800;cursor:pointer;font-family:'DM Sans',sans-serif;white-space:nowrap;box-shadow:0 4px 14px rgba(237,115,12,0.4);"
                    onmouseover="this.style.background='#d4620a'" onmouseout="this.style.background='#ED730C'">Search</button>
            </div>
            <!-- Mobile pill -->
            <div class="svc-search-mobile" style="display:none;max-width:760px;margin:0 auto 20px;background:#fff;border-radius:999px;align-items:center;padding:5px 5px 5px 16px;box-shadow:0 4px 20px rgba(0,0,0,0.10);border:1.5px solid #EBEBEB;">
                <svg width="14" height="14" fill="none" stroke="#9ca3af" stroke-width="2" viewBox="0 0 24 24" style="flex-shrink:0;margin-right:8px;"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
                <input v-model="searchInput" type="text" placeholder="Search"
                    style="flex:1;border:none;outline:none;font-size:0.875rem;color:#1A1A1A;background:transparent;font-family:'DM Sans',sans-serif;min-width:0;">
                <div style="display:flex;align-items:center;gap:4px;padding:0 10px;border-left:1px solid #EBEBEB;flex-shrink:0;">
                    <svg width="12" height="12" fill="#9ca3af" viewBox="0 0 24 24"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>
                    <input v-model="locationInput" type="text" placeholder="Location"
                        style="border:none;outline:none;font-size:0.82rem;color:#1A1A1A;background:transparent;font-family:'DM Sans',sans-serif;width:70px;">
                </div>
                <button style="background:#ED730C;color:#fff;border:none;border-radius:999px;padding:9px 18px;font-size:0.82rem;font-weight:800;cursor:pointer;font-family:'DM Sans',sans-serif;white-space:nowrap;flex-shrink:0;">Search</button>
            </div>
            <!-- Filter pills -->
            <div style="display:flex;gap:8px;flex-wrap:wrap;justify-content:center;">
                <button v-for="cat in categories" :key="cat" @click="activeCategory = cat"
                    :style="{padding:'7px 16px',borderRadius:'999px',fontSize:'0.78rem',fontWeight:'600',fontFamily:'\'DM Sans\',sans-serif',cursor:'pointer',border:activeCategory===cat?'none':'1px solid #EBEBEB',background:activeCategory===cat?'#1A1A1A':'#fff',color:activeCategory===cat?'#fff':'#4b5563',boxShadow:activeCategory===cat?'0 4px 12px rgba(0,0,0,0.15)':'none',transition:'all 0.15s'}">
                    {{ cat }}
                </button>
            </div>
        </div>
    </section>

    <!-- ══════════════════════════════════════════
         MAIN LAYOUT
    ══════════════════════════════════════════ -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" style="padding-top:36px;padding-bottom:72px;">
        <div style="display:flex;gap:32px;align-items:flex-start;">

            <!-- ════════════════════════════
                 SERVICES GRID
            ════════════════════════════ -->
            <div style="flex:1;min-width:0;">

                <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:20px;">

                    <div
                        v-for="service in filtered"
                        :key="service.id"
                        style="background:#fff;border-radius:16px;overflow:hidden;border:1px solid #f3f4f6;display:flex;flex-direction:column;transition:border-color .2s,box-shadow .2s;"
                        onmouseover="this.style.borderColor='#e5e7eb';this.style.boxShadow='0 4px 20px rgba(0,0,0,0.07)'"
                        onmouseout="this.style.borderColor='#f3f4f6';this.style.boxShadow='none'"
                    >

                        <!-- Image + badges -->
                        <div style="position:relative;aspect-ratio:16/10;overflow:hidden;background:#f3f4f6;">
                            <img
                                :src="service.image"
                                :alt="service.title"
                                style="width:100%;height:100%;object-fit:cover;transition:transform .35s;"
                                onmouseover="this.style.transform='scale(1.04)'"
                                onmouseout="this.style.transform='scale(1)'"
                            >
                            <!-- Dual badge row: SERVICE + category -->
                            <div style="position:absolute;top:10px;left:10px;display:flex;gap:6px;">
                                <span style="background:#149189;color:#fff;font-size:0.62rem;font-weight:800;padding:4px 9px;border-radius:6px;letter-spacing:.05em;text-transform:uppercase;">
                                    Service
                                </span>
                                <span style="background:rgba(255,255,255,0.92);color:#3A3330;font-size:0.62rem;font-weight:700;padding:4px 9px;border-radius:6px;letter-spacing:.04em;text-transform:uppercase;backdrop-filter:blur(4px);">
                                    {{ service.tag }}
                                </span>
                            </div>
                        </div>

                        <!-- Card body -->
                        <div style="padding:16px 18px 18px;flex:1;display:flex;flex-direction:column;">

                            <!-- Title -->
                            <h3 style="font-size:1rem;font-weight:700;color:#3A3330;line-height:1.3;margin-bottom:7px;">
                                {{ service.title }}
                            </h3>

                            <!-- Description -->
                            <p style="font-size:0.8375rem;color:#6b7280;line-height:1.55;margin-bottom:14px;flex:1;">
                                {{ service.description }}
                            </p>

                            <!-- Match indicator row -->
                            <div style="display:flex;align-items:center;gap:8px;margin-bottom:14px;">
                                <!-- Provider avatar stack -->
                                <div style="display:flex;align-items:center;">
                                    <div style="width:26px;height:26px;border-radius:50%;background:#ED730C;border:2px solid #fff;display:flex;align-items:center;justify-content:center;font-size:0.55rem;font-weight:800;color:#fff;flex-shrink:0;">
                                        {{ service.provider_initials }}
                                    </div>
                                    <div style="width:26px;height:26px;border-radius:50%;background:#149189;border:2px solid #fff;margin-left:-8px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                        <!-- Swap icon -->
                                        <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.5" stroke-linecap="round">
                                            <path d="M7 16V4m0 0L3 8m4-4l4 4M17 8v12m0 0l4-4m-4 4l-4-4"/>
                                        </svg>
                                    </div>
                                </div>

                                <!-- Match badge text -->
                                <template v-if="service.match_type === 'swap_potential'">
                                    <span style="font-size:0.78rem;font-weight:700;color:#ED730C;">
                                        {{ service.match_percent }}% Swap Potential
                                    </span>
                                </template>
                                <template v-else-if="matchBadge(service)">
                                    <span :style="{fontSize:'0.78rem',fontWeight:'700',color:matchBadge(service).color}">
                                        {{ matchBadge(service).label }}
                                    </span>
                                </template>
                            </div>

                            <!-- Action row: Swap for Service + Bookmark -->
                            <div style="display:flex;align-items:center;gap:10px;">
                                <a
                                    :href="'/services/' + service.id"
                                    style="flex:1;display:flex;align-items:center;justify-content:center;background:#ED730C;color:#fff;font-size:0.8125rem;font-weight:700;padding:11px 16px;border-radius:999px;text-decoration:none;font-family:'DM Sans',sans-serif;transition:background .15s;text-align:center;"
                                    onmouseover="this.style.background='#d4620a'"
                                    onmouseout="this.style.background='#ED730C'"
                                >
                                    Swap for Service
                                </a>
                                <!-- Bookmark -->
                                <button
                                    @click="toggleBookmark(service.id)"
                                    :style="{
                                        width:'38px',height:'38px',border:'1.5px solid',borderRadius:'10px',
                                        background:'#fff',cursor:'pointer',display:'flex',
                                        alignItems:'center',justifyContent:'center',flexShrink:'0',
                                        borderColor: bookmarked.has(service.id) ? '#ED730C' : '#e5e7eb',
                                        transition:'all .15s',
                                    }"
                                >
                                    <svg
                                        :style="{width:'15px',height:'15px',fill:bookmarked.has(service.id)?'#ED730C':'none',stroke:bookmarked.has(service.id)?'#ED730C':'#9ca3af',strokeWidth:'2',transition:'all .15s'}"
                                        viewBox="0 0 24 24"
                                    >
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/>
                                    </svg>
                                </button>
                            </div>

                        </div>
                    </div>

                </div>

                <!-- Empty state -->
                <div v-if="filtered.length === 0" style="text-align:center;padding:80px 0;">
                    <div style="font-size:3.5rem;margin-bottom:16px;">🛠️</div>
                    <h3 style="font-size:1.125rem;font-weight:700;color:#3A3330;margin-bottom:8px;">No services found</h3>
                    <p style="font-size:0.875rem;color:#9ca3af;margin-bottom:20px;">Try a different category or disable the match filter.</p>
                    <button
                        @click="activeCategory='All Services';filterEnabled=false;swapyMatchOnly=false"
                        style="font-size:0.8rem;font-weight:700;color:#149189;background:none;border:1.5px solid #149189;border-radius:999px;padding:9px 22px;cursor:pointer;font-family:'DM Sans',sans-serif;"
                    >
                        Clear filters
                    </button>
                </div>

            </div><!-- end grid -->
        </div>
    </div>

</div>
</template>

<style scoped>
@media (max-width: 767px) {
  .svc-search-desktop { display: none !important; }
  .svc-search-mobile  { display: flex !important; }
}
</style>