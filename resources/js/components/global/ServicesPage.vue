<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import ServiceCard from './ServiceCard.vue'
import { setCurrencyFromCountry } from '../../constants/currency'

const el       = document.getElementById('services-app')
const services = ref(JSON.parse(el?.dataset.services || '[]'))

// ── sticky search ─────────────────────────────────────────────────────────────
const heroSearchEl = ref(null)
const scrollY      = ref(0)
function onScroll() {
  scrollY.value = window.scrollY
  const slot = document.getElementById('nav-sticky-search')
  if (!slot) return
  let threshold = 280
  if (heroSearchEl.value) threshold = heroSearchEl.value.offsetTop + heroSearchEl.value.offsetHeight - 60
  // Hysteresis: open past the hero, close only well before it — no flicker
  const isOpen = slot.classList.contains('open')
  if (!isOpen && scrollY.value > threshold + 40) slot.classList.add('open')
  else if (isOpen && scrollY.value < threshold - 40) slot.classList.remove('open')
}
onMounted(() => window.addEventListener('scroll', onScroll, { passive: true }))
onUnmounted(() => window.removeEventListener('scroll', onScroll))

// ── geolocation ───────────────────────────────────────────────────────────────
const cityName   = ref('')
const areaName   = ref('')
const nearbyCity = ref('General Santos')
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
    if (addr.country_code) setCurrencyFromCountry(addr.country_code)
    cityName.value   = addr.city || addr.town || addr.municipality || ''
    areaName.value   = addr.suburb || addr.neighbourhood || addr.quarter || addr.village || cityName.value
    nearbyCity.value = 'General Santos'
  } catch { /* geolocation denied */ }
})

