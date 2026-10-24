<template>
  <div class="referees">
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
})

const state = (light) => (light ? (light.good ? 'good' : 'failed') : 'off')
</script>

<style scoped>
/*
  The referee lights, large in the bottom right corner (beside the lower third, which is at most ~1280 px wide and
  centered). Each lamp lights up as its referee decides, and stays with the result.
*/
.referees {
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

.lamp {
  position: relative;
  width: 54px;
  height: 54px;
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
  box-shadow: 0 0 22px rgba(255, 255, 255, 0.7);
}

.failed .glow {
  background: var(--overlay-failed);
  box-shadow: 0 0 22px rgba(199, 69, 61, 0.8);
}

/* Reason cards (red/yellow/blue) under a red lamp. */
.cards {
  position: absolute;
  bottom: -14px;
  left: 50%;
  display: flex;
  gap: 3px;
  transform: translateX(-50%);
}

.card {
  width: 13px;
  height: 17px;
  border-radius: 2px;
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
