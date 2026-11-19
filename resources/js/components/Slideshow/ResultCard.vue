<template>
  <!-- One result on the results slide (ResultatSlide.vue). It fills its place there, which is a size container: the
       text scales with it, a card holding about 10 lines of the base size and its lifts about 19 characters across. -->
  <article
    class="relative flex h-full w-full min-w-0 flex-col overflow-hidden rounded-[1.6vh] ring-1"
    :class="
      result.records.length
        ? 'bg-gkk bg-linear-to-br from-white/[0.14] to-amber-300/[0.08] ring-amber-300/60 shadow-[0_0_4vh_rgba(246,201,69,0.18)]'
        : 'bg-gkk bg-linear-to-br from-white/[0.08] to-white/[0.08] ring-white/10'
    "
    style="font-size: min(100cqw / 19, 100cqh / 10, 7vh)"
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
          <span v-if="result.points" class="shrink-0 rounded-full bg-white/12 px-[0.7em] py-[0.2em] tabular-nums">
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
            :class="isRecord(lift.key) ? 'bg-amber-300 text-gkk' : 'bg-white/[0.07]'"
          >
            <div
              class="flex items-center gap-[0.25em] text-[0.55em] font-semibold uppercase tracking-wider"
              :class="isRecord(lift.key) ? 'text-gkk/80' : 'text-white/55'"
            >
              {{ lift.label }}
              <Crown v-if="isRecord(lift.key)" class="h-[1.1em] w-[1.1em]" stroke-width="2.75" />
            </div>
            <div class="text-[0.95em] font-bold leading-tight tabular-nums whitespace-nowrap">
              {{ formatKg(result[lift.key]) }}
            </div>
          </div>
        </template>

        <div
          class="flex min-w-0 flex-col justify-center rounded-[0.45em] px-[0.6em] py-[0.35em]"
          :class="isRecord(heroLift) ? 'bg-amber-300 text-gkk' : 'bg-white text-gkk'"
        >
          <div class="flex items-center gap-[0.25em] text-[0.55em] font-bold uppercase tracking-wider text-gkk/70">
            {{ result.bench_only ? 'Bänkpress' : 'Total' }}
            <Crown v-if="isRecord(heroLift)" class="h-[1.1em] w-[1.1em]" stroke-width="2.75" />
          </div>
          <div class="text-[1.45em] font-extrabold leading-none tabular-nums whitespace-nowrap">
            {{ formatKg(result[heroLift]) }}<span class="ml-[0.15em] text-[0.5em] font-semibold">kg</span>
          </div>
        </div>
      </div>
    </div>
  </article>
</template>

<script setup>
import { computed } from 'vue'
import { Crown, Medal } from 'lucide-vue-next'

const { result } = defineProps({
  result: {
    type: Object,
    required: true,
  },
})

const LIFTS = [
  { key: 'squat', label: 'Böj' },
  { key: 'bench', label: 'Bänk' },
  { key: 'deadlift', label: 'Mark' },
]

const heroLift = computed(() => (result.bench_only ? 'bench' : 'total'))

function isRecord(lift) {
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
