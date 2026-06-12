<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'

const el    = document.getElementById('homes-app')
const homes = ref(JSON.parse(el?.dataset.homes || '[]'))

// ── sticky search ─────────────────────────────────────────────────────────────
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
    cityName.value = addr.city || addr.town || addr.municipality || ''
    areaName.value = addr.suburb || addr.neighbourhood || addr.quarter || addr.village || cityName.value
    nearbyCity.value = 'General Santos'
  } catch { /* geolocation denied */ }
})

// ── fallback home data ────────────────────────────────────────────────────────
const fakeHomes = [
  { id:1,  is_promoted:true, type:'Swap',     title:'Modern Studio in BGC',              location:'Bonifacio Global City, Taguig',  city:'Taguig',      area:'BGC',       distance:'1.2', beds:'Studio', baths:1, sqm:32,  value:18000,   swap_terms:'Open to swap with condo in Makati or Ortigas', tags:['Furnished','Pet-friendly','High-floor'],      rating:4.9, owner:'Marco R.',   owner_initials:'MR', owner_color:'#ED730C', listed_at:'2h ago',  images:['https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?w=800&q=80'] },
  { id:2,  type:'Rent',     title:'2BR Condo with Pool View',          location:'Salcedo Village, Makati',        city:'Makati',      area:'Salcedo',   distance:'2.8', beds:'2',      baths:2, sqm:68,  value:35000,   swap_terms:null,                                           tags:['Semi-furnished','With parking','Near MRT'],   rating:4.7, owner:'Angela T.',  owner_initials:'AT', owner_color:'#14b8a6', listed_at:'Today',   images:['https://images.unsplash.com/photo-1493809842364-78817add7ffb?w=800&q=80'] },
  { id:3,  is_promoted:true, type:'Sell',     title:'Townhouse in Paranaque',            location:'BF Homes, Paranaque City',       city:'Paranaque',   area:'BF Homes',  distance:'5.4', beds:'3',      baths:2, sqm:120, value:6500000, swap_terms:'Open to partial swap + cash top-up',           tags:['With garage','Corner lot','Renovated'],       rating:5.0, owner:'Dennis L.',  owner_initials:'DL', owner_color:'#8b5cf6', listed_at:'1d ago',  images:['https://images.unsplash.com/photo-1568605114967-8130f3a36994?w=800&q=80'] },
  { id:4,  type:'Co-living',title:'Private Room in Co-living Hub',     location:'Poblacion, Makati',              city:'Makati',      area:'Poblacion', distance:'3.1', beds:'1',      baths:1, sqm:18,  value:8500,    swap_terms:'Open to skill-swap for rent offset',           tags:['All utilities','Fast WiFi','Cowork space'],   rating:4.8, owner:'Carla M.',   owner_initials:'CM', owner_color:'#f59e0b', listed_at:'3h ago',  images:['https://images.unsplash.com/photo-1555854877-bab0e564b8d5?w=800&q=80'] },
  { id:5,  type:'Swap',     title:'1BR Loft in Eastwood City',         location:'Eastwood City, Quezon City',     city:'Quezon City', area:'Eastwood',  distance:'4.7', beds:'1',      baths:1, sqm:45,  value:22000,   swap_terms:'Swap for 1BR in Pasig or Ortigas area',        tags:['Loft-style','City view','Fully furnished'],   rating:4.6, owner:'Jose P.',    owner_initials:'JP', owner_color:'#ec4899', listed_at:'5h ago',  images:['https://images.unsplash.com/photo-1502672260266-1c1ef2d93688?w=800&q=80'] },
  { id:6,  type:'Rent',     title:'Spacious 3BR House with Garden',    location:'Filinvest, Alabang',             city:'Muntinlupa',  area:'Alabang',   distance:'8.2', beds:'3',      baths:3, sqm:180, value:55000,   swap_terms:null,                                           tags:['Garden','Helper room','Quiet village'],       rating:4.9, owner:'Ruth V.',    owner_initials:'RV', owner_color:'#149189', listed_at:'2d ago',  images:['https://images.unsplash.com/photo-1570129477492-45c003edd2be?w=800&q=80'] },
  { id:7,  is_promoted:true, type:'Rent',     title:'Modern 1BR in Davao City CBD',      location:'Davao City, Davao del Sur',      city:'Davao City',  area:'CBD',       distance:'0.5', beds:'1',      baths:1, sqm:40,  value:15000,   swap_terms:null,                                           tags:['Fully furnished','Near mall','High-floor'],   rating:4.8, owner:'Liza G.',    owner_initials:'LG', owner_color:'#ED730C', listed_at:'1h ago',  images:['https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?w=800&q=80'] },
  { id:8,  type:'Swap',     title:'Cozy Studio in Davao City',         location:'Matina, Davao City',             city:'Davao City',  area:'Matina',    distance:'1.1', beds:'Studio', baths:1, sqm:28,  value:12000,   swap_terms:'Open to swap near Ateneo de Davao',            tags:['Pet-friendly','Near school','Parking'],       rating:4.9, owner:'Ramon D.',   owner_initials:'RD', owner_color:'#14b8a6', listed_at:'3h ago',  images:['https://images.unsplash.com/photo-1502672260266-1c1ef2d93688?w=800&q=80'] },
  { id:9,  type:'Sell',     title:'Family Home in Davao City',         location:'Buhangin, Davao City',           city:'Davao City',  area:'Buhangin',  distance:'2.3', beds:'3',      baths:2, sqm:110, value:4800000, swap_terms:null,                                           tags:['With garden','Quiet village','Near airport'], rating:4.7, owner:'Fe C.',      owner_initials:'FC', owner_color:'#8b5cf6', listed_at:'Today',   images:['https://images.unsplash.com/photo-1568605114967-8130f3a36994?w=800&q=80'] },
  { id:10, is_promoted:true, type:'Co-living',title:'Shared Space in Poblacion Davao',   location:'Poblacion, Davao City',          city:'Davao City',  area:'Poblacion', distance:'0.3', beds:'1',      baths:1, sqm:20,  value:7000,    swap_terms:'Open to skill-swap',                           tags:['Fast WiFi','All utilities','Cowork space'],   rating:4.9, owner:'Nadia P.',   owner_initials:'NP', owner_color:'#f59e0b', listed_at:'30m ago', images:['https://images.unsplash.com/photo-1555854877-bab0e564b8d5?w=800&q=80'] },
  { id:11, type:'Rent',     title:'2BR Unit in Poblacion District',    location:'Poblacion, Davao City',          city:'Davao City',  area:'Poblacion', distance:'0.6', beds:'2',      baths:1, sqm:55,  value:20000,   swap_terms:null,                                           tags:['Semi-furnished','City view','Near market'],   rating:4.8, owner:'Jake M.',    owner_initials:'JM', owner_color:'#ec4899', listed_at:'2h ago',  images:['https://images.unsplash.com/photo-1493809842364-78817add7ffb?w=800&q=80'] },
  { id:12, is_promoted:true, type:'Swap',     title:'Loft-style Unit near Poblacion',    location:'Poblacion, Davao City',          city:'Davao City',  area:'Poblacion', distance:'0.9', beds:'1',      baths:1, sqm:38,  value:14000,   swap_terms:'Swap for unit in Toril or Calinan area',       tags:['Loft-style','Balcony','Fully furnished'],     rating:5.0, owner:'Clara S.',   owner_initials:'CS', owner_color:'#149189', listed_at:'4h ago',  images:['https://images.unsplash.com/photo-1570129477492-45c003edd2be?w=800&q=80'] },
  { id:13, type:'Rent',     title:'Studio Apartment in Gensan',        location:'General Santos City',            city:'General Santos', area:'Downtown', distance:'0.4', beds:'Studio', baths:1, sqm:30, value:9000,    swap_terms:null,                                           tags:['Furnished','Near market','City center'],     rating:4.7, owner:'Leo T.',     owner_initials:'LT', owner_color:'#ED730C', listed_at:'Today',   images:['https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?w=800&q=80'] },
  { id:14, type:'Swap',     title:'2BR House in General Santos',       location:'Lagao, General Santos City',     city:'General Santos', area:'Lagao',    distance:'2.1', beds:'2',      baths:1, sqm:75,  value:18000,   swap_terms:'Open to swap with unit in Davao or CDO',       tags:['With garden','Quiet street','Near school'],   rating:4.8, owner:'Emma R.',    owner_initials:'ER', owner_color:'#14b8a6', listed_at:'1d ago',  images:['https://images.unsplash.com/photo-1568605114967-8130f3a36994?w=800&q=80'] },
]

