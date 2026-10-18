<template>
  <div class="debug" :class="status">
    <div class="row">
      <strong>{{ statusLabel }}</strong>
      <span>{{ source }}</span>
      <span v-if="age != null">uppdaterad {{ age }} s sedan</span>
      <span v-if="failures">{{ failures }} fel i rad</span>
    </div>
    <div v-if="error" class="row">{{ error }}</div>
    <div class="row muted">Lyftare: {{ currentId ?? '–' }} · Resultat: {{ resultId ?? '–' }}</div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  status: { type: String, required: true }, // 'loading' | 'live' | 'stale' | 'offline'
  source: { type: String, required: true },
  age: { type: Number, default: null },
  failures: { type: Number, default: 0 },
  error: { type: String, default: null },
  currentId: { type: String, default: null },
  resultId: { type: String, default: null },
})

const statusLabel = computed(
  () => ({ loading: 'LADDAR', live: 'LIVE', stale: 'FÖRDRÖJD', offline: 'OFFLINE' })[props.status] ?? props.status,
)
</script>

<style scoped>
.debug {
  position: absolute;
  z-index: 10;
  top: 24px;
  right: 24px;
  padding: 10px 14px;
  border-radius: 6px;
  border-left: 6px solid #888888;
  background: rgba(0, 0, 0, 0.75);
  font-size: 18px;
  line-height: 1.5;
}

.debug.live {
  border-color: var(--overlay-good);
}

.debug.stale {
  border-color: var(--overlay-card-yellow);
}

.debug.offline {
  border-color: var(--overlay-failed);
}

.row {
  display: flex;
  gap: 14px;
}

.muted {
  color: var(--overlay-text-muted);
}
</style>
