<script setup>
import { ref, computed } from 'vue'

const el   = document.getElementById('dashboard-app')
function ds(key, fallback = '[]') {
  try { return JSON.parse(el?.dataset[key] || fallback) } catch { return JSON.parse(fallback) }
}

const user = ref(ds('user', '{}'))
const firstName = computed(() => user.value.name?.split(' ')[0] || 'Alex')
const greeting  = computed(() => {
  const h = new Date().getHours()
  if (h < 12) return 'Good morning'
  if (h < 17) return 'Good afternoon'
  return 'Good evening'
})

const stats = [
  { label:'Active Listings',  value:'8',   icon:'box',       color:'#ED730C', bg:'#FFF4EC' },
  { label:'Completed Swaps',  value:'24',  icon:'swap',      color:'#149189', bg:'#EDFAF9' },
  { label:'New Matches',      value:'12',  icon:'lightning', color:'#8b5cf6', bg:'#F5F3FF' },
  { label:'Avg. Rating',      value:'4.9', icon:'star',      color:'#f59e0b', bg:'#FFFBEB' },
]

const quickActions = [
  { label:'List an Item',      icon:'plus',   color:'#fff', bg:'#ED730C', href:'/listings/create'    },
  { label:'Start Garage Sale', icon:'store',  color:'#fff', bg:'#149189', href:'/garage-sale/create' },
  { label:'Offer a Service',   icon:'wrench', color:'#fff', bg:'#8b5cf6', href:'/services/create'    },
  { label:'Smart Match',       icon:'zap',    color:'#fff', bg:'#f59e0b', href:'/matches'            },
]

const swapRequests = ref([
  { id:'sr1', requester:'Marcus Chen',  avatar:'MC', avatarColor:'#ED730C', rating:4.8, myItem:'Vintage Camera',   theirItem:'Gaming Console',  myImage:'https://images.unsplash.com/photo-1516035069371-29a1b244cc32?w=120&q=80', theirImage:'https://images.unsplash.com/photo-1606318313647-135f3c3a7c11?w=120&q=80', time:'2h ago' },
  { id:'sr2', requester:'Elena Rossi',  avatar:'ER', avatarColor:'#149189', rating:4.9, myItem:'Mech Keyboard',    theirItem:'Sony Headphones', myImage:'https://images.unsplash.com/photo-1618384887929-16ec33fab9ef?w=120&q=80', theirImage:'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=120&q=80', time:'5h ago' },
  { id:'sr3', requester:'Liam Smith',   avatar:'LS', avatarColor:'#8b5cf6', rating:4.7, myItem:'Ceramic Vase Set', theirItem:'Leather Bag',     myImage:'https://images.unsplash.com/photo-1565193566173-7a0ee3dbe261?w=120&q=80', theirImage:'https://images.unsplash.com/photo-1548036328-c9fa89d128fa?w=120&q=80', time:'1d ago'  },
])

const myListings = ref([
  { id:'ml1', category:'Electronics', condition:'Like New', title:'iPhone 14 Pro Max 256GB', desc:'Excellent condition, unlocked, box included.',       value:900,  wants:'Gaming Laptop',     status:'active', views:124, image:'https://images.unsplash.com/photo-1591337676887-a217a6970a8a?w=600&q=80' },
  { id:'ml2', category:'Photography', condition:'Good',     title:'Sony A7 III Full Frame',  desc:'3k shutter count, kit lens and extra battery.',      value:1800, wants:'Canon R5 / Lenses', status:'active', views:89,  image:'https://images.unsplash.com/photo-1516035069371-29a1b244cc32?w=600&q=80' },
  { id:'ml3', category:'Home',        condition:'Mint',     title:'Artisan Ceramic Set',     desc:'Handmade set of 6. No chips or cracks whatsoever.', value:220,  wants:'Outdoor Planter',   status:'paused', views:42,  image:'https://images.unsplash.com/photo-1565193566173-7a0ee3dbe261?w=600&q=80' },
  { id:'ml4', category:'Fashion',     condition:'Good',     title:'Leather Camera Bag',      desc:'Fits mirrorless + 2 lenses. Genuine real leather.', value:180,  wants:'Travel Suitcase',   status:'active', views:67,  image:'https://images.unsplash.com/photo-1548036328-c9fa89d128fa?w=600&q=80' },
])

const statusCfg = {
  active:  { label:'Active',  color:'#149189', bg:'#EDFAF9' },
  paused:  { label:'Paused',  color:'#9ca3af', bg:'#f3f4f6' },
  pending: { label:'Pending', color:'#f59e0b', bg:'#FFFBEB' },
}

