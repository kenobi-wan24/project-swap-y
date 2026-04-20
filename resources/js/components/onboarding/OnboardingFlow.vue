<script setup>
// filepath: resources/js/components/onboarding/OnboardingFlow.vue
import { ref, computed, onMounted } from 'vue'

// ── entrance animation ────────────────────────────────────────────────────────
const mounted = ref(false)
onMounted(() => setTimeout(() => mounted.value = true, 30))

// ── user context (passed from Laravel) ───────────────────────────────────────
const el   = document.getElementById('onboarding-app')
const user = ref(JSON.parse(el?.dataset.user || '{}'))

// ── step state ────────────────────────────────────────────────────────────────
const currentStep = ref(1)
const totalSteps  = 2
const direction   = ref('forward') // for animation direction

function goNext() {
    if (currentStep.value < totalSteps) {
        direction.value = 'forward'
        currentStep.value++
    }
}
function goBack() {
    if (currentStep.value > 1) {
        direction.value = 'back'
        currentStep.value--
    }
}

// ── STEP 1: preferences ───────────────────────────────────────────────────────
const categories = [
    { id: 'electronics', label: 'Electronics' },
    { id: 'clothing',    label: 'Clothing'    },
    { id: 'furniture',   label: 'Furniture'   },
    { id: 'books',       label: 'Books'       },
    { id: 'sports',      label: 'Sports'      },
    { id: 'services',    label: 'Services'    },
    { id: 'diy',         label: 'DIY'         },
    { id: 'art',         label: 'Art'         },
]
const selectedCategories = ref(['electronics', 'furniture'])

function toggleCategory(id) {
    const i = selectedCategories.value.indexOf(id)
    if (i === -1) selectedCategories.value.push(id)
    else selectedCategories.value.splice(i, 1)
}
function isCategorySelected(id) {
    return selectedCategories.value.includes(id)
}

const valueMin      = ref(0)
const valueMax      = ref(1000)
const maxDistance   = ref(25)
const notification  = ref('balanced') // 'frequent' | 'balanced' | 'minimal'

const notifOptions = [
    {
        id:    'frequent',
        label: 'Frequent',
        desc:  'Instant alerts for every new match and message.',
        recommended: false,
    },
    {
        id:    'balanced',
        label: 'Balanced',
        desc:  'Daily summaries and high-priority match alerts.',
        recommended: true,
    },
    {
        id:    'minimal',
        label: 'Minimal',
        desc:  'Weekly digest and critical account security only.',
        recommended: false,
    },
]

// ── STEP 2: intent ────────────────────────────────────────────────────────────
const intent     = ref('post') // 'post' | 'explore'
const s2Loading  = ref(false)

// ── submit / navigation ───────────────────────────────────────────────────────
async function finishOnboarding() {
    s2Loading.value = true

    /*
    | TODO (Backend): POST /api/user/onboarding with:
    | {
    |   categories:   selectedCategories.value,
    |   value_min:    valueMin.value,
    |   value_max:    valueMax.value,
    |   max_distance: maxDistance.value,
    |   notification: notification.value,
    |   intent:       intent.value,
    | }
    | Then redirect to /dashboard (or /post-item if intent === 'post')
    */

    await new Promise(r => setTimeout(r, 900))

    if (intent.value === 'post') {
        window.location.href = '/dashboard'   // swap for /post-item once built
    } else {
        window.location.href = '/dashboard'
    }
}

function skipOnboarding() {
    window.location.href = '/dashboard'
}

// ── progress fill for range slider track ─────────────────────────────────────
function trackStyle(val, min, max) {
    const pct = ((val - min) / (max - min)) * 100
    return `background: linear-gradient(to right, #ED730C ${pct}%, #e5e7eb ${pct}%);`
}
</script>

