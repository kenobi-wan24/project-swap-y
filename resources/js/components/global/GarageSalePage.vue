<script setup>
import { ref, computed } from 'vue'

const el      = document.getElementById('garage-sale-app')
const sellers = ref(JSON.parse(el?.dataset.sellers || '[]'))

// ── filter state ──────────────────────────────────────────────────────────────
const search   = ref('')
const sortBy   = ref('Most Items')
const loading  = ref(false)

const sortOptions = ['Most Items', 'Nearest First', 'Recently Active', 'Highest Rated']

// ── filtered + sorted sellers ─────────────────────────────────────────────────
const filtered = computed(() => {
    let list = sellers.value
    if (search.value.trim())
        list = list.filter(s =>
            s.name.toLowerCase().includes(search.value.toLowerCase()) ||
            s.username.toLowerCase().includes(search.value.toLowerCase())
        )
    if (sortBy.value === 'Most Items')       list = [...list].sort((a,b) => b.item_count - a.item_count)
    if (sortBy.value === 'Nearest First')    list = [...list].sort((a,b) => parseFloat(a.distance) - parseFloat(b.distance))
    if (sortBy.value === 'Highest Rated')    list = [...list].sort((a,b) => b.rating - a.rating)
    return list
})

async function loadMore() {
    loading.value = true
    await new Promise(r => setTimeout(r, 700))
    loading.value = false
}
</script>

