<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'

const el          = document.getElementById('services-app')
const allServices = ref(JSON.parse(el?.dataset.services || '[]'))

// ── sticky search on scroll ────────────────────────────────────────────────────
const heroSearchEl = ref(null)
const scrollY      = ref(0)
function onScroll() {
  scrollY.value = window.scrollY
  const slot = document.getElementById('nav-sticky-search')
  if (!slot) return
  let threshold = 280
  if (heroSearchEl.value) {
    threshold = heroSearchEl.value.offsetTop + heroSearchEl.value.offsetHeight - 60
  }
  slot.classList.toggle('open', scrollY.value > threshold)
}
onMounted(() => window.addEventListener('scroll', onScroll, { passive: true }))
onUnmounted(() => window.removeEventListener('scroll', onScroll))

// ── Nominatim geolocation ─────────────────────────────────────────────────────
const cityName   = ref('')
const areaName   = ref('')
onMounted(async () => {
  try {
    const pos = await new Promise((res, rej) =>
      navigator.geolocation.getCurrentPosition(res, rej, { timeout: 5000 })
    )
    const { latitude, longitude } = pos.coords
    const r = await fetch(
      `https://nominatim.openstreetmap.org/reverse?lat=${latitude}&lon=${longitude}&format=json`,
      { headers: { 'Accept-Language': 'en' } }
    )
    const data = await r.json()
    const addr = data.address || {}
    cityName.value = addr.city || addr.town || addr.municipality || ''
    areaName.value = addr.suburb || addr.neighbourhood || addr.quarter || addr.village || cityName.value
  } catch { /* geolocation denied or unavailable */ }
})

// ── filter state ──────────────────────────────────────────────────────────────
const activeCategory  = ref('All Services')
const searchInput     = ref('')
const locationInput   = ref('')
const swapyMatchOnly  = ref(false)
const filterEnabled   = ref(false)
const skeletonLoading = ref(false)
const showSortDropdown = ref(false)
const sortBy          = ref('Best Match')

const sortOptions = ['Best Match', 'Top Rated', 'Nearest First', 'Newest']

const categories = [
    'All Services',
    'Design & Creative',
    'Tech & Digital',
    'Education & Tutoring',
    'Home & Repair',
    'Business',
    'Creative',
]

function closeAllPanels() { showSortDropdown.value = false }

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

async function loadMore() {
  skeletonLoading.value = true
  await new Promise(r => setTimeout(r, 900))
  skeletonLoading.value = false
}
</script>

