<script setup>
import { ref, computed, watch, nextTick, onUnmounted } from 'vue'

// ── step state ────────────────────────────────────────────────────────────────
const currentStep = ref(1)
const totalSteps  = 3
const loading     = ref(false)
const posted      = ref(false)
const postedHome  = ref(null)
const stepLabels  = ['Add Photos', 'Home Details', 'Description & Tags']

// ── step 1: photos ────────────────────────────────────────────────────────────
const images        = ref([])
const imagePreviews = ref([])
const primaryIndex  = ref(0)
const isDragging    = ref(false)

function onFileChange(e) { addFiles(Array.from(e.target.files)) }
function onDrop(e) { isDragging.value = false; addFiles(Array.from(e.dataTransfer.files)) }

function addFiles(files) {
  files.forEach(file => {
    if (images.value.length >= 8) return
    if (!['image/jpeg', 'image/png', 'image/webp'].includes(file.type)) return
    images.value.push(file)
    const reader = new FileReader()
    reader.onload = e => imagePreviews.value.push(e.target.result)
    reader.readAsDataURL(file)
  })
}
function removeImage(index) {
  images.value.splice(index, 1)
  imagePreviews.value.splice(index, 1)
  if (primaryIndex.value >= images.value.length) primaryIndex.value = 0
}
function setPrimary(index) { primaryIndex.value = index }

const step1Valid = computed(() => images.value.length > 0)

// ── step 2: home details ──────────────────────────────────────────────────────
const type        = ref('Swap')
const title       = ref('')
const beds        = ref('')
const baths       = ref('')
const sqm         = ref('')
const value       = ref('')
const location    = ref('')
const latitude    = ref(null)
const longitude   = ref(null)
const mapReady    = ref(false)
const geocoding   = ref(false)

const typeOptions = [
  { value: 'Swap',      color: '#ED730C', bg: '#FFF4EC', border: '#fed7aa' },
  { value: 'Rent',      color: '#14b8a6', bg: '#EDFAF9', border: '#99f6e4' },
  { value: 'Sell',      color: '#8b5cf6', bg: '#F5F3FF', border: '#ddd6fe' },
  { value: 'Co-living', color: '#f59e0b', bg: '#FFFBEB', border: '#fde68a' },
]
const bedsOptions = ['Studio', '1', '2', '3', '4', '5+']

const valueLabel = computed(() => {
  if (type.value === 'Sell') return 'Selling Price (₱)'
  if (type.value === 'Rent' || type.value === 'Co-living') return 'Monthly Rent (₱)'
  return 'Estimated Value (₱)'
})

const step2Valid = computed(() =>
  title.value.trim() &&
  beds.value &&
  String(baths.value).trim() !== '' &&
  String(value.value).trim() !== ''
)

// ── Leaflet loader ────────────────────────────────────────────────────────────
let leafletMap = null, leafletMarker = null

function loadLeaflet() {
  return new Promise((resolve) => {
    if (window.L) { resolve(); return }
    if (!document.getElementById('leaflet-css')) {
      const link = document.createElement('link')
      link.id = 'leaflet-css'; link.rel = 'stylesheet'
      link.href = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css'
      document.head.appendChild(link)
    }
    const script = document.createElement('script')
    script.src = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js'
    script.onload = resolve
    document.head.appendChild(script)
  })
}

