<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'

const el      = document.getElementById('garage-sale-app')
const sellers = ref(JSON.parse(el?.dataset.sellers || '[]'))

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
  slot.classList.toggle('open', scrollY.value > threshold)
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

// ── fallback seller data ──────────────────────────────────────────────────────
const fakeSellers = [
  { id:1,  is_promoted:true, name:'Sarah L.',    username:'sarah_listening',  rating:'5.0', distance:'3.5', active_since:'Active 2h ago',  item_count:21, categories:['Audio','Electronics','Books'],          cover:'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=700&q=80',  avatar:'https://i.pravatar.cc/48?img=47', city:'Davao City',   area:'Poblacion' },
  { id:2,  name:'Julian M.',   username:'julian_design',    rating:'4.9', distance:'1.2', active_since:'Active today',   item_count:14, categories:['Electronics','Cameras','Accessories'],   cover:'https://images.unsplash.com/photo-1593642632559-0c6d3fc62b89?w=700&q=80', avatar:'https://i.pravatar.cc/48?img=12', city:'Davao City',   area:'Matina' },
  { id:3,  name:'David K.',    username:'david_k',          rating:'4.8', distance:'7.3', active_since:'Active 3h ago',  item_count:11, categories:['Gaming','Electronics','PC Parts'],       cover:'https://images.unsplash.com/photo-1587202372583-49330a15584d?w=700&q=80', avatar:'https://i.pravatar.cc/48?img=15', city:'Davao City',   area:'Buhangin' },
  { id:4,  is_promoted:true, name:'Mia R.',      username:'mia_creates',      rating:'4.7', distance:'0.8', active_since:'Active now',     item_count:33, categories:['Fashion','Accessories','Vintage'],       cover:'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=700&q=80',  avatar:'https://i.pravatar.cc/48?img=44', city:'Davao City',   area:'Poblacion' },
  { id:5,  name:'Leo B.',      username:'leo_books',        rating:'4.9', distance:'2.4', active_since:'Active 5h ago',  item_count:8,  categories:['Books','Collectibles','Art'],           cover:'https://images.unsplash.com/photo-1524995997946-a1c2e315a42f?w=700&q=80', avatar:'https://i.pravatar.cc/48?img=67', city:'General Santos', area:'Downtown' },
  { id:6,  name:'Nina T.',     username:'nina_trades',      rating:'5.0', distance:'5.1', active_since:'Active today',   item_count:17, categories:['Home','Furniture','Decor'],              cover:'https://images.unsplash.com/photo-1555041469-a586c61ea9bc?w=700&q=80',  avatar:'https://i.pravatar.cc/48?img=32', city:'General Santos', area:'Lagao' },
  { id:7,  is_promoted:true, name:'Carla D.',    username:'carla_davao',      rating:'5.0', distance:'0.4', active_since:'Active now',     item_count:25, categories:['Fashion','Bags','Shoes'],                cover:'https://images.unsplash.com/photo-1445205170230-053b83016050?w=700&q=80', avatar:'https://i.pravatar.cc/48?img=21', city:'Davao City',   area:'Poblacion' },
  { id:8,  name:'Marco P.',    username:'marco_palengke',   rating:'4.8', distance:'1.0', active_since:'Active 1h ago',  item_count:19, categories:['Appliances','Home','Tools'],             cover:'https://images.unsplash.com/photo-1555041469-a586c61ea9bc?w=700&q=80',  avatar:'https://i.pravatar.cc/48?img=33', city:'General Santos', area:'Downtown' },
  { id:9,  name:'Joy S.',      username:'joy_swaps',        rating:'4.9', distance:'2.1', active_since:'Active today',   item_count:30, categories:['Kids','Toys','Books'],                   cover:'https://images.unsplash.com/photo-1524995997946-a1c2e315a42f?w=700&q=80', avatar:'https://i.pravatar.cc/48?img=54', city:'Davao City',   area:'Matina' },
  { id:10, is_promoted:true, name:'Ren A.',      username:'ren_antiques',     rating:'5.0', distance:'0.2', active_since:'Active now',     item_count:12, categories:['Antiques','Collectibles','Art'],         cover:'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=700&q=80', avatar:'https://i.pravatar.cc/48?img=60', city:'Davao City',   area:'Poblacion' },
  { id:11, name:'Tanya M.',    username:'tanya_market',     rating:'4.8', distance:'0.5', active_since:'Active 30m ago', item_count:22, categories:['Clothes','Vintage','Accessories'],       cover:'https://images.unsplash.com/photo-1593642632559-0c6d3fc62b89?w=700&q=80', avatar:'https://i.pravatar.cc/48?img=41', city:'Davao City',   area:'Poblacion' },
  { id:12, name:'Ben C.',      username:'ben_collectibles', rating:'4.9', distance:'0.7', active_since:'Active today',   item_count:16, categories:['Gaming','Gadgets','Electronics'],        cover:'https://images.unsplash.com/photo-1587202372583-49330a15584d?w=700&q=80', avatar:'https://i.pravatar.cc/48?img=70', city:'Davao City',   area:'Buhangin' },
]

