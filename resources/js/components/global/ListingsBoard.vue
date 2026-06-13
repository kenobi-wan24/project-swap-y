<script setup>
import { ref, reactive, computed } from 'vue'
import { currency } from '../../constants/currency'

const props = defineProps({
  items:       { type: Array, default: () => [] },
  homes:       { type: Array, default: () => [] },
  garageSales: { type: Array, default: () => [] },
  services:    { type: Array, default: () => [] },
})

const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')

// Local reactive copies so boost/edit reflect immediately
const lists = reactive({
  items:       props.items.map(c => ({ ...c })),
  homes:       props.homes.map(c => ({ ...c })),
  garageSales: props.garageSales.map(c => ({ ...c })),
  services:    props.services.map(c => ({ ...c })),
})

const itemCats = ['Electronics', 'Clothing', 'Furniture', 'Books', 'Sports', 'Services', 'DIY', 'Art', 'Other']
const svcCats  = ['Design & Creative', 'Tech & Digital', 'Education & Tutoring', 'Home & Repair', 'Business', 'Creative']
const homeTypes = ['Swap', 'Rent', 'Sell', 'Co-living']

const groups = computed(() => [
  { key: 'items',       routeType: 'item',        label: 'Items',        single: 'Item',        icon: 'box',    color: '#ED730C', createUrl: '/items/create',       data: lists.items,       catLabel: 'Category',     catOptions: itemCats,  priceLabel: `Est. Value (${currency.value.symbol})` },
  { key: 'homes',       routeType: 'home',        label: 'Homes',        single: 'Home',        icon: 'home',   color: '#2563eb', createUrl: '/homes/create',       data: lists.homes,       catLabel: 'Listing Type', catOptions: homeTypes, priceLabel: `Price / Rent (${currency.value.symbol})` },
  { key: 'garageSales', routeType: 'garage-sale', label: 'Garage Sales', single: 'Garage Sale', icon: 'store',  color: '#149189', createUrl: '/garage-sale/create', data: lists.garageSales, catLabel: null,           catOptions: null,      priceLabel: null },
  { key: 'services',    routeType: 'service',     label: 'Services',     single: 'Service',     icon: 'wrench', color: '#8b5cf6', createUrl: '/services/create',    data: lists.services,    catLabel: 'Category',     catOptions: svcCats,   priceLabel: `Rate (${currency.value.symbol})` },
])

const total = computed(() => groups.value.reduce((n, g) => n + g.data.length, 0))

const activeTab = ref('all')
const visibleGroups = computed(() =>
  activeTab.value === 'all' ? groups.value : groups.value.filter(g => g.key === activeTab.value)
)

const statusCfg = {
  active: { label: 'Active', color: '#0e7c66', bg: '#e7f6f1' },
  paused: { label: 'Paused', color: '#6b7280', bg: '#f3f4f6' },
  draft:  { label: 'Draft',  color: '#b45309', bg: '#fef3c7' },
  ended:  { label: 'Ended',  color: '#6b7280', bg: '#f3f4f6' },
}
function status(s) { return statusCfg[s] || statusCfg.active }

// ── Boost / promote toggle ────────────────────────────────────────────────────
async function toggleBoost(card, group) {
  if (card._busy) return
  card._busy = true
  try {
    const res = await fetch(`/listings/${group.routeType}/${card.id}/promote`, {
      method: 'POST',
      headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf },
    })
    const data = await res.json()
    if (data.success) card.is_promoted = data.is_promoted
  } catch (e) { console.error('Boost failed', e) }
  finally { card._busy = false }
}

// ── Quick edit ────────────────────────────────────────────────────────────────
const editing   = ref(null)
const editGroup  = ref(null)
const saving     = ref(false)
const form = reactive({ title: '', description: '', status: 'active', price: '', category: '' })

function openEdit(card, group) {
  editing.value  = card
  editGroup.value = group
  form.title       = card.title ?? ''
  form.description = card.description ?? ''
  form.status      = card.status ?? 'active'
  form.price       = card.price ?? ''
  form.category    = card.category ?? ''
}
function closeEdit() { editing.value = null; editGroup.value = null }

