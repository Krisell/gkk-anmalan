<template>
  <!-- An A4 signpost (landscape) that shows the way to the places of the competition, each with an arrow in one of
       eight directions. The places are laid out as a map of their directions: ahead at the top, back at the bottom,
       left aligned to the left and right to the right. Places that share a direction share an arrow, and a place to
       the left and one to the right may share a row when that lets the text be larger. -->
  <SignSheet :preview="preview" plates="corner">
    <div class="flex h-full flex-col px-[16mm] pb-[12mm] pt-[11mm]">
      <header class="flex items-center gap-[5mm] border-b-[1.2mm] border-black pb-[5mm]">
        <img class="h-[22mm] w-[22mm] shrink-0 object-contain grayscale" :src="LOGO" alt="Göteborg Kraftsportklubb" />
        <div class="min-w-0">
          <p class="text-[4mm] font-bold uppercase leading-none tracking-[0.22em] text-black/55">
            Göteborg Kraftsportklubb
          </p>
          <p class="mt-[2mm] text-[11mm] font-extrabold leading-none tracking-tight">Hitta rätt</p>
        </div>
        <p
          v-if="kicker"
          class="ml-auto max-w-[120mm] rounded-full border-[0.5mm] border-black px-[5mm] py-[1.6mm] text-right text-[4.2mm] font-bold uppercase leading-tight tracking-[0.16em]"
        >
          {{ kicker }}
        </p>
      </header>

      <div ref="box" class="mt-[4mm] flex min-h-0 flex-1 flex-col justify-center overflow-hidden">
        <p v-if="!rows.length" class="text-center text-[12mm] font-extrabold text-black/15">Välj riktningar</p>
        <div ref="list" class="flex flex-col">
          <div
            v-for="(row, index) in rows"
            :key="index"
            class="flex items-stretch border-black/20"
            :class="{ 'border-t-[0.4mm]': index > 0 }"
          >
            <div
              v-for="(cell, cellIndex) in row"
              :key="cell.direction"
              class="flex min-w-0 flex-1 items-center py-[0.2em]"
              :class="[
                ALIGN[cell.side],
                {
                  'border-l-[0.4mm] border-black/20 pl-[0.4em]': cellIndex > 0,
                  'pr-[0.4em]': row.length > 1 && cellIndex === 0,
                },
              ]"
            >
              <div
                data-fit
                class="flex w-max shrink-0 items-center gap-[0.32em]"
                :class="{ 'flex-row-reverse text-right': cell.side === 'right' }"
              >
                <svg class="h-[1.12em] w-[1.12em] shrink-0" viewBox="0 0 100 100" aria-hidden="true">
                  <circle cx="50" cy="50" r="46" fill="none" stroke="currentColor" stroke-width="5" />
                  <polygon
                    points="50,16 78,46 60,46 60,82 40,82 40,46 22,46"
                    fill="currentColor"
                    stroke="currentColor"
                    stroke-width="3"
                    stroke-linejoin="round"
                    :transform="`rotate(${ANGLES[cell.direction]} 50 50)`"
                  />
                </svg>
                <div>
                  <p
                    v-for="place in cell.places"
                    :key="place"
                    class="whitespace-nowrap font-extrabold leading-[1.08] tracking-tight"
                  >
                    {{ place }}
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </SignSheet>
</template>

<script setup>
import { computed, nextTick, onMounted, ref, watch } from 'vue'
import SignSheet from './SignSheet.vue'

const LOGO = 'https://goteborg-kraftsportklubb.web.app/img/logo-min.png'

const ANGLES = { n: 0, ne: 45, e: 90, se: 135, s: 180, sw: 225, w: 270, nw: 315 }
const ALIGN = { left: 'justify-start', center: 'justify-center', right: 'justify-end' }

// The directions as a map: from ahead to back, each from left to right.
const LEVELS = [
  { left: 'nw', center: 'n', right: 'ne', centerFirst: true },
  { left: 'w', right: 'e' },
  { left: 'sw', center: 's', right: 'se', centerFirst: false },
]

const props = defineProps({
  // [{ label, direction }], where direction is one of ANGLES' keys, or empty to leave the place out.
  items: { type: Array, default: () => [] },
  kicker: { type: String, default: '' },
  preview: { type: Boolean, default: false },
})

const box = ref()
const list = ref()
// Whether places to the left and right share a row.
const split = ref(false)

const rows = computed(() => {
  const cell = (direction, side) => {
    const places = props.items.filter((item) => item.direction === direction).map((item) => item.label)
    return places.length ? { direction, side, places } : null
  }
  const result = []
  for (const level of LEVELS) {
    const center = level.center && cell(level.center, 'center')
    const sides = [cell(level.left, 'left'), cell(level.right, 'right')].filter(Boolean)
    const sideRows = split.value && sides.length === 2 ? [sides] : sides.map((side) => [side])
    if (center && level.centerFirst) {
      result.push([center])
    }
    result.push(...sideRows)
    if (center && !level.centerFirst) {
      result.push([center])
    }
  }
  return result
})

const canSplit = computed(() =>
  LEVELS.some((level) => [level.left, level.right].every((d) => props.items.some((item) => item.direction === d))),
)

// The largest font size at which every row fits the sheet, in the layout (left and right in one row or not) that
// gives the largest text.
async function largest() {
  await nextTick()
  const el = list.value
  const room = (cell) => {
    const style = getComputedStyle(cell)
    return cell.clientWidth - parseFloat(style.paddingLeft) - parseFloat(style.paddingRight)
  }
  const fits = () =>
    el.offsetHeight <= box.value.clientHeight &&
    [...el.querySelectorAll('[data-fit]')].every((content) => content.offsetWidth <= room(content.parentElement))
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
  return low
}

async function measure() {
  await document.fonts?.ready
  if (!list.value || !box.value) {
    return
  }
  split.value = false
  const stacked = await largest()
  let best = { split: false, size: stacked }
  if (canSplit.value) {
    split.value = true
    const side = await largest()
    if (side > stacked) {
      best = { split: true, size: side }
    }
  }
  split.value = best.split
  await nextTick()
  list.value.style.fontSize = `${best.size}px`
}

// One fitting at a time, as each changes the layout while it measures.
let fitting = Promise.resolve()
function fit() {
  fitting = fitting.then(measure)
  return fitting
}

onMounted(fit)
watch(() => props.items.map((item) => `${item.label}:${item.direction}`).join(), fit)

defineExpose({ fit })
</script>
