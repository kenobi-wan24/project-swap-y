<script setup>
import { ref, computed, onMounted, onBeforeUnmount, nextTick, watch } from 'vue'
import L from 'leaflet'
import 'leaflet/dist/leaflet.css'
import { currency, formatMoney } from '../../constants/currency'

const el = document.getElementById('search-app')
function parse(key) { try { return JSON.parse(el?.dataset[key] || '[]') } catch { return [] } }

// ── Query state ───────────────────────────────────────────────────────────────
// The search input now lives in the nav bar (navigation.blade.php) on this
// route — the page just reads the current query for its header + results.
const query    = ref(el?.dataset.q || '')
const locInput = ref(el?.dataset.location || '')

// ── Type metadata (badge / chip / pin / legend) ───────────────────────────────
const TYPE_META = {
  'item':        { label: 'Item',        plural: 'Items',        color: '#ED730C',
                   icon: '<path d="M21 16V8a2 2 0 00-1-1.73l-7-4a2 2 0 00-2 0l-7 4A2 2 0 003 8v8a2 2 0 001 1.73l7 4a2 2 0 002 0l7-4A2 2 0 0021 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/>' },
  'home':        { label: 'Home',        plural: 'Homes',        color: '#2563eb',
                   icon: '<path d="M3 9.5L12 3l9 6.5V21H3z"/>' },
  'garage-sale': { label: 'Garage Sale', plural: 'Garage Sales', color: '#8b5cf6',
                   icon: '<path d="M20.59 13.41l-7.17 7.17a2 2 0 01-2.83 0L2 12V2h10l8.59 8.59a2 2 0 010 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/>' },
  'service':     { label: 'Service',     plural: 'Services',     color: '#14b8a6',
                   icon: '<path d="M18 8a6 6 0 00-12 0c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 01-3.46 0"/>' },
}
function iconSvg(type, color = 'currentColor', size = 13) {
  const m = TYPE_META[type]
  return `<svg width="${size}" height="${size}" fill="none" stroke="${color}" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">${m?.icon || ''}</svg>`
}

// ── Data + geolocation distance ───────────────────────────────────────────────
const allResults = ref(parse('results'))
const userPos    = ref(null)

function milesBetween(lat1, lng1, lat2, lng2) {
  const toRad = d => (d * Math.PI) / 180
  const R = 3958.8
  const a = Math.sin(toRad(lat2 - lat1) / 2) ** 2 +
            Math.cos(toRad(lat1)) * Math.cos(toRad(lat2)) * Math.sin(toRad(lng2 - lng1) / 2) ** 2
  return 2 * R * Math.asin(Math.sqrt(a))
}
onMounted(() => {
  navigator.geolocation?.getCurrentPosition(
    pos => { userPos.value = { lat: pos.coords.latitude, lng: pos.coords.longitude } },
    () => {}, { timeout: 5000 }
  )
})

const decorated = computed(() => {
  if (!userPos.value) return allResults.value
  return allResults.value.map(r => ({
    ...r,
    distance: (r.latitude && r.longitude)
      ? +milesBetween(userPos.value.lat, userPos.value.lng, r.latitude, r.longitude).toFixed(1)
      : null,
  }))
})

// ── Filter / sort ─────────────────────────────────────────────────────────────
const activeType = ref('all')
const sortBy     = ref('Relevance')
const sortOptions = ['Relevance', 'Nearest', 'Name (A–Z)']
const showSortDropdown = ref(false)
const viewMode   = ref('split')

const typeCounts = computed(() => {
  const counts = { item: 0, home: 0, 'garage-sale': 0, service: 0 }
  decorated.value.forEach(r => { counts[r.type] = (counts[r.type] || 0) + 1 })
  return counts
})
const presentTypes = computed(() => Object.keys(TYPE_META).filter(t => typeCounts.value[t] > 0))

