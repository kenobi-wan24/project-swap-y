<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'

const el       = document.getElementById('browse-app')
const allItems = ref(JSON.parse(el?.dataset.listings || '[]'))

function parseDataset(key) {
  try { return JSON.parse(el?.dataset[key] || '[]') } catch { return [] }
}

// ── sticky search on scroll ────────────────────────────────────────────────────
const scrollY = ref(0)
const showStickySearch = computed(() => scrollY.value > 280)
function onScroll() {
  scrollY.value = window.scrollY
  const slot = document.getElementById('nav-sticky-search')
  if (slot) slot.classList.toggle('open', scrollY.value > 280)
}
onMounted(() => window.addEventListener('scroll', onScroll, { passive: true }))
onUnmounted(() => window.removeEventListener('scroll', onScroll))

// ── filters ────────────────────────────────────────────────────────────────────
const search       = ref('')
const searchInput  = ref('')
const activeTab    = ref('All')
const valueMax     = ref(5000)
const distance     = ref(50)
const itemType     = ref('Physical Good')
const sortBy       = ref('Recent First')
const viewMode     = ref('grid')
const skeletonLoading = ref(false)

const showCatDropdown    = ref(false)
const showFiltersPanel   = ref(false)
const showSortDropdown   = ref(false)

const categories  = ['All', 'Electronics', 'Clothing', 'Collectibles', 'Furniture', 'Books', 'Outdoor', 'Gaming', 'Photography', 'Home', 'Fashion']
const sortOptions = ['Recent First', 'Value: Low to High', 'Value: High to Low', 'Distance: Nearest', 'Best Match']

function closeAllPanels() {
  showCatDropdown.value  = false
  showFiltersPanel.value = false
  showSortDropdown.value = false
}

// ── above-the-fold listings (shown immediately below hero) ─────────────────────
const aboveFoldItems = [
  { id:'af1', category:'Electronics', condition:'Like New', title:'iPhone 14 Pro Max 256GB', value:900,  wants:'Gaming Laptop',    badge:'Hot Swap', badgeColor:'#ED730C', owner:'Marcus C.', avatar:'MC', avatarColor:'#ED730C', image:'https://images.unsplash.com/photo-1591337676887-a217a6970a8a?w=600&q=80', match:98 },
  { id:'af2', category:'Gaming',      condition:'Good',     title:'ASUS ROG Gaming Laptop',  value:1200, wants:'iPhone or MacBook', badge:'Trending', badgeColor:'#8b5cf6', owner:'Juno K.',  avatar:'JK', avatarColor:'#8b5cf6', image:'https://images.unsplash.com/photo-1603302576837-37561b2e2302?w=600&q=80', match:91 },
  { id:'af3', category:'Photography', condition:'Like New', title:'Sony A7 III Full Frame',  value:1800, wants:'Canon R5 / Lenses', badge:'Verified', badgeColor:'#2563eb', owner:'Chris P.', avatar:'CP', avatarColor:'#2563eb', image:'https://images.unsplash.com/photo-1516035069371-29a1b244cc32?w=600&q=80', match:77 },
  { id:'af4', category:'Home',        condition:'Good',     title:'Dyson V11 Vacuum',        value:320,  wants:'Air Purifier',     badge:null,       badgeColor:'',        owner:'Sara J.',  avatar:'SJ', avatarColor:'#f59e0b', image:'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=600&q=80', match:88 },
  { id:'af5', category:'Fashion',     condition:'Mint',     title:'Supreme Box Logo Hoodie', value:380,  wants:'Sneakers / Caps',  badge:null,       badgeColor:'',        owner:'Elena R.', avatar:'ER', avatarColor:'#14b8a6', image:'https://images.unsplash.com/photo-1556821840-3a63f15732ce?w=600&q=80', match:85 },
  { id:'af6', category:'Outdoor',     condition:'Good',     title:'Trek Mountain Bike 29"',  value:700,  wants:'Surfboard / Kayak', badge:'New',      badgeColor:'#149189', owner:'Alex R.',  avatar:'AR', avatarColor:'#22c55e', image:'https://images.unsplash.com/photo-1485965120184-e220f721d03e?w=600&q=80', match:79 },
]

// ── featured swaps ─────────────────────────────────────────────────────────────
const fakeFeaturedItems = [
  { id:'f1', badge:'Hot Swap',  badgeColor:'#ED730C', category:'Electronics', title:'iPhone 14 Pro Max 256GB', desc:'Excellent condition, unlocked. Includes original box and all accessories.', owner:'Marcus C.', avatar:'MC', avatarColor:'#ED730C', image:'https://images.unsplash.com/photo-1591337676887-a217a6970a8a?w=600&q=80' },
  { id:'f2', badge:'Trending',  badgeColor:'#8b5cf6', category:'Gaming',      title:'ASUS ROG Gaming Laptop',  desc:'RTX 3070, 16GB RAM, 1TB SSD. Perfect for gaming and creative work.',      owner:'Juno K.',   avatar:'JK', avatarColor:'#8b5cf6', image:'https://images.unsplash.com/photo-1603302576837-37561b2e2302?w=600&q=80' },
  { id:'f3', badge:'Verified',  badgeColor:'#2563eb', category:'Photography', title:'Sony A7 III Full Frame',  desc:'Only 3k shutter count. Kit lens and extra battery included.',               owner:'Chris P.',  avatar:'CP', avatarColor:'#2563eb', image:'https://images.unsplash.com/photo-1516035069371-29a1b244cc32?w=600&q=80' },
  { id:'f4', badge:'New',       badgeColor:'#149189', category:'Home',        title:'Dyson V11 Vacuum',        desc:'Powerful cordless vacuum. All attachments included. Works perfectly.',     owner:'Sara J.',   avatar:'SJ', avatarColor:'#f59e0b', image:'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=600&q=80' },
  { id:'f5', badge:'',          badgeColor:'',        category:'Fashion',     title:'Supreme Box Logo Hoodie', desc:'Size L, worn twice. No stains or defects. Rare FW22 colourway.',           owner:'Elena R.',  avatar:'ER', avatarColor:'#14b8a6', image:'https://images.unsplash.com/photo-1556821840-3a63f15732ce?w=600&q=80' },
]
const _realFeatured = parseDataset('featured')
const featuredItems = ref(_realFeatured.length ? _realFeatured : fakeFeaturedItems)

// ── near you ───────────────────────────────────────────────────────────────────
const fakeNearYouItems = [
  { id:'n1', category:'Electronics', title:'Vintage Mech Keyboard',   condition:'Like New', badge:'NEW',    badgeColor:'#1a1a1a', desc:'Custom switches, PBT keycaps. Daily driver for 6 months.',          wants:'DSLR Camera or Studio Mic',  owner:'Marcus Chen',  avatar:'MC', avatarColor:'#ED730C', city:'Brooklyn, NY',    distance:'0.3 mi', image:'https://images.unsplash.com/photo-1618384887929-16ec33fab9ef?w=600&q=80' },
  { id:'n2', category:'Fashion',     title:'Leather Camera Bag',      condition:'Good',     badge:null,     badgeColor:'',        desc:'Genuine leather. Fits mirrorless body and two lenses comfortably.', wants:'Hard-shell Travel Suitcase', owner:'Elena Rossi',   avatar:'ER', avatarColor:'#ec4899', city:'Queens, NY',      distance:'0.8 mi', image:'https://images.unsplash.com/photo-1548036328-c9fa89d128fa?w=600&q=80' },
  { id:'n3', category:'Electronics', title:'Noise Cancel Headphones', condition:'Like New', badge:'3-WAY',  badgeColor:'#149189', desc:'Sony WH-1000XM4. Barely used, includes case and all cables.',         wants:'Graphic Tablet / iPad Air',  owner:'Liam Smith',    avatar:'LS', avatarColor:'#8b5cf6', city:'Jersey City, NJ', distance:'1.2 mi', image:'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=600&q=80' },
  { id:'n4', category:'Home',        title:'Artisan Ceramic Set',     condition:'Mint',     badge:null,     badgeColor:'',        desc:'Handmade set of 6. Food-safe glazing. No chips or cracks.',           wants:'Outdoor Planter / Rug',      owner:'Sara Jenkins',  avatar:'SJ', avatarColor:'#f59e0b', city:'Manhattan, NY',   distance:'1.5 mi', image:'https://images.unsplash.com/photo-1565193566173-7a0ee3dbe261?w=600&q=80' },
  { id:'n5', category:'Collectibles',title:'Vinyl Records (50+)',     condition:'Good',     badge:null,     badgeColor:'',        desc:'Mixed genres, 60s–90s. Rare finds, all in original sleeves.',         wants:'Cassette Player',            owner:'Leo B.',        avatar:'LB', avatarColor:'#14b8a6', city:'Brooklyn, NY',    distance:'1.9 mi', image:'https://images.unsplash.com/photo-1603048588665-791ca8aea617?w=600&q=80' },
]
const _realNearYou = parseDataset('nearYou')
const nearYouItems = ref(_realNearYou.length ? _realNearYou : fakeNearYouItems)

