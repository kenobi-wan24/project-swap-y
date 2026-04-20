<script setup>
import { ref, computed } from 'vue'

const el       = document.getElementById('browse-app')
const allItems = ref(JSON.parse(el?.dataset.listings || '[]'))

const search    = ref('')
const activeTab = ref('All')
const valueMax  = ref(500)
const distance  = ref(5)
const itemType  = ref('Physical Good')
const sortBy    = ref('Recent First')
const loading   = ref(false)
const viewMode  = ref('grid')
const skeletonLoading = ref(false)

const categories  = ['All', 'Electronics', 'Clothing', 'Collectibles', 'Furniture', 'Books', 'Outdoor']
const sortOptions = ['Recent First', 'Value: Low to High', 'Value: High to Low', 'Distance: Nearest']

const topFeatured = ref([
    { id: 'f1', title: 'iPhone 14 Pro Max 256GB', value: 900, condition: 'Excellent condition, unlocked, box & accessories', location: 'New York, NY', rating: 4.9, badge: 'Hot Swap', badgeColor: '#ED730C', wants: 'Gaming Laptop', image: 'https://images.unsplash.com/photo-1591337676887-a217a6970a8a?w=600&q=80' },
    { id: 'f2', title: 'ASUS ROG Gaming Laptop', value: 1200, condition: 'RTX 3070, 16GB RAM, 1TB SSD', location: 'Los Angeles, CA', rating: 5.0, badge: 'Trending', badgeColor: '#8b5cf6', wants: 'iPhone, MacBook', image: 'https://images.unsplash.com/photo-1603302576837-37561b2e2302?w=600&q=80' },
    { id: 'f3', title: '2019 Honda Civic', value: 15000, condition: 'Low mileage, one owner, clean title', location: 'Miami, FL', rating: 4.8, badge: 'Verified', badgeColor: '#6b7280', wants: 'Truck, SUV', image: 'https://images.unsplash.com/photo-1533473359331-0135ef1b58bf?w=600&q=80' },
])

