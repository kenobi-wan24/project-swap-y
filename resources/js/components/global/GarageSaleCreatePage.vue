<script setup>
import { ref, computed, watch, nextTick, onUnmounted } from 'vue'

// ── step state ────────────────────────────────────────────────────────────────
const currentStep = ref(1)
const totalSteps  = 3
const loading     = ref(false)
const posted      = ref(false)
const postedSale  = ref(null)

// ── step 1: cover & details ───────────────────────────────────────────────────
const coverFile    = ref(null)
const coverPreview = ref(null)
const title        = ref('')
const description  = ref('')
const isDragging   = ref(false)

function onCoverChange(e) {
    const file = e.target.files[0]
    if (file) setCover(file)
}

function onCoverDrop(e) {
    isDragging.value = false
    const file = e.dataTransfer.files[0]
    if (file) setCover(file)
}

function setCover(file) {
    if (!['image/jpeg', 'image/png', 'image/webp'].includes(file.type)) return
    coverFile.value = file
    const reader = new FileReader()
    reader.onload = e => { coverPreview.value = e.target.result }
    reader.readAsDataURL(file)
}

function removeCover() {
    coverFile.value    = null
    coverPreview.value = null
}

const step1Valid = computed(() => title.value.trim().length > 0)

// ── step 2: location ──────────────────────────────────────────────────────────
const location  = ref('')
const latitude  = ref(null)
const longitude = ref(null)
const mapReady  = ref(false)
const geocoding = ref(false)

let leafletMap    = null
let leafletMarker = null

function loadLeaflet() {
    return new Promise((resolve) => {
        if (window.L) { resolve(); return }
        if (!document.getElementById('leaflet-css')) {
            const link = document.createElement('link')
            link.id   = 'leaflet-css'
            link.rel  = 'stylesheet'
            link.href = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css'
            document.head.appendChild(link)
        }
        const script   = document.createElement('script')
        script.src     = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js'
        script.onload  = resolve
        document.head.appendChild(script)
    })
}

async function initMap() {
    if (mapReady.value) {
        if (leafletMap) leafletMap.invalidateSize()
        return
    }
    await loadLeaflet()

    leafletMap = window.L.map('gs-map', {
        center: [12.8797, 121.7740],
        zoom: 6,
        zoomControl: true,
    })

    window.L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors',
        maxZoom: 19,
    }).addTo(leafletMap)

    const tealIcon = window.L.divIcon({
        className: '',
        html: `<div style="width:32px;height:32px;background:#ED730C;border:3px solid #fff;border-radius:50% 50% 50% 0;transform:rotate(-45deg);box-shadow:0 2px 8px rgba(0,0,0,0.3);"></div>`,
        iconSize: [32, 32],
        iconAnchor: [16, 32],
    })

    leafletMap.on('click', async (e) => {
        latitude.value  = e.latlng.lat
        longitude.value = e.latlng.lng

        if (leafletMarker) {
            leafletMarker.setLatLng(e.latlng)
        } else {
            leafletMarker = window.L.marker(e.latlng, { icon: tealIcon }).addTo(leafletMap)
        }

        geocoding.value = true
        try {
            const res  = await fetch(`https://nominatim.openstreetmap.org/reverse?lat=${e.latlng.lat}&lon=${e.latlng.lng}&format=json`, { headers: { 'Accept-Language': 'en' } })
            const data = await res.json()
            if (data?.display_name) location.value = data.display_name
        } catch (err) {
            console.error('Reverse geocode failed', err)
        } finally {
            geocoding.value = false
        }
    })

    mapReady.value = true
    setTimeout(() => leafletMap?.invalidateSize(), 200)
}

watch(currentStep, async (step) => {
    if (step === 2) {
        await nextTick()
        initMap()
    }
})

onUnmounted(() => {
    if (leafletMap) { leafletMap.remove(); leafletMap = null }
})

// ── step 3: items ─────────────────────────────────────────────────────────────
const saleItems = ref([])
const showItemForm = ref(false)

const categories = ['Electronics', 'Clothing', 'Furniture', 'Books', 'Sports', 'DIY', 'Art', 'Collectibles', 'Other']
const conditions = [
    { value: 'new',      label: 'New'      },
    { value: 'like_new', label: 'Like New' },
    { value: 'good',     label: 'Good'     },
    { value: 'fair',     label: 'Fair'     },
]