async function saveEdit() {
  if (!editing.value || saving.value) return
  saving.value = true
  const g = editGroup.value
  try {
    const res = await fetch(`/listings/${g.routeType}/${editing.value.id}`, {
      method: 'PUT',
      headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf },
      body: JSON.stringify({
        title:       form.title,
        description: form.description,
        status:      form.status,
        price:       g.priceLabel ? (form.price === '' ? null : Number(form.price)) : null,
        category:    g.catOptions ? form.category : null,
      }),
    })
    const data = await res.json()
    if (data.success) {
      const c = editing.value
      c.title       = data.title
      c.status      = data.status
      c.category    = data.category
      c.price       = data.price
      c.price_label = data.price_label
      c.description = data.description
      closeEdit()
    }
  } catch (e) { console.error('Update failed', e) }
  finally { saving.value = false }
}
</script>

<template>
<div class="ml-board">

  <div class="ml-head-row">
    <div>
      <p class="ml-eyebrow">Everything you've posted</p>
      <h2 class="ml-h2">My Listings <span class="ml-total">{{ total }}</span></h2>
    </div>
  </div>

  <!-- Type filter tabs -->
  <div class="ml-tabs">
    <button :class="['ml-tab', { on: activeTab === 'all' }]" @click="activeTab = 'all'">
      All <span class="ml-tab-count">{{ total }}</span>
    </button>
    <button v-for="g in groups" :key="g.key" :class="['ml-tab', { on: activeTab === g.key }]" @click="activeTab = g.key">
      {{ g.label }} <span class="ml-tab-count">{{ g.data.length }}</span>
    </button>
  </div>

  <!-- Global empty -->
  <div v-if="total === 0" class="ml-empty-all">
    <div class="ml-empty-emoji">📂</div>
    <h3>No listings yet</h3>
    <p>Post your first item, home, garage sale, or service to see it here.</p>
    <div class="ml-empty-actions">
      <a v-for="g in groups" :key="g.key" :href="g.createUrl" class="ml-empty-btn" :style="{ background: g.color }">+ {{ g.single }}</a>
    </div>
  </div>

  <!-- Sections -->
  <template v-else>
    <section v-for="g in visibleGroups" :key="g.key" class="ml-section">
      <div class="ml-section-head">
        <div class="ml-section-title">
          <span class="ml-section-icon" :style="{ background: g.color + '14', color: g.color }">
            <svg v-if="g.icon==='box'"        width="16" height="16" fill="none" :stroke="g.color" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 10V11"/></svg>
            <svg v-else-if="g.icon==='home'"  width="16" height="16" fill="none" :stroke="g.color" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 9.5L12 3l9 6.5V20a1 1 0 01-1 1h-5v-6H9v6H4a1 1 0 01-1-1V9.5z"/></svg>
            <svg v-else-if="g.icon==='store'" width="16" height="16" fill="none" :stroke="g.color" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 9l1-5h16l1 5v1a2 2 0 01-2 2 2 2 0 01-2-2 2 2 0 01-2 2 2 2 0 01-2-2 2 2 0 01-2 2 2 2 0 01-2-2 2 2 0 01-2 2 2 2 0 01-2-2V9zm2 5v6h14v-6"/></svg>
            <svg v-else width="16" height="16" fill="none" :stroke="g.color" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.7 6.3a1 1 0 000 1.4l1.6 1.6a1 1 0 001.4 0l3.77-3.77a6 6 0 01-7.94 7.94l-6.91 6.91a2.12 2.12 0 01-3-3l6.91-6.91a6 6 0 017.94-7.94l-3.76 3.76z"/></svg>
          </span>
          <h3>{{ g.label }}</h3>
          <span class="ml-count">{{ g.data.length }}</span>
        </div>
        <a :href="g.createUrl" class="ml-add">
          <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" d="M12 5v14M5 12h14"/></svg>
          Add {{ g.single }}
        </a>
      </div>

      <!-- Cards -->
      <div v-if="g.data.length" class="ml-grid">
        <div v-for="card in g.data" :key="g.key + '-' + card.id" class="ml-card">
          <a :href="card.url" class="ml-card-link">
            <div class="ml-thumb">
              <img v-if="card.image" :src="card.image" :alt="card.title" loading="lazy">
              <div v-else class="ml-noimg" :style="{ color: g.color }">
                <svg width="26" height="26" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="M21 15l-5-5L5 21"/></svg>
              </div>
              <span v-if="card.is_promoted" class="ml-promoted"><svg width="9" height="9" viewBox="0 0 24 24" fill="#fff"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>Promoted</span>
              <span class="ml-status" :style="{ color: status(card.status).color, background: status(card.status).bg }">{{ status(card.status).label }}</span>
            </div>
            <div class="ml-card-body">
              <span class="ml-cat">{{ card.category }}</span>
              <h4 class="ml-title">{{ card.title }}</h4>
              <p v-if="card.meta" class="ml-meta">{{ card.meta }}</p>
              <div class="ml-foot">
                <span class="ml-price">{{ card.price_label || '—' }}</span>
                <span class="ml-date">{{ card.created_at }}</span>
              </div>
            </div>
          </a>

          <!-- Actions -->
          <div class="ml-actions">
            <button class="ml-edit-btn" @click="openEdit(card, g)">
              <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7M18.5 2.5a2.12 2.12 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
              Edit
            </button>
            <button class="ml-boost-btn" :class="{ on: card.is_promoted }" :disabled="card._busy" @click="toggleBoost(card, g)">
              <svg v-if="card.is_promoted" width="13" height="13" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
              <svg v-else width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/></svg>
              {{ card._busy ? '…' : (card.is_promoted ? 'Promoted' : 'Boost') }}
            </button>
          </div>
        </div>
      </div>

      <!-- Per-section empty -->
      <div v-else class="ml-empty">
        <p>No {{ g.label.toLowerCase() }} yet.</p>
        <a :href="g.createUrl" class="ml-empty-link" :style="{ color: g.color }">Create your first {{ g.single.toLowerCase() }} →</a>
      </div>
    </section>
  </template>

  <!-- ── Quick edit modal ── -->
  <div v-if="editing" class="ml-modal-backdrop" @click.self="closeEdit">
    <div class="ml-modal" role="dialog" aria-modal="true">
      <div class="ml-modal-head">
        <h3>Edit {{ editGroup.single }}</h3>
        <button class="ml-modal-x" @click="closeEdit" aria-label="Close">
          <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
      </div>
      <div class="ml-modal-body">
        <label class="ml-label">Title</label>
        <input v-model="form.title" class="ml-input" placeholder="Listing title">

        <div v-if="editGroup.catOptions || editGroup.priceLabel" class="ml-modal-row">
          <div v-if="editGroup.catOptions">
            <label class="ml-label">{{ editGroup.catLabel }}</label>
            <select v-model="form.category" class="ml-input">
              <option v-for="c in editGroup.catOptions" :key="c" :value="c">{{ c }}</option>
            </select>
          </div>
          <div v-if="editGroup.priceLabel">
            <label class="ml-label">{{ editGroup.priceLabel }}</label>
            <input v-model="form.price" type="number" min="0" class="ml-input" placeholder="0">
          </div>
        </div>

        <label class="ml-label">Status</label>
        <select v-model="form.status" class="ml-input">
          <option value="active">Active</option>
          <option value="paused">Paused</option>
          <option value="ended">Ended</option>
        </select>

        <label class="ml-label">Description</label>
        <textarea v-model="form.description" rows="3" class="ml-input" placeholder="Optional description"></textarea>
      </div>
      <div class="ml-modal-foot">
        <button class="ml-btn-ghost" @click="closeEdit">Cancel</button>
        <button class="ml-btn-primary" :disabled="saving || !form.title.trim()" @click="saveEdit">{{ saving ? 'Saving…' : 'Save Changes' }}</button>
      </div>
    </div>
  </div>
