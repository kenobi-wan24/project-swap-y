<script setup>
// filepath: resources/js/components/global/DashboardPage.vue
import { ref, computed } from 'vue'
import PostItemModal from '../dashboard/PostItemModal.vue'

const el          = document.getElementById('dashboard-app')
const user        = ref(JSON.parse(el?.dataset.user        || '{}'))
const stats       = ref(JSON.parse(el?.dataset.stats       || '[]'))
const matches     = ref(JSON.parse(el?.dataset.matches     || '[]'))
const swaps       = ref(JSON.parse(el?.dataset.swaps       || '[]'))
const messages    = ref(JSON.parse(el?.dataset.messages    || '[]'))
const credibility = ref(JSON.parse(el?.dataset.credibility || '{}'))
const showPostModal = ref(false)

// ── Fake data ─────────────────────────────────────────────────────────────────
const fakeUser = { name: 'Alex Rivera', username: 'alex_r', email: 'alex@example.com', avatar: null }

const fakeStats = [
    { key: 'listings',     label: 'Active Listings',      value: '4',   accent: '#ED730C', bg: '#fff7ed', border: '#fed7aa' },
    { key: 'swaps',        label: 'Completed Swaps',       value: '12',  accent: '#14b8a6', bg: '#f0fdf4', border: '#a7f3d0' },
    { key: 'negotiations', label: 'Ongoing Negotiations',  value: '3',   accent: '#8b5cf6', bg: '#f5f3ff', border: '#ddd6fe' },
    { key: 'views',        label: 'Item Views Today',      value: '127', accent: '#f59e0b', bg: '#fffbeb', border: '#fde68a' },
]

