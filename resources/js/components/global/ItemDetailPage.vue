<script setup>
import { ref, computed } from 'vue'

const el      = document.getElementById('item-detail-app')
const item    = ref(JSON.parse(el?.dataset.item || '{}'))
const isGuest = ref((el?.dataset.guest || 'true') === 'true')

// ── images ────────────────────────────────────────────────────────────────────
const images = computed(() => Array.isArray(item.value.images) ? item.value.images : [])
const hasImages = computed(() => images.value.length > 0)
const activeImage = ref(0)

// ── description ───────────────────────────────────────────────────────────────
const descExpanded = ref(false)
const description = computed(() => item.value.description || '')
const shortDesc = computed(() => description.value.slice(0, 220) + (description.value.length > 220 ? '…' : ''))

// ── looking for (array from controller) ──────────────────────────────────────
const lookingFor = computed(() => Array.isArray(item.value.looking_for) ? item.value.looking_for : [])

// ── details (real fields only) ────────────────────────────────────────────────
const details = computed(() => {
  const d = []
  if (item.value.category)  d.push({ label: 'Category',   value: item.value.category })
  if (item.value.condition) d.push({ label: 'Condition',  value: item.value.condition, hl: true })
  if (item.value.location)  d.push({ label: 'Location',   value: item.value.location })
  if (item.value.value)     d.push({ label: 'Est. Value', value: '₱' + Number(item.value.value).toLocaleString() })
  return d
})

const owner = computed(() => item.value.owner || {})
const ownerFirst = computed(() => (owner.value.name || 'the owner').split(' ')[0])
const valueLabel = computed(() => item.value.value ? '₱' + Number(item.value.value).toLocaleString() : null)

const safetyTips = [
  'Meet in a well-lit, public place',
  'Bring a friend when possible',
  'Inspect the item thoroughly before accepting',
  'Use SWAPY messaging — avoid sharing personal contacts early',
  'Never send money or deposits in advance',
]

// ── wishlist + auth gate ──────────────────────────────────────────────────────
const saved = ref(false)
const showModal = ref(false)
function toggleSave() { if (isGuest.value) { showModal.value = true; return } saved.value = !saved.value }
function requireAuth() { if (isGuest.value) { showModal.value = true; return false } return true }
</script>

