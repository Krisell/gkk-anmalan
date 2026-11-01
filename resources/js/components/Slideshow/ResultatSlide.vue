<template>
  <div class="relative overflow-hidden bg-gkk text-white">
    <!-- Soft light from above and a warm glow from below, as on the DM and GM slides. -->
    <div
      class="absolute inset-0 bg-[radial-gradient(ellipse_at_top,rgba(255,255,255,0.14),transparent_60%),radial-gradient(ellipse_at_bottom_right,rgba(246,201,69,0.16),transparent_55%)]"
    ></div>

    <div class="relative flex h-full flex-col gap-[3.5vh] px-[4vw] pt-[4.5vh] pb-[4vh]">
      <header class="flex shrink-0 items-end justify-between gap-[3vw]">
        <div class="min-w-0">
          <div
            class="inline-flex items-center gap-[0.6vw] rounded-full bg-white/10 px-[1.1vw] py-[0.7vh] text-[min(1.1vw,2vh)] font-semibold uppercase tracking-[0.18em] text-white/80 ring-1 ring-white/15"
          >
            <span class="h-[0.8vh] w-[0.8vh] rounded-full bg-amber-300"></span>
            Senaste 30 dagarna
          </div>
          <h1 class="mt-[1.6vh] text-[min(4.4vw,7.5vh)] font-extrabold leading-none tracking-tight">
            Tävlingsresultat
          </h1>
        </div>

        <div class="flex shrink-0 items-center gap-[1vw] text-[min(1.4vw,2.5vh)]">
          <div class="rounded-[1.2vh] bg-white/10 px-[1.2vw] py-[1vh] text-center ring-1 ring-white/10">
            <div class="text-[2em] font-bold leading-none tabular-nums">{{ competitions }}</div>
            <div class="mt-[0.5vh] text-white/65">{{ competitions == 1 ? 'tävling' : 'tävlingar' }}</div>
          </div>
          <div class="rounded-[1.2vh] bg-white/10 px-[1.2vw] py-[1vh] text-center ring-1 ring-white/10">
            <div class="text-[2em] font-bold leading-none tabular-nums">{{ lifters }}</div>
            <div class="mt-[0.5vh] text-white/65">lyftare</div>
          </div>
          <div
            v-if="recordCount"
            class="slideshow-shine relative overflow-hidden rounded-[1.2vh] bg-amber-300 px-[1.2vw] py-[1vh] text-center text-gkk shadow-lg shadow-amber-300/20"
          >
            <div class="flex items-center justify-center gap-[0.3em] text-[2em] font-bold leading-none tabular-nums">
              <Crown class="h-[0.8em] w-[0.8em]" stroke-width="2.5" />
              {{ recordCount }}
            </div>
            <div class="mt-[0.5vh] font-semibold">klubbrekord</div>
          </div>
        </div>
      </header>

      <!-- One card per result; the cards grow to fill the slide, and the last row is centred. With more than three rows,
           the cards keep the size of three rows and scroll slowly (see results.js), faded at the edges. -->
      <div
        ref="viewport"
        class="relative min-h-0 flex-1"
        :class="{ 'overflow-hidden': layout.scrolls, 'slideshow-results-fade': layout.scrolls }"
        :style="{ '--gap': 'min(1.4vw, 2.4vh)', containerType: 'size' }"
      >
        <div
          ref="list"
          class="flex flex-wrap justify-center gap-[var(--gap)]"
          :class="layout.scrolls ? 'py-[var(--gap)]' : 'h-full content-stretch'"
        >
          <article
            v-for="result in results"
            :key="result.id"
            class="relative flex min-w-0 flex-col overflow-hidden rounded-[1.6vh] ring-1"
            :class="
              result.records.length
                ? 'bg-linear-to-br from-white/[0.14] to-amber-300/[0.08] ring-amber-300/60 shadow-[0_0_4vh_rgba(246,201,69,0.18)]'
                : 'bg-white/[0.08] ring-white/10'
            "
            :style="cardStyle"
          >
            <div
              v-if="result.records.length"
              class="slideshow-shine absolute top-0 right-0 flex items-center gap-[0.35em] overflow-hidden rounded-bl-[1.2vh] bg-amber-300 px-[0.9em] py-[0.35em] text-[0.62em] font-extrabold uppercase tracking-[0.14em] text-gkk"
            >
              <Crown class="h-[1.1em] w-[1.1em]" stroke-width="2.75" />
              Klubbrekord
            </div>

            <div class="flex min-h-0 flex-1 flex-col justify-between gap-[0.6em] p-[0.9em]">
              <div class="min-w-0">
                <div class="flex items-center gap-[0.5em] pr-[7em] text-[0.62em] font-medium text-white/60">
                  <span class="whitespace-nowrap tabular-nums">{{ formatDate(result.date) }}</span>
                  <span class="h-[0.3em] w-[0.3em] shrink-0 rounded-full bg-white/30"></span>
                  <span class="truncate">{{ competitionName(result.competition) }}</span>
                </div>
                <h2 class="mt-[0.25em] truncate text-[1.45em] font-bold leading-tight">{{ result.name }}</h2>
                <!-- One line: whatever doesn't fit is cut, never pushing the lifts out of the card. -->
                <div
                  class="mt-[0.35em] flex flex-nowrap items-center gap-[0.4em] overflow-hidden text-[0.62em] font-semibold whitespace-nowrap"
                >
                  <span class="shrink-0 rounded-full bg-white/12 px-[0.7em] py-[0.2em]">{{ classLabel(result) }}</span>
                  <span
                    v-if="result.rank"
                    class="flex shrink-0 items-center gap-[0.3em] rounded-full px-[0.7em] py-[0.2em]"
                    :class="medalClass(result.rank)"
                  >
                    <Medal v-if="result.rank <= 3" class="h-[1.1em] w-[1.1em]" stroke-width="2.5" />
                    {{ placement(result.rank) }} plats
                  </span>
                  <span
                    v-if="result.points"
                    class="shrink-0 rounded-full bg-white/12 px-[0.7em] py-[0.2em] tabular-nums"
                  >
                    {{ formatPoints(result.points) }} GL
                  </span>
                  <span v-if="ageLabel(result)" class="rounded-full bg-white/12 px-[0.7em] py-[0.2em] text-white/75">
                    {{ ageLabel(result) }}
                  </span>
                  <span v-if="result.equipped" class="rounded-full bg-white/12 px-[0.7em] py-[0.2em] text-white/75">
                    Utrustat
                  </span>
                </div>
              </div>

              <div
                class="grid items-stretch gap-[0.4em]"
                :class="result.bench_only ? 'grid-cols-1' : 'grid-cols-[1fr_1fr_1fr_1.75fr]'"
              >
                <template v-if="!result.bench_only">
                  <div
                    v-for="lift in LIFTS"
                    :key="lift.key"
                    class="flex min-w-0 flex-col justify-center rounded-[0.45em] px-[0.4em] py-[0.35em]"
                    :class="isRecord(result, lift.key) ? 'bg-amber-300 text-gkk' : 'bg-white/[0.07]'"
                  >
                    <div
                      class="flex items-center gap-[0.25em] text-[0.55em] font-semibold uppercase tracking-wider"
                      :class="isRecord(result, lift.key) ? 'text-gkk/80' : 'text-white/55'"
                    >
                      {{ lift.label }}
                      <Crown v-if="isRecord(result, lift.key)" class="h-[1.1em] w-[1.1em]" stroke-width="2.75" />
                    </div>
                    <div class="text-[0.95em] font-bold leading-tight tabular-nums whitespace-nowrap">
                      {{ formatKg(result[lift.key]) }}
                    </div>
                  </div>
                </template>

                <div
                  class="flex min-w-0 flex-col justify-center rounded-[0.45em] px-[0.6em] py-[0.35em]"
                  :class="isRecord(result, heroLift(result)) ? 'bg-amber-300 text-gkk' : 'bg-white text-gkk'"
                >
                  <div
                    class="flex items-center gap-[0.25em] text-[0.55em] font-bold uppercase tracking-wider text-gkk/70"
                  >
                    {{ result.bench_only ? 'Bänkpress' : 'Total' }}
                    <Crown v-if="isRecord(result, heroLift(result))" class="h-[1.1em] w-[1.1em]" stroke-width="2.75" />
                  </div>
                  <div class="text-[1.45em] font-extrabold leading-none tabular-nums whitespace-nowrap">
                    {{ formatKg(result[heroLift(result)])
                    }}<span class="ml-[0.15em] text-[0.5em] font-semibold">kg</span>
                  </div>
                </div>
              </div>
            </div>
          </article>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, onBeforeUnmount, onMounted, useTemplateRef } from 'vue'
