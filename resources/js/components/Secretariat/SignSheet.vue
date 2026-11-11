<template>
  <!-- An A4 sheet (landscape) for printed signs, drawn at its real size. As a preview it is scaled down to the width it
       is given, so a preview shows exactly what is printed. The sheet is white with black ink and only hairline grey
       weight plates as decoration, so it prints lightly and looks the same in black and white. -->
  <div ref="frame" :class="preview ? 'relative aspect-[297/210] w-full overflow-hidden bg-white' : ''">
    <div
      class="sign-sheet relative overflow-hidden bg-white text-black"
      :class="preview ? 'absolute left-0 top-0 origin-top-left' : ''"
      :style="preview ? { transform: `scale(${scale})` } : {}"
    >
      <svg
        v-for="plate in PLATES[plates]"
        :key="plate"
        class="pointer-events-none absolute text-black/[0.07]"
        :class="plate"
        viewBox="0 0 200 200"
        fill="none"
        stroke="currentColor"
        aria-hidden="true"
      >
        <circle cx="100" cy="100" r="98" stroke-width="2" />
        <circle cx="100" cy="100" r="84" stroke-width="1" />
        <circle cx="100" cy="100" r="66" stroke-width="1" />
        <circle cx="100" cy="100" r="16" stroke-width="7" />
      </svg>

      <div class="relative h-full">
        <slot />
      </div>
    </div>
  </div>
</template>

<script setup>
import { onBeforeUnmount, onMounted, ref } from 'vue'

// 297mm at the 96 dpi of CSS pixels.
const SHEET_WIDTH = (297 / 25.4) * 96

// Where the weight plates are, as in the slideshow: one on each side (the GM slide) or one in a corner (the DM slide).
const PLATES = {
  sides: [
    'top-1/2 -left-[118mm] h-[170mm] w-[170mm] -translate-y-1/2',
    'top-1/2 -right-[118mm] h-[170mm] w-[170mm] -translate-y-1/2',
  ],
  corner: ['-right-[135mm] -bottom-[135mm] h-[240mm] w-[240mm]'],
}

const props = defineProps({
  preview: { type: Boolean, default: false },
  plates: { type: String, default: 'sides' },
})

const frame = ref()
const scale = ref(0.25)

let observer
onMounted(() => {
  if (props.preview) {
    observer = new ResizeObserver(([entry]) => (scale.value = entry.contentRect.width / SHEET_WIDTH))
    observer.observe(frame.value)
  }
})
onBeforeUnmount(() => observer?.disconnect())
</script>

<style scoped>
.sign-sheet {
  width: 297mm;
  height: 210mm;
  box-sizing: border-box;
  print-color-adjust: exact;
  -webkit-print-color-adjust: exact;
}
</style>
