<script setup>
import { ref, computed } from 'vue'

// ── seed data from Laravel (via data attribute) ───────────────────────────────
const el   = document.getElementById('item-detail-app')
const item = ref(JSON.parse(el?.dataset.item || '{}'))
const isGuest = ref((el?.dataset.guest || 'true') === 'true')

// ── static similar items (replace with API when ready) ───────────────────────
const similarItems = ref([
    { id: 's1', title: 'Razer Blade 15 Advanced', value: 1900, distance: '15.2', owner: 'Alex R.', image: 'https://images.unsplash.com/photo-1593642632559-0c6d3fc62b89?w=600&q=80', avatar: 'https://i.pravatar.cc/32?img=11' },
    { id: 's2', title: 'MacBook Air M2 (Midnight)', value: 1100, distance: '8.1', owner: 'Sarah J.', image: 'https://images.unsplash.com/photo-1611186871348-b1ce696e52c9?w=600&q=80', avatar: 'https://i.pravatar.cc/32?img=5' },
    { id: 's3', title: 'Custom Gaming Rig (RTX 3080)', value: 2100, distance: '21.5', owner: 'David K.', image: 'https://images.unsplash.com/photo-1587202372583-49330a15584d?w=600&q=80', avatar: 'https://i.pravatar.cc/32?img=7' },
])

// ── image gallery ─────────────────────────────────────────────────────────────
const images = computed(() => item.value.images || [
    item.value.image || 'https://images.unsplash.com/photo-1611186871348-b1ce696e52c9?w=800&q=80',
    'https://images.unsplash.com/photo-1541807084-5c52b6b3adef?w=400&q=80',
    'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?w=400&q=80',
    'https://images.unsplash.com/photo-1496181133206-80ce9b88a853?w=400&q=80',
])
const activeImage = ref(0)

// ── description / details / looking for ─────────────────────────────────────
const descExpanded = ref(false)
const description = computed(() =>
    item.value.description ||
    'Like-new condition. Purchased 6 months ago and always kept in a case with a screen protector. Battery health at 98%. Includes original box, charging cable, and all documentation. Unlocked and works with all carriers. No scratches or dents. Perfect for someone looking for a premium device without the premium price tag.'
)
const shortDesc = computed(() => description.value.slice(0, 200) + (description.value.length > 200 ? '…' : ''))

const itemDetails = computed(() => item.value.details || [
    { label: 'Category',  value: item.value.category || 'Electronics'        },
    { label: 'Condition', value: item.value.condition || 'Like New', hl: true },
    { label: 'Brand',     value: item.value.brand    || 'Apple'               },
    { label: 'Model',     value: item.value.model    || 'iPhone 14 Pro Max'   },
    { label: 'Storage',   value: item.value.storage  || '256 GB'              },
    { label: 'Color',     value: item.value.color    || 'Space Black'         },
])

const lookingFor = computed(() => item.value.looking_for || [
    'Gaming Laptop (RTX 3060+)',
    'MacBook Pro M1 or newer',
    'iPad Pro 11" or 12.9"',
    'Open to other reasonable offers',
])

const safetyTips = [
    'Meet in a well-lit, public place',
    'Bring a friend when possible',
    'Inspect the item thoroughly before accepting',
    'Use SWAPY messaging — avoid sharing personal contacts early',
    'Never send money or deposits in advance',
]

// ── wishlist ──────────────────────────────────────────────────────────────────
const saved = ref(false)
function toggleSave() {
    if (isGuest.value) { showModal.value = true; return }
    saved.value = !saved.value
}

// ── guest modal ───────────────────────────────────────────────────────────────
const showModal = ref(false)
function requireAuth(action) {
    if (isGuest.value) { showModal.value = true; return false }
    return true
}
</script>