import { Crown, Medal } from 'lucide-vue-next'
import { SCROLL_PAUSE_MS, resultsLayout } from './results.js'

const { results } = defineProps({
  // GKK's results from the last 30 days, newest first (App\Services\StyrkelyftResults, in the slideshow snapshot).
  results: {
    type: Array,
    required: false,
    default: () => [],
  },
})

const LIFTS = [
  { key: 'squat', label: 'Böj' },
  { key: 'bench', label: 'Bänk' },
  { key: 'deadlift', label: 'Mark' },
]

const competitions = computed(() => new Set(results.map((result) => result.competition)).size)
const lifters = computed(() => new Set(results.map((result) => result.name)).size)
const recordCount = computed(() => results.reduce((count, result) => count + (result.records?.length ?? 0), 0))

// As many columns as fit the results nicely on a wide screen; the text scales with the size of the cards.
const layout = computed(() => resultsLayout(results.length))

const cardStyle = computed(() => {
  const { columns, visibleRows, scrolls } = layout.value
  const width = `((100cqw - ${columns - 1} * var(--gap)) / ${columns})`
  // While scrolling, the list has a gap above and below, so the first and last rows aren't cut by the fade.
  const area = scrolls ? '(100cqh - 2 * var(--gap))' : '100cqh'
  const height = `((${area} - ${visibleRows - 1} * var(--gap)) / ${visibleRows})`
  // The base text size, from the size of a card (in the card area, which is smaller while a message is shown): a card
  // holds about 10 lines of it, and its lifts about 19 characters across. Capped, so a single card isn't huge.
  return {
    width: `calc${width}`,
    height: `calc${height}`,
    fontSize: `min(calc(${width} / 19), calc(${height} / 10), 5vh)`,
  }
})

