<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { ITEM_CATEGORIES } from '../../constants/categories'

// ref for the hero search bar element
const heroSearchEl = ref(null)

const el       = document.getElementById('items-app')
const allItems = ref(JSON.parse(el?.dataset.listings || '[]'))

function parseDataset(key) {
  try { return JSON.parse(el?.dataset[key] || '[]') } catch { return [] }
}

// ── sticky search on scroll ────────────────────────────────────────────────────
const scrollY = ref(0)
function onScroll() {
  scrollY.value = window.scrollY
  const slot = document.getElementById('nav-sticky-search')
  if (!slot) return
  // Detect the actual bottom edge of the hero search bar; fall back to 280
  let threshold = 280
  if (heroSearchEl.value) {
    threshold = heroSearchEl.value.offsetTop + heroSearchEl.value.offsetHeight - 60
  }
  // Hysteresis: open past the hero, close only well before it — no flicker
  const isOpen = slot.classList.contains('open')
  if (!isOpen && scrollY.value > threshold + 40) slot.classList.add('open')
  else if (isOpen && scrollY.value < threshold - 40) slot.classList.remove('open')
}
onMounted(() => window.addEventListener('scroll', onScroll, { passive: true }))
onUnmounted(() => window.removeEventListener('scroll', onScroll))

// ── Nominatim geolocation (OpenStreetMap) ─────────────────────────────────────
const cityName   = ref('')
const areaName   = ref('')
const nearbyCity = ref('General Santos')
const userLat    = ref(null)
const userLng    = ref(null)

onMounted(async () => {
  try {
    const pos = await new Promise((res, rej) =>
      navigator.geolocation.getCurrentPosition(res, rej, { timeout: 5000 })
    )
    userLat.value = pos.coords.latitude
    userLng.value = pos.coords.longitude
    const r = await fetch(
      `https://nominatim.openstreetmap.org/reverse?lat=${userLat.value}&lon=${userLng.value}&format=json`,
      { headers: { 'Accept-Language': 'en' } }
    )
    const data = await r.json()
    const addr = data.address || {}
    cityName.value = addr.city || addr.town || addr.municipality || ''
    areaName.value = addr.suburb || addr.neighbourhood || addr.quarter || addr.village || cityName.value
  } catch {
    // geolocation denied — sections fall back to recent listings
  }
})

// ── filters ────────────────────────────────────────────────────────────────────
const search        = ref('')
const searchInput   = ref('')
const locationInput = ref('')
const activeTab     = ref('All')
const valueMax    = ref(5000)
const sortBy      = ref('Recent First')
const viewMode    = ref('grid')

const showFiltersPanel = ref(false)
const showSortDropdown = ref(false)

const categories  = ['All', ...ITEM_CATEGORIES]
const sortOptions = ['Recent First', 'Value: Low to High', 'Value: High to Low', 'Best Match']

function closeAllPanels() {
  showFiltersPanel.value = false
  showSortDropdown.value = false
}

// ── section data pools — all sliced from real listings ────────────────────────
const featuredItems = ref(parseDataset('featured'))

function milesBetween(lat1, lng1, lat2, lng2) {
  const toRad = d => (d * Math.PI) / 180
  const R = 3958.8 // miles
  const a = Math.sin(toRad(lat2 - lat1) / 2) ** 2 +
            Math.cos(toRad(lat1)) * Math.cos(toRad(lat2)) * Math.sin(toRad(lng2 - lng1) / 2) ** 2
  return 2 * R * Math.asin(Math.sqrt(a))
}

function inCity(item, city) {
  return city && (item.location || '').toLowerCase().includes(city.toLowerCase())
}

// Popular in [city]: best match scores in the user's city; falls back to
// the whole pool until geolocation resolves a city name.
const popularCityItems = computed(() => {
  const local = allItems.value.filter(i => inCity(i, cityName.value))
  const pool  = local.length >= 3 ? local : allItems.value
  return [...pool].sort((a, b) => (b.match || 0) - (a.match || 0)).slice(0, 8)
})

// Available in [nearby city]: only real listings located there.
const nearbyCityItems = computed(() =>
  allItems.value.filter(i => inCity(i, nearbyCity.value)).slice(0, 8)
)

// Swaps near [area]: closest first once the user's position is known.
const nearAreaItems = computed(() => {
  if (userLat.value == null) return allItems.value.slice(0, 8)
  return allItems.value
    .filter(i => i.latitude && i.longitude)
    .map(i => ({ ...i, distance: milesBetween(userLat.value, userLng.value, i.latitude, i.longitude).toFixed(1) }))
    .sort((a, b) => a.distance - b.distance)
    .slice(0, 8)
})