const smartMatches = ref([
  { id:'sm1', category:'Electronics', condition:'Like New', title:'MacBook Air M2',          desc:'Barely used. All original accessories included.',    value:1100, match:96, badge:'Top Match',  badgeColor:'#149189', owner:'Priya M.',  avatar:'PM', avatarColor:'#ec4899', location:'2.1 mi', image:'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?w=600&q=80' },
  { id:'sm2', category:'Gaming',      condition:'Good',     title:'PlayStation 5 + 3 Games', desc:'Digital edition, 14 months old, great condition.',   value:480,  match:88, badge:'High Match', badgeColor:'#8b5cf6', owner:'James K.',  avatar:'JK', avatarColor:'#6366f1', location:'4.5 mi', image:'https://images.unsplash.com/photo-1606813907291-d86efa9b94db?w=600&q=80' },
  { id:'sm3', category:'Outdoor',     condition:'Mint',     title:'Trek MTB Carbon 29"',     desc:'Carbon frame, Shimano groupset, ridden ~200km.',     value:720,  match:81, badge:'',           badgeColor:'',        owner:'Alex R.',   avatar:'AR', avatarColor:'#22c55e', location:'1.8 mi', image:'https://images.unsplash.com/photo-1485965120184-e220f721d03e?w=600&q=80' },
  { id:'sm4', category:'Fashion',     condition:'Mint',     title:'Supreme FW22 Hoodie L',   desc:'Worn once. With tags. Rare colourway. Size L.',      value:360,  match:76, badge:'',           badgeColor:'',        owner:'Elena R.',  avatar:'ER', avatarColor:'#14b8a6', location:'6.2 mi', image:'https://images.unsplash.com/photo-1556821840-3a63f15732ce?w=600&q=80' },
])

const messages = ref([
  { id:'m1', name:'Marcus Chen', avatar:'MC', color:'#ED730C', preview:'"Hey! Still down to swap the keyboard?"',   time:'2m',  unread:true  },
  { id:'m2', name:'Elena Rossi', avatar:'ER', color:'#149189', preview:'"Deal confirmed ✓ — shipping tomorrow"',    time:'1h',  unread:true  },
  { id:'m3', name:'Priya Mehta', avatar:'PM', color:'#ec4899', preview:'"Can we meet at the usual spot on Sat?"',   time:'3h',  unread:false },
  { id:'m4', name:'James Kim',   avatar:'JK', color:'#6366f1', preview:'"Thanks for the smooth swap 🙌"',           time:'1d',  unread:false },
])

const unreadCount = computed(() => messages.value.filter(m => m.unread).length)
const wishlisted  = ref(new Set())
function toggleWish(id) { const s=new Set(wishlisted.value); s.has(id)?s.delete(id):s.add(id); wishlisted.value=s }
function acceptSwap(id)  { swapRequests.value = swapRequests.value.filter(r => r.id !== id) }
function declineSwap(id) { swapRequests.value = swapRequests.value.filter(r => r.id !== id) }

const activeSection = ref('overview')
const navSections = [
  { key:'overview', label:'Overview'    },
  { key:'listings', label:'My Listings' },
  { key:'swaps',    label:'Swaps'       },
  { key:'messages', label:'Messages'    },
  { key:'matches',  label:'Matches'     },
]
</script>

