<template>
  <div class="container mx-auto max-w-5xl px-4">
    <h1 class="text-center text-3xl font-thin mt-10 mb-6">Administration av klubbrekord</h1>

    <!-- Add New Record Form -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-8 overflow-hidden">
      <div class="bg-gray-800 px-5 py-3">
        <h2 class="text-white font-medium">Lägg till nytt rekord</h2>
      </div>

      <form class="p-5">
        <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Kön</label>
            <div class="flex gap-3">
              <label
                v-for="g in genders"
                :key="g.value"
                :class="[
                  'flex-1 flex items-center justify-center px-3 py-2 border rounded-lg cursor-pointer transition-all text-sm',
                  result.gender === g.value
                    ? 'border-gkk bg-gkk/5 text-gkk font-medium'
                    : 'border-gray-300 hover:border-gray-400',
                ]"
              >
                <input type="radio" :value="g.value" v-model="result.gender" class="sr-only" />
                {{ g.label }}
              </label>
            </div>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Lyftare</label>
            <select
              v-model="result.user_id"
              class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-1 focus:ring-gkk focus:border-gkk"
            >
              <option value="">Välj lyftare...</option>
              <option v-for="lifter in sortedUsers" :key="lifter.id" :value="lifter.id">
                {{ lifter.first_name }} {{ lifter.last_name }}
              </option>
            </select>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Viktklass</label>
            <select
              v-model="result.weight_class"
              class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-1 focus:ring-gkk focus:border-gkk"
            >
              <option value="">Välj...</option>
              <option v-for="wc in weightClassesForForm" :key="wc" :value="wc">{{ wc }} kg</option>
            </select>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Gren</label>
            <select
              v-model="result.event"
              class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-1 focus:ring-gkk focus:border-gkk"
            >
              <option v-for="event in events" :key="event" :value="event">{{ event }}</option>
            </select>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Datum</label>
            <input
              v-model="result.competition_date"
              type="date"
              class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-1 focus:ring-gkk focus:border-gkk"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Resultat (kg)</label>
            <input
              v-model="result.result"
              type="number"
              step="0.5"
              placeholder="0"
              class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-1 focus:ring-gkk focus:border-gkk"
            />
          </div>
        </div>

        <div class="mt-4 flex items-center gap-3">
          <Button @click.prevent="createResult">Spara rekord</Button>
          <span v-if="saving" class="text-sm text-gray-500">Sparar...</span>
        </div>

        <div v-if="createResultError" class="mt-3 p-3 bg-red-50 border border-red-200 rounded-lg text-sm text-red-700">
          Kunde inte spara rekordet. Kontrollera att alla fält är ifyllda.
        </div>
      </form>
    </div>

    <!-- Records Display -->
    <div v-for="gender in genders" :key="gender.value" class="mb-6">
      <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
        <div
          v-for="weightClass in weightClassesFor(gender.value)"
          :key="weightClass"
          class="bg-white rounded-lg shadow-sm overflow-hidden border border-gray-200"
        >
          <div class="bg-gray-800 px-4 py-2.5">
            <h3 class="text-white font-medium">{{ gender.label }} {{ weightClass }} kg</h3>
          </div>

          <div class="divide-y divide-gray-100">
            <div
              v-for="event in events"
              :key="event"
              class="px-4 py-3 hover:bg-gray-50 transition-colors"
            >
              <div class="text-xs text-gray-400 uppercase tracking-wide font-medium">{{ event }}</div>
              <div v-if="getRecord(gender.value, weightClass, event)" class="mt-1">
                <div class="flex items-end justify-between gap-2">
                  <div class="flex-1 min-w-0">
                    <div class="font-semibold text-gray-900 truncate">
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
                  <button
                    @click.prevent="confirmDelete(getRecord(gender.value, weightClass, event))"
                    class="p-1.5 text-gray-300 hover:text-red-500 hover:bg-red-50 rounded transition-colors flex-shrink-0"
                    title="Ta bort"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                  </button>
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

    <!-- Delete Confirmation Modal -->
    <div
      v-if="deleteConfirm"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/50"
      @click.self="deleteConfirm = null"
    >
      <div class="bg-white rounded-lg shadow-xl p-5 max-w-sm mx-4">
        <h3 class="text-lg font-semibold text-gray-900 mb-2">Ta bort rekord?</h3>
        <p class="text-gray-600 text-sm mb-4">
          Ta bort rekordet för <strong>{{ deleteConfirm.name || name(deleteConfirm.user) }}</strong> i {{ deleteConfirm.event }} ({{ deleteConfirm.weight_class }} kg)?
        </p>
        <div class="flex justify-end gap-2">
          <button
            @click="deleteConfirm = null"
            class="px-3 py-1.5 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors"
          >
            Avbryt
          </button>
          <button
            @click="remove(deleteConfirm)"
            class="px-3 py-1.5 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 transition-colors"
          >
            Ta bort
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import Button from './ui/Button.vue'
import Date from '../modules/Date.js'
import axios from 'axios'

export default {
  components: { Button },
  props: ['results', 'users'],
  data() {
    return {
      genders: [
        { value: 'F', label: 'Kvinnor' },
        { value: 'M', label: 'Män' },
      ],
      events: ['Knäböj', 'Bänkpress', 'Marklyft', 'Total'],
      maleClasses: ['52', '59', '66', '74', '83', '93', '105', '120', '120+'],
      femaleClasses: ['43', '47', '52', '57', '63', '69', '76', '84', '84+'],
      result: {
        gender: 'F',
        competition_date: '',
        weight_class: '',
        event: 'Knäböj',
        user_id: '',
        result: '',
      },
      createResultError: false,
      saving: false,
      deleteConfirm: null,
    }
  },
  computed: {
    sortedUsers() {
      return this.users
        .slice()
        .sort((a, b) => `${a.first_name} ${a.last_name}`.localeCompare(`${b.first_name} ${b.last_name}`))
    },
    weightClassesForForm() {
      return this.result.gender === 'M' ? this.maleClasses : this.femaleClasses
    },
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
    confirmDelete(result) {
      this.deleteConfirm = result
    },
    remove(result) {
      axios.delete(`/admin/results/${result.id}`).then(() => window.location.reload())
    },
    createResult() {
      this.saving = true
      this.createResultError = false
      axios
        .post('/admin/results', this.result)
        .then(() => window.location.reload())
        .catch(() => {
          this.createResultError = true
          this.saving = false
        })
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