<template>
<div style="min-height:100vh;background:#f9fafb;font-family:'DM Sans',sans-serif;">

    <!-- ══ HERO ══ -->
    <div style="background:#fff;border-bottom:1px solid #f3f4f6;padding:40px 0 32px;">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div style="display:flex;align-items:flex-end;justify-content:space-between;flex-wrap:wrap;gap:16px;">
                <div>
                    <div style="display:inline-flex;align-items:center;gap:8px;background:#fff4ec;border-radius:999px;padding:5px 14px;margin-bottom:14px;">
                        <span style="font-size:1rem;">🏷️</span>
                        <span style="font-size:0.75rem;font-weight:700;color:#ED730C;letter-spacing:.06em;text-transform:uppercase;">Community Garage Sales</span>
                    </div>
                    <h1 style="font-size:2.5rem;font-weight:800;color:#1a1a1a;line-height:1.1;margin-bottom:10px;">Garage Sale</h1>
                    <p style="font-size:0.9375rem;color:#6b7280;max-width:480px;line-height:1.6;">
                        Browse personal collections from real people in your area. Click any seller to explore their full garage sale.
                    </p>
                </div>
                <div style="display:flex;align-items:center;gap:10px;background:#f9fafb;border:1px solid #e5e7eb;border-radius:12px;padding:6px 14px;">
                    <span style="font-size:0.78rem;font-weight:600;color:#6b7280;">Sort by</span>
                    <select v-model="sortBy"
                        style="font-size:0.82rem;font-weight:700;color:#1a1a1a;border:none;background:transparent;cursor:pointer;font-family:'DM Sans',sans-serif;outline:none;">
                        <option v-for="o in sortOptions" :key="o">{{ o }}</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- ══ SEARCH BAR ══ -->
    <div style="background:#fff;border-bottom:1px solid #f3f4f6;padding:14px 0;">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div style="position:relative;max-width:480px;">
                <svg style="position:absolute;left:14px;top:50%;transform:translateY(-50%);width:16px;height:16px;color:#9ca3af;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input v-model="search" type="text" placeholder="Search by seller name or username..."
                    style="width:100%;padding:11px 14px 11px 42px;border:1.5px solid #e5e7eb;border-radius:10px;font-size:0.875rem;color:#1a1a1a;background:#fff;outline:none;font-family:'DM Sans',sans-serif;box-sizing:border-box;transition:border-color .15s;"
                    @focus="$event.target.style.borderColor='#ED730C'"
                    @blur="$event.target.style.borderColor='#e5e7eb'"
                >
            </div>
        </div>
    </div>

    <!-- ══ SELLER GRID ══ -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" style="padding-top:32px;padding-bottom:72px;">

        <!-- Count -->
        <p style="font-size:0.82rem;color:#9ca3af;margin-bottom:20px;">
            Showing <strong style="color:#1a1a1a;">{{ filtered.length }}</strong> garage sales near you
        </p>

        <!-- Grid -->
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:22px;">
            <a
                v-for="seller in filtered"
                :key="seller.username"
                :href="'/store/' + seller.username"
                style="text-decoration:none;display:block;background:#fff;border-radius:18px;overflow:hidden;border:1px solid #f3f4f6;transition:border-color 0.2s,box-shadow 0.2s,transform 0.2s;cursor:pointer;"
                onmouseover="this.style.borderColor='#e5e7eb';this.style.boxShadow='0 10px 36px rgba(0,0,0,0.09)';this.style.transform='translateY(-2px)'"
                onmouseout="this.style.borderColor='#f3f4f6';this.style.boxShadow='none';this.style.transform='translateY(0)'"
            >
                <!-- Cover image -->
                <div style="position:relative;height:160px;overflow:hidden;background:#f3f4f6;">
                    <img
                        :src="seller.cover || 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=600&q=80'"
                        :alt="seller.name"
                        style="width:100%;height:100%;object-fit:cover;transition:transform 0.4s;"
                        onmouseover="this.style.transform='scale(1.04)'"
                        onmouseout="this.style.transform='scale(1)'"
                    >
                    <!-- Dark overlay -->
                    <div style="position:absolute;inset:0;background:linear-gradient(to top,rgba(0,0,0,0.5) 0%,transparent 60%);"></div>

                    <!-- Item count badge -->
                    <span style="position:absolute;top:12px;right:12px;background:rgba(0,0,0,0.55);color:#fff;font-size:0.72rem;font-weight:700;padding:4px 10px;border-radius:999px;backdrop-filter:blur(4px);">
                        {{ seller.item_count }} items
                    </span>

                    <!-- Category tags bottom-left -->
                    <div style="position:absolute;bottom:10px;left:12px;display:flex;gap:5px;flex-wrap:wrap;">
                        <span
                            v-for="cat in (seller.categories || []).slice(0,3)"
                            :key="cat"
                            style="background:rgba(255,255,255,0.18);backdrop-filter:blur(6px);border:1px solid rgba(255,255,255,0.3);color:#fff;font-size:0.65rem;font-weight:700;padding:3px 9px;border-radius:999px;letter-spacing:.04em;"
                        >{{ cat }}</span>
                    </div>
                </div>

                <!-- Seller info -->
                <div style="padding:16px 18px 18px;">
                    <div style="display:flex;align-items:center;gap:12px;margin-bottom:12px;">
                        <!-- Avatar -->
                        <div style="position:relative;flex-shrink:0;">
                            <img
                                :src="seller.avatar || 'https://i.pravatar.cc/48?u=' + seller.username"
                                :alt="seller.name"
                                style="width:46px;height:46px;border-radius:50%;object-fit:cover;border:2.5px solid #fff;box-shadow:0 2px 8px rgba(0,0,0,0.12);"
                            >
                            <span style="position:absolute;bottom:0;right:0;width:13px;height:13px;background:#22c55e;border-radius:50%;border:2px solid #fff;"></span>
                        </div>
                        <div style="min-width:0;">
                            <div style="font-weight:700;font-size:0.95rem;color:#1a1a1a;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                                {{ seller.name }}'s Garage Sale
                            </div>
                            <div style="font-size:0.78rem;color:#9ca3af;">@{{ seller.username }}</div>
                        </div>
                    </div>

                    <!-- Stats row -->
                    <div style="display:flex;align-items:center;gap:16px;margin-bottom:14px;padding-bottom:12px;border-bottom:1px solid #f3f4f6;">
                        <div style="display:flex;align-items:center;gap:4px;">
                            <svg width="13" height="13" fill="#f59e0b" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            <span style="font-size:0.8rem;font-weight:700;color:#1a1a1a;">{{ seller.rating || '4.8' }}</span>
                        </div>
                        <div style="display:flex;align-items:center;gap:4px;color:#9ca3af;">
                            <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <span style="font-size:0.78rem;">{{ seller.distance || '—' }} mi away</span>
                        </div>
                        <div style="margin-left:auto;font-size:0.75rem;color:#9ca3af;">
                            {{ seller.active_since || 'Active recently' }}
                        </div>
                    </div>

                    <!-- CTA -->
                    <div style="display:flex;align-items:center;justify-content:space-between;">
                        <span style="font-size:0.8rem;color:#6b7280;">
                            <strong style="color:#ED730C;">{{ seller.item_count }}</strong> items for swap
                        </span>
                        <span style="display:inline-flex;align-items:center;gap:5px;font-size:0.82rem;font-weight:700;color:#ED730C;">
                            Visit Sale
                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                        </span>
                    </div>
                </div>
            </a>
        </div>

        <!-- Empty state -->
        <div v-if="filtered.length === 0" style="text-align:center;padding:80px 0;">
            <div style="font-size:3.5rem;margin-bottom:16px;">🏠</div>
            <h3 style="font-size:1.125rem;font-weight:700;color:#1a1a1a;margin-bottom:8px;">No garage sales found</h3>
            <p style="font-size:0.875rem;color:#9ca3af;margin-bottom:20px;">Try a different search term.</p>
            <button @click="search=''"
                style="font-size:0.82rem;font-weight:700;color:#ED730C;background:none;border:1.5px solid #ED730C;border-radius:999px;padding:10px 24px;cursor:pointer;font-family:'DM Sans',sans-serif;">
                Clear search
            </button>
        </div>

        <!-- Load More -->
        <div v-if="filtered.length > 0" style="text-align:center;margin-top:52px;">
            <button @click="loadMore" :disabled="loading"
                style="display:inline-flex;align-items:center;gap:8px;background:#f3f4f6;border:none;border-radius:999px;padding:13px 32px;font-size:0.875rem;font-weight:600;color:#1a1a1a;cursor:pointer;font-family:'DM Sans',sans-serif;transition:background .15s;"
                onmouseover="this.style.background='#e5e7eb'" onmouseout="this.style.background='#f3f4f6'"
            >
                <span v-if="!loading">Load More Sellers</span>
                <span v-else>Loading...</span>
                <svg v-if="!loading" style="width:15px;height:15px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
        </div>

    </div>
</div>
</template>