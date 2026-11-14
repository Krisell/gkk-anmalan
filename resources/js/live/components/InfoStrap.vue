<template>
  <div class="info" :class="{ small }">
    <span class="text">{{ text }}</span>
  </div>
</template>

<script setup>
defineProps({
  text: { type: String, required: true },
  // The permanent note: smaller, above the messages.
  small: { type: Boolean, default: false },
})
</script>

<style scoped>
/*
  A message from the remote control (e.g. "Rekordförsök!" or "Prisutdelning efter grupp 3"), top left inside the safe
  area, where nothing else is shown. It slides in and stays until it is taken down.
*/
.info {
  display: flex;
  align-items: stretch;
  max-width: 1300px;
  border-radius: 10px;
  overflow: hidden;
  background: var(--overlay-panel);
  /* The silver edge marks it as a message. */
  border-left: 12px solid var(--overlay-silver);
  box-shadow: 0 14px 34px rgba(0, 0, 0, 0.4);
  animation: info-in var(--motion-slow) var(--motion-enter) both;
}

.text {
  padding: 18px 30px;
  font-family: var(--overlay-font);
  font-size: 40px;
  font-weight: 700;
  line-height: 1.1;
}

.info.small {
  border-left-width: 8px;
  border-radius: 8px;
}

.small .text {
  padding: 9px 22px 10px;
  font-size: 26px;
  font-weight: 600;
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
