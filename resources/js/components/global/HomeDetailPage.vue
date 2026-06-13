<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { currency, formatMoney } from '../../constants/currency'

const el   = document.getElementById('home-detail-app')
const raw  = JSON.parse(el?.dataset.home || '{}')

// ── fallback demo data ────────────────────────────────────────────────────────
const home = ref(Object.keys(raw).length ? raw : {
  id: 1,
  type: 'Swap',
  title: 'Modern Studio in BGC',
  location: 'Bonifacio Global City, Taguig, Metro Manila',
  distance: '1.2',
  beds: 'Studio', baths: 1, sqm: 32, floor: 18,
  value: 18000,
  swap_terms: 'Open to swap with a condo unit in Makati CBD or Ortigas. Willing to do short-term or long-term arrangement.',
  description: "This bright, fully furnished studio on the 18th floor of a BGC tower offers stunning city views and a clean modern aesthetic. The unit features a queen bed, built-in wardrobe, kitchen with appliances, and a dedicated work-from-home nook.\n\nThe building has 24/7 security, a rooftop pool, fitness center, and co-working lounge. Steps from Bonifacio High Street, restaurants, and BGC bus stops.",
  tags: ['Fully Furnished', 'Pet-friendly', 'High-floor', 'City View', 'With Parking', 'Near BGC'],
  amenities: [
    { icon: 'wifi',     label: 'Fast WiFi' },
    { icon: 'ac',       label: 'Aircon' },
    { icon: 'pool',     label: 'Pool Access' },
    { icon: 'gym',      label: 'Gym' },
    { icon: 'parking',  label: 'Parking' },
    { icon: 'security', label: '24/7 Security' },
    { icon: 'pet',      label: 'Pet Friendly' },
    { icon: 'balcony',  label: 'Balcony' },
  ],
  rating: 4.9,
  review_count: 24,
  response_rate: 98,
  response_time: 'within an hour',
  owner: { name: 'Marco Reyes', username: 'marco_reyes', initials: 'MR', color: '#ED730C', member_since: 'Jan 2023', verified: true },
  images: [
    'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?w=1200&q=85',
    'https://images.unsplash.com/photo-1502672260266-1c1ef2d93688?w=800&q=85',
    'https://images.unsplash.com/photo-1493809842364-78817add7ffb?w=800&q=85',
    'https://images.unsplash.com/photo-1484154218962-a197022b5858?w=800&q=85',
    'https://images.unsplash.com/photo-1556909114-f6e7ad7d3136?w=800&q=85',
  ],
  similar: [
    { id:2, type:'Rent',  title:'1BR in Salcedo Village', location:'Makati', value:22000, beds:'1',      image:'https://images.unsplash.com/photo-1493809842364-78817add7ffb?w=600&q=80', rating:4.7 },
    { id:3, type:'Swap',  title:'Loft in Eastwood City',  location:'QC',     value:19500, beds:'Studio', image:'https://images.unsplash.com/photo-1502672260266-1c1ef2d93688?w=600&q=80', rating:4.8 },
    { id:4, type:'Sell',  title:'Townhouse in Paranaque', location:'Paranaque', value:6500000, beds:'3', image:'https://images.unsplash.com/photo-1568605114967-8130f3a36994?w=600&q=80', rating:5.0 },
  ],
})

// ── gallery ───────────────────────────────────────────────────────────────────
const showGallery   = ref(false)
const galleryIndex  = ref(0)

function openGallery(i) { galleryIndex.value = i; showGallery.value = true }
function closeGallery()  { showGallery.value = false }
function prevPhoto()     { galleryIndex.value = (galleryIndex.value - 1 + home.value.images.length) % home.value.images.length }
function nextPhoto()     { galleryIndex.value = (galleryIndex.value + 1) % home.value.images.length }

function onKeydown(e) {
  if (!showGallery.value) return
  if (e.key === 'ArrowLeft')  prevPhoto()
  if (e.key === 'ArrowRight') nextPhoto()
  if (e.key === 'Escape')     closeGallery()
}
onMounted(()  => window.addEventListener('keydown', onKeydown))
onUnmounted(() => window.removeEventListener('keydown', onKeydown))

// ── description expand ────────────────────────────────────────────────────────
const descExpanded = ref(false)
const descLines    = computed(() => home.value.description?.split('\n').filter(Boolean) || [])

// ── wishlist ──────────────────────────────────────────────────────────────────
const wishlisted = ref(false)