<template>
<div style="min-height:100vh;background:#FAF8F5;font-family:'DM Sans',sans-serif;">

  <!-- ══ HERO / WELCOME BANNER ══════════════════════════════════════════════ -->
  <section style="position:relative;overflow:hidden;padding:52px 40px 44px;">

    <!-- BG image -->
    <div style="position:absolute;inset:0;z-index:0;">
      <img
        src="https://images.unsplash.com/photo-1529156069898-49953e39b3ac?w=1600&q=85"
        alt=""
        style="width:100%;height:100%;object-fit:cover;object-position:center 30%;"
      >
      <!-- Heavy left-side gradient, image peeks right -->
      <div style="position:absolute;inset:0;background:linear-gradient(100deg,rgba(250,245,237,0.98) 0%,rgba(250,245,237,0.93) 52%,rgba(250,245,237,0.52) 100%);"></div>
      <!-- Dot texture (matches Browse) -->
      <div style="position:absolute;inset:0;background-image:radial-gradient(circle,rgba(237,115,12,0.055) 1px,transparent 1px);background-size:28px 28px;"></div>
    </div>

    <div style="position:relative;z-index:1;max-width:1200px;margin:0 auto;">

      <!-- Top row: greeting + profile pill -->
      <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:24px;flex-wrap:wrap;margin-bottom:28px;">
        <div>
          <p style="font-size:0.72rem;font-weight:800;letter-spacing:.12em;text-transform:uppercase;color:#ED730C;margin:0 0 6px;">{{ greeting }}</p>
          <h1 style="font-size:clamp(1.9rem,3.5vw,2.75rem);font-weight:900;color:#1A1A1A;margin:0 0 8px;letter-spacing:-.03em;line-height:1.1;">
            {{ firstName }},<br>ready to swap today?
          </h1>
          <p style="font-size:0.9rem;color:#5c5751;margin:0;max-width:420px;line-height:1.6;">
            You have <strong style="color:#ED730C;">{{ swapRequests.length }} pending requests</strong> and
            <strong style="color:#149189;">{{ smartMatches.length }} new AI matches</strong>.
          </p>
        </div>

        <!-- Notif + profile -->
        <div style="display:flex;align-items:center;gap:10px;flex-shrink:0;">
          <button style="position:relative;width:40px;height:40px;border-radius:50%;background:rgba(255,255,255,0.85);backdrop-filter:blur(6px);border:1px solid rgba(237,232,224,0.8);box-shadow:0 2px 8px rgba(0,0,0,0.07);display:flex;align-items:center;justify-content:center;cursor:pointer;">
            <svg width="17" height="17" fill="none" stroke="#5c5751" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17H20L18.595 15.595A1 1 0 0118 14.806V11a6 6 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.806c0 .263-.1.52-.28.71L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
            <span v-if="unreadCount>0" style="position:absolute;top:-2px;right:-2px;width:16px;height:16px;background:#ED730C;border-radius:50%;border:2px solid #FAF8F5;font-size:0.55rem;font-weight:800;color:#fff;display:flex;align-items:center;justify-content:center;">{{ unreadCount }}</span>
          </button>
          <div style="display:flex;align-items:center;gap:9px;background:rgba(255,255,255,0.85);backdrop-filter:blur(6px);border:1px solid rgba(237,232,224,0.8);border-radius:999px;padding:5px 14px 5px 5px;box-shadow:0 2px 8px rgba(0,0,0,0.07);">
            <div style="width:30px;height:30px;border-radius:50%;background:#ED730C;color:#fff;font-size:0.7rem;font-weight:800;display:flex;align-items:center;justify-content:center;">{{ firstName.charAt(0) }}</div>
            <span style="font-size:0.82rem;font-weight:700;color:#1A1A1A;">{{ firstName }}</span>
            <svg width="10" height="10" fill="#9ca3af" viewBox="0 0 20 20"><path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"/></svg>
          </div>
        </div>
      </div>

      <!-- Stats -->
      <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:28px;">
        <div v-for="s in stats" :key="s.label"
          style="background:rgba(255,255,255,0.82);backdrop-filter:blur(8px);border:1px solid rgba(237,232,224,0.7);border-radius:16px;padding:14px 18px;display:flex;align-items:center;gap:12px;box-shadow:0 2px 10px rgba(0,0,0,0.06);">
          <div :style="{width:'38px',height:'38px',borderRadius:'10px',background:s.bg,display:'flex',alignItems:'center',justifyContent:'center',flexShrink:0}">
            <svg v-if="s.icon==='box'" width="17" height="17" fill="none" :stroke="s.color" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 10V11"/></svg>
            <svg v-if="s.icon==='swap'" width="17" height="17" fill="none" :stroke="s.color" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 16V4m0 0L3 8m4-4l4 4M17 8v12m0 0l4-4m-4 4l-4-4"/></svg>
            <svg v-if="s.icon==='lightning'" width="17" height="17" :fill="s.color" viewBox="0 0 24 24"><path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/></svg>
            <svg v-if="s.icon==='star'" width="17" height="17" :fill="s.color" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
          </div>
          <div>
            <p style="font-size:1.25rem;font-weight:900;margin:0;line-height:1;" :style="{color:s.color}">{{ s.value }}</p>
            <p style="font-size:0.72rem;font-weight:600;color:#9ca3af;margin:3px 0 0;white-space:nowrap;">{{ s.label }}</p>
          </div>
        </div>
      </div>

      <!-- Quick actions -->
      <div style="display:flex;flex-wrap:wrap;gap:10px;">
        <a v-for="a in quickActions" :key="a.label" :href="a.href" class="qa-btn"
          :style="{display:'inline-flex',alignItems:'center',gap:'8px',padding:'10px 20px',borderRadius:'999px',background:a.bg,color:a.color,fontSize:'0.82rem',fontWeight:'800',letterSpacing:'.02em',textDecoration:'none',fontFamily:'\'DM Sans\',sans-serif',boxShadow:`0 4px 14px ${a.bg}55`}">
          <svg v-if="a.icon==='plus'"   width="13" height="13" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
          <svg v-if="a.icon==='store'"  width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 9l1-5h16l1 5v1a2 2 0 01-2 2 2 2 0 01-2-2 2 2 0 01-2 2 2 2 0 01-2-2 2 2 0 01-2 2 2 2 0 01-2-2 2 2 0 01-2 2 2 2 0 01-2-2V9zm2 5v6h14v-6"/></svg>
          <svg v-if="a.icon==='wrench'" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.7 6.3a1 1 0 000 1.4l1.6 1.6a1 1 0 001.4 0l3.77-3.77a6 6 0 01-7.94 7.94l-6.91 6.91a2.12 2.12 0 01-3-3l6.91-6.91a6 6 0 017.94-7.94l-3.76 3.76z"/></svg>
          <svg v-if="a.icon==='zap'"    width="13" height="13" fill="currentColor" viewBox="0 0 24 24"><path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/></svg>
          {{ a.label }}
        </a>
      </div>
    </div>
  </section>

  <!-- ══ SUB-NAV ═══════════════════════════════════════════════════════════ -->
  <div style="background:#fff;border-bottom:1px solid #EDE8E0;padding:0 40px;box-shadow:0 1px 4px rgba(0,0,0,0.04);">
    <div style="max-width:1200px;margin:0 auto;display:flex;gap:0;">
      <button v-for="s in navSections" :key="s.key" @click="activeSection=s.key"
        :style="{padding:'13px 18px',background:'transparent',border:'none',borderBottom:activeSection===s.key?'2.5px solid #ED730C':'2.5px solid transparent',color:activeSection===s.key?'#ED730C':'#9ca3af',fontSize:'0.82rem',fontWeight:'700',cursor:'pointer',fontFamily:'\'DM Sans\',sans-serif',transition:'all .15s'}">
        {{ s.label }}
        <span v-if="s.key==='swaps'&&swapRequests.length" style="margin-left:6px;background:#ED730C;color:#fff;font-size:0.58rem;font-weight:800;padding:1px 6px;border-radius:999px;">{{ swapRequests.length }}</span>
        <span v-if="s.key==='messages'&&unreadCount" style="margin-left:6px;background:#149189;color:#fff;font-size:0.58rem;font-weight:800;padding:1px 6px;border-radius:999px;">{{ unreadCount }}</span>
      </button>
    </div>
  </div>

  <!-- ══ BODY ═══════════════════════════════════════════════════════════════ -->
  <div style="max-width:1200px;margin:0 auto;padding:44px 40px 80px;">

    <!-- ── SWAP REQUESTS ── -->
    <section style="margin-bottom:52px;">
      <div style="display:flex;align-items:flex-end;justify-content:space-between;margin-bottom:22px;">
        <div>
          <p style="font-size:0.68rem;font-weight:800;letter-spacing:.12em;color:#ED730C;text-transform:uppercase;margin:0 0 4px;">Pending</p>
          <h2 style="font-size:1.5rem;font-weight:900;color:#1A1A1A;margin:0;letter-spacing:-.02em;">
            Swap Requests
            <span style="font-size:0.75rem;font-weight:700;background:#FFF4EC;color:#ED730C;border-radius:999px;padding:3px 10px;margin-left:8px;vertical-align:middle;">{{ swapRequests.length }}</span>
          </h2>
        </div>
        <a href="/swap-requests" style="font-size:0.8rem;font-weight:700;color:#ED730C;text-decoration:none;">View all →</a>
      </div>

      <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(320px,1fr));gap:14px;">
        <div v-for="req in swapRequests" :key="req.id" class="swapy-card"
          style="background:#fff;border-radius:18px;border:1px solid #EDE8E0;box-shadow:0 4px 16px rgba(0,0,0,0.06);padding:18px 20px;">

          <!-- Items exchange -->
          <div style="display:flex;align-items:center;gap:10px;margin-bottom:14px;">
            <div style="flex:1;text-align:center;">
              <div style="width:64px;height:64px;border-radius:12px;overflow:hidden;background:#f3f4f6;margin:0 auto 5px;">
                <img :src="req.myImage" style="width:100%;height:100%;object-fit:cover;">
              </div>
              <p style="font-size:0.7rem;font-weight:700;color:#1A1A1A;margin:0;line-height:1.2;">{{ req.myItem }}</p>
              <p style="font-size:0.62rem;color:#9ca3af;margin:2px 0 0;">Your item</p>
            </div>
            <div style="flex-shrink:0;">
              <svg width="30" height="30" viewBox="0 0 40 40" fill="none">
                <path d="M28 8L38 14L28 20" stroke="#ED730C" stroke-width="3.5" stroke-linecap="round"/>
                <path d="M38 14H10" stroke="#ED730C" stroke-width="3.5" stroke-linecap="round"/>
                <path d="M12 32L2 26L12 20" stroke="#149189" stroke-width="3.5" stroke-linecap="round"/>
                <path d="M2 26H30" stroke="#149189" stroke-width="3.5" stroke-linecap="round"/>
              </svg>
            </div>
            <div style="flex:1;text-align:center;">
              <div style="width:64px;height:64px;border-radius:12px;overflow:hidden;background:#f3f4f6;margin:0 auto 5px;">
                <img :src="req.theirImage" style="width:100%;height:100%;object-fit:cover;">
              </div>
              <p style="font-size:0.7rem;font-weight:700;color:#1A1A1A;margin:0;line-height:1.2;">{{ req.theirItem }}</p>
              <p style="font-size:0.62rem;color:#9ca3af;margin:2px 0 0;">They offer</p>
            </div>
          </div>

          <div style="height:1px;background:#f3f4f6;margin-bottom:12px;"></div>

          <!-- Requester row -->
          <div style="display:flex;align-items:center;gap:8px;margin-bottom:13px;">
            <div :style="{width:'28px',height:'28px',borderRadius:'50%',background:req.avatarColor,color:'#fff',fontSize:'0.6rem',fontWeight:'800',display:'flex',alignItems:'center',justifyContent:'center',flexShrink:0}">{{ req.avatar }}</div>
            <div style="flex:1;">
              <span style="font-size:0.82rem;font-weight:800;color:#1A1A1A;">{{ req.requester }}</span>
              <span style="font-size:0.72rem;color:#6b7280;margin-left:6px;">wants your <strong style="color:#1A1A1A;">{{ req.myItem }}</strong></span>
            </div>
            <div style="display:flex;align-items:center;gap:3px;">
              <svg width="10" height="10" fill="#f59e0b" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
              <span style="font-size:0.75rem;font-weight:700;color:#1A1A1A;">{{ req.rating }}</span>
              <span style="font-size:0.67rem;color:#9ca3af;margin-left:4px;">{{ req.time }}</span>
            </div>
          </div>

          <!-- Actions -->
          <div style="display:flex;gap:8px;">
            <button @click="acceptSwap(req.id)" class="btn-accept"
              style="flex:1;padding:10px;background:#149189;color:#fff;border:none;border-radius:10px;font-size:0.8rem;font-weight:800;cursor:pointer;font-family:'DM Sans',sans-serif;">
              Accept
            </button>
            <button @click="declineSwap(req.id)" class="btn-decline"
              style="flex:1;padding:10px;background:#fff;color:#6b7280;border:1.5px solid #EDE8E0;border-radius:10px;font-size:0.8rem;font-weight:700;cursor:pointer;font-family:'DM Sans',sans-serif;">
              Decline
            </button>
            <button class="btn-msg"
              style="width:38px;height:38px;background:#f9f9f8;border:1.5px solid #EDE8E0;border-radius:10px;display:flex;align-items:center;justify-content:center;cursor:pointer;flex-shrink:0;">
              <svg width="14" height="14" fill="none" stroke="#9ca3af" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
            </button>
          </div>
        </div>
      </div>
    </section>

    <!-- ── MY ACTIVE LISTINGS ── -->
    <section style="margin-bottom:52px;">
      <div style="display:flex;align-items:flex-end;justify-content:space-between;margin-bottom:22px;">
        <div>
          <p style="font-size:0.68rem;font-weight:800;letter-spacing:.12em;color:#9ca3af;text-transform:uppercase;margin:0 0 4px;">Your Items</p>
          <h2 style="font-size:1.5rem;font-weight:900;color:#1A1A1A;margin:0;letter-spacing:-.02em;">My Active Listings</h2>
        </div>
        <div style="display:flex;gap:10px;align-items:center;">
          <a href="/listings" style="font-size:0.8rem;font-weight:700;color:#9ca3af;text-decoration:none;">View all →</a>
          <a href="/listings/create" class="btn-addnew"
            style="display:inline-flex;align-items:center;gap:7px;padding:8px 18px;background:#ED730C;color:#fff;border-radius:999px;font-size:0.8rem;font-weight:800;text-decoration:none;font-family:'DM Sans',sans-serif;box-shadow:0 4px 12px rgba(237,115,12,0.28);">
            <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Add New
          </a>
        </div>
      </div>

      <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(255px,1fr));gap:18px;">
        <div v-for="item in myListings" :key="item.id" class="swapy-card"
          style="background:#fff;border-radius:20px;overflow:hidden;border:1px solid #EDE8E0;box-shadow:0 4px 18px rgba(0,0,0,0.07);display:flex;flex-direction:column;">

          <div style="position:relative;aspect-ratio:4/3;overflow:hidden;background:#f3f4f6;flex-shrink:0;">
            <img :src="item.image" :alt="item.title" class="card-img" style="width:100%;height:100%;object-fit:cover;transition:transform .4s;">
            <span :style="{position:'absolute',top:'11px',left:'11px',background:statusCfg[item.status]?.bg,color:statusCfg[item.status]?.color,fontSize:'0.6rem',fontWeight:'800',padding:'4px 10px',borderRadius:'999px',letterSpacing:'.06em',textTransform:'uppercase',border:`1px solid ${statusCfg[item.status]?.color}44`}">
              {{ statusCfg[item.status]?.label }}
            </span>
            <span style="position:absolute;top:11px;right:11px;background:rgba(0,0,0,0.42);backdrop-filter:blur(4px);color:#fff;font-size:0.62rem;font-weight:700;padding:3px 9px;border-radius:999px;display:flex;align-items:center;gap:3px;">
              <svg width="9" height="9" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
              {{ item.views }}
            </span>
          </div>

          <div style="padding:15px 17px 17px;display:flex;flex-direction:column;flex:1;">
            <div style="display:flex;align-items:center;gap:6px;margin-bottom:7px;">
              <span style="font-size:0.62rem;font-weight:800;letter-spacing:.08em;color:#9ca3af;text-transform:uppercase;">{{ item.category }}</span>
              <span style="width:3px;height:3px;border-radius:50%;background:#d1d5db;display:inline-block;"></span>
              <span style="font-size:0.62rem;font-weight:700;color:#149189;background:#EDFAF9;padding:2px 7px;border-radius:4px;">{{ item.condition }}</span>
            </div>
            <h3 style="font-size:0.9375rem;font-weight:800;color:#1A1A1A;line-height:1.3;margin:0 0 4px;">{{ item.title }}</h3>
            <p style="font-size:0.78rem;color:#9ca3af;margin:0 0 10px;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;line-height:1.5;">{{ item.desc }}</p>
            <div style="background:#FFF4EC;border-radius:8px;padding:6px 10px;margin-bottom:14px;flex:1;">
              <p style="font-size:0.58rem;font-weight:800;letter-spacing:.07em;color:#c4a882;text-transform:uppercase;margin:0 0 1px;">Wants in exchange</p>
              <p style="font-size:0.76rem;font-weight:700;color:#ED730C;margin:0;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ item.wants }}</p>
            </div>
            <div style="display:flex;gap:8px;">
              <a :href="'/listings/'+item.id+'/edit'" class="btn-edit"
                style="flex:1;text-align:center;padding:10px;background:#fff;color:#1A1A1A;border:1.5px solid #EDE8E0;border-radius:10px;font-size:0.78rem;font-weight:800;text-decoration:none;font-family:'DM Sans',sans-serif;display:block;">
                Edit
              </a>
              <button class="btn-pause"
                style="flex:1;padding:10px;background:#FAF8F5;color:#9ca3af;border:1.5px solid #EDE8E0;border-radius:10px;font-size:0.78rem;font-weight:700;cursor:pointer;font-family:'DM Sans',sans-serif;">
                {{ item.status==='paused' ? 'Resume' : 'Pause' }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ── SMART MATCHES ── -->
    <section style="margin-bottom:52px;">
      <div style="display:flex;align-items:flex-end;justify-content:space-between;margin-bottom:22px;">
        <div>
          <p style="font-size:0.68rem;font-weight:800;letter-spacing:.12em;color:#149189;text-transform:uppercase;margin:0 0 4px;">AI Matchmaker</p>
          <h2 style="font-size:1.5rem;font-weight:900;color:#1A1A1A;margin:0;letter-spacing:-.02em;">Your Top Matches</h2>
        </div>
        <a href="/matches" style="font-size:0.8rem;font-weight:700;color:#ED730C;text-decoration:none;">See all →</a>
      </div>

      <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(255px,1fr));gap:18px;">
        <div v-for="item in smartMatches" :key="item.id" class="swapy-card"
          style="background:#fff;border-radius:20px;overflow:hidden;border:1px solid #EDE8E0;box-shadow:0 4px 18px rgba(0,0,0,0.07);display:flex;flex-direction:column;">

          <div style="position:relative;aspect-ratio:4/3;overflow:hidden;background:#f3f4f6;flex-shrink:0;">
            <img :src="item.image" :alt="item.title" class="card-img" style="width:100%;height:100%;object-fit:cover;transition:transform .4s;">
            <span v-if="item.badge" :style="{position:'absolute',top:'11px',left:'11px',background:item.badgeColor,color:'#fff',fontSize:'0.6rem',fontWeight:'800',padding:'4px 10px',borderRadius:'999px',letterSpacing:'.06em',textTransform:'uppercase'}">{{ item.badge }}</span>
            <div style="position:absolute;top:11px;right:11px;background:rgba(20,145,137,0.88);backdrop-filter:blur(4px);color:#fff;font-size:0.68rem;font-weight:900;padding:4px 10px;border-radius:999px;">{{ item.match }}% match</div>
            <button @click.stop="toggleWish(item.id)" class="wish-btn"
              style="position:absolute;bottom:10px;right:10px;width:30px;height:30px;background:rgba(255,255,255,0.88);border:none;border-radius:50%;display:flex;align-items:center;justify-content:center;cursor:pointer;box-shadow:0 2px 8px rgba(0,0,0,0.10);">
              <svg :style="{width:'13px',height:'13px',fill:wishlisted.has(item.id)?'#ED730C':'none',stroke:wishlisted.has(item.id)?'#ED730C':'#6b7280',strokeWidth:'2'}" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
            </button>
          </div>

          <div style="padding:15px 17px 17px;display:flex;flex-direction:column;flex:1;">
            <div style="display:flex;align-items:center;gap:6px;margin-bottom:7px;">
              <span style="font-size:0.62rem;font-weight:800;letter-spacing:.08em;color:#9ca3af;text-transform:uppercase;">{{ item.category }}</span>
              <span style="width:3px;height:3px;border-radius:50%;background:#d1d5db;display:inline-block;"></span>
              <span style="font-size:0.62rem;font-weight:700;color:#149189;background:#EDFAF9;padding:2px 7px;border-radius:4px;">{{ item.condition }}</span>
            </div>
            <h3 style="font-size:0.9375rem;font-weight:800;color:#1A1A1A;line-height:1.3;margin:0 0 4px;">{{ item.title }}</h3>
            <p style="font-size:0.78rem;color:#9ca3af;margin:0 0 12px;flex:1;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;line-height:1.5;">{{ item.desc }}</p>
            <div style="display:flex;align-items:center;gap:7px;margin-bottom:13px;padding-top:10px;border-top:1px solid #f3f4f6;">
              <div style="position:relative;width:34px;height:24px;flex-shrink:0;">
                <div :style="{width:'24px',height:'24px',borderRadius:'50%',background:item.avatarColor,color:'#fff',fontSize:'0.58rem',fontWeight:'800',display:'flex',alignItems:'center',justifyContent:'center',position:'absolute',left:'0',zIndex:2,border:'1.5px solid #fff'}">{{ item.avatar }}</div>
                <div style="width:22px;height:22px;border-radius:50%;background:#149189;display:flex;align-items:center;justify-content:center;position:absolute;left:12px;z-index:1;border:1.5px solid #fff;">
                  <svg width="9" height="9" viewBox="0 0 40 40" fill="none"><path d="M28 8L38 14L28 20" stroke="white" stroke-width="4" stroke-linecap="round"/><path d="M38 14H10" stroke="white" stroke-width="4" stroke-linecap="round"/><path d="M12 32L2 26L12 20" stroke="white" stroke-width="4" stroke-linecap="round"/><path d="M2 26H30" stroke="white" stroke-width="4" stroke-linecap="round"/></svg>
                </div>
              </div>
              <span style="font-size:0.78rem;font-weight:600;color:#149189;flex:1;">{{ item.owner }}</span>
              <span style="font-size:0.72rem;color:#9ca3af;display:flex;align-items:center;gap:3px;">
                <svg width="9" height="9" fill="#9ca3af" viewBox="0 0 24 24"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z"/></svg>
                {{ item.location }}
              </span>
            </div>
            <a :href="'/item/'+item.id" class="swap-btn"
              style="display:block;text-align:center;background:#ED730C;color:#fff;font-size:0.82rem;font-weight:800;padding:11px;border-radius:999px;text-decoration:none;letter-spacing:.03em;font-family:'DM Sans',sans-serif;box-shadow:0 4px 12px rgba(237,115,12,0.28);">
              Contact Swapper
            </a>
          </div>
        </div>
      </div>
    </section>

    <!-- ── MESSAGES ── -->
    <section>
      <div style="display:flex;align-items:flex-end;justify-content:space-between;margin-bottom:22px;">
        <div>
          <p style="font-size:0.68rem;font-weight:800;letter-spacing:.12em;color:#9ca3af;text-transform:uppercase;margin:0 0 4px;">Inbox</p>
          <h2 style="font-size:1.5rem;font-weight:900;color:#1A1A1A;margin:0;letter-spacing:-.02em;">Recent Messages</h2>
        </div>
        <a href="/messages" style="font-size:0.8rem;font-weight:700;color:#ED730C;text-decoration:none;">Open all →</a>
      </div>

      <div style="background:#fff;border-radius:20px;border:1px solid #EDE8E0;box-shadow:0 4px 18px rgba(0,0,0,0.06);overflow:hidden;">
        <div v-for="(msg,i) in messages" :key="msg.id" class="msg-row"
          :style="{display:'flex',alignItems:'center',gap:'13px',padding:'15px 20px',cursor:'pointer',borderBottom:i<messages.length-1?'1px solid #f3f4f6':'none'}">
          <div :style="{width:'40px',height:'40px',borderRadius:'50%',background:msg.color,color:'#fff',fontSize:'0.75rem',fontWeight:'800',display:'flex',alignItems:'center',justifyContent:'center',flexShrink:0}">{{ msg.avatar }}</div>
          <div style="flex:1;min-width:0;">
            <div style="display:flex;align-items:center;gap:6px;margin-bottom:2px;">
              <span style="font-size:0.875rem;font-weight:800;color:#1A1A1A;">{{ msg.name }}</span>
              <span v-if="msg.unread" style="width:7px;height:7px;border-radius:50%;background:#ED730C;flex-shrink:0;"></span>
            </div>
            <p style="font-size:0.8rem;color:#6b7280;margin:0;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ msg.preview }}</p>
          </div>
          <div style="display:flex;flex-direction:column;align-items:flex-end;gap:4px;flex-shrink:0;">
            <span :style="{fontSize:'0.68rem',fontWeight:'700',color:msg.unread?'#ED730C':'#9ca3af'}">{{ msg.time }}</span>
            <svg width="12" height="12" fill="none" stroke="#d1d5db" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 18l6-6-6-6"/></svg>
          </div>
        </div>
      </div>
    </section>

  </div>