const filtered = computed(() => {
  let list = [...decorated.value]
  if (activeType.value !== 'all') list = list.filter(r => r.type === activeType.value)
  if (sortBy.value === 'Relevance')  list.sort((a, b) => (b.score || 0) - (a.score || 0))
  if (sortBy.value === 'Nearest')    list.sort((a, b) => (a.distance ?? Infinity) - (b.distance ?? Infinity))
  if (sortBy.value === 'Name (A–Z)') list.sort((a, b) => a.title.localeCompare(b.title))
  // Promoted results get sponsored placement — pinned above the rest in every sort.
  return [...list.filter(r => r.is_promoted), ...list.filter(r => !r.is_promoted)]
})

function closeAll() { showSortDropdown.value = false }

// ── Per-type display helpers ──────────────────────────────────────────────────
function priceText(r) {
  if (r.type === 'item')        return formatMoney(r.value)
  if (r.type === 'service')     return ''
  if (r.type === 'garage-sale') return `${r.item_count} items`
  if (r.type === 'home') {
    if (r.home_type === 'Sell') return currency.value.symbol + (r.value / 1000000).toFixed(1) + 'M'
    if (r.home_type === 'Rent' || r.home_type === 'Co-living') return formatMoney(r.value) + '/mo'
    return formatMoney(r.value)
  }
  return ''
}

// ── Map (Leaflet + OpenStreetMap) ─────────────────────────────────────────────
const mapEl = ref(null)
let map = null, markerLayer = null
const DEFAULT_CENTER = [6.1164, 125.1716] // General Santos
const mappable = computed(() => filtered.value.filter(r => r.latitude && r.longitude))

function escapeHtml(s) {
  return String(s ?? '').replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]))
}
function pinIcon(r) {
  const color = TYPE_META[r.type]?.color || '#ED730C'
  if (r.is_promoted) {
    // Promoted: filled, larger, white halo — dominates the map.
    return L.divIcon({
      className: 'search-pin-wrap',
      html: `<div class="search-pin search-pin--promoted" style="background:${color};border-color:#fff">${iconSvg(r.type, '#fff', 17)}</div>`,
      iconSize: [42, 42], iconAnchor: [21, 42], popupAnchor: [0, -38],
    })
  }
  return L.divIcon({
    className: 'search-pin-wrap',
    html: `<div class="search-pin" style="border-color:${color}">${iconSvg(r.type, color, 15)}</div>`,
    iconSize: [34, 34], iconAnchor: [17, 34], popupAnchor: [0, -32],
  })
}
function popupHtml(r) {
  const img   = r.image ? `<img src="${escapeHtml(r.image)}" alt="">` : ''
  const price = priceText(r)
  const meta  = price ? `<strong>${escapeHtml(price)}</strong>` : escapeHtml(r.category || '')
  const badge = TYPE_META[r.type]?.label || ''
  return `
    <div class="swapy-pop">
      <div class="swapy-pop-img">${img}<span class="swapy-pop-badge" style="background:${TYPE_META[r.type]?.color}">${escapeHtml(badge)}</span></div>
      <div class="swapy-pop-body">
        <h4>${escapeHtml(r.title)}</h4>
        <p>${escapeHtml(r.subtitle || '')}${meta ? ' &middot; ' + meta : ''}</p>
        <a href="${escapeHtml(r.url)}">View ${escapeHtml((TYPE_META[r.type]?.label || '').toLowerCase())}</a>
      </div>
    </div>`
}
function renderMarkers() {
  if (!map) return
  markerLayer.clearLayers()
  const bounds = []
  mappable.value.forEach(r => {
    const marker = L.marker([r.latitude, r.longitude], { icon: pinIcon(r), zIndexOffset: r.is_promoted ? 1000 : 0 })
      .bindPopup(popupHtml(r), { className: 'swapy-popup', closeButton: true, offset: [0, -8], maxWidth: 280 })
    markerLayer.addLayer(marker)
    bounds.push([r.latitude, r.longitude])
  })
  if (bounds.length) map.fitBounds(bounds, { padding: [56, 56], maxZoom: 14 })
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
}
onMounted(() => nextTick(initMap))
onBeforeUnmount(() => { map?.remove(); map = null })
watch(filtered, () => renderMarkers())
watch(viewMode, async () => { await nextTick(); map?.invalidateSize() })
</script>

