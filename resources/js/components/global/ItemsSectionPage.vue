<script setup>
import { ref, computed, watch, onMounted, onBeforeUnmount, nextTick } from 'vue'
import { formatMoney } from '../../constants/currency'
import L from 'leaflet'
import 'leaflet/dist/leaflet.css'
import { ITEM_CATEGORIES } from '../../constants/categories'

const el = document.getElementById('items-section-app')

function parseDataset(key) {
  try { return JSON.parse(el?.dataset[key] || '[]') } catch { return [] }
}

// ── Sections ───────────────────────────────────────────────────────────────────
const SECTIONS = {
  'featured': { title: 'Top Featured Swaps', sub: 'Promoted listings from our community' },
  'popular':  { title: 'Popular in Your City', sub: 'Most viewed & matched swaps in your city' },
  'nearby':   { title: 'Available Nearby', sub: 'Active swaps from a nearby city' },
  'near-you': { title: 'Swaps Near You', sub: 'Items closest to your location' },
}

const sectionKey = ref(SECTIONS[el?.dataset.section] ? el.dataset.section : 'featured')
const section    = computed(() => SECTIONS[sectionKey.value])

const showSectionDropdown = ref(false)

function switchSection(key) {
  showSectionDropdown.value = false
  if (key !== sectionKey.value) window.location.href = `/items/section/${key}`
}

// ── Data (real listings from the server) ──────────────────────────────────────
const allItems = ref(parseDataset('listings'))
// Every item with coords drives the map (independent of the card filter), so
// panning reveals listings everywhere — still items-only on the items page.
const mapPool  = ref(parseDataset('mapListings'))
const userPos  = ref(null)

function milesBetween(lat1, lng1, lat2, lng2) {
  const toRad = d => (d * Math.PI) / 180
  const R = 3958.8 // miles
  const a = Math.sin(toRad(lat2 - lat1) / 2) ** 2 +
            Math.cos(toRad(lat1)) * Math.cos(toRad(lat2)) * Math.sin(toRad(lng2 - lng1) / 2) ** 2
  return 2 * R * Math.asin(Math.sqrt(a))
}
function withDistance(list, lat, lng) {
  return list.map(i => ({
    ...i,
    distance: i.latitude && i.longitude ? milesBetween(lat, lng, i.latitude, i.longitude).toFixed(1) : null,
  }))
}

// Real distances + city-first map centering once the visitor shares position.
onMounted(() => {
  navigator.geolocation?.getCurrentPosition(pos => {
    const { latitude, longitude } = pos.coords
    userPos.value  = [latitude, longitude]
    allItems.value = withDistance(allItems.value, latitude, longitude)
    mapPool.value  = withDistance(mapPool.value, latitude, longitude)
    map?.setView(userPos.value, 12)
  }, () => {}, { timeout: 5000 })
})

// ── Filters / sort / view ─────────────────────────────────────────────────────
const categories  = ['All', ...ITEM_CATEGORIES]
const conditions  = ['New', 'Like New', 'Good', 'Fair']
const sortOptions = ['Best match', 'Recent first', 'Value: Low to High', 'Value: High to Low']

const activeCat   = ref('All')
const valueMax    = ref(5000)
const activeConds = ref(new Set())
const sortBy      = ref('Best match')
const viewMode    = ref('split')

const showFiltersPanel = ref(false)
const showSortDropdown = ref(false)

function closeAllPanels() {
  showSectionDropdown.value = false
  showFiltersPanel.value    = false
  showSortDropdown.value    = false
}

function toggleCondition(c) {
  const s = new Set(activeConds.value)
  s.has(c) ? s.delete(c) : s.add(c)
  activeConds.value = s
}

const activeFilterCount = computed(() =>
  (valueMax.value < 5000 ? 1 : 0) + activeConds.value.size
)

function clearFilters() {
  activeCat.value   = 'All'
  valueMax.value    = 5000
  activeConds.value = new Set()
}