<template>
<div style="min-height:100vh;background:#f9fafb;font-family:'DM Sans',sans-serif;">

    <!-- ══ BREADCRUMB ══ -->
    <div style="max-width:1200px;margin:0 auto;padding:20px 24px 0;">
        <div style="display:flex;align-items:center;gap:8px;font-size:0.82rem;color:#6b7280;">
            <a href="/browse" style="color:#6b7280;text-decoration:none;display:inline-flex;align-items:center;gap:4px;transition:color 0.15s;"
                onmouseover="this.style.color='#ED730C'" onmouseout="this.style.color='#6b7280'">
                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Back to Browse
            </a>
            <span style="color:#d1d5db;">/</span>
            <span style="color:#3A3330;font-weight:600;">{{ item.category || 'Tech & Electronics' }}</span>
        </div>
    </div>

    <!-- ══ ITEM TITLE HEADER ══ -->
    <div style="max-width:1200px;margin:0 auto;padding:16px 24px 0;">
        <!-- Category badges -->
        <div style="display:flex;gap:8px;margin-bottom:14px;flex-wrap:wrap;">
            <span style="display:inline-block;padding:5px 13px;border-radius:999px;border:1px solid #e5e7eb;font-size:0.75rem;font-weight:600;color:#374151;background:#fff;">
                {{ item.category || 'Electronics' }}
            </span>
            <span style="display:inline-block;padding:5px 13px;border-radius:999px;border:1px solid #e5e7eb;font-size:0.75rem;font-weight:600;color:#374151;background:#fff;">
                {{ item.condition || 'Mint Condition' }}
            </span>
        </div>
        <!-- Title + action icons row -->
        <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:16px;margin-bottom:12px;">
            <h1 style="font-size:1.9rem;font-weight:900;color:#1a1a1a;line-height:1.2;margin:0;letter-spacing:-.02em;">
                {{ item.title || 'iPhone 14 Pro Max 256GB – Space Black' }}
            </h1>
            <div style="display:flex;gap:8px;flex-shrink:0;margin-top:4px;">
                <button style="width:38px;height:38px;border-radius:50%;border:1px solid #e5e7eb;background:#fff;box-shadow:0 1px 6px rgba(0,0,0,0.07);display:flex;align-items:center;justify-content:center;cursor:pointer;transition:all 0.15s;" onmouseover="this.style.borderColor='#ED730C'" onmouseout="this.style.borderColor='#e5e7eb'">
                    <svg width="16" height="16" fill="none" stroke="#6b7280" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/></svg>
                </button>
                <button @click="toggleSave" style="width:38px;height:38px;border-radius:50%;border:1px solid #e5e7eb;background:#fff;box-shadow:0 1px 6px rgba(0,0,0,0.07);display:flex;align-items:center;justify-content:center;cursor:pointer;transition:all 0.15s;" onmouseover="this.style.borderColor='#ED730C'" onmouseout="this.style.borderColor='#e5e7eb'">
                    <svg width="16" height="16" :fill="saved ? '#ED730C' : 'none'" :stroke="saved ? '#ED730C' : '#6b7280'" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                </button>
            </div>
        </div>
        <!-- Meta row -->
        <div style="display:flex;align-items:center;gap:20px;flex-wrap:wrap;margin-bottom:4px;">
            <span style="display:flex;align-items:center;gap:5px;font-size:0.83rem;color:#6b7280;">
                <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                {{ item.location || 'New York, NY' }}
            </span>
            <span style="display:flex;align-items:center;gap:5px;font-size:0.83rem;color:#6b7280;">
                <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                {{ item.views || 124 }} views
            </span>
            <span style="display:flex;align-items:center;gap:5px;font-size:0.83rem;color:#6b7280;">
                <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2"/></svg>
                Listed {{ item.listed_at || '2 days ago' }}
            </span>
        </div>
    </div>

    <!-- ══ MAIN CONTENT ══ -->
    <div style="max-width:1200px;margin:0 auto;padding:24px 24px 60px;display:grid;grid-template-columns:1fr 420px;gap:40px;align-items:start;">

        <!-- LEFT — Image gallery -->
        <div>
            <!-- Main image -->
            <div style="border-radius:18px;overflow:hidden;background:#f3f4f6;aspect-ratio:4/3;margin-bottom:14px;box-shadow:0 4px 20px rgba(0,0,0,0.10);">
                <img
                    :src="images[activeImage]"
                    :alt="item.title"
                    style="width:100%;height:100%;object-fit:cover;transition:opacity 0.2s;"
                >
            </div>
            <!-- Thumbnails -->
            <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:10px;">
                <div
                    v-for="(img, i) in images.slice(1,4)" :key="i"
                    @click="activeImage = i + 1"
                    :style="{
                        borderRadius:'12px',overflow:'hidden',cursor:'pointer',
                        aspectRatio:'4/3',
                        border: activeImage === i+1 ? '2.5px solid #ED730C' : '2.5px solid transparent',
                        boxShadow: '0 2px 10px rgba(0,0,0,0.08)',
                        transition:'border-color 0.15s,box-shadow 0.15s'
                    }"
                >
                    <img :src="img" style="width:100%;height:100%;object-fit:cover;">
                </div>
            </div>
        </div>

        <!-- RIGHT — Action panel -->
        <div style="position:sticky;top:90px;">

            <!-- ── Estimated Value card ── -->
            <div style="background:#fff;border-radius:18px;border:1px solid #f0f0f0;box-shadow:0 4px 20px rgba(0,0,0,0.07);padding:20px 22px;margin-bottom:14px;display:flex;align-items:center;justify-content:space-between;">
                <div>
                    <p style="font-size:0.72rem;font-weight:600;color:#9ca3af;text-transform:uppercase;letter-spacing:.07em;margin:0 0 4px;">Estimated Value</p>
                    <span style="font-size:2rem;font-weight:900;color:#ED730C;letter-spacing:-.02em;">${{ (item.value || 900).toLocaleString() }}</span>
                </div>
                <span style="display:inline-flex;align-items:center;gap:6px;background:#ED730C;color:#fff;font-size:0.75rem;font-weight:800;padding:8px 14px;border-radius:10px;letter-spacing:.03em;">
                    <svg width="13" height="13" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                    Premium Item
                </span>
            </div>

            <!-- ── Seller profile card ── -->
            <div style="background:#fff;border-radius:18px;border:1px solid #f0f0f0;box-shadow:0 4px 20px rgba(0,0,0,0.07);padding:22px 22px 20px;margin-bottom:14px;">

                <!-- Avatar + name + rating -->
                <div style="display:flex;align-items:center;gap:14px;margin-bottom:18px;">
                    <div style="width:52px;height:52px;border-radius:50%;background:#ED730C;color:#fff;font-size:1rem;font-weight:800;display:flex;align-items:center;justify-content:center;flex-shrink:0;box-shadow:0 4px 12px rgba(237,115,12,0.35);">
                        {{ (item.owner_name || 'Sarah Johnson').split(' ').map(n => n[0]).join('').slice(0,2) }}
                    </div>
                    <div>
                        <div style="font-size:1rem;font-weight:800;color:#1a1a1a;margin-bottom:3px;">{{ item.owner_name || 'Sarah Johnson' }}</div>
                        <div style="display:flex;align-items:center;gap:6px;">
                            <svg width="13" height="13" fill="#f59e0b" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            <span style="font-size:0.82rem;font-weight:700;color:#374151;">{{ item.owner_rating || '4.9' }}</span>
                            <span style="font-size:0.78rem;color:#9ca3af;">{{ item.owner_swaps || '15' }} swaps</span>
                        </div>
                    </div>
                </div>

                <!-- Divider -->
                <div style="height:1px;background:#f3f4f6;margin-bottom:16px;"></div>

                <!-- Trust signals -->
                <div style="display:flex;flex-direction:column;gap:13px;margin-bottom:18px;">
                    <!-- Response time -->
                    <div style="display:flex;align-items:center;gap:12px;">
                        <div style="width:30px;height:30px;border-radius:8px;background:#fff7ed;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <svg width="15" height="15" fill="none" stroke="#ED730C" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2"/></svg>
                        </div>
                        <span style="font-size:0.85rem;color:#4b5563;">Usually responds in <strong style="color:#1a1a1a;">{{ item.owner_response || '2 hours' }}</strong></span>
                    </div>
                    <!-- Member since -->
                    <div style="display:flex;align-items:center;gap:12px;">
                        <div style="width:30px;height:30px;border-radius:8px;background:#fff7ed;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <svg width="15" height="15" fill="none" stroke="#ED730C" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                        </div>
                        <span style="font-size:0.85rem;color:#4b5563;">Member since <strong style="color:#1a1a1a;">{{ item.owner_since || 'Jan 2024' }}</strong></span>
                    </div>
                    <!-- Verified -->
                    <div style="display:flex;align-items:center;gap:12px;">
                        <div style="width:30px;height:30px;border-radius:8px;background:#f0faf9;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <svg width="15" height="15" fill="none" stroke="#149189" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        </div>
                        <span style="font-size:0.85rem;color:#4b5563;"><strong style="color:#1a1a1a;">Verified</strong> swapper</span>
                    </div>
                </div>

                <!-- Divider -->
                <div style="height:1px;background:#f3f4f6;margin-bottom:18px;"></div>

                <!-- CTA Buttons -->
                <button
                    @click="requireAuth('message')"
                    style="width:100%;padding:15px;background:#ED730C;color:#fff;border:none;border-radius:12px;font-size:0.9rem;font-weight:800;letter-spacing:.03em;cursor:pointer;margin-bottom:10px;font-family:'DM Sans',sans-serif;display:flex;align-items:center;justify-content:center;gap:9px;box-shadow:0 4px 14px rgba(237,115,12,0.35);transition:all 0.15s;"
                    onmouseover="this.style.background='#d4620a';this.style.boxShadow='0 6px 18px rgba(237,115,12,0.45)'"
                    onmouseout="this.style.background='#ED730C';this.style.boxShadow='0 4px 14px rgba(237,115,12,0.35)'"
                >
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                    Message {{ (item.owner_name || 'Sarah Johnson').split(' ')[0] }}
                </button>
                <button
                    @click="requireAuth('swap')"
                    style="width:100%;padding:14px;background:#fff;color:#ED730C;border:1.5px solid #ED730C;border-radius:12px;font-size:0.9rem;font-weight:800;letter-spacing:.03em;cursor:pointer;font-family:'DM Sans',sans-serif;display:flex;align-items:center;justify-content:center;gap:9px;transition:all 0.15s;"
                    onmouseover="this.style.background='#fff7ed'" onmouseout="this.style.background='#fff'"
                >
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 16V4m0 0L3 8m4-4l4 4M17 8v12m0 0l4-4m-4 4l-4-4"/></svg>
                    Make an Offer
                </button>

                <!-- Save -->
                <button
                    @click="toggleSave"
                    style="width:100%;padding:10px;margin-top:8px;background:none;border:none;font-size:0.82rem;font-weight:600;cursor:pointer;font-family:'DM Sans',sans-serif;transition:color 0.15s;display:flex;align-items:center;justify-content:center;gap:6px;"
                    :style="{color: saved ? '#ED730C' : '#9ca3af'}"
                >
                    <svg width="14" height="14" :fill="saved ? '#ED730C' : 'none'" :stroke="saved ? '#ED730C' : '#9ca3af'" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                    {{ saved ? 'Saved for Later' : 'Save Item for Later' }}
                </button>
            </div>

            <!-- Match badge card -->
            <div style="background:#f0fdf9;border:1.5px solid #a7f3e8;border-radius:16px;box-shadow:0 2px 12px rgba(20,145,137,0.08);padding:16px 18px;margin-bottom:14px;">
                <div style="display:flex;align-items:center;gap:10px;margin-bottom:5px;">
                    <span style="background:#149189;color:#fff;font-size:0.65rem;font-weight:800;padding:3px 8px;border-radius:6px;letter-spacing:.05em;">{{ item.match_score || '92' }}% MATCH</span>
                    <span style="font-size:0.84rem;font-weight:700;color:#0d7470;">{{ item.match_label || "Matches your listing" }}</span>
                </div>
                <p style="font-size:0.75rem;color:#6b7280;line-height:1.5;margin:0;">Sign up to see your full compatibility score and AI-powered swap analysis.</p>
            </div>

            <!-- Disclaimer -->
            <p style="font-size:0.72rem;color:#9ca3af;line-height:1.6;padding:0 2px;text-align:center;">
                By initiating a swap, you agree to SWAPY's <a href="#" style="color:#ED730C;text-decoration:none;">terms of service</a>.
            </p>
        </div>
    </div>

    <!-- ══ DESCRIPTION + DETAILS + LOOKING FOR + SAFETY ══ -->
    <div style="max-width:1200px;margin:0 auto;padding:0 24px 40px;display:grid;grid-template-columns:1fr 420px;gap:40px;align-items:start;">

        <!-- LEFT column: Description → Item Details → Looking For -->
        <div style="display:flex;flex-direction:column;gap:20px;">

            <!-- Description -->
            <div style="background:#fff;border-radius:18px;border:1px solid #f0f0f0;box-shadow:0 4px 20px rgba(0,0,0,0.07);padding:28px 30px;">
                <h2 style="font-size:1.15rem;font-weight:800;color:#1a1a1a;margin-bottom:14px;padding-bottom:14px;border-bottom:1px solid #f3f4f6;">
                    Description
                </h2>
                <p style="font-size:0.9rem;color:#4b5563;line-height:1.75;margin:0;">
                    {{ descExpanded ? description : shortDesc }}
                </p>
                <button
                    v-if="description.length > 200"
                    @click="descExpanded = !descExpanded"
                    style="margin-top:12px;background:none;border:none;padding:0;font-size:0.85rem;font-weight:700;color:#ED730C;cursor:pointer;font-family:'DM Sans',sans-serif;"
                >
                    {{ descExpanded ? 'Show less ↑' : 'Read more ↓' }}
                </button>
            </div>

            <!-- Item Details -->
            <div style="background:#fff;border-radius:18px;border:1px solid #f0f0f0;box-shadow:0 4px 20px rgba(0,0,0,0.07);padding:28px 30px;">
                <h2 style="font-size:1.15rem;font-weight:800;color:#1a1a1a;margin-bottom:18px;padding-bottom:14px;border-bottom:1px solid #f3f4f6;">
                    Item Details
                </h2>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:0;">
                    <div
                        v-for="(d, i) in itemDetails" :key="d.label"
                        :style="{
                            padding:'14px 0',
                            borderBottom: i < itemDetails.length - 2 ? '1px solid #f3f4f6' : 'none',
                            paddingRight: i % 2 === 0 ? '24px' : '0',
                            paddingLeft:  i % 2 === 1 ? '24px' : '0',
                            borderLeft:   i % 2 === 1 ? '1px solid #f3f4f6' : 'none',
                        }"
                    >
                        <p style="font-size:0.75rem;font-weight:600;color:#9ca3af;text-transform:uppercase;letter-spacing:.06em;margin-bottom:5px;">
                            {{ d.label }}
                        </p>
                        <!-- Condition pill -->
                        <span v-if="d.hl"
                            style="display:inline-block;padding:4px 12px;background:#dcfce7;color:#16a34a;font-size:0.82rem;font-weight:700;border-radius:999px;">
                            {{ d.value }}
                        </span>
                        <p v-else style="font-size:0.9rem;font-weight:700;color:#1a1a1a;margin:0;">
                            {{ d.value }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- What I'm Looking For -->
            <div style="background:#fff;border-radius:18px;border:1px solid #f0f0f0;box-shadow:0 4px 20px rgba(0,0,0,0.07);padding:28px 30px;">
                <h2 style="font-size:1.15rem;font-weight:800;color:#1a1a1a;margin-bottom:6px;padding-bottom:14px;border-bottom:1px solid #f3f4f6;">
                    What I'm Looking For
                </h2>
                <p style="font-size:0.82rem;color:#9ca3af;margin:-8px 0 16px;">Items the owner would like in exchange</p>
                <div style="display:flex;flex-direction:column;gap:10px;">
                    <div
                        v-for="(want, i) in lookingFor" :key="i"
                        style="display:flex;align-items:center;gap:12px;padding:12px 16px;background:#f9fafb;border-radius:12px;border:1px solid #f3f4f6;transition:border-color 0.15s;"
                        onmouseover="this.style.borderColor='#ED730C22'" onmouseout="this.style.borderColor='#f3f4f6'"
                    >
                        <div style="width:30px;height:30px;background:linear-gradient(135deg,#ED730C,#f59e0b);border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <svg width="14" height="14" fill="none" stroke="white" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M7 16V4m0 0L3 8m4-4l4 4M17 8v12m0 0l4-4m-4 4l-4-4"/>
                            </svg>
                        </div>
                        <span style="font-size:0.875rem;font-weight:600;color:#374151;">{{ want }}</span>
                    </div>
                </div>
            </div>

        </div>

        <!-- RIGHT column: Safety Tips -->
        <div>
            <div style="background:#fff;border-radius:18px;border:1px solid #f0f0f0;box-shadow:0 4px 20px rgba(0,0,0,0.07);padding:24px 26px;position:sticky;top:90px;">
                <!-- Header -->
                <div style="display:flex;align-items:center;gap:10px;margin-bottom:18px;padding-bottom:14px;border-bottom:1px solid #f3f4f6;">
                    <div style="width:34px;height:34px;background:#fef3c7;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <svg width="17" height="17" fill="none" stroke="#d97706" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 style="font-size:0.95rem;font-weight:800;color:#1a1a1a;margin:0;">Safety Tips</h3>
                        <p style="font-size:0.75rem;color:#9ca3af;margin:2px 0 0;">Stay safe when swapping</p>
                    </div>
                </div>

                <!-- Tips list -->
                <div style="display:flex;flex-direction:column;gap:12px;">
                    <div
                        v-for="(tip, i) in safetyTips" :key="i"
                        style="display:flex;align-items:flex-start;gap:11px;"
                    >
                        <div style="width:22px;height:22px;background:#149189;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-top:1px;">
                            <svg width="10" height="10" fill="none" stroke="white" stroke-width="3" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <span style="font-size:0.83rem;color:#4b5563;line-height:1.55;">{{ tip }}</span>
                    </div>
                </div>

                <!-- Report link -->
                <div style="margin-top:20px;padding-top:16px;border-top:1px solid #f3f4f6;text-align:center;">
                    <button style="background:none;border:none;font-size:0.78rem;color:#9ca3af;cursor:pointer;font-family:'DM Sans',sans-serif;transition:color 0.15s;"
                        onmouseover="this.style.color='#ef4444'" onmouseout="this.style.color='#9ca3af'">
                        🚩 Report this listing
                    </button>
                </div>
            </div>
        </div>

    </div>

    <!-- ══ SIMILAR ITEMS ══ -->
    <div style="max-width:1200px;margin:0 auto;padding:0 24px 80px;">
        <h2 style="font-size:1.375rem;font-weight:800;color:#1a1a1a;margin-bottom:24px;">Similar items you might like</h2>
        <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:20px;">
            <div
                v-for="s in similarItems" :key="s.id"
                style="background:#fff;border-radius:16px;overflow:hidden;border:1px solid #f0f0f0;box-shadow:0 4px 16px rgba(0,0,0,0.07);cursor:pointer;transition:box-shadow 0.2s,border-color 0.2s,transform 0.2s;"
                onmouseover="this.style.boxShadow='0 10px 32px rgba(0,0,0,0.12)';this.style.borderColor='#e5e7eb';this.style.transform='translateY(-3px)'"
                onmouseout="this.style.boxShadow='0 4px 16px rgba(0,0,0,0.07)';this.style.borderColor='#f0f0f0';this.style.transform='translateY(0)'"
            >
                <div style="position:relative;aspect-ratio:16/10;overflow:hidden;background:#f3f4f6;">
                    <img :src="s.image" :alt="s.title" style="width:100%;height:100%;object-fit:cover;transition:transform 0.3s;"
                        onmouseover="this.style.transform='scale(1.04)'" onmouseout="this.style.transform='scale(1)'">
                    <span style="position:absolute;top:10px;right:10px;background:rgba(0,0,0,0.6);color:#fff;font-size:0.7rem;font-weight:700;padding:4px 9px;border-radius:6px;backdrop-filter:blur(4px);">
                        {{ s.distance }} MILES
                    </span>
                </div>
                <div style="padding:14px 16px 16px;">
                    <h3 style="font-size:0.9rem;font-weight:700;color:#1a1a1a;margin-bottom:4px;">{{ s.title }}</h3>
                    <p style="font-size:0.85rem;font-weight:700;color:#ED730C;margin-bottom:10px;">Value: ${{ s.value.toLocaleString() }}</p>
                    <div style="display:flex;align-items:center;gap:8px;">
                        <img :src="s.avatar" style="width:26px;height:26px;border-radius:50%;object-fit:cover;">
                        <span style="font-size:0.78rem;color:#6b7280;">{{ s.owner }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ══ GUEST MODAL ══ -->
<Transition name="modal">
    <div v-if="showModal"
        style="position:fixed;inset:0;background:rgba(0,0,0,0.5);display:flex;align-items:center;justify-content:center;z-index:9999;padding:20px;backdrop-filter:blur(4px);"
        @click.self="showModal = false"
    >
        <div style="background:#fff;border-radius:24px;padding:40px 36px;max-width:420px;width:100%;position:relative;box-shadow:0 24px 60px rgba(0,0,0,0.18);">

            <!-- Close -->
            <button @click="showModal = false"
                style="position:absolute;top:16px;right:16px;width:32px;height:32px;border:none;background:#f3f4f6;border-radius:50%;cursor:pointer;display:flex;align-items:center;justify-content:center;"
                onmouseover="this.style.background='#e5e7eb'" onmouseout="this.style.background='#f3f4f6'"
            >
                <svg width="14" height="14" fill="none" stroke="#6b7280" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>

            <!-- Logo -->
            <div style="text-align:center;margin-bottom:16px;">
                <img :src="'/images/logo-swapy.png'" alt="SWAPY" style="height:64px;object-fit:contain;">
            </div>

            <h2 style="text-align:center;font-size:1.35rem;font-weight:800;color:#1a1a1a;margin-bottom:10px;">
                Sign In Required
            </h2>
            <p style="text-align:center;font-size:0.875rem;color:#6b7280;margin-bottom:24px;line-height:1.6;">
                You need to be signed in to list items and start swapping. Join SWAPY today!
            </p>

            <!-- CTAs -->
            <a href="/login"
                style="display:block;width:100%;padding:15px;background:#ED730C;color:#fff;border-radius:12px;font-size:0.9rem;font-weight:800;cursor:pointer;text-align:center;text-decoration:none;margin-bottom:10px;box-sizing:border-box;font-family:'DM Sans',sans-serif;transition:background 0.15s;"
                onmouseover="this.style.background='#d4620a'" onmouseout="this.style.background='#ED730C'"
            >
                Sign In
            </a>
            <a href="/register"
                style="display:block;width:100%;padding:15px;background:#fff;color:#ED730C;border:1.5px solid #ED730C;border-radius:12px;font-size:0.9rem;font-weight:700;cursor:pointer;text-align:center;text-decoration:none;box-sizing:border-box;font-family:'DM Sans',sans-serif;"
            >
                Create Account
            </a>

            <!-- Checklist -->
            <div style="margin-top:20px;background:#f9fafb;border-radius:12px;padding:16px 20px;display:flex;flex-direction:column;gap:12px;">
                <div v-for="b in ['Free to join', 'Start swapping instantly', 'No hidden fees']" :key="b"
                    style="display:flex;align-items:center;gap:10px;">
                    <div style="width:22px;height:22px;background:#149189;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <svg width="11" height="11" fill="none" stroke="white" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <span style="font-size:0.85rem;font-weight:600;color:#374151;">{{ b }}</span>
                </div>
            </div>

        </div>
    </div>
</Transition>

</div>
</template>

<style scoped>
.modal-enter-active, .modal-leave-active { transition: opacity 0.2s; }
.modal-enter-from, .modal-leave-to { opacity: 0; }
.modal-enter-active > div, .modal-leave-active > div { transition: transform 0.2s; }
.modal-enter-from > div { transform: scale(0.95) translateY(10px); }
.modal-leave-to > div { transform: scale(0.95) translateY(10px); }
</style>    