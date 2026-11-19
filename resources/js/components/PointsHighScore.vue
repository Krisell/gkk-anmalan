<template>
  <div class="mx-auto max-w-6xl px-4 pb-16 sm:px-6 lg:px-8">
    <header class="mt-10 flex flex-col gap-6 sm:mt-16 sm:flex-row sm:items-end sm:justify-between">
      <div>
        <div
          class="inline-flex items-center gap-2 rounded-full bg-gkk/5 px-3 py-1 text-xs font-semibold uppercase tracking-wider text-gkk ring-1 ring-inset ring-gkk/10"
        >
          <Trophy class="size-3.5" stroke-width="2.5" />
          Säsongen {{ currentYear }}
        </div>
        <h1 class="mt-3 text-4xl font-extrabold tracking-tight text-gray-900 sm:text-5xl">Poängtoppen</h1>
        <p class="mt-2 max-w-xl text-gray-500">
          Klubbens bästa resultat i år, efter GL-poäng. Varje lyftares bästa tävling räknas.
        </p>
      </div>

      <!-- Classic powerlifting or bench press. -->
      <div
        class="relative inline-grid shrink-0 auto-cols-fr grid-flow-col self-start rounded-full bg-gray-100 p-1 ring-1 ring-inset ring-gray-900/5 sm:self-auto"
      >
        <span
          class="absolute inset-y-1 left-1 w-[calc(50%-0.25rem)] rounded-full bg-gkk shadow-sm transition-transform duration-300 ease-out"
          :style="{ transform: event === 'kbp' ? 'translateX(100%)' : 'translateX(0)' }"
        ></span>
        <button
          v-for="option in EVENTS"
          :key="option.value"
          type="button"
          class="relative z-10 flex items-center justify-center gap-2 rounded-full px-5 py-2 text-sm font-semibold transition-colors duration-300"
          :class="event === option.value ? 'text-white' : 'text-gray-600 hover:text-gkk'"
          @click="event = option.value"
        >
          <!-- Seen from the side, the bar end-on: a lifter starting a deadlift, or pressing on the bench. -->
          <svg
            class="size-5 shrink-0"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
            stroke-linecap="round"
            stroke-linejoin="round"
            aria-hidden="true"
          >
            <template v-if="option.value === 'ksl'">
              <circle cx="7" cy="17" r="4" />
              <circle cx="6.5" cy="4.5" r="2" />
              <path d="M8.5 7.5 16 11l-4.5 3.5L13.5 21M8.5 7.5 7.5 17M2 21h20" />
            </template>
            <template v-else>
              <circle cx="9" cy="5" r="3.5" />
              <circle cx="4.5" cy="13" r="2" />
              <path d="M7 14.5h8l4-1 1.5 7.5M9 14.5V8.5M2 17h17M4 17v4M17 17v4" />
            </template>
          </svg>
          {{ option.label }}
        </button>
      </div>
    </header>

    <div v-if="loading" class="mt-10 grid gap-6 lg:grid-cols-2">
      <div v-for="n in 2" :key="n" class="h-96 animate-pulse rounded-2xl bg-white ring-1 ring-gray-900/5"></div>
    </div>

    <div v-else-if="mustSign" class="mt-10 rounded-2xl bg-white p-10 text-center ring-1 ring-gray-900/5">
      <p class="text-gray-700">Du behöver signera klubbens avtal innan du kan se poängtoppen.</p>
      <a
        href="/sign-agreements"
        class="mt-4 inline-block rounded-full bg-gkk px-5 py-2 text-sm font-semibold text-white hover:bg-gkk/90"
      >
        Till avtalen
      </a>
    </div>

    <div v-else-if="error" class="mt-10 rounded-2xl bg-white p-10 text-center ring-1 ring-gray-900/5">
      <p class="text-red-600">{{ error }}</p>
      <button
        type="button"
        class="mt-4 rounded-full bg-gkk px-5 py-2 text-sm font-semibold text-white hover:bg-gkk/90"
        @click="fetchRanking"
      >
        Försök igen
      </button>
    </div>

    <div v-else class="mt-10 grid items-start gap-6 lg:grid-cols-2">
      <section
        v-for="gender in GENDERS"
        :key="gender.value"
        class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-gray-900/5"
      >
        <div class="flex items-center justify-between bg-gkk px-5 py-4 text-white">
          <h2 class="text-lg font-semibold">{{ gender.label }}</h2>
          <span class="text-sm text-white/60">{{ list(gender.value).length }} lyftare</span>
        </div>

        <ol v-if="list(gender.value).length" class="divide-y divide-gray-100">
          <li
            v-for="(item, index) in list(gender.value)"
            :key="item.id ?? `${item.name}-${index}`"
            class="flex items-center gap-4 px-5 py-3.5"
          >
            <span
              class="flex size-8 shrink-0 items-center justify-center rounded-full bg-gray-100 text-sm font-bold text-gray-500 tabular-nums"
            >
              {{ index + 1 }}
            </span>

            <div class="min-w-0 flex-1">
              <div class="truncate font-semibold text-gray-900">{{ item.name }}</div>
              <div class="mt-0.5 text-sm text-gray-500 tabular-nums">
                {{ resultLine(item) }}
              </div>
              <!-- The points against the leader's. -->
              <div class="mt-2 h-1 overflow-hidden rounded-full bg-gray-100">
                <div class="h-full rounded-full bg-gkk/35" :style="{ width: `${barWidth(gender.value, item)}%` }"></div>
              </div>
            </div>

            <div class="shrink-0 text-right">
              <div class="text-xl font-extrabold tracking-tight text-gray-900 tabular-nums">
                {{ formatPoints(points(item)) }}
              </div>
              <div class="text-xs font-medium uppercase tracking-wider text-gray-400">GL</div>
            </div>
          </li>
        </ol>

        <p v-else class="px-5 py-12 text-center text-gray-500">Inga resultat ännu i år.</p>
      </section>
    </div>
  </div>
