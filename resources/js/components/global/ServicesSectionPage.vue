<script setup>
import { ref, computed, onMounted, onBeforeUnmount, nextTick, watch } from 'vue'
import L from 'leaflet'
import 'leaflet/dist/leaflet.css'
import ServiceCard from './ServiceCard.vue'

const el = document.getElementById('services-section-app')
function parse(key) { try { return JSON.parse(el?.dataset[key] || '[]') } catch { return [] } }

// ── Sections (mirror the browse page rows) ────────────────────────────────────
const SECTIONS = {
  'featured': { title: 'Featured Services', sub: 'Promoted providers from the community' },
  'popular':  { title: 'Popular Services',  sub: 'Most requested services across Swapy' },
  'nearby':   { title: 'Services Nearby',   sub: 'Providers from nearby cities' },
  'near-you': { title: 'Services Near You',  sub: 'Providers closest to your location' },
}
const sectionKey = ref(SECTIONS[el?.dataset.section] ? el.dataset.section : 'featured')
const section    = computed(() => SECTIONS[sectionKey.value])

const showSectionDropdown = ref(false)
const showSortDropdown    = ref(false)
function closeAll() { showSectionDropdown.value = false; showSortDropdown.value = false }
function switchSection(key) {
  showSectionDropdown.value = false
  if (key !== sectionKey.value) window.location.href = `/services/section/${key}`
}

// ── Data ──────────────────────────────────────────────────────────────────────
const allServices = ref(parse('services'))
const mapPool     = ref(parse('mapServices'))   // every service with coords — drives the map, not the cards
const userPos     = ref(null)

const categoryChips = computed(() => {
  const set = new Set()
  allServices.value.forEach(s => { if (s.category) set.add(s.category) })
  return ['All', ...set]
})
const sortOptions = ['Best match', 'Top rated', 'Most recent']
const activeCat   = ref('All')
const sortBy      = ref('Best match')
const viewMode    = ref('split')

const filtered = computed(() => {
  let list = [...allServices.value]
  if (activeCat.value !== 'All') list = list.filter(s => s.category === activeCat.value)
  if (sortBy.value === 'Best match') list.sort((a, b) => (b.match_percent || 0) - (a.match_percent || 0))
  if (sortBy.value === 'Top rated')  list.sort((a, b) => (b.rating || 0) - (a.rating || 0))
  // Promoted services get sponsored placement — pinned above the rest.
  return [...list.filter(s => s.is_promoted), ...list.filter(s => !s.is_promoted)]
})

const bookmarked = ref(new Set())
function toggleBookmark(id) {
  const s = new Set(bookmarked.value)
  s.has(id) ? s.delete(id) : s.add(id)
  bookmarked.value = s
}

function clearFilters() { activeCat.value = 'All'; sortBy.value = 'Best match' }

// ── Map (Leaflet + OpenStreetMap) ─────────────────────────────────────────────
const mapEl = ref(null)
let map = null, markerLayer = null
const DEFAULT_CENTER = [6.1164, 125.1716] // General Santos
// The map shows the full pool (every service), independent of the card filter.
const mappable = computed(() => mapPool.value.filter(s => s.latitude && s.longitude))