const filtered = computed(() => {
  let list = [...allItems.value]
  if (activeCat.value !== 'All')  list = list.filter(i => i.category === activeCat.value)
  if (activeConds.value.size)     list = list.filter(i => activeConds.value.has(i.condition))
  list = list.filter(i => (i.value || 0) <= valueMax.value)
  if (sortBy.value === 'Best match')         list.sort((a, b) => (b.match || 0) - (a.match || 0))
  if (sortBy.value === 'Value: Low to High') list.sort((a, b) => (a.value || 0) - (b.value || 0))
  if (sortBy.value === 'Value: High to Low') list.sort((a, b) => (b.value || 0) - (a.value || 0))
  // Promoted listings get sponsored placement — pinned above the rest.
  return [...list.filter(i => i.promoted), ...list.filter(i => !i.promoted)]
})

// ── Wishlist ──────────────────────────────────────────────────────────────────
const wishlisted = ref(new Set())
function toggleWish(id) {
  const s = new Set(wishlisted.value)
  s.has(id) ? s.delete(id) : s.add(id)
  wishlisted.value = s
}

// ── Real map (Leaflet + OpenStreetMap) ────────────────────────────────────────
const mapEl = ref(null)
let map = null
let markerLayer = null

// General Santos City — fallback center when no listing has coordinates
const DEFAULT_CENTER = [6.1164, 125.1716]

const mappable = computed(() => mapPool.value.filter(i => i.latitude && i.longitude))

function escapeHtml(s) {
  return String(s ?? '').replace(/[&<>"']/g, c => (
    { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]
  ))
}

// Pins show the personal Match % — 0% included, so every scored listing
// reads consistently. Unscored listings (guest viewers, your own items)
// get a small neutral dot instead.
function pinIcon(item) {
  if (item.promoted) {
    const label = item.match != null ? `${item.match}% Match` : 'Promoted'
    return L.divIcon({
      className: 'swapy-pin-wrap',
      html: `<div class="swapy-pin swapy-pin--promoted"><svg width="11" height="11" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>${label}</div>`,
      iconSize: [0, 0],
    })
  }
  if (item.match == null) {
    return L.divIcon({
      className: 'swapy-pin-wrap',
      html: '<div class="swapy-pin swapy-pin--dot"></div>',
      iconSize: [0, 0],
    })
  }
  return L.divIcon({
    className: 'swapy-pin-wrap',
    html: `<div class="swapy-pin"><svg width="10" height="10" fill="currentColor" viewBox="0 0 24 24"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>${item.match}% Match</div>`,
    iconSize: [0, 0],
  })
}

function popupHtml(item) {
  const img = item.image
    ? `<img src="${escapeHtml(item.image)}" alt="">`
    : ''
  const dist = item.distance ? ` &middot; ${escapeHtml(item.distance)} mi away` : ''
  return `
    <div class="swapy-pop">
      <div class="swapy-pop-img">${img}</div>
      <div class="swapy-pop-body">
        <h4>${escapeHtml(item.title)}</h4>
        <p>Swap for <strong>${escapeHtml(item.wants || 'open offers')}</strong>${dist}</p>
        <a href="/item/${escapeHtml(item.id)}">View swap</a>
      </div>
    </div>`
}

function renderMarkers() {
  if (!map) return
  markerLayer.clearLayers()
  mappable.value.forEach(item => {
    const marker = L.marker([item.latitude, item.longitude], { icon: pinIcon(item), zIndexOffset: item.promoted ? 1000 : 0 })
      .bindPopup(popupHtml(item), {
        className: 'swapy-popup',
        closeButton: true,
        offset: [0, -18],
        maxWidth: 280,
      })
    marker.on('popupopen',  () => marker.getElement()?.querySelector('.swapy-pin')?.classList.add('active'))
    marker.on('popupclose', () => marker.getElement()?.querySelector('.swapy-pin')?.classList.remove('active'))
    markerLayer.addLayer(marker)
  })
}

// Start on the viewer's city when we know it; otherwise frame this section's
// own listings. The full pool stays on the map to pan through.
function setInitialView() {
  if (!map) return
  if (userPos.value) { map.setView(userPos.value, 12); return }
  const pts = filtered.value.filter(i => i.latitude && i.longitude).map(i => [i.latitude, i.longitude])
  if (pts.length) map.fitBounds(pts, { padding: [56, 56], maxZoom: 12 })
  else if (mappable.value.length) map.fitBounds(mappable.value.map(i => [i.latitude, i.longitude]), { padding: [56, 56], maxZoom: 11 })
  else map.setView(DEFAULT_CENTER, 12)
}

