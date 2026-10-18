<template>
  <div class="pb">
    <div class="stamp">
      <div class="text">PB ALERT</div>
      <div v-if="detail" class="detail">{{ detail }}</div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { formatWeight } from '../format.js'

const props = defineProps({
  // The attempt on screen when the stamp was triggered, if any.
  attempt: { type: Object, default: null },
})

const detail = computed(() => {
  if (!props.attempt?.name) {
    return null
  }

  return props.attempt.weight ? `${props.attempt.name} · ${formatWeight(props.attempt.weight)} kg` : props.attempt.name
})
</script>

<style scoped>
/* A rubber stamp across the middle of the screen: it drops in large, lands with a small bounce, then fades. */
.pb {
  --pb-ink: #f5c542;

  position: absolute;
  inset: 0;
  display: grid;
  place-items: center;
  pointer-events: none;
}

.stamp {
  padding: 18px 64px 24px;
  border: 12px solid var(--pb-ink);
  border-radius: 18px;
  background: rgba(20, 28, 54, 0.72);
  box-shadow:
    0 0 0 4px rgba(20, 28, 54, 0.6),
    0 0 60px rgba(245, 197, 66, 0.45);
  color: var(--pb-ink);
  text-align: center;
  transform: rotate(-12deg);
  animation:
    stamp-in 450ms cubic-bezier(0.2, 0.8, 0.3, 1.2) both,
    stamp-out 400ms ease-in 3000ms both;
}

.text {
  font-family: var(--overlay-font);
  font-size: 200px;
  font-weight: 700;
  line-height: 1;
  letter-spacing: 0.04em;
  text-shadow: 0 4px 24px rgba(0, 0, 0, 0.5);
}

.detail {
  margin-top: 8px;
  font-family: var(--overlay-font);
  font-size: 44px;
  font-weight: 600;
  letter-spacing: 0.12em;
  text-transform: uppercase;
  color: #ffffff;
}

@keyframes stamp-in {
  from {
    opacity: 0;
    transform: rotate(-12deg) scale(2.4);
  }
  60% {
    opacity: 1;
    transform: rotate(-12deg) scale(0.94);
  }
  to {
    opacity: 1;
    transform: rotate(-12deg) scale(1);
  }
}

@keyframes stamp-out {
  to {
    opacity: 0;
    transform: rotate(-12deg) scale(1.08);
  }
}
</style>