// ── main all-items grid ────────────────────────────────────────────────────────
const total    = Number(el?.dataset.total || 0)
const mainGrid = computed(() => allItems.value)

// ── computed ───────────────────────────────────────────────────────────────────
const filtered = computed(() => {
  let list = [...mainGrid.value]
  if (activeTab.value !== 'All') list = list.filter(i => i.category === activeTab.value)
  if (search.value.trim())       list = list.filter(i => i.title?.toLowerCase().includes(search.value.toLowerCase()))
  list = list.filter(i => (i.value || 0) <= valueMax.value)
  if (sortBy.value === 'Value: Low to High') list = list.sort((a,b) => a.value - b.value)
  if (sortBy.value === 'Value: High to Low') list = list.sort((a,b) => b.value - a.value)
  return list
})

// ── wishlist ───────────────────────────────────────────────────────────────────
const wishlisted = ref(new Set())
function toggleWish(id) {
  const s = new Set(wishlisted.value)
  s.has(id) ? s.delete(id) : s.add(id)
  wishlisted.value = s
}

function doSearch() { search.value = searchInput.value }
</script>

<template>
<div style="min-height:100vh;background:#fff;font-family:'DM Sans',sans-serif;" @click="closeAllPanels">

  <!-- ═══════════════════════════════════════════
       STICKY NAV SEARCH
  ═══════════════════════════════════════════ -->
  <Teleport to="#nav-sticky-search">
    <div style="max-width:760px;margin:0 auto;" @click.stop>
      <!-- Desktop: full pill with category + location -->
      <div class="sticky-search-desktop" style="background:#fff;border-radius:999px;display:flex;align-items:center;padding:6px 6px 6px 20px;box-shadow:0 8px 32px rgba(0,0,0,0.12);border:1.5px solid #EBEBEB;">
        <div style="flex:1;display:flex;align-items:center;gap:8px;border-right:1px solid #EBEBEB;padding-right:16px;">
          <svg width="15" height="15" fill="none" stroke="#9ca3af" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
          <input v-model="searchInput" type="text" placeholder="What are you looking for?"
            style="border:none;outline:none;font-size:0.875rem;color:#1A1A1A;background:transparent;font-family:'DM Sans',sans-serif;width:100%;"
            @keydown.enter="doSearch">
        </div>
        <div style="display:flex;align-items:center;gap:6px;padding:0 14px;">
          <svg width="13" height="13" fill="#9ca3af" viewBox="0 0 24 24"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>
          <input v-model="locationInput" type="text" placeholder="Location" style="border:none;outline:none;font-size:0.875rem;color:#1A1A1A;background:transparent;font-family:'DM Sans',sans-serif;width:90px;">
        </div>
        <button @click="doSearch"
          style="background:#ED730C;color:#fff;border:none;border-radius:999px;padding:12px 28px;font-size:0.875rem;font-weight:800;cursor:pointer;font-family:'DM Sans',sans-serif;white-space:nowrap;box-shadow:0 4px 14px rgba(237,115,12,0.4);"
          onmouseover="this.style.background='#d4620a'" onmouseout="this.style.background='#ED730C'">Search</button>
      </div>
      <!-- Mobile: query + location + button -->
      <div class="sticky-search-mobile" style="background:#fff;border-radius:999px;align-items:center;padding:5px 5px 5px 16px;box-shadow:0 4px 20px rgba(0,0,0,0.10);border:1.5px solid #EBEBEB;">
        <svg width="14" height="14" fill="none" stroke="#9ca3af" stroke-width="2" viewBox="0 0 24 24" style="flex-shrink:0;margin-right:8px;"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
        <input v-model="searchInput" type="text" placeholder="Search"
          style="flex:1;border:none;outline:none;font-size:0.875rem;color:#1A1A1A;background:transparent;font-family:'DM Sans',sans-serif;min-width:0;"
          @keydown.enter="doSearch">
        <div style="display:flex;align-items:center;gap:4px;padding:0 10px;border-left:1px solid #EBEBEB;flex-shrink:0;">
          <svg width="12" height="12" fill="#9ca3af" viewBox="0 0 24 24"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>
          <input v-model="locationInput" type="text" placeholder="Location" style="border:none;outline:none;font-size:0.82rem;color:#1A1A1A;background:transparent;font-family:'DM Sans',sans-serif;width:70px;">
        </div>
        <button @click="doSearch"
          style="background:#ED730C;color:#fff;border:none;border-radius:999px;padding:9px 18px;font-size:0.82rem;font-weight:800;cursor:pointer;font-family:'DM Sans',sans-serif;white-space:nowrap;flex-shrink:0;">Search</button>
      </div>
    </div>
  </Teleport>

  <!-- ═══════════════════════════════════════════
       HERO
  ═══════════════════════════════════════════ -->
  <section style="padding:52px 24px 40px;background:#fff;text-align:center;">
    <div style="max-width:860px;margin:0 auto;">
      <div style="display:flex;align-items:baseline;gap:16px;flex-wrap:wrap;margin-bottom:28px;justify-content:center;">
        <h1 style="font-size:clamp(2.2rem,5vw,3.6rem);font-weight:900;line-height:1.05;letter-spacing:-.03em;color:#1A1A1A;margin:0;">Your stuff.</h1>
        <h1 style="font-size:clamp(2.2rem,5vw,3.6rem);font-weight:900;line-height:1.05;letter-spacing:-.03em;color:#ED730C;margin:0;">Your terms.</h1>
      </div>
      <!-- Hero search: full pill on desktop, simple input on mobile -->
      <div ref="heroSearchEl" class="hero-search-wrap">
        <!-- Desktop pill -->
        <div class="hero-search-desktop">
          <div style="flex:1;display:flex;align-items:center;gap:8px;border-right:1px solid #EBEBEB;padding-right:16px;">
            <svg width="15" height="15" fill="none" stroke="#9ca3af" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
            <input v-model="searchInput" type="text" placeholder="What are you looking for?"
              style="border:none;outline:none;font-size:0.875rem;color:#1A1A1A;background:transparent;font-family:'DM Sans',sans-serif;width:100%;"
              @keydown.enter="doSearch">
          </div>
          <div style="display:flex;align-items:center;gap:6px;padding:0 14px;">
            <svg width="13" height="13" fill="#9ca3af" viewBox="0 0 24 24"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>
            <input v-model="locationInput" type="text" placeholder="Location" style="border:none;outline:none;font-size:0.875rem;color:#1A1A1A;background:transparent;font-family:'DM Sans',sans-serif;width:110px;">
          </div>
          <button @click="doSearch"
            style="background:#ED730C;color:#fff;border:none;border-radius:999px;padding:12px 28px;font-size:0.875rem;font-weight:800;cursor:pointer;font-family:'DM Sans',sans-serif;white-space:nowrap;box-shadow:0 4px 14px rgba(237,115,12,0.4);"
            onmouseover="this.style.background='#d4620a'" onmouseout="this.style.background='#ED730C'">Search</button>
        </div>
        <!-- Mobile pill: query + location + button -->
        <div class="hero-search-mobile">
          <svg width="15" height="15" fill="none" stroke="#9ca3af" stroke-width="2" viewBox="0 0 24 24" style="flex-shrink:0;"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
          <input v-model="searchInput" type="text" placeholder="Search"
            style="flex:1;border:none;outline:none;font-size:0.875rem;color:#1A1A1A;background:transparent;font-family:'DM Sans',sans-serif;min-width:0;"
            @keydown.enter="doSearch">
          <div style="display:flex;align-items:center;gap:4px;padding:0 10px;border-left:1px solid #EBEBEB;flex-shrink:0;">
            <svg width="12" height="12" fill="#9ca3af" viewBox="0 0 24 24"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>
            <input v-model="locationInput" type="text" placeholder="Location" style="border:none;outline:none;font-size:0.82rem;color:#1A1A1A;background:transparent;font-family:'DM Sans',sans-serif;width:70px;">
          </div>
          <button @click="doSearch"
            style="background:#ED730C;color:#fff;border:none;border-radius:999px;padding:10px 18px;font-size:0.82rem;font-weight:800;cursor:pointer;font-family:'DM Sans',sans-serif;white-space:nowrap;flex-shrink:0;">Search</button>
        </div>
      </div>
      <div style="display:flex;gap:8px;flex-wrap:wrap;justify-content:center;">
        <button v-for="cat in categories.slice(0,8)" :key="cat" @click="activeTab = cat"
          :style="{padding:'7px 16px',borderRadius:'999px',fontSize:'0.78rem',fontWeight:'600',fontFamily:'\'DM Sans\',sans-serif',cursor:'pointer',border:activeTab===cat?'none':'1px solid #EBEBEB',background:activeTab===cat?'#1A1A1A':'#fff',color:activeTab===cat?'#fff':'#4b5563',boxShadow:activeTab===cat?'0 4px 12px rgba(0,0,0,0.15)':'none',transition:'all 0.15s'}">
          {{ cat }}
        </button>
      </div>
    </div>
  </section>

  <!-- ───────────────────────────────────────────
       1. TOP FEATURED SWAPS
  ─────────────────────────────────────────── -->
  <section v-if="featuredItems.length" class="scroll-section">
    <div class="section-inner">
      <div class="section-header">
        <div>
          <h2 class="section-title">Top Featured Swaps</h2>
          <p class="section-sub">Promoted listings from our community</p>
        </div>
        <a href="/items/section/featured" class="see-all">See all →</a>
      </div>
      <div class="hscroll">
        <div v-for="item in featuredItems" :key="item.id" class="swapy-card hscroll-card">
          <a :href="'/item/'+item.id" class="card-link">
            <div class="card-img-wrap">
              <img :src="item.image" :alt="item.title" class="card-img">
              <div class="pills-row">
                <span class="badge-pill">
                  <svg width="9" height="9" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" style="display:inline-block;vertical-align:middle;margin-right:3px;"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
                  Featured
                </span>
                <span v-if="item.match != null" class="match-pill">{{ item.match }}% Match</span>
              </div>
              <button @click.stop="toggleWish(item.id)" class="wish-btn">
                <svg :class="['wish-icon', {wishlisted: wishlisted.has(item.id)}]" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
              </button>
            </div>
            <div class="card-body">
              <h3 class="card-title">{{ item.title }}</h3>
              <p class="card-wants">Wants: <span class="wants-value">{{ item.wants }}</span></p>
              <div class="card-value-row">
                <span class="card-value-label">Est. Value</span>
                <p class="card-value">${{ item.value?.toLocaleString() }}</p>
              </div>
            </div>
          </a>
        </div>
      </div>
    </div>
  </section>

  <!-- ───────────────────────────────────────────
       2. POPULAR IN [CITY]
  ─────────────────────────────────────────── -->
  <section v-if="popularCityItems.length" class="scroll-section">
    <div class="section-inner">
      <div class="section-header">
        <div>
          <h2 class="section-title">Popular in {{ cityName || 'Your City' }}</h2>
          <p class="section-sub">Most viewed &amp; matched swaps in your city</p>
        </div>
        <a href="/items/section/popular" class="see-all">See all →</a>
      </div>
      <div class="hscroll">
        <div v-for="item in popularCityItems" :key="item.id" class="swapy-card hscroll-card">
          <a :href="'/item/'+item.id" class="card-link">
            <div class="card-img-wrap">
              <img :src="item.image" :alt="item.title" class="card-img">
              <span v-if="item.match != null" class="match-pill match-pill--solo">{{ item.match }}% Match</span>
              <button @click.stop="toggleWish(item.id)" class="wish-btn">
                <svg :class="['wish-icon', {wishlisted: wishlisted.has(item.id)}]" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
              </button>
            </div>
            <div class="card-body">
              <h3 class="card-title">{{ item.title }}</h3>
              <p class="card-wants">Wants: <span class="wants-value">{{ item.wants }}</span></p>
              <div class="card-value-row">
                <span class="card-value-label">Est. Value</span>
                <p class="card-value">${{ item.value?.toLocaleString() }}</p>
              </div>
            </div>
          </a>
        </div>
      </div>
    </div>
  </section>

  <!-- ───────────────────────────────────────────
       3. AVAILABLE IN [NEARBY CITY]
       — only renders when nearbyCity is resolved
       — and it's different from the user's city
  ─────────────────────────────────────────── -->
  <section v-if="nearbyCity && nearbyCity !== cityName && nearbyCityItems.length" class="scroll-section">
    <div class="section-inner">
      <div class="section-header">
        <div>
          <h2 class="section-title">Available in {{ nearbyCity }}</h2>
          <p class="section-sub">Active swaps from a nearby city</p>
        </div>
        <a href="/items/section/nearby" class="see-all">See all →</a>
      </div>
      <div class="hscroll">
        <div v-for="item in nearbyCityItems" :key="item.id" class="swapy-card hscroll-card">
          <a :href="'/item/'+item.id" class="card-link">
            <div class="card-img-wrap">
              <img :src="item.image" :alt="item.title" class="card-img">
              <span v-if="item.match != null" class="match-pill match-pill--solo">{{ item.match }}% Match</span>
              <button @click.stop="toggleWish(item.id)" class="wish-btn">
                <svg :class="['wish-icon', {wishlisted: wishlisted.has(item.id)}]" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
              </button>
            </div>
            <div class="card-body">
              <h3 class="card-title">{{ item.title }}</h3>
              <p class="card-wants">Wants: <span class="wants-value">{{ item.wants }}</span></p>
              <div class="card-value-row">
                <span class="card-value-label">Est. Value</span>
                <p class="card-value">${{ item.value?.toLocaleString() }}</p>
              </div>
            </div>
          </a>
        </div>
      </div>
    </div>
  </section>

  <!-- ───────────────────────────────────────────
       4. SWAPS NEAR [AREA]
  ─────────────────────────────────────────── -->
  <section v-if="nearAreaItems.length" class="scroll-section">
    <div class="section-inner">
      <div class="section-header">
        <div>
          <h2 class="section-title">Swaps near {{ areaName || 'Your Area' }}</h2>
          <p class="section-sub">Items closest to your location</p>
        </div>
        <a href="/items/section/near-you" class="see-all">See all →</a>
      </div>
      <div class="hscroll">
        <div v-for="item in nearAreaItems" :key="item.id" class="swapy-card hscroll-card">
          <a :href="'/item/'+item.id" class="card-link">
            <div class="card-img-wrap">
              <img :src="item.image" :alt="item.title" class="card-img">
              <span v-if="item.match != null" class="match-pill match-pill--solo">{{ item.match }}% Match</span>
              <button @click.stop="toggleWish(item.id)" class="wish-btn">
                <svg :class="['wish-icon', {wishlisted: wishlisted.has(item.id)}]" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
              </button>
            </div>
            <div class="card-body">
              <h3 class="card-title">{{ item.title }}</h3>
              <p class="card-wants">Wants: <span class="wants-value">{{ item.wants }}</span></p>
              <div class="card-value-row">
                <span class="card-value-label">Est. Value</span>
                <p class="card-value">${{ item.value?.toLocaleString() }}</p>
              </div>
            </div>
          </a>
        </div>
      </div>
    </div>
  </section>

  <!-- ───────────────────────────────────────────
       5. ALL ITEMS
  ─────────────────────────────────────────── -->
  <section style="background:#fff;">

    <div class="filter-bar" @click.stop>
      <div class="filter-bar-inner">
        <div class="filter-left">
          <div style="position:relative;">
            <button @click.stop="showFiltersPanel=!showFiltersPanel;showSortDropdown=false" class="filter-btn">
              <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 4h13M3 8h9m-9 4h9m5-4v12m0 0l-4-4m4 4l4-4"/></svg>
              {{ activeTab === 'All' ? 'All Categories' : activeTab }}
            </button>
            <div v-if="showFiltersPanel" class="dropdown-panel">
              <button v-for="cat in categories" :key="cat"
                @click.stop="activeTab=cat;showFiltersPanel=false"
                :class="['dropdown-item', {active: activeTab===cat}]">{{ cat }}</button>
            </div>
          </div>
          <div class="filter-btn" style="gap:8px;">
            <span style="font-size:0.82rem;font-weight:600;color:#6b7280;white-space:nowrap;">Up to ${{ valueMax.toLocaleString() }}</span>
            <input type="range" v-model.number="valueMax" min="50" max="5000" step="50"
              style="width:80px;accent-color:#ED730C;cursor:pointer;">
          </div>
          <div style="position:relative;">
            <button @click.stop="showSortDropdown=!showSortDropdown;showFiltersPanel=false" class="filter-btn">
              <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12"/></svg>
              {{ sortBy }}
            </button>
            <div v-if="showSortDropdown" class="dropdown-panel">
              <button v-for="s in sortOptions" :key="s"
                @click.stop="sortBy=s;showSortDropdown=false"
                :class="['dropdown-item', {active: sortBy===s}]">{{ s }}</button>
            </div>
          </div>
        </div>
        <div style="display:flex;align-items:center;gap:14px;">
          <p style="font-size:0.82rem;color:#9ca3af;font-weight:600;margin:0;white-space:nowrap;">
            <strong style="color:#1A1A1A;">{{ filtered.length }}</strong> items
          </p>
          <div class="view-toggle">
            <button @click="viewMode='grid'" :class="['view-btn', {active: viewMode==='grid'}]">
              <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
            </button>
            <button @click="viewMode='list'" :class="['view-btn', {active: viewMode==='list'}]" style="border-left:1px solid #EBEBEB;">
              <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
          </div>
        </div>
      </div>
    </div>

    <div class="section-inner" style="padding-top:28px;padding-bottom:0;">
      <h2 class="section-title">All Items</h2>
      <p class="section-sub" style="margin-top:4px;">Browse everything on Swapy</p>
    </div>

    <div class="section-inner" style="padding-top:20px;padding-bottom:72px;">

      <div v-if="viewMode==='grid'" class="main-grid">
        <div v-for="item in filtered" :key="item.id" class="swapy-card">
          <a :href="'/item/'+item.id" class="card-link">
            <div class="card-img-wrap">
              <img :src="item.image" :alt="item.title" class="card-img">
              <span v-if="item.match != null" class="match-pill match-pill--solo">{{ item.match }}% Match</span>
              <button @click.stop="toggleWish(item.id)" class="wish-btn">
                <svg :class="['wish-icon', {wishlisted: wishlisted.has(item.id)}]" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
              </button>
            </div>
            <div class="card-body">
              <h3 class="card-title">{{ item.title }}</h3>
              <p class="card-wants">Wants: <span class="wants-value">{{ item.wants }}</span></p>
              <div class="card-value-row">
                <span class="card-value-label">Est. Value</span>
                <p class="card-value">${{ item.value?.toLocaleString() }}</p>
              </div>
            </div>
          </a>
        </div>
      </div>

      <div v-else style="display:flex;flex-direction:column;gap:10px;">
        <a v-for="item in filtered" :key="item.id" :href="'/item/'+item.id" class="swapy-card list-card">
          <div class="list-img">
            <img :src="item.image" :alt="item.title" class="card-img">
            <span v-if="item.match != null" class="match-pill match-pill--solo">{{ item.match }}% Match</span>
          </div>
          <div class="list-body">
            <div>
              <p class="list-meta">{{ item.category }} · {{ item.condition }}</p>
              <h3 class="list-title">{{ item.title }}</h3>
              <p class="card-wants" style="margin-top:2px;">Wants: <span class="wants-value">{{ item.wants }}</span></p>
            </div>
            <p class="list-value">${{ item.value?.toLocaleString() }}</p>
          </div>
        </a>
      </div>

      <div v-if="filtered.length === 0" style="text-align:center;padding:80px 0;">
        <div style="margin-bottom:16px;display:flex;justify-content:center;">
          <svg width="52" height="52" fill="none" stroke="#d1d5db" stroke-width="1.5" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path stroke-linecap="round" d="M21 21l-4.35-4.35"/></svg>
        </div>
        <h3 style="font-size:1.125rem;font-weight:700;color:#1A1A1A;margin-bottom:8px;">No items found</h3>
        <p style="font-size:0.875rem;color:#9ca3af;margin-bottom:20px;">Try adjusting your filters or search term.</p>
        <button @click="activeTab='All';valueMax=5000;search=''" class="clear-btn">Clear Filters</button>
      </div>

      <div v-if="filtered.length > 0" style="text-align:center;margin-top:48px;">
        <p style="margin-top:12px;font-size:0.78rem;color:#9ca3af;">Showing {{ filtered.length }} of {{ total }} swaps</p>
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

