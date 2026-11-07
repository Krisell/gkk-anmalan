<template>
  <!-- An A4 sign (landscape), drawn at its real size. As a preview it is scaled down to the width it is given; the
       text is fitted to the sheet itself, so the preview shows the same lines and size as the printed sheet. -->
  <div ref="frame" :class="preview ? 'relative aspect-[297/210] w-full overflow-hidden' : ''">
    <div
      class="platslapp-sheet flex flex-col bg-white text-black"
      :class="preview ? 'absolute left-0 top-0 origin-top-left' : ''"
      :style="preview ? { transform: `scale(${scale})` } : {}"
    >
      <header class="mt-[8mm] flex justify-center">
        <img class="h-[44mm] w-[44mm] object-contain grayscale" :src="LOGO" alt="Göteborg Kraftsportklubb" />
      </header>

      <div class="min-h-0 flex-1 px-[22mm] py-[10mm]">
        <div ref="box" class="flex h-full w-full items-center justify-center overflow-hidden">
          <p
            ref="label"
            class="w-full text-center font-black uppercase leading-[1.02] tracking-tight break-normal hyphens-none"
            :class="{ 'text-black/15': !text }"
          >
            {{ text || placeholder }}
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { nextTick, onBeforeUnmount, onMounted, ref, watch } from 'vue'

const LOGO = 'https://goteborg-kraftsportklubb.web.app/img/logo-min.png'
// 297mm at the 96 dpi of CSS pixels.
const SHEET_WIDTH = (297 / 25.4) * 96

const props = defineProps({
  text: { type: String, default: '' },
  placeholder: { type: String, default: 'Din text' },
  preview: { type: Boolean, default: false },
})

const frame = ref()
const box = ref()
const label = ref()
const scale = ref(0.25)

// The largest font size at which the text fits the sheet, without breaking a word.
async function fit() {
  await nextTick()
  await document.fonts?.ready
  const el = label.value
  if (!el || !box.value) {
    return
  }
  const fits = () => el.scrollWidth <= box.value.clientWidth && el.scrollHeight <= box.value.clientHeight
  let low = 8
  let high = 1000
  while (high - low > 1) {
    const mid = Math.floor((low + high) / 2)
    el.style.fontSize = `${mid}px`
    if (fits()) {
      low = mid
    } else {
      high = mid
    }
  }
  el.style.fontSize = `${low}px`
}

let observer
onMounted(() => {
  fit()
  if (props.preview) {
    observer = new ResizeObserver(([entry]) => (scale.value = entry.contentRect.width / SHEET_WIDTH))
    observer.observe(frame.value)
  }
})
onBeforeUnmount(() => observer?.disconnect())

watch(() => props.text, fit)

defineExpose({ fit })
</script>

<style scoped>
.platslapp-sheet {
  width: 297mm;
  height: 210mm;
  padding: 14mm 16mm;
  box-sizing: border-box;
  print-color-adjust: exact;
  -webkit-print-color-adjust: exact;
}
</style>