<template>
<div :style="{
    minHeight:'100vh', display:'flex', flexDirection:'column', background:'#fff',
    opacity: mounted ? 1 : 0,
    transform: mounted ? 'translateY(0)' : 'translateY(20px)',
    transition: 'opacity 0.55s cubic-bezier(.4,0,.2,1), transform 0.55s cubic-bezier(.4,0,.2,1)'
}">

    <!-- ══════════════════════════════════════════
         STEP DOTS
    ══════════════════════════════════════════ -->
    <div style="padding:32px 0 0;display:flex;justify-content:center;gap:8px;align-items:center;">
        <div
            v-for="n in totalSteps"
            :key="n"
            class="step-dot"
            :class="{
                active:  n === currentStep,
                done:    n < currentStep,
                pending: n > currentStep,
            }"
        ></div>
    </div>

    <!-- ══════════════════════════════════════════
         STEP CONTENT — animated transition
    ══════════════════════════════════════════ -->
    <div style="flex:1;display:flex;align-items:center;justify-content:center;overflow:hidden;">

        <transition name="step" mode="out-in">

            <!-- ════════════
                 STEP 1
            ════════════ -->
            <div
                v-if="currentStep === 1"
                key="step1"
                style="width:100%;max-width:640px;padding:40px 24px 32px;"
            >
                <h1 style="font-size:2.125rem;font-weight:800;color:#3A3330;text-align:center;margin-bottom:36px;line-height:1.2;">
                    What do you like to swap?
                </h1>

                <!-- Categories -->
                <div style="margin-bottom:40px;">
                    <p style="font-size:0.7rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:#9ca3af;margin-bottom:14px;">Categories</p>
                    <div style="display:flex;flex-wrap:wrap;gap:10px;">
                        <button
                            v-for="cat in categories"
                            :key="cat.id"
                            @click="toggleCategory(cat.id)"
                            :style="{
                                padding:'10px 20px',
                                borderRadius:'999px',
                                border:'1.5px solid',
                                fontSize:'0.875rem',
                                fontWeight:'600',
                                fontFamily:'\'DM Sans\',sans-serif',
                                cursor:'pointer',
                                transition:'all .18s cubic-bezier(.4,0,.2,1)',
                                borderColor: isCategorySelected(cat.id) ? '#ED730C' : '#e5e7eb',
                                background:  isCategorySelected(cat.id) ? '#ED730C' : '#fff',
                                color:       isCategorySelected(cat.id) ? '#fff'    : '#4b5563',
                                transform:   isCategorySelected(cat.id) ? 'scale(1.03)' : 'scale(1)',
                            }"
                        >{{ cat.label }}</button>
                    </div>
                </div>

                <!-- Preferred Value Range -->
                <div style="margin-bottom:40px;">
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:14px;">
                        <p style="font-size:0.7rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:#9ca3af;">Preferred Value Range</p>
                        <span style="font-size:1rem;font-weight:800;color:#3A3330;">${{ valueMin }} - ${{ valueMax }}</span>
                    </div>
                    <!-- Min slider -->
                    <input
                        type="range"
                        v-model.number="valueMin"
                        :min="0" :max="valueMax - 50" step="50"
                        :style="trackStyle(valueMin, 0, 1000)"
                        style="margin-bottom:10px;"
                    >
                    <!-- Max slider -->
                    <input
                        type="range"
                        v-model.number="valueMax"
                        :min="valueMin + 50" :max="1000" step="50"
                        :style="trackStyle(valueMax, 0, 1000)"
                    >
                    <div style="display:flex;justify-content:space-between;margin-top:6px;">
                        <span style="font-size:0.78rem;color:#9ca3af;">$0</span>
                        <span style="font-size:0.78rem;color:#9ca3af;">$1000</span>
                    </div>
                </div>

                <!-- Max Distance -->
                <div style="margin-bottom:40px;">
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:14px;">
                        <p style="font-size:0.7rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:#9ca3af;">Max Distance</p>
                        <span style="font-size:1rem;font-weight:800;color:#ED730C;">{{ maxDistance }} Miles</span>
                    </div>
                    <input
                        type="range"
                        v-model.number="maxDistance"
                        min="1" max="100" step="1"
                        :style="trackStyle(maxDistance, 1, 100)"
                    >
                    <p style="font-size:0.78rem;color:#9ca3af;margin-top:8px;">
                        Show me items within <strong style="color:#3A3330;">{{ maxDistance }} Miles</strong>
                    </p>
                </div>

                <!-- Notification Frequency -->
                <div style="margin-bottom:40px;">
                    <p style="font-size:0.7rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:#9ca3af;margin-bottom:14px;">Notification Frequency</p>
                    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:10px;">
                        <div
                            v-for="opt in notifOptions"
                            :key="opt.id"
                            @click="notification = opt.id"
                            style="position:relative;border-radius:14px;padding:16px 14px;cursor:pointer;transition:all .2s cubic-bezier(.4,0,.2,1);"
                            :style="{
                                border: notification===opt.id ? '2px solid #ED730C' : '1.5px solid #e5e7eb',
                                background: notification===opt.id ? '#fff9f5' : '#fff',
                                transform: notification===opt.id ? 'translateY(-2px)' : 'translateY(0)',
                                boxShadow: notification===opt.id ? '0 4px 16px rgba(237,115,12,0.12)' : 'none',
                            }"
                        >
                            <!-- RECOMMENDED badge -->
                            <div
                                v-if="opt.recommended"
                                style="position:absolute;top:-11px;left:50%;transform:translateX(-50%);background:#ED730C;color:#fff;font-size:0.6rem;font-weight:800;padding:3px 10px;border-radius:999px;letter-spacing:.06em;text-transform:uppercase;white-space:nowrap;"
                            >Recommended</div>
                            <p style="font-size:0.9rem;font-weight:700;color:#3A3330;margin-bottom:6px;">{{ opt.label }}</p>
                            <p style="font-size:0.78rem;color:#6b7280;line-height:1.5;">{{ opt.desc }}</p>
                        </div>
                    </div>
                </div>

                <!-- Continue button -->
                <button
                    @click="goNext"
                    style="width:100%;padding:17px;background:#ED730C;color:#fff;font-size:1rem;font-weight:700;border:none;border-radius:14px;cursor:pointer;font-family:'DM Sans',sans-serif;transition:background .15s;letter-spacing:.01em;"
                    onmouseover="this.style.background='#d4620a'"
                    onmouseout="this.style.background='#ED730C'"
                >Continue</button>

                <p style="text-align:center;font-size:0.82rem;color:#9ca3af;margin-top:16px;">Step 1 of 2</p>

            </div>

            <!-- ════════════
                 STEP 2
            ════════════ -->
            <div
                v-else-if="currentStep === 2"
                key="step2"
                style="width:100%;max-width:700px;padding:40px 24px 32px;text-align:center;"
            >
                <h1 style="font-size:2.5rem;font-weight:800;color:#3A3330;margin-bottom:40px;line-height:1.15;">
                    Got something to swap?
                </h1>

                <!-- Choice cards -->
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:36px;">

                    <!-- Post my first item -->
                    <div
                        @click="intent = 'post'"
                        style="position:relative;border-radius:20px;padding:36px 24px;cursor:pointer;transition:all .2s cubic-bezier(.4,0,.2,1);"
                        :style="{
                            border: intent==='post' ? '2px solid #ED730C' : '1.5px solid #e5e7eb',
                            background: intent==='post' ? '#fff' : '#fafafa',
                            transform: intent==='post' ? 'translateY(-3px)' : 'translateY(0)',
                            boxShadow: intent==='post' ? '0 8px 28px rgba(237,115,12,0.14)' : 'none',
                        }"
                    >
                        <!-- Checkmark badge -->
                        <div
                            v-if="intent === 'post'"
                            style="position:absolute;top:14px;right:14px;width:22px;height:22px;background:#ED730C;border-radius:50%;display:flex;align-items:center;justify-content:center;"
                        >
                            <svg width="11" height="11" viewBox="0 0 12 10" fill="none">
                                <path d="M1 5l3.5 3.5L11 1" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>

                        <!-- Camera icon circle -->
                        <div style="width:64px;height:64px;border-radius:50%;background:#fff4ec;display:flex;align-items:center;justify-content:center;margin:0 auto 20px;">
                            <svg width="26" height="26" fill="none" stroke="#ED730C" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                <path d="M23 19a2 2 0 01-2 2H3a2 2 0 01-2-2V8a2 2 0 012-2h4l2-3h6l2 3h4a2 2 0 012 2z"/>
                                <circle cx="12" cy="13" r="4"/>
                                <line x1="18" y1="3" x2="18.01" y2="3"/>
                            </svg>
                        </div>

                        <h3 style="font-size:1.0625rem;font-weight:700;color:#3A3330;margin-bottom:8px;">Post my first item</h3>
                        <p style="font-size:0.875rem;color:#6b7280;line-height:1.5;">List an item and start getting matched</p>
                    </div>

                    <!-- Explore first -->
                    <div
                        @click="intent = 'explore'"
                        style="border-radius:20px;padding:36px 24px;cursor:pointer;transition:all .2s cubic-bezier(.4,0,.2,1);"
                        :style="{
                            border: intent==='explore' ? '2px solid #ED730C' : '1.5px solid #e5e7eb',
                            background: intent==='explore' ? '#fff' : '#fafafa',
                            transform: intent==='explore' ? 'translateY(-3px)' : 'translateY(0)',
                            boxShadow: intent==='explore' ? '0 8px 28px rgba(237,115,12,0.14)' : 'none',
                        }"
                    >
                        <!-- Checkmark badge -->
                        <div
                            v-if="intent === 'explore'"
                            style="position:absolute;top:14px;right:14px;width:22px;height:22px;background:#ED730C;border-radius:50%;display:flex;align-items:center;justify-content:center;"
                        >
                            <svg width="11" height="11" viewBox="0 0 12 10" fill="none">
                                <path d="M1 5l3.5 3.5L11 1" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>

                        <!-- Compass icon circle -->
                        <div style="width:64px;height:64px;border-radius:50%;background:#f3f4f6;display:flex;align-items:center;justify-content:center;margin:0 auto 20px;">
                            <svg width="26" height="26" fill="none" stroke="#9ca3af" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="10"/>
                                <polygon points="16.24 7.76 14.12 14.12 7.76 16.24 9.88 9.88 16.24 7.76"/>
                            </svg>
                        </div>

                        <h3 style="font-size:1.0625rem;font-weight:700;color:#3A3330;margin-bottom:8px;">Explore first</h3>
                        <p style="font-size:0.875rem;color:#6b7280;line-height:1.5;">Browse what's available and post later</p>
                    </div>

                </div>

                <!-- Continue button -->
                <button
                    @click="finishOnboarding"
                    :disabled="s2Loading"
                    style="padding:16px 64px;background:#ED730C;color:#fff;font-size:1rem;font-weight:700;border:none;border-radius:14px;cursor:pointer;font-family:'DM Sans',sans-serif;transition:background .15s;min-width:200px;display:inline-flex;align-items:center;justify-content:center;gap:8px;"
                    onmouseover="this.style.background='#d4620a'"
                    onmouseout="this.style.background='#ED730C'"
                >
                    <span v-if="!s2Loading">Continue</span>
                    <span v-else style="display:inline-flex;align-items:center;gap:8px;">
                        <svg style="width:16px;height:16px;animation:spin .9s linear infinite;" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.5">
                            <path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4"/>
                        </svg>
                        Setting up your account...
                    </span>
                </button>

                <!-- Skip -->
                <div style="margin-top:18px;">
                    <button
                        @click="skipOnboarding"
                        style="font-size:0.875rem;font-weight:600;color:#9ca3af;background:none;border:1.5px solid #e5e7eb;border-radius:10px;padding:11px 28px;cursor:pointer;font-family:'DM Sans',sans-serif;transition:color .15s,border-color .15s;"
                        onmouseover="this.style.color='#6b7280';this.style.borderColor='#d1d5db'"
                        onmouseout="this.style.color='#9ca3af';this.style.borderColor='#e5e7eb'"
                    >Skip for now</button>
                </div>

                <!-- Back link -->
                <div style="margin-top:12px;">
                    <button
                        @click="goBack"
                        style="font-size:0.78rem;color:#9ca3af;background:none;border:none;cursor:pointer;font-family:'DM Sans',sans-serif;display:inline-flex;align-items:center;gap:4px;"
                    >
                        <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
                        </svg>
                        Back
                    </button>
                </div>

            </div>

        </transition>

    </div>

    <!-- Bottom SWAPY wordmark -->
    <div style="padding:24px;text-align:center;">
        <a href="/" style="display:inline-flex;align-items:center;text-decoration:none;opacity:0.45;transition:opacity .15s;" onmouseover="this.style.opacity='0.7'" onmouseout="this.style.opacity='0.45'">
            <img :src="'/images/logo-swapy.png'" alt="SWAPY" style="height:36px;object-fit:contain;">
        </a>
    </div>

</div>
</template>

<style scoped>
@keyframes spin { to { transform: rotate(360deg); } }
</style>