const fakeMatches = [
    { id: 1, title: 'Minimalist Chrono Watch', wants: 'Vintage Camera', match_percent: 94, distance: '1.2', image: 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=400&q=80', user: 'Jordan K.', initials: 'JK', user_color: '#14b8a6' },
    { id: 2, title: 'Sony WH-1000XM4',         wants: 'Gaming Chair',   match_percent: 87, distance: '2.5', image: 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=400&q=80', user: 'Sarah L.',  initials: 'SL', user_color: '#ED730C' },
    { id: 3, title: 'Canon EOS R6',             wants: 'MacBook Pro',    match_percent: 91, distance: '0.8', image: 'https://images.unsplash.com/photo-1516035069371-29a1b244cc32?w=400&q=80', user: 'Mike C.',   initials: 'MC', user_color: '#8b5cf6' },
    { id: 4, title: 'Trek Mountain Bike',        wants: 'Drone',          match_percent: 78, distance: '3.1', image: 'https://images.unsplash.com/photo-1485965120184-e220f721d03e?w=400&q=80', user: 'Tiana R.',  initials: 'TR', user_color: '#f59e0b' },
]

const fakeSwapFeed = [
    { id: 1, title: 'Air Jordan 1 Retro', category: 'Streetwear', distance: '0.8km', swapper: 'Marcus T.', initials: 'MT', color: '#14b8a6', image: 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=500&q=80' },
    { id: 2, title: 'Fujica ST801 Film',  category: 'Tech',        distance: '2.4km', swapper: 'Elena S.',  initials: 'ES', color: '#ED730C', image: 'https://images.unsplash.com/photo-1516035069371-29a1b244cc32?w=500&q=80' },
    { id: 3, title: 'Minimalist Watch',   category: 'Accessories', distance: '1.1km', swapper: 'Jordan K.', initials: 'JK', color: '#8b5cf6', image: 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=500&q=80' },
    { id: 4, title: 'Sony WH-1000XM4',   category: 'Electronics', distance: '3.2km', swapper: 'Sarah L.',  initials: 'SL', color: '#f59e0b', image: 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=500&q=80' },
]

const fakeSwaps = [
    { id: 1, title: 'Your Watch → Canon Lens', status: 'pending',    status_text: 'Awaiting your response · 2h ago', cta: 'Respond Now', primary: true,  img_a: 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=80', img_b: 'https://images.unsplash.com/photo-1516035069371-29a1b244cc32?w=80' },
    { id: 2, title: 'MacBook → Sony Camera',   status: 'in_transit', status_text: 'Items shipped · Est. delivery Fri',  cta: 'Track',       primary: false, img_a: 'https://images.unsplash.com/photo-1603302576837-37561b2e2302?w=80', img_b: 'https://images.unsplash.com/photo-1516035069371-29a1b244cc32?w=80' },
    { id: 3, title: 'Headphones → Keyboard',   status: 'completed',  status_text: 'Completed 3 days ago',              cta: 'Review',      primary: false, img_a: 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=80', img_b: 'https://images.unsplash.com/photo-1561154464-82e9adf32764?w=80' },
]

const fakeTrending = [
    { id: 1, label: 'STREETWEAR',    count: '1.2k swaps', image: 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=700&q=80' },
    { id: 2, label: 'TECH',          count: '840 swaps',  image: 'https://images.unsplash.com/photo-1603302576837-37561b2e2302?w=400&q=80' },
    { id: 3, label: 'VINYL RECORDS', count: '320 swaps',  image: 'https://images.unsplash.com/photo-1603048588665-791ca8aea617?w=400&q=80' },
    { id: 4, label: 'HOME DECOR',    count: '510 swaps',  image: 'https://images.unsplash.com/photo-1555041469-a586c61ea9bc?w=500&q=80' },
]

const fakeTopSwappers = [
    { id: 1, rank: 1, name: 'Jordan K.', swaps: 42, image: 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=100&q=80' },
    { id: 2, rank: 2, name: 'Sarah L.',  swaps: 38, image: 'https://images.unsplash.com/photo-1494790108755-2616b612b786?w=100&q=80' },
    { id: 3, rank: 3, name: 'Mike Chen', swaps: 35, image: 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=100&q=80' },
    { id: 4, rank: 4, name: 'Tiana R.',  swaps: 31, image: 'https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=100&q=80' },
    { id: 5, rank: 5, name: 'Chris V.',  swaps: 29, image: 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=100&q=80' },
]

const fakeMessages = [
    { id: 1, name: 'Marcus K.',   initials: 'MK', preview: 'Hey! Still interested in the lens swap?',      time: '2m',  unread: true,  color: '#14b8a6' },
    { id: 2, name: 'Sarah L.',    initials: 'SL', preview: 'Deal confirmed ✓ Shipping tomorrow',            time: '1h',  unread: true,  color: '#ED730C' },
    { id: 3, name: 'Julian D.',   initials: 'JD', preview: 'Thanks for the smooth swap!',                  time: '3h',  unread: false, color: '#8b5cf6' },
    { id: 4, name: 'Anna W.',     initials: 'AW', preview: 'Can we do a partial value difference?',         time: '1d',  unread: false, color: '#f59e0b' },
]

const fakeCredibility = { score: 4.8, total_reviews: 28, verified: true, member_since: 'Jan 2024' }

// Resolve
const displayUser     = computed(() => Object.keys(user.value).length     ? user.value     : fakeUser)
const displayStats    = computed(() => stats.value.length                 ? stats.value    : fakeStats)
const displayMatches  = computed(() => matches.value.length               ? matches.value  : fakeMatches)
const displayFeed     = computed(() => (matches.value.length ? matches.value : fakeSwapFeed).slice(0, 4))
const displayMessages = computed(() => messages.value.length              ? messages.value : fakeMessages)

const firstName    = computed(() => displayUser.value.name?.split(' ')[0] || 'Alex')
const unreadCount  = computed(() => displayMessages.value.filter(m => m.unread).length)
const pendingCount = computed(() => fakeSwaps.filter(s => s.status === 'pending').length)
const strongMatchCount = computed(() => displayMatches.value.filter(m => m.match_percent >= 85).length)

const greeting = computed(() => {
    const h = new Date().getHours()
    if (h < 12) return 'Good morning'
    if (h < 18) return 'Good afternoon'
    return 'Good evening'
})

const statusConfig = {
    pending:    { label: 'Pending',    bg: '#fff7ed', color: '#EA580C', border: '#fed7aa' },
    in_transit: { label: 'In Transit', bg: '#e6f7f6', color: '#0d9488', border: '#99f6e4' },
    completed:  { label: 'Completed',  bg: '#eff6ff', color: '#2563eb', border: '#bfdbfe' },
}

function matchBadgeBg(pct) {
    if (pct >= 90) return { bg: '#14b8a6', shadow: 'rgba(20,184,166,0.35)' }
    if (pct >= 80) return { bg: '#ED730C', shadow: 'rgba(237,115,12,0.35)' }
    return { bg: '#6b7280', shadow: 'rgba(107,114,128,0.2)' }
}
</script>

<template>
<div style="min-height:100vh;background:#f3f4f6;font-family:'DM Sans',sans-serif;overflow-x:hidden;width:100%;">
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" style="padding-top:28px;padding-bottom:72px;">

<!-- ══════════════════════════════════════════════════════════════════
     HERO — strong gradient, editorial, high visual priority
══════════════════════════════════════════════════════════════════ -->
<div style="position:relative;border-radius:24px;overflow:hidden;margin-bottom:20px;min-height:220px;display:flex;flex-direction:column;justify-content:center;">

    <!-- Layered gradient background — matches landing page language -->
    <div style="position:absolute;inset:0;background:linear-gradient(135deg,#1c0d06 0%,#2e1309 35%,#0a2320 70%,#0d1f1d 100%);"></div>
    <!-- Orange blob top-left -->
    <div style="position:absolute;top:-60px;left:-40px;width:360px;height:360px;background:radial-gradient(circle,rgba(237,115,12,0.35) 0%,transparent 65%);pointer-events:none;"></div>
    <!-- Teal blob bottom-right -->
    <div style="position:absolute;bottom:-80px;right:80px;width:320px;height:320px;background:radial-gradient(circle,rgba(20,184,166,0.25) 0%,transparent 65%);pointer-events:none;"></div>
    <!-- Subtle centre shine -->
    <div style="position:absolute;top:10%;left:30%;width:500px;height:200px;background:radial-gradient(ellipse,rgba(255,255,255,0.04) 0%,transparent 70%);pointer-events:none;"></div>
    <!-- Decorative ✦ watermark -->
    <div style="position:absolute;top:-20px;right:48px;font-size:11rem;opacity:0.03;color:#fff;font-weight:900;pointer-events:none;user-select:none;line-height:1;">✦</div>
    <div style="position:absolute;bottom:-28px;right:260px;font-size:7rem;opacity:0.025;color:#ED730C;pointer-events:none;user-select:none;line-height:1;">✦</div>

    <!-- Content -->
    <div style="position:relative;z-index:1;padding:clamp(20px, 4vw, 36px) clamp(16px, 4vw, 40px);">
        <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:24px;">

            <!-- Left: greeting + alerts -->
            <div>
                <p style="font-size:0.65rem;font-weight:800;letter-spacing:.15em;text-transform:uppercase;color:rgba(237,115,12,0.85);margin:0 0 10px;">Community Dashboard</p>
                <h1 style="font-size:2.375rem;font-weight:900;color:#fff;margin:0 0 20px;line-height:1.08;letter-spacing:-.01em;">
                    {{ greeting }}, <span style="color:#ED730C;">{{ firstName }}!</span>
                </h1>

                <!-- STATUS INDICATORS — clean icon-based blocks -->
                <div style="display:flex;flex-wrap:wrap;gap:8px;">

                    <!-- Strong matches -->
                    <div v-if="strongMatchCount > 0" class="status-chip" style="display:inline-flex;align-items:center;gap:8px;background:rgba(20,184,166,0.12);border:1px solid rgba(20,184,166,0.25);border-radius:10px;padding:8px 14px;transition:all .2s;cursor:pointer;"
                         onmouseover="this.style.background='rgba(20,184,166,0.2)'"
                         onmouseout="this.style.background='rgba(20,184,166,0.12)'">
                        <div style="width:28px;height:28px;border-radius:7px;background:rgba(20,184,166,0.2);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <svg width="14" height="14" fill="none" stroke="#14b8a6" stroke-width="2.5" stroke-linecap="round" viewBox="0 0 24 24"><path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/></svg>
                        </div>
                        <div>
                            <p style="font-size:0.8rem;font-weight:800;color:#fff;margin:0;line-height:1.2;">{{ strongMatchCount }} strong matches</p>
                            <p style="font-size:0.65rem;color:rgba(255,255,255,0.45);margin:0;">waiting for you</p>
                        </div>
                    </div>

                    <!-- Pending swap requests -->
                    <div v-if="pendingCount > 0" class="status-chip" style="display:inline-flex;align-items:center;gap:8px;background:rgba(237,115,12,0.12);border:1px solid rgba(237,115,12,0.25);border-radius:10px;padding:8px 14px;transition:all .2s;cursor:pointer;"
                         onmouseover="this.style.background='rgba(237,115,12,0.2)'"
                         onmouseout="this.style.background='rgba(237,115,12,0.12)'">
                        <div style="width:28px;height:28px;border-radius:7px;background:rgba(237,115,12,0.2);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <svg width="14" height="14" fill="none" stroke="#ED730C" stroke-width="2.5" stroke-linecap="round" viewBox="0 0 24 24"><path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 01-3.46 0"/></svg>
                        </div>
                        <div>
                            <p style="font-size:0.8rem;font-weight:800;color:#fff;margin:0;line-height:1.2;">{{ pendingCount }} swap {{ pendingCount === 1 ? 'request' : 'requests' }}</p>
                            <p style="font-size:0.65rem;color:rgba(255,255,255,0.45);margin:0;">needs your response</p>
                        </div>
                    </div>

                    <!-- Unread messages -->
                    <div v-if="unreadCount > 0" class="status-chip" style="display:inline-flex;align-items:center;gap:8px;background:rgba(139,92,246,0.12);border:1px solid rgba(139,92,246,0.25);border-radius:10px;padding:8px 14px;transition:all .2s;cursor:pointer;"
                         onmouseover="this.style.background='rgba(139,92,246,0.2)'"
                         onmouseout="this.style.background='rgba(139,92,246,0.12)'">
                        <div style="width:28px;height:28px;border-radius:7px;background:rgba(139,92,246,0.2);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <svg width="14" height="14" fill="none" stroke="#8b5cf6" stroke-width="2.5" stroke-linecap="round" viewBox="0 0 24 24"><path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/></svg>
                        </div>
                        <div>
                            <p style="font-size:0.8rem;font-weight:800;color:#fff;margin:0;line-height:1.2;">{{ unreadCount }} unread messages</p>
                            <p style="font-size:0.65rem;color:rgba(255,255,255,0.45);margin:0;">open messenger</p>
                        </div>
                    </div>

                    <!-- Views -->
                    <div class="status-chip" style="display:inline-flex;align-items:center;gap:8px;background:rgba(245,158,11,0.12);border:1px solid rgba(245,158,11,0.25);border-radius:10px;padding:8px 14px;transition:all .2s;cursor:pointer;"
                         onmouseover="this.style.background='rgba(245,158,11,0.2)'"
                         onmouseout="this.style.background='rgba(245,158,11,0.12)'">
                        <div style="width:28px;height:28px;border-radius:7px;background:rgba(245,158,11,0.2);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <svg width="14" height="14" fill="none" stroke="#f59e0b" stroke-width="2.5" stroke-linecap="round" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        </div>
                        <div>
                            <p style="font-size:0.8rem;font-weight:800;color:#fff;margin:0;line-height:1.2;">127 item views today</p>
                            <p style="font-size:0.65rem;color:rgba(255,255,255,0.45);margin:0;">your listing is trending</p>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Right: CTAs -->
            <div style="display:flex;flex-direction:column;gap:10px;flex-shrink:0;">
                <div
    @click="showPostModal = true"
    style="display:inline-flex;align-items:center;justify-content:center;gap:8px;background:#ED730C;color:#fff;font-size:0.9rem;font-weight:800;padding:14px 26px;border-radius:13px;cursor:pointer;text-decoration:none;transition:all .2s;box-shadow:0 6px 24px rgba(237,115,12,0.45);white-space:nowrap;letter-spacing:.01em;"
    onmouseover="this.style.background='#d4620a';this.style.transform='translateY(-2px)';this.style.boxShadow='0 10px 32px rgba(237,115,12,0.5)'"
    onmouseout="this.style.background='#ED730C';this.style.transform='translateY(0)';this.style.boxShadow='0 6px 24px rgba(237,115,12,0.45)'"
>
    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" viewBox="0 0 24 24">
        <line x1="12" y1="5" x2="12" y2="19"/>
        <line x1="5" y1="12" x2="19" y2="12"/>
    </svg>
    List an Item
</div>
                <a href="/browse"
                   style="display:inline-flex;align-items:center;justify-content:center;gap:8px;background:rgba(255,255,255,0.1);color:#fff;font-size:0.9rem;font-weight:700;padding:14px 26px;border-radius:13px;text-decoration:none;transition:all .2s;border:1.5px solid rgba(255,255,255,0.2);white-space:nowrap;backdrop-filter:blur(8px);"
                   onmouseover="this.style.background='rgba(255,255,255,0.18)';this.style.transform='translateY(-2px)'"
                   onmouseout="this.style.background='rgba(255,255,255,0.1)';this.style.transform='translateY(0)'">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
                    Browse Items
                </a>
            </div>

        </div>
    </div>
</div>

<!-- ══ STATS CARDS — accent borders, consistent icons, depth ══ -->
<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:20px;" class="stats-grid">
    <div v-for="stat in displayStats" :key="stat.key" class="stat-card"
         :style="{background:'#fff',borderRadius:'16px',padding:'20px 22px',borderLeft:`4px solid ${stat.accent}`,boxShadow:'0 1px 4px rgba(0,0,0,0.05)',display:'flex',alignItems:'center',gap:'16px',transition:'all .2s',cursor:'default'}">
        <!-- Icon box -->
        <div :style="{width:'48px',height:'48px',borderRadius:'13px',background:stat.bg,border:`1px solid ${stat.border}`,display:'flex',alignItems:'center',justifyContent:'center',flexShrink:0}">
            <!-- Listings icon -->
            <svg v-if="stat.key==='listings'" width="20" height="20" fill="none" :stroke="stat.accent" stroke-width="2" stroke-linecap="round" viewBox="0 0 24 24"><path d="M21 16V8a2 2 0 00-1-1.73l-7-4a2 2 0 00-2 0l-7 4A2 2 0 003 8v8a2 2 0 001 1.73l7 4a2 2 0 002 0l7-4A2 2 0 0021 16z"/></svg>
            <!-- Swaps icon -->
            <svg v-if="stat.key==='swaps'" width="20" height="20" fill="none" :stroke="stat.accent" stroke-width="2" stroke-linecap="round" viewBox="0 0 24 24"><path d="M8 7h12m0 0l-4-4m4 4l-4 4M16 17H4m0 0l4 4m-4-4l4-4"/></svg>
            <!-- Negotiations icon -->
            <svg v-if="stat.key==='negotiations'" width="20" height="20" fill="none" :stroke="stat.accent" stroke-width="2" stroke-linecap="round" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/></svg>
            <!-- Views icon -->
            <svg v-if="stat.key==='views'" width="20" height="20" fill="none" :stroke="stat.accent" stroke-width="2" stroke-linecap="round" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
        </div>
        <div>
            <p :style="{fontSize:'1.75rem',fontWeight:'900',color:'#1a1a1a',margin:'0',lineHeight:'1',letterSpacing:'-.01em'}">{{ stat.value }}</p>
            <p style="font-size:0.72rem;color:#9ca3af;margin:4px 0 0;font-weight:500;line-height:1.3;">{{ stat.label }}</p>
        </div>
    </div>
</div>

<!-- ══ MAIN TWO-COLUMN GRID ══ -->
<div style="display:grid;grid-template-columns:1fr minmax(0,320px);gap:20px;align-items:start;" class="main-grid">

    <!-- ══ LEFT COLUMN ══ -->
    <div style="display:flex;flex-direction:column;gap:20px;">

        <!-- ── TOP MATCHES ── -->
        <div style="background:#fff;border-radius:20px;padding:24px;box-shadow:0 1px 6px rgba(0,0,0,0.05);border:1px solid #f0f0ec;">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:6px;">
                <div style="display:flex;align-items:center;gap:10px;">
                    <h2 style="font-size:1.0625rem;font-weight:800;color:#1a1a1a;margin:0;">Top Matches for You</h2>
                    <span style="background:linear-gradient(135deg,#e6f7f6,#d1fae5);color:#14b8a6;font-size:0.6rem;font-weight:800;padding:3px 10px;border-radius:999px;letter-spacing:.07em;text-transform:uppercase;border:1px solid #a7f3d0;">AI Powered</span>
                </div>
                <a href="/browse" style="font-size:0.78rem;font-weight:700;color:#14b8a6;text-decoration:none;display:inline-flex;align-items:center;gap:3px;transition:color .15s;"
                   onmouseover="this.style.color='#0d9488'" onmouseout="this.style.color='#14b8a6'">
                    View All <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>
            <p style="font-size:0.78rem;color:#9ca3af;margin:0 0 18px;">Items our algorithm thinks you'll love based on your swap preferences</p>

            <div style="display:flex;gap:14px;overflow-x:auto;padding-bottom:6px;" class="hide-scrollbar">
                <div v-for="match in displayMatches" :key="match.id" class="match-card"
                     style="flex-shrink:0;width:210px;border:1.5px solid #f0f0ec;border-radius:16px;overflow:hidden;cursor:pointer;background:#fff;transition:all .22s;">

                    <div style="position:relative;aspect-ratio:4/3;overflow:hidden;background:#f5f5f0;">
                        <img :src="match.image" :alt="match.title" class="match-img" style="width:100%;height:100%;object-fit:cover;transition:transform .35s;">
                        <!-- Match % badge -->
                        <div :style="{
                            position:'absolute',top:'9px',right:'9px',
                            background:matchBadgeBg(match.match_percent).bg,
                            boxShadow:`0 3px 10px ${matchBadgeBg(match.match_percent).shadow}`,
                            color:'#fff',fontSize:'0.62rem',fontWeight:'900',
                            padding:'4px 9px',borderRadius:'8px',letterSpacing:'.04em'
                        }">{{ match.match_percent }}% MATCH</div>
                        <!-- Distance -->
                        <div style="position:absolute;bottom:8px;left:8px;background:rgba(0,0,0,0.6);color:#fff;font-size:0.62rem;font-weight:600;padding:3px 7px;border-radius:6px;backdrop-filter:blur(6px);display:inline-flex;align-items:center;gap:3px;">
                            <svg width="9" height="9" fill="#fff" viewBox="0 0 24 24"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z"/></svg>
                            {{ match.distance }} mi
                        </div>
                    </div>

                    <div style="padding:12px 14px 14px;">
                        <p style="font-size:0.875rem;font-weight:700;color:#1a1a1a;margin:0 0 3px;line-height:1.3;">{{ match.title }}</p>
                        <p style="font-size:0.72rem;color:#9ca3af;margin:0 0 10px;">Wants: <strong style="color:#6b7280;">{{ match.wants }}</strong></p>
                        <div style="display:flex;align-items:center;justify-content:space-between;">
                            <div style="display:flex;align-items:center;gap:5px;">
                                <div :style="{width:'20px',height:'20px',borderRadius:'50%',background:match.user_color,display:'flex',alignItems:'center',justifyContent:'center',fontSize:'0.5rem',fontWeight:'800',color:'#fff'}">{{ match.initials }}</div>
                                <span style="font-size:0.72rem;color:#9ca3af;">{{ match.user }}</span>
                            </div>
                            <button class="swap-btn-sm" style="background:#14b8a6;color:#fff;font-size:0.7rem;font-weight:700;border:none;padding:6px 12px;border-radius:8px;cursor:pointer;font-family:'DM Sans',sans-serif;transition:all .15s;box-shadow:0 2px 8px rgba(20,184,166,0.3);">
                                Swap
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ── YOUR SWAP FEED ── -->
        <div style="background:#fff;border-radius:20px;padding:24px;box-shadow:0 1px 6px rgba(0,0,0,0.05);border:1px solid #f0f0ec;">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:18px;">
                <h2 style="font-size:1.0625rem;font-weight:800;color:#1a1a1a;margin:0;">Your Swap Feed</h2>
                <a href="/browse" style="font-size:0.78rem;font-weight:700;color:#14b8a6;text-decoration:none;display:inline-flex;align-items:center;gap:3px;transition:color .15s;"
                   onmouseover="this.style.color='#0d9488'" onmouseout="this.style.color='#14b8a6'">
                    View All <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;" class="feed-grid">
                <div v-for="item in displayFeed" :key="item.id" class="feed-card"
                     style="border:1.5px solid #f0f0ec;border-radius:16px;overflow:hidden;cursor:pointer;transition:all .22s;background:#fff;">
                    <div style="position:relative;aspect-ratio:4/3;overflow:hidden;background:#f5f5f0;">
                        <img :src="item.image" :alt="item.title" class="feed-img" style="width:100%;height:100%;object-fit:cover;transition:transform .35s;">
                        <span style="position:absolute;top:10px;left:10px;background:rgba(255,255,255,0.92);color:#1a1a1a;font-size:0.62rem;font-weight:700;padding:4px 10px;border-radius:999px;backdrop-filter:blur(6px);letter-spacing:.03em;">{{ item.category }}</span>
                    </div>
                    <div style="padding:13px 14px 15px;">
                        <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:8px;">
                            <h3 style="font-size:0.9rem;font-weight:700;color:#1a1a1a;margin:0;line-height:1.3;flex:1;">{{ item.title }}</h3>
                            <span style="font-size:0.7rem;font-weight:700;color:#14b8a6;white-space:nowrap;margin-left:6px;margin-top:1px;">{{ item.distance }}</span>
                        </div>
                        <div style="display:flex;align-items:center;gap:5px;margin-bottom:11px;">
                            <div :style="{width:'20px',height:'20px',borderRadius:'50%',background:item.color||'#ED730C',display:'flex',alignItems:'center',justifyContent:'center',fontSize:'0.5rem',fontWeight:'700',color:'#fff',flexShrink:0}">{{ item.initials }}</div>
                            <span style="font-size:0.73rem;color:#9ca3af;">Swapping by <strong style="color:#6b7280;">{{ item.swapper }}</strong></span>
                        </div>
                        <button class="offer-btn" style="width:100%;padding:9px;background:transparent;color:#ED730C;font-size:0.78rem;font-weight:700;border:1.5px solid #ED730C;border-radius:10px;cursor:pointer;font-family:'DM Sans',sans-serif;transition:all .15s;">
                            Make an Offer
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- ── ACTIVE SWAPS ── -->
        <div style="background:#fff;border-radius:20px;padding:24px;box-shadow:0 1px 6px rgba(0,0,0,0.05);border:1px solid #f0f0ec;">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:18px;">
                <div>
                    <h2 style="font-size:1.0625rem;font-weight:800;color:#1a1a1a;margin:0 0 3px;">Active Swaps</h2>
                    <p style="font-size:0.75rem;color:#9ca3af;margin:0;">Your ongoing exchanges</p>
                </div>
                <a href="#" style="font-size:0.78rem;font-weight:600;color:#9ca3af;text-decoration:none;transition:color .15s;"
                   onmouseover="this.style.color='#6b7280'" onmouseout="this.style.color='#9ca3af'">See all</a>
            </div>

            <div style="display:flex;flex-direction:column;gap:10px;">
                <div v-for="swap in fakeSwaps" :key="swap.id" class="swap-row"
                     style="display:flex;align-items:center;gap:12px;padding:14px 16px;border:1.5px solid #f0f0ec;border-radius:14px;transition:all .15s;cursor:pointer;">
                    <!-- Image pair -->
                    <div style="display:flex;align-items:center;flex-shrink:0;">
                        <div style="width:44px;height:44px;border-radius:11px;overflow:hidden;border:2px solid #fff;box-shadow:0 0 0 1.5px #f0f0ec;">
                            <img :src="swap.img_a" style="width:100%;height:100%;object-fit:cover;">
                        </div>
                        <div style="width:24px;height:24px;border-radius:50%;background:#f3f4f6;border:2px solid #fff;display:flex;align-items:center;justify-content:center;margin:0 -6px;z-index:1;box-shadow:0 0 0 1px #f0f0ec;">
                            <svg width="9" height="9" fill="none" stroke="#9ca3af" stroke-width="2" viewBox="0 0 24 24"><path d="M8 7h12m0 0l-4-4m4 4l-4 4M16 17H4m0 0l4 4m-4-4l4-4"/></svg>
                        </div>
                        <div style="width:44px;height:44px;border-radius:11px;overflow:hidden;border:2px solid #fff;box-shadow:0 0 0 1.5px #f0f0ec;">
                            <img :src="swap.img_b" style="width:100%;height:100%;object-fit:cover;">
                        </div>
                    </div>
                    <!-- Details -->
                    <div style="flex:1;min-width:0;">
                        <p style="font-size:0.875rem;font-weight:700;color:#1a1a1a;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;margin:0 0 3px;">{{ swap.title }}</p>
                        <p style="font-size:0.72rem;color:#9ca3af;margin:0;">{{ swap.status_text }}</p>
                    </div>
                    <!-- Status + CTA -->
                    <div style="display:flex;align-items:center;gap:8px;flex-shrink:0;">
                        <span style="font-size:0.62rem;font-weight:700;padding:4px 10px;border-radius:8px;letter-spacing:.04em;text-transform:uppercase;border:1px solid;"
                              :style="{background:statusConfig[swap.status]?.bg,color:statusConfig[swap.status]?.color,borderColor:statusConfig[swap.status]?.border}">
                            {{ statusConfig[swap.status]?.label }}
                        </span>
                        <button v-if="swap.cta"
                                style="font-size:0.75rem;font-weight:700;padding:8px 15px;border-radius:999px;border:none;cursor:pointer;font-family:'DM Sans',sans-serif;transition:all .15s;white-space:nowrap;"
                                :style="{background:swap.primary?'#ED730C':'#f3f4f6',color:swap.primary?'#fff':'#3A3330',boxShadow:swap.primary?'0 3px 10px rgba(237,115,12,0.3)':'none'}"
                                :onmouseover="swap.primary?`this.style.background='#d4620a';this.style.transform='translateY(-1px)'`:`this.style.background='#e5e7eb'`"
                                :onmouseout="swap.primary?`this.style.background='#ED730C';this.style.transform='translateY(0)'`:`this.style.background='#f3f4f6'`">
                            {{ swap.cta }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- ── TRENDING COLLECTIONS ── -->
        <div style="background:#fff;border-radius:20px;padding:24px;box-shadow:0 1px 6px rgba(0,0,0,0.05);border:1px solid #f0f0ec;">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:18px;">
                <h2 style="font-size:1.0625rem;font-weight:800;color:#1a1a1a;margin:0;">Trending Collections</h2>
                <span style="background:#f0fdf4;color:#14b8a6;font-size:0.62rem;font-weight:700;padding:4px 10px;border-radius:999px;letter-spacing:.05em;text-transform:uppercase;border:1px solid #a7f3d0;">Updated Hourly</span>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:10px;">
                <div style="grid-column:1/3;position:relative;border-radius:14px;overflow:hidden;aspect-ratio:2/1;cursor:pointer;" class="trend-card">
                    <img :src="fakeTrending[0].image" style="width:100%;height:100%;object-fit:cover;transition:transform .4s;" class="trend-img">
                    <div style="position:absolute;inset:0;background:linear-gradient(to top,rgba(0,0,0,0.7) 0%,transparent 55%);"></div>
                    <div style="position:absolute;bottom:14px;left:16px;">
                        <p style="font-size:0.9rem;font-weight:900;color:#fff;margin:0 0 2px;letter-spacing:.07em;text-transform:uppercase;">{{ fakeTrending[0].label }}</p>
                        <p style="font-size:0.68rem;color:rgba(255,255,255,0.65);margin:0;">{{ fakeTrending[0].count }}</p>
                    </div>
                </div>
                <div v-for="item in fakeTrending.slice(1,3)" :key="item.id"
                     style="position:relative;border-radius:14px;overflow:hidden;aspect-ratio:1/1;cursor:pointer;" class="trend-card">
                    <img :src="item.image" style="width:100%;height:100%;object-fit:cover;transition:transform .4s;" class="trend-img">
                    <div style="position:absolute;inset:0;background:linear-gradient(to top,rgba(0,0,0,0.7) 0%,transparent 60%);"></div>
                    <div style="position:absolute;bottom:10px;left:12px;">
                        <p style="font-size:0.7rem;font-weight:900;color:#fff;margin:0 0 2px;letter-spacing:.06em;text-transform:uppercase;">{{ item.label }}</p>
                        <p style="font-size:0.62rem;color:rgba(255,255,255,0.65);margin:0;">{{ item.count }}</p>
                    </div>
                </div>
                <div style="grid-column:1/2;position:relative;border-radius:14px;overflow:hidden;aspect-ratio:4/3;cursor:pointer;" class="trend-card">
                    <img :src="fakeTrending[3].image" style="width:100%;height:100%;object-fit:cover;transition:transform .4s;" class="trend-img">
                    <div style="position:absolute;inset:0;background:linear-gradient(to top,rgba(0,0,0,0.7) 0%,transparent 55%);"></div>
                    <div style="position:absolute;bottom:10px;left:12px;">
                        <p style="font-size:0.7rem;font-weight:900;color:#fff;margin:0 0 2px;letter-spacing:.06em;text-transform:uppercase;">{{ fakeTrending[3].label }}</p>
                        <p style="font-size:0.62rem;color:rgba(255,255,255,0.65);margin:0;">{{ fakeTrending[3].count }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- ── TOP SWAPPERS ── -->
        <div style="background:#fff;border-radius:20px;padding:24px;box-shadow:0 1px 6px rgba(0,0,0,0.05);border:1px solid #f0f0ec;">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:6px;">
                <div>
                    <h2 style="font-size:1.0625rem;font-weight:800;color:#1a1a1a;margin:0 0 3px;">Top Swappers This Month</h2>
                    <p style="font-size:0.75rem;color:#9ca3af;margin:0;">Community MVPs leading the circular revolution.</p>
                </div>
                <button style="background:#f5f5f0;color:#14b8a6;font-size:0.75rem;font-weight:700;padding:8px 14px;border:1.5px solid #a7f3d0;border-radius:10px;cursor:pointer;font-family:'DM Sans',sans-serif;transition:background .15s;"
                        onmouseover="this.style.background='#e6f7f6'" onmouseout="this.style.background='#f5f5f0'">
                    View Rankings
                </button>
            </div>
            <div style="display:flex;justify-content:space-around;align-items:flex-end;padding:20px 0 8px;gap:10px;overflow-x:auto;" class="hide-scrollbar">
                <div v-for="s in fakeTopSwappers" :key="s.id"
                     style="display:flex;flex-direction:column;align-items:center;gap:8px;flex-shrink:0;cursor:pointer;transition:transform .2s;"
                     onmouseover="this.style.transform='translateY(-4px)'"
                     onmouseout="this.style.transform='translateY(0)'">
                    <div style="position:relative;">
                        <div :style="{
                            width: s.rank<=2?'64px':'56px', height: s.rank<=2?'64px':'56px',
                            borderRadius:'50%', overflow:'hidden',
                            border: s.rank===1?'3px solid #f59e0b':s.rank===2?'3px solid #9ca3af':'2px solid #f0f0ec',
                            boxShadow: s.rank===1?'0 0 0 4px rgba(245,158,11,0.15)':'none',
                        }">
                            <img :src="s.image" :alt="s.name" style="width:100%;height:100%;object-fit:cover;">
                        </div>
                        <div :style="{
                            position:'absolute', bottom:'-4px', right:'-4px',
                            width:'22px', height:'22px', borderRadius:'50%',
                            background: s.rank===1?'#f59e0b':s.rank===2?'#9ca3af':s.rank===3?'#cd7c2e':'#e5e7eb',
                            border:'2.5px solid #fff',
                            display:'flex', alignItems:'center', justifyContent:'center',
                            fontSize:'0.6rem', fontWeight:'900', color:'#fff',
                        }">{{ s.rank }}</div>
                    </div>
                    <div style="text-align:center;">
                        <p style="font-size:0.8125rem;font-weight:700;color:#1a1a1a;margin:0 0 2px;">{{ s.name }}</p>
                        <p style="font-size:0.72rem;color:#9ca3af;margin:0;">{{ s.swaps }} Swaps</p>
                    </div>
                </div>
            </div>
        </div>

    </div><!-- end left column -->

    <!-- ══ RIGHT SIDEBAR ══ -->
    <div style="display:flex;flex-direction:column;gap:16px;">

        <!-- Messages -->
        <div style="background:#fff;border-radius:20px;overflow:hidden;box-shadow:0 1px 6px rgba(0,0,0,0.05);border:1px solid #f0f0ec;">
            <div style="display:flex;align-items:center;justify-content:space-between;padding:18px 18px 0;">
                <div style="display:flex;align-items:center;gap:8px;margin-bottom:14px;">
                    <h3 style="font-size:1.0625rem;font-weight:800;color:#1a1a1a;margin:0;">Messages</h3>
                    <span v-if="unreadCount>0"
                          style="background:#ED730C;color:#fff;font-size:0.6rem;font-weight:800;min-width:20px;height:20px;padding:0 5px;border-radius:999px;display:inline-flex;align-items:center;justify-content:center;box-shadow:0 2px 8px rgba(237,115,12,0.35);">
                        {{ unreadCount }}
                    </span>
                </div>
                <a href="#" style="font-size:0.72rem;font-weight:600;color:#9ca3af;text-decoration:none;margin-bottom:14px;">See all</a>
            </div>

            <!-- Unread highlight bar -->
            <div v-if="unreadCount>0" style="margin:0 14px 12px;background:linear-gradient(135deg,#fff7ed,#fef3c7);border:1px solid #fed7aa;border-radius:10px;padding:10px 12px;display:flex;align-items:center;gap:8px;">
                <svg width="14" height="14" fill="none" stroke="#ED730C" stroke-width="2.5" stroke-linecap="round" viewBox="0 0 24 24"><path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/></svg>
                <p style="font-size:0.78rem;font-weight:700;color:#EA580C;margin:0;">{{ unreadCount }} new messages waiting for you</p>
            </div>

            <div v-for="msg in displayMessages" :key="msg.id"
                 style="display:flex;align-items:center;gap:10px;padding:10px 18px;cursor:pointer;transition:background .12s;border-top:1px solid #f9fafb;"
                 onmouseover="this.style.background='#fafaf8'" onmouseout="this.style.background='transparent'">
                <div style="position:relative;flex-shrink:0;">
                    <div :style="{width:'38px',height:'38px',borderRadius:'50%',background:msg.color,display:'flex',alignItems:'center',justifyContent:'center',fontSize:'0.7rem',fontWeight:'800',color:'#fff'}">
                        {{ msg.initials }}
                    </div>
                    <div v-if="msg.unread" style="position:absolute;bottom:0;right:0;width:11px;height:11px;background:#ED730C;border-radius:50%;border:2px solid #fff;box-shadow:0 0 0 1px rgba(237,115,12,0.2);"></div>
                </div>
                <div style="flex:1;min-width:0;">
                    <div style="display:flex;justify-content:space-between;align-items:baseline;gap:4px;margin-bottom:2px;">
                        <p :style="{fontSize:'0.8125rem',fontWeight:msg.unread?'700':'600',color:'#1a1a1a',whiteSpace:'nowrap',overflow:'hidden',textOverflow:'ellipsis',margin:0}">{{ msg.name }}</p>
                        <span style="font-size:0.65rem;color:#c4c9d4;flex-shrink:0;">{{ msg.time }}</span>
                    </div>
                    <p :style="{fontSize:'0.73rem',color:msg.unread?'#6b7280':'#9ca3af',whiteSpace:'nowrap',overflow:'hidden',textOverflow:'ellipsis',fontWeight:msg.unread?'500':'400',margin:0}">{{ msg.preview }}</p>
                </div>
            </div>

            <div style="padding:12px 18px;border-top:1px solid #f5f5f0;">
                <a href="#" style="display:block;text-align:center;font-size:0.75rem;font-weight:700;color:#14b8a6;text-decoration:none;letter-spacing:.05em;text-transform:uppercase;padding:9px;border-radius:9px;transition:background .12s;"
                   onmouseover="this.style.background='#f0fdf4'" onmouseout="this.style.background='transparent'">
                    Open Messenger →
                </a>
            </div>
        </div>

        <!-- Swap Score card — dark premium -->
        <div style="background:linear-gradient(135deg,#1a0f0a 0%,#2d1810 50%,#0d1f1d 100%);border-radius:20px;padding:22px;position:relative;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,0.15);">
            <div style="position:absolute;top:-30px;right:-30px;width:140px;height:140px;background:radial-gradient(circle,rgba(237,115,12,0.2) 0%,transparent 70%);pointer-events:none;"></div>
            <div style="position:absolute;bottom:-40px;left:-20px;width:120px;height:120px;background:radial-gradient(circle,rgba(20,184,166,0.15) 0%,transparent 70%);pointer-events:none;"></div>

            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;position:relative;z-index:1;">
                <p style="font-size:0.65rem;font-weight:800;letter-spacing:.1em;text-transform:uppercase;color:rgba(255,255,255,0.4);margin:0;">Swap Score</p>
                <a href="#" style="font-size:0.68rem;color:rgba(255,255,255,0.3);text-decoration:none;transition:color .15s;"
                   onmouseover="this.style.color='rgba(255,255,255,0.65)'" onmouseout="this.style.color='rgba(255,255,255,0.3)'">View profile →</a>
            </div>

            <div style="display:flex;align-items:center;gap:16px;position:relative;z-index:1;">
                <svg width="68" height="68" style="transform:rotate(-90deg);flex-shrink:0;">
                    <circle cx="34" cy="34" r="28" fill="none" stroke="rgba(255,255,255,0.07)" stroke-width="5"/>
                    <circle cx="34" cy="34" r="28" fill="none" stroke="#ED730C" stroke-width="5"
                            stroke-dasharray="141.4 175.9"
                            stroke-linecap="round"/>
                </svg>
                <div>
                    <p style="font-size:2.25rem;font-weight:900;color:#fff;line-height:1;margin:0 0 5px;">4.8</p>
                    <div style="display:flex;gap:2px;margin-bottom:6px;">
                        <template v-for="i in 5" :key="i">
                            <svg width="13" height="13" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" :fill="i<=4?'#ED730C':'rgba(255,255,255,0.12)'"/></svg>
                        </template>
                    </div>
                    <p style="font-size:0.72rem;color:rgba(255,255,255,0.4);margin:0;"><strong style="color:#ED730C;">3</strong> swaps this month</p>
                </div>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1px;margin-top:18px;padding-top:18px;border-top:1px solid rgba(255,255,255,0.07);position:relative;z-index:1;">
                <div style="text-align:center;padding:4px 0;">
                    <p style="font-size:1.375rem;font-weight:900;color:#fff;margin:0 0 2px;">28</p>
                    <p style="font-size:0.62rem;color:rgba(255,255,255,0.35);text-transform:uppercase;letter-spacing:.07em;margin:0;">Reviews</p>
                </div>
                <div style="text-align:center;padding:4px 0;border-left:1px solid rgba(255,255,255,0.07);">
                    <p style="font-size:1.375rem;font-weight:900;color:#14b8a6;margin:0 0 2px;">98%</p>
                    <p style="font-size:0.62rem;color:rgba(255,255,255,0.35);text-transform:uppercase;letter-spacing:.07em;margin:0;">Response</p>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div style="background:#fff;border-radius:20px;padding:20px;box-shadow:0 1px 6px rgba(0,0,0,0.05);border:1px solid #f0f0ec;">
            <p style="font-size:0.65rem;font-weight:800;letter-spacing:.1em;text-transform:uppercase;color:#9ca3af;margin:0 0 12px;">Quick Actions</p>
            <div style="display:flex;flex-direction:column;gap:8px;">
                <a href="#" style="display:flex;align-items:center;gap:10px;padding:12px 14px;background:#fff7ed;border-radius:12px;text-decoration:none;border:1.5px solid #fed7aa;transition:all .15s;"
                   onmouseover="this.style.background='#ffedd5';this.style.transform='translateX(2px)'" onmouseout="this.style.background='#fff7ed';this.style.transform='translateX(0)'">
                    <div style="width:32px;height:32px;border-radius:9px;background:rgba(237,115,12,0.15);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <svg width="15" height="15" fill="none" stroke="#ED730C" stroke-width="2.5" stroke-linecap="round" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    </div>
                    <span style="font-size:0.845rem;font-weight:700;color:#EA580C;">Post New Item</span>
                    <svg style="margin-left:auto;" width="13" height="13" fill="none" stroke="#ED730C" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                </a>
                <a href="/services" style="display:flex;align-items:center;gap:10px;padding:12px 14px;background:#f0fdf4;border-radius:12px;text-decoration:none;border:1.5px solid #a7f3d0;transition:all .15s;"
                   onmouseover="this.style.background='#dcfce7';this.style.transform='translateX(2px)'" onmouseout="this.style.background='#f0fdf4';this.style.transform='translateX(0)'">
                    <div style="width:32px;height:32px;border-radius:9px;background:rgba(20,184,166,0.15);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <svg width="15" height="15" fill="none" stroke="#14b8a6" stroke-width="2.5" stroke-linecap="round" viewBox="0 0 24 24"><path d="M8 7h12m0 0l-4-4m4 4l-4 4M16 17H4m0 0l4 4m-4-4l4-4"/></svg>
                    </div>
                    <span style="font-size:0.845rem;font-weight:700;color:#0d9488;">Offer a Service</span>
                    <svg style="margin-left:auto;" width="13" height="13" fill="none" stroke="#14b8a6" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                </a>
                <a href="/garage-sale" style="display:flex;align-items:center;gap:10px;padding:12px 14px;background:#f5f5f0;border-radius:12px;text-decoration:none;border:1.5px solid #e5e7eb;transition:all .15s;"
                   onmouseover="this.style.background='#e5e7eb';this.style.transform='translateX(2px)'" onmouseout="this.style.background='#f5f5f0';this.style.transform='translateX(0)'">
                    <div style="width:32px;height:32px;border-radius:9px;background:rgba(107,114,128,0.1);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <svg width="15" height="15" fill="none" stroke="#6b7280" stroke-width="2.5" stroke-linecap="round" viewBox="0 0 24 24"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 01-8 0"/></svg>
                    </div>
                    <span style="font-size:0.845rem;font-weight:700;color:#6b7280;">My Garage Sale</span>
                    <svg style="margin-left:auto;" width="13" height="13" fill="none" stroke="#9ca3af" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>
        </div>

    </div><!-- end sidebar -->

</div><!-- end main grid -->
</div><!-- end max-w container -->
<PostItemModal
    v-if="showPostModal"
    @close="showPostModal = false"
    @posted="showPostModal = false"
/>
</div>
</template>

<style>
/* Scrollbar */
.hide-scrollbar::-webkit-scrollbar { display: none; }
.hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

/* Stat card hover */
.stat-card:hover {
    transform: translateY(-3px) !important;
    box-shadow: 0 8px 24px rgba(0,0,0,0.08) !important;
}

/* Match card hover */
.match-card:hover {
    border-color: #14b8a6 !important;
    box-shadow: 0 8px 28px rgba(20,184,166,0.14) !important;
    transform: translateY(-3px);
}
.match-card:hover .match-img { transform: scale(1.05); }

/* Feed card hover */
.feed-card:hover {
    border-color: #ED730C !important;
    box-shadow: 0 8px 28px rgba(237,115,12,0.1) !important;
    transform: translateY(-3px);
}
.feed-card:hover .feed-img { transform: scale(1.05); }

/* Offer button hover */
.offer-btn:hover {
    background: #ED730C !important;
    color: #fff !important;
}

/* Swap button */
.swap-btn-sm:hover {
    background: #0d9488 !important;
    transform: translateY(-1px);
}

/* Swap row hover */
.swap-row:hover {
    border-color: #e5e7eb !important;
    background: #fafafa !important;
}

/* Trend card hover */
.trend-card:hover .trend-img { transform: scale(1.06); }

/* Responsive */
@media (max-width: 1200px) {
    .main-grid { grid-template-columns: 1fr !important; }
}
@media (max-width: 768px) {
    .stats-grid { grid-template-columns: repeat(2,1fr) !important; }
    .feed-grid  { grid-template-columns: 1fr !important; }
}
@media (max-width: 480px) {
    .stats-grid { grid-template-columns: 1fr !important; }
}
</style>