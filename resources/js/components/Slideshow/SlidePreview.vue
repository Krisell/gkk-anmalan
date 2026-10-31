<template>
  <!--
    A small, true-to-screen preview of a slide (see Slide.vue). The screen sizes its text by the viewport; here the same
    proportions are relative to the preview box (cqw), so it looks like the screen at any size.
  -->
  <div class="@container relative aspect-video overflow-hidden bg-linear-to-br from-slate-50 via-white to-slate-100">
    <div v-if="records" class="absolute inset-0 flex flex-col items-center justify-center gap-[3cqw] bg-gkk/5 text-gkk">
      <Trophy class="size-[12cqw] opacity-80" :stroke-width="1.5" />
      <span class="text-[5cqw] font-semibold">Senaste klubbrekorden</span>
    </div>
    <div v-else-if="isEmpty" class="absolute inset-0 flex items-center justify-center text-[4cqw] text-gray-400">
      Tom slide
    </div>
    <div v-else class="absolute inset-0 flex flex-col items-center justify-center gap-[2.5cqw] px-[4cqw] py-[4cqw]">
      <div v-if="text" class="flex shrink-0 flex-col items-center gap-[1.5cqw]">
        <div class="h-[0.6cqw] min-h-px w-[6cqw] rounded-full bg-gkk/80"></div>
        <p
          class="text-center font-semibold leading-tight tracking-tight text-balance text-gkk"
          :class="image ? 'text-[4.5cqw]' : 'max-w-[85%] text-[6.5cqw]'"
        >
          {{ text }}
        </p>
      </div>
      <p
        v-if="subText"
        class="shrink-0 text-center font-light text-balance text-gkk-light"
        :class="image ? 'text-[2.2cqw]' : 'max-w-[75%] text-[3cqw]'"
      >
        {{ subText }}
      </p>
      <div v-if="image" class="flex min-h-0 w-full flex-1 items-center justify-center">
        <img class="max-h-full max-w-full rounded-[1cqw] object-contain drop-shadow-md" :src="image" alt="" />
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { Trophy } from 'lucide-vue-next'

const props = defineProps({
  text: { type: String, default: null },
  subText: { type: String, default: null },
  image: { type: String, default: null },
  // The automatic club records slide.
  records: { type: Boolean, default: false },
})

const isEmpty = computed(() => !props.text && !props.subText && !props.image)
</script>
