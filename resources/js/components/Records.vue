<template>
  <div class="container mx-auto max-w-5xl px-4">
    <div class="h-4"> </div>
    <div v-for="gender in genders" :key="gender.value" class="mb-6">
      <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
        <div
          v-for="weightClass in weightClassesFor(gender.value)"
          :key="weightClass"
          class="bg-white rounded-lg shadow-sm overflow-hidden border border-gray-200 hover:shadow-md transition-shadow duration-200"
        >
          <div class="bg-gray-800 px-4 py-2.5">
            <h3 class="text-white font-medium">{{ gender.label }} {{ weightClass }} kg</h3>
          </div>

          <div class="divide-y divide-gray-100">
            <div
              v-for="event in events"
              :key="event"
              class="px-4 py-3 hover:bg-gray-50 transition-colors"
              :data-testid="`record-${gender.value}-${weightClass}-${event}`"
            >
              <div class="text-xs text-gray-400 uppercase tracking-wide font-medium">{{ event }}</div>
              <div v-if="getRecord(gender.value, weightClass, event)" class="mt-1">
                <div class="font-semibold text-gray-900">
                  {{ getRecord(gender.value, weightClass, event).name || name(getRecord(gender.value, weightClass, event).user) }}
                </div>
                <div class="flex items-baseline gap-2 mt-0.5">
                  <span class="text-xl font-bold text-gray-600">{{ getRecord(gender.value, weightClass, event).result }}</span>
                  <span class="text-sm text-gray-400">kg</span>
                  <span
                    :class="['text-xs ml-auto', withinAYear(getRecord(gender.value, weightClass, event).competition_date) ? 'text-green-600 font-medium' : 'text-gray-400']"
                    v-tooltip.bottom="withinAYear(getRecord(gender.value, weightClass, event).competition_date) ? 'Nytt senaste året' : null"
                  >
                    {{ formatDate(getRecord(gender.value, weightClass, event).competition_date) }}
                  </span>
                </div>
              </div>
              <div v-else class="text-sm text-gray-300 mt-1">—</div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div v-if="hasNoRecords" class="text-center py-12 text-gray-500">
      Inga rekord registrerade ännu.
    </div>
  </div>
</template>

<script>
import Date from '../modules/Date.js'

export default {
  props: ['results'],
  data() {
    return {
      genders: [
        { value: 'F', label: 'Kvinnor' },
        { value: 'M', label: 'Män' },
      ],
      events: ['Knäböj', 'Bänkpress', 'Marklyft', 'Total'],
      maleClasses: ['52', '59', '66', '74', '83', '93', '105', '120', '120+'],
      femaleClasses: ['43', '47', '52', '57', '63', '69', '76', '84', '84+'],
    }
  },
  computed: {
    hasNoRecords() {
      return this.results.length === 0
    },
  },
  methods: {
    weightClassesFor(gender) {
      const classes = gender === 'M' ? this.maleClasses : this.femaleClasses
      const genderResults = this.results.filter((r) => r.gender === gender)
      return classes.filter((wc) => genderResults.some((r) => r.weight_class === wc))
    },
    getRecord(gender, weightClass, event) {
      const records = this.results.filter(
        (r) => r.gender === gender && r.weight_class === weightClass && r.event === event,
      )
      if (records.length === 0) return null
      return records.reduce((best, current) => (Number(current.result) > Number(best.result) ? current : best))
    },
    withinAYear(date) {
      return Date.withinAYear(date)
    },
    name(user) {
      return user ? `${user.first_name} ${user.last_name}` : ''
    },
    formatDate(dateStr) {
      if (!dateStr) return ''
      const date = new window.Date(dateStr)
      return date.toLocaleDateString('sv-SE', { year: 'numeric', month: 'short', day: 'numeric' })
    },
  },
}
</script>