const blankItem = () => ({
    title: '', category: '', condition: '', value: '', wants: '', swap_terms: '',
    imageFile: null, imagePreview: null,
})

const draftItem = ref(blankItem())

function onItemImageChange(e) {
    const file = e.target.files[0]
    if (!file) return
    draftItem.value.imageFile = file
    const reader = new FileReader()
    reader.onload = e => { draftItem.value.imagePreview = e.target.result }
    reader.readAsDataURL(file)
}

const draftValid = computed(() =>
    draftItem.value.title.trim() &&
    draftItem.value.category &&
    draftItem.value.condition
)

function addItem() {
    if (!draftValid.value) return
    saleItems.value.push({ ...draftItem.value })
    draftItem.value = blankItem()
    showItemForm.value = false
}

function removeItem(i) {
    saleItems.value.splice(i, 1)
}

function cancelItemForm() {
    draftItem.value    = blankItem()
    showItemForm.value = false
}

// ── navigation ────────────────────────────────────────────────────────────────
function goNext() { if (currentStep.value < totalSteps) currentStep.value++ }
function goBack() { if (currentStep.value > 1) currentStep.value-- }

// ── submit ────────────────────────────────────────────────────────────────────
async function publishSale() {
    loading.value = true
    const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')

    const fd = new FormData()
    fd.append('title',       title.value)
    fd.append('description', description.value)
    fd.append('location',    location.value)
    fd.append('latitude',    latitude.value  ?? '')
    fd.append('longitude',   longitude.value ?? '')
    if (coverFile.value) fd.append('cover_image', coverFile.value)

    saleItems.value.forEach((item, i) => {
        fd.append(`items[${i}][title]`,      item.title)
        fd.append(`items[${i}][category]`,   item.category)
        fd.append(`items[${i}][condition]`,  item.condition)
        fd.append(`items[${i}][value]`,      item.value || 0)
        fd.append(`items[${i}][wants]`,      item.wants || '')
        fd.append(`items[${i}][swap_terms]`, item.swap_terms || '')
        if (item.imageFile) fd.append(`item_images[${i}]`, item.imageFile)
    })

    try {
        const res  = await fetch('/garage-sale', { method: 'POST', headers: { 'X-CSRF-TOKEN': csrf }, body: fd })
        const data = await res.json()
        if (data.success) {
            postedSale.value = data.sale
            posted.value     = true
        }
    } catch (e) {
        console.error('Publish garage sale failed', e)
    } finally {
        loading.value = false
    }
}

const stepLabels = ['Sale Details', 'Location', 'Add Items']
</script>