async function initMap() {
  if (mapReady.value) { if (leafletMap) leafletMap.invalidateSize(); return }
  await loadLeaflet()

  leafletMap = window.L.map('home-map', { center: [12.8797, 121.7740], zoom: 6, zoomControl: true })
  window.L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap contributors', maxZoom: 19,
  }).addTo(leafletMap)

  const orangeIcon = window.L.divIcon({
    className: '',
    html: `<div style="width:32px;height:32px;background:#ED730C;border:3px solid #fff;border-radius:50% 50% 50% 0;transform:rotate(-45deg);box-shadow:0 2px 8px rgba(0,0,0,0.3);"></div>`,
    iconSize: [32, 32], iconAnchor: [16, 32],
  })

  leafletMap.on('click', async (e) => {
    latitude.value = e.latlng.lat
    longitude.value = e.latlng.lng
    if (leafletMarker) leafletMarker.setLatLng(e.latlng)
    else leafletMarker = window.L.marker(e.latlng, { icon: orangeIcon }).addTo(leafletMap)

    geocoding.value = true
    try {
      const res = await fetch(`https://nominatim.openstreetmap.org/reverse?lat=${e.latlng.lat}&lon=${e.latlng.lng}&format=json`, { headers: { 'Accept-Language': 'en' } })
      const data = await res.json()
      if (data && data.display_name) location.value = data.display_name
    } catch (err) { console.error('Reverse geocode failed', err) }
    finally { geocoding.value = false }
  })

  mapReady.value = true
  setTimeout(() => leafletMap?.invalidateSize(), 200)
}

watch(currentStep, async (step) => {
  if (step === 2) { await nextTick(); initMap() }
})
onUnmounted(() => { if (leafletMap) { leafletMap.remove(); leafletMap = null } })

// ── step 3: description, tags, swap terms ─────────────────────────────────────
const description = ref('')
const swapTerms   = ref('')
const tags        = ref([])
const tagOptions  = [
  'Furnished', 'Semi-furnished', 'Unfurnished', 'Pet-friendly', 'With parking',
  'Near transit', 'High-floor', 'Pool', 'Gym', '24/7 Security', 'Balcony', 'Newly renovated',
]
function toggleTag(t) {
  const i = tags.value.indexOf(t)
  if (i === -1) tags.value.push(t)
  else tags.value.splice(i, 1)
}

// ── navigation ────────────────────────────────────────────────────────────────
function goNext() {
  if (currentStep.value === 1 && !step1Valid.value) return
  if (currentStep.value === 2 && !step2Valid.value) return
  if (currentStep.value < totalSteps) currentStep.value++
}
function goBack() { if (currentStep.value > 1) currentStep.value-- }

// ── submit ────────────────────────────────────────────────────────────────────
async function publishHome() {
  loading.value = true
  const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')

  const formData = new FormData()
  formData.append('type',        type.value)
  formData.append('title',       title.value)
  formData.append('description', description.value)
  formData.append('beds',        beds.value)
  formData.append('baths',       baths.value)
  formData.append('sqm',         sqm.value)
  formData.append('value',       value.value)
  formData.append('swap_terms',  type.value === 'Swap' ? swapTerms.value : '')
  formData.append('location',    location.value)
  formData.append('latitude',    latitude.value  ?? '')
  formData.append('longitude',   longitude.value ?? '')
  tags.value.forEach(t => formData.append('tags[]', t))

  const reordered = [...images.value]
  const primary   = reordered.splice(primaryIndex.value, 1)[0]
  if (primary) reordered.unshift(primary)
  reordered.forEach(file => formData.append('images[]', file))

  try {
    const res = await fetch('/homes', { method: 'POST', headers: { 'X-CSRF-TOKEN': csrfToken }, body: formData })
    const data = await res.json()
    if (data.success) { postedHome.value = data.home; posted.value = true }
  } catch (e) { console.error('Publish home failed', e) }
  finally { loading.value = false }
}
</script>

