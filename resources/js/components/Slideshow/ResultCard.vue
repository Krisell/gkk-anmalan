<template>
  <!-- One result on the results slide (ResultatSlide.vue). It fills its place there, which is a size container: the
       text scales with it, a card holding about 14 lines of the base size and 22 characters across. -->
  <article
    class="relative flex h-full w-full min-w-0 flex-col justify-between gap-[0.7em] overflow-hidden rounded-[1.6vh] bg-gkk p-[0.9em] ring-1"
    :class="
      result.records.length
        ? 'bg-linear-to-br from-white/[0.14] to-amber-300/[0.08] ring-amber-300/60 shadow-[0_0_4vh_rgba(246,201,69,0.18)]'
        : 'bg-linear-to-br from-white/[0.09] to-white/[0.05] ring-white/10'
    "
    style="font-size: min(100cqw / 22, 100cqh / 14, 6vh)"
  >
    <div
      v-if="result.records.length"
      class="slideshow-shine absolute top-0 right-0 flex items-center gap-[0.35em] overflow-hidden rounded-bl-[1.2vh] bg-amber-300 px-[0.9em] py-[0.35em] text-[0.62em] font-extrabold uppercase tracking-[0.14em] text-gkk"
    >
      <Crown class="h-[1.1em] w-[1.1em]" stroke-width="2.75" />
      Klubbrekord
    </div>

    <!-- The date as a calendar sheet, beside the competition. -->
    <div class="flex min-w-0 items-center gap-[0.7em]" :class="{ 'pr-[5.5em]': result.records.length }">
      <div
        class="w-[3.4em] shrink-0 overflow-hidden rounded-[0.4em] bg-white text-center text-gkk shadow-lg shadow-black/25"
      >
        <div class="relative bg-amber-300 py-[0.15em] text-[0.62em] font-extrabold uppercase tracking-[0.18em]">
          {{ month }}
        </div>
        <div class="pt-[0.05em] text-[1.9em] font-extrabold leading-none tabular-nums">{{ day }}</div>
        <div class="pb-[0.3em] text-[0.55em] font-semibold uppercase tracking-[0.14em] text-gkk/60">{{ weekday }}</div>
      </div>
      <div class="min-w-0">
        <div class="text-[0.58em] font-semibold uppercase tracking-[0.16em] text-amber-300/90">Tävling</div>
        <p class="line-clamp-2 text-[1.05em] font-bold leading-tight text-white/95">
          {{ competitionName(result.competition) }}
        </p>
      </div>
    </div>

    <div class="min-w-0">
      <h2 class="line-clamp-2 text-[2.1em] font-extrabold leading-[1.05] tracking-tight">{{ result.name }}</h2>
      <!-- One line: whatever doesn't fit is cut, never pushing the lifts out of the card. -->
      <div
        class="mt-[0.5em] flex flex-nowrap items-center gap-[0.4em] overflow-hidden text-[0.66em] font-semibold whitespace-nowrap"
      >
        <span
          v-if="result.rank"
          class="flex shrink-0 items-center gap-[0.3em] rounded-full px-[0.7em] py-[0.2em]"
          :class="medalClass(result.rank)"
        >
          <Medal v-if="result.rank <= 3" class="h-[1.1em] w-[1.1em]" stroke-width="2.5" />
          {{ placement(result.rank) }} plats
        </span>
        <span v-if="result.debut" class="shrink-0 rounded-full bg-sky-300 px-[0.7em] py-[0.2em] text-gkk">
          Tävlingsdebut
        </span>
        <span
          v-else-if="pbCount"
          class="flex shrink-0 items-center gap-[0.3em] rounded-full bg-emerald-400 px-[0.7em] py-[0.2em] text-gkk"
        >
          <TrendingUp class="h-[1.1em] w-[1.1em]" stroke-width="2.75" />
          {{ pbCount }} {{ pbCount == 1 ? 'personbästa' : 'personbästan' }}
        </span>
        <span class="shrink-0 rounded-full bg-white/12 px-[0.7em] py-[0.2em]">{{ classLabel(result) }}</span>
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

    <div class="flex flex-col gap-[0.4em]">
      <div v-if="!result.bench_only" class="grid grid-cols-3 gap-[0.4em]">
        <div
          v-for="lift in LIFTS"
          :key="lift.key"
          class="flex min-w-0 flex-col justify-center rounded-[0.45em] px-[0.55em] py-[0.35em]"
          :class="liftClass(lift.key)"
        >
          <div class="flex items-center justify-between gap-[0.25em] text-[0.55em] font-bold uppercase tracking-wider">
            <span :class="isRecord(lift.key) ? 'text-gkk/75' : 'text-white/60'">{{ lift.label }}</span>
            <Crown v-if="isRecord(lift.key)" class="h-[1.2em] w-[1.2em]" stroke-width="2.75" />
            <span v-else-if="isPb(lift.key)" class="rounded-full bg-emerald-400 px-[0.5em] text-gkk">PB</span>
          </div>
          <div class="text-[1.25em] font-extrabold leading-tight tabular-nums whitespace-nowrap">
            {{ formatKg(result[lift.key]) }}
          </div>
        </div>
      </div>

      <!-- The total (or the bench press, at a bench press competition) on its own row. -->
      <div
        class="flex min-w-0 items-center justify-between gap-[0.6em] rounded-[0.5em] px-[0.7em] py-[0.3em]"
        :class="isRecord(heroLift) ? 'bg-amber-300 text-gkk' : 'bg-white text-gkk'"
      >
        <div class="flex items-center gap-[0.4em] text-[0.75em] font-extrabold uppercase tracking-[0.14em] text-gkk/70">
          {{ result.bench_only ? 'Bänkpress' : 'Total' }}
          <Crown v-if="isRecord(heroLift)" class="h-[1.2em] w-[1.2em] text-gkk" stroke-width="2.75" />
          <span
            v-if="isPb(heroLift)"
            class="rounded-full bg-emerald-500 px-[0.6em] py-[0.05em] text-[0.85em] tracking-wider text-white"
            >PB</span
          >
        </div>
        <div class="text-[2.3em] font-extrabold leading-none tabular-nums whitespace-nowrap">
          {{ formatKg(result[heroLift]) }}<span class="ml-[0.15em] text-[0.45em] font-bold">kg</span>
        </div>
      </div>
    </div>
  </article>
</template>

<script setup>
import { computed } from 'vue'
import { Crown, Medal, TrendingUp } from 'lucide-vue-next'

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

// A personal best (see StyrkelyftResults::withPersonalBests); not shown at a debut, where every lift is one.
function isPb(lift) {
  return !result.debut && (result.personal_bests?.includes(lift) ?? false)
}

const pbCount = computed(() => (result.debut ? 0 : (result.personal_bests?.length ?? 0)))

function liftClass(lift) {
  if (isRecord(lift)) {
    return 'bg-amber-300 text-gkk'
  }
  return isPb(lift) ? 'bg-emerald-400/15 ring-1 ring-emerald-400/70' : 'bg-white/[0.07]'
}

// The calendar sheet: "NOV", "14", "fre".
const date = computed(() => new Date(`${result.date}T12:00:00`))
const month = computed(() => date.value.toLocaleDateString('sv-SE', { month: 'short' }).replace('.', ''))
const day = computed(() => date.value.getDate())
const weekday = computed(() => date.value.toLocaleDateString('sv-SE', { weekday: 'short' }).replace('.', ''))

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