// ── fallback data ─────────────────────────────────────────────────────────────
const fakeServices = [
  { id:1,  is_promoted:true, title:'Logo & Brand Identity Design',       description:'Complete brand package: logo, color palette, typography, and brand guide.',  category:'Design & Creative',   tag:'Design',   city:'Davao City',     area:'Poblacion', rating:4.9, provider:'Marco R.',  provider_initials:'MR', provider_color:'#ED730C', match_type:'high_match',      match_percent:92, is_match:true,  image:'https://images.unsplash.com/photo-1561070791-2526d30994b5?w=800&q=80' },
  { id:2,  title:'React / Vue.js Web Development',     description:'Build fast, responsive SPAs and dashboards. API integration included.',       category:'Tech & Digital',      tag:'Tech',     city:'Davao City',     area:'CBD',       rating:4.8, provider:'Angela T.', provider_initials:'AT', provider_color:'#14b8a6', match_type:'direct_match',    match_percent:88, is_match:true,  image:'https://images.unsplash.com/photo-1537432376769-00f5c2f4c8d2?w=800&q=80' },
  { id:3,  title:'Math & Science Tutoring (K–12)',      description:'Patient, effective tutoring for students struggling with STEM subjects.',      category:'Education & Tutoring',tag:'Tutoring', city:'Davao City',     area:'Matina',    rating:5.0, provider:'Clara S.',  provider_initials:'CS', provider_color:'#8b5cf6', match_type:'verified',        match_percent:95, is_match:true,  image:'https://images.unsplash.com/photo-1503676260728-1c00da094a0b?w=800&q=80' },
  { id:4,  title:'Home Painting & Finishing',           description:'Interior and exterior painting, wallpaper, and decorative finishes.',          category:'Home & Repair',       tag:'Repair',   city:'Davao City',     area:'Buhangin',  rating:4.7, provider:'Dennis L.', provider_initials:'DL', provider_color:'#f59e0b', match_type:'swap_potential',  match_percent:74, is_match:false, image:'https://images.unsplash.com/photo-1562259949-e8e7689d7828?w=800&q=80' },
  { id:5,  title:'Social Media Content Creation',       description:'Monthly content calendar, captions, graphics, and scheduling for IG/FB.',     category:'Creative',            tag:'Creative', city:'Davao City',     area:'Poblacion', rating:4.9, provider:'Nadia P.',  provider_initials:'NP', provider_color:'#ec4899', match_type:'mutual_interest', match_percent:81, is_match:true,  image:'https://images.unsplash.com/photo-1611162617213-7d7a39e9b1d7?w=800&q=80' },
  { id:6,  title:'Business Plan Writing & Consulting',  description:'Investor-ready business plans, pitch decks, and market research.',             category:'Business',            tag:'Business', city:'Davao City',     area:'CBD',       rating:4.8, provider:'Jose P.',   provider_initials:'JP', provider_color:'#ED730C', match_type:'local_match',     match_percent:87, is_match:true,  image:'https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?w=800&q=80' },
  { id:7,  is_promoted:true, title:'UI/UX Design & Prototyping',          description:'Figma wireframes, clickable prototypes, and user journey mapping.',            category:'Design & Creative',   tag:'Design',   city:'Davao City',     area:'Matina',    rating:4.9, provider:'Emma R.',   provider_initials:'ER', provider_color:'#ED730C', match_type:'high_match',      match_percent:93, is_match:true,  image:'https://images.unsplash.com/photo-1581291518857-4e27b48ff24e?w=800&q=80' },
  { id:8,  title:'Electrical Installation & Repair',    description:'Safe, code-compliant wiring, outlets, panels, and lighting fixtures.',         category:'Home & Repair',       tag:'Repair',   city:'Davao City',     area:'Agdao',     rating:4.6, provider:'Ramon D.',  provider_initials:'RD', provider_color:'#f59e0b', match_type:'swap_potential',  match_percent:68, is_match:false, image:'https://images.unsplash.com/photo-1621905251189-08b45d6a269e?w=800&q=80' },
  { id:9,  title:'English Language Coaching',           description:'Conversational English, business writing, and IELTS/TOEFL preparation.',        category:'Education & Tutoring',tag:'Tutoring', city:'Taguig',         area:'BGC',       rating:4.8, provider:'Ruth V.',   provider_initials:'RV', provider_color:'#8b5cf6', match_type:'direct_match',    match_percent:85, is_match:true,  image:'https://images.unsplash.com/photo-1456513080510-7bf3a84b82f8?w=800&q=80' },
  { id:10, is_promoted:true, title:'Full-Stack Laravel Development',      description:'End-to-end web apps with Laravel, Vue, REST APIs, and deployment pipelines.',  category:'Tech & Digital',      tag:'Tech',     city:'Makati',         area:'Salcedo',   rating:5.0, provider:'Jake M.',   provider_initials:'JM', provider_color:'#14b8a6', match_type:'high_match',      match_percent:97, is_match:true,  image:'https://images.unsplash.com/photo-1555066931-4365d14431b9?w=800&q=80' },
  { id:11, is_promoted:true, title:'Photography & Photo Editing',         description:'Product, lifestyle, and event photography with quick turnaround edits.',        category:'Creative',            tag:'Creative', city:'Taguig',         area:'BGC',       rating:4.9, provider:'Liza G.',   provider_initials:'LG', provider_color:'#ec4899', match_type:'verified',        match_percent:90, is_match:true,  image:'https://images.unsplash.com/photo-1516035069371-29a1b244cc32?w=800&q=80' },
  { id:12, title:'Bookkeeping & Tax Filing',            description:'Monthly bookkeeping, BIR filing, and financial statement preparation.',          category:'Business',            tag:'Business', city:'Quezon City',    area:'Eastwood',  rating:4.7, provider:'Fe C.',     provider_initials:'FC', provider_color:'#ED730C', match_type:'local_match',     match_percent:79, is_match:true,  image:'https://images.unsplash.com/photo-1554224155-6726b3ff858f?w=800&q=80' },
  { id:13, title:'Plumbing & Waterproofing',            description:'Leak detection, pipe repairs, and waterproofing for residential properties.',   category:'Home & Repair',       tag:'Repair',   city:'General Santos', area:'Downtown',  rating:4.7, provider:'Leo T.',    provider_initials:'LT', provider_color:'#ED730C', match_type:'swap_potential',  match_percent:72, is_match:false, image:'https://images.unsplash.com/photo-1607472586893-edb57bdc0e39?w=800&q=80' },
  { id:14, title:'Video Editing & Motion Graphics',     description:'YouTube videos, reels, corporate edits, and animated explainer videos.',        category:'Creative',            tag:'Creative', city:'General Santos', area:'Lagao',     rating:4.8, provider:'Carla M.',  provider_initials:'CM', provider_color:'#f59e0b', match_type:'mutual_interest', match_percent:83, is_match:true,  image:'https://images.unsplash.com/photo-1574717024653-61fd2cf4d44d?w=800&q=80' },
]

const allServices = computed(() => services.value.length ? services.value : fakeServices)

