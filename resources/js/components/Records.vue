<template>
  <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
    <div
      v-if="!hasNoRecords"
      class="sticky top-16 z-20 -mx-4 mb-6 border-b border-gray-900/5 bg-gray-50/90 px-4 py-4 backdrop-blur-md sm:mx-0 sm:rounded-2xl sm:border sm:bg-white/80 sm:px-5 sm:shadow-sm"
    >
      <div
        class="relative inline-grid auto-cols-fr grid-flow-col rounded-full bg-gray-100 p-1 ring-1 ring-inset ring-gray-900/5"
        role="radiogroup"
        aria-label="Filtrera på kön"
      >
        <span
          class="absolute inset-y-1 left-1 rounded-full bg-gkk shadow-sm transition-transform duration-300 ease-out"
          :style="{
            width: `calc((100% - 0.5rem) / ${genderOptions.length})`,
            transform: `translateX(${selectedGenderIndex * 100}%)`,
          }"
          aria-hidden="true"
        ></span>
        <button
          v-for="option in genderOptions"
          :key="option.value"
          type="button"
          role="radio"
          :aria-checked="selectedGender === option.value"
          @click="selectGender(option.value)"
          class="relative z-10 rounded-full px-5 py-2 text-sm font-semibold transition-colors duration-300"
          :class="selectedGender === option.value ? 'text-white' : 'text-gray-600 hover:text-gkk'"
        >
          {{ option.label }}
        </button>
      </div>

      <div
        v-if="selectedGender"
        class="mt-3 flex flex-wrap items-center gap-2 border-t border-gray-900/5 pt-3"
        role="group"
        aria-label="Filtrera på viktklass"
      >
        <span class="mr-1 text-xs font-semibold uppercase tracking-wider text-gray-400">Viktklass</span>
        <button
          type="button"
          @click="selectedClasses = []"
          :aria-pressed="selectedClasses.length === 0"
          class="rounded-full px-3 py-1 text-sm font-medium transition"
          :class="
            selectedClasses.length === 0
              ? 'bg-gkk/10 text-gkk ring-1 ring-gkk/30'
              : 'text-gray-500 ring-1 ring-gray-900/10 hover:text-gkk'
          "
        >
          Alla
        </button>
        <button
          v-for="weightClass in weightClassesFor(selectedGender)"
          :key="weightClass"
          type="button"
          @click="toggleClass(weightClass)"
          :aria-pressed="selectedClasses.includes(weightClass)"
          class="rounded-full px-3 py-1 text-sm font-medium tabular-nums transition"
          :class="
            selectedClasses.includes(weightClass)
              ? 'bg-gkk text-white ring-1 ring-gkk'
              : 'bg-white text-gray-600 ring-1 ring-gray-900/10 hover:text-gkk hover:ring-gkk/30'
          "
        >
          {{ weightClass }} kg
        </button>
      </div>
    </div>

    <div v-for="gender in visibleGenders" :key="gender.value" class="mb-6">
      <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
        <div
          v-for="weightClass in visibleClassesFor(gender.value)"
          :key="weightClass"
          class="bg-white rounded-lg shadow-xs overflow-hidden border border-gray-200 hover:shadow-md transition-shadow duration-200"
        >
          <div class="bg-gkk px-4 py-2.5">
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
                  {{
                    getRecord(gender.value, weightClass, event).name ||
                    name(getRecord(gender.value, weightClass, event).user)
                  }}
                </div>
                <div class="flex items-baseline gap-2 mt-0.5">
                  <span class="text-xl font-bold text-gray-600">{{
                    getRecord(gender.value, weightClass, event).result
                  }}</span>
                  <span class="text-sm text-gray-400">kg</span>
                  <span
                    :class="[
                      'text-xs ml-auto',
                      withinAYear(getRecord(gender.value, weightClass, event).competition_date)
                        ? 'text-green-600 font-medium'
                        : 'text-gray-400',
                    ]"
                    v-tooltip.bottom="
                      withinAYear(getRecord(gender.value, weightClass, event).competition_date)
                        ? 'Nytt senaste året'
                        : null
                    "
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

    <div v-if="hasNoRecords" class="text-center py-12 text-gray-500">Inga rekord registrerade ännu.</div>
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
      selectedGender: null,
      selectedClasses: [],
    }
  },
  computed: {
    hasNoRecords() {
      return this.results.length === 0
    },
    genderOptions() {
      const count = (gender) => this.weightClassesFor(gender).length

      return [
        { value: null, label: 'Alla', count: count('F') + count('M') },
        ...this.genders.map((gender) => ({ ...gender, count: count(gender.value) })),
      ].filter((option) => option.count > 0 || option.value === null)
    },
    selectedGenderIndex() {
      return Math.max(
        0,
        this.genderOptions.findIndex((option) => option.value === this.selectedGender),
      )
    },
    visibleGenders() {
      return this.genders.filter((gender) => !this.selectedGender || gender.value === this.selectedGender)
    },
  },
  watch: {
    selectedGender() {
      this.syncUrl()
    },
    selectedClasses() {
      this.syncUrl()
    },
  },
  created() {
    // Restore filters from the URL so a filtered view can be shared
    const params = new URLSearchParams(window.location.search)
    const gender = params.get('kon')

    if (this.genders.some((option) => option.value === gender)) {
      this.selectedGender = gender
      const available = this.weightClassesFor(gender)
      this.selectedClasses = (params.get('klass') || '').split(',').filter((wc) => available.includes(wc))
    }
  },
  methods: {
    selectGender(gender) {
      this.selectedGender = gender
      this.selectedClasses = []
    },
    toggleClass(weightClass) {
      this.selectedClasses = this.selectedClasses.includes(weightClass)
        ? this.selectedClasses.filter((wc) => wc !== weightClass)
        : [...this.selectedClasses, weightClass]
    },
    visibleClassesFor(gender) {
      const classes = this.weightClassesFor(gender)

      return this.selectedClasses.length ? classes.filter((wc) => this.selectedClasses.includes(wc)) : classes
    },
    syncUrl() {
      // Built by hand (not URLSearchParams) to keep the commas readable: ?kon=F&klass=63,69
      const parts = []

      if (this.selectedGender) {
        parts.push(`kon=${this.selectedGender}`)
      }

      if (this.selectedClasses.length) {
        parts.push(`klass=${this.selectedClasses.map(encodeURIComponent).join(',')}`)
      }

      window.history.replaceState({}, '', window.location.pathname + (parts.length ? `?${parts.join('&')}` : ''))
    },
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