<template>
<div style="min-height:100vh;background:#fff;font-family:'DM Sans',sans-serif;">

  <!-- ══════════════════════════════ SUCCESS SCREEN ══════════════════════════════ -->
  <div v-if="posted" style="min-height:100vh;display:flex;align-items:center;justify-content:center;padding:24px;">
    <div style="background:#fff;border-radius:24px;padding:48px 32px;max-width:480px;width:100%;text-align:center;box-shadow:0 4px 24px rgba(0,0,0,0.08);border:1px solid #EDE8E0;">
      <div style="width:72px;height:72px;background:#FFF4EC;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 24px;">
        <svg width="32" height="32" fill="none" stroke="#ED730C" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true"><path d="M20 6L9 17l-5-5"/></svg>
      </div>
      <h2 style="font-size:1.5rem;font-weight:800;color:#3A3330;margin:0 0 10px;">Home Listed!</h2>
      <p style="font-size:0.9rem;color:#6b7280;margin:0 0 32px;">
        <strong style="color:#3A3330;">{{ postedHome?.title }}</strong> is now live for nearby swappers and renters.
      </p>

      <div v-if="imagePreviews.length" style="width:100%;height:160px;border-radius:16px;overflow:hidden;margin-bottom:28px;">
        <img :src="imagePreviews[primaryIndex]" alt="Your home listing photo" style="width:100%;height:100%;object-fit:cover;">
      </div>

      <a href="/homes"     style="display:block;width:100%;padding:14px;background:#ED730C;color:#fff;font-size:0.9rem;font-weight:700;border-radius:12px;text-decoration:none;margin-bottom:10px;box-sizing:border-box;text-align:center;">Browse Homes</a>
      <a href="/dashboard" style="display:block;width:100%;padding:14px;background:#fff;color:#3A3330;font-size:0.9rem;font-weight:700;border:1.5px solid #EDE8E0;border-radius:12px;text-decoration:none;box-sizing:border-box;text-align:center;">Back to Dashboard</a>
    </div>
  </div>

  <!-- ══════════════════════════════ WIZARD ══════════════════════════════ -->
  <template v-else>

    <!-- Top bar -->
    <div style="background:#fff;border-bottom:1px solid #EDE8E0;padding:16px 24px;position:sticky;top:0;z-index:100;">
      <div style="max-width:640px;margin:0 auto;">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;">
          <div style="display:flex;align-items:center;gap:12px;">
            <a href="/dashboard" aria-label="Back to dashboard" style="display:flex;align-items:center;justify-content:center;width:36px;height:36px;border-radius:10px;border:1.5px solid #EDE8E0;color:#6b7280;text-decoration:none;">
              <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <div>
              <p style="font-size:0.62rem;font-weight:700;color:#ED730C;text-transform:uppercase;letter-spacing:.08em;margin:0 0 1px;">Step {{ currentStep }} of {{ totalSteps }}</p>
              <h1 style="font-size:1.1rem;font-weight:800;color:#3A3330;margin:0;">{{ stepLabels[currentStep - 1] }}</h1>
            </div>
          </div>
          <div style="display:flex;gap:6px;align-items:center;">
            <div v-for="n in totalSteps" :key="n" :style="{ width: n === currentStep ? '20px' : '8px', height: '8px', borderRadius: '4px', background: n <= currentStep ? '#ED730C' : '#EDE8E0', transition: 'all .3s' }"></div>
          </div>
        </div>
        <div style="height:3px;background:#f0f0ec;border-radius:2px;" role="progressbar" :aria-valuenow="Math.round((currentStep / totalSteps) * 100)" aria-valuemin="0" aria-valuemax="100" aria-label="Form progress">
          <div :style="{ width: ((currentStep / totalSteps) * 100) + '%', background:'#ED730C', height:'100%', borderRadius:'2px', transition:'width .3s' }"></div>
        </div>
      </div>
    </div>

    <!-- Content -->
    <div style="max-width:640px;margin:0 auto;padding:28px 24px 120px;">

      <!-- ════════════ STEP 1 — Photos ════════════ -->
      <div v-if="currentStep === 1">
        <p style="font-size:0.65rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:#5c5751;margin:0 0 8px;">Home Photos <span style="color:#ef4444;">*</span> <span style="color:#9ca3af;font-weight:400;text-transform:none;letter-spacing:0;">(at least one, up to 8)</span></p>

        <div
          @dragover.prevent="isDragging = true"
          @dragleave="isDragging = false"
          @drop.prevent="onDrop"
          :style="{ border: isDragging ? '2px dashed #ED730C' : '2px dashed #EDE8E0', background: isDragging ? '#FFF4EC' : '#fff', borderRadius: '16px', padding: '48px 24px', textAlign: 'center', transition: 'all .2s', marginBottom: '20px' }"
        >
          <div style="width:52px;height:52px;background:#f3f4f6;border-radius:14px;display:flex;align-items:center;justify-content:center;margin:0 auto 14px;">
            <svg width="24" height="24" fill="none" stroke="#9ca3af" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="M21 15l-5-5L5 21"/></svg>
          </div>
          <p style="font-size:0.9rem;font-weight:700;color:#3A3330;margin:0 0 4px;">Drag and drop your photos here</p>
          <p style="font-size:0.78rem;color:#857c70;margin:0 0 16px;">PNG, JPG or WEBP up to 10MB</p>
          <label style="display:inline-flex;align-items:center;gap:8px;padding:10px 20px;background:#ED730C;color:#fff;border-radius:10px;cursor:pointer;font-size:0.845rem;font-weight:700;font-family:'DM Sans',sans-serif;">
            <svg width="14" height="14" fill="none" stroke="#fff" stroke-width="2.5" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4M17 8l-5-5-5 5M12 3v12"/></svg>
            Upload from device
            <input type="file" multiple accept="image/jpeg,image/png,image/webp" class="sr-only" aria-label="Upload home photos" @change="onFileChange">
          </label>
        </div>

        <div v-if="imagePreviews.length">
          <p style="font-size:0.65rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:#5c5751;margin:0 0 10px;">Uploaded Photos <span style="color:#9ca3af;font-weight:400;text-transform:none;letter-spacing:0;">— tap to set the main photo</span></p>
          <div style="display:flex;flex-wrap:wrap;gap:10px;">
            <div
              v-for="(src, i) in imagePreviews" :key="i"
              role="button" tabindex="0"
              :aria-label="'Set photo ' + (i + 1) + ' as the main photo'"
              :aria-pressed="i === primaryIndex"
              @click="setPrimary(i)"
              @keydown.enter.prevent="setPrimary(i)"
              @keydown.space.prevent="setPrimary(i)"
              :style="{ position: 'relative', width: '96px', height: '96px', borderRadius: '10px', overflow: 'hidden', cursor: 'pointer', flexShrink: '0', border: i === primaryIndex ? '2.5px solid #ED730C' : '2px solid #EDE8E0' }"
            >
              <img :src="src" :alt="'Home photo ' + (i + 1)" style="width:100%;height:100%;object-fit:cover;">
              <div v-if="i === primaryIndex" style="position:absolute;bottom:0;left:0;right:0;background:#ED730C;color:#fff;font-size:0.55rem;font-weight:800;text-align:center;padding:3px;text-transform:uppercase;letter-spacing:.05em;">Main</div>
              <button @click.stop="removeImage(i)" :aria-label="'Remove photo ' + (i + 1)" style="position:absolute;top:4px;right:4px;width:18px;height:18px;background:rgba(0,0,0,0.55);border:none;border-radius:50%;cursor:pointer;display:flex;align-items:center;justify-content:center;">
                <svg width="8" height="8" fill="none" stroke="#fff" stroke-width="2.5" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" d="M6 18L18 6M6 6l12 12"/></svg>
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- ════════════ STEP 2 — Home Details ════════════ -->
      <div v-else-if="currentStep === 2">
        <!-- Listing type -->
        <div style="margin-bottom:18px;">
          <span style="font-size:0.65rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:#5c5751;display:block;margin-bottom:8px;">Listing Type <span style="color:#ef4444;">*</span></span>
          <div role="radiogroup" aria-label="Listing type" style="display:grid;grid-template-columns:repeat(2,1fr);gap:10px;">
            <button v-for="opt in typeOptions" :key="opt.value" type="button" role="radio" :aria-checked="type === opt.value" @click="type = opt.value"
              :style="{ padding:'13px', borderRadius:'12px', textAlign:'center', fontSize:'0.875rem', fontWeight:'700', cursor:'pointer', fontFamily:`'DM Sans',sans-serif`, transition:'all .15s', border: type === opt.value ? '2px solid ' + opt.color : '1.5px solid #EDE8E0', background: type === opt.value ? opt.bg : '#fff', color: type === opt.value ? opt.color : '#6b7280' }">
              {{ opt.value }}
            </button>
          </div>
        </div>

        <!-- Title -->
        <div style="margin-bottom:14px;">
          <label for="hm-title" style="font-size:0.65rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:#5c5751;display:block;margin-bottom:6px;">Listing Title <span style="color:#ef4444;">*</span></label>
          <input id="hm-title" v-model="title" required placeholder="e.g., Modern Studio in BGC" style="width:100%;padding:11px 13px;border:1.5px solid #EDE8E0;border-radius:11px;font-size:0.9rem;font-family:'DM Sans',sans-serif;outline:none;color:#3A3330;box-sizing:border-box;" @focus="e => e.target.style.borderColor='#ED730C'" @blur="e => e.target.style.borderColor='#EDE8E0'">
        </div>

        <!-- Beds + Baths -->
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:14px;">
          <div>
            <label for="hm-beds" style="font-size:0.65rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:#5c5751;display:block;margin-bottom:6px;">Bedrooms <span style="color:#ef4444;">*</span></label>
            <select id="hm-beds" v-model="beds" style="width:100%;padding:11px 13px;border:1.5px solid #EDE8E0;border-radius:11px;font-size:0.9rem;font-family:'DM Sans',sans-serif;outline:none;color:#3A3330;background:#fff;box-sizing:border-box;">
              <option value="" disabled>Select</option>
              <option v-for="b in bedsOptions" :key="b" :value="b">{{ b === 'Studio' ? 'Studio' : b + ' Bed' + (b === '1' ? '' : 's') }}</option>
            </select>
          </div>
          <div>
            <label for="hm-baths" style="font-size:0.65rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:#5c5751;display:block;margin-bottom:6px;">Bathrooms <span style="color:#ef4444;">*</span></label>
            <input id="hm-baths" v-model="baths" type="number" min="0" max="50" inputmode="numeric" placeholder="e.g., 2" style="width:100%;padding:11px 13px;border:1.5px solid #EDE8E0;border-radius:11px;font-size:0.9rem;font-family:'DM Sans',sans-serif;outline:none;color:#3A3330;box-sizing:border-box;">
          </div>
        </div>

        <!-- Sqm + Value -->
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:14px;">
          <div>
            <label for="hm-sqm" style="font-size:0.65rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:#5c5751;display:block;margin-bottom:6px;">Floor Area (sqm)</label>
            <input id="hm-sqm" v-model="sqm" type="number" min="0" inputmode="numeric" placeholder="e.g., 68" style="width:100%;padding:11px 13px;border:1.5px solid #EDE8E0;border-radius:11px;font-size:0.9rem;font-family:'DM Sans',sans-serif;outline:none;color:#3A3330;box-sizing:border-box;">
          </div>
          <div>
            <label for="hm-value" style="font-size:0.65rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:#5c5751;display:block;margin-bottom:6px;">{{ valueLabel }} <span style="color:#ef4444;">*</span></label>
            <input id="hm-value" v-model="value" type="number" min="0" inputmode="numeric" placeholder="e.g., 35000" style="width:100%;padding:11px 13px;border:1.5px solid #EDE8E0;border-radius:11px;font-size:0.9rem;font-family:'DM Sans',sans-serif;outline:none;color:#3A3330;box-sizing:border-box;">
          </div>
        </div>

        <!-- Location map -->
        <div style="margin-bottom:4px;">
          <label style="font-size:0.65rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:#5c5751;display:block;margin-bottom:6px;">Location <span style="color:#9ca3af;font-weight:400;text-transform:none;letter-spacing:0;">— click the map to pin</span></label>
          <div id="home-map" style="position:relative;z-index:0;width:100%;height:240px;border-radius:16px;border:1.5px solid #EDE8E0;overflow:hidden;margin-bottom:8px;background:#f3f4f6;"></div>
          <div style="position:relative;">
            <input v-model="location" aria-label="Selected home location" placeholder="Click the map to set a location..." readonly style="width:100%;padding:11px 13px 11px 38px;border:1.5px solid #EDE8E0;border-radius:11px;font-size:0.85rem;font-family:'DM Sans',sans-serif;outline:none;color:#3A3330;background:#FAF8F5;box-sizing:border-box;cursor:default;">
            <svg style="position:absolute;left:13px;top:50%;transform:translateY(-50%);" width="15" height="15" fill="none" stroke="#ED730C" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z"/><circle cx="12" cy="9" r="2.5"/></svg>
          </div>
          <p v-if="geocoding" style="font-size:0.72rem;color:#ED730C;margin:5px 0 0;">Finding address...</p>
          <p v-else-if="latitude" style="font-size:0.72rem;color:#9ca3af;margin:5px 0 0;">{{ latitude.toFixed(5) }}, {{ longitude.toFixed(5) }}</p>
        </div>
      </div>

      <!-- ════════════ STEP 3 — Description & Tags ════════════ -->
      <div v-else-if="currentStep === 3">
        <!-- Description -->
        <div style="margin-bottom:18px;">
          <label for="hm-desc" style="font-size:0.65rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:#5c5751;display:block;margin-bottom:6px;">Description <span style="color:#9ca3af;font-weight:400;text-transform:none;letter-spacing:0;">(optional)</span></label>
          <textarea id="hm-desc" v-model="description" placeholder="Describe the space, the neighborhood, what makes it special..." rows="3" style="width:100%;padding:11px 13px;border:1.5px solid #EDE8E0;border-radius:11px;font-size:0.9rem;font-family:'DM Sans',sans-serif;outline:none;color:#3A3330;resize:vertical;box-sizing:border-box;" @focus="e => e.target.style.borderColor='#ED730C'" @blur="e => e.target.style.borderColor='#EDE8E0'"></textarea>
        </div>

        <!-- Tags -->
        <div style="margin-bottom:18px;">
          <span style="font-size:0.65rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:#5c5751;display:block;margin-bottom:10px;">Features &amp; Amenities</span>
          <div style="display:flex;flex-wrap:wrap;gap:8px;">
            <button v-for="t in tagOptions" :key="t" type="button" :aria-pressed="tags.includes(t)" @click="toggleTag(t)"
              :style="{ padding:'8px 14px', borderRadius:'999px', fontSize:'0.8rem', fontWeight:'700', cursor:'pointer', fontFamily:`'DM Sans',sans-serif`, transition:'all .15s', border: tags.includes(t) ? '1.5px solid #ED730C' : '1.5px solid #EDE8E0', background: tags.includes(t) ? '#FFF4EC' : '#fff', color: tags.includes(t) ? '#ED730C' : '#6b7280' }">
              {{ t }}
            </button>
          </div>
        </div>

        <!-- Swap terms (Swap listings only) -->
        <div v-if="type === 'Swap'" style="margin-bottom:4px;">
          <label for="hm-swap" style="font-size:0.65rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:#5c5751;display:block;margin-bottom:6px;">What would you swap for? <span style="color:#9ca3af;font-weight:400;text-transform:none;letter-spacing:0;">(optional)</span></label>
          <textarea id="hm-swap" v-model="swapTerms" placeholder="e.g., Open to swap with a condo in Makati or Ortigas, similar size..." rows="2" style="width:100%;padding:11px 13px;border:1.5px solid #EDE8E0;border-radius:11px;font-size:0.9rem;font-family:'DM Sans',sans-serif;outline:none;color:#3A3330;resize:vertical;box-sizing:border-box;" @focus="e => e.target.style.borderColor='#ED730C'" @blur="e => e.target.style.borderColor='#EDE8E0'"></textarea>
        </div>
      </div>
    </div>

    <!-- ══════════════════════════════ STICKY BOTTOM NAV ══════════════════════════════ -->
    <div style="position:fixed;bottom:0;left:0;right:0;background:#fff;border-top:1px solid #EDE8E0;padding:16px 24px;z-index:100;">
      <div style="max-width:640px;margin:0 auto;display:flex;justify-content:space-between;align-items:center;gap:12px;">
        <button v-if="currentStep > 1" @click="goBack" style="padding:13px 24px;background:#fff;color:#3A3330;font-size:0.9rem;font-weight:700;border:1.5px solid #EDE8E0;border-radius:12px;cursor:pointer;font-family:'DM Sans',sans-serif;display:flex;align-items:center;gap:8px;">
          <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
          Back
        </button>
        <div v-else></div>

        <!-- Step 1: Next (requires at least one photo) -->
        <button v-if="currentStep === 1" @click="goNext" :disabled="!step1Valid" :style="{ padding:'13px 32px', fontSize:'0.9rem', fontWeight:'700', background: step1Valid ? '#ED730C' : '#EDE8E0', color: step1Valid ? '#fff' : '#9ca3af', border:'none', borderRadius:'12px', cursor: step1Valid ? 'pointer' : 'not-allowed', fontFamily:`'DM Sans',sans-serif`, flex: 1 }">Next</button>

        <!-- Step 2: Next (gated) -->
        <button v-if="currentStep === 2" @click="goNext" :disabled="!step2Valid" :style="{ padding:'13px 32px', fontSize:'0.9rem', fontWeight:'700', background: step2Valid ? '#ED730C' : '#EDE8E0', color: step2Valid ? '#fff' : '#9ca3af', border:'none', borderRadius:'12px', cursor: step2Valid ? 'pointer' : 'not-allowed', fontFamily:`'DM Sans',sans-serif`, flex: 1 }">Next</button>

        <!-- Step 3: Publish -->
        <button v-if="currentStep === 3" @click="publishHome" :disabled="loading" style="flex:1;padding:13px 32px;background:#ED730C;color:#fff;font-size:0.9rem;font-weight:700;border:none;border-radius:12px;cursor:pointer;font-family:'DM Sans',sans-serif;display:inline-flex;align-items:center;justify-content:center;gap:8px;">
          <span v-if="!loading">Publish Listing</span>
          <span v-else style="display:inline-flex;align-items:center;gap:6px;">
            <svg style="width:14px;height:14px;animation:spin .9s linear infinite;" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.5" aria-hidden="true"><path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4"/></svg>
            Publishing...
          </span>
        </button>
      </div>
    </div>

  </template>
