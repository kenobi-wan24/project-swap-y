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

    <!-- ══ MAIN CONTENT ══ -->
    <div style="max-width:1200px;margin:0 auto;padding:24px 24px 60px;display:grid;grid-template-columns:1fr 420px;gap:40px;align-items:start;">

        <!-- LEFT — Image gallery -->
        <div>
            <!-- Main image -->
            <div style="border-radius:18px;overflow:hidden;background:#f3f4f6;aspect-ratio:4/3;margin-bottom:14px;">
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
                        aspectRatio:'4/3',border: activeImage === i+1 ? '2.5px solid #ED730C' : '2.5px solid transparent',
                        transition:'border-color 0.15s'
                    }"
                >
                    <img :src="img" style="width:100%;height:100%;object-fit:cover;">
                </div>
            </div>
        </div>

        <!-- RIGHT — Item info -->
        <div>
            <!-- Category badges -->
            <div style="display:flex;gap:8px;margin-bottom:16px;flex-wrap:wrap;">
                <span style="display:inline-block;padding:5px 12px;border-radius:999px;border:1px solid #e5e7eb;font-size:0.75rem;font-weight:600;color:#374151;">
                    {{ item.category || 'Electronics' }}
                </span>
                <span style="display:inline-block;padding:5px 12px;border-radius:999px;border:1px solid #e5e7eb;font-size:0.75rem;font-weight:600;color:#374151;">
                    {{ item.condition || 'Mint Condition' }}
                </span>
            </div>

            <!-- Title -->
            <h1 style="font-size:1.75rem;font-weight:800;color:#1a1a1a;line-height:1.25;margin-bottom:16px;">
                {{ item.title || 'MacBook Pro 16" M2 Max (32GB RAM, 1TB SSD)' }}
            </h1>

            <!-- Value + Distance -->
            <div style="display:flex;align-items:center;gap:14px;margin-bottom:20px;">
                <span style="background:#149189;color:#fff;font-size:0.9rem;font-weight:700;padding:7px 16px;border-radius:8px;">
                    Valued at ${{ (item.value || 2450).toLocaleString() }}
                </span>
                <span style="font-size:0.82rem;font-weight:700;color:#6b7280;letter-spacing:.04em;text-transform:uppercase;display:flex;align-items:center;gap:5px;">
                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    {{ item.distance || '12.4' }} miles away
                </span>
            </div>

            <!-- Owner -->
            <div style="display:flex;align-items:center;gap:12px;padding:14px 16px;background:#fff;border-radius:14px;border:1px solid #f3f4f6;margin-bottom:20px;">
                <img
                    :src="item.owner_avatar || 'https://i.pravatar.cc/40?img=3'"
                    style="width:42px;height:42px;border-radius:50%;object-fit:cover;"
                >
                <div style="flex:1;">
                    <div style="font-weight:700;font-size:0.9rem;color:#1a1a1a;">{{ item.owner_name || 'Marcus Chen' }}</div>
                    <div style="font-size:0.78rem;color:#9ca3af;">@{{ item.owner_username || 'mchen_tech' }}</div>
                </div>
                <div style="display:flex;align-items:center;gap:4px;">
                    <svg width="14" height="14" fill="#f59e0b" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                    <span style="font-size:0.82rem;font-weight:700;color:#f59e0b;">{{ item.owner_rating || '4.7' }}</span>
                </div>
            </div>

            <!-- What I want in return -->
            <div style="margin-bottom:16px;">
                <p style="font-size:0.7rem;font-weight:800;color:#9ca3af;letter-spacing:.08em;text-transform:uppercase;margin-bottom:10px;">What I want in return</p>
                <div style="padding:14px 16px;background:#fff;border-radius:14px;border:1.5px solid #e5e7eb;display:flex;align-items:flex-start;gap:12px;margin-bottom:10px;">
                    <div style="width:32px;height:32px;border-radius:50%;background:#f0faf9;border:1.5px solid #149189;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <svg width="15" height="15" fill="none" stroke="#149189" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/></svg>
                    </div>
                    <div>
                        <div style="font-weight:700;font-size:0.9rem;color:#1a1a1a;margin-bottom:2px;">{{ item.wants_title || 'High-end Camera Gear' }}</div>
                        <div style="font-size:0.78rem;color:#6b7280;">{{ item.wants_desc || 'Sony A7 series or equivalent lens kits' }}</div>
                    </div>
                </div>
                <p style="font-size:0.8rem;color:#6b7280;padding:0 2px;">
                    Target Value: <strong style="color:#3A3330;">${{ item.wants_min || '2,200' }} – ${{ item.wants_max || '2,600' }}</strong>
                </p>
            </div>

            <!-- Match badge -->
            <div style="padding:14px 16px;background:#f0fdf9;border:1.5px solid #a7f3e8;border-radius:14px;margin-bottom:20px;">
                <div style="display:flex;align-items:center;gap:10px;margin-bottom:4px;">
                    <span style="background:#149189;color:#fff;font-size:0.65rem;font-weight:800;padding:3px 8px;border-radius:6px;letter-spacing:.05em;">{{ item.match_score || '92' }}% MATCH</span>
                    <span style="font-size:0.85rem;font-weight:700;color:#0d7470;">{{ item.match_label || "Matches your 'Sony A7R IV' listing" }}</span>
                </div>
                <p style="font-size:0.75rem;color:#6b7280;line-height:1.5;">Sign up to see your full compatibility score and AI-powered swap analysis.</p>
            </div>

            <!-- CTAs -->
            <button
                @click="requireAuth('swap')"
                style="width:100%;padding:15px;background:#ED730C;color:#fff;border:none;border-radius:12px;font-size:0.9rem;font-weight:800;letter-spacing:.04em;cursor:pointer;margin-bottom:10px;font-family:'DM Sans',sans-serif;transition:background 0.15s;"
                onmouseover="this.style.background='#d4620a'" onmouseout="this.style.background='#ED730C'"
            >
                Send Swap Offer
            </button>
            <button
                @click="requireAuth('message')"
                style="width:100%;padding:15px;background:#149189;color:#fff;border:none;border-radius:12px;font-size:0.9rem;font-weight:800;letter-spacing:.04em;cursor:pointer;margin-bottom:14px;font-family:'DM Sans',sans-serif;display:flex;align-items:center;justify-content:center;gap:8px;transition:background 0.15s;"
                onmouseover="this.style.background='#0f7068'" onmouseout="this.style.background='#149189'"
            >
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                Message Owner
            </button>
            <button
                @click="toggleSave"
                style="width:100%;padding:12px;background:none;border:none;font-size:0.85rem;font-weight:600;cursor:pointer;font-family:'DM Sans',sans-serif;transition:color 0.15s;"
                :style="{color: saved ? '#ED730C' : '#6b7280'}"
            >
                {{ saved ? '♥ Saved for Later' : 'Save Item for later' }}
            </button>

            <!-- Disclaimer -->
            <p style="font-size:0.72rem;color:#9ca3af;line-height:1.6;margin-top:14px;padding:0 2px;">
                By initiating a swap, you agree to SWAPY's terms of service and peer-to-peer exchange protection guidelines. Guest users must create an account to finalize transactions.
            </p>
        </div>
    </div>

    <!-- ══ SIMILAR ITEMS ══ -->
    <div style="max-width:1200px;margin:0 auto;padding:0 24px 80px;">
        <h2 style="font-size:1.375rem;font-weight:800;color:#1a1a1a;margin-bottom:24px;">Similar items you might like</h2>
        <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:20px;">
            <div
                v-for="s in similarItems" :key="s.id"
                style="background:#fff;border-radius:16px;overflow:hidden;border:1px solid #f3f4f6;cursor:pointer;transition:box-shadow 0.2s,border-color 0.2s;"
                onmouseover="this.style.boxShadow='0 8px 30px rgba(0,0,0,0.09)';this.style.borderColor='#e5e7eb'"
                onmouseout="this.style.boxShadow='none';this.style.borderColor='#f3f4f6'"
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