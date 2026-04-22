<script setup>
// filepath: resources/js/components/dashboard/DashboardPage.vue
import { ref, computed } from 'vue'

const el          = document.getElementById('dashboard-app')
const user        = ref(JSON.parse(el?.dataset.user        || '{}'))
const stats       = ref(JSON.parse(el?.dataset.stats       || '[]'))
const matches     = ref(JSON.parse(el?.dataset.matches     || '[]'))
const swaps       = ref(JSON.parse(el?.dataset.swaps       || '[]'))
const messages    = ref(JSON.parse(el?.dataset.messages    || '[]'))
const credibility = ref(JSON.parse(el?.dataset.credibility || '{}'))

const firstName = computed(() => user.value.name?.split(' ')[0] || 'there')

const greeting = computed(() => {
    const h = new Date().getHours()
    if (h < 12) return 'Good morning'
    if (h < 18) return 'Good afternoon'
    return 'Good evening'
})

// Items that need immediate attention
const urgent = computed(() =>
    swaps.value.filter(s => s.status === 'pending')
)
const unreadCount = computed(() =>
    messages.value.filter(m => m.unread).length
)

function stars(rating) {
    return Array.from({ length: 5 }, (_, i) => {
        const diff = rating - i
        if (diff >= 1) return 'full'
        if (diff >= 0.5) return 'half'
        return 'empty'
    })
}

const statusConfig = {
    pending:    { label: 'Pending',    bg: '#fef3c7', color: '#92400e' },
    in_transit: { label: 'In Transit', bg: '#d1fae5', color: '#065f46' },
    completed:  { label: 'Completed',  bg: '#e0f2fe', color: '#0c4a6e' },
    declined:   { label: 'Declined',   bg: '#fee2e2', color: '#991b1b' },
}
</script>

