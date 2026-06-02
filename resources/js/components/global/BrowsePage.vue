<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'

const el       = document.getElementById('browse-app')
const allItems = ref(JSON.parse(el?.dataset.listings || '[]'))

function parseDataset(key) {
  try { return JSON.parse(el?.dataset[key] || '[]') } catch { return [] }
}

// ── sticky search on scroll ────────────────────────────────────────────────────
const scrollY = ref(0)
function onScroll() {
  scrollY.value = window.scrollY
  const slot = document.getElementById('nav-sticky-search')
  if (slot) slot.classList.toggle('open', scrollY.value > 280)
}
onMounted(() => window.addEventListener('scroll', onScroll, { passive: true }))
onUnmounted(() => window.removeEventListener('scroll', onScroll))

// ── Nominatim geolocation (OpenStreetMap) ─────────────────────────────────────
const cityName = ref('')
const areaName = ref('')

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
  } catch {
    cityName.value = ''
    areaName.value = ''
  }
})

// ── filters ────────────────────────────────────────────────────────────────────
const search      = ref('')
const searchInput = ref('')
const activeTab   = ref('All')
const valueMax    = ref(5000)
const sortBy      = ref('Recent First')
const viewMode    = ref('grid')
const skeletonLoading = ref(false)

const showFiltersPanel = ref(false)
const showSortDropdown = ref(false)

const categories  = ['All', 'Electronics', 'Clothing', 'Collectibles', 'Furniture', 'Books', 'Outdoor', 'Gaming', 'Photography', 'Home', 'Fashion']
const sortOptions = ['Recent First', 'Value: Low to High', 'Value: High to Low', 'Best Match']

function closeAllPanels() {
  showFiltersPanel.value = false
  showSortDropdown.value = false
}

// ── section data pools ─────────────────────────────────────────────────────────
const featuredItems = ref((() => {
  const real = parseDataset('featured')
  return real.length ? real : [
    { id:'f1', category:'Electronics', condition:'Like New', title:'iPhone 14 Pro Max 256GB', wants:'Gaming Laptop',     value:900,  badge:'Featured', owner:'Marcus C.', avatar:'MC', avatarColor:'#ED730C', image:'https://images.unsplash.com/photo-1591337676887-a217a6970a8a?w=600&q=80' },
    { id:'f2', category:'Gaming',      condition:'Good',     title:'ASUS ROG Gaming Laptop',  wants:'iPhone or MacBook', value:1200, badge:'Featured', owner:'Juno K.',  avatar:'JK', avatarColor:'#8b5cf6', image:'https://images.unsplash.com/photo-1603302576837-37561b2e2302?w=600&q=80' },
    { id:'f3', category:'Photography', condition:'Like New', title:'Sony A7 III Full Frame',  wants:'Canon R5 / Lenses', value:1800, badge:'Featured', owner:'Chris P.', avatar:'CP', avatarColor:'#2563eb', image:'https://images.unsplash.com/photo-1516035069371-29a1b244cc32?w=600&q=80' },
    { id:'f4', category:'Home',        condition:'Good',     title:'Dyson V11 Vacuum',        wants:'Air Purifier',      value:320,  badge:'Featured', owner:'Sara J.',  avatar:'SJ', avatarColor:'#f59e0b', image:'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=600&q=80' },
    { id:'f5', category:'Fashion',     condition:'Mint',     title:'Supreme Box Logo Hoodie', wants:'Sneakers / Caps',   value:380,  badge:'Featured', owner:'Elena R.', avatar:'ER', avatarColor:'#14b8a6', image:'https://images.unsplash.com/photo-1556821840-3a63f15732ce?w=600&q=80' },
    { id:'f6', category:'Outdoor',     condition:'Good',     title:'Trek Mountain Bike 29"',  wants:'Surfboard / Kayak', value:700,  badge:'Featured', owner:'Alex R.',  avatar:'AR', avatarColor:'#22c55e', image:'https://images.unsplash.com/photo-1485965120184-e220f721d03e?w=600&q=80' },
  ]
})())