/* ── Horizontal scroll row ── */
.hscroll {
  display: grid;
  grid-auto-flow: column;
  grid-auto-columns: calc((100% - 14px * 4) / 5);
  gap: 14px;
  overflow-x: auto;
  padding-bottom: 10px;
  -webkit-overflow-scrolling: touch;
}
.hscroll::-webkit-scrollbar { display: none; }
.hscroll { scrollbar-width: none; }
.hscroll-card { min-width: 0; }

/* ── Card shell ── */
.swapy-card {
  background: #fff;
  border-radius: 16px;
  overflow: hidden;
  border: 1px solid #EBEBEB;
  cursor: pointer;
  transition: box-shadow 0.2s, transform 0.2s;
}
.swapy-card:hover {
  box-shadow: 0 8px 28px rgba(0,0,0,0.10);
  transform: translateY(-2px);
}

/* ── Card link wrapper ── */
.card-link {
  display: flex;
  flex-direction: column;
  height: 100%;
  text-decoration: none;
  color: inherit;
}

/* ── Card image ── */
.card-img-wrap {
  position: relative;
  overflow: hidden;
  background: #f3f4f6;
  aspect-ratio: 4 / 3;
}
.card-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
  transition: transform 0.4s ease;
}
.swapy-card:hover .card-img { transform: scale(1.04); }