<template>
<div style="min-height:100vh;background:#f8f9fa;">

    <!-- ══════════════════════════════════════
         TOP BAR — greeting + stats
    ══════════════════════════════════════ -->
    <div style="background:#fff;border-bottom:1px solid #f3f4f6;">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" style="padding-top:24px;padding-bottom:24px;">
            <div style="display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:16px;">

                <!-- Greeting -->
                <div>
                    <p style="font-size:0.8rem;color:#9ca3af;margin-bottom:2px;">{{ greeting }}</p>
                    <h1 style="font-size:1.625rem;font-weight:800;color:#3A3330;line-height:1.15;">
                        {{ firstName }} 👋
                    </h1>
                </div>

                <!-- Slim stat pills -->
                <div style="display:flex;gap:8px;flex-wrap:wrap;">
                    <div v-for="stat in stats" :key="stat.key"
                         style="display:flex;align-items:center;gap:8px;padding:8px 14px;background:#f9fafb;border:1px solid #f3f4f6;border-radius:999px;">
                        <span style="font-size:1rem;font-weight:800;color:#3A3330;">{{ stat.value }}</span>
                        <span style="font-size:0.72rem;font-weight:600;color:#9ca3af;letter-spacing:.04em;text-transform:uppercase;">{{ stat.label }}</span>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" style="padding-top:24px;padding-bottom:64px;">
        <div style="display:grid;grid-template-columns:1fr 288px;gap:20px;align-items:start;" class="dash-grid">

            <!-- ══ LEFT COLUMN ══ -->
            <div style="display:flex;flex-direction:column;gap:20px;">

                <!-- ===== URGENT ATTENTION ZONE ===== -->
                <div v-if="urgent.length > 0 || unreadCount > 0"
                     style="background:#fff8f3;border:1.5px solid #fed7aa;border-radius:16px;padding:16px 18px;">
                    <p style="font-size:0.72rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:#ED730C;margin-bottom:12px;">
                        Needs your attention
                    </p>
                    <div style="display:flex;flex-direction:column;gap:10px;">
                        <!-- Pending swaps -->
                        <div v-for="swap in urgent" :key="'u'+swap.id"
                             style="display:flex;align-items:center;gap:12px;background:#fff;border-radius:12px;padding:12px 14px;border:1px solid #fee;">
                            <div style="width:8px;height:8px;border-radius:50%;background:#ED730C;flex-shrink:0;"></div>
                            <div style="flex:1;min-width:0;">
                                <p style="font-size:0.8375rem;font-weight:700;color:#3A3330;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                                    {{ swap.title }}
                                </p>
                                <p style="font-size:0.75rem;color:#9ca3af;">{{ swap.status_text }}</p>
                            </div>
                            <button style="background:#ED730C;color:#fff;font-size:0.75rem;font-weight:700;padding:7px 14px;border-radius:999px;border:none;cursor:pointer;font-family:'DM Sans',sans-serif;white-space:nowrap;">
                                Respond Now
                            </button>
                        </div>
                        <!-- Unread messages nudge -->
                        <div v-if="unreadCount > 0"
                             style="display:flex;align-items:center;gap:12px;background:#fff;border-radius:12px;padding:12px 14px;border:1px solid #f3f4f6;">
                            <div style="width:8px;height:8px;border-radius:50%;background:#149189;flex-shrink:0;"></div>
                            <p style="flex:1;font-size:0.8375rem;font-weight:600;color:#3A3330;">
                                You have <strong style="color:#149189;">{{ unreadCount }} unread {{ unreadCount === 1 ? 'message' : 'messages' }}</strong>
                            </p>
                            <a href="#" style="font-size:0.75rem;font-weight:700;color:#149189;text-decoration:none;white-space:nowrap;">Open</a>
                        </div>
                    </div>
                </div>

                <!-- ===== QUICK ACTIONS — slim icon row ===== -->
                <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:10px;" class="actions-grid">

                    <a href="#" style="text-decoration:none;">
                        <div style="background:#fff;border:1px solid #f3f4f6;border-radius:14px;padding:14px;display:flex;flex-direction:column;align-items:center;gap:8px;transition:all .2s;text-align:center;"
                             onmouseover="this.style.borderColor='#e5e7eb';this.style.transform='translateY(-2px)'"
                             onmouseout="this.style.borderColor='#f3f4f6';this.style.transform='translateY(0)'">
                            <div style="width:38px;height:38px;border-radius:10px;background:#f9fafb;border:1px solid #f3f4f6;display:flex;align-items:center;justify-content:center;">
                                <svg width="17" height="17" fill="none" stroke="#6b7280" stroke-width="2" stroke-linecap="round" viewBox="0 0 24 24">
                                    <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                                    <polyline points="9 22 9 12 15 12 15 22"/>
                                </svg>
                            </div>
                            <span style="font-size:0.78rem;font-weight:600;color:#3A3330;">My Store</span>
                        </div>
                    </a>

                    <a href="#" style="text-decoration:none;">
                        <div style="background:#ED730C;border-radius:14px;padding:14px;display:flex;flex-direction:column;align-items:center;gap:8px;transition:all .2s;text-align:center;"
                             onmouseover="this.style.background='#d4620a';this.style.transform='translateY(-2px)'"
                             onmouseout="this.style.background='#ED730C';this.style.transform='translateY(0)'">
                            <div style="width:38px;height:38px;border-radius:10px;background:rgba(255,255,255,0.2);display:flex;align-items:center;justify-content:center;">
                                <svg width="17" height="17" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" viewBox="0 0 24 24">
                                    <line x1="12" y1="5" x2="12" y2="19"/>
                                    <line x1="5" y1="12" x2="19" y2="12"/>
                                </svg>
                            </div>
                            <span style="font-size:0.78rem;font-weight:600;color:#fff;">Post Item</span>
                        </div>
                    </a>

                    <a href="/services" style="text-decoration:none;">
                        <div style="background:#149189;border-radius:14px;padding:14px;display:flex;flex-direction:column;align-items:center;gap:8px;transition:all .2s;text-align:center;"
                             onmouseover="this.style.background='#0e7a72';this.style.transform='translateY(-2px)'"
                             onmouseout="this.style.background='#149189';this.style.transform='translateY(0)'">
                            <div style="width:38px;height:38px;border-radius:10px;background:rgba(255,255,255,0.2);display:flex;align-items:center;justify-content:center;">
                                <svg width="17" height="17" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" viewBox="0 0 24 24">
                                    <path d="M8 7h12m0 0l-4-4m4 4l-4 4M16 17H4m0 0l4 4m-4-4l4-4"/>
                                </svg>
                            </div>
                            <span style="font-size:0.78rem;font-weight:600;color:#fff;">Offer Service</span>
                        </div>
                    </a>

                    <a href="/garage-sale" style="text-decoration:none;">
                        <div style="background:#fff;border:1px solid #f3f4f6;border-radius:14px;padding:14px;display:flex;flex-direction:column;align-items:center;gap:8px;transition:all .2s;text-align:center;"
                             onmouseover="this.style.borderColor='#e5e7eb';this.style.transform='translateY(-2px)'"
                             onmouseout="this.style.borderColor='#f3f4f6';this.style.transform='translateY(0)'">
                            <div style="width:38px;height:38px;border-radius:10px;background:#f9fafb;border:1px solid #f3f4f6;display:flex;align-items:center;justify-content:center;">
                                <svg width="17" height="17" fill="none" stroke="#6b7280" stroke-width="2" stroke-linecap="round" viewBox="0 0 24 24">
                                    <path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/>
                                    <line x1="3" y1="6" x2="21" y2="6"/>
                                    <path d="M16 10a4 4 0 01-8 0"/>
                                </svg>
                            </div>
                            <span style="font-size:0.78rem;font-weight:600;color:#3A3330;">Garage Sale</span>
                        </div>
                    </a>

                </div>

                <!-- ===== KINETIC MATCHES ===== -->
                <div style="background:#fff;border:1px solid #f3f4f6;border-radius:16px;padding:20px;">

                    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">
                        <div>
                            <h2 style="font-size:1rem;font-weight:800;color:#3A3330;">Kinetic Matches</h2>
                            <p style="font-size:0.75rem;color:#9ca3af;margin-top:2px;">Items matched to your swap preferences</p>
                        </div>
                        <a href="/browse" style="font-size:0.78rem;font-weight:700;color:#149189;text-decoration:none;display:flex;align-items:center;gap:3px;white-space:nowrap;">
                            View All
                            <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                            </svg>
                        </a>
                    </div>

                    <!-- Empty state -->
                    <div v-if="matches.length === 0"
                         style="text-align:center;padding:32px 0;">
                        <div style="width:48px;height:48px;background:#f9fafb;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 12px;">
                            <svg width="20" height="20" fill="none" stroke="#d1d5db" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M8 7h12m0 0l-4-4m4 4l-4 4M16 17H4m0 0l4 4m-4-4l4-4"/>
                            </svg>
                        </div>
                        <p style="font-size:0.875rem;font-weight:600;color:#3A3330;margin-bottom:4px;">No matches yet</p>
                        <p style="font-size:0.8rem;color:#9ca3af;margin-bottom:16px;max-width:240px;margin-left:auto;margin-right:auto;line-height:1.5;">
                            Post your first item and we'll find you matches instantly.
                        </p>
                        <a href="#" style="display:inline-flex;align-items:center;gap:6px;background:#ED730C;color:#fff;font-size:0.8rem;font-weight:700;padding:9px 20px;border-radius:999px;text-decoration:none;">
                            Post First Item →
                        </a>
                    </div>

                    <!-- Match cards — horizontal scroll -->
                    <div v-else
                         style="display:flex;gap:12px;overflow-x:auto;padding-bottom:4px;"
                         class="hide-scrollbar">
                        <div v-for="match in matches" :key="match.id"
                             style="flex-shrink:0;width:190px;border:1px solid #f3f4f6;border-radius:14px;overflow:hidden;transition:border-color .2s;"
                             onmouseover="this.style.borderColor='#149189'"
                             onmouseout="this.style.borderColor='#f3f4f6'">
                            <!-- Image -->
                            <div style="position:relative;aspect-ratio:4/3;overflow:hidden;background:#f9fafb;">
                                <img :src="match.image" :alt="match.title"
                                     style="width:100%;height:100%;object-fit:cover;">
                                <span style="position:absolute;top:7px;right:7px;background:#149189;color:#fff;font-size:0.6rem;font-weight:800;padding:3px 7px;border-radius:6px;">
                                    {{ match.match_percent }}% MATCH
                                </span>
                                <span style="position:absolute;bottom:7px;left:7px;background:rgba(0,0,0,0.5);color:#fff;font-size:0.62rem;font-weight:600;padding:2px 6px;border-radius:5px;">
                                    {{ match.distance }} MI
                                </span>
                            </div>
                            <!-- Info -->
                            <div style="padding:10px 12px;">
                                <p style="font-size:0.8375rem;font-weight:700;color:#3A3330;margin-bottom:2px;line-height:1.3;">{{ match.title }}</p>
                                <p style="font-size:0.72rem;color:#9ca3af;margin-bottom:10px;">Wants: {{ match.wants }}</p>
                                <button style="width:100%;padding:7px;background:#e6f7f6;color:#149189;font-size:0.72rem;font-weight:700;border:none;border-radius:8px;cursor:pointer;font-family:'DM Sans',sans-serif;letter-spacing:.03em;transition:background .15s;"
                                        onmouseover="this.style.background='#c8eeec'"
                                        onmouseout="this.style.background='#e6f7f6'">
                                    Initiate Swap
                                </button>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- ===== ACTIVE SWAPS ===== -->
                <div style="background:#fff;border:1px solid #f3f4f6;border-radius:16px;padding:20px;">

                    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">
                        <div>
                            <h2 style="font-size:1rem;font-weight:800;color:#3A3330;">Active Swaps</h2>
                            <p style="font-size:0.75rem;color:#9ca3af;margin-top:2px;">Your ongoing exchanges</p>
                        </div>
                    </div>

                    <div v-if="swaps.length === 0"
                         style="text-align:center;padding:24px 0;">
                        <p style="font-size:0.8375rem;color:#9ca3af;">No active swaps. Match with someone to get started.</p>
                    </div>

                    <div v-else style="display:flex;flex-direction:column;gap:8px;">
                        <div v-for="swap in swaps" :key="swap.id"
                             style="display:flex;align-items:center;gap:12px;padding:12px 14px;border:1px solid #f3f4f6;border-radius:12px;transition:border-color .15s;"
                             onmouseover="this.style.borderColor='#e5e7eb'"
                             onmouseout="this.style.borderColor='#f3f4f6'">

                            <!-- Item image pair -->
                            <div style="display:flex;align-items:center;flex-shrink:0;">
                                <div style="width:40px;height:40px;border-radius:9px;overflow:hidden;border:2px solid #fff;box-shadow:0 0 0 1px #f3f4f6;">
                                    <img :src="swap.item_a_image" style="width:100%;height:100%;object-fit:cover;">
                                </div>
                                <div style="width:20px;height:20px;border-radius:50%;background:#f9fafb;border:2px solid #fff;display:flex;align-items:center;justify-content:center;margin:0 -5px;z-index:1;box-shadow:0 0 0 1px #f3f4f6;">
                                    <svg width="9" height="9" fill="none" stroke="#9ca3af" stroke-width="2" viewBox="0 0 24 24">
                                        <path d="M8 7h12m0 0l-4-4m4 4l-4 4M16 17H4m0 0l4 4m-4-4l4-4"/>
                                    </svg>
                                </div>
                                <div style="width:40px;height:40px;border-radius:9px;overflow:hidden;border:2px solid #fff;box-shadow:0 0 0 1px #f3f4f6;">
                                    <img :src="swap.item_b_image" style="width:100%;height:100%;object-fit:cover;">
                                </div>
                            </div>

                            <!-- Details -->
                            <div style="flex:1;min-width:0;">
                                <p style="font-size:0.8375rem;font-weight:700;color:#3A3330;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;margin-bottom:2px;">
                                    {{ swap.title }}
                                </p>
                                <p style="font-size:0.72rem;color:#9ca3af;">{{ swap.status_text }}</p>
                            </div>

                            <!-- Status + CTA -->
                            <div style="display:flex;align-items:center;gap:8px;flex-shrink:0;">
                                <span style="font-size:0.65rem;font-weight:700;padding:3px 9px;border-radius:6px;letter-spacing:.04em;text-transform:uppercase;"
                                      :style="{background: statusConfig[swap.status]?.bg, color: statusConfig[swap.status]?.color}">
                                    {{ statusConfig[swap.status]?.label }}
                                </span>
                                <button v-if="swap.cta_label"
                                        style="font-size:0.75rem;font-weight:700;padding:7px 14px;border-radius:999px;border:none;cursor:pointer;font-family:'DM Sans',sans-serif;white-space:nowrap;transition:background .15s;"
                                        :style="{background: swap.cta_primary ? '#ED730C':'#f3f4f6', color: swap.cta_primary ? '#fff':'#3A3330'}">
                                    {{ swap.cta_label }}
                                </button>
                            </div>
                        </div>
                    </div>

                </div>

            </div><!-- end left column -->

            <!-- ══ RIGHT SIDEBAR ══ -->
            <div style="display:flex;flex-direction:column;gap:16px;">

                <!-- Recent Messages -->
                <div style="background:#fff;border:1px solid #f3f4f6;border-radius:16px;overflow:hidden;">

                    <div style="display:flex;align-items:center;justify-content:space-between;padding:16px 16px 12px;">
                        <div style="display:flex;align-items:center;gap:7px;">
                            <h3 style="font-size:0.9375rem;font-weight:700;color:#3A3330;">Messages</h3>
                            <span v-if="unreadCount > 0"
                                  style="background:#ED730C;color:#fff;font-size:0.6rem;font-weight:800;min-width:18px;height:18px;padding:0 4px;border-radius:999px;display:inline-flex;align-items:center;justify-content:center;">
                                {{ unreadCount }}
                            </span>
                        </div>
                        <a href="#" style="font-size:0.72rem;font-weight:600;color:#9ca3af;text-decoration:none;">See all</a>
                    </div>

                    <div v-if="messages.length === 0"
                         style="padding:20px 16px;text-align:center;">
                        <p style="font-size:0.8125rem;color:#9ca3af;">No messages yet.</p>
                    </div>

                    <div v-else>
                        <div v-for="msg in messages" :key="msg.id"
                             style="display:flex;align-items:center;gap:10px;padding:10px 16px;cursor:pointer;transition:background .12s;border-top:1px solid #f9fafb;"
                             onmouseover="this.style.background='#fafafa'"
                             onmouseout="this.style.background='transparent'">

                            <!-- Avatar -->
                            <div style="width:34px;height:34px;border-radius:50%;flex-shrink:0;overflow:hidden;background:#f3f4f6;display:flex;align-items:center;justify-content:center;position:relative;">
                                <img v-if="msg.avatar" :src="msg.avatar"
                                     style="width:100%;height:100%;object-fit:cover;">
                                <span v-else style="font-size:0.68rem;font-weight:800;color:#6b7280;">
                                    {{ msg.initials }}
                                </span>
                                <!-- Online dot -->
                                <div v-if="msg.unread"
                                     style="position:absolute;bottom:0;right:0;width:9px;height:9px;background:#ED730C;border-radius:50%;border:1.5px solid #fff;">
                                </div>
                            </div>

                            <!-- Content -->
                            <div style="flex:1;min-width:0;">
                                <div style="display:flex;justify-content:space-between;align-items:baseline;gap:4px;margin-bottom:1px;">
                                    <p style="font-size:0.8125rem;font-weight:700;color:#3A3330;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                                        {{ msg.name }}
                                    </p>
                                    <span style="font-size:0.67rem;color:#c4c9d4;flex-shrink:0;">{{ msg.time }}</span>
                                </div>
                                <p style="font-size:0.75rem;color:#9ca3af;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                                    {{ msg.preview }}
                                </p>
                            </div>

                        </div>
                    </div>

                    <div style="padding:12px 16px;border-top:1px solid #f9fafb;">
                        <a href="#"
                           style="display:block;text-align:center;font-size:0.75rem;font-weight:700;color:#149189;text-decoration:none;letter-spacing:.04em;text-transform:uppercase;">
                            Open Messenger
                        </a>
                    </div>
                </div>

                <!-- Credibility — compact version -->
                <div style="background:#fff;border:1px solid #f3f4f6;border-radius:16px;padding:16px;">
                    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;">
                        <p style="font-size:0.72rem;font-weight:700;letter-spacing:.07em;text-transform:uppercase;color:#9ca3af;">
                            Swap Score
                        </p>
                        <a href="#" style="font-size:0.72rem;color:#9ca3af;text-decoration:none;">View profile →</a>
                    </div>

                    <div style="display:flex;align-items:center;gap:12px;">
                        <!-- Score ring -->
                        <div style="width:52px;height:52px;border-radius:50%;background:#fff8f3;border:3px solid #ED730C;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <span style="font-size:1rem;font-weight:800;color:#ED730C;line-height:1;">{{ credibility.score }}</span>
                        </div>
                        <div>
                            <!-- Stars -->
                            <div style="display:flex;gap:2px;margin-bottom:4px;">
                                <template v-for="(type, i) in stars(credibility.score)" :key="i">
                                    <svg width="13" height="13" viewBox="0 0 24 24">
                                        <path v-if="type==='full'"
                                              d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"
                                              fill="#ED730C"/>
                                        <path v-else
                                              d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"
                                              fill="none" stroke="#e5e7eb" stroke-width="1.5"/>
                                    </svg>
                                </template>
                            </div>
                            <p style="font-size:0.75rem;color:#6b7280;line-height:1.4;">
                                <strong style="color:#ED730C;">{{ credibility.items_this_month }}</strong> swaps this month
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Quick stats breakdown -->
                <div style="background:#fff;border:1px solid #f3f4f6;border-radius:16px;padding:16px;">
                    <p style="font-size:0.72rem;font-weight:700;letter-spacing:.07em;text-transform:uppercase;color:#9ca3af;margin-bottom:12px;">
                        Overview
                    </p>
                    <div style="display:flex;flex-direction:column;gap:10px;">
                        <div v-for="stat in stats" :key="'s'+stat.key"
                             style="display:flex;align-items:center;justify-content:space-between;">
                            <span style="font-size:0.8125rem;color:#6b7280;">{{ stat.label }}</span>
                            <span style="font-size:0.9375rem;font-weight:800;color:#3A3330;">{{ stat.value }}</span>
                        </div>
                    </div>
                </div>

            </div><!-- end sidebar -->

        </div>
    </div>

</div>
</template>

<style>
.hide-scrollbar::-webkit-scrollbar { display:none; }
.hide-scrollbar { -ms-overflow-style:none; scrollbar-width:none; }
@media (max-width:1024px) {
    .dash-grid { grid-template-columns: 1fr !important; }
}
@media (max-width:640px) {
    .actions-grid { grid-template-columns: repeat(2,1fr) !important; }
}
</style>