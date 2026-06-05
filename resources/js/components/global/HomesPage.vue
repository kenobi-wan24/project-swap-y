<script setup>
import { ref, computed } from 'vue'

const el     = document.getElementById('homes-app')
const homes  = ref(JSON.parse(el?.dataset.homes || '[]'))

// ── filter state ──────────────────────────────────────────────────────────────
const search      = ref('')
const activeType  = ref('All')
const activeBeds  = ref('Any')
const sortBy      = ref('Newest')

const listingTypes = ['All', 'Swap', 'Rent', 'Sell', 'Co-living']
const bedOptions   = ['Any', 'Studio', '1', '2', '3', '4+']
const sortOptions  = ['Newest', 'Nearest First', 'Price: Low–High', 'Price: High–Low', 'Highest Rated']

// ── fallback data ─────────────────────────────────────────────────────────────
const fakeHomes = [
  {
    id: 1,
    type: 'Swap',
    title: 'Modern Studio in BGC',
    location: 'Bonifacio Global City, Taguig',
    distance: '1.2',
    beds: 'Studio', baths: 1, sqm: 32,
    value: 18000,
    swap_terms: 'Open to swap with condo in Makati or Ortigas',
    tags: ['Furnished', 'Pet-friendly', 'High-floor'],
    rating: 4.9,
    owner: 'Marco R.',
    owner_initials: 'MR',
    owner_color: '#ED730C',
    listed_at: '2h ago',
    images: ['https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?w=800&q=80'],
  },
  {
    id: 2,
    type: 'Rent',
    title: '2BR Condo with Pool View',
    location: 'Salcedo Village, Makati',
    distance: '2.8',
    beds: '2', baths: 2, sqm: 68,
    value: 35000,
    swap_terms: null,
    tags: ['Semi-furnished', 'With parking', 'Near MRT'],
    rating: 4.7,
    owner: 'Angela T.',
    owner_initials: 'AT',
    owner_color: '#14b8a6',
    listed_at: 'Today',
    images: ['https://images.unsplash.com/photo-1493809842364-78817add7ffb?w=800&q=80'],
  },
  {
    id: 3,
    type: 'Sell',
    title: 'Townhouse in Paranaque',
    location: 'BF Homes, Paranaque City',
    distance: '5.4',
    beds: '3', baths: 2, sqm: 120,
    value: 6500000,
    swap_terms: 'Open to partial swap + cash top-up',
    tags: ['With garage', 'Corner lot', 'Renovated'],
    rating: 5.0,
    owner: 'Dennis L.',
    owner_initials: 'DL',
    owner_color: '#8b5cf6',
    listed_at: '1d ago',
    images: ['https://images.unsplash.com/photo-1568605114967-8130f3a36994?w=800&q=80'],
  },
  {
    id: 4,
    type: 'Co-living',
    title: 'Private Room in Co-living Hub',
    location: 'Poblacion, Makati',
    distance: '3.1',
    beds: '1', baths: 1, sqm: 18,
    value: 8500,
    swap_terms: 'Open to skill-swap for rent offset',
    tags: ['All utilities', 'Fast WiFi', 'Cowork space'],
    rating: 4.8,
    owner: 'Carla M.',
    owner_initials: 'CM',
    owner_color: '#f59e0b',
    listed_at: '3h ago',
    images: ['https://images.unsplash.com/photo-1555854877-bab0e564b8d5?w=800&q=80'],
  },
  {
    id: 5,
    type: 'Swap',
    title: '1BR Loft in Eastwood City',
    location: 'Eastwood City, Quezon City',
    distance: '4.7',
    beds: '1', baths: 1, sqm: 45,
    value: 22000,
    swap_terms: 'Swap for 1BR in Pasig or Ortigas area',
    tags: ['Loft-style', 'City view', 'Fully furnished'],
    rating: 4.6,
    owner: 'Jose P.',
    owner_initials: 'JP',
    owner_color: '#ec4899',
    listed_at: '5h ago',
    images: ['https://images.unsplash.com/photo-1502672260266-1c1ef2d93688?w=800&q=80'],
  },
  {
    id: 6,
    type: 'Rent',
    title: 'Spacious 3BR House with Garden',
    location: 'Filinvest, Alabang',
    distance: '8.2',
    beds: '3', baths: 3, sqm: 180,
    value: 55000,
    swap_terms: null,
    tags: ['Garden', 'With helper\'s room', 'Quiet village'],
    rating: 4.9,
    owner: 'Ruth V.',
    owner_initials: 'RV',
    owner_color: '#149189',
    listed_at: '2d ago',
    images: ['https://images.unsplash.com/photo-1570129477492-45c003edd2be?w=800&q=80'],
  },
]

