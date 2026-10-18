<template>
  <img :src="logo" alt="Göteborg Kraftsportklubb" class="bug" />
</template>

<script setup>
import { BROADCASTER_LOGO as logo } from '../clubs.js'
</script>

<style scoped>
/* The permanent corner graphic of the broadcast: our logo, inside the safe area. */
.bug {
  position: absolute;
  top: var(--safe-y);
  right: var(--safe-x);
  width: 84px;
  height: 84px;
  padding: 3px;
  border-radius: 50%;
  background: #ffffff;
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.35);
  animation: bug-in var(--motion-slow) var(--motion-enter) both;
}

@keyframes bug-in {
  from {
    opacity: 0;
    transform: scale(0.7);
  }
  to {
    opacity: 1;
    transform: scale(1);
  }
}
</style>
