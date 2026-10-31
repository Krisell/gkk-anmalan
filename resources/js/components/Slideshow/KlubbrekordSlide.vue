<template>
  <div class="flex flex-col h-full gap-[3vh] px-[4vw] pt-[5vh] pb-[4vh] overflow-hidden">
    <div class="flex flex-col items-center gap-[1.5vh] shrink-0">
      <div class="h-[0.6vh] min-h-1 w-[6vw] rounded-full bg-gkk/80"></div>
      <h1 class="text-center text-gkk font-semibold tracking-tight leading-tight text-[min(4vw,6.5vh)]">
        {{ title }}
      </h1>
    </div>

    <div v-if="records.length" class="flex-1 min-h-0 grid grid-cols-2 grid-rows-5 gap-x-[1.5vw] gap-y-[1.6vh]">
      <div
        v-for="record in records"
        :key="record.id"
        class="relative bg-white rounded-[1.2vh] shadow-sm ring-1 ring-gkk/10 overflow-hidden flex items-stretch min-h-0"
      >
        <div class="bg-gkk text-white flex flex-col items-center justify-center px-[1vw] w-[22%] shrink-0 text-center">
          <div class="text-[min(1.6vw,2.8vh)] font-semibold leading-tight">{{ record.event }}</div>
          <div class="text-[min(1vw,1.8vh)] text-white/75 leading-tight mt-[0.3vh]">
            {{ genderLabel(record.gender) }} {{ record.weight_class }} kg
          </div>
        </div>
        <div class="flex-1 flex items-center justify-between gap-[1vw] px-[1.5vw] min-w-0">
          <div class="flex flex-col min-w-0">
            <div class="text-[min(2vw,3.6vh)] text-gkk truncate leading-tight">
              {{ athleteName(record) }}
            </div>
            <div
              class="text-[min(1vw,1.8vh)] leading-tight mt-[0.3vh]"
              :class="withinAYear(record.competition_date) ? 'text-emerald-600' : 'text-gray-400'"
            >
              {{ formatDate(record.competition_date) }}
            </div>
          </div>
          <div class="flex items-center gap-[1vw] shrink-0">
            <span
              v-if="withinAYear(record.competition_date)"
              class="rounded-full bg-emerald-100 text-emerald-700 font-semibold uppercase tracking-wide text-[min(0.9vw,1.6vh)] px-[0.7vw] py-[0.3vh]"
            >
              Nytt
            </span>
            <div class="text-[min(2.6vw,4.6vh)] font-bold text-gkk whitespace-nowrap tabular-nums leading-none">
              {{ record.result }}<span class="text-[0.5em] font-normal text-gkk-light ml-[0.2em]">kg</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div v-else class="flex-1 flex items-center justify-center text-[min(3vw,5vh)] text-gray-400 font-light">
      Inga klubbrekord registrerade ännu.
    </div>
  </div>
</template>

<script setup>
const { title, records } = defineProps({
  title: {
    type: String,
    required: false,
    default: 'Senaste klubbrekorden',
  },
  // Part of the slideshow snapshot (see Slideshow.vue).
  records: {
    type: Array,
    required: false,
    default: () => [],
  },
})

function genderLabel(gender) {
  return gender === 'F' ? 'Kvinnor' : 'Män'
}

function athleteName(record) {
  if (record.name) {
    return record.name
  }
  return record.user ? `${record.user.first_name} ${record.user.last_name}` : ''
}

function withinAYear(date) {
  if (!date) return false
  const oneYearMs = 365 * 24 * 60 * 60 * 1000
  return new Date() - new Date(date) < oneYearMs
}

function formatDate(date) {
  if (!date) return ''
  return new Date(date).toLocaleDateString('sv-SE', { year: 'numeric', month: 'short', day: 'numeric' })
}
</script>
