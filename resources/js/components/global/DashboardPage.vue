<script setup>
// filepath: resources/js/components/global/DashboardPage.vue
import { ref, computed } from 'vue'

const el          = document.getElementById('dashboard-app')
const user        = ref(JSON.parse(el?.dataset.user        || '{}'))
const stats       = ref(JSON.parse(el?.dataset.stats       || '[]'))
const matches     = ref(JSON.parse(el?.dataset.matches     || '[]'))
const swaps       = ref(JSON.parse(el?.dataset.swaps       || '[]'))
const messages    = ref(JSON.parse(el?.dataset.messages    || '[]'))
const credibility = ref(JSON.parse(el?.dataset.credibility || '{}'))

// ── Fake fallback data ────────────────────────────────────────────────────────
const fakeUser = { name: 'Alex Rivera', username: 'alex_r', email: 'alex@example.com', avatar: null }

const fakeStats = [
    { key: 'listings',     label: 'Active Listings',       value: '4',   icon: '📦', color: '#ED730C', bg: '#fff7ed' },
    { key: 'swaps',        label: 'Completed Swaps',       value: '12',  icon: '🔄', color: '#14b8a6', bg: '#f0fdf4' },
    { key: 'negotiations', label: 'Ongoing Negotiations',  value: '3',   icon: '🤝', color: '#6366f1', bg: '#eef2ff' },
    { key: 'views',        label: 'Item Views Today',      value: '127', icon: '👁', color: '#f59e0b', bg: '#fffbeb' },
]

