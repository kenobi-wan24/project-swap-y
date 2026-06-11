<script setup>
defineProps({
  svc:   { type: Object,  required: true },
  saved: { type: Boolean, default: false },
})
defineEmits(['toggle-save'])

function ratingText(r) {
  const n = Number(r)
  return Number.isFinite(n) ? n.toFixed(1) : '—'
}
</script>

<template>
  <a :href="'/services/' + svc.id" class="svc-card">

    <!-- Media -->
    <div class="svc-media">
      <img :src="svc.image" :alt="svc.title" loading="lazy" @error="e => (e.target.style.visibility = 'hidden')">

      <span class="svc-tag">{{ svc.tag || svc.category }}</span>

      <button
        type="button"
        class="svc-save"
        :class="{ on: saved }"
        :aria-label="saved ? 'Remove from saved' : 'Save service'"
        @click.stop.prevent="$emit('toggle-save')"
      >
        <svg viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/></svg>
      </button>

      <span v-if="svc.is_match" class="svc-match">
        <span class="svc-match-dot"></span>{{ svc.match_percent }}% match
      </span>

      <span v-if="svc.is_promoted" class="svc-promoted">
        <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>Promoted
      </span>
    </div>

    <!-- Body -->
    <div class="svc-body">
      <div class="svc-provider">
        <span class="svc-avatar" :style="{ background: svc.provider_color || '#ED730C' }">{{ svc.provider_initials }}</span>
        <span class="svc-provider-name">{{ svc.provider }}</span>
        <svg class="svc-verified" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
          <path d="M12 1.6l2.6 1.9 3.2.05.95 3.05 2.6 1.9-1 3.05 1 3.05-2.6 1.9-.95 3.05-3.2.05L12 22.4l-2.6-1.9-3.2-.05-.95-3.05-2.6-1.9 1-3.05-1-3.05 2.6-1.9.95-3.05 3.2-.05L12 1.6z"/>
          <path d="M10.6 14.3l-2-2-1.3 1.3 3.3 3.3 6-6-1.3-1.3-4.7 4.7z" fill="#fff"/>
        </svg>
      </div>

      <h3 class="svc-title">{{ svc.title }}</h3>
      <p class="svc-desc">{{ svc.description }}</p>

      <div class="svc-foot">
        <span class="svc-rating">
          <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
          {{ ratingText(svc.rating) }}
        </span>
        <span v-if="svc.city" class="svc-loc">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M21 10c0 7-9 12-9 12s-9-5-9-12a9 9 0 1118 0z"/><circle cx="12" cy="10" r="3"/></svg>
          {{ svc.city }}
        </span>
      </div>
    </div>
  </a>
</template>

<style scoped>
.svc-card {
  display: flex; flex-direction: column;
  background: #fff; border: 1px solid #EDE8E0; border-radius: 18px;
  overflow: hidden; text-decoration: none; color: inherit;
  min-width: 0; height: 100%;
  transition: border-color .2s, box-shadow .2s, transform .2s;
}
.svc-card:hover { border-color: #ED730C; box-shadow: 0 10px 32px rgba(237,115,12,0.12); transform: translateY(-3px); }
.svc-card:hover .svc-media img { transform: scale(1.05); }

/* Media */
.svc-media { position: relative; aspect-ratio: 16/10; overflow: hidden; background: #f3f4f6; flex-shrink: 0; }
.svc-media img { width: 100%; height: 100%; object-fit: cover; transition: transform .4s; }

.svc-tag {
  position: absolute; top: 11px; left: 11px;
  background: rgba(255,255,255,0.92); backdrop-filter: blur(6px);
  color: #3A3330; font-size: 0.62rem; font-weight: 800;
  padding: 5px 10px; border-radius: 999px; letter-spacing: .04em; text-transform: uppercase;
}

.svc-save {
  position: absolute; top: 9px; right: 9px;
  width: 32px; height: 32px; border: none; border-radius: 50%;
  background: rgba(255,255,255,0.9); backdrop-filter: blur(6px);
  display: flex; align-items: center; justify-content: center;
  cursor: pointer; box-shadow: 0 2px 8px rgba(0,0,0,0.12); transition: transform .15s;
}
.svc-save:hover { transform: scale(1.12); }
.svc-save svg { width: 15px; height: 15px; fill: none; stroke: #6b7280; stroke-width: 2; transition: all .15s; }
.svc-save.on svg { fill: #ED730C; stroke: #ED730C; }

.svc-match {
  position: absolute; bottom: 11px; left: 11px;
  display: flex; align-items: center; gap: 5px;
  background: rgba(255,255,255,0.94); backdrop-filter: blur(6px);
  color: #ED730C; font-size: 0.66rem; font-weight: 800;
  padding: 5px 10px; border-radius: 999px; box-shadow: 0 2px 8px rgba(0,0,0,0.10);
}
.svc-match-dot { width: 6px; height: 6px; border-radius: 50%; background: #ED730C; }

.svc-promoted {
  position: absolute; top: 11px; right: 49px;
  display: flex; align-items: center; gap: 3px;
  background: #1A1A1A; color: #fff;
  font-size: 0.55rem; font-weight: 800;
  padding: 4px 9px; border-radius: 999px; letter-spacing: .04em; text-transform: uppercase;
}
.svc-promoted svg { width: 9px; height: 9px; }

/* Body */
.svc-body { padding: 13px 15px 15px; display: flex; flex-direction: column; flex: 1; }

.svc-provider { display: flex; align-items: center; gap: 7px; margin-bottom: 9px; }
.svc-avatar {
  width: 24px; height: 24px; border-radius: 50%; color: #fff;
  font-size: 0.56rem; font-weight: 800; flex-shrink: 0;
  display: flex; align-items: center; justify-content: center;
}
.svc-provider-name { font-size: 0.76rem; font-weight: 700; color: #5c5751; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.svc-verified { width: 13px; height: 13px; color: #149189; flex-shrink: 0; }

.svc-title {
  font-size: 0.9rem; font-weight: 800; color: #1A1A1A; line-height: 1.3;
  margin: 0 0 5px; letter-spacing: -.01em;
  display: -webkit-box; -webkit-line-clamp: 2; line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;
}
.svc-desc {
  font-size: 0.78rem; color: #9ca3af; line-height: 1.5; margin: 0 0 12px; flex: 1;
  display: -webkit-box; -webkit-line-clamp: 1; line-clamp: 1; -webkit-box-orient: vertical; overflow: hidden;
}

.svc-foot { display: flex; align-items: center; justify-content: space-between; gap: 8px; padding-top: 11px; border-top: 1px solid #f3f0ec; }
.svc-rating { display: flex; align-items: center; gap: 4px; font-size: 0.78rem; font-weight: 800; color: #1A1A1A; flex-shrink: 0; }
.svc-rating svg { width: 13px; height: 13px; color: #f59e0b; }
.svc-loc { display: flex; align-items: center; gap: 3px; font-size: 0.72rem; font-weight: 600; color: #9ca3af; white-space: nowrap; overflow: hidden; min-width: 0; }
.svc-loc svg { width: 11px; height: 11px; flex-shrink: 0; }
</style>