/* ── Card body ── */
.card-body {
  padding: 12px 14px 14px;
  display: grid;
  grid-template-rows: 2.43rem 1.4rem auto;
  flex: 1;
}
.card-title {
  font-size: 0.88rem;
  font-weight: 700;
  color: #1A1A1A;
  line-height: 1.35;
  margin: 0;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
  align-self: center;
}
.card-wants {
  font-size: 0.75rem;
  color: #9ca3af;
  margin: 0;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  align-self: center;
}
.wants-value {
  color: #149189;
  font-weight: 600;
}
.card-value-row {
  padding-top: 8px;
  margin-top: 4px;
  border-top: 1px solid #f3f4f6;
}
.card-value-label {
  display: block;
  font-size: 0.58rem;
  font-weight: 700;
  color: #9ca3af;
  text-transform: uppercase;
  letter-spacing: .06em;
  margin: 0 0 2px;
}
.card-value {
  font-size: 0.95rem;
  font-weight: 800;
  color: #1A1A1A;
  margin: 0;
}

/* ── Pills ── */
.pills-row {
  position: absolute;
  top: 10px; left: 10px;
  display: flex;
  gap: 5px;
  align-items: center;
}
.badge-pill {
  background: #ED730C;
  color: #fff;
  font-size: 0.62rem;
  font-weight: 800;
  padding: 5px 10px;
  border-radius: 999px;
  letter-spacing: .04em;
  font-family: 'DM Sans', sans-serif;
  white-space: nowrap;
}
.match-pill {
  background: rgba(26,26,26,0.75);
  color: #fff;
  font-size: 0.62rem;
  font-weight: 800;
  padding: 5px 10px;
  border-radius: 999px;
  letter-spacing: .04em;
  font-family: 'DM Sans', sans-serif;
  white-space: nowrap;
  backdrop-filter: blur(4px);
}
.match-pill--solo {
  position: absolute;
  top: 10px; left: 10px;
  background: rgba(26,26,26,0.75);
  backdrop-filter: blur(4px);
}