// ── trending ───────────────────────────────────────────────────────────────────
const fakeTrendingItems = [
  { id:'t1', label:'TRENDING', badge:'HOT',  badgeColor:'#ED730C', title:'Minimalist Watch',      wants:'Headphones',   city:'Los Angeles, CA', image:'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=200&q=80' },
  { id:'t2', label:'COMMUNITY',badge:'',     badgeColor:'',        title:'Rare Book Collection',  wants:'Vinyl Player', city:'Chicago, IL',     image:'https://images.unsplash.com/photo-1524995997946-a1c2e315a42f?w=200&q=80' },
  { id:'t3', label:'GEAR',     badge:'SWAP', badgeColor:'#149189', title:'Pro Gaming Mouse',      wants:'Desk Lamp',    city:'Seattle, WA',     image:'https://images.unsplash.com/photo-1527864550417-7fd91fc51a46?w=200&q=80' },
]
const _realTrending = parseDataset('trending')
const trendingItems = ref(_realTrending.length ? _realTrending : fakeTrendingItems)

// ── today's picks ──────────────────────────────────────────────────────────────
const fakeTodaysPicks = [
  { id:'tp1', category:'Electronics', condition:'Like New', title:'iPad Pro 12.9" M2',        desc:'Barely used, with Apple Pencil and Magic Keyboard case.', value:950,  wants:'MacBook Air M2',    owner:'James K.',  avatar:'JK', avatarColor:'#6366f1', image:'https://images.unsplash.com/photo-1544244015-0df4b3ffc6b0?w=600&q=80', match:94, badge:'Daily Pick', badgeColor:'#ED730C' },
  { id:'tp2', category:'Photography', condition:'Good',     title:'DJI Mavic 3 Drone',        desc:'Includes 3 batteries, controller, and carry case. Flown 12x.', value:1400, wants:'Sony Camera',    owner:'Priya M.', avatar:'PM', avatarColor:'#ec4899', image:'https://images.unsplash.com/photo-1473968512647-3e447244af8f?w=600&q=80', match:87, badge:'',           badgeColor:'' },
  { id:'tp3', category:'Outdoor',     condition:'Mint',     title:'Trek Mountain Bike 29"',   desc:'Carbon frame, Shimano gears, bought this year.', value:700,  wants:'Surfboard or Kayak', owner:'Alex R.',  avatar:'AR', avatarColor:'#22c55e', image:'https://images.unsplash.com/photo-1485965120184-e220f721d03e?w=600&q=80', match:79, badge:'',           badgeColor:'' },
]
const _realTodaysPicks = parseDataset('todaysPicks')
const todaysPicks = ref(_realTodaysPicks.length ? _realTodaysPicks : fakeTodaysPicks)

// ── main grid ──────────────────────────────────────────────────────────────────
const mainGrid = ref([
  { id:'g1', category:'Electronics', condition:'Like New', title:'iPhone 14 Pro Max 256GB', desc:'Excellent condition, unlocked, all accessories included.', value:900,  location:'New York, NY',     match:98, wants:'Gaming Laptop',   badge:'Hot Swap',  badgeColor:'#ED730C', owner:'Marcus C.', avatar:'MC', avatarColor:'#ED730C', image:'https://images.unsplash.com/photo-1591337676887-a217a6970a8a?w=600&q=80', rating:4.9 },
  { id:'g2', category:'Gaming',      condition:'Good',     title:'ASUS ROG Gaming Laptop',  desc:'RTX 3070, 16GB RAM, 1TB SSD. Perfect for gaming & design.', value:1200, location:'Los Angeles, CA',  match:91, wants:'iPhone, MacBook',  badge:'Trending',  badgeColor:'#8b5cf6', owner:'Juno K.',  avatar:'JK', avatarColor:'#8b5cf6', image:'https://images.unsplash.com/photo-1603302576837-37561b2e2302?w=600&q=80', rating:5.0 },
  { id:'g3', category:'Fashion',     condition:'Mint',     title:'Supreme Box Logo Hoodie', desc:'Size L, worn twice. No stains or defects. Rare colourway.', value:380,  location:'Chicago, IL',      match:85, wants:'Sneakers / Caps',  badge:'',          badgeColor:'',        owner:'Elena R.', avatar:'ER', avatarColor:'#14b8a6', image:'https://images.unsplash.com/photo-1556821840-3a63f15732ce?w=600&q=80', rating:4.7 },
  { id:'g4', category:'Photography', condition:'Like New', title:'Sony A7 III Full Frame',  desc:'Only 3k shutter count. Includes kit lens and extra battery.', value:1800, location:'Austin, TX',       match:77, wants:'Canon R5, Lenses',  badge:'Verified',  badgeColor:'#2563eb', owner:'Chris P.', avatar:'CP', avatarColor:'#2563eb', image:'https://images.unsplash.com/photo-1516035069371-29a1b244cc32?w=600&q=80', rating:4.8 },
  { id:'g5', category:'Home',        condition:'Good',     title:'Dyson V11 Vacuum',        desc:'Powerful cordless. All attachments included. Works perfectly.', value:320,  location:'Miami, FL',        match:88, wants:'Air Purifier',     badge:'',          badgeColor:'',        owner:'Sara J.', avatar:'SJ', avatarColor:'#f59e0b', image:'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=600&q=80', rating:4.6 },
  { id:'g6', category:'Collectibles',condition:'Mint',     title:'Vinyl Records (50+)',      desc:'Mixed genres. 60s–90s. Rare finds and classic albums.', value:200,  location:'Portland, OR',     match:72, wants:'Cassette Player',  badge:'',          badgeColor:'',        owner:'Leo B.',  avatar:'LB', avatarColor:'#ec4899', image:'https://images.unsplash.com/photo-1603048588665-791ca8aea617?w=600&q=80', rating:4.5 },
])

// ── computed ───────────────────────────────────────────────────────────────────
const filtered = computed(() => {
  let list = allItems.value.length ? allItems.value : mainGrid.value
  if (activeTab.value !== 'All') list = list.filter(i => i.category === activeTab.value)
  if (search.value.trim()) list = list.filter(i => i.title?.toLowerCase().includes(search.value.toLowerCase()))
  list = list.filter(i => (i.value || 0) <= valueMax.value)
  if (sortBy.value === 'Value: Low to High') list = [...list].sort((a,b) => a.value - b.value)
  if (sortBy.value === 'Value: High to Low') list = [...list].sort((a,b) => b.value - a.value)
  if (sortBy.value === 'Best Match')         list = [...list].sort((a,b) => b.match - a.match)
  return list
})

const activeFilterCount = computed(() => {
  let n = 0
  if (activeTab.value !== 'All') n++
  if (valueMax.value < 5000) n++
  if (distance.value < 50) n++
  if (itemType.value !== 'Physical Good') n++
  return n
})

// ── wishlist ───────────────────────────────────────────────────────────────────
const wishlisted = ref(new Set())
function toggleWish(id) {
  const s = new Set(wishlisted.value)
  s.has(id) ? s.delete(id) : s.add(id)
  wishlisted.value = s
}

function doSearch() { search.value = searchInput.value }

async function loadMore() {
  skeletonLoading.value = true
  await new Promise(r => setTimeout(r, 900))
  skeletonLoading.value = false
}
</script>