<template>
<div style="min-height:100vh;background:#fff;font-family:'DM Sans',sans-serif;">

    <!-- ══════════════════════════════
         SUCCESS SCREEN
    ══════════════════════════════ -->
    <div v-if="posted" style="min-height:100vh;display:flex;align-items:center;justify-content:center;padding:24px;">
        <div style="background:#fff;border-radius:24px;padding:48px 32px;max-width:480px;width:100%;text-align:center;box-shadow:0 4px 24px rgba(0,0,0,0.08);">
            <div style="width:72px;height:72px;background:#FFF4EC;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 24px;">
                <svg width="32" height="32" fill="none" stroke="#ED730C" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                    <path d="M20 6L9 17l-5-5"/>
                </svg>
            </div>
            <h2 style="font-size:1.5rem;font-weight:800;color:#3A3330;margin:0 0 10px;">Garage Sale is Live!</h2>
            <p style="font-size:0.9rem;color:#6b7280;margin:0 0 32px;">
                <strong style="color:#3A3330;">{{ postedSale?.title }}</strong> is now visible to nearby buyers and swappers.
            </p>

            <div v-if="coverPreview" style="width:100%;height:160px;border-radius:16px;overflow:hidden;margin-bottom:28px;">
                <img :src="coverPreview" alt="Your garage sale cover photo" style="width:100%;height:100%;object-fit:cover;">
            </div>

            <a href="/garage-sale" style="display:block;width:100%;padding:14px;background:#ED730C;color:#fff;font-size:0.9rem;font-weight:700;border-radius:12px;text-decoration:none;margin-bottom:10px;box-sizing:border-box;text-align:center;">Browse Garage Sales</a>
            <a href="/dashboard"   style="display:block;width:100%;padding:14px;background:#fff;color:#3A3330;font-size:0.9rem;font-weight:700;border:1.5px solid #EDE8E0;border-radius:12px;text-decoration:none;box-sizing:border-box;text-align:center;">Back to Dashboard</a>
        </div>
    </div>

    <!-- ══════════════════════════════
         WIZARD
    ══════════════════════════════ -->
    <template v-else>

        <!-- Top bar -->
        <div style="background:#fff;border-bottom:1px solid #EDE8E0;padding:16px 24px;position:sticky;top:0;z-index:100;">
            <div style="max-width:640px;margin:0 auto;">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;">
                    <div style="display:flex;align-items:center;gap:12px;">
                        <a href="/dashboard" aria-label="Back to dashboard" style="display:flex;align-items:center;justify-content:center;width:36px;height:36px;border-radius:10px;border:1.5px solid #EDE8E0;color:#6b7280;text-decoration:none;">
                            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                            </svg>
                        </a>
                        <div>
                            <p style="font-size:0.62rem;font-weight:700;color:#ED730C;text-transform:uppercase;letter-spacing:.08em;margin:0 0 1px;">Step {{ currentStep }} of {{ totalSteps }}</p>
                            <h1 style="font-size:1.1rem;font-weight:800;color:#3A3330;margin:0;">{{ stepLabels[currentStep - 1] }}</h1>
                        </div>
                    </div>
                    <!-- Step dots -->
                    <div style="display:flex;gap:6px;align-items:center;">
                        <div v-for="n in totalSteps" :key="n"
                            :style="{
                                width: n === currentStep ? '20px' : '8px',
                                height: '8px',
                                borderRadius: '4px',
                                background: n <= currentStep ? '#ED730C' : '#EDE8E0',
                                transition: 'all .3s',
                            }"
                        ></div>
                    </div>
                </div>
                <!-- Progress bar -->
                <div style="height:3px;background:#f0f0ec;border-radius:2px;">
                    <div :style="{ width: ((currentStep / totalSteps) * 100) + '%', background:'#ED730C', height:'100%', borderRadius:'2px', transition:'width .3s' }"></div>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div style="max-width:640px;margin:0 auto;padding:28px 24px 120px;">

            <!-- ════════════
                 STEP 1 — Sale Details
            ════════════ -->
            <div v-if="currentStep === 1">

                <!-- Cover photo -->
                <div style="margin-bottom:24px;">
                    <p style="font-size:0.65rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:#5c5751;margin:0 0 8px;">Cover Photo</p>

                    <div v-if="!coverPreview"
                        @dragover.prevent="isDragging = true"
                        @dragleave="isDragging = false"
                        @drop.prevent="onCoverDrop"
                        :style="{
                            border: isDragging ? '2px dashed #ED730C' : '2px dashed #EDE8E0',
                            background: isDragging ? '#FFF4EC' : '#fff',
                            borderRadius: '16px',
                            padding: '48px 24px',
                            textAlign: 'center',
                            transition: 'all .2s',
                        }"
                    >
                        <div style="width:52px;height:52px;background:#f3f4f6;border-radius:14px;display:flex;align-items:center;justify-content:center;margin:0 auto 14px;">
                            <svg width="24" height="24" fill="none" stroke="#9ca3af" stroke-width="1.8" viewBox="0 0 24 24">
                                <rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/>
                                <path d="M21 15l-5-5L5 21"/>
                            </svg>
                        </div>
                        <p style="font-size:0.9rem;font-weight:700;color:#3A3330;margin:0 0 4px;">Add a cover photo</p>
                        <p style="font-size:0.78rem;color:#857c70;margin:0 0 16px;">PNG, JPG or WEBP up to 10MB</p>
                        <label style="display:inline-flex;align-items:center;gap:8px;padding:10px 20px;background:#ED730C;color:#fff;border-radius:10px;cursor:pointer;font-size:0.845rem;font-weight:700;font-family:'DM Sans',sans-serif;">
                            <svg width="14" height="14" fill="none" stroke="#fff" stroke-width="2.5" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4M17 8l-5-5-5 5M12 3v12"/>
                            </svg>
                            Upload photo
                            <input type="file" accept="image/jpeg,image/png,image/webp" class="sr-only" aria-label="Upload cover photo" @change="onCoverChange">
                        </label>
                    </div>

                    <div v-else style="position:relative;border-radius:16px;overflow:hidden;">
                        <img :src="coverPreview" alt="Cover photo preview" style="width:100%;height:200px;object-fit:cover;display:block;">
                        <button @click="removeCover" aria-label="Remove cover photo" style="position:absolute;top:10px;right:10px;background:rgba(0,0,0,0.6);border:none;border-radius:8px;color:#fff;padding:6px 12px;font-size:0.78rem;font-weight:700;cursor:pointer;font-family:'DM Sans',sans-serif;">Remove</button>
                    </div>
                </div>

                <!-- Sale title -->
                <div style="margin-bottom:16px;">
                    <label for="gs-title" style="font-size:0.65rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:#5c5751;display:block;margin-bottom:6px;">Sale Title <span style="color:#ef4444;">*</span></label>
                    <input
                        id="gs-title"
                        v-model="title"
                        required
                        placeholder="e.g., Weekend Declutter Sale, Tech & Gadgets Garage Sale"
                        style="width:100%;padding:12px 14px;border:1.5px solid #EDE8E0;border-radius:12px;font-size:0.9rem;font-family:'DM Sans',sans-serif;outline:none;color:#3A3330;box-sizing:border-box;"
                        @focus="e => e.target.style.borderColor='#ED730C'"
                        @blur="e => e.target.style.borderColor='#EDE8E0'"
                    >
                </div>

                <!-- Description -->
                <div style="margin-bottom:8px;">
                    <label for="gs-desc" style="font-size:0.65rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:#5c5751;display:block;margin-bottom:6px;">Description <span style="color:#9ca3af;font-weight:400;text-transform:none;letter-spacing:0;">(optional)</span></label>
                    <textarea
                        id="gs-desc"
                        v-model="description"
                        placeholder="Tell buyers what to expect — types of items, condition, any special deals..."
                        rows="4"
                        style="width:100%;padding:12px 14px;border:1.5px solid #EDE8E0;border-radius:12px;font-size:0.9rem;font-family:'DM Sans',sans-serif;outline:none;color:#3A3330;resize:vertical;box-sizing:border-box;"
                        @focus="e => e.target.style.borderColor='#ED730C'"
                        @blur="e => e.target.style.borderColor='#EDE8E0'"
                    ></textarea>
                </div>
            </div>

            <!-- ════════════
                 STEP 2 — Location
            ════════════ -->
            <div v-else-if="currentStep === 2">
                <p style="font-size:0.875rem;color:#6b7280;margin:0 0 16px;">Pin where your garage sale will take place. Buyers nearby will see it first.</p>

                <div id="gs-map" style="position:relative;z-index:0;width:100%;height:320px;border-radius:16px;border:1.5px solid #EDE8E0;overflow:hidden;margin-bottom:12px;background:#f3f4f6;"></div>

                <div style="position:relative;margin-bottom:8px;">
                    <input
                        v-model="location"
                        aria-label="Selected sale location"
                        placeholder="Click the map to pin your location..."
                        readonly
                        style="width:100%;padding:12px 14px 12px 40px;border:1.5px solid #EDE8E0;border-radius:12px;font-size:0.875rem;font-family:'DM Sans',sans-serif;outline:none;color:#3A3330;background:#FAF8F5;box-sizing:border-box;cursor:default;"
                    >
                    <svg style="position:absolute;left:13px;top:50%;transform:translateY(-50%);" width="15" height="15" fill="none" stroke="#ED730C" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z"/>
                        <circle cx="12" cy="9" r="2.5"/>
                    </svg>
                </div>

                <p v-if="geocoding" style="font-size:0.72rem;color:#ED730C;margin:0;font-family:'DM Sans',sans-serif;">Finding address...</p>
                <p v-else-if="latitude" style="font-size:0.72rem;color:#9ca3af;margin:0;font-family:'DM Sans',sans-serif;">{{ latitude.toFixed(5) }}, {{ longitude.toFixed(5) }}</p>

                <div style="background:#FFF4EC;border-radius:12px;padding:14px;margin-top:16px;display:flex;gap:10px;align-items:flex-start;">
                    <svg width="16" height="16" fill="none" stroke="#ED730C" stroke-width="2" viewBox="0 0 24 24" style="flex-shrink:0;margin-top:1px;">
                        <circle cx="12" cy="12" r="10"/><path stroke-linecap="round" d="M12 8v4M12 16h.01"/>
                    </svg>
                    <p style="font-size:0.8rem;color:#ED730C;margin:0;line-height:1.5;">You can skip this step and add your location later from your sale settings.</p>
                </div>
            </div>

            <!-- ════════════
                 STEP 3 — Add Items
            ════════════ -->
            <div v-else-if="currentStep === 3">

                <!-- Items list -->
                <div v-if="saleItems.length" style="margin-bottom:20px;display:flex;flex-direction:column;gap:12px;">
                    <div
                        v-for="(item, i) in saleItems"
                        :key="i"
                        style="display:flex;align-items:center;gap:14px;background:#fff;border-radius:14px;border:1.5px solid #EDE8E0;padding:12px;"
                    >
                        <div style="width:56px;height:56px;border-radius:10px;overflow:hidden;flex-shrink:0;background:#f3f4f6;">
                            <img v-if="item.imagePreview" :src="item.imagePreview" :alt="item.title || 'Item photo'" style="width:100%;height:100%;object-fit:cover;">
                            <div v-else style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;">
                                <svg width="20" height="20" fill="none" stroke="#d1d5db" stroke-width="1.5" viewBox="0 0 24 24">
                                    <rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="M21 15l-5-5L5 21"/>
                                </svg>
                            </div>
                        </div>
                        <div style="flex:1;min-width:0;">
                            <p style="font-size:0.875rem;font-weight:700;color:#3A3330;margin:0 0 2px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ item.title }}</p>
                            <p style="font-size:0.75rem;color:#9ca3af;margin:0;">{{ item.category }} · {{ conditions.find(c=>c.value===item.condition)?.label }} <span v-if="item.value"> · ₱{{ parseInt(item.value).toLocaleString() }}</span></p>
                        </div>
                        <button @click="removeItem(i)" :aria-label="'Remove ' + (item.title || 'item')" style="background:none;border:none;cursor:pointer;color:#9ca3af;padding:4px;flex-shrink:0;">
                            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Empty state -->
                <div v-if="!saleItems.length && !showItemForm" style="text-align:center;padding:40px 24px;background:#fff;border-radius:16px;border:2px dashed #EDE8E0;margin-bottom:20px;">
                    <div style="width:48px;height:48px;background:#f3f4f6;border-radius:12px;display:flex;align-items:center;justify-content:center;margin:0 auto 12px;">
                        <svg width="22" height="22" fill="none" stroke="#9ca3af" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20 7H4a2 2 0 00-2 2v10a2 2 0 002 2h16a2 2 0 002-2V9a2 2 0 00-2-2zM16 3H8a2 2 0 00-2 2v2h12V5a2 2 0 00-2-2z"/>
                        </svg>
                    </div>
                    <p style="font-size:0.9rem;font-weight:700;color:#3A3330;margin:0 0 4px;">No items yet</p>
                    <p style="font-size:0.8rem;color:#9ca3af;margin:0;">Add items people can buy or swap at your sale.</p>
                </div>

                <!-- Add item button -->
                <button v-if="!showItemForm" @click="showItemForm = true"
                    style="width:100%;padding:13px;border:1.5px dashed #ED730C;border-radius:12px;background:transparent;color:#ED730C;font-size:0.875rem;font-weight:700;cursor:pointer;font-family:'DM Sans',sans-serif;display:flex;align-items:center;justify-content:center;gap:8px;margin-bottom:4px;"
                >
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" d="M12 5v14M5 12h14"/>
                    </svg>
                    Add an Item
                </button>

                <!-- Item form -->
                <div v-if="showItemForm" style="background:#fff;border-radius:16px;border:1.5px solid #ED730C;padding:20px;margin-bottom:4px;">
                    <p style="font-size:0.75rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:#ED730C;margin:0 0 16px;">New Item</p>

                    <!-- Image -->
                    <div style="margin-bottom:14px;">
                        <label style="font-size:0.65rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:#5c5751;display:block;margin-bottom:6px;">Photo</label>
                        <div v-if="!draftItem.imagePreview" style="display:flex;align-items:center;gap:10px;">
                            <label style="display:inline-flex;align-items:center;gap:6px;padding:8px 16px;border:1.5px solid #EDE8E0;border-radius:10px;cursor:pointer;font-size:0.8rem;font-weight:600;color:#6b7280;font-family:'DM Sans',sans-serif;">
                                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4M17 8l-5-5-5 5M12 3v12"/>
                                </svg>
                                Upload
                                <input type="file" accept="image/jpeg,image/png,image/webp" class="sr-only" aria-label="Upload item photo" @change="onItemImageChange">
                            </label>
                        </div>
                        <div v-else style="position:relative;display:inline-block;">
                            <img :src="draftItem.imagePreview" alt="Item photo preview" style="width:72px;height:72px;border-radius:10px;object-fit:cover;display:block;">
                            <button @click="draftItem.imageFile=null;draftItem.imagePreview=null" aria-label="Remove item photo" style="position:absolute;top:-6px;right:-6px;width:18px;height:18px;background:#3A3330;border:none;border-radius:50%;cursor:pointer;display:flex;align-items:center;justify-content:center;">
                                <svg width="8" height="8" fill="none" stroke="#fff" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                    </div>

                    <!-- Title + Category -->
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:12px;">
                        <div>
                            <label for="gs-item-title" style="font-size:0.65rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:#5c5751;display:block;margin-bottom:5px;">Title <span style="color:#ef4444;">*</span></label>
                            <input id="gs-item-title" v-model="draftItem.title" placeholder="e.g., Sony Headphones"
                                style="width:100%;padding:10px 12px;border:1.5px solid #EDE8E0;border-radius:10px;font-size:0.855rem;font-family:'DM Sans',sans-serif;outline:none;color:#3A3330;box-sizing:border-box;">
                        </div>
                        <div>
                            <label for="gs-item-cat" style="font-size:0.65rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:#5c5751;display:block;margin-bottom:5px;">Category <span style="color:#ef4444;">*</span></label>
                            <select id="gs-item-cat" v-model="draftItem.category"
                                style="width:100%;padding:10px 12px;border:1.5px solid #EDE8E0;border-radius:10px;font-size:0.855rem;font-family:'DM Sans',sans-serif;outline:none;color:#3A3330;background:#fff;box-sizing:border-box;">
                                <option value="" disabled>Select</option>
                                <option v-for="cat in categories" :key="cat" :value="cat">{{ cat }}</option>
                            </select>
                        </div>
                    </div>

                    <!-- Condition + Value -->
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:12px;">
                        <div>
                            <label for="gs-item-cond" style="font-size:0.65rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:#5c5751;display:block;margin-bottom:5px;">Condition <span style="color:#ef4444;">*</span></label>
                            <select id="gs-item-cond" v-model="draftItem.condition"
                                style="width:100%;padding:10px 12px;border:1.5px solid #EDE8E0;border-radius:10px;font-size:0.855rem;font-family:'DM Sans',sans-serif;outline:none;color:#3A3330;background:#fff;box-sizing:border-box;">
                                <option value="" disabled>Select</option>
                                <option v-for="c in conditions" :key="c.value" :value="c.value">{{ c.label }}</option>
                            </select>
                        </div>
                        <div>
                            <label for="gs-item-price" style="font-size:0.65rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:#5c5751;display:block;margin-bottom:5px;">Price (₱)</label>
                            <input id="gs-item-price" v-model="draftItem.value" type="number" min="0" inputmode="numeric" placeholder="e.g., 1500"
                                style="width:100%;padding:10px 12px;border:1.5px solid #EDE8E0;border-radius:10px;font-size:0.855rem;font-family:'DM Sans',sans-serif;outline:none;color:#3A3330;box-sizing:border-box;">
                        </div>
                    </div>

                    <!-- Looking for -->
                    <div style="margin-bottom:14px;">
                        <label for="gs-item-wants" style="font-size:0.65rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:#5c5751;display:block;margin-bottom:5px;">Open to swap for</label>
                        <input id="gs-item-wants" v-model="draftItem.wants" placeholder="e.g., iPhone, camera lenses, books"
                            style="width:100%;padding:10px 12px;border:1.5px solid #EDE8E0;border-radius:10px;font-size:0.855rem;font-family:'DM Sans',sans-serif;outline:none;color:#3A3330;box-sizing:border-box;">
                    </div>

                    <!-- Actions -->
                    <div style="display:flex;gap:10px;justify-content:flex-end;">
                        <button @click="cancelItemForm" style="padding:10px 20px;background:#fff;color:#6b7280;font-size:0.855rem;font-weight:600;border:1.5px solid #EDE8E0;border-radius:10px;cursor:pointer;font-family:'DM Sans',sans-serif;">Cancel</button>
                        <button @click="addItem" :disabled="!draftValid"
                            :style="{
                                padding:'10px 20px',
                                background: draftValid ? '#ED730C' : '#EDE8E0',
                                color: draftValid ? '#fff' : '#9ca3af',
                                fontSize:'0.855rem', fontWeight:'700',
                                border:'none', borderRadius:'10px',
                                cursor: draftValid ? 'pointer' : 'not-allowed',
                                fontFamily:`'DM Sans',sans-serif`,
                            }"
                        >Add Item</button>
                    </div>
                </div>

                <p v-if="saleItems.length" style="font-size:0.78rem;color:#9ca3af;margin:12px 0 0;text-align:center;">{{ saleItems.length }} item{{ saleItems.length === 1 ? '' : 's' }} added</p>
            </div>
        </div>

        <!-- ══════════════════════════════
             STICKY BOTTOM NAV
        ══════════════════════════════ -->
        <div style="position:fixed;bottom:0;left:0;right:0;background:#fff;border-top:1px solid #EDE8E0;padding:16px 24px;z-index:100;">
            <div style="max-width:640px;margin:0 auto;display:flex;justify-content:space-between;align-items:center;gap:12px;">
                <button v-if="currentStep > 1" @click="goBack"
                    style="padding:13px 24px;background:#fff;color:#3A3330;font-size:0.9rem;font-weight:700;border:1.5px solid #EDE8E0;border-radius:12px;cursor:pointer;font-family:'DM Sans',sans-serif;display:flex;align-items:center;gap:8px;"
                >
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Back
                </button>
                <div v-else></div>

                <!-- Step 1: Next -->
                <button v-if="currentStep === 1" @click="goNext" :disabled="!step1Valid"
                    :style="{
                        padding:'13px 32px', fontSize:'0.9rem', fontWeight:'700',
                        background: step1Valid ? '#ED730C' : '#EDE8E0',
                        color: step1Valid ? '#fff' : '#9ca3af',
                        border:'none', borderRadius:'12px',
                        cursor: step1Valid ? 'pointer' : 'not-allowed',
                        fontFamily:`'DM Sans',sans-serif`,
                        flex: 1,
                    }"
                >Next</button>

                <!-- Step 2: Next (optional location) -->
                <button v-if="currentStep === 2" @click="goNext"
                    style="flex:1;padding:13px 32px;background:#ED730C;color:#fff;font-size:0.9rem;font-weight:700;border:none;border-radius:12px;cursor:pointer;font-family:'DM Sans',sans-serif;"
                >Next</button>

                <!-- Step 3: Publish -->
                <button v-if="currentStep === 3" @click="publishSale" :disabled="loading"
                    style="flex:1;padding:13px 32px;background:#ED730C;color:#fff;font-size:0.9rem;font-weight:700;border:none;border-radius:12px;cursor:pointer;font-family:'DM Sans',sans-serif;display:inline-flex;align-items:center;justify-content:center;gap:8px;"
                >
                    <span v-if="!loading">Publish Sale</span>
                    <span v-else style="display:inline-flex;align-items:center;gap:6px;">
                        <svg style="width:14px;height:14px;animation:spin .9s linear infinite;" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.5">
                            <path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4"/>
                        </svg>
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
/* Visually hidden but still focusable & announced (keyboard + screen readers) */
.sr-only {
    position: absolute;
    width: 1px; height: 1px;
    padding: 0; margin: -1px;
    overflow: hidden; clip: rect(0, 0, 0, 0);
    white-space: nowrap; border: 0;
}

/* Visible keyboard focus ring (overrides the inline outline:none on fields) */
input:focus-visible,
textarea:focus-visible,
select:focus-visible {
    outline: 2px solid #ED730C !important;
    outline-offset: 2px;
    border-color: #ED730C !important;
}
a:focus-visible,
button:focus-visible {
    outline: 2px solid #ED730C;
    outline-offset: 2px;
}
/* Upload buttons wrap a hidden file input — surface its focus on the label */
label:focus-within {
    outline: 2px solid #ED730C;
    outline-offset: 2px;
}

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