// ── sections 1–4 ──────────────────────────────────────────────────────────────
// Featured = promoted listings only
const featuredServices = computed(() =>
  allServices.value.filter(s => s.is_promoted).slice(0, 8)
)
const cityServices = computed(() => {
  if (!cityName.value) return allServices.value.slice(0, 6)
  const inCity = allServices.value.filter(s => s.city?.toLowerCase().includes(cityName.value.toLowerCase()))
  return inCity.length >= 3 ? inCity.slice(0, 8) : allServices.value.slice(0, 6)
})
const nearbyCityServices = computed(() => {
  if (!nearbyCity.value || nearbyCity.value === cityName.value) return []
  const inNearby = allServices.value.filter(s => s.city?.toLowerCase().includes(nearbyCity.value.toLowerCase()))
  return inNearby.length >= 3 ? inNearby.slice(0, 8) : []
})
const areaServices = computed(() => {
  if (!areaName.value || areaName.value === cityName.value) return allServices.value.slice(0, 6)
  const inArea = allServices.value.filter(s => s.area?.toLowerCase().includes(areaName.value.toLowerCase()))
  return inArea.length >= 3 ? inArea.slice(0, 8) : allServices.value.slice(0, 6)
})

// ── section 5: filters ────────────────────────────────────────────────────────
const searchInput    = ref('')
const locationInput  = ref('')
const search         = ref('')
const activeCategory = ref('All')
const sortBy         = ref('Best Match')
const skeletonLoading    = ref(false)
const showSortDropdown   = ref(false)
const showCatPanel       = ref(false)

const categories  = ['All', 'Design & Creative', 'Tech & Digital', 'Education & Tutoring', 'Home & Repair', 'Business', 'Creative']
const sortOptions = ['Best Match', 'Top Rated', 'Nearest First', 'Newest']

function closeAllPanels() { showSortDropdown.value = false; showCatPanel.value = false }
function doSearch() {
  const params = new URLSearchParams()
  if (searchInput.value.trim())   params.set('q', searchInput.value.trim())
  if (locationInput.value.trim()) params.set('location', locationInput.value.trim())
  window.location.href = '/search?' + params.toString()
}

const filtered = computed(() => {
  let list = [...allServices.value]
  if (search.value.trim()) list = list.filter(s => s.title.toLowerCase().includes(search.value.toLowerCase()) || s.description.toLowerCase().includes(search.value.toLowerCase()))
  if (activeCategory.value !== 'All') list = list.filter(s => s.category === activeCategory.value)
  if (sortBy.value === 'Top Rated') list = [...list].sort((a, b) => b.rating - a.rating)
  return list
})

// ── saved services ────────────────────────────────────────────────────────────
const bookmarked = ref(new Set())
function toggleBookmark(id) {
  const s = new Set(bookmarked.value)
  s.has(id) ? s.delete(id) : s.add(id)
  bookmarked.value = s
}

async function loadMore() {
  skeletonLoading.value = true
  await new Promise(r => setTimeout(r, 900))
  skeletonLoading.value = false
}
</script>