// ── type config ───────────────────────────────────────────────────────────────
const typeCfg = {
  'Swap':      { color:'#ED730C', bg:'#FFF4EC', border:'#fed7aa', cta:'Request Swap'    },
  'Rent':      { color:'#14b8a6', bg:'#EDFAF9', border:'#99f6e4', cta:'Inquire to Rent' },
  'Sell':      { color:'#8b5cf6', bg:'#F5F3FF', border:'#ddd6fe', cta:'Make an Offer'   },
  'Co-living': { color:'#f59e0b', bg:'#FFFBEB', border:'#fde68a', cta:'Request a Room'  },
}
const cfg = computed(() => typeCfg[home.value.type] || typeCfg['Swap'])

// ── price display ─────────────────────────────────────────────────────────────
const priceLabel = computed(() => {
  if (home.value.type === 'Sell') return currency.value.symbol + (home.value.value / 1000000).toFixed(2) + 'M'
  return formatMoney(home.value.value) + '/mo'
})
const priceNote = computed(() => {
  if (home.value.type === 'Sell')      return 'Asking price'
  if (home.value.type === 'Swap')      return 'Estimated value'
  if (home.value.type === 'Co-living') return 'Per room / month'
  return 'Per month'
})
</script>

<template>
<div style="min-height:100vh;background:#FAF8F5;font-family:DM Sans,sans-serif;">

  <!-- GALLERY MODAL -->
  <div v-if="showGallery"
    style="position:fixed;inset:0;z-index:9999;background:rgba(0,0,0,0.95);display:flex;align-items:center;justify-content:center;"
    @click.self="closeGallery">
    <button @click="closeGallery"
      style="position:absolute;top:20px;right:24px;width:40px;height:40px;border-radius:50%;background:rgba(255,255,255,0.12);border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;">
      <svg width="18" height="18" fill="none" stroke="#fff" stroke-width="2.5" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
    </button>
    <p style="position:absolute;top:24px;left:50%;transform:translateX(-50%);color:rgba(255,255,255,0.6);font-size:0.82rem;font-weight:700;">
      {{ galleryIndex + 1 }} / {{ home.images.length }}
    </p>
    <button @click="prevPhoto"
      style="position:absolute;left:20px;width:44px;height:44px;border-radius:50%;background:rgba(255,255,255,0.12);border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;">
      <svg width="18" height="18" fill="none" stroke="#fff" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg>
    </button>
    <img :src="home.images[galleryIndex]" :alt="home.title"
      style="max-width:90vw;max-height:85vh;object-fit:contain;border-radius:12px;">
    <button @click="nextPhoto"
      style="position:absolute;right:20px;width:44px;height:44px;border-radius:50%;background:rgba(255,255,255,0.12);border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;">
      <svg width="18" height="18" fill="none" stroke="#fff" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg>
    </button>
    <div style="position:absolute;bottom:24px;left:50%;transform:translateX(-50%);display:flex;gap:8px;">
      <div v-for="(img, i) in home.images" :key="i" @click="galleryIndex = i"
        :style="{width:'52px',height:'38px',borderRadius:'6px',overflow:'hidden',cursor:'pointer',border: i===galleryIndex ? '2px solid #ED730C' : '2px solid transparent',opacity: i===galleryIndex ? 1 : 0.45,transition:'all .15s'}">
        <img :src="img" style="width:100%;height:100%;object-fit:cover;">
      </div>
    </div>
  </div>

  <!-- BREADCRUMB -->
  <div class="hd-crumb">
    <div style="max-width:1280px;margin:0 auto;display:flex;align-items:center;gap:6px;font-size:0.78rem;font-weight:600;color:#9ca3af;">
      <a href="/homes" style="color:#9ca3af;text-decoration:none;display:inline-flex;align-items:center;gap:4px;" onmouseover="this.style.color='#ED730C'" onmouseout="this.style.color='#9ca3af'">
        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
        Homes
      </a>
      <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg>
      <span style="color:#1A1A1A;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ home.title }}</span>
    </div>
  </div>

  <div class="hd-main">

    <!-- TITLE ROW -->
    <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:16px;margin-bottom:20px;flex-wrap:wrap;">
      <div>
        <span :style="{display:'inline-block',background:cfg.bg,color:cfg.color,border:'1.5px solid '+cfg.border,fontSize:'0.62rem',fontWeight:'800',padding:'4px 12px',borderRadius:'999px',letterSpacing:'.08em',textTransform:'uppercase',marginBottom:'10px'}">{{ home.type }}</span>
        <h1 style="font-size:clamp(1.6rem,3vw,2.2rem);font-weight:900;color:#1A1A1A;margin:0 0 8px;letter-spacing:-.03em;line-height:1.1;">{{ home.title }}</h1>
        <div style="display:flex;align-items:center;gap:12px;flex-wrap:wrap;">
          <div style="display:flex;align-items:center;gap:4px;">
            <svg width="12" height="12" fill="#f59e0b" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
            <span style="font-size:0.82rem;font-weight:800;color:#1A1A1A;">{{ home.rating }}</span>
            <span style="font-size:0.82rem;color:#9ca3af;">({{ home.review_count }} reviews)</span>
          </div>
          <span style="color:#EDE8E0;">·</span>
          <div style="display:flex;align-items:center;gap:4px;">
            <svg width="12" height="12" fill="none" stroke="#9ca3af" stroke-width="2" viewBox="0 0 24 24"><path d="M21 10c0 7-9 13-9 13S3 17 3 10a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
            <span style="font-size:0.82rem;color:#6b7280;font-weight:600;">{{ home.location }}</span>
          </div>
          <span style="color:#EDE8E0;">·</span>
          <span style="font-size:0.82rem;color:#6b7280;font-weight:600;">{{ home.distance }} mi away</span>
        </div>
      </div>
      <div style="display:flex;align-items:center;gap:8px;flex-shrink:0;">
        <button style="display:flex;align-items:center;gap:6px;padding:8px 16px;border-radius:999px;border:1.5px solid #EDE8E0;background:#fff;font-size:0.78rem;font-weight:700;color:#3a3a3a;cursor:pointer;font-family:DM Sans,sans-serif;">
          <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><line x1="8.59" y1="13.51" x2="15.42" y2="17.49"/><line x1="15.41" y1="6.51" x2="8.59" y2="10.49"/></svg>
          Share
        </button>
        <button @click="wishlisted = !wishlisted"
          :style="{display:'flex',alignItems:'center',gap:'6px',padding:'8px 16px',borderRadius:'999px',border:'1.5px solid',borderColor:wishlisted?'#ED730C':'#EDE8E0',background:wishlisted?'#FFF4EC':'#fff',fontSize:'0.78rem',fontWeight:'700',color:wishlisted?'#ED730C':'#3a3a3a',cursor:'pointer',fontFamily:'DM Sans,sans-serif',transition:'all .15s'}">
          <svg width="13" height="13" :fill="wishlisted?'#ED730C':'none'" :stroke="wishlisted?'#ED730C':'currentColor'" stroke-width="2" viewBox="0 0 24 24"><path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z"/></svg>
          {{ wishlisted ? 'Saved' : 'Save' }}
        </button>
      </div>
    </div>

    <!-- PHOTO GALLERY — layout adapts to how many photos the listing has -->
    <div class="gal-wrap">

      <!-- one photo: single wide hero, no empty space -->
      <div v-if="home.images.length === 1" class="gal-cell gal-single" @click="openGallery(0)">
        <img :src="home.images[0]" :alt="home.title" class="gal-img">
      </div>

      <!-- two photos: even split -->
      <div v-else-if="home.images.length === 2" class="gal-two">
        <div v-for="(img, i) in home.images" :key="i" class="gal-cell" @click="openGallery(i)">
          <img :src="img" :alt="home.title" class="gal-img">
        </div>
      </div>

      <!-- three or four: hero + stacked side -->
      <div v-else-if="home.images.length <= 4" class="gal-hero-side">
        <div class="gal-cell" @click="openGallery(0)">
          <img :src="home.images[0]" :alt="home.title" class="gal-img">
        </div>
        <div class="gal-side" :style="{ gridTemplateRows: `repeat(${home.images.length - 1}, 1fr)` }">
          <div v-for="(img, i) in home.images.slice(1)" :key="i" class="gal-cell" @click="openGallery(i + 1)">
            <img :src="img" :alt="home.title" class="gal-img">
          </div>
        </div>
      </div>

      <!-- five or more: hero + 2×2 -->
      <div v-else class="gal-five">
        <div class="gal-cell gal-big" @click="openGallery(0)">
          <img :src="home.images[0]" :alt="home.title" class="gal-img">
        </div>
        <div v-for="(img, i) in home.images.slice(1, 5)" :key="i" class="gal-cell" @click="openGallery(i + 1)">
          <img :src="img" :alt="home.title" class="gal-img">
          <div v-if="i === 3 && home.images.length > 5" class="gal-more">+{{ home.images.length - 5 }} more</div>
        </div>
      </div>

      <!-- show-all pill -->
      <button v-if="home.images.length > 1" class="gal-all-btn" @click="openGallery(0)">
        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
        Show all {{ home.images.length }} photos
      </button>
    </div>

    <!-- TWO-COLUMN LAYOUT -->
    <div class="hd-columns">

      <!-- LEFT COLUMN -->
      <div>

        <!-- Quick stats -->
        <div class="hd-stats">
          <div style="padding:18px 14px;text-align:center;border-right:1.5px solid #EDE8E0;">
            <svg width="20" height="20" fill="none" stroke="#ED730C" stroke-width="1.8" viewBox="0 0 24 24" style="display:block;margin:0 auto 6px;"><path d="M3 7v13h18V7"/><path d="M1 10h22"/><path d="M7 10V4h10v6"/></svg>
            <p style="font-size:0.8rem;font-weight:800;color:#1A1A1A;margin:0;">{{ home.beds }}</p>
            <p style="font-size:0.65rem;font-weight:700;color:#9ca3af;margin:2px 0 0;text-transform:uppercase;letter-spacing:.05em;">Beds</p>
          </div>
          <div style="padding:18px 14px;text-align:center;border-right:1.5px solid #EDE8E0;">
            <svg width="20" height="20" fill="none" stroke="#ED730C" stroke-width="1.8" viewBox="0 0 24 24" style="display:block;margin:0 auto 6px;"><path d="M5 12H19"/><path d="M5 12a7 7 0 0114 0"/><path d="M5 19h14"/><path d="M5 12v7"/><path d="M19 12v7"/></svg>
            <p style="font-size:0.8rem;font-weight:800;color:#1A1A1A;margin:0;">{{ home.baths }}</p>
            <p style="font-size:0.65rem;font-weight:700;color:#9ca3af;margin:2px 0 0;text-transform:uppercase;letter-spacing:.05em;">Baths</p>
          </div>
          <div style="padding:18px 14px;text-align:center;border-right:1.5px solid #EDE8E0;">
            <svg width="20" height="20" fill="none" stroke="#ED730C" stroke-width="1.8" viewBox="0 0 24 24" style="display:block;margin:0 auto 6px;"><rect x="3" y="3" width="18" height="18" rx="1"/><path d="M3 9h18M9 3v18"/></svg>
            <p style="font-size:0.8rem;font-weight:800;color:#1A1A1A;margin:0;">{{ home.sqm }}m2</p>
            <p style="font-size:0.65rem;font-weight:700;color:#9ca3af;margin:2px 0 0;text-transform:uppercase;letter-spacing:.05em;">Area</p>
          </div>
          <div style="padding:18px 14px;text-align:center;">
            <svg width="20" height="20" fill="none" stroke="#ED730C" stroke-width="1.8" viewBox="0 0 24 24" style="display:block;margin:0 auto 6px;"><rect x="2" y="3" width="20" height="4" rx="1"/><rect x="2" y="10" width="20" height="4" rx="1"/><rect x="2" y="17" width="20" height="4" rx="1"/></svg>
            <p style="font-size:0.8rem;font-weight:800;color:#1A1A1A;margin:0;">{{ home.floor ? 'Floor ' + home.floor : 'Ground' }}</p>
            <p style="font-size:0.65rem;font-weight:700;color:#9ca3af;margin:2px 0 0;text-transform:uppercase;letter-spacing:.05em;">Level</p>
          </div>
        </div>

        <!-- Owner profile -->
        <div style="display:flex;align-items:center;justify-content:space-between;padding-bottom:28px;border-bottom:1px solid #F3F0EC;margin-bottom:28px;flex-wrap:wrap;gap:12px;">
          <div style="display:flex;align-items:center;gap:14px;">
            <div :style="{width:'52px',height:'52px',borderRadius:'50%',background:home.owner.color,color:'#fff',fontSize:'1rem',fontWeight:'900',display:'flex',alignItems:'center',justifyContent:'center',boxShadow:'0 4px 12px rgba(0,0,0,0.15)'}">{{ home.owner.initials }}</div>
            <div>
              <div style="display:flex;align-items:center;gap:6px;">
                <p style="font-size:0.95rem;font-weight:800;color:#1A1A1A;margin:0;">Listed by {{ home.owner.name }}</p>
                <svg v-if="home.owner.verified" width="15" height="15" fill="#14b8a6" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
              </div>
              <p style="font-size:0.78rem;color:#9ca3af;font-weight:600;margin:3px 0 0;">@{{ home.owner.username }} · Member since {{ home.owner.member_since }}</p>
            </div>
          </div>
          <div style="text-align:right;">
            <p style="font-size:0.72rem;font-weight:700;color:#9ca3af;margin:0 0 2px;">Response rate</p>
            <p style="font-size:0.88rem;font-weight:800;color:#149189;margin:0;">{{ home.response_rate }}% · {{ home.response_time }}</p>
          </div>
        </div>

        <!-- Description -->
        <div style="padding-bottom:28px;border-bottom:1px solid #F3F0EC;margin-bottom:28px;">
          <h2 style="font-size:1.1rem;font-weight:900;color:#1A1A1A;margin:0 0 14px;letter-spacing:-.02em;">About this space</h2>
          <div :style="{overflow:'hidden',maxHeight:descExpanded?'none':'96px',position:'relative'}">
            <p v-for="(line, i) in descLines" :key="i" style="font-size:0.88rem;color:#4b5563;line-height:1.8;margin:0 0 10px;">{{ line }}</p>
            <div v-if="!descExpanded" style="position:absolute;bottom:0;left:0;right:0;height:48px;background:linear-gradient(to bottom,transparent,#FAF8F5);"></div>
          </div>
          <button @click="descExpanded = !descExpanded" style="margin-top:10px;background:none;border:none;font-size:0.85rem;font-weight:800;color:#1A1A1A;cursor:pointer;text-decoration:underline;padding:0;font-family:DM Sans,sans-serif;">
            {{ descExpanded ? 'Show less' : 'Show more' }}
          </button>
        </div>

        <!-- Amenities -->
        <div v-if="home.amenities && home.amenities.length" style="padding-bottom:28px;border-bottom:1px solid #F3F0EC;margin-bottom:28px;">
          <h2 style="font-size:1.1rem;font-weight:900;color:#1A1A1A;margin:0 0 16px;letter-spacing:-.02em;">What this place offers</h2>
          <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
            <div v-for="a in home.amenities" :key="a.label" style="display:flex;align-items:center;gap:10px;padding:12px 14px;background:#fff;border-radius:12px;border:1.5px solid #EDE8E0;">
              <svg v-if="a.icon==='wifi'"     width="17" height="17" fill="none" stroke="#ED730C" stroke-width="2" viewBox="0 0 24 24"><path d="M5 12.55a11 11 0 0114.08 0"/><path d="M1.42 9a16 16 0 0121.16 0"/><path d="M8.53 16.11a6 6 0 016.95 0"/><circle cx="12" cy="20" r="1" fill="#ED730C"/></svg>
              <svg v-if="a.icon==='ac'"       width="17" height="17" fill="none" stroke="#ED730C" stroke-width="2" viewBox="0 0 24 24"><rect x="2" y="6" width="20" height="8" rx="2"/><path d="M7 14v4M12 14v4M17 14v4"/></svg>
              <svg v-if="a.icon==='pool'"     width="17" height="17" fill="none" stroke="#ED730C" stroke-width="2" viewBox="0 0 24 24"><path d="M2 12h20M2 18h20"/></svg>
              <svg v-if="a.icon==='gym'"      width="17" height="17" fill="none" stroke="#ED730C" stroke-width="2" viewBox="0 0 24 24"><path d="M6 4v16M18 4v16M2 8h4M18 8h4M2 16h4M18 16h4M6 12h12"/></svg>
              <svg v-if="a.icon==='parking'"  width="17" height="17" fill="none" stroke="#ED730C" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M9 17V7h4a3 3 0 010 6H9"/></svg>
              <svg v-if="a.icon==='security'" width="17" height="17" fill="none" stroke="#ED730C" stroke-width="2" viewBox="0 0 24 24"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
              <svg v-if="a.icon==='pet'"      width="17" height="17" fill="none" stroke="#ED730C" stroke-width="2" viewBox="0 0 24 24"><circle cx="9" cy="7" r="2"/><circle cx="15" cy="7" r="2"/><circle cx="6" cy="12" r="2"/><circle cx="18" cy="12" r="2"/><path d="M12 22c-3 0-6-2-6-5s3-5 6-5 6 2 6 5-3 5-6 5z"/></svg>
              <svg v-if="a.icon==='balcony'"  width="17" height="17" fill="none" stroke="#ED730C" stroke-width="2" viewBox="0 0 24 24"><rect x="2" y="10" width="20" height="3"/><path d="M6 13v6M18 13v6M2 19h20M9 10V4h6v6"/></svg>
              <span style="font-size:0.82rem;font-weight:700;color:#3a3a3a;">{{ a.label }}</span>
            </div>
          </div>
        </div>

        <!-- Tags -->
        <div v-if="home.tags && home.tags.length" style="padding-bottom:28px;border-bottom:1px solid #F3F0EC;margin-bottom:28px;">
          <h2 style="font-size:1.1rem;font-weight:900;color:#1A1A1A;margin:0 0 14px;letter-spacing:-.02em;">Tags</h2>
          <div style="display:flex;flex-wrap:wrap;gap:8px;">
            <span v-for="tag in home.tags" :key="tag" style="font-size:0.8rem;font-weight:700;color:#4b5563;background:#F3F0EC;padding:6px 14px;border-radius:999px;border:1px solid #EDE8E0;">{{ tag }}</span>
          </div>
        </div>

        <!-- Swap terms -->
        <div v-if="home.swap_terms" style="background:#FFF4EC;border:1.5px solid #fed7aa;border-radius:16px;padding:20px 22px;margin-bottom:28px;">
          <div style="display:flex;align-items:center;gap:8px;margin-bottom:10px;">
            <svg width="15" height="15" fill="none" stroke="#ED730C" stroke-width="2" viewBox="0 0 24 24"><path d="M7 16l-4-4 4-4M17 8l4 4-4 4M14 4l-4 16"/></svg>
            <h3 style="font-size:0.8rem;font-weight:900;color:#92400e;margin:0;text-transform:uppercase;letter-spacing:.07em;">Swap Terms</h3>
          </div>
          <p style="font-size:0.88rem;color:#78350f;line-height:1.65;margin:0;">{{ home.swap_terms }}</p>
        </div>

        <!-- Map placeholder -->
        <div>
          <h2 style="font-size:1.1rem;font-weight:900;color:#1A1A1A;margin:0 0 14px;letter-spacing:-.02em;">Location</h2>
          <div style="border-radius:16px;overflow:hidden;border:1.5px solid #EDE8E0;background:#f3f4f6;height:220px;display:flex;align-items:center;justify-content:center;">
            <div style="text-align:center;">
              <svg width="28" height="28" fill="none" stroke="#9ca3af" stroke-width="1.5" viewBox="0 0 24 24" style="display:block;margin:0 auto 8px;"><path d="M21 10c0 7-9 13-9 13S3 17 3 10a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
              <p style="font-size:0.82rem;font-weight:700;color:#9ca3af;margin:0;">{{ home.location }}</p>
              <p style="font-size:0.7rem;color:#d1d5db;margin:4px 0 0;">Map coming soon</p>
            </div>
          </div>
        </div>

      </div>

      <!-- RIGHT COLUMN — STICKY CARD -->
      <div class="hd-sticky">
        <div style="background:#fff;border-radius:20px;border:1.5px solid #EDE8E0;box-shadow:0 8px 40px rgba(0,0,0,0.1);overflow:hidden;">

          <!-- Price -->
          <div style="padding:24px 24px 18px;border-bottom:1px solid #F3F0EC;">
            <p style="font-size:0.65rem;font-weight:800;letter-spacing:.1em;text-transform:uppercase;color:#9ca3af;margin:0 0 4px;">{{ priceNote }}</p>
            <p style="font-size:2rem;font-weight:900;color:#1A1A1A;margin:0;letter-spacing:-.03em;line-height:1;">{{ priceLabel }}</p>
            <div style="display:flex;align-items:center;gap:5px;margin-top:8px;">
              <svg width="12" height="12" fill="#f59e0b" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
              <span style="font-size:0.8rem;font-weight:800;color:#1A1A1A;">{{ home.rating }}</span>
              <span style="font-size:0.8rem;color:#9ca3af;">· {{ home.review_count }} reviews</span>
            </div>
          </div>

          <!-- Summary -->
          <div style="padding:16px 24px;border-bottom:1px solid #F3F0EC;display:flex;gap:0;">
            <div style="flex:1;text-align:center;padding:8px 0;">
              <p style="font-size:1rem;font-weight:900;color:#1A1A1A;margin:0;">{{ home.beds }}</p>
              <p style="font-size:0.65rem;font-weight:700;color:#9ca3af;margin:2px 0 0;text-transform:uppercase;letter-spacing:.05em;">Beds</p>
            </div>
            <div style="width:1px;background:#F3F0EC;"></div>
            <div style="flex:1;text-align:center;padding:8px 0;">
              <p style="font-size:1rem;font-weight:900;color:#1A1A1A;margin:0;">{{ home.baths }}</p>
              <p style="font-size:0.65rem;font-weight:700;color:#9ca3af;margin:2px 0 0;text-transform:uppercase;letter-spacing:.05em;">Baths</p>
            </div>
            <div style="width:1px;background:#F3F0EC;"></div>
            <div style="flex:1;text-align:center;padding:8px 0;">
              <p style="font-size:1rem;font-weight:900;color:#1A1A1A;margin:0;">{{ home.sqm }}m2</p>
              <p style="font-size:0.65rem;font-weight:700;color:#9ca3af;margin:2px 0 0;text-transform:uppercase;letter-spacing:.05em;">Area</p>
            </div>
          </div>

          <!-- CTAs -->
          <div style="padding:20px 24px;display:flex;flex-direction:column;gap:10px;">
            <button :style="{width:'100%',padding:'14px',background:'#ED730C',color:'#fff',border:'none',borderRadius:'14px',fontSize:'0.9rem',fontWeight:'900',cursor:'pointer',fontFamily:'DM Sans,sans-serif',letterSpacing:'.02em',boxShadow:'0 6px 20px rgba(237,115,12,0.35)',transition:'background .15s,transform .1s'}"
              onmouseover="this.style.background='#d4620a';this.style.transform='translateY(-1px)'"
              onmouseout="this.style.background='#ED730C';this.style.transform='translateY(0)'">
              {{ cfg.cta }}
            </button>
            <button style="width:100%;padding:13px;background:#fff;color:#1A1A1A;border:1.5px solid #EDE8E0;border-radius:14px;font-size:0.88rem;font-weight:800;cursor:pointer;font-family:DM Sans,sans-serif;transition:border-color .15s;"
              onmouseover="this.style.borderColor='#1A1A1A'" onmouseout="this.style.borderColor='#EDE8E0'">
              Message {{ home.owner.name.split(' ')[0] }}
            </button>
            <p style="font-size:0.72rem;color:#9ca3af;text-align:center;margin:2px 0 0;font-weight:600;">You won't be charged yet</p>
          </div>

          <!-- Owner mini -->
          <div style="padding:14px 24px 18px;border-top:1px solid #F3F0EC;display:flex;align-items:center;gap:10px;">
            <div :style="{width:'36px',height:'36px',borderRadius:'50%',background:home.owner.color,color:'#fff',fontSize:'0.7rem',fontWeight:'900',display:'flex',alignItems:'center',justifyContent:'center',flexShrink:0}">{{ home.owner.initials }}</div>
            <div style="flex:1;min-width:0;">
              <p style="font-size:0.78rem;font-weight:800;color:#1A1A1A;margin:0;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ home.owner.name }}</p>
              <p style="font-size:0.7rem;color:#9ca3af;font-weight:600;margin:2px 0 0;">Responds {{ home.response_time }}</p>
            </div>
            <div style="display:flex;align-items:center;gap:3px;flex-shrink:0;">
              <svg width="11" height="11" fill="#f59e0b" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
              <span style="font-size:0.75rem;font-weight:800;color:#1A1A1A;">{{ home.rating }}</span>
            </div>
          </div>

        </div>
        <p style="text-align:center;margin-top:14px;">
          <a href="#" style="font-size:0.72rem;color:#9ca3af;font-weight:600;text-decoration:underline;">Report this listing</a>
        </p>
      </div>

    </div>

    <!-- SIMILAR LISTINGS -->
    <div v-if="home.similar && home.similar.length" style="margin-top:56px;padding-top:40px;border-top:1.5px solid #EDE8E0;">
      <div style="display:flex;align-items:baseline;justify-content:space-between;margin-bottom:24px;">
        <div>
          <p style="font-size:0.65rem;font-weight:800;letter-spacing:.1em;text-transform:uppercase;color:#ED730C;margin:0 0 4px;">More to explore</p>
          <h2 style="font-size:1.35rem;font-weight:900;color:#1A1A1A;margin:0;letter-spacing:-.02em;">Similar Listings</h2>
        </div>
        <a href="/homes" style="font-size:0.8rem;font-weight:800;color:#ED730C;text-decoration:none;">View all</a>
      </div>
      <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:20px;">
        <a v-for="s in home.similar" :key="s.id" :href="'/homes/'+s.id"
          style="background:#fff;border-radius:18px;overflow:hidden;border:1px solid #EDE8E0;box-shadow:0 2px 12px rgba(0,0,0,0.06);text-decoration:none;display:block;transition:box-shadow .2s,transform .2s;"
          onmouseover="this.style.boxShadow='0 8px 28px rgba(0,0,0,0.11)';this.style.transform='translateY(-2px)'"
          onmouseout="this.style.boxShadow='0 2px 12px rgba(0,0,0,0.06)';this.style.transform='translateY(0)'">
          <div style="aspect-ratio:16/10;overflow:hidden;background:#f3f4f6;">
            <img :src="s.image" :alt="s.title" style="width:100%;height:100%;object-fit:cover;transition:transform .4s;" onmouseover="this.style.transform='scale(1.04)'" onmouseout="this.style.transform='scale(1)'">
          </div>
          <div style="padding:14px 16px 16px;">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:4px;">
              <span style="font-size:0.65rem;font-weight:800;color:#9ca3af;text-transform:uppercase;letter-spacing:.06em;">{{ s.type }} · {{ s.location }}</span>
              <div style="display:flex;align-items:center;gap:3px;">
                <svg width="10" height="10" fill="#f59e0b" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                <span style="font-size:0.72rem;font-weight:800;color:#1A1A1A;">{{ s.rating }}</span>
              </div>
            </div>
            <p style="font-size:0.88rem;font-weight:800;color:#1A1A1A;margin:0 0 4px;line-height:1.3;">{{ s.title }}</p>
            <p style="font-size:0.85rem;font-weight:900;color:#ED730C;margin:0;">
              {{ s.type === 'Sell' ? currency.symbol + (s.value/1000000).toFixed(1) + 'M' : formatMoney(s.value) + '/mo' }}
            </p>
          </div>
        </a>
      </div>
    </div>

  </div>
