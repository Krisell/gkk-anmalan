<template>
  <div class="info">
    <span class="eyebrow">Info</span>
    <span class="text">{{ text }}</span>
  </div>
</template>

<script setup>
defineProps({
  text: { type: String, required: true },
})
</script>

<style scoped>
/*
  A message from the remote control (e.g. "Rekordförsök!" or "Prisutdelning efter grupp 3"), top left inside the safe
  area, where nothing else is shown. It slides in and stays until it is taken down.
*/
.info {
  position: absolute;
  top: var(--safe-y);
  left: var(--safe-x);
  display: flex;
  align-items: stretch;
  max-width: 1300px;
  border-radius: 10px;
  overflow: hidden;
  background: var(--overlay-panel);
  box-shadow: 0 14px 34px rgba(0, 0, 0, 0.4);
  animation: info-in var(--motion-slow) var(--motion-enter) both;
}

.eyebrow {
  display: flex;
  align-items: center;
  padding: 0 22px;
  background: var(--overlay-silver);
  font-family: var(--overlay-font);
  font-size: 22px;
  font-weight: 700;
  letter-spacing: 0.18em;
  text-transform: uppercase;
  color: var(--overlay-navy-deep);
}

.text {
  padding: 18px 30px;
  font-family: var(--overlay-font);
  font-size: 40px;
  font-weight: 700;
  line-height: 1.1;
}

@keyframes info-in {
  from {
    opacity: 0;
    transform: translateX(-60px);
  }
  to {
    opacity: 1;
    transform: translateX(0);
  }
}
</style>
