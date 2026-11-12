<template>
  <!-- One A4 page of competition cards, in one of the layouts of cardLayouts.js (e.g. six cards two by three, or four
       on a landscape page), with room between them to cut. -->
  <SignSheet :preview="preview" plates="none" :portrait="layout.orientation === 'portrait'">
    <div
      class="grid h-full content-start justify-center gap-x-[6mm] gap-y-[5mm] px-[7mm] py-[8mm]"
      :style="{ gridTemplateColumns: `repeat(${layout.columns}, minmax(0, 1fr))` }"
    >
      <div v-for="card in cards" :key="card.lifter.id ?? card.lifter.name" class="flex justify-center">
        <Tavlingskort :lifter="card.lifter" :group="card.group" :width="layout.width" :zoom="layout.zoom" />
      </div>
    </div>
  </SignSheet>
</template>

<script setup>
import SignSheet from './SignSheet.vue'
import Tavlingskort from './Tavlingskort.vue'

defineProps({
  // As many as the layout has room for: [{ group, lifter }]
  cards: { type: Array, required: true },
  // One of CARD_LAYOUTS (cardLayouts.js).
  layout: { type: Object, required: true },
  preview: { type: Boolean, default: false },
})
</script>
