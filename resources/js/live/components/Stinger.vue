<template>
  <div class="stinger">
    <div class="band accent" />
    <div class="band deep" />
    <div class="band main" />
    <img :src="logo" alt="" class="logo" />
  </div>
</template>

<script setup>
import { BROADCASTER_LOGO as logo } from '../clubs.js'
</script>

<style scoped>
/*
  The scene transition (OBS "Stinger"), rendered to a video with transparency by tools/render-stinger.mjs.
  1.2 s long: diagonal bands sweep in and cover the whole picture between 0.54 and 0.66 s (OBS swaps the scene
  underneath at 0.6 s, the "transition point"), with the logo in the middle, then sweep out to the right.
*/
.stinger {
  --duration: 1200ms;

  position: absolute;
  inset: 0;
  overflow: hidden;
}

.band {
  position: absolute;
  top: -10%;
  left: -340px;
  width: 2600px;
  height: 120%;
  transform: translateX(-2900px) skewX(-20deg);
  animation: sweep var(--duration) var(--motion-move) both;
}

.accent {
  background: var(--overlay-silver);
}

.deep {
  background: var(--overlay-navy-deep);
  animation-delay: 40ms;
}

.main {
  background: var(--overlay-navy);
  animation-delay: 80ms;
}

.logo {
  position: absolute;
  top: 50%;
  left: 50%;
  width: 260px;
  height: 260px;
  margin: -130px 0 0 -130px;
  padding: 9px;
  border-radius: 50%;
  background: #ffffff;
  box-shadow: 0 16px 50px rgba(0, 0, 0, 0.45);
  animation: logo var(--duration) linear both;
}

@keyframes sweep {
  0% {
    transform: translateX(-2900px) skewX(-20deg);
  }
  45%,
  55% {
    transform: translateX(0) skewX(-20deg);
  }
  100% {
    transform: translateX(2900px) skewX(-20deg);
  }
}

@keyframes logo {
  0%,
  30% {
    opacity: 0;
    transform: scale(0.6);
  }
  45% {
    opacity: 1;
    transform: scale(1);
  }
  62% {
    opacity: 1;
    transform: scale(1.04);
  }
  72%,
  100% {
    opacity: 0;
    transform: scale(1.12);
  }
}
</style>