</div>
</template>

<style scoped>
@keyframes spin { to { transform: rotate(360deg); } }

:deep(.leaflet-control-attribution) {
  font-size: 0.55rem !important;
  opacity: 0.4 !important;
  background: transparent !important;
  box-shadow: none !important;
}

/* ── Accessibility ───────────────────────────────────────────── */
.sr-only {
  position: absolute;
  width: 1px; height: 1px;
  padding: 0; margin: -1px;
  overflow: hidden; clip: rect(0, 0, 0, 0);
  white-space: nowrap; border: 0;
}
input:focus-visible,
textarea:focus-visible,
select:focus-visible {
  outline: 2px solid #ED730C !important;
  outline-offset: 2px;
  border-color: #ED730C !important;
}
a:focus-visible,
button:focus-visible,
[role="button"]:focus-visible,
[role="radio"]:focus-visible {
  outline: 2px solid #ED730C;
  outline-offset: 2px;
}
label:focus-within { outline: 2px solid #ED730C; outline-offset: 2px; }

::placeholder { color: #9b938a; opacity: 1; }

@media (prefers-reduced-motion: reduce) {
  *, *::before, *::after {
    animation-duration: 0.001ms !important;
    animation-iteration-count: 1 !important;
    transition-duration: 0.001ms !important;
    scroll-behavior: auto !important;
  }
}
</style>
