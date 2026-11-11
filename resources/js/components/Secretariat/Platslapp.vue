<template>
  <!-- An A4 sign (landscape) with one text, in the look of the slideshow's GM slide: the club's crest between two
       hairlines, the text as large as fits, and a short bar under it. The text is fitted to the sheet itself, so the
       preview shows the same lines and size as the printed sheet. -->
  <SignSheet :preview="preview" plates="sides">
    <div class="flex h-full flex-col items-center px-[26mm] pb-[16mm] pt-[12mm]">
      <div class="flex w-full items-center gap-[7mm]">
        <span class="h-[0.5mm] flex-1 rounded-full bg-black/25"></span>
        <img class="h-[36mm] w-[36mm] object-contain grayscale" :src="LOGO" alt="Göteborg Kraftsportklubb" />
        <span class="h-[0.5mm] flex-1 rounded-full bg-black/25"></span>
      </div>

      <div ref="box" class="flex min-h-0 w-full flex-1 items-center justify-center overflow-hidden mt-[6mm]">
        <div ref="content" class="flex w-full flex-col items-center">
          <p
            ref="label"
            class="w-full text-center font-black leading-[1.02] tracking-tight text-balance break-normal hyphens-none"
            :class="{ uppercase: !subText, 'text-black/15': !text }"
          >
            {{ text || placeholder }}
          </p>
          <span class="mt-[7mm] h-[2.2mm] w-[26mm] shrink-0 rounded-full bg-black"></span>

          <!-- A smaller line under the text, e.g. who welcomes the lifters. -->
          <p
            v-if="subText"
            class="mt-[7mm] max-w-[220mm] text-center text-[10mm] font-semibold leading-tight text-black/75 text-balance"
          >
            {{ subText }}
          </p>
        </div>
      </div>
    </div>
  </SignSheet>
</template>

<script setup>
import { nextTick, onMounted, ref, watch } from 'vue'
import SignSheet from './SignSheet.vue'

const LOGO = 'https://goteborg-kraftsportklubb.web.app/img/logo-min.png'

const props = defineProps({
  text: { type: String, default: '' },
  placeholder: { type: String, default: 'Din text' },
  subText: { type: String, default: '' },
  preview: { type: Boolean, default: false },
})

const box = ref()
const content = ref()
const label = ref()

// The largest font size at which the text (and the bar and line under it) fits the sheet, without breaking a word.
async function fit() {
  await nextTick()
  await document.fonts?.ready
  const el = label.value
  if (!el || !box.value) {
    return
  }
  const fits = () => el.scrollWidth <= el.clientWidth && content.value.offsetHeight <= box.value.clientHeight
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

onMounted(fit)
watch(() => [props.text, props.subText], fit)

defineExpose({ fit })
</script>