</div>
</template>

<style scoped>
.swapy-card { transition: border-color .2s, box-shadow .2s, transform .2s; }
.swapy-card:hover { border-color:#ED730C !important; box-shadow:0 10px 36px rgba(237,115,12,0.12) !important; transform:translateY(-3px); }
.swapy-card:hover .card-img { transform:scale(1.06); }
.qa-btn { transition:all .18s; }
.qa-btn:hover { transform:translateY(-2px); filter:brightness(1.08); }
.wish-btn { transition:transform .15s; }
.wish-btn:hover { transform:scale(1.2); }
.swap-btn:hover { background:#d4620a !important; box-shadow:0 6px 18px rgba(237,115,12,0.40) !important; transform:translateY(-1px); }
.btn-accept { transition:background .15s; }
.btn-accept:hover { background:#0f7068 !important; }
.btn-decline:hover { border-color:#dc2626 !important; color:#dc2626 !important; }
.btn-msg:hover { border-color:#ED730C !important; }
.btn-addnew { transition:all .15s; }
.btn-addnew:hover { background:#d4620a !important; }
.btn-edit:hover { border-color:#1A1A1A !important; }
.btn-pause:hover { color:#ED730C !important; border-color:#ED730C !important; }
.msg-row { transition:background .15s; }
.msg-row:hover { background:#FAF8F5 !important; }
</style>