<template>
  <div class="viewport">
    <div class="stage" :style="{ height: `${height}px`, transform: `translate(-50%, -50%) scale(${scale})` }">
      <slot />
    </div>
  </div>
</template>

<script setup>
import { onBeforeUnmount, onMounted, ref, watch } from 'vue'

// Everything is laid out on a 1920 px wide canvas (1080 px high unless the page asks for another height, as the
// warm-up hall screen does to fill its frame) and scaled to fit the browser source.
const WIDTH = 1920

const props = defineProps({
  height: { type: Number, default: 1080 },
})

const scale = ref(1)

function resize() {
  scale.value = Math.min(window.innerWidth / WIDTH, window.innerHeight / props.height)
}

watch(() => props.height, resize)

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