<template>
<div style="min-height:100vh;background:#f9fafb;font-family:'DM Sans',sans-serif;">

  <!-- ══ BREADCRUMB ══ -->
  <div style="max-width:1200px;margin:0 auto;padding:20px 24px 0;">
    <div style="display:flex;align-items:center;gap:8px;font-size:0.82rem;color:#6b7280;">
      <a href="/browse" style="color:#6b7280;text-decoration:none;display:inline-flex;align-items:center;gap:4px;"
        onmouseover="this.style.color='#ED730C'" onmouseout="this.style.color='#6b7280'">
        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Back to Browse
      </a>
      <span v-if="item.category" style="color:#d1d5db;">/</span>
      <span v-if="item.category" style="color:#3A3330;font-weight:600;">{{ item.category }}</span>
    </div>
  </div>

  <!-- ══ TITLE HEADER ══ -->
  <div style="max-width:1200px;margin:0 auto;padding:16px 24px 0;">
    <div style="display:flex;gap:8px;margin-bottom:14px;flex-wrap:wrap;">
      <span v-if="item.category" style="padding:5px 13px;border-radius:999px;border:1px solid #e5e7eb;font-size:0.75rem;font-weight:600;color:#374151;background:#fff;">{{ item.category }}</span>
      <span v-if="item.condition" style="padding:5px 13px;border-radius:999px;border:1px solid #e5e7eb;font-size:0.75rem;font-weight:600;color:#374151;background:#fff;">{{ item.condition }}</span>
      <span v-if="item.is_promoted" style="display:inline-flex;align-items:center;gap:4px;padding:5px 12px;border-radius:999px;background:#1A1A1A;color:#fff;font-size:0.72rem;font-weight:800;letter-spacing:.04em;text-transform:uppercase;"><svg width="10" height="10" viewBox="0 0 24 24" fill="#fff"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>Promoted</span>
    </div>
    <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:16px;margin-bottom:12px;">
      <h1 style="font-size:1.9rem;font-weight:900;color:#1a1a1a;line-height:1.2;margin:0;letter-spacing:-.02em;">{{ item.title }}</h1>
      <div style="display:flex;gap:8px;flex-shrink:0;margin-top:4px;">
        <button @click="toggleSave" style="width:38px;height:38px;border-radius:50%;border:1px solid #e5e7eb;background:#fff;box-shadow:0 1px 6px rgba(0,0,0,0.07);display:flex;align-items:center;justify-content:center;cursor:pointer;" onmouseover="this.style.borderColor='#ED730C'" onmouseout="this.style.borderColor='#e5e7eb'">
          <svg width="16" height="16" :fill="saved ? '#ED730C' : 'none'" :stroke="saved ? '#ED730C' : '#6b7280'" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
        </button>
      </div>
    </div>
    <div style="display:flex;align-items:center;gap:20px;flex-wrap:wrap;">
      <span v-if="item.location" style="display:flex;align-items:center;gap:5px;font-size:0.83rem;color:#6b7280;">
        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
        {{ item.location }}
      </span>
      <span v-if="item.created_at" style="display:flex;align-items:center;gap:5px;font-size:0.83rem;color:#6b7280;">
        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2"/></svg>
        Listed {{ item.created_at }}
      </span>
    </div>
  </div>

  <!-- ══ MAIN ══ -->
  <div class="detail-grid" style="max-width:1200px;margin:0 auto;padding:24px 24px 60px;display:grid;grid-template-columns:1fr 420px;gap:40px;align-items:start;">

    <!-- LEFT — gallery -->
    <div>
      <div style="border-radius:18px;overflow:hidden;background:#f3f4f6;aspect-ratio:4/3;margin-bottom:14px;box-shadow:0 4px 20px rgba(0,0,0,0.10);">
        <img v-if="hasImages" :src="images[activeImage]" :alt="item.title" style="width:100%;height:100%;object-fit:cover;">
        <div v-else style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;color:#d1d5db;">
          <svg width="60" height="60" fill="none" stroke="currentColor" stroke-width="1.4" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="M21 15l-5-5L5 21"/></svg>
        </div>
      </div>
      <div v-if="images.length > 1" style="display:grid;grid-template-columns:repeat(4,1fr);gap:10px;">
        <div v-for="(img, i) in images" :key="i" @click="activeImage = i"
          :style="{ borderRadius:'12px', overflow:'hidden', cursor:'pointer', aspectRatio:'1/1', border: activeImage === i ? '2.5px solid #ED730C' : '2.5px solid transparent', boxShadow:'0 2px 10px rgba(0,0,0,0.08)' }">
          <img :src="img" style="width:100%;height:100%;object-fit:cover;">
        </div>
      </div>
    </div>

    <!-- RIGHT — action panel -->
    <div style="position:sticky;top:90px;">

      <!-- Value -->
      <div style="background:#fff;border-radius:18px;border:1px solid #f0f0f0;box-shadow:0 4px 20px rgba(0,0,0,0.07);padding:20px 22px;margin-bottom:14px;display:flex;align-items:center;justify-content:space-between;gap:12px;">
        <div>
          <p style="font-size:0.72rem;font-weight:600;color:#9ca3af;text-transform:uppercase;letter-spacing:.07em;margin:0 0 4px;">Estimated Value</p>
          <span style="font-size:2rem;font-weight:900;color:#ED730C;letter-spacing:-.02em;">{{ valueLabel || 'Open to offers' }}</span>
        </div>
      </div>

      <!-- Seller -->
      <div style="background:#fff;border-radius:18px;border:1px solid #f0f0f0;box-shadow:0 4px 20px rgba(0,0,0,0.07);padding:22px 22px 20px;margin-bottom:14px;">
        <div style="display:flex;align-items:center;gap:14px;margin-bottom:18px;">
          <div style="width:52px;height:52px;border-radius:50%;background:#ED730C;color:#fff;font-size:1rem;font-weight:800;display:flex;align-items:center;justify-content:center;flex-shrink:0;box-shadow:0 4px 12px rgba(237,115,12,0.35);">{{ owner.initials }}</div>
          <div>
            <a v-if="owner.username" :href="'/store/' + owner.username" style="font-size:1rem;font-weight:800;color:#1a1a1a;text-decoration:none;display:block;margin-bottom:3px;">{{ owner.name }}</a>
            <div v-else style="font-size:1rem;font-weight:800;color:#1a1a1a;margin-bottom:3px;">{{ owner.name }}</div>
            <div v-if="owner.member_since" style="font-size:0.78rem;color:#9ca3af;">Member since {{ owner.member_since }}</div>
          </div>
        </div>

        <div style="height:1px;background:#f3f4f6;margin-bottom:18px;"></div>

        <button @click="requireAuth()" style="width:100%;padding:15px;background:#ED730C;color:#fff;border:none;border-radius:12px;font-size:0.9rem;font-weight:800;letter-spacing:.03em;cursor:pointer;margin-bottom:10px;font-family:'DM Sans',sans-serif;display:flex;align-items:center;justify-content:center;gap:9px;box-shadow:0 4px 14px rgba(237,115,12,0.35);"
          onmouseover="this.style.background='#d4620a'" onmouseout="this.style.background='#ED730C'">
          <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
          Message {{ ownerFirst }}
        </button>
        <button @click="requireAuth()" style="width:100%;padding:14px;background:#fff;color:#ED730C;border:1.5px solid #ED730C;border-radius:12px;font-size:0.9rem;font-weight:800;letter-spacing:.03em;cursor:pointer;font-family:'DM Sans',sans-serif;display:flex;align-items:center;justify-content:center;gap:9px;"
          onmouseover="this.style.background='#fff7ed'" onmouseout="this.style.background='#fff'">
          <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 16V4m0 0L3 8m4-4l4 4M17 8v12m0 0l4-4m-4 4l-4-4"/></svg>
          Make an Offer
        </button>

        <button @click="toggleSave" :style="{ width:'100%', padding:'10px', marginTop:'8px', background:'none', border:'none', fontSize:'0.82rem', fontWeight:'600', cursor:'pointer', fontFamily:`'DM Sans',sans-serif`, display:'flex', alignItems:'center', justifyContent:'center', gap:'6px', color: saved ? '#ED730C' : '#9ca3af' }">
          <svg width="14" height="14" :fill="saved ? '#ED730C' : 'none'" :stroke="saved ? '#ED730C' : '#9ca3af'" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
          {{ saved ? 'Saved for Later' : 'Save Item for Later' }}
        </button>
      </div>

      <p style="font-size:0.72rem;color:#9ca3af;line-height:1.6;padding:0 2px;text-align:center;">
        By initiating a swap, you agree to SWAPY's <a href="#" style="color:#ED730C;text-decoration:none;">terms of service</a>.
      </p>
    </div>
  </div>

  <!-- ══ DESCRIPTION + DETAILS + LOOKING FOR + SAFETY ══ -->
  <div class="detail-grid" style="max-width:1200px;margin:0 auto;padding:0 24px 80px;display:grid;grid-template-columns:1fr 420px;gap:40px;align-items:start;">

    <div style="display:flex;flex-direction:column;gap:20px;">

      <!-- Description -->
      <div style="background:#fff;border-radius:18px;border:1px solid #f0f0f0;box-shadow:0 4px 20px rgba(0,0,0,0.07);padding:28px 30px;">
        <h2 style="font-size:1.15rem;font-weight:800;color:#1a1a1a;margin-bottom:14px;padding-bottom:14px;border-bottom:1px solid #f3f4f6;">Description</h2>
        <p v-if="description" style="font-size:0.9rem;color:#4b5563;line-height:1.75;margin:0;white-space:pre-line;">{{ descExpanded ? description : shortDesc }}</p>
        <p v-else style="font-size:0.9rem;color:#9ca3af;margin:0;">No description provided.</p>
        <button v-if="description.length > 220" @click="descExpanded = !descExpanded" style="margin-top:12px;background:none;border:none;padding:0;font-size:0.85rem;font-weight:700;color:#ED730C;cursor:pointer;font-family:'DM Sans',sans-serif;">
          {{ descExpanded ? 'Show less ↑' : 'Read more ↓' }}
        </button>
      </div>

      <!-- Item details -->
      <div style="background:#fff;border-radius:18px;border:1px solid #f0f0f0;box-shadow:0 4px 20px rgba(0,0,0,0.07);padding:28px 30px;">
        <h2 style="font-size:1.15rem;font-weight:800;color:#1a1a1a;margin-bottom:18px;padding-bottom:14px;border-bottom:1px solid #f3f4f6;">Item Details</h2>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:18px 24px;">
          <div v-for="d in details" :key="d.label">
            <p style="font-size:0.75rem;font-weight:600;color:#9ca3af;text-transform:uppercase;letter-spacing:.06em;margin-bottom:5px;">{{ d.label }}</p>
            <span v-if="d.hl" style="display:inline-block;padding:4px 12px;background:#dcfce7;color:#16a34a;font-size:0.82rem;font-weight:700;border-radius:999px;">{{ d.value }}</span>
            <p v-else style="font-size:0.9rem;font-weight:700;color:#1a1a1a;margin:0;">{{ d.value }}</p>
          </div>
        </div>
      </div>

      <!-- Looking for -->
      <div v-if="lookingFor.length" style="background:#fff;border-radius:18px;border:1px solid #f0f0f0;box-shadow:0 4px 20px rgba(0,0,0,0.07);padding:28px 30px;">
        <h2 style="font-size:1.15rem;font-weight:800;color:#1a1a1a;margin-bottom:6px;padding-bottom:14px;border-bottom:1px solid #f3f4f6;">What I'm Looking For</h2>
        <p style="font-size:0.82rem;color:#9ca3af;margin:-8px 0 16px;">Items the owner would like in exchange</p>
        <div style="display:flex;flex-direction:column;gap:10px;">
          <div v-for="(want, i) in lookingFor" :key="i" style="display:flex;align-items:center;gap:12px;padding:12px 16px;background:#f9fafb;border-radius:12px;border:1px solid #f3f4f6;">
            <div style="width:30px;height:30px;background:linear-gradient(135deg,#ED730C,#f59e0b);border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
              <svg width="14" height="14" fill="none" stroke="white" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 16V4m0 0L3 8m4-4l4 4M17 8v12m0 0l4-4m-4 4l-4-4"/></svg>
            </div>
            <span style="font-size:0.875rem;font-weight:600;color:#374151;">{{ want }}</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Safety tips -->
    <div>
      <div style="background:#fff;border-radius:18px;border:1px solid #f0f0f0;box-shadow:0 4px 20px rgba(0,0,0,0.07);padding:24px 26px;position:sticky;top:90px;">
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:18px;padding-bottom:14px;border-bottom:1px solid #f3f4f6;">
          <div style="width:34px;height:34px;background:#fef3c7;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <svg width="17" height="17" fill="none" stroke="#d97706" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
          </div>
          <div>
            <h3 style="font-size:0.95rem;font-weight:800;color:#1a1a1a;margin:0;">Safety Tips</h3>
            <p style="font-size:0.75rem;color:#9ca3af;margin:2px 0 0;">Stay safe when swapping</p>
          </div>
        </div>
        <div style="display:flex;flex-direction:column;gap:12px;">
          <div v-for="(tip, i) in safetyTips" :key="i" style="display:flex;align-items:flex-start;gap:11px;">
            <div style="width:22px;height:22px;background:#149189;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-top:1px;">
              <svg width="10" height="10" fill="none" stroke="white" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            </div>
            <span style="font-size:0.83rem;color:#4b5563;line-height:1.55;">{{ tip }}</span>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- ══ GUEST MODAL ══ -->
  <Transition name="modal">
    <div v-if="showModal" style="position:fixed;inset:0;background:rgba(0,0,0,0.5);display:flex;align-items:center;justify-content:center;z-index:9999;padding:20px;backdrop-filter:blur(4px);" @click.self="showModal = false">
      <div style="background:#fff;border-radius:24px;padding:40px 36px;max-width:420px;width:100%;position:relative;box-shadow:0 24px 60px rgba(0,0,0,0.18);">
        <button @click="showModal = false" style="position:absolute;top:16px;right:16px;width:32px;height:32px;border:none;background:#f3f4f6;border-radius:50%;cursor:pointer;display:flex;align-items:center;justify-content:center;">
          <svg width="14" height="14" fill="none" stroke="#6b7280" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
        <h2 style="text-align:center;font-size:1.35rem;font-weight:800;color:#1a1a1a;margin:8px 0 10px;">Sign in to continue</h2>
        <p style="text-align:center;font-size:0.875rem;color:#6b7280;margin-bottom:24px;line-height:1.6;">You need an account to message swappers and make offers. Join SWAPY — it's free.</p>
        <a href="/login" style="display:block;width:100%;padding:15px;background:#ED730C;color:#fff;border-radius:12px;font-size:0.9rem;font-weight:800;text-align:center;text-decoration:none;margin-bottom:10px;box-sizing:border-box;font-family:'DM Sans',sans-serif;">Sign In</a>
        <a href="/register" style="display:block;width:100%;padding:15px;background:#fff;color:#ED730C;border:1.5px solid #ED730C;border-radius:12px;font-size:0.9rem;font-weight:700;text-align:center;text-decoration:none;box-sizing:border-box;font-family:'DM Sans',sans-serif;">Create Account</a>
      </div>
    </div>
  </Transition>

</div>
</template>

<style scoped>
.modal-enter-active, .modal-leave-active { transition: opacity 0.2s; }
.modal-enter-from, .modal-leave-to { opacity: 0; }
@media (max-width: 900px) {
  .detail-grid { grid-template-columns: 1fr !important; }
}
</style>