<template>
<div style="min-height:100vh;background:#fff;font-family:'DM Sans',sans-serif;" @click="closeAllPanels">

  <!-- ═══════════════════════════════════════════
       STICKY NAV SEARCH
  ═══════════════════════════════════════════ -->
  <Teleport to="#nav-sticky-search">
    <!-- identical markup to the search-page nav bar (navigation.blade.php) -->
    <form class="nav-search-bar" role="search" @submit.prevent="doSearch" @click.stop>
      <svg class="nsb-icon" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path stroke-linecap="round" d="M21 21l-4.35-4.35"/></svg>
      <input v-model="searchInput" type="search" class="nsb-q" placeholder="What are you looking for?" enterkeyhint="search" autocomplete="off" aria-label="Search">
      <span class="nsb-divider"></span>
      <svg class="nsb-icon" width="15" height="15" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5a2.5 2.5 0 110-5 2.5 2.5 0 010 5z"/></svg>
      <input v-model="locationInput" type="text" class="nsb-loc" placeholder="Location" aria-label="Location">
      <button type="submit" class="nsb-btn">Search</button>
    </form>
  </Teleport>

  <!-- ═══════════════════════════════════════════
       HERO
  ═══════════════════════════════════════════ -->
  <section style="padding:52px 24px 40px;background:#fff;text-align:center;">
    <div style="max-width:860px;margin:0 auto;">
      <div style="display:flex;align-items:baseline;gap:16px;flex-wrap:wrap;margin-bottom:28px;justify-content:center;">
        <h1 style="font-size:clamp(2.2rem,5vw,3.6rem);font-weight:900;line-height:1.05;letter-spacing:-.03em;color:#1A1A1A;margin:0;">Real skills.</h1>
        <h1 style="font-size:clamp(2.2rem,5vw,3.6rem);font-weight:900;line-height:1.05;letter-spacing:-.03em;color:#ED730C;margin:0;">Real people.</h1>
      </div>
      <div ref="heroSearchEl" class="hero-search-wrap">
        <div class="hero-search-desktop">
          <div style="flex:1;display:flex;align-items:center;gap:8px;border-right:1px solid #EBEBEB;padding-right:16px;">
            <svg width="15" height="15" fill="none" stroke="#9ca3af" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
            <input v-model="searchInput" type="text" placeholder="What are you looking for?" style="border:none;outline:none;font-size:0.875rem;color:#1A1A1A;background:transparent;font-family:'DM Sans',sans-serif;width:100%;" @keydown.enter="doSearch">
          </div>
          <div style="display:flex;align-items:center;gap:6px;padding:0 14px;">
            <svg width="13" height="13" fill="#9ca3af" viewBox="0 0 24 24"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>
            <input v-model="locationInput" type="text" placeholder="Location" style="border:none;outline:none;font-size:0.875rem;color:#1A1A1A;background:transparent;font-family:'DM Sans',sans-serif;width:110px;">
          </div>
          <button @click="doSearch" style="background:#ED730C;color:#fff;border:none;border-radius:999px;padding:12px 28px;font-size:0.875rem;font-weight:800;cursor:pointer;font-family:'DM Sans',sans-serif;white-space:nowrap;box-shadow:0 4px 14px rgba(237,115,12,0.4);" onmouseover="this.style.background='#d4620a'" onmouseout="this.style.background='#ED730C'">Search</button>
        </div>
        <div class="hero-search-mobile">
          <svg width="15" height="15" fill="none" stroke="#9ca3af" stroke-width="2" viewBox="0 0 24 24" style="flex-shrink:0;"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
          <input v-model="searchInput" type="text" placeholder="Search services" style="flex:1;border:none;outline:none;font-size:0.875rem;color:#1A1A1A;background:transparent;font-family:'DM Sans',sans-serif;min-width:0;" @keydown.enter="doSearch">
          <div style="display:flex;align-items:center;gap:4px;padding:0 10px;border-left:1px solid #EBEBEB;flex-shrink:0;">
            <svg width="12" height="12" fill="#9ca3af" viewBox="0 0 24 24"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>
            <input v-model="locationInput" type="text" placeholder="Location" style="border:none;outline:none;font-size:0.82rem;color:#1A1A1A;background:transparent;font-family:'DM Sans',sans-serif;width:70px;">
          </div>
          <button @click="doSearch" style="background:#ED730C;color:#fff;border:none;border-radius:999px;padding:10px 18px;font-size:0.82rem;font-weight:800;cursor:pointer;font-family:'DM Sans',sans-serif;white-space:nowrap;flex-shrink:0;">Search</button>
        </div>
      </div>
      <div style="display:flex;gap:8px;flex-wrap:wrap;justify-content:center;">
        <button v-for="cat in categories" :key="cat" @click="activeCategory = cat"
          :style="{padding:'7px 16px',borderRadius:'999px',fontSize:'0.78rem',fontWeight:'600',fontFamily:'\'DM Sans\',sans-serif',cursor:'pointer',border:activeCategory===cat?'none':'1px solid #EBEBEB',background:activeCategory===cat?'#1A1A1A':'#fff',color:activeCategory===cat?'#fff':'#4b5563',boxShadow:activeCategory===cat?'0 4px 12px rgba(0,0,0,0.15)':'none',transition:'all 0.15s'}">
          {{ cat }}
        </button>
      </div>
    </div>
  </section>

  <!-- ───────────────────────────────────────────
       1. FEATURED SERVICES (promoted listings only)
  ─────────────────────────────────────────── -->
  <section v-if="featuredServices.length" class="scroll-section">
    <div class="section-inner">
      <div class="section-header">
        <div>
          <h2 class="section-title">Featured Services</h2>
          <p class="section-sub">Promoted providers from the community</p>
        </div>
        <a href="/services/section/featured" class="see-all">See all →</a>
      </div>
      <div class="hscroll">
        <ServiceCard v-for="svc in featuredServices" :key="'feat-'+svc.id" class="hscroll-card"
          :svc="svc" :saved="bookmarked.has(svc.id)" @toggle-save="toggleBookmark(svc.id)" />
      </div>
    </div>
  </section>

  <!-- ───────────────────────────────────────────
       2. POPULAR IN [CITY]
  ─────────────────────────────────────────── -->
  <section class="scroll-section">
    <div class="section-inner">
      <div class="section-header">
        <div>
          <h2 class="section-title">Popular in {{ cityName || 'Your City' }}</h2>
          <p class="section-sub">Most requested services in your city</p>
        </div>
        <a href="/services/section/popular" class="see-all">See all →</a>
      </div>
      <div class="hscroll">
        <ServiceCard v-for="svc in cityServices" :key="'city-'+svc.id" class="hscroll-card"
          :svc="svc" :saved="bookmarked.has(svc.id)" @toggle-save="toggleBookmark(svc.id)" />
      </div>
    </div>
  </section>

  <!-- ───────────────────────────────────────────
       3. AVAILABLE IN [NEARBY CITY]  (conditional)
  ─────────────────────────────────────────── -->
  <section v-if="nearbyCityServices.length" class="scroll-section">
    <div class="section-inner">
      <div class="section-header">
        <div>
          <h2 class="section-title">Available in {{ nearbyCity }}</h2>
          <p class="section-sub">Service providers from a nearby city</p>
        </div>
        <a href="/services/section/nearby" class="see-all">See all →</a>
      </div>
      <div class="hscroll">
        <ServiceCard v-for="svc in nearbyCityServices" :key="'near-'+svc.id" class="hscroll-card"
          :svc="svc" :saved="bookmarked.has(svc.id)" @toggle-save="toggleBookmark(svc.id)" />
      </div>
    </div>
  </section>

  <!-- ───────────────────────────────────────────
       4. SERVICES NEAR [AREA]
  ─────────────────────────────────────────── -->
  <section class="scroll-section">
    <div class="section-inner">
      <div class="section-header">
        <div>
          <h2 class="section-title">Services near {{ areaName || 'Your Area' }}</h2>
          <p class="section-sub">Providers closest to your location</p>
        </div>
        <a href="/services/section/near-you" class="see-all">See all →</a>
      </div>
      <div class="hscroll">
        <ServiceCard v-for="svc in areaServices" :key="'area-'+svc.id" class="hscroll-card"
          :svc="svc" :saved="bookmarked.has(svc.id)" @toggle-save="toggleBookmark(svc.id)" />
      </div>
    </div>
  </section>

  <!-- ───────────────────────────────────────────
       5. ALL SERVICES
  ─────────────────────────────────────────── -->
  <section style="background:#fff;" @click.stop>

    <!-- Filter bar -->
    <div class="filter-bar">
      <div class="filter-bar-inner">
        <div class="filter-left">
          <!-- Category -->
          <div style="position:relative;">
            <button @click.stop="showCatPanel=!showCatPanel;showSortDropdown=false" class="filter-btn">
              <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 4h13M3 8h9m-9 4h9m5-4v12m0 0l-4-4m4 4l4-4"/></svg>
              {{ activeCategory === 'All' ? 'All Categories' : activeCategory }}
            </button>
            <div v-if="showCatPanel" class="dropdown-panel">
              <button v-for="cat in categories" :key="cat" @click.stop="activeCategory=cat;showCatPanel=false" :class="['dropdown-item', {active: activeCategory===cat}]">{{ cat }}</button>
            </div>
          </div>
          <!-- Sort -->
          <div style="position:relative;">
            <button @click.stop="showSortDropdown=!showSortDropdown;showCatPanel=false" class="filter-btn">
              <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 6h18M6 12h12M9 18h6"/></svg>
              {{ sortBy }}
            </button>
            <div v-if="showSortDropdown" class="dropdown-panel">
              <button v-for="s in sortOptions" :key="s" @click.stop="sortBy=s;showSortDropdown=false" :class="['dropdown-item', {active: sortBy===s}]">{{ s }}</button>
            </div>
          </div>
        </div>
        <p style="font-size:0.82rem;color:#9ca3af;font-weight:600;margin:0;white-space:nowrap;">
          <strong style="color:#1A1A1A;">{{ filtered.length }}</strong> services
        </p>
      </div>
    </div>

    <div class="section-inner" style="padding-top:28px;padding-bottom:0;">
      <h2 class="section-title">{{ activeCategory === 'All' ? 'All Services' : activeCategory }}</h2>
      <p class="section-sub" style="margin-top:4px;">Browse every service listed on Swapy</p>
    </div>

    <div class="section-inner" style="padding-top:20px;padding-bottom:72px;">

      <!-- Skeleton -->
      <div v-if="skeletonLoading" class="main-grid">
        <div v-for="n in 6" :key="n" class="skeleton-card">
          <div class="skeleton" style="aspect-ratio:16/10;"></div>
          <div style="padding:16px 18px;">
            <div class="skeleton" style="height:14px;width:70%;border-radius:4px;margin-bottom:8px;"></div>
            <div class="skeleton" style="height:11px;width:50%;border-radius:4px;margin-bottom:12px;"></div>
            <div class="skeleton" style="height:36px;border-radius:12px;"></div>
          </div>
        </div>
      </div>

      <!-- Empty -->
      <div v-else-if="filtered.length === 0" style="text-align:center;padding:80px 0;">
        <div style="font-size:3rem;margin-bottom:16px;">🛠️</div>
        <h3 style="font-size:1.125rem;font-weight:800;color:#1A1A1A;margin-bottom:8px;">No services found</h3>
        <p style="font-size:0.875rem;color:#9ca3af;margin-bottom:20px;">Try a different category or disable the match filter.</p>
        <button @click="activeCategory='All';search=''" class="clear-btn">Clear Filters</button>
      </div>

      <!-- Grid -->
      <div v-else class="main-grid">
        <ServiceCard v-for="svc in filtered" :key="svc.id"
          :svc="svc" :saved="bookmarked.has(svc.id)" @toggle-save="toggleBookmark(svc.id)" />
      </div>

      <!-- Load more -->
      <div v-if="!skeletonLoading && filtered.length > 0" style="text-align:center;margin-top:48px;">
        <button @click="loadMore" class="load-more-btn">
          <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" d="M19 9l-7 7-7-7"/></svg>
          Load More
        </button>
        <p style="margin-top:12px;font-size:0.78rem;color:#9ca3af;">Showing {{ filtered.length }} services</p>
      </div>

    </div>
  </section>