const allHomes = computed(() => homes.value.length ? homes.value : fakeHomes)

// ── section 1: featured ───────────────────────────────────────────────────────
// Featured = promoted listings only
const featuredHomes = computed(() =>
  allHomes.value.filter(h => h.is_promoted).slice(0, 8)
)

// ── section 2: popular in city ────────────────────────────────────────────────
const cityHomes = computed(() => {
  if (!cityName.value) return allHomes.value.slice(0, 6)
  const inCity = allHomes.value.filter(h => h.city?.toLowerCase().includes(cityName.value.toLowerCase()) || h.location?.toLowerCase().includes(cityName.value.toLowerCase()))
  return inCity.length >= 3 ? inCity.slice(0,8) : allHomes.value.slice(0,6)
})

// ── section 3: nearby city (conditional) ─────────────────────────────────────
const nearbyCityHomes = computed(() => {
  if (!nearbyCity.value || nearbyCity.value === cityName.value) return []
  const inNearby = allHomes.value.filter(h => h.city?.toLowerCase().includes(nearbyCity.value.toLowerCase()) || h.location?.toLowerCase().includes(nearbyCity.value.toLowerCase()))
  return inNearby.length >= 3 ? inNearby.slice(0,8) : []
})

// ── section 4: near area ──────────────────────────────────────────────────────
const areaHomes = computed(() => {
  if (!areaName.value || areaName.value === cityName.value) return allHomes.value.slice(0, 6)
  const inArea = allHomes.value.filter(h => h.area?.toLowerCase().includes(areaName.value.toLowerCase()) || h.location?.toLowerCase().includes(areaName.value.toLowerCase()))
  return inArea.length >= 3 ? inArea.slice(0,8) : allHomes.value.slice(0, 6)
})

// ── section 5: all — filters ──────────────────────────────────────────────────
const search        = ref('')
const searchInput   = ref('')
const locationInput = ref('')
const activeType    = ref('All')
const activeBeds    = ref('Any')
const sortBy        = ref('Newest')
const skeletonLoading   = ref(false)
const showSortDropdown  = ref(false)
const showTypePanel     = ref(false)
const showBedsPanel     = ref(false)

const listingTypes = ['All', 'Swap', 'Rent', 'Sell', 'Co-living']
const bedOptions   = ['Any', 'Studio', '1', '2', '3', '4+']
const sortOptions  = ['Newest', 'Nearest First', 'Price: Low–High', 'Price: High–Low', 'Highest Rated']

function closeAllPanels() {
  showSortDropdown.value = false
  showTypePanel.value    = false
  showBedsPanel.value    = false
}

