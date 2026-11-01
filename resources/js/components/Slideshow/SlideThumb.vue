<template>
  <!-- A small picture of a slide in the admin list: the club's own slides as on the screen, the built-in ones by icon. -->
  <div class="aspect-video shrink-0 overflow-hidden rounded-sm ring-1 ring-gray-200">
    <div
      v-if="icon"
      class="flex h-full w-full items-center justify-center"
      :class="dark ? 'bg-gkk text-amber-300' : 'bg-gray-100 text-gkk'"
    >
      <component :is="icon" class="size-1/3" :stroke-width="1.5" />
    </div>
    <SlidePreview v-else v-bind="slide.data" />
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { CalendarDays, Flag, Medal, QrCode, Trophy } from 'lucide-vue-next'
import SlidePreview from './SlidePreview.vue'

const props = defineProps({ slide: { type: Object, required: true } })

const ICONS = { Klubbrekord: Trophy, Resultat: Medal, Dm2026: Flag, Gm2027: CalendarDays, Swish: QrCode }
const icon = computed(() => ICONS[props.slide.type] ?? null)
// The DM and GM slides are dark on the screen.
const dark = computed(() => ['Dm2026', 'Gm2027'].includes(props.slide.type))
</script>
