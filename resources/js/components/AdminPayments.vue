<template>
  <div class="container mx-auto">
    <h2 class="text-2xl font-thin text-center m-4">Betalningsadministration</h2>

    <!-- Filters -->
    <div class="flex flex-col items-center mb-6">
      <div class="flex gap-4 mb-4">
        <div class="flex flex-col gap-2">
          <label class="text-sm font-medium text-gray-700">Betalningstyp</label>
          <select v-model="filters.type" class="rounded-md border-0 py-1.5 px-3 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600">
            <option value="">Alla typer</option>
            <option value="MEMBERSHIP">Medlemsavgift</option>
            <option value="SSFLICENSE">Licensavgift</option>
            <option value="COMPETITION">Tävlingsavgift</option>
          </select>
        </div>
        <div class="flex flex-col gap-2">
          <label class="text-sm font-medium text-gray-700">Status</label>
          <select v-model="filters.state" class="rounded-md border-0 py-1.5 px-3 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600">
            <option value="">Alla statusar</option>
            <option value="PAID">Betald</option>
            <option value="PENDING">Väntande</option>
            <option value="">Obetald</option>
          </select>
        </div>
        <div class="flex flex-col gap-2">
          <label class="text-sm font-medium text-gray-700">År</label>
          <select v-model="filters.year" class="rounded-md border-0 py-1.5 px-3 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600">
            <option value="">Alla år</option>
            <option v-for="year in availableYears" :key="year" :value="year">{{ year }}</option>
          </select>
        </div>
      </div>

      <!-- Search -->
      <div class="relative rounded-md shadow-sm w-64">
        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
          <i class="fa fa-search text-gray-400"></i>
        </div>
        <div
          v-if="search.length > 0"
          @click="search = ''"
          class="cursor-pointer absolute inset-y-0 right-0 flex items-center pr-3"
        >
          <i class="fa fa-times text-gray-400"></i>
        </div>
        <input
          v-model="search"
          class="block w-full rounded-md border-0 py-1.5 pl-10 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm/6"
          placeholder="Sök medlem"
        />
      </div>
    </div>

    <!-- Payments Table -->
    <div class="flex flex-col">
      <div class="-my-2 py-2 overflow-x-auto sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8">
        <div class="align-middle inline-block min-w-full shadow overflow-hidden sm:rounded-lg border-b border-gray-200">
          <table class="min-w-full">
            <thead>
              <tr>
                <th @click="sortBy('user_name')"
                  class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider cursor-pointer">
                  Medlem
                </th>
                <th @click="sortBy('type')"
                  class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-center text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider cursor-pointer">
                  Typ / Tävling
                </th>
                <th @click="sortBy('year')"
                  class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-center text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider cursor-pointer">
                  År
                </th>
                <th @click="sortBy('sek_amount')"
                  class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-center text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider cursor-pointer">
                  Belopp (SEK)
                </th>
                <th @click="sortBy('state')"
                  class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-center text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider cursor-pointer">
                  Status
                </th>
                <th @click="sortBy('created_at')"
                  class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-center text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider cursor-pointer">
                  Skapad
                </th>
              </tr>
            </thead>
            <tbody class="bg-white">
              <tr v-for="payment in filteredSortedPayments" :key="payment.id">
                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                  <div class="text-sm leading-5 font-medium text-gray-900">
                    {{ payment.user.first_name }} {{ payment.user.last_name }}
                  </div>
                  <div class="text-sm leading-5 text-gray-500">{{ payment.user.email }}</div>
                </td>
                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200 text-center">
                  <div class="flex flex-col items-center gap-1">
                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full"
                      :class="getTypeClass(payment.type)">
                      {{ getTypeText(payment.type) }}
                    </span>
                    <div v-if="payment.type === 'COMPETITION' && payment.competition" class="text-xs text-gray-600 text-center">
                      {{ payment.competition.name }}
                      <div class="text-xs text-gray-500">{{ dateString(payment.competition.date) }}</div>
                    </div>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200 text-center text-sm text-gray-500">
                  {{ payment.year }}
                </td>
                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200 text-center text-sm text-gray-500">
                  <span v-if="payment.sek_amount">{{ payment.sek_amount }} kr</span>
                  <span v-else class="text-gray-400">-</span>
                </td>
                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200 text-center">
                  <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full"
                    :class="getStatusClass(payment.state)">
                    {{ getStatusText(payment.state) }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200 text-center text-sm text-gray-500">
                  {{ dateString(payment.created_at) }}
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Summary Stats -->
    <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-4">
      <div class="bg-white p-6 rounded-lg shadow">
        <h3 class="text-lg font-medium text-gray-900 mb-2">Total antal betalningar</h3>
        <p class="text-3xl font-bold text-gray-700">{{ filteredSortedPayments.length }}</p>
      </div>
      <div class="bg-white p-6 rounded-lg shadow">
        <h3 class="text-lg font-medium text-gray-900 mb-2">Betalda</h3>
        <p class="text-3xl font-bold text-green-600">{{ paidCount }}</p>
      </div>
      <div class="bg-white p-6 rounded-lg shadow">
        <h3 class="text-lg font-medium text-gray-900 mb-2">Väntande/Obetalda</h3>
        <p class="text-3xl font-bold text-red-600">{{ unpaidCount }}</p>
      </div>
    </div>
  </div>
</template>

<script>
import ToggleButton from './ui/ToggleButton.vue'

export default {
  components: { ToggleButton },
  props: {
    initialPayments: {
      type: Array,
      required: true,
    },
    user: {
      type: Object,
      required: true,
    },
  },
  data() {
    return {
      payments: this.initialPayments,
      search: '',
      sortKey: 'created_at',
      sortOrder: -1,
      filters: {
        type: '',
        state: '',
        year: '',
      }
    }
  },
  computed: {
    availableYears() {
      const years = [...new Set(this.payments.map(p => p.year))]
      return years.sort((a, b) => b - a)
    },
    filteredSortedPayments() {
      let filtered = this.payments

      // Apply type filter
      if (this.filters.type) {
        filtered = filtered.filter(p => p.type === this.filters.type)
      }

      // Apply state filter
      if (this.filters.state !== '') {
        if (this.filters.state === '') {
          filtered = filtered.filter(p => !p.state || p.state === null)
        } else {
          filtered = filtered.filter(p => p.state === this.filters.state)
        }
      }

      // Apply year filter
      if (this.filters.year) {
        filtered = filtered.filter(p => p.year == this.filters.year)
      }

      // Apply search
      if (this.search) {
        const searchLower = this.search.toLowerCase()
        filtered = filtered.filter(p => {
          const fullName = `${p.user.first_name} ${p.user.last_name}`.toLowerCase()
          const email = p.user.email.toLowerCase()
          return fullName.includes(searchLower) || email.includes(searchLower)
        })
      }

      // Sort
      return filtered.sort((a, b) => {
        let aVal = a[this.sortKey]
        let bVal = b[this.sortKey]

        // Special handling for user name sorting
        if (this.sortKey === 'user_name') {
          aVal = `${a.user.first_name} ${a.user.last_name}`
          bVal = `${b.user.first_name} ${b.user.last_name}`
        }

        if (aVal === null || aVal === undefined) aVal = ''
        if (bVal === null || bVal === undefined) bVal = ''

        return this.sortOrder * String(aVal).localeCompare(String(bVal), undefined, { numeric: true })
      })
    },
    paidCount() {
      return this.filteredSortedPayments.filter(p => p.state === 'PAID').length
    },
    unpaidCount() {
      return this.filteredSortedPayments.filter(p => p.state !== 'PAID').length
    }
  },
  methods: {
    sortBy(key) {
      if (this.sortKey === key) {
        this.sortOrder *= -1
      } else {
        this.sortOrder = 1
      }
      this.sortKey = key
    },
    dateString(date) {
      if (!date) return ''
      return date.substr(0, 10)
    },
    getStatusText(state) {
      switch (state) {
        case 'PAID': return 'Betald'
        case 'PENDING': return 'Väntande'
        case null:
        case undefined:
        case '': return 'Obetald'
        default: return state || 'Obetald'
      }
    },
    getStatusClass(state) {
      switch (state) {
        case 'PAID': return 'bg-green-100 text-green-800'
        case 'PENDING': return 'bg-yellow-100 text-yellow-800'
        case null:
        case undefined:
        case '': return 'bg-red-100 text-red-800'
        default: return 'bg-gray-100 text-gray-800'
      }
    },
    getTypeText(type) {
      switch (type) {
        case 'MEMBERSHIP': return 'Medlemsavgift'
        case 'SSFLICENSE': return 'Licensavgift'
        case 'COMPETITION': return 'Tävlingsavgift'
        default: return type
      }
    },
    getTypeClass(type) {
      switch (type) {
        case 'MEMBERSHIP': return 'bg-blue-100 text-blue-800'
        case 'SSFLICENSE': return 'bg-green-100 text-green-800'
        case 'COMPETITION': return 'bg-purple-100 text-purple-800'
        default: return 'bg-gray-100 text-gray-800'
      }
    },
  }
}
</script>