<template>
<div class="search-page" @click="closeAll">

  <!-- ═══ SEARCH HEADER ═══ -->
  <div class="page-head">
    <div class="head-inner">
      <div class="crumb-row">
        <a href="/items" class="crumb-back">
          <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
          Back to browsing
        </a>
      </div>

      <div v-if="query" class="result-meta">
        Results for <strong>“{{ query }}”</strong><span v-if="locInput"> in <strong>{{ locInput }}</strong></span>
        <span class="sub-dot">·</span> {{ filtered.length }} {{ filtered.length === 1 ? 'result' : 'results' }}
      </div>

      <!-- type chips + tools -->
      <div v-if="allResults.length" class="tools-row" @click.stop>
        <div class="chips-scroll">
          <button :class="['type-chip', { active: activeType === 'all' }]" @click="activeType = 'all'">
            All <span class="chip-count">{{ allResults.length }}</span>
          </button>
          <button v-for="t in presentTypes" :key="t"
            :class="['type-chip', { active: activeType === t }]" @click="activeType = t"
            :style="activeType === t ? { background: TYPE_META[t].color, borderColor: TYPE_META[t].color } : {}">
            <span class="chip-ic" v-html="iconSvg(t, activeType === t ? '#fff' : TYPE_META[t].color, 13)"></span>
            {{ TYPE_META[t].plural }} <span class="chip-count">{{ typeCounts[t] }}</span>
          </button>
        </div>
        <div class="tools-right">
          <div style="position:relative;">
            <button class="tool-btn" @click="showSortDropdown = !showSortDropdown">
              <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 7h13M3 12h9m-9 5h6m7-9l3-3m0 0l3 3m-3-3v12"/></svg>
              Sort: <strong>{{ sortBy }}</strong>
            </button>
            <div v-if="showSortDropdown" class="panel sort-panel">
              <button v-for="s in sortOptions" :key="s" :class="['sort-option', { active: sortBy === s }]" @click="sortBy = s; showSortDropdown = false">{{ s }}</button>
            </div>
          </div>
          <div class="view-seg">
            <button :class="['seg-btn', { active: viewMode === 'split' }]" @click="viewMode = 'split'">Split</button>
            <button :class="['seg-btn', { active: viewMode === 'grid'  }]" @click="viewMode = 'grid'">Grid</button>
            <button :class="['seg-btn', { active: viewMode === 'map'   }]" @click="viewMode = 'map'">Map</button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- ═══ CONTENT ═══ -->
  <div class="content-inner">

    <!-- nothing searched yet -->
    <div v-if="!query && !locInput" class="empty-state">
      <svg width="56" height="56" fill="none" stroke="#d1d5db" stroke-width="1.5" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path stroke-linecap="round" d="M21 21l-4.35-4.35"/></svg>
      <h3>Search across everything on Swapy</h3>
      <p>One search covers items, homes, garage sales and services.</p>
    </div>

    <!-- no matches -->
    <div v-else-if="filtered.length === 0" class="empty-state">
      <svg width="56" height="56" fill="none" stroke="#d1d5db" stroke-width="1.5" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path stroke-linecap="round" d="M21 21l-4.35-4.35"/></svg>
      <h3>No results found</h3>
      <p>Nothing matched <strong>“{{ query }}”</strong>. Try a different word or fewer filters.</p>
      <button v-if="activeType !== 'all'" class="clear-btn" @click="activeType = 'all'">Show all types</button>
    </div>

    <!-- results -->
    <div v-else :class="['content-wrap', viewMode]">

      <!-- cards -->
      <div v-if="viewMode !== 'map'" :class="viewMode === 'split' ? 'cards-split' : 'cards-grid'">
        <a v-for="r in filtered" :key="r.type + '-' + r.id" :href="r.url" class="result-card">
          <div class="rc-img-wrap">
            <img v-if="r.image" :src="r.image" :alt="r.title" class="rc-img">
            <div v-else class="rc-img-ph">[ {{ r.title }} ]</div>
            <span class="rc-badge" :style="{ background: TYPE_META[r.type].color }">
              <span class="rc-badge-ic" v-html="iconSvg(r.type, '#fff', 11)"></span>{{ TYPE_META[r.type].label }}
            </span>
            <span v-if="r.is_promoted" class="rc-promoted">Promoted</span>
          </div>
          <div class="rc-body">
            <h3 class="rc-title">{{ r.title }}</h3>
            <p class="rc-sub">{{ r.subtitle }}</p>
            <div class="rc-foot">
              <span class="rc-loc">
                <span v-if="r.distance != null">{{ r.distance }} mi</span>
                <span v-else>{{ r.location || '—' }}</span>
              </span>
              <span v-if="priceText(r)" class="rc-price">{{ priceText(r) }}</span>
            </div>
          </div>
        </a>
      </div>

      <!-- map -->
      <div v-show="viewMode !== 'grid'" class="map-panel">
        <div class="map-canvas">
          <div ref="mapEl" class="map-el"></div>
          <div class="map-count"><span class="count-dot"></span>{{ mappable.length }} on map</div>
          <div class="map-legend">
            <span v-for="t in presentTypes" :key="t" class="legend-item">
              <span class="legend-dot" :style="{ background: TYPE_META[t].color }"></span>{{ TYPE_META[t].plural }}
            </span>
          </div>
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

