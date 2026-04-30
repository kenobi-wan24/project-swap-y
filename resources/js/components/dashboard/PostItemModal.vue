<script setup>
// filepath: resources/js/components/dashboard/PostItemModal.vue
import { ref, computed } from 'vue'

const emit = defineEmits(['close', 'posted'])

// ── step state ────────────────────────────────────────────────────────────────
const currentStep = ref(1)
const totalSteps  = 3
const loading     = ref(false)
const posted      = ref(false)
const postedItem  = ref(null)

// ── step 1: photos ────────────────────────────────────────────────────────────
const images        = ref([])
const imagePreviews = ref([])
const primaryIndex  = ref(0)
const isDragging    = ref(false)

function onFileChange(e) {
    addFiles(Array.from(e.target.files))
}

function onDrop(e) {
    isDragging.value = false
    addFiles(Array.from(e.dataTransfer.files))
}

function addFiles(files) {
    files.forEach(file => {
        if (images.value.length >= 5) return
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
    if (primaryIndex.value >= images.value.length) {
        primaryIndex.value = 0
    }
}

function setPrimary(index) {
    primaryIndex.value = index
}

// ── step 2: item details ──────────────────────────────────────────────────────
const title       = ref('')
const category    = ref('')
const condition   = ref('')
const location    = ref('')
const description = ref('')

const categories = [
    'Electronics', 'Clothing', 'Furniture', 'Books',
    'Sports', 'Services', 'DIY', 'Art', 'Other',
]

const conditions = [
    { value: 'new',      label: 'New'       },
    { value: 'like_new', label: 'Like New'  },
    { value: 'good',     label: 'Good'      },
    { value: 'fair',     label: 'Fair'      },
]

const step2Valid = computed(() =>
    title.value.trim() &&
    category.value &&
    condition.value
)

// ── step 3: listing preferences ───────────────────────────────────────────────
const lookingFor      = ref('')
const swapConditions  = ref([])

const swapConditionOptions = [
    { value: 'meet_in_public', label: 'Willing to meet in public place' },
    { value: 'ship_tracking',  label: 'Can ship with tracking'          },
    { value: 'accept_cash',    label: 'Accept cash to balance swap'     },
    { value: 'add_cash',       label: 'I can add cash to balance swap'  },
]

function toggleCondition(val) {
    const i = swapConditions.value.indexOf(val)
    if (i === -1) swapConditions.value.push(val)
    else swapConditions.value.splice(i, 1)
}

// ── navigation ────────────────────────────────────────────────────────────────
function goNext() {
    if (currentStep.value < totalSteps) currentStep.value++
}

function goBack() {
    if (currentStep.value > 1) currentStep.value--
}

function closeModal() {
    emit('close')
}

// ── submit ────────────────────────────────────────────────────────────────────
async function postItem() {
    loading.value = true

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')

    const formData = new FormData()
    formData.append('title',       title.value)
    formData.append('description', description.value)
    formData.append('category',    category.value)
    formData.append('condition',   condition.value)
    formData.append('location',    location.value)
    formData.append('looking_for', lookingFor.value)

    swapConditions.value.forEach(c => formData.append('swap_conditions[]', c))

    // reorder so primary is first
    const reordered = [...images.value]
    const primary   = reordered.splice(primaryIndex.value, 1)[0]
    if (primary) reordered.unshift(primary)
    reordered.forEach(file => formData.append('images[]', file))

    try {
        const res = await fetch('/items', {
            method:  'POST',
            headers: { 'X-CSRF-TOKEN': csrfToken },
            body:    formData,
        })

        const data = await res.json()

        if (data.success) {
            postedItem.value = data.item
            posted.value     = true
            emit('posted', data.item)
        }
    } catch (e) {
        console.error('Post item failed', e)
    } finally {
        loading.value = false
    }
}
</script>

<template>
<!-- Backdrop -->
<div
    @click.self="closeModal"
    style="position:fixed;inset:0;background:rgba(0,0,0,0.55);z-index:1000;display:flex;align-items:center;justify-content:center;padding:16px;"
>

    <!-- Modal -->
    <div style="background:#fff;border-radius:20px;width:100%;max-width:560px;max-height:90vh;overflow-y:auto;position:relative;">

        <!-- ══════════════════════════════
             SUCCESS SCREEN
        ══════════════════════════════ -->
        <div v-if="posted" style="padding:48px 32px;text-align:center;">
            <!-- Checkmark -->
            <div style="width:64px;height:64px;background:#f0fdf4;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 20px;">
                <svg width="28" height="28" fill="none" stroke="#22c55e" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                    <path d="M20 6L9 17l-5-5"/>
                </svg>
            </div>

            <h2 style="font-size:1.375rem;font-weight:800;color:#3A3330;margin:0 0 8px;">Item Posted Successfully!</h2>
            <p style="font-size:0.875rem;color:#6b7280;margin:0 0 28px;">Your {{ postedItem?.title }} is now live and ready for matches.</p>

            <!-- Item preview -->
            <div v-if="postedItem" style="display:flex;align-items:center;gap:14px;background:#f9fafb;border-radius:14px;padding:14px;margin-bottom:28px;text-align:left;border:1px solid #f0f0ec;">
                <div style="width:52px;height:52px;border-radius:10px;background:#e5e7eb;overflow:hidden;flex-shrink:0;">
                    <img
                        v-if="imagePreviews.length"
                        :src="imagePreviews[primaryIndex]"
                        style="width:100%;height:100%;object-fit:cover;"
                    >
                </div>
                <div>
                    <p style="font-size:0.9rem;font-weight:700;color:#3A3330;margin:0 0 2px;">{{ postedItem.title }}</p>
                    <p style="font-size:0.75rem;color:#ED730C;font-weight:600;margin:0;">LIVE LISTING</p>
                </div>
                <svg style="margin-left:auto;" width="16" height="16" fill="none" stroke="#9ca3af" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                </svg>
            </div>

            <!-- Actions -->
            <button
                @click="closeModal"
                style="width:100%;padding:14px;background:#ED730C;color:#fff;font-size:0.9rem;font-weight:700;border:none;border-radius:12px;cursor:pointer;margin-bottom:10px;font-family:'DM Sans',sans-serif;"
            >View My Store</button>
            <button
                @click="closeModal"
                style="width:100%;padding:14px;background:#fff;color:#3A3330;font-size:0.9rem;font-weight:700;border:1.5px solid #e5e7eb;border-radius:12px;cursor:pointer;font-family:'DM Sans',sans-serif;"
            >Done</button>
        </div>

        <!-- ══════════════════════════════
             FORM STEPS
        ══════════════════════════════ -->
        <template v-else>

            <!-- Header -->
            <div style="padding:20px 24px 0;border-bottom:1px solid #f0f0ec;">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;">
                    <div>
                        <p style="font-size:0.65rem;font-weight:700;color:#ED730C;text-transform:uppercase;letter-spacing:.08em;margin:0 0 2px;">Step {{ currentStep }} of {{ totalSteps }}</p>
                        <h2 style="font-size:1.25rem;font-weight:800;color:#3A3330;margin:0;">
                            {{ currentStep === 1 ? 'Add Photos' : currentStep === 2 ? 'Item Details' : 'Listing Preferences' }}
                        </h2>
                    </div>
                    <button @click="closeModal" style="background:none;border:none;cursor:pointer;padding:4px;color:#9ca3af;">
                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                <!-- Progress bar -->
                <div style="height:3px;background:#f0f0ec;border-radius:2px;margin-bottom:0;">
                    <div :style="{ width: ((currentStep / totalSteps) * 100) + '%', background: '#ED730C', height: '100%', borderRadius: '2px', transition: 'width .3s' }"></div>
                </div>
            </div>

            <!-- ════════════
                 STEP 1 — Photos
            ════════════ -->
            <div v-if="currentStep === 1" style="padding:24px;">

                <!-- Drop zone -->
                <div
                    @dragover.prevent="isDragging = true"
                    @dragleave="isDragging = false"
                    @drop.prevent="onDrop"
                    :style="{
                        border: isDragging ? '2px dashed #ED730C' : '2px dashed #e5e7eb',
                        background: isDragging ? '#fff7ed' : '#fafafa',
                        borderRadius: '14px',
                        padding: '40px 24px',
                        textAlign: 'center',
                        transition: 'all .2s',
                        marginBottom: '20px',
                    }"
                >
                    <div style="width:48px;height:48px;background:#f3f4f6;border-radius:12px;display:flex;align-items:center;justify-content:center;margin:0 auto 14px;">
                        <svg width="22" height="22" fill="none" stroke="#9ca3af" stroke-width="1.8" viewBox="0 0 24 24">
                            <rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/>
                            <path d="M21 15l-5-5L5 21"/>
                        </svg>
                    </div>
                    <p style="font-size:0.9rem;font-weight:700;color:#3A3330;margin:0 0 4px;">Drag and drop your files here</p>
                    <p style="font-size:0.78rem;color:#9ca3af;margin:0 0 16px;">PNG, JPG or WEBP up to 10MB</p>
                    <label style="display:inline-flex;align-items:center;gap:8px;padding:10px 20px;background:#ED730C;color:#fff;border-radius:10px;cursor:pointer;font-size:0.845rem;font-weight:700;font-family:'DM Sans',sans-serif;">
                        <svg width="14" height="14" fill="none" stroke="#fff" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4M17 8l-5-5-5 5M12 3v12"/>
                        </svg>
                        Upload from device
                        <input type="file" multiple accept="image/jpeg,image/png,image/webp" style="display:none;" @change="onFileChange">
                    </label>
                </div>

                <!-- Previews -->
                <div v-if="imagePreviews.length">
                    <p style="font-size:0.65rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:#9ca3af;margin:0 0 10px;">Uploaded Assets</p>
                    <div style="display:flex;flex-wrap:wrap;gap:10px;">
                        <div
                            v-for="(src, i) in imagePreviews"
                            :key="i"
                            style="position:relative;width:88px;height:88px;border-radius:10px;overflow:hidden;cursor:pointer;flex-shrink:0;"
                            :style="{ border: i === primaryIndex ? '2.5px solid #ED730C' : '2px solid #e5e7eb' }"
                            @click="setPrimary(i)"
                        >
                            <img :src="src" style="width:100%;height:100%;object-fit:cover;">
                            <!-- Primary badge -->
                            <div v-if="i === primaryIndex" style="position:absolute;bottom:0;left:0;right:0;background:#ED730C;color:#fff;font-size:0.55rem;font-weight:800;text-align:center;padding:3px;text-transform:uppercase;letter-spacing:.05em;">Primary</div>
                            <!-- Remove -->
                            <button
                                @click.stop="removeImage(i)"
                                style="position:absolute;top:4px;right:4px;width:18px;height:18px;background:rgba(0,0,0,0.55);border:none;border-radius:50%;cursor:pointer;display:flex;align-items:center;justify-content:center;"
                            >
                                <svg width="8" height="8" fill="none" stroke="#fff" stroke-width="2.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div style="display:flex;justify-content:space-between;align-items:center;margin-top:28px;">
                    <button @click="closeModal" style="font-size:0.875rem;font-weight:600;color:#9ca3af;background:none;border:none;cursor:pointer;font-family:'DM Sans',sans-serif;">Cancel</button>
                    <button
                        @click="goNext"
                        style="padding:12px 28px;background:#ED730C;color:#fff;font-size:0.9rem;font-weight:700;border:none;border-radius:12px;cursor:pointer;font-family:'DM Sans',sans-serif;"
                    >Next</button>
                </div>
            </div>

            <!-- ════════════
                 STEP 2 — Item Details
            ════════════ -->
            <div v-else-if="currentStep === 2" style="padding:24px;">

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:14px;">
                    <!-- Title -->
                    <div>
                        <label style="font-size:0.65rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:#9ca3af;display:block;margin-bottom:6px;">Item Title</label>
                        <input
                            v-model="title"
                            placeholder="e.g., Vintage Leica M4"
                            style="width:100%;padding:10px 12px;border:1.5px solid #e5e7eb;border-radius:10px;font-size:0.875rem;font-family:'DM Sans',sans-serif;outline:none;color:#3A3330;"
                        >
                    </div>
                    <!-- Category -->
                    <div>
                        <label style="font-size:0.65rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:#9ca3af;display:block;margin-bottom:6px;">Category</label>
                        <select
                            v-model="category"
                            style="width:100%;padding:10px 12px;border:1.5px solid #e5e7eb;border-radius:10px;font-size:0.875rem;font-family:'DM Sans',sans-serif;outline:none;color:#3A3330;background:#fff;"
                        >
                            <option value="" disabled>Select category</option>
                            <option v-for="cat in categories" :key="cat" :value="cat">{{ cat }}</option>
                        </select>
                    </div>
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:14px;">
                    <!-- Condition -->
                    <div>
                        <label style="font-size:0.65rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:#9ca3af;display:block;margin-bottom:6px;">Condition</label>
                        <select
                            v-model="condition"
                            style="width:100%;padding:10px 12px;border:1.5px solid #e5e7eb;border-radius:10px;font-size:0.875rem;font-family:'DM Sans',sans-serif;outline:none;color:#3A3330;background:#fff;"
                        >
                            <option value="" disabled>Select condition</option>
                            <option v-for="c in conditions" :key="c.value" :value="c.value">{{ c.label }}</option>
                        </select>
                    </div>
                    <!-- Location -->
                    <div>
                        <label style="font-size:0.65rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:#9ca3af;display:block;margin-bottom:6px;">Location</label>
                        <input
                            v-model="location"
                            placeholder="City, State or Zip Code"
                            style="width:100%;padding:10px 12px;border:1.5px solid #e5e7eb;border-radius:10px;font-size:0.875rem;font-family:'DM Sans',sans-serif;outline:none;color:#3A3330;"
                        >
                    </div>
                </div>

                <!-- Description -->
                <div style="margin-bottom:14px;">
                    <label style="font-size:0.65rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:#9ca3af;display:block;margin-bottom:6px;">Description</label>
                    <textarea
                        v-model="description"
                        placeholder="Describe the history, features, and why you're swapping it..."
                        rows="4"
                        style="width:100%;padding:10px 12px;border:1.5px solid #e5e7eb;border-radius:10px;font-size:0.875rem;font-family:'DM Sans',sans-serif;outline:none;color:#3A3330;resize:vertical;"
                    ></textarea>
                </div>

                <!-- Footer -->
                <div style="display:flex;justify-content:space-between;align-items:center;margin-top:8px;">
                    <button @click="goBack" style="font-size:0.875rem;font-weight:600;color:#9ca3af;background:none;border:none;cursor:pointer;font-family:'DM Sans',sans-serif;">Back</button>
                    <button
                        @click="goNext"
                        :disabled="!step2Valid"
                        :style="{
                            padding: '12px 28px',
                            background: step2Valid ? '#ED730C' : '#e5e7eb',
                            color: step2Valid ? '#fff' : '#9ca3af',
                            fontSize: '0.9rem',
                            fontWeight: '700',
                            border: 'none',
                            borderRadius: '12px',
                            cursor: step2Valid ? 'pointer' : 'not-allowed',
                            fontFamily: `'DM Sans', sans-serif`,
                        }"
                    >Next</button>
                </div>
            </div>

            <!-- ════════════
                 STEP 3 — Listing Preferences
            ════════════ -->
            <div v-else-if="currentStep === 3" style="padding:24px;">

                <!-- Listing type — swap only for now -->
                <div style="margin-bottom:20px;">
                    <label style="font-size:0.65rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:#9ca3af;display:block;margin-bottom:8px;">Listing Type</label>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
                        <div style="padding:12px;border-radius:10px;border:2px solid #ED730C;background:#fff7ed;text-align:center;font-size:0.875rem;font-weight:700;color:#EA580C;">Swap</div>
                        <div style="padding:12px;border-radius:10px;border:1.5px solid #e5e7eb;background:#f9fafb;text-align:center;font-size:0.875rem;font-weight:600;color:#9ca3af;">Sell or swap</div>
                    </div>
                </div>

                <!-- Looking for -->
                <div style="margin-bottom:20px;">
                    <label style="font-size:0.65rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:#9ca3af;display:block;margin-bottom:6px;">What are you looking for?</label>
                    <input
                        v-model="lookingFor"
                        placeholder="e.g., iPhone, sneakers, gadgets"
                        style="width:100%;padding:10px 12px;border:1.5px solid #e5e7eb;border-radius:10px;font-size:0.875rem;font-family:'DM Sans',sans-serif;outline:none;color:#3A3330;"
                    >
                </div>

                <!-- Swap conditions -->
                <div style="margin-bottom:24px;">
                    <label style="font-size:0.65rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:#9ca3af;display:block;margin-bottom:10px;">Swap Conditions</label>
                    <div style="display:flex;flex-direction:column;gap:10px;">
                        <label
                            v-for="opt in swapConditionOptions"
                            :key="opt.value"
                            style="display:flex;align-items:center;gap:10px;cursor:pointer;"
                        >
                            <div
                                @click="toggleCondition(opt.value)"
                                :style="{
                                    width: '18px',
                                    height: '18px',
                                    borderRadius: '5px',
                                    border: swapConditions.includes(opt.value) ? '2px solid #ED730C' : '2px solid #e5e7eb',
                                    background: swapConditions.includes(opt.value) ? '#ED730C' : '#fff',
                                    display: 'flex',
                                    alignItems: 'center',
                                    justifyContent: 'center',
                                    flexShrink: '0',
                                    transition: 'all .15s',
                                }"
                            >
                                <svg v-if="swapConditions.includes(opt.value)" width="10" height="10" fill="none" stroke="#fff" stroke-width="3" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M20 6L9 17l-5-5"/>
                                </svg>
                            </div>
                            <span style="font-size:0.875rem;color:#3A3330;">{{ opt.label }}</span>
                        </label>
                    </div>
                </div>

                <!-- Footer -->
                <div style="display:flex;justify-content:space-between;align-items:center;">
                    <button @click="goBack" style="font-size:0.875rem;font-weight:600;color:#9ca3af;background:none;border:none;cursor:pointer;font-family:'DM Sans',sans-serif;">Back</button>
                    <button
                        @click="postItem"
                        :disabled="loading"
                        style="padding:12px 28px;background:#ED730C;color:#fff;font-size:0.9rem;font-weight:700;border:none;border-radius:12px;cursor:pointer;font-family:'DM Sans',sans-serif;display:inline-flex;align-items:center;gap:8px;"
                    >
                        <span v-if="!loading">Post Item</span>
                        <span v-else style="display:inline-flex;align-items:center;gap:6px;">
                            <svg style="width:14px;height:14px;animation:spin .9s linear infinite;" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.5">
                                <path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4"/>
                            </svg>
                            Posting...
                        </span>
                    </button>
                </div>

            </div>

        </template>

    </div>
</div>
</template>

<style scoped>
@keyframes spin { to { transform: rotate(360deg); } }
</style>