const todaysPicks = ref([
    { id: 't1', title: 'Sony WH-1000XM4', value: 280, location: 'Seattle, WA', image: 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=400&q=80' },
    { id: 't2', title: 'Apple Watch Series 8', value: 340, location: 'Portland, OR', image: 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=400&q=80' },
    { id: 't3', title: 'Ray-Ban Aviators', value: 120, location: 'San Francisco, CA', image: 'https://images.unsplash.com/photo-1572635196237-14b3f281503f?w=400&q=80' },
    { id: 't4', title: 'Canon EOS R6', value: 1899, location: 'Austin, TX', image: 'https://images.unsplash.com/photo-1516035069371-29a1b244cc32?w=400&q=80' },
    { id: 't5', title: 'Nike Air Max 270', value: 95, location: 'Miami, FL', image: 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=400&q=80' },
    { id: 't6', title: 'iPad Pro 12.9"', value: 750, location: 'Chicago, IL', image: 'https://images.unsplash.com/photo-1544244015-0df4b3ffc6b0?w=400&q=80' },
])

const nearYou = ref([
    { id: 'n1', title: 'Nike Air Max 270', value: 120, distance: '0.3 mi', image: 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=400&q=80' },
    { id: 'n2', title: 'Sectional Sofa', value: 800, distance: '0.8 mi', image: 'https://images.unsplash.com/photo-1555041469-a586c61ea9bc?w=400&q=80' },
    { id: 'n3', title: 'Fossil Smartwatch', value: 300, distance: '1.2 mi', image: 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=400&q=80' },
    { id: 'n4', title: 'Vinyl Records (50+)', value: 200, distance: '1.5 mi', image: 'https://images.unsplash.com/photo-1603048588665-791ca8aea617?w=400&q=80' },
    { id: 'n5', title: 'Trek Mountain Bike', value: 450, distance: '1.9 mi', image: 'https://images.unsplash.com/photo-1485965120184-e220f721d03e?w=400&q=80' },
    { id: 'n6', title: 'Sony WH-1000XM5', value: 250, distance: '2.1 mi', image: 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=400&q=80' },
])

const filtered = computed(() => {
    let list = allItems.value
    if (activeTab.value !== 'All') list = list.filter(i => i.category === activeTab.value)
    if (search.value.trim()) list = list.filter(i => i.title.toLowerCase().includes(search.value.toLowerCase()))
    list = list.filter(i => i.value <= valueMax.value)
    return list
})

const wishlisted = ref(new Set())
function toggleWish(id) {
    if (wishlisted.value.has(id)) wishlisted.value.delete(id)
    else wishlisted.value.add(id)
    wishlisted.value = new Set(wishlisted.value)
}

function renderStars(rating) { return Math.round(rating) }

function avatarColor(username) {
    const colors = ['#3b82f6','#8b5cf6','#14b8a6','#ef4444','#f59e0b','#10b981','#ED730C']
    return colors[(username?.charCodeAt(0) || 0) % colors.length]
}

async function loadMore() {
    skeletonLoading.value = true
    await new Promise(r => setTimeout(r, 900))
    skeletonLoading.value = false
}
</script>

<template>
<div style="min-height:100vh;background:#f3f4f6;">

    <!-- ══ SEARCH BAR ══ -->
    <div style="background:#fff;border-bottom:1px solid #f3f4f6;padding:18px 0;">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div style="position:relative;">
                <svg style="position:absolute;left:16px;top:50%;transform:translateY(-50%);width:18px;height:18px;color:#9ca3af;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input v-model="search" type="text"
                    placeholder="Search for unique items, vintage collectibles, or artisan services..."
                    style="width:100%;padding:14px 16px 14px 46px;border:1.5px solid #e5e7eb;border-radius:12px;font-size:0.9rem;color:#3A3330;background:#fff;outline:none;font-family:'DM Sans',sans-serif;box-sizing:border-box;transition:border-color 0.15s;"
                    @focus="$event.target.style.borderColor='#ED730C'"
                    @blur="$event.target.style.borderColor='#e5e7eb'">
            </div>
        </div>
    </div>

    <!-- ══ FILTER BAR ══ -->
    <div style="background:#fff;border-bottom:1px solid #f3f4f6;padding:12px 0;">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div style="display:flex;flex-wrap:wrap;align-items:center;gap:16px;justify-content:space-between;">
                <div style="display:flex;flex-wrap:wrap;gap:12px;align-items:center;">
                    <div style="display:flex;align-items:center;gap:6px;">
                        <span style="font-size:0.7rem;font-weight:700;color:#9ca3af;letter-spacing:.06em;text-transform:uppercase;">Category</span>
                        <select v-model="activeTab" style="font-size:0.8rem;font-weight:600;color:#3A3330;border:none;background:transparent;cursor:pointer;font-family:'DM Sans',sans-serif;padding:2px 4px;">
                            <option v-for="c in categories" :key="c" :value="c">{{ c === 'All' ? 'All Categories' : c }}</option>
                        </select>
                    </div>
                    <div style="width:1px;height:20px;background:#e5e7eb;"></div>
                    <div style="display:flex;align-items:center;gap:6px;">
                        <span style="font-size:0.7rem;font-weight:700;color:#9ca3af;letter-spacing:.06em;text-transform:uppercase;">Value Range</span>
                        <span style="font-size:0.8rem;font-weight:600;color:#3A3330;">$0 — ${{ valueMax }}</span>
                        <input type="range" v-model.number="valueMax" min="50" max="5000" step="50" style="width:80px;accent-color:#ED730C;">
                    </div>
                    <div style="width:1px;height:20px;background:#e5e7eb;"></div>
                    <div style="display:flex;align-items:center;gap:6px;">
                        <span style="font-size:0.7rem;font-weight:700;color:#9ca3af;letter-spacing:.06em;text-transform:uppercase;">Distance</span>
                        <span style="font-size:0.8rem;font-weight:600;color:#3A3330;">{{ distance }} Miles</span>
                        <input type="range" v-model.number="distance" min="1" max="100" step="1" style="width:80px;accent-color:#ED730C;">
                    </div>
                    <div style="width:1px;height:20px;background:#e5e7eb;"></div>
                    <div style="display:flex;align-items:center;gap:6px;">
                        <span style="font-size:0.7rem;font-weight:700;color:#9ca3af;letter-spacing:.06em;text-transform:uppercase;">Item Type</span>
                        <select v-model="itemType" style="font-size:0.8rem;font-weight:600;color:#3A3330;border:none;background:transparent;cursor:pointer;font-family:'DM Sans',sans-serif;padding:2px 4px;">
                            <option>Physical Good</option><option>Service</option><option>Real Estate</option>
                        </select>
                    </div>
                </div>
                <div style="display:flex;align-items:center;gap:6px;">
                    <span style="font-size:0.7rem;font-weight:700;color:#9ca3af;letter-spacing:.06em;text-transform:uppercase;">Sort by:</span>
                    <select v-model="sortBy" style="font-size:0.8rem;font-weight:600;color:#3A3330;border:none;background:transparent;cursor:pointer;font-family:'DM Sans',sans-serif;padding:2px 4px;">
                        <option v-for="s in sortOptions" :key="s">{{ s }}</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- ══ CATEGORY PILLS ══ -->
    <div style="background:#fff;padding:14px 0;border-bottom:1px solid #f3f4f6;">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div style="display:flex;gap:8px;flex-wrap:wrap;">
                <button v-for="cat in categories" :key="cat" @click="activeTab = cat"
                    :style="{padding:'7px 18px',borderRadius:'999px',fontSize:'0.8rem',fontWeight:'600',fontFamily:'\'DM Sans\',sans-serif',cursor:'pointer',border:activeTab===cat?'1.5px solid #ED730C':'1.5px solid #e5e7eb',background:activeTab===cat?'#ED730C':'#fff',color:activeTab===cat?'#fff':'#4b5563',transition:'all 0.15s'}">
                    {{ cat }}
                </button>
            </div>
        </div>
    </div>

    <!-- ══ TOP FEATURED ══ -->
    <div style="background:#fff;padding:32px 0 36px;border-bottom:1px solid #f3f4f6;">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;">
                <div style="display:flex;align-items:center;gap:12px;">
                    <h2 style="font-size:1.25rem;font-weight:800;color:#3A3330;">Top Featured</h2>
                    <span style="background:#ED730C;color:#fff;font-size:0.65rem;font-weight:800;padding:3px 10px;border-radius:999px;letter-spacing:.06em;text-transform:uppercase;">Curated</span>
                </div>
                <a href="#" style="font-size:0.875rem;font-weight:700;color:#ED730C;text-decoration:none;">See all →</a>
            </div>
            <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:20px;">
                <div v-for="item in topFeatured" :key="item.id" class="swap-card"
                    style="background:#fff;border-radius:18px;overflow:hidden;border:1.5px solid #f3f4f6;cursor:pointer;">
                    <div style="position:relative;aspect-ratio:4/3;overflow:hidden;background:#f3f4f6;">
                        <img :src="item.image" :alt="item.title" class="card-img" style="width:100%;height:100%;object-fit:cover;transition:transform 0.4s;">
                        <span :style="{position:'absolute',top:'12px',left:'12px',background:item.badgeColor,color:'#fff',fontSize:'0.7rem',fontWeight:'800',padding:'5px 12px',borderRadius:'999px'}">{{ item.badge }}</span>
                        <button @click.stop="toggleWish(item.id)" class="wish-btn"
                            style="position:absolute;top:10px;right:10px;width:34px;height:34px;background:rgba(255,255,255,0.92);border:none;border-radius:50%;display:flex;align-items:center;justify-content:center;cursor:pointer;box-shadow:0 2px 8px rgba(0,0,0,0.12);">
                            <svg :style="{width:'16px',height:'16px',fill:wishlisted.has(item.id)?'#ED730C':'none',stroke:wishlisted.has(item.id)?'#ED730C':'#6b7280',strokeWidth:'2'}" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                            </svg>
                        </button>
                    </div>
                    <div style="padding:16px 18px 18px;">
                        <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:6px;">
                            <h3 style="font-size:1rem;font-weight:700;color:#1a1a1a;line-height:1.3;flex:1;">{{ item.title }}</h3>
                            <span style="font-size:1.05rem;font-weight:800;color:#ED730C;white-space:nowrap;margin-left:10px;">${{ item.value.toLocaleString() }}</span>
                        </div>
                        <p style="font-size:0.8rem;color:#9ca3af;margin-bottom:10px;line-height:1.5;font-weight:400;">{{ item.condition }}</p>
                        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px;">
                            <span style="font-size:0.78rem;color:#9ca3af;display:inline-flex;align-items:center;gap:4px;">
                                <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                {{ item.location }}
                            </span>
                            <span style="display:inline-flex;align-items:center;gap:2px;">
                                <span v-for="s in 5" :key="s" :style="{color:s<=renderStars(item.rating)?'#f59e0b':'#e5e7eb',fontSize:'0.75rem'}">★</span>
                                <span style="font-size:0.72rem;color:#6b7280;margin-left:2px;font-weight:600;">{{ item.rating }}</span>
                            </span>
                        </div>
                        <div style="border-top:1px solid #f3f4f6;padding-top:10px;font-size:0.8rem;color:#9ca3af;">
                            Wants: <strong style="color:#3A3330;font-weight:700;">{{ item.wants }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ══ TODAY'S PICKS ══ -->
    <div style="background:#fff;padding:32px 0 36px;border-bottom:1px solid #f3f4f6;">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:6px;">
                <div style="display:flex;align-items:center;gap:12px;">
                    <h2 style="font-size:1.25rem;font-weight:800;color:#3A3330;">Today's Picks</h2>
                    <span style="background:#14b8a6;color:#fff;font-size:0.65rem;font-weight:800;padding:3px 10px;border-radius:999px;letter-spacing:.06em;text-transform:uppercase;">Daily</span>
                </div>
                <a href="#" style="font-size:0.875rem;font-weight:700;color:#ED730C;text-decoration:none;">See all →</a>
            </div>
            <p style="font-size:0.8rem;color:#9ca3af;margin-bottom:20px;">Curated selections updated daily</p>
            <div class="hscroll" style="display:flex;gap:16px;overflow-x:auto;padding-bottom:4px;">
                <div v-for="item in todaysPicks" :key="item.id" class="swap-card"
                    style="flex-shrink:0;width:196px;background:#fff;border-radius:16px;overflow:hidden;border:1.5px solid #f3f4f6;cursor:pointer;">
                    <div style="position:relative;aspect-ratio:1/1;overflow:hidden;background:#f3f4f6;">
                        <img :src="item.image" :alt="item.title" class="card-img" style="width:100%;height:100%;object-fit:cover;transition:transform 0.35s;">
                        <span style="position:absolute;bottom:8px;left:8px;background:rgba(0,0,0,0.55);color:#fff;font-size:0.6rem;font-weight:700;padding:3px 8px;border-radius:6px;backdrop-filter:blur(4px);">Today's Pick</span>
                        <button @click.stop="toggleWish(item.id)" class="wish-btn"
                            style="position:absolute;top:8px;right:8px;width:30px;height:30px;background:rgba(255,255,255,0.92);border:none;border-radius:50%;display:flex;align-items:center;justify-content:center;cursor:pointer;box-shadow:0 2px 6px rgba(0,0,0,0.1);">
                            <svg :style="{width:'14px',height:'14px',fill:wishlisted.has(item.id)?'#ED730C':'none',stroke:wishlisted.has(item.id)?'#ED730C':'#6b7280',strokeWidth:'2'}" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                            </svg>
                        </button>
                    </div>
                    <div style="padding:12px 14px 14px;">
                        <p style="font-size:0.875rem;font-weight:700;color:#1a1a1a;margin-bottom:4px;line-height:1.3;">{{ item.title }}</p>
                        <p style="font-size:1rem;font-weight:800;color:#ED730C;margin-bottom:4px;">${{ item.value }}</p>
                        <p style="font-size:0.72rem;color:#9ca3af;display:inline-flex;align-items:center;gap:3px;">
                            <svg width="10" height="10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                            {{ item.location }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ══ POPULAR NEAR YOU ══ -->
    <div style="background:#fff;padding:32px 0 36px;border-bottom:1px solid #f3f4f6;">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;">
                <div style="display:flex;align-items:center;gap:12px;">
                    <h2 style="font-size:1.25rem;font-weight:800;color:#3A3330;display:inline-flex;align-items:center;gap:6px;">
                        <svg width="16" height="16" fill="#ED730C" viewBox="0 0 24 24"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>
                        Popular Near You
                    </h2>
                    <span style="background:#f59e0b;color:#fff;font-size:0.65rem;font-weight:800;padding:3px 10px;border-radius:999px;letter-spacing:.06em;text-transform:uppercase;">Local</span>
                </div>
                <a href="#" style="font-size:0.875rem;font-weight:700;color:#ED730C;text-decoration:none;">See all →</a>
            </div>
            <div class="hscroll" style="display:flex;gap:14px;overflow-x:auto;padding-bottom:4px;">
                <div v-for="item in nearYou" :key="item.id" class="swap-card"
                    style="flex-shrink:0;width:178px;background:#fff;border-radius:16px;overflow:hidden;border:1.5px solid #f3f4f6;cursor:pointer;">
                    <div style="position:relative;aspect-ratio:1/1;overflow:hidden;background:#f3f4f6;">
                        <img :src="item.image" :alt="item.title" class="card-img" style="width:100%;height:100%;object-fit:cover;transition:transform 0.35s;">
                        <span style="position:absolute;bottom:8px;left:8px;background:rgba(0,0,0,0.6);color:#fff;font-size:0.65rem;font-weight:700;padding:4px 9px;border-radius:6px;backdrop-filter:blur(4px);display:inline-flex;align-items:center;gap:4px;">
                            <svg width="9" height="9" fill="#fff" viewBox="0 0 24 24"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z"/></svg>
                            {{ item.distance }}
                        </span>
                        <button @click.stop="toggleWish(item.id)" class="wish-btn"
                            style="position:absolute;top:8px;right:8px;width:30px;height:30px;background:rgba(255,255,255,0.92);border:none;border-radius:50%;display:flex;align-items:center;justify-content:center;cursor:pointer;box-shadow:0 2px 6px rgba(0,0,0,0.1);">
                            <svg :style="{width:'14px',height:'14px',fill:wishlisted.has(item.id)?'#ED730C':'none',stroke:wishlisted.has(item.id)?'#ED730C':'#6b7280',strokeWidth:'2'}" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                            </svg>
                        </button>
                    </div>
                    <div style="padding:12px 14px 14px;">
                        <p style="font-size:0.875rem;font-weight:700;color:#1a1a1a;margin-bottom:4px;line-height:1.3;">{{ item.title }}</p>
                        <p style="font-size:1rem;font-weight:800;color:#ED730C;">${{ item.value }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ══ MAIN GRID ══ -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" style="padding-top:32px;padding-bottom:56px;">

        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
            <h2 style="font-size:1rem;font-weight:800;color:#3A3330;">{{ filtered.length.toLocaleString() }} items available</h2>
            <div style="display:inline-flex;border:1px solid #e5e7eb;border-radius:8px;overflow:hidden;">
                <button @click="viewMode='grid'" :style="{padding:'7px 12px',background:viewMode==='grid'?'#f3f4f6':'#fff',border:'none',cursor:'pointer',display:'flex',alignItems:'center'}">
                    <svg width="16" height="16" fill="none" :stroke="viewMode==='grid'?'#ED730C':'#9ca3af'" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1" stroke-width="2"/><rect x="14" y="3" width="7" height="7" rx="1" stroke-width="2"/><rect x="3" y="14" width="7" height="7" rx="1" stroke-width="2"/><rect x="14" y="14" width="7" height="7" rx="1" stroke-width="2"/></svg>
                </button>
                <button @click="viewMode='list'" :style="{padding:'7px 12px',background:viewMode==='list'?'#f3f4f6':'#fff',border:'none',borderLeft:'1px solid #e5e7eb',cursor:'pointer',display:'flex',alignItems:'center'}">
                    <svg width="16" height="16" fill="none" :stroke="viewMode==='list'?'#ED730C':'#9ca3af'" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
            </div>
        </div>

        <!-- Skeleton loaders -->
        <div v-if="skeletonLoading" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:20px;">
            <div v-for="n in 6" :key="n" style="background:#fff;border-radius:18px;overflow:hidden;border:1.5px solid #f3f4f6;">
                <div class="skeleton" style="aspect-ratio:4/3;width:100%;"></div>
                <div style="padding:16px 18px 18px;">
                    <div class="skeleton" style="height:16px;width:70%;border-radius:6px;margin-bottom:10px;"></div>
                    <div class="skeleton" style="height:12px;width:90%;border-radius:6px;margin-bottom:8px;"></div>
                    <div class="skeleton" style="height:12px;width:50%;border-radius:6px;margin-bottom:16px;"></div>
                    <div class="skeleton" style="height:40px;width:100%;border-radius:999px;"></div>
                </div>
            </div>
        </div>

        <!-- Grid view — Image 1 card style -->
        <div v-else-if="viewMode === 'grid'" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:20px;">
            <div v-for="item in filtered" :key="item.id" class="swap-card"
                style="background:#fff;border-radius:18px;overflow:hidden;border:1.5px solid #f3f4f6;cursor:pointer;">

                <!-- 4:3 locked image -->
                <div style="position:relative;aspect-ratio:4/3;overflow:hidden;background:#f3f4f6;">
                    <img :src="item.image" :alt="item.title" class="card-img" style="width:100%;height:100%;object-fit:cover;transition:transform 0.35s;">
                    <span v-if="item.promoted" style="position:absolute;top:10px;left:10px;background:#ED730C;color:#fff;font-size:0.65rem;font-weight:800;padding:4px 10px;border-radius:999px;letter-spacing:.04em;text-transform:uppercase;">Promoted</span>
                    <span style="position:absolute;top:10px;right:10px;background:#149189;color:#fff;font-size:0.7rem;font-weight:700;padding:4px 10px;border-radius:6px;">${{ item.value }} Value</span>
                    <!-- Distance overlaid on image -->
                    <span v-if="item.distance" style="position:absolute;bottom:10px;left:10px;background:rgba(0,0,0,0.55);color:#fff;font-size:0.65rem;font-weight:700;padding:3px 8px;border-radius:6px;backdrop-filter:blur(4px);display:inline-flex;align-items:center;gap:3px;">
                        <svg width="9" height="9" fill="#fff" viewBox="0 0 24 24"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z"/></svg>
                        {{ item.distance }} mi
                    </span>
                </div>

                <!-- Card body: Image 1 layout -->
                <div style="padding:14px 16px 16px;">
                    <h3 style="font-size:0.9375rem;font-weight:700;color:#1a1a1a;margin-bottom:10px;line-height:1.35;">{{ item.title }}</h3>

                    <!-- Owner row: avatar + username left, distance/rating right -->
                    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;">
                        <div style="display:flex;align-items:center;gap:7px;">
                            <div :style="{width:'24px',height:'24px',borderRadius:'50%',background:avatarColor(item.username),color:'#fff',fontSize:'0.58rem',fontWeight:'700',display:'flex',alignItems:'center',justifyContent:'center',flexShrink:0}">
                                {{ item.username?.slice(0,2).toUpperCase() }}
                            </div>
                            <span style="font-size:0.78rem;color:#6b7280;font-weight:500;">@{{ item.username }}</span>
                        </div>
                        <!-- Stars if rating, else distance text -->
                        <span v-if="item.rating" style="display:inline-flex;align-items:center;gap:2px;">
                            <span v-for="s in 5" :key="s" :style="{color:s<=renderStars(item.rating)?'#f59e0b':'#e5e7eb',fontSize:'0.68rem'}">★</span>
                            <span style="font-size:0.72rem;color:#6b7280;margin-left:2px;font-weight:600;">{{ item.rating }}</span>
                        </span>
                        <span v-else style="font-size:0.72rem;color:#9ca3af;font-weight:500;letter-spacing:.02em;">{{ item.distance }} MILES AWAY</span>
                    </div>

                    <!-- SWAP NOW button + heart icon — exact Image 1 layout -->
                    <div style="display:flex;align-items:center;gap:10px;">
                        <a :href="'/item/' + item.id" class="swap-btn"
                            style="flex:1;display:block;text-align:center;background:#ED730C;color:#fff;font-size:0.8rem;font-weight:800;padding:11px 0;border-radius:999px;text-decoration:none;transition:background 0.15s,transform 0.15s;letter-spacing:.05em;text-transform:uppercase;font-family:'DM Sans',sans-serif;">
                            SWAP NOW
                        </a>
                        <button @click.stop="toggleWish(item.id)"
                            :style="{width:'38px',height:'38px',flexShrink:0,background:wishlisted.has(item.id)?'#fff4ec':'#f3f4f6',border:'none',borderRadius:'50%',display:'flex',alignItems:'center',justifyContent:'center',cursor:'pointer',transition:'background 0.15s'}">
                            <svg :style="{width:'16px',height:'16px',fill:wishlisted.has(item.id)?'#ED730C':'none',stroke:wishlisted.has(item.id)?'#ED730C':'#9ca3af',strokeWidth:'2',transition:'all 0.15s'}" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- List view -->
        <div v-else style="display:flex;flex-direction:column;gap:12px;">
            <div v-for="item in filtered" :key="item.id" class="swap-card"
                style="background:#fff;border-radius:14px;overflow:hidden;border:1.5px solid #f3f4f6;display:flex;align-items:center;cursor:pointer;">
                <div style="width:110px;height:82px;flex-shrink:0;overflow:hidden;">
                    <img :src="item.image" :alt="item.title" style="width:100%;height:100%;object-fit:cover;">
                </div>
                <div style="flex:1;padding:12px 16px;display:flex;justify-content:space-between;align-items:center;gap:12px;">
                    <div>
                        <h3 style="font-size:0.9rem;font-weight:700;color:#1a1a1a;margin-bottom:3px;">{{ item.title }}</h3>
                        <p v-if="item.wants" style="font-size:0.76rem;color:#9ca3af;">Wants: <strong style="color:#6b7280;">{{ item.wants }}</strong></p>
                        <p style="font-size:0.72rem;color:#9ca3af;margin-top:2px;">{{ item.distance }} miles away</p>
                    </div>
                    <div style="text-align:right;flex-shrink:0;">
                        <p style="font-size:1.05rem;font-weight:800;color:#ED730C;margin-bottom:6px;">${{ item.value }}</p>
                        <button @click.stop="toggleWish(item.id)"
                            :style="{background:wishlisted.has(item.id)?'#fff4ec':'#f3f4f6',border:'none',borderRadius:'999px',padding:'5px 14px',fontSize:'0.75rem',fontWeight:'600',cursor:'pointer',color:wishlisted.has(item.id)?'#ED730C':'#6b7280',transition:'all 0.15s'}">
                            {{ wishlisted.has(item.id) ? '♥ Saved' : '♡ Save' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Empty state -->
        <div v-if="filtered.length === 0 && !skeletonLoading" style="text-align:center;padding:80px 0;">
            <div style="font-size:3rem;margin-bottom:16px;">🔍</div>
            <h3 style="font-size:1.125rem;font-weight:600;color:#3A3330;margin-bottom:8px;">No items found</h3>
            <p style="font-size:0.875rem;color:#9ca3af;">Try adjusting your filters or search term.</p>
        </div>

        <!-- Load more -->
        <div v-if="filtered.length > 0" style="text-align:center;margin-top:40px;">
            <button @click="loadMore" :disabled="skeletonLoading"
                style="font-size:0.8125rem;font-weight:700;color:#ED730C;background:transparent;border:1.5px solid #ED730C;cursor:pointer;letter-spacing:.06em;text-transform:uppercase;font-family:'DM Sans',sans-serif;display:inline-flex;align-items:center;gap:8px;padding:12px 28px;border-radius:999px;transition:background 0.15s;"
                onmouseover="this.style.background='#fff4ec'"
                onmouseout="this.style.background='transparent'">
                Load More Discoveries
            </button>
            <p style="margin-top:12px;font-size:0.8rem;color:#9ca3af;">Showing {{ filtered.length }} of 2,492 available swaps in your area</p>
        </div>

    </div>
</div>
</template>

<style scoped>
@keyframes shimmer {
    0%   { background-position: -400px 0; }
    100% { background-position:  400px 0; }
}
.skeleton {
    background: linear-gradient(90deg, #f0f0f0 25%, #e8e8e8 50%, #f0f0f0 75%);
    background-size: 800px 100%;
    animation: shimmer 1.4s ease-in-out infinite;
}

/* Orange border glow on hover */
.swap-card {
    transition: border-color 0.2s, box-shadow 0.2s;
}
.swap-card:hover {
    border-color: #ED730C !important;
    box-shadow: 0 8px 32px rgba(237,115,12,0.13) !important;
}

/* Image zoom on card hover */
.swap-card:hover .card-img {
    transform: scale(1.05);
}

/* Wishlist bounce */
.wish-btn {
    transition: transform 0.15s;
}
.wish-btn:hover {
    transform: scale(1.18);
}

/* Swap now lift */
.swap-btn:hover {
    background: #d4620a !important;
    transform: translateY(-1px);
}

/* Hide scrollbars on horizontal sections */
.hscroll::-webkit-scrollbar { display: none; }
.hscroll { scrollbar-width: none; }
</style>