.search-page { min-height: 100vh; background: #fff; font-family: 'DM Sans', sans-serif; }

/* ── Header ── */
.page-head  { background: #fff; border-bottom: 1px solid #EBEBEB; }
.head-inner { max-width: 1680px; margin: 0 auto; padding: 18px 40px 0; }
.crumb-row  { display: flex; align-items: center; gap: 8px; font-size: 0.85rem; margin-bottom: 14px; }
.crumb-back { display: inline-flex; align-items: center; gap: 4px; color: #6b7280; text-decoration: none; font-weight: 600; }
.crumb-back:hover { color: #ED730C; }

/* search bar */
.search-bar { display: flex; align-items: center; gap: 6px; background: #fff; border: 1.5px solid #EBEBEB; border-radius: 999px; padding: 6px 6px 6px 18px; box-shadow: 0 4px 20px rgba(0,0,0,0.06); max-width: 760px; }
.sb-field { display: flex; align-items: center; gap: 9px; flex: 1; min-width: 0; }
.sb-field input { border: none; outline: none; background: transparent; font-family: 'DM Sans', sans-serif; font-size: 0.9rem; color: #1A1A1A; width: 100%; }
.sb-loc { flex: 0 1 180px; }
.sb-divider { width: 1px; height: 26px; background: #EBEBEB; flex-shrink: 0; }
.sb-btn { background: #ED730C; color: #fff; border: none; border-radius: 999px; padding: 11px 26px; font-family: 'DM Sans', sans-serif; font-size: 0.88rem; font-weight: 800; cursor: pointer; flex-shrink: 0; box-shadow: 0 4px 14px rgba(237,115,12,0.35); transition: background .15s; }
.sb-btn:hover { background: #d4620a; }

.result-meta { font-size: 0.9rem; color: #6b7280; margin: 16px 0 0; }
.result-meta strong { color: #1A1A1A; font-weight: 800; }
.sub-dot { margin: 0 6px; color: #d1d5db; }

/* tools row */
.tools-row { display: flex; align-items: center; justify-content: space-between; gap: 14px; padding: 16px 0; }
.chips-scroll { display: flex; gap: 8px; overflow-x: auto; scrollbar-width: none; -webkit-overflow-scrolling: touch; flex: 1; }
.chips-scroll::-webkit-scrollbar { display: none; }
.type-chip { display: inline-flex; align-items: center; gap: 7px; padding: 9px 16px; border-radius: 999px; border: 1px solid #EBEBEB; background: #fff; font-family: 'DM Sans', sans-serif; font-size: 0.85rem; font-weight: 700; color: #4b5563; cursor: pointer; white-space: nowrap; transition: all .15s; }
.type-chip:hover { border-color: #1A1A1A; }
.type-chip.active { color: #fff; border-color: #1A1A1A; background: #1A1A1A; }
.chip-ic { display: inline-flex; }
.chip-count { font-size: 0.72rem; font-weight: 800; opacity: 0.7; }
.tools-right { display: flex; gap: 10px; flex-shrink: 0; align-items: center; }
.tool-btn { display: flex; align-items: center; gap: 7px; padding: 10px 16px; border-radius: 999px; border: 1.5px solid #e2ddd8; background: #fff; font-family: 'DM Sans', sans-serif; font-size: 0.85rem; font-weight: 600; color: #1A1A1A; cursor: pointer; white-space: nowrap; transition: border-color .15s; }
.tool-btn:hover { border-color: #1A1A1A; }
.tool-btn strong { font-weight: 800; }
.panel { position: absolute; top: calc(100% + 10px); right: 0; z-index: 1200; background: #fff; border: 1px solid #EBEBEB; border-radius: 16px; box-shadow: 0 12px 40px rgba(0,0,0,0.12); }
.sort-panel { padding: 6px; min-width: 200px; }
.sort-option { display: block; width: 100%; text-align: left; padding: 10px 14px; border: none; border-radius: 10px; background: transparent; font-family: 'DM Sans', sans-serif; font-size: 0.85rem; font-weight: 500; color: #1A1A1A; cursor: pointer; }
.sort-option:hover { background: #f9f9f9; }
.sort-option.active { background: #fff4ec; color: #ED730C; font-weight: 700; }
.view-seg { display: inline-flex; background: #f1ece6; border-radius: 14px; padding: 4px; gap: 2px; }
.seg-btn { padding: 9px 18px; border: none; border-radius: 11px; background: transparent; font-family: 'DM Sans', sans-serif; font-size: 0.85rem; font-weight: 700; color: #6b7280; cursor: pointer; transition: all .15s; }
.seg-btn.active { background: #fff; color: #1A1A1A; font-weight: 800; box-shadow: 0 2px 8px rgba(0,0,0,0.08); }

/* ── Content ── */
.content-inner { max-width: 1680px; margin: 0 auto; padding: 26px 40px 72px; }
.content-wrap.split { display: grid; grid-template-columns: minmax(0, 1.15fr) minmax(0, 1fr); gap: 20px; align-items: start; }
.cards-split { display: grid; grid-template-columns: repeat(2, 1fr); gap: 18px; }
.cards-grid  { display: grid; gap: 20px; }

/* ── Result card ── */
.result-card { background: #fff; border-radius: 16px; overflow: hidden; border: 1px solid #EBEBEB; text-decoration: none; color: inherit; display: flex; flex-direction: column; transition: box-shadow .2s, transform .2s; }
.result-card:hover { box-shadow: 0 8px 28px rgba(0,0,0,0.10); transform: translateY(-2px); }
.result-card:hover .rc-img { transform: scale(1.04); }
.rc-img-wrap { position: relative; overflow: hidden; background: #f3f4f6; aspect-ratio: 4 / 3; }
.rc-img { width: 100%; height: 100%; object-fit: cover; display: block; transition: transform .4s ease; }
.rc-img-ph { width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; color: #9ca3af; font-size: 0.8rem; font-weight: 600; background: repeating-linear-gradient(45deg, #eef0f4 0 14px, #e7eaf0 14px 28px); }
.rc-badge { position: absolute; top: 10px; left: 10px; display: inline-flex; align-items: center; gap: 4px; color: #fff; font-size: 0.62rem; font-weight: 800; padding: 4px 9px; border-radius: 999px; letter-spacing: .04em; text-transform: uppercase; }
.rc-badge-ic { display: inline-flex; }
.rc-promoted { position: absolute; top: 10px; right: 10px; background: #1A1A1A; color: #fff; font-size: 0.55rem; font-weight: 800; padding: 4px 8px; border-radius: 999px; letter-spacing: .05em; text-transform: uppercase; }
.rc-body { padding: 12px 14px 14px; flex: 1; display: flex; flex-direction: column; }
.rc-title { font-size: 0.92rem; font-weight: 700; color: #1A1A1A; line-height: 1.35; margin: 0 0 3px; display: -webkit-box; -webkit-line-clamp: 1; -webkit-box-orient: vertical; overflow: hidden; }
.rc-sub { font-size: 0.78rem; color: #9ca3af; margin: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.rc-foot { display: flex; align-items: center; justify-content: space-between; gap: 8px; padding-top: 9px; margin-top: auto; }
.rc-loc { font-size: 0.72rem; color: #9ca3af; font-weight: 600; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.rc-price { font-size: 0.92rem; font-weight: 800; color: #1A1A1A; white-space: nowrap; }

/* ── Map ── */
.map-panel  { position: sticky; top: 84px; }
.map-canvas { position: relative; overflow: hidden; border-radius: 18px; border: 1px solid #EBEBEB; height: calc(100vh - 130px); min-height: 460px; background: #eef0e4; }
.map-el { position: absolute; inset: 0; }
.map-count { position: absolute; top: 16px; left: 16px; z-index: 1000; display: inline-flex; align-items: center; gap: 7px; background: #fff; border-radius: 999px; padding: 9px 16px; font-size: 0.82rem; font-weight: 800; color: #1A1A1A; box-shadow: 0 4px 16px rgba(0,0,0,0.10); }
.count-dot { width: 8px; height: 8px; border-radius: 50%; background: #ED730C; }
.map-legend { position: absolute; top: 16px; right: 16px; z-index: 1000; display: flex; flex-direction: column; gap: 5px; background: #fff; border-radius: 12px; padding: 10px 12px; box-shadow: 0 4px 16px rgba(0,0,0,0.10); }
.legend-item { display: flex; align-items: center; gap: 7px; font-size: 0.72rem; font-weight: 700; color: #4b5563; }
.legend-dot { width: 9px; height: 9px; border-radius: 50%; }
.map-zoom { position: absolute; bottom: 26px; right: 16px; z-index: 1000; display: flex; flex-direction: column; gap: 6px; }
.map-zoom button { width: 38px; height: 38px; border: none; border-radius: 12px; background: #fff; font-size: 1.2rem; font-weight: 700; color: #1A1A1A; cursor: pointer; box-shadow: 0 4px 14px rgba(0,0,0,0.12); }
.map-zoom button:hover { background: #f9f9f9; }

/* ── Empty state ── */
.empty-state { text-align: center; padding: 100px 0; }
.empty-state h3 { font-size: 1.2rem; font-weight: 800; color: #1A1A1A; margin: 16px 0 6px; }
.empty-state p  { font-size: 0.9rem; color: #9ca3af; margin: 0 0 20px; }
.empty-state strong { color: #6b7280; }
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
  .tools-row { flex-direction: column; align-items: stretch; gap: 12px; }
  .tools-right { justify-content: space-between; }
  .cards-grid  { grid-template-columns: repeat(2, 1fr); }
  .content-wrap.split { grid-template-columns: 1fr; }
  .cards-split { grid-template-columns: repeat(2, 1fr); }
  .content-wrap.split .map-panel  { position: static; order: -1; margin-bottom: 18px; }
  .content-wrap.split .map-canvas { height: 320px; min-height: 0; }
}
@media (max-width: 480px) {
  .head-inner, .content-inner { padding-left: 16px; padding-right: 16px; }
  .search-bar { flex-wrap: wrap; border-radius: 20px; padding: 10px 12px; }
  .sb-loc { flex-basis: 100%; }
  .sb-divider { display: none; }
  .sb-btn { width: 100%; }
  .tools-row { flex-direction: column; align-items: stretch; gap: 12px; }
  .tools-right { justify-content: space-between; }
  .cards-grid  { grid-template-columns: 1fr; }
  .content-wrap.split { grid-template-columns: 1fr; }
  .cards-split { grid-template-columns: 1fr; }
  .content-wrap.split .map-panel  { position: static; order: -1; margin-bottom: 18px; }
  .content-wrap.split .map-canvas { height: 300px; min-height: 0; }
}
</style>

<!-- Leaflet renders pins/popups outside Vue's scoped tree -->
<style>
.search-pin-wrap { background: transparent; border: none; }
.search-pin { width: 34px; height: 34px; background: #fff; border: 2.5px solid #ED730C; border-radius: 50% 50% 50% 0; transform: rotate(-45deg); display: flex; align-items: center; justify-content: center; box-shadow: 0 3px 10px rgba(0,0,0,0.28); cursor: pointer; transition: transform .15s; }
.search-pin svg { transform: rotate(45deg); }
.search-pin:hover { transform: rotate(-45deg) scale(1.12); }
.search-pin--promoted { width: 42px; height: 42px; box-shadow: 0 0 0 3px rgba(255,255,255,0.92), 0 7px 20px rgba(0,0,0,0.42); }
.search-pin--promoted:hover { transform: rotate(-45deg) scale(1.1); }
.swapy-popup .leaflet-popup-content-wrapper { border-radius: 16px; padding: 0; overflow: hidden; box-shadow: 0 16px 48px rgba(0,0,0,0.22); }
.swapy-popup .leaflet-popup-content { margin: 0; width: 248px !important; font-family: 'DM Sans', sans-serif; }
.swapy-popup .leaflet-popup-tip { box-shadow: 0 4px 14px rgba(0,0,0,0.12); }
.swapy-popup .leaflet-popup-close-button { top: 8px; right: 8px; width: 24px; height: 24px; border-radius: 50%; background: rgba(255,255,255,0.92); color: #4b5563; font-size: 16px; line-height: 22px; box-shadow: 0 2px 6px rgba(0,0,0,0.12); }
.swapy-pop-img { position: relative; height: 110px; background: repeating-linear-gradient(45deg, #efe9e0 0 14px, #eae3d8 14px 28px); }
.swapy-pop-img img { width: 100%; height: 100%; object-fit: cover; display: block; }
.swapy-pop-badge { position: absolute; top: 10px; left: 10px; color: #fff; font-size: 0.6rem; font-weight: 800; padding: 3px 8px; border-radius: 999px; letter-spacing: .04em; text-transform: uppercase; }
.swapy-pop-body { padding: 14px 16px 16px; }
.swapy-pop-body h4 { font-size: 1rem; font-weight: 800; color: #1A1A1A; margin: 0 0 4px; }
.swapy-pop-body p  { font-size: 0.8rem; color: #9ca3af; margin: 0 0 12px; }
.swapy-pop-body p strong { color: #1A1A1A; }
.swapy-pop-body a { display: block; text-align: center; background: #ED730C; color: #fff !important; border-radius: 10px; padding: 11px; font-size: 0.88rem; font-weight: 800; text-decoration: none; }
.swapy-pop-body a:hover { background: #d4620a; }
</style>