</template>

<script setup>
import { onMounted, ref } from 'vue'
import { Trophy } from 'lucide-vue-next'

const EVENTS = [
  { value: 'ksl', label: 'Styrkelyft' },
  { value: 'kbp', label: 'Bänkpress' },
]
const GENDERS = [
  { value: 'men', label: 'Herrar' },
  { value: 'women', label: 'Damer' },
]

const currentYear = new Date().getFullYear()
const event = ref('ksl')
const loading = ref(true)
const error = ref(null)
// The ranking is behind the agreements (EnsureAgreementsAreSignedMiddleware): it redirects to the signing page.
const mustSign = ref(false)

class AgreementsRequired extends Error {}
const ranking = ref({ men: { ksl: [], kbp: [] }, women: { ksl: [], kbp: [] } })

async function fetchJson(competitionType) {
  const response = await fetch(`/api/ranking/points?year=${currentYear}&competition_type=${competitionType}`, {
    headers: { Accept: 'application/json' },
  })
  if (response.redirected && new URL(response.url).pathname.startsWith('/sign-agreements')) {
    throw new AgreementsRequired()
  }
  if (!response.ok || !response.headers.get('content-type')?.includes('application/json')) {
    throw new Error('Kunde inte hämta poängtoppen.')
  }
  return (await response.json()).data
}

// Each lifter once, with their best points; a result without points (no valid lift) isn't listed.
function bestPerLifter(results, key) {
  const best = new Map()
  for (const result of results.filter((result) => result.points?.[key] > 0)) {
    const current = best.get(result.name)
    if (!current || result.points[key] > current.points[key]) {
      best.set(result.name, result)
    }
  }
  return [...best.values()].sort((a, b) => b.points[key] - a.points[key])
}

async function fetchRanking() {
  loading.value = true
  error.value = null
  mustSign.value = false

  try {
    const [classic, bench] = await Promise.all([fetchJson(1), fetchJson(2)])
    ranking.value = {
      men: {
        ksl: bestPerLifter(classic.men_ksl, 'ipfgl'),
        // The bench press in a powerlifting competition counts for the bench press too.
        kbp: bestPerLifter([...bench.men_ksl, ...bench.men_kbp], 'ipfglbp'),
      },
      women: {
        ksl: bestPerLifter(classic.women_ksl, 'ipfgl'),
        kbp: bestPerLifter([...bench.women_ksl, ...bench.women_kbp], 'ipfglbp'),
      },
    }
  } catch (e) {
    if (e instanceof AgreementsRequired) {
      mustSign.value = true
      return
    }
    console.error('Error fetching ranking:', e)
    error.value = e.message || 'Ett fel uppstod vid hämtning av data'
  } finally {
    loading.value = false
  }
}

onMounted(fetchRanking)

function list(gender) {
  return ranking.value[gender][event.value]
}

function points(item) {
  return item.points[event.value === 'kbp' ? 'ipfglbp' : 'ipfgl']
}

function barWidth(gender, item) {
  const leader = points(list(gender)[0])
  return leader ? Math.max((points(item) / leader) * 100, 2) : 0
}

const number = new Intl.NumberFormat('sv-SE', { maximumFractionDigits: 2 })
const twoDecimals = new Intl.NumberFormat('sv-SE', { minimumFractionDigits: 2, maximumFractionDigits: 2 })

function formatPoints(value) {
  return twoDecimals.format(Number(value))
}

// "815 kg · kv: 108,6 kg" (kv: kroppsvikt, the body weight)
function resultLine(item) {
  const lifted = event.value === 'kbp' ? item.benchpress : item.total
  const parts = []
  if (lifted) {
    parts.push(`${number.format(lifted)} kg`)
  }
  if (item.body_weight) {
    parts.push(`kv: ${number.format(item.body_weight)} kg`)
  }
  return parts.join(' · ')
}
</script>