const filtered = computed(() => {
  let list = [...allHomes.value]
  if (search.value.trim())
    list = list.filter(h => h.title.toLowerCase().includes(search.value.toLowerCase()) || h.location.toLowerCase().includes(search.value.toLowerCase()))
  if (activeType.value !== 'All')  list = list.filter(h => h.type === activeType.value)
  if (activeBeds.value !== 'Any')  list = list.filter(h => h.beds === activeBeds.value)
  if (sortBy.value === 'Newest')           list = [...list]
  if (sortBy.value === 'Nearest First')    list = [...list].sort((a,b) => parseFloat(a.distance||0) - parseFloat(b.distance||0))
  if (sortBy.value === 'Price: Low–High')  list = [...list].sort((a,b) => a.value - b.value)
  if (sortBy.value === 'Price: High–Low')  list = [...list].sort((a,b) => b.value - a.value)
  if (sortBy.value === 'Highest Rated')    list = [...list].sort((a,b) => b.rating - a.rating)
  return list
})

function doSearch() { search.value = searchInput.value }

// ── helpers ───────────────────────────────────────────────────────────────────
const typeCfg = {
  'Swap':      { color:'#ED730C', bg:'#FFF4EC', border:'#fed7aa' },
  'Rent':      { color:'#14b8a6', bg:'#EDFAF9', border:'#99f6e4' },
  'Sell':      { color:'#8b5cf6', bg:'#F5F3FF', border:'#ddd6fe' },
  'Co-living': { color:'#f59e0b', bg:'#FFFBEB', border:'#fde68a' },
}

function formatValue(home) {
  if (home.type === 'Sell') return '₱' + (home.value / 1000000).toFixed(1) + 'M'
  return '₱' + home.value.toLocaleString() + '/mo'
}

