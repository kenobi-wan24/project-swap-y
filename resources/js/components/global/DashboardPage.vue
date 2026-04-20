<script setup>
// filepath: resources/js/components/dashboard/DashboardPage.vue
import { ref, computed } from 'vue'

const el = document.getElementById('dashboard-app')
const user        = ref(JSON.parse(el?.dataset.user        || '{}'))
const stats       = ref(JSON.parse(el?.dataset.stats       || '[]'))
const matches     = ref(JSON.parse(el?.dataset.matches     || '[]'))
const swaps       = ref(JSON.parse(el?.dataset.swaps       || '[]'))
const messages    = ref(JSON.parse(el?.dataset.messages    || '[]'))
const credibility = ref(JSON.parse(el?.dataset.credibility || '{}'))

const firstName = computed(() => user.value.name?.split(' ')[0] || 'there')

// greeting based on time of day
const greeting = computed(() => {
    const h = new Date().getHours()
    if (h < 12) return 'Good morning'
    if (h < 18) return 'Good afternoon'
    return 'Good evening'
})

// star rating helper
function stars(rating) {
    return Array.from({ length: 5 }, (_, i) => {
        const diff = rating - i
        if (diff >= 1) return 'full'
        if (diff >= 0.5) return 'half'
        return 'empty'
    })
}