function initMap() {
  if (map || !mapEl.value) return
  map = L.map(mapEl.value, { zoomControl: false, scrollWheelZoom: true })
  L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
  }).addTo(map)
  markerLayer = L.layerGroup().addTo(map)
  renderMarkers()
  setInitialView()
}

onMounted(() => nextTick(initMap))
onBeforeUnmount(() => { map?.remove(); map = null })

watch(mapPool, () => renderMarkers())
watch(viewMode, async () => {
  await nextTick()
  map?.invalidateSize()
})
</script>

<template>
<div class="section-page" @click="closeAllPanels">

  <!-- ═══ HEADER ═══ -->
  <div class="page-head">
    <div class="head-inner">

      <!-- breadcrumb -->
      <div class="crumb-row">
        <a href="/items" class="crumb-back">
          <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
          Items
        </a>
        <span class="crumb-sep">/</span>
        <span class="crumb-current">{{ section.title }}</span>
      </div>

      <div class="title-row">
        <div>
          <div class="title-wrap" @click.stop>
            <h1 class="page-title">{{ section.title }}</h1>
            <button class="title-caret" @click="showSectionDropdown = !showSectionDropdown; showFiltersPanel = false; showSortDropdown = false">
              <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" :style="{transform: showSectionDropdown ? 'rotate(180deg)' : '', transition:'transform .15s'}"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
            </button>

            <!-- section switcher -->
            <div v-if="showSectionDropdown" class="section-dropdown">
              <button v-for="(meta, key) in SECTIONS" :key="key"
                :class="['section-option', { active: key === sectionKey }]"
                @click="switchSection(key)">
                <span :class="['section-dot', { active: key === sectionKey }]"></span>
                {{ meta.title }}
              </button>
            </div>
          </div>
          <p class="page-sub">{{ section.sub }} <span class="sub-dot">·</span> <strong>{{ filtered.length }} of {{ allItems.length }} swaps</strong></p>
        </div>

        <!-- view toggle -->
        <div class="view-seg">
          <button :class="['seg-btn', { active: viewMode === 'split' }]" @click="viewMode = 'split'">Split</button>
          <button :class="['seg-btn', { active: viewMode === 'grid'  }]" @click="viewMode = 'grid'">Grid</button>
          <button :class="['seg-btn', { active: viewMode === 'map'   }]" @click="viewMode = 'map'">Map</button>
        </div>
      </div>

      <!-- chips + filters + sort -->
      <div class="tools-row" @click.stop>
        <div class="chips-scroll">
          <button v-for="cat in categories" :key="cat"
            :class="['cat-chip', { active: activeCat === cat }]"
            @click="activeCat = cat">{{ cat }}</button>
        </div>
        <div class="tools-right">
          <div style="position:relative;">
            <button class="tool-btn" @click="showFiltersPanel = !showFiltersPanel; showSortDropdown = false">
              <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 4h18M7 12h10M10 20h4"/></svg>
              Filters
              <span v-if="activeFilterCount" class="filter-count">{{ activeFilterCount }}</span>
            </button>
            <div v-if="showFiltersPanel" class="panel filters-panel">
              <p class="panel-label">Max value · {{ formatMoney(valueMax) }}</p>
              <input type="range" v-model.number="valueMax" min="50" max="5000" step="50" class="value-slider">
              <p class="panel-label" style="margin-top:16px;">Condition</p>
              <div class="cond-chips">
                <button v-for="c in conditions" :key="c"
                  :class="['cond-chip', { active: activeConds.has(c) }]"
                  @click="toggleCondition(c)">{{ c }}</button>
              </div>
              <button class="panel-clear" @click="clearFilters">Clear all</button>
            </div>
          </div>
          <div style="position:relative;">
            <button class="tool-btn" @click="showSortDropdown = !showSortDropdown; showFiltersPanel = false">
              <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 7h13M3 12h9m-9 5h6m7-9l3-3m0 0l3 3m-3-3v12"/></svg>
              Sort: <strong>{{ sortBy }}</strong>
            </button>
            <div v-if="showSortDropdown" class="panel sort-panel">
              <button v-for="s in sortOptions" :key="s"
                :class="['sort-option', { active: sortBy === s }]"
                @click="sortBy = s; showSortDropdown = false">{{ s }}</button>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>

  <!-- ═══ CONTENT ═══ -->
  <div class="content-inner">

    <!-- empty state -->
    <div v-if="filtered.length === 0" class="empty-state">
      <svg width="52" height="52" fill="none" stroke="#d1d5db" stroke-width="1.5" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path stroke-linecap="round" d="M21 21l-4.35-4.35"/></svg>
      <h3>No swaps found</h3>
      <p>Try adjusting your filters.</p>
      <button class="clear-btn" @click="clearFilters">Clear Filters</button>
    </div>

    <div :class="['content-wrap', viewMode]" v-show="filtered.length > 0">

      <!-- cards -->
      <div v-if="viewMode !== 'map'" :class="viewMode === 'split' ? 'cards-split' : 'cards-grid'">
        <div v-for="item in filtered" :key="item.id" class="swapy-card">
          <a :href="'/item/' + item.id" class="card-link">
            <div class="card-img-wrap">
              <img v-if="item.image" :src="item.image" :alt="item.title" class="card-img">
              <div v-else class="card-img-placeholder">[ {{ item.title }} ]</div>
              <span class="cat-pill">{{ item.category }}</span>
              <span v-if="item.match != null" class="match-pill">{{ item.match }}% Match</span>
              <button class="wish-btn" @click.prevent.stop="toggleWish(item.id)">
                <svg :class="['wish-icon', { wishlisted: wishlisted.has(item.id) }]" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
              </button>
            </div>
            <div class="card-body">
              <h3 class="card-title">{{ item.title }}</h3>
              <p class="card-wants">
                <svg width="12" height="12" fill="none" stroke="#149189" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 16V4m0 0L3 8m4-4l4 4m6 4v12m0 0l4-4m-4 4l-4-4"/></svg>
                Swap for <span class="wants-value">{{ item.wants || 'open to offers' }}</span>
              </p>
              <div class="card-value-row">
                <span class="card-meta">{{ item.location || (item.distance + ' mi away') }}</span>
                <p class="card-value">{{ formatMoney(item.value) }}</p>
              </div>
            </div>
          </a>
        </div>
      </div>

      <!-- real map -->
      <div v-show="viewMode !== 'grid'" class="map-panel" @click.stop>
        <div class="map-canvas">
          <div ref="mapEl" class="map-el"></div>
          <div class="map-count"><span class="count-dot"></span>{{ mappable.length }} on map</div>
          <div class="map-zoom">
            <button @click="map?.zoomIn()">+</button>
            <button @click="map?.zoomOut()">−</button>
          </div>
        </div>
      </div>

    </div>
  </div>