</div>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700;800;900&display=swap');
@keyframes shimmer { 0%{background-position:-600px 0} 100%{background-position:600px 0} }

/* ── Layout ── */
.scroll-section { padding: 0 0 48px; background: #fff; }
.section-inner  { max-width: 1680px; margin: 0 auto; padding: 0 40px; }
.section-header { display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 18px; }
.section-title  { font-size: clamp(1.1rem, 2vw, 1.5rem); font-weight: 900; color: #1A1A1A; margin: 0; letter-spacing: -.02em; }
.section-sub    { font-size: 0.78rem; color: #9ca3af; font-weight: 500; margin: 3px 0 0; }
.see-all        { font-size: 0.8rem; font-weight: 700; color: #ED730C; text-decoration: none; white-space: nowrap; margin-top: 4px; }
.see-all:hover  { text-decoration: underline; }

/* ── Horizontal scroll ── */
.hscroll {
  display: grid;
  grid-auto-flow: column;
  grid-auto-columns: calc((100% - 22px * 4) / 5);
  gap: 22px;
  overflow-x: auto;
  padding-bottom: 10px;
  -webkit-overflow-scrolling: touch;
}
.hscroll::-webkit-scrollbar { display: none; }
.hscroll { scrollbar-width: none; }
.hscroll-card { min-width: 0; }

/* ── Main grid ── */
.main-grid { display: grid; gap: 22px; }

/* ── Skeleton ── */
.skeleton { background: linear-gradient(90deg, #f3f4f6 25%, #e9eaec 50%, #f3f4f6 75%); background-size: 1200px 100%; animation: shimmer 1.4s ease-in-out infinite; }
.skeleton-card { background: #fff; border-radius: 20px; overflow: hidden; border: 1px solid #f3f4f6; }

/* ── Filter bar ── */
.filter-bar { background: #fff; border-bottom: 1px solid #EBEBEB; padding: 12px 40px; }
.filter-bar-inner { max-width: 1680px; margin: 0 auto; display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap; }
.filter-left { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
.filter-btn { display: flex; align-items: center; gap: 7px; padding: 8px 16px; border-radius: 999px; border: 1.5px solid #e2ddd8; background: #fff; font-family: 'DM Sans', sans-serif; font-size: 0.82rem; font-weight: 700; color: #1A1A1A; cursor: pointer; transition: border-color 0.15s; }
.filter-btn:hover { border-color: #1A1A1A; }

/* ── Dropdown ── */
.dropdown-panel { position: absolute; top: calc(100% + 8px); left: 0; background: #fff; border: 1px solid #EBEBEB; border-radius: 16px; padding: 6px; min-width: 220px; box-shadow: 0 8px 32px rgba(0,0,0,0.10); z-index: 100; }
.dropdown-item { display: block; width: 100%; text-align: left; padding: 9px 14px; border-radius: 10px; border: none; background: transparent; color: #1A1A1A; font-size: 0.85rem; font-weight: 500; font-family: 'DM Sans', sans-serif; cursor: pointer; transition: background 0.1s; }
.dropdown-item:hover { background: #f9f9f9; }
.dropdown-item.active { background: #fff4ec; color: #ED730C; font-weight: 700; }

/* ── Misc ── */
.clear-btn { background: #ED730C; color: #fff; border: none; border-radius: 999px; padding: 11px 28px; font-size: 0.85rem; font-weight: 700; cursor: pointer; font-family: 'DM Sans', sans-serif; }
.load-more-btn { display: inline-flex; align-items: center; gap: 8px; padding: 13px 36px; background: #fff; border: 1.5px solid #1A1A1A; border-radius: 999px; font-size: 0.82rem; font-weight: 800; color: #1A1A1A; cursor: pointer; font-family: 'DM Sans', sans-serif; letter-spacing: .04em; transition: background 0.15s, color 0.15s; }
.load-more-btn:hover { background: #1A1A1A; color: #fff; }

/* ── Responsive ── */
@media (max-width: 480px) {
  .section-inner { padding: 0 16px; }
  .filter-bar    { padding: 12px 16px; }
  .hscroll       { grid-auto-columns: calc((100% - 22px) / 1.2); }
  .main-grid     { grid-template-columns: 1fr; }
}
@media (min-width: 481px) and (max-width: 768px) {
  .section-inner { padding: 0 24px; }
  .filter-bar    { padding: 12px 24px; }
  .hscroll       { grid-auto-columns: calc((100% - 22px * 2) / 2.2); }
  .main-grid     { grid-template-columns: repeat(2, 1fr); }
}
@media (min-width: 769px) and (max-width: 1024px) {
  .section-inner { padding: 0 32px; }
  .filter-bar    { padding: 12px 32px; }
  .hscroll       { grid-auto-columns: calc((100% - 22px * 2) / 3); }
  .main-grid     { grid-template-columns: repeat(3, 1fr); }
}
@media (min-width: 1025px) and (max-width: 1439px) {
  .section-inner { padding: 0 40px; }
  .filter-bar    { padding: 12px 40px; }
  .hscroll       { grid-auto-columns: calc((100% - 22px * 3) / 4); }
  .main-grid     { grid-template-columns: repeat(4, 1fr); }
}
@media (min-width: 1440px) {
  .section-inner { padding: 0 80px; }
  .filter-bar    { padding: 12px 80px; }
  .hscroll       { grid-auto-columns: calc((100% - 22px * 4) / 5); }
  .main-grid     { grid-template-columns: repeat(5, 1fr); }
}

/* ── Sticky / hero search responsive ── */
.sticky-search-mobile { display: none; }
@media (max-width: 767px) {
  .sticky-search-desktop { display: none !important; }
  .sticky-search-mobile  { display: flex !important; }
}
.hero-search-wrap { max-width: 760px; margin: 0 auto 20px; }
.hero-search-desktop { background: #fff; border-radius: 999px; display: flex; align-items: center; padding: 6px 6px 6px 20px; box-shadow: 0 8px 32px rgba(0,0,0,0.12); border: 1.5px solid #EBEBEB; }
.hero-search-mobile  { display: none; background: #fff; border-radius: 999px; align-items: center; gap: 10px; padding: 10px 8px 10px 18px; box-shadow: 0 8px 32px rgba(0,0,0,0.12); border: 1.5px solid #EBEBEB; }
@media (max-width: 767px) {
  .hero-search-desktop { display: none !important; }
  .hero-search-mobile  { display: flex !important; }
}
</style>
