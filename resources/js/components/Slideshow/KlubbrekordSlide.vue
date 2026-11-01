<template>
  <div class="relative overflow-hidden bg-gkk text-white">
    <!-- Soft light from above and a warm glow from the left, as on the other slides of the club. -->
    <div
      class="absolute inset-0 bg-[radial-gradient(ellipse_at_top_right,rgba(255,255,255,0.14),transparent_60%),radial-gradient(ellipse_at_bottom_left,rgba(246,201,69,0.18),transparent_55%)]"
    ></div>

    <div class="relative flex h-full flex-col gap-[3.5vh] px-[4vw] pt-[4.5vh] pb-[4vh]">
      <header class="flex shrink-0 items-end justify-between gap-[3vw]">
        <div class="min-w-0">
          <div
            class="inline-flex items-center gap-[0.6vw] rounded-full bg-white/10 px-[1.1vw] py-[0.7vh] text-[min(1.1vw,2vh)] font-semibold uppercase tracking-[0.18em] text-white/80 ring-1 ring-white/15"
          >
            <Crown class="h-[1.1em] w-[1.1em] text-amber-300" stroke-width="2.5" />
            Klubbrekord
          </div>
          <h1 class="mt-[1.6vh] text-[min(4.4vw,7.5vh)] font-extrabold leading-none tracking-tight">{{ title }}</h1>
        </div>

        <div
          v-if="newCount"
          class="shrink-0 rounded-[1.2vh] bg-amber-300 px-[1.4vw] py-[1vh] text-center text-[min(1.4vw,2.5vh)] text-gkk shadow-lg shadow-amber-300/20"
        >
          <div class="flex items-center justify-center gap-[0.3em] text-[2em] font-bold leading-none tabular-nums">
            <Crown class="h-[0.8em] w-[0.8em]" stroke-width="2.5" />
            {{ newCount }}
          </div>
          <div class="mt-[0.5vh] font-semibold">slagna senaste året</div>
        </div>
      </header>

      <div v-if="latest" class="grid min-h-0 flex-1 grid-cols-[minmax(0,1fr)_minmax(0,2.1fr)] gap-[min(1.4vw,2.4vh)]">
        <!-- The newest record, large. -->
        <article
          class="relative flex min-h-0 flex-col overflow-hidden rounded-[2vh] bg-amber-300 p-[min(2vw,3.4vh)] text-gkk shadow-2xl shadow-black/25"
        >
          <div class="relative flex items-center gap-[0.6vw]">
            <span
              class="rounded-full bg-gkk px-[0.9vw] py-[0.5vh] text-[min(1vw,1.8vh)] font-bold uppercase tracking-[0.16em] text-amber-300"
            >
              Senaste rekordet
            </span>
          </div>
          <p class="relative mt-[2.5vh] text-[min(2.4vw,4.2vh)] font-extrabold uppercase leading-none tracking-wide">
            {{ latest.event }}
          </p>
          <p class="relative mt-[1vh] text-[min(1.4vw,2.4vh)] font-semibold text-gkk/75">{{ classLabel(latest) }}</p>

          <div class="relative mt-auto">
            <p class="line-clamp-2 text-[min(2.8vw,5vh)] font-bold leading-tight">{{ athleteName(latest) }}</p>
            <p class="mt-[1vh] flex items-baseline gap-[0.4vw] leading-none tabular-nums">
              <span class="text-[min(8vw,14vh)] font-extrabold tracking-tight">{{ formatResult(latest.result) }}</span>
              <span class="text-[min(2.4vw,4vh)] font-bold">kg</span>
            </p>
            <p class="mt-[1.5vh] text-[min(1.3vw,2.2vh)] font-semibold text-gkk/70">
              {{ formatDate(latest.competition_date) }}
            </p>
          </div>
        </article>

        <!-- The rest, newest first. -->
        <!-- Up to nine in three rows; with fewer records, the cards keep their size and the bottom stays free. -->
        <div class="grid min-h-0 grid-cols-3 grid-rows-3 gap-[min(1.4vw,2.4vh)]">
          <article
            v-for="record in rest"
            :key="record.id"
            class="relative flex min-h-0 min-w-0 flex-col justify-between overflow-hidden rounded-[1.6vh] px-[min(1.2vw,2.1vh)] py-[min(1vw,1.8vh)] ring-1"
            :class="isNew(record) ? 'bg-white/[0.12] ring-amber-300/70' : 'bg-white/[0.07] ring-white/10'"
          >
            <div class="flex items-center justify-between gap-[0.6vw]">
              <span
                class="flex min-w-0 items-center gap-[0.35vw] truncate text-[min(1vw,1.75vh)] font-bold uppercase tracking-[0.14em]"
                :class="isNew(record) ? 'text-amber-300' : 'text-white/70'"
              >
                <Crown v-if="isNew(record)" class="h-[1.1em] w-[1.1em] shrink-0" stroke-width="2.5" />
                {{ record.event }}
              </span>
              <span
                class="shrink-0 rounded-full bg-white/10 px-[0.6vw] py-[0.25vh] text-[min(0.95vw,1.65vh)] font-semibold whitespace-nowrap text-white/85"
              >
                {{ classLabel(record) }}
              </span>
            </div>
            <p class="truncate text-[min(1.6vw,2.8vh)] font-bold leading-tight">{{ athleteName(record) }}</p>
            <div class="flex items-end justify-between gap-[0.6vw]">
              <p class="flex items-baseline gap-[0.25vw] leading-none tabular-nums">
                <span class="text-[min(3vw,5.2vh)] font-extrabold tracking-tight">{{
                  formatResult(record.result)
                }}</span>
                <span class="text-[min(1.2vw,2vh)] font-semibold text-white/70">kg</span>
              </p>
              <p class="pb-[0.3vh] text-[min(1vw,1.75vh)] font-medium whitespace-nowrap text-white/55">
                {{ formatDate(record.competition_date) }}
              </p>
            </div>
          </article>
        </div>
      </div>

      <div v-else class="flex flex-1 items-center justify-center text-[min(3vw,5vh)] font-light text-white/60">
        Inga klubbrekord registrerade ännu.
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { Crown } from 'lucide-vue-next'