/* ── Wishlist heart ── */
.wish-btn {
  position: absolute;
  top: 9px; right: 9px;
  width: 30px; height: 30px;
  background: rgba(255,255,255,0.92);
  border: none;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  box-shadow: 0 2px 8px rgba(0,0,0,0.10);
  transition: transform 0.15s;
  z-index: 1;
}
.wish-btn:hover { transform: scale(1.15); }
.wish-icon {
  width: 13px; height: 13px;
  fill: none;
  stroke: #6b7280;
  stroke-width: 2;
  transition: fill 0.15s, stroke 0.15s;
}
.wish-icon.wishlisted { fill: #ED730C; stroke: #ED730C; }

/* ── Main grid ── */
.main-grid {
  display: grid;
  gap: 16px;
}

/* ── Skeleton ── */
.skeleton {
  background: linear-gradient(90deg, #f3f4f6 25%, #e9eaec 50%, #f3f4f6 75%);
  background-size: 1200px 100%;
  animation: shimmer 1.4s ease-in-out infinite;
}
.skeleton-card {
  background: #fff;
  border-radius: 16px;
  overflow: hidden;
  border: 1px solid #EBEBEB;
}

/* ── Filter bar ── */
.filter-bar { background: #fff; border-bottom: 1px solid #EBEBEB; padding: 12px 40px; }
.filter-bar-inner {
  max-width: 1680px;
  margin: 0 auto;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
  flex-wrap: wrap;
}
.filter-left { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
.filter-btn {
  display: flex; align-items: center; gap: 7px;
  padding: 8px 16px; border-radius: 999px;
  border: 1.5px solid #e2ddd8; background: #fff;
  font-family: 'DM Sans', sans-serif; font-size: 0.82rem; font-weight: 700;
  color: #1A1A1A; cursor: pointer; transition: border-color 0.15s;
}
.filter-btn:hover { border-color: #1A1A1A; }

/* ── Dropdown ── */
.dropdown-panel {
  position: absolute; top: calc(100% + 8px); left: 0;
  background: #fff; border: 1px solid #EBEBEB; border-radius: 16px;
  padding: 6px; min-width: 200px;
  box-shadow: 0 8px 32px rgba(0,0,0,0.10); z-index: 100;
}
.dropdown-item {
  display: block; width: 100%; text-align: left;
  padding: 9px 14px; border-radius: 10px; border: none;
  background: transparent; color: #1A1A1A; font-size: 0.85rem;
  font-weight: 500; font-family: 'DM Sans', sans-serif;
  cursor: pointer; transition: background 0.1s;
}
.dropdown-item:hover { background: #f9f9f9; }
.dropdown-item.active { background: #fff4ec; color: #ED730C; font-weight: 700; }

/* ── View toggle ── */
.view-toggle { display: inline-flex; border: 1.5px solid #EBEBEB; border-radius: 10px; overflow: hidden; background: #fff; }
.view-btn {
  padding: 7px 12px; background: transparent; border: none; cursor: pointer;
  display: flex; align-items: center; color: #9ca3af;
  transition: background 0.15s, color 0.15s;
}
.view-btn.active { background: #1A1A1A; color: #fff; }

/* ── List view ── */
.list-card { display: flex; align-items: center; text-decoration: none; color: inherit; border-radius: 14px; }
.list-img { position: relative; width: 100px; height: 80px; flex-shrink: 0; overflow: hidden; background: #f3f4f6; }
.list-body { flex: 1; padding: 12px 18px; display: flex; justify-content: space-between; align-items: center; gap: 16px; }
.list-meta  { font-size: 0.62rem; font-weight: 800; letter-spacing: .07em; color: #9ca3af; text-transform: uppercase; margin: 0 0 3px; }
.list-title { font-size: 0.9rem; font-weight: 800; color: #1A1A1A; margin: 0 0 2px; }
.list-value { font-size: 1rem; font-weight: 900; color: #1A1A1A; margin: 0; white-space: nowrap; }

/* ── Misc ── */
.clear-btn {
  background: #ED730C; color: #fff; border: none; border-radius: 999px;
  padding: 11px 28px; font-size: 0.85rem; font-weight: 700; cursor: pointer; font-family: 'DM Sans', sans-serif;
}
.load-more-btn {
  display: inline-flex; align-items: center; gap: 8px;
  padding: 13px 36px; background: #fff; border: 1.5px solid #1A1A1A;
  border-radius: 999px; font-size: 0.82rem; font-weight: 800; color: #1A1A1A;
  cursor: pointer; font-family: 'DM Sans', sans-serif; letter-spacing: .04em;
  transition: background 0.15s, color 0.15s;
}
.load-more-btn:hover { background: #1A1A1A; color: #fff; }

/* ── Responsive ── */
@media (max-width: 480px) {
  .section-inner  { padding: 0 16px; }
  .filter-bar     { padding: 12px 16px; }
  .hscroll        { grid-auto-columns: calc((100% - 14px) / 2); }
  .main-grid      { grid-template-columns: repeat(2, 1fr); }
  .list-img       { width: 80px; height: 68px; }
}
@media (min-width: 481px) and (max-width: 768px) {
  .section-inner  { padding: 0 24px; }
  .filter-bar     { padding: 12px 24px; }
  .hscroll        { grid-auto-columns: calc((100% - 14px * 2) / 3); }
  .main-grid      { grid-template-columns: repeat(3, 1fr); }
}
@media (min-width: 769px) and (max-width: 1024px) {
  .section-inner  { padding: 0 32px; }
  .filter-bar     { padding: 12px 32px; }
  .hscroll        { grid-auto-columns: calc((100% - 14px * 3) / 4); }
  .main-grid      { grid-template-columns: repeat(4, 1fr); }
}
@media (min-width: 1025px) and (max-width: 1439px) {
  .section-inner  { padding: 0 40px; }
  .filter-bar     { padding: 12px 40px; }
  .hscroll        { grid-auto-columns: calc((100% - 14px * 4) / 5); }
  .main-grid      { grid-template-columns: repeat(5, 1fr); }
}
@media (min-width: 1440px) {
  .section-inner  { padding: 0 80px; }
  .filter-bar     { padding: 12px 80px; }
  .hscroll        { grid-auto-columns: calc((100% - 14px * 5) / 6); }
  .main-grid      { grid-template-columns: repeat(6, 1fr); }
}

/* ── Sticky search responsive variants ── */
.sticky-search-mobile { display: none; }
@media (max-width: 767px) {
  .sticky-search-desktop { display: none !important; }
  .sticky-search-mobile  { display: flex !important; }
}

/* ── Hero search responsive ── */
.hero-search-wrap {
  max-width: 760px;
  margin: 0 auto 20px;
}
.hero-search-desktop {
  background: #fff;
  border-radius: 999px;
  display: flex;
  align-items: center;
  padding: 6px 6px 6px 20px;
  box-shadow: 0 8px 32px rgba(0,0,0,0.12);
  border: 1.5px solid #EBEBEB;
}
.hero-search-mobile {
  display: none;
  background: #fff;
  border-radius: 999px;
  align-items: center;
  gap: 10px;
  padding: 10px 8px 10px 18px;
  box-shadow: 0 8px 32px rgba(0,0,0,0.12);
  border: 1.5px solid #EBEBEB;
}
@media (max-width: 767px) {
  .hero-search-desktop { display: none !important; }
  .hero-search-mobile  { display: flex !important; }
}
</style>