</div>
</template>

<style scoped>
.ml-board { font-family: 'DM Sans', sans-serif; }

.ml-head-row { margin-bottom: 18px; }
.ml-eyebrow { font-size: 0.68rem; font-weight: 800; letter-spacing: .12em; color: #9ca3af; text-transform: uppercase; margin: 0 0 4px; }
.ml-h2 { font-size: 1.5rem; font-weight: 900; color: #1A1A1A; margin: 0; letter-spacing: -.02em; display: flex; align-items: center; gap: 10px; }
.ml-total { font-size: 0.75rem; font-weight: 800; background: #FFF4EC; color: #ED730C; border-radius: 999px; padding: 3px 11px; }

/* Tabs */
.ml-tabs { display: flex; gap: 4px; overflow-x: auto; scrollbar-width: none; border-bottom: 1px solid #EDE8E0; margin-bottom: 26px; }
.ml-tabs::-webkit-scrollbar { display: none; }
.ml-tab { flex-shrink: 0; display: inline-flex; align-items: center; gap: 7px; padding: 11px 14px; background: none; border: none; border-bottom: 2.5px solid transparent; font-family: 'DM Sans', sans-serif; font-size: 0.85rem; font-weight: 700; color: #9ca3af; cursor: pointer; white-space: nowrap; transition: color .15s; }
.ml-tab:hover { color: #1A1A1A; }
.ml-tab.on { color: #ED730C; border-bottom-color: #ED730C; }
.ml-tab-count { font-size: 0.72rem; font-weight: 800; background: #f3f0ec; color: #6b7280; border-radius: 999px; padding: 1px 8px; min-width: 20px; text-align: center; }
.ml-tab.on .ml-tab-count { background: #FFF4EC; color: #ED730C; }

/* Sections */
.ml-section { margin-bottom: 38px; }
.ml-section-head { display: flex; align-items: center; justify-content: space-between; gap: 12px; margin-bottom: 16px; }
.ml-section-title { display: flex; align-items: center; gap: 10px; }
.ml-section-icon { width: 32px; height: 32px; border-radius: 9px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.ml-section-title h3 { font-size: 1.05rem; font-weight: 900; color: #1A1A1A; margin: 0; letter-spacing: -.01em; }
.ml-count { font-size: 0.72rem; font-weight: 800; color: #9ca3af; background: #f3f0ec; border-radius: 999px; padding: 2px 9px; }
.ml-add { display: inline-flex; align-items: center; gap: 5px; font-size: 0.8rem; font-weight: 800; color: #1A1A1A; text-decoration: none; border: 1.5px solid #EDE8E0; border-radius: 999px; padding: 7px 15px; white-space: nowrap; transition: border-color .15s, background .15s; }
.ml-add:hover { border-color: #ED730C; background: #FFF4EC; color: #ED730C; }

/* Grid + cards */
.ml-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(min(240px, 100%), 1fr)); gap: 18px; }
.ml-card { display: flex; flex-direction: column; background: #fff; border: 1px solid #EDE8E0; border-radius: 16px; overflow: hidden; transition: border-color .2s, box-shadow .2s, transform .2s; }
.ml-card:hover { border-color: #ED730C; box-shadow: 0 10px 30px rgba(237,115,12,0.10); transform: translateY(-2px); }
.ml-card:hover .ml-thumb img { transform: scale(1.04); }
.ml-card-link { text-decoration: none; color: inherit; display: flex; flex-direction: column; flex: 1; }

.ml-thumb { position: relative; aspect-ratio: 16/10; overflow: hidden; background: #f7f5f2; flex-shrink: 0; }
.ml-thumb img { width: 100%; height: 100%; object-fit: cover; transition: transform .4s; }
.ml-noimg { width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; opacity: .5; }
.ml-status { position: absolute; top: 10px; right: 10px; font-size: 0.6rem; font-weight: 800; padding: 4px 9px; border-radius: 999px; letter-spacing: .04em; text-transform: uppercase; }
.ml-promoted { position: absolute; top: 10px; left: 10px; display: flex; align-items: center; gap: 3px; background: #1A1A1A; color: #fff; font-size: 0.55rem; font-weight: 800; padding: 4px 8px; border-radius: 999px; letter-spacing: .04em; text-transform: uppercase; }

.ml-card-body { padding: 13px 15px 13px; display: flex; flex-direction: column; flex: 1; }
.ml-cat { font-size: 0.6rem; font-weight: 800; letter-spacing: .08em; text-transform: uppercase; color: #9ca3af; margin-bottom: 5px; }
.ml-title { font-size: 0.92rem; font-weight: 800; color: #1A1A1A; line-height: 1.3; margin: 0 0 5px; letter-spacing: -.01em; display: -webkit-box; -webkit-line-clamp: 2; line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
.ml-meta { font-size: 0.78rem; color: #6b7280; font-weight: 600; margin: 0 0 12px; }
.ml-foot { display: flex; align-items: center; justify-content: space-between; gap: 8px; margin-top: auto; padding-top: 11px; border-top: 1px solid #f3f0ec; }
.ml-price { font-size: 0.85rem; font-weight: 900; color: #1A1A1A; }
.ml-date { font-size: 0.7rem; color: #9ca3af; font-weight: 600; white-space: nowrap; }

/* Card actions */
.ml-actions { display: flex; gap: 8px; padding: 10px 14px 13px; border-top: 1px solid #f3f0ec; }
.ml-edit-btn, .ml-boost-btn { flex: 1; display: inline-flex; align-items: center; justify-content: center; gap: 5px; padding: 9px 10px; border-radius: 10px; font-family: 'DM Sans', sans-serif; font-size: 0.78rem; font-weight: 800; cursor: pointer; transition: all .15s; }
.ml-edit-btn { background: #fff; color: #1A1A1A; border: 1.5px solid #EDE8E0; }
.ml-edit-btn:hover { border-color: #1A1A1A; }
.ml-boost-btn { border: none; background: #ED730C; color: #fff; }
.ml-boost-btn:hover { background: #d4620a; }
.ml-boost-btn.on { background: #1A1A1A; }
.ml-boost-btn.on:hover { background: #000; }
.ml-boost-btn:disabled { opacity: .6; cursor: default; }

/* Empties */
.ml-empty { border: 1.5px dashed #EDE8E0; border-radius: 16px; padding: 32px; text-align: center; }
.ml-empty p { font-size: 0.875rem; color: #9ca3af; font-weight: 600; margin: 0 0 6px; }
.ml-empty-link { font-size: 0.85rem; font-weight: 800; text-decoration: none; }
.ml-empty-link:hover { text-decoration: underline; }
.ml-empty-all { text-align: center; padding: 60px 24px; }
.ml-empty-emoji { font-size: 3rem; margin-bottom: 14px; }
.ml-empty-all h3 { font-size: 1.25rem; font-weight: 900; color: #1A1A1A; margin: 0 0 8px; }
.ml-empty-all p { font-size: 0.9rem; color: #9ca3af; margin: 0 0 24px; }
.ml-empty-actions { display: flex; flex-wrap: wrap; gap: 10px; justify-content: center; }
.ml-empty-btn { color: #fff; font-size: 0.82rem; font-weight: 800; padding: 11px 20px; border-radius: 999px; text-decoration: none; }

/* Modal */
.ml-modal-backdrop { position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1000; display: flex; align-items: center; justify-content: center; padding: 20px; }
.ml-modal { background: #fff; border-radius: 20px; width: 100%; max-width: 440px; max-height: 90vh; overflow-y: auto; box-shadow: 0 20px 60px rgba(0,0,0,0.25); }
.ml-modal-head { display: flex; align-items: center; justify-content: space-between; padding: 20px 22px 14px; border-bottom: 1px solid #EDE8E0; }
.ml-modal-head h3 { font-size: 1.1rem; font-weight: 900; color: #1A1A1A; margin: 0; }
.ml-modal-x { background: none; border: none; color: #9ca3af; cursor: pointer; padding: 2px; display: flex; }
.ml-modal-x:hover { color: #1A1A1A; }
.ml-modal-body { padding: 18px 22px; }
.ml-modal-row { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
.ml-label { display: block; font-size: 0.65rem; font-weight: 800; letter-spacing: .06em; text-transform: uppercase; color: #6b7280; margin: 0 0 6px; }
.ml-input { width: 100%; padding: 10px 12px; border: 1.5px solid #EDE8E0; border-radius: 10px; font-size: 0.88rem; font-family: 'DM Sans', sans-serif; color: #1A1A1A; background: #fff; box-sizing: border-box; outline: none; margin-bottom: 14px; }
.ml-input:focus-visible { outline: 2px solid #ED730C; outline-offset: 1px; border-color: #ED730C; }
textarea.ml-input { resize: vertical; }
.ml-modal-foot { display: flex; gap: 10px; justify-content: flex-end; padding: 14px 22px 20px; border-top: 1px solid #EDE8E0; }
.ml-btn-ghost { padding: 10px 18px; background: #fff; color: #6b7280; border: 1.5px solid #EDE8E0; border-radius: 10px; font-family: 'DM Sans', sans-serif; font-size: 0.85rem; font-weight: 700; cursor: pointer; }
.ml-btn-ghost:hover { border-color: #1A1A1A; color: #1A1A1A; }
.ml-btn-primary { padding: 10px 22px; background: #ED730C; color: #fff; border: none; border-radius: 10px; font-family: 'DM Sans', sans-serif; font-size: 0.85rem; font-weight: 800; cursor: pointer; }
.ml-btn-primary:hover { background: #d4620a; }
.ml-btn-primary:disabled { opacity: .55; cursor: default; }

@media (max-width: 768px) {
  .ml-grid { grid-template-columns: repeat(auto-fill, minmax(min(160px, 100%), 1fr)); gap: 12px; }
}

@media (max-width: 480px) {
  .ml-modal-backdrop { padding: 14px; }
  .ml-modal-row { grid-template-columns: 1fr; gap: 0; }
  .ml-modal-foot { flex-direction: column-reverse; }
  .ml-modal-foot .ml-btn-ghost,
  .ml-modal-foot .ml-btn-primary { width: 100%; padding-top: 13px; padding-bottom: 13px; }
}
</style>