const props = defineProps({
  title: {
    type: String,
    required: false,
    default: 'Senaste klubbrekorden',
  },
  // Part of the slideshow snapshot (see Slideshow.vue): the latest records, newest first.
  records: {
    type: Array,
    required: false,
    default: () => [],
  },
})

const latest = computed(() => props.records[0] ?? null)
const rest = computed(() => props.records.slice(1))
const newCount = computed(() => props.records.filter(isNew).length)

// "Herrar -83 kg", "Damer +84 kg", as on the results slide.
function classLabel(record) {
  const weightClass = String(record.weight_class ?? '')
  const limit = weightClass.endsWith('+') ? `+${weightClass.slice(0, -1)}` : `-${weightClass}`
  return `${record.gender === 'F' ? 'Damer' : 'Herrar'} ${limit} kg`
}

function athleteName(record) {
  if (record.name) {
    return record.name
  }
  return record.user ? `${record.user.first_name} ${record.user.last_name}` : ''
}

// "82,5", with a Swedish decimal comma.
function formatResult(result) {
  return String(result ?? '').replace('.', ',')
}

// Set within the last year.
function isNew(record) {
  if (!record?.competition_date) return false
  const oneYearMs = 365 * 24 * 60 * 60 * 1000
  return new Date() - new Date(record.competition_date) < oneYearMs
}

function formatDate(date) {
  if (!date) return ''
  return new Date(date).toLocaleDateString('sv-SE', { year: 'numeric', month: 'short', day: 'numeric' })
}
</script>
