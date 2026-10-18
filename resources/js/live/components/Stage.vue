<template>
  <div class="viewport">
    <div class="stage" :style="{ transform: `translate(-50%, -50%) scale(${scale})` }">
      <slot />
    </div>
  </div>
</template>

<script setup>
import { onBeforeUnmount, onMounted, ref } from 'vue'

// Everything is laid out on a fixed 1920×1080 canvas and scaled to fit the browser source.
const WIDTH = 1920
const HEIGHT = 1080

const scale = ref(1)

function resize() {
  scale.value = Math.min(window.innerWidth / WIDTH, window.innerHeight / HEIGHT)
}

onMounted(() => {
  resize()
  window.addEventListener('resize', resize)
})

onBeforeUnmount(() => window.removeEventListener('resize', resize))
</script>

<style scoped>
.viewport {
  position: fixed;
  inset: 0;
  overflow: hidden;
}

.stage {
  position: absolute;
  top: 50%;
  left: 50%;
  width: 1920px;
  height: 1080px;
  transform-origin: center;
}
</style>
