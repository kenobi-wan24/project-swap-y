<script setup>
import { ref, computed } from 'vue'

const el  = document.getElementById('garage-sale-detail-app')
const raw = JSON.parse(el?.dataset.sale || '{}')

// ── fallback demo data ────────────────────────────────────────────────────────
const sale = ref(Object.keys(raw).length ? raw : {
  seller: {
    id: 1,
    name: 'Sarah L.',
    username: 'sarah_listening',
    avatar: 'https://i.pravatar.cc/120?img=47',
    initials: 'SL',
    color: '#ED730C',
    rating: 5.0,
    review_count: 38,
    member_since: 'Feb 2022',
    verified: true,
    response_time: 'within 30 mins',
    response_rate: 99,
    location: 'Quezon City, Metro Manila',
    distance: '3.5',
    active_since: 'Active 2h ago',
    bio: 'Audiophile, photographer, and avid reader. Everything I list is well-loved and honestly described. Open to creative swaps — just reach out!',
    total_swaps: 47,
    followers: 124,
  },
  item_count: 21,
  categories: ['Audio', 'Electronics', 'Books'],
  cover: 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=1400&q=85',
  items: [
    { id:1,  title:'Sony WH-1000XM5',        category:'Audio',       condition:'Like New', value:8500,  wants:'IEM or DAC',          image:'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=600&q=80',  swap_terms:'Open to swap for quality IEM or portable DAC' },
    { id:2,  title:'Fujifilm X-T30 II',       category:'Electronics', condition:'Good',     value:22000, wants:'Mirrorless Lens',      image:'https://images.unsplash.com/photo-1516035069371-29a1b244cc32?w=600&q=80',  swap_terms:'Looking for Fuji XF lenses, 35mm preferred' },
    { id:3,  title:'Mechanical Keyboard TKL', category:'Electronics', condition:'Mint',     value:3200,  wants:'Wireless Mouse',       image:'https://images.unsplash.com/photo-1618384887929-16ec33fab9ef?w=600&q=80',  swap_terms:null },
    { id:4,  title:'Atomic Habits (hardback)',category:'Books',       condition:'Good',     value:350,   wants:'Any self-help book',   image:'https://images.unsplash.com/photo-1544947950-fa07a98d237f?w=600&q=80',  swap_terms:null },
    { id:5,  title:'Bose SoundLink Mini II',  category:'Audio',       condition:'Good',     value:4800,  wants:'Earbuds or speaker',   image:'https://images.unsplash.com/photo-1608043152269-423dbba4e7e1?w=600&q=80',  swap_terms:'Open to any decent Bluetooth earbuds' },
    { id:6,  title:'Canon EF 50mm f/1.8',    category:'Electronics', condition:'Like New', value:5500,  wants:'Wide angle lens',      image:'https://images.unsplash.com/photo-1510127034890-ba27508e9f1c?w=600&q=80',  swap_terms:'Prefer Canon EF mount wide angle' },
    { id:7,  title:'Thinking Fast and Slow',  category:'Books',       condition:'Mint',     value:420,   wants:'Psychology/Finance',   image:'https://images.unsplash.com/photo-1589998059171-988d887df646?w=600&q=80',  swap_terms:null },
    { id:8,  title:'Rode VideoMicro',         category:'Audio',       condition:'Like New', value:3800,  wants:'Lavalier mic',         image:'https://images.unsplash.com/photo-1598550476439-6847785fcea6?w=600&q=80',  swap_terms:'Any quality lavalier or wireless mic system' },
  ],
})

// ── filter state ──────────────────────────────────────────────────────────────
const activeCategory = ref('All')
const search         = ref('')
const sortBy         = ref('Default')
const sortOptions    = ['Default', 'Price: Low-High', 'Price: High-Low', 'Condition']

const conditionCfg = {
  'Mint':      { color:'#149189', bg:'#EDFAF9', border:'#99f6e4' },
  'Like New':  { color:'#8b5cf6', bg:'#F5F3FF', border:'#ddd6fe' },
  'Good':      { color:'#f59e0b', bg:'#FFFBEB', border:'#fde68a' },
  'Fair':      { color:'#9ca3af', bg:'#f3f4f6', border:'#e5e7eb' },
}

const allCategories = computed(() => {
  const cats = new Set(sale.value.items.map(i => i.category))
  return ['All', ...cats]
})