const displayHomes = computed(() => homes.value.length ? homes.value : fakeHomes)

// ── filter + sort ──────────────────────────────────────────────────────────────
const filtered = computed(() => {
  let list = displayHomes.value

  if (search.value.trim())
    list = list.filter(h =>
      h.title.toLowerCase().includes(search.value.toLowerCase()) ||
      h.location.toLowerCase().includes(search.value.toLowerCase())
    )

  if (activeType.value !== 'All')
    list = list.filter(h => h.type === activeType.value)

  if (activeBeds.value !== 'Any')
    list = list.filter(h => h.beds === activeBeds.value)

  if (sortBy.value === 'Newest')            list = [...list]
  if (sortBy.value === 'Nearest First')     list = [...list].sort((a,b) => parseFloat(a.distance) - parseFloat(b.distance))
  if (sortBy.value === 'Price: Low–High')   list = [...list].sort((a,b) => a.value - b.value)
  if (sortBy.value === 'Price: High–Low')   list = [...list].sort((a,b) => b.value - a.value)
  if (sortBy.value === 'Highest Rated')     list = [...list].sort((a,b) => b.rating - a.rating)

  return list
})

// ── type badge config ─────────────────────────────────────────────────────────
const typeCfg = {
  'Swap':      { color: '#ED730C', bg: '#FFF4EC', border: '#fed7aa' },
  'Rent':      { color: '#14b8a6', bg: '#EDFAF9', border: '#99f6e4' },
  'Sell':      { color: '#8b5cf6', bg: '#F5F3FF', border: '#ddd6fe' },
  'Co-living': { color: '#f59e0b', bg: '#FFFBEB', border: '#fde68a' },
}

// ── price formatter ────────────────────────────────────────────────────────────
function formatValue(home) {
  if (home.type === 'Sell') return '₱' + (home.value / 1000000).toFixed(1) + 'M'
  return '₱' + home.value.toLocaleString() + '/mo'
}

// ── wishlisted ────────────────────────────────────────────────────────────────
const wishlisted = ref(new Set())
function toggleWish(id) {
  const s = new Set(wishlisted.value)
  s.has(id) ? s.delete(id) : s.add(id)
  wishlisted.value = s
}
</script>