function escapeHtml(s) {
  return String(s ?? '').replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]))
}
const STAR_SVG = '<svg width="11" height="11" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>'
function pinIcon(svc) {
  const promo = svc.is_promoted
  const inner = promo ? STAR_SVG : '<svg width="10" height="10" fill="currentColor" viewBox="0 0 24 24"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>'
  return L.divIcon({
    className: 'swapy-pin-wrap',
    html: `<div class="swapy-pin${promo ? ' swapy-pin--promoted' : ''}">${inner}${svc.match_percent}% Match</div>`,
    iconSize: [0, 0],
  })
}
function popupHtml(svc) {
  const img = svc.image ? `<img src="${escapeHtml(svc.image)}" alt="">` : ''
  return `
    <div class="swapy-pop">
      <div class="swapy-pop-img">${img}</div>
      <div class="swapy-pop-body">
        <h4>${escapeHtml(svc.title)}</h4>
        <p><strong>${escapeHtml(svc.category)}</strong> &middot; ${escapeHtml(svc.provider)}</p>
        <a href="/services/${escapeHtml(svc.id)}">View service</a>
      </div>
    </div>`
}
function renderMarkers() {
  if (!map) return
  markerLayer.clearLayers()
  mappable.value.forEach(svc => {
    const marker = L.marker([svc.latitude, svc.longitude], { icon: pinIcon(svc), zIndexOffset: svc.is_promoted ? 1000 : 0 })
      .bindPopup(popupHtml(svc), { className: 'swapy-popup', closeButton: true, offset: [0, -18], maxWidth: 280 })
    marker.on('popupopen',  () => marker.getElement()?.querySelector('.swapy-pin')?.classList.add('active'))
    marker.on('popupclose', () => marker.getElement()?.querySelector('.swapy-pin')?.classList.remove('active'))
    markerLayer.addLayer(marker)
  })
}
// Start on the viewer's city when we know it; otherwise frame this section's
// own services. The full pool stays on the map to pan through.
function setInitialView() {
  if (!map) return
  if (userPos.value) { map.setView(userPos.value, 12); return }
  const pts = filtered.value.filter(s => s.latitude && s.longitude).map(s => [s.latitude, s.longitude])
  if (pts.length) map.fitBounds(pts, { padding: [56, 56], maxZoom: 12 })
  else if (mappable.value.length) map.fitBounds(mappable.value.map(s => [s.latitude, s.longitude]), { padding: [56, 56], maxZoom: 11 })
  else map.setView(DEFAULT_CENTER, 11)
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
onMounted(() => {
  nextTick(initMap)
  navigator.geolocation?.getCurrentPosition(
    pos => { userPos.value = [pos.coords.latitude, pos.coords.longitude]; map?.setView(userPos.value, 12) },
    () => {}, { timeout: 5000 }
  )
})
onBeforeUnmount(() => { map?.remove(); map = null })
watch(viewMode, async () => { await nextTick(); map?.invalidateSize() })
</script>

<template>
<div class="section-page" @click="closeAll">

  <!-- ═══ HEADER ═══ -->
  <div class="page-head">
    <div class="head-inner">
      <div class="crumb-row">
        <a href="/services" class="crumb-back">
          <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
          Services
        </a>
        <span class="crumb-sep">/</span>
        <span class="crumb-current">{{ section.title }}</span>
      </div>

      <div class="title-row">
        <div>
          <div class="title-wrap" @click.stop>
            <h1 class="page-title">{{ section.title }}</h1>
            <button class="title-caret" @click="showSectionDropdown = !showSectionDropdown; showSortDropdown = false">
              <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" :style="{transform: showSectionDropdown ? 'rotate(180deg)' : '', transition:'transform .15s'}"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <div v-if="showSectionDropdown" class="section-dropdown">
              <button v-for="(meta, key) in SECTIONS" :key="key"
                :class="['section-option', { active: key === sectionKey }]" @click="switchSection(key)">
                <span :class="['section-dot', { active: key === sectionKey }]"></span>
                {{ meta.title }}
              </button>
            </div>
          </div>
          <p class="page-sub">{{ section.sub }} <span class="sub-dot">·</span> <strong>{{ filtered.length }} {{ filtered.length === 1 ? 'service' : 'services' }}</strong></p>
        </div>
        <div class="view-seg">
          <button :class="['seg-btn', { active: viewMode === 'split' }]" @click="viewMode = 'split'">Split</button>
          <button :class="['seg-btn', { active: viewMode === 'grid'  }]" @click="viewMode = 'grid'">Grid</button>
          <button :class="['seg-btn', { active: viewMode === 'map'   }]" @click="viewMode = 'map'">Map</button>
        </div>
      </div>

      <div class="tools-row" @click.stop>
        <div class="chips-scroll">
          <button v-for="c in categoryChips" :key="c" :class="['cat-chip', { active: activeCat === c }]" @click="activeCat = c">{{ c }}</button>
        </div>
        <div class="tools-right">
          <div style="position:relative;">
            <button class="tool-btn" @click="showSortDropdown = !showSortDropdown; showSectionDropdown = false">
              <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 7h13M3 12h9m-9 5h6m7-9l3-3m0 0l3 3m-3-3v12"/></svg>
              Sort: <strong>{{ sortBy }}</strong>
            </button>
            <div v-if="showSortDropdown" class="panel sort-panel">
              <button v-for="s in sortOptions" :key="s" :class="['sort-option', { active: sortBy === s }]" @click="sortBy = s; showSortDropdown = false">{{ s }}</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- ═══ CONTENT ═══ -->
  <div class="content-inner">
    <div v-if="filtered.length === 0" class="empty-state">
      <div style="font-size:3rem;margin-bottom:12px;">🛠️</div>
      <h3>No services here yet</h3>
      <p>Try a different category or section.</p>
      <button class="clear-btn" @click="clearFilters">Clear Filters</button>
    </div>

    <div v-else :class="['content-wrap', viewMode]">

      <!-- cards -->
      <div v-if="viewMode !== 'map'" :class="viewMode === 'split' ? 'cards-split' : 'cards-grid'">
        <ServiceCard v-for="svc in filtered" :key="svc.id"
          :svc="svc" :saved="bookmarked.has(svc.id)" @toggle-save="toggleBookmark(svc.id)" />
      </div>

      <!-- map -->
      <div v-show="viewMode !== 'grid'" class="map-panel">
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
.title-caret { width: 36px; height: 36px; border-radius: 12px; border: 1px solid #EBEBEB; background: #faf7f3; color: #1A1A1A; cursor: pointer; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.title-caret:hover { border-color: #1A1A1A; }
.page-sub  { font-size: 0.85rem; color: #9ca3af; font-weight: 500; margin: 6px 0 0; }
.page-sub strong { color: #6b7280; font-weight: 700; }
.sub-dot   { margin: 0 4px; }
.section-dropdown { position: absolute; top: calc(100% + 12px); left: 0; z-index: 1200; background: #fff; border: 1px solid #EBEBEB; border-radius: 18px; padding: 8px; min-width: 280px; box-shadow: 0 16px 48px rgba(0,0,0,0.14); }
.section-option { display: flex; align-items: center; gap: 12px; width: 100%; padding: 13px 16px; border: none; border-radius: 12px; background: transparent; font-family: 'DM Sans', sans-serif; font-size: 0.95rem; font-weight: 700; color: #1A1A1A; cursor: pointer; text-align: left; transition: background .1s; }
.section-option:hover  { background: #f9f9f9; }
.section-option.active { background: #fff4ec; color: #ED730C; }
.section-dot { width: 9px; height: 9px; border-radius: 50%; background: #d1d5db; flex-shrink: 0; }
.section-dot.active { background: #ED730C; }

/* tools row */
.tools-row { display: flex; align-items: center; justify-content: space-between; gap: 14px; padding: 18px 0 16px; }
.chips-scroll { display: flex; gap: 8px; overflow-x: auto; scrollbar-width: none; -webkit-overflow-scrolling: touch; flex: 1; }
.chips-scroll::-webkit-scrollbar { display: none; }
.cat-chip { padding: 9px 20px; border-radius: 999px; border: 1px solid #EBEBEB; background: #fff; font-family: 'DM Sans', sans-serif; font-size: 0.85rem; font-weight: 600; color: #4b5563; cursor: pointer; white-space: nowrap; transition: all .15s; }
.cat-chip:hover  { border-color: #1A1A1A; }
.cat-chip.active { background: #1A1A1A; color: #fff; border-color: #1A1A1A; }
.tools-right { display: flex; gap: 10px; flex-shrink: 0; }
.tool-btn { display: flex; align-items: center; gap: 7px; padding: 10px 18px; border-radius: 999px; border: 1.5px solid #e2ddd8; background: #fff; font-family: 'DM Sans', sans-serif; font-size: 0.85rem; font-weight: 600; color: #1A1A1A; cursor: pointer; white-space: nowrap; transition: border-color .15s; }
.tool-btn:hover  { border-color: #1A1A1A; }
.tool-btn strong { font-weight: 800; }
.panel { position: absolute; top: calc(100% + 10px); right: 0; z-index: 1200; background: #fff; border: 1px solid #EBEBEB; border-radius: 16px; box-shadow: 0 12px 40px rgba(0,0,0,0.12); }
.sort-panel  { padding: 6px; min-width: 210px; }
.sort-option { display: block; width: 100%; text-align: left; padding: 10px 14px; border: none; border-radius: 10px; background: transparent; font-family: 'DM Sans', sans-serif; font-size: 0.85rem; font-weight: 500; color: #1A1A1A; cursor: pointer; }
.sort-option:hover  { background: #f9f9f9; }
.sort-option.active { background: #fff4ec; color: #ED730C; font-weight: 700; }

/* view toggle */
.view-seg { display: inline-flex; background: #f1ece6; border-radius: 14px; padding: 4px; gap: 2px; margin-top: 4px; }
.seg-btn { padding: 9px 22px; border: none; border-radius: 11px; background: transparent; font-family: 'DM Sans', sans-serif; font-size: 0.88rem; font-weight: 700; color: #6b7280; cursor: pointer; transition: all .15s; }
.seg-btn.active { background: #fff; color: #1A1A1A; font-weight: 800; box-shadow: 0 2px 8px rgba(0,0,0,0.08); }

/* ── Content ── */
.content-inner { max-width: 1680px; margin: 0 auto; padding: 28px 40px 72px; }
.content-wrap.split { display: grid; grid-template-columns: minmax(0, 1.15fr) minmax(0, 1fr); gap: 20px; align-items: start; }
.cards-split { display: grid; grid-template-columns: repeat(2, 1fr); gap: 18px; }
.cards-grid  { display: grid; gap: 22px; }

/* ── Map ── */
.map-panel  { position: sticky; top: 84px; }
.map-canvas { position: relative; overflow: hidden; border-radius: 18px; border: 1px solid #EBEBEB; height: calc(100vh - 130px); min-height: 460px; background: #eef0e4; }
.map-el { position: absolute; inset: 0; }
.map-count { position: absolute; top: 16px; left: 16px; z-index: 1000; display: inline-flex; align-items: center; gap: 7px; background: #fff; border-radius: 999px; padding: 9px 16px; font-size: 0.82rem; font-weight: 800; color: #1A1A1A; box-shadow: 0 4px 16px rgba(0,0,0,0.10); }
.count-dot { width: 8px; height: 8px; border-radius: 50%; background: #ED730C; }
.map-zoom { position: absolute; bottom: 26px; right: 16px; z-index: 1000; display: flex; flex-direction: column; gap: 6px; }
.map-zoom button { width: 38px; height: 38px; border: none; border-radius: 12px; background: #fff; font-size: 1.2rem; font-weight: 700; color: #1A1A1A; cursor: pointer; box-shadow: 0 4px 14px rgba(0,0,0,0.12); }
.map-zoom button:hover { background: #f9f9f9; }

/* ── Empty state ── */
.empty-state { text-align: center; padding: 90px 0; }
.empty-state h3 { font-size: 1.125rem; font-weight: 700; color: #1A1A1A; margin: 14px 0 6px; }
.empty-state p  { font-size: 0.875rem; color: #9ca3af; margin: 0 0 20px; }
.clear-btn { background: #ED730C; color: #fff; border: none; border-radius: 999px; padding: 11px 28px; font-size: 0.85rem; font-weight: 700; cursor: pointer; font-family: 'DM Sans', sans-serif; }

/* ── Responsive (matches Items see-all) ── */
@media (min-width: 1440px) {
  .head-inner, .content-inner { padding-left: 80px; padding-right: 80px; }
  .cards-grid  { grid-template-columns: repeat(5, 1fr); }
  .cards-split { grid-template-columns: repeat(2, 1fr); }
}
@media (min-width: 1025px) and (max-width: 1439px) {
  .cards-grid  { grid-template-columns: repeat(4, 1fr); }
  .cards-split { grid-template-columns: repeat(2, 1fr); }
}
@media (min-width: 769px) and (max-width: 1024px) {
  .head-inner, .content-inner { padding-left: 32px; padding-right: 32px; }
  .cards-grid  { grid-template-columns: repeat(3, 1fr); }
  .content-wrap.split { grid-template-columns: 1fr; }
  .cards-split { grid-template-columns: repeat(3, 1fr); }
  .content-wrap.split .map-panel  { position: static; order: -1; margin-bottom: 18px; }
  .content-wrap.split .map-canvas { height: 340px; min-height: 0; }
}
@media (min-width: 481px) and (max-width: 768px) {
  .head-inner, .content-inner { padding-left: 24px; padding-right: 24px; }
  .title-row { flex-direction: column; align-items: stretch; }
  .view-seg  { align-self: flex-start; }
  .cards-grid  { grid-template-columns: repeat(2, 1fr); }
  .content-wrap.split { grid-template-columns: 1fr; }
  .cards-split { grid-template-columns: repeat(2, 1fr); }
  .content-wrap.split .map-panel  { position: static; order: -1; margin-bottom: 18px; }
  .content-wrap.split .map-canvas { height: 320px; min-height: 0; }
}
@media (max-width: 480px) {
  .head-inner, .content-inner { padding-left: 16px; padding-right: 16px; }
  .title-row { flex-direction: column; align-items: stretch; }
  .view-seg  { align-self: flex-start; }
  .cards-grid  { grid-template-columns: 1fr; }
  .content-wrap.split { grid-template-columns: 1fr; }
  .cards-split { grid-template-columns: 1fr; }
  .content-wrap.split .map-panel  { position: static; order: -1; margin-bottom: 18px; }
  .content-wrap.split .map-canvas { height: 300px; min-height: 0; }
}
</style>

<!-- Leaflet renders pins/popups outside Vue's scoped tree -->
<style>
.swapy-pin-wrap { background: transparent; border: none; }
.swapy-pin { position: absolute; transform: translate(-50%, -100%); display: inline-flex; align-items: center; gap: 4px; background: #fff; border-radius: 999px; padding: 7px 13px; font-family: 'DM Sans', sans-serif; font-size: 0.8rem; font-weight: 800; color: #1A1A1A; cursor: pointer; box-shadow: 0 3px 12px rgba(0,0,0,0.22); white-space: nowrap; transition: transform .15s, background .15s; }
.swapy-pin::after { content: ''; position: absolute; left: 50%; bottom: -5px; width: 10px; height: 10px; background: inherit; transform: translateX(-50%) rotate(45deg); border-radius: 2px; }
.swapy-pin:hover  { transform: translate(-50%, -100%) scale(1.1); z-index: 10; }
.swapy-pin.active { background: #ED730C; color: #fff; }
.swapy-pin--promoted { background: #ED730C; color: #fff; padding: 8px 14px; font-size: 0.82rem; box-shadow: 0 0 0 3px rgba(237,115,12,0.30), 0 6px 16px rgba(0,0,0,0.32); }
.swapy-pin--promoted:hover { transform: translate(-50%, -100%) scale(1.12); }
.swapy-pin--promoted.active { background: #d4620a; }
.swapy-popup .leaflet-popup-content-wrapper { border-radius: 16px; padding: 0; overflow: hidden; box-shadow: 0 16px 48px rgba(0,0,0,0.22); }
.swapy-popup .leaflet-popup-content { margin: 0; width: 248px !important; font-family: 'DM Sans', sans-serif; }
.swapy-popup .leaflet-popup-tip { box-shadow: 0 4px 14px rgba(0,0,0,0.12); }
.swapy-popup .leaflet-popup-close-button { top: 8px; right: 8px; width: 24px; height: 24px; border-radius: 50%; background: rgba(255,255,255,0.92); color: #4b5563; font-size: 16px; line-height: 22px; box-shadow: 0 2px 6px rgba(0,0,0,0.12); }
.swapy-pop-img { height: 110px; background: repeating-linear-gradient(45deg, #efe9e0 0 14px, #eae3d8 14px 28px); }
.swapy-pop-img img { width: 100%; height: 100%; object-fit: cover; display: block; }
.swapy-pop-body { padding: 14px 16px 16px; }
.swapy-pop-body h4 { font-size: 1rem; font-weight: 800; color: #1A1A1A; margin: 0 0 4px; }
.swapy-pop-body p  { font-size: 0.8rem; color: #9ca3af; margin: 0 0 12px; }
.swapy-pop-body p strong { color: #1A1A1A; }
.swapy-pop-body a { display: block; text-align: center; background: #ED730C; color: #fff !important; border-radius: 10px; padding: 11px; font-size: 0.88rem; font-weight: 800; text-decoration: none; }
.swapy-pop-body a:hover { background: #d4620a; }
</style>