</div>
</template>

<style scoped>
/* ── Page frame ── */
.hd-crumb { background: #fff; border-bottom: 1px solid #EDE8E0; padding: 12px 40px; }
.hd-main  { max-width: 1280px; margin: 0 auto; padding: 32px 40px 80px; }

/* ── Photo gallery ── */
.gal-wrap { position: relative; border-radius: 20px; overflow: hidden; margin-bottom: 40px; }
.gal-img  { width: 100%; height: 100%; object-fit: cover; display: block; transition: transform .4s ease; }
.gal-cell { position: relative; overflow: hidden; cursor: pointer; background: #f3f4f6; }
.gal-cell:hover .gal-img { transform: scale(1.04); }

.gal-single    { height: 420px; }
.gal-two       { display: grid; grid-template-columns: 1fr 1fr; gap: 4px; height: 420px; }
.gal-hero-side { display: grid; grid-template-columns: 2fr 1fr; gap: 4px; height: 420px; }
.gal-side      { display: grid; gap: 4px; min-height: 0; }
.gal-five      { display: grid; grid-template-columns: 2fr 1fr 1fr; grid-template-rows: 1fr 1fr; gap: 4px; height: 420px; }
.gal-big       { grid-row: 1 / 3; }
.gal-more {
  position: absolute; inset: 0; background: rgba(0,0,0,0.45);
  color: #fff; display: flex; align-items: center; justify-content: center;
  font-size: 0.9rem; font-weight: 800;
}
.gal-all-btn {
  position: absolute; bottom: 16px; right: 16px;
  display: inline-flex; align-items: center; gap: 7px;
  background: #fff; border: 1px solid #1A1A1A; border-radius: 10px;
  padding: 9px 14px; font-family: 'DM Sans', sans-serif;
  font-size: 0.8rem; font-weight: 800; color: #1A1A1A;
  cursor: pointer; box-shadow: 0 2px 10px rgba(0,0,0,0.15);
}
.gal-all-btn:hover { background: #f7f7f7; }

/* ── Content columns ── */
.hd-columns { display: grid; grid-template-columns: 1fr 380px; gap: 48px; align-items: start; }
.hd-sticky  { position: sticky; top: 96px; }
.hd-stats {
  display: grid; grid-template-columns: repeat(4, 1fr);
  border: 1.5px solid #EDE8E0; border-radius: 16px;
  overflow: hidden; margin-bottom: 32px; background: #fff;
}

/* ── Responsive ── */
@media (max-width: 1024px) {
  .hd-columns { grid-template-columns: 1fr; gap: 32px; }
  .hd-sticky  { position: static; }
}
@media (max-width: 768px) {
  .hd-crumb { padding: 10px 16px; }
  .hd-main  { padding: 20px 16px 64px; }
  .gal-wrap { border-radius: 14px; }
  .gal-single, .gal-two, .gal-hero-side, .gal-five { height: 280px; }
  .gal-five { grid-template-columns: 2fr 1fr; }
  .gal-five .gal-cell:nth-child(n+4) { display: none; } /* hero + 2 on phones */
  .hd-stats { grid-template-columns: repeat(2, 1fr); }
  .hd-stats > div:nth-child(2n) { border-right: none !important; }
  .hd-stats > div:nth-child(-n+2) { border-bottom: 1.5px solid #EDE8E0; }
}
</style>