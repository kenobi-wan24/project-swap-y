<script setup>
import { ref, computed } from 'vue'

const el      = document.getElementById('garage-sale-app')
const sellers = ref(JSON.parse(el?.dataset.sellers || '[]'))

// ── filter state ──────────────────────────────────────────────────────────────
const search        = ref('')
const locationInput = ref('')
const sortBy        = ref('Most Items')
const loading       = ref(false)

const sortOptions = ['Most Items', 'Nearest First', 'Recently Active', 'Highest Rated']

// ── fake fallback sellers ─────────────────────────────────────────────────────
const fakeSellers = [
  { id:1, name:'Sarah L.',  username:'sarah_listening', rating:'5.0', distance:'3.5', active_since:'Active 2h ago', item_count:21, categories:['Audio','Electronics','Books'],    cover:'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=700&q=80', avatar:'https://i.pravatar.cc/48?img=47' },
  { id:2, name:'Julian M.', username:'julian_design',   rating:'4.9', distance:'1.2', active_since:'Active today',  item_count:14, categories:['Electronics','Cameras','Accessories'], cover:'https://images.unsplash.com/photo-1593642632559-0c6d3fc62b89?w=700&q=80', avatar:'https://i.pravatar.cc/48?img=12' },
  { id:3, name:'David K.',  username:'david_k',         rating:'4.8', distance:'7.3', active_since:'Active 3h ago', item_count:11, categories:['Gaming','Electronics','PC Parts'],  cover:'https://images.unsplash.com/photo-1587202372583-49330a15584d?w=700&q=80', avatar:'https://i.pravatar.cc/48?img=15' },
  { id:4, name:'Mia R.',    username:'mia_creates',     rating:'4.7', distance:'0.8', active_since:'Active now',    item_count:33, categories:['Fashion','Accessories','Vintage'],  cover:'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=700&q=80', avatar:'https://i.pravatar.cc/48?img=44' },
  { id:5, name:'Leo B.',    username:'leo_books',       rating:'4.9', distance:'2.4', active_since:'Active 5h ago', item_count:8,  categories:['Books','Collectibles','Art'],       cover:'https://images.unsplash.com/photo-1524995997946-a1c2e315a42f?w=700&q=80', avatar:'https://i.pravatar.cc/48?img=67' },
  { id:6, name:'Nina T.',   username:'nina_trades',     rating:'5.0', distance:'5.1', active_since:'Active today',  item_count:17, categories:['Home','Furniture','Decor'],         cover:'https://images.unsplash.com/photo-1555041469-a586c61ea9bc?w=700&q=80', avatar:'https://i.pravatar.cc/48?img=32' },
]

const displaySellers = computed(() => sellers.value.length ? sellers.value : fakeSellers)

// ── filtered + sorted ─────────────────────────────────────────────────────────
const filtered = computed(() => {
  let list = displaySellers.value
  if (search.value.trim())
    list = list.filter(s =>
      s.name.toLowerCase().includes(search.value.toLowerCase()) ||
      s.username.toLowerCase().includes(search.value.toLowerCase())
    )
  if (sortBy.value === 'Most Items')      list = [...list].sort((a,b) => b.item_count - a.item_count)
  if (sortBy.value === 'Nearest First')   list = [...list].sort((a,b) => parseFloat(a.distance) - parseFloat(b.distance))
  if (sortBy.value === 'Highest Rated')   list = [...list].sort((a,b) => b.rating - a.rating)
  return list
})

async function loadMore() {
  loading.value = true
  await new Promise(r => setTimeout(r, 700))
  loading.value = false
}
</script>