const fakeMatches = [
    { id: 1, title: 'Minimalist Chrono Watch', wants: 'Vintage Camera', match_percent: 94, distance: '1.2', image: 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=400&q=80', category: 'Accessories', user: 'Jordan K.', initials: 'JK', user_color: '#14b8a6' },
    { id: 2, title: 'Sony WH-1000XM4',         wants: 'Gaming Chair',   match_percent: 87, distance: '2.5', image: 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=400&q=80', category: 'Electronics', user: 'Sarah L.',  initials: 'SL', user_color: '#ED730C' },
    { id: 3, title: 'Canon EOS R6',             wants: 'MacBook Pro',    match_percent: 91, distance: '0.8', image: 'https://images.unsplash.com/photo-1516035069371-29a1b244cc32?w=400&q=80', category: 'Photography', user: 'Mike C.',   initials: 'MC', user_color: '#6366f1' },
    { id: 4, title: 'Trek Mountain Bike',        wants: 'Drone',          match_percent: 78, distance: '3.1', image: 'https://images.unsplash.com/photo-1485965120184-e220f721d03e?w=400&q=80', category: 'Sports',      user: 'Tiana R.', initials: 'TR', user_color: '#f59e0b' },
]

const fakeSwapFeed = [
    { id: 1, title: 'Air Jordan 1 Retro',  category: 'Streetwear',  distance: '0.8km', swapper: 'Marcus T.', initials: 'MT', color: '#ED730C', image: 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=500&q=80' },
    { id: 2, title: 'Fujica ST801 Film',   category: 'Tech',         distance: '2.4km', swapper: 'Elena S.',  initials: 'ES', color: '#14b8a6', image: 'https://images.unsplash.com/photo-1516035069371-29a1b244cc32?w=500&q=80' },
    { id: 3, title: 'Minimalist Watch',    category: 'Accessories',  distance: '1.1km', swapper: 'Jordan K.', initials: 'JK', color: '#6366f1', image: 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=500&q=80' },
    { id: 4, title: 'Sony WH-1000XM4',    category: 'Electronics',  distance: '3.2km', swapper: 'Sarah L.',  initials: 'SL', color: '#f59e0b', image: 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=500&q=80' },
]

const fakeActivity = [
    { id: 1, icon: '🔄', title: 'Swap Request Accepted!',  desc: 'Jordan K. accepted your swap for "Mechanical Keyboard".', time: 'Just now',   color: '#ED730C', bg: '#fff7ed' },
    { id: 2, icon: '📦', title: 'Package Arrived',          desc: 'Your vinyl set from Sarah is ready for pickup at Hub #04.',   time: '2 hours ago', color: '#14b8a6', bg: '#f0fdf4' },
    { id: 3, icon: '💬', title: 'New Message',              desc: '"Is the 35mm lens still available for a swap?"',                time: 'Yesterday',   color: '#6366f1', bg: '#eef2ff' },
]

const fakeMessages = [
    { id: 1, name: 'Marcus K.',  initials: 'MK', preview: 'Hey! Still interested in the lens swap?', time: '2m',  unread: true,  color: '#14b8a6' },
    { id: 2, name: 'Sarah L.',   initials: 'SL', preview: 'Deal confirmed ✓ Shipping tomorrow',       time: '1h',  unread: true,  color: '#ED730C' },
    { id: 3, name: 'Julian D.',  initials: 'JD', preview: 'Thanks for the smooth swap!',              time: '3h',  unread: false, color: '#8b5cf6' },
    { id: 4, name: 'Anna W.',    initials: 'AW', preview: 'Can we do a partial value difference?',    time: '1d',  unread: false, color: '#f59e0b' },
]

const fakeSwaps = [
    { id: 1, title: 'Your Watch → Canon Lens',   status: 'pending',    status_text: 'Awaiting your response · 2h ago', cta: 'Respond Now', primary: true,  img_a: 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=80', img_b: 'https://images.unsplash.com/photo-1516035069371-29a1b244cc32?w=80' },
    { id: 2, title: 'MacBook → Sony Camera',      status: 'in_transit', status_text: 'Items shipped · Est. delivery Fri', cta: 'Track',       primary: false, img_a: 'https://images.unsplash.com/photo-1603302576837-37561b2e2302?w=80', img_b: 'https://images.unsplash.com/photo-1516035069371-29a1b244cc32?w=80' },
    { id: 3, title: 'Headphones → Keyboard',      status: 'completed',  status_text: 'Completed 3 days ago',              cta: 'Review',      primary: false, img_a: 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=80', img_b: 'https://images.unsplash.com/photo-1561154464-82e9adf32764?w=80' },
]

const fakeTrending = [
    { id: 1, label: 'STREETWEAR',   count: '1.2k swaps', image: 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=700&q=80' },
    { id: 2, label: 'TECH',         count: '840 swaps',  image: 'https://images.unsplash.com/photo-1603302576837-37561b2e2302?w=400&q=80' },
    { id: 3, label: 'VINYL RECORDS',count: '320 swaps',  image: 'https://images.unsplash.com/photo-1603048588665-791ca8aea617?w=400&q=80' },
    { id: 4, label: 'HOME DECOR',   count: '510 swaps',  image: 'https://images.unsplash.com/photo-1555041469-a586c61ea9bc?w=500&q=80' },
]

const fakeTopSwappers = [
    { id: 1, rank: 1, name: 'Jordan K.', swaps: 42, image: 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=100&q=80' },
    { id: 2, rank: 2, name: 'Sarah L.',  swaps: 38, image: 'https://images.unsplash.com/photo-1494790108755-2616b612b786?w=100&q=80' },
    { id: 3, rank: 3, name: 'Mike Chen', swaps: 35, image: 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=100&q=80' },
    { id: 4, rank: 4, name: 'Tiana R.',  swaps: 31, image: 'https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=100&q=80' },
    { id: 5, rank: 5, name: 'Chris V.',  swaps: 29, image: 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=100&q=80' },
]

const fakeCredibility = { score: 4.8, items_this_month: 3, total_reviews: 28, verified: true, member_since: 'Jan 2024' }

// Resolve display data
const displayUser        = computed(() => Object.keys(user.value).length ? user.value : fakeUser)
const displayStats       = computed(() => stats.value.length       ? stats.value       : fakeStats)
const displayMatches     = computed(() => matches.value.length     ? matches.value     : fakeMatches)
const displaySwapFeed    = computed(() => (matches.value.length ? matches.value : fakeSwapFeed).slice(0, 4))
const displayActivity    = computed(() => swaps.value.length       ? swaps.value       : fakeActivity)
const displayMessages    = computed(() => messages.value.length    ? messages.value    : fakeMessages)
const displaySwaps       = computed(() => (swaps.value.filter ? swaps.value : fakeSwaps))
const displayCredibility = computed(() => Object.keys(credibility.value).length ? credibility.value : fakeCredibility)

const firstName   = computed(() => displayUser.value.name?.split(' ')[0] || 'Alex')
const unreadCount = computed(() => displayMessages.value.filter(m => m.unread).length)
const pendingCount = computed(() => fakeSwaps.filter(s => s.status === 'pending').length)
const strongMatches = computed(() => displayMatches.value.filter(m => m.match_percent >= 85))

const greeting = computed(() => {
    const h = new Date().getHours()
    if (h < 12) return 'Good morning'
    if (h < 18) return 'Good afternoon'
    return 'Good evening'
})

const statusConfig = {
    pending:    { label: 'Pending',    bg: '#fff7ed', color: '#EA580C', border: '#fed7aa' },
    in_transit: { label: 'In Transit', bg: '#f0fdf4', color: '#16a34a', border: '#bbf7d0' },
    completed:  { label: 'Completed',  bg: '#eff6ff', color: '#2563eb', border: '#bfdbfe' },
    declined:   { label: 'Declined',   bg: '#fef2f2', color: '#dc2626', border: '#fecaca' },
}

function matchColor(pct) {
    if (pct >= 90) return '#14b8a6'
    if (pct >= 80) return '#ED730C'
    return '#6b7280'
}
</script>

<template>
<div style="min-height:100vh;background:#f5f5f0;font-family:'DM Sans',sans-serif;">
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" style="padding-top:28px;padding-bottom:64px;">

    <!-- ══════════════════════════════════════════════════════
         WELCOME BANNER — full width, warm, with CTAs
    ══════════════════════════════════════════════════════ -->
    <div style="position:relative;background:linear-gradient(135deg,#fdf6ee 0%,#fef9f5 50%,#f0fdf9 100%);border-radius:22px;padding:32px 36px;overflow:hidden;border:1px solid #f3ebe0;margin-bottom:24px;">

        <!-- Decorative watermark -->
        <div style="position:absolute;top:-10px;right:60px;font-size:9rem;opacity:0.04;color:#3A3330;font-weight:900;pointer-events:none;user-select:none;line-height:1;">✦</div>
        <div style="position:absolute;bottom:-30px;right:200px;font-size:6rem;opacity:0.03;color:#ED730C;pointer-events:none;user-select:none;line-height:1;">✦</div>

        <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:20px;">
            <div>
                <p style="font-size:0.68rem;font-weight:800;letter-spacing:.12em;text-transform:uppercase;color:#ED730C;margin:0 0 8px;">Community Dashboard</p>
                <h1 style="font-size:2rem;font-weight:900;color:#1a1a1a;margin:0 0 10px;line-height:1.1;">
                    {{ greeting }}, <span style="color:#ED730C;">{{ firstName }}!</span>
                </h1>

                <!-- Smart personalised hints -->
                <div style="display:flex;flex-wrap:wrap;gap:8px;">
                    <span v-if="strongMatches.length > 0"
                          style="display:inline-flex;align-items:center;gap:5px;background:#f0fdf4;border:1px solid #bbf7d0;color:#16a34a;font-size:0.78rem;font-weight:600;padding:5px 12px;border-radius:999px;">
                        ⚡ {{ strongMatches.length }} strong matches waiting
                    </span>
                    <span v-if="pendingCount > 0"
                          style="display:inline-flex;align-items:center;gap:5px;background:#fff7ed;border:1px solid #fed7aa;color:#EA580C;font-size:0.78rem;font-weight:600;padding:5px 12px;border-radius:999px;">
                        🔔 {{ pendingCount }} swap {{ pendingCount === 1 ? 'request' : 'requests' }} need response
                    </span>
                    <span v-if="unreadCount > 0"
                          style="display:inline-flex;align-items:center;gap:5px;background:#eef2ff;border:1px solid #c7d2fe;color:#6366f1;font-size:0.78rem;font-weight:600;padding:5px 12px;border-radius:999px;">
                        💬 {{ unreadCount }} unread messages
                    </span>
                    <span style="display:inline-flex;align-items:center;gap:5px;background:#fffbeb;border:1px solid #fde68a;color:#d97706;font-size:0.78rem;font-weight:600;padding:5px 12px;border-radius:999px;">
                        👁 Your item has 127 views today
                    </span>
                </div>
            </div>

            <!-- Primary CTAs -->
            <div style="display:flex;align-items:center;gap:10px;flex-shrink:0;">
                <a href="#"
                   style="display:inline-flex;align-items:center;gap:7px;background:#ED730C;color:#fff;font-size:0.875rem;font-weight:700;padding:12px 22px;border-radius:12px;text-decoration:none;transition:all .15s;box-shadow:0 4px 16px rgba(237,115,12,0.3);white-space:nowrap;"
                   onmouseover="this.style.background='#d4620a';this.style.transform='translateY(-1px)'"
                   onmouseout="this.style.background='#ED730C';this.style.transform='translateY(0)'">
                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    List an Item
                </a>
                <a href="/browse"
                   style="display:inline-flex;align-items:center;gap:7px;background:#fff;color:#3A3330;font-size:0.875rem;font-weight:700;padding:12px 22px;border-radius:12px;text-decoration:none;transition:all .15s;border:1.5px solid #e5e7eb;white-space:nowrap;"
                   onmouseover="this.style.borderColor='#ED730C';this.style.color='#ED730C';this.style.transform='translateY(-1px)'"
                   onmouseout="this.style.borderColor='#e5e7eb';this.style.color='#3A3330';this.style.transform='translateY(0)'">
                    Browse Items
                </a>
            </div>
        </div>
    </div>

    <!-- ══ STATS ROW — progress & activity feedback ══ -->
    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:24px;" class="stats-grid">
        <div v-for="stat in displayStats" :key="stat.key"
             :style="{background:'#fff',borderRadius:'16px',padding:'18px 20px',border:'1.5px solid #f0f0ec',display:'flex',alignItems:'center',gap:'14px',transition:'all .2s',cursor:'default'}"
             onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 6px 20px rgba(0,0,0,0.06)'"
             onmouseout="this.style.transform='translateY(0)';this.style.boxShadow='none'">
            <div :style="{width:'44px',height:'44px',borderRadius:'12px',background:stat.bg||'#fff7ed',display:'flex',alignItems:'center',justifyContent:'center',fontSize:'1.25rem',flexShrink:0}">
                {{ stat.icon }}
            </div>
            <div>
                <p style="font-size:1.5rem;font-weight:900;color:#1a1a1a;margin:0;line-height:1;">{{ stat.value }}</p>
                <p style="font-size:0.72rem;color:#9ca3af;margin:3px 0 0;font-weight:500;">{{ stat.label }}</p>
            </div>
        </div>
    </div>

    <!-- ══ MAIN CONTENT GRID ══ -->
    <div style="display:grid;grid-template-columns:1fr 340px;gap:20px;align-items:start;" class="main-grid">

        <!-- LEFT COLUMN -->
        <div style="display:flex;flex-direction:column;gap:20px;">

            <!-- ── TOP MATCHES (matchmaking highlight) ── -->
            <div style="background:#fff;border-radius:18px;padding:22px;border:1.5px solid #f0f0ec;">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:6px;">
                    <div style="display:flex;align-items:center;gap:10px;">
                        <h2 style="font-size:1.0625rem;font-weight:800;color:#1a1a1a;margin:0;">Top Matches for You</h2>
                        <span style="background:#f0fdf4;color:#14b8a6;font-size:0.62rem;font-weight:800;padding:3px 10px;border-radius:999px;letter-spacing:.05em;text-transform:uppercase;">AI Powered</span>
                    </div>
                    <a href="/browse" style="font-size:0.78rem;font-weight:700;color:#14b8a6;text-decoration:none;display:inline-flex;align-items:center;gap:3px;"
                       onmouseover="this.style.color='#0d9488'" onmouseout="this.style.color='#14b8a6'">
                        View All <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>
                <p style="font-size:0.78rem;color:#9ca3af;margin:0 0 18px;">Items our algorithm thinks you'll love based on your swap preferences</p>

                <div style="display:flex;gap:14px;overflow-x:auto;padding-bottom:6px;" class="hide-scrollbar">
                    <div v-for="match in displayMatches" :key="match.id"
                         class="match-card"
                         style="flex-shrink:0;width:210px;border:1.5px solid #f0f0ec;border-radius:16px;overflow:hidden;cursor:pointer;background:#fff;transition:all .2s;">

                        <div style="position:relative;aspect-ratio:4/3;overflow:hidden;background:#f5f5f0;">
                            <img :src="match.image" :alt="match.title" class="match-img" style="width:100%;height:100%;object-fit:cover;transition:transform .35s;">
                            <!-- Match % badge -->
                            <div :style="{
                                position:'absolute',top:'9px',right:'9px',
                                background:matchColor(match.match_percent),
                                color:'#fff',fontSize:'0.65rem',fontWeight:'900',
                                padding:'4px 9px',borderRadius:'8px',letterSpacing:'.03em'
                            }">{{ match.match_percent }}% MATCH</div>
                            <!-- Distance -->
                            <div style="position:absolute;bottom:8px;left:8px;background:rgba(0,0,0,0.55);color:#fff;font-size:0.62rem;font-weight:600;padding:3px 7px;border-radius:6px;backdrop-filter:blur(4px);">
                                📍 {{ match.distance }} mi
                            </div>
                        </div>

                        <div style="padding:12px 14px 14px;">
                            <p style="font-size:0.875rem;font-weight:700;color:#1a1a1a;margin:0 0 3px;line-height:1.3;">{{ match.title }}</p>
                            <p style="font-size:0.72rem;color:#9ca3af;margin:0 0 10px;">Wants: <strong style="color:#6b7280;">{{ match.wants }}</strong></p>
                            <!-- User row -->
                            <div style="display:flex;align-items:center;justify-content:space-between;">
                                <div style="display:flex;align-items:center;gap:5px;">
                                    <div :style="{width:'20px',height:'20px',borderRadius:'50%',background:match.user_color||'#ED730C',display:'flex',alignItems:'center',justifyContent:'center',fontSize:'0.5rem',fontWeight:'800',color:'#fff'}">
                                        {{ match.initials }}
                                    </div>
                                    <span style="font-size:0.72rem;color:#9ca3af;">{{ match.user }}</span>
                                </div>
                                <button style="background:#e6f7f6;color:#14b8a6;font-size:0.7rem;font-weight:700;border:none;padding:5px 11px;border-radius:8px;cursor:pointer;font-family:'DM Sans',sans-serif;transition:all .15s;"
                                        onmouseover="this.style.background='#14b8a6';this.style.color='#fff'"
                                        onmouseout="this.style.background='#e6f7f6';this.style.color='#14b8a6'">
                                    Swap
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ── YOUR SWAP FEED ── -->
            <div style="background:#fff;border-radius:18px;padding:22px;border:1.5px solid #f0f0ec;">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:18px;">
                    <h2 style="font-size:1.0625rem;font-weight:800;color:#1a1a1a;margin:0;">Your Swap Feed</h2>
                    <a href="/browse" style="font-size:0.78rem;font-weight:700;color:#14b8a6;text-decoration:none;display:inline-flex;align-items:center;gap:3px;"
                       onmouseover="this.style.color='#0d9488'" onmouseout="this.style.color='#14b8a6'">
                        View All <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;" class="feed-grid">
                    <div v-for="item in displaySwapFeed" :key="item.id"
                         class="feed-card"
                         style="border:1.5px solid #f0f0ec;border-radius:16px;overflow:hidden;cursor:pointer;transition:all .2s;background:#fff;">
                        <div style="position:relative;aspect-ratio:4/3;overflow:hidden;background:#f5f5f0;">
                            <img :src="item.image" :alt="item.title" class="feed-img" style="width:100%;height:100%;object-fit:cover;transition:transform .35s;">
                            <span style="position:absolute;top:10px;left:10px;background:rgba(255,255,255,0.92);color:#1a1a1a;font-size:0.65rem;font-weight:700;padding:4px 10px;border-radius:999px;backdrop-filter:blur(6px);">{{ item.category }}</span>
                        </div>
                        <div style="padding:13px 14px 15px;">
                            <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:8px;">
                                <h3 style="font-size:0.9rem;font-weight:700;color:#1a1a1a;margin:0;line-height:1.3;flex:1;">{{ item.title }}</h3>
                                <span style="font-size:0.72rem;font-weight:700;color:#14b8a6;white-space:nowrap;margin-left:6px;">{{ item.distance }}</span>
                            </div>
                            <div style="display:flex;align-items:center;gap:5px;margin-bottom:11px;">
                                <div :style="{width:'20px',height:'20px',borderRadius:'50%',background:item.color||'#ED730C',display:'flex',alignItems:'center',justifyContent:'center',fontSize:'0.5rem',fontWeight:'700',color:'#fff',flexShrink:0}">{{ item.initials }}</div>
                                <span style="font-size:0.73rem;color:#9ca3af;">Swapping by <strong style="color:#6b7280;">{{ item.swapper }}</strong></span>
                            </div>
                            <button style="width:100%;padding:9px;background:transparent;color:#ED730C;font-size:0.78rem;font-weight:700;border:1.5px solid #ED730C;border-radius:10px;cursor:pointer;font-family:'DM Sans',sans-serif;transition:all .15s;"
                                    onmouseover="this.style.background='#ED730C';this.style.color='#fff'"
                                    onmouseout="this.style.background='transparent';this.style.color='#ED730C'">
                                Make an Offer
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ── ACTIVE SWAPS ── -->
            <div style="background:#fff;border-radius:18px;padding:22px;border:1.5px solid #f0f0ec;">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:18px;">
                    <div>
                        <h2 style="font-size:1.0625rem;font-weight:800;color:#1a1a1a;margin:0 0 3px;">Active Swaps</h2>
                        <p style="font-size:0.75rem;color:#9ca3af;margin:0;">Your ongoing exchanges</p>
                    </div>
                    <a href="#" style="font-size:0.78rem;font-weight:600;color:#9ca3af;text-decoration:none;">See all</a>
                </div>
                <div style="display:flex;flex-direction:column;gap:10px;">
                    <div v-for="swap in fakeSwaps" :key="swap.id"
                         style="display:flex;align-items:center;gap:12px;padding:13px 15px;border:1.5px solid #f0f0ec;border-radius:14px;transition:all .15s;cursor:pointer;"
                         onmouseover="this.style.borderColor='#e5e7eb';this.style.background='#fafaf8'"
                         onmouseout="this.style.borderColor='#f0f0ec';this.style.background='transparent'">
                        <!-- Image pair -->
                        <div style="display:flex;align-items:center;flex-shrink:0;">
                            <div style="width:42px;height:42px;border-radius:10px;overflow:hidden;border:2px solid #fff;box-shadow:0 0 0 1.5px #f0f0ec;">
                                <img :src="swap.img_a" style="width:100%;height:100%;object-fit:cover;">
                            </div>
                            <div style="width:22px;height:22px;border-radius:50%;background:#f5f5f0;border:2px solid #fff;display:flex;align-items:center;justify-content:center;margin:0 -5px;z-index:1;box-shadow:0 0 0 1px #f0f0ec;">
                                <svg width="9" height="9" fill="none" stroke="#9ca3af" stroke-width="2" viewBox="0 0 24 24"><path d="M8 7h12m0 0l-4-4m4 4l-4 4M16 17H4m0 0l4 4m-4-4l4-4"/></svg>
                            </div>
                            <div style="width:42px;height:42px;border-radius:10px;overflow:hidden;border:2px solid #fff;box-shadow:0 0 0 1.5px #f0f0ec;">
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
                                    style="font-size:0.75rem;font-weight:700;padding:7px 14px;border-radius:999px;border:none;cursor:pointer;font-family:'DM Sans',sans-serif;transition:all .15s;white-space:nowrap;"
                                    :style="{background:swap.primary?'#ED730C':'#f3f4f6',color:swap.primary?'#fff':'#3A3330',boxShadow:swap.primary?'0 2px 8px rgba(237,115,12,0.25)':'none'}"
                                    :onmouseover="swap.primary?`this.style.background='#d4620a'`:`this.style.background='#e5e7eb'`"
                                    :onmouseout="swap.primary?`this.style.background='#ED730C'`:`this.style.background='#f3f4f6'`">
                                {{ swap.cta }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ── TRENDING COLLECTIONS ── -->
            <div style="background:#fff;border-radius:18px;padding:22px;border:1.5px solid #f0f0ec;">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:18px;">
                    <h2 style="font-size:1.0625rem;font-weight:800;color:#1a1a1a;margin:0;">Trending Collections</h2>
                    <span style="background:#f0fdf4;color:#14b8a6;font-size:0.62rem;font-weight:700;padding:4px 10px;border-radius:999px;letter-spacing:.04em;text-transform:uppercase;">Updated Hourly</span>
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr 1fr;grid-template-rows:auto auto;gap:10px;">
                    <div style="grid-column:1/3;position:relative;border-radius:14px;overflow:hidden;aspect-ratio:2/1;cursor:pointer;" class="trend-card">
                        <img :src="fakeTrending[0].image" style="width:100%;height:100%;object-fit:cover;transition:transform .4s;" class="trend-img">
                        <div style="position:absolute;inset:0;background:linear-gradient(to top,rgba(0,0,0,0.65) 0%,transparent 50%);"></div>
                        <div style="position:absolute;bottom:14px;left:16px;">
                            <p style="font-size:0.875rem;font-weight:900;color:#fff;margin:0 0 2px;letter-spacing:.06em;text-transform:uppercase;">{{ fakeTrending[0].label }}</p>
                            <p style="font-size:0.7rem;color:rgba(255,255,255,0.7);margin:0;">{{ fakeTrending[0].count }}</p>
                        </div>
                    </div>
                    <div v-for="item in fakeTrending.slice(1,3)" :key="item.id"
                         style="position:relative;border-radius:14px;overflow:hidden;aspect-ratio:1/1;cursor:pointer;" class="trend-card">
                        <img :src="item.image" style="width:100%;height:100%;object-fit:cover;transition:transform .4s;" class="trend-img">
                        <div style="position:absolute;inset:0;background:linear-gradient(to top,rgba(0,0,0,0.65) 0%,transparent 60%);"></div>
                        <div style="position:absolute;bottom:10px;left:12px;">
                            <p style="font-size:0.72rem;font-weight:900;color:#fff;margin:0 0 2px;letter-spacing:.05em;text-transform:uppercase;">{{ item.label }}</p>
                            <p style="font-size:0.62rem;color:rgba(255,255,255,0.7);margin:0;">{{ item.count }}</p>
                        </div>
                    </div>
                    <div style="grid-column:1/2;position:relative;border-radius:14px;overflow:hidden;aspect-ratio:4/3;cursor:pointer;" class="trend-card">
                        <img :src="fakeTrending[3].image" style="width:100%;height:100%;object-fit:cover;transition:transform .4s;" class="trend-img">
                        <div style="position:absolute;inset:0;background:linear-gradient(to top,rgba(0,0,0,0.65) 0%,transparent 55%);"></div>
                        <div style="position:absolute;bottom:10px;left:12px;">
                            <p style="font-size:0.72rem;font-weight:900;color:#fff;margin:0 0 2px;letter-spacing:.05em;text-transform:uppercase;">{{ fakeTrending[3].label }}</p>
                            <p style="font-size:0.62rem;color:rgba(255,255,255,0.7);margin:0;">{{ fakeTrending[3].count }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ── TOP SWAPPERS ── -->
            <div style="background:#fff;border-radius:18px;padding:22px;border:1.5px solid #f0f0ec;">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:6px;">
                    <div>
                        <h2 style="font-size:1.0625rem;font-weight:800;color:#1a1a1a;margin:0 0 3px;">Top Swappers This Month</h2>
                        <p style="font-size:0.75rem;color:#9ca3af;margin:0;">Community MVPs leading the circular revolution.</p>
                    </div>
                    <button style="background:#f5f5f0;color:#14b8a6;font-size:0.75rem;font-weight:700;padding:8px 14px;border:1.5px solid #d1fae5;border-radius:10px;cursor:pointer;font-family:'DM Sans',sans-serif;transition:background .15s;"
                            onmouseover="this.style.background='#f0fdf4'" onmouseout="this.style.background='#f5f5f0'">
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
                                width:s.rank<=2?'60px':'52px',height:s.rank<=2?'60px':'52px',
                                borderRadius:'50%',overflow:'hidden',
                                border:s.rank===1?'3px solid #f59e0b':s.rank===2?'3px solid #9ca3af':'2px solid #f0f0ec',
                                boxShadow:s.rank===1?'0 0 0 4px rgba(245,158,11,0.15)':'none'
                            }">
                                <img :src="s.image" :alt="s.name" style="width:100%;height:100%;object-fit:cover;">
                            </div>
                            <div :style="{
                                position:'absolute',bottom:'-4px',right:'-4px',
                                width:'20px',height:'20px',borderRadius:'50%',
                                background:s.rank===1?'#f59e0b':s.rank===2?'#9ca3af':s.rank===3?'#cd7c2e':'#e5e7eb',
                                border:'2px solid #fff',display:'flex',alignItems:'center',justifyContent:'center',
                                fontSize:'0.58rem',fontWeight:'900',color:'#fff',
                            }">{{ s.rank }}</div>
                        </div>
                        <div style="text-align:center;">
                            <p style="font-size:0.8rem;font-weight:700;color:#1a1a1a;margin:0 0 2px;">{{ s.name }}</p>
                            <p style="font-size:0.7rem;color:#9ca3af;margin:0;">{{ s.swaps }} Swaps</p>
                        </div>
                    </div>
                </div>
            </div>

        </div><!-- end left col -->

        <!-- RIGHT SIDEBAR -->
        <div style="display:flex;flex-direction:column;gap:16px;">

            <!-- ── MESSAGES — visible & prominent ── -->
            <div style="background:#fff;border-radius:18px;overflow:hidden;border:1.5px solid #f0f0ec;">
                <div style="display:flex;align-items:center;justify-content:space-between;padding:18px 18px 12px;">
                    <div style="display:flex;align-items:center;gap:8px;">
                        <h3 style="font-size:1rem;font-weight:800;color:#1a1a1a;margin:0;">Messages</h3>
                        <span v-if="unreadCount>0"
                              style="background:#ED730C;color:#fff;font-size:0.6rem;font-weight:800;min-width:18px;height:18px;padding:0 5px;border-radius:999px;display:inline-flex;align-items:center;justify-content:center;">
                            {{ unreadCount }}
                        </span>
                    </div>
                    <a href="#" style="font-size:0.72rem;font-weight:600;color:#9ca3af;text-decoration:none;">See all</a>
                </div>

                <!-- Unread banner -->
                <div v-if="unreadCount > 0"
                     style="margin:0 12px 10px;background:#fff7ed;border:1px solid #fed7aa;border-radius:10px;padding:10px 12px;display:flex;align-items:center;gap:8px;">
                    <span style="font-size:1rem;">💬</span>
                    <p style="font-size:0.78rem;font-weight:600;color:#EA580C;margin:0;">
                        <strong>{{ unreadCount }} new messages</strong> waiting for you
                    </p>
                </div>

                <div v-for="msg in displayMessages" :key="msg.id"
                     style="display:flex;align-items:center;gap:10px;padding:10px 18px;cursor:pointer;transition:background .12s;border-top:1px solid #f9fafb;"
                     onmouseover="this.style.background='#fafaf8'"
                     onmouseout="this.style.background='transparent'">
                    <div style="position:relative;flex-shrink:0;">
                        <div :style="{width:'36px',height:'36px',borderRadius:'50%',background:msg.color||'#ED730C',display:'flex',alignItems:'center',justifyContent:'center',fontSize:'0.7rem',fontWeight:'800',color:'#fff'}">
                            {{ msg.initials }}
                        </div>
                        <div v-if="msg.unread" style="position:absolute;bottom:0;right:0;width:10px;height:10px;background:#ED730C;border-radius:50%;border:2px solid #fff;"></div>
                    </div>
                    <div style="flex:1;min-width:0;">
                        <div style="display:flex;justify-content:space-between;align-items:baseline;gap:4px;margin-bottom:1px;">
                            <p :style="{fontSize:'0.8125rem',fontWeight:msg.unread?'700':'600',color:'#1a1a1a',whiteSpace:'nowrap',overflow:'hidden',textOverflow:'ellipsis',margin:0}">{{ msg.name }}</p>
                            <span style="font-size:0.65rem;color:#c4c9d4;flex-shrink:0;">{{ msg.time }}</span>
                        </div>
                        <p :style="{fontSize:'0.73rem',color:msg.unread?'#6b7280':'#9ca3af',whiteSpace:'nowrap',overflow:'hidden',textOverflow:'ellipsis',fontWeight:msg.unread?'500':'400',margin:0}">{{ msg.preview }}</p>
                    </div>
                </div>

                <div style="padding:12px 18px;border-top:1px solid #f5f5f0;">
                    <a href="#" style="display:block;text-align:center;font-size:0.75rem;font-weight:700;color:#14b8a6;text-decoration:none;letter-spacing:.04em;text-transform:uppercase;padding:8px;border-radius:8px;transition:background .12s;"
                       onmouseover="this.style.background='#f0fdf4'" onmouseout="this.style.background='transparent'">
                        Open Messenger →
                    </a>
                </div>
            </div>

            <!-- ── ACTIVITY FEED ── -->
            <div style="background:#fff;border-radius:18px;padding:20px;border:1.5px solid #f0f0ec;">
                <h3 style="font-size:1rem;font-weight:800;color:#1a1a1a;margin:0 0 14px;">Recent Activity</h3>
                <div style="display:flex;flex-direction:column;gap:10px;">
                    <div v-for="activity in displayActivity" :key="activity.id"
                         :style="{display:'flex',gap:'11px',padding:'13px',borderRadius:'13px',cursor:'pointer',background:activity.bg||'#f9fafb',transition:'filter .15s'}"
                         onmouseover="this.style.filter='brightness(0.97)'"
                         onmouseout="this.style.filter='brightness(1)'">
                        <div style="width:36px;height:36px;border-radius:50%;background:rgba(255,255,255,0.8);display:flex;align-items:center;justify-content:center;font-size:0.9rem;flex-shrink:0;border:1px solid rgba(0,0,0,0.06);">
                            {{ activity.icon }}
                        </div>
                        <div style="flex:1;min-width:0;">
                            <p style="font-size:0.8125rem;font-weight:700;color:#1a1a1a;margin:0 0 3px;line-height:1.3;">{{ activity.title }}</p>
                            <p style="font-size:0.73rem;color:#6b7280;margin:0 0 5px;line-height:1.4;">{{ activity.desc }}</p>
                            <p :style="{fontSize:'0.62rem',fontWeight:'800',color:activity.color||'#ED730C',letterSpacing:'.06em',margin:0}">{{ activity.time }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ── QUICK ACTIONS ── -->
            <div style="background:#fff;border-radius:18px;padding:18px;border:1.5px solid #f0f0ec;">
                <p style="font-size:0.68rem;font-weight:800;letter-spacing:.09em;text-transform:uppercase;color:#9ca3af;margin:0 0 12px;">Quick Actions</p>
                <div style="display:flex;flex-direction:column;gap:8px;">
                    <a href="#" style="display:flex;align-items:center;gap:10px;padding:12px 14px;background:#fff7ed;border-radius:12px;text-decoration:none;border:1.5px solid #fed7aa;transition:all .15s;"
                       onmouseover="this.style.background='#ffedd5'" onmouseout="this.style.background='#fff7ed'">
                        <svg width="16" height="16" fill="none" stroke="#ED730C" stroke-width="2" stroke-linecap="round" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                        <span style="font-size:0.8375rem;font-weight:700;color:#ED730C;">Post New Item</span>
                    </a>
                    <a href="/services" style="display:flex;align-items:center;gap:10px;padding:12px 14px;background:#f0fdf4;border-radius:12px;text-decoration:none;border:1.5px solid #bbf7d0;transition:all .15s;"
                       onmouseover="this.style.background='#dcfce7'" onmouseout="this.style.background='#f0fdf4'">
                        <svg width="16" height="16" fill="none" stroke="#14b8a6" stroke-width="2" stroke-linecap="round" viewBox="0 0 24 24"><path d="M8 7h12m0 0l-4-4m4 4l-4 4M16 17H4m0 0l4 4m-4-4l4-4"/></svg>
                        <span style="font-size:0.8375rem;font-weight:700;color:#14b8a6;">Offer a Service</span>
                    </a>
                    <a href="/garage-sale" style="display:flex;align-items:center;gap:10px;padding:12px 14px;background:#f5f5f0;border-radius:12px;text-decoration:none;border:1.5px solid #e5e7eb;transition:all .15s;"
                       onmouseover="this.style.background='#e5e7eb'" onmouseout="this.style.background='#f5f5f0'">
                        <svg width="16" height="16" fill="none" stroke="#6b7280" stroke-width="2" stroke-linecap="round" viewBox="0 0 24 24"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 01-8 0"/></svg>
                        <span style="font-size:0.8375rem;font-weight:700;color:#6b7280;">My Garage Sale</span>
                    </a>
                    <a href="#" style="display:flex;align-items:center;gap:10px;padding:12px 14px;background:#f5f5f0;border-radius:12px;text-decoration:none;border:1.5px solid #e5e7eb;transition:all .15s;"
                       onmouseover="this.style.background='#e5e7eb'" onmouseout="this.style.background='#f5f5f0'">
                        <svg width="16" height="16" fill="none" stroke="#6b7280" stroke-width="2" stroke-linecap="round" viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                        <span style="font-size:0.8375rem;font-weight:700;color:#6b7280;">My Store</span>
                    </a>
                </div>
            </div>

            <!-- ── SWAP SCORE ── -->
            <div style="background:linear-gradient(135deg,#1a0f0a,#2d1810);border-radius:18px;padding:20px;position:relative;overflow:hidden;">
                <div style="position:absolute;top:-20px;right:-20px;width:120px;height:120px;background:radial-gradient(circle,rgba(237,115,12,0.2) 0%,transparent 70%);pointer-events:none;"></div>
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;">
                    <p style="font-size:0.68rem;font-weight:800;letter-spacing:.08em;text-transform:uppercase;color:rgba(255,255,255,0.45);margin:0;">Swap Score</p>
                    <a href="#" style="font-size:0.68rem;color:rgba(255,255,255,0.35);text-decoration:none;"
                       onmouseover="this.style.color='rgba(255,255,255,0.7)'" onmouseout="this.style.color='rgba(255,255,255,0.35)'">View profile →</a>
                </div>
                <div style="display:flex;align-items:center;gap:14px;">
                    <div style="width:56px;height:56px;border-radius:50%;background:rgba(237,115,12,0.15);border:3px solid #ED730C;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <span style="font-size:1.125rem;font-weight:900;color:#ED730C;">{{ displayCredibility.score || '4.8' }}</span>
                    </div>
                    <div>
                        <p style="font-size:0.72rem;color:rgba(255,255,255,0.4);margin:0 0 5px;">
                            <strong style="color:#ED730C;">{{ displayCredibility.items_this_month || 3 }}</strong> swaps this month
                        </p>
                        <p style="font-size:0.72rem;color:rgba(255,255,255,0.4);margin:0;">
                            <strong style="color:#fff;">{{ displayCredibility.total_reviews || 28 }}</strong> reviews · <strong style="color:#14b8a6;">98%</strong> response
                        </p>
                    </div>
                </div>
            </div>

        </div><!-- end sidebar -->

    </div><!-- end main grid -->

</div>
</div>
</template>

<style>
.hide-scrollbar::-webkit-scrollbar { display: none; }
.hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

.match-card:hover, .feed-card:hover {
    border-color: #ED730C !important;
    box-shadow: 0 8px 28px rgba(237,115,12,0.1) !important;
    transform: translateY(-3px);
}
.match-card:hover .match-img,
.feed-card:hover .feed-img { transform: scale(1.05); }
.trend-card:hover .trend-img { transform: scale(1.06); }

@media (max-width: 1100px) {
    .main-grid  { grid-template-columns: 1fr !important; }
}
@media (max-width: 768px) {
    .stats-grid { grid-template-columns: repeat(2,1fr) !important; }
    .feed-grid  { grid-template-columns: 1fr !important; }
}
@media (max-width: 480px) {
    .stats-grid { grid-template-columns: 1fr !important; }
}
</style>