</div>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700;800;900&display=swap');

.section-page { min-height: 100vh; background: #fff; font-family: 'DM Sans', sans-serif; }

/* ── Header ── */
.page-head  { background: #fff; border-bottom: 1px solid #EBEBEB; }
.head-inner { max-width: 1680px; margin: 0 auto; padding: 20px 40px 0; }

.crumb-row     { display: flex; align-items: center; gap: 8px; font-size: 0.85rem; margin-bottom: 10px; }
.crumb-back    { display: inline-flex; align-items: center; gap: 4px; color: #6b7280; text-decoration: none; font-weight: 600; }
.crumb-back:hover { color: #ED730C; }
.crumb-sep     { color: #d1d5db; }
.crumb-current { color: #1A1A1A; font-weight: 700; }

.title-row  { display: flex; align-items: flex-start; justify-content: space-between; gap: 16px; flex-wrap: wrap; }
.title-wrap { position: relative; display: inline-flex; align-items: center; gap: 10px; }
.page-title { font-size: clamp(1.6rem, 3.4vw, 2.6rem); font-weight: 900; letter-spacing: -.03em; color: #1A1A1A; margin: 0; line-height: 1.05; }
.title-caret {
  width: 36px; height: 36px; border-radius: 12px; border: 1px solid #EBEBEB;
  background: #faf7f3; color: #1A1A1A; cursor: pointer;
  display: flex; align-items: center; justify-content: center; flex-shrink: 0;
}
.title-caret:hover { border-color: #1A1A1A; }
.page-sub  { font-size: 0.85rem; color: #9ca3af; font-weight: 500; margin: 6px 0 0; }
.page-sub strong { color: #6b7280; font-weight: 700; }
.sub-dot   { margin: 0 4px; }

/* section switcher dropdown */
.section-dropdown {
  position: absolute; top: calc(100% + 12px); left: 0; z-index: 1200;
  background: #fff; border: 1px solid #EBEBEB; border-radius: 18px;
  padding: 8px; min-width: 280px; box-shadow: 0 16px 48px rgba(0,0,0,0.14);
}
.section-option {
  display: flex; align-items: center; gap: 12px; width: 100%;
  padding: 13px 16px; border: none; border-radius: 12px; background: transparent;
  font-family: 'DM Sans', sans-serif; font-size: 0.95rem; font-weight: 700;
  color: #1A1A1A; cursor: pointer; text-align: left; transition: background .1s;
}
.section-option:hover  { background: #f9f9f9; }
.section-option.active { background: #fff4ec; color: #ED730C; }
.section-dot {
  width: 9px; height: 9px; border-radius: 50%; background: #d1d5db; flex-shrink: 0;
}
.section-dot.active { background: #ED730C; }

/* view segmented control */
.view-seg {
  display: inline-flex; background: #f1ece6; border-radius: 14px; padding: 4px; gap: 2px; margin-top: 4px;
}
.seg-btn {
  padding: 9px 22px; border: none; border-radius: 11px; background: transparent;
  font-family: 'DM Sans', sans-serif; font-size: 0.88rem; font-weight: 700;
  color: #6b7280; cursor: pointer; transition: all .15s;
}
.seg-btn.active { background: #fff; color: #1A1A1A; font-weight: 800; box-shadow: 0 2px 8px rgba(0,0,0,0.08); }

/* tools row */
.tools-row { display: flex; align-items: center; justify-content: space-between; gap: 14px; padding: 18px 0 16px; }
.chips-scroll {
  display: flex; gap: 8px; overflow-x: auto; scrollbar-width: none; -webkit-overflow-scrolling: touch; flex: 1;
}
.chips-scroll::-webkit-scrollbar { display: none; }
.cat-chip {
  padding: 9px 20px; border-radius: 999px; border: 1px solid #EBEBEB; background: #fff;
  font-family: 'DM Sans', sans-serif; font-size: 0.85rem; font-weight: 600; color: #4b5563;
  cursor: pointer; white-space: nowrap; transition: all .15s;
}
.cat-chip:hover  { border-color: #1A1A1A; }
.cat-chip.active { background: #1A1A1A; color: #fff; border-color: #1A1A1A; }
.tools-right { display: flex; gap: 10px; flex-shrink: 0; }
.tool-btn {
  display: flex; align-items: center; gap: 7px; padding: 10px 18px;
  border-radius: 999px; border: 1.5px solid #e2ddd8; background: #fff;
  font-family: 'DM Sans', sans-serif; font-size: 0.85rem; font-weight: 600;
  color: #1A1A1A; cursor: pointer; white-space: nowrap; transition: border-color .15s;
}
.tool-btn:hover  { border-color: #1A1A1A; }
.tool-btn strong { font-weight: 800; }
.filter-count {
  background: #ED730C; color: #fff; font-size: 0.68rem; font-weight: 800;
  min-width: 17px; height: 17px; border-radius: 999px;
  display: inline-flex; align-items: center; justify-content: center; padding: 0 4px;
}

/* dropdown panels */
.panel {
  position: absolute; top: calc(100% + 10px); right: 0; z-index: 1200;
  background: #fff; border: 1px solid #EBEBEB; border-radius: 16px;
  box-shadow: 0 12px 40px rgba(0,0,0,0.12);
}
.filters-panel { padding: 18px; width: 260px; }
.panel-label   { font-size: 0.72rem; font-weight: 800; letter-spacing: .05em; text-transform: uppercase; color: #9ca3af; margin: 0 0 10px; }
.value-slider  { width: 100%; accent-color: #ED730C; cursor: pointer; }
.cond-chips    { display: flex; flex-wrap: wrap; gap: 6px; }
.cond-chip {
  padding: 7px 14px; border-radius: 999px; border: 1px solid #EBEBEB; background: #fff;
  font-family: 'DM Sans', sans-serif; font-size: 0.78rem; font-weight: 600; color: #4b5563; cursor: pointer;
}
.cond-chip.active { background: #fff4ec; border-color: #ED730C; color: #ED730C; font-weight: 700; }
.panel-clear {
  margin-top: 16px; width: 100%; padding: 10px; border: none; border-radius: 10px;
  background: #f3f4f6; color: #1A1A1A; font-family: 'DM Sans', sans-serif;
  font-size: 0.8rem; font-weight: 700; cursor: pointer;
}
.panel-clear:hover { background: #e5e7eb; }
.sort-panel  { padding: 6px; min-width: 210px; }
.sort-option {
  display: block; width: 100%; text-align: left; padding: 10px 14px;
  border: none; border-radius: 10px; background: transparent;
  font-family: 'DM Sans', sans-serif; font-size: 0.85rem; font-weight: 500;
  color: #1A1A1A; cursor: pointer;
}
.sort-option:hover  { background: #f9f9f9; }
.sort-option.active { background: #fff4ec; color: #ED730C; font-weight: 700; }

/* ── Content ── */
.content-inner { max-width: 1680px; margin: 0 auto; padding: 24px 40px 72px; }

.content-wrap.split {
  display: grid; grid-template-columns: minmax(0, 1.15fr) minmax(0, 1fr); gap: 20px; align-items: start;
}

.cards-split { display: grid; grid-template-columns: repeat(2, 1fr); gap: 16px; }
.cards-grid  { display: grid; gap: 16px; }

/* ── Card (matches Items page design) ── */
.swapy-card {
  background: #fff; border-radius: 16px; overflow: hidden; border: 1px solid #EBEBEB;
  cursor: pointer; transition: box-shadow .2s, transform .2s;
}
.swapy-card:hover { box-shadow: 0 8px 28px rgba(0,0,0,0.10); transform: translateY(-2px); }
.card-link { display: flex; flex-direction: column; height: 100%; text-decoration: none; color: inherit; }
.card-img-wrap { position: relative; overflow: hidden; background: #f3f4f6; aspect-ratio: 4 / 3; }
.card-img  { width: 100%; height: 100%; object-fit: cover; display: block; transition: transform .4s ease; }
.swapy-card:hover .card-img { transform: scale(1.04); }
.card-img-placeholder {
  width: 100%; height: 100%; display: flex; align-items: center; justify-content: center;
  color: #9ca3af; font-size: 0.8rem; font-weight: 600; letter-spacing: .04em;
  background: repeating-linear-gradient(45deg, #eef0f4 0 14px, #e7eaf0 14px 28px);
}
.cat-pill {
  position: absolute; bottom: 10px; left: 10px; background: rgba(255,255,255,0.92);
  color: #1A1A1A; font-size: 0.66rem; font-weight: 700; padding: 5px 10px;
  border-radius: 999px; backdrop-filter: blur(4px);
}
.match-pill {
  position: absolute; top: 10px; left: 10px; background: rgba(26,26,26,0.75);
  color: #fff; font-size: 0.62rem; font-weight: 800; padding: 5px 10px;
  border-radius: 999px; letter-spacing: .04em; backdrop-filter: blur(4px); white-space: nowrap;
}
.wish-btn {
  position: absolute; top: 9px; right: 9px; width: 30px; height: 30px;
  background: rgba(255,255,255,0.92); border: none; border-radius: 50%;
  display: flex; align-items: center; justify-content: center; cursor: pointer;
  box-shadow: 0 2px 8px rgba(0,0,0,0.10); transition: transform .15s; z-index: 1;
}
.wish-btn:hover { transform: scale(1.15); }
.wish-icon { width: 13px; height: 13px; fill: none; stroke: #6b7280; stroke-width: 2; transition: fill .15s, stroke .15s; }
.wish-icon.wishlisted { fill: #ED730C; stroke: #ED730C; }
.card-body  { padding: 12px 14px 14px; flex: 1; display: flex; flex-direction: column; }
.card-title {
  font-size: 0.92rem; font-weight: 700; color: #1A1A1A; line-height: 1.35; margin: 0 0 4px;
  display: -webkit-box; -webkit-line-clamp: 1; -webkit-box-orient: vertical; overflow: hidden;
}
.card-wants {
  display: flex; align-items: center; gap: 5px; font-size: 0.78rem; color: #9ca3af; margin: 0;
  white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}
.wants-value { color: #149189; font-weight: 700; }
.card-value-row {
  display: flex; align-items: center; justify-content: space-between; gap: 8px;
  padding-top: 9px; margin-top: 10px; border-top: 1px solid #f3f4f6;
}
.card-meta  {
  font-size: 0.72rem; color: #9ca3af; font-weight: 600;
  white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}
.card-value { font-size: 0.95rem; font-weight: 800; color: #1A1A1A; margin: 0; }

/* ── Map panel ── */
.map-panel  { position: sticky; top: 84px; }
.map-canvas {
  position: relative; overflow: hidden; border-radius: 18px; border: 1px solid #EBEBEB;
  height: calc(100vh - 130px); min-height: 460px;
  background: #eef0e4;
}
.map-el { position: absolute; inset: 0; }

.map-count {
  position: absolute; top: 16px; left: 16px; z-index: 1000;
  display: inline-flex; align-items: center; gap: 7px;
  background: #fff; border-radius: 999px; padding: 9px 16px;
  font-size: 0.82rem; font-weight: 800; color: #1A1A1A;
  box-shadow: 0 4px 16px rgba(0,0,0,0.10);
}
.count-dot { width: 8px; height: 8px; border-radius: 50%; background: #ED730C; }

.map-zoom {
  position: absolute; bottom: 26px; right: 16px; z-index: 1000;
  display: flex; flex-direction: column; gap: 6px;
}
.map-zoom button {
  width: 38px; height: 38px; border: none; border-radius: 12px; background: #fff;
  font-size: 1.2rem; font-weight: 700; color: #1A1A1A; cursor: pointer;
  box-shadow: 0 4px 14px rgba(0,0,0,0.12);
}
.map-zoom button:hover { background: #f9f9f9; }

/* ── Empty state ── */
.empty-state { text-align: center; padding: 90px 0; }
.empty-state h3 { font-size: 1.125rem; font-weight: 700; color: #1A1A1A; margin: 14px 0 6px; }
.empty-state p  { font-size: 0.875rem; color: #9ca3af; margin: 0 0 20px; }
.clear-btn {
  background: #ED730C; color: #fff; border: none; border-radius: 999px;
  padding: 11px 28px; font-size: 0.85rem; font-weight: 700; cursor: pointer; font-family: 'DM Sans', sans-serif;
}

/* ── Responsive (matches site breakpoints) ── */
@media (min-width: 1440px) {
  .head-inner, .content-inner { padding-left: 80px; padding-right: 80px; }
  .cards-grid  { grid-template-columns: repeat(6, 1fr); }
  .cards-split { grid-template-columns: repeat(3, 1fr); }
}
@media (min-width: 1025px) and (max-width: 1439px) {
  .cards-grid { grid-template-columns: repeat(5, 1fr); }
}
@media (min-width: 769px) and (max-width: 1024px) {
  .head-inner, .content-inner { padding-left: 32px; padding-right: 32px; }
  .cards-grid  { grid-template-columns: repeat(4, 1fr); }
  .content-wrap.split { grid-template-columns: 1fr; }
  .cards-split { grid-template-columns: repeat(3, 1fr); }
  .content-wrap.split .map-panel { position: static; order: -1; }
  .content-wrap.split .map-canvas { height: 340px; min-height: 0; }
}
@media (max-width: 768px) {
  .head-inner, .content-inner { padding-left: 16px; padding-right: 16px; }
  .title-row   { flex-direction: column; align-items: stretch; }
  .view-seg    { align-self: flex-start; }
  .tools-row   { flex-wrap: wrap; }
  .cards-grid  { grid-template-columns: repeat(2, 1fr); }
  .content-wrap.split { grid-template-columns: 1fr; }
  .cards-split { grid-template-columns: repeat(2, 1fr); }
  .content-wrap.split .map-panel  { position: static; order: -1; margin-bottom: 18px; }
  .content-wrap.split .map-canvas { height: 300px; min-height: 0; }
  .map-canvas  { height: 70vh; }
}
</style>

<!-- Leaflet renders pins/popups outside Vue's scoped tree -->
<style>
.swapy-pin-wrap { background: transparent; border: none; }
.swapy-pin {
  position: absolute; transform: translate(-50%, -100%);
  display: inline-flex; align-items: center; gap: 4px;
  background: #fff; border-radius: 999px; padding: 7px 13px;
  font-family: 'DM Sans', sans-serif; font-size: 0.8rem; font-weight: 800; color: #1A1A1A;
  cursor: pointer; box-shadow: 0 3px 12px rgba(0,0,0,0.22);
  white-space: nowrap; transition: transform .15s, background .15s;
}
.swapy-pin::after {
  content: ''; position: absolute; left: 50%; bottom: -5px;
  width: 10px; height: 10px; background: inherit;
  transform: translateX(-50%) rotate(45deg);
  border-radius: 2px;
}
.swapy-pin:hover  { transform: translate(-50%, -100%) scale(1.1); z-index: 10; }
.swapy-pin.active { background: #ED730C; color: #fff; }
.swapy-pin--promoted { background: #ED730C; color: #fff; padding: 8px 14px; font-size: 0.82rem; box-shadow: 0 0 0 3px rgba(237,115,12,0.30), 0 6px 16px rgba(0,0,0,0.32); }
.swapy-pin--promoted:hover { transform: translate(-50%, -100%) scale(1.12); }
.swapy-pin--promoted.active { background: #d4620a; }
.swapy-pin--dot {
  width: 16px; height: 16px; padding: 0;
  background: #ED730C; border: 2.5px solid #fff;
  transform: translate(-50%, -50%);
}
.swapy-pin--dot::after { display: none; }
.swapy-pin--dot:hover  { transform: translate(-50%, -50%) scale(1.2); }
.swapy-pin--dot.active { background: #1A1A1A; }

.swapy-popup .leaflet-popup-content-wrapper {
  border-radius: 16px; padding: 0; overflow: hidden;
  box-shadow: 0 16px 48px rgba(0,0,0,0.22);
}
.swapy-popup .leaflet-popup-content { margin: 0; width: 248px !important; font-family: 'DM Sans', sans-serif; }
.swapy-popup .leaflet-popup-tip { box-shadow: 0 4px 14px rgba(0,0,0,0.12); }
.swapy-popup .leaflet-popup-close-button {
  top: 8px; right: 8px; width: 24px; height: 24px;
  border-radius: 50%; background: rgba(255,255,255,0.92);
  color: #4b5563; font-size: 16px; line-height: 22px;
  box-shadow: 0 2px 6px rgba(0,0,0,0.12);
}

.swapy-pop-img { height: 110px; background: repeating-linear-gradient(45deg, #efe9e0 0 14px, #eae3d8 14px 28px); }
.swapy-pop-img img { width: 100%; height: 100%; object-fit: cover; display: block; }
.swapy-pop-body { padding: 14px 16px 16px; }
.swapy-pop-body h4 { font-size: 1rem; font-weight: 800; color: #1A1A1A; margin: 0 0 4px; }
.swapy-pop-body p  { font-size: 0.8rem; color: #9ca3af; margin: 0 0 12px; }
.swapy-pop-body p strong { color: #1A1A1A; }
.swapy-pop-body a {
  display: block; text-align: center; background: #ED730C; color: #fff !important;
  border-radius: 10px; padding: 11px; font-size: 0.88rem; font-weight: 800; text-decoration: none;
}
.swapy-pop-body a:hover { background: #d4620a; }
</style>