const recentItems = ref((() => {
  const real = parseDataset('recent')
  return real.length ? real : [
    { id:'r1', category:'Electronics', condition:'Like New', title:'iPad Pro 12.9" M2',        wants:'MacBook Air M2',     value:950,  badge:null, owner:'James K.',  avatar:'JK', avatarColor:'#6366f1', image:'https://images.unsplash.com/photo-1544244015-0df4b3ffc6b0?w=600&q=80' },
    { id:'r2', category:'Photography', condition:'Good',     title:'DJI Mavic 3 Drone',        wants:'Sony Camera',        value:1400, badge:null, owner:'Priya M.', avatar:'PM', avatarColor:'#ec4899', image:'https://images.unsplash.com/photo-1473968512647-3e447244af8f?w=600&q=80' },
    { id:'r3', category:'Outdoor',     condition:'Mint',     title:'Trek Mountain Bike 29"',   wants:'Surfboard or Kayak', value:700,  badge:null, owner:'Alex R.',  avatar:'AR', avatarColor:'#22c55e', image:'https://images.unsplash.com/photo-1485965120184-e220f721d03e?w=600&q=80' },
    { id:'r4', category:'Electronics', condition:'Good',     title:'MacBook Pro 14" M3',       wants:'Gaming PC Setup',    value:2100, badge:null, owner:'Sam T.',   avatar:'ST', avatarColor:'#3b82f6', image:'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?w=600&q=80' },
    { id:'r5', category:'Gaming',      condition:'Like New', title:'PS5 + 3 Games Bundle',    wants:'Xbox Series X',      value:550,  badge:null, owner:'Leo B.',   avatar:'LB', avatarColor:'#8b5cf6', image:'https://images.unsplash.com/photo-1607853202273-797f1c22a38e?w=600&q=80' },
    { id:'r6', category:'Fashion',     condition:'Good',     title:'Nike Air Jordan 1 Retro', wants:'Adidas Ultraboost',  value:220,  badge:null, owner:'Rina S.',  avatar:'RS', avatarColor:'#ef4444', image:'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=600&q=80' },
  ]
})())

const popularCityItems = ref((() => {
  const real = parseDataset('popularCity')
  return real.length ? real : [
    { id:'pc1', category:'Electronics', condition:'Like New', title:'Vintage Mech Keyboard',   wants:'DSLR Camera or Mic',   value:180, badge:null, owner:'Marcus Chen',  avatar:'MC', avatarColor:'#ED730C', image:'https://images.unsplash.com/photo-1618384887929-16ec33fab9ef?w=600&q=80' },
    { id:'pc2', category:'Fashion',     condition:'Good',     title:'Leather Camera Bag',      wants:'Hard-shell Suitcase',  value:120, badge:null, owner:'Elena Rossi',  avatar:'ER', avatarColor:'#ec4899', image:'https://images.unsplash.com/photo-1548036328-c9fa89d128fa?w=600&q=80' },
    { id:'pc3', category:'Electronics', condition:'Like New', title:'Sony WH-1000XM4',         wants:'Graphic Tablet / iPad',value:250, badge:null, owner:'Liam Smith',   avatar:'LS', avatarColor:'#8b5cf6', image:'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=600&q=80' },
    { id:'pc4', category:'Home',        condition:'Mint',     title:'Artisan Ceramic Set',     wants:'Outdoor Planter / Rug',value:90,  badge:null, owner:'Sara Jenkins', avatar:'SJ', avatarColor:'#f59e0b', image:'https://images.unsplash.com/photo-1565193566173-7a0ee3dbe261?w=600&q=80' },
    { id:'pc5', category:'Collectibles',condition:'Good',     title:'Vinyl Records (50+)',      wants:'Cassette Player',      value:200, badge:null, owner:'Leo B.',       avatar:'LB', avatarColor:'#14b8a6', image:'https://images.unsplash.com/photo-1603048588665-791ca8aea617?w=600&q=80' },
    { id:'pc6', category:'Outdoor',     condition:'Good',     title:'Camping Gear Bundle',     wants:'Fishing Equipment',    value:310, badge:null, owner:'Jake M.',      avatar:'JM', avatarColor:'#22c55e', image:'https://images.unsplash.com/photo-1504280390367-361c6d9f38f4?w=600&q=80' },
  ]
})())

const nearAreaItems = ref((() => {
  const real = parseDataset('nearArea')
  return real.length ? real : [
    { id:'na1', category:'Electronics', condition:'Good',     title:'Dell UltraSharp 27" 4K',  wants:'Gaming Monitor',       value:350, badge:null, owner:'Chris P.', avatar:'CP', avatarColor:'#2563eb', image:'https://images.unsplash.com/photo-1527443224154-c4a3942d3acf?w=600&q=80' },
    { id:'na2', category:'Books',       condition:'Like New', title:'Design Thinking Library', wants:'Programming Books',     value:80,  badge:null, owner:'Ana L.',   avatar:'AL', avatarColor:'#a78bfa', image:'https://images.unsplash.com/photo-1524995997946-a1c2e315a42f?w=600&q=80' },
    { id:'na3', category:'Home',        condition:'Mint',     title:'Standing Desk Frame',     wants:'Office Chair',          value:280, badge:null, owner:'Tom W.',   avatar:'TW', avatarColor:'#0ea5e9', image:'https://images.unsplash.com/photo-1593642632559-0c6d3fc62b89?w=600&q=80' },
    { id:'na4', category:'Gaming',      condition:'Good',     title:'Razer Blade 15 Gaming',   wants:'MacBook Pro',           value:1300,badge:null, owner:'Kai D.',   avatar:'KD', avatarColor:'#10b981', image:'https://images.unsplash.com/photo-1593642634524-b40b5baae6bb?w=600&q=80' },
    { id:'na5', category:'Fashion',     condition:'Like New', title:'Uniqlo Cashmere Crew',   wants:'Linen Shirts',          value:65,  badge:null, owner:'Mia S.',   avatar:'MS', avatarColor:'#f43f5e', image:'https://images.unsplash.com/photo-1434389677669-e08b4cac3105?w=600&q=80' },
    { id:'na6', category:'Photography', condition:'Good',     title:'Godox LED Panel',         wants:'Ring Light / Softbox',  value:120, badge:null, owner:'Ben H.',   avatar:'BH', avatarColor:'#f97316', image:'https://images.unsplash.com/photo-1598300042247-d088f8ab3a91?w=600&q=80' },
  ]
})())