const allSellers = computed(() => sellers.value.length ? sellers.value : fakeSellers)

// ── section 1: top featured ───────────────────────────────────────────────────
// Featured = promoted listings only
const featuredSellers = computed(() =>
  allSellers.value.filter(s => s.is_promoted).slice(0, 8)
)

// ── section 2: popular in city ────────────────────────────────────────────────
const citySellers = computed(() => {
  if (!cityName.value) return allSellers.value.slice(0, 6)
  const inCity = allSellers.value.filter(s => s.city?.toLowerCase().includes(cityName.value.toLowerCase()))
  return inCity.length >= 3 ? inCity.slice(0,8) : allSellers.value.slice(0,6)
})

// ── section 3: nearby city (conditional) ─────────────────────────────────────
const nearbyCitySellers = computed(() => {
  if (!nearbyCity.value || nearbyCity.value === cityName.value) return []
  const inNearby = allSellers.value.filter(s => s.city?.toLowerCase().includes(nearbyCity.value.toLowerCase()))
  return inNearby.length >= 3 ? inNearby.slice(0,8) : []
})

// ── section 4: near area ──────────────────────────────────────────────────────
const areaSellers = computed(() => {
  if (!areaName.value || areaName.value === cityName.value) return allSellers.value.slice(0, 6)
  const inArea = allSellers.value.filter(s => s.area?.toLowerCase().includes(areaName.value.toLowerCase()))
  return inArea.length >= 3 ? inArea.slice(0,8) : allSellers.value.slice(0, 6)
})

// ── section 5: all — filters ──────────────────────────────────────────────────
const search        = ref('')
const searchInput   = ref('')
const locationInput = ref('')
const activeCategory = ref('All')
const sortBy         = ref('Most Items')
const sortOptions    = ['Most Items', 'Nearest First', 'Highest Rated', 'Recently Active']
const showSortDropdown  = ref(false)
const showCategoryPanel = ref(false)
const skeletonLoading   = ref(false)

const allCategories = computed(() => {
  const cats = new Set(allSellers.value.flatMap(s => s.categories || []))
  return ['All', ...cats]
})

const filtered = computed(() => {
  let list = [...allSellers.value]
  if (search.value.trim())
    list = list.filter(s => s.name.toLowerCase().includes(search.value.toLowerCase()) || (s.username||'').toLowerCase().includes(search.value.toLowerCase()))
  if (activeCategory.value !== 'All')
    list = list.filter(s => (s.categories||[]).includes(activeCategory.value))
  if (sortBy.value === 'Most Items')      list = list.sort((a,b) => b.item_count - a.item_count)
  if (sortBy.value === 'Nearest First')   list = list.sort((a,b) => parseFloat(a.distance||0) - parseFloat(b.distance||0))
  if (sortBy.value === 'Highest Rated')   list = list.sort((a,b) => parseFloat(b.rating||0) - parseFloat(a.rating||0))
  if (sortBy.value === 'Recently Active') list = list.sort((a,b) => a.active_since?.localeCompare(b.active_since||''))
  return list
})

function doSearch() { search.value = searchInput.value }

function closeAllPanels() {
  showSortDropdown.value  = false
  showCategoryPanel.value = false
}

