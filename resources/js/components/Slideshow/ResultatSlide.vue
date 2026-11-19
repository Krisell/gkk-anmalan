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
            v-if="pbCount"
            class="rounded-[1.2vh] bg-emerald-400 px-[1.2vw] py-[1vh] text-center text-gkk shadow-lg shadow-emerald-400/20"
          >
            <div class="flex items-center justify-center gap-[0.3em] text-[2em] font-bold leading-none tabular-nums">
              <TrendingUp class="h-[0.8em] w-[0.8em]" stroke-width="2.5" />
              {{ pbCount }}
            </div>
            <div class="mt-[0.5vh] font-semibold">personbästan</div>
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

      <!-- Two results at a time, side by side, turning to the next two like the page of a book: the right-hand card
           (the leaf) swings over the spine to the left, its back showing the next left-hand card, and uncovers the
           next right-hand card beneath it. -->
      <div
        class="slideshow-book relative grid min-h-0 flex-1 grid-cols-2 gap-[var(--gap)]"
        :style="{ '--gap': 'min(2vw, 3.6vh)', '--flip': `${FLIP_MS}ms` }"
      >
        <div ref="viewport" class="relative min-w-0 [container-type:size]">
          <ResultCard v-if="current[0]" :key="current[0].id" :result="current[0]" />
          <div v-if="flipping" class="slideshow-shade-under pointer-events-none absolute inset-0 rounded-[1.6vh]"></div>
        </div>
        <div class="relative min-w-0 [container-type:size]">
          <ResultCard v-if="under[1]" :key="under[1].id" :result="under[1]" />
          <div
            v-if="flipping"
            class="slideshow-shade-revealed pointer-events-none absolute inset-0 rounded-[1.6vh]"
          ></div>
        </div>

        <div v-if="flipping" class="slideshow-leaf absolute inset-y-0 right-0 w-[calc(50%-var(--gap)/2)]">
          <div class="slideshow-face absolute inset-0 [container-type:size]">
            <ResultCard v-if="current[1]" :result="current[1]" />
            <div class="slideshow-shade-front absolute inset-0 rounded-[1.6vh]"></div>
          </div>
          <div class="slideshow-face slideshow-face-back absolute inset-0 [container-type:size]">
            <ResultCard v-if="next[0]" :result="next[0]" />
            <div class="slideshow-shade-back absolute inset-0 rounded-[1.6vh]"></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, onBeforeUnmount, onMounted, ref, useTemplateRef } from 'vue'
import { Crown, TrendingUp } from 'lucide-vue-next'
import ResultCard from './ResultCard.vue'
import { FLIP_MS, pageDuration, resultPages } from './results.js'

const { results } = defineProps({
  // GKK's results from the last 30 days, newest first (App\Services\StyrkelyftResults, in the slideshow snapshot).
  results: {
    type: Array,
    required: false,
    default: () => [],
  },
})

const competitions = computed(() => new Set(results.map((result) => result.competition)).size)
const lifters = computed(() => new Set(results.map((result) => result.name)).size)
// Lifts heavier than the lifter's earlier best; a debut's lifts aren't counted.
const pbCount = computed(() =>
  results.reduce((count, result) => count + (result.debut ? 0 : (result.personal_bests?.length ?? 0)), 0),
)
const recordCount = computed(() => results.reduce((count, result) => count + (result.records?.length ?? 0), 0))

const pages = computed(() => resultPages(results))
const page = ref(0)
const flipping = ref(false)
const nextPage = computed(() => (page.value + 1) % Math.max(pages.value.length, 1))
const current = computed(() => pages.value[page.value] ?? [])
const next = computed(() => pages.value[nextPage.value] ?? [])
// Beneath the leaf on the right: the next page's card while turning, else this page's.
const under = computed(() => (flipping.value ? next.value : current.value))

// The pages: from the first each time the slide comes on screen (the slideshow hides the others with display: none,
// which an IntersectionObserver sees), each shown for its time (see results.js), and stopped when it goes. After the last page it turns
// back to the first, so a slide held from the live control keeps showing them all.
const viewport = useTemplateRef('viewport')
let pageTimer = null
let flipTimer = null
let observer = null

function start() {
  stop()
  page.value = 0
  if (pages.value.length > 1) {
    schedule(0)
  }
}

// The page is on screen from the moment it starts turning in, so its time counts from there.
function schedule(index) {
  pageTimer = setTimeout(turn, pageDuration(pages.value[index]))
}

function turn() {
  flipping.value = true
  schedule(nextPage.value)
  flipTimer = setTimeout(() => {
    page.value = nextPage.value
    flipping.value = false
  }, FLIP_MS)
}

function stop() {
  clearTimeout(pageTimer)
  clearTimeout(flipTimer)
  pageTimer = flipTimer = null
  flipping.value = false
}

onMounted(() => {
  observer = new IntersectionObserver(([entry]) => (entry.isIntersecting ? start() : stop()))
  observer.observe(viewport.value)
})

onBeforeUnmount(() => {
  observer?.disconnect()
  stop()
})
</script>

<style scoped>
/* The book: the leaf turns over the spine, in the middle of the gap between the two cards. */
.slideshow-book {
  perspective: 260vw;
}

.slideshow-leaf {
  transform-style: preserve-3d;
  transform-origin: calc(var(--gap) / -2) 50%;
  animation: slideshow-turn var(--flip) cubic-bezier(0.45, 0.05, 0.3, 1) forwards;
}

.slideshow-face {
  backface-visibility: hidden;
}

.slideshow-face-back {
  transform: rotateY(180deg);
}

@keyframes slideshow-turn {
  from {
    transform: rotateY(0deg);
  }
  to {
    transform: rotateY(-180deg);
  }
}

/* Light and shadow as the paper turns: its front darkens as it lifts, its back brightens as it lands, the card it
   uncovers comes out of its shadow and the card it lands on goes into it. */
.slideshow-shade-front,
.slideshow-shade-back,
.slideshow-shade-under,
.slideshow-shade-revealed {
  animation-duration: var(--flip);
  animation-timing-function: cubic-bezier(0.45, 0.05, 0.3, 1);
  animation-fill-mode: forwards;
}

.slideshow-shade-front {
  background: linear-gradient(to right, rgba(0, 0, 0, 0.45), rgba(0, 0, 0, 0.1));
  animation-name: slideshow-shade-in;
}

.slideshow-shade-back {
  background: linear-gradient(to left, rgba(0, 0, 0, 0.45), rgba(0, 0, 0, 0.1));
  animation-name: slideshow-shade-out;
}

.slideshow-shade-revealed {
  background: linear-gradient(to right, rgba(0, 0, 0, 0.55), rgba(0, 0, 0, 0.15));
  animation-name: slideshow-shade-out;
}

.slideshow-shade-under {
  background: linear-gradient(to left, rgba(0, 0, 0, 0.55), rgba(0, 0, 0, 0.15));
  animation-name: slideshow-shade-in-late;
}

@keyframes slideshow-shade-in {
  from {
    opacity: 0;
  }
  to {
    opacity: 1;
  }
}

@keyframes slideshow-shade-out {
  from {
    opacity: 1;
  }
  to {
    opacity: 0;
  }
}

@keyframes slideshow-shade-in-late {
  0%,
  50% {
    opacity: 0;
  }
  85% {
    opacity: 1;
  }
  100% {
    opacity: 0;
  }
}

/* A slow glint over the club record badge. */
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