<template>
<div style="min-height:100vh;background:#FAF8F5;" @click="closeAllPanels">

    <!-- ══ STICKY NAV SEARCH ════════════════════════════════════════════════ -->
    <Teleport to="#nav-sticky-search">
        <div style="max-width:760px;margin:0 auto;" @click.stop>
            <div class="svc-sticky-desktop" style="background:#fff;border-radius:999px;display:flex;align-items:center;padding:6px 6px 6px 20px;box-shadow:0 8px 32px rgba(0,0,0,0.12);border:1.5px solid #EBEBEB;">
                <div style="flex:1;display:flex;align-items:center;gap:8px;border-right:1px solid #EBEBEB;padding-right:16px;">
                    <svg width="15" height="15" fill="none" stroke="#9ca3af" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
                    <input v-model="searchInput" type="text" placeholder="What are you looking for?"
                        style="border:none;outline:none;font-size:0.875rem;color:#1A1A1A;background:transparent;font-family:'DM Sans',sans-serif;width:100%;">
                </div>
                <div style="display:flex;align-items:center;gap:6px;padding:0 14px;">
                    <svg width="13" height="13" fill="#9ca3af" viewBox="0 0 24 24"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>
                    <input v-model="locationInput" type="text" placeholder="Location"
                        style="border:none;outline:none;font-size:0.875rem;color:#1A1A1A;background:transparent;font-family:'DM Sans',sans-serif;width:90px;">
                </div>
                <button style="background:#ED730C;color:#fff;border:none;border-radius:999px;padding:12px 28px;font-size:0.875rem;font-weight:800;cursor:pointer;font-family:'DM Sans',sans-serif;white-space:nowrap;box-shadow:0 4px 14px rgba(237,115,12,0.4);"
                    onmouseover="this.style.background='#d4620a'" onmouseout="this.style.background='#ED730C'">Search</button>
            </div>
            <div class="svc-sticky-mobile" style="background:#fff;border-radius:999px;align-items:center;padding:5px 5px 5px 16px;box-shadow:0 4px 20px rgba(0,0,0,0.10);border:1.5px solid #EBEBEB;">
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
        </div>
    </Teleport>

    <!-- ══════════════════════════════════════════
         HERO
    ══════════════════════════════════════════ -->
    <section style="padding:52px 24px 40px;background:#fff;text-align:center;border-bottom:1px solid #f3f4f6;">
        <div style="max-width:860px;margin:0 auto;">
            <div style="display:flex;align-items:baseline;gap:16px;flex-wrap:wrap;margin-bottom:28px;justify-content:center;">
                <h1 style="font-size:clamp(2.2rem,5vw,3.6rem);font-weight:900;line-height:1.05;letter-spacing:-.03em;color:#1A1A1A;margin:0;">Real skills.</h1>
                <h1 style="font-size:clamp(2.2rem,5vw,3.6rem);font-weight:900;line-height:1.05;letter-spacing:-.03em;color:#ED730C;margin:0;">Real people.</h1>
            </div>

            <!-- Hero search wrap (ref for threshold) -->
            <div ref="heroSearchEl" style="max-width:760px;margin:0 auto 20px;">
                <!-- Desktop pill -->
                <div class="svc-search-desktop" style="background:#fff;border-radius:999px;display:flex;align-items:center;padding:6px 6px 6px 20px;box-shadow:0 8px 32px rgba(0,0,0,0.12);border:1.5px solid #EBEBEB;">
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
                <div class="svc-search-mobile" style="display:none;background:#fff;border-radius:999px;align-items:center;padding:5px 5px 5px 16px;box-shadow:0 4px 20px rgba(0,0,0,0.10);border:1.5px solid #EBEBEB;">
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

    <!-- ══ STICKY FILTER BAR ═════════════════════════════════════════════════ -->
    <div style="position:sticky;top:0;z-index:40;background:#fff;border-bottom:1px solid #EDE8E0;box-shadow:0 2px 8px rgba(0,0,0,0.04);">
        <div style="max-width:1280px;margin:0 auto;padding:10px 24px;display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;">

            <!-- Left: Swapy match toggle -->
            <div style="display:flex;align-items:center;gap:10px;">
                <label style="display:flex;align-items:center;gap:8px;cursor:pointer;user-select:none;">
                    <div @click="swapyMatchOnly = !swapyMatchOnly; filterEnabled = swapyMatchOnly"
                        :style="{
                            width:'36px', height:'20px', borderRadius:'999px', position:'relative',
                            background: swapyMatchOnly ? '#ED730C' : '#e5e7eb',
                            transition:'background .2s', cursor:'pointer', flexShrink:'0',
                        }">
                        <div :style="{
                            position:'absolute', top:'2px',
                            left: swapyMatchOnly ? '18px' : '2px',
                            width:'16px', height:'16px', borderRadius:'50%',
                            background:'#fff', transition:'left .2s',
                            boxShadow:'0 1px 4px rgba(0,0,0,0.2)',
                        }"></div>
                    </div>
                    <span style="font-size:0.78rem;font-weight:700;color:#1A1A1A;font-family:'DM Sans',sans-serif;">Swapy Matches Only</span>
                </label>
            </div>

            <!-- Right: sort dropdown -->
            <div style="position:relative;" @click.stop>
                <button @click="showSortDropdown = !showSortDropdown"
                    style="display:flex;align-items:center;gap:7px;padding:8px 16px;border-radius:999px;border:1.5px solid #e2ddd8;background:#fff;font-family:'DM Sans',sans-serif;font-size:0.82rem;font-weight:700;color:#1A1A1A;cursor:pointer;transition:border-color .15s;">
                    <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 6h18M6 12h12M9 18h6"/></svg>
                    {{ sortBy }}
                    <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M6 9l6 6 6-6"/></svg>
                </button>
                <div v-if="showSortDropdown"
                    style="position:absolute;top:calc(100% + 8px);right:0;background:#fff;border:1px solid #EBEBEB;border-radius:16px;padding:6px;min-width:180px;box-shadow:0 8px 32px rgba(0,0,0,0.10);z-index:100;">
                    <button v-for="opt in sortOptions" :key="opt" @click="sortBy = opt; showSortDropdown = false"
                        :style="{display:'block',width:'100%',textAlign:'left',padding:'9px 14px',borderRadius:'10px',border:'none',background:sortBy===opt?'#fff4ec':'transparent',color:sortBy===opt?'#ED730C':'#1A1A1A',fontSize:'0.85rem',fontWeight:sortBy===opt?'700':'500',fontFamily:'\'DM Sans\',sans-serif',cursor:'pointer'}">
                        {{ opt }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- ══════════════════════════════════════════
         MAIN LAYOUT
    ══════════════════════════════════════════ -->
    <div style="max-width:1280px;margin:0 auto;padding:36px 24px 72px;">

        <!-- Section label -->
        <div style="margin-bottom:24px;">
            <p style="font-size:0.65rem;font-weight:800;letter-spacing:.1em;text-transform:uppercase;color:#ED730C;margin:0 0 4px;">
                {{ activeCategory === 'All Services' ? 'All Services' : activeCategory }}
            </p>
            <h2 style="font-size:1.35rem;font-weight:900;color:#1A1A1A;margin:0;letter-spacing:-.02em;">
                Services {{ cityName ? 'in ' + cityName : 'Near You' }}
            </h2>
        </div>

        <!-- Skeleton loading -->
        <div v-if="skeletonLoading" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:20px;">
            <div v-for="n in 6" :key="n" style="background:#fff;border-radius:16px;overflow:hidden;border:1px solid #f3f4f6;">
                <div style="aspect-ratio:16/10;background:linear-gradient(90deg,#f0ece6 25%,#e8e3dc 50%,#f0ece6 75%);background-size:200% 100%;animation:shimmer 1.4s infinite;"></div>
                <div style="padding:16px 18px 18px;">
                    <div style="height:14px;background:#f0ece6;border-radius:6px;margin-bottom:8px;width:75%;animation:shimmer 1.4s infinite;"></div>
                    <div style="height:11px;background:#f0ece6;border-radius:6px;width:50%;animation:shimmer 1.4s infinite;"></div>
                </div>
            </div>
        </div>

        <!-- Services grid -->
        <div v-else style="display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:20px;">

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

                            <!-- Action row -->
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
        <div v-if="!skeletonLoading && filtered.length === 0" style="text-align:center;padding:80px 0;">
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

        <!-- Load more -->
        <div v-if="!skeletonLoading && filtered.length > 0" style="text-align:center;margin-top:52px;">
            <button @click="loadMore"
                style="display:inline-flex;align-items:center;gap:8px;padding:13px 36px;background:#fff;border:1.5px solid #1A1A1A;border-radius:999px;font-size:0.82rem;font-weight:800;color:#1A1A1A;cursor:pointer;font-family:'DM Sans',sans-serif;letter-spacing:.04em;transition:all .15s;"
                onmouseover="this.style.background='#1A1A1A';this.style.color='#fff'"
                onmouseout="this.style.background='#fff';this.style.color='#1A1A1A'">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" d="M19 9l-7 7-7-7"/></svg>
                Load More Services
            </button>
        </div>

    </div>

</div>
</template>

<style scoped>
@media (max-width: 767px) {
  .svc-search-desktop { display: none !important; }
  .svc-search-mobile  { display: flex !important; }
  .svc-sticky-desktop { display: none !important; }
  .svc-sticky-mobile  { display: flex !important; }
}
.svc-sticky-mobile { display: none; }

@keyframes shimmer {
  0%   { background-position: 200% 0; }
  100% { background-position: -200% 0; }
}
</style>