async function loadMore() {
  skeletonLoading.value = true
  await new Promise(r => setTimeout(r, 700))
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
          <input v-model="searchInput" type="text" placeholder="Search garage sales..."
            style="border:none;outline:none;font-size:0.875rem;color:#1A1A1A;background:transparent;font-family:'DM Sans',sans-serif;width:100%;"
            @keydown.enter="doSearch">
        </div>
        <div style="display:flex;align-items:center;gap:6px;padding:0 14px;">
          <svg width="13" height="13" fill="#9ca3af" viewBox="0 0 24 24"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>
          <input v-model="locationInput" type="text" placeholder="Location" style="border:none;outline:none;font-size:0.875rem;color:#1A1A1A;background:transparent;font-family:'DM Sans',sans-serif;width:90px;">
        </div>
        <button @click="doSearch" style="background:#ED730C;color:#fff;border:none;border-radius:999px;padding:12px 28px;font-size:0.875rem;font-weight:800;cursor:pointer;font-family:'DM Sans',sans-serif;white-space:nowrap;box-shadow:0 4px 14px rgba(237,115,12,0.4);"
          onmouseover="this.style.background='#d4620a'" onmouseout="this.style.background='#ED730C'">Search</button>
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
        <h1 style="font-size:clamp(2.2rem,5vw,3.6rem);font-weight:900;line-height:1.05;letter-spacing:-.03em;color:#1A1A1A;margin:0;">Real people.</h1>
        <h1 style="font-size:clamp(2.2rem,5vw,3.6rem);font-weight:900;line-height:1.05;letter-spacing:-.03em;color:#ED730C;margin:0;">Real collections.</h1>
      </div>
      <div ref="heroSearchEl" class="hero-search-wrap">
        <div class="hero-search-desktop">
          <div style="flex:1;display:flex;align-items:center;gap:8px;border-right:1px solid #EBEBEB;padding-right:16px;">
            <svg width="15" height="15" fill="none" stroke="#9ca3af" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
            <input v-model="searchInput" type="text" placeholder="Search garage sales..."
              style="border:none;outline:none;font-size:0.875rem;color:#1A1A1A;background:transparent;font-family:'DM Sans',sans-serif;width:100%;"
              @keydown.enter="doSearch">
          </div>
          <div style="display:flex;align-items:center;gap:6px;padding:0 14px;">
            <svg width="13" height="13" fill="#9ca3af" viewBox="0 0 24 24"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>
            <input v-model="locationInput" type="text" placeholder="Location" style="border:none;outline:none;font-size:0.875rem;color:#1A1A1A;background:transparent;font-family:'DM Sans',sans-serif;width:110px;">
          </div>
          <button @click="doSearch" style="background:#ED730C;color:#fff;border:none;border-radius:999px;padding:12px 28px;font-size:0.875rem;font-weight:800;cursor:pointer;font-family:'DM Sans',sans-serif;white-space:nowrap;box-shadow:0 4px 14px rgba(237,115,12,0.4);"
            onmouseover="this.style.background='#d4620a'" onmouseout="this.style.background='#ED730C'">Search</button>
        </div>
        <div class="hero-search-mobile">
          <svg width="15" height="15" fill="none" stroke="#9ca3af" stroke-width="2" viewBox="0 0 24 24" style="flex-shrink:0;"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
          <input v-model="searchInput" type="text" placeholder="Search"
            style="flex:1;border:none;outline:none;font-size:0.875rem;color:#1A1A1A;background:transparent;font-family:'DM Sans',sans-serif;min-width:0;"
            @keydown.enter="doSearch">
          <div style="display:flex;align-items:center;gap:4px;padding:0 10px;border-left:1px solid #EBEBEB;flex-shrink:0;">
            <svg width="12" height="12" fill="#9ca3af" viewBox="0 0 24 24"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>
            <input v-model="locationInput" type="text" placeholder="Location" style="border:none;outline:none;font-size:0.82rem;color:#1A1A1A;background:transparent;font-family:'DM Sans',sans-serif;width:70px;">
          </div>
          <button @click="doSearch" style="background:#ED730C;color:#fff;border:none;border-radius:999px;padding:10px 18px;font-size:0.82rem;font-weight:800;cursor:pointer;font-family:'DM Sans',sans-serif;white-space:nowrap;flex-shrink:0;">Search</button>
        </div>
      </div>
      <div style="display:flex;gap:8px;flex-wrap:wrap;justify-content:center;">
        <button v-for="opt in sortOptions" :key="opt" @click="sortBy = opt"
          :style="{padding:'7px 16px',borderRadius:'999px',fontSize:'0.78rem',fontWeight:'600',fontFamily:'\'DM Sans\',sans-serif',cursor:'pointer',border:sortBy===opt?'none':'1px solid #EBEBEB',background:sortBy===opt?'#1A1A1A':'#fff',color:sortBy===opt?'#fff':'#4b5563',boxShadow:sortBy===opt?'0 4px 12px rgba(0,0,0,0.15)':'none',transition:'all 0.15s'}">
          {{ opt }}
        </button>
      </div>
    </div>
  </section>

  <!-- ───────────────────────────────────────────
       1. FEATURED GARAGE SALES (promoted listings only)
  ─────────────────────────────────────────── -->
  <section v-if="featuredSellers.length" class="scroll-section">
    <div class="section-inner">
      <div class="section-header">
        <div>
          <h2 class="section-title">Featured Garage Sales</h2>
          <p class="section-sub">Promoted sellers in the community</p>
        </div>
        <a href="#" class="see-all">See all →</a>
      </div>
      <div class="hscroll">
        <a v-for="seller in featuredSellers" :key="'feat-'+seller.username"
          :href="'/store/'+seller.username" class="gs-card hscroll-card">
          <div class="gs-cover">
            <img :src="seller.cover||'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=700&q=80'" :alt="seller.name" class="gs-img">
            <div class="gs-cover-grad"></div>
            <span class="gs-promoted"><svg width="9" height="9" viewBox="0 0 24 24" fill="#fff"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>Promoted</span>
            <span class="gs-count-pill">{{ seller.item_count }} items</span>
            <div class="gs-cats">
              <span v-for="cat in (seller.categories||[]).slice(0,3)" :key="cat" class="gs-cat-pill">{{ cat }}</span>
            </div>
          </div>
          <div class="gs-body">
            <div class="gs-seller-row">
              <div class="gs-avatar-wrap">
                <img :src="seller.avatar||'https://i.pravatar.cc/48?u='+seller.username" :alt="seller.name" class="gs-avatar">
                <span class="gs-online-dot"></span>
              </div>
              <div style="min-width:0;flex:1;">
                <div class="gs-seller-name">{{ seller.name }}'s Garage Sale</div>
                <div class="gs-seller-handle">@{{ seller.username }}</div>
              </div>
            </div>
            <div class="gs-meta-row">
              <div style="display:flex;align-items:center;gap:3px;">
                <svg width="11" height="11" fill="#f59e0b" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                <span style="font-size:0.75rem;font-weight:700;color:#1A1A1A;">{{ seller.rating }}</span>
              </div>
              <span class="gs-divider"></span>
              <span style="font-size:0.72rem;color:#9ca3af;">{{ seller.distance }} mi</span>
              <span class="gs-divider"></span>
              <span style="font-size:0.72rem;color:#9ca3af;"><strong style="color:#1A1A1A;">{{ seller.item_count }}</strong> items</span>
            </div>
            <span class="gs-cta">Visit Sale <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg></span>
          </div>
        </a>
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
          <p class="section-sub">Most active garage sales in your city</p>
        </div>
        <a href="#" class="see-all">See all →</a>
      </div>
      <div class="hscroll">
        <a v-for="seller in citySellers" :key="'city-'+seller.username"
          :href="'/store/'+seller.username" class="gs-card hscroll-card">
          <div class="gs-cover">
            <img :src="seller.cover||'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=700&q=80'" :alt="seller.name" class="gs-img">
            <div class="gs-cover-grad"></div>
            <span class="gs-count-pill">{{ seller.item_count }} items</span>
            <div class="gs-cats">
              <span v-for="cat in (seller.categories||[]).slice(0,3)" :key="cat" class="gs-cat-pill">{{ cat }}</span>
            </div>
          </div>
          <div class="gs-body">
            <div class="gs-seller-row">
              <div class="gs-avatar-wrap">
                <img :src="seller.avatar||'https://i.pravatar.cc/48?u='+seller.username" :alt="seller.name" class="gs-avatar">
                <span class="gs-online-dot"></span>
              </div>
              <div style="min-width:0;flex:1;">
                <div class="gs-seller-name">{{ seller.name }}'s Garage Sale</div>
                <div class="gs-seller-handle">@{{ seller.username }}</div>
              </div>
            </div>
            <div class="gs-meta-row">
              <div style="display:flex;align-items:center;gap:3px;">
                <svg width="11" height="11" fill="#f59e0b" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                <span style="font-size:0.75rem;font-weight:700;color:#1A1A1A;">{{ seller.rating }}</span>
              </div>
              <span class="gs-divider"></span>
              <span style="font-size:0.72rem;color:#9ca3af;">{{ seller.distance }} mi</span>
              <span class="gs-divider"></span>
              <span style="font-size:0.72rem;color:#9ca3af;"><strong style="color:#1A1A1A;">{{ seller.item_count }}</strong> items</span>
            </div>
            <span class="gs-cta">Visit Sale <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg></span>
          </div>
        </a>
      </div>
    </div>
  </section>

  <!-- ───────────────────────────────────────────
       3. ACTIVE IN [NEARBY CITY]  (conditional)
  ─────────────────────────────────────────── -->
  <section v-if="nearbyCitySellers.length" class="scroll-section">
    <div class="section-inner">
      <div class="section-header">
        <div>
          <h2 class="section-title">Active in {{ nearbyCity }}</h2>
          <p class="section-sub">Garage sales from a nearby city</p>
        </div>
        <a href="#" class="see-all">See all →</a>
      </div>
      <div class="hscroll">
        <a v-for="seller in nearbyCitySellers" :key="'nearby-'+seller.username"
          :href="'/store/'+seller.username" class="gs-card hscroll-card">
          <div class="gs-cover">
            <img :src="seller.cover||'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=700&q=80'" :alt="seller.name" class="gs-img">
            <div class="gs-cover-grad"></div>
            <span class="gs-count-pill">{{ seller.item_count }} items</span>
            <div class="gs-cats">
              <span v-for="cat in (seller.categories||[]).slice(0,3)" :key="cat" class="gs-cat-pill">{{ cat }}</span>
            </div>
          </div>
          <div class="gs-body">
            <div class="gs-seller-row">
              <div class="gs-avatar-wrap">
                <img :src="seller.avatar||'https://i.pravatar.cc/48?u='+seller.username" :alt="seller.name" class="gs-avatar">
                <span class="gs-online-dot"></span>
              </div>
              <div style="min-width:0;flex:1;">
                <div class="gs-seller-name">{{ seller.name }}'s Garage Sale</div>
                <div class="gs-seller-handle">@{{ seller.username }}</div>
              </div>
            </div>
            <div class="gs-meta-row">
              <div style="display:flex;align-items:center;gap:3px;">
                <svg width="11" height="11" fill="#f59e0b" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                <span style="font-size:0.75rem;font-weight:700;color:#1A1A1A;">{{ seller.rating }}</span>
              </div>
              <span class="gs-divider"></span>
              <span style="font-size:0.72rem;color:#9ca3af;">{{ seller.distance }} mi</span>
              <span class="gs-divider"></span>
              <span style="font-size:0.72rem;color:#9ca3af;"><strong style="color:#1A1A1A;">{{ seller.item_count }}</strong> items</span>
            </div>
            <span class="gs-cta">Visit Sale <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg></span>
          </div>
        </a>
      </div>
    </div>
  </section>

  <!-- ───────────────────────────────────────────
       4. SALES NEAR [AREA]
  ─────────────────────────────────────────── -->
  <section class="scroll-section">
    <div class="section-inner">
      <div class="section-header">
        <div>
          <h2 class="section-title">Sales near {{ areaName || 'Your Area' }}</h2>
          <p class="section-sub">Closest garage sales to your location</p>
        </div>
        <a href="#" class="see-all">See all →</a>
      </div>
      <div class="hscroll">
        <a v-for="seller in areaSellers" :key="'area-'+seller.username"
          :href="'/store/'+seller.username" class="gs-card hscroll-card">
          <div class="gs-cover">
            <img :src="seller.cover||'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=700&q=80'" :alt="seller.name" class="gs-img">
            <div class="gs-cover-grad"></div>
            <span class="gs-count-pill">{{ seller.item_count }} items</span>
            <div class="gs-cats">
              <span v-for="cat in (seller.categories||[]).slice(0,3)" :key="cat" class="gs-cat-pill">{{ cat }}</span>
            </div>
          </div>
          <div class="gs-body">
            <div class="gs-seller-row">
              <div class="gs-avatar-wrap">
                <img :src="seller.avatar||'https://i.pravatar.cc/48?u='+seller.username" :alt="seller.name" class="gs-avatar">
                <span class="gs-online-dot"></span>
              </div>
              <div style="min-width:0;flex:1;">
                <div class="gs-seller-name">{{ seller.name }}'s Garage Sale</div>
                <div class="gs-seller-handle">@{{ seller.username }}</div>
              </div>
            </div>
            <div class="gs-meta-row">
              <div style="display:flex;align-items:center;gap:3px;">
                <svg width="11" height="11" fill="#f59e0b" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                <span style="font-size:0.75rem;font-weight:700;color:#1A1A1A;">{{ seller.rating }}</span>
              </div>
              <span class="gs-divider"></span>
              <span style="font-size:0.72rem;color:#9ca3af;">{{ seller.distance }} mi</span>
              <span class="gs-divider"></span>
              <span style="font-size:0.72rem;color:#9ca3af;"><strong style="color:#1A1A1A;">{{ seller.item_count }}</strong> items</span>
            </div>
            <span class="gs-cta">Visit Sale <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg></span>
          </div>
        </a>
      </div>
    </div>
  </section>

  <!-- ───────────────────────────────────────────
       5. ALL GARAGE SALES
  ─────────────────────────────────────────── -->
  <section style="background:#fff;" @click.stop>

    <!-- Filter bar -->
    <div class="filter-bar">
      <div class="filter-bar-inner">
        <div class="filter-left">
          <!-- Category -->
          <div style="position:relative;">
            <button @click.stop="showCategoryPanel=!showCategoryPanel;showSortDropdown=false" class="filter-btn">
              <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 4h13M3 8h9m-9 4h9m5-4v12m0 0l-4-4m4 4l4-4"/></svg>
              {{ activeCategory === 'All' ? 'All Categories' : activeCategory }}
            </button>
            <div v-if="showCategoryPanel" class="dropdown-panel">
              <button v-for="cat in allCategories" :key="cat"
                @click.stop="activeCategory=cat;showCategoryPanel=false"
                :class="['dropdown-item', {active: activeCategory===cat}]">{{ cat }}</button>
            </div>
          </div>
          <!-- Sort -->
          <div style="position:relative;">
            <button @click.stop="showSortDropdown=!showSortDropdown;showCategoryPanel=false" class="filter-btn">
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
        <p style="font-size:0.82rem;color:#9ca3af;font-weight:600;margin:0;white-space:nowrap;">
          <strong style="color:#1A1A1A;">{{ filtered.length }}</strong> garage sales
        </p>
      </div>
    </div>

    <div class="section-inner" style="padding-top:28px;padding-bottom:0;">
      <h2 class="section-title">All Garage Sales</h2>
      <p class="section-sub" style="margin-top:4px;">Browse every garage sale on Swapy</p>
    </div>

    <div class="section-inner" style="padding-top:20px;padding-bottom:72px;">

      <!-- Skeleton -->
      <div v-if="skeletonLoading" class="main-grid">
        <div v-for="n in 6" :key="n" class="skeleton-card">
          <div class="skeleton" style="height:185px;"></div>
          <div style="padding:16px 18px;">
            <div style="display:flex;gap:10px;margin-bottom:12px;">
              <div class="skeleton" style="width:42px;height:42px;border-radius:50%;flex-shrink:0;"></div>
              <div style="flex:1;"><div class="skeleton" style="height:13px;width:70%;border-radius:4px;margin-bottom:6px;"></div><div class="skeleton" style="height:10px;width:40%;border-radius:4px;"></div></div>
            </div>
            <div class="skeleton" style="height:36px;border-radius:999px;"></div>
          </div>
        </div>
      </div>

      <!-- Grid -->
      <div v-else class="main-grid">
        <a v-for="seller in filtered" :key="seller.username"
          :href="'/store/'+seller.username" class="gs-card">
          <div class="gs-cover gs-cover--tall">
            <img :src="seller.cover||'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=700&q=80'" :alt="seller.name" class="gs-img">
            <div class="gs-cover-grad"></div>
            <span class="gs-count-pill">{{ seller.item_count }} items</span>
            <div class="gs-cats">
              <span v-for="cat in (seller.categories||[]).slice(0,3)" :key="cat" class="gs-cat-pill">{{ cat }}</span>
            </div>
          </div>
          <div class="gs-body gs-body--tall">
            <div class="gs-seller-row">
              <div class="gs-avatar-wrap">
                <img :src="seller.avatar||'https://i.pravatar.cc/48?u='+seller.username" :alt="seller.name" class="gs-avatar gs-avatar--lg">
                <span class="gs-online-dot"></span>
              </div>
              <div style="min-width:0;flex:1;">
                <div class="gs-seller-name gs-seller-name--lg">{{ seller.name }}'s Garage Sale</div>
                <div class="gs-seller-handle">@{{ seller.username }}</div>
              </div>
              <span style="font-size:0.7rem;color:#9ca3af;white-space:nowrap;flex-shrink:0;">{{ seller.active_since }}</span>
            </div>
            <div class="gs-meta-row gs-meta-row--border">
              <div style="display:flex;align-items:center;gap:4px;">
                <svg width="12" height="12" fill="#f59e0b" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                <span style="font-size:0.8rem;font-weight:700;color:#1A1A1A;">{{ seller.rating }}</span>
              </div>
              <span class="gs-divider"></span>
              <div style="display:flex;align-items:center;gap:4px;">
                <svg width="11" height="11" fill="#9ca3af" viewBox="0 0 24 24"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z"/></svg>
                <span style="font-size:0.78rem;color:#9ca3af;">{{ seller.distance }} mi away</span>
              </div>
              <span class="gs-divider"></span>
              <span style="font-size:0.78rem;color:#9ca3af;"><strong style="color:#1A1A1A;">{{ seller.item_count }}</strong> items</span>
            </div>
            <span class="gs-cta gs-cta--full">Visit Sale <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg></span>
          </div>
        </a>
      </div>

      <!-- Empty -->
      <div v-if="filtered.length === 0 && !skeletonLoading" style="text-align:center;padding:80px 0;">
        <div style="font-size:3rem;margin-bottom:16px;">🏷️</div>
        <h3 style="font-size:1.125rem;font-weight:800;color:#1A1A1A;margin-bottom:8px;">No garage sales found</h3>
        <p style="font-size:0.875rem;color:#9ca3af;margin-bottom:20px;">Try a different search or category.</p>
        <button @click="search='';activeCategory='All'" class="clear-btn">Clear Filters</button>
      </div>

      <!-- Load more -->
      <div v-if="filtered.length > 0" style="text-align:center;margin-top:48px;">
        <button @click="loadMore" :disabled="skeletonLoading" class="load-more-btn">
          <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" d="M19 9l-7 7-7-7"/></svg>
          Load More
        </button>
        <p style="margin-top:12px;font-size:0.78rem;color:#9ca3af;">Showing {{ filtered.length }} garage sales</p>
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

/* ── Garage sale card ── */
.gs-card {
  background: #fff;
  border-radius: 18px;
  overflow: hidden;
  border: 1px solid #EDE8E0;
  box-shadow: 0 4px 20px rgba(0,0,0,0.07);
  text-decoration: none;
  display: flex;
  flex-direction: column;
  transition: border-color .2s, box-shadow .2s, transform .2s;
}
.gs-card:hover {
  border-color: #ED730C;
  box-shadow: 0 10px 36px rgba(237,115,12,0.12);
  transform: translateY(-3px);
}
.gs-card:hover .gs-img { transform: scale(1.05); }

/* ── Cover image ── */
.gs-cover {
  position: relative;
  height: 160px;
  overflow: hidden;
  background: #f3f4f6;
  flex-shrink: 0;
}
.gs-cover--tall { height: 185px; }
.gs-img {
  width: 100%; height: 100%; object-fit: cover;
  display: block; transition: transform .4s;
}
.gs-cover-grad {
  position: absolute; inset: 0;
  background: linear-gradient(to top, rgba(0,0,0,0.55) 0%, transparent 55%);
}
.gs-count-pill {
  position: absolute; top: 11px; right: 11px;
  background: rgba(0,0,0,0.52); color: #fff;
  font-size: 0.68rem; font-weight: 800;
  padding: 4px 11px; border-radius: 999px;
  backdrop-filter: blur(6px); letter-spacing: .03em;
  font-family: 'DM Sans', sans-serif;
}
.gs-cats {
  position: absolute; bottom: 11px; left: 12px;
  display: flex; gap: 5px; flex-wrap: wrap;
}
.gs-promoted {
  position: absolute; top: 11px; left: 12px;
  display: flex; align-items: center; gap: 3px;
  background: #1A1A1A; color: #fff;
  font-size: 0.58rem; font-weight: 800;
  padding: 4px 9px; border-radius: 999px;
  letter-spacing: .04em; text-transform: uppercase;
  font-family: 'DM Sans', sans-serif;
}
.gs-cat-pill {
  background: rgba(255,255,255,0.92); color: #3A3330;
  font-size: 0.62rem; font-weight: 700;
  padding: 3px 9px; border-radius: 6px;
  letter-spacing: .04em; backdrop-filter: blur(4px);
  font-family: 'DM Sans', sans-serif;
}

/* ── Card body ── */
.gs-body { padding: 14px 16px 16px; display: flex; flex-direction: column; flex: 1; }
.gs-body--tall { padding: 18px 20px 20px; }

.gs-seller-row { display: flex; align-items: center; gap: 10px; margin-bottom: 10px; }
.gs-avatar-wrap { position: relative; flex-shrink: 0; }
.gs-avatar { width: 38px; height: 38px; border-radius: 50%; object-fit: cover; border: 2px solid #fff; box-shadow: 0 2px 8px rgba(0,0,0,0.12); }
.gs-avatar--lg { width: 46px; height: 46px; }
.gs-online-dot { position: absolute; bottom: 0; right: 0; width: 10px; height: 10px; background: #22c55e; border-radius: 50%; border: 2px solid #fff; }
.gs-seller-name { font-size: 0.875rem; font-weight: 800; color: #1A1A1A; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; line-height: 1.2; margin-bottom: 2px; }
.gs-seller-name--lg { font-size: 0.9375rem; }
.gs-seller-handle { font-size: 0.72rem; color: #9ca3af; }

.gs-meta-row { display: flex; align-items: center; gap: 12px; padding: 8px 0; border-top: 1px solid #f3f4f6; margin-bottom: 10px; }
.gs-meta-row--border { border-bottom: 1px solid #f3f4f6; margin-bottom: 16px; }
.gs-divider { width: 1px; height: 12px; background: #EDE8E0; display: inline-block; }

.gs-cta {
  display: flex; align-items: center; justify-content: center; gap: 6px;
  background: #ED730C; color: #fff;
  font-size: 0.8rem; font-weight: 800;
  padding: 9px 14px; border-radius: 999px;
  letter-spacing: .03em; box-shadow: 0 4px 12px rgba(237,115,12,0.28);
  font-family: 'DM Sans', sans-serif;
  transition: all .15s;
}
.gs-cta--full { flex: 1; font-size: 0.8125rem; padding: 11px 16px; }
.gs-cta:hover { background: #d4620a; box-shadow: 0 6px 18px rgba(237,115,12,0.40); transform: translateY(-1px); }

/* ── Main grid ── */
.main-grid { display: grid; gap: 22px; }

/* ── Skeleton ── */
.skeleton {
  background: linear-gradient(90deg, #f3f4f6 25%, #e9eaec 50%, #f3f4f6 75%);
  background-size: 1200px 100%;
  animation: shimmer 1.4s ease-in-out infinite;
}
.skeleton-card { background: #fff; border-radius: 18px; overflow: hidden; border: 1px solid #EDE8E0; }

/* ── Filter bar ── */
.filter-bar { background: #fff; border-bottom: 1px solid #EBEBEB; padding: 12px 40px; }
.filter-bar-inner { max-width: 1680px; margin: 0 auto; display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap; }
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

/* ── Sticky search responsive ── */
.sticky-search-mobile { display: none; }
@media (max-width: 767px) {
  .sticky-search-desktop { display: none !important; }
  .sticky-search-mobile  { display: flex !important; }
}

/* ── Hero search responsive ── */
.hero-search-wrap { max-width: 760px; margin: 0 auto 20px; }
.hero-search-desktop { background: #fff; border-radius: 999px; display: flex; align-items: center; padding: 6px 6px 6px 20px; box-shadow: 0 8px 32px rgba(0,0,0,0.12); border: 1.5px solid #EBEBEB; }
.hero-search-mobile  { display: none; background: #fff; border-radius: 999px; align-items: center; gap: 10px; padding: 10px 8px 10px 18px; box-shadow: 0 8px 32px rgba(0,0,0,0.12); border: 1.5px solid #EBEBEB; }
@media (max-width: 767px) {
  .hero-search-desktop { display: none !important; }
  .hero-search-mobile  { display: flex !important; }
}
</style>