<template>
<div style="min-height:100vh;background:#FAF8F5;font-family:DM Sans,sans-serif;">

  <!-- ══ HERO ══════════════════════════════════════════════════════════════ -->
  <section style="padding:72px 40px 56px;text-align:center;background:#fff;border-bottom:1px solid #EDE8E0;">
    <div style="max-width:1280px;margin:0 auto;">

      <p style="font-size:0.65rem;font-weight:800;letter-spacing:.12em;text-transform:uppercase;color:#ED730C;margin:0 0 14px;">Home Listings</p>

      <h1 style="font-size:clamp(2rem,4vw,3rem);font-weight:900;color:#1A1A1A;line-height:1.1;letter-spacing:-.03em;margin:0 0 12px;">
        Find your next home. <span style="color:#ED730C;">Swap, rent, or buy.</span>
      </h1>
      <p style="font-size:0.9375rem;color:#6b7280;line-height:1.65;margin:0 auto 32px;max-width:440px;">
        Browse homes from real people — trade your space, find a rental, or secure your next property. No agents, no fees.
      </p>

      <!-- Search pill -->
      <div style="background:#fff;border-radius:999px;display:flex;align-items:center;padding:7px 7px 7px 20px;box-shadow:0 4px 24px rgba(0,0,0,0.09);border:1.5px solid #EDE8E0;max-width:560px;margin:0 auto;">
        <svg width="15" height="15" fill="none" stroke="#9ca3af" stroke-width="2" viewBox="0 0 24 24" style="flex-shrink:0;"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
        <input v-model="search" type="text" placeholder="Search by location, city, or title..."
          style="flex:1;border:none;outline:none;font-size:0.875rem;color:#1A1A1A;background:transparent;font-family:DM Sans,sans-serif;margin:0 10px;">
        <button v-if="search" @click="search=''"
          style="width:28px;height:28px;border-radius:50%;background:#f3f4f6;border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-right:4px;">
          <svg width="10" height="10" fill="none" stroke="#9ca3af" stroke-width="2.5" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        </button>
        <button style="background:#ED730C;color:#fff;border:none;border-radius:999px;padding:10px 24px;font-size:0.82rem;font-weight:800;cursor:pointer;font-family:DM Sans,sans-serif;white-space:nowrap;box-shadow:0 4px 12px rgba(237,115,12,0.35);flex-shrink:0;"
          onmouseover="this.style.background='#d4620a'" onmouseout="this.style.background='#ED730C'">
          Search
        </button>
      </div>

    </div>
  </section>

  <!-- ══ FILTER BAR ════════════════════════════════════════════════════════ -->
  <section style="background:#fff;border-bottom:1px solid #EDE8E0;position:sticky;top:0;z-index:40;">
    <div style="max-width:1280px;margin:0 auto;padding:0 40px;display:flex;align-items:center;gap:0;justify-content:space-between;flex-wrap:wrap;">

      <!-- Listing type tabs -->
      <div style="display:flex;align-items:center;gap:0;">
        <button v-for="t in listingTypes" :key="t" @click="activeType = t"
          :style="{
            padding:'16px 20px',
            fontSize:'0.82rem',
            fontWeight: activeType===t ? '800' : '600',
            color: activeType===t ? '#ED730C' : '#6b7280',
            background:'transparent',
            border:'none',
            borderBottom: activeType===t ? '2.5px solid #ED730C' : '2.5px solid transparent',
            cursor:'pointer',
            fontFamily:'DM Sans,sans-serif',
            transition:'all .15s',
            whiteSpace:'nowrap',
          }">
          {{ t }}
        </button>
      </div>

      <!-- Right controls -->
      <div style="display:flex;align-items:center;gap:12px;padding:12px 0;">

        <!-- Bedrooms filter -->
        <div style="display:flex;align-items:center;gap:6px;">
          <span style="font-size:0.75rem;font-weight:700;color:#9ca3af;">Beds:</span>
          <div style="display:flex;gap:4px;">
            <button v-for="b in bedOptions" :key="b" @click="activeBeds = b"
              :style="{
                padding:'5px 11px',
                fontSize:'0.75rem',
                fontWeight:'700',
                borderRadius:'999px',
                border:'1.5px solid',
                borderColor: activeBeds===b ? '#ED730C' : '#EDE8E0',
                background: activeBeds===b ? '#FFF4EC' : '#fff',
                color: activeBeds===b ? '#ED730C' : '#6b7280',
                cursor:'pointer',
                fontFamily:'DM Sans,sans-serif',
                transition:'all .15s',
              }">
              {{ b }}
            </button>
          </div>
        </div>

        <!-- Sort dropdown -->
        <select v-model="sortBy"
          style="padding:7px 14px;border-radius:999px;border:1.5px solid #EDE8E0;font-size:0.78rem;font-weight:700;color:#1A1A1A;background:#fff;font-family:DM Sans,sans-serif;cursor:pointer;outline:none;">
          <option v-for="s in sortOptions" :key="s" :value="s">{{ s }}</option>
        </select>

        <!-- Result count -->
        <span style="font-size:0.78rem;font-weight:700;color:#9ca3af;white-space:nowrap;">
          {{ filtered.length }} listing{{ filtered.length !== 1 ? 's' : '' }}
        </span>

      </div>
    </div>
  </section>

  <!-- ══ MAIN CONTENT ═══════════════════════════════════════════════════════ -->
  <section style="padding:40px 40px 64px;">
    <div style="max-width:1280px;margin:0 auto;">

      <!-- Section label -->
      <div style="display:flex;align-items:baseline;justify-content:space-between;margin-bottom:24px;">
        <div>
          <p style="font-size:0.65rem;font-weight:800;letter-spacing:.1em;text-transform:uppercase;color:#ED730C;margin:0 0 4px;">
            {{ activeType === 'All' ? 'All Listings' : activeType + ' Listings' }}
          </p>
          <h2 style="font-size:1.35rem;font-weight:900;color:#1A1A1A;margin:0;letter-spacing:-.02em;">
            Homes Near You
          </h2>
        </div>
        <a href="/homes/post" style="display:inline-flex;align-items:center;gap:7px;padding:10px 20px;background:#ED730C;color:#fff;border-radius:999px;font-size:0.8rem;font-weight:800;text-decoration:none;box-shadow:0 4px 12px rgba(237,115,12,0.28);">
          <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
          Post a Home
        </a>
      </div>

      <!-- Empty state -->
      <div v-if="filtered.length === 0"
        style="background:#fff;border-radius:20px;border:1px solid #EDE8E0;padding:72px 40px;text-align:center;">
        <div style="font-size:2.5rem;margin-bottom:12px;">🏠</div>
        <p style="font-size:0.9rem;font-weight:700;color:#1A1A1A;margin:0 0 4px;">No listings found</p>
        <p style="font-size:0.8rem;color:#9ca3af;margin:0;">Try adjusting your filters or search terms.</p>
      </div>

      <!-- Card grid -->
      <div v-else style="display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:22px;">

        <div v-for="home in filtered" :key="home.id" class="home-card"
          style="background:#fff;border-radius:20px;overflow:hidden;border:1px solid #EDE8E0;box-shadow:0 2px 12px rgba(0,0,0,0.06);display:flex;flex-direction:column;cursor:pointer;transition:box-shadow .2s,transform .2s;"
          onmouseover="this.style.boxShadow='0 8px 32px rgba(0,0,0,0.12)';this.style.transform='translateY(-2px)'"
          onmouseout="this.style.boxShadow='0 2px 12px rgba(0,0,0,0.06)';this.style.transform='translateY(0)'">

          <!-- Cover image -->
          <div style="position:relative;aspect-ratio:16/10;overflow:hidden;background:#f3f4f6;flex-shrink:0;">
            <img v-if="home.images && home.images[0]" :src="home.images[0]" :alt="home.title"
              style="width:100%;height:100%;object-fit:cover;transition:transform .4s;"
              onmouseover="this.style.transform='scale(1.04)'" onmouseout="this.style.transform='scale(1)'"
              @error="e => e.target.style.display='none'">
            <div v-else style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;background:#f3f4f6;">
              <svg width="40" height="40" fill="none" stroke="#d1d5db" stroke-width="1.5" viewBox="0 0 24 24"><path d="M3 9.5L12 3l9 6.5V20a1 1 0 01-1 1H4a1 1 0 01-1-1V9.5z"/><path d="M9 21V12h6v9"/></svg>
            </div>

            <!-- Type badge -->
            <span :style="{
              position:'absolute', top:'12px', left:'12px',
              background: typeCfg[home.type]?.bg,
              color: typeCfg[home.type]?.color,
              border: `1.5px solid ${typeCfg[home.type]?.border}`,
              fontSize:'0.62rem', fontWeight:'800', padding:'4px 11px',
              borderRadius:'999px', letterSpacing:'.07em', textTransform:'uppercase',
            }">{{ home.type }}</span>

            <!-- Wishlist -->
            <button @click.stop="toggleWish(home.id)"
              :style="{
                position:'absolute', top:'10px', right:'10px',
                width:'32px', height:'32px', borderRadius:'50%',
                background: wishlisted.has(home.id) ? '#ED730C' : 'rgba(255,255,255,0.92)',
                border:'none', cursor:'pointer', display:'flex', alignItems:'center', justifyContent:'center',
                boxShadow:'0 2px 8px rgba(0,0,0,0.12)', transition:'background .15s',
              }">
              <svg width="14" height="14" viewBox="0 0 24 24"
                :fill="wishlisted.has(home.id) ? '#fff' : 'none'"
                :stroke="wishlisted.has(home.id) ? '#fff' : '#6b7280'"
                stroke-width="2"><path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z"/></svg>
            </button>

            <!-- Listed at -->
            <span style="position:absolute;bottom:10px;right:12px;font-size:0.62rem;font-weight:700;color:#fff;background:rgba(0,0,0,0.42);padding:3px 9px;border-radius:999px;backdrop-filter:blur(4px);">
              {{ home.listed_at }}
            </span>
          </div>

          <!-- Card body -->
          <div style="padding:16px 18px 18px;display:flex;flex-direction:column;flex:1;">

            <!-- Title + location -->
            <h3 style="font-size:0.95rem;font-weight:800;color:#1A1A1A;margin:0 0 4px;line-height:1.3;letter-spacing:-.01em;">{{ home.title }}</h3>
            <div style="display:flex;align-items:center;gap:4px;margin-bottom:12px;">
              <svg width="11" height="11" fill="none" stroke="#9ca3af" stroke-width="2" viewBox="0 0 24 24"><path d="M21 10c0 7-9 13-9 13S3 17 3 10a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
              <span style="font-size:0.75rem;color:#9ca3af;font-weight:600;">{{ home.location }}</span>
              <span style="font-size:0.75rem;color:#d1d5db;margin:0 2px;">·</span>
              <span style="font-size:0.75rem;color:#9ca3af;font-weight:600;">{{ home.distance }} mi</span>
            </div>

            <!-- Stats row: beds / baths / sqm -->
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

            <!-- Tags -->
            <div style="display:flex;flex-wrap:wrap;gap:6px;margin-bottom:14px;">
              <span v-for="tag in home.tags" :key="tag"
                style="font-size:0.68rem;font-weight:700;color:#6b7280;background:#F3F0EC;padding:3px 10px;border-radius:999px;">
                {{ tag }}
              </span>
            </div>

            <!-- Swap terms (only for Swap + Co-living) -->
            <div v-if="home.swap_terms"
              style="background:#FFF4EC;border:1px solid #fed7aa;border-radius:10px;padding:8px 12px;margin-bottom:14px;display:flex;align-items:flex-start;gap:7px;">
              <svg width="13" height="13" fill="none" stroke="#ED730C" stroke-width="2" viewBox="0 0 24 24" style="flex-shrink:0;margin-top:1px;"><path d="M7 16l-4-4 4-4M17 8l4 4-4 4M14 4l-4 16"/></svg>
              <p style="font-size:0.72rem;font-weight:600;color:#92400e;margin:0;line-height:1.45;">{{ home.swap_terms }}</p>
            </div>

            <!-- Price + Owner row -->
            <div style="display:flex;align-items:center;justify-content:space-between;margin-top:auto;">
              <div>
                <p style="font-size:0.62rem;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.06em;margin:0 0 2px;">
                  {{ home.type === 'Sell' ? 'Asking Price' : home.type === 'Swap' ? 'Est. Value' : 'Monthly Rate' }}
                </p>
                <p style="font-size:1.05rem;font-weight:900;color:#1A1A1A;margin:0;letter-spacing:-.02em;">{{ formatValue(home) }}</p>
              </div>
              <div style="display:flex;align-items:center;gap:7px;">
                <div :style="{
                  width:'30px', height:'30px', borderRadius:'50%',
                  background: home.owner_color, color:'#fff',
                  fontSize:'0.65rem', fontWeight:'800',
                  display:'flex', alignItems:'center', justifyContent:'center',
                }">{{ home.owner_initials }}</div>
                <div>
                  <p style="font-size:0.72rem;font-weight:700;color:#1A1A1A;margin:0;line-height:1.2;">{{ home.owner }}</p>
                  <div style="display:flex;align-items:center;gap:3px;">
                    <svg width="10" height="10" fill="#f59e0b" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                    <span style="font-size:0.68rem;font-weight:700;color:#6b7280;">{{ home.rating }}</span>
                  </div>
                </div>
              </div>
            </div>

            <!-- CTA -->
            <a :href="'/homes/' + home.id"
              style="display:block;margin-top:14px;padding:11px;background:#ED730C;color:#fff;border-radius:12px;font-size:0.82rem;font-weight:800;text-align:center;text-decoration:none;letter-spacing:.02em;transition:background .15s;box-shadow:0 4px 12px rgba(237,115,12,0.25);"
              onmouseover="this.style.background='#d4620a'" onmouseout="this.style.background='#ED730C'">
              View Listing →
            </a>

          </div>
        </div>

      </div>
    </div>
  </section>

</div>
</template>