<template>
  <div class="referees" :class="{ large }">
    <div v-for="(light, index) in lights" :key="index" class="lamp" :class="state(light)">
      <!-- Keyed by the light, so each lamp animates on when its referee decides. -->
      <span v-if="light" :key="state(light)" class="glow" />
      <div v-if="light?.cards.length" class="cards">
        <span v-for="card in light.cards" :key="card" class="card" :class="card" />
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  // [left, center, right]: a light ({ good, cards }) per referee who has decided, null for one who hasn't yet.
  lights: { type: Array, required: true },
  // Very large, placed by the parent (the warm-up hall screen shows them in the middle), instead of in the corner.
  large: { type: Boolean, default: false },
})

const state = (light) => (light ? (light.good ? 'good' : 'failed') : 'off')
</script>

<style scoped>
/*
  The referee lights, large in the bottom right corner (beside the lower third, which is at most ~1280 px wide and
  centered). Each lamp lights up as its referee decides, and stays with the result.
*/
.referees {
  --lamp: 54px;
  position: absolute;
  right: var(--safe-x);
  bottom: var(--safe-y);
  display: flex;
  gap: 12px;
  padding: 16px 16px 22px;
  border-radius: 14px;
  background: var(--overlay-panel-deep);
  box-shadow: 0 16px 34px rgba(0, 0, 0, 0.42);
  animation: panel-in var(--motion-base) var(--motion-enter) both;
}

.referees.large {
  --lamp: 200px;
  position: relative;
  right: auto;
  bottom: auto;
  gap: 40px;
  padding: 36px 44px 72px;
  border-radius: 28px;
}

.lamp {
  position: relative;
  width: var(--lamp);
  height: var(--lamp);
  border-radius: 50%;
  background: rgba(255, 255, 255, 0.06);
  box-shadow: inset 0 0 0 2px rgba(255, 255, 255, 0.22);
}

.glow {
  position: absolute;
  inset: 0;
  border-radius: 50%;
  animation: lamp-on 280ms ease-out both;
}

.good .glow {
  background: #ffffff;
  box-shadow: 0 0 calc(var(--lamp) * 0.4) rgba(255, 255, 255, 0.7);
}

.failed .glow {
  background: var(--overlay-failed);
  box-shadow: 0 0 calc(var(--lamp) * 0.4) rgba(199, 69, 61, 0.8);
}

/* Reason cards (red/yellow/blue) under a red lamp. */
.cards {
  position: absolute;
  bottom: calc(var(--lamp) * -0.26);
  left: 50%;
  display: flex;
  gap: calc(var(--lamp) * 0.055);
  transform: translateX(-50%);
}

.card {
  width: calc(var(--lamp) * 0.24);
  height: calc(var(--lamp) * 0.315);
  border-radius: calc(var(--lamp) * 0.04);
  box-shadow: 0 0 0 1px rgba(0, 0, 0, 0.35);
  animation: lamp-on 280ms ease-out both;
}

.card.red {
  background: var(--overlay-card-red);
}

.card.yellow {
  background: var(--overlay-card-yellow);
}

.card.blue {
  background: var(--overlay-card-blue);
}

@keyframes panel-in {
  from {
    opacity: 0;
    transform: translateY(24px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes lamp-on {
  from {
    opacity: 0;
    transform: scale(0.6);
  }
  to {
    opacity: 1;
    transform: scale(1);
  }
}
</style>