// ── main all-items grid ────────────────────────────────────────────────────────
const mainGrid = ref(allItems.value.length ? allItems.value : [
  { id:'g1', category:'Electronics', condition:'Like New', title:'iPhone 14 Pro Max 256GB', desc:'Excellent condition, unlocked, all accessories included.',    value:900,  wants:'Gaming Laptop',    badge:null, owner:'Marcus C.', avatar:'MC', avatarColor:'#ED730C', image:'https://images.unsplash.com/photo-1591337676887-a217a6970a8a?w=600&q=80' },
  { id:'g2', category:'Gaming',      condition:'Good',     title:'ASUS ROG Gaming Laptop',  desc:'RTX 3070, 16GB RAM, 1TB SSD. Perfect for gaming & design.',   value:1200, wants:'iPhone, MacBook',  badge:null, owner:'Juno K.',  avatar:'JK', avatarColor:'#8b5cf6', image:'https://images.unsplash.com/photo-1603302576837-37561b2e2302?w=600&q=80' },
  { id:'g3', category:'Fashion',     condition:'Mint',     title:'Supreme Box Logo Hoodie', desc:'Size L, worn twice. No stains or defects. Rare colourway.',    value:380,  wants:'Sneakers / Caps',  badge:null, owner:'Elena R.', avatar:'ER', avatarColor:'#14b8a6', image:'https://images.unsplash.com/photo-1556821840-3a63f15732ce?w=600&q=80' },
  { id:'g4', category:'Photography', condition:'Like New', title:'Sony A7 III Full Frame',  desc:'Only 3k shutter count. Kit lens and extra battery included.',   value:1800, wants:'Canon R5, Lenses',  badge:null, owner:'Chris P.', avatar:'CP', avatarColor:'#2563eb', image:'https://images.unsplash.com/photo-1516035069371-29a1b244cc32?w=600&q=80' },
  { id:'g5', category:'Home',        condition:'Good',     title:'Dyson V11 Vacuum',        desc:'Powerful cordless. All attachments included. Works perfectly.', value:320,  wants:'Air Purifier',     badge:null, owner:'Sara J.',  avatar:'SJ', avatarColor:'#f59e0b', image:'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=600&q=80' },
  { id:'g6', category:'Collectibles',condition:'Mint',     title:'Vinyl Records (50+)',      desc:'Mixed genres. 60s–90s. Rare finds and classic albums.',        value:200,  wants:'Cassette Player',  badge:null, owner:'Leo B.',  avatar:'LB', avatarColor:'#ec4899', image:'https://images.unsplash.com/photo-1603048588665-791ca8aea617?w=600&q=80' },
  { id:'g7', category:'Outdoor',     condition:'Good',     title:'Trek Mountain Bike 29"',  desc:'Carbon frame, Shimano gears, bought this year.',              value:700,  wants:'Surfboard / Kayak',badge:null, owner:'Alex R.',  avatar:'AR', avatarColor:'#22c55e', image:'https://images.unsplash.com/photo-1485965120184-e220f721d03e?w=600&q=80' },
  { id:'g8', category:'Electronics', condition:'Like New', title:'iPad Pro 12.9" M2',       desc:'Barely used, with Apple Pencil and Magic Keyboard case.',      value:950,  wants:'MacBook Air M2',   badge:null, owner:'James K.', avatar:'JK', avatarColor:'#6366f1', image:'https://images.unsplash.com/photo-1544244015-0df4b3ffc6b0?w=600&q=80' },
])

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
      <div style="background:#fff;border-radius:999px;display:flex;align-items:center;padding:6px 6px 6px 20px;box-shadow:0 8px 32px rgba(0,0,0,0.12);border:1.5px solid #EBEBEB;">
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
          style="background:#ED730C;color:#fff;border:none;border-radius:999px;padding:12px 28px;font-size:0.875rem;font-weight:800;cursor:pointer;font-family:'DM Sans',sans-serif;white-space:nowrap;box-shadow:0 4px 14px rgba(237,115,12,0.4);"
          onmouseover="this.style.background='#d4620a'" onmouseout="this.style.background='#ED730C'">Search</button>
      </div>
    </div>
  </Teleport>

  <!-- ═══════════════════════════════════════════
       HERO
  ═══════════════════════════════════════════ -->
  <section style="padding:52px 24px 40px;background:#fff;text-align:center;">
    <div style="max-width:860px;margin:0 auto;">
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
          style="background:#ED730C;color:#fff;border:none;border-radius:999px;padding:12px 28px;font-size:0.875rem;font-weight:800;cursor:pointer;font-family:'DM Sans',sans-serif;white-space:nowrap;box-shadow:0 4px 14px rgba(237,115,12,0.4);"
          onmouseover="this.style.background='#d4620a'" onmouseout="this.style.background='#ED730C'">Search</button>
      </div>
      <!-- Category pills -->
      <div style="display:flex;gap:8px;flex-wrap:wrap;justify-content:center;">
        <button v-for="cat in categories.slice(0,8)" :key="cat" @click="activeTab = cat"
          :style="{padding:'7px 16px',borderRadius:'999px',fontSize:'0.78rem',fontWeight:'600',fontFamily:'\'DM Sans\',sans-serif',cursor:'pointer',border:activeTab===cat?'none':'1px solid #EBEBEB',background:activeTab===cat?'#1A1A1A':'#fff',color:activeTab===cat?'#fff':'#4b5563',boxShadow:activeTab===cat?'0 4px 12px rgba(0,0,0,0.15)':'none',transition:'all 0.15s'}">
          {{ cat }}
        </button>
      </div>
    </div>
  </section>

  <!-- ═══════════════════════════════════════════
       UNIFIED CARD — inline template component
       Same structure used in all 4 horizontal sections
  ═══════════════════════════════════════════ -->

  <!-- ───────────────────────────────────────────
       1. TOP FEATURED SWAPS
  ─────────────────────────────────────────── -->
  <section style="padding:0 0 52px;background:#fff;">
    <div style="max-width:1280px;margin:0 auto;padding:0 24px;">
      <div style="display:flex;align-items:flex-end;justify-content:space-between;margin-bottom:20px;">
        <div>
          <h2 style="font-size:1.5rem;font-weight:900;color:#1A1A1A;margin:0;letter-spacing:-.02em;">Top Featured Swaps</h2>
        </div>
        <a href="#" style="font-size:0.8rem;font-weight:700;color:#ED730C;text-decoration:none;">See all →</a>
      </div>
      <div class="hscroll" style="display:flex;gap:16px;overflow-x:auto;padding-bottom:8px;">
        <div v-for="item in featuredItems" :key="item.id" class="swapy-card swap-card-base" style="flex-shrink:0;width:230px;">
          <div style="position:relative;height:175px;overflow:hidden;background:#f3f4f6;">
            <img :src="item.image" :alt="item.title" class="card-img" style="width:100%;height:100%;object-fit:cover;transition:transform 0.35s;">
            <span v-if="item.badge" class="badge-pill" style="background:#ED730C;">{{ item.badge }}</span>
            <button @click.stop="toggleWish(item.id)" class="wish-btn wish-abs">
              <svg :style="{width:'13px',height:'13px',fill:wishlisted.has(item.id)?'#ED730C':'none',stroke:wishlisted.has(item.id)?'#ED730C':'#6b7280',strokeWidth:'2'}" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
            </button>
          </div>
          <div class="card-body">
            <div class="card-meta"><span class="cat-label">{{ item.category }}</span><span class="dot"></span><span class="cond-label">{{ item.condition }}</span></div>
            <h3 class="card-title">{{ item.title }}</h3>
            <p class="card-wants">Wants: <span style="color:#ED730C;font-weight:700;">{{ item.wants }}</span></p>
            <div class="card-footer">
              <div class="owner-row">
                <div class="avatar" :style="{background:item.avatarColor}">{{ item.avatar }}</div>
                <span class="owner-name">{{ item.owner }}</span>
              </div>
              <span class="card-value">${{ item.value?.toLocaleString() }}</span>
            </div>
            <a :href="'/item/'+item.id" class="swap-btn swap-btn-full">Swap Now</a>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- ───────────────────────────────────────────
       2. RECENTLY ADDED
  ─────────────────────────────────────────── -->
  <section style="padding:0 0 52px;background:#fff;">
    <div style="max-width:1280px;margin:0 auto;padding:0 24px;">
      <div style="display:flex;align-items:flex-end;justify-content:space-between;margin-bottom:20px;">
        <div>
          <h2 style="font-size:1.5rem;font-weight:900;color:#1A1A1A;margin:0;letter-spacing:-.02em;">Recently Added</h2>
        </div>
        <a href="#" style="font-size:0.8rem;font-weight:700;color:#ED730C;text-decoration:none;">See all →</a>
      </div>
      <div class="hscroll" style="display:flex;gap:16px;overflow-x:auto;padding-bottom:8px;">
        <div v-for="item in recentItems" :key="item.id" class="swapy-card swap-card-base" style="flex-shrink:0;width:230px;">
          <div style="position:relative;height:175px;overflow:hidden;background:#f3f4f6;">
            <img :src="item.image" :alt="item.title" class="card-img" style="width:100%;height:100%;object-fit:cover;transition:transform 0.35s;">
            <button @click.stop="toggleWish(item.id)" class="wish-btn wish-abs">
              <svg :style="{width:'13px',height:'13px',fill:wishlisted.has(item.id)?'#ED730C':'none',stroke:wishlisted.has(item.id)?'#ED730C':'#6b7280',strokeWidth:'2'}" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
            </button>
          </div>
          <div class="card-body">
            <div class="card-meta"><span class="cat-label">{{ item.category }}</span><span class="dot"></span><span class="cond-label">{{ item.condition }}</span></div>
            <h3 class="card-title">{{ item.title }}</h3>
            <p class="card-wants">Wants: <span style="color:#ED730C;font-weight:700;">{{ item.wants }}</span></p>
            <div class="card-footer">
              <div class="owner-row">
                <div class="avatar" :style="{background:item.avatarColor}">{{ item.avatar }}</div>
                <span class="owner-name">{{ item.owner }}</span>
              </div>
              <span class="card-value">${{ item.value?.toLocaleString() }}</span>
            </div>
            <a :href="'/item/'+item.id" class="swap-btn swap-btn-full">Swap Now</a>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- ───────────────────────────────────────────
       3. POPULAR IN [CITY] — Nominatim
  ─────────────────────────────────────────── -->
  <section style="padding:0 0 52px;background:#fff;">
    <div style="max-width:1280px;margin:0 auto;padding:0 24px;">
      <div style="display:flex;align-items:flex-end;justify-content:space-between;margin-bottom:20px;">
        <div>
          <h2 style="font-size:1.5rem;font-weight:900;color:#1A1A1A;margin:0;letter-spacing:-.02em;">Popular in {{ cityName || 'Your City' }}</h2>
        </div>
        <a href="#" style="font-size:0.8rem;font-weight:700;color:#ED730C;text-decoration:none;">See all →</a>
      </div>
      <div class="hscroll" style="display:flex;gap:16px;overflow-x:auto;padding-bottom:8px;">
        <div v-for="item in popularCityItems" :key="item.id" class="swapy-card swap-card-base" style="flex-shrink:0;width:230px;">
          <div style="position:relative;height:175px;overflow:hidden;background:#f3f4f6;">
            <img :src="item.image" :alt="item.title" class="card-img" style="width:100%;height:100%;object-fit:cover;transition:transform 0.35s;">
            <button @click.stop="toggleWish(item.id)" class="wish-btn wish-abs">
              <svg :style="{width:'13px',height:'13px',fill:wishlisted.has(item.id)?'#ED730C':'none',stroke:wishlisted.has(item.id)?'#ED730C':'#6b7280',strokeWidth:'2'}" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
            </button>
          </div>
          <div class="card-body">
            <div class="card-meta"><span class="cat-label">{{ item.category }}</span><span class="dot"></span><span class="cond-label">{{ item.condition }}</span></div>
            <h3 class="card-title">{{ item.title }}</h3>
            <p class="card-wants">Wants: <span style="color:#ED730C;font-weight:700;">{{ item.wants }}</span></p>
            <div class="card-footer">
              <div class="owner-row">
                <div class="avatar" :style="{background:item.avatarColor}">{{ item.avatar }}</div>
                <span class="owner-name">{{ item.owner }}</span>
              </div>
              <span class="card-value">${{ item.value?.toLocaleString() }}</span>
            </div>
            <a :href="'/item/'+item.id" class="swap-btn swap-btn-full">Swap Now</a>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- ───────────────────────────────────────────
       4. SWAPS NEAR [AREA] — Nominatim
  ─────────────────────────────────────────── -->
  <section style="padding:0 0 52px;background:#fff;">
    <div style="max-width:1280px;margin:0 auto;padding:0 24px;">
      <div style="display:flex;align-items:flex-end;justify-content:space-between;margin-bottom:20px;">
        <div>
          <h2 style="font-size:1.5rem;font-weight:900;color:#1A1A1A;margin:0;letter-spacing:-.02em;">Swaps near {{ areaName || 'Your Area' }}</h2>
        </div>
        <a href="#" style="font-size:0.8rem;font-weight:700;color:#ED730C;text-decoration:none;">See all →</a>
      </div>
      <div class="hscroll" style="display:flex;gap:16px;overflow-x:auto;padding-bottom:8px;">
        <div v-for="item in nearAreaItems" :key="item.id" class="swapy-card swap-card-base" style="flex-shrink:0;width:230px;">
          <div style="position:relative;height:175px;overflow:hidden;background:#f3f4f6;">
            <img :src="item.image" :alt="item.title" class="card-img" style="width:100%;height:100%;object-fit:cover;transition:transform 0.35s;">
            <button @click.stop="toggleWish(item.id)" class="wish-btn wish-abs">
              <svg :style="{width:'13px',height:'13px',fill:wishlisted.has(item.id)?'#ED730C':'none',stroke:wishlisted.has(item.id)?'#ED730C':'#6b7280',strokeWidth:'2'}" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
            </button>
          </div>
          <div class="card-body">
            <div class="card-meta"><span class="cat-label">{{ item.category }}</span><span class="dot"></span><span class="cond-label">{{ item.condition }}</span></div>
            <h3 class="card-title">{{ item.title }}</h3>
            <p class="card-wants">Wants: <span style="color:#ED730C;font-weight:700;">{{ item.wants }}</span></p>
            <div class="card-footer">
              <div class="owner-row">
                <div class="avatar" :style="{background:item.avatarColor}">{{ item.avatar }}</div>
                <span class="owner-name">{{ item.owner }}</span>
              </div>
              <span class="card-value">${{ item.value?.toLocaleString() }}</span>
            </div>
            <a :href="'/item/'+item.id" class="swap-btn swap-btn-full">Swap Now</a>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- ───────────────────────────────────────────
       5. ALL ITEMS — filterable, grid/list toggle
  ─────────────────────────────────────────── -->
  <section style="background:#fff;">

    <!-- Sticky filter toolbar -->
    <div style="background:#fff;border-bottom:1px solid #EBEBEB;padding:14px 24px;" @click.stop>
      <div style="max-width:1280px;margin:0 auto;display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;">
        <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">

          <!-- Category filter -->
          <div style="position:relative;">
            <button @click.stop="showFiltersPanel=!showFiltersPanel;showSortDropdown=false"
              style="display:flex;align-items:center;gap:7px;padding:8px 16px;border-radius:999px;border:1.5px solid #d1cdc7;background:#fff;font-family:'DM Sans',sans-serif;font-size:0.82rem;font-weight:700;color:#1A1A1A;cursor:pointer;">
              <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 4h13M3 8h9m-9 4h9m5-4v12m0 0l-4-4m4 4l4-4"/></svg>
              {{ activeTab === 'All' ? 'All Categories' : activeTab }}
            </button>
            <div v-if="showFiltersPanel"
              style="position:absolute;top:calc(100% + 8px);left:0;background:#fff;border:1px solid #EBEBEB;border-radius:16px;padding:8px;min-width:200px;box-shadow:0 8px 32px rgba(0,0,0,0.12);z-index:100;">
              <button v-for="cat in categories" :key="cat"
                @click.stop="activeTab=cat;showFiltersPanel=false"
                :style="{display:'block',width:'100%',textAlign:'left',padding:'9px 14px',borderRadius:'10px',border:'none',background:activeTab===cat?'#fff4ec':'transparent',color:activeTab===cat?'#ED730C':'#1A1A1A',fontSize:'0.85rem',fontWeight:activeTab===cat?'700':'500',fontFamily:'\'DM Sans\',sans-serif',cursor:'pointer'}">
                {{ cat }}
              </button>
            </div>
          </div>

          <!-- Value range -->
          <div style="display:flex;align-items:center;gap:8px;padding:7px 14px;border-radius:999px;border:1.5px solid #d1cdc7;background:#fff;">
            <span style="font-size:0.82rem;font-weight:600;color:#6b7280;white-space:nowrap;">Up to ${{ valueMax.toLocaleString() }}</span>
            <input type="range" v-model.number="valueMax" min="50" max="5000" step="50"
              style="width:80px;accent-color:#ED730C;cursor:pointer;">
          </div>

          <!-- Sort -->
          <div style="position:relative;">
            <button @click.stop="showSortDropdown=!showSortDropdown;showFiltersPanel=false"
              style="display:flex;align-items:center;gap:7px;padding:8px 16px;border-radius:999px;border:1.5px solid #d1cdc7;background:#fff;font-family:'DM Sans',sans-serif;font-size:0.82rem;font-weight:700;color:#1A1A1A;cursor:pointer;">
              <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12"/></svg>
              {{ sortBy }}
            </button>
            <div v-if="showSortDropdown"
              style="position:absolute;top:calc(100% + 8px);left:0;background:#fff;border:1px solid #EBEBEB;border-radius:16px;padding:8px;min-width:220px;box-shadow:0 8px 32px rgba(0,0,0,0.12);z-index:100;">
              <button v-for="s in sortOptions" :key="s"
                @click.stop="sortBy=s;showSortDropdown=false"
                :style="{display:'block',width:'100%',textAlign:'left',padding:'9px 14px',borderRadius:'10px',border:'none',background:sortBy===s?'#fff4ec':'transparent',color:sortBy===s?'#ED730C':'#1A1A1A',fontSize:'0.85rem',fontWeight:sortBy===s?'700':'500',fontFamily:'\'DM Sans\',sans-serif',cursor:'pointer'}">
                {{ s }}
              </button>
            </div>
          </div>
        </div>

        <!-- Count + view toggle -->
        <div style="display:flex;align-items:center;gap:14px;">
          <p style="font-size:0.82rem;color:#9ca3af;font-weight:600;margin:0;white-space:nowrap;">
            <strong style="color:#1A1A1A;">{{ filtered.length }}</strong> items
          </p>
          <div style="display:inline-flex;border:1.5px solid #EBEBEB;border-radius:10px;overflow:hidden;background:#fff;">
            <button @click="viewMode='grid'" :style="{padding:'7px 12px',background:viewMode==='grid'?'#1A1A1A':'transparent',border:'none',cursor:'pointer',display:'flex',alignItems:'center',transition:'background 0.15s'}">
              <svg width="13" height="13" fill="none" :stroke="viewMode==='grid'?'#fff':'#9ca3af'" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
            </button>
            <button @click="viewMode='list'" :style="{padding:'7px 12px',background:viewMode==='list'?'#1A1A1A':'transparent',border:'none',borderLeft:'1px solid #EBEBEB',cursor:'pointer',display:'flex',alignItems:'center',transition:'background 0.15s'}">
              <svg width="13" height="13" fill="none" :stroke="viewMode==='list'?'#fff':'#9ca3af'" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Section label -->
    <div style="max-width:1280px;margin:0 auto;padding:28px 24px 0;">
      <h2 style="font-size:1.5rem;font-weight:900;color:#1A1A1A;margin:0;letter-spacing:-.02em;">All Items</h2>
    </div>

    <div style="max-width:1280px;margin:0 auto;padding:20px 24px 72px;">

      <!-- Skeleton -->
      <div v-if="skeletonLoading" style="display:grid;grid-template-columns:repeat(5,1fr);gap:18px;">
        <div v-for="n in 6" :key="n" style="background:#fff;border-radius:20px;overflow:hidden;border:1px solid #EBEBEB;">
          <div class="skeleton" style="height:175px;width:100%;"></div>
          <div style="padding:14px 16px 16px;">
            <div class="skeleton" style="height:10px;width:35%;border-radius:4px;margin-bottom:10px;"></div>
            <div class="skeleton" style="height:16px;width:80%;border-radius:4px;margin-bottom:8px;"></div>
            <div class="skeleton" style="height:10px;width:60%;border-radius:4px;margin-bottom:20px;"></div>
            <div class="skeleton" style="height:38px;width:100%;border-radius:999px;"></div>
          </div>
        </div>
      </div>

      <!-- Grid view — same unified card -->
      <div v-else-if="viewMode==='grid'" style="display:grid;grid-template-columns:repeat(5,1fr);gap:18px;">
        <div v-for="item in filtered" :key="item.id" class="swapy-card swap-card-base">
          <div style="position:relative;height:175px;overflow:hidden;background:#f3f4f6;flex-shrink:0;">
            <img :src="item.image" :alt="item.title" class="card-img" style="width:100%;height:100%;object-fit:cover;transition:transform 0.4s;">
            <span v-if="item.badge" class="badge-pill" style="background:#ED730C;">{{ item.badge }}</span>
            <button @click.stop="toggleWish(item.id)" class="wish-btn wish-abs">
              <svg :style="{width:'13px',height:'13px',fill:wishlisted.has(item.id)?'#ED730C':'none',stroke:wishlisted.has(item.id)?'#ED730C':'#6b7280',strokeWidth:'2'}" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
            </button>
          </div>
          <div class="card-body">
            <div class="card-meta"><span class="cat-label">{{ item.category }}</span><span class="dot"></span><span class="cond-label">{{ item.condition }}</span></div>
            <h3 class="card-title">{{ item.title }}</h3>
            <p class="card-wants">Wants: <span style="color:#ED730C;font-weight:700;">{{ item.wants }}</span></p>
            <div class="card-footer">
              <div class="owner-row">
                <div class="avatar" :style="{background:item.avatarColor}">{{ item.avatar }}</div>
                <span class="owner-name">{{ item.owner }}</span>
              </div>
              <span class="card-value">${{ item.value?.toLocaleString() }}</span>
            </div>
            <a :href="'/item/'+item.id" class="swap-btn swap-btn-full">Swap Now</a>
          </div>
        </div>
      </div>

      <!-- List view -->
      <div v-else style="display:flex;flex-direction:column;gap:12px;">
        <div v-for="item in filtered" :key="item.id"
          class="swapy-card"
          style="background:#fff;border-radius:16px;overflow:hidden;border:1px solid #EBEBEB;display:flex;align-items:center;cursor:pointer;">
          <div style="width:110px;height:90px;flex-shrink:0;overflow:hidden;background:#f3f4f6;">
            <img :src="item.image" :alt="item.title" class="card-img" style="width:100%;height:100%;object-fit:cover;transition:transform 0.35s;">
          </div>
          <div style="flex:1;padding:14px 20px;display:flex;justify-content:space-between;align-items:center;gap:16px;">
            <div>
              <p style="font-size:0.62rem;font-weight:800;letter-spacing:.07em;color:#9ca3af;text-transform:uppercase;margin:0 0 3px;">{{ item.category }} · {{ item.condition }}</p>
              <h3 style="font-size:0.9rem;font-weight:800;color:#1A1A1A;margin:0 0 3px;">{{ item.title }}</h3>
              <p style="font-size:0.76rem;color:#9ca3af;margin:0;">Wants: <strong style="color:#ED730C;">{{ item.wants }}</strong></p>
            </div>
            <div style="text-align:right;flex-shrink:0;display:flex;flex-direction:column;align-items:flex-end;gap:8px;">
              <p style="font-size:1rem;font-weight:900;color:#1A1A1A;margin:0;">${{ item.value?.toLocaleString() }}</p>
              <a :href="'/item/'+item.id" class="swap-btn" style="background:#ED730C;color:#fff;font-size:0.75rem;font-weight:800;padding:8px 20px;border-radius:999px;text-decoration:none;font-family:'DM Sans',sans-serif;">Swap Now</a>
            </div>
          </div>
        </div>
      </div>

      <!-- Empty state -->
      <div v-if="filtered.length === 0 && !skeletonLoading" style="text-align:center;padding:80px 0;">
        <div style="font-size:3rem;margin-bottom:16px;">🔍</div>
        <h3 style="font-size:1.125rem;font-weight:700;color:#1A1A1A;margin-bottom:8px;">No items found</h3>
        <p style="font-size:0.875rem;color:#9ca3af;margin-bottom:20px;">Try adjusting your filters or search term.</p>
        <button @click="activeTab='All';valueMax=5000;search=''"
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
  </section>