<template>
<div style="min-height:100vh;background:#FAF8F5;font-family:'DM Sans',sans-serif;" @click="closeAllPanels">

  <!-- ═══════════════════════════════════════════
       STICKY NAV SEARCH — teleported into #nav-sticky-search on scroll
  ═══════════════════════════════════════════ -->
  <Teleport to="#nav-sticky-search">
    <div style="max-width:760px;margin:0 auto;" @click.stop>

      <!-- Exact same search pill as the hero -->
      <div style="background:#fff;border-radius:999px;display:flex;align-items:center;padding:6px 6px 6px 20px;box-shadow:0 8px 32px rgba(0,0,0,0.12);border:1.5px solid #EBEBEB;max-width:760px;margin:0 auto;">
        <div style="flex:1;display:flex;align-items:center;gap:8px;border-right:1px solid #EBEBEB;padding-right:16px;">
          <svg width="15" height="15" fill="none" stroke="#9ca3af" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
          <input v-model="searchInput" type="text" placeholder="What do you have to swap?"
            style="border:none;outline:none;font-size:0.875rem;color:#1A1A1A;background:transparent;font-family:'DM Sans',sans-serif;width:100%;"
            @keydown.enter="doSearch">
        </div>
        <div style="display:flex;align-items:center;gap:6px;padding:0 16px;border-right:1.5px solid #EBEBEB;">
          <svg width="14" height="14" fill="none" stroke="#9ca3af" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h8m-8 6h16"/></svg>
          <select v-model="activeTab" @click.stop style="border:none;outline:none;font-size:0.875rem;color:#1A1A1A;background:transparent;font-family:'DM Sans',sans-serif;cursor:pointer;appearance:none;padding-right:6px;">
            <option v-for="c in categories" :key="c" :value="c">{{ c === 'All' ? 'Category' : c }}</option>
          </select>
        </div>
        <div style="display:flex;align-items:center;gap:6px;padding:0 14px;">
          <svg width="13" height="13" fill="#9ca3af" viewBox="0 0 24 24"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>
          <input type="text" placeholder="Location" style="border:none;outline:none;font-size:0.875rem;color:#1A1A1A;background:transparent;font-family:'DM Sans',sans-serif;width:90px;">
        </div>
        <button @click="doSearch"
          style="background:#ED730C;color:#fff;border:none;border-radius:999px;padding:12px 28px;font-size:0.875rem;font-weight:800;cursor:pointer;font-family:'DM Sans',sans-serif;white-space:nowrap;transition:background 0.15s;box-shadow:0 4px 14px rgba(237,115,12,0.4);"
          onmouseover="this.style.background='#d4620a'" onmouseout="this.style.background='#ED730C'">Search</button>
      </div>

    </div>
  </Teleport>

  <!-- ═══════════════════════════════════════════
       HERO — compact so listings show above fold
  ═══════════════════════════════════════════ -->
  <section style="padding:52px 24px 40px;background:#FAF8F5;text-align:center;">
    <div style="max-width:860px;margin:0 auto;">

      <!-- Headline -->
      <div style="display:flex;align-items:baseline;gap:16px;flex-wrap:wrap;margin-bottom:28px;justify-content:center;">
        <h1 style="font-size:clamp(2.2rem,5vw,3.6rem);font-weight:900;line-height:1.05;letter-spacing:-.03em;color:#1A1A1A;margin:0;">Don't buy it.</h1>
        <h1 style="font-size:clamp(2.2rem,5vw,3.6rem);font-weight:900;line-height:1.05;letter-spacing:-.03em;color:#ED730C;margin:0;">Swap it.</h1>
      </div>

      <!-- Search pill -->
      <div style="background:#fff;border-radius:999px;display:flex;align-items:center;padding:6px 6px 6px 20px;box-shadow:0 8px 32px rgba(0,0,0,0.12);border:1.5px solid #EBEBEB;max-width:760px;margin:0 auto 20px;">
        <div style="flex:1;display:flex;align-items:center;gap:8px;border-right:1px solid #EBEBEB;padding-right:16px;">
          <svg width="15" height="15" fill="none" stroke="#9ca3af" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
          <input v-model="searchInput" type="text" placeholder="What do you have to swap?"
            style="border:none;outline:none;font-size:0.875rem;color:#1A1A1A;background:transparent;font-family:'DM Sans',sans-serif;width:100%;"
            @keydown.enter="doSearch">
        </div>
        <div style="display:flex;align-items:center;gap:6px;padding:0 16px;border-right:1.5px solid #EBEBEB;">
          <svg width="14" height="14" fill="none" stroke="#9ca3af" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h8m-8 6h16"/></svg>
          <select v-model="activeTab" @click.stop style="border:none;outline:none;font-size:0.875rem;color:#1A1A1A;background:transparent;font-family:'DM Sans',sans-serif;cursor:pointer;appearance:none;padding-right:6px;">
            <option v-for="c in categories" :key="c" :value="c">{{ c === 'All' ? 'Category' : c }}</option>
          </select>
        </div>
        <div style="display:flex;align-items:center;gap:6px;padding:0 14px;">
          <svg width="13" height="13" fill="#9ca3af" viewBox="0 0 24 24"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>
          <input type="text" placeholder="Location" style="border:none;outline:none;font-size:0.875rem;color:#1A1A1A;background:transparent;font-family:'DM Sans',sans-serif;width:90px;">
        </div>
        <button @click="doSearch"
          style="background:#ED730C;color:#fff;border:none;border-radius:999px;padding:12px 28px;font-size:0.875rem;font-weight:800;cursor:pointer;font-family:'DM Sans',sans-serif;white-space:nowrap;transition:background 0.15s;box-shadow:0 4px 14px rgba(237,115,12,0.4);"
          onmouseover="this.style.background='#d4620a'" onmouseout="this.style.background='#ED730C'">Search</button>
      </div>

      <!-- Category pill shortcuts -->
      <div style="display:flex;gap:8px;flex-wrap:wrap;justify-content:center;">
        <button v-for="cat in categories.slice(0,8)" :key="cat" @click="activeTab = cat"
          :style="{padding:'7px 16px',borderRadius:'999px',fontSize:'0.78rem',fontWeight:'600',fontFamily:'\'DM Sans\',sans-serif',cursor:'pointer',border:activeTab===cat?'none':'1px solid #EBEBEB',background:activeTab===cat?'#1A1A1A':'#fff',color:activeTab===cat?'#fff':'#4b5563',boxShadow:activeTab===cat?'0 4px 12px rgba(0,0,0,0.15)':'none',transition:'all 0.15s'}">
          {{ cat }}
        </button>
      </div>
    </div>
  </section>

  <!-- ═══════════════════════════════════════════
       ABOVE-THE-FOLD LISTINGS
       Visible immediately on page load — no scroll needed
  ═══════════════════════════════════════════ -->
  <section style="padding:0 24px 48px;background:#FAF8F5;">
    <div style="max-width:1280px;margin:0 auto;">

      <!-- Section header -->
      <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;">
        <div>
          <p style="font-size:0.68rem;font-weight:800;letter-spacing:.12em;color:#ED730C;text-transform:uppercase;margin:0 0 3px;">Near You</p>
          <h2 style="font-size:1.5rem;font-weight:900;color:#1A1A1A;margin:0;letter-spacing:-.02em;">Popular swaps in your area</h2>
        </div>
        <a href="#" style="font-size:0.8rem;font-weight:700;color:#ED730C;text-decoration:none;white-space:nowrap;">See all →</a>
      </div>

      <!-- 6-column listing grid — same pattern as Airbnb -->
      <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(190px,1fr));gap:16px;">
        <div v-for="item in aboveFoldItems" :key="item.id"
          class="swapy-card"
          style="background:#fff;border-radius:16px;overflow:hidden;border:1px solid #EDE8E0;cursor:pointer;display:flex;flex-direction:column;">

          <!-- Image -->
          <div style="position:relative;aspect-ratio:4/3;overflow:hidden;background:#f3f4f6;">
            <img :src="item.image" :alt="item.title" class="card-img" style="width:100%;height:100%;object-fit:cover;transition:transform 0.35s;">

            <!-- Badge -->
            <span v-if="item.badge"
              :style="{position:'absolute',top:'9px',left:'9px',background:item.badgeColor,color:'#fff',fontSize:'0.58rem',fontWeight:'800',padding:'3px 8px',borderRadius:'5px',letterSpacing:'.05em',textTransform:'uppercase'}">
              {{ item.badge }}
            </span>

            <!-- Heart -->
            <button @click.stop="toggleWish(item.id)" class="wish-btn"
              style="position:absolute;top:8px;right:8px;width:28px;height:28px;background:rgba(255,255,255,0.92);border:none;border-radius:50%;display:flex;align-items:center;justify-content:center;cursor:pointer;box-shadow:0 1px 4px rgba(0,0,0,0.10);">
              <svg :style="{width:'12px',height:'12px',fill:wishlisted.has(item.id)?'#ED730C':'none',stroke:wishlisted.has(item.id)?'#ED730C':'#6b7280',strokeWidth:'2'}" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
              </svg>
            </button>
          </div>

          <!-- Card body — compact -->
          <div style="padding:12px 14px 14px;flex:1;display:flex;flex-direction:column;gap:4px;">
            <!-- Category + condition -->
            <div style="display:flex;align-items:center;gap:6px;">
              <span style="font-size:0.6rem;font-weight:800;letter-spacing:.07em;color:#9ca3af;text-transform:uppercase;">{{ item.category }}</span>
              <span style="width:2px;height:2px;border-radius:50%;background:#d1d5db;display:inline-block;"></span>
              <span style="font-size:0.6rem;font-weight:700;color:#149189;">{{ item.condition }}</span>
            </div>

            <!-- Title -->
            <h3 style="font-size:0.875rem;font-weight:800;color:#1A1A1A;line-height:1.3;margin:0;display:-webkit-box;-webkit-line-clamp:1;-webkit-box-orient:vertical;overflow:hidden;">{{ item.title }}</h3>

            <!-- Wants line -->
            <p style="font-size:0.75rem;color:#9ca3af;margin:0;">Wants: <span style="color:#ED730C;font-weight:600;">{{ item.wants }}</span></p>

            <!-- Match + value row -->
            <div style="display:flex;align-items:center;justify-content:space-between;margin-top:6px;padding-top:8px;border-top:1px solid #f3f4f6;">
              <span style="font-size:0.72rem;font-weight:700;color:#149189;">{{ item.match }}% match</span>
              <span style="font-size:0.875rem;font-weight:900;color:#1A1A1A;">${{ item.value?.toLocaleString() }}</span>
            </div>

            <!-- CTA -->
            <a :href="'/item/'+item.id" class="swap-btn"
              style="display:block;text-align:center;background:#ED730C;color:#fff;font-size:0.775rem;font-weight:800;padding:9px 0;border-radius:999px;text-decoration:none;letter-spacing:.03em;font-family:'DM Sans',sans-serif;transition:all 0.15s;margin-top:6px;">
              Swap Now
            </a>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- ═══════════════════════════════════════════
       TOP FEATURED SWAPS
  ═══════════════════════════════════════════ -->
  <section style="padding:0 0 48px;background:#FAF8F5;">
    <div style="max-width:1280px;margin:0 auto;padding:0 24px;">
      <div style="display:flex;align-items:flex-end;justify-content:space-between;margin-bottom:20px;">
        <div>
          <p style="font-size:0.68rem;font-weight:800;letter-spacing:.12em;color:#ED730C;text-transform:uppercase;margin:0 0 4px;">Curated</p>
          <h2 style="font-size:1.5rem;font-weight:900;color:#1A1A1A;margin:0;letter-spacing:-.02em;">Top Featured Swaps</h2>
        </div>
        <a href="#" style="font-size:0.8rem;font-weight:700;color:#ED730C;text-decoration:none;">See all →</a>
      </div>

      <div class="hscroll" style="display:flex;gap:16px;overflow-x:auto;padding-bottom:8px;">
        <div v-for="item in featuredItems" :key="item.id"
          class="swapy-card"
          style="background:#fff;border-radius:16px;overflow:hidden;border:1px solid #f3f4f6;flex-shrink:0;width:260px;display:flex;flex-direction:column;">

          <div style="position:relative;aspect-ratio:16/10;overflow:hidden;background:#f3f4f6;">
            <img :src="item.image" :alt="item.title" class="card-img" style="width:100%;height:100%;object-fit:cover;transition:transform .35s;">
            <div style="position:absolute;top:10px;left:10px;display:flex;gap:6px;">
              <span v-if="item.badge"
                :style="{background:item.badgeColor,color:'#fff',fontSize:'0.62rem',fontWeight:'800',padding:'4px 9px',borderRadius:'6px',letterSpacing:'.05em',textTransform:'uppercase'}">
                {{ item.badge }}
              </span>
              <span style="background:rgba(255,255,255,0.92);color:#3A3330;font-size:0.62rem;font-weight:700;padding:4px 9px;border-radius:6px;letter-spacing:.04em;text-transform:uppercase;backdrop-filter:blur(4px);">
                {{ item.category }}
              </span>
            </div>
            <button @click.stop="toggleWish(item.id)" class="wish-btn"
              style="position:absolute;top:9px;right:9px;width:30px;height:30px;background:rgba(255,255,255,0.92);border:none;border-radius:50%;display:flex;align-items:center;justify-content:center;cursor:pointer;box-shadow:0 2px 6px rgba(0,0,0,0.10);">
              <svg :style="{width:'13px',height:'13px',fill:wishlisted.has(item.id)?'#ED730C':'none',stroke:wishlisted.has(item.id)?'#ED730C':'#6b7280',strokeWidth:'2'}" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
              </svg>
            </button>
          </div>

          <div style="padding:16px 18px 18px;flex:1;display:flex;flex-direction:column;">
            <h3 style="font-size:1rem;font-weight:700;color:#1A1A1A;line-height:1.3;margin:0 0 7px;">{{ item.title }}</h3>
            <p style="font-size:0.8375rem;color:#6b7280;line-height:1.55;margin:0 0 14px;flex:1;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">{{ item.desc }}</p>
            <div style="display:flex;align-items:center;gap:8px;margin-bottom:14px;padding-top:10px;border-top:1px solid #f3f4f6;">
              <div style="display:flex;align-items:center;">
                <div :style="{width:'26px',height:'26px',borderRadius:'50%',background:item.avatarColor,border:'2px solid #fff',display:'flex',alignItems:'center',justifyContent:'center',fontSize:'0.55rem',fontWeight:'800',color:'#fff',flexShrink:0}">{{ item.avatar }}</div>
                <div style="width:26px;height:26px;border-radius:50%;background:#149189;border:2px solid #fff;margin-left:-8px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                  <svg width="10" height="10" fill="none" stroke="#fff" stroke-width="2.5" stroke-linecap="round" viewBox="0 0 24 24"><path d="M7 16V4m0 0L3 8m4-4l4 4M17 8v12m0 0l4-4m-4 4l-4-4"/></svg>
                </div>
              </div>
              <span style="font-size:0.78rem;font-weight:600;color:#6b7280;">{{ item.owner }}</span>
            </div>
            <div style="display:flex;align-items:center;gap:10px;">
              <a :href="'/item/'+item.id" class="swap-btn"
                style="flex:1;display:flex;align-items:center;justify-content:center;background:#ED730C;color:#fff;font-size:0.8125rem;font-weight:700;padding:11px 16px;border-radius:999px;text-decoration:none;font-family:'DM Sans',sans-serif;transition:background .15s;">
                Swap Now
              </a>
              <button @click.stop="toggleWish(item.id)"
                :style="{width:'38px',height:'38px',border:'1.5px solid',borderRadius:'10px',background:'#fff',cursor:'pointer',display:'flex',alignItems:'center',justifyContent:'center',flexShrink:0,borderColor:wishlisted.has(item.id)?'#ED730C':'#e5e7eb',transition:'all .15s'}">
                <svg :style="{width:'15px',height:'15px',fill:wishlisted.has(item.id)?'#ED730C':'none',stroke:wishlisted.has(item.id)?'#ED730C':'#9ca3af',strokeWidth:'2'}" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/>
                </svg>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- ═══════════════════════════════════════════
       NEAR YOU (horizontal scroll)
  ═══════════════════════════════════════════ -->
  <section style="padding:0 0 48px;background:#FAF8F5;">
    <div style="max-width:1280px;margin:0 auto;padding:0 24px;">
      <div style="display:flex;align-items:flex-end;justify-content:space-between;margin-bottom:20px;">
        <div>
          <p style="font-size:0.68rem;font-weight:800;letter-spacing:.12em;color:#ED730C;text-transform:uppercase;margin:0 0 4px;">Local</p>
          <h2 style="font-size:1.5rem;font-weight:900;color:#1A1A1A;margin:0;letter-spacing:-.02em;">Popular Near You</h2>
        </div>
        <a href="#" style="font-size:0.8rem;font-weight:700;color:#ED730C;text-decoration:none;">See all →</a>
      </div>

      <div class="hscroll" style="display:flex;gap:16px;overflow-x:auto;padding-bottom:8px;">
        <div v-for="item in nearYouItems" :key="item.id"
          class="swapy-card"
          style="background:#fff;border-radius:16px;overflow:hidden;border:1px solid #f3f4f6;flex-shrink:0;width:240px;display:flex;flex-direction:column;">

          <div style="position:relative;aspect-ratio:16/10;overflow:hidden;background:#f3f4f6;">
            <img :src="item.image" :alt="item.title" class="card-img" style="width:100%;height:100%;object-fit:cover;transition:transform .35s;">
            <div style="position:absolute;top:10px;left:10px;display:flex;gap:6px;">
              <span v-if="item.badge"
                :style="{background:item.badgeColor,color:'#fff',fontSize:'0.62rem',fontWeight:'800',padding:'4px 9px',borderRadius:'6px',letterSpacing:'.05em',textTransform:'uppercase'}">
                {{ item.badge }}
              </span>
              <span style="background:rgba(255,255,255,0.92);color:#3A3330;font-size:0.62rem;font-weight:700;padding:4px 9px;border-radius:6px;letter-spacing:.04em;text-transform:uppercase;backdrop-filter:blur(4px);">
                {{ item.category }}
              </span>
            </div>
            <span style="position:absolute;bottom:9px;left:9px;background:rgba(0,0,0,0.52);color:#fff;font-size:0.62rem;font-weight:700;padding:3px 8px;border-radius:5px;display:flex;align-items:center;gap:3px;backdrop-filter:blur(4px);">
              <svg width="8" height="8" fill="#fff" viewBox="0 0 24 24"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z"/></svg>
              {{ item.distance }}
            </span>
            <button @click.stop="toggleWish(item.id)" class="wish-btn"
              style="position:absolute;top:9px;right:9px;width:30px;height:30px;background:rgba(255,255,255,0.92);border:none;border-radius:50%;display:flex;align-items:center;justify-content:center;cursor:pointer;box-shadow:0 2px 6px rgba(0,0,0,0.10);">
              <svg :style="{width:'13px',height:'13px',fill:wishlisted.has(item.id)?'#ED730C':'none',stroke:wishlisted.has(item.id)?'#ED730C':'#6b7280',strokeWidth:'2'}" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
              </svg>
            </button>
          </div>

          <div style="padding:16px 18px 18px;flex:1;display:flex;flex-direction:column;">
            <div style="margin-bottom:8px;">
              <span style="font-size:0.62rem;font-weight:700;color:#149189;background:#EDFAF9;padding:2px 7px;border-radius:4px;">{{ item.condition }}</span>
            </div>
            <h3 style="font-size:1rem;font-weight:700;color:#1A1A1A;line-height:1.3;margin:0 0 7px;">{{ item.title }}</h3>
            <p style="font-size:0.8375rem;color:#6b7280;line-height:1.55;margin:0 0 14px;flex:1;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">{{ item.desc }}</p>
            <div style="display:flex;align-items:center;gap:8px;margin-bottom:14px;padding-top:10px;border-top:1px solid #f3f4f6;">
              <div style="display:flex;align-items:center;">
                <div :style="{width:'26px',height:'26px',borderRadius:'50%',background:item.avatarColor,border:'2px solid #fff',display:'flex',alignItems:'center',justifyContent:'center',fontSize:'0.55rem',fontWeight:'800',color:'#fff',flexShrink:0}">{{ item.avatar }}</div>
                <div style="width:26px;height:26px;border-radius:50%;background:#149189;border:2px solid #fff;margin-left:-8px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                  <svg width="10" height="10" fill="none" stroke="#fff" stroke-width="2.5" stroke-linecap="round" viewBox="0 0 24 24"><path d="M7 16V4m0 0L3 8m4-4l4 4M17 8v12m0 0l4-4m-4 4l-4-4"/></svg>
                </div>
              </div>
              <span style="font-size:0.78rem;font-weight:600;color:#6b7280;">{{ item.owner }}</span>
            </div>
            <div style="display:flex;align-items:center;gap:10px;">
              <a :href="'/item/'+item.id" class="swap-btn"
                style="flex:1;display:flex;align-items:center;justify-content:center;background:#ED730C;color:#fff;font-size:0.8125rem;font-weight:700;padding:11px 16px;border-radius:999px;text-decoration:none;font-family:'DM Sans',sans-serif;transition:background .15s;">
                Swap Now
              </a>
              <button @click.stop="toggleWish(item.id)"
                :style="{width:'38px',height:'38px',border:'1.5px solid',borderRadius:'10px',background:'#fff',cursor:'pointer',display:'flex',alignItems:'center',justifyContent:'center',flexShrink:0,borderColor:wishlisted.has(item.id)?'#ED730C':'#e5e7eb',transition:'all .15s'}">
                <svg :style="{width:'15px',height:'15px',fill:wishlisted.has(item.id)?'#ED730C':'none',stroke:wishlisted.has(item.id)?'#ED730C':'#9ca3af',strokeWidth:'2'}" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/>
                </svg>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- ═══════════════════════════════════════════
       AI MATCHMAKER BANNER
  ═══════════════════════════════════════════ -->
  <section style="padding:0 24px 48px;">
    <div style="max-width:1280px;margin:0 auto;">
      <div style="background:#F5EDE0;border-radius:24px;padding:40px 52px;display:flex;align-items:center;justify-content:space-between;gap:32px;overflow:hidden;position:relative;">
        <div style="position:absolute;right:0;top:0;bottom:0;width:40%;background:radial-gradient(ellipse at 80% 50%,rgba(237,115,12,0.08) 0%,transparent 70%);pointer-events:none;"></div>
        <div style="position:relative;z-index:1;max-width:480px;">
          <div style="display:inline-flex;align-items:center;gap:7px;background:#fff;border:1px solid #EDE8E0;border-radius:999px;padding:5px 14px;margin-bottom:18px;">
            <svg width="11" height="11" fill="#ED730C" viewBox="0 0 24 24"><path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/></svg>
            <span style="font-size:0.68rem;font-weight:800;color:#ED730C;letter-spacing:.1em;text-transform:uppercase;">AI Matchmaker</span>
          </div>
          <h2 style="font-size:clamp(1.5rem,3vw,2rem);font-weight:900;color:#1A1A1A;line-height:1.15;letter-spacing:-.025em;margin:0 0 12px;">Finding your perfect swap<br>is now automated.</h2>
          <p style="font-size:0.875rem;color:#5c5751;line-height:1.65;margin:0 0 24px;max-width:380px;">Our intelligent algorithm analyzes what you have and what you want to create effortless 2-way and 3-way swap cycles.</p>
          <button style="background:#1A1A1A;color:#fff;border:none;border-radius:999px;padding:13px 28px;font-size:0.875rem;font-weight:800;cursor:pointer;font-family:'DM Sans',sans-serif;letter-spacing:.02em;transition:all 0.15s;"
            onmouseover="this.style.background='#ED730C'" onmouseout="this.style.background='#1A1A1A'">
            Try Smart Match
          </button>
        </div>
        <div style="position:relative;z-index:1;flex-shrink:0;">
          <div style="width:160px;height:160px;border-radius:50%;background:rgba(237,115,12,0.10);display:flex;align-items:center;justify-content:center;">
            <div style="width:108px;height:108px;border-radius:50%;background:rgba(237,115,12,0.16);display:flex;align-items:center;justify-content:center;">
              <svg width="44" height="44" fill="none" viewBox="0 0 24 24"><path d="M12 2l2.4 7.4H22l-6.2 4.5 2.4 7.4L12 17l-6.2 4.3 2.4-7.4L2 9.4h7.6z" fill="#ED730C" opacity=".9"/></svg>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- ═══════════════════════════════════════════
       TRENDING SWAPS
  ═══════════════════════════════════════════ -->
  <section style="padding:0 24px 48px;">
    <div style="max-width:1280px;margin:0 auto;">
      <div style="display:flex;align-items:flex-end;justify-content:space-between;margin-bottom:20px;">
        <div>
          <p style="font-size:0.68rem;font-weight:800;letter-spacing:.12em;color:#9ca3af;text-transform:uppercase;margin:0 0 4px;">Popular</p>
          <h2 style="font-size:1.5rem;font-weight:900;color:#1A1A1A;margin:0;letter-spacing:-.02em;">Trending Swaps</h2>
        </div>
        <a href="#" style="font-size:0.8rem;font-weight:700;color:#ED730C;text-decoration:none;">View all →</a>
      </div>
      <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:14px;">
        <div v-for="item in trendingItems" :key="item.id"
          class="swapy-card"
          style="background:#fff;border-radius:16px;padding:14px;display:flex;align-items:center;gap:14px;border:1px solid #EDE8E0;cursor:pointer;">
          <div style="width:72px;height:72px;border-radius:12px;overflow:hidden;flex-shrink:0;background:#f3f4f6;">
            <img :src="item.image" :alt="item.title" style="width:100%;height:100%;object-fit:cover;transition:transform 0.4s;">
          </div>
          <div style="flex:1;min-width:0;">
            <div style="display:flex;align-items:center;gap:6px;margin-bottom:4px;">
              <span style="font-size:0.6rem;font-weight:800;letter-spacing:.09em;color:#9ca3af;text-transform:uppercase;">{{ item.label }}</span>
              <span v-if="item.badge" :style="{background:item.badgeColor,color:'#fff',fontSize:'0.58rem',fontWeight:'800',padding:'2px 8px',borderRadius:'4px',letterSpacing:'.06em',textTransform:'uppercase'}">{{ item.badge }}</span>
            </div>
            <p style="font-size:0.875rem;font-weight:800;color:#1A1A1A;margin:0 0 2px;line-height:1.2;">{{ item.title }}</p>
            <p style="font-size:0.72rem;color:#9ca3af;margin:0 0 3px;">Looking for: <span style="color:#ED730C;font-weight:600;">{{ item.wants }}</span></p>
            <p style="font-size:0.68rem;color:#9ca3af;margin:0;display:flex;align-items:center;gap:3px;">
              <svg width="9" height="9" fill="#9ca3af" viewBox="0 0 24 24"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z"/></svg>
              {{ item.city }}
            </p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- ═══════════════════════════════════════════
       TODAY'S PICKS
  ═══════════════════════════════════════════ -->
  <section style="padding:0 24px 48px;">
    <div style="max-width:1280px;margin:0 auto;">
      <div style="display:flex;align-items:flex-end;justify-content:space-between;margin-bottom:20px;">
        <div>
          <p style="font-size:0.68rem;font-weight:800;letter-spacing:.12em;color:#149189;text-transform:uppercase;margin:0 0 4px;">Updated Daily</p>
          <h2 style="font-size:1.5rem;font-weight:900;color:#1A1A1A;margin:0;letter-spacing:-.02em;">Today's Picks</h2>
        </div>
        <a href="#" style="font-size:0.8rem;font-weight:700;color:#ED730C;text-decoration:none;">See all →</a>
      </div>

      <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:18px;">
        <div v-for="item in todaysPicks" :key="item.id"
          class="swapy-card"
          style="background:#fff;border-radius:20px;overflow:hidden;border:1px solid #EDE8E0;cursor:pointer;display:flex;flex-direction:column;">

          <div style="position:relative;height:190px;overflow:hidden;background:#f3f4f6;">
            <img :src="item.image" :alt="item.title" class="card-img" style="width:100%;height:100%;object-fit:cover;transition:transform 0.4s;">
            <span v-if="item.badge"
              :style="{position:'absolute',top:'10px',left:'10px',background:item.badgeColor,color:'#fff',fontSize:'0.6rem',fontWeight:'800',padding:'4px 10px',borderRadius:'999px',letterSpacing:'.06em',textTransform:'uppercase'}">
              {{ item.badge }}
            </span>
            <button @click.stop="toggleWish(item.id)" class="wish-btn"
              style="position:absolute;top:10px;right:10px;width:30px;height:30px;background:rgba(255,255,255,0.92);border:none;border-radius:50%;display:flex;align-items:center;justify-content:center;cursor:pointer;">
              <svg :style="{width:'13px',height:'13px',fill:wishlisted.has(item.id)?'#ED730C':'none',stroke:wishlisted.has(item.id)?'#ED730C':'#6b7280',strokeWidth:'2'}" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
              </svg>
            </button>
          </div>

          <div style="padding:16px 18px 18px;flex:1;display:flex;flex-direction:column;">
            <div style="display:flex;align-items:center;gap:7px;margin-bottom:7px;">
              <span style="font-size:0.62rem;font-weight:800;letter-spacing:.08em;color:#9ca3af;text-transform:uppercase;">{{ item.category }}</span>
              <span style="width:3px;height:3px;border-radius:50%;background:#d1d5db;"></span>
              <span style="font-size:0.62rem;font-weight:700;color:#149189;background:#EDFAF9;padding:2px 7px;border-radius:4px;">{{ item.condition }}</span>
            </div>
            <h3 style="font-size:1rem;font-weight:800;color:#1A1A1A;line-height:1.3;margin:0 0 5px;">{{ item.title }}</h3>
            <p style="font-size:0.78rem;color:#9ca3af;line-height:1.55;margin:0 0 14px;flex:1;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">{{ item.desc }}</p>

            <div style="display:flex;align-items:center;gap:8px;margin-bottom:14px;padding-top:10px;border-top:1px solid #f3f4f6;">
              <div style="position:relative;width:36px;height:24px;flex-shrink:0;">
                <div :style="{width:'24px',height:'24px',borderRadius:'50%',background:item.avatarColor,color:'#fff',fontSize:'0.58rem',fontWeight:'800',display:'flex',alignItems:'center',justifyContent:'center',position:'absolute',left:'0',zIndex:2,border:'1.5px solid #fff'}">{{ item.avatar }}</div>
                <div style="width:22px;height:22px;border-radius:50%;background:#149189;display:flex;align-items:center;justify-content:center;position:absolute;left:12px;z-index:1;border:1.5px solid #fff;">
                  <svg width="10" height="10" fill="white" viewBox="0 0 40 40"><path d="M28 8L38 14L28 20" stroke="white" stroke-width="4" stroke-linecap="round" fill="none"/><path d="M38 14H14" stroke="white" stroke-width="4" stroke-linecap="round"/><path d="M12 32L2 26L12 20" stroke="white" stroke-width="4" stroke-linecap="round" fill="none"/><path d="M2 26H26" stroke="white" stroke-width="4" stroke-linecap="round"/></svg>
                </div>
              </div>
              <span v-if="item.match !== null && !item.is_own" style="font-size:0.78rem;font-weight:700;color:#149189;">{{ item.match }}% Swap Potential</span>
              <span v-else-if="item.is_own" style="font-size:0.78rem;font-weight:700;color:#9ca3af;">Your Listing</span>
            </div>

            <div style="display:flex;align-items:center;gap:10px;">
              <a :href="'/item/'+item.id" class="swap-btn"
                style="flex:1;text-align:center;background:#ED730C;color:#fff;font-size:0.8rem;font-weight:800;padding:12px 0;border-radius:999px;text-decoration:none;letter-spacing:.04em;font-family:'DM Sans',sans-serif;transition:all 0.15s;">
                Swap Now
              </a>
              <button @click.stop="toggleWish(item.id)"
                :style="{width:'40px',height:'40px',flexShrink:0,background:wishlisted.has(item.id)?'#fff4ec':'#f9f9f8',border:'1px solid '+(wishlisted.has(item.id)?'#fdd1ac':'#EDE8E0'),borderRadius:'50%',display:'flex',alignItems:'center',justifyContent:'center',cursor:'pointer',transition:'all 0.15s'}">
                <svg :style="{width:'15px',height:'15px',fill:wishlisted.has(item.id)?'#ED730C':'none',stroke:wishlisted.has(item.id)?'#ED730C':'#9ca3af',strokeWidth:'2'}" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                </svg>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- ═══════════════════════════════════════════
       FILTER BAR — sticky
  ═══════════════════════════════════════════ -->
  <div style="background:#FAF8F5;border-top:1px solid #EDE8E0;border-bottom:1px solid #EDE8E0;padding:12px 24px;position:sticky;top:64px;z-index:20;" @click.stop>
    <div style="max-width:1280px;margin:0 auto;display:flex;align-items:center;gap:10px;justify-content:space-between;">

      <div style="display:flex;align-items:center;gap:8px;">
        <!-- Categories -->
        <div style="position:relative;">
          <button @click.stop="showCatDropdown=!showCatDropdown;showFiltersPanel=false;showSortDropdown=false"
            :style="{display:'flex',alignItems:'center',gap:7,padding:'8px 16px',borderRadius:'999px',border:'1.5px solid',borderColor:activeTab!=='All'?'#ED730C':'#d1cdc7',background:activeTab!=='All'?'#fff4ec':'#fff',fontFamily:'\'DM Sans\',sans-serif',fontSize:'0.82rem',fontWeight:'700',color:activeTab!=='All'?'#ED730C':'#1A1A1A',cursor:'pointer',transition:'all .15s'}">
            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h8m-8 6h16"/></svg>
            {{ activeTab === 'All' ? 'Categories' : activeTab }}
            <svg width="11" height="11" fill="currentColor" viewBox="0 0 20 20"><path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"/></svg>
          </button>
          <div v-if="showCatDropdown"
            style="position:absolute;top:calc(100% + 8px);left:0;background:#fff;border:1px solid #EDE8E0;border-radius:16px;padding:8px;min-width:200px;box-shadow:0 8px 32px rgba(0,0,0,0.12);z-index:100;">
            <button v-for="cat in categories" :key="cat"
              @click.stop="activeTab=cat;showCatDropdown=false"
              :style="{display:'block',width:'100%',textAlign:'left',padding:'9px 14px',borderRadius:'10px',border:'none',background:activeTab===cat?'#fff4ec':'transparent',color:activeTab===cat?'#ED730C':'#1A1A1A',fontSize:'0.85rem',fontWeight:activeTab===cat?'700':'500',fontFamily:'\'DM Sans\',sans-serif',cursor:'pointer'}">
              {{ cat }}
            </button>
          </div>
        </div>

        <!-- Filters -->
        <div style="position:relative;">
          <button @click.stop="showFiltersPanel=!showFiltersPanel;showCatDropdown=false;showSortDropdown=false"
            :style="{display:'flex',alignItems:'center',gap:7,padding:'8px 16px',borderRadius:'999px',border:'1.5px solid',borderColor:activeFilterCount>0?'#ED730C':'#d1cdc7',background:activeFilterCount>0?'#fff4ec':'#fff',fontFamily:'\'DM Sans\',sans-serif',fontSize:'0.82rem',fontWeight:'700',color:activeFilterCount>0?'#ED730C':'#1A1A1A',cursor:'pointer',transition:'all .15s'}">
            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="4" y1="6" x2="20" y2="6"/><line x1="8" y1="12" x2="16" y2="12"/><line x1="11" y1="18" x2="13" y2="18"/></svg>
            Filters
            <span v-if="activeFilterCount > 0"
              style="background:#ED730C;color:#fff;font-size:0.62rem;font-weight:800;width:17px;height:17px;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;">
              {{ activeFilterCount }}
            </span>
          </button>
          <div v-if="showFiltersPanel"
            style="position:absolute;top:calc(100% + 8px);left:0;background:#fff;border:1px solid #EDE8E0;border-radius:20px;padding:24px;width:320px;box-shadow:0 8px 32px rgba(0,0,0,0.12);z-index:100;">
            <p style="font-size:0.7rem;font-weight:800;letter-spacing:.08em;text-transform:uppercase;color:#9ca3af;margin:0 0 12px;">Value Range</p>
            <div style="display:flex;justify-content:space-between;margin-bottom:8px;">
              <span style="font-size:0.82rem;color:#6b7280;">$0</span>
              <span style="font-size:0.82rem;font-weight:700;color:#1A1A1A;">${{ valueMax.toLocaleString() }}</span>
            </div>
            <input type="range" v-model.number="valueMax" min="50" max="20000" step="50" style="width:100%;accent-color:#ED730C;margin-bottom:20px;">
            <p style="font-size:0.7rem;font-weight:800;letter-spacing:.08em;text-transform:uppercase;color:#9ca3af;margin:0 0 12px;">Max Distance</p>
            <div style="display:flex;justify-content:space-between;margin-bottom:8px;">
              <span style="font-size:0.82rem;color:#6b7280;">1 mi</span>
              <span style="font-size:0.82rem;font-weight:700;color:#1A1A1A;">{{ distance }} mi</span>
            </div>
            <input type="range" v-model.number="distance" min="1" max="100" style="width:100%;accent-color:#ED730C;margin-bottom:20px;">
            <p style="font-size:0.7rem;font-weight:800;letter-spacing:.08em;text-transform:uppercase;color:#9ca3af;margin:0 0 12px;">Item Type</p>
            <div style="display:flex;flex-direction:column;gap:8px;">
              <label v-for="t in ['Physical Good','Service','Real Estate']" :key="t"
                style="display:flex;align-items:center;gap:10px;cursor:pointer;">
                <input type="radio" v-model="itemType" :value="t" style="accent-color:#ED730C;">
                <span style="font-size:0.85rem;font-weight:600;color:#1A1A1A;">{{ t }}</span>
              </label>
            </div>
          </div>
        </div>

        <!-- Sort -->
        <div style="position:relative;">
          <button @click.stop="showSortDropdown=!showSortDropdown;showCatDropdown=false;showFiltersPanel=false"
            style="display:flex;align-items:center;gap:7px;padding:8px 16px;border-radius:999px;border:1.5px solid #d1cdc7;background:#fff;font-family:'DM Sans',sans-serif;font-size:0.82rem;font-weight:700;color:#1A1A1A;cursor:pointer;">
            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12"/></svg>
            Sort
            <svg width="11" height="11" fill="currentColor" viewBox="0 0 20 20"><path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"/></svg>
          </button>
          <div v-if="showSortDropdown"
            style="position:absolute;top:calc(100% + 8px);left:0;background:#fff;border:1px solid #EDE8E0;border-radius:16px;padding:8px;min-width:220px;box-shadow:0 8px 32px rgba(0,0,0,0.12);z-index:100;">
            <button v-for="s in sortOptions" :key="s"
              @click.stop="sortBy=s;showSortDropdown=false"
              :style="{display:'block',width:'100%',textAlign:'left',padding:'9px 14px',borderRadius:'10px',border:'none',background:sortBy===s?'#fff4ec':'transparent',color:sortBy===s?'#ED730C':'#1A1A1A',fontSize:'0.85rem',fontWeight:sortBy===s?'700':'500',fontFamily:'\'DM Sans\',sans-serif',cursor:'pointer'}">
              {{ s }}
            </button>
          </div>
        </div>
      </div>

      <div style="display:flex;align-items:center;gap:14px;">
        <p style="font-size:0.82rem;color:#9ca3af;font-weight:600;margin:0;white-space:nowrap;">
          <strong style="color:#1A1A1A;">{{ filtered.length }}</strong> items
        </p>
        <div style="display:inline-flex;border:1.5px solid #EDE8E0;border-radius:10px;overflow:hidden;background:#fff;">
          <button @click="viewMode='grid'" :style="{padding:'7px 12px',background:viewMode==='grid'?'#1A1A1A':'transparent',border:'none',cursor:'pointer',display:'flex',alignItems:'center',transition:'background 0.15s'}">
            <svg width="13" height="13" fill="none" :stroke="viewMode==='grid'?'#fff':'#9ca3af'" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
          </button>
          <button @click="viewMode='list'" :style="{padding:'7px 12px',background:viewMode==='list'?'#1A1A1A':'transparent',border:'none',borderLeft:'1px solid #EDE8E0',cursor:'pointer',display:'flex',alignItems:'center',transition:'background 0.15s'}">
            <svg width="13" height="13" fill="none" :stroke="viewMode==='list'?'#fff':'#9ca3af'" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- ═══════════════════════════════════════════
       MAIN GRID
  ═══════════════════════════════════════════ -->
  <div style="max-width:1280px;margin:0 auto;padding:28px 24px 72px;">

    <!-- Skeleton -->
    <div v-if="skeletonLoading" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:20px;">
      <div v-for="n in 6" :key="n" style="background:#fff;border-radius:20px;overflow:hidden;border:1px solid #EDE8E0;">
        <div class="skeleton" style="height:200px;width:100%;"></div>
        <div style="padding:16px 18px 18px;">
          <div class="skeleton" style="height:12px;width:35%;border-radius:4px;margin-bottom:10px;"></div>
          <div class="skeleton" style="height:18px;width:80%;border-radius:4px;margin-bottom:8px;"></div>
          <div class="skeleton" style="height:12px;width:95%;border-radius:4px;margin-bottom:20px;"></div>
          <div class="skeleton" style="height:42px;width:100%;border-radius:999px;"></div>
        </div>
      </div>
    </div>

    <!-- Grid view -->
    <div v-else-if="viewMode==='grid'" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:20px;">
      <div v-for="item in filtered" :key="item.id"
        class="swapy-card"
        style="background:#fff;border-radius:20px;overflow:hidden;border:1px solid #EDE8E0;cursor:pointer;display:flex;flex-direction:column;">

        <div style="position:relative;height:200px;overflow:hidden;background:#f3f4f6;flex-shrink:0;">
          <img :src="item.image" :alt="item.title" class="card-img" style="width:100%;height:100%;object-fit:cover;transition:transform 0.4s;">
          <span v-if="item.badge"
            :style="{position:'absolute',top:'10px',left:'10px',background:item.badgeColor,color:'#fff',fontSize:'0.6rem',fontWeight:'800',padding:'4px 10px',borderRadius:'999px',letterSpacing:'.06em',textTransform:'uppercase'}">
            {{ item.badge }}
          </span>
          <button @click.stop="toggleWish(item.id)" class="wish-btn"
            style="position:absolute;top:10px;right:10px;width:30px;height:30px;background:rgba(255,255,255,0.92);border:none;border-radius:50%;display:flex;align-items:center;justify-content:center;cursor:pointer;">
            <svg :style="{width:'13px',height:'13px',fill:wishlisted.has(item.id)?'#ED730C':'none',stroke:wishlisted.has(item.id)?'#ED730C':'#6b7280',strokeWidth:'2'}" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
            </svg>
          </button>
        </div>

        <div style="padding:16px 18px 18px;display:flex;flex-direction:column;flex:1;">
          <div style="display:flex;align-items:center;gap:7px;margin-bottom:8px;">
            <span style="font-size:0.62rem;font-weight:800;letter-spacing:.08em;color:#9ca3af;text-transform:uppercase;">{{ item.category }}</span>
            <span style="width:3px;height:3px;border-radius:50%;background:#d1d5db;display:inline-block;"></span>
            <span style="font-size:0.62rem;font-weight:700;color:#149189;background:#EDFAF9;padding:2px 7px;border-radius:4px;">{{ item.condition }}</span>
          </div>
          <h3 style="font-size:0.9375rem;font-weight:800;color:#1A1A1A;line-height:1.3;margin:0 0 5px;">{{ item.title }}</h3>
          <p style="font-size:0.78rem;color:#9ca3af;line-height:1.55;margin:0 0 12px;flex:1;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">{{ item.desc }}</p>

          <div style="display:flex;align-items:center;gap:7px;margin-bottom:14px;padding-top:10px;border-top:1px solid #f3f4f6;">
            <div style="position:relative;width:36px;height:24px;flex-shrink:0;">
              <div :style="{width:'24px',height:'24px',borderRadius:'50%',background:item.avatarColor,color:'#fff',fontSize:'0.58rem',fontWeight:'800',display:'flex',alignItems:'center',justifyContent:'center',position:'absolute',left:'0',zIndex:2,border:'1.5px solid #fff'}">{{ item.avatar }}</div>
              <div style="width:22px;height:22px;border-radius:50%;background:#149189;display:flex;align-items:center;justify-content:center;position:absolute;left:12px;z-index:1;border:1.5px solid #fff;">
                <svg width="10" height="10" fill="white" viewBox="0 0 40 40"><path d="M28 8L38 14L28 20" stroke="white" stroke-width="4" stroke-linecap="round" fill="none"/><path d="M38 14H14" stroke="white" stroke-width="4" stroke-linecap="round"/><path d="M12 32L2 26L12 20" stroke="white" stroke-width="4" stroke-linecap="round" fill="none"/><path d="M2 26H26" stroke="white" stroke-width="4" stroke-linecap="round"/></svg>
              </div>
            </div>
            <span v-if="item.match !== null && !item.is_own" style="font-size:0.78rem;font-weight:700;color:#149189;">{{ item.match }}% Swap Potential</span>
            <span v-else-if="item.is_own" style="font-size:0.78rem;font-weight:700;color:#9ca3af;">Your Listing</span>
            <span v-else style="font-size:0.78rem;color:#9ca3af;">{{ item.owner }}</span>
          </div>

          <div style="display:flex;align-items:center;gap:10px;">
            <a :href="'/item/'+item.id" class="swap-btn"
              style="flex:1;text-align:center;background:#ED730C;color:#fff;font-size:0.8rem;font-weight:800;padding:12px 0;border-radius:999px;text-decoration:none;letter-spacing:.04em;font-family:'DM Sans',sans-serif;transition:all 0.15s;">
              Swap Now
            </a>
            <button @click.stop="toggleWish(item.id)"
              :style="{width:'40px',height:'40px',flexShrink:0,background:wishlisted.has(item.id)?'#fff4ec':'#f9f9f8',border:'1px solid '+(wishlisted.has(item.id)?'#fdd1ac':'#EDE8E0'),borderRadius:'50%',display:'flex',alignItems:'center',justifyContent:'center',cursor:'pointer',transition:'all 0.15s'}">
              <svg :style="{width:'15px',height:'15px',fill:wishlisted.has(item.id)?'#ED730C':'none',stroke:wishlisted.has(item.id)?'#ED730C':'#9ca3af',strokeWidth:'2'}" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
              </svg>
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- List view -->
    <div v-else style="display:flex;flex-direction:column;gap:12px;">
      <div v-for="item in filtered" :key="item.id"
        class="swapy-card"
        style="background:#fff;border-radius:16px;overflow:hidden;border:1px solid #EDE8E0;display:flex;align-items:center;cursor:pointer;">
        <div style="width:110px;height:90px;flex-shrink:0;overflow:hidden;background:#f3f4f6;">
          <img :src="item.image" :alt="item.title" style="width:100%;height:100%;object-fit:cover;">
        </div>
        <div style="flex:1;padding:14px 20px;display:flex;justify-content:space-between;align-items:center;gap:16px;">
          <div>
            <p style="font-size:0.62rem;font-weight:800;letter-spacing:.07em;color:#9ca3af;text-transform:uppercase;margin:0 0 3px;">{{ item.category }}</p>
            <h3 style="font-size:0.9rem;font-weight:800;color:#1A1A1A;margin:0 0 3px;">{{ item.title }}</h3>
            <p style="font-size:0.76rem;color:#9ca3af;margin:0;">Wants: <strong style="color:#ED730C;">{{ item.wants }}</strong></p>
          </div>
          <div style="text-align:right;flex-shrink:0;display:flex;flex-direction:column;align-items:flex-end;gap:8px;">
            <p style="font-size:1.05rem;font-weight:900;color:#ED730C;margin:0;">${{ item.value?.toLocaleString() }}</p>
            <a :href="'/item/'+item.id" style="background:#ED730C;color:#fff;font-size:0.75rem;font-weight:800;padding:8px 20px;border-radius:999px;text-decoration:none;font-family:'DM Sans',sans-serif;">Swap Now</a>
          </div>
        </div>
      </div>
    </div>

    <!-- Empty state -->
    <div v-if="filtered.length === 0 && !skeletonLoading" style="text-align:center;padding:80px 0;">
      <div style="font-size:3rem;margin-bottom:16px;">🔍</div>
      <h3 style="font-size:1.125rem;font-weight:700;color:#1A1A1A;margin-bottom:8px;">No items found</h3>
      <p style="font-size:0.875rem;color:#9ca3af;margin-bottom:20px;">Try adjusting your filters or search term.</p>
      <button @click="activeTab='All';valueMax=5000;distance=50;search=''"
        style="background:#ED730C;color:#fff;border:none;border-radius:999px;padding:11px 28px;font-size:0.85rem;font-weight:700;cursor:pointer;font-family:'DM Sans',sans-serif;">
        Clear Filters
      </button>
    </div>

    <!-- Load more -->
    <div v-if="filtered.length > 0" style="text-align:center;margin-top:48px;">
      <button @click="loadMore" :disabled="skeletonLoading"
        style="display:inline-flex;align-items:center;gap:8px;padding:13px 36px;background:#fff;border:1.5px solid #1A1A1A;border-radius:999px;font-size:0.82rem;font-weight:800;color:#1A1A1A;cursor:pointer;font-family:'DM Sans',sans-serif;letter-spacing:.04em;transition:all 0.15s;"
        onmouseover="this.style.background='#1A1A1A';this.style.color='#fff'"
        onmouseout="this.style.background='#fff';this.style.color='#1A1A1A'">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" d="M19 9l-7 7-7-7"/></svg>
        Load More
      </button>
      <p style="margin-top:12px;font-size:0.78rem;color:#9ca3af;">Showing {{ filtered.length }} of 2,492 swaps</p>
    </div>

  </div>

</div>
</template>

<style scoped>
@keyframes hpulse { 0%,100%{opacity:1;transform:scale(1)} 50%{opacity:.4;transform:scale(.75)} }
@keyframes shimmer { 0%{background-position:-600px 0} 100%{background-position:600px 0} }

.skeleton {
  background:linear-gradient(90deg,#f0ece6 25%,#e8e3dc 50%,#f0ece6 75%);
  background-size:1200px 100%;
  animation:shimmer 1.5s ease-in-out infinite;
}
.swapy-card { transition:border-color 0.2s,box-shadow 0.2s,transform 0.2s; }
.swapy-card:hover { border-color:#ED730C !important;box-shadow:0 10px 36px rgba(237,115,12,0.13) !important;transform:translateY(-3px); }
.swapy-card:hover .card-img { transform:scale(1.06); }
.wish-btn { transition:transform 0.15s; }
.wish-btn:hover { transform:scale(1.18); }
.swap-btn:hover { background:#d4620a !important;box-shadow:0 6px 18px rgba(237,115,12,0.40) !important; }
.hscroll::-webkit-scrollbar { display:none; }
.hscroll { scrollbar-width:none; }
</style>