<template>
  <div class="lights">
    <div
      v-for="(light, index) in lights"
      :key="index"
      class="light"
      :class="light.good ? 'good' : 'failed'"
      :style="{ animationDelay: `${index * 90}ms` }"
    >
      <div class="cards">
        <span v-for="card in light.cards" :key="card" class="card" :class="card" />
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  lights: { type: Array, required: true },
})
</script>

<style scoped>
.lights {
  display: flex;
  align-items: center;
  gap: 12px;
}

.light {
  position: relative;
  width: 26px;
  height: 26px;
  border-radius: 50%;
  border: 2px solid rgba(255, 255, 255, 0.35);
  animation: light-on 260ms ease-out both;
}

.light.good {
  background: #ffffff;
  box-shadow: 0 0 12px rgba(255, 255, 255, 0.6);
}

.light.failed {
  background: var(--overlay-failed);
  box-shadow: 0 0 12px rgba(199, 69, 61, 0.7);
}

/* Reason cards (red/yellow/blue) as small chips under a red light. */
.cards {
  position: absolute;
  bottom: -7px;
  left: 50%;
  display: flex;
  gap: 2px;
  transform: translateX(-50%);
}

.card {
  width: 8px;
  height: 10px;
  border-radius: 1px;
  box-shadow: 0 0 0 1px rgba(0, 0, 0, 0.35);
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

@keyframes light-on {
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