const filtered = computed(() => {
  let list = sale.value.items
  if (activeCategory.value !== 'All')
    list = list.filter(i => i.category === activeCategory.value)
  if (search.value.trim())
    list = list.filter(i => i.title.toLowerCase().includes(search.value.toLowerCase()))
  if (sortBy.value === 'Price: Low-High')  list = [...list].sort((a,b) => a.value - b.value)
  if (sortBy.value === 'Price: High-Low')  list = [...list].sort((a,b) => b.value - a.value)
  if (sortBy.value === 'Condition') {
    const order = { 'Mint':0, 'Like New':1, 'Good':2, 'Fair':3 }
    list = [...list].sort((a,b) => (order[a.condition]??9) - (order[b.condition]??9))
  }
  return list
})

// ── wishlist ──────────────────────────────────────────────────────────────────
const wishlisted = ref(new Set())
function toggleWish(id) {
  const s = new Set(wishlisted.value)
  s.has(id) ? s.delete(id) : s.add(id)
  wishlisted.value = s
}
</script>

<template>
<div style="min-height:100vh;background:#FAF8F5;font-family:DM Sans,sans-serif;">

  <!-- BREADCRUMB -->
  <div style="background:#fff;border-bottom:1px solid #EDE8E0;padding:12px 40px;">
    <div style="max-width:1280px;margin:0 auto;display:flex;align-items:center;gap:6px;font-size:0.78rem;font-weight:600;color:#9ca3af;">
      <a href="/garage-sale" style="color:#9ca3af;text-decoration:none;" onmouseover="this.style.color='#ED730C'" onmouseout="this.style.color='#9ca3af'">Garage Sales</a>
      <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg>
      <span style="color:#1A1A1A;">{{ sale.seller.name }}'s Sale</span>
    </div>
  </div>

  <!-- SELLER HERO -->
  <div style="position:relative;">

    <!-- Cover image -->
    <div style="height:280px;overflow:hidden;position:relative;background:#1A1A1A;">
      <img v-if="sale.cover" :src="sale.cover" alt="Cover"
        style="width:100%;height:100%;object-fit:cover;opacity:0.55;">
      <!-- Gradient overlay -->
      <div style="position:absolute;inset:0;background:linear-gradient(to bottom, transparent 30%, rgba(26,26,26,0.85) 100%);"></div>

      <!-- Cover content -->
      <div style="position:absolute;bottom:0;left:0;right:0;padding:28px 40px;">
        <div style="max-width:1280px;margin:0 auto;display:flex;align-items:flex-end;justify-content:space-between;flex-wrap:wrap;gap:16px;">
          <div style="display:flex;align-items:flex-end;gap:18px;">
            <!-- Avatar -->
            <div style="position:relative;flex-shrink:0;">
              <img v-if="sale.seller.avatar" :src="sale.seller.avatar"
                style="width:72px;height:72px;border-radius:50%;object-fit:cover;border:3px solid #fff;box-shadow:0 4px 16px rgba(0,0,0,0.3);">
              <div v-else :style="{width:'72px',height:'72px',borderRadius:'50%',background:sale.seller.color,color:'#fff',fontSize:'1.4rem',fontWeight:'900',display:'flex',alignItems:'center',justifyContent:'center',border:'3px solid #fff'}">
                {{ sale.seller.initials }}
              </div>
              <!-- Online dot -->
              <div style="position:absolute;bottom:4px;right:4px;width:14px;height:14px;background:#22c55e;border-radius:50%;border:2px solid #fff;"></div>
            </div>
            <!-- Name + meta -->
            <div style="padding-bottom:4px;">
              <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">
                <h1 style="font-size:1.5rem;font-weight:900;color:#fff;margin:0;letter-spacing:-.02em;">{{ sale.seller.name }}'s Garage Sale</h1>
                <svg v-if="sale.seller.verified" width="18" height="18" fill="#14b8a6" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
              </div>
              <div style="display:flex;align-items:center;gap:12px;flex-wrap:wrap;">
                <span style="font-size:0.78rem;color:rgba(255,255,255,0.75);font-weight:600;">@{{ sale.seller.username }}</span>
                <span style="color:rgba(255,255,255,0.3);">·</span>
                <div style="display:flex;align-items:center;gap:4px;">
                  <svg width="12" height="12" fill="#f59e0b" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                  <span style="font-size:0.78rem;color:rgba(255,255,255,0.85);font-weight:700;">{{ sale.seller.rating }}</span>
                  <span style="font-size:0.78rem;color:rgba(255,255,255,0.55);">({{ sale.seller.review_count }})</span>
                </div>
                <span style="color:rgba(255,255,255,0.3);">·</span>
                <div style="display:flex;align-items:center;gap:4px;">
                  <svg width="12" height="12" fill="none" stroke="rgba(255,255,255,0.6)" stroke-width="2" viewBox="0 0 24 24"><path d="M21 10c0 7-9 13-9 13S3 17 3 10a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
                  <span style="font-size:0.78rem;color:rgba(255,255,255,0.75);font-weight:600;">{{ sale.seller.location }}</span>
                </div>
                <span style="color:rgba(255,255,255,0.3);">·</span>
                <span style="font-size:0.78rem;color:rgba(255,255,255,0.75);font-weight:600;">{{ sale.seller.active_since }}</span>
              </div>
            </div>
          </div>

          <!-- CTA buttons -->
          <div style="display:flex;gap:8px;padding-bottom:4px;flex-shrink:0;">
            <button style="display:flex;align-items:center;gap:6px;padding:10px 18px;border-radius:999px;background:rgba(255,255,255,0.15);border:1.5px solid rgba(255,255,255,0.3);color:#fff;font-size:0.8rem;font-weight:700;cursor:pointer;font-family:DM Sans,sans-serif;backdrop-filter:blur(8px);">
              <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><line x1="8.59" y1="13.51" x2="15.42" y2="17.49"/><line x1="15.41" y1="6.51" x2="8.59" y2="10.49"/></svg>
              Share
            </button>
            <button style="display:flex;align-items:center;gap:6px;padding:10px 20px;border-radius:999px;background:#ED730C;border:none;color:#fff;font-size:0.8rem;font-weight:800;cursor:pointer;font-family:DM Sans,sans-serif;box-shadow:0 4px 14px rgba(237,115,12,0.4);"
              onmouseover="this.style.background='#d4620a'" onmouseout="this.style.background='#ED730C'">
              <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/></svg>
              Message Seller
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- SELLER INFO STRIP -->
    <div style="background:#fff;border-bottom:1px solid #EDE8E0;">
      <div style="max-width:1280px;margin:0 auto;padding:20px 40px;display:flex;align-items:center;gap:32px;flex-wrap:wrap;">

        <!-- Stats -->
        <div style="display:flex;align-items:center;gap:28px;">
          <div style="text-align:center;">
            <p style="font-size:1.2rem;font-weight:900;color:#1A1A1A;margin:0;letter-spacing:-.02em;">{{ sale.item_count }}</p>
            <p style="font-size:0.65rem;font-weight:700;color:#9ca3af;margin:2px 0 0;text-transform:uppercase;letter-spacing:.06em;">Items</p>
          </div>
          <div style="width:1px;height:32px;background:#EDE8E0;"></div>
          <div style="text-align:center;">
            <p style="font-size:1.2rem;font-weight:900;color:#1A1A1A;margin:0;letter-spacing:-.02em;">{{ sale.seller.total_swaps }}</p>
            <p style="font-size:0.65rem;font-weight:700;color:#9ca3af;margin:2px 0 0;text-transform:uppercase;letter-spacing:.06em;">Swaps Done</p>
          </div>
          <div style="width:1px;height:32px;background:#EDE8E0;"></div>
          <div style="text-align:center;">
            <p style="font-size:1.2rem;font-weight:900;color:#1A1A1A;margin:0;letter-spacing:-.02em;">{{ sale.seller.response_rate }}%</p>
            <p style="font-size:0.65rem;font-weight:700;color:#9ca3af;margin:2px 0 0;text-transform:uppercase;letter-spacing:.06em;">Response</p>
          </div>
          <div style="width:1px;height:32px;background:#EDE8E0;"></div>
          <div style="text-align:center;">
            <p style="font-size:1.2rem;font-weight:900;color:#1A1A1A;margin:0;letter-spacing:-.02em;">{{ sale.seller.followers }}</p>
            <p style="font-size:0.65rem;font-weight:700;color:#9ca3af;margin:2px 0 0;text-transform:uppercase;letter-spacing:.06em;">Followers</p>
          </div>
        </div>

        <!-- Divider -->
        <div style="width:1px;height:40px;background:#EDE8E0;"></div>

        <!-- Bio -->
        <p style="font-size:0.84rem;color:#6b7280;line-height:1.6;margin:0;flex:1;min-width:200px;max-width:520px;">{{ sale.seller.bio }}</p>

        <!-- Member since + response -->
        <div style="margin-left:auto;text-align:right;flex-shrink:0;">
          <p style="font-size:0.72rem;font-weight:700;color:#9ca3af;margin:0 0 3px;">Member since {{ sale.seller.member_since }}</p>
          <p style="font-size:0.78rem;font-weight:700;color:#149189;margin:0;">Responds {{ sale.seller.response_time }}</p>
        </div>

      </div>
    </div>

  </div>

  <!-- ITEMS SECTION -->
  <div style="max-width:1280px;margin:0 auto;padding:36px 40px 72px;">

    <!-- Section header + filters -->
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;flex-wrap:wrap;gap:12px;">
      <div>
        <p style="font-size:0.65rem;font-weight:800;letter-spacing:.1em;text-transform:uppercase;color:#ED730C;margin:0 0 4px;">Browse the collection</p>
        <h2 style="font-size:1.35rem;font-weight:900;color:#1A1A1A;margin:0;letter-spacing:-.02em;">
          {{ filtered.length }} item{{ filtered.length !== 1 ? 's' : '' }} for sale
        </h2>
      </div>

      <!-- Controls -->
      <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
        <!-- Search -->
        <div style="display:flex;align-items:center;gap:8px;background:#fff;border:1.5px solid #EDE8E0;border-radius:999px;padding:7px 14px;">
          <svg width="13" height="13" fill="none" stroke="#9ca3af" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
          <input v-model="search" type="text" placeholder="Search items..."
            style="border:none;outline:none;font-size:0.8rem;color:#1A1A1A;background:transparent;font-family:DM Sans,sans-serif;width:130px;">
        </div>
        <!-- Sort -->
        <select v-model="sortBy"
          style="padding:8px 14px;border-radius:999px;border:1.5px solid #EDE8E0;font-size:0.78rem;font-weight:700;color:#1A1A1A;background:#fff;font-family:DM Sans,sans-serif;cursor:pointer;outline:none;">
          <option v-for="s in sortOptions" :key="s" :value="s">{{ s }}</option>
        </select>
      </div>
    </div>

    <!-- Category pills -->
    <div style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:28px;">
      <button v-for="cat in allCategories" :key="cat" @click="activeCategory = cat"
        :style="{
          padding:'7px 18px', borderRadius:'999px', fontSize:'0.8rem', fontWeight:'700',
          border:'1.5px solid', cursor:'pointer', fontFamily:'DM Sans,sans-serif', transition:'all .15s',
          borderColor: activeCategory===cat ? '#ED730C' : '#EDE8E0',
          background: activeCategory===cat ? '#ED730C' : '#fff',
          color: activeCategory===cat ? '#fff' : '#6b7280',
        }">
        {{ cat }}
      </button>
    </div>

    <!-- Empty state -->
    <div v-if="filtered.length === 0"
      style="background:#fff;border-radius:20px;border:1px solid #EDE8E0;padding:60px;text-align:center;">
      <div style="font-size:2rem;margin-bottom:12px;">🔍</div>
      <p style="font-size:0.9rem;font-weight:700;color:#1A1A1A;margin:0 0 4px;">No items found</p>
      <p style="font-size:0.8rem;color:#9ca3af;margin:0;">Try a different category or search term.</p>
    </div>

    <!-- Item grid -->
    <div v-else style="display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:20px;">
      <div v-for="item in filtered" :key="item.id"
        style="background:#fff;border-radius:18px;overflow:hidden;border:1px solid #EDE8E0;box-shadow:0 2px 12px rgba(0,0,0,0.06);display:flex;flex-direction:column;transition:box-shadow .2s,transform .2s;"
        onmouseover="this.style.boxShadow='0 8px 28px rgba(0,0,0,0.11)';this.style.transform='translateY(-2px)'"
        onmouseout="this.style.boxShadow='0 2px 12px rgba(0,0,0,0.06)';this.style.transform='translateY(0)'">

        <!-- Image -->
        <div style="position:relative;aspect-ratio:4/3;overflow:hidden;background:#f3f4f6;flex-shrink:0;">
          <img v-if="item.image" :src="item.image" :alt="item.title"
            style="width:100%;height:100%;object-fit:cover;transition:transform .4s;"
            onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'"
            @error="e => e.target.style.display='none'">
          <div v-else style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;">
            <svg width="36" height="36" fill="none" stroke="#d1d5db" stroke-width="1.5" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="M21 15l-5-5L5 21"/></svg>
          </div>

          <!-- Condition badge -->
          <span :style="{
            position:'absolute', top:'10px', left:'10px',
            background: conditionCfg[item.condition]?.bg || '#f3f4f6',
            color: conditionCfg[item.condition]?.color || '#6b7280',
            border: '1.5px solid ' + (conditionCfg[item.condition]?.border || '#e5e7eb'),
            fontSize:'0.6rem', fontWeight:'800', padding:'3px 10px',
            borderRadius:'999px', letterSpacing:'.07em', textTransform:'uppercase',
          }">{{ item.condition }}</span>

          <!-- Wishlist -->
          <button @click.stop="toggleWish(item.id)"
            :style="{
              position:'absolute', top:'8px', right:'8px',
              width:'30px', height:'30px', borderRadius:'50%',
              background: wishlisted.has(item.id) ? '#ED730C' : 'rgba(255,255,255,0.92)',
              border:'none', cursor:'pointer', display:'flex', alignItems:'center', justifyContent:'center',
              boxShadow:'0 2px 8px rgba(0,0,0,0.12)', transition:'background .15s',
            }">
            <svg width="13" height="13" :fill="wishlisted.has(item.id)?'#fff':'none'" :stroke="wishlisted.has(item.id)?'#fff':'#6b7280'" stroke-width="2" viewBox="0 0 24 24"><path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z"/></svg>
          </button>
        </div>

        <!-- Card body -->
        <div style="padding:14px 16px 16px;display:flex;flex-direction:column;flex:1;">

          <!-- Category + title -->
          <p style="font-size:0.65rem;font-weight:800;color:#9ca3af;text-transform:uppercase;letter-spacing:.07em;margin:0 0 4px;">{{ item.category }}</p>
          <h3 style="font-size:0.92rem;font-weight:800;color:#1A1A1A;margin:0 0 10px;line-height:1.3;letter-spacing:-.01em;">{{ item.title }}</h3>

          <!-- Price -->
          <div style="margin-bottom:10px;">
            <p style="font-size:0.6rem;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.06em;margin:0 0 2px;">Est. Value</p>
            <p style="font-size:1.05rem;font-weight:900;color:#1A1A1A;margin:0;letter-spacing:-.02em;">
              P{{ Number(item.value).toLocaleString() }}
            </p>
          </div>

          <!-- Swap terms -->
          <div v-if="item.swap_terms"
            style="background:#FFF4EC;border:1px solid #fed7aa;border-radius:9px;padding:7px 10px;margin-bottom:12px;display:flex;align-items:flex-start;gap:6px;">
            <svg width="11" height="11" fill="none" stroke="#ED730C" stroke-width="2" viewBox="0 0 24 24" style="flex-shrink:0;margin-top:1px;"><path d="M7 16l-4-4 4-4M17 8l4 4-4 4"/></svg>
            <p style="font-size:0.7rem;color:#92400e;font-weight:600;margin:0;line-height:1.45;">{{ item.swap_terms }}</p>
          </div>

          <!-- Wants -->
          <div v-if="item.wants && !item.swap_terms" style="margin-bottom:12px;">
            <p style="font-size:0.68rem;font-weight:700;color:#9ca3af;margin:0 0 2px;">Wants:</p>
            <p style="font-size:0.78rem;font-weight:700;color:#ED730C;margin:0;">{{ item.wants }}</p>
          </div>

          <!-- CTA -->
          <button style="margin-top:auto;width:100%;padding:10px;background:#ED730C;color:#fff;border:none;border-radius:10px;font-size:0.82rem;font-weight:800;cursor:pointer;font-family:DM Sans,sans-serif;letter-spacing:.02em;box-shadow:0 4px 12px rgba(237,115,12,0.22);transition:background .15s;"
            onmouseover="this.style.background='#d4620a'" onmouseout="this.style.background='#ED730C'">
            Swap for This
          </button>
        </div>
      </div>
    </div>

  </div>
</div>
</template>