// The scroll: started from the top each time the slide comes on screen (the slideshow hides the others with
// display: none, which an IntersectionObserver sees), and stopped when it goes. A pause at the top, down to the
// bottom, a pause there, and back again, so a slide held from the live control keeps showing them all.
const viewport = useTemplateRef('viewport')
const list = useTemplateRef('list')
let scroll = null
let observer = null

function startScroll() {
  stopScroll()
  const distance = list.value.scrollHeight - viewport.value.clientHeight
  if (!layout.value.scrolls || distance <= 0) {
    return
  }
  const { scrollMs } = layout.value
  const total = SCROLL_PAUSE_MS + scrollMs + SCROLL_PAUSE_MS
  scroll = list.value.animate(
    [
      { transform: 'translateY(0)', offset: 0 },
      { transform: 'translateY(0)', offset: SCROLL_PAUSE_MS / total, easing: 'ease-in-out' },
      { transform: `translateY(${-distance}px)`, offset: (SCROLL_PAUSE_MS + scrollMs) / total },
      { transform: `translateY(${-distance}px)`, offset: 1 },
    ],
    { duration: total, iterations: Infinity, direction: 'alternate' },
  )
}

function stopScroll() {
  scroll?.cancel()
  scroll = null
}

onMounted(() => {
  observer = new IntersectionObserver(([entry]) => (entry.isIntersecting ? startScroll() : stopScroll()))
  observer.observe(viewport.value)
})

onBeforeUnmount(() => {
  observer?.disconnect()
  stopScroll()
})

function heroLift(result) {
  return result.bench_only ? 'bench' : 'total'
}

function isRecord(result, lift) {
  return result.records?.includes(lift) ?? false
}

// "Veteran (50-59 år)" → "Veteran 50–59"; a senior is not labelled, as most are.
function ageLabel(result) {
  const label = result.age_category ?? ''
  if (!label || label === 'Senior') {
    return null
  }
  const ages = label.match(/\((\d+)\s*-\s*(\d+)/)
  return ages ? `${label.replace(/\s*\(.*$/, '')} ${ages[1]}–${ages[2]}` : label.replace(/\s*\(.*$/, '')
}

function classLabel(result) {
  const weightClass = result.weight_class.endsWith('+')
    ? `+${result.weight_class.slice(0, -1)}`
    : `-${result.weight_class}`
  return `${result.gender === 'F' ? 'Damer' : 'Herrar'} ${weightClass} kg`
}

// "2026 SM Klassisk styrkelyft" → "SM Klassisk styrkelyft", "2026 - Järnvägspressen - Serie 4" → "Järnvägspressen - Serie 4"
function competitionName(name) {
  return name.replace(/^\d{4}\s*[-–]?\s*/, '') || name
}

function placement(rank) {
  return `${rank}:${[1, 2].includes(rank % 10) && ![11, 12].includes(rank % 100) ? 'a' : 'e'}`
}

function medalClass(rank) {
  return (
    { 1: 'bg-amber-300 text-gkk', 2: 'bg-slate-200 text-gkk', 3: 'bg-orange-300 text-gkk' }[rank] ??
    'bg-white/12 text-white/80'
  )
}

const kg = new Intl.NumberFormat('sv-SE', { maximumFractionDigits: 1 })
const points = new Intl.NumberFormat('sv-SE', { minimumFractionDigits: 1, maximumFractionDigits: 1 })

function formatKg(value) {
  return value ? kg.format(value) : '–'
}

function formatPoints(value) {
  return points.format(value)
}

function formatDate(date) {
  return new Date(`${date}T12:00:00`).toLocaleDateString('sv-SE', { day: 'numeric', month: 'short' })
}
</script>

<style scoped>
/* The scrolling cards fade out at the top and bottom edges. */
.slideshow-results-fade {
  mask-image: linear-gradient(to bottom, transparent, black var(--gap), black calc(100% - var(--gap)), transparent);
}

/* A slow glint over the club record badges. */
.slideshow-shine::after {
  content: '';
  position: absolute;
  inset: 0;
  background: linear-gradient(110deg, transparent 35%, rgba(255, 255, 255, 0.55) 50%, transparent 65%);
  transform: translateX(-120%);
  animation: slideshow-shine 4.5s ease-in-out infinite;
  pointer-events: none;
}

@keyframes slideshow-shine {
  0%,
  55% {
    transform: translateX(-120%);
  }
  100% {
    transform: translateX(120%);
  }
}
</style>