// status badge config
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
         WELCOME BANNER
    ══════════════════════════════════════ -->
    <section style="background:#fff;border-bottom:1px solid #f3f4f6;padding:28px 0;">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div style="display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:20px;">

                <!-- Greeting -->
                <div>
                    <p style="font-size:0.8125rem;color:#9ca3af;margin-bottom:3px;">{{ greeting }},</p>
                    <h1 style="font-size:1.875rem;font-weight:800;color:#3A3330;line-height:1.1;">
                        {{ firstName }}! <span style="font-size:1.5rem;">👋</span>
                    </h1>
                    <p style="font-size:0.875rem;color:#9ca3af;margin-top:4px;">Your swap exchange hub is active and moving.</p>
                </div>

                <!-- Stat pills -->
                <div style="display:flex;gap:10px;flex-wrap:wrap;">
                    <div
                        v-for="stat in stats"
                        :key="stat.key"
                        style="display:flex;align-items:center;gap:10px;background:#f9fafb;border:1px solid #f3f4f6;border-radius:14px;padding:12px 18px;min-width:130px;"
                    >
                        <div style="width:36px;height:36px;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;"
                             :style="{background: stat.iconBg}">
                            <svg width="17" height="17" fill="none" :stroke="stat.iconColor" stroke-width="2" stroke-linecap="round" viewBox="0 0 24 24" v-html="stat.iconPath"></svg>
                        </div>
                        <div>
                            <p style="font-size:1.375rem;font-weight:800;color:#3A3330;line-height:1;">{{ stat.value }}</p>
                            <p style="font-size:0.7rem;font-weight:600;color:#9ca3af;text-transform:uppercase;letter-spacing:.06em;margin-top:1px;">{{ stat.label }}</p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- ══════════════════════════════════════
         MAIN CONTENT
    ══════════════════════════════════════ -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" style="padding-top:28px;padding-bottom:64px;">
        <div style="display:grid;grid-template-columns:1fr 300px;gap:24px;align-items:start;" class="dash-grid">

            <!-- ── LEFT COLUMN ── -->
            <div style="display:flex;flex-direction:column;gap:28px;">

                <!-- ===== QUICK ACTIONS ===== -->
                <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:12px;" class="actions-grid">

                    <a href="#" style="text-decoration:none;">
                        <div style="background:#fff;border:1px solid #f3f4f6;border-radius:16px;padding:20px 16px;transition:all .2s;cursor:pointer;"
                             onmouseover="this.style.borderColor='#e5e7eb';this.style.boxShadow='0 4px 16px rgba(0,0,0,0.06)';this.style.transform='translateY(-2px)'"
                             onmouseout="this.style.borderColor='#f3f4f6';this.style.boxShadow='none';this.style.transform='translateY(0)'">
                            <div style="width:40px;height:40px;border-radius:10px;background:#f9fafb;border:1px solid #f3f4f6;display:flex;align-items:center;justify-content:center;margin-bottom:12px;">
                                <svg width="18" height="18" fill="none" stroke="#6b7280" stroke-width="2" stroke-linecap="round" viewBox="0 0 24 24">
                                    <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/>
                                </svg>
                            </div>
                            <p style="font-size:0.875rem;font-weight:700;color:#3A3330;margin-bottom:4px;">My Store</p>
                            <p style="font-size:0.75rem;color:#9ca3af;line-height:1.4;">Manage your inventory and collections.</p>
                        </div>
                    </a>

                    <a href="#" style="text-decoration:none;">
                        <div style="background:#ED730C;border-radius:16px;padding:20px 16px;transition:all .2s;cursor:pointer;position:relative;overflow:hidden;"
                             onmouseover="this.style.background='#d4620a';this.style.transform='translateY(-2px)'"
                             onmouseout="this.style.background='#ED730C';this.style.transform='translateY(0)'">
                            <div style="position:absolute;bottom:-12px;right:-8px;font-size:4rem;opacity:.12;font-weight:800;color:#fff;line-height:1;">S</div>
                            <div style="width:40px;height:40px;border-radius:10px;background:rgba(255,255,255,0.2);display:flex;align-items:center;justify-content:center;margin-bottom:12px;">
                                <svg width="18" height="18" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" viewBox="0 0 24 24">
                                    <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                                </svg>
                            </div>
                            <p style="font-size:0.875rem;font-weight:700;color:#fff;margin-bottom:4px;">Post a New Item</p>
                            <p style="font-size:0.75rem;color:rgba(255,255,255,0.8);line-height:1.4;">Ready for something new?</p>
                        </div>
                    </a>

                    <a href="{{ route('services') }}" style="text-decoration:none;">
                        <div style="background:#149189;border-radius:16px;padding:20px 16px;transition:all .2s;cursor:pointer;position:relative;overflow:hidden;"
                             onmouseover="this.style.background='#0e7a72';this.style.transform='translateY(-2px)'"
                             onmouseout="this.style.background='#149189';this.style.transform='translateY(0)'">
                            <div style="position:absolute;bottom:-12px;right:-8px;font-size:4rem;opacity:.12;font-weight:800;color:#fff;line-height:1;">S</div>
                            <div style="width:40px;height:40px;border-radius:10px;background:rgba(255,255,255,0.2);display:flex;align-items:center;justify-content:center;margin-bottom:12px;">
                                <svg width="18" height="18" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" viewBox="0 0 24 24">
                                    <path d="M8 7h12m0 0l-4-4m4 4l-4 4M16 17H4m0 0l4 4m-4-4l4-4"/>
                                </svg>
                            </div>
                            <p style="font-size:0.875rem;font-weight:700;color:#fff;margin-bottom:4px;">Offer a Service</p>
                            <p style="font-size:0.75rem;color:rgba(255,255,255,0.8);line-height:1.4;">Swap your skills for gear.</p>
                        </div>
                    </a>

                    <a href="{{ route('garage-sale') }}" style="text-decoration:none;">
                        <div style="background:#fff;border:1px solid #f3f4f6;border-radius:16px;padding:20px 16px;transition:all .2s;cursor:pointer;"
                             onmouseover="this.style.borderColor='#e5e7eb';this.style.boxShadow='0 4px 16px rgba(0,0,0,0.06)';this.style.transform='translateY(-2px)'"
                             onmouseout="this.style.borderColor='#f3f4f6';this.style.boxShadow='none';this.style.transform='translateY(0)'">
                            <div style="width:40px;height:40px;border-radius:10px;background:#f9fafb;border:1px solid #f3f4f6;display:flex;align-items:center;justify-content:center;margin-bottom:12px;">
                                <svg width="18" height="18" fill="none" stroke="#6b7280" stroke-width="2" stroke-linecap="round" viewBox="0 0 24 24">
                                    <path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 01-8 0"/>
                                </svg>
                            </div>
                            <p style="font-size:0.875rem;font-weight:700;color:#3A3330;margin-bottom:4px;">Start a Garage Sale</p>
                            <p style="font-size:0.75rem;color:#9ca3af;line-height:1.4;">List multiple items at once.</p>
                        </div>
                    </a>

                </div>

                <!-- ===== KINETIC MATCHES ===== -->
                <div>
                    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">
                        <h2 style="font-size:1.125rem;font-weight:800;color:#3A3330;">Kinetic Matches</h2>
                        <a href="{{ route('browse') }}" style="font-size:0.8125rem;font-weight:700;color:#149189;text-decoration:none;display:flex;align-items:center;gap:4px;">
                            View All
                            <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    </div>

                    <!-- Empty state for new users -->
                    <div v-if="matches.length === 0"
                         style="background:#fff;border:1.5px dashed #e5e7eb;border-radius:16px;padding:40px;text-align:center;">
                        <div style="width:56px;height:56px;background:#f9fafb;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
                            <svg width="24" height="24" fill="none" stroke="#d1d5db" stroke-width="2" viewBox="0 0 24 24"><path d="M8 7h12m0 0l-4-4m4 4l-4 4M16 17H4m0 0l4 4m-4-4l4-4"/></svg>
                        </div>
                        <p style="font-size:0.9375rem;font-weight:700;color:#3A3330;margin-bottom:6px;">No matches yet</p>
                        <p style="font-size:0.8375rem;color:#9ca3af;margin-bottom:20px;max-width:280px;margin-left:auto;margin-right:auto;line-height:1.5;">Post your first item and our algorithm will find people who want to swap with you.</p>
                        <a href="#" style="display:inline-flex;align-items:center;gap:6px;background:#ED730C;color:#fff;font-size:0.8125rem;font-weight:700;padding:10px 22px;border-radius:999px;text-decoration:none;font-family:'DM Sans',sans-serif;">
                            Post First Item
                        </a>
                    </div>

                    <!-- Match cards horizontal scroll -->
                    <div v-else style="display:flex;gap:14px;overflow-x:auto;padding-bottom:8px;" class="hide-scrollbar">
                        <div
                            v-for="match in matches"
                            :key="match.id"
                            style="flex-shrink:0;width:210px;background:#fff;border-radius:16px;overflow:hidden;border:1px solid #f3f4f6;transition:border-color .2s,box-shadow .2s;"
                            onmouseover="this.style.borderColor='#149189';this.style.boxShadow='0 4px 16px rgba(20,145,137,0.1)'"
                            onmouseout="this.style.borderColor='#f3f4f6';this.style.boxShadow='none'"
                        >
                            <div style="position:relative;aspect-ratio:4/3;overflow:hidden;background:#f3f4f6;">
                                <img :src="match.image" :alt="match.title" style="width:100%;height:100%;object-fit:cover;">
                                <span style="position:absolute;top:8px;right:8px;background:#149189;color:#fff;font-size:0.62rem;font-weight:800;padding:3px 8px;border-radius:6px;letter-spacing:.04em;">
                                    {{ match.match_percent }}% MATCH
                                </span>
                                <span style="position:absolute;bottom:8px;left:8px;background:rgba(0,0,0,0.55);color:#fff;font-size:0.65rem;font-weight:600;padding:2px 7px;border-radius:6px;backdrop-filter:blur(4px);">
                                    {{ match.distance }} MILES
                                </span>
                            </div>
                            <div style="padding:12px 14px;">
                                <p style="font-size:0.875rem;font-weight:700;color:#3A3330;margin-bottom:2px;line-height:1.3;">{{ match.title }}</p>
                                <p style="font-size:0.75rem;color:#9ca3af;margin-bottom:12px;">Wants: {{ match.wants }}</p>
                                <button style="width:100%;padding:8px;background:#e6f7f6;color:#149189;font-size:0.75rem;font-weight:700;border:none;border-radius:8px;cursor:pointer;font-family:'DM Sans',sans-serif;letter-spacing:.04em;transition:background .15s;"
                                        onmouseover="this.style.background='#ccf0ed'"
                                        onmouseout="this.style.background='#e6f7f6'">
                                    INITIATE SWAP
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ===== ACTIVE SWAPS ===== -->
                <div>
                    <h2 style="font-size:1.125rem;font-weight:800;color:#3A3330;margin-bottom:16px;">Active Swaps</h2>

                    <div v-if="swaps.length === 0"
                         style="background:#fff;border:1.5px dashed #e5e7eb;border-radius:16px;padding:32px;text-align:center;">
                        <p style="font-size:0.875rem;color:#9ca3af;">No active swaps yet. Once you match with someone, your swaps will appear here.</p>
                    </div>

                    <div v-else style="display:flex;flex-direction:column;gap:10px;">
                        <div
                            v-for="swap in swaps"
                            :key="swap.id"
                            style="background:#fff;border:1px solid #f3f4f6;border-radius:14px;padding:16px 18px;display:flex;align-items:center;gap:14px;transition:border-color .2s;"
                            onmouseover="this.style.borderColor='#e5e7eb'"
                            onmouseout="this.style.borderColor='#f3f4f6'"
                        >
                            <!-- Item image pair -->
                            <div style="display:flex;align-items:center;gap:0;flex-shrink:0;">
                                <div style="width:44px;height:44px;border-radius:10px;overflow:hidden;border:2px solid #fff;box-shadow:0 0 0 1px #f3f4f6;">
                                    <img :src="swap.item_a_image" style="width:100%;height:100%;object-fit:cover;">
                                </div>
                                <div style="width:22px;height:22px;border-radius:50%;background:#f9fafb;border:2px solid #fff;display:flex;align-items:center;justify-content:center;margin:0 -4px;z-index:1;box-shadow:0 0 0 1px #f3f4f6;">
                                    <svg width="10" height="10" fill="none" stroke="#9ca3af" stroke-width="2" viewBox="0 0 24 24"><path d="M8 7h12m0 0l-4-4m4 4l-4 4M16 17H4m0 0l4 4m-4-4l4-4"/></svg>
                                </div>
                                <div style="width:44px;height:44px;border-radius:10px;overflow:hidden;border:2px solid #fff;box-shadow:0 0 0 1px #f3f4f6;">
                                    <img :src="swap.item_b_image" style="width:100%;height:100%;object-fit:cover;">
                                </div>
                            </div>

                            <!-- Swap info -->
                            <div style="flex:1;min-width:0;">
                                <p style="font-size:0.875rem;font-weight:700;color:#3A3330;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;margin-bottom:2px;">
                                    Trading: {{ swap.title }}
                                </p>
                                <p style="font-size:0.75rem;color:#9ca3af;">{{ swap.status_text }}</p>
                            </div>

                            <!-- Status + CTA -->
                            <div style="display:flex;align-items:center;gap:8px;flex-shrink:0;">
                                <span style="font-size:0.68rem;font-weight:700;padding:4px 10px;border-radius:6px;letter-spacing:.04em;text-transform:uppercase;"
                                      :style="{background: statusConfig[swap.status]?.bg, color: statusConfig[swap.status]?.color}">
                                    {{ statusConfig[swap.status]?.label }}
                                </span>
                                <button v-if="swap.cta_label"
                                        style="font-size:0.78rem;font-weight:700;padding:8px 16px;border-radius:999px;border:none;cursor:pointer;font-family:'DM Sans',sans-serif;white-space:nowrap;transition:background .15s;"
                                        :style="{background: swap.cta_primary ? '#ED730C':'#f3f4f6', color: swap.cta_primary ? '#fff':'#3A3330'}"
                                        :onmouseover="swap.cta_primary ? 'this.style.background=\'#d4620a\'' : 'this.style.background=\'#e5e7eb\''"
                                        :onmouseout="swap.cta_primary ? 'this.style.background=\'#ED730C\'' : 'this.style.background=\'#f3f4f6\''">
                                    {{ swap.cta_label }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

            </div><!-- end left column -->

            <!-- ── RIGHT SIDEBAR ── -->
            <div style="display:flex;flex-direction:column;gap:16px;">

                <!-- Recent Messages -->
                <div style="background:#fff;border:1px solid #f3f4f6;border-radius:16px;overflow:hidden;">
                    <div style="display:flex;align-items:center;justify-content:space-between;padding:16px 18px 12px;border-bottom:1px solid #f9fafb;">
                        <div style="display:flex;align-items:center;gap:8px;">
                            <h3 style="font-size:0.9375rem;font-weight:700;color:#3A3330;">Recent Messages</h3>
                            <span v-if="messages.filter(m=>m.unread).length > 0"
                                  style="background:#ED730C;color:#fff;font-size:0.6rem;font-weight:800;width:18px;height:18px;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;">
                                {{ messages.filter(m=>m.unread).length }}
                            </span>
                        </div>
                        <button style="background:none;border:none;cursor:pointer;color:#9ca3af;">
                            <svg width="14" height="14" fill="currentColor" viewBox="0 0 24 24"><circle cx="5" cy="12" r="2"/><circle cx="12" cy="12" r="2"/><circle cx="19" cy="12" r="2"/></svg>
                        </button>
                    </div>

                    <div v-if="messages.length === 0" style="padding:24px 18px;text-align:center;">
                        <p style="font-size:0.8125rem;color:#9ca3af;">No messages yet.</p>
                    </div>

                    <div v-else>
                        <div
                            v-for="msg in messages"
                            :key="msg.id"
                            style="display:flex;align-items:flex-start;gap:10px;padding:12px 18px;border-bottom:1px solid #f9fafb;cursor:pointer;transition:background .12s;"
                            onmouseover="this.style.background='#fafafa'"
                            onmouseout="this.style.background='transparent'"
                        >
                            <div style="width:36px;height:36px;border-radius:50%;flex-shrink:0;overflow:hidden;background:#f3f4f6;display:flex;align-items:center;justify-content:center;">
                                <img v-if="msg.avatar" :src="msg.avatar" style="width:100%;height:100%;object-fit:cover;">
                                <span v-else style="font-size:0.7rem;font-weight:800;color:#9ca3af;">{{ msg.initials }}</span>
                            </div>
                            <div style="flex:1;min-width:0;">
                                <div style="display:flex;justify-content:space-between;align-items:baseline;margin-bottom:2px;">
                                    <p style="font-size:0.8125rem;font-weight:700;color:#3A3330;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ msg.name }}</p>
                                    <span style="font-size:0.7rem;color:#9ca3af;flex-shrink:0;margin-left:8px;">{{ msg.time }}</span>
                                </div>
                                <p style="font-size:0.75rem;color:#9ca3af;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ msg.preview }}</p>
                            </div>
                            <div v-if="msg.unread" style="width:7px;height:7px;border-radius:50%;background:#ED730C;margin-top:5px;flex-shrink:0;"></div>
                        </div>
                    </div>

                    <div style="padding:12px 18px;">
                        <a href="#" style="display:block;text-align:center;font-size:0.78rem;font-weight:700;color:#149189;text-decoration:none;letter-spacing:.04em;text-transform:uppercase;">
                            Open Messenger Center
                        </a>
                    </div>
                </div>

                <!-- Swap Credibility -->
                <div style="background:#fff;border:1px solid #f3f4f6;border-radius:16px;padding:20px 18px;">
                    <p style="font-size:0.7rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:#9ca3af;margin-bottom:14px;">Swap Credibility</p>

                    <div style="display:flex;align-items:baseline;gap:4px;margin-bottom:10px;">
                        <span style="font-size:2.5rem;font-weight:800;color:#3A3330;line-height:1;">{{ credibility.score }}</span>
                        <span style="font-size:1rem;color:#9ca3af;font-weight:500;">/5.0</span>
                    </div>

                    <!-- Stars -->
                    <div style="display:flex;gap:3px;margin-bottom:16px;">
                        <template v-for="(type, i) in stars(credibility.score)" :key="i">
                            <svg width="18" height="18" viewBox="0 0 24 24">
                                <path v-if="type==='full'"
                                      d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"
                                      fill="#ED730C"/>
                                <path v-else-if="type==='half'"
                                      d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"
                                      fill="url(#halfFill)" stroke="none"/>
                                <path v-else
                                      d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"
                                      fill="none" stroke="#e5e7eb" stroke-width="1.5"/>
                            </svg>
                        </template>
                    </div>

                    <!-- Velocity -->
                    <div style="background:#f9fafb;border-radius:10px;padding:12px 14px;">
                        <p style="font-size:0.7rem;font-weight:700;letter-spacing:.06em;text-transform:uppercase;color:#149189;margin-bottom:4px;">Your Velocity</p>
                        <p style="font-size:0.8rem;color:#6b7280;line-height:1.5;">
                            You have exchanged
                            <strong style="color:#ED730C;">{{ credibility.items_this_month }} items</strong>
                            this month. Keep the momentum!
                        </p>
                    </div>
                </div>

            </div><!-- end right sidebar -->

        </div><!-- end grid -->
    </div>

</div>
</template>

<style>
.hide-scrollbar::-webkit-scrollbar { display:none; }
.hide-scrollbar { -ms-overflow-style:none; scrollbar-width:none; }
@media(max-width:1024px) {
    .dash-grid { grid-template-columns: 1fr !important; }
}
@media(max-width:768px) {
    .actions-grid { grid-template-columns: repeat(2,1fr) !important; }
}
</style>