const wishlisted = ref(new Set())
function toggleWish(id) {
  const s = new Set(wishlisted.value)
  s.has(id) ? s.delete(id) : s.add(id)
  wishlisted.value = s
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
    <div style="max-width:760px;margin:0 auto;" @click.stop>
      <div class="sticky-search-desktop" style="background:#fff;border-radius:999px;display:flex;align-items:center;padding:6px 6px 6px 20px;box-shadow:0 8px 32px rgba(0,0,0,0.12);border:1.5px solid #EBEBEB;">
        <div style="flex:1;display:flex;align-items:center;gap:8px;border-right:1px solid #EBEBEB;padding-right:16px;">
          <svg width="15" height="15" fill="none" stroke="#9ca3af" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
          <input v-model="searchInput" type="text" placeholder="Search homes..." style="border:none;outline:none;font-size:0.875rem;color:#1A1A1A;background:transparent;font-family:'DM Sans',sans-serif;width:100%;" @keydown.enter="doSearch">
        </div>
        <div style="display:flex;align-items:center;gap:6px;padding:0 14px;">
          <svg width="13" height="13" fill="#9ca3af" viewBox="0 0 24 24"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>
          <input v-model="locationInput" type="text" placeholder="Location" style="border:none;outline:none;font-size:0.875rem;color:#1A1A1A;background:transparent;font-family:'DM Sans',sans-serif;width:90px;">
        </div>
        <button @click="doSearch" style="background:#ED730C;color:#fff;border:none;border-radius:999px;padding:12px 28px;font-size:0.875rem;font-weight:800;cursor:pointer;font-family:'DM Sans',sans-serif;white-space:nowrap;box-shadow:0 4px 14px rgba(237,115,12,0.4);" onmouseover="this.style.background='#d4620a'" onmouseout="this.style.background='#ED730C'">Search</button>
      </div>
      <div class="sticky-search-mobile" style="background:#fff;border-radius:999px;align-items:center;padding:5px 5px 5px 16px;box-shadow:0 4px 20px rgba(0,0,0,0.10);border:1.5px solid #EBEBEB;">
        <svg width="14" height="14" fill="none" stroke="#9ca3af" stroke-width="2" viewBox="0 0 24 24" style="flex-shrink:0;margin-right:8px;"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
        <input v-model="searchInput" type="text" placeholder="Search" style="flex:1;border:none;outline:none;font-size:0.875rem;color:#1A1A1A;background:transparent;font-family:'DM Sans',sans-serif;min-width:0;" @keydown.enter="doSearch">
        <div style="display:flex;align-items:center;gap:4px;padding:0 10px;border-left:1px solid #EBEBEB;flex-shrink:0;">
          <svg width="12" height="12" fill="#9ca3af" viewBox="0 0 24 24"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>
          <input v-model="locationInput" type="text" placeholder="Location" style="border:none;outline:none;font-size:0.82rem;color:#1A1A1A;background:transparent;font-family:'DM Sans',sans-serif;width:70px;">
        </div>
        <button @click="doSearch" style="background:#ED730C;color:#fff;border:none;border-radius:999px;padding:9px 18px;font-size:0.82rem;font-weight:800;cursor:pointer;font-family:'DM Sans',sans-serif;white-space:nowrap;flex-shrink:0;">Search</button>
      </div>
    </div>
  </Teleport>

  <!-- ═══════════════════════════════════════════
       HERO
  ═══════════════════════════════════════════ -->
  <section style="padding:52px 24px 40px;background:#fff;text-align:center;">
    <div style="max-width:860px;margin:0 auto;">
      <div style="display:flex;align-items:baseline;gap:16px;flex-wrap:wrap;margin-bottom:28px;justify-content:center;">
        <h1 style="font-size:clamp(2.2rem,5vw,3.6rem);font-weight:900;line-height:1.05;letter-spacing:-.03em;color:#1A1A1A;margin:0;">Find your next home.</h1>
        <h1 style="font-size:clamp(2.2rem,5vw,3.6rem);font-weight:900;line-height:1.05;letter-spacing:-.03em;color:#ED730C;margin:0;">Swap, rent, or buy.</h1>
      </div>
      <div ref="heroSearchEl" class="hero-search-wrap">
        <div class="hero-search-desktop">
          <div style="flex:1;display:flex;align-items:center;gap:8px;border-right:1px solid #EBEBEB;padding-right:16px;">
            <svg width="15" height="15" fill="none" stroke="#9ca3af" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
            <input v-model="searchInput" type="text" placeholder="Search homes..." style="border:none;outline:none;font-size:0.875rem;color:#1A1A1A;background:transparent;font-family:'DM Sans',sans-serif;width:100%;" @keydown.enter="doSearch">
          </div>
          <div style="display:flex;align-items:center;gap:6px;padding:0 14px;">
            <svg width="13" height="13" fill="#9ca3af" viewBox="0 0 24 24"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>
            <input v-model="locationInput" type="text" placeholder="Location" style="border:none;outline:none;font-size:0.875rem;color:#1A1A1A;background:transparent;font-family:'DM Sans',sans-serif;width:110px;">
          </div>
          <button @click="doSearch" style="background:#ED730C;color:#fff;border:none;border-radius:999px;padding:12px 28px;font-size:0.875rem;font-weight:800;cursor:pointer;font-family:'DM Sans',sans-serif;white-space:nowrap;box-shadow:0 4px 14px rgba(237,115,12,0.4);" onmouseover="this.style.background='#d4620a'" onmouseout="this.style.background='#ED730C'">Search</button>
        </div>
        <div class="hero-search-mobile">
          <svg width="15" height="15" fill="none" stroke="#9ca3af" stroke-width="2" viewBox="0 0 24 24" style="flex-shrink:0;"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
          <input v-model="searchInput" type="text" placeholder="Search" style="flex:1;border:none;outline:none;font-size:0.875rem;color:#1A1A1A;background:transparent;font-family:'DM Sans',sans-serif;min-width:0;" @keydown.enter="doSearch">
          <div style="display:flex;align-items:center;gap:4px;padding:0 10px;border-left:1px solid #EBEBEB;flex-shrink:0;">
            <svg width="12" height="12" fill="#9ca3af" viewBox="0 0 24 24"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>
            <input v-model="locationInput" type="text" placeholder="Location" style="border:none;outline:none;font-size:0.82rem;color:#1A1A1A;background:transparent;font-family:'DM Sans',sans-serif;width:70px;">
          </div>
          <button @click="doSearch" style="background:#ED730C;color:#fff;border:none;border-radius:999px;padding:10px 18px;font-size:0.82rem;font-weight:800;cursor:pointer;font-family:'DM Sans',sans-serif;white-space:nowrap;flex-shrink:0;">Search</button>
        </div>
      </div>
      <div style="display:flex;gap:8px;flex-wrap:wrap;justify-content:center;">
        <button v-for="t in listingTypes" :key="t" @click="activeType = t"
          :style="{padding:'7px 16px',borderRadius:'999px',fontSize:'0.78rem',fontWeight:'600',fontFamily:'\'DM Sans\',sans-serif',cursor:'pointer',border:activeType===t?'none':'1px solid #EBEBEB',background:activeType===t?'#1A1A1A':'#fff',color:activeType===t?'#fff':'#4b5563',boxShadow:activeType===t?'0 4px 12px rgba(0,0,0,0.15)':'none',transition:'all 0.15s'}">
          {{ t }}
        </button>
      </div>
    </div>
  </section>

  <!-- ───────────────────────────────────────────
       1. FEATURED HOMES (promoted listings only)
  ─────────────────────────────────────────── -->
  <section v-if="featuredHomes.length" class="scroll-section">
    <div class="section-inner">
      <div class="section-header">
        <div>
          <h2 class="section-title">Featured Homes</h2>
          <p class="section-sub">Promoted listings from hosts in the community</p>
        </div>
        <a href="#" class="see-all">See all →</a>
      </div>
      <div class="hscroll">
        <div v-for="home in featuredHomes" :key="'feat-'+home.id" class="home-card hscroll-card">
          <div style="position:relative;aspect-ratio:16/10;overflow:hidden;background:#f3f4f6;flex-shrink:0;">
            <img :src="home.images[0]" :alt="home.title" style="width:100%;height:100%;object-fit:cover;transition:transform .4s;">
            <span :style="{position:'absolute',top:'12px',left:'12px',background:typeCfg[home.type]?.bg,color:typeCfg[home.type]?.color,border:`1.5px solid ${typeCfg[home.type]?.border}`,fontSize:'0.62rem',fontWeight:'800',padding:'4px 11px',borderRadius:'999px',letterSpacing:'.07em',textTransform:'uppercase'}">{{ home.type }}</span>
            <span style="position:absolute;top:12px;right:12px;display:flex;align-items:center;gap:3px;background:#1A1A1A;color:#fff;font-size:0.55rem;font-weight:800;padding:4px 9px;border-radius:999px;letter-spacing:.05em;text-transform:uppercase;"><svg width="9" height="9" viewBox="0 0 24 24" fill="#fff"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>Promoted</span>
          </div>
          <div style="padding:14px 16px 16px;display:flex;flex-direction:column;flex:1;">
            <h3 class="home-title">{{ home.title }}</h3>
            <div style="display:flex;align-items:center;gap:4px;margin-bottom:10px;">
              <svg width="11" height="11" fill="none" stroke="#9ca3af" stroke-width="2" viewBox="0 0 24 24"><path d="M21 10c0 7-9 13-9 13S3 17 3 10a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
              <span style="font-size:0.72rem;color:#9ca3af;font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ home.location }}</span>
            </div>
            <div class="home-stats">
              <span>{{ home.beds === 'Studio' ? 'Studio' : home.beds + ' bed' }}</span>
              <span>{{ home.baths }} bath</span>
              <span>{{ home.sqm }} m²</span>
            </div>
            <div style="display:flex;align-items:center;justify-content:space-between;margin-top:auto;padding-top:10px;">
              <p style="font-size:1rem;font-weight:900;color:#1A1A1A;margin:0;">{{ formatValue(home) }}</p>
              <div style="display:flex;align-items:center;gap:3px;">
                <svg width="11" height="11" fill="#f59e0b" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                <span style="font-size:0.75rem;font-weight:700;color:#6b7280;">{{ home.rating }}</span>
              </div>
            </div>
            <a :href="`/homes/${home.id}`" class="home-cta">View Listing →</a>
          </div>
        </div>
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
          <p class="section-sub">Most viewed listings in your city</p>
        </div>
        <a href="#" class="see-all">See all →</a>
      </div>
      <div class="hscroll">
        <div v-for="home in cityHomes" :key="'city-'+home.id" class="home-card hscroll-card">
          <div style="position:relative;aspect-ratio:16/10;overflow:hidden;background:#f3f4f6;flex-shrink:0;">
            <img :src="home.images[0]" :alt="home.title" style="width:100%;height:100%;object-fit:cover;transition:transform .4s;">
            <span :style="{position:'absolute',top:'12px',left:'12px',background:typeCfg[home.type]?.bg,color:typeCfg[home.type]?.color,border:`1.5px solid ${typeCfg[home.type]?.border}`,fontSize:'0.62rem',fontWeight:'800',padding:'4px 11px',borderRadius:'999px',letterSpacing:'.07em',textTransform:'uppercase'}">{{ home.type }}</span>
            <span style="position:absolute;bottom:10px;right:12px;font-size:0.62rem;font-weight:700;color:#fff;background:rgba(0,0,0,0.42);padding:3px 9px;border-radius:999px;backdrop-filter:blur(4px);">{{ home.listed_at }}</span>
          </div>
          <div style="padding:14px 16px 16px;display:flex;flex-direction:column;flex:1;">
            <h3 class="home-title">{{ home.title }}</h3>
            <div style="display:flex;align-items:center;gap:4px;margin-bottom:10px;">
              <svg width="11" height="11" fill="none" stroke="#9ca3af" stroke-width="2" viewBox="0 0 24 24"><path d="M21 10c0 7-9 13-9 13S3 17 3 10a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
              <span style="font-size:0.72rem;color:#9ca3af;font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ home.location }}</span>
            </div>
            <div class="home-stats">
              <span>{{ home.beds === 'Studio' ? 'Studio' : home.beds + ' bed' }}</span>
              <span>{{ home.baths }} bath</span>
              <span>{{ home.sqm }} m²</span>
            </div>
            <div style="display:flex;align-items:center;justify-content:space-between;margin-top:auto;padding-top:10px;">
              <p style="font-size:1rem;font-weight:900;color:#1A1A1A;margin:0;">{{ formatValue(home) }}</p>
              <div style="display:flex;align-items:center;gap:3px;">
                <svg width="11" height="11" fill="#f59e0b" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                <span style="font-size:0.75rem;font-weight:700;color:#6b7280;">{{ home.rating }}</span>
              </div>
            </div>
            <a :href="`/homes/${home.id}`" class="home-cta">View Listing →</a>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- ───────────────────────────────────────────
       3. AVAILABLE IN [NEARBY CITY]  (conditional)
  ─────────────────────────────────────────── -->
  <section v-if="nearbyCityHomes.length" class="scroll-section">
    <div class="section-inner">
      <div class="section-header">
        <div>
          <h2 class="section-title">Available in {{ nearbyCity }}</h2>
          <p class="section-sub">Listings from a nearby city</p>
        </div>
        <a href="#" class="see-all">See all →</a>
      </div>
      <div class="hscroll">
        <div v-for="home in nearbyCityHomes" :key="'nearby-'+home.id" class="home-card hscroll-card">
          <div style="position:relative;aspect-ratio:16/10;overflow:hidden;background:#f3f4f6;flex-shrink:0;">
            <img :src="home.images[0]" :alt="home.title" style="width:100%;height:100%;object-fit:cover;transition:transform .4s;">
            <span :style="{position:'absolute',top:'12px',left:'12px',background:typeCfg[home.type]?.bg,color:typeCfg[home.type]?.color,border:`1.5px solid ${typeCfg[home.type]?.border}`,fontSize:'0.62rem',fontWeight:'800',padding:'4px 11px',borderRadius:'999px',letterSpacing:'.07em',textTransform:'uppercase'}">{{ home.type }}</span>
            <span style="position:absolute;bottom:10px;right:12px;font-size:0.62rem;font-weight:700;color:#fff;background:rgba(0,0,0,0.42);padding:3px 9px;border-radius:999px;backdrop-filter:blur(4px);">{{ home.listed_at }}</span>
          </div>
          <div style="padding:14px 16px 16px;display:flex;flex-direction:column;flex:1;">
            <h3 class="home-title">{{ home.title }}</h3>
            <div style="display:flex;align-items:center;gap:4px;margin-bottom:10px;">
              <svg width="11" height="11" fill="none" stroke="#9ca3af" stroke-width="2" viewBox="0 0 24 24"><path d="M21 10c0 7-9 13-9 13S3 17 3 10a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
              <span style="font-size:0.72rem;color:#9ca3af;font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ home.location }}</span>
            </div>
            <div class="home-stats">
              <span>{{ home.beds === 'Studio' ? 'Studio' : home.beds + ' bed' }}</span>
              <span>{{ home.baths }} bath</span>
              <span>{{ home.sqm }} m²</span>
            </div>
            <div style="display:flex;align-items:center;justify-content:space-between;margin-top:auto;padding-top:10px;">
              <p style="font-size:1rem;font-weight:900;color:#1A1A1A;margin:0;">{{ formatValue(home) }}</p>
              <div style="display:flex;align-items:center;gap:3px;">
                <svg width="11" height="11" fill="#f59e0b" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                <span style="font-size:0.75rem;font-weight:700;color:#6b7280;">{{ home.rating }}</span>
              </div>
            </div>
            <a :href="`/homes/${home.id}`" class="home-cta">View Listing →</a>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- ───────────────────────────────────────────
       4. HOMES NEAR [AREA]
  ─────────────────────────────────────────── -->
  <section class="scroll-section">
    <div class="section-inner">
      <div class="section-header">
        <div>
          <h2 class="section-title">Homes near {{ areaName || 'Your Area' }}</h2>
          <p class="section-sub">Listings closest to your location</p>
        </div>
        <a href="#" class="see-all">See all →</a>
      </div>
      <div class="hscroll">
        <div v-for="home in areaHomes" :key="'area-'+home.id" class="home-card hscroll-card">
          <div style="position:relative;aspect-ratio:16/10;overflow:hidden;background:#f3f4f6;flex-shrink:0;">
            <img :src="home.images[0]" :alt="home.title" style="width:100%;height:100%;object-fit:cover;transition:transform .4s;">
            <span :style="{position:'absolute',top:'12px',left:'12px',background:typeCfg[home.type]?.bg,color:typeCfg[home.type]?.color,border:`1.5px solid ${typeCfg[home.type]?.border}`,fontSize:'0.62rem',fontWeight:'800',padding:'4px 11px',borderRadius:'999px',letterSpacing:'.07em',textTransform:'uppercase'}">{{ home.type }}</span>
            <span style="position:absolute;bottom:10px;right:12px;font-size:0.62rem;font-weight:700;color:#fff;background:rgba(0,0,0,0.42);padding:3px 9px;border-radius:999px;backdrop-filter:blur(4px);">{{ home.listed_at }}</span>
          </div>
          <div style="padding:14px 16px 16px;display:flex;flex-direction:column;flex:1;">
            <h3 class="home-title">{{ home.title }}</h3>
            <div style="display:flex;align-items:center;gap:4px;margin-bottom:10px;">
              <svg width="11" height="11" fill="none" stroke="#9ca3af" stroke-width="2" viewBox="0 0 24 24"><path d="M21 10c0 7-9 13-9 13S3 17 3 10a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
              <span style="font-size:0.72rem;color:#9ca3af;font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ home.location }}</span>
            </div>
            <div class="home-stats">
              <span>{{ home.beds === 'Studio' ? 'Studio' : home.beds + ' bed' }}</span>
              <span>{{ home.baths }} bath</span>
              <span>{{ home.sqm }} m²</span>
            </div>
            <div style="display:flex;align-items:center;justify-content:space-between;margin-top:auto;padding-top:10px;">
              <p style="font-size:1rem;font-weight:900;color:#1A1A1A;margin:0;">{{ formatValue(home) }}</p>
              <div style="display:flex;align-items:center;gap:3px;">
                <svg width="11" height="11" fill="#f59e0b" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                <span style="font-size:0.75rem;font-weight:700;color:#6b7280;">{{ home.rating }}</span>
              </div>
            </div>
            <a :href="`/homes/${home.id}`" class="home-cta">View Listing →</a>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- ───────────────────────────────────────────
       5. ALL HOMES
  ─────────────────────────────────────────── -->
  <section style="background:#fff;" @click.stop>

    <!-- Filter bar -->
    <div class="filter-bar">
      <div class="filter-bar-inner">
        <div class="filter-left">
          <!-- Type -->
          <div style="position:relative;">
            <button @click.stop="showTypePanel=!showTypePanel;showBedsPanel=false;showSortDropdown=false" class="filter-btn">
              <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 4h13M3 8h9m-9 4h9m5-4v12m0 0l-4-4m4 4l4-4"/></svg>
              {{ activeType === 'All' ? 'All Types' : activeType }}
            </button>
            <div v-if="showTypePanel" class="dropdown-panel">
              <button v-for="t in listingTypes" :key="t" @click.stop="activeType=t;showTypePanel=false" :class="['dropdown-item', {active: activeType===t}]">{{ t }}</button>
            </div>
          </div>
          <!-- Beds -->
          <div style="position:relative;">
            <button @click.stop="showBedsPanel=!showBedsPanel;showTypePanel=false;showSortDropdown=false" class="filter-btn">
              <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 7v13h18V7"/><path d="M1 10h22"/><path d="M7 10V4h10v6"/></svg>
              {{ activeBeds === 'Any' ? 'Any Beds' : activeBeds === 'Studio' ? 'Studio' : activeBeds + ' Bed' }}
            </button>
            <div v-if="showBedsPanel" class="dropdown-panel">
              <button v-for="b in bedOptions" :key="b" @click.stop="activeBeds=b;showBedsPanel=false" :class="['dropdown-item', {active: activeBeds===b}]">{{ b === 'Any' ? 'Any Beds' : b === 'Studio' ? 'Studio' : b + ' Bedroom' }}</button>
            </div>
          </div>
          <!-- Sort -->
          <div style="position:relative;">
            <button @click.stop="showSortDropdown=!showSortDropdown;showTypePanel=false;showBedsPanel=false" class="filter-btn">
              <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12"/></svg>
              {{ sortBy }}
            </button>
            <div v-if="showSortDropdown" class="dropdown-panel">
              <button v-for="s in sortOptions" :key="s" @click.stop="sortBy=s;showSortDropdown=false" :class="['dropdown-item', {active: sortBy===s}]">{{ s }}</button>
            </div>
          </div>
        </div>
        <p style="font-size:0.82rem;color:#9ca3af;font-weight:600;margin:0;white-space:nowrap;">
          <strong style="color:#1A1A1A;">{{ filtered.length }}</strong> listings
        </p>
      </div>
    </div>

    <div class="section-inner" style="padding-top:28px;padding-bottom:0;">
      <h2 class="section-title">{{ activeType === 'All' ? 'All Listings' : activeType + ' Listings' }}</h2>
      <p class="section-sub" style="margin-top:4px;">Browse every home listing on Swapy</p>
    </div>

    <div class="section-inner" style="padding-top:20px;padding-bottom:72px;">

      <!-- Skeleton -->
      <div v-if="skeletonLoading" class="main-grid">
        <div v-for="n in 6" :key="n" class="skeleton-card">
          <div class="skeleton" style="aspect-ratio:16/10;"></div>
          <div style="padding:16px 18px;">
            <div class="skeleton" style="height:14px;width:70%;border-radius:4px;margin-bottom:8px;"></div>
            <div class="skeleton" style="height:11px;width:45%;border-radius:4px;margin-bottom:12px;"></div>
            <div class="skeleton" style="height:36px;border-radius:12px;"></div>
          </div>
        </div>
      </div>

      <!-- Empty -->
      <div v-else-if="filtered.length === 0" style="text-align:center;padding:80px 0;">
        <div style="font-size:3rem;margin-bottom:16px;">🏠</div>
        <h3 style="font-size:1.125rem;font-weight:800;color:#1A1A1A;margin-bottom:8px;">No listings found</h3>
        <p style="font-size:0.875rem;color:#9ca3af;margin-bottom:20px;">Try adjusting your filters or search term.</p>
        <button @click="activeType='All';activeBeds='Any';search=''" class="clear-btn">Clear Filters</button>
      </div>

      <!-- Grid -->
      <div v-else class="main-grid">
        <div v-for="home in filtered" :key="home.id" class="home-card home-card--full">

          <!-- Cover image -->
          <div style="position:relative;aspect-ratio:16/10;overflow:hidden;background:#f3f4f6;flex-shrink:0;">
            <img :src="home.images[0]" :alt="home.title" style="width:100%;height:100%;object-fit:cover;transition:transform .4s;" @error="e => e.target.style.display='none'">
            <span :style="{position:'absolute',top:'12px',left:'12px',background:typeCfg[home.type]?.bg,color:typeCfg[home.type]?.color,border:`1.5px solid ${typeCfg[home.type]?.border}`,fontSize:'0.62rem',fontWeight:'800',padding:'4px 11px',borderRadius:'999px',letterSpacing:'.07em',textTransform:'uppercase'}">{{ home.type }}</span>
            <button @click.stop="toggleWish(home.id)" :style="{position:'absolute',top:'10px',right:'10px',width:'32px',height:'32px',borderRadius:'50%',background:wishlisted.has(home.id)?'#ED730C':'rgba(255,255,255,0.92)',border:'none',cursor:'pointer',display:'flex',alignItems:'center',justifyContent:'center',boxShadow:'0 2px 8px rgba(0,0,0,0.12)',transition:'background .15s'}">
              <svg width="14" height="14" viewBox="0 0 24 24" :fill="wishlisted.has(home.id)?'#fff':'none'" :stroke="wishlisted.has(home.id)?'#fff':'#6b7280'" stroke-width="2"><path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z"/></svg>
            </button>
            <span style="position:absolute;bottom:10px;right:12px;font-size:0.62rem;font-weight:700;color:#fff;background:rgba(0,0,0,0.42);padding:3px 9px;border-radius:999px;backdrop-filter:blur(4px);">{{ home.listed_at }}</span>
          </div>

          <!-- Body -->
          <div style="padding:16px 18px 18px;display:flex;flex-direction:column;flex:1;">
            <h3 style="font-size:0.95rem;font-weight:800;color:#1A1A1A;margin:0 0 4px;line-height:1.3;letter-spacing:-.01em;">{{ home.title }}</h3>
            <div style="display:flex;align-items:center;gap:4px;margin-bottom:12px;">
              <svg width="11" height="11" fill="none" stroke="#9ca3af" stroke-width="2" viewBox="0 0 24 24"><path d="M21 10c0 7-9 13-9 13S3 17 3 10a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
              <span style="font-size:0.75rem;color:#9ca3af;font-weight:600;">{{ home.location }} · {{ home.distance }} mi</span>
            </div>
            <div style="display:flex;align-items:center;gap:14px;padding:10px 0;border-top:1px solid #F3F0EC;border-bottom:1px solid #F3F0EC;margin-bottom:12px;">
              <div style="display:flex;align-items:center;gap:5px;">
                <svg width="13" height="13" fill="none" stroke="#6b7280" stroke-width="2" viewBox="0 0 24 24"><path d="M3 7v13h18V7"/><path d="M1 10h22"/><path d="M7 10V4h10v6"/></svg>
                <span style="font-size:0.78rem;font-weight:700;color:#3a3a3a;">{{ home.beds === 'Studio' ? 'Studio' : home.beds + ' bed' }}</span>
              </div>
              <div style="display:flex;align-items:center;gap:5px;">
                <svg width="13" height="13" fill="none" stroke="#6b7280" stroke-width="2" viewBox="0 0 24 24"><path d="M5 12H19"/><path d="M5 12a7 7 0 0114 0"/><path d="M5 19h14"/><path d="M5 12v7"/><path d="M19 12v7"/></svg>
                <span style="font-size:0.78rem;font-weight:700;color:#3a3a3a;">{{ home.baths }} bath</span>
              </div>
              <div style="display:flex;align-items:center;gap:5px;">
                <svg width="13" height="13" fill="none" stroke="#6b7280" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="1"/><path d="M3 9h18M9 3v18"/></svg>
                <span style="font-size:0.78rem;font-weight:700;color:#3a3a3a;">{{ home.sqm }} m²</span>
              </div>
            </div>
            <div style="display:flex;flex-wrap:wrap;gap:6px;margin-bottom:12px;">
              <span v-for="tag in home.tags" :key="tag" style="font-size:0.68rem;font-weight:700;color:#6b7280;background:#F3F0EC;padding:3px 10px;border-radius:999px;">{{ tag }}</span>
            </div>
            <div v-if="home.swap_terms" style="background:#FFF4EC;border:1px solid #fed7aa;border-radius:10px;padding:8px 12px;margin-bottom:12px;display:flex;align-items:flex-start;gap:7px;">
              <svg width="13" height="13" fill="none" stroke="#ED730C" stroke-width="2" viewBox="0 0 24 24" style="flex-shrink:0;margin-top:1px;"><path d="M7 16l-4-4 4-4M17 8l4 4-4 4M14 4l-4 16"/></svg>
              <p style="font-size:0.72rem;font-weight:600;color:#92400e;margin:0;line-height:1.45;">{{ home.swap_terms }}</p>
            </div>
            <div style="display:flex;align-items:center;justify-content:space-between;margin-top:auto;">
              <div>
                <p style="font-size:0.62rem;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.06em;margin:0 0 2px;">{{ home.type === 'Sell' ? 'Asking Price' : home.type === 'Swap' ? 'Est. Value' : 'Monthly Rate' }}</p>
                <p style="font-size:1.05rem;font-weight:900;color:#1A1A1A;margin:0;letter-spacing:-.02em;">{{ formatValue(home) }}</p>
              </div>
              <div style="display:flex;align-items:center;gap:7px;">
                <div :style="{width:'30px',height:'30px',borderRadius:'50%',background:home.owner_color,color:'#fff',fontSize:'0.65rem',fontWeight:'800',display:'flex',alignItems:'center',justifyContent:'center'}">{{ home.owner_initials }}</div>
                <div>
                  <p style="font-size:0.72rem;font-weight:700;color:#1A1A1A;margin:0;line-height:1.2;">{{ home.owner }}</p>
                  <div style="display:flex;align-items:center;gap:3px;">
                    <svg width="10" height="10" fill="#f59e0b" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                    <span style="font-size:0.68rem;font-weight:700;color:#6b7280;">{{ home.rating }}</span>
                  </div>
                </div>
              </div>
            </div>
            <a :href="`/homes/${home.id}`" style="display:block;margin-top:14px;padding:11px;background:#ED730C;color:#fff;border-radius:12px;font-size:0.82rem;font-weight:800;text-align:center;text-decoration:none;letter-spacing:.02em;box-shadow:0 4px 12px rgba(237,115,12,0.25);" onmouseover="this.style.background='#d4620a'" onmouseout="this.style.background='#ED730C'">View Listing →</a>
          </div>
        </div>
      </div>

      <!-- Load more -->
      <div v-if="!skeletonLoading && filtered.length > 0" style="text-align:center;margin-top:48px;">
        <button @click="loadMore" class="load-more-btn">
          <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" d="M19 9l-7 7-7-7"/></svg>
          Load More
        </button>
        <p style="margin-top:12px;font-size:0.78rem;color:#9ca3af;">Showing {{ filtered.length }} listings</p>
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

/* ── Home card ── */
.home-card {
  background: #fff;
  border-radius: 20px;
  overflow: hidden;
  border: 1px solid #EDE8E0;
  box-shadow: 0 2px 12px rgba(0,0,0,0.06);
  display: flex;
  flex-direction: column;
  transition: box-shadow .2s, transform .2s;
}
.home-card:hover { box-shadow: 0 8px 32px rgba(0,0,0,0.12); transform: translateY(-2px); }
.home-card:hover img { transform: scale(1.04); }
.home-card--full { cursor: pointer; }

.home-title { font-size: 0.88rem; font-weight: 800; color: #1A1A1A; margin: 0 0 4px; line-height: 1.3; display: -webkit-box; -webkit-line-clamp: 2; line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
.home-stats { display: flex; align-items: center; gap: 12px; padding: 8px 0; border-top: 1px solid #F3F0EC; border-bottom: 1px solid #F3F0EC; margin-bottom: 10px; font-size: 0.78rem; font-weight: 700; color: #3a3a3a; }
.home-cta { display: block; margin-top: 12px; padding: 10px; background: #ED730C; color: #fff; border-radius: 12px; font-size: 0.8rem; font-weight: 800; text-align: center; text-decoration: none; letter-spacing: .02em; box-shadow: 0 4px 12px rgba(237,115,12,0.25); transition: background .15s; }
.home-cta:hover { background: #d4620a; }

/* ── Main grid ── */
.main-grid { display: grid; gap: 22px; }

/* ── Skeleton ── */
.skeleton { background: linear-gradient(90deg, #f3f4f6 25%, #e9eaec 50%, #f3f4f6 75%); background-size: 1200px 100%; animation: shimmer 1.4s ease-in-out infinite; }
.skeleton-card { background: #fff; border-radius: 20px; overflow: hidden; border: 1px solid #EDE8E0; }

/* ── Filter bar ── */
.filter-bar { background: #fff; border-bottom: 1px solid #EBEBEB; padding: 12px 40px; }
.filter-bar-inner { max-width: 1680px; margin: 0 auto; display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap; }
.filter-left { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
.filter-btn { display: flex; align-items: center; gap: 7px; padding: 8px 16px; border-radius: 999px; border: 1.5px solid #e2ddd8; background: #fff; font-family: 'DM Sans', sans-serif; font-size: 0.82rem; font-weight: 700; color: #1A1A1A; cursor: pointer; transition: border-color 0.15s; }
.filter-btn:hover { border-color: #1A1A1A; }

/* ── Dropdown ── */
.dropdown-panel { position: absolute; top: calc(100% + 8px); left: 0; background: #fff; border: 1px solid #EBEBEB; border-radius: 16px; padding: 6px; min-width: 200px; box-shadow: 0 8px 32px rgba(0,0,0,0.10); z-index: 100; }
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