<template>
<div style="min-height:100vh;background:#fff;font-family:'DM Sans',sans-serif;">

  <!-- ══ HERO ══════════════════════════════════════════════════════════════ -->
  <section style="padding:52px 24px 40px;background:#fff;text-align:center;">
    <div style="max-width:860px;margin:0 auto;">

      <!-- Headline -->
      <div style="display:flex;align-items:baseline;gap:16px;flex-wrap:wrap;margin-bottom:28px;justify-content:center;">
        <h1 style="font-size:clamp(2.2rem,5vw,3.6rem);font-weight:900;line-height:1.05;letter-spacing:-.03em;color:#1A1A1A;margin:0;">Real people.</h1>
        <h1 style="font-size:clamp(2.2rem,5vw,3.6rem);font-weight:900;line-height:1.05;letter-spacing:-.03em;color:#ED730C;margin:0;">Real collections.</h1>
      </div>

      <!-- Desktop search pill -->
      <div class="gs-search-desktop" style="max-width:760px;margin:0 auto 20px;background:#fff;border-radius:999px;display:flex;align-items:center;padding:6px 6px 6px 20px;box-shadow:0 8px 32px rgba(0,0,0,0.12);border:1.5px solid #EBEBEB;">
        <div style="flex:1;display:flex;align-items:center;gap:8px;border-right:1px solid #EBEBEB;padding-right:16px;">
          <svg width="15" height="15" fill="none" stroke="#9ca3af" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
          <input v-model="search" type="text" placeholder="What are you looking for?"
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

      <!-- Mobile search pill -->
      <div class="gs-search-mobile" style="display:none;max-width:760px;margin:0 auto 20px;background:#fff;border-radius:999px;align-items:center;padding:5px 5px 5px 16px;box-shadow:0 4px 20px rgba(0,0,0,0.10);border:1.5px solid #EBEBEB;">
        <svg width="14" height="14" fill="none" stroke="#9ca3af" stroke-width="2" viewBox="0 0 24 24" style="flex-shrink:0;margin-right:8px;"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
        <input v-model="search" type="text" placeholder="Search"
          style="flex:1;border:none;outline:none;font-size:0.875rem;color:#1A1A1A;background:transparent;font-family:'DM Sans',sans-serif;min-width:0;">
        <div style="display:flex;align-items:center;gap:4px;padding:0 10px;border-left:1px solid #EBEBEB;flex-shrink:0;">
          <svg width="12" height="12" fill="#9ca3af" viewBox="0 0 24 24"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>
          <input v-model="locationInput" type="text" placeholder="Location"
            style="border:none;outline:none;font-size:0.82rem;color:#1A1A1A;background:transparent;font-family:'DM Sans',sans-serif;width:70px;">
        </div>
        <button style="background:#ED730C;color:#fff;border:none;border-radius:999px;padding:9px 18px;font-size:0.82rem;font-weight:800;cursor:pointer;font-family:'DM Sans',sans-serif;white-space:nowrap;flex-shrink:0;">Search</button>
      </div>

      <!-- Sort pills -->
      <div style="display:flex;gap:8px;flex-wrap:wrap;justify-content:center;">
        <button v-for="opt in sortOptions" :key="opt" @click="sortBy = opt"
          :style="{padding:'7px 16px',borderRadius:'999px',fontSize:'0.78rem',fontWeight:'600',fontFamily:'\'DM Sans\',sans-serif',cursor:'pointer',border:sortBy===opt?'none':'1px solid #EBEBEB',background:sortBy===opt?'#1A1A1A':'#fff',color:sortBy===opt?'#fff':'#4b5563',boxShadow:sortBy===opt?'0 4px 12px rgba(0,0,0,0.15)':'none',transition:'all 0.15s'}">
          {{ opt }}
        </button>
      </div>

    </div>
  </section>

  <!-- ══ SELLER GRID ═══════════════════════════════════════════════════════ -->
  <div style="max-width:1200px;margin:0 auto;padding:36px 24px 80px;">

    <!-- Count -->
    <p style="font-size:0.82rem;color:#9ca3af;margin-bottom:20px;">
      Showing <strong style="color:#1a1a1a;">{{ filtered.length }}</strong> garage sales near you
    </p>

    <!-- Grid -->
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(340px,1fr));gap:20px;">
      <a
        v-for="seller in filtered"
        :key="seller.username"
        :href="'/store/' + seller.username"
        class="gs-card"
        style="text-decoration:none;display:flex;flex-direction:column;background:#fff;border-radius:18px;overflow:hidden;border:1px solid #EDE8E0;box-shadow:0 4px 20px rgba(0,0,0,0.07);transition:border-color .2s,box-shadow .2s,transform .2s;"
      >
        <!-- Cover image -->
        <div style="position:relative;height:185px;overflow:hidden;background:#f3f4f6;flex-shrink:0;">
          <img
            :src="seller.cover || 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=700&q=80'"
            :alt="seller.name"
            class="gs-img"
            style="width:100%;height:100%;object-fit:cover;transition:transform .4s;"
          >
          <!-- Bottom gradient for readability -->
          <div style="position:absolute;inset:0;background:linear-gradient(to top,rgba(0,0,0,0.55) 0%,transparent 55%);"></div>

          <!-- Item count badge — top right -->
          <span style="position:absolute;top:11px;right:11px;background:rgba(0,0,0,0.52);color:#fff;font-size:0.68rem;font-weight:800;padding:4px 11px;border-radius:999px;backdrop-filter:blur(6px);letter-spacing:.03em;">
            {{ seller.item_count }} items
          </span>

          <!-- Category pills — bottom left, same style as Browse -->
          <div style="position:absolute;bottom:11px;left:12px;display:flex;gap:5px;flex-wrap:wrap;">
            <span
              v-for="cat in (seller.categories || []).slice(0,3)"
              :key="cat"
              style="background:rgba(255,255,255,0.92);color:#3A3330;font-size:0.62rem;font-weight:700;padding:3px 9px;border-radius:6px;letter-spacing:.04em;backdrop-filter:blur(4px);"
            >{{ cat }}</span>
          </div>
        </div>

        <!-- Seller info -->
        <div style="padding:18px 20px 20px;display:flex;flex-direction:column;flex:1;">

          <!-- Avatar + name -->
          <div style="display:flex;align-items:center;gap:12px;margin-bottom:14px;">
            <div style="position:relative;flex-shrink:0;">
              <img
                :src="seller.avatar || 'https://i.pravatar.cc/48?u=' + seller.username"
                :alt="seller.name"
                style="width:46px;height:46px;border-radius:50%;object-fit:cover;border:2.5px solid #fff;box-shadow:0 2px 8px rgba(0,0,0,0.12);"
              >
              <span style="position:absolute;bottom:0;right:0;width:12px;height:12px;background:#22c55e;border-radius:50%;border:2px solid #fff;"></span>
            </div>
            <div style="min-width:0;flex:1;">
              <div style="font-size:0.9375rem;font-weight:800;color:#1A1A1A;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;line-height:1.2;margin-bottom:2px;">
                {{ seller.name }}'s Garage Sale
              </div>
              <div style="font-size:0.75rem;color:#9ca3af;font-weight:500;">@{{ seller.username }}</div>
            </div>
            <span style="font-size:0.7rem;color:#9ca3af;white-space:nowrap;flex-shrink:0;">{{ seller.active_since }}</span>
          </div>

          <!-- Stats row -->
          <div style="display:flex;align-items:center;gap:16px;padding:12px 0;border-top:1px solid #f3f4f6;border-bottom:1px solid #f3f4f6;margin-bottom:16px;">
            <!-- Rating -->
            <div style="display:flex;align-items:center;gap:4px;">
              <svg width="12" height="12" fill="#f59e0b" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
              <span style="font-size:0.8rem;font-weight:700;color:#1A1A1A;">{{ seller.rating }}</span>
            </div>
            <!-- Divider -->
            <span style="width:1px;height:14px;background:#EDE8E0;display:inline-block;"></span>
            <!-- Distance -->
            <div style="display:flex;align-items:center;gap:4px;">
              <svg width="11" height="11" fill="#9ca3af" viewBox="0 0 24 24"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z"/></svg>
              <span style="font-size:0.78rem;color:#9ca3af;">{{ seller.distance }} mi away</span>
            </div>
            <!-- Divider -->
            <span style="width:1px;height:14px;background:#EDE8E0;display:inline-block;"></span>
            <!-- Item count -->
            <div style="display:flex;align-items:center;gap:4px;">
              <svg width="11" height="11" fill="none" stroke="#9ca3af" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 10V11"/></svg>
              <span style="font-size:0.78rem;color:#9ca3af;"><strong style="color:#1A1A1A;">{{ seller.item_count }}</strong> items</span>
            </div>
          </div>

          <!-- CTA button — full width pill matching Browse's "Swap Now" -->
          <div style="display:flex;align-items:center;gap:10px;">
            <span class="gs-cta"
              style="flex:1;display:flex;align-items:center;justify-content:center;gap:7px;background:#ED730C;color:#fff;font-size:0.8125rem;font-weight:800;padding:11px 16px;border-radius:999px;letter-spacing:.03em;font-family:'DM Sans',sans-serif;box-shadow:0 4px 12px rgba(237,115,12,0.28);transition:all .15s;">
              Visit Sale
              <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
            </span>
          </div>

        </div>
      </a>
    </div>

    <!-- Empty state -->
    <div v-if="filtered.length === 0" style="text-align:center;padding:80px 0;">
      <div style="font-size:3.5rem;margin-bottom:16px;">🏷️</div>
      <h3 style="font-size:1.125rem;font-weight:800;color:#1A1A1A;margin-bottom:8px;">No garage sales found</h3>
      <p style="font-size:0.875rem;color:#9ca3af;margin-bottom:20px;">Try a different search term.</p>
      <button @click="search=''"
        style="font-size:0.82rem;font-weight:700;color:#fff;background:#ED730C;border:none;border-radius:999px;padding:11px 28px;cursor:pointer;font-family:'DM Sans',sans-serif;box-shadow:0 4px 12px rgba(237,115,12,0.28);">
        Clear Search
      </button>
    </div>

    <!-- Load more -->
    <div v-if="filtered.length > 0" style="text-align:center;margin-top:52px;">
      <button @click="loadMore" :disabled="loading"
        style="display:inline-flex;align-items:center;gap:8px;padding:13px 32px;background:#fff;border:1.5px solid #1A1A1A;border-radius:999px;font-size:0.82rem;font-weight:800;color:#1A1A1A;cursor:pointer;font-family:'DM Sans',sans-serif;transition:all .15s;"
        onmouseover="this.style.background='#1A1A1A';this.style.color='#fff'"
        onmouseout="this.style.background='#fff';this.style.color='#1A1A1A'">
        <svg v-if="!loading" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" d="M19 9l-7 7-7-7"/></svg>
        <span>{{ loading ? 'Loading...' : 'Load More Sellers' }}</span>
      </button>
      <p style="margin-top:10px;font-size:0.78rem;color:#9ca3af;">Showing {{ filtered.length }} of {{ displaySellers.length }} sellers</p>
    </div>

  </div>
</div>
</template>

<style scoped>
/* ── Mobile search pill ── */
@media (max-width: 767px) {
  .gs-search-desktop { display: none !important; }
  .gs-search-mobile  { display: flex !important; }
}

/* ── Card hover ── */
.gs-card {
  transition: border-color .2s, box-shadow .2s, transform .2s;
}
.gs-card:hover {
  border-color: #ED730C !important;
  box-shadow: 0 10px 36px rgba(237,115,12,0.12) !important;
  transform: translateY(-3px);
}
.gs-card:hover .gs-img {
  transform: scale(1.05);
}

/* ── CTA hover ── */
.gs-cta:hover {
  background: #d4620a !important;
  box-shadow: 0 6px 18px rgba(237,115,12,0.40) !important;
  transform: translateY(-1px);
}
</style>