</div>
</template>

<style scoped>
@keyframes shimmer { 0%{background-position:-600px 0} 100%{background-position:600px 0} }

/* ── Skeleton ── */
.skeleton {
  background:linear-gradient(90deg,#f3f4f6 25%,#e5e7eb 50%,#f3f4f6 75%);
  background-size:1200px 100%;
  animation:shimmer 1.5s ease-in-out infinite;
}

/* ── Unified card shell ── */
.swap-card-base {
  background:#fff;
  border-radius:20px;
  overflow:hidden;
  border:1px solid #EBEBEB;
  cursor:pointer;
  display:flex;
  flex-direction:column;
}

/* ── Card body ── */
.card-body {
  padding:14px 16px 16px;
  flex:1;
  display:flex;
  flex-direction:column;
  gap:5px;
}
.card-meta { display:flex;align-items:center;gap:6px; }
.cat-label { font-size:0.6rem;font-weight:800;letter-spacing:.07em;color:#9ca3af;text-transform:uppercase; }
.dot { width:2px;height:2px;border-radius:50%;background:#d1d5db;display:inline-block; }
.cond-label { font-size:0.6rem;font-weight:700;color:#149189; }
.card-title { font-size:0.9rem;font-weight:800;color:#1A1A1A;line-height:1.3;margin:0;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden; }
.card-wants { font-size:0.75rem;color:#9ca3af;margin:0; }
.card-footer { display:flex;align-items:center;justify-content:space-between;padding-top:8px;margin-top:4px;border-top:1px solid #f3f4f6; }
.owner-row { display:flex;align-items:center;gap:6px; }
.avatar { width:22px;height:22px;border-radius:50%;color:#fff;font-size:0.5rem;font-weight:800;display:flex;align-items:center;justify-content:center;flex-shrink:0; }
.owner-name { font-size:0.72rem;font-weight:600;color:#6b7280; }
.card-value { font-size:0.9rem;font-weight:900;color:#1A1A1A; }

/* ── Badge ── */
.badge-pill {
  position:absolute;top:10px;left:10px;
  color:#fff;font-size:0.6rem;font-weight:800;
  padding:4px 10px;border-radius:999px;
  letter-spacing:.05em;text-transform:uppercase;
}

/* ── Heart ── */
.wish-abs {
  position:absolute;top:9px;right:9px;
  width:30px;height:30px;
  background:rgba(255,255,255,0.92);
  box-shadow:0 2px 8px rgba(0,0,0,0.10);
}
.wish-btn {
  border:none;border-radius:50%;
  display:flex;align-items:center;justify-content:center;
  cursor:pointer;transition:transform 0.15s;
}
.wish-btn:hover { transform:scale(1.18); }

/* ── Swap button ── */
.swap-btn-full {
  display:block;text-align:center;
  background:#ED730C;color:#fff;
  font-size:0.8rem;font-weight:800;
  padding:10px 0;border-radius:999px;
  text-decoration:none;letter-spacing:.03em;
  font-family:'DM Sans',sans-serif;
  margin-top:6px;
}

/* ── Card hover ── */
.swapy-card { transition:border-color 0.2s,box-shadow 0.2s,transform 0.2s; }
.swapy-card:hover { border-color:#ED730C !important;box-shadow:0 10px 36px rgba(237,115,12,0.13) !important;transform:translateY(-3px); }
.swapy-card:hover .card-img { transform:scale(1.06); }
.swap-btn:hover, .swap-btn-full:hover { background:#d4620a !important;box-shadow:0 6px 18px rgba(237,115,12,0.40) !important; }

/* ── Horizontal scroll ── */
.hscroll::-webkit-scrollbar { display:none; }
.hscroll { scrollbar-width